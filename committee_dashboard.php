<?php
require_once __DIR__ . '/include/dbConfig.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 13;
$user_name = $_SESSION['user_name'] ?? 'Riya Desai';
$user_email = $_SESSION['user_email'] ?? 'committee@zealeducation.com';
$user_role = $_SESSION['user_role'] ?? 'Committee Member';
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
.stat-card{background:var(--white);border-radius:16px;padding:22px 24px;border:1px solid var(--line-dark);transition:transform .2s ease,box-shadow .3s ease;position:relative;overflow:hidden;cursor:pointer;}
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
.data-table th{font-family:var(--ff-mono);font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted-dark);padding:10px 12px;text-align:left;border-bottom:1px solid var(--line-dark);}
.data-table td{padding:12px 12px;font-size:.85rem;border-bottom:1px solid rgba(8,23,51,.05);}
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

/* Tab Sections Control */
.tab-section { display: none; }
.tab-section.active-tab { display: block; animation: fadeIn 0.25s ease-in-out; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(6px); }
  to { opacity: 1; transform: translateY(0); }
}

@media(max-width:1100px){.stats-grid{grid-template-columns:repeat(2,1fr);}.dash-grid{grid-template-columns:1fr;}.dash-grid-3{grid-template-columns:1fr 1fr;}}
@media(max-width:768px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}.sidebar-overlay.open{display:block;}.main{margin-left:0;}.hamburger-btn{display:flex;}.content{padding:20px;}.stats-grid{grid-template-columns:1fr 1fr;}.dash-grid-3{grid-template-columns:1fr;}.cert-grid{grid-template-columns:1fr;}}
@media(max-width:480px){.stats-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo">
      <img src="images/aimsa_logo.jpg" alt="AIMSA Logo" style="width:100%; height:100%; object-fit:cover;">
    </div>
    <div class="brand-info"><b>AIMSA Portal</b><span>Committee Access</span></div>
  </div>
  <div class="sidebar-role">
    <div class="role-avatar"><div class="in" id="sidebarUserInitials">RD</div></div>
    <div class="role-info"><b id="sidebarUserName"><?php echo htmlspecialchars($user_name); ?></b><span id="sidebarUserRole">Committee Member</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <a class="nav-item active" id="nav-dashboard" onclick="showSection('dashboard')">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard
    </a>
    <div class="nav-section-label">Events</div>
    <a class="nav-item" id="nav-assigned" onclick="showSection('assigned')">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Assigned Events
    </a>
    <a class="nav-item" id="nav-attendance" onclick="showSection('attendance')">
      <svg class="nav-icon" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Attendance Records
    </a>
    <a class="nav-item" id="nav-reports" onclick="showSection('reports')">
      <svg class="nav-icon" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>Event Reports
    </a>
    <div class="nav-section-label">Communication</div>
    <a class="nav-item" id="nav-notifications" onclick="showSection('notifications')">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>Notifications
      <span class="nav-badge" id="sidebarNotifBadge">0</span>
    </a>
    <div class="nav-section-label">Account</div>
    <a class="nav-item" id="nav-profile" onclick="showSection('profile')">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>Profile
    </a>
  </nav>
  <div class="sidebar-footer">
    <a class="nav-item" href="index.php" onclick="sessionStorage.clear();"><svg class="nav-icon" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>Logout</a>
  </div>
</aside>

<div class="main">
  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="hamburger-btn" id="hamburgerBtn"><span></span><span></span><span></span></button>
      <div class="logo-container">
        <img src="images/icons/college_logo.png" alt="Zeal Logo" style="height:32px; width:32px; border-radius:50%; object-fit:cover;" title="Zeal Education Society">
        <img src="images/aimsa_logo.jpg" alt="AIMSA Logo" style="height:32px; width:auto; border-radius:50%; object-fit:contain;" title="AIMSA Association">
      </div>
      <div>
        <div class="page-title" id="topbarTitle">AIMSA Portal</div>
        <div class="breadcrumb" id="topbarBreadcrumb">AI &amp; ML Department (Committee)</div>
      </div>
    </div>

    <!-- Center Search Bar -->
    <div class="header-search-bar">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted-dark)" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="headerSearchInput" placeholder="Search events, attendance, tasks...">
    </div>

    <div class="topbar-right">
      <!-- Language selection dropdown -->
      <select id="langSelect" style="background:var(--paper); border:1.5px solid var(--line-dark); border-radius:8px; padding:6px 12px; font-size:0.75rem; font-weight:600; font-family:inherit; cursor:pointer;" onchange="changeLanguage()">
        <option value="en">English</option>
        <option value="mr">मराठी (Marathi)</option>
      </select>

      <!-- Notification button -->
      <button class="topbar-icon-btn" onclick="showSection('notifications')" style="position:relative;"><svg viewBox="0 0 24 24" width="20" height="20"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg><span class="notif-dot" id="topbarNotifDot"></span></button>

      <!-- Profile Menu -->
      <div style="position:relative; display:inline-block;" id="profileMenuWrapper">
        <div style="display:flex; align-items:center; gap:8px; cursor:pointer;" onclick="toggleProfileDropdown()">
          <div style="width:32px; height:32px; border-radius:50%; background:var(--accent); color:var(--white); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem;" id="headerUserAvatar">RD</div>
          <div style="display:flex; flex-direction:column; text-align:left;">
            <span style="font-size:0.78rem; font-weight:600; color:var(--navy-950);" id="headerUserName"><?php echo htmlspecialchars($user_name); ?></span>
            <span style="font-size:0.65rem; color:var(--muted-dark);" id="headerUserRole">Committee Member</span>
          </div>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div id="profileDropdown" style="display:none; position:absolute; right:0; top:42px; background:var(--white); border:1px solid var(--line-dark); border-radius:12px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1); width:180px; z-index:150; padding:6px 0;">
          <a href="#" onclick="showSection('profile'); toggleProfileDropdown(); return false;" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:var(--navy-950); text-decoration:none; font-weight:500;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg> My Profile
          </a>
          <a href="#" onclick="openDrawer('changePasswordDrawer'); toggleProfileDropdown(); return false;" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:var(--navy-950); text-decoration:none; font-weight:500;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Change Password
          </a>
          <div style="border-top:1px solid var(--line-dark); margin:4px 0;"></div>
          <a href="index.php" onclick="sessionStorage.clear();" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:#ef4444; text-decoration:none; font-weight:600;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg> Logout
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    
    <!-- ==================== 1. DASHBOARD TAB SECTION ==================== -->
    <div id="section-dashboard" class="tab-section active-tab">
      <div class="section-eyebrow">Committee Member</div>
      <div class="content-title" id="welcomeHeading">Hello, Riya! 👋</div>
      <div class="content-sub">Technical Committee · Your activity overview — AIMSA Portal MySQL Backend</div>

      <!-- STATS GRID -->
      <div class="stats-grid">
        <div class="stat-card" onclick="showSection('assigned')">
          <div class="stat-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
          <span class="stat-val" id="statAssignedVal">4</span>
          <div class="stat-label">Assigned Events</div>
          <span class="stat-delta up">↑ Active in MySQL</span>
        </div>
        <div class="stat-card" onclick="showSection('attendance')">
          <div class="stat-icon"><svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg></div>
          <span class="stat-val" id="statAttendanceVal">92%</span>
          <div class="stat-label">Attendance Rate</div>
          <span class="stat-delta up">↑ Excellent</span>
        </div>
        <div class="stat-card" onclick="showSection('reports')">
          <div class="stat-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
          <span class="stat-val" id="statReportsVal">3</span>
          <div class="stat-label">Event Reports Filed</div>
          <span class="stat-delta up">↑ Live Database</span>
        </div>
        <div class="stat-card" onclick="showSection('notifications')">
          <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg></div>
          <span class="stat-val" id="statNotifVal">2</span>
          <div class="stat-label">Unread Notifications</div>
          <span class="stat-delta dn">↓ Action needed</span>
        </div>
      </div>

      <!-- MAIN GRID -->
      <div class="dash-grid">
        <!-- Assigned Events Table & Tasks preview -->
        <div class="card">
          <div class="card-head">
            <div class="card-title">Assigned Events</div>
            <span class="card-action" onclick="showSection('assigned')">View All</span>
          </div>
          <table class="data-table">
            <thead><tr><th>Event</th><th>Date</th><th>Role</th><th>Tasks</th><th>Status</th></tr></thead>
            <tbody id="dashAssignedTableBody">
              <!-- Populated dynamically via MySQL -->
            </tbody>
          </table>

          <div style="margin-top:24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
              <div style="font-family:var(--ff-display);font-size:.9rem;font-weight:700;">My Pending Tasks</div>
              <button class="btn btn-primary" style="font-size:0.72rem; padding:4px 12px;" onclick="openDrawer('addAssignedEventDrawer')">+ Add Event / Task</button>
            </div>
            <div id="dashTaskListContainer">
              <!-- Populated dynamically via MySQL -->
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div style="display:flex;flex-direction:column;gap:24px;">
          <!-- Notifications Card -->
          <div class="card">
            <div class="card-head">
              <div class="card-title">Notifications</div>
              <span class="card-action" onclick="clearAllNotifications()">Mark Read</span>
            </div>
            <div id="dashNotifContainer">
              <!-- Populated dynamically -->
            </div>
          </div>

          <!-- Event Reports Card -->
          <div class="card">
            <div class="card-head">
              <div class="card-title">Event Reports</div>
              <span class="card-action" onclick="openDrawer('submitReportDrawer')">Submit New</span>
            </div>
            <div id="dashReportsContainer">
              <!-- Populated dynamically -->
            </div>
          </div>
        </div>
      </div>

      <!-- BOTTOM GRID -->
      <div class="dash-grid-3">
        <!-- Attendance Record Preview -->
        <div class="card">
          <div class="card-head">
            <div class="card-title">Attendance Record</div>
            <span class="card-action" onclick="showSection('attendance')">Full Log</span>
          </div>
          <div class="attend-grid">
            <div class="attend-day attend-present">1</div><div class="attend-day attend-present">2</div>
            <div class="attend-day attend-absent">3</div><div class="attend-day attend-present">4</div>
            <div class="attend-day attend-present">5</div><div class="attend-day attend-present">6</div>
            <div class="attend-day attend-present">7</div><div class="attend-day attend-present">8</div>
            <div class="attend-day attend-present">9</div><div class="attend-day attend-present">10</div>
            <div class="attend-day attend-present">11</div><div class="attend-day attend-absent">12</div>
            <div class="attend-day attend-present">13</div><div class="attend-day attend-upcoming">14</div>
            <div class="attend-day attend-upcoming">15</div><div class="attend-day attend-upcoming">16</div>
            <div class="attend-day attend-upcoming">17</div><div class="attend-day attend-upcoming">18</div>
            <div class="attend-day attend-upcoming">19</div><div class="attend-day attend-upcoming">20</div>
            <div class="attend-day attend-upcoming">21</div>
          </div>
        </div>

        <!-- Certificates -->
        <div class="card" style="grid-column:span 2;">
          <div class="card-head">
            <div class="card-title">My Certificates</div>
            <span class="card-action" onclick="alert('All verified certificates are loaded.')">View All</span>
          </div>
          <div class="cert-grid">
            <div class="cert-card">
              <div class="cert-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
              <h4>Tech Symposium 2025</h4>
              <span>Organiser · Dec 2025</span>
              <div class="cert-download" onclick="downloadCert('Tech Symposium 2025')">↓ Download PDF</div>
            </div>
            <div class="cert-card">
              <div class="cert-icon"><svg viewBox="0 0 24 24"><path d="M12 2l2 6h6l-5 4 2 6-5-4-5 4 2-6-5-4h6z"/></svg></div>
              <h4>Hackathon 2025</h4>
              <span>Participant · Aug 2025</span>
              <div class="cert-download" onclick="downloadCert('Hackathon 2025')">↓ Download PDF</div>
            </div>
            <div class="cert-card">
              <div class="cert-icon"><svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg></div>
              <h4>AI Workshop Q4 2025</h4>
              <span>Volunteer · Nov 2025</span>
              <div class="cert-download" onclick="downloadCert('AI Workshop Q4 2025')">↓ Download PDF</div>
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


    <!-- ==================== 2. ASSIGNED EVENTS TAB SECTION ==================== -->
    <div id="section-assigned" class="tab-section">
      <div class="section-eyebrow">Events Management</div>
      <div class="content-title">Assigned Events &amp; Tasks</div>
      <div class="content-sub">View and manage all events assigned to you as a Committee Member.</div>

      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; gap:10px;">
          <button class="btn btn-ghost" style="padding:6px 14px; font-size:0.8rem;" onclick="filterAssignedTable('All')">All Events</button>
          <button class="btn btn-ghost" style="padding:6px 14px; font-size:0.8rem;" onclick="filterAssignedTable('Confirmed')">Confirmed</button>
          <button class="btn btn-ghost" style="padding:6px 14px; font-size:0.8rem;" onclick="filterAssignedTable('Registered')">Registered</button>
          <button class="btn btn-ghost" style="padding:6px 14px; font-size:0.8rem;" onclick="filterAssignedTable('Pending')">Pending</button>
        </div>
        <button class="btn btn-primary" onclick="openDrawer('addAssignedEventDrawer')">+ Add / Assign New Event</button>
      </div>

      <div class="card" style="margin-bottom:24px;">
        <div class="card-head"><div class="card-title">Assigned Events Directory (MySQL database)</div></div>
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Event Title</th>
              <th>Event Date</th>
              <th>My Role</th>
              <th>Tasks Assigned</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="fullAssignedEventsTableBody">
            <!-- Loaded via MySQL -->
          </tbody>
        </table>
      </div>

      <div class="card">
        <div class="card-head"><div class="card-title">Committee Checklist &amp; Action Items</div></div>
        <div id="fullTasksListContainer">
          <!-- Loaded via MySQL -->
        </div>
      </div>
    </div>


    <!-- ==================== 3. ATTENDANCE RECORDS TAB SECTION ==================== -->
    <div id="section-attendance" class="tab-section">
      <div class="section-eyebrow">Attendance System</div>
      <div class="content-title">Attendance Records &amp; Logging</div>
      <div class="content-sub">Record, track, and update student attendance for association meetings and events.</div>

      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:12px;">
          <span style="font-size:0.85rem; font-weight:600;">Filter by Status:</span>
          <select id="attendanceStatusFilter" onchange="filterAttendanceTable()" style="padding:6px 12px; border-radius:8px; border:1px solid var(--line-dark);">
            <option value="All">All Statuses</option>
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
          </select>
        </div>
        <button class="btn btn-primary" onclick="openDrawer('markAttendanceDrawer')">+ Mark New Attendance</button>
      </div>

      <div class="card">
        <div class="card-head">
          <div class="card-title">Student Attendance Log (MySQL database)</div>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Student Name</th>
              <th>Email / ID</th>
              <th>Attendance Status</th>
              <th>Marked By</th>
              <th>Timestamp</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="fullAttendanceTableBody">
            <!-- Loaded via MySQL -->
          </tbody>
        </table>
      </div>
    </div>


    <!-- ==================== 4. EVENT REPORTS TAB SECTION ==================== -->
    <div id="section-reports" class="tab-section">
      <div class="section-eyebrow">Documentation</div>
      <div class="content-title">Event Reports</div>
      <div class="content-sub">Submit, review, and download official AIMSA event outcome reports.</div>

      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div style="font-weight:600; font-size:0.9rem;">Filed &amp; Submitted Reports</div>
        <button class="btn btn-primary" onclick="openDrawer('submitReportDrawer')">+ Submit New Event Report</button>
      </div>

      <div class="card">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Report Title</th>
              <th>Category</th>
              <th>Summary</th>
              <th>Created By</th>
              <th>Date</th>
              <th>Format / PDF</th>
            </tr>
          </thead>
          <tbody id="fullReportsTableBody">
            <!-- Loaded via MySQL -->
          </tbody>
        </table>
      </div>
    </div>


    <!-- ==================== 5. NOTIFICATIONS TAB SECTION ==================== -->
    <div id="section-notifications" class="tab-section">
      <div class="section-eyebrow">Communication</div>
      <div class="content-title">Notifications Center</div>
      <div class="content-sub">System updates, task assignments, and committee reminders.</div>

      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div style="font-weight:600; font-size:0.9rem;">Your Inbox Alerts</div>
        <button class="btn btn-ghost" onclick="clearAllNotifications()">Mark All as Read</button>
      </div>

      <div class="card">
        <div id="fullNotificationsList">
          <!-- Loaded via MySQL -->
        </div>
      </div>
    </div>


    <!-- ==================== 6. PROFILE TAB SECTION ==================== -->
    <div id="section-profile" class="tab-section">
      <div class="section-eyebrow">User Account</div>
      <div class="content-title">Committee Member Profile</div>
      <div class="content-sub">Manage your personal credentials, committee designation, and portal settings.</div>

      <div class="dash-grid">
        <!-- Profile Details Card -->
        <div class="card">
          <div class="card-head"><div class="card-title">Profile Overview</div></div>
          <div style="display:flex; align-items:center; gap:20px; margin-bottom:24px; padding-bottom:20px; border-bottom:1px solid var(--line-dark);">
            <div style="width:70px; height:70px; border-radius:50%; background:var(--accent); color:var(--white); display:flex; align-items:center; justify-content:center; font-size:1.6rem; font-weight:800;" id="profileAvatarLarge">RD</div>
            <div>
              <h3 style="font-family:var(--ff-display); font-size:1.3rem;" id="profileNameText">Riya Desai</h3>
              <p style="color:var(--accent); font-weight:600; font-size:0.88rem;" id="profileRoleText">Committee Member — Technical Committee</p>
              <p style="color:var(--muted-dark); font-size:0.78rem; font-family:var(--ff-mono);" id="profileEmailText">committee@zealeducation.com</p>
            </div>
          </div>

          <form id="editProfileForm" onsubmit="event.preventDefault(); saveProfileDetails();">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" id="profInputName" value="Riya Desai" required>
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" id="profInputEmail" value="committee@zealeducation.com" required>
            </div>
            <div class="form-group">
              <label>Committee Designation / Role</label>
              <input type="text" id="profInputDesignation" value="Technical Committee">
            </div>
            <div class="form-group">
              <label>Department / Branch</label>
              <input type="text" id="profInputBranch" value="AI & ML Department">
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:10px;">Save Profile Changes</button>
          </form>
        </div>

        <!-- Change Password Card -->
        <div class="card">
          <div class="card-head"><div class="card-title">Security &amp; Password</div></div>
          <form id="profileChangePassForm" onsubmit="event.preventDefault(); updatePasswordFromProfile();">
            <div class="form-group">
              <label>Current Password</label>
              <input type="password" id="profCurrPass" placeholder="••••••••" required>
            </div>
            <div class="form-group">
              <label>New Password</label>
              <input type="password" id="profNewPass" placeholder="••••••••" required>
            </div>
            <div class="form-group">
              <label>Confirm New Password</label>
              <input type="password" id="profConfPass" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:10px;">Update Security Password</button>
          </form>
        </div>
      </div>
    </div>

  </div>

  <!-- PORTAL FOOTER -->
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

