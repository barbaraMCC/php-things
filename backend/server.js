const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const mysql = require('mysql2/promise');
const path = require('path');

// MySQL 연결 설정 (본인의 DB 정보로 수정 필요)
const dbConfig = {
  host: 'localhost',
  user: 'root',
  password: '', // 비밀번호를 설정하세요
  database: 'lab_equipment_res'
};

let pool;

async function initDB() {
  pool = mysql.createPool(dbConfig);
  console.log('MySQL connected');
}

const app = express();
app.use(cors());
app.use(bodyParser.json());

// GET /api/equipment - 모든 장비 조회
app.get('/api/equipment', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT * FROM equipment');
    res.json(rows);
  } catch (err) {
    console.error(err);
    res.status(500).send('database error');
  }
});

// GET /api/reservations - 모든 예약 조회 (특정 날짜 필터 가능)
app.get('/api/reservations', async (req, res) => {
  try {
    const { date } = req.query;
    let query = `
      SELECT r.*, u.user_name, u.email, e.equipment_name, e.equipment_number,
             DATE_FORMAT(r.reservation_date, "%Y-%m-%d") as reservation_date_formatted
      FROM reservation r
      JOIN user u ON r.user_id = u.user_id
      JOIN equipment e ON r.equipment_id = e.equipment_id
    `;
    const params = [];
    if (date) {
      query += ' WHERE DATE_FORMAT(r.reservation_date, "%Y-%m-%d") = ?';
      params.push(date);
    }
    let [rows] = await pool.query(query, params);
    // 응답 전에 reservation_date를 formatted 버전으로 변경
    rows = rows.map(row => ({
      ...row,
      reservation_date: row.reservation_date_formatted
    }));
    // reservation_date_formatted 필드 제거
    rows = rows.map(({reservation_date_formatted, ...row}) => row);
    res.json(rows);
  } catch (err) {
    console.error(err);
    res.status(500).send('database error');
  }
});

// POST /api/reservations - 새 예약 생성
app.post('/api/reservations', async (req, res) => {
  const { user_id, equipment_id, reservation_date, start_time, end_time, purpose } = req.body;
  if (!user_id || !equipment_id || !reservation_date || !start_time || !end_time) {
    return res.status(400).send('missing required fields');
  }
  try {
    // 중복 예약 체크
    const [existing] = await pool.query(
      `SELECT * FROM reservation 
       WHERE equipment_id = ? AND DATE_FORMAT(reservation_date, "%Y-%m-%d") = ? 
       AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?) OR (start_time >= ? AND end_time <= ?))`,
      [equipment_id, reservation_date, end_time, start_time, end_time, start_time, start_time, end_time]
    );
    if (existing.length > 0) {
      return res.status(409).send('time slot already reserved');
    }
    
    const [result] = await pool.query(
      'INSERT INTO reservation (user_id, equipment_id, reservation_date, start_time, end_time, purpose, created_at, status) VALUES (?,?,?,?,?,?,NOW(),?)',
      [user_id, equipment_id, reservation_date, start_time, end_time, purpose || '', 'confirmed']
    );
    res.json({ ok: true, reservation_id: result.insertId });
  } catch (err) {
    console.error(err);
    res.status(500).send('database error');
  }
});

// DELETE /api/reservations/:id - 예약 삭제
app.delete('/api/reservations/:id', async (req, res) => {
  try {
    const [result] = await pool.query('DELETE FROM reservation WHERE reservation_id = ?', [req.params.id]);
    if (result.affectedRows === 0) {
      return res.status(404).send('reservation not found');
    }
    res.json({ ok: true });
  } catch (err) {
    console.error(err);
    res.status(500).send('database error');
  }
});

// GET /api/users - 모든 사용자 조회
app.get('/api/users', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT * FROM user');
    res.json(rows);
  } catch (err) {
    console.error(err);
    res.status(500).send('database error');
  }
});

// POST /api/users - 새 사용자 생성
app.post('/api/users', async (req, res) => {
  const { user_name, email } = req.body;
  if (!user_name || !email) {
    return res.status(400).send('user_name and email required');
  }
  try {
    // 이메일 중복 체크
    const [existing] = await pool.query('SELECT * FROM user WHERE email = ?', [email]);
    if (existing.length > 0) {
      return res.status(409).send('Email already exists');
    }
    const [result] = await pool.query('INSERT INTO user (user_name, email) VALUES (?,?)', [user_name, email]);
    res.json({ ok: true, user_id: result.insertId });
  } catch (err) {
    console.error(err);
    res.status(500).send('database error');
  }
});

// POST /api/users/login - 이메일로 로그인
app.post('/api/users/login', async (req, res) => {
  const { email } = req.body;
  if (!email) {
    return res.status(400).send('email required');
  }
  try {
    const [rows] = await pool.query('SELECT * FROM user WHERE email = ?', [email]);
    if (rows.length === 0) {
      return res.status(404).send('User not found');
    }
    res.json({ ok: true, user: rows[0] });
  } catch (err) {
    console.error(err);
    res.status(500).send('database error');
  }
});

// GET /api/reports - 모든 문제 보고 조회
app.get('/api/reports', async (req, res) => {
  try {
    const [rows] = await pool.query(`
      SELECT pr.*, r.reservation_date, r.start_time, r.end_time,
             u.user_name, e.equipment_name
      FROM problem_report pr
      JOIN reservation r ON pr.reservation_id = r.reservation_id
      JOIN user u ON r.user_id = u.user_id
      JOIN equipment e ON r.equipment_id = e.equipment_id
    `);
    res.json(rows);
  } catch (err) {
    console.error(err);
    res.status(500).send('database error');
  }
});

// POST /api/reports - 새 문제 보고 생성
app.post('/api/reports', async (req, res) => {
  const { reservation_id, description } = req.body;
  if (!reservation_id || !description) {
    return res.status(400).send('reservation_id and description required');
  }
  try {
    const [result] = await pool.query(
      'INSERT INTO problem_report (reservation_id, description, reported_at, status) VALUES (?,?,NOW(),?)',
      [reservation_id, description, 'pending']
    );
    res.json({ ok: true, report_id: result.insertId });
  } catch (err) {
    console.error(err);
    res.status(500).send('database error');
  }
});

app.get('/api/ping', (req, res) => res.json({ ok: true }));


// Serve static files for convenience (so you can run backend from e:/web/backend)
app.use('/', express.static(path.join(__dirname, '..')));

const PORT = process.env.PORT || 3000;

initDB().then(() => {
  app.listen(PORT, () => console.log(`Lab booking backend listening on ${PORT}`));
}).catch(err => {
  console.error('Failed to initialize database:', err);
  process.exit(1);
});
