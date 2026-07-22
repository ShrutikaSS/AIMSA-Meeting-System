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
<title>Student Member Dashboard — AIMSA Portal</title>
<meta name="description" content="Student Member portal dashboard for AIMSA — AI & ML Student Association.">
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

/* ── MEMBERSHIP CARD ── */
.membership-card{background:linear-gradient(135deg,var(--navy-950),var(--navy-700));border-radius:18px;padding:28px;color:var(--white);position:relative;overflow:hidden;margin-bottom:24px;border:1px solid var(--line);}
.membership-card::before{content:'';position:absolute;right:-60px;top:-60px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,var(--accent-glow),transparent 70%);}
.membership-card::after{content:'';position:absolute;left:-40px;bottom:-40px;width:150px;height:150px;border-radius:50%;background:radial-gradient(circle,rgba(127,176,255,.2),transparent 70%);}
.membership-inner{position:relative;z-index:1;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:20px;}
.membership-label{font-family:var(--ff-mono);font-size:.62rem;letter-spacing:.18em;text-transform:uppercase;color:var(--accent-soft);margin-bottom:8px;}
.membership-name{font-family:var(--ff-display);font-size:1.3rem;font-weight:700;margin-bottom:4px;}
.membership-id{font-family:var(--ff-mono);font-size:.75rem;letter-spacing:.08em;color:var(--muted);}
.membership-right{text-align:right;}
.membership-status{display:inline-flex;align-items:center;gap:6px;background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.3);color:#4ade80;padding:5px 12px;border-radius:999px;font-family:var(--ff-mono);font-size:.65rem;letter-spacing:.06em;text-transform:uppercase;margin-bottom:8px;}
.membership-status::before{content:'';width:6px;height:6px;border-radius:50%;background:#4ade80;animation:blink 2s infinite;}
@keyframes blink{0%,100%{opacity:1;}50%{opacity:.4;}}
.membership-expiry{font-family:var(--ff-mono);font-size:.68rem;color:var(--muted);}

/* ── STATS ── */
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

.dash-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:24px;margin-bottom:24px;}
.dash-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:24px;}
.card{background:var(--white);border-radius:var(--radius);border:1px solid var(--line-dark);padding:24px;}
.card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.card-title{font-family:var(--ff-display);font-size:1rem;font-weight:700;}
.card-action{font-family:var(--ff-mono);font-size:.65rem;letter-spacing:.08em;color:var(--accent);text-transform:uppercase;cursor:pointer;padding:5px 10px;border-radius:999px;border:1px solid rgba(62,139,255,.3);transition:.2s ease;}
.card-action:hover{background:var(--accent);color:var(--white);}
.badge{font-family:var(--ff-mono);font-size:.6rem;letter-spacing:.06em;padding:4px 10px;border-radius:999px;text-transform:uppercase;display:inline-block;}
.badge-green{background:rgba(34,197,94,.1);color:#16a34a;border:1px solid rgba(34,197,94,.2);}
.badge-blue{background:rgba(62,139,255,.1);color:var(--accent);border:1px solid rgba(62,139,255,.2);}
.badge-orange{background:rgba(249,115,22,.1);color:#ea580c;border:1px solid rgba(249,115,22,.2);}
.badge-purple{background:rgba(168,85,247,.1);color:#9333ea;border:1px solid rgba(168,85,247,.2);}
.list-item{display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid rgba(8,23,51,.05);}
.list-item:last-child{border-bottom:none;}
.list-dot{width:9px;height:9px;border-radius:50%;background:var(--accent);flex-shrink:0;box-shadow:0 0 0 3px rgba(62,139,255,.18);}
.list-text{flex:1;}
.list-text b{font-size:.88rem;display:block;margin-bottom:2px;}
.list-text span{font-size:.76rem;color:var(--muted-dark);font-family:var(--ff-mono);}

/* ── CALENDAR ── */
.calendar-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:16px;}
.cal-header{font-family:var(--ff-mono);font-size:.58rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted-dark);text-align:center;padding:4px 0;}
.cal-day{height:32px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.76rem;font-weight:500;cursor:pointer;transition:.15s ease;}
.cal-day:hover{background:var(--paper-dim);}
.cal-event{background:linear-gradient(135deg,rgba(62,139,255,.2),rgba(62,139,255,.08));color:var(--accent);border:1px solid rgba(62,139,255,.25);}
.cal-today{background:var(--navy-950);color:var(--white);font-weight:700;}
.cal-empty{opacity:0;pointer-events:none;}

/* ── ACHIEVEMENTS ── */
.ach-stack{display:flex;flex-direction:column;gap:10px;}
.achievement-badge{display:flex;align-items:center;gap:14px;padding:14px;border-radius:12px;border:1px solid var(--line-dark);background:var(--white);transition:.2s ease;}
.achievement-badge:hover{border-color:var(--accent);background:var(--paper);transform:translateX(4px);}
.ach-icon{width:44px;height:44px;border-radius:12px;flex-shrink:0;background:linear-gradient(135deg,var(--navy-950),var(--navy-800));display:flex;align-items:center;justify-content:center;transition:transform .3s cubic-bezier(.34,1.56,.64,1);}
.achievement-badge:hover .ach-icon{transform:scale(1.12) rotate(-6deg);background:linear-gradient(135deg,var(--accent),#2563eb);}
.ach-icon svg{width:20px;height:20px;stroke:var(--accent-soft);fill:none;stroke-width:1.7;}
.ach-info b{font-size:.88rem;display:block;margin-bottom:2px;}
.ach-info span{font-family:var(--ff-mono);font-size:.65rem;color:var(--muted-dark);}

/* ── CERT CARDS ── */
.cert-grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;}
.cert-card{background:linear-gradient(135deg,var(--navy-950),var(--navy-800));border-radius:14px;padding:18px;color:var(--white);border:1px solid var(--line);position:relative;overflow:hidden;transition:transform .2s ease,box-shadow .3s ease;}
.cert-card:hover{transform:translateY(-4px);box-shadow:0 20px 40px -14px var(--accent-glow);}
.cert-card::after{content:'';position:absolute;right:-40px;top:-40px;width:120px;height:120px;border-radius:50%;background:radial-gradient(circle,var(--accent-glow),transparent 70%);}
.cert-card h4{font-size:.88rem;margin-bottom:4px;font-family:var(--ff-display);position:relative;z-index:1;}
.cert-card span{font-family:var(--ff-mono);font-size:.63rem;color:var(--muted);position:relative;z-index:1;}
.cert-download{margin-top:12px;font-family:var(--ff-mono);font-size:.6rem;letter-spacing:.06em;text-transform:uppercase;color:var(--accent-soft);cursor:pointer;display:inline-flex;align-items:center;gap:6px;position:relative;z-index:1;}
.cert-download:hover{color:var(--white);}

/* ── REGISTERED EVENTS TABLE ── */
.data-table{width:100%;border-collapse:collapse;}
.data-table th{font-family:var(--ff-mono);font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;color:var(--muted-dark);padding:8px 12px;text-align:left;border-bottom:1px solid var(--line-dark);}
.data-table td{padding:11px 12px;font-size:.85rem;border-bottom:1px solid rgba(8,23,51,.05);}
.data-table tr:last-child td{border-bottom:none;}
.data-table tr:hover td{background:var(--paper);}

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
@media(max-width:768px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}.sidebar-overlay.open{display:block;}.main{margin-left:0;}.hamburger-btn{display:flex;}.content{padding:20px;}.stats-grid{grid-template-columns:1fr 1fr;}.dash-grid-3{grid-template-columns:1fr;}.cert-grid-2{grid-template-columns:1fr;}}
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
    <div class="brand-info"><b>AIMSA Portal</b><span>Student Access</span></div>
  </div>
  <div class="sidebar-role">
    <div class="role-avatar"><div class="in">SM</div></div>
    <div class="role-info"><b>Arjun Patil</b><span>Student Member</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <a class="nav-item active" href="student_dashboard.html">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard
    </a>
    <a class="nav-item" href="#membership">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>Membership Status
    </a>
    <div class="nav-section-label">Events</div>
    <a class="nav-item" href="#registered">
      <svg class="nav-icon" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Registered Events
    </a>
    <a class="nav-item" href="#upcoming">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Upcoming Events
    </a>
    <a class="nav-item" href="#calendar">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><polyline points="9 14 11 16 15 12"/></svg>Event Calendar
    </a>
    <div class="nav-section-label">Achievements</div>
    <a class="nav-item" href="#certificates">
      <svg class="nav-icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>Certificates
    </a>
    <a class="nav-item" href="#achievements">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg>Achievements
    </a>
    <div class="nav-section-label">Communication</div>
    <a class="nav-item" href="#notifications">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>Notifications
      <span class="nav-badge">5</span>
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
        <div class="breadcrumb" style="font-size:0.68rem; color:var(--muted-dark);">AI &amp; ML Student Association</div>
      </div>
    </div>

    <!-- Center Search Bar -->
    <div class="header-search-bar">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted-dark)" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="headerSearchInput" placeholder="Search events, tasks...">
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
            <span style="font-size:0.78rem; font-weight:600; color:var(--navy-950);" id="headerUserName">Arjun Patil</span>
            <span style="font-size:0.65rem; color:var(--muted-dark);" id="headerUserRole">Student Member</span>
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
    <div class="section-eyebrow">Student Member</div>
    <div class="content-title">Hey, Arjun! 👋</div>
    <div class="content-sub">Your AIMSA journey at a glance — July 21, 2026</div>

    <!-- Membership Card -->
    <div class="membership-card" id="membership">
      <div class="membership-inner">
        <div>
          <div class="membership-label">AIMSA Membership Card</div>
          <div class="membership-name">Arjun Patil</div>
          <div class="membership-id">Member ID · AIMSA-2024-0127 · AIML-B (3rd Year)</div>
        </div>
        <div class="membership-right">
          <div class="membership-status">● Active Member</div>
          <div class="membership-expiry">Valid until: May 31, 2027</div>
        </div>
      </div>
    </div>

    <!-- STATS — Events Attended, Upcoming Events, Certificates, Achievements -->
    <div class="stats-grid">
      <div class="stat-card" id="registered">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
        <span class="stat-val">7</span>
        <div class="stat-label">Events Attended</div>
        <span class="stat-delta up">↑ 2 this semester</span>
      </div>
      <div class="stat-card" id="upcoming">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><polyline points="9 14 11 16 15 12"/></svg></div>
        <span class="stat-val">3</span>
        <div class="stat-label">Upcoming Events</div>
        <span class="stat-delta up">↑ Registered</span>
      </div>
      <div class="stat-card" id="certificates">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
        <span class="stat-val">2</span>
        <div class="stat-label">Certificates Earned</div>
        <span class="stat-delta up">↑ 1 new this month</span>
      </div>
      <div class="stat-card" id="achievements">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg></div>
        <span class="stat-val">4</span>
        <div class="stat-label">Achievements</div>
        <span class="stat-delta up">↑ Keep going!</span>
      </div>
    </div>

    <!-- MAIN GRID -->
    <div class="dash-grid">
      <!-- Event Calendar -->
      <div class="card" id="calendar">
        <div class="card-head"><div class="card-title">Event Calendar — July 2026</div><span class="card-action">Register</span></div>
        <div class="calendar-grid">
          <div class="cal-header">MON</div><div class="cal-header">TUE</div><div class="cal-header">WED</div>
          <div class="cal-header">THU</div><div class="cal-header">FRI</div><div class="cal-header">SAT</div><div class="cal-header">SUN</div>
          <div class="cal-day cal-empty"></div><div class="cal-day cal-empty"></div><div class="cal-day cal-empty"></div>
          <div class="cal-day cal-empty"></div><div class="cal-day">4</div><div class="cal-day">5</div><div class="cal-day">6</div>
          <div class="cal-day">7</div><div class="cal-day">8</div><div class="cal-day">9</div>
          <div class="cal-day">10</div><div class="cal-day">11</div><div class="cal-day">12</div><div class="cal-day">13</div>
          <div class="cal-day">14</div><div class="cal-day">15</div><div class="cal-day">16</div>
          <div class="cal-day">17</div><div class="cal-day">18</div><div class="cal-day">19</div><div class="cal-day">20</div>
          <div class="cal-day cal-today" title="Today">21</div><div class="cal-day">22</div><div class="cal-day">23</div>
          <div class="cal-day">24</div><div class="cal-day">25</div><div class="cal-day">26</div><div class="cal-day">27</div>
          <div class="cal-day cal-event" title="Tech Symposium 2026">28</div><div class="cal-day">29</div><div class="cal-day">30</div>
          <div class="cal-day">31</div>
        </div>
        <!-- Registered Events list -->
        <div style="margin-top:12px;">
          <div style="font-family:var(--ff-mono);font-size:.65rem;color:var(--accent);margin-bottom:10px;text-transform:uppercase;letter-spacing:.1em;">My Registered Events</div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Tech Symposium 2026</b><span>Jul 28 · Main Auditorium · <span class="badge badge-blue">Registered</span></span></div></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>AI Workshop Series</b><span>Aug 3 · Lab 402 · <span class="badge badge-orange">Register Now</span></span></div></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Hackathon 2026</b><span>Aug 15 · Online + Campus · <span class="badge badge-blue">Registered</span></span></div></div>
        </div>
      </div>

      <!-- Right Column -->
      <div style="display:flex;flex-direction:column;gap:24px;">
        <!-- Upcoming Events -->
        <div class="card">
          <div class="card-head"><div class="card-title">Upcoming Events</div><span class="card-action">Explore All</span></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>AI Workshop Series</b><span>Aug 3 · 67 registered · 33 seats left</span></div></div>
          <div class="list-item"><div class="list-dot" style="background:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,.18);"></div><div class="list-text"><b>Guest Lecture: ML Healthcare</b><span>Aug 22 · Open registration</span></div></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Cultural Night 2026</b><span>Aug 22 · 203 registered</span></div></div>
          <button class="btn btn-primary" style="width:100%;margin-top:16px;justify-content:center;">Browse All Events →</button>
        </div>

        <!-- Notifications -->
        <div class="card" id="notifications">
          <div class="card-head"><div class="card-title">Notifications</div><span class="card-action">Mark Read</span></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Tech Symposium registration confirmed</b><span>See you on Jul 28 · 1 hr ago</span></div></div>
          <div class="list-item"><div class="list-dot" style="background:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,.18);"></div><div class="list-text"><b>New certificate available</b><span>AI Workshop Q4 2025 · Yesterday</span></div></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Membership renewed successfully</b><span>Valid until May 2027 · 3 days ago</span></div></div>
          <div class="list-item"><div class="list-dot" style="background:#f97316;box-shadow:0 0 0 3px rgba(249,115,22,.18);"></div><div class="list-text"><b>Hackathon team registration deadline</b><span>Register your team by Aug 10</span></div></div>
          <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Achievement unlocked: Active Participant</b><span>Attended 5+ events · 2 weeks ago</span></div></div>
        </div>
      </div>
    </div>

    <!-- BOTTOM GRID -->
    <div class="dash-grid-3">
      <!-- Certificates -->
      <div class="card" style="grid-column:span 2;">
        <div class="card-head"><div class="card-title">My Certificates</div><span class="card-action">View All</span></div>
        <div class="cert-grid-2">
          <div class="cert-card">
            <h4>Tech Symposium 2025</h4>
            <span>Participant · Dec 2025</span>
            <div class="cert-download">↓ Download PDF</div>
          </div>
          <div class="cert-card">
            <h4>AI Workshop Series Q4</h4>
            <span>Attendee · Nov 2025</span>
            <div class="cert-download">↓ Download PDF</div>
          </div>
        </div>
        <!-- Registered Events Table -->
        <div style="margin-top:20px;">
          <div style="font-family:var(--ff-display);font-size:.88rem;font-weight:700;margin-bottom:12px;">All Registered Events</div>
          <table class="data-table">
            <thead><tr><th>Event</th><th>Date</th><th>Role</th><th>Status</th></tr></thead>
            <tbody>
              <tr><td><b>Tech Symposium 2026</b></td><td>Jul 28</td><td>Attendee</td><td><span class="badge badge-blue">Registered</span></td></tr>
              <tr><td><b>AI Workshop Series</b></td><td>Aug 3</td><td>Attendee</td><td><span class="badge badge-orange">Pending</span></td></tr>
              <tr><td><b>Hackathon 2026</b></td><td>Aug 15</td><td>Participant</td><td><span class="badge badge-blue">Registered</span></td></tr>
              <tr><td><b>Tech Symposium 2025</b></td><td>Dec 2025</td><td>Attendee</td><td><span class="badge badge-green">Completed</span></td></tr>
              <tr><td><b>AI Workshop Q4 2025</b></td><td>Nov 2025</td><td>Attendee</td><td><span class="badge badge-green">Completed</span></td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Achievements -->
      <div class="card">
        <div class="card-head"><div class="card-title">Achievements</div><span class="card-action">View All</span></div>
        <div class="ach-stack">
          <div class="achievement-badge">
            <div class="ach-icon"><svg viewBox="0 0 24 24"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg></div>
            <div class="ach-info"><b>Active Participant</b><span>Attended 5+ events</span></div>
          </div>
          <div class="achievement-badge">
            <div class="ach-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
            <div class="ach-info"><b>Workshop Graduate</b><span>Completed AI Workshop</span></div>
          </div>
          <div class="achievement-badge">
            <div class="ach-icon"><svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg></div>
            <div class="ach-info"><b>Loyal Member</b><span>1 year AIMSA membership</span></div>
          </div>
          <div class="achievement-badge">
            <div class="ach-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
            <div class="ach-info"><b>Hackathon Finalist</b><span>Top 10 in Hackathon 2025</span></div>
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

