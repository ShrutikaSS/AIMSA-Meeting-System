<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/include/dbConfig.php';

$sessionUser = $_SESSION['user'] ?? null;
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
.ann-card{padding:16px;border-radius:12px;border:1px solid var(--line-dark);background:var(--paper);margin-bottom:10px;transition:.2s ease;position:relative;overflow:hidden;}
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
@media(max-width:768px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}.sidebar-overlay.open{display:block;}.main{margin-left:0;}.hamburger-btn{display:flex;}.content{padding:20px;}.stats-grid{grid-template-columns:1fr 1fr;}.dash-grid-3{grid-template-columns:1fr;}}
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
  <div class="sidebar-role">
    <div class="role-avatar"><div class="in">AP</div></div>
    <div class="role-info"><b>Karan Mehta</b><span>Association President</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <a class="nav-item active" href="president_dashboard.html">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard
    </a>
    <div class="nav-section-label">Management</div>
    <a class="nav-item" href="#committee">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>Committee Members
    </a>
    <a class="nav-item" href="#registrations">
      <svg class="nav-icon" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Event Registrations
    </a>
    <a class="nav-item" href="#approvals">
      <svg class="nav-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Pending Approvals
      <span class="nav-badge">5</span>
    </a>
    <a class="nav-item" href="#upcoming">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Upcoming Events
    </a>
    <div class="nav-section-label">Communication</div>
    <a class="nav-item" href="#announcements">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M22 4s-3.5 3-10 3S2 4 2 4v6c0 5.52 4.48 10 10 10s10-4.48 10-10V4z"/></svg>Announcements
    </a>
    <a class="nav-item" href="#notifications">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>Notifications
      <span class="nav-badge">6</span>
    </a>
    <div class="nav-section-label">Account</div>
    <a class="nav-item" href="#"><svg class="nav-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>Profile</a>
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
            <span style="font-size:0.78rem; font-weight:600; color:var(--navy-950);" id="headerUserName">Karan Mehta</span>
            <span style="font-size:0.65rem; color:var(--muted-dark);" id="headerUserRole">Association President</span>
          </div>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div id="profileDropdown" style="display:none; position:absolute; right:0; top:42px; background:var(--white); border:1px solid var(--line-dark); border-radius:12px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1); width:180px; z-index:150; padding:6px 0;">
          <a href="#" onclick="openDrawer('changePasswordDrawer'); toggleProfileDropdown(); return false;" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:var(--navy-950); text-decoration:none; font-weight:500;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Change Password
          </a>
          <div style="border-top:1px solid var(--line-dark); margin:4px 0;"></div>
          <a href="index.php" onclick="sessionStorage.removeItem('current_user');" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:#ef4444; text-decoration:none; font-weight:600;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            Logout
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="section-eyebrow">Association President</div>
    <div class="content-title">Hey, Karan! ⭐</div>
    <div class="content-sub">AIMSA leadership dashboard — July 21, 2026</div>

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
        <div class="stat-label">Upcoming Events</div>
        <span class="stat-delta up">↑ Next: Jul 28</span>
      </div>
    </div>

    <!-- MAIN GRID -->
    <div class="dash-grid">
      <!-- Pending Approvals -->
      <div class="card">
        <div class="card-head"><div class="card-title">Pending Approvals</div><span class="card-action">View All</span></div>
        <div class="approval-item">
          <div class="approval-info"><b>Robotics Workshop — Aug 10</b><span>Proposed by Tech Committee · Budget: ₹12,000</span></div>
          <div class="approval-actions"><button class="approve-btn">Approve</button><button class="reject-btn">Reject</button></div>
        </div>
        <div class="approval-item">
          <div class="approval-info"><b>Cultural Night 2026 — Aug 20</b><span>Proposed by Cultural Committee · Budget: ₹25,000</span></div>
          <div class="approval-actions"><button class="approve-btn">Approve</button><button class="reject-btn">Reject</button></div>
        </div>
        <div class="approval-item">
          <div class="approval-info"><b>Alumni Connect — Sep 5</b><span>Proposed by Outreach Committee · Budget: ₹8,000</span></div>
          <div class="approval-actions"><button class="approve-btn">Approve</button><button class="reject-btn">Reject</button></div>
        </div>
        <div class="approval-item">
          <div class="approval-info"><b>Media Partnership — MNC Brand</b><span>Sponsorship proposal · Outreach Committee</span></div>
          <div class="approval-actions"><button class="approve-btn">Approve</button><button class="reject-btn">Reject</button></div>
        </div>
        <div class="approval-item">
          <div class="approval-info"><b>New Committee Member — Harsh Shah</b><span>Finance Committee · Application pending</span></div>
          <div class="approval-actions"><button class="approve-btn">Accept</button><button class="reject-btn">Decline</button></div>
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

    <!-- BOTTOM GRID -->
    <div class="dash-grid-3">
      <!-- Committee Members -->
      <div class="card">
        <div class="card-head"><div class="card-title">Committee Members</div><span class="card-action">Manage</span></div>
        <div class="member-card"><div class="member-avatar"><div class="in">RD</div></div><div class="member-info"><b>Riya Desai</b><span>Technical Committee Lead</span></div><span class="badge badge-green">Lead</span></div>
        <div class="member-card"><div class="member-avatar"><div class="in">AK</div></div><div class="member-info"><b>Aman Kulkarni</b><span>Cultural Committee Lead</span></div><span class="badge badge-blue">Lead</span></div>
        <div class="member-card"><div class="member-avatar"><div class="in">PJ</div></div><div class="member-info"><b>Priya Joshi</b><span>Media & PR Committee</span></div><span class="badge badge-green">Member</span></div>
        <div class="member-card"><div class="member-avatar"><div class="in">DP</div></div><div class="member-info"><b>Dev Patil</b><span>Finance Committee</span></div><span class="badge badge-orange">Forming</span></div>
        <div class="member-card"><div class="member-avatar"><div class="in">SR</div></div><div class="member-info"><b>Sneha Rao</b><span>Outreach Committee Lead</span></div><span class="badge badge-green">Lead</span></div>
      </div>

      <!-- Upcoming Events -->
      <div class="card">
        <div class="card-head"><div class="card-title">Upcoming Events</div><span class="card-action">Create Event</span></div>
        <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Tech Symposium 2026</b><span>Jul 28 · 148 registered · Auditorium</span></div></div>
        <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>AI Workshop Series</b><span>Aug 3 · 67 registered · Lab 402</span></div></div>
        <div class="list-item"><div class="list-dot" style="background:#f97316;box-shadow:0 0 0 3px rgba(249,115,22,.18);"></div><div class="list-text"><b>Hackathon 2026</b><span>Aug 15 · 203 registered · Online + Campus</span></div></div>
        <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Guest Lecture: ML in Healthcare</b><span>Aug 22 · 89 registered · Seminar Hall</span></div></div>
        <button class="btn btn-primary" style="width:100%;margin-top:16px;justify-content:center;">+ Create New Event</button>
      </div>

      <!-- Announcements -->
      <div class="card" id="announcements">
        <div class="card-head"><div class="card-title">Announcements</div><span class="card-action">Post New</span></div>
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

  <!-- consistent footer -->
  <footer class="portal-footer" style="margin-top:40px; padding:24px 30px; background:var(--white); border-top:1px solid var(--line-dark); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
    <div style="font-size:0.78rem; color:var(--muted-dark);">
      <span>© 2026 <b>Department of AIML</b>, Zeal College of Engineering and Research, Pune. All rights reserved.</span>
    </div>
    <div style="display:flex; align-items:center; gap:20px; font-size:0.75rem; color:var(--muted-dark);">
      <span>📧 Support: <a href="mailto:support.aimsa@zealeducation.com" style="color:var(--accent); text-decoration:none; font-weight:600;">support.aimsa@zealeducation.com</a></span>
      <span>📞 Call: <b>+91 20 6720 6000</b></span>
      <span style="color:var(--line-dark);">|</span>
      <a href="#" onclick="alert('Privacy Policy: All membership data is kept strictly confidential within Zeal Society.')" style="color:inherit; text-decoration:none; font-weight:600;">Privacy Policy</a>
      <a href="#" onclick="alert('Terms &amp; Conditions: AIMSA portal usage is governed by college guidelines.')" style="color:inherit; text-decoration:none; font-weight:600;">Terms &amp; Conditions</a>
      <span style="color:var(--line-dark);">|</span>
      <span>Version: <b>v2.1.0</b></span>
      <span>Last Updated: <b>July 21, 2026</b></span>
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
    <input type="number" id="evtMaxParticipants" value="100">
  </div>
  <div class="form-group">
    <label>Registration Deadline</label>
    <input type="date" id="evtDeadline">
  </div>
  <div class="form-group">
    <label>Faculty Coordinator</label>
    <select id="evtCoordinator">
      <option value="Prof. Meera Nair">Prof. Meera Nair</option>
      <option value="Dipali Shende">Dipali Shende</option>
    </select>
  </div>
  <button class="btn btn-primary" style="width:100%; margin-top:10px;" id="saveEventBtn">Publish Event</button>
