<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/include/dbConfig.php';

$sessionUser = $_SESSION['user'] ?? null;
if (!$sessionUser || !in_array($sessionUser['role'] ?? '', ['Faculty Coordinator', 'Faculty', 'HOD'])) {
    header("Location: index.php?auth_error=" . urlencode("Unauthorized access. Please login with Faculty Coordinator credentials."));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculty Coordinator Dashboard — AIMSA Portal</title>
<meta name="description" content="Faculty Coordinator portal dashboard for AIMSA — AI & ML Student Association.">
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
.page-title{font-family:var(--ff-display);font-size:1.15rem;font-weight:700;color:var(--navy-950);}
.breadcrumb{font-family:var(--ff-mono);font-size:.68rem;letter-spacing:.08em;color:var(--muted-dark);text-transform:uppercase;}
.topbar-right{display:flex;align-items:center;gap:14px;}
.topbar-icon-btn{width:36px;height:36px;border-radius:10px;border:1px solid var(--line-dark);background:transparent;display:flex;align-items:center;justify-content:center;transition:.2s ease;position:relative;}
.topbar-icon-btn:hover{background:var(--navy-950);border-color:var(--navy-950);}
.topbar-icon-btn:hover svg{stroke:var(--white);}
.topbar-icon-btn svg{width:18px;height:18px;stroke:var(--navy-800);fill:none;stroke-width:1.8;}
.notif-dot{position:absolute;top:7px;right:7px;width:7px;height:7px;border-radius:50%;background:var(--accent);border:2px solid var(--white);}
.hamburger-btn{display:none;width:36px;height:36px;border-radius:10px;border:1px solid var(--line-dark);background:transparent;align-items:center;justify-content:center;flex-direction:column;gap:4px;}
.hamburger-btn span{width:18px;height:2px;background:var(--navy-800);border-radius:2px;display:block;}
.content{padding:32px;flex:1;}
.section-eyebrow{font-family:var(--ff-mono);font-size:.68rem;letter-spacing:.18em;text-transform:uppercase;color:var(--accent);display:flex;align-items:center;gap:10px;margin-bottom:8px;}
.section-eyebrow::before{content:'';width:22px;height:1px;background:var(--accent);}
.content-title{font-family:var(--ff-display);font-size:1.6rem;font-weight:700;color:var(--navy-950);margin-bottom:4px;}
.content-sub{color:var(--muted-dark);font-size:.9rem;margin-bottom:32px;}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:32px;}
.stat-card{background:var(--white);border-radius:16px;padding:22px 24px;border:1px solid var(--line-dark);transition:transform .2s ease,box-shadow .3s ease;position:relative;overflow:hidden;}
.stat-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);}
.stat-card::after{content:'';position:absolute;right:-30px;top:-30px;width:100px;height:100px;border-radius:50%;background:radial-gradient(circle,var(--accent-glow),transparent 70%);opacity:.4;}
.stat-icon{width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,var(--navy-950),var(--navy-800));display:flex;align-items:center;justify-content:center;margin-bottom:16px;transition:transform .3s cubic-bezier(.34,1.56,.64,1);}
.stat-card:hover .stat-icon{transform:scale(1.12) rotate(-6deg);background:linear-gradient(135deg,var(--accent),#2563eb);}
.stat-icon svg{width:20px;height:20px;stroke:var(--white);fill:none;stroke-width:1.8;}
.stat-val{font-family:var(--ff-display);font-size:2rem;font-weight:700;color:var(--navy-950);display:block;}
.stat-label{font-size:.8rem;color:var(--muted-dark);margin-top:2px;}
.stat-delta{font-family:var(--ff-mono);font-size:.65rem;letter-spacing:.06em;margin-top:8px;display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:999px;}
.stat-delta.up{background:rgba(34,197,94,.1);color:#16a34a;}
.stat-delta.dn{background:rgba(239,68,68,.1);color:#dc2626;}
.dash-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:24px;margin-bottom:24px;}
.dash-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:24px;}
.dash-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;}
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
/* Attendance Summary */
.attend-summary{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;}
.attend-box{padding:16px;border-radius:12px;border:1px solid var(--line-dark);text-align:center;}
.attend-box.present{background:rgba(34,197,94,.05);border-color:rgba(34,197,94,.2);}
.attend-box.absent{background:rgba(239,68,68,.05);border-color:rgba(239,68,68,.2);}
.attend-box.total{background:rgba(62,139,255,.05);border-color:rgba(62,139,255,.2);}
.attend-box-val{font-family:var(--ff-display);font-size:1.8rem;font-weight:700;display:block;color:var(--navy-950);}
.attend-box.present .attend-box-val{color:#16a34a;}
.attend-box.absent .attend-box-val{color:#dc2626;}
.attend-box.total .attend-box-val{color:var(--accent);}
.attend-box-label{font-family:var(--ff-mono);font-size:.62rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted-dark);margin-top:4px;}
/* Progress */
.progress-item{margin-bottom:16px;}
.progress-label{display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:6px;}
.progress-label span:last-child{font-family:var(--ff-mono);color:var(--accent);}
.progress-bar{height:6px;background:var(--paper-dim);border-radius:999px;overflow:hidden;}
.progress-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,var(--accent),var(--accent-soft));}
/* Achievement */
.ach-item{display:flex;align-items:center;gap:14px;padding:14px;border-radius:12px;border:1px solid var(--line-dark);background:var(--paper);transition:.2s ease;margin-bottom:10px;}
.ach-item:hover{border-color:var(--accent);background:var(--white);transform:translateX(4px);}
.ach-icon{width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,var(--navy-950),var(--navy-800));display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:.3s ease;}
.ach-item:hover .ach-icon{background:linear-gradient(135deg,var(--accent),#2563eb);}
.ach-icon svg{width:18px;height:18px;stroke:var(--accent-soft);fill:none;stroke-width:1.7;}
.ach-info b{font-size:.88rem;display:block;margin-bottom:2px;}
.ach-info span{font-size:.75rem;color:var(--muted-dark);font-family:var(--ff-mono);}
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

@media(max-width:1100px){.stats-grid{grid-template-columns:repeat(2,1fr);}.dash-grid{grid-template-columns:1fr;}.dash-grid-3{grid-template-columns:1fr 1fr;}.dash-grid-2{grid-template-columns:1fr;}}
@media(max-width:768px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}.sidebar-overlay.open{display:block;}.main{margin-left:0;}.hamburger-btn{display:flex;}.content{padding:20px;}.stats-grid{grid-template-columns:1fr 1fr;}.dash-grid-3{grid-template-columns:1fr;}.attend-summary{grid-template-columns:1fr;}}
@media(max-width:480px){.stats-grid{grid-template-columns:1fr;}}
@media(max-width:375px){
  .topbar{padding:12px 14px !important; flex-wrap:wrap; gap:8px;}
  .topbar-left{gap:8px; width:100%;}
  .topbar-right{gap:8px; flex-wrap:wrap;}
  .page-title{font-size:.9rem;}
  .breadcrumb{font-size:.6rem;}
  .header-search-bar{width:100%; order:10; margin-top:4px;}
  .content{padding:14px !important;}
  .content-title{font-size:1.2rem;}
  .content-sub{font-size:.82rem; margin-bottom:20px;}
  .stats-grid{gap:10px;}
  .stat-card{padding:16px;}
  .stat-icon{width:36px; height:36px; margin-bottom:12px;}
  .stat-val{font-size:1.4rem;}
  .card{padding:16px;}
  .card-head{flex-wrap:wrap; gap:8px;}
  .card-title{font-size:.88rem;}
  .dash-grid{gap:16px;}
  .dash-grid-3{gap:16px;}
  .drawer{padding:20px 14px; width:min(100vw, 100%); right:-100%;}
  .drawer.open{right:0;}
  .drawer-title{font-size:1rem;}
  .form-group input,.form-group select,.form-group textarea{padding:8px 10px; font-size:.82rem;}
  .btn{padding:8px 16px; font-size:.8rem;}
  .data-table{font-size:.72rem;}
  .data-table th,.data-table td{padding:6px 8px;}
  .list-item{padding:10px 0; gap:10px;}
  .list-text b{font-size:.82rem;}
  .list-text span{font-size:.7rem;}
  .portal-footer{flex-direction:column; text-align:center; padding:16px 14px !important; gap:12px;}
  .portal-footer > div{gap:8px; flex-direction:column; align-items:center;}
}
</style>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo" style="width:42px; height:42px; border-radius:50%; background:var(--white); display:flex; align-items:center; justify-content:center; overflow:hidden; border:2px solid rgba(255,255,255,.3); flex-shrink:0;">
      <img src="images/aimsa_logo.jpg" alt="AIMSA Logo" style="width:100%; height:100%; object-fit:cover;">
    </div>
    <div class="brand-info"><b>AIMSA Portal</b><span>Faculty Access</span></div>
  </div>
  <div class="sidebar-role" style="cursor:pointer;" onclick="openDrawer('viewProfileDrawer')">
    <div class="role-avatar"><div class="in" id="sidebarAvatar">FC</div></div>
    <div class="role-info"><b id="sidebarUserName">Prof. Manisha Devgunde</b><span>Faculty Coordinator</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <a class="nav-item active" href="faculty_dashboard.php">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard
    </a>
    <div class="nav-section-label">Events</div>
    <a class="nav-item" id="navEvents" href="#approved">
      <svg class="nav-icon" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Approved Events
    </a>
    <a class="nav-item" href="#pending">
      <svg class="nav-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Pending Events
      <span class="nav-badge">3</span>
    </a>
    <a class="nav-item" id="navScheduleMeeting" href="#" onclick="openDrawer('scheduleMeetingDrawer'); return false;">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>Schedule Meeting
    </a>
    <div class="nav-section-label">Statistics</div>
    <a class="nav-item" id="navMemberStats" href="#memberstats">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M10 11a4 4 0 100-8 4 4 0 000 8z"/></svg>Member Statistics
    </a>
    <a class="nav-item" id="navAttendance" href="#attendance">
      <svg class="nav-icon" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>Attendance Summary
    </a>
    <a class="nav-item" id="navAchievements" href="#achievements">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg>Student Achievements
    </a>
    <div class="nav-section-label">Communication</div>
    <a class="nav-item" href="#notifications">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>Notifications
      <span class="nav-badge">5</span>
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
        <div class="breadcrumb" style="font-size:0.68rem; color:var(--muted-dark);">AI &amp; ML Department (Faculty)</div>
      </div>
    </div>

    <!-- Center Search Bar -->
    <div class="header-search-bar">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted-dark)" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="headerSearchInput" placeholder="Search events, nominations...">
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
          <div style="width:32px; height:32px; border-radius:50%; background:var(--accent); color:var(--white); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem;" id="headerUserAvatar">FC</div>
          <div style="display:flex; flex-direction:column; text-align:left;">
            <span style="font-size:0.78rem; font-weight:600; color:var(--navy-950);" id="headerUserName">Prof. Manisha Devgunde</span>
            <span style="font-size:0.65rem; color:var(--muted-dark);" id="headerUserRole">Faculty Coordinator</span>
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
    <div class="section-eyebrow" data-i18n="dash.faculty_eyebrow">Faculty Coordinator</div>
    <div class="content-title">Good Morning, <?= htmlspecialchars($sessionUser['name'] ?? 'Prof. Manisha Devgunde') ?> 👋</div>
    <div class="content-sub">Your faculty oversight portal — <span class="liveDateText"><?php echo $sqlCurrentDateFormatted; ?></span></div>

    <!-- STATS — Approved Events, Pending Events, Member Stats, Attendance -->
    <div class="stats-grid">
      <div class="stat-card" id="approved">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg></div>
        <span class="stat-val">18</span>
        <div class="stat-label">Approved Events</div>
        <span class="stat-delta up">↑ 6 this semester</span>
      </div>
      <div class="stat-card" id="pending">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <span class="stat-val">3</span>
        <div class="stat-label">Pending Events</div>
        <span class="stat-delta dn">↓ Needs review</span>
      </div>
      <div class="stat-card" id="memberstats">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M10 11a4 4 0 100-8 4 4 0 000 8z"/></svg></div>
        <span class="stat-val">247</span>
        <div class="stat-label">Total Student Members</div>
        <span class="stat-delta up">↑ 12 new this month</span>
      </div>
      <div class="stat-card" id="attendance">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
        <span class="stat-val">89%</span>
        <div class="stat-label">Avg. Attendance</div>
        <span class="stat-delta up">↑ +3% this sem</span>
      </div>
    </div>

    <!-- MAIN GRID -->
    <div class="dash-grid">
      <!-- Pending Events for Approval -->
      <div class="card">
        <div class="card-head"><div class="card-title">Events Pending Approval</div><span class="card-action">Review All</span></div>
        <table class="data-table">
          <thead><tr><th>Event</th><th>Proposed By</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
          <tbody id="pendingEventsBody">
            <!-- Dynamically populated -->
          </tbody>
        </table>
        <div style="margin-top:16px;">
          <div class="card-head" style="margin-bottom:12px;"><div class="card-title" style="font-size:.9rem;">Approved Events This Semester</div></div>
          <table class="data-table">
            <thead><tr><th>Event</th><th>Date</th><th>Registrations</th><th>Status</th></tr></thead>
            <tbody id="approvedEventsBody">
              <!-- Dynamically populated -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Right Column -->
      <div style="display:flex;flex-direction:column;gap:24px;">
        <!-- Notifications -->
        <div class="card" id="notifications">
          <div class="card-head"><div class="card-title">Notifications</div><span class="card-action">Mark Read</span></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Robotics Workshop awaits approval</b><span>Submitted by Tech Committee · 1 hr ago</span></div></div>
          <div class="list-item"><div class="list-dot" style="background:#f97316;box-shadow:0 0 0 3px rgba(249,115,22,.18);"></div><div class="list-text"><b>3 student achievement nominations</b><span>Review required · 2 hrs ago</span></div></div>
          <div class="list-item"><div class="list-dot" style="background:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,.18);"></div><div class="list-text"><b>Tech Symposium attendance: 94%</b><span>Report submitted · Yesterday</span></div></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>New membership applications: 8</b><span>Pending faculty acknowledgement · 3 hrs ago</span></div></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>HOD review requested</b><span>Monthly report due · Jul 25</span></div></div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
          <div class="card-head"><div class="card-title">Quick Actions</div></div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <button id="quickApproveEvent" style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:14px 10px;border-radius:12px;border:1.5px solid var(--line-dark);background:var(--paper);cursor:pointer;transition:.2s;" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--line-dark)'">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#123163" stroke-width="1.8"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
              <span style="font-size:.76rem;font-weight:600;color:var(--navy-900);">Approve Event</span>
            </button>
            <button id="quickViewReports" style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:14px 10px;border-radius:12px;border:1.5px solid var(--line-dark);background:var(--paper);cursor:pointer;transition:.2s;" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--line-dark)'">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#123163" stroke-width="1.8"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
              <span style="font-size:.76rem;font-weight:600;color:var(--navy-900);">View Reports</span>
            </button>
            <button id="quickNominate" style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:14px 10px;border-radius:12px;border:1.5px solid var(--line-dark);background:var(--paper);cursor:pointer;transition:.2s;" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--line-dark)'">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#123163" stroke-width="1.8"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg>
              <span style="font-size:.76rem;font-weight:600;color:var(--navy-900);">Nominate</span>
            </button>
            <button id="quickNotifyStudents" style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:14px 10px;border-radius:12px;border:1.5px solid var(--line-dark);background:var(--paper);cursor:pointer;transition:.2s;" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--line-dark)'">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#123163" stroke-width="1.8"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
              <span style="font-size:.76rem;font-weight:600;color:var(--navy-900);">Send Notification</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- BOTTOM GRID -->
    <div class="dash-grid-3">
      <!-- Attendance Summary -->
      <div class="card">
        <div class="card-head"><div class="card-title">Attendance Summary</div><span class="card-action" id="btnFullLogAttendance">Full Log</span></div>
        <div class="attend-summary">
          <div class="attend-box present"><span class="attend-box-val">89%</span><div class="attend-box-label">Present Rate</div></div>
          <div class="attend-box absent"><span class="attend-box-val">11%</span><div class="attend-box-label">Absent Rate</div></div>
          <div class="attend-box total"><span class="attend-box-val">24</span><div class="attend-box-label">Total Events</div></div>
        </div>
        <div class="progress-item"><div class="progress-label"><span>Tech Symposium</span><span>94%</span></div><div class="progress-bar"><div class="progress-fill" style="width:94%"></div></div></div>
        <div class="progress-item"><div class="progress-label"><span>AI Workshop</span><span>88%</span></div><div class="progress-bar"><div class="progress-fill" style="width:88%"></div></div></div>
        <div class="progress-item"><div class="progress-label"><span>Hackathon</span><span>91%</span></div><div class="progress-bar"><div class="progress-fill" style="width:91%"></div></div></div>
        <div class="progress-item"><div class="progress-label"><span>Guest Lecture</span><span>82%</span></div><div class="progress-bar"><div class="progress-fill" style="width:82%"></div></div></div>
      </div>

      <!-- Member Statistics -->
      <div class="card">
        <div class="card-head"><div class="card-title">Member Statistics</div><span class="card-action" id="btnDetailViewMemberStats">Detail View</span></div>
        <div class="progress-item"><div class="progress-label"><span>AIML 3rd Year</span><span>87 students</span></div><div class="progress-bar"><div class="progress-fill" style="width:87%"></div></div></div>
        <div class="progress-item"><div class="progress-label"><span>AIML 2nd Year</span><span>72 students</span></div><div class="progress-bar"><div class="progress-fill" style="width:72%"></div></div></div>
        <div class="progress-item"><div class="progress-label"><span>CS Students</span><span>54 students</span></div><div class="progress-bar"><div class="progress-fill" style="width:54%"></div></div></div>
        <div class="progress-item"><div class="progress-label"><span>Data Science</span><span>34 students</span></div><div class="progress-bar"><div class="progress-fill" style="width:34%"></div></div></div>
        <div style="margin-top:16px;padding:12px;background:var(--paper);border-radius:10px;border:1px solid var(--line-dark);">
          <div style="font-family:var(--ff-mono);font-size:.65rem;color:var(--muted-dark);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;">Active / Total</div>
          <div style="font-family:var(--ff-display);font-size:1.4rem;font-weight:700;color:var(--navy-950);">247 <span style="font-size:.9rem;color:var(--muted-dark);">/ 312 enrolled</span></div>
        </div>
      </div>

      <!-- Student Achievements -->
      <div class="card" id="achievements">
        <div class="card-head"><div class="card-title">Student Achievements</div><span class="card-action" id="btnNominateAchievement">Nominate</span></div>
        <div class="ach-item">
          <div class="ach-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
          <div class="ach-info"><b>Arjun Patil — 1st Place Hackathon</b><span>AIML 3rd Year · Hackathon 2026</span></div>
        </div>
        <div class="ach-item">
          <div class="ach-icon"><svg viewBox="0 0 24 24"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg></div>
          <div class="ach-info"><b>Riya Desai — Best Organiser</b><span>Technical Committee · Tech Symposium</span></div>
        </div>
        <div class="ach-item">
          <div class="ach-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
          <div class="ach-info"><b>Sneha Rao — Research Paper Published</b><span>AIML 4th Year · IEEE Conference</span></div>
        </div>
        <div class="ach-item">
          <div class="ach-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
          <div class="ach-info"><b>Aman Kulkarni — Top Performer</b><span>100% attendance · Semester II</span></div>
        </div>
      </div>
    </div>
  </div>

    <!-- SCHEDULED MEETINGS & SYNCS MANAGEMENT -->
    <div class="card" id="scheduledMeetingsCard" style="margin-top:24px;">
      <div class="card-head" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
          <div class="card-title" style="display:flex; align-items:center; gap:8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
            Scheduled Meetings &amp; Member Syncs
          </div>
          <p style="font-size:0.75rem; color:var(--muted-dark); margin-top:2px;">Schedule meetings for specific member roles (All Members, Committee, Faculty, Students) and cancel active syncs.</p>
        </div>
        <button class="btn btn-primary" onclick="openDrawer('scheduleMeetingDrawer')" style="font-size:0.8rem; padding:8px 16px;">
          📅 Schedule New Meeting
        </button>
      </div>
      <div style="overflow-x:auto; margin-top:12px;">
        <table class="data-table" style="width:100%; font-size:0.85rem;">
          <thead>
            <tr>
              <th>Meeting Title</th>
              <th>Date &amp; Time</th>
              <th>Venue / Location</th>
              <th>Target Audience (Member Selection)</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="scheduledMeetingsTableBody">
            <!-- Dynamically populated from MySQL via AJAX -->
          </tbody>
        </table>
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

