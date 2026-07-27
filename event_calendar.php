<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/include/dbConfig.php';
require_once __DIR__ . '/include/dbSetup.php';

$sessionUser = $_SESSION['user'] ?? null;
if (!$sessionUser) {
    header("Location: index.php?auth_error=" . urlencode("Please login to access the interactive Event Calendar."));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Full Event Calendar — AIMSA Portal</title>
<meta name="description" content="Full Interactive Event Calendar for AIMSA Student Members. View departmental events, dates, venues and register for upcoming activities.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
:root{
  --ink:#050d1a;
  --navy-950:#081733;
  --navy-900:#0c2148;
  --navy-800:#123163;
  --navy-700:#1a4180;
  --accent:#3E8BFF;
  --accent-soft:#7fb0ff;
  --accent-glow:rgba(62,139,255,.35);
  --green:#10b981;
  --green-soft:rgba(16,185,129,.15);
  --purple:#8b5cf6;
  --amber:#f59e0b;
  --rose:#f43f5e;
  --white:#ffffff;
  --paper:#f4f7fc;
  --paper-dim:#e7edf7;
  --muted:#93a7c9;
  --muted-dark:#5b6d8c;
  --line:rgba(255,255,255,.12);
  --line-dark:rgba(8,23,51,.1);
  --radius:16px;
  --shadow-lg:0 20px 40px -15px rgba(6,16,35,.35);
  --ff-display:'Sora',sans-serif;
  --ff-body:'Inter',sans-serif;
  --ff-mono:'JetBrains Mono',monospace;
}

*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:var(--ff-body);background:var(--paper);color:var(--navy-950);overflow-x:hidden;-webkit-font-smoothing:antialiased;min-height:100vh;display:flex;flex-direction:column;}
a{color:inherit;text-decoration:none;}
button, input, select{font-family:inherit;}

/* TOPBAR */
.topbar{background:var(--navy-950);color:var(--white);border-bottom:1px solid var(--line);padding:0 28px;height:70px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 4px 20px rgba(0,0,0,.15);}
.topbar-left{display:flex;align-items:center;gap:16px;}
.brand-logo{width:40px;height:40px;border-radius:50%;background:var(--white);display:flex;align-items:center;justify-content:center;overflow:hidden;border:2px solid rgba(255,255,255,.3);}
.brand-logo svg{width:22px;height:22px;stroke:var(--navy-950);fill:none;stroke-width:2;}
.brand-text b{font-family:var(--ff-display);font-size:1.05rem;color:var(--white);display:block;line-height:1.2;}
.brand-text span{font-family:var(--ff-mono);font-size:.6rem;letter-spacing:.12em;color:var(--accent-soft);text-transform:uppercase;}

.topbar-right{display:flex;align-items:center;gap:16px;}
.user-badge{display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.08);padding:6px 14px;border-radius:999px;border:1px solid var(--line);}
.user-avatar{width:28px;height:28px;border-radius:50%;background:var(--accent);color:var(--white);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem;}
.user-info{font-size:0.8rem;color:var(--white);}
.user-info span{font-size:0.68rem;color:var(--muted);display:block;}
.btn-back{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--accent),var(--navy-700));color:var(--white);padding:8px 16px;border-radius:10px;font-size:0.82rem;font-weight:600;transition:all .2s ease;border:none;cursor:pointer;}
.btn-back:hover{transform:translateY(-1px);box-shadow:0 6px 16px var(--accent-glow);}

/* MAIN CONTAINER */
.container{max-width:1400px;width:100%;margin:0 auto;padding:24px;flex:1;display:flex;flex-direction:column;gap:24px;}

/* CONTROLS HEADER */
.controls-card{background:var(--white);border-radius:var(--radius);padding:20px 24px;border:1px solid var(--line-dark);box-shadow:0 4px 16px rgba(8,23,51,.04);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;}
.cal-nav-group{display:flex;align-items:center;gap:12px;}
.cal-nav-btn{background:var(--paper);border:1px solid var(--line-dark);color:var(--navy-900);width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;cursor:pointer;transition:.2s ease;}
.cal-nav-btn:hover{background:var(--navy-950);color:var(--white);border-color:var(--navy-950);}
.current-month-title{font-family:var(--ff-display);font-size:1.35rem;font-weight:800;color:var(--navy-950);min-width:190px;}
.btn-today{background:var(--navy-950);color:var(--white);border:none;padding:8px 16px;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;transition:.2s ease;}
.btn-today:hover{background:var(--accent);}