<!-- ── MARK ATTENDANCE DRAWER ── -->
<div class="drawer" id="markAttendanceDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Mark Student Attendance</div>
    <button class="drawer-close" onclick="closeDrawer('markAttendanceDrawer')">&times;</button>
  </div>
  <input type="hidden" id="attendRecordId" value="">
  <div class="form-group">
    <label>Select Event</label>
    <select id="attendEventSelect">
      <option value="Tech Symposium 2026">Tech Symposium 2026</option>
      <option value="AI Workshop Series">AI Workshop Series</option>
      <option value="Hackathon 2026">Hackathon 2026</option>
      <option value="ML Guest Lecture">ML Guest Lecture</option>
    </select>
  </div>
  <div class="form-group">
    <label>Select Student</label>
    <select id="attendStudentSelect">
      <!-- Populated dynamically from MySQL -->
    </select>
  </div>
  <div class="form-group">
    <label>Status</label>
    <select id="attendStatusSelect">
      <option value="Present">Present</option>
      <option value="Absent">Absent</option>
    </select>
  </div>
  <button class="btn btn-primary" style="width:100%; margin-top:10px;" id="saveAttendanceBtn" onclick="saveAttendanceRecord()">Save Attendance Record</button>
</div>

<!-- ── ADD ASSIGNED EVENT DRAWER ── -->
<div class="drawer" id="addAssignedEventDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Assign New Event</div>
    <button class="drawer-close" onclick="closeDrawer('addAssignedEventDrawer')">&times;</button>
  </div>
  <div class="form-group">
    <label>Event Name</label>
    <input type="text" id="newEventName" placeholder="e.g. Deep Learning Bootcamp">
  </div>
  <div class="form-group">
    <label>Event Date</label>
    <input type="text" id="newEventDate" placeholder="e.g. Aug 28, 2026">
  </div>
  <div class="form-group">
    <label>Assigned Role</label>
    <select id="newEventRole">
      <option value="Organiser">Organiser</option>
      <option value="Volunteer">Volunteer</option>
      <option value="Participant">Participant</option>
      <option value="Attendee">Attendee</option>
    </select>
  </div>
  <div class="form-group">
    <label>Tasks Summary</label>
    <input type="text" id="newEventTasks" placeholder="e.g. Stage setup, Registration desk">
  </div>
  <div class="form-group">
    <label>Status</label>
    <select id="newEventStatus">
      <option value="Confirmed">Confirmed</option>
      <option value="Registered">Registered</option>
      <option value="Pending">Pending</option>
    </select>
  </div>
  <button class="btn btn-primary" style="width:100%; margin-top:10px;" onclick="saveAssignedEvent()">Save Event Assignment</button>