<!-- ── SCHEDULE MEETING DRAWER ── -->
<div class="drawer" id="scheduleMeetingDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Schedule New Meeting</div>
    <button class="drawer-close" onclick="closeDrawer('scheduleMeetingDrawer')">&times;</button>
  </div>
  <form id="scheduleMeetingForm" onsubmit="submitScheduleMeeting(event)">
    <input type="hidden" id="meetId" value="0">
    <div class="form-group">
      <label>Meeting Title</label>
      <input type="text" id="meetTitle" placeholder="e.g. Faculty Oversight &amp; Event Planning Sync" required>
    </div>
    <div class="form-group">
      <label>Meeting Date</label>
      <input type="date" id="meetDate" required>
    </div>
    <div class="form-group">
      <label>Meeting Time</label>
      <input type="text" id="meetTime" placeholder="e.g. 02:00 PM" value="02:00 PM" required>
    </div>
    <div class="form-group">
      <label>Venue / Location / Meeting Link</label>
      <input type="text" id="meetVenue" placeholder="e.g. Faculty Coordination Room / Google Meet link" required>
    </div>
    <div class="form-group">
      <label>Meeting Category</label>
      <select id="meetCategory">
        <option value="Faculty Review">Faculty Coordination Review</option>
        <option value="Executive Sync">Executive Board Sync</option>
        <option value="Committee Sync">Committee Meeting</option>
        <option value="General Body">General Body Meeting</option>
        <option value="Emergency Meeting">Emergency Sync</option>
      </select>
    </div>
    <div class="form-group">
      <label>Target Member Selection (Target Audience)</label>
      <select id="meetTargetAudience" required>
        <option value="All Members">👥 All Members (Students, Committee &amp; Faculty)</option>
        <option value="Committee Members Only">⭐ Committee Members Only</option>
        <option value="Faculty Coordinators Only">👨‍🏫 Faculty Coordinators Only</option>
        <option value="Student Members Only">🎓 Student Members Only</option>
        <option value="Executive Board">🏛️ Executive Board (HOD, Faculty, President, VP)</option>
      </select>
    </div>
    <div class="form-group">
      <label>Meeting Agenda &amp; Objective</label>
      <textarea id="meetAgenda" rows="3" placeholder="Specify key discussion topics and agenda points..."></textarea>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">📅 Schedule Meeting &amp; Broadcast Notification</button>
  </form>
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

