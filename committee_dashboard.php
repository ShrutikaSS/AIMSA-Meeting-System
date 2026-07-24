<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/include/dbConfig.php';

$sessionUser = $_SESSION['user'] ?? null;
if (!$sessionUser || !in_array($sessionUser['role'] ?? '', ['Committee Member', 'Association President', 'President', 'Faculty Coordinator', 'HOD'])) {
    header("Location: index.php?auth_error=" . urlencode("Unauthorized access. Please login with Committee Member credentials."));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Committee Member Dashboard — AIMSA Portal</title>
<meta name="description" content="Committee Member portal dashboard for AIMSA — AI & ML Student Association.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
:root{
  --ink:#050d1a;--navy-950:#081733;--navy-900:#0c2148;--navy-800:#123163;--navy-700:#1a4180;
  --accent:#3E8BFF;--accent-soft:#7fb0ff;--accent-glow:rgba(62,139,255,.45);
  --white:#ffffff;--paper:#f4f7fc;--paper-dim:#e7edf7;--muted:#93a7c9;--muted-dark:#5b6d8c;
  --line:rgba(255,255,255,.12);--line-dark:rgba(8,23,51,.1);--radius:18px;
  --shadow-lg:0 30px 60px -25px rgba(6,16,35,.55);--sidebar-w:260px;
  --ff-display:'Sora',sans-serif;--ff-body:'Inter',sans-serif;--ff-mono:'JetBrains Mono',monospace;
}
*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:var(--ff-body);background:var(--paper);color:var(--navy-950);overflow-x:hidden;-webkit-font-smoothing:antialiased;display:flex;min-height:100vh;}
a{color:inherit;text-decoration:none;}ul{list-style:none;}button{font-family:inherit;cursor:pointer;}
.sidebar{width:var(--sidebar-w);flex-shrink:0;background:var(--navy-950);min-height:100vh;position:fixed;left:0;top:0;bottom:0;display:flex;flex-direction:column;border-right:1px solid var(--line);z-index:100;transition:transform .3s ease;}
.sidebar-brand{padding:24px 22px 20px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:12px;}
.brand-logo{width:42px;height:42px;border-radius:50%;background:var(--white);display:flex;align-items:center;justify-content:center;overflow:hidden;border:2px solid rgba(255,255,255,.3);flex-shrink:0;}
.brand-logo svg{width:22px;height:22px;stroke:var(--navy-950);fill:none;stroke-width:1.8;}
.brand-info b{font-family:var(--ff-display);color:var(--white);font-size:.95rem;display:block;}
.brand-info span{font-family:var(--ff-mono);font-size:.58rem;letter-spacing:.14em;color:var(--accent-soft);text-transform:uppercase;}
.sidebar-role{padding:16px 22px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:12px;}
.role-avatar{width:40px;height:40px;border-radius:50%;background:conic-gradient(from 180deg,var(--accent),var(--navy-700),var(--accent));padding:2.5px;flex-shrink:0;}
.role-avatar .in{width:100%;height:100%;border-radius:50%;background:var(--navy-800);display:flex;align-items:center;justify-content:center;font-family:var(--ff-display);font-weight:700;color:var(--white);font-size:.9rem;}
.role-info b{color:var(--white);font-size:.85rem;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.role-info span{font-family:var(--ff-mono);font-size:.6rem;letter-spacing:.1em;color:var(--accent-soft);text-transform:uppercase;}
.sidebar-nav{flex:1;padding:14px 12px;overflow-y:auto;}
.nav-section-label{font-family:var(--ff-mono);font-size:.58rem;letter-spacing:.18em;text-transform:uppercase;color:var(--muted);padding:10px 10px 6px;}
.nav-item{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;color:var(--muted);font-size:.87rem;font-weight:500;transition:all .2s ease;cursor:pointer;position:relative;border:1px solid transparent;}
.nav-item:hover{background:rgba(255,255,255,.06);color:var(--white);border-color:var(--line);}
.nav-item.active{background:linear-gradient(135deg,rgba(62,139,255,.18),rgba(62,139,255,.06));color:var(--white);border-color:rgba(62,139,255,.3);}
.nav-item.active::before{content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);width:3px;height:60%;background:var(--accent);border-radius:999px;}
.nav-icon{width:18px;height:18px;flex-shrink:0;stroke:currentColor;fill:none;stroke-width:1.8;}
.nav-badge{margin-left:auto;font-family:var(--ff-mono);font-size:.6rem;background:var(--accent);color:var(--white);padding:2px 7px;border-radius:999px;}
.sidebar-footer{padding:16px 12px;border-top:1px solid var(--line);}
.main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;}
.topbar{background:rgba(255,255,255,.95);backdrop-filter:blur(12px);border-bottom:1px solid var(--line-dark);padding:0 32px;height:64px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
.topbar-left{display:flex;align-items:center;gap:14px;}
.page-title{font-family:var(--ff-display);font-size:1.15rem;font-weight:700;}
.breadcrumb{font-family:var(--ff-mono);font-size:.68rem;letter-spacing:.08em;color:var(--muted-dark);text-transform:uppercase;}
.topbar-right{display:flex;align-items:center;gap:14px;}
.topbar-icon-btn{width:36px;height:36px;border-radius:10px;border:1px solid var(--line-dark);background:transparent;display:flex;align-items:center;justify-content:center;transition:.2s ease;position:relative;}
.topbar-icon-btn:hover{background:var(--navy-950);}
.topbar-icon-btn:hover svg{stroke:var(--white);}
.topbar-icon-btn svg{width:18px;height:18px;stroke:var(--navy-800);fill:none;stroke-width:1.8;}
.notif-dot{position:absolute;top:7px;right:7px;width:7px;height:7px;border-radius:50%;background:var(--accent);border:2px solid var(--white);}
.hamburger-btn{display:none;width:36px;height:36px;border-radius:10px;border:1px solid var(--line-dark);background:transparent;align-items:center;justify-content:center;flex-direction:column;gap:4px;}
.hamburger-btn span{width:18px;height:2px;background:var(--navy-800);border-radius:2px;display:block;}
.content{padding:32px;flex:1;}
.section-eyebrow{font-family:var(--ff-mono);font-size:.68rem;letter-spacing:.18em;text-transform:uppercase;color:var(--accent);display:flex;align-items:center;gap:10px;margin-bottom:8px;}
.section-eyebrow::before{content:'';width:22px;height:1px;background:var(--accent);}
.content-title{font-family:var(--ff-display);font-size:1.6rem;font-weight:700;margin-bottom:4px;}
.content-sub{color:var(--muted-dark);font-size:.9rem;margin-bottom:32px;}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:32px;}
.stat-card{background:var(--white);border-radius:16px;padding:22px 24px;border:1px solid var(--line-dark);transition:transform .2s ease,box-shadow .3s ease;position:relative;overflow:hidden;}
.stat-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);}
.stat-card::after{content:'';position:absolute;right:-30px;top:-30px;width:100px;height:100px;border-radius:50%;background:radial-gradient(circle,var(--accent-glow),transparent 70%);opacity:.4;}
.stat-icon{width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,var(--navy-950),var(--navy-800));display:flex;align-items:center;justify-content:center;margin-bottom:16px;transition:transform .3s cubic-bezier(.34,1.56,.64,1);}
.stat-card:hover .stat-icon{transform:scale(1.12) rotate(-6deg);background:linear-gradient(135deg,var(--accent),#2563eb);}
.stat-icon svg{width:20px;height:20px;stroke:var(--white);fill:none;stroke-width:1.8;}
.stat-val{font-family:var(--ff-display);font-size:2rem;font-weight:700;display:block;}
.stat-label{font-size:.8rem;color:var(--muted-dark);margin-top:2px;}
.stat-delta{font-family:var(--ff-mono);font-size:.65rem;margin-top:8px;display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:999px;}
.stat-delta.up{background:rgba(34,197,94,.1);color:#16a34a;}
.stat-delta.dn{background:rgba(239,68,68,.1);color:#dc2626;}
.dash-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:24px;margin-bottom:24px;}
.dash-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:24px;}
.card{background:var(--white);border-radius:var(--radius);border:1px solid var(--line-dark);padding:24px;}
.card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.card-title{font-family:var(--ff-display);font-size:1rem;font-weight:700;}
.card-action{font-family:var(--ff-mono);font-size:.65rem;letter-spacing:.08em;color:var(--accent);text-transform:uppercase;cursor:pointer;padding:5px 10px;border-radius:999px;border:1px solid rgba(62,139,255,.3);transition:.2s ease;}
.card-action:hover{background:var(--accent);color:var(--white);}
.data-table{width:100%;border-collapse:collapse;}
.data-table th{font-family:var(--ff-mono);font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted-dark);padding:8px 12px;text-align:left;border-bottom:1px solid var(--line-dark);}
.data-table td{padding:11px 12px;font-size:.85rem;border-bottom:1px solid rgba(8,23,51,.05);}
.data-table tr:last-child td{border-bottom:none;}
.data-table tr:hover td{background:var(--paper);}
.badge{font-family:var(--ff-mono);font-size:.6rem;letter-spacing:.06em;padding:4px 10px;border-radius:999px;text-transform:uppercase;display:inline-block;}
.badge-green{background:rgba(34,197,94,.1);color:#16a34a;border:1px solid rgba(34,197,94,.2);}
.badge-blue{background:rgba(62,139,255,.1);color:var(--accent);border:1px solid rgba(62,139,255,.2);}
.badge-orange{background:rgba(249,115,22,.1);color:#ea580c;border:1px solid rgba(249,115,22,.2);}
.badge-gray{background:var(--paper-dim);color:var(--muted-dark);border:1px solid var(--line-dark);}
.list-item{display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid rgba(8,23,51,.05);}
.list-item:last-child{border-bottom:none;}
.list-dot{width:9px;height:9px;border-radius:50%;background:var(--accent);flex-shrink:0;box-shadow:0 0 0 3px rgba(62,139,255,.18);}
.list-text{flex:1;}
.list-text b{font-size:.88rem;display:block;margin-bottom:2px;}
.list-text span{font-size:.76rem;color:var(--muted-dark);font-family:var(--ff-mono);}
/* Attendance grid */
.attend-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:6px;margin-bottom:12px;}
.attend-day{height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-family:var(--ff-mono);font-size:.62rem;font-weight:600;}
.attend-present{background:rgba(62,139,255,.15);color:var(--accent);border:1px solid rgba(62,139,255,.25);}
.attend-absent{background:rgba(239,68,68,.08);color:#dc2626;border:1px solid rgba(239,68,68,.15);}
.attend-upcoming{background:var(--paper-dim);color:var(--muted-dark);border:1px solid var(--line-dark);}
/* Certificate cards */
.cert-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;}
.cert-card{background:linear-gradient(135deg,var(--navy-950),var(--navy-800));border-radius:14px;padding:20px;color:var(--white);border:1px solid var(--line);position:relative;overflow:hidden;transition:transform .2s ease,box-shadow .3s ease;}
.cert-card:hover{transform:translateY(-4px);box-shadow:0 20px 40px -14px var(--accent-glow);}
.cert-card::after{content:'';position:absolute;right:-40px;top:-40px;width:120px;height:120px;border-radius:50%;background:radial-gradient(circle,var(--accent-glow),transparent 70%);}
.cert-card .cert-icon{width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;margin-bottom:14px;position:relative;z-index:1;}
.cert-card .cert-icon svg{width:20px;height:20px;stroke:var(--accent-soft);fill:none;stroke-width:1.8;}
.cert-card h4{font-size:.9rem;margin-bottom:4px;font-family:var(--ff-display);position:relative;z-index:1;}
.cert-card span{font-family:var(--ff-mono);font-size:.65rem;color:var(--muted);position:relative;z-index:1;}
.cert-download{margin-top:14px;font-family:var(--ff-mono);font-size:.62rem;letter-spacing:.06em;text-transform:uppercase;color:var(--accent-soft);cursor:pointer;display:inline-flex;align-items:center;gap:6px;position:relative;z-index:1;}
.cert-download:hover{color:var(--white);}
/* Task item */
.task-item{display:flex;align-items:flex-start;gap:12px;padding:12px;border-radius:10px;border:1px solid var(--line-dark);background:var(--paper);transition:.2s ease;margin-bottom:8px;}
.task-item:hover{border-color:var(--accent);background:var(--white);}
.task-check{width:18px;height:18px;border-radius:4px;border:2px solid var(--line-dark);flex-shrink:0;margin-top:2px;cursor:pointer;transition:.2s;}
.task-check.done{background:var(--accent);border-color:var(--accent);display:flex;align-items:center;justify-content:center;}
.task-check.done::after{content:'✓';color:var(--white);font-size:.65rem;font-weight:700;}
.task-info b{font-size:.85rem;display:block;margin-bottom:2px;}
.task-info span{font-size:.73rem;color:var(--muted-dark);font-family:var(--ff-mono);}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(5,13,26,.5);z-index:99;}
.btn{display:inline-flex;align-items:center;gap:8px;justify-content:center;padding:10px 20px;border-radius:999px;font-weight:600;font-size:.84rem;border:1px solid transparent;transition:all .25s ease;cursor:pointer;}
.btn-primary{background:linear-gradient(135deg,var(--accent),#2563eb);color:var(--white);box-shadow:0 8px 20px -8px var(--accent-glow);}
.btn-primary:hover{transform:translateY(-2px);}
.btn-ghost{background:transparent;border-color:var(--line-dark);color:var(--navy-800);}
.btn-ghost:hover{background:var(--paper-dim);}

/* Header Topbar Search Bar & Logo styles */
.header-search-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--paper);
  border: 1.5px solid var(--line-dark);
  border-radius: 99px;
  padding: 6px 14px;
  width: min(280px, 100%);
  transition: border-color 0.2s;
}
.header-search-bar:focus-within {
  border-color: var(--accent);
}
.header-search-bar input {
  background: transparent;
  border: none;
  font-size: 0.8rem;
  font-family: inherit;
  color: var(--navy-950);
  outline: none;
  width: 100%;
}
.logo-container {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-right: 12px;
}
.logo-container svg {
  transition: transform 0.2s;
}
.logo-container svg:hover {
  transform: scale(1.1);
}

/* Drawer Panel styles */
.drawer{
  position:fixed; top:0; right:-450px; width:min(420px, 100%); height:100vh;
  background:var(--white); border-left:1px solid var(--line-dark); z-index:200;
  transition:right 0.3s cubic-bezier(0.16, 1, 0.3, 1); padding:30px;
  display:flex; flex-direction:column; gap:20px; overflow-y:auto;
  box-shadow: -10px 0 30px -10px rgba(0,0,0,0.15);
}
.drawer.open{right:0;}
.drawer-header{display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--line-dark); padding-bottom:15px; margin-bottom:10px;}
.drawer-title{font-family:var(--ff-display); font-size:1.15rem; font-weight:700;}
.drawer-close{background:none; border:none; font-size:1.5rem; color:var(--muted-dark); cursor:pointer;}
.form-group{display:flex; flex-direction:column; gap:6px; margin-bottom:12px;}
.form-group label{font-size:0.8rem; font-weight:600; color:var(--navy-950);}
.form-group input, .form-group select, .form-group textarea{
  width:100%; padding:10px 12px; border-radius:8px; border:1.5px solid var(--line-dark); font-size:0.88rem; font-family:inherit;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus{
  outline:none; border-color:var(--accent);
}

@media(max-width:1100px){.stats-grid{grid-template-columns:repeat(2,1fr);}.dash-grid{grid-template-columns:1fr;}.dash-grid-3{grid-template-columns:1fr 1fr;}}
@media(max-width:768px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}.sidebar-overlay.open{display:block;}.main{margin-left:0;}.hamburger-btn{display:flex;}.content{padding:20px;}.stats-grid{grid-template-columns:1fr 1fr;}.dash-grid-3{grid-template-columns:1fr;}.cert-grid{grid-template-columns:1fr;}}
@media(max-width:480px){.stats-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo" style="width:42px; height:42px; border-radius:50%; background:var(--white); display:flex; align-items:center; justify-content:center; overflow:hidden; border:2px solid rgba(255,255,255,.3); flex-shrink:0;">
      <img src="images/aimsa_logo.jpg" alt="AIMSA Logo" style="width:100%; height:100%; object-fit:cover;">
    </div>
    <div class="brand-info"><b>AIMSA Portal</b><span>Committee Access</span></div>
  </div>
  <div class="sidebar-role">
    <div class="role-avatar"><div class="in">CM</div></div>
    <div class="role-info"><b>Riya Desai</b><span>Committee Member</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <a class="nav-item active" href="#" onclick="openDashboardView(); return false;" id="navDashboard">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard
    </a>
    <div class="nav-section-label">Events</div>
    <a class="nav-item" href="#" onclick="openAssignedEventsWindow(); return false;" id="navAssigned">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Assigned Events
    </a>
    <a class="nav-item" href="#" onclick="openAttendanceWindow(); return false;" id="navAttendance">
      <svg class="nav-icon" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Attendance Records
    </a>
    <a class="nav-item" href="#" onclick="openReportsWindow(); return false;" id="navReports">
      <svg class="nav-icon" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>Event Reports
    </a>
    <div class="nav-section-label">Communication</div>
    <a class="nav-item" href="#" onclick="openNotificationsWindow(); return false;" id="navNotifications">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>Notifications
      <span class="nav-badge" id="navNotifBadge">0</span>
    </a>
    <div class="nav-section-label">Account</div>
    <a class="nav-item" href="#" onclick="openProfileWindow(); return false;" id="navProfile"><svg class="nav-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>Profile</a>
  </nav>
  <div class="sidebar-footer">
    <a class="nav-item" href="index.php" onclick="sessionStorage.removeItem('current_user');"><svg class="nav-icon" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>Logout</a>
  </div>
</aside>

<div class="main">
  <div class="topbar" style="justify-content:space-between; padding:15px 30px; background:var(--white); border-bottom:1px solid var(--line-dark);">
    <div class="topbar-left" style="display:flex; align-items:center; gap:16px;">
      <button class="hamburger-btn" id="hamburgerBtn" style="margin-right:8px;"><span></span><span></span><span></span></button>
      <div class="logo-container" style="display:flex; align-items:center; gap:8px;">
        <!-- College Logo -->
        <img src="images/icons/college_logo.png" alt="Zeal Logo" style="height:32px; width:32px; border-radius:50%; object-fit:cover;" title="Zeal Education Society">
        <!-- AIML Dept Logo -->
        <img src="images/aimsa_logo.jpg" alt="AIMSA Logo" style="height:32px; width:auto; border-radius:50%; object-fit:contain;" title="AIMSA Association">
      </div>
      <div>
        <div class="page-title" style="font-family:var(--ff-display); font-size:1.05rem; font-weight:800; color:var(--navy-950);">AIMSA Portal</div>
        <div class="breadcrumb" style="font-size:0.68rem; color:var(--muted-dark);">AI &amp; ML Department (Committee)</div>
      </div>
    </div>

    <!-- Center Search Bar -->
    <div class="header-search-bar">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted-dark)" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="headerSearchInput" placeholder="Search events, attendance..." data-i18n-ph="dash.search_ph">
    </div>

    <div class="topbar-right" style="display:flex; align-items:center; gap:20px;">
      <!-- Live clock badge -->
      <div style="font-family:var(--ff-mono); font-size:0.72rem; color:var(--accent); background:rgba(62,139,255,0.1); padding:5px 12px; border-radius:999px; border:1px solid rgba(62,139,255,0.25); display:inline-flex; align-items:center; gap:6px;">
        <span style="width:7px; height:7px; border-radius:50%; background:#22c55e; display:inline-block; box-shadow:0 0 8px #22c55e;"></span>
        <span class="liveClockText">Loading live time...</span>
      </div>

      <!-- Language selection dropdown -->
      <select id="langSelect" style="background:var(--paper); border:1.5px solid var(--line-dark); border-radius:8px; padding:6px 12px; font-size:0.75rem; font-weight:600; font-family:inherit; cursor:pointer;" onchange="changeLanguage()">
        <option value="en">English</option>
        <option value="mr">मराठी (Marathi)</option>
      </select>

      <!-- Notification button -->
      <button class="topbar-icon-btn" onclick="openNotifications()" style="position:relative;"><svg viewBox="0 0 24 24" width="20" height="20"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg><span class="notif-dot"></span></button>

      <!-- Profile Menu -->
      <div style="position:relative; display:inline-block;" id="profileMenuWrapper">
        <div style="display:flex; align-items:center; gap:8px; cursor:pointer;" onclick="toggleProfileDropdown()">
          <div style="width:32px; height:32px; border-radius:50%; background:var(--accent); color:var(--white); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem;" id="headerUserAvatar">RD</div>
          <div style="display:flex; flex-direction:column; text-align:left;">
            <span style="font-size:0.78rem; font-weight:600; color:var(--navy-950);" id="headerUserName">Riya Desai</span>
            <span style="font-size:0.65rem; color:var(--muted-dark);" id="headerUserRole">Committee Member</span>
          </div>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div id="profileDropdown" style="display:none; position:absolute; right:0; top:42px; background:var(--white); border:1px solid var(--line-dark); border-radius:12px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1); width:180px; z-index:150; padding:6px 0;">
          <a href="#" onclick="openDrawer('changePasswordDrawer'); toggleProfileDropdown(); return false;" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:var(--navy-950); text-decoration:none; font-weight:500;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span data-i18n="dash.change_password">Change Password</span>
          </a>
          <div style="border-top:1px solid var(--line-dark); margin:4px 0;"></div>
          <a href="index.php" onclick="sessionStorage.removeItem('current_user');" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:#ef4444; text-decoration:none; font-weight:600;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            <span data-i18n="dash.logout">Logout</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="section-eyebrow" data-i18n="dash.committee_eyebrow">Committee Member</div>
    <div class="content-title">Good Morning, <?= htmlspecialchars($sessionUser['name'] ?? 'Committee Member') ?> 👋</div>
    <div class="content-sub">Technical Committee · Your activity overview — <span class="liveDateText"><?php echo $sqlCurrentDateFormatted; ?></span></div>

    <!-- STATS — Assigned Events, Attendance Records, Event Reports, Notifications -->
    <div class="stats-grid">
      <div class="stat-card" id="statCardAssigned" onclick="openAssignedEventsWindow()" style="cursor:pointer;">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
        <span class="stat-val" id="statAssignedVal">4</span>
        <div class="stat-label" data-i18n="stat.assigned_events">Assigned Events</div>
        <span class="stat-delta up">↑ Active events</span>
      </div>
      <div class="stat-card" id="statCardAttendance" onclick="openAttendanceWindow()" style="cursor:pointer;">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg></div>
        <span class="stat-val" id="statAttendanceVal">92%</span>
        <div class="stat-label" data-i18n="stat.attendance_rate">Attendance Rate</div>
        <span class="stat-delta up">↑ Excellent</span>
      </div>
      <div class="stat-card" id="statCardReports" onclick="openReportsWindow()" style="cursor:pointer;">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
        <span class="stat-val" id="statReportsVal">3</span>
        <div class="stat-label" data-i18n="stat.reports_filed">Event Reports Filed</div>
        <span class="stat-delta up">↑ Filed in system</span>
      </div>
      <div class="stat-card" id="statCardNotifs" onclick="openNotificationsWindow()" style="cursor:pointer;">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg></div>
        <span class="stat-val" id="statNotifsVal">2</span>
        <div class="stat-label" data-i18n="stat.unread_notifs">Notifications</div>
        <span class="stat-delta dn">↓ View updates</span>
      </div>
    </div>

    <!-- MAIN GRID -->
    <div class="dash-grid">
      <!-- Assigned Events Table -->
      <div class="card">
        <div class="card-head"><div class="card-title">Assigned Events</div><span class="card-action">View All</span></div>
        <table class="data-table">
          <thead><tr><th>Event</th><th>Date</th><th>Role</th><th>Tasks</th><th>Status</th></tr></thead>
          <tbody>
            <tr><td><b>Tech Symposium 2026</b></td><td>Jul 28</td><td>Organiser</td><td>Setup, Registration</td><td><span class="badge badge-green">Confirmed</span></td></tr>
            <tr><td><b>AI Workshop Series</b></td><td>Aug 3</td><td>Volunteer</td><td>Venue arrangement</td><td><span class="badge badge-green">Confirmed</span></td></tr>
            <tr><td><b>Hackathon 2026</b></td><td>Aug 15</td><td>Participant</td><td>Team formation</td><td><span class="badge badge-blue">Registered</span></td></tr>
            <tr><td><b>ML Guest Lecture</b></td><td>Sep 5</td><td>Attendee</td><td>—</td><td><span class="badge badge-orange">Pending</span></td></tr>
          </tbody>
        </table>
        <!-- Pending Tasks -->
        <div style="margin-top:20px;">
          <div style="font-family:var(--ff-display);font-size:.9rem;font-weight:700;margin-bottom:12px;">My Pending Tasks</div>
          <div class="task-item">
            <div class="task-check"></div>
            <div class="task-info"><b>Finalize Tech Symposium stage setup plan</b><span>Due: Jul 25 · High Priority</span></div>
          </div>
          <div class="task-item">
            <div class="task-check done"></div>
            <div class="task-info"><b>Collect registration forms — AI Workshop</b><span>Completed · Jul 20</span></div>
          </div>
          <div class="task-item">
            <div class="task-check"></div>
            <div class="task-info"><b>Submit Hackathon team details</b><span>Due: Aug 10 · Medium Priority</span></div>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div style="display:flex;flex-direction:column;gap:24px;">
        <!-- Notifications -->
        <div class="card" id="notificationsCard">
          <div class="card-head"><div class="card-title">Notifications</div><span class="card-action" style="cursor:pointer;" onclick="openNotificationsWindow()">View All</span></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Tech Symposium briefing tomorrow</b><span>10:00 AM, Lab 402 · 2 hrs ago</span></div></div>
        </div>

        <!-- Event Reports -->
        <div class="card" id="reportsCard">
          <div class="card-head"><div class="card-title">Event Reports</div><span class="card-action" style="cursor:pointer;" onclick="openReportsWindow()">Submit New</span></div>
          <div id="reportsCardBody">
            <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Tech Symposium 2025</b><span>Report ready · Attendance 96%</span></div><span class="card-action" style="font-size:.58rem;padding:3px 8px;">↓ PDF</span></div>
          </div>
        </div>
      </div>
    </div>

    <!-- BOTTOM GRID -->
    <div class="dash-grid-3">
      <!-- Attendance Record & Event Calendar -->
      <div class="card">
        <div class="card-head">
          <div class="card-title">Attendance &amp; Event Log</div>
          <span class="card-action" onclick="openAttendanceWindow()" style="cursor:pointer;">Full Log</span>
        </div>
        <div class="attend-grid" id="dashboardCalendarGrid">
          <!-- Calendar Days 1 to 21 populated dynamically -->
        </div>
        <div style="display:flex;gap:12px;margin-top:10px;font-family:var(--ff-mono);font-size:.6rem;color:var(--muted-dark);flex-wrap:wrap;">
          <span style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;border-radius:2px;background:rgba(62,139,255,.15);display:inline-block;"></span>Present</span>
          <span style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;border-radius:2px;background:rgba(239,68,68,.08);display:inline-block;"></span>Absent</span>
          <span style="display:flex;align-items:center;gap:5px;"><span style="width:10px;height:10px;border-radius:2px;background:var(--paper-dim);display:inline-block;"></span>Event Scheduled</span>
        </div>
        <a href="event_calendar.php" style="display:inline-flex; align-items:center; gap:6px; margin-top:12px; font-size:0.75rem; font-family:var(--ff-mono); color:var(--accent); font-weight:600;">
          📅 Open Full Interactive Event Calendar &rarr;
        </a>
      </div>

      <!-- Certificates -->
      <div class="card" style="grid-column:span 2;">
        <div class="card-head"><div class="card-title">My Certificates</div><span class="card-action">View All</span></div>
        <div class="cert-grid">
          <div class="cert-card">
            <div class="cert-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
            <h4>Tech Symposium 2025</h4>
            <span>Organiser · Dec 2025</span>
            <div class="cert-download">↓ Download PDF</div>
          </div>
          <div class="cert-card">
            <div class="cert-icon"><svg viewBox="0 0 24 24"><path d="M12 2l2 6h6l-5 4 2 6-5-4-5 4 2-6-5-4h6z"/></svg></div>
            <h4>Hackathon 2025</h4>
            <span>Participant · Aug 2025</span>
            <div class="cert-download">↓ Download PDF</div>
          </div>
          <div class="cert-card">
            <div class="cert-icon"><svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg></div>
            <h4>AI Workshop Q4 2025</h4>
            <span>Volunteer · Nov 2025</span>
            <div class="cert-download">↓ Download PDF</div>
          </div>
          <div class="cert-card" style="opacity:.5;pointer-events:none;">
            <div class="cert-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg></div>
            <h4>Hackathon 2026</h4>
            <span>Pending event · Aug 2026</span>
            <div class="cert-download" style="color:var(--muted);">Not yet issued</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- consistent footer -->
  <footer class="portal-footer" style="margin-top:40px; padding:24px 30px; background:var(--white); border-top:1px solid var(--line-dark); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
    <div style="font-size:0.78rem; color:var(--muted-dark);">
      <span>© <span class="currentYearText"><?php echo date('Y'); ?></span> <b>Department of AIML</b>, Zeal College of Engineering and Research, Pune. All rights reserved.</span>
    </div>
    <div style="display:flex; align-items:center; gap:20px; font-size:0.75rem; color:var(--muted-dark);">
      <span>📧 Support: <a href="mailto:support.aimsa@zealeducation.com" style="color:var(--accent); text-decoration:none; font-weight:600;">support.aimsa@zealeducation.com</a></span>
      <span>📞 Call: <b>+91 20 6720 6000</b></span>
      <span style="color:var(--line-dark);">|</span>
      <a href="#" onclick="alert('Privacy Policy: All membership data is kept strictly confidential within Zeal Society.')" style="color:inherit; text-decoration:none; font-weight:600;">Privacy Policy</a>
      <a href="terms.php" target="_blank" style="color:inherit; text-decoration:none; font-weight:600;">Terms &amp; Conditions</a>
      <span style="color:var(--line-dark);">|</span>
      <span>Version: <b>v2.1.0</b></span>
      <span>Last Updated: <b class="liveDateText"><?php echo date('F j, Y'); ?></b></span>
    </div>
  </footer>