<!-- ── PROFILE DRAWER ── -->
<div class="drawer" id="profileDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Update Profile</div>
    <button class="drawer-close" onclick="closeDrawer('profileDrawer')">&times;</button>
  </div>
  <div style="display:flex; flex-direction:column; align-items:center; gap:12px; margin-bottom:20px;">
    <div style="width:100px; height:100px; border-radius:50%; background:var(--paper-dim); border:2px solid var(--accent); overflow:hidden; display:flex; align-items:center; justify-content:center;" id="photoPreviewContainer">
      <span style="font-size:2rem; color:var(--muted-dark);" id="avatarInitials">SM</span>
    </div>
    <div class="form-group" style="width:100%;">
      <label>Upload Photograph</label>
      <input type="file" id="profilePhotoInput" accept="image/*" style="padding:4px;">
    </div>
  </div>
  <div class="form-group">
    <label>Full Name</label>
    <input type="text" id="profileName">
  </div>
  <div class="form-group">
    <label>College Email ID</label>
    <input type="email" id="profileEmail" readonly style="background:var(--paper-dim);">
  </div>
  <div class="form-group">
    <label>Student ID</label>
    <input type="text" id="profileStudentID">
  </div>
  <div class="form-group">
    <label>Contact Number</label>
    <input type="text" id="profilePhone" placeholder="+91 XXXXX XXXXX">
  </div>
  <div class="form-group">
    <label>Academic Year</label>
    <select id="profileYear">
      <option value="1st Year">1st Year</option>
      <option value="2nd Year">2nd Year</option>
      <option value="3rd Year" selected>3rd Year</option>
      <option value="4th Year">4th Year</option>
    </select>
  </div>
  <div class="form-group">
    <label>Department / Branch</label>
    <select id="profileBranch">
      <option value="AI & ML">AI & ML</option>
      <option value="Computer Science">Computer Science</option>
      <option value="Data Science">Data Science</option>
    </select>
  </div>
  <button class="btn btn-primary" style="margin-top:10px; width:100%;" id="saveProfileBtn">Save Profile Changes</button>