<!-- ── FACULTY PROFILE DRAWER ── -->
<div class="drawer" id="viewProfileDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Faculty Profile Overview</div>
    <button class="drawer-close" onclick="closeDrawer('viewProfileDrawer')">&times;</button>
  </div>
  <div style="text-align:center; padding:15px 0; border-bottom:1px solid var(--line-dark);">
    <div style="width:70px; height:70px; border-radius:50%; background:conic-gradient(from 180deg,var(--accent),var(--navy-700),var(--accent)); padding:3px; margin:0 auto 10px;">
      <div style="width:100%; height:100%; border-radius:50%; background:var(--navy-800); display:flex; align-items:center; justify-content:center; font-family:var(--ff-display); font-weight:700; color:var(--white); font-size:1.4rem;" id="profDrawerAvatar">FC</div>
    </div>
    <b style="font-size:1.1rem; color:var(--navy-950); display:block;" id="profDrawerName">Prof. Manisha Devgunde</b>
    <span style="font-family:var(--ff-mono); font-size:0.72rem; color:var(--accent); letter-spacing:0.1em; text-transform:uppercase;">Faculty Coordinator — Dept. of AI &amp; ML</span>
  </div>
  <div class="form-group" style="margin-top:10px;">
    <label>Full Name</label>
    <input type="text" id="profNameInput" placeholder="e.g. Prof. Manisha Devgunde">
  </div>
  <div class="form-group">
    <label>College Email ID</label>
    <input type="email" id="profEmailInput" placeholder="faculty@zealeducation.com">
  </div>
  <div class="form-group">
    <label>Unique ZPRN</label>
    <input type="text" id="profZprnInput" placeholder="e.g. 125UAM1002">
  </div>
  <div class="form-group">
    <label>Faculty Staff ID</label>
    <input type="text" id="profStaffIdInput" placeholder="e.g. FC-AIML-2024">
  </div>
  <div class="form-group">
    <label>Mobile Number</label>
    <input type="text" id="profPhoneInput" placeholder="+91 XXXXX XXXXX">
  </div>
  <div class="form-group">
    <label>Department / Designation</label>
    <input type="text" id="profDeptInput" value="AI &amp; ML Department · Assistant Professor" readonly style="background:var(--paper-dim);">
  </div>
  <button class="btn btn-primary" style="width:100%; margin-top:10px;" id="saveProfileBtn">Save Profile Changes</button>
</div>

<!-- ── APPROVED EVENTS DRAWER ── -->
<div class="drawer" id="approvedEventsDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Approved Events Overview</div>
    <button class="drawer-close" onclick="closeDrawer('approvedEventsDrawer')">&times;</button>
  </div>
  <p style="font-size:0.85rem; color:var(--muted-dark); line-height:1.5;">List of all official AIMSA department events approved and verified by Faculty Coordinator.</p>
  <div style="display:flex; gap:10px; margin-bottom:12px;">
    <input type="text" id="searchApprovedEvtInput" placeholder="Search approved events..." style="flex:1; padding:8px 12px; border-radius:8px; border:1px solid var(--line-dark); font-size:0.82rem;">
    <button class="btn btn-primary" style="padding:8px 14px; font-size:0.78rem;" onclick="openDrawer('createEventDrawer')">+ Propose Event</button>
  </div>
  <div style="flex:1; overflow-y:auto;">
    <table class="data-table">
      <thead><tr><th>Event Title</th><th>Date</th><th>Registrations</th><th>Status</th></tr></thead>
      <tbody id="drawerApprovedEventsBody">
        <!-- Dynamically populated -->
      </tbody>
    </table>
  </div>
</div>

<!-- ── MEMBER STATISTICS DRAWER ── -->
<div class="drawer" id="memberStatsDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Department Member Statistics</div>
    <button class="drawer-close" onclick="closeDrawer('memberStatsDrawer')">&times;</button>
  </div>
  <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:12px;">
    <div style="background:rgba(62,139,255,0.06); border:1px solid rgba(62,139,255,0.2); padding:12px; border-radius:10px; text-align:center;">
      <b style="font-family:var(--ff-display); font-size:1.4rem; color:var(--accent); display:block;" id="statTotalMemVal">247</b>
      <span style="font-size:0.65rem; color:var(--muted-dark); font-family:var(--ff-mono); text-transform:uppercase;">Total Enrolled</span>
    </div>
    <div style="background:rgba(34,197,94,0.06); border:1px solid rgba(34,197,94,0.2); padding:12px; border-radius:10px; text-align:center;">
      <b style="font-family:var(--ff-display); font-size:1.4rem; color:#16a34a; display:block;" id="statActiveRatioVal">92%</b>
      <span style="font-size:0.65rem; color:var(--muted-dark); font-family:var(--ff-mono); text-transform:uppercase;">Active Ratio</span>
    </div>
    <div style="background:rgba(249,115,22,0.06); border:1px solid rgba(249,115,22,0.2); padding:12px; border-radius:10px; text-align:center;">
      <b style="font-family:var(--ff-display); font-size:1.4rem; color:#ea580c; display:block;" id="statTopBranchVal">AI &amp; ML</b>
      <span style="font-size:0.65rem; color:var(--muted-dark); font-family:var(--ff-mono); text-transform:uppercase;">Top Department</span>
    </div>
  </div>
  <div style="font-weight:700; font-size:0.9rem; margin:10px 0 6px;">Branch Breakdown</div>
  <div id="drawerBranchStats"></div>
  
  <div style="font-weight:700; font-size:0.9rem; margin:14px 0 6px;">Batch / Year Distribution</div>
  <div id="drawerBatchStats"></div>

  <div style="font-weight:700; font-size:0.9rem; margin:16px 0 6px;">Student Directory</div>
  <input type="text" id="searchMemberInput" placeholder="Search members by name, email, ZPRN..." style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--line-dark); font-size:0.82rem; margin-bottom:10px;">
  <div style="flex:1; overflow-y:auto;">
    <table class="data-table">
      <thead><tr><th>Name</th><th>Email / ZPRN</th><th>Branch</th><th>Role</th></tr></thead>
      <tbody id="drawerMembersDirectoryBody">
        <!-- Dynamically populated -->
      </tbody>
    </table>
  </div>