</div>

<!-- ── ASSIGNED EVENTS DRAWER ── -->
<div class="drawer" id="assignedEventsDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Assigned Events &amp; Duties</div>
    <button class="drawer-close" onclick="closeDrawer('assignedEventsDrawer')">&times;</button>
  </div>
  <p style="font-size:0.82rem; color:var(--muted-dark);">Overview of all technical events assigned to you in the AIMSA system.</p>
  <div id="assignedEventsList" style="display:flex; flex-direction:column; gap:12px; margin-top:10px; overflow-y:auto; flex:1;">
    <!-- Populated dynamically from MySQL -->
  </div>
</div>

<!-- ── MARK ATTENDANCE & FULL EVENT LOG DRAWER ── -->
<div class="drawer" id="markAttendanceDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Attendance Records &amp; Event Log</div>
    <button class="drawer-close" onclick="closeDrawer('markAttendanceDrawer')">&times;</button>
  </div>
  
  <div style="background:var(--paper); padding:16px; border-radius:12px; border:1px solid var(--line-dark);">
    <h4 style="font-size:0.9rem; margin-bottom:12px; font-family:var(--ff-display);" id="attendFormTitle">Mark Student Attendance</h4>
    <input type="hidden" id="attendRecordId" value="">
    <div class="form-group">
      <label>Select Event</label>
      <select id="attendEventSelect">
        <!-- Populated dynamically -->
      </select>
    </div>
    <div class="form-group">
      <label>Select Student Member</label>
      <select id="attendStudentSelect">
        <!-- Populated dynamically -->
      </select>
    </div>
    <div class="form-group">
      <label>Attendance Status</label>
      <select id="attendStatusSelect">
        <option value="Present">Present</option>
        <option value="Absent">Absent</option>
      </select>
    </div>
    <button class="btn btn-primary" style="width:100%; margin-top:6px;" id="saveAttendanceBtn">Save Attendance Record</button>
  </div>

  <!-- Filter Log by Event Date -->
  <div style="background:var(--white); padding:14px; border-radius:12px; border:1px solid var(--line-dark); margin-top:14px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
      <h4 style="font-size:0.85rem; font-family:var(--ff-display);">📅 Filter Log by Event Date</h4>
      <a href="event_calendar.php" style="font-size:0.72rem; color:var(--accent); font-weight:600; text-decoration:none;">Full Calendar &rarr;</a>
    </div>
    <input type="date" id="attendCalendarFilterDate" onchange="filterAttendanceByDate()" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--line-dark); font-size:0.8rem; font-family:inherit;">
    <div id="eventsOnSelectedDate" style="margin-top:8px; font-size:0.75rem; color:var(--muted-dark);"></div>
  </div>

  <div style="margin-top:16px; flex:1; display:flex; flex-direction:column; overflow:hidden;">
    <h4 style="font-size:0.9rem; font-family:var(--ff-display); margin-bottom:10px;">Submitted Attendance Logs</h4>
    <div id="attendanceRecordsContainer" style="display:flex; flex-direction:column; gap:8px; overflow-y:auto; flex:1;">
      <!-- Populated dynamically -->
    </div>
  </div>

  <a href="event_calendar.php" class="btn btn-ghost" style="width:100%; margin-top:10px; border-color:var(--accent); color:var(--accent);">
    📅 Open Full Department Event Calendar
  </a>