.filter-group{display:flex;align-items:center;gap:12px;flex-wrap:wrap;}
.search-input{background:var(--paper);border:1px solid var(--line-dark);padding:8px 14px;border-radius:10px;font-size:0.82rem;width:220px;outline:none;transition:.2s ease;}
.search-input:focus{border-color:var(--accent);background:var(--white);box-shadow:0 0 0 3px rgba(62,139,255,.15);}
.select-input{background:var(--paper);border:1px solid var(--line-dark);padding:8px 12px;border-radius:10px;font-size:0.82rem;color:var(--navy-950);outline:none;cursor:pointer;}
.select-input:focus{border-color:var(--accent);}
.view-btn-group{display:flex;background:var(--paper);padding:3px;border-radius:10px;border:1px solid var(--line-dark);}
.view-btn{padding:6px 14px;border-radius:7px;border:none;background:transparent;font-size:0.8rem;font-weight:600;color:var(--muted-dark);cursor:pointer;transition:.2s ease;}
.view-btn.active{background:var(--navy-950);color:var(--white);}

/* LAYOUT GRID */
.calendar-layout{display:grid;grid-template-columns:1fr 340px;gap:24px;}
@media (max-width: 1024px){
  .calendar-layout{grid-template-columns:1fr;}
}

/* CALENDAR GRID CONTAINER */
.calendar-card{background:var(--white);border-radius:var(--radius);border:1px solid var(--line-dark);box-shadow:0 4px 20px rgba(8,23,51,.05);padding:24px;overflow:hidden;display:flex;flex-direction:column;}

/* MONTH GRID STYLING */
.month-header{display:grid;grid-template-columns:repeat(7,1fr);gap:8px;margin-bottom:12px;text-align:center;}
.day-name{font-family:var(--ff-mono);font-size:0.72rem;font-weight:600;letter-spacing:.08em;color:var(--muted-dark);text-transform:uppercase;padding:8px 0;}