</div>

<!-- ── ACHIEVEMENT UPLOAD DRAWER ── -->
<div class="drawer" id="achievementDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Submit Achievement</div>
    <button class="drawer-close" onclick="closeDrawer('achievementDrawer')">&times;</button>
  </div>
  <div class="form-group">
    <label>Achievement Category</label>
    <select id="achCategory">
      <option value="Competition Certificate">Competition Certificate</option>
      <option value="Internship Certificate">Internship Certificate</option>
      <option value="Research Publication">Research Publication</option>
      <option value="Hackathon Achievement">Hackathon Achievement</option>
    </select>
  </div>
  <div class="form-group">
    <label>Title</label>
    <input type="text" id="achTitle" placeholder="e.g. 1st Place Smart India Hackathon">
  </div>
  <div class="form-group">
    <label>Description / Details</label>
    <textarea id="achDescription" rows="4" placeholder="Briefly describe your achievement, organization, and date..."></textarea>
  </div>
  <div class="form-group">
    <label>Attach Certificate (PDF / Image)</label>
    <input type="file" id="achFile" accept="image/*,.pdf" style="padding:4px;">
  </div>
  <button class="btn btn-primary" style="margin-top:10px; width:100%;" id="submitAchievementBtn">Submit Achievement</button>
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
  email: 'student@zealeducation.com',
  name: 'Student Member',
  role: 'Student Member',
  studentId: 'AIMSA-2026-0101',
  membershipStatus: 'Active',
  membershipRenewed: true,
  photograph: '',
  phone: '',
  year: '3rd Year',
  branch: 'AI & ML'
};