</div>

<!-- ── EVENT REPORTS DRAWER ── -->
<div class="drawer" id="eventReportsDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Event Reports Management</div>
    <button class="drawer-close" onclick="closeDrawer('eventReportsDrawer')">&times;</button>
  </div>

  <div style="background:var(--paper); padding:16px; border-radius:12px; border:1px solid var(--line-dark);">
    <h4 style="font-size:0.9rem; margin-bottom:12px; font-family:var(--ff-display);">Submit Event Completion Report</h4>
    <div class="form-group">
      <label>Report Title</label>
      <input type="text" id="reportTitleInput" placeholder="e.g., Tech Symposium 2026 Summary Report">
    </div>
    <div class="form-group">
      <label>Category</label>
      <select id="reportCategorySelect">
        <option value="Event Report">Event Report</option>
        <option value="Workshop Summary">Workshop Summary</option>
        <option value="Hackathon Outcome">Hackathon Outcome</option>
        <option value="General Analytics">General Analytics</option>
      </select>
    </div>
    <div class="form-group">
      <label>Key Highlights &amp; Summary</label>
      <textarea id="reportSummaryInput" rows="3" placeholder="Brief outcome, turnout, participant count, key highlights..."></textarea>
    </div>
    <button class="btn btn-primary" style="width:100%;" id="submitReportBtn">Submit Event Report</button>
  </div>

  <div style="margin-top:20px; flex:1; display:flex; flex-direction:column; overflow:hidden;">
    <h4 style="font-size:0.9rem; font-family:var(--ff-display); margin-bottom:10px;">Filed Reports Log</h4>
    <div id="reportsListContainer" style="display:flex; flex-direction:column; gap:8px; overflow-y:auto; flex:1;">
      <!-- Populated dynamically from MySQL -->
    </div>
  </div>