</div>

<!-- ── SUBMIT EVENT REPORT DRAWER ── -->
<div class="drawer" id="submitReportDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Submit Event Report</div>
    <button class="drawer-close" onclick="closeDrawer('submitReportDrawer')">&times;</button>
  </div>
  <div class="form-group">
    <label>Report Title</label>
    <input type="text" id="reportTitle" placeholder="e.g. AI Workshop Q2 2026 Completion Report">
  </div>
  <div class="form-group">
    <label>Category</label>
    <select id="reportCategory">
      <option value="Event Report">Event Report</option>
      <option value="Member Report">Member Report</option>
      <option value="Financial Report">Financial Report</option>
    </select>
  </div>
  <div class="form-group">
    <label>Summary / Outcome Description</label>
    <textarea id="reportSummary" rows="4" placeholder="Brief details about event attendance, outcomes, and feedback..."></textarea>
  </div>
  <div class="form-group">
    <label>Report Format</label>
    <select id="reportFormat">
      <option value="PDF">PDF</option>
      <option value="DOCX">DOCX</option>
    </select>
  </div>
  <button class="btn btn-primary" style="width:100%; margin-top:10px;" onclick="saveEventReport()">Submit Report</button>
</div>

<script>
const sidebar = document.getElementById('sidebar'), overlay = document.getElementById('sidebarOverlay'), hamburger = document.getElementById('hamburgerBtn');
hamburger.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); closeAllDrawers(); });

