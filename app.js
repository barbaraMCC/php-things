
let EQUIPMENT = []; // Will be loaded from database

const USER_KEY = 'booking-current-user';
let equipmentMap = {}; // equipment_name -> equipment_id
let currentUserId = null;

async function loadEquipment() {
  try {
    const res = await fetch('get_equipment.php');
    const data = await res.json();

    EQUIPMENT = data.map(eq => eq.equipment_name);
    equipmentMap = {};
    data.forEach(eq => {
      equipmentMap[eq.equipment_name] = eq.equipment_id;
    });

  } catch (e) {
    console.error('Failed to load equipment:', e);
    // Fallback
    EQUIPMENT = ['Microscope A', 'Microscope B', 'Oscilloscope', '3D Printer', 'Spectrometer', 'Centrifuge'];
  }
}


async function loadReservations(date) {
  try {
    const dateStr = date || new Date().toISOString().split('T')[0];
    const res = await fetch('get_reservations.php?date=' + dateStr ,
  { cache: 'no-store' });
    const data = await res.json();
    return data;
  } catch (e) {
    console.error('Failed to load reservations:', e);
    return [];
  }
}


async function saveReport(reservationId, description) {
  try {
    const res = await fetch(`${API_BASE}/reports`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ reservation_id: reservationId, description })
    });
    return await res.json();
  } catch (e) {
    console.error('Failed to save report:', e);
    return null;
  }
}

function logout() {
  localStorage.removeItem(USER_KEY);
  currentUserId = null;
  updateUserUI();
}

function makeHours(start, end){
  const arr = [];
  for(let h=start; h<end; h++) arr.push(h);
  return arr;
}

function updateUserUI() {
  const el = document.getElementById('userInfo');
  if (!el) return;

  const user = bookingApp.currentUser;
  if (user && bookingApp.loggedIn) {
    el.innerHTML = `<button id="logoutBtn">Logout</button>`;
    document.getElementById('logoutBtn').addEventListener('click', () => {
      window.location.href = 'logout.php'; // créer un logout simple
    });
  } else {
    el.innerHTML = '';
  }
}

function escapeHtml(s) {
  return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

let currentReservations = [];
let selectedDate = new Date().toISOString().split('T')[0];

async function render() {
  // Ensure equipment is loaded
  if (EQUIPMENT.length === 0) {
    await loadEquipment();
  }
  
  // fixed hours 9..20
  const start = 9;
  const end = 20;
  const hours = makeHours(start, end);
  const table = document.getElementById('schedule');
  if (!table) return;
  table.innerHTML = '';

  const thead = document.createElement('thead');
  const hrow = document.createElement('tr');
  const thEmpty = document.createElement('th');
  thEmpty.textContent = 'Equipment · Time';
  hrow.appendChild(thEmpty);
  hours.forEach(h => {
    const th = document.createElement('th');
    th.textContent = `${pad(h)}:00 - ${pad(h + 1)}:00`;
    hrow.appendChild(th);
  });
  thead.appendChild(hrow);
  table.appendChild(thead);

  const tbody = document.createElement('tbody');
  currentReservations = await loadReservations(selectedDate);
  const user = bookingApp.currentUser;

  EQUIPMENT.forEach((eq, ri) => {
    const tr = document.createElement('tr');
    const tdEq = document.createElement('td');
    tdEq.className = 'equipment';
    tdEq.textContent = eq;
    tr.appendChild(tdEq);

    hours.forEach(h => {
      const td = document.createElement('td');
      td.className = 'slot';
      td.dataset.eq = eq;
      td.dataset.hour = h;

      // 해당 시간에 이 장비의 예약이 있는지 확인
      const reservation = currentReservations.find(r => {
        const match = r.equipment_name === eq &&
               r.status.trim() !== 'canceled' &&
               r.reservation_date === selectedDate &&
               parseInt(r.start_time.split(':')[0]) <= h &&
               parseInt(r.end_time.split(':')[0]) > h;
        return match;
      });

      if (reservation) {
        td.classList.add('booked');
        td.title = `Booked by user ${reservation.user_id} `;
        td.textContent = 'X';
        if (user && reservation.user_id === user.user_id) {
          td.classList.add('mine');
          td.dataset.reservationId = reservation.reservation_id;
        }
      }
      td.addEventListener('click', () => {
        toggleBooking(eq, h, reservation);
      });
      tr.appendChild(td);
    });

    tbody.appendChild(tr);
  });

  table.appendChild(tbody);
}

function pad(n) {
  return n.toString().padStart(2, '0');
}

// Modal helpers (use the modal in index.php for nicer confirmations)
let _modalResolve = null;
function initModal() {
  const modal = document.getElementById('modal');
  const ok = document.getElementById('modalOk');
  const cancel = document.getElementById('modalCancel');
  const title = document.getElementById('modalTitle');
  const body = document.getElementById('modalBody');
  if (!modal) return;
  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      closeModal(false);
    }
  });
  ok.addEventListener('click', () => closeModal(true));
  cancel.addEventListener('click', () => closeModal(false));
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal(false);
  });
  function closeModal(result) {
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    modal.style.pointerEvents = 'none';
    ok.disabled = true;
    cancel.disabled = true;
    if (_modalResolve) {
      _modalResolve(result);
      _modalResolve = null;
    }
  }
  window.showConfirm = function(ctitle, cbody) {
    return new Promise((resolve) => {
      _modalResolve = resolve;
      title.textContent = ctitle || 'Confirm';
      body.textContent = cbody || '';
      modal.classList.remove('hidden');
      modal.setAttribute('aria-hidden', 'false');
      modal.style.pointerEvents = 'auto';
      ok.disabled = false;
      cancel.disabled = false;
    });
  };
}