</div>

<!-- ── ATTENDANCE SUMMARY DRAWER ── -->
<div class="drawer" id="attendanceSummaryDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Student Attendance Summary</div>
    <button class="drawer-close" onclick="closeDrawer('attendanceSummaryDrawer')">&times;</button>
  </div>
  <div class="attend-summary" style="margin-bottom:12px;">
    <div class="attend-box present"><span class="attend-box-val" id="drawerAvgAttRate">89%</span><div class="attend-box-label">Avg Attendance</div></div>
    <div class="attend-box total"><span class="attend-box-val" id="drawerTotalEventsCount">24</span><div class="attend-box-label">Events &amp; Syncs</div></div>
    <div class="attend-box present"><span class="attend-box-val" id="drawerVerifiedStatus">Verified</span><div class="attend-box-label">Faculty Sign-off</div></div>
  </div>

  <div style="font-weight:700; font-size:0.9rem; margin:10px 0 6px;">Event &amp; Meeting Attendance Breakdown</div>
  <div style="max-height:180px; overflow-y:auto; margin-bottom:14px;">
    <table class="data-table">
      <thead><tr><th>Event / Meeting</th><th>Date</th><th>Present / Total</th><th>Rate</th><th>Action</th></tr></thead>
      <tbody id="drawerMeetingsAttBody">
        <!-- Dynamically populated -->
      </tbody>
    </table>
  </div>

  <div style="font-weight:700; font-size:0.9rem; margin:10px 0 6px;">All Students Attendance Roster</div>
  <input type="text" id="searchRosterInput" placeholder="Filter student roster by name, ZPRN, status..." style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--line-dark); font-size:0.82rem; margin-bottom:8px;">
  <div style="flex:1; overflow-y:auto;">
    <table class="data-table">
      <thead><tr><th>Student Name</th><th>Email / ZPRN</th><th>Event</th><th>Status</th></tr></thead>
      <tbody id="drawerStudentsRosterBody">
        <!-- Dynamically populated -->
      </tbody>
    </table>
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
    <input type="text" id="evtCoordinator" value="Prof. Manisha Devgunde" readonly style="background:var(--paper-dim);">
  </div>
  <button class="btn btn-primary" style="width:100%; margin-top:10px;" id="saveEventBtn">Publish Event</button>
</div>

<!-- ── VERIFY ATTENDANCE DRAWER ── -->
<div class="drawer" id="verifyAttendanceDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Verify Attendance</div>
    <button class="drawer-close" onclick="closeDrawer('verifyAttendanceDrawer')">&times;</button>
  </div>
  <div class="form-group">
    <label>Select Event</label>
    <select id="verifyAttendEvent">
      <option value="Tech Symposium 2026">Tech Symposium 2026</option>
      <option value="AI Workshop Series">AI Workshop Series</option>
      <option value="Hackathon 2026">Hackathon 2026</option>
    </select>
  </div>
  <div style="flex:1; overflow-y:auto; margin-top:10px;">
    <table class="data-table">
      <thead><tr><th>Student</th><th>Marked By</th><th>Status</th></tr></thead>
      <tbody>
        <tr><td><b>Arjun Patil</b></td><td>Tech Committee</td><td><span class="badge badge-green">Present</span></td></tr>
        <tr><td><b>Sneha Rao</b></td><td>Tech Committee</td><td><span class="badge badge-green">Present</span></td></tr>
        <tr><td><b>Aman Kulkarni</b></td><td>Tech Committee</td><td><span class="badge badge-orange">Absent</span></td></tr>
      </tbody>
    </table>
  </div>
  <button class="btn btn-primary" style="width:100%; margin-top:15px;" id="verifyAttendBtn">Sign off &amp; Verify</button>
</div>

<!-- ── APPROVE ACHIEVEMENTS DRAWER ── -->
<div class="drawer" id="approveAchievementsDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Student Achievements &amp; Nominations</div>
    <button class="drawer-close" onclick="closeDrawer('approveAchievementsDrawer')">&times;</button>
  </div>
  
  <!-- Navigation Sub-tabs -->
  <div style="display:flex; gap:8px; margin-bottom:14px; background:var(--paper-dim); padding:4px; border-radius:10px; border:1px solid var(--line-dark);">
    <button class="btn" id="tabBtnReviewNom" style="flex:1; font-size:0.78rem; padding:8px; border-radius:8px; background:var(--white); color:var(--navy-950); box-shadow:0 2px 8px rgba(0,0,0,0.06);" onclick="switchNominationTab('review')">Review Nominations</button>
    <button class="btn" id="tabBtnCreateNom" style="flex:1; font-size:0.78rem; padding:8px; border-radius:8px; background:transparent; color:var(--muted-dark);" onclick="switchNominationTab('create')">+ Nominate Student</button>
  </div>

  <!-- ── REVIEW NOMINATIONS SECTION ── -->
  <div id="reviewNominationsSection">
    <!-- Review KPI Summary -->
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:12px;">
      <div style="background:rgba(249,115,22,0.06); border:1px solid rgba(249,115,22,0.2); padding:10px; border-radius:10px; text-align:center;">
        <b style="font-family:var(--ff-display); font-size:1.3rem; color:#ea580c; display:block;" id="kpiPendingNomCount">2</b>
        <span style="font-size:0.62rem; color:var(--muted-dark); font-family:var(--ff-mono); text-transform:uppercase;">Pending Review</span>
      </div>
      <div style="background:rgba(34,197,94,0.06); border:1px solid rgba(34,197,94,0.2); padding:10px; border-radius:10px; text-align:center;">
        <b style="font-family:var(--ff-display); font-size:1.3rem; color:#16a34a; display:block;" id="kpiApprovedNomCount">2</b>
        <span style="font-size:0.62rem; color:var(--muted-dark); font-family:var(--ff-mono); text-transform:uppercase;">Approved</span>
      </div>
      <div style="background:rgba(62,139,255,0.06); border:1px solid rgba(62,139,255,0.2); padding:10px; border-radius:10px; text-align:center;">
        <b style="font-family:var(--ff-display); font-size:1.3rem; color:var(--accent); display:block;" id="kpiTotalNomCount">4</b>
        <span style="font-size:0.62rem; color:var(--muted-dark); font-family:var(--ff-mono); text-transform:uppercase;">Total Submitted</span>
      </div>
    </div>

    <!-- Search & Filter Controls -->
    <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:12px;">
      <input type="text" id="searchNominationInput" placeholder="Search nominations by student, title, category..." style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--line-dark); font-size:0.82rem;">
      <div style="display:flex; gap:6px; overflow-x:auto; padding-bottom:2px;">
        <button class="btn btn-ghost nom-filter-pill active" data-status="all" style="padding:4px 10px; font-size:0.7rem; border-radius:99px;" onclick="filterNominationList('all', this)">All</button>
        <button class="btn btn-ghost nom-filter-pill" data-status="Pending" style="padding:4px 10px; font-size:0.7rem; border-radius:99px;" onclick="filterNominationList('Pending', this)">Pending</button>
        <button class="btn btn-ghost nom-filter-pill" data-status="Approved" style="padding:4px 10px; font-size:0.7rem; border-radius:99px;" onclick="filterNominationList('Approved', this)">Approved</button>
        <button class="btn btn-ghost nom-filter-pill" data-status="Rejected" style="padding:4px 10px; font-size:0.7rem; border-radius:99px;" onclick="filterNominationList('Rejected', this)">Rejected</button>
      </div>
    </div>

    <div id="pendingAchievementsContainer" style="display:flex; flex-direction:column; gap:12px; overflow-y:auto; flex:1; max-height:420px;">
      <!-- Dynamically populated -->
    </div>
  </div>

  <!-- ── SUBMIT NEW NOMINATION SECTION (Form) ── -->
  <div id="newNominationFormSection" style="display:none; border:1.5px solid var(--accent); border-radius:12px; padding:16px; background:var(--paper); margin-bottom:14px;">
    <b style="font-size:0.9rem; color:var(--navy-950); display:block; margin-bottom:10px;">Submit Student Achievement Nomination</b>
    <div class="form-group">
      <label>Student Name</label>
      <input type="text" id="nomStudentName" placeholder="e.g. Arjun Patil">
    </div>
    <div class="form-group">
      <label>Student Email ID</label>
      <input type="email" id="nomStudentEmail" placeholder="e.g. student@zealeducation.com">
    </div>
    <div class="form-group">
      <label>Achievement Title</label>
      <input type="text" id="nomTitle" placeholder="e.g. 1st Place National Hackathon 2026">
    </div>
    <div class="form-group">
      <label>Category</label>
      <select id="nomCategory">
        <option value="Hackathon Winner">Hackathon Winner</option>
        <option value="Academic Excellence">Academic Excellence</option>
        <option value="Research Paper">Research Paper Published</option>
        <option value="Leadership & Organizing">Leadership &amp; Organizing</option>
      </select>
    </div>
    <div class="form-group">
      <label>Description / Remarks</label>
      <textarea id="nomDescription" rows="3" placeholder="Details about student achievement..."></textarea>
    </div>
    <button class="btn btn-primary" style="width:100%; margin-top:6px;" onclick="submitNewNomination()">Submit &amp; Approve Nomination</button>
  </div>