// Render user profile details
function renderProfile() {
  // Update name in headers/cards
  document.querySelector('.content-title').innerHTML = `Hey, ${currentUser.name.split(' ')[0]}! 👋`;
  document.querySelector('.role-info b').textContent = currentUser.name;
  document.querySelector('.membership-name').textContent = currentUser.name;
  
  const initials = currentUser.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
  document.querySelector('.role-avatar .in').textContent = initials;
  document.getElementById('avatarInitials').textContent = initials;

  // Membership status card render
  const cardID = document.querySelector('.membership-id');
  cardID.textContent = `Member ID · ${currentUser.studentId || 'AIMSA-PENDING'} · ${currentUser.branch || 'AI & ML'} (${currentUser.year || '3rd Year'})`;

  const statusBadge = document.querySelector('.membership-status');
  statusBadge.textContent = `● ${currentUser.membershipStatus || 'Pending'}`;
  
  if (currentUser.membershipStatus === 'Active') {
    statusBadge.style.color = '#4ade80';
    statusBadge.style.borderColor = 'rgba(34,197,94,.3)';
    statusBadge.style.background = 'rgba(34,197,94,.15)';
  } else if (currentUser.membershipStatus === 'Pending') {
    statusBadge.style.color = '#fbbf24';
    statusBadge.style.borderColor = 'rgba(251,191,36,.3)';
    statusBadge.style.background = 'rgba(251,191,36,.15)';
  } else {
    statusBadge.style.color = '#f87171';
    statusBadge.style.borderColor = 'rgba(239,110,110,.3)';
    statusBadge.style.background = 'rgba(239,110,110,.15)';
  }

  // If photo exists, render preview
  if (currentUser.photograph) {
    const photoURL = currentUser.photograph;
    document.getElementById('photoPreviewContainer').innerHTML = `<img src="${photoURL}" style="width:100%; height:100%; object-fit:cover;">`;
  }
}