</div>

<!-- ── COMMITTEE RESPONSIBILITY DRAWER ── -->
<div class="drawer" id="commResponsibilityDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Assign Responsibilities</div>
    <button class="drawer-close" onclick="closeDrawer('commResponsibilityDrawer')">&times;</button>
  </div>
  <div class="form-group">
    <label>Select Committee Member</label>
    <select id="commMemberSelect">
      <!-- Populated dynamically -->
    </select>
  </div>
  <div class="form-group">
    <label>Assign Responsibility / Action Plan</label>
    <textarea id="commResponsibilityText" rows="4" placeholder="Enter assigned tasks..."></textarea>
  </div>
  <button class="btn btn-primary" style="width:100%; margin-top:10px;" id="saveResponsibilityBtn">Assign Task</button>
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

document.querySelector('.content-title').innerHTML = `Welcome back, ${currentUser.name.split(' ')[0]}! 👋`;

// Handle Event Creation (Publish Event)
document.getElementById('saveEventBtn').addEventListener('click', () => {
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

  // Save new event to localStorage
  const events = JSON.parse(localStorage.getItem('aimsa_published_events')) || [];
  events.push({name, cat, date, time, venue, desc, max, dl, coordinator});
  localStorage.setItem('aimsa_published_events', JSON.stringify(events));

  addNotification('New Event Published', `${currentUser.name} proposed: ${name}.`, 'green', 'all');

  // Update stats counters
  const approvedStat = document.querySelector('#upcomingEvents .stat-val');
  if (approvedStat) {
    approvedStat.textContent = parseInt(approvedStat.textContent) + 1;
  }

  closeDrawer('createEventDrawer');
  alert('Event successfully proposed and published! Student notifications sent.');
});