async function toggleBooking(eq, h, existingReservation) {
   const user = bookingApp.currentUser;
  
  // Pop-up si pas connecté
  if (!window.bookingApp.loggedIn || !user || !user.user_id) {
    const go = await showConfirm(
      'Login required',
      'You must be logged in to book. Go to login page?'
    );
    if (go) window.location.href = 'login.php';
    return;
  }

  if (existingReservation) {
    if (existingReservation.user_id !== user.user_id) {
      alert('This slot is booked by another user.');
      return;
    }
    const ok = await showConfirm('Cancel booking', `Cancel booking for ${eq} at ${pad(h)}:00?`);
    if (!ok) return;
    
    try {
      const res = await fetch('cancel_booking.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `reservation_id=${existingReservation.reservation_id}`
      });

      if (res.ok) {
        await render();
      } else {
        alert('Failed to cancel booking');
      }

    } catch (e) {
      console.error(e);
      alert('Failed to cancel booking');
    }
  } else {
    const ok = await showConfirm('Confirm booking', `Book ${eq} at ${pad(h)}:00?`);
    if (!ok) return;

    const equipmentId = equipmentMap[eq];
    if (!equipmentId) {
      alert('Equipment not found');
      return;
    }

    try {
      const now = new Date();
      const pad = n => n.toString().padStart(2, '0');
      const formattedNow = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

      const res = await fetch('create_reservation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          user_id: user.user_id,
          equipment_id: equipmentId,
          reservation_date: selectedDate,
          start_time: `${pad(h)}:00:00`,
          end_time: `${pad(h + 1)}:00:00`,
          created_at: formattedNow,
          status: 'In progress',
          purpose: 'Lab work'
        })
      });
      if (res.ok) {
        await render();
      } else {
        const text = await res.text();
        alert(`Failed to create booking: ${text}`);
      }
    } catch (e) {
      console.error(e);
      alert('Failed to create booking');
    }
  }
}


document.addEventListener('DOMContentLoaded', async () => {
  await loadEquipment();
  const userData = bookingApp.currentUser;
  if (userData) {
    currentUserId = userData.user_id;
  }
  updateUserUI();
  initModal();
  
  await render();
});

// Expose helpers for other pages (login/mybookings)
window.bookingApp.logout = logout;
window.bookingApp.loadReservations = loadReservations;
window.bookingApp.saveReport = saveReport;
window.bookingApp.loadEquipment = loadEquipment;
window.bookingApp.updateUserUI = updateUserUI;
// rajoute juste ça si jamais elle n'existe pas encore
window.bookingApp.currentUser = window.bookingApp.currentUser || null;