</div>

<!-- ── NOTIFICATIONS DRAWER ── -->
<div class="drawer" id="notificationsDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Notifications &amp; Alerts</div>
    <button class="drawer-close" onclick="closeDrawer('notificationsDrawer')">&times;</button>
  </div>
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <span style="font-size:0.8rem; color:var(--muted-dark);">Live department updates</span>
    <button class="card-action" style="background:none; border:none; color:var(--accent); cursor:pointer;" onclick="markAllNotifsRead()">Mark All Read</button>
  </div>
  <div id="notificationsListContainer" style="display:flex; flex-direction:column; gap:10px; overflow-y:auto; flex:1;">
    <!-- Populated dynamically from MySQL -->
  </div>
</div>

<!-- ── PROFILE DRAWER ── -->
<div class="drawer" id="profileDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Committee Member Profile</div>
    <button class="drawer-close" onclick="closeDrawer('profileDrawer')">&times;</button>
  </div>

  <div style="display:flex; align-items:center; gap:16px; padding:16px; background:var(--paper); border-radius:14px; border:1px solid var(--line-dark);">
    <div style="width:56px; height:56px; border-radius:50%; background:var(--navy-800); color:var(--white); display:flex; align-items:center; justify-content:center; font-size:1.3rem; font-weight:700; font-family:var(--ff-display);" id="profileAvatarBig">RD</div>
    <div>
      <h3 style="font-size:1rem; font-family:var(--ff-display);" id="profileNameBig">Riya Desai</h3>
      <span style="font-size:0.75rem; color:var(--accent); font-weight:600;" id="profileRoleBadge">Committee Member</span>
      <div style="font-size:0.72rem; color:var(--muted-dark); margin-top:2px;" id="profileZprnBadge">ZPRN: 125UAM1004</div>
    </div>
  </div>

  <div style="background:var(--white); border:1px solid var(--line-dark); border-radius:12px; padding:16px;">
    <h4 style="font-size:0.85rem; font-family:var(--ff-display); margin-bottom:12px; border-bottom:1px solid var(--line-dark); padding-bottom:6px;">Academic Details</h4>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.8rem;">
      <div><span style="color:var(--muted-dark); display:block; font-size:0.7rem;">Branch</span><b id="profileBranchText">AI &amp; ML</b></div>
      <div><span style="color:var(--muted-dark); display:block; font-size:0.7rem;">Batch / Year</span><b id="profileBatchText">2025</b></div>
      <div><span style="color:var(--muted-dark); display:block; font-size:0.7rem;">Designation</span><b id="profileDesignationText">Technical Committee</b></div>
      <div><span style="color:var(--muted-dark); display:block; font-size:0.7rem;">Membership</span><b id="profileStatusText" style="color:#16a34a;">Active</b></div>
    </div>
  </div>

  <div style="background:var(--paper); padding:16px; border-radius:12px; border:1px solid var(--line-dark);">
    <h4 style="font-size:0.85rem; font-family:var(--ff-display); margin-bottom:10px;">Edit Contact Details</h4>
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" id="editProfileName" value="Riya Desai">
    </div>
    <div class="form-group">
      <label>Phone Number</label>
      <input type="text" id="editProfilePhone" placeholder="+91 98765 43210">
    </div>
    <button class="btn btn-primary" style="width:100%;" id="saveProfileBtn">Update Profile</button>
  </div>

  <button class="btn btn-ghost" style="width:100%; margin-top:5px; border-color:var(--accent); color:var(--accent);" onclick="openDrawer('changePasswordDrawer')">🔒 Change Password</button>
