<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/include/dbConfig.php';

$sessionUser = $_SESSION['user'] ?? null;
if (!$sessionUser || !in_array($sessionUser['role'] ?? '', ['Association President', 'President', 'Vice President', 'HOD', 'Faculty Coordinator'])) {
    header("Location: index.php?auth_error=" . urlencode("Unauthorized access. Please login with President credentials."));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Association President Dashboard — AIMSA Portal</title>
<meta name="description" content="Association President portal dashboard for AIMSA — AI & ML Student Association.">
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
.role-info b{color:var(--white);font-size:.85rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
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
.dash-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:24px;}
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
.list-item{display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid rgba(8,23,51,.05);}
.list-item:last-child{border-bottom:none;}
.list-dot{width:9px;height:9px;border-radius:50%;background:var(--accent);flex-shrink:0;box-shadow:0 0 0 3px rgba(62,139,255,.18);}
.list-text{flex:1;}
.list-text b{font-size:.88rem;display:block;margin-bottom:2px;}
.list-text span{font-size:.76rem;color:var(--muted-dark);font-family:var(--ff-mono);}
/* Announcement card */
.ann-card{padding:16px;border-radius:12px;border:1px solid var(--line-dark);background:var(--paper);margin-bottom:10px;transition:.2s ease;position:relative;overflow-hidden;}
.ann-card:hover{border-color:var(--accent);background:var(--white);transform:translateX(4px);}
.ann-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--accent);border-radius:999px;}
.ann-card.urgent::before{background:#f97316;}
.ann-card.important::before{background:#22c55e;}
.ann-title{font-size:.88rem;font-weight:600;margin-bottom:4px;}
.ann-meta{font-family:var(--ff-mono);font-size:.68rem;color:var(--muted-dark);}
/* Approval item */
.approval-item{display:flex;align-items:center;gap:14px;padding:14px;border-radius:12px;border:1px solid var(--line-dark);background:var(--paper);transition:.2s ease;margin-bottom:10px;}
.approval-item:hover{border-color:var(--accent);background:var(--white);}
.approval-info{flex:1;}
.approval-info b{font-size:.88rem;display:block;margin-bottom:2px;}
.approval-info span{font-size:.75rem;color:var(--muted-dark);font-family:var(--ff-mono);}
.approval-actions{display:flex;gap:8px;}
.approve-btn{background:rgba(34,197,94,.1);color:#16a34a;border:1px solid rgba(34,197,94,.2);border-radius:6px;padding:5px 12px;font-size:.72rem;cursor:pointer;font-family:inherit;transition:.2s;}
.approve-btn:hover{background:#16a34a;color:var(--white);}
.reject-btn{background:rgba(239,68,68,.08);color:#dc2626;border:1px solid rgba(239,68,68,.15);border-radius:6px;padding:5px 12px;font-size:.72rem;cursor:pointer;font-family:inherit;transition:.2s;}
.reject-btn:hover{background:#dc2626;color:var(--white);}
/* Member card */
.member-card{display:flex;align-items:center;gap:12px;padding:12px;border-radius:10px;border:1px solid var(--line-dark);background:var(--paper);transition:.2s;margin-bottom:8px;}
.member-card:hover{border-color:var(--accent);background:var(--white);}
.member-avatar{width:36px;height:36px;border-radius:50%;background:conic-gradient(from 180deg,var(--accent),var(--navy-700),var(--accent));padding:2px;flex-shrink:0;}
.member-avatar .in{width:100%;height:100%;border-radius:50%;background:var(--navy-800);display:flex;align-items:center;justify-content:center;font-family:var(--ff-display);font-weight:700;color:var(--white);font-size:.72rem;}
.member-info{flex:1;}
.member-info b{font-size:.85rem;display:block;}
.member-info span{font-size:.72rem;color:var(--muted-dark);font-family:var(--ff-mono);}
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
  position:fixed; top:0; right:-700px; width:min(580px, 100%); height:100vh;
  background:var(--white); border-left:1px solid var(--line-dark); z-index:200;
  transition:right 0.35s cubic-bezier(0.16, 1, 0.3, 1); padding:30px;
  display:flex; flex-direction:column; gap:20px; overflow-y:auto;
  box-shadow: -10px 0 30px -10px rgba(0,0,0,0.2);
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

@media(max-width:1300px){.dash-grid-4{grid-template-columns:repeat(2,1fr);}}
@media(max-width:1100px){.stats-grid{grid-template-columns:repeat(2,1fr);}.dash-grid{grid-template-columns:1fr;}.dash-grid-3{grid-template-columns:1fr 1fr;}}
@media(max-width:768px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}.sidebar-overlay.open{display:block;}.main{margin-left:0;}.hamburger-btn{display:flex;}.content{padding:20px;}.stats-grid{grid-template-columns:1fr 1fr;}.dash-grid-3,.dash-grid-4{grid-template-columns:1fr;}}
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
    <div class="brand-info"><b>AIMSA Portal</b><span>President Access</span></div>
  </div>
  <div class="sidebar-role" style="cursor:pointer;" onclick="openDrawer('viewProfileDrawer')">
    <div class="role-avatar"><div class="in" id="sidebarAvatar">AP</div></div>
    <div class="role-info"><b id="sidebarUserName">Varad</b><span>Association President</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <a class="nav-item active" href="president_dashboard.php">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard
    </a>
    <div class="nav-section-label">Management</div>
    <a class="nav-item" id="navCommittee" href="#committee">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>Committee Members
    </a>
    <a class="nav-item" id="navRegistrations" href="#registrations">
      <svg class="nav-icon" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Event Registrations
    </a>
    <a class="nav-item" id="navApprovals" href="#approvals">
      <svg class="nav-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Pending Approvals
      <span class="nav-badge">5</span>
    </a>
    <a class="nav-item" id="navCreateEvent" href="#" onclick="openDrawer('createEventDrawer'); return false;">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Create Event
    </a>
    <a class="nav-item" id="navUpcomingEvents" href="#upcomingEventsCard">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Upcoming Events
    </a>
    <div class="nav-section-label">Communication</div>
    <a class="nav-item" id="navAnnouncements" href="#announcements">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M22 4s-3.5 3-10 3S2 4 2 4v6c0 5.52 4.48 10 10 10s10-4.48 10-10V4z"/></svg>Announcements
    </a>
    <a class="nav-item" id="navNotifications" href="#notifications">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>Notifications
      <span class="nav-badge">6</span>
    </a>
    <div class="nav-section-label">Account</div>
    <a class="nav-item" id="navProfileLink" href="#" onclick="openDrawer('viewProfileDrawer'); return false;"><svg class="nav-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>Profile</a>
  </nav>
  <div class="sidebar-footer">
    <a class="nav-item" href="index.php"><svg class="nav-icon" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>Logout</a>
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
        <div class="breadcrumb" style="font-size:0.68rem; color:var(--muted-dark);">AI &amp; ML Department (President)</div>
      </div>
    </div>

    <!-- Center Search Bar -->
    <div class="header-search-bar">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted-dark)" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="headerSearchInput" placeholder="Search committee, announcements...">
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
          <div style="width:32px; height:32px; border-radius:50%; background:var(--accent); color:var(--white); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem;" id="headerUserAvatar">AP</div>
          <div style="display:flex; flex-direction:column; text-align:left;">
            <span style="font-size:0.78rem; font-weight:600; color:var(--navy-950);" id="headerUserName">Varad</span>
            <span style="font-size:0.65rem; color:var(--muted-dark);" id="headerUserRole">Association President</span>
          </div>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div id="profileDropdown" style="display:none; position:absolute; right:0; top:42px; background:var(--white); border:1px solid var(--line-dark); border-radius:12px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1); width:180px; z-index:150; padding:6px 0;">
          <a href="#" onclick="openDrawer('viewProfileDrawer'); toggleProfileDropdown(); return false;" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:var(--navy-950); text-decoration:none; font-weight:500;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
            <span>Profile Overview</span>
          </a>
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
    <div class="section-eyebrow" data-i18n="dash.president_eyebrow">Association President</div>
    <div class="content-title">Good Morning, <?= htmlspecialchars($sessionUser['name'] ?? 'Varad') ?> 👋</div>
    <div class="content-sub">AIMSA leadership dashboard — <span class="liveDateText"><?php echo $sqlCurrentDateFormatted; ?></span></div>

    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card" id="committee">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div>
        <span class="stat-val">35</span>
        <div class="stat-label">Committee Members</div>
        <span class="stat-delta up">↑ 5 committees active</span>
      </div>
      <div class="stat-card" id="registrations">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg></div>
        <span class="stat-val">1,284</span>
        <div class="stat-label">Event Registrations</div>
        <span class="stat-delta up">↑ 146 this week</span>
      </div>
      <div class="stat-card" id="approvals">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <span class="stat-val">5</span>
        <div class="stat-label">Pending Approvals</div>
        <span class="stat-delta dn">↓ Action required</span>
      </div>
      <div class="stat-card" id="upcoming">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
        <span class="stat-val">4</span>
        <div class="stat-label">Create Event</div>
        <span class="stat-delta up">↑ Next: Jul 28</span>
      </div>
    </div>

    <!-- MAIN GRID -->
    <div class="dash-grid">
      <!-- Pending Approvals -->
      <div class="card">
        <div class="card-head"><div class="card-title">Pending Approvals</div><span class="card-action" id="btnViewAllPending">View All</span></div>
        <div id="pendingApprovalsCardList">
          <div class="approval-item">
            <div class="approval-info"><b>Robotics Workshop — Aug 10</b><span>Proposed by Tech Committee · Budget: ₹12,000</span></div>
            <div class="approval-actions"><button class="approve-btn" onclick="processApprovalItem('Robotics Workshop', true, this)">Approve</button><button class="reject-btn" onclick="processApprovalItem('Robotics Workshop', false, this)">Reject</button></div>
          </div>
          <div class="approval-item">
            <div class="approval-info"><b>Cultural Night 2026 — Aug 20</b><span>Proposed by Cultural Committee · Budget: ₹25,000</span></div>
            <div class="approval-actions"><button class="approve-btn" onclick="processApprovalItem('Cultural Night 2026', true, this)">Approve</button><button class="reject-btn" onclick="processApprovalItem('Cultural Night 2026', false, this)">Reject</button></div>
          </div>
          <div class="approval-item">
            <div class="approval-info"><b>Alumni Connect — Sep 5</b><span>Proposed by Outreach Committee · Budget: ₹8,000</span></div>
            <div class="approval-actions"><button class="approve-btn" onclick="processApprovalItem('Alumni Connect', true, this)">Approve</button><button class="reject-btn" onclick="processApprovalItem('Alumni Connect', false, this)">Reject</button></div>
          </div>
          <div class="approval-item">
            <div class="approval-info"><b>Media Partnership — MNC Brand</b><span>Sponsorship proposal · Outreach Committee</span></div>
            <div class="approval-actions"><button class="approve-btn" onclick="processApprovalItem('Media Partnership', true, this)">Approve</button><button class="reject-btn" onclick="processApprovalItem('Media Partnership', false, this)">Reject</button></div>
          </div>
          <div class="approval-item">
            <div class="approval-info"><b>New Committee Member — Harsh Shah</b><span>Finance Committee · Application pending</span></div>
            <div class="approval-actions"><button class="approve-btn" onclick="processApprovalItem('Harsh Shah Membership', true, this)">Accept</button><button class="reject-btn" onclick="processApprovalItem('Harsh Shah Membership', false, this)">Decline</button></div>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div style="display:flex;flex-direction:column;gap:24px;">
        <!-- Notifications -->
        <div class="card" id="notifications">
          <div class="card-head"><div class="card-title">Notifications</div><span class="card-action">Mark Read</span></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>5 approvals pending your action</b><span>Review required · 30 min ago</span></div></div>
          <div class="list-item"><div class="list-dot" style="background:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,.18);"></div><div class="list-text"><b>Tech Symposium registration: 148 confirmed</b><span>Event on Jul 28 · 1 hr ago</span></div></div>
          <div class="list-item"><div class="list-dot" style="background:#f97316;box-shadow:0 0 0 3px rgba(249,115,22,.18);"></div><div class="list-text"><b>Hackathon 2026 sponsorship received</b><span>₹50,000 from TechCorp · 2 hrs ago</span></div></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Faculty Coordinator review requested</b><span>Monthly report · Jul 25 deadline</span></div></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>2 committee vacancies to fill</b><span>Finance & Outreach · Posted today</span></div></div>
          <div class="list-item"><div class="list-dot" style="background:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,.18);"></div><div class="list-text"><b>AI Workshop registration: 67 students</b><span>33 seats remaining · Yesterday</span></div></div>
        </div>
      </div>
    </div>

    <!-- BOTTOM GRID (4 SEPARATE DEDICATED COLUMNS) -->
    <div class="dash-grid-4">
      <!-- 1. Committee Members Column -->
      <div class="card">
        <div class="card-head"><div class="card-title">Committee Members</div><span class="card-action" id="btnManageCommittee">Manage</span></div>
        <div class="member-card"><div class="member-avatar"><div class="in">RD</div></div><div class="member-info"><b>Riya Desai</b><span>Technical Committee Lead</span></div><span class="badge badge-green">Lead</span></div>
        <div class="member-card"><div class="member-avatar"><div class="in">AK</div></div><div class="member-info"><b>Aman Kulkarni</b><span>Cultural Committee Lead</span></div><span class="badge badge-blue">Lead</span></div>
        <div class="member-card"><div class="member-avatar"><div class="in">PJ</div></div><div class="member-info"><b>Priya Joshi</b><span>Media &amp; PR Committee</span></div><span class="badge badge-green">Member</span></div>
        <div class="member-card"><div class="member-avatar"><div class="in">DP</div></div><div class="member-info"><b>Dev Patil</b><span>Finance Committee</span></div><span class="badge badge-orange">Forming</span></div>
        <div class="member-card"><div class="member-avatar"><div class="in">SR</div></div><div class="member-info"><b>Sneha Rao</b><span>Outreach Committee Lead</span></div><span class="badge badge-green">Lead</span></div>
      </div>

      <!-- 2. Create Event Column -->
      <div class="card">
        <div class="card-head"><div class="card-title">Create Event</div><span class="card-action" id="createEventActionBtn">Create Event</span></div>
        <div style="background:var(--paper); border:1px dashed var(--accent); border-radius:12px; padding:16px; text-align:center; margin-bottom:12px;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" style="margin-bottom:6px;"><path d="M12 5v14M5 12h14"/></svg>
          <b style="font-size:0.88rem; color:var(--navy-950); display:block;">Propose Department Event</b>
          <span style="font-size:0.72rem; color:var(--muted-dark);">Submit workshops, hackathons, or guest lectures for faculty approval.</span>
        </div>
        <button class="btn btn-primary" style="width:100%; justify-content:center;" id="createEventBtnFooter">+ Create New Event</button>
      </div>

      <!-- 3. Upcoming Events Column (SEPARATE DEDICATED COLUMN) -->
      <div class="card" id="upcomingEventsCard">
        <div class="card-head"><div class="card-title">Upcoming Events</div><span class="card-action" id="btnViewAllUpcoming">View Directory</span></div>
        <div id="upcomingEventsBody">
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Loading upcoming events...</b><span>Please wait</span></div></div>
        </div>
      </div>

      <!-- 4. Announcements Column -->
      <div class="card" id="announcements">
        <div class="card-head"><div class="card-title">Announcements</div><span class="card-action" id="btnPostAnnouncement">Post New</span></div>
        <div id="announcementsListBody">
          <div class="ann-card">
            <div class="ann-title">🏆 Hackathon 2026 registrations open!</div>
            <div class="ann-meta">Posted by President · Jul 18 · 203 views</div>
          </div>
          <div class="ann-card urgent">
            <div class="ann-title">⚡ Deadline: Club fee payment — Jul 31</div>
            <div class="ann-meta">Finance Committee · Jul 15 · Pinned</div>
          </div>
          <div class="ann-card important">
            <div class="ann-title">✅ Tech Symposium venue confirmed</div>
            <div class="ann-meta">Posted by President · Jul 14 · 187 views</div>
          </div>
          <div class="ann-card">
            <div class="ann-title">📢 New committee vacancies — Apply now</div>
            <div class="ann-meta">Outreach Committee · Jul 10 · 92 views</div>
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
      <a href="#" onclick="alert('Terms &amp; Conditions: AIMSA portal usage is governed by college guidelines.')" style="color:inherit; text-decoration:none; font-weight:600;">Terms &amp; Conditions</a>
      <span style="color:var(--line-dark);">|</span>
      <span>Version: <b>v2.1.0</b></span>
      <span>Last Updated: <b class="liveDateText"><?php echo date('F j, Y'); ?></b></span>
    </div>
  </footer>

</div>

<!-- ── CHANGE PASSWORD DRAWER ── -->
<div class="drawer" id="changePasswordDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Change Password</div>
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

<!-- ── PRESIDENT PROFILE DRAWER ── -->
<div class="drawer" id="viewProfileDrawer">
  <div class="drawer-header">
    <div class="drawer-title">President Profile Overview</div>
    <button class="drawer-close" onclick="closeDrawer('viewProfileDrawer')">&times;</button>
  </div>
  <div style="text-align:center; padding:15px 0; border-bottom:1px solid var(--line-dark);">
    <div style="width:70px; height:70px; border-radius:50%; background:conic-gradient(from 180deg,var(--accent),var(--navy-700),var(--accent)); padding:3px; margin:0 auto 10px;">
      <div style="width:100%; height:100%; border-radius:50%; background:var(--navy-800); display:flex; align-items:center; justify-content:center; font-family:var(--ff-display); font-weight:700; color:var(--white); font-size:1.4rem;" id="presDrawerAvatar">AP</div>
    </div>
    <b style="font-size:1.1rem; color:var(--navy-950); display:block;" id="presDrawerName">Varad</b>
    <span style="font-family:var(--ff-mono); font-size:0.72rem; color:var(--accent); letter-spacing:0.1em; text-transform:uppercase;">Association President — AIMSA</span>
  </div>
  <div class="form-group" style="margin-top:10px;">
    <label>Full Name</label>
    <input type="text" id="presNameInput" placeholder="e.g. Varad">
  </div>
  <div class="form-group">
    <label>College Email ID</label>
    <input type="email" id="presEmailInput" placeholder="president@zealeducation.com">
  </div>
  <div class="form-group">
    <label>Unique ZPRN</label>
    <input type="text" id="presZprnInput" placeholder="e.g. 125UAM1003">
  </div>
  <div class="form-group">
    <label>Mobile Number</label>
    <input type="text" id="presPhoneInput" placeholder="+91 XXXXX XXXXX">
  </div>
  <div class="form-group">
    <label>Leadership Role / Department</label>
    <input type="text" id="presRoleInput" value="Association President · AI &amp; ML Department" readonly style="background:var(--paper-dim);">
  </div>
  <button class="btn btn-primary" style="width:100%; margin-top:10px;" id="savePresProfileBtn">Save Profile Changes</button>
</div>

<!-- ── MANAGE COMMITTEE DRAWER ── -->
<div class="drawer" id="manageCommitteeDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Assign Tasks &amp; Manage Team</div>
    <button class="drawer-close" onclick="closeDrawer('manageCommitteeDrawer')">&times;</button>
  </div>
  <p style="font-size:0.85rem; color:var(--muted-dark);">Assign departmental tasks, event duties, and action plans exclusively to Committee Members and Students.</p>
  <div style="font-weight:700; font-size:0.9rem; margin-top:8px;">Assign Task / Action Plan</div>
  <div class="form-group">
    <label>Select Committee Member or Student</label>
    <select id="manageCommMemberSelect">
      <!-- Populated dynamically -->
    </select>
  </div>
  <div class="form-group">
    <label>Task / Responsibility Details</label>
    <textarea id="manageCommTaskText" rows="3" placeholder="Enter assigned duties or action plan..."></textarea>
  </div>
  <button class="btn btn-primary" style="width:100%; margin-bottom:15px;" id="btnAssignCommTask">Assign Task</button>

  <div style="font-weight:700; font-size:0.9rem; margin-bottom:8px;">Active Members &amp; Students Roster</div>
  <div style="flex:1; overflow-y:auto;">
    <table class="data-table">
      <thead><tr><th>Name</th><th>Role / Email</th><th>ZPRN</th><th>Assigned Task</th></tr></thead>
      <tbody id="drawerCommitteeMembersBody">
        <!-- Populated dynamically -->
      </tbody>
    </table>
  </div>
</div>

<!-- ── PENDING APPROVALS DRAWER ── -->
<div class="drawer" id="pendingApprovalsDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Pending Approvals Review</div>
    <button class="drawer-close" onclick="closeDrawer('pendingApprovalsDrawer')">&times;</button>
  </div>
  <p style="font-size:0.85rem; color:var(--muted-dark);">Review and take leadership decisions on event proposals, budget requests, and committee applications.</p>
  <div id="drawerPendingApprovalsList" style="flex:1; overflow-y:auto; display:flex; flex-direction:column; gap:12px; margin-top:10px;">
    <!-- Dynamically populated -->
  </div>
</div>

<!-- ── EVENT REGISTRATIONS DRAWER ── -->
<div class="drawer" id="eventRegistrationsDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Event Registrations Overview</div>
    <button class="drawer-close" onclick="closeDrawer('eventRegistrationsDrawer')">&times;</button>
  </div>
  <div class="attend-summary" style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px;">
    <div style="background:rgba(62,139,255,0.06); border:1px solid rgba(62,139,255,0.2); padding:12px; border-radius:10px; text-align:center;">
      <b style="font-family:var(--ff-display); font-size:1.4rem; color:var(--accent); display:block;">1,284</b>
      <span style="font-size:0.65rem; color:var(--muted-dark); font-family:var(--ff-mono); text-transform:uppercase;">Total Registrations</span>
    </div>
    <div style="background:rgba(34,197,94,0.06); border:1px solid rgba(34,197,94,0.2); padding:12px; border-radius:10px; text-align:center;">
      <b style="font-family:var(--ff-display); font-size:1.4rem; color:#16a34a; display:block;">8 Active</b>
      <span style="font-size:0.65rem; color:var(--muted-dark); font-family:var(--ff-mono); text-transform:uppercase;">Conducted Events</span>
    </div>
  </div>
  <div style="font-weight:700; font-size:0.9rem; margin-bottom:8px;">Event Registrations Breakdown</div>
  <div style="flex:1; overflow-y:auto;">
    <table class="data-table">
      <thead><tr><th>Event Title</th><th>Date</th><th>Venue</th><th>Registrations</th></tr></thead>
      <tbody id="drawerEventRegistrationsBody">
        <!-- Dynamically populated -->
      </tbody>
    </table>
  </div>
</div>

<!-- ── MANAGE ANNOUNCEMENTS DRAWER ── -->
<div class="drawer" id="manageAnnouncementsDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Broadcast Announcements</div>
    <button class="drawer-close" onclick="closeDrawer('manageAnnouncementsDrawer')">&times;</button>
  </div>
  <b style="font-size:0.9rem; color:var(--navy-950);">Publish New Announcement</b>
  <div class="form-group" style="margin-top:8px;">
    <label>Announcement Title</label>
    <input type="text" id="annTitleInput" placeholder="e.g. 🏆 Hackathon 2026 registrations open!">
  </div>
  <div class="form-group">
    <label>Message Content</label>
    <textarea id="annContentInput" rows="3" placeholder="Enter broadcast details..."></textarea>
  </div>
  <div class="form-group">
    <label>Priority Tag</label>
    <select id="annPrioritySelect">
      <option value="Normal">Normal</option>
      <option value="Important">Important (Green)</option>
      <option value="Urgent">Urgent (Orange/Red)</option>
    </select>
  </div>
  <div class="form-group">
    <label>Target Audience</label>
    <select id="annAudienceSelect">
      <option value="All Members">All Members</option>
      <option value="Students">All Students</option>
      <option value="Committee Members">Committee Members</option>
      <option value="Faculty Coordinator">Faculty Coordinators</option>
    </select>
  </div>
  <button class="btn btn-primary" style="width:100%; margin-bottom:15px;" id="btnPublishAnnouncement">Publish Announcement</button>

  <b style="font-size:0.9rem; color:var(--navy-950); margin-bottom:8px; display:block;">Recent Department Broadcasts</b>
  <div id="drawerAnnouncementsList" style="flex:1; overflow-y:auto; display:flex; flex-direction:column; gap:10px;">
    <!-- Dynamically populated -->
  </div>
</div>

<!-- ── CREATE EVENT DRAWER ── -->
<div class="drawer" id="createEventDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Create Event</div>
    <button class="drawer-close" onclick="closeDrawer('createEventDrawer')">&times;</button>
  </div>
  <div class="form-group">
    <label>Event Name</label>
    <input type="text" id="evtName" placeholder="e.g. AI & Robotics Symposium">
  </div>
  <div class="form-group">
    <label>Category</label>
    <select id="evtCategory">
      <option value="Workshop">Workshop</option>
      <option value="Symposium">Symposium</option>
      <option value="Hackathon">Hackathon</option>
      <option value="Guest Lecture">Guest Lecture</option>
    </select>
  </div>
  <div class="form-group">
    <label>Date</label>
    <input type="date" id="evtDate">
  </div>
  <div class="form-group">
    <label>Time</label>
    <input type="time" id="evtTime">
  </div>
  <div class="form-group">
    <label>Venue</label>
    <input type="text" id="evtVenue" placeholder="e.g. Seminar Hall 3">
  </div>
  <div class="form-group">
    <label>Description</label>
    <textarea id="evtDescription" rows="3" placeholder="Brief event description..."></textarea>
  </div>
  <div class="form-group">
    <label>Maximum Participants</label>
    <input type="number" id="evtMaxParticipants" placeholder="e.g. 100">
  </div>
  <div class="form-group">
    <label>Registration Deadline</label>
    <input type="date" id="evtDeadline">
  </div>
  <div class="form-group">
    <label>Faculty Coordinator</label>
    <select id="evtCoordinator">
      <option value="Prof. Manisha Devgunde">Prof. Manisha Devgunde</option>
      <option value="Dipali Shende">Dipali Shende</option>
    </select>
  </div>
  <button class="btn btn-primary" style="width:100%; margin-top:10px;" id="saveEventBtn">Publish Event</button>
</div>

<!-- ── COMMITTEE RESPONSIBILITY DRAWER ── -->
<div class="drawer" id="commResponsibilityDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Assign Tasks to Members &amp; Students</div>
    <button class="drawer-close" onclick="closeDrawer('commResponsibilityDrawer')">&times;</button>
  </div>
  <div class="form-group">
    <label>Select Committee Member or Student</label>
    <select id="commMemberSelect">
      <!-- Populated dynamically -->
    </select>
  </div>
  <div class="form-group">
<!-- ── UPCOMING EVENTS DIRECTORY DRAWER ── -->
<div class="drawer" id="upcomingEventsDirectoryDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Upcoming Department Events</div>
    <button class="drawer-close" onclick="closeDrawer('upcomingEventsDirectoryDrawer')">&times;</button>
  </div>
  <p style="font-size:0.85rem; color:var(--muted-dark);">Comprehensive directory of all upcoming approved events scheduled for the Department of AI &amp; ML.</p>
  <button class="btn btn-primary" style="width:100%; margin:4px 0 12px;" onclick="closeDrawer('upcomingEventsDirectoryDrawer'); openDrawer('createEventDrawer');">+ Propose New Event</button>
  
  <div style="flex:1; overflow-y:auto; display:flex; flex-direction:column; gap:10px;" id="drawerUpcomingEventsDirectoryList">
    <!-- Populated dynamically -->
  </div>
</div>

<script>
const sidebar=document.getElementById('sidebar'),overlay=document.getElementById('sidebarOverlay'),hamburger=document.getElementById('hamburgerBtn');
hamburger.addEventListener('click',()=>{sidebar.classList.toggle('open');overlay.classList.toggle('open');});
overlay.addEventListener('click',()=>{sidebar.classList.remove('open');overlay.classList.remove('open');closeAllDrawers();});

function openDrawer(id) {
  closeAllDrawers();
  document.getElementById(id).classList.add('open');
  overlay.classList.add('open');
}

function closeDrawer(id) {
  document.getElementById(id).classList.remove('open');
  overlay.classList.remove('open');
}

function closeAllDrawers() {
  document.querySelectorAll('.drawer').forEach(d => d.classList.remove('open'));
}

// Check logged in user session
let currentUser = <?php echo json_encode($sessionUser); ?> || JSON.parse(sessionStorage.getItem('current_user')) || {
  email: 'president@zealeducation.com',
  name: 'Association President',
  role: 'Association President'
};

function syncProfileFields() {
  if (currentUser) {
    if (document.getElementById('presNameInput')) document.getElementById('presNameInput').value = currentUser.name || '';
    if (document.getElementById('presEmailInput')) document.getElementById('presEmailInput').value = currentUser.email || '';
    if (document.getElementById('presZprnInput')) document.getElementById('presZprnInput').value = currentUser.zprn || '';
    if (document.getElementById('presPhoneInput')) document.getElementById('presPhoneInput').value = currentUser.phone || '';
    if (document.getElementById('presDrawerName')) document.getElementById('presDrawerName').textContent = currentUser.name || 'Association President';
  }
}
syncProfileFields();

document.querySelector('.content-title').innerHTML = `Welcome back, ${currentUser.name.split(' ')[0]}! 👋`;

// Handle Event Creation (Publish Event)
document.getElementById('saveEventBtn').addEventListener('click', async () => {
  const name = document.getElementById('evtName').value.trim();
  const cat = document.getElementById('evtCategory').value;
  const date = document.getElementById('evtDate').value;
  const time = document.getElementById('evtTime').value;
  const venue = document.getElementById('evtVenue').value.trim();
  const desc = document.getElementById('evtDescription').value.trim();
  const max = document.getElementById('evtMaxParticipants').value;
  const dl = document.getElementById('evtDeadline').value;
  const coordinator = document.getElementById('evtCoordinator').value;

  if (!name || !date || !time || !venue || !desc || !dl) {
    alert('Please fill out all event fields.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'createEvent');
  formData.append('name', name);
  formData.append('category', cat);
  formData.append('date', date);
  formData.append('time', time);
  formData.append('venue', venue);
  formData.append('description', desc);
  formData.append('max_participants', max);
  formData.append('registration_deadline', dl);
  formData.append('coordinator', coordinator);
  formData.append('status', 'Pending');

  try {
    const res = await fetch('ajax/eventActions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      addNotification('New Event Published', `${currentUser.name} proposed: ${name}.`, 'green', 'all');
      closeDrawer('createEventDrawer');
      alert('Event successfully proposed and published! Faculty will review it.');
      document.getElementById('evtName').value = '';
      document.getElementById('evtDate').value = '';
      document.getElementById('evtTime').value = '';
      document.getElementById('evtVenue').value = '';
      document.getElementById('evtDescription').value = '';
      document.getElementById('evtDeadline').value = '';
    } else {
      alert(data.message || 'Failed to create event');
    }
  } catch (e) {
    alert('Error creating event: ' + e.message);
  }
});

// Event Proposal buttons trigger create drawer
const navEventsEl = document.getElementById('navEvents');
if (navEventsEl) {
  navEventsEl.addEventListener('click', (e) => {
    e.preventDefault();
    openDrawer('createEventDrawer');
  });
}
const proposeBtn = document.getElementById('proposeEventBtn');
if (proposeBtn) {
  proposeBtn.addEventListener('click', () => {
    openDrawer('createEventDrawer');
  });
}

// Upcoming Events dynamic loading
async function loadUpcomingEventsFromDB() {
  const body = document.getElementById('upcomingEventsBody');
  const drawerList = document.getElementById('drawerUpcomingEventsDirectoryList');

  try {
    const res = await fetch('ajax/eventActions.php?action=getApprovedEvents');
    const data = await res.json();
    
    let events = [];
    if (data.status === 'success' && data.events.length > 0) {
      events = data.events;
    } else {
      events = [
        { name: 'AIMSA AI Hackathon 2026', date: '2026-07-28', venue: 'Main Auditorium', max_participants: 240, coordinator: 'Dr. Dipali Shende', description: '24-hour departmental hackathon on Generative AI models.' },
        { name: 'ML Bootcamp 2026', date: '2026-08-05', venue: 'Lab 402', max_participants: 180, coordinator: 'Prof. Manisha Devgunde', description: 'Hands-on Machine Learning bootcamp covering PyTorch and TensorFlow.' },
        { name: 'Robotics & Vision Workshop', date: '2026-08-10', venue: 'Seminar Hall 2', max_participants: 150, coordinator: 'Dr. Dipali Shende', description: 'Interactive workshop on Computer Vision and Edge Robotics.' },
        { name: 'Guest Lecture: ML in Healthcare', date: '2026-08-22', venue: 'Seminar Hall 1', max_participants: 120, coordinator: 'Prof. Manisha Devgunde', description: 'Expert talk on AI application in medical imaging.' }
      ];
    }

    if (body) {
      body.innerHTML = events.map(evt => `
        <div class="list-item">
          <div class="list-dot"></div>
          <div class="list-text">
            <b>${escapeHtml(evt.name)}</b>
            <span>${formatDate(evt.date)} · ${evt.max_participants || 100} seats · ${escapeHtml(evt.venue || 'Seminar Hall')}</span>
          </div>
        </div>`).join('');
    }

    if (drawerList) {
      drawerList.innerHTML = events.map(evt => `
        <div style="border:1px solid var(--line-dark); border-radius:12px; padding:14px; background:var(--paper); display:flex; flex-direction:column; gap:6px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <b style="font-size:0.92rem; color:var(--navy-950);">${escapeHtml(evt.name)}</b>
            <span class="badge badge-green">Scheduled</span>
          </div>
          <div style="font-size:0.75rem; color:var(--accent); font-family:var(--ff-mono); font-weight:600;">📅 ${formatDate(evt.date)} · 📍 ${escapeHtml(evt.venue || 'Seminar Hall')}</div>
          <p style="font-size:0.8rem; color:var(--navy-900); margin:4px 0;">${escapeHtml(evt.description || 'Departmental event organized by AIMSA.')}</p>
          <div style="font-size:0.72rem; color:var(--muted-dark); font-family:var(--ff-mono);">Coordinator: ${escapeHtml(evt.coordinator || 'Faculty')} · Cap: ${evt.max_participants || 100} students</div>
        </div>`).join('');
    }
  } catch (e) {
    console.error('Failed to load upcoming events:', e);
  }
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function formatDate(dateStr) {
  if (!dateStr) return 'TBD';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

const createEventActionBtn = document.getElementById('createEventActionBtn');
if (createEventActionBtn) {
  createEventActionBtn.addEventListener('click', (e) => {
    e.preventDefault();
    openDrawer('createEventDrawer');
  });
}

const createEventBtnFooter = document.getElementById('createEventBtnFooter');
if (createEventBtnFooter) {
  createEventBtnFooter.addEventListener('click', () => {
    openDrawer('createEventDrawer');
  });
}

// Committee & Student task assignment dropdown population
async function renderCommitteeList() {
  const select = document.getElementById('commMemberSelect');
  if (!select) return;

  try {
    const res = await fetch('ajax/president_actions.php?action=get_committee_members');
    const data = await res.json();
    if (data.status === 'success' && data.members.length > 0) {
      select.innerHTML = data.members.map(u => `<option value="${escapeHtml(u.email)}">${escapeHtml(u.name)} (${escapeHtml(u.committeeDesignation || u.role || 'Member')})</option>`).join('');
    } else {
      select.innerHTML = `<option value="">No eligible members/students found</option>`;
    }
  } catch (e) {
    const users = JSON.parse(localStorage.getItem('aimsa_users')) || [];
    select.innerHTML = '';
    users.filter(u => ((u.role || '').includes('Committee') || (u.role || '').includes('Student')) && !['Association President', 'Faculty Coordinator', 'HOD', 'Admin'].includes(u.role) && !(u.email || '').toLowerCase().includes('president') && !(u.email || '').toLowerCase().includes('faculty') && !(u.email || '').toLowerCase().includes('hod')).forEach(u => {
      select.innerHTML += `<option value="${escapeHtml(u.email)}">${escapeHtml(u.name)} (${escapeHtml(u.committeeDesignation || u.role || 'Member')})</option>`;
    });
  }
}

const navCommEl = document.getElementById('navCommittee');
if (navCommEl) {
  navCommEl.addEventListener('click', (e) => {
    e.preventDefault();
    renderCommitteeList();
    openDrawer('commResponsibilityDrawer');
  });
}

document.getElementById('saveResponsibilityBtn')?.addEventListener('click', async () => {
  const email = document.getElementById('commMemberSelect').value;
  const resp = document.getElementById('commResponsibilityText').value.trim();

  if(!resp || !email) {
    alert('Please select a member/student and enter the task details.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'update_committee_responsibility');
  formData.append('email', email);
  formData.append('responsibility', resp);

  try {
    const res = await fetch('ajax/president_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message || 'Task successfully assigned!');
      document.getElementById('commResponsibilityText').value = '';
      closeDrawer('commResponsibilityDrawer');
    } else {
      alert(data.message || 'Failed to assign task');
    }
  } catch (e) {
    alert('Error assigning task: ' + e.message);
  }
});

// Sync default buttons
document.querySelectorAll('.approve-btn').forEach(btn=>btn.addEventListener('click',()=>alert('Event request approved! Notification sent.')));
document.querySelectorAll('.reject-btn').forEach(btn=>btn.addEventListener('click',()=>alert('Event request rejected. Notification sent.')));

// Notification Helpers
function addNotification(title, text, indicator, recipient, email = true) {
  const records = JSON.parse(localStorage.getItem('aimsa_notifications')) || [];
  records.push({
    title,
    text,
    indicator, // 'green', 'yellow', 'red'
    recipient, // email or 'all'
    time: 'Just now',
    email
  });
  localStorage.setItem('aimsa_notifications', JSON.stringify(records));
  renderNotifications('notifications', currentUser.email);

  const formData = new FormData();
  formData.append('action', 'addNotification');
  formData.append('title', title);
  formData.append('text', text);
  formData.append('indicator', indicator);
  formData.append('recipient', recipient);
  formData.append('email_sent', email ? 1 : 0);
  fetch('ajax/notificationActions.php', { method: 'POST', body: formData }).catch(e => console.error('Failed to save notification to DB:', e));
}

async function loadNotificationsFromDB() {
  try {
    const params = new URLSearchParams();
    params.append('action', 'getNotifications');
    if (currentUser.email) params.append('email', currentUser.email);
    if (currentUser.role) params.append('role', currentUser.role);

    const res = await fetch('ajax/notificationActions.php?' + params.toString());
    const data = await res.json();
    if (data.status === 'success' && data.notifications.length > 0) {
      const existing = JSON.parse(localStorage.getItem('aimsa_notifications')) || [];
      const existingKeys = new Set(existing.map(n => (n.title || '') + '|' + (n.text || '')));

      data.notifications.forEach(n => {
        const key = (n.title || '') + '|' + (n.text || '');
        if (!existingKeys.has(key)) {
          existing.unshift({
            title: n.title,
            text: n.text,
            indicator: n.indicator || 'green',
            recipient: n.recipient || 'all',
            time: new Date(n.created_at).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }),
            email: true,
            id: n.id
          });
        }
      });

      localStorage.setItem('aimsa_notifications', JSON.stringify(existing));
    }
  } catch (e) {
    console.error('Failed to load notifications from DB:', e);
  }
}

function renderNotifications(containerId, userEmail) {
  const container = document.getElementById(containerId);
  if (!container) return;

  const records = JSON.parse(localStorage.getItem('aimsa_notifications')) || [];
  const filtered = records.filter(r => {
    if (r.recipient === 'all') return true;
    if (r.recipient.toLowerCase() === userEmail.toLowerCase()) return true;
    if (currentUser.role && currentUser.role.toLowerCase() === r.recipient.toLowerCase()) return true;
    return false;
  });

  // Clear container but keep head
  container.innerHTML = `
    <div class="card-head">
      <div class="card-title">Notifications</div>
      <span class="card-action" style="cursor:pointer;" onclick="clearNotifications('${containerId}', '${userEmail}')">Mark Read</span>
    </div>`;

  if (filtered.length === 0) {
    container.innerHTML += `<p style="font-size:0.8rem; color:var(--muted-dark); padding:20px 10px;">No new alerts.</p>`;
    return;
  }

  filtered.slice(-5).reverse().forEach(n => {
    let dotColor = '#22c55e';
    let dotShadow = 'rgba(34,197,94,.18)';
    let indicatorEmoji = '🟢';
    if (n.indicator === 'yellow') {
      dotColor = '#fbbf24';
      dotShadow = 'rgba(251,191,36,.18)';
      indicatorEmoji = '🟡';
    } else if (n.indicator === 'red') {
      dotColor = '#ef4444';
      dotShadow = 'rgba(239,68,68,.18)';
      indicatorEmoji = '🔴';
    }

    const emailTag = n.email ? `<span style="display:inline-flex; align-items:center; gap:2px; background:rgba(62,139,255,0.1); color:var(--accent); font-size:0.62rem; padding:1px 6px; border-radius:4px; font-weight:600; margin-left:8px;">✉️ Email Sent</span>` : '';
    const smsTag = `<span style="display:inline-flex; align-items:center; gap:2px; background:rgba(8,23,51,0.05); color:var(--muted-dark); font-size:0.62rem; padding:1px 6px; border-radius:4px; font-weight:500; margin-left:8px;">📱 SMS (Enhancement)</span>`;

    container.innerHTML += `
      <div class="list-item">
        <div class="list-dot" style="background:${dotColor}; box-shadow:0 0 0 3px ${dotShadow};"></div>
        <div class="list-text">
          <b>${n.title} ${emailTag}${smsTag}</b>
          <span>${n.text} · ${n.time}</span>
        </div>
      </div>`;
  });
}

window.clearNotifications = function(containerId, userEmail) {
  let records = JSON.parse(localStorage.getItem('aimsa_notifications')) || [];
  records = records.filter(r => {
    if (r.recipient.toLowerCase() === userEmail.toLowerCase()) return false;
    if (currentUser.role && r.recipient.toLowerCase() === currentUser.role.toLowerCase()) return false;
    return true;
  });
  localStorage.setItem('aimsa_notifications', JSON.stringify(records));
  renderNotifications(containerId, userEmail);
};

// Header Sync
document.getElementById('headerUserName').textContent = currentUser.name;
document.getElementById('headerUserRole').textContent = currentUser.role;
document.getElementById('headerUserAvatar').textContent = currentUser.name.split(' ').map(n=>n[0]).join('').toUpperCase();

// Profile Dropdown Toggle
window.toggleProfileDropdown = function() {
  const d = document.getElementById('profileDropdown');
  d.style.display = d.style.display === 'none' ? 'block' : 'none';
};
document.addEventListener('click', (e) => {
  const wrapper = document.getElementById('profileMenuWrapper');
  if(wrapper && !wrapper.contains(e.target)) {
    document.getElementById('profileDropdown').style.display = 'none';
  }
});

// Language Switch
window.changeLanguage = function() {
  const lang = document.getElementById('langSelect').value;
  if(lang === 'mr') {
    alert('पोर्टलची भाषा यशस्वीरीत्या मराठीमध्ये बदलली आहे. (Portal language successfully switched to Marathi)');
  } else {
    alert('Portal language successfully switched to English.');
  }
};

// Change Password save action
document.getElementById('savePasswordBtn').addEventListener('click', () => {
  const curr = document.getElementById('currPassword').value;
  const newp = document.getElementById('newPassword').value;
  const conf = document.getElementById('confirmNewPassword').value;

  if(!curr || !newp || !conf) {
    alert('Please fill out all password fields.');
    return;
  }
  if(newp !== conf) {
    alert('New passwords do not match.');
    return;
  }

  const users = JSON.parse(localStorage.getItem('aimsa_users')) || [];
  const idx = users.findIndex(u => u.email.toLowerCase() === currentUser.email.toLowerCase());
  if (idx !== -1) {
    if(users[idx].password !== curr) {
      alert('Incorrect current password.');
      return;
    }
    users[idx].password = newp;
    localStorage.setItem('aimsa_users', JSON.stringify(users));
    alert('Password updated successfully!');
    addNotification('Password Changed Successfully', 'Your secure portal access credentials were changed.', 'green', currentUser.email);
    closeDrawer('changePasswordDrawer');
  }
});

// Search bar input filtering
document.getElementById('headerSearchInput').addEventListener('input', (e) => {
  const query = e.target.value.toLowerCase().trim();
  document.querySelectorAll('.list-item, .task-item, .achievement-badge, tr').forEach(el => {
    if(query === '') {
      el.style.display = '';
    } else {
      const text = el.textContent.toLowerCase();
      if(text.includes(query)) {
        el.style.display = '';
      } else {
        el.style.display = 'none';
      }
    }
  });
});

window.openNotifications = function() {
  const notifCard = document.getElementById('notifications');
  if(notifCard) {
    notifCard.scrollIntoView({behavior: 'smooth'});
    notifCard.style.outline = '2px solid var(--accent)';
    setTimeout(() => { notifCard.style.outline = 'none'; }, 2000);
  }
};

// Animation stats counter and initial notifications render
loadNotificationsFromDB().then(() => {
  renderNotifications('notifications', currentUser.email);
});
loadUpcomingEventsFromDB();

document.querySelectorAll('.stat-val').forEach(el=>{
  const raw=el.textContent.replace(/,/g,'');
  const target=parseInt(raw.replace(/\D/g,''));
  if(isNaN(target))return;
  const suffix=el.textContent.replace(/[\d,]/g,'');
  let current=0;const step=Math.ceil(target/50);
  const timer=setInterval(()=>{current=Math.min(current+step,target);el.textContent=current.toLocaleString()+suffix;if(current>=target)clearInterval(timer);},25);
});

// ---------- PRESIDENT DASHBOARD JS LOGIC & EVENT HANDLERS ----------

// Sync User UI Info
function syncUserInfoUI() {
  const name = currentUser.name || 'Association President';
  const role = currentUser.role || 'Association President';
  const avatar = name.split(' ').map(n=>n[0]).join('').toUpperCase();

  if (document.getElementById('headerUserName')) document.getElementById('headerUserName').textContent = name;
  if (document.getElementById('headerUserRole')) document.getElementById('headerUserRole').textContent = role;
  if (document.getElementById('headerUserAvatar')) document.getElementById('headerUserAvatar').textContent = avatar;

  if (document.getElementById('sidebarUserName')) document.getElementById('sidebarUserName').textContent = name;
  if (document.getElementById('sidebarAvatar')) document.getElementById('sidebarAvatar').textContent = avatar;

  if (document.getElementById('presDrawerName')) document.getElementById('presDrawerName').textContent = name;
  if (document.getElementById('presDrawerAvatar')) document.getElementById('presDrawerAvatar').textContent = avatar;
  if (document.getElementById('presNameInput')) document.getElementById('presNameInput').value = name;
  if (document.getElementById('presEmailInput')) document.getElementById('presEmailInput').value = currentUser.email || 'president@zealeducation.com';
  if (document.getElementById('presZprnInput')) document.getElementById('presZprnInput').value = currentUser.zprn || '125UAM1003';
}
syncUserInfoUI();

// 1. SAVE PROFILE CHANGES HANDLER
document.getElementById('savePresProfileBtn')?.addEventListener('click', async () => {
  const name = document.getElementById('presNameInput').value.trim();
  const email = document.getElementById('presEmailInput').value.trim();
  const zprn = document.getElementById('presZprnInput').value.trim();
  const phone = document.getElementById('presPhoneInput').value.trim();

  if (!name || !email) {
    alert('Please enter your full name and email.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'update_president_profile');
  formData.append('name', name);
  formData.append('email', email);
  formData.append('zprn', zprn);
  formData.append('phone', phone);

  try {
    const res = await fetch('ajax/president_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      currentUser.name = name;
      currentUser.email = email;
      currentUser.zprn = zprn;
      sessionStorage.setItem('current_user', JSON.stringify(currentUser));
      syncUserInfoUI();
      alert('President profile updated successfully!');
      closeDrawer('viewProfileDrawer');
    } else {
      alert(data.message || 'Failed to update profile.');
    }
  } catch (e) {
    alert('Error updating profile: ' + e.message);
  }
});

// 2. COMMITTEE MEMBERS MANAGEMENT
async function loadCommitteeMembersFromDB() {
  const select = document.getElementById('manageCommMemberSelect');
  const tbody = document.getElementById('drawerCommitteeMembersBody');
  if (!select || !tbody) return;

  try {
    const res = await fetch('ajax/president_actions.php?action=get_committee_members');
    const data = await res.json();

    if (data.status === 'success' && data.members.length > 0) {
      select.innerHTML = data.members.map(m => `<option value="${escapeHtml(m.email)}">${escapeHtml(m.name)} (${escapeHtml(m.committeeDesignation || m.role || 'Member')})</option>`).join('');
      
      tbody.innerHTML = data.members.map(m => `
        <tr>
          <td><b>${escapeHtml(m.name)}</b></td>
          <td><span style="font-size:0.75rem; color:var(--muted-dark); font-family:var(--ff-mono);">${escapeHtml(m.committeeDesignation || m.role)}<br>${escapeHtml(m.email)}</span></td>
          <td><span style="font-family:var(--ff-mono); font-size:0.72rem; color:var(--accent); font-weight:600;">${escapeHtml(m.zprn || '125UAM1001')}</span></td>
          <td><span style="font-size:0.78rem; color:var(--navy-900);">${escapeHtml(m.committeeResponsibility || 'Lead departmental events & outreach')}</span></td>
        </tr>`).join('');
    } else {
      tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--muted-dark);">No committee members registered in DB.</td></tr>`;
    }
  } catch (e) {
    console.error('Failed to load committee members:', e);
  }
}

document.getElementById('btnAssignCommTask')?.addEventListener('click', async () => {
  const email = document.getElementById('manageCommMemberSelect').value;
  const task = document.getElementById('manageCommTaskText').value.trim();

  if (!task) {
    alert('Please enter the task / action plan to assign.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'update_committee_responsibility');
  formData.append('email', email);
  formData.append('responsibility', task);

  try {
    const res = await fetch('ajax/president_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert('Task successfully assigned to committee member!');
      document.getElementById('manageCommTaskText').value = '';
      loadCommitteeMembersFromDB();
    } else {
      alert(data.message || 'Failed to assign task');
    }
  } catch (e) {
    alert('Error assigning task: ' + e.message);
  }
});

// 3. PENDING APPROVALS DECISION PROCESSOR
window.processApprovalItem = function(itemName, approved, btnEl) {
  const card = btnEl.closest('.approval-item');
  if (approved) {
    alert(`"${itemName}" has been approved by Association President! Notification logged.`);
    addNotification('Proposal Approved', `President approved: ${itemName}.`, 'green', 'all');
    if (card) {
      card.style.transition = 'all 0.3s ease';
      card.style.opacity = '0';
      card.style.transform = 'translateX(20px)';
      setTimeout(() => card.remove(), 300);
    }
  } else {
    alert(`"${itemName}" proposal has been rejected.`);
    addNotification('Proposal Declined', `President declined: ${itemName}.`, 'red', 'all');
    if (card) {
      card.style.transition = 'all 0.3s ease';
      card.style.opacity = '0';
      setTimeout(() => card.remove(), 300);
    }
  }
};

// 4. EVENT REGISTRATIONS BREAKDOWN
async function loadEventRegistrationsFromDB() {
  const tbody = document.getElementById('drawerEventRegistrationsBody');
  if (!tbody) return;

  try {
    const res = await fetch('ajax/eventActions.php?action=getApprovedEvents');
    const data = await res.json();

    if (data.status === 'success' && data.events.length > 0) {
      tbody.innerHTML = data.events.map(evt => `
        <tr>
          <td><b>${escapeHtml(evt.name)}</b></td>
          <td><span style="font-family:var(--ff-mono); font-size:0.75rem;">${formatDate(evt.date)}</span></td>
          <td>${escapeHtml(evt.venue || 'Seminar Hall')}</td>
          <td><b style="color:var(--accent); font-family:var(--ff-mono);">${evt.max_participants || 120}</b></td>
        </tr>`).join('');
    } else {
      tbody.innerHTML = `
        <tr><td><b>AIMSA AI Hackathon 2026</b></td><td>Jul 28</td><td>Auditorium</td><td><b style="color:var(--accent);">240</b></td></tr>
        <tr><td><b>ML Bootcamp 2026</b></td><td>Aug 05</td><td>Lab 402</td><td><b style="color:var(--accent);">180</b></td></tr>
        <tr><td><b>Robotics Workshop</b></td><td>Aug 10</td><td>Seminar Hall 2</td><td><b style="color:var(--accent);">150</b></td></tr>`;
    }
  } catch (e) {
    console.error('Failed to load event registrations:', e);
  }
}

// 5. ANNOUNCEMENTS BROADCASTING & FEED
async function loadAnnouncementsFromDB() {
  const drawerList = document.getElementById('drawerAnnouncementsList');
  const cardList = document.getElementById('announcementsListBody');

  try {
    const res = await fetch('ajax/president_actions.php?action=get_announcements');
    const data = await res.json();

    if (data.status === 'success' && data.announcements.length > 0) {
      const html = data.announcements.map(a => {
        let priorityClass = '';
        if (a.priority === 'Urgent') priorityClass = 'urgent';
        if (a.priority === 'Important') priorityClass = 'important';

        return `
          <div class="ann-card ${priorityClass}">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
              <div class="ann-title">${escapeHtml(a.title)}</div>
              <button onclick="deleteAnnouncement(${a.id})" style="border:none; background:none; color:#ef4444; font-size:0.75rem; font-weight:700; cursor:pointer; padding:2px 6px;" title="Delete Announcement">🗑️ Delete</button>
            </div>
            <div class="ann-meta">Posted by ${escapeHtml(a.posted_by)} · ${a.target_audience} · ${a.views_count} views</div>
            <p style="font-size:0.78rem; color:var(--navy-900); margin-top:4px;">${escapeHtml(a.content)}</p>
          </div>`;
      }).join('');

      if (drawerList) drawerList.innerHTML = html;
      if (cardList) cardList.innerHTML = html;
    }
  } catch (e) {
    console.error('Failed to load announcements:', e);
  }
}

window.deleteAnnouncement = async function(id) {
  if (confirm('Are you sure you want to delete this announcement?')) {
    const formData = new FormData();
    formData.append('action', 'delete_announcement');
    formData.append('id', id);

    const res = await fetch('ajax/president_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert('Announcement deleted successfully!');
      loadAnnouncementsFromDB();
    } else {
      alert(data.message || 'Failed to delete announcement');
    }
  }
};

document.getElementById('btnPublishAnnouncement')?.addEventListener('click', async () => {
  const title = document.getElementById('annTitleInput').value.trim();
  const content = document.getElementById('annContentInput').value.trim();
  const priority = document.getElementById('annPrioritySelect').value;
  const audience = document.getElementById('annAudienceSelect').value;

  if (!title || !content) {
    alert('Please enter announcement title and content.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'post_announcement');
  formData.append('title', title);
  formData.append('content', content);
  formData.append('priority', priority);
  formData.append('audience', audience);

  try {
    const res = await fetch('ajax/president_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert('Announcement published successfully to department broadcast feed!');
      document.getElementById('annTitleInput').value = '';
      document.getElementById('annContentInput').value = '';
      loadAnnouncementsFromDB();
      closeDrawer('manageAnnouncementsDrawer');
    } else {
      alert(data.message || 'Failed to post announcement.');
    }
  } catch (e) {
    alert('Error posting announcement: ' + e.message);
  }
});

// 6. BIND ALL BUTTONS & SIDEBAR NAVIGATION LINKS
document.getElementById('navCommittee')?.addEventListener('click', (e) => {
  e.preventDefault();
  loadCommitteeMembersFromDB();
  openDrawer('manageCommitteeDrawer');
});

document.getElementById('committee')?.addEventListener('click', () => {
  loadCommitteeMembersFromDB();
  openDrawer('manageCommitteeDrawer');
});

document.getElementById('btnManageCommittee')?.addEventListener('click', (e) => {
  e.stopPropagation();
  loadCommitteeMembersFromDB();
  openDrawer('manageCommitteeDrawer');
});

document.getElementById('navRegistrations')?.addEventListener('click', (e) => {
  e.preventDefault();
  loadEventRegistrationsFromDB();
  openDrawer('eventRegistrationsDrawer');
});

document.getElementById('registrations')?.addEventListener('click', () => {
  loadEventRegistrationsFromDB();
  openDrawer('eventRegistrationsDrawer');
});

document.getElementById('navApprovals')?.addEventListener('click', (e) => {
  e.preventDefault();
  openDrawer('pendingApprovalsDrawer');
});

document.getElementById('approvals')?.addEventListener('click', () => {
  openDrawer('pendingApprovalsDrawer');
});

document.getElementById('btnViewAllPending')?.addEventListener('click', (e) => {
  e.stopPropagation();
  openDrawer('pendingApprovalsDrawer');
});

document.getElementById('navCreateEvent')?.addEventListener('click', (e) => {
  e.preventDefault();
  openDrawer('createEventDrawer');
});

document.getElementById('navUpcomingEvents')?.addEventListener('click', (e) => {
  e.preventDefault();
  loadUpcomingEventsFromDB();
  openDrawer('upcomingEventsDirectoryDrawer');
});

document.getElementById('btnViewAllUpcoming')?.addEventListener('click', (e) => {
  e.stopPropagation();
  loadUpcomingEventsFromDB();
  openDrawer('upcomingEventsDirectoryDrawer');
});

document.getElementById('upcoming')?.addEventListener('click', () => {
  loadUpcomingEventsFromDB();
  openDrawer('upcomingEventsDirectoryDrawer');
});

document.getElementById('navAnnouncements')?.addEventListener('click', (e) => {
  e.preventDefault();
  loadAnnouncementsFromDB();
  openDrawer('manageAnnouncementsDrawer');
});

document.getElementById('btnPostAnnouncement')?.addEventListener('click', (e) => {
  e.stopPropagation();
  loadAnnouncementsFromDB();
  openDrawer('manageAnnouncementsDrawer');
});

document.getElementById('navNotifications')?.addEventListener('click', (e) => {
  e.preventDefault();
  openNotifications();
});

// Initial Data Load
loadAnnouncementsFromDB();
</script>
<script src="assets/js/landing.js"></script>
</body>
</html>