</div>

<!-- ── REPORT HUB DRAWER ── -->
<div class="drawer" id="reportHubDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Reports Hub</div>
    <button class="drawer-close" onclick="closeDrawer('reportHubDrawer')">&times;</button>
  </div>
  <div class="form-group">
    <label>Select Report Type</label>
    <select id="reportCategory">
      <option value="Member Report">Member Report</option>
      <option value="Event Report">Event Report</option>
      <option value="Attendance Report">Attendance Report</option>
      <option value="Achievement Report">Achievement Report</option>
      <option value="Certificate Report">Certificate Report</option>
      <option value="Committee Report">Committee Report</option>
      <option value="Participation Statistics">Participation Statistics</option>
    </select>
  </div>
  <div style="display:flex; flex-direction:column; gap:12px; margin-top:15px;">
    <button class="btn btn-primary" style="justify-content:center;" onclick="exportReport('PDF')">Export as PDF</button>
    <button class="btn btn-ghost" style="justify-content:center;" onclick="exportReport('Excel')">Export as Excel</button>
    <button class="btn btn-ghost" style="justify-content:center;" onclick="exportReport('Print')">Print Report</button>
  </div>
</div>

<!-- ── NOTIFY STUDENTS DRAWER ── -->
<div class="drawer" id="notifyStudentsDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Send Notification</div>
    <button class="drawer-close" onclick="closeDrawer('notifyStudentsDrawer')">&times;</button>
  </div>
  <div class="form-group">
    <label>Notification Title</label>
    <input type="text" id="notifTitle" placeholder="e.g. Event Reminder">
  </div>
  <div class="form-group">
    <label>Message</label>
    <textarea id="notifMessage" rows="4" placeholder="Enter your message..."></textarea>
  </div>
  <div class="form-group">
    <label>Recipients</label>
    <select id="notifRecipients">
      <option value="all">All Users</option>
      <option value="Student Member">All Students</option>
      <option value="Faculty Coordinator">All Faculty</option>
      <option value="HOD">HOD</option>
      <option value="Association President">Association President</option>
      <option value="Committee Member">Committee Members</option>
      <option value="3rd Year">3rd Year Students</option>
      <option value="2nd Year">2nd Year Students</option>
      <option value="4th Year">4th Year Students</option>
    </select>
  </div>
  <div class="form-group">
    <label>Priority</label>
    <select id="notifPriority">
      <option value="green">Normal</option>
      <option value="yellow">Important</option>
      <option value="red">Urgent</option>
    </select>
  </div>
  <button class="btn btn-primary" style="width:100%; margin-top:10px;" id="sendNotifBtn">Send Notification</button>
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
  email: 'faculty@zealeducation.com',
  name: 'Faculty Coordinator',
  role: 'Faculty Coordinator'
};