.month-days-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:8px;flex:1;}
.day-cell{min-height:120px;background:var(--paper);border-radius:12px;border:1px solid rgba(8,23,51,.06);padding:8px;display:flex;flex-direction:column;gap:6px;transition:all .2s ease;position:relative;}
.day-cell:hover{border-color:var(--accent-soft);background:rgba(255,255,255,.9);}
.day-cell.other-month{opacity:0.35;background:#f9fafb;}
.day-cell.today{background:rgba(62,139,255,.05);border:2px solid var(--accent);}
.day-cell.today .day-number{color:var(--accent);font-weight:800;}
.day-cell.today::after{content:'TODAY';position:absolute;top:6px;right:8px;font-family:var(--ff-mono);font-size:0.55rem;background:var(--accent);color:var(--white);padding:1px 5px;border-radius:4px;font-weight:700;}

.day-header-number{display:flex;align-items:center;justify-content:space-between;margin-bottom:2px;}
.day-number{font-family:var(--ff-display);font-size:0.9rem;font-weight:700;color:var(--navy-950);}

.event-pills-list{display:flex;flex-direction:column;gap:4px;flex:1;overflow-y:auto;max-height:100px;scrollbar-width:thin;}

/* EVENT PILL */
.event-pill{background:var(--white);border-left:3.5px solid var(--accent);border-radius:6px;padding:5px 7px;box-shadow:0 2px 6px rgba(0,0,0,.04);cursor:pointer;transition:transform .15s ease, box-shadow .15s ease;display:flex;flex-direction:column;gap:2px;}
.event-pill:hover{transform:scale(1.02);box-shadow:0 4px 10px rgba(0,0,0,.08);z-index:2;}
.event-pill.cat-workshop{border-left-color:#3E8BFF;}
.event-pill.cat-symposium{border-left-color:#8b5cf6;}
.event-pill.cat-hackathon{border-left-color:#f59e0b;}
.event-pill.cat-lecture{border-left-color:#10b981;}

.event-pill-title{font-size:0.73rem;font-weight:700;color:var(--navy-950);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.event-pill-meta{font-family:var(--ff-mono);font-size:0.62rem;color:var(--muted-dark);display:flex;align-items:center;justify-content:space-between;}
.event-pill-reg{background:rgba(16,185,129,.15);color:#047857;padding:1px 4px;border-radius:4px;font-size:0.58rem;font-weight:600;}

/* LIST VIEW STYLING */
.list-view-container{display:flex;flex-direction:column;gap:14px;}
.event-card-item{background:var(--white);border:1px solid var(--line-dark);border-radius:14px;padding:18px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;transition:all .2s ease;box-shadow:0 2px 8px rgba(0,0,0,.02);}
.event-card-item:hover{border-color:var(--accent);box-shadow:0 6px 18px rgba(62,139,255,.12);transform:translateY(-2px);}
.event-date-badge{background:var(--navy-950);color:var(--white);border-radius:12px;padding:10px 16px;text-align:center;min-width:85px;flex-shrink:0;}
.event-date-badge .day{font-family:var(--ff-display);font-size:1.4rem;font-weight:800;line-height:1;}
.event-date-badge .month{font-family:var(--ff-mono);font-size:0.65rem;text-transform:uppercase;color:var(--accent-soft);margin-top:2px;}

.event-main-details{flex:1;}
.event-cat-tag{display:inline-block;font-family:var(--ff-mono);font-size:0.62rem;text-transform:uppercase;letter-spacing:.08em;padding:2px 8px;border-radius:999px;background:rgba(62,139,255,.12);color:var(--accent);font-weight:600;margin-bottom:6px;}
.event-title-text{font-family:var(--ff-display);font-size:1.05rem;font-weight:700;color:var(--navy-950);margin-bottom:4px;}
.event-sub-meta{font-size:0.8rem;color:var(--muted-dark);display:flex;align-items:center;gap:14px;flex-wrap:wrap;}
.event-desc-text{font-size:0.82rem;color:var(--muted-dark);margin-top:6px;line-height:1.4;}

.event-action-col{display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0;}

/* SIDEBAR WIDGETS */
.sidebar-col{display:flex;flex-direction:column;gap:20px;}
.side-card{background:var(--white);border-radius:var(--radius);border:1px solid var(--line-dark);padding:20px;box-shadow:0 4px 16px rgba(8,23,51,.03);}
.side-card-title{font-family:var(--ff-display);font-size:0.95rem;font-weight:700;color:var(--navy-950);margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;}

.stats-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.stat-box{background:var(--paper);border-radius:12px;padding:14px;text-align:center;border:1px solid rgba(8,23,51,.05);}
.stat-val{font-family:var(--ff-display);font-size:1.4rem;font-weight:800;color:var(--navy-950);}
.stat-lbl{font-family:var(--ff-mono);font-size:0.62rem;color:var(--muted-dark);text-transform:uppercase;margin-top:2px;}

.my-reg-item{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--paper-dim);}
.my-reg-item:last-child{border-bottom:none;}
.my-reg-dot{width:8px;height:8px;border-radius:50%;background:var(--green);flex-shrink:0;}
.my-reg-info b{font-size:0.82rem;color:var(--navy-950);display:block;}
.my-reg-info span{font-family:var(--ff-mono);font-size:0.68rem;color:var(--muted-dark);}

/* BUTTONS */
.btn-primary{background:linear-gradient(135deg,var(--accent),var(--navy-700));color:var(--white);border:none;padding:8px 16px;border-radius:10px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:.2s ease;display:inline-flex;align-items:center;gap:6px;}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 14px var(--accent-glow);}
.btn-ghost{background:transparent;border:1px solid var(--line-dark);color:var(--navy-900);padding:7px 14px;border-radius:10px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:.2s ease;}
.btn-ghost:hover{background:var(--paper-dim);}
.btn-danger{background:rgba(244,63,94,.1);border:1px solid rgba(244,63,94,.3);color:#e11d48;padding:7px 14px;border-radius:10px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:.2s ease;}
.btn-danger:hover{background:#e11d48;color:var(--white);}

/* MODAL STYLING */
.modal-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(8,23,51,.65);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;z-index:200;opacity:0;pointer-events:none;transition:opacity .25s ease;}
.modal-overlay.active{opacity:1;pointer-events:auto;}
.modal-card{background:var(--white);border-radius:20px;max-width:540px;width:90%;padding:28px;box-shadow:0 25px 50px -12px rgba(0,0,0,.3);transform:scale(0.95);transition:transform .25s ease;}
.modal-overlay.active .modal-card{transform:scale(1);}
.modal-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;}
.modal-title{font-family:var(--ff-display);font-size:1.25rem;font-weight:800;color:var(--navy-950);}
.modal-close{background:none;border:none;font-size:1.4rem;color:var(--muted-dark);cursor:pointer;line-height:1;}
.modal-body{display:flex;flex-direction:column;gap:14px;font-size:0.88rem;color:var(--navy-950);}
.modal-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;background:var(--paper);padding:14px;border-radius:12px;font-family:var(--ff-mono);font-size:0.75rem;}
.modal-footer{margin-top:20px;display:flex;justify-content:flex-end;gap:12px;}
@media(max-width:375px){
  .topbar{padding:12px 14px !important; flex-wrap:wrap; gap:8px;}
  .topbar-left{gap:8px; width:100%;}
  .topbar-right{gap:8px; flex-wrap:wrap;}
  .page-title{font-size:.9rem;}
  .month-header{gap:4px;}
  .day-name{font-size:.6rem; padding:4px 0;}
  .month-days-grid{gap:4px;}
  .day-cell{min-height:80px; padding:6px;}
  .day-number{font-size:.78rem;}
  .event-pill-title{font-size:.65rem;}
  .event-pill-meta{font-size:.55rem;}
  .calendar-card{padding:14px;}
  .filter-group{flex-direction:column; align-items:stretch;}
  .search-input{width:100%;}
  .select-input{width:100%;}
  .view-btn-group{width:100%; justify-content:center;}
  .event-card-item{flex-direction:column; align-items:flex-start; padding:14px;}
  .event-date-badge{min-width:70px; padding:8px 12px;}
  .event-date-badge .day{font-size:1.1rem;}
  .event-action-col{width:100%; flex-direction:row; justify-content:flex-end;}
  .side-card{padding:14px;}
  .modal-card{padding:20px; width:94%;}
  .modal-meta-grid{grid-template-columns:1fr; gap:6px;}
  .event-sub-meta{flex-direction:column; gap:6px;}
}
</style>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
  <div class="topbar-left">
    <div class="brand-logo">
      <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
    </div>
    <div class="brand-text">
      <b>AIMSA Event Calendar</b>
      <span>AI & ML Student Association Portal</span>
    </div>
  </div>
  <div class="topbar-right">
    <div class="user-badge" id="userBadge">
      <div class="user-avatar" id="userAvatar">A</div>
      <div class="user-info">
        <b id="userNameDisplay">Student Member</b>
        <span id="userZprnDisplay">125UAM1005</span>
      </div>
    </div>
    <a href="student_dashboard.php" class="btn-back">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Dashboard
    </a>
  </div>
</header>

<!-- MAIN CONTAINER -->
<main class="container">

  <!-- CONTROLS CARD -->
  <section class="controls-card">
    <div class="cal-nav-group">
      <button class="cal-nav-btn" id="btnPrevMonth" title="Previous Month">◄</button>
      <div class="current-month-title" id="currentMonthTitle">July 2026</div>
      <button class="cal-nav-btn" id="btnNextMonth" title="Next Month">►</button>
      <button class="btn-today" id="btnToday">Today</button>
    </div>

    <div class="filter-group">
      <input type="text" class="search-input" id="searchFilter" placeholder="🔍 Search events...">
      
      <select class="select-input" id="categoryFilter">
        <option value="all">All Categories</option>
        <option value="Workshop">Workshops</option>
        <option value="Symposium">Symposiums</option>
        <option value="Hackathon">Hackathons</option>
        <option value="Guest Lecture">Guest Lectures</option>
      </select>

      <select class="select-input" id="statusFilter">
        <option value="all">All Events</option>
        <option value="registered">Registered</option>
        <option value="open">Open for Reg</option>
      </select>

      <div class="view-btn-group">
        <button class="view-btn active" id="btnGridView" onclick="switchView('grid')">📅 Grid</button>
        <button class="view-btn" id="btnListView" onclick="switchView('list')">📋 List</button>
      </div>
    </div>
  </section>

  <!-- LAYOUT GRID -->
  <div class="calendar-layout">
    
    <!-- MAIN CALENDAR CONTAINER -->
    <div class="calendar-card">
      
      <!-- GRID VIEW -->
      <div id="gridViewSection">
        <div class="month-header">
          <div class="day-name">MON</div>
          <div class="day-name">TUE</div>
          <div class="day-name">WED</div>
          <div class="day-name">THU</div>
          <div class="day-name">FRI</div>
          <div class="day-name">SAT</div>
          <div class="day-name">SUN</div>
        </div>

        <div class="month-days-grid" id="monthDaysGrid">
          <!-- Calendar Days rendered via JS -->
        </div>
      </div>

      <!-- LIST VIEW (Hidden by default) -->
      <div id="listViewSection" style="display:none;">
        <div class="list-view-container" id="listViewContainer">
          <!-- List View rendered via JS -->
        </div>
      </div>

    </div>

    <!-- SIDEBAR -->
    <aside class="sidebar-col">
      
      <!-- MONTH STATS -->
      <div class="side-card">
        <div class="side-card-title">Calendar Summary</div>
        <div class="stats-grid">
          <div class="stat-box">
            <div class="stat-val" id="statApprovedCount">0</div>
            <div class="stat-lbl">Approved Events</div>
          </div>
          <div class="stat-box">
            <div class="stat-val" id="statMyRegCount">0</div>
            <div class="stat-lbl">My Registered</div>
          </div>
        </div>
      </div>

      <!-- MY REGISTERED EVENTS -->
      <div class="side-card">
        <div class="side-card-title">My Registered Events</div>
        <div id="myRegisteredList">
          <p style="font-size:0.8rem; color:var(--muted-dark);">Loading your registrations...</p>
        </div>
      </div>

      <!-- UPCOMING FEATURED EVENT -->
      <div class="side-card" style="background:linear-gradient(135deg,var(--navy-950),var(--navy-900));color:var(--white);">
        <div class="side-card-title" style="color:var(--white);">Next Flagship Event</div>
        <div id="nextFeaturedEvent">
          <p style="font-size:0.8rem; color:var(--muted);">Loading next event...</p>
        </div>
      </div>

    </aside>
  </div>

</main>

<!-- EVENT DETAIL MODAL -->
<div class="modal-overlay" id="eventModal">
  <div class="modal-card">
    <div class="modal-header">
      <div>
        <span class="event-cat-tag" id="modalCategoryTag">WORKSHOP</span>
        <h3 class="modal-title" id="modalTitle">Event Title</h3>
      </div>
      <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body">
      <p id="modalDescription" style="color:var(--muted-dark); line-height:1.5;">Event details description text here...</p>
      
      <div class="modal-meta-grid">
        <div>📅 <b>Date:</b> <span id="modalDate">-</span></div>
        <div>📍 <b>Venue:</b> <span id="modalVenue">-</span></div>
        <div>👤 <b>Coordinator:</b> <span id="modalCoordinator">-</span></div>
        <div>👥 <b>Registered:</b> <span id="modalRegCount">0</span> attendees</div>
      </div>
    </div>
    <div class="modal-footer" id="modalFooter">
      <button class="btn-ghost" onclick="closeModal()">Close</button>
      <div id="modalActionBtn"></div>
    </div>
  </div>
</div>

<script>
// State Management
let sessionUser = <?php echo json_encode($sessionUser); ?> || JSON.parse(sessionStorage.getItem('current_user')) || {
  email: 'student@zealeducation.com',
  name: 'Arjun Patil',
  role: 'Student Member',
  branch: 'AI & ML',
  batch: '2026',
  zprn: '125UAM1005'
};

let currentDate = new Date();
let eventsData = [];
let registeredEvents = [];
let activeView = 'grid';

// Initialize UI
document.addEventListener('DOMContentLoaded', () => {
  renderUserBadge();
  fetchCalendarData();

  document.getElementById('btnPrevMonth').addEventListener('click', () => changeMonth(-1));
  document.getElementById('btnNextMonth').addEventListener('click', () => changeMonth(1));
  document.getElementById('btnToday').addEventListener('click', jumpToToday);

  document.getElementById('searchFilter').addEventListener('input', renderCalendar);
  document.getElementById('categoryFilter').addEventListener('change', renderCalendar);
  document.getElementById('statusFilter').addEventListener('change', renderCalendar);
});

function renderUserBadge() {
  if (!sessionUser) return;
  document.getElementById('userNameDisplay').textContent = sessionUser.name || 'Student Member';
  document.getElementById('userZprnDisplay').textContent = sessionUser.zprn || sessionUser.email || '';
  document.getElementById('userAvatar').textContent = (sessionUser.name || 'S').charAt(0).toUpperCase();
}

async function fetchCalendarData() {
  try {
    const res = await fetch(`ajax/student_actions.php?action=getDashboardData&email=${encodeURIComponent(sessionUser.email)}`);
    const data = await res.json();
    if (data.status === 'success') {
      eventsData = data.upcoming_events || [];
      registeredEvents = data.registered_events || [];
      renderCalendar();
      renderSidebar();
    }
  } catch (e) {
    console.error('Failed to load calendar data:', e);
  }
}

function changeMonth(delta) {
  currentDate.setMonth(currentDate.getMonth() + delta);
  renderCalendar();
}

function jumpToToday() {
  currentDate = new Date();
  renderCalendar();
}

function switchView(view) {
  activeView = view;
  document.getElementById('btnGridView').classList.toggle('active', view === 'grid');
  document.getElementById('btnListView').classList.toggle('active', view === 'list');
  document.getElementById('gridViewSection').style.display = view === 'grid' ? 'block' : 'none';
  document.getElementById('listViewSection').style.display = view === 'list' ? 'block' : 'none';
  renderCalendar();
}

function getFilteredEvents() {
  const query = document.getElementById('searchFilter').value.toLowerCase().trim();
  const category = document.getElementById('categoryFilter').value;
  const status = document.getElementById('statusFilter').value;

  return eventsData.filter(e => {
    const title = (e.title || e.name || '').toLowerCase();
    const loc = (e.location || e.venue || '').toLowerCase();
    const matchesSearch = !query || title.includes(query) || loc.includes(query);

    const matchesCat = category === 'all' || (e.category && e.category.toLowerCase() === category.toLowerCase());

    const isReg = registeredEvents.some(r => r.event_id == e.id || r.event_name.toLowerCase() === (e.title || e.name || '').toLowerCase());
    const matchesStatus = status === 'all' || (status === 'registered' && isReg) || (status === 'open' && !isReg);

    return matchesSearch && matchesCat && matchesStatus;
  });
}

function renderCalendar() {
  const filtered = getFilteredEvents();
  const year = currentDate.getFullYear();
  const month = currentDate.getMonth();

  // Update Title
  const monthName = currentDate.toLocaleString('en-US', { month: 'long', year: 'numeric' });
  document.getElementById('currentMonthTitle').textContent = monthName;

  if (activeView === 'grid') {
    renderGrid(year, month, filtered);
  } else {
    renderList(filtered);
  }

  // Update Stats
  document.getElementById('statApprovedCount').textContent = filtered.length;
  document.getElementById('statMyRegCount').textContent = registeredEvents.length;
}

function renderGrid(year, month, events) {
  const grid = document.getElementById('monthDaysGrid');
  grid.innerHTML = '';

  const firstDay = new Date(year, month, 1).getDay(); // 0 is Sun
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const daysInPrevMonth = new Date(year, month, 0).getDate();

  // Offset for Monday start (0=Mon, 6=Sun)
  let offset = firstDay === 0 ? 6 : firstDay - 1;

  const today = new Date();
  const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;

  // Prev Month trailing days
  for (let i = offset - 1; i >= 0; i--) {
    const prevDayNum = daysInPrevMonth - i;
    grid.appendChild(createDayCell(prevDayNum, true));
  }

  // Current Month days
  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    const dayEvts = events.filter(e => e.event_date === dateStr || e.date === dateStr);
    const isToday = isCurrentMonth && d === today.getDate();

    const cell = createDayCell(d, false, isToday, dayEvts);
    grid.appendChild(cell);
  }

  // Next Month leading days to fill grid 35 or 42
  const totalRendered = offset + daysInMonth;
  const targetTotal = totalRendered > 35 ? 42 : 35;
  for (let nextD = 1; nextD <= (targetTotal - totalRendered); nextD++) {
    grid.appendChild(createDayCell(nextD, true));
  }
}

function createDayCell(dayNum, isOtherMonth, isToday = false, dayEvents = []) {
  const cell = document.createElement('div');
  cell.className = 'day-cell' + (isOtherMonth ? ' other-month' : '') + (isToday ? ' today' : '');

  let html = `
    <div class="day-header-number">
      <span class="day-number">${dayNum}</span>
    </div>
    <div class="event-pills-list">
  `;

  if (!isOtherMonth && dayEvents.length > 0) {
    dayEvents.forEach(evt => {
      const evtTitle = evt.title || evt.name || 'Event';
      const isReg = registeredEvents.some(r => r.event_id == evt.id || r.event_name.toLowerCase() === evtTitle.toLowerCase());
      const catClass = getCategoryClass(evt.category);

      html += `
        <div class="event-pill ${catClass}" onclick="openEventModal(${evt.id}); event.stopPropagation();">
          <div class="event-pill-title">${escapeHtml(evtTitle)}</div>
          <div class="event-pill-meta">
            <span>📍 ${escapeHtml(evt.location || evt.venue || 'Campus')}</span>
            ${isReg ? `<span class="event-pill-reg">✓ Reg</span>` : ''}
          </div>
        </div>
      `;
    });
  }

  html += `</div>`;
  cell.innerHTML = html;
  return cell;
}

function getCategoryClass(cat) {
  if (!cat) return 'cat-workshop';
  const c = cat.toLowerCase();
  if (c.includes('workshop')) return 'cat-workshop';
  if (c.includes('symposium')) return 'cat-symposium';
  if (c.includes('hack')) return 'cat-hackathon';
  if (c.includes('lecture') || c.includes('guest')) return 'cat-lecture';
  return 'cat-workshop';
}

function renderList(events) {
  const container = document.getElementById('listViewContainer');
  if (events.length === 0) {
    container.innerHTML = `<p style="font-size:0.85rem; color:var(--muted-dark); text-align:center; padding:30px 0;">No events matching filters for this view.</p>`;
    return;
  }

  container.innerHTML = events.map(e => {
    const evtTitle = e.title || e.name || 'Event';
    const evtDate = new Date(e.event_date || e.date || Date.now());
    const day = evtDate.getDate();
    const monthStr = evtDate.toLocaleString('en-US', { month: 'short' });
    const isReg = registeredEvents.some(r => r.event_id == e.id || r.event_name.toLowerCase() === evtTitle.toLowerCase());

    return `
      <div class="event-card-item">
        <div class="event-date-badge">
          <div class="day">${day}</div>
          <div class="month">${monthStr}</div>
        </div>
        <div class="event-main-details">
          <span class="event-cat-tag">${escapeHtml(e.category || 'General')}</span>
          <h4 class="event-title-text">${escapeHtml(evtTitle)}</h4>
          <div class="event-sub-meta">
            <span>📅 ${e.event_date || e.date}</span>
            <span>📍 ${escapeHtml(e.location || e.venue || 'Campus')}</span>
            <span>👥 ${e.registrations_count || 0} Registered</span>
          </div>
          <p class="event-desc-text">${escapeHtml(e.description || 'Departmental AIMSA activity.')}</p>
        </div>
        <div class="event-action-col">
          ${isReg ? `<span class="event-pill-reg" style="font-size:0.75rem; padding:4px 10px;">✓ Registered</span>
                     <button class="btn-danger" onclick="cancelRegistration(${e.id}, '${escapeJs(evtTitle)}')">Cancel</button>`
                  : `<button class="btn-primary" onclick="registerEvent(${e.id}, '${escapeJs(evtTitle)}')">Register Now</button>`}
          <button class="btn-ghost" style="padding:4px 10px; font-size:0.75rem;" onclick="openEventModal(${e.id})">Details →</button>
        </div>
      </div>
    `;
  }).join('');
}

function renderSidebar() {
  const regContainer = document.getElementById('myRegisteredList');
  if (registeredEvents.length === 0) {
    regContainer.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark);">No registered events yet.</p>`;
  } else {
    regContainer.innerHTML = registeredEvents.map(r => `
      <div class="my-reg-item">
        <div class="my-reg-dot"></div>
        <div class="my-reg-info">
          <b>${escapeHtml(r.event_name)}</b>
          <span>${r.event_date || 'Scheduled'} · <span style="color:#e11d48; cursor:pointer; font-weight:600;" onclick="cancelRegistration(${r.event_id}, '${escapeJs(r.event_name)}')">Cancel</span></span>
        </div>
      </div>
    `).join('');
  }

  // Next Featured Event
  const featured = eventsData.find(e => new Date(e.event_date || e.date) >= new Date()) || eventsData[0];
  const featContainer = document.getElementById('nextFeaturedEvent');
  if (featured) {
    const fTitle = featured.title || featured.name;
    const isReg = registeredEvents.some(r => r.event_id == featured.id || r.event_name.toLowerCase() === fTitle.toLowerCase());
    featContainer.innerHTML = `
      <div style="font-family:var(--ff-display); font-weight:700; font-size:1.05rem; margin-bottom:6px;">${escapeHtml(fTitle)}</div>
      <div style="font-family:var(--ff-mono); font-size:0.72rem; color:var(--accent-soft); margin-bottom:10px;">
        📅 ${featured.event_date || featured.date} | 📍 ${escapeHtml(featured.location || featured.venue || 'Campus')}
      </div>
      <p style="font-size:0.78rem; color:var(--muted); margin-bottom:14px; line-height:1.4;">${escapeHtml(featured.description || 'Departmental event.')}</p>
      ${isReg ? `<button class="btn-ghost" style="width:100%; color:var(--white); border-color:rgba(255,255,255,.2);" onclick="cancelRegistration(${featured.id}, '${escapeJs(fTitle)}')">Cancel Registration</button>`
              : `<button class="btn-primary" style="width:100%; justify-content:center;" onclick="registerEvent(${featured.id}, '${escapeJs(fTitle)}')">Register Now →</button>`}
    `;
  } else {
    featContainer.innerHTML = `<p style="font-size:0.8rem; color:var(--muted);">No upcoming events.</p>`;
  }
}

// Modal Functions
function openEventModal(eventId) {
  const event = eventsData.find(e => e.id == eventId);
  if (!event) return;

  const evtTitle = event.title || event.name;
  const isReg = registeredEvents.some(r => r.event_id == event.id || r.event_name.toLowerCase() === evtTitle.toLowerCase());

  document.getElementById('modalCategoryTag').textContent = (event.category || 'General').toUpperCase();
  document.getElementById('modalTitle').textContent = evtTitle;
  document.getElementById('modalDescription').textContent = event.description || 'No detailed description provided.';
  document.getElementById('modalDate').textContent = event.event_date || event.date || 'TBA';
  document.getElementById('modalVenue').textContent = event.location || event.venue || 'Main Campus';
  document.getElementById('modalCoordinator').textContent = event.created_by || 'AIMSA Committee';
  document.getElementById('modalRegCount').textContent = event.registrations_count || 0;

  const btnContainer = document.getElementById('modalActionBtn');
  if (isReg) {
    btnContainer.innerHTML = `<button class="btn-danger" onclick="cancelRegistration(${event.id}, '${escapeJs(evtTitle)}'); closeModal();">Cancel Registration</button>`;
  } else {
    btnContainer.innerHTML = `<button class="btn-primary" onclick="registerEvent(${event.id}, '${escapeJs(evtTitle)}'); closeModal();">Register for Event</button>`;
  }

  document.getElementById('eventModal').classList.add('active');
}

function closeModal() {
  document.getElementById('eventModal').classList.remove('active');
}

// AJAX Actions
async function registerEvent(eventId, eventName) {
  const formData = new FormData();
  formData.append('action', 'registerEvent');
  formData.append('email', sessionUser.email);
  formData.append('event_id', eventId);
  formData.append('event_name', eventName);

  try {
    const res = await fetch('ajax/student_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      fetchCalendarData();
    } else {
      alert(data.message || 'Failed to register');
    }
  } catch (e) {
    alert('Error connecting to server');
  }
}

async function cancelRegistration(eventId, eventName) {
  if (!confirm(`Cancel your registration for "${eventName}"?`)) return;

  const formData = new FormData();
  formData.append('action', 'cancelRegistration');
  formData.append('email', sessionUser.email);
  formData.append('event_id', eventId);
  formData.append('event_name', eventName);

  try {
    const res = await fetch('ajax/student_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      fetchCalendarData();
    } else {
      alert(data.message || 'Failed to cancel registration');
    }
  } catch (e) {
    alert('Error connecting to server');
  }
}

function escapeHtml(str) {
  if (!str) return '';
  return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function escapeJs(str) {
  if (!str) return '';
  return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
}
</script>
</body>
</html>