// Handle photograph upload preview
document.getElementById('profilePhotoInput').addEventListener('change', (e) => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(evt) {
      currentUser.photograph = evt.target.result;
      document.getElementById('photoPreviewContainer').innerHTML = `<img src="${evt.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
    };
    reader.readAsDataURL(file);
  }
});

// Trigger Profile Drawer
document.querySelector('a[href="#"]').addEventListener('click', (e) => {
  e.preventDefault();
  // Populate fields
  document.getElementById('profileName').value = currentUser.name;
  document.getElementById('profileEmail').value = currentUser.email;
  document.getElementById('profileStudentID').value = currentUser.studentId || '';
  document.getElementById('profilePhone').value = currentUser.phone || '';
  document.getElementById('profileYear').value = currentUser.year || '3rd Year';
  document.getElementById('profileBranch').value = currentUser.branch || 'AI & ML';
  openDrawer('profileDrawer');
});

// Save profile updates
document.getElementById('saveProfileBtn').addEventListener('click', () => {
  currentUser.name = document.getElementById('profileName').value.trim();
  currentUser.studentId = document.getElementById('profileStudentID').value.trim();
  currentUser.phone = document.getElementById('profilePhone').value.trim();
  currentUser.year = document.getElementById('profileYear').value;
  currentUser.branch = document.getElementById('profileBranch').value;

  sessionStorage.setItem('current_user', JSON.stringify(currentUser));
  
  // Sync to localStorage aimsa_users
  const users = JSON.parse(localStorage.getItem('aimsa_users')) || [];
  const idx = users.findIndex(u => u.email.toLowerCase() === currentUser.email.toLowerCase());
  if (idx !== -1) {
    users[idx] = {...users[idx], ...currentUser};
    localStorage.setItem('aimsa_users', JSON.stringify(users));
  }

  addNotification('Profile Updated', 'Successfully updated profile details & settings.', 'green', currentUser.email);
  renderProfile();
  closeDrawer('profileDrawer');
  alert('Profile updated successfully!');
});

// Membership Renewal Button
document.querySelector('.membership-card').addEventListener('click', (e) => {
  // If clicked inside membership right (renew button trigger)
  if (currentUser.membershipStatus === 'Expired' || !currentUser.membershipRenewed) {
    const confirmRenew = confirm('Would you like to renew your AIMSA student membership for the upcoming academic year 2026-27?');
    if (confirmRenew) {
      currentUser.membershipStatus = 'Active';
      currentUser.membershipRenewed = true;
      sessionStorage.setItem('current_user', JSON.stringify(currentUser));
      
      const users = JSON.parse(localStorage.getItem('aimsa_users')) || [];
      const idx = users.findIndex(u => u.email.toLowerCase() === currentUser.email.toLowerCase());
      if (idx !== -1) {
        users[idx].membershipStatus = 'Active';
        users[idx].membershipRenewed = true;
        localStorage.setItem('aimsa_users', JSON.stringify(users));
      }
      addNotification('Membership Approved', 'Your membership renewal for 2026-27 has been processed.', 'green', currentUser.email);
      renderProfile();
      alert('Membership renewed successfully!');
    }
  }
});

// Setup dynamic events list registration / cancel registration
let regEvents = JSON.parse(localStorage.getItem('registered_events_list')) || [
  'Tech Symposium 2026', 'Hackathon 2026'
];

function renderEvents() {
  const container = document.querySelector('#calendar .list-item').parentNode;
  container.innerHTML = `<div style="font-family:var(--ff-mono);font-size:.65rem;color:var(--accent);margin-bottom:10px;text-transform:uppercase;letter-spacing:.1em;">My Registered Events</div>`;

  // Render registered events inside left column
  if (regEvents.length === 0) {
    container.innerHTML += `<p style="font-size:0.8rem; color:var(--muted-dark); padding:10px 0;">No registered events yet.</p>`;
  } else {
    regEvents.forEach(evt => {
      container.innerHTML += `
        <div class="list-item">
          <div class="list-dot"></div>
          <div class="list-text">
            <b>${evt}</b>
            <span>Scheduled · <span class="badge badge-blue">Registered</span> <span style="color:#ef4444; cursor:pointer; margin-left:12px; font-weight:600;" onclick="cancelEventReg('${evt}')">Cancel</span></span>
          </div>
        </div>`;
    });
  }

  // Update register status table
  const tbody = document.querySelector('.data-table tbody');
  tbody.innerHTML = '';
  const allEvents = [
    {name: 'Tech Symposium 2026', date: 'Jul 28', type: 'Attendee', dl: '2026-07-27'},
    {name: 'AI Workshop Series', date: 'Aug 3', type: 'Attendee', dl: '2026-08-02'},
    {name: 'Hackathon 2026', date: 'Aug 15', type: 'Participant', dl: '2026-08-12'}
  ];

  allEvents.forEach(evt => {
    const isReg = regEvents.includes(evt.name);
    tbody.innerHTML += `
      <tr>
        <td><b>${evt.name}</b></td>
        <td>${evt.date}</td>
        <td>${evt.type}</td>
        <td>
          ${isReg ? `<span class="badge badge-blue">Registered</span>` : `<button class="btn btn-ghost" style="padding:4px 10px; font-size:0.75rem;" onclick="registerForEvent('${evt.name}')">Register</button>`}
        </td>
      </tr>`;
  });
  
  // Update stat card upcoming count
  document.querySelector('#upcoming .stat-val').textContent = regEvents.length;
}

window.registerForEvent = function(name) {
  if (!regEvents.includes(name)) {
    regEvents.push(name);
    localStorage.setItem('registered_events_list', JSON.stringify(regEvents));
    addNotification('Registration Successful', `You successfully registered for event: ${name}.`, 'green', currentUser.email);
    renderEvents();
    alert(`Successfully registered for ${name}!`);
  }
};

window.cancelEventReg = function(name) {
  const confirmCancel = confirm(`Are you sure you want to cancel your registration for ${name}?`);
  if (confirmCancel) {
    regEvents = regEvents.filter(e => e !== name);
    localStorage.setItem('registered_events_list', JSON.stringify(regEvents));
    addNotification('Registration Cancelled', `You cancelled registration for event: ${name}.`, 'red', currentUser.email);
    renderEvents();
  }
};

// Handle Achievement Upload Drawer trigger
document.querySelector('a[href="#achievements"]').addEventListener('click', (e) => {
  e.preventDefault();
  openDrawer('achievementDrawer');
});

document.getElementById('submitAchievementBtn').addEventListener('click', () => {
  const cat = document.getElementById('achCategory').value;
  const title = document.getElementById('achTitle').value.trim();
  const desc = document.getElementById('achDescription').value.trim();

  if (!title || !desc) {
    alert('Please fill out the achievement title and details.');
    return;
  }

  // Push achievement into local storage list
  const achievements = JSON.parse(localStorage.getItem('aimsa_achievements')) || [];
  achievements.push({
    student: currentUser.name,
    email: currentUser.email,
    category: cat,
    title: title,
    description: desc,
    status: 'Pending',
    date: new Date().toLocaleDateString('en-US', {month: 'short', year: 'numeric'})
  });
  localStorage.setItem('aimsa_achievements', JSON.stringify(achievements));

  // Dynamically append new achievement badge
  const stack = document.querySelector('.ach-stack');
  const badgeHTML = `
    <div class="achievement-badge">
      <div class="ach-icon"><svg viewBox="0 0 24 24"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg></div>
      <div class="ach-info"><b>${title}</b><span>${cat} · Pending Approval</span></div>
    </div>`;
  stack.insertAdjacentHTML('afterbegin', badgeHTML);

  // Update achievements stats
  const statVal = document.querySelector('#achievements .stat-val');
  statVal.textContent = parseInt(statVal.textContent) + 1;

  addNotification('Achievement Pending Approval', `Achievement nomination "${title}" submitted for review.`, 'yellow', currentUser.email);
  closeDrawer('achievementDrawer');
  alert('Achievement successfully submitted for faculty approval!');
});

// Setup sidebar nav trigger to open profile/membership
document.querySelectorAll('.nav-item').forEach(item=>{
  item.addEventListener('click',(e)=>{
    if(item.href&&(item.href.includes('index')||item.href.includes('student_dashboard')))return;
    e.preventDefault();
    document.querySelectorAll('.nav-item').forEach(i=>i.classList.remove('active'));
    item.classList.add('active');
  });
});

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

// Page Initialization
renderProfile();
renderEvents();
renderNotifications('notifications', currentUser.email);

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

document.querySelectorAll('.stat-val').forEach(el=>{
  const raw=el.textContent.replace(/,/g,'');
  const target=parseInt(raw.replace(/\D/g,''));
  if(isNaN(target))return;
  const suffix=el.textContent.replace(/[\d,]/g,'');
  let current=0;const step=Math.ceil(target/50)||1;
  const timer=setInterval(()=>{current=Math.min(current+step,target);el.textContent=current.toLocaleString()+suffix;if(current>=target)clearInterval(timer);},25);
});

// Certificate generator simulation
document.querySelectorAll('.cert-download').forEach(btn=>btn.addEventListener('click',(e)=>{
  const certTitle = e.target.parentNode.querySelector('h4').textContent;
  alert(`Generating secure PDF for your ${certTitle}... Success! Your certificate download will start shortly.`);
}));

document.querySelectorAll('.cal-day:not(.cal-header):not(.cal-empty)').forEach(day=>{
  day.addEventListener('click',()=>{
    document.querySelectorAll('.cal-day:not(.cal-header)').forEach(d=>{
      if(!d.classList.contains('cal-empty')){d.classList.remove('cal-today');}
    });
    day.classList.add('cal-today');
  });
});
</script>
</body>
</html>