function openDrawer(id) {
  closeAllDrawers();
  const d = document.getElementById(id);
  if(d) d.classList.add('open');
  overlay.classList.add('open');
}

function closeDrawer(id) {
  const d = document.getElementById(id);
  if(d) d.classList.remove('open');
  overlay.classList.remove('open');
}

function closeAllDrawers() {
  document.querySelectorAll('.drawer').forEach(d => d.classList.remove('open'));
}

// TAB SECTION SWITCHER
window.showSection = function(tabId) {
  document.querySelectorAll('.tab-section').forEach(sec => sec.classList.remove('active-tab'));
  document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));

  const targetSec = document.getElementById('section-' + tabId);
  const targetNav = document.getElementById('nav-' + tabId);

  if (targetSec) targetSec.classList.add('active-tab');
  if (targetNav) targetNav.classList.add('active');

  // Update topbar breadcrumb
  const titles = {
    'dashboard': 'AIMSA Portal Overview',
    'assigned': 'Assigned Events & Tasks',
    'attendance': 'Attendance Records & Logging',
    'reports': 'Event Outcome Reports',
    'notifications': 'Notifications & System Alerts',
    'profile': 'Committee Member Profile'
  };
  document.getElementById('topbarTitle').textContent = titles[tabId] || 'AIMSA Portal';
  document.getElementById('topbarBreadcrumb').textContent = `AI & ML Department (${tabId.toUpperCase()})`;

  // Update URL hash without page reload
  history.replaceState(null, null, '#' + tabId);

  // Close mobile sidebar if open
  sidebar.classList.remove('open');
  overlay.classList.remove('open');
};

