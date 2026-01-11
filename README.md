# Lab Equipment Booking (Demo)

This is a simple client-side demo. Open the files in the `web` folder in a browser to try it.

Files:

- `index.html` - Main page
- `styles.css` - Styling
- `app.js` - Booking logic (stores state in `localStorage`)

Additional pages:

- `login.html` - Login page (stores a simple username in `localStorage`)
- `mybookings.html` - Shows your personal bookings and allows reporting issues

Run (PowerShell):

```powershell
Start-Process -FilePath "${PWD.Path}\web\index.html"
```

Notes:

- The left column lists equipment.
- The top row shows hourly timeslots.
- Click a cell to book or cancel (confirmation). Booked cells display `X`.

Notes about authentication & reports:

- This demo uses a simple client-side "login" (no password). The username is stored in `localStorage` and used to mark bookings.
- Go to `login.html` to set your username before booking. Visit `mybookings.html` to see only your bookings and to submit problem reports for a booking.

Future ideas:

- Multi-user backend: Add an API + database for concurrency control and real-time sync.
- User accounts and name display for bookings.
 
Backend prototype (Node + SQLite)

I added a small prototype backend under `web/backend` that you can run to add real SQL-backed accounts:

- `web/backend/package.json` - Node deps and start script
- `web/backend/server.js` - Simple Express server with `/api/register` and `/api/login` endpoints and SQLite DB

Run the backend (requires Node.js installed):

```powershell
Set-Location e:\web\backend
npm install
node server.js
```

The server serves the static `web` folder at `http://localhost:3000` as well. If the backend is running, the login/register pages will call the API; otherwise they fall back to local (browser-only) accounts.