document.querySelector('.content-title').innerHTML = `Welcome, ${currentUser.name} 👋`;

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
  formData.append('coordinator', currentUser.name);
  formData.append('status', 'Approved');

  try {
    const res = await fetch('ajax/eventActions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      addNotification('New Event Published', `${currentUser.name} proposed: ${name}.`, 'green', 'all');
      await loadEventsFromDB();
      closeDrawer('createEventDrawer');
      alert('Event published successfully! Students are notified.');
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

// Load events from MySQL
async function loadEventsFromDB() {
  try {
    const [pendingRes, approvedRes] = await Promise.all([
      fetch('ajax/eventActions.php?action=getPendingEvents'),
      fetch('ajax/eventActions.php?action=getApprovedEvents')
    ]);
    const pendingData = await pendingRes.json();
    const approvedData = await approvedRes.json();

    const pendingBody = document.getElementById('pendingEventsBody');
    const approvedBody = document.getElementById('approvedEventsBody');
    pendingBody.innerHTML = '';
    approvedBody.innerHTML = '';

    if (pendingData.status === 'success' && pendingData.events.length > 0) {
      pendingData.events.forEach(evt => {
        const row = document.createElement('tr');
        row.innerHTML = `<td><b>${escapeHtml(evt.name)}</b></td><td>${escapeHtml(evt.coordinator || 'N/A')}</td><td>${formatDate(evt.date)}</td><td><span class="badge badge-orange">Pending</span></td><td><button class="btn btn-primary" style="padding:4px 12px;font-size:.72rem;" onclick="approveEvent(${evt.id})">Approve</button> <button class="btn btn-ghost" style="padding:4px 12px;font-size:.72rem;border-color:#f87171;color:#ef4444;" onclick="rejectEvent(${evt.id})">Reject</button></td>`;
        pendingBody.appendChild(row);
      });
    } else {
      pendingBody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--muted-dark);padding:20px;">No pending events.</td></tr>';
    }

    if (approvedData.status === 'success' && approvedData.events.length > 0) {
      approvedData.events.forEach(evt => {
        const row = document.createElement('tr');
        row.innerHTML = `<td><b>${escapeHtml(evt.name)}</b></td><td>${formatDate(evt.date)}</td><td>${evt.max_participants || 0}</td><td><span class="badge badge-green">Approved</span></td>`;
        approvedBody.appendChild(row);
      });
    } else {
      approvedBody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:var(--muted-dark);padding:20px;">No approved events yet.</td></tr>';
    }

    // Update stats
    const pendingStat = document.querySelector('#pending .stat-val');
    const approvedStat = document.querySelector('#approved .stat-val');
    if (pendingStat) pendingStat.textContent = pendingData.events.length;
    if (approvedStat) approvedStat.textContent = approvedData.events.length;
  } catch (e) {
    console.error('Failed to load events:', e);
  }
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function formatDate(dateStr) {
  if (!dateStr) return 'N/A';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

window.approveEvent = async function(id) {
  if (!confirm('Are you sure you want to approve this event?')) return;
  const formData = new FormData();
  formData.append('action', 'approveEvent');
  formData.append('id', id);
  try {
    const res = await fetch('ajax/eventActions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      addNotification('Event Approved', 'An event proposal has been approved.', 'green', 'all');
      await loadEventsFromDB();
    } else {
      alert(data.message || 'Failed to approve event');
    }
  } catch (e) {
    alert('Error approving event: ' + e.message);
  }
};

window.rejectEvent = async function(id) {
  if (!confirm('Are you sure you want to reject this event?')) return;
  const formData = new FormData();
  formData.append('action', 'rejectEvent');
  formData.append('id', id);
  try {
    const res = await fetch('ajax/eventActions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      addNotification('Event Rejected', 'An event proposal has been rejected.', 'red', 'all');
      await loadEventsFromDB();
    } else {
      alert(data.message || 'Failed to reject event');
    }
  } catch (e) {
    alert('Error rejecting event: ' + e.message);
  }
};

// Trigger drawers from quick actions & cards
const quickApproveBtn = document.getElementById('quickApproveEvent');
if (quickApproveBtn) {
  quickApproveBtn.addEventListener('click', () => {
    const pendingSection = document.getElementById('pendingEventsBody');
    const pendingCard = document.getElementById('pending');
    if (pendingSection) {
      pendingSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
      loadEventsFromDB();
      if (pendingCard) {
        pendingCard.style.outline = '2px solid var(--accent)';
        setTimeout(() => { pendingCard.style.outline = 'none'; }, 2000);
      }
    }
  });
}

const quickReportsBtn = document.getElementById('quickViewReports');
if (quickReportsBtn) {
  quickReportsBtn.addEventListener('click', () => {
    openDrawer('reportHubDrawer');
  });
}

const quickNominateBtn = document.getElementById('quickNominate');
if (quickNominateBtn) {
  quickNominateBtn.addEventListener('click', () => {
    renderPendingAchievements();
    openDrawer('approveAchievementsDrawer');
  });
}

const quickNotifyBtn = document.getElementById('quickNotifyStudents');
if (quickNotifyBtn) {
  quickNotifyBtn.addEventListener('click', () => {
    openDrawer('notifyStudentsDrawer');
  });
}

// ---------- APPROVED EVENTS DRAWER & LOGIC ----------
async function loadApprovedEventsDrawer() {
  try {
    const res = await fetch('ajax/eventActions.php?action=getApprovedEvents');
    const data = await res.json();
    const body = document.getElementById('drawerApprovedEventsBody');
    if (!body) return;

    if (data.status === 'success' && data.events.length > 0) {
      body.innerHTML = data.events.map(evt => `
        <tr>
          <td><b>${escapeHtml(evt.name)}</b><br><span style="font-size:0.72rem; color:var(--muted-dark);">${escapeHtml(evt.venue || 'Campus')}</span></td>
          <td>${formatDate(evt.date)}</td>
          <td>${evt.max_participants || 0} enrolled</td>
          <td><span class="badge badge-green">Approved</span></td>
        </tr>`).join('');
    } else {
      body.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--muted-dark); padding:20px;">No approved events found.</td></tr>`;
    }
  } catch (e) {
    console.error('Failed to load approved events for drawer:', e);
  }
}

const navEvEl = document.getElementById('navEvents');
if (navEvEl) {
  navEvEl.addEventListener('click', (e) => {
    e.preventDefault();
    loadApprovedEventsDrawer();
    openDrawer('approvedEventsDrawer');
  });
}
const apprEl = document.getElementById('approved');
if (apprEl) {
  apprEl.addEventListener('click', () => {
    loadApprovedEventsDrawer();
    openDrawer('approvedEventsDrawer');
  });
}

// ---------- MEMBER STATISTICS DRAWER & LOGIC ----------
async function loadMemberStatsFromDB() {
  try {
    const res = await fetch('ajax/faculty_actions.php?action=get_member_statistics');
    const data = await res.json();
    if (data.status === 'success') {
      const totalCount = data.members ? data.members.length : 247;
      if (document.getElementById('statTotalMemVal')) document.getElementById('statTotalMemVal').textContent = totalCount;

      const branchDiv = document.getElementById('drawerBranchStats');
      const batchDiv = document.getElementById('drawerBatchStats');
      const membersBody = document.getElementById('drawerMembersDirectoryBody');

      if (branchDiv) {
        branchDiv.innerHTML = data.branch_breakdown.map(b => `
          <div class="progress-item">
            <div class="progress-label"><span>${escapeHtml(b.branch)}</span><span>${b.count} members</span></div>
            <div class="progress-bar"><div class="progress-fill" style="width:${Math.min(100, Math.round((b.count / totalCount) * 100))}%"></div></div>
          </div>`).join('');
      }

      if (batchDiv) {
        batchDiv.innerHTML = data.batch_breakdown.map(b => `
          <div class="progress-item">
            <div class="progress-label"><span>Batch ${escapeHtml(b.batch)}</span><span>${b.count} members</span></div>
            <div class="progress-bar"><div class="progress-fill" style="width:${Math.min(100, Math.round((b.count / totalCount) * 100))}%"></div></div>
          </div>`).join('');
      }

      if (membersBody) {
        membersBody.innerHTML = data.members.map(m => `
          <tr>
            <td><b>${escapeHtml(m.name)}</b></td>
            <td><span style="font-size:0.75rem; color:var(--muted-dark);">${escapeHtml(m.email)}</span><br><span class="badge badge-gray">${escapeHtml(m.zprn || 'N/A')}</span></td>
            <td>${escapeHtml(m.branch || 'AI & ML')}</td>
            <td><span class="badge ${m.role === 'Student Member' ? 'badge-blue' : 'badge-green'}">${escapeHtml(m.role)}</span></td>
          </tr>`).join('');
      }
    }
  } catch (e) {
    console.error('Failed to load member statistics:', e);
  }
}

const navMemStatsEl = document.getElementById('navMemberStats');
if (navMemStatsEl) {
  navMemStatsEl.addEventListener('click', (e) => {
    e.preventDefault();
    loadMemberStatsFromDB();
    openDrawer('memberStatsDrawer');
  });
}
const memStatsCardEl = document.getElementById('memberstats');
if (memStatsCardEl) {
  memStatsCardEl.addEventListener('click', () => {
    loadMemberStatsFromDB();
    openDrawer('memberStatsDrawer');
  });
}

// ---------- ATTENDANCE SUMMARY DRAWER & LOGIC ----------
async function loadAttendanceSummaryFromDB() {
  try {
    const res = await fetch('ajax/faculty_actions.php?action=get_attendance_summary');
    const data = await res.json();
    if (data.status === 'success') {
      if (document.getElementById('drawerAvgAttRate')) document.getElementById('drawerAvgAttRate').textContent = data.overall_rate;
      if (document.getElementById('drawerTotalEventsCount')) document.getElementById('drawerTotalEventsCount').textContent = data.total_meetings;

      const meetingsBody = document.getElementById('drawerMeetingsAttBody');
      if (meetingsBody) {
        meetingsBody.innerHTML = data.meetings.map(m => {
          const total = m.present_count + m.absent_count;
          const rate = total > 0 ? Math.round((m.present_count / total) * 100) : 100;
          return `
            <tr>
              <td><b>${escapeHtml(m.title)}</b></td>
              <td>${formatDate(m.meeting_date)}</td>
              <td>${m.present_count} / ${total}</td>
              <td><span class="badge badge-green">${rate}%</span></td>
              <td><button class="btn btn-primary" style="padding:3px 8px; font-size:0.68rem;" onclick="verifyMeetingAttendance(${m.id})">${m.verified_by ? 'Verified ✓' : 'Sign-off'}</button></td>
            </tr>`;
        }).join('');
      }

      const rosterBody = document.getElementById('drawerStudentsRosterBody');
      if (rosterBody) {
        rosterBody.innerHTML = data.roster.map(r => `
          <tr>
            <td><b>${escapeHtml(r.student_name)}</b></td>
            <td><span style="font-size:0.75rem; color:var(--muted-dark);">${escapeHtml(r.student_email)}</span><br><span class="badge badge-gray">${escapeHtml(r.zprn || 'N/A')}</span></td>
            <td>${escapeHtml(r.event_name || 'General Body Sync')}</td>
            <td><span class="badge ${r.status === 'Present' ? 'badge-green' : 'badge-orange'}">${escapeHtml(r.status)}</span></td>
          </tr>`).join('');
      }
    }
  } catch (e) {
    console.error('Failed to load attendance summary:', e);
  }
}

window.verifyMeetingAttendance = async function(meetingId) {
  const formData = new FormData();
  formData.append('action', 'verify_meeting_attendance');
  formData.append('meeting_id', meetingId);
  formData.append('verified_by', currentUser.name);

  try {
    const res = await fetch('ajax/faculty_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      await loadAttendanceSummaryFromDB();
    }
  } catch (e) {
    alert('Error verifying attendance: ' + e.message);
  }
};

const attEl = document.getElementById('attendance');
if (attEl) {
  attEl.addEventListener('click', () => {
    loadAttendanceSummaryFromDB();
    openDrawer('attendanceSummaryDrawer');
  });
}
const navAttEl2 = document.getElementById('navAttendance');
if (navAttEl2) {
  navAttEl2.addEventListener('click', (e) => {
    e.preventDefault();
    loadAttendanceSummaryFromDB();
    openDrawer('attendanceSummaryDrawer');
  });
}

// Explicit listeners for Full Log, Detail View, and Nominate card action buttons
document.getElementById('btnFullLogAttendance')?.addEventListener('click', (e) => {
  e.stopPropagation();
  loadAttendanceSummaryFromDB();
  openDrawer('attendanceSummaryDrawer');
});

document.getElementById('btnDetailViewMemberStats')?.addEventListener('click', (e) => {
  e.stopPropagation();
  loadMemberStatsFromDB();
  openDrawer('memberStatsDrawer');
});

// ---------- STUDENT ACHIEVEMENTS & NOMINATIONS LOGIC ----------
let currentNominationFilter = 'all';

window.switchNominationTab = function(tab) {
  const reviewSec = document.getElementById('reviewNominationsSection');
  const createSec = document.getElementById('newNominationFormSection');
  const btnReview = document.getElementById('tabBtnReviewNom');
  const btnCreate = document.getElementById('tabBtnCreateNom');

  if (tab === 'review') {
    if (reviewSec) reviewSec.style.display = 'block';
    if (createSec) createSec.style.display = 'none';
    if (btnReview) { btnReview.style.background = 'var(--white)'; btnReview.style.color = 'var(--navy-950)'; }
    if (btnCreate) { btnCreate.style.background = 'transparent'; btnCreate.style.color = 'var(--muted-dark)'; }
    renderPendingAchievements(currentNominationFilter);
  } else {
    if (reviewSec) reviewSec.style.display = 'none';
    if (createSec) createSec.style.display = 'block';
    if (btnCreate) { btnCreate.style.background = 'var(--white)'; btnCreate.style.color = 'var(--navy-950)'; }
    if (btnReview) { btnReview.style.background = 'transparent'; btnReview.style.color = 'var(--muted-dark)'; }
  }
};

window.filterNominationList = function(status, pillEl) {
  currentNominationFilter = status;
  document.querySelectorAll('.nom-filter-pill').forEach(el => {
    el.style.background = 'transparent';
    el.style.borderColor = 'var(--line-dark)';
    el.style.color = 'var(--navy-800)';
  });
  if (pillEl) {
    pillEl.style.background = 'var(--accent)';
    pillEl.style.borderColor = 'var(--accent)';
    pillEl.style.color = 'var(--white)';
  }
  const query = document.getElementById('searchNominationInput')?.value.trim() || '';
  renderPendingAchievements(status, query);
};

window.renderPendingAchievements = function(filterStatus = 'all', searchQuery = '') {
  const container = document.getElementById('pendingAchievementsContainer');
  if (!container) return;

  let achievements = JSON.parse(localStorage.getItem('aimsa_achievements'));
  if (!achievements || achievements.length === 0) {
    achievements = [
      { student: 'Arjun Patil', email: 'student@zealeducation.com', title: '1st Place Hackathon 2026', category: 'Hackathon Winner', description: 'Won 1st prize in AIMSA AI Hackathon 2026.', status: 'Approved', nominated_by: 'Tech Committee', date: '2026-07-20' },
      { student: 'Riya Desai', email: 'committee@zealeducation.com', title: 'Best Organiser Award', category: 'Leadership & Organizing', description: 'Exemplary leadership during Tech Symposium 2026.', status: 'Approved', nominated_by: 'Technical Committee', date: '2026-07-21' },
      { student: 'Sneha Rao', email: 'sneha.rao@zealeducation.com', title: 'IEEE Research Paper Published', category: 'Research Paper', description: 'Published research paper on AI models in healthcare.', status: 'Pending', nominated_by: 'Dr. Dipali Shende', date: '2026-07-22' },
      { student: 'Aman Kulkarni', email: 'aman.k@zealeducation.com', title: 'Top Performer - 100% Attendance', category: 'Academic Excellence', description: 'Maintained perfect attendance for Semester II.', status: 'Pending', nominated_by: 'Prof. Manisha Devgunde', date: '2026-07-22' }
    ];
    localStorage.setItem('aimsa_achievements', JSON.stringify(achievements));
  }

  // Update KPI counters
  const pendingCount = achievements.filter(a => a.status === 'Pending').length;
  const approvedCount = achievements.filter(a => a.status === 'Approved').length;
  const totalCount = achievements.length;

  if (document.getElementById('kpiPendingNomCount')) document.getElementById('kpiPendingNomCount').textContent = pendingCount;
  if (document.getElementById('kpiApprovedNomCount')) document.getElementById('kpiApprovedNomCount').textContent = approvedCount;
  if (document.getElementById('kpiTotalNomCount')) document.getElementById('kpiTotalNomCount').textContent = totalCount;

  // Filter achievements
  let list = achievements;
  if (filterStatus !== 'all') {
    list = list.filter(a => (a.status || 'Pending').toLowerCase() === filterStatus.toLowerCase());
  }

  if (searchQuery) {
    const q = searchQuery.toLowerCase();
    list = list.filter(a => (a.student || '').toLowerCase().includes(q) || (a.title || '').toLowerCase().includes(q) || (a.category || '').toLowerCase().includes(q));
  }

  if (list.length === 0) {
    container.innerHTML = `<p style="text-align:center; color:var(--muted-dark); padding:30px 10px; font-size:0.85rem;">No nomination records found.</p>`;
    return;
  }

  container.innerHTML = list.map((a, idx) => {
    const originalIndex = achievements.findIndex(item => item.title === a.title && item.student === a.student);
    const targetIdx = originalIndex !== -1 ? originalIndex : idx;

    let badgeHtml = '<span class="badge badge-orange">Pending Review</span>';
    let actionsHtml = `
      <div style="display:flex; gap:8px; margin-top:8px;">
        <button class="btn btn-primary" style="flex:1; padding:6px 10px; font-size:0.75rem;" onclick="approveAchievement(${targetIdx})">✓ Approve</button>
        <button class="btn btn-ghost" style="flex:1; padding:6px 10px; font-size:0.75rem; border-color:#f87171; color:#ef4444;" onclick="rejectAchievement(${targetIdx})">✕ Reject</button>
        <button class="btn btn-ghost" style="padding:6px 10px; font-size:0.75rem;" onclick="requestNominationDetails(${targetIdx})">💬 Details</button>
      </div>`;

    if (a.status === 'Approved') {
      badgeHtml = '<span class="badge badge-green">Approved ✓</span>';
      actionsHtml = `
        <div style="display:flex; gap:8px; margin-top:8px;">
          <button class="btn btn-ghost" style="flex:1; padding:5px 8px; font-size:0.72rem; border-color:var(--accent); color:var(--accent);" onclick="issueNominationCert('${escapeHtml(a.student)}', '${escapeHtml(a.title)}')">📜 Issue Certificate</button>
        </div>`;
    } else if (a.status === 'Rejected') {
      badgeHtml = '<span class="badge badge-gray" style="color:#ef4444; border-color:#f87171;">Rejected</span>';
      actionsHtml = `
        <div style="display:flex; gap:8px; margin-top:8px;">
          <button class="btn btn-ghost" style="padding:4px 8px; font-size:0.72rem;" onclick="approveAchievement(${targetIdx})">Re-evaluate &amp; Approve</button>
        </div>`;
    }

    return `
      <div style="border:1px solid var(--line-dark); border-radius:12px; padding:14px; background:var(--paper); display:flex; flex-direction:column; gap:6px; transition:border-color 0.2s;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
          <div>
            <b style="font-size:0.9rem; color:var(--navy-950); display:block;">${escapeHtml(a.title)}</b>
            <span style="font-size:0.75rem; color:var(--accent); font-family:var(--ff-mono); font-weight:600;">${escapeHtml(a.student)}</span>
          </div>
          ${badgeHtml}
        </div>
        <div style="font-size:0.72rem; color:var(--muted-dark); font-family:var(--ff-mono);">Category: ${escapeHtml(a.category)} · Nominated by: ${escapeHtml(a.nominated_by || 'Faculty')} · ${a.date || 'Today'}</div>
        <p style="font-size:0.8rem; color:var(--navy-900); margin:4px 0; background:var(--white); padding:8px 10px; border-radius:6px; border:1px solid rgba(8,23,51,0.06);">${escapeHtml(a.description || 'No description provided.')}</p>
        ${actionsHtml}
      </div>`;
  }).join('');
};

window.approveAchievement = function(idx) {
  const achievements = JSON.parse(localStorage.getItem('aimsa_achievements')) || [];
  if (achievements[idx]) {
    achievements[idx].status = 'Approved';
    localStorage.setItem('aimsa_achievements', JSON.stringify(achievements));
    addNotification('Achievement Approved', `Achievement nomination "${achievements[idx].title}" was approved.`, 'green', achievements[idx].email || 'all');
    alert('Achievement approved successfully!');
    renderPendingAchievements(currentNominationFilter);
  }
};

window.rejectAchievement = function(idx) {
  const achievements = JSON.parse(localStorage.getItem('aimsa_achievements')) || [];
  if (achievements[idx]) {
    achievements[idx].status = 'Rejected';
    localStorage.setItem('aimsa_achievements', JSON.stringify(achievements));
    addNotification('Achievement Rejected', `Achievement nomination "${achievements[idx].title}" was rejected.`, 'red', achievements[idx].email || 'all');
    alert('Achievement rejected.');
    renderPendingAchievements(currentNominationFilter);
  }
};

window.requestNominationDetails = function(idx) {
  const achievements = JSON.parse(localStorage.getItem('aimsa_achievements')) || [];
  if (achievements[idx]) {
    alert(`Clarification Request sent to nominator (${achievements[idx].nominated_by}) for nomination: "${achievements[idx].title}".`);
  }
};

window.issueNominationCert = function(student, title) {
  alert(`Certificate of Achievement issued for ${student} (${title})! Certificate record logged.`);
};

document.getElementById('btnNominateAchievement')?.addEventListener('click', (e) => {
  e.stopPropagation();
  renderPendingAchievements();
  openDrawer('approveAchievementsDrawer');
});

const navAchEl = document.getElementById('navAchievements');
if (navAchEl) {
  navAchEl.addEventListener('click', (e) => {
    e.preventDefault();
    renderPendingAchievements();
    openDrawer('approveAchievementsDrawer');
  });
}

const achCardEl = document.getElementById('achievements');
if (achCardEl) {
  achCardEl.addEventListener('click', (e) => {
    renderPendingAchievements();
    openDrawer('approveAchievementsDrawer');
  });
}

window.toggleNominationForm = function(show) {
  const form = document.getElementById('newNominationFormSection');
  if (form) form.style.display = show ? 'block' : 'none';
};

window.submitNewNomination = function() {
  const name = document.getElementById('nomStudentName').value.trim();
  const email = document.getElementById('nomStudentEmail').value.trim();
  const title = document.getElementById('nomTitle').value.trim();
  const cat = document.getElementById('nomCategory').value;
  const desc = document.getElementById('nomDescription').value.trim();

  if (!name || !email || !title) {
    alert('Please enter Student Name, Email, and Achievement Title.');
    return;
  }

  const achievements = JSON.parse(localStorage.getItem('aimsa_achievements')) || [];
  achievements.unshift({
    student: name,
    email: email,
    title: title,
    category: cat,
    description: desc,
    status: 'Approved',
    nominated_by: currentUser.name || 'Faculty Coordinator',
    date: new Date().toLocaleDateString()
  });
  localStorage.setItem('aimsa_achievements', JSON.stringify(achievements));

  addNotification('New Student Achievement Nominated', `${name} nominated for: ${title}`, 'green', 'all');
  alert(`Student nomination for ${name} submitted successfully!`);
  
  document.getElementById('nomStudentName').value = '';
  document.getElementById('nomStudentEmail').value = '';
  document.getElementById('nomTitle').value = '';
  document.getElementById('nomDescription').value = '';
  toggleNominationForm(false);
  renderPendingAchievements();
};

// ---------- PROFILE UPDATE LOGIC ----------
const saveProfileBtn = document.getElementById('saveProfileBtn');
if (saveProfileBtn) {
  saveProfileBtn.addEventListener('click', async () => {
    const name = document.getElementById('profNameInput').value.trim();
    const email = document.getElementById('profEmailInput').value.trim();
    const zprn = document.getElementById('profZprnInput').value.trim();
    const phone = document.getElementById('profPhoneInput').value.trim();
    const staffId = document.getElementById('profStaffIdInput').value.trim();

    if (!name || !email) {
      alert('Name and Email are required.');
      return;
    }

    const formData = new FormData();
    formData.append('action', 'update_faculty_profile');
    formData.append('name', name);
    formData.append('email', email);
    formData.append('zprn', zprn);
    formData.append('phone', phone);
    formData.append('staff_id', staffId);

    try {
      const res = await fetch('ajax/faculty_actions.php', { method: 'POST', body: formData });
      const data = await res.json();
      if (data.status === 'success') {
        alert(data.message);
        currentUser.name = name;
        currentUser.email = email;
        currentUser.zprn = zprn;
        sessionStorage.setItem('current_user', JSON.stringify(currentUser));
        if (document.getElementById('headerUserName')) document.getElementById('headerUserName').textContent = name;
        if (document.getElementById('sidebarUserName')) document.getElementById('sidebarUserName').textContent = name;
        if (document.getElementById('profDrawerName')) document.getElementById('profDrawerName').textContent = name;
        document.querySelector('.content-title').innerHTML = `Welcome, ${name} 👋`;
        closeDrawer('viewProfileDrawer');
      } else {
        alert(data.message || 'Failed to update profile.');
      }
    } catch (e) {
      alert('Error updating profile: ' + e.message);
    }
  });
}

window.exportReport = function(format) {
  const cat = document.getElementById('reportCategory').value;
  alert(`Exporting ${cat}... Format: ${format} Success! Download initiated.`);
};

window.sendStudentNotification = function() {
  const title = document.getElementById('notifTitle').value.trim();
  const message = document.getElementById('notifMessage').value.trim();
  const recipients = document.getElementById('notifRecipients').value;
  const priority = document.getElementById('notifPriority').value;

  if (!title || !message) {
    alert('Please enter both title and message.');
    return;
  }

  const recipientLabel = recipients === 'all' ? 'All Users' : recipients;
  addNotification(title, message, priority, recipients);
  alert(`Notification sent to ${recipientLabel}!`);
  closeDrawer('notifyStudentsDrawer');
  document.getElementById('notifTitle').value = '';
  document.getElementById('notifMessage').value = '';
};

const sendNotifBtn = document.getElementById('sendNotifBtn');
if (sendNotifBtn) {
  sendNotifBtn.addEventListener('click', () => {
    window.sendStudentNotification();
  });
}

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
    if (currentUser.year && currentUser.year.toLowerCase() === r.recipient.toLowerCase()) return true;
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
    if (currentUser.year && currentUser.year.toLowerCase() === r.recipient.toLowerCase()) return false;
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

// Drawer table search filters
document.getElementById('searchApprovedEvtInput')?.addEventListener('input', (e) => {
  const query = e.target.value.toLowerCase().trim();
  document.querySelectorAll('#drawerApprovedEventsBody tr').forEach(tr => {
    tr.style.display = tr.textContent.toLowerCase().includes(query) ? '' : 'none';
  });
});

document.getElementById('searchMemberInput')?.addEventListener('input', (e) => {
  const query = e.target.value.toLowerCase().trim();
  document.querySelectorAll('#drawerMembersDirectoryBody tr').forEach(tr => {
    tr.style.display = tr.textContent.toLowerCase().includes(query) ? '' : 'none';
  });
});

document.getElementById('searchRosterInput')?.addEventListener('input', (e) => {
  const query = e.target.value.toLowerCase().trim();
  document.querySelectorAll('#drawerStudentsRosterBody tr').forEach(tr => {
    tr.style.display = tr.textContent.toLowerCase().includes(query) ? '' : 'none';
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

// ── SCHEDULED MEETINGS & SYNCS ENGINE ──
async function fetchScheduledMeetings() {
  const tbody = document.getElementById('scheduledMeetingsTableBody');
  if (!tbody) return;

  try {
    const res = await fetch('ajax/faculty_actions.php?action=get_meetings');
    const data = await res.json();
    if (data.status === 'success' && data.meetings && data.meetings.length > 0) {
      tbody.innerHTML = data.meetings.map(m => {
        const isCancelled = (m.status || '').toLowerCase() === 'cancelled';
        const isCompleted = (m.status || '').toLowerCase() === 'completed';
        let statusBadge = '<span class="badge badge-green">Scheduled</span>';
        if (isCancelled) statusBadge = '<span class="badge" style="background:#ef4444; color:#fff;">Cancelled</span>';
        else if (isCompleted) statusBadge = '<span class="badge badge-blue">Completed</span>';

        return `
          <tr>
            <td><b>${escapeHtml(m.title)}</b><div style="font-size:0.75rem; color:var(--muted-dark);">${escapeHtml(m.category || 'General Sync')}</div></td>
            <td>📅 ${formatDate(m.meeting_date)} · ⏰ ${escapeHtml(m.meeting_time || '10:00 AM')}</td>
            <td>📍 ${escapeHtml(m.venue || 'Seminar Hall')}</td>
            <td><span class="badge badge-blue">${escapeHtml(m.target_audience || 'All Members')}</span></td>
            <td>${statusBadge}</td>
            <td style="white-space:nowrap;">
              ${!isCancelled && !isCompleted ? `
                <button class="btn" style="background:#dbeafe; color:#1d4ed8; border:1px solid #93c5fd; padding:4px 10px; font-size:0.75rem; margin-right:4px;" onclick="editMeeting(${m.id},'${escapeHtml(m.title)}','${m.meeting_date}','${escapeHtml(m.meeting_time)}','${escapeHtml(m.venue)}','${escapeHtml(m.category || 'General Body')}','${escapeHtml(m.target_audience || 'All Members')}','${escapeHtml(m.agenda || '')}')">
                  ✏️ Reschedule
                </button>
                <button class="btn" style="background:#fee2e2; color:#dc2626; border:1px solid #fca5a5; padding:4px 10px; font-size:0.75rem;" onclick="cancelMeeting(${m.id}, '${escapeHtml(m.title)}')">
                  🚫 Cancel
                </button>
              ` : `<span style="font-size:0.75rem; color:var(--muted-dark);">${isCancelled ? 'Cancelled' : 'Closed'}</span>`}
            </td>
          </tr>
        `;
      }).join('');
    } else {
      tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:var(--muted-dark); padding:20px;">No scheduled meetings found. Click "Schedule New Meeting" to create one.</td></tr>`;
    }
  } catch (e) {
    console.error('Failed to load meetings:', e);
  }
}

window.editMeeting = function(meetingId, title, date, time, venue, category, audience, agenda) {
  document.getElementById('meetId').value = meetingId;
  document.getElementById('meetTitle').value = title;
  document.getElementById('meetDate').value = date;
  document.getElementById('meetTime').value = time;
  document.getElementById('meetVenue').value = venue;
  if (document.getElementById('meetCategory')) document.getElementById('meetCategory').value = category;
  if (document.getElementById('meetTargetAudience')) document.getElementById('meetTargetAudience').value = audience;
  if (document.getElementById('meetAgenda')) document.getElementById('meetAgenda').value = agenda;
  document.querySelector('#scheduleMeetingDrawer .drawer-title').textContent = 'Reschedule Meeting';
  document.querySelector('#scheduleMeetingDrawer button[type="submit"]').textContent = 'Update Meeting & Broadcast Notification';
  openDrawer('scheduleMeetingDrawer');
};

async function submitScheduleMeeting(e) {
  e.preventDefault();
  const meetingId = parseInt(document.getElementById('meetId').value, 10);
  const isUpdate = meetingId > 0;
  const formData = new FormData();
  formData.append('action', isUpdate ? 'update_meeting' : 'schedule_meeting');
  if (isUpdate) formData.append('meeting_id', meetingId);
  formData.append('title', document.getElementById('meetTitle').value);
  formData.append('meeting_date', document.getElementById('meetDate').value);
  formData.append('meeting_time', document.getElementById('meetTime').value);
  formData.append('venue', document.getElementById('meetVenue').value);
  formData.append('category', document.getElementById('meetCategory').value);
  formData.append('target_audience', document.getElementById('meetTargetAudience').value);
  formData.append('agenda', document.getElementById('meetAgenda').value);

  try {
    const res = await fetch('ajax/faculty_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      document.getElementById('scheduleMeetingForm').reset();
      document.getElementById('meetId').value = '0';
      document.querySelector('#scheduleMeetingDrawer .drawer-title').textContent = 'Schedule New Meeting';
      document.querySelector('#scheduleMeetingDrawer button[type="submit"]').textContent = 'Schedule Meeting & Broadcast Notification';
      closeDrawer('scheduleMeetingDrawer');
      fetchScheduledMeetings();
    } else {
      alert(data.message || 'Failed to schedule meeting.');
    }
  } catch (err) {
    alert('Error scheduling meeting: ' + err.message);
  }
}

window.cancelMeeting = async function(meetingId, title) {
  if (!confirm(`Are you sure you want to cancel the meeting "${title}"? An urgent notification will be broadcasted to all participants.`)) {
    return;
  }

  const formData = new FormData();
  formData.append('action', 'cancel_meeting');
  formData.append('meeting_id', meetingId);

  try {
    const res = await fetch('ajax/faculty_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      fetchScheduledMeetings();
    } else {
      alert(data.message || 'Failed to cancel meeting.');
    }
  } catch (err) {
    alert('Error cancelling meeting: ' + err.message);
  }
};

function formatDate(dateStr) {
  if (!dateStr) return 'TBD';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

// Initialization and animation stats counter
loadNotificationsFromDB().then(() => {
  renderNotifications('notifications', currentUser.email);
});
loadEventsFromDB();
fetchScheduledMeetings();

// Real-time notification polling every 30s
setInterval(() => {
  loadNotificationsFromDB().then(() => {
    renderNotifications('notifications', currentUser.email);
  });
}, 30000);

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
    if(item.href&&(item.href.includes('index')||item.href.includes('faculty_dashboard')))return;
    e.preventDefault();
    document.querySelectorAll('.nav-item').forEach(i=>i.classList.remove('active'));
    item.classList.add('active');
  });
});
</script>
<script src="assets/js/landing.js"></script>
</body>
</html>