// Check URL Hash on page load
window.addEventListener('load', () => {
  const hash = window.location.hash.replace('#', '');
  if (hash && document.getElementById('section-' + hash)) {
    showSection(hash);
  } else {
    showSection('dashboard');
  }
  loadDashboardData();
});

// GLOBAL DATA STATE FROM MYSQL
let cachedData = null;

function loadDashboardData() {
  fetch('ajax/committee_actions.php?action=get_dashboard_summary')
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        cachedData = data;
        renderDashboard(data);
      } else {
        console.error('Failed to load dashboard data:', data.message);
      }
    })
    .catch(err => console.error('Error fetching dashboard data:', err));
}

function renderDashboard(data) {
  // Stats values
  document.getElementById('statAssignedVal').textContent = data.stats.assigned_events;
  document.getElementById('statAttendanceVal').textContent = data.stats.attendance_rate;
  document.getElementById('statReportsVal').textContent = data.stats.reports_filed;
  document.getElementById('statNotifVal').textContent = data.stats.notifications;

  // Sidebar & header badges
  document.getElementById('sidebarNotifBadge').textContent = data.stats.notifications;
  const notifDot = document.getElementById('topbarNotifDot');
  if (data.stats.notifications > 0) {
    notifDot.style.display = 'block';
  } else {
    notifDot.style.display = 'none';
  }

  // Profile info
  if (data.profile) {
    document.getElementById('welcomeHeading').textContent = `Hello, ${data.profile.name.split(' ')[0]}! 👋`;
    document.getElementById('sidebarUserName').textContent = data.profile.name;
    document.getElementById('sidebarUserRole').textContent = data.profile.role;
    document.getElementById('headerUserName').textContent = data.profile.name;
    document.getElementById('headerUserRole').textContent = data.profile.role;

    const initials = data.profile.name.split(' ').map(n=>n[0]).join('').toUpperCase();
    document.getElementById('sidebarUserInitials').textContent = initials;
    document.getElementById('headerUserAvatar').textContent = initials;
    document.getElementById('profileAvatarLarge').textContent = initials;
    document.getElementById('profileNameText').textContent = data.profile.name;
    document.getElementById('profileRoleText').textContent = `${data.profile.role} — ${data.profile.committeeDesignation || 'Technical'}`;
    document.getElementById('profileEmailText').textContent = data.profile.email;

    document.getElementById('profInputName').value = data.profile.name;
    document.getElementById('profInputEmail').value = data.profile.email;
    document.getElementById('profInputDesignation').value = data.profile.committeeDesignation || '';
    document.getElementById('profInputBranch').value = data.profile.branch || 'AI & ML Department';
  }

  // 1. Render Dashboard Assigned Events
  const dashAssignedBody = document.getElementById('dashAssignedTableBody');
  const fullAssignedBody = document.getElementById('fullAssignedEventsTableBody');
  dashAssignedBody.innerHTML = '';
  fullAssignedBody.innerHTML = '';

  data.assigned_events.forEach((ev, i) => {
    let badgeClass = 'badge-blue';
    if(ev.status === 'Confirmed') badgeClass = 'badge-green';
    if(ev.status === 'Pending') badgeClass = 'badge-orange';

    const row = `
      <tr>
        <td><b>${ev.event_name}</b></td>
        <td>${ev.event_date}</td>
        <td>${ev.role}</td>
        <td>${ev.tasks_summary || '—'}</td>
        <td><span class="badge ${badgeClass}">${ev.status}</span></td>
      </tr>`;
    
    if(i < 4) dashAssignedBody.innerHTML += row;

    fullAssignedBody.innerHTML += `
      <tr>
        <td>#EV-${ev.id}</td>
        <td><b>${ev.event_name}</b></td>
        <td>${ev.event_date}</td>
        <td>${ev.role}</td>
        <td>${ev.tasks_summary || '—'}</td>
        <td><span class="badge ${badgeClass}">${ev.status}</span></td>
        <td><button class="btn btn-ghost" style="padding:4px 10px; font-size:0.7rem;" onclick="alert('Event details for ${ev.event_name}')">View</button></td>
      </tr>`;
  });

  // 2. Render Tasks List
  const dashTaskList = document.getElementById('dashTaskListContainer');
  const fullTaskList = document.getElementById('fullTasksListContainer');
  dashTaskList.innerHTML = '';
  fullTaskList.innerHTML = '';

  data.tasks.forEach(t => {
    const isDone = t.status === 'Completed';
    const taskHtml = `
      <div class="task-item">
        <div class="task-check ${isDone ? 'done' : ''}" onclick="toggleTaskStatus(${t.id})"></div>
        <div class="task-info">
          <b style="${isDone ? 'text-decoration:line-through; opacity:0.7;' : ''}">${t.task_title}</b>
          <span>Due: ${t.due_date || 'N/A'} · Priority: ${t.priority || 'Medium'}</span>
        </div>
      </div>`;
    
    dashTaskList.innerHTML += taskHtml;
    fullTaskList.innerHTML += taskHtml;
  });

  // 3. Render Attendance Records
  const fullAttBody = document.getElementById('fullAttendanceTableBody');
  fullAttBody.innerHTML = '';

  const studentSelect = document.getElementById('attendStudentSelect');
  studentSelect.innerHTML = '';

  data.students.forEach(s => {
    studentSelect.innerHTML += `<option value="${s.name}">${s.name} (${s.email})</option>`;
  });

  data.attendance.forEach(att => {
    let badgeClass = att.status === 'Present' ? 'badge-green' : 'badge-orange';
    fullAttBody.innerHTML += `
      <tr>
        <td>#ATT-${att.id}</td>
        <td><b>${att.student_name}</b></td>
        <td>${att.student_email || att.branch || 'Student'}</td>
        <td><span class="badge ${badgeClass}">${att.status}</span></td>
        <td>${att.marked_by || 'Committee'}</td>
        <td>${att.marked_at ? att.marked_at.substring(0, 16) : 'Just now'}</td>
        <td><button class="btn btn-ghost" style="padding:4px 10px; font-size:0.7rem;" onclick="editAttendanceRecord(${att.id}, '${att.student_name}', '${att.status}')">Edit</button></td>
      </tr>`;
  });

  // 4. Render Event Reports
  const dashRepContainer = document.getElementById('dashReportsContainer');
  const fullRepBody = document.getElementById('fullReportsTableBody');
  dashRepContainer.innerHTML = '';
  fullRepBody.innerHTML = '';

  data.reports.forEach((rep, i) => {
    if(i < 4) {
      dashRepContainer.innerHTML += `
        <div class="list-item">
          <div class="list-dot"></div>
          <div class="list-text">
            <b>${rep.title}</b>
            <span>${rep.summary ? rep.summary.substring(0,40) : 'Report ready'}...</span>
          </div>
          <span class="card-action" style="font-size:.58rem;padding:3px 8px;" onclick="downloadReport('${rep.title}')">↓ ${rep.format || 'PDF'}</span>
        </div>`;
    }

    fullRepBody.innerHTML += `
      <tr>
        <td>#REP-${rep.id}</td>
        <td><b>${rep.title}</b></td>
        <td><span class="badge badge-blue">${rep.category || 'Event Report'}</span></td>
        <td>${rep.summary || 'Summary ready.'}</td>
        <td>${rep.created_by || 'Committee Member'}</td>
        <td>${rep.created_at ? rep.created_at.substring(0,10) : 'Recent'}</td>
        <td><button class="btn btn-primary" style="font-size:0.72rem; padding:4px 12px;" onclick="downloadReport('${rep.title}')">Download ${rep.format || 'PDF'}</button></td>
      </tr>`;
  });

  // 5. Render Notifications
  const dashNotifContainer = document.getElementById('dashNotifContainer');
  const fullNotifList = document.getElementById('fullNotificationsList');
  dashNotifContainer.innerHTML = '';
  fullNotifList.innerHTML = '';

  if (data.notifications.length === 0) {
    dashNotifContainer.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:10px;">No new alerts.</p>`;
    fullNotifList.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:20px;">No alerts in inbox.</p>`;
  } else {
    data.notifications.forEach((n, idx) => {
      let dotColor = '#3E8BFF';
      let dotShadow = 'rgba(62,139,255,.18)';
      if (n.indicator === 'green') { dotColor = '#22c55e'; dotShadow = 'rgba(34,197,94,.18)'; }
      if (n.indicator === 'yellow') { dotColor = '#fbbf24'; dotShadow = 'rgba(251,191,36,.18)'; }
      if (n.indicator === 'red') { dotColor = '#ef4444'; dotShadow = 'rgba(239,68,68,.18)'; }

      const notifHtml = `
        <div class="list-item">
          <div class="list-dot" style="background:${dotColor}; box-shadow:0 0 0 3px ${dotShadow};"></div>
          <div class="list-text">
            <b>${n.title}</b>
            <span>${n.text} · ${n.created_at ? n.created_at.substring(0,16) : 'Just now'}</span>
          </div>
        </div>`;

      if(idx < 4) dashNotifContainer.innerHTML += notifHtml;
      fullNotifList.innerHTML += notifHtml;
    });
  }
}