</div>

<!-- ── CHANGE PASSWORD DRAWER ── -->
<div class="drawer" id="changePasswordDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Change Account Password</div>
    <button class="drawer-close" onclick="closeDrawer('changePasswordDrawer')">&times;</button>
  </div>
  <div class="form-group">
    <label>Current Password</label>
    <input type="password" id="currPassword" placeholder="••••••••">
  </div>
  <div class="form-group">
    <label>New Password</label>
    <input type="password" id="newPassword" placeholder="••••••••">
  </div>
  <div class="form-group">
    <label>Confirm New Password</label>
    <input type="password" id="confirmNewPassword" placeholder="••••••••">
  </div>
  <button class="btn btn-primary" style="width:100%; margin-top:10px;" id="savePasswordBtn">Update Password</button>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const hamburger = document.getElementById('hamburgerBtn');

hamburger.addEventListener('click', () => {
  sidebar.classList.toggle('open');
  overlay.classList.toggle('open');
});

overlay.addEventListener('click', () => {
  sidebar.classList.remove('open');
  overlay.classList.remove('open');
  closeAllDrawers();
});

function openDrawer(id) {
  closeAllDrawers();
  const drawerEl = document.getElementById(id);
  if (drawerEl) {
    drawerEl.classList.add('open');
    overlay.classList.add('open');
  }
}