// Event Proposal buttons trigger create drawer
document.getElementById('navEvents').addEventListener('click', (e) => {
  e.preventDefault();
  openDrawer('createEventDrawer');
});
document.getElementById('proposeEventBtn').addEventListener('click', () => {
  openDrawer('createEventDrawer');
});

// Committee list rendering
function renderCommitteeList() {
  const users = JSON.parse(localStorage.getItem('aimsa_users')) || [];
  const select = document.getElementById('commMemberSelect');
  select.innerHTML = '';
  users.filter(u => u.role === 'Committee Member' || u.committeeDesignation).forEach(u => {
    select.innerHTML += `<option value="${u.email}">${u.name} (${u.committeeDesignation || 'Member'})</option>`;
  });
}

document.getElementById('navCommittee').addEventListener('click', (e) => {
  e.preventDefault();
  renderCommitteeList();
  openDrawer('commResponsibilityDrawer');
});
document.getElementById('committeeCard').addEventListener('click', () => {
  renderCommitteeList();
  openDrawer('commResponsibilityDrawer');
});

document.getElementById('saveResponsibilityBtn').addEventListener('click', () => {
  const email = document.getElementById('commMemberSelect').value;
  const resp = document.getElementById('commResponsibilityText').value.trim();

  if(!resp) {
    alert('Please enter responsibilities to assign.');
    return;
  }

  const users = JSON.parse(localStorage.getItem('aimsa_users')) || [];
  const idx = users.findIndex(u => u.email.toLowerCase() === email.toLowerCase());
  if (idx !== -1) {
    users[idx].committeeResponsibility = resp;
    localStorage.setItem('aimsa_users', JSON.stringify(users));
    alert('Responsibilities successfully assigned to the committee member!');
    closeDrawer('commResponsibilityDrawer');
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
}

function renderNotifications(containerId, userEmail) {
  const container = document.getElementById(containerId);
  if (!container) return;

  const records = JSON.parse(localStorage.getItem('aimsa_notifications')) || [];
  const filtered = records.filter(r => r.recipient === 'all' || r.recipient.toLowerCase() === userEmail.toLowerCase());

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
  records = records.filter(r => r.recipient !== 'all' && r.recipient.toLowerCase() !== userEmail.toLowerCase());
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
renderNotifications('notifications', currentUser.email);

document.querySelectorAll('.stat-val').forEach(el=>{
  const raw=el.textContent.replace(/,/g,'');
  const target=parseInt(raw.replace(/\D/g,''));
  if(isNaN(target))return;
  const suffix=el.textContent.replace(/[\d,]/g,'');
  let current=0;const step=Math.ceil(target/50);
  const timer=setInterval(()=>{current=Math.min(current+step,target);el.textContent=current.toLocaleString()+suffix;if(current>=target)clearInterval(timer);},25);
});

document.querySelectorAll('.nav-item').forEach(item=>{
  item.addEventListener('click',(e)=>{
    if(item.href&&(item.href.includes('index')||item.href.includes('president_dashboard')))return;
    e.preventDefault();
    document.querySelectorAll('.nav-item').forEach(i=>i.classList.remove('active'));
    item.classList.add('active');
  });
});
</script>
</body>
</html>