// MYSQL AJAX ACTIONS
function toggleTaskStatus(taskId) {
  const formData = new FormData();
  formData.append('action', 'toggle_task');
  formData.append('task_id', taskId);

  fetch('ajax/committee_actions.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
      if(res.status === 'success') {
        loadDashboardData();
      }
    });
}

function saveAttendanceRecord() {
  const event_name = document.getElementById('attendEventSelect').value;
  const student_name = document.getElementById('attendStudentSelect').value;
  const status = document.getElementById('attendStatusSelect').value;
  const record_id = document.getElementById('attendRecordId').value;

  const formData = new FormData();
  formData.append('action', 'save_attendance');
  formData.append('event_name', event_name);
  formData.append('student_name', student_name);
  formData.append('status', status);
  if(record_id) formData.append('record_id', record_id);

  fetch('ajax/committee_actions.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
      alert(res.message);
      closeDrawer('markAttendanceDrawer');
      document.getElementById('attendRecordId').value = '';
      loadDashboardData();
    });
}

function editAttendanceRecord(id, studentName, status) {
  document.getElementById('attendRecordId').value = id;
  document.getElementById('attendStudentSelect').value = studentName;
  document.getElementById('attendStatusSelect').value = status;
  openDrawer('markAttendanceDrawer');
}