function closeDrawer(id) {
  const drawerEl = document.getElementById(id);
  if (drawerEl) {
    drawerEl.classList.remove('open');
  }
  overlay.classList.remove('open');
}

function closeAllDrawers() {
  document.querySelectorAll('.drawer').forEach(d => d.classList.remove('open'));
}

// Window opener helpers for sidebar items
window.openDashboardView = function() {
  closeAllDrawers();
  setActiveNav('navDashboard');
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

window.openAssignedEventsWindow = function() {
  setActiveNav('navAssigned');
  openDrawer('assignedEventsDrawer');
};

window.openAttendanceWindow = function() {
  setActiveNav('navAttendance');
  openDrawer('markAttendanceDrawer');
};

window.openReportsWindow = function() {
  setActiveNav('navReports');
  openDrawer('eventReportsDrawer');
};

window.openNotificationsWindow = function() {
  setActiveNav('navNotifications');
  openDrawer('notificationsDrawer');
};

window.openProfileWindow = function() {
  setActiveNav('navProfile');
  openDrawer('profileDrawer');
};

function setActiveNav(id) {
  document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
  const el = document.getElementById(id);
  if (el) el.classList.add('active');
}

// State store
let cachedData = null;

// Primary Data Fetcher from MySQL Backend
async function loadDashboardData() {
  try {
    const res = await fetch('ajax/committee_actions.php?action=get_dashboard_data');
    const data = await res.json();
    if (data.status === 'success') {
      cachedData = data;
      renderDashboardUI(data);
    } else {
      console.error('Backend returned error:', data.message);
    }
  } catch (err) {
    console.error('Failed to load dashboard data from MySQL backend:', err);
  }
}

function renderDashboardUI(data) {
  const user = data.user || {};
  const stats = data.stats || {};
  const events = data.events || [];
  const students = data.students || [];
  const tasks = data.tasks || [];
  const attendanceRecords = data.attendance_records || [];
  const reports = data.reports || [];
  const notifications = data.notifications || [];

  // Update Header & Welcome User
  const firstName = user.name ? user.name.split(' ')[0] : 'Committee';
  const titleEl = document.querySelector('.content-title');
  if (titleEl) titleEl.innerHTML = `Hello, ${firstName}! 👋`;
  
  const roleNameEl = document.querySelector('.role-info b');
  if (roleNameEl) roleNameEl.textContent = user.name || 'Committee Member';

  const avatarInitials = user.name ? user.name.split(' ').map(n=>n[0]).join('').toUpperCase() : 'CM';
  document.querySelectorAll('#headerUserAvatar, .role-avatar .in, #profileAvatarBig').forEach(el => {
    el.textContent = avatarInitials;
  });
  
  document.getElementById('headerUserName').textContent = user.name || 'Committee Member';
  document.getElementById('headerUserRole').textContent = user.committeeDesignation || user.role || 'Committee Member';

  // Update Stat Cards
  document.getElementById('statAssignedVal').textContent = events.length;
  document.getElementById('statAttendanceVal').textContent = data.attendance_rate || '92%';
  document.getElementById('statReportsVal').textContent = reports.length;
  document.getElementById('statNotifsVal').textContent = notifications.length;
  document.getElementById('navNotifBadge').textContent = notifications.length;

  // Render Main Assigned Events Table
  const tbody = document.querySelector('.data-table tbody');
  if (tbody && events.length > 0) {
    tbody.innerHTML = '';
    events.slice(0, 5).forEach(e => {
      let badgeClass = 'badge-green';
      if (e.status === 'Pending') badgeClass = 'badge-orange';
      if (e.status === 'Registered') badgeClass = 'badge-blue';

      tbody.innerHTML += `
        <tr>
          <td><b>${e.title}</b></td>
          <td>${e.event_date ? new Date(e.event_date).toLocaleDateString('en-US', {month:'short', day:'numeric'}) : 'TBD'}</td>
          <td>Coordinator</td>
          <td>${e.location || 'Campus'}</td>
          <td><span class="badge ${badgeClass}">${e.status}</span></td>
        </tr>`;
    });
  }

  // Render Drawer Assigned Events List
  const drawerEventsList = document.getElementById('assignedEventsList');
  if (drawerEventsList) {
    drawerEventsList.innerHTML = '';
    if (events.length === 0) {
      drawerEventsList.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark);">No assigned events found in database.</p>`;
    } else {
      events.forEach(e => {
        let badgeClass = 'badge-green';
        if (e.status === 'Pending') badgeClass = 'badge-orange';
        drawerEventsList.innerHTML += `
          <div style="background:var(--paper); padding:14px; border-radius:12px; border:1px solid var(--line-dark);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
              <b style="font-size:0.9rem;">${e.title}</b>
              <span class="badge ${badgeClass}">${e.status}</span>
            </div>
            <p style="font-size:0.78rem; color:var(--muted-dark); margin-bottom:8px;">${e.description || 'No description available.'}</p>
            <div style="font-family:var(--ff-mono); font-size:0.7rem; color:var(--accent);">
              📅 Date: ${e.event_date} · 📍 Venue: ${e.location}
            </div>
          </div>`;
      });
    }
  }

  // Render Tasks List (Main View & MySQL Sync)
  const taskContainer = document.querySelector('.card:has(.task-item)') || document.querySelector('.dash-grid .card');
  const taskParent = taskContainer ? taskContainer.querySelector('div[style*="margin-top:20px"]') : null;
  if (taskParent) {
    let taskHtml = `<div style="font-family:var(--ff-display);font-size:.9rem;font-weight:700;margin-bottom:12px;">My Pending Tasks (${tasks.length})</div>`;
    if (tasks.length === 0) {
      taskHtml += `<p style="font-size:0.8rem; color:var(--muted-dark);">All tasks completed!</p>`;
    } else {
      tasks.forEach(t => {
        const isDone = t.status === 'Completed';
        const checkClass = isDone ? 'task-check done' : 'task-check';
        const strikeStyle = isDone ? 'style="text-decoration:line-through;"' : '';
        taskHtml += `
          <div class="task-item" data-task-id="${t.id}">
            <div class="${checkClass}" onclick="toggleTaskStatusMySQL(${t.id}, '${isDone ? 'Pending' : 'Completed'}')"></div>
            <div class="task-info">
              <b ${strikeStyle}>${t.task_title}</b>
              <span>Due: ${t.due_date || 'Ongoing'} · ${t.priority || 'Medium'}</span>
            </div>
          </div>`;
      });
    }
    taskParent.innerHTML = taskHtml;
  }

  // Render Attendance Form Student Select Dropdown
  const studentSelect = document.getElementById('attendStudentSelect');
  if (studentSelect) {
    studentSelect.innerHTML = '';
    if (students.length === 0) {
      studentSelect.innerHTML = `<option value="">No student members found</option>`;
    } else {
      students.forEach(s => {
        studentSelect.innerHTML += `<option value="${s.email}">${s.name} (${s.zprn || s.branch})</option>`;
      });
    }
  }

  // Render Attendance Form Event Select Dropdown
  const eventSelect = document.getElementById('attendEventSelect');
  if (eventSelect && events.length > 0) {
    eventSelect.innerHTML = '';
    events.forEach(e => {
      eventSelect.innerHTML += `<option value="${e.title}">${e.title}</option>`;
    });
  }

  // Render Attendance Log Records in Drawer
  const attLogContainer = document.getElementById('attendanceRecordsContainer');
  if (attLogContainer) {
    attLogContainer.innerHTML = '';
    if (attendanceRecords.length === 0) {
      attLogContainer.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark);">No attendance records in MySQL yet.</p>`;
    } else {
      attendanceRecords.forEach(rec => {
        const statusBadge = rec.status === 'Present' ? 'badge-green' : 'badge-orange';
        attLogContainer.innerHTML += `
          <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 12px; border:1px solid var(--line-dark); border-radius:10px; background:var(--paper);">
            <div>
              <b style="font-size:0.85rem;">${rec.student_name}</b>
              <div style="font-size:0.72rem; color:var(--muted-dark); font-family:var(--ff-mono);">${rec.event_name || 'Event'} · <span class="badge ${statusBadge}">${rec.status}</span></div>
            </div>
            <button style="border:1px solid var(--accent); background:transparent; color:var(--accent); font-size:0.72rem; padding:4px 10px; border-radius:6px; font-weight:600; cursor:pointer;" onclick="editAttendanceRecordMySQL(${rec.id}, '${rec.student_email}', '${rec.event_name || ''}', '${rec.status}')">Edit</button>
          </div>`;
      });
    }
  }

  // Render Event Reports in Drawer & Right Column Card
  const reportsDrawerContainer = document.getElementById('reportsListContainer');
  if (reportsDrawerContainer) {
    reportsDrawerContainer.innerHTML = '';
    if (reports.length === 0) {
      reportsDrawerContainer.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark);">No event reports in MySQL yet.</p>`;
    } else {
      reports.forEach(r => {
        reportsDrawerContainer.innerHTML += `
          <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 12px; border:1px solid var(--line-dark); border-radius:10px; background:var(--paper);">
            <div>
              <b style="font-size:0.85rem;">${r.title}</b>
              <div style="font-size:0.72rem; color:var(--muted-dark); font-family:var(--ff-mono);">${r.category} · By ${r.created_by}</div>
            </div>
            <button class="btn btn-ghost" style="font-size:0.7rem; padding:4px 10px;" onclick="downloadReportPDF('${r.title}')">↓ PDF</button>
          </div>`;
      });
    }
  }

  // Render Notifications List in Drawer & Main Card
  const notifContainer = document.getElementById('notificationsListContainer');
  const notifCard = document.getElementById('notificationsCard');

  const renderNotifItems = (nList) => {
    if (nList.length === 0) return `<p style="font-size:0.8rem; color:var(--muted-dark); padding:10px;">No alerts recorded.</p>`;
    return nList.map(n => {
      let dotColor = '#22c55e';
      if (n.indicator === 'yellow') dotColor = '#fbbf24';
      if (n.indicator === 'red') dotColor = '#ef4444';
      return `
        <div class="list-item" style="padding:10px; border:1px solid var(--line-dark); border-radius:8px; background:var(--paper); margin-bottom:6px;">
          <div class="list-dot" style="background:${dotColor};"></div>
          <div class="list-text">
            <b>${n.title}</b>
            <span>${n.text}</span>
          </div>
        </div>`;
    }).join('');
  };

  if (notifContainer) {
    notifContainer.innerHTML = renderNotifItems(notifications);
  }

  if (notifCard) {
    notifCard.innerHTML = `
      <div class="card-head"><div class="card-title">Notifications (${notifications.length})</div><span class="card-action" style="cursor:pointer;" onclick="openNotificationsWindow()">View All</span></div>
      ${renderNotifItems(notifications.slice(0, 4))}`;
  }

  // Render Profile Info
  if (user) {
    document.getElementById('profileNameBig').textContent = user.name || 'Riya Desai';
    document.getElementById('profileRoleBadge').textContent = user.committeeDesignation || user.role || 'Committee Member';
    document.getElementById('profileZprnBadge').textContent = `ZPRN: ${user.zprn || '125UAM1004'}`;
    document.getElementById('profileBranchText').textContent = user.branch || 'AI & ML';
    document.getElementById('profileBatchText').textContent = user.batch || '2025';
    document.getElementById('profileDesignationText').textContent = user.committeeDesignation || 'Technical Committee';
    document.getElementById('profileStatusText').textContent = user.membershipStatus || 'Active';
    document.getElementById('editProfileName').value = user.name || '';
    document.getElementById('editProfilePhone').value = user.phone || '';
  }
}