function saveAssignedEvent() {
  const event_name = document.getElementById('newEventName').value;
  const event_date = document.getElementById('newEventDate').value;
  const role = document.getElementById('newEventRole').value;
  const tasks_summary = document.getElementById('newEventTasks').value;
  const status = document.getElementById('newEventStatus').value;

  if(!event_name || !event_date) {
    alert('Please enter event name and date.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'add_assigned_event');
  formData.append('event_name', event_name);
  formData.append('event_date', event_date);
  formData.append('role', role);
  formData.append('tasks_summary', tasks_summary);
  formData.append('status', status);

  fetch('ajax/committee_actions.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
      alert(res.message);
      closeDrawer('addAssignedEventDrawer');
      loadDashboardData();
    });
}

function saveEventReport() {
  const title = document.getElementById('reportTitle').value;
  const category = document.getElementById('reportCategory').value;
  const summary = document.getElementById('reportSummary').value;
  const format = document.getElementById('reportFormat').value;

  if(!title || !summary) {
    alert('Please provide report title and summary.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'submit_report');
  formData.append('title', title);
  formData.append('category', category);
  formData.append('summary', summary);
  formData.append('format', format);

  fetch('ajax/committee_actions.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
      alert(res.message);
      closeDrawer('submitReportDrawer');
      loadDashboardData();
    });
}

function clearAllNotifications() {
  const formData = new FormData();
  formData.append('action', 'clear_notifications');
  fetch('ajax/committee_actions.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
      loadDashboardData();
      alert('Notifications marked as read.');
    });
}

function saveProfileDetails() {
  const name = document.getElementById('profInputName').value;
  const email = document.getElementById('profInputEmail').value;
  const committeeDesignation = document.getElementById('profInputDesignation').value;
  const branch = document.getElementById('profInputBranch').value;

  const formData = new FormData();
  formData.append('action', 'update_profile');
  formData.append('name', name);
  formData.append('email', email);
  formData.append('committeeDesignation', committeeDesignation);
  formData.append('branch', branch);

  fetch('ajax/committee_actions.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
      alert(res.message);
      loadDashboardData();
    });
}

function updatePasswordFromProfile() {
  const currPassword = document.getElementById('profCurrPass').value;
  const newPassword = document.getElementById('profNewPass').value;
  const confPassword = document.getElementById('profConfPass').value;

  if (newPassword !== confPassword) {
    alert('New passwords do not match!');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'change_password');
  formData.append('currPassword', currPassword);
  formData.append('newPassword', newPassword);

  fetch('ajax/committee_actions.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
      alert(res.message);
      if(res.status === 'success') {
        document.getElementById('profCurrPass').value = '';
        document.getElementById('profNewPass').value = '';
        document.getElementById('profConfPass').value = '';
      }
    });
}

// Password drawer save action
document.getElementById('savePasswordBtn').addEventListener('click', () => {
  const currPassword = document.getElementById('currPassword').value;
  const newPassword = document.getElementById('newPassword').value;
  const confirmNewPassword = document.getElementById('confirmNewPassword').value;

  if (newPassword !== confirmNewPassword) {
    alert('Passwords do not match.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'change_password');
  formData.append('currPassword', currPassword);
  formData.append('newPassword', newPassword);

  fetch('ajax/committee_actions.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(res => {
      alert(res.message);
      if(res.status === 'success') {
        closeDrawer('changePasswordDrawer');
      }
    });
});

// Profile dropdown toggle
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

// Search bar filter
document.getElementById('headerSearchInput').addEventListener('input', (e) => {
  const query = e.target.value.toLowerCase().trim();
  document.querySelectorAll('tr, .list-item, .task-item').forEach(el => {
    if(query === '') {
      el.style.display = '';
    } else {
      const text = el.textContent.toLowerCase();
      el.style.display = text.includes(query) ? '' : 'none';
    }
  });
});

function downloadCert(name) {
  alert(`Generating secure PDF certificate for ${name}... Download started!`);
}

function downloadReport(title) {
  alert(`Preparing export for ${title}... Report downloaded successfully.`);
}

function filterAssignedTable(status) {
  const rows = document.querySelectorAll('#fullAssignedEventsTableBody tr');
  rows.forEach(r => {
    if(status === 'All' || r.textContent.includes(status)) {
      r.style.display = '';
    } else {
      r.style.display = 'none';
    }
  });
}

function filterAttendanceTable() {
  const status = document.getElementById('attendanceStatusFilter').value;
  const rows = document.querySelectorAll('#fullAttendanceTableBody tr');
  rows.forEach(r => {
    if(status === 'All' || r.textContent.includes(status)) {
      r.style.display = '';
    } else {
      r.style.display = 'none';
    }
  });
}

function changeLanguage() {
  const lang = document.getElementById('langSelect').value;
  if(lang === 'mr') {
    alert('पोर्टलची भाषा यशस्वीरीत्या मराठीमध्ये बदलली आहे. (Portal language switched to Marathi)');
  } else {
    alert('Portal language switched to English.');
  }
}
</script>
</body>
</html>