// Mark Attendance Form Submission (MySQL)
document.getElementById('saveAttendanceBtn').addEventListener('click', async () => {
  const eventName = document.getElementById('attendEventSelect').value;
  const studentEmail = document.getElementById('attendStudentSelect').value;
  const status = document.getElementById('attendStatusSelect').value;
  const recordId = document.getElementById('attendRecordId').value;

  if (!eventName || !studentEmail) {
    alert('Please select both an event and a student member.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'mark_attendance');
  formData.append('event_name', eventName);
  formData.append('student_email', studentEmail);
  formData.append('status', status);
  if (recordId) formData.append('record_id', recordId);

  try {
    const res = await fetch('ajax/committee_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      document.getElementById('attendRecordId').value = '';
      document.getElementById('attendFormTitle').textContent = 'Mark Student Attendance';
      document.getElementById('saveAttendanceBtn').textContent = 'Save Attendance Record';
      loadDashboardData();
    } else {
      alert('Error: ' + data.message);
    }
  } catch (err) {
    console.error('Failed to submit attendance to MySQL:', err);
    alert('Failed to connect to MySQL database server.');
  }
});

// Edit Attendance Handler
window.editAttendanceRecordMySQL = function(id, email, eventName, status) {
  document.getElementById('attendRecordId').value = id;
  if (eventName) document.getElementById('attendEventSelect').value = eventName;
  if (email) document.getElementById('attendStudentSelect').value = email;
  if (status) document.getElementById('attendStatusSelect').value = status;
  document.getElementById('attendFormTitle').textContent = 'Edit Attendance Record #' + id;
  document.getElementById('saveAttendanceBtn').textContent = 'Update Attendance Record';
};

// Submit Event Report Form Submission (MySQL)
document.getElementById('submitReportBtn').addEventListener('click', async () => {
  const title = document.getElementById('reportTitleInput').value.trim();
  const category = document.getElementById('reportCategorySelect').value;
  const summary = document.getElementById('reportSummaryInput').value.trim();

  if (!title) {
    alert('Please enter a title for the event report.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'submit_event_report');
  formData.append('title', title);
  formData.append('category', category);
  formData.append('summary', summary);

  try {
    const res = await fetch('ajax/committee_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      document.getElementById('reportTitleInput').value = '';
      document.getElementById('reportSummaryInput').value = '';
      loadDashboardData();
    } else {
      alert('Error: ' + data.message);
    }
  } catch (err) {
    console.error('Failed to submit report to MySQL:', err);
    alert('Failed to connect to MySQL database.');
  }
});

// Toggle Task Status (MySQL)
window.toggleTaskStatusMySQL = async function(taskId, newStatus) {
  const formData = new FormData();
  formData.append('action', 'toggle_task_status');
  formData.append('task_id', taskId);
  formData.append('status', newStatus);

  try {
    const res = await fetch('ajax/committee_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      loadDashboardData();
    }
  } catch (err) {
    console.error('Failed to toggle task in MySQL:', err);
  }
};

// Update Profile Submission (MySQL)
document.getElementById('saveProfileBtn').addEventListener('click', async () => {
  const name = document.getElementById('editProfileName').value.trim();
  const phone = document.getElementById('editProfilePhone').value.trim();

  if (!name) {
    alert('Name cannot be left empty.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'update_profile');
  formData.append('name', name);
  formData.append('phone', phone);

  try {
    const res = await fetch('ajax/committee_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      loadDashboardData();
    } else {
      alert('Error: ' + data.message);
    }
  } catch (err) {
    console.error('Failed to update profile in MySQL:', err);
  }
});

// Change Password Action (MySQL)
document.getElementById('savePasswordBtn').addEventListener('click', async () => {
  const curr = document.getElementById('currPassword').value;
  const newp = document.getElementById('newPassword').value;
  const conf = document.getElementById('confirmNewPassword').value;

  if (!curr || !newp || !conf) {
    alert('Please fill in all password fields.');
    return;
  }
  if (newp !== conf) {
    alert('New passwords do not match.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'change_password');
  formData.append('curr_password', curr);
  formData.append('new_password', newp);

  try {
    const res = await fetch('ajax/committee_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      document.getElementById('currPassword').value = '';
      document.getElementById('newPassword').value = '';
      document.getElementById('confirmNewPassword').value = '';
      closeDrawer('changePasswordDrawer');
    } else {
      alert('Error: ' + data.message);
    }
  } catch (err) {
    console.error('Failed to change password in MySQL:', err);
  }
});

// Mark All Notifications Read
window.markAllNotifsRead = function() {
  alert('All notifications marked as read in system.');
  closeDrawer('notificationsDrawer');
};

// Download Report PDF Helper
window.downloadReportPDF = function(title) {
  alert(`Downloading PDF report for "${title}"... Completed!`);
};

// Profile Menu Dropdown Toggle
window.toggleProfileDropdown = function() {
  const d = document.getElementById('profileDropdown');
  if (d) d.style.display = d.style.display === 'none' ? 'block' : 'none';
};
document.addEventListener('click', (e) => {
  const wrapper = document.getElementById('profileMenuWrapper');
  if (wrapper && !wrapper.contains(e.target)) {
    const d = document.getElementById('profileDropdown');
    if (d) d.style.display = 'none';
  }
});

// Search Bar Realtime Filter
document.getElementById('headerSearchInput').addEventListener('input', (e) => {
  const query = e.target.value.toLowerCase().trim();
  document.querySelectorAll('.list-item, .task-item, tr').forEach(el => {
    if (query === '') {
      el.style.display = '';
    } else {
      const text = el.textContent.toLowerCase();
      el.style.display = text.includes(query) ? '' : 'none';
    }
  });
});

// Initial Data Load
loadDashboardData();
</script>
<script src="assets/js/landing.js"></script>
</body>
</html>

