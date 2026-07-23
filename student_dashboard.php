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
.role-avatar{width:40px;height:40px;border-radius:50%;background:conic-gradient(from 180deg,var(--accent),var(--navy-700),var(--accent));padding:2.5px;flex-shrink:0;overflow:hidden;}
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
.membership-card{background:linear-gradient(135deg,var(--navy-950),var(--navy-700));border-radius:18px;padding:28px;color:var(--white);position:relative;overflow:hidden;margin-bottom:24px;border:1px solid var(--line);cursor:pointer;transition:transform 0.25s ease, box-shadow 0.25s ease;}
.membership-card:hover{transform:translateY(-3px); box-shadow:0 16px 35px -10px var(--accent-glow);}
.membership-card::before{content:'';position:absolute;right:-60px;top:-60px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,var(--accent-glow),transparent 70%);}
.membership-card::after{content:'';position:absolute;left:-40px;bottom:-40px;width:150px;height:150px;border-radius:50%;background:radial-gradient(circle,rgba(127,176,255,.2),transparent 70%);}
.membership-inner{position:relative;z-index:1;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:20px;}
.membership-label{font-family:var(--ff-mono);font-size:.62rem;letter-spacing:.18em;text-transform:uppercase;color:var(--accent-soft);margin-bottom:8px;}
.membership-name{font-family:var(--ff-display);font-size:1.3rem;font-weight:700;margin-bottom:4px;}
.membership-id{font-family:var(--ff-mono);font-size:.75rem;letter-spacing:.08em;color:var(--muted);}
.membership-right{text-align:right;}
.membership-status{display:inline-flex;align-items:center;gap:6px;background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.3);color:#4ade80;padding:5px 12px;border-radius:999px;font-family:var(--ff-mono);font-size:.65rem;letter-spacing:.06em;text-transform:uppercase;margin-bottom:8px;}
.membership-status::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor;animation:blink 2s infinite;}
@keyframes blink{0%,100%{opacity:1;}50%{opacity:.4;}}
.membership-expiry{font-family:var(--ff-mono);font-size:.68rem;color:var(--muted);}

/* ── STATS ── */
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
.cal-day{height:32px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.76rem;font-weight:500;cursor:pointer;transition:.15s ease;position:relative;}
.cal-day:hover{background:var(--paper-dim);}
.cal-event{background:linear-gradient(135deg,rgba(62,139,255,.25),rgba(62,139,255,.1));color:var(--accent);border:1px solid rgba(62,139,255,.3);font-weight:700;}
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

.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(5,13,26,.55);backdrop-filter:blur(4px);z-index:400;}
.sidebar-overlay.open{display:block !important;}
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

/* Drawer Panel styles (Fixed root level) */
.drawer{
  position:fixed; top:0; right:-600px; width:min(500px, 92vw); height:100vh;
  background:var(--white); border-left:1px solid var(--line-dark); z-index:500;
  transition:right 0.35s cubic-bezier(0.16, 1, 0.3, 1); padding:28px 24px;
  display:flex; flex-direction:column; gap:16px; overflow-y:auto;
  box-shadow: -10px 0 40px rgba(0,0,0,0.2);
}
.drawer.open{right:0 !important;}
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
<body id="top">
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo" style="width:42px; height:42px; border-radius:50%; background:var(--white); display:flex; align-items:center; justify-content:center; overflow:hidden; border:2px solid rgba(255,255,255,.3); flex-shrink:0;">
      <img src="images/aimsa_logo.jpg" alt="AIMSA Logo" style="width:100%; height:100%; object-fit:cover;">
    </div>
    <div class="brand-info"><b>AIMSA Portal</b><span>Student Access</span></div>
  </div>
  <div class="sidebar-role">
    <div class="role-avatar" id="sidebarAvatarContainer"><div class="in" id="sidebarInitials">SM</div></div>
    <div class="role-info"><b id="sidebarUserName">Arjun Patil</b><span>Student Member</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <a class="nav-item active" href="#top" id="navDashboard" onclick="window.scrollTo({top:0, behavior:'smooth'}); return false;">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard
    </a>
    <a class="nav-item" href="#membership" id="navMembership" onclick="openDrawer('membershipDrawer'); return false;">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>Membership Status
    </a>
    <div class="nav-section-label">Events</div>
    <a class="nav-item" href="#registered" id="navRegistered" onclick="scrollToSection('registered'); return false;">
      <svg class="nav-icon" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Registered Events
    </a>
    <a class="nav-item" href="#upcoming" id="navUpcoming" onclick="openDrawer('allEventsDrawer'); return false;">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Upcoming Events
    </a>
    <a class="nav-item" href="event_calendar.php" target="_blank" id="navCalendar">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><polyline points="9 14 11 16 15 12"/></svg>Event Calendar ↗
    </a>
    <div class="nav-section-label">Achievements</div>
    <a class="nav-item" href="#certificates" id="navCertificates" onclick="scrollToSection('certificates'); return false;">
      <svg class="nav-icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>Certificates
    </a>
    <a class="nav-item" href="#achievements" id="navAchievements" onclick="scrollToSection('achievements'); return false;">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg>Achievements
    </a>
    <div class="nav-section-label">Communication</div>
    <a class="nav-item" href="#notifications" id="navNotifications" onclick="scrollToSection('notifications'); return false;">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>Notifications
      <span class="nav-badge" id="sidebarNotifBadge">0</span>
    </a>
    <div class="nav-section-label">Account</div>
    <a class="nav-item" href="#profile" id="navProfile" onclick="openDrawer('profileDrawer'); return false;">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>Profile
    </a>
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
        <div class="breadcrumb" style="font-size:0.68rem; color:var(--muted-dark);">AI &amp; ML Student Association</div>
      </div>
    </div>

    <!-- Center Search Bar -->
    <div class="header-search-bar">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted-dark)" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="headerSearchInput" placeholder="Search events, certificates, tasks...">
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
          <div style="width:32px; height:32px; border-radius:50%; background:var(--accent); color:var(--white); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem; overflow:hidden;" id="headerUserAvatar">AP</div>
          <div style="display:flex; flex-direction:column; text-align:left;">
            <span style="font-size:0.78rem; font-weight:600; color:var(--navy-950);" id="headerUserName">Arjun Patil</span>
            <span style="font-size:0.65rem; color:var(--muted-dark);" id="headerUserRole">Student Member</span>
          </div>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div id="profileDropdown" style="display:none; position:absolute; right:0; top:42px; background:var(--white); border:1px solid var(--line-dark); border-radius:12px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1); width:180px; z-index:150; padding:6px 0;">
          <a href="#" onclick="openDrawer('profileDrawer'); toggleProfileDropdown(); return false;" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:var(--navy-950); text-decoration:none; font-weight:500;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
            <span>Update Profile</span>
          </a>
          <a href="#" onclick="openDrawer('membershipDrawer'); toggleProfileDropdown(); return false;" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:var(--navy-950); text-decoration:none; font-weight:500;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><line x1="12" y1="12" x2="12" y2="16"/></svg>
            <span>Membership Card</span>
          </a>
          <a href="#" onclick="openDrawer('changePasswordDrawer'); toggleProfileDropdown(); return false;" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:var(--navy-950); text-decoration:none; font-weight:500;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span>Change Password</span>
          </a>
          <div style="border-top:1px solid var(--line-dark); margin:4px 0;"></div>
          <a href="index.php" onclick="sessionStorage.removeItem('current_user');" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:#ef4444; text-decoration:none; font-weight:600;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            <span>Logout</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="section-eyebrow">Student Member</div>
    <div class="content-title" id="greetingTitle">Hey, Student! 👋</div>
    <div class="content-sub">Your AIMSA journey at a glance — <span class="liveDateText"><?php echo date('F j, Y'); ?></span></div>

    <!-- Membership Card -->
    <div class="membership-card" id="membership" onclick="openDrawer('membershipDrawer')" title="Click to view full digital pass & renew membership">
      <div class="membership-inner">
        <div>
          <div class="membership-label">AIMSA Membership Pass (Click for Pass View)</div>
          <div class="membership-name" id="cardStudentName">Arjun Patil</div>
          <div class="membership-id" id="cardStudentID">Member ID · 125UAM1005 · AI & ML (3rd Year)</div>
        </div>
        <div class="membership-right">
          <div class="membership-status" id="cardMembershipBadge">● Active Member</div>
          <div class="membership-expiry" id="cardMembershipExpiry">Valid until: May 31, 2027</div>
        </div>
      </div>
    </div>

    <!-- STATS — Events Attended, Upcoming Events, Certificates, Achievements -->
    <div class="stats-grid">
      <div class="stat-card" id="statEventsAttendedCard" onclick="scrollToSection('registered')">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
        <span class="stat-val" id="statEventsAttendedVal">0</span>
        <div class="stat-label">Events Attended</div>
        <span class="stat-delta up">↑ Verified DB History</span>
      </div>
      <div class="stat-card" id="statUpcomingCard" onclick="scrollToSection('registered')">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><polyline points="9 14 11 16 15 12"/></svg></div>
        <span class="stat-val" id="statUpcomingVal">0</span>
        <div class="stat-label">My Registered Events</div>
        <span class="stat-delta up">↑ Active Registrations</span>
      </div>
      <div class="stat-card" id="statCertificatesCard" onclick="scrollToSection('certificates')">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
        <span class="stat-val" id="statCertificatesVal">0</span>
        <div class="stat-label">Certificates Earned</div>
        <span class="stat-delta up">↑ Ready to Download</span>
      </div>
      <div class="stat-card" id="statAchievementsCard" onclick="scrollToSection('achievements')">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg></div>
        <span class="stat-val" id="statAchievementsVal">0</span>
        <div class="stat-label">Achievements</div>
        <span class="stat-delta up">↑ Active Badges</span>
      </div>
    </div>

    <!-- MAIN GRID -->
    <div class="dash-grid">
      <!-- Event Calendar & Registered Events list -->
      <div class="card" id="calendar">
        <div class="card-head">
          <div class="card-title" id="calMonthTitle">Event Calendar — <?php echo date('F Y'); ?></div>
          <a href="event_calendar.php" target="_blank" class="card-action" style="display:inline-flex;align-items:center;gap:4px;text-decoration:none;color:var(--accent);font-weight:600;">Event Calendar ↗</a>
        </div>
        <div class="calendar-grid" id="calendarGrid">
          <!-- Calendar dynamic days rendered via JS -->
        </div>
        <button class="btn btn-primary" style="width:100%;margin-top:12px;justify-content:center;" onclick="window.open('event_calendar.php', '_blank')">📅 Event Calendar ↗</button>

        <!-- Registered Events list -->
        <div style="margin-top:20px;" id="registered">
          <div style="font-family:var(--ff-mono);font-size:.65rem;color:var(--accent);margin-bottom:10px;text-transform:uppercase;letter-spacing:.1em;">My Registered Events</div>
          <div id="myRegisteredList">
            <p style="font-size:0.8rem; color:var(--muted-dark); padding:10px 0;">Loading registrations from MySQL database...</p>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div style="display:flex;flex-direction:column;gap:24px;">
        <!-- Upcoming Events Card -->
        <div class="card" id="upcoming">
          <div class="card-head"><div class="card-title">Upcoming Events</div><span class="card-action" onclick="openDrawer('allEventsDrawer')">Explore All</span></div>
          <div id="upcomingEventsList">
            <p style="font-size:0.8rem; color:var(--muted-dark); padding:10px 0;">Loading upcoming events from MySQL database...</p>
          </div>
          <button class="btn btn-primary" style="width:100%;margin-top:16px;justify-content:center;" onclick="openDrawer('allEventsDrawer')">Browse All Events →</button>
        </div>

        <!-- Notifications Card -->
        <div class="card" id="notifications">
          <div class="card-head"><div class="card-title">Notifications</div><span class="card-action" style="cursor:pointer;" onclick="clearNotifications()">Mark Read</span></div>
          <div id="notificationsList">
            <p style="font-size:0.8rem; color:var(--muted-dark); padding:10px 0;">Loading alerts...</p>
          </div>
        </div>
      </div>
    </div>

    <!-- BOTTOM GRID -->
    <div class="dash-grid-3">
      <!-- Certificates & Registered Table -->
      <div class="card" id="certificates" style="grid-column:span 2;">
        <div class="card-head"><div class="card-title">My Certificates</div><span class="card-action" onclick="openDrawer('allEventsDrawer')">Earn More</span></div>
        <div class="cert-grid-2" id="certificatesGrid">
          <!-- Populated from MySQL DB -->
        </div>

        <!-- Registered Events Table -->
        <div style="margin-top:24px;">
          <div style="font-family:var(--ff-display);font-size:.88rem;font-weight:700;margin-bottom:12px;">All Event Registrations &amp; Status</div>
          <table class="data-table">
            <thead><tr><th>Event</th><th>Date</th><th>Role</th><th>Status</th></tr></thead>
            <tbody id="registeredTableBody">
              <tr><td colspan="4" style="text-align:center; color:var(--muted-dark); padding:15px;">Loading event registrations table...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Achievements Card -->
      <div class="card" id="achievements">
        <div class="card-head">
          <div class="card-title">Achievements</div>
          <span class="card-action" onclick="openDrawer('achievementDrawer')">+ Submit New</span>
        </div>
        <div class="ach-stack" id="achievementsStack">
          <!-- Populated dynamically from MySQL DB -->
        </div>
      </div>
    </div>
    
    <!-- Consistent Footer -->
    <footer class="portal-footer" style="margin-top:40px; padding:24px 30px; background:var(--white); border-top:1px solid var(--line-dark); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
      <div style="font-size:0.78rem; color:var(--muted-dark);">
        <span>© <span class="currentYearText"><?php echo date('Y'); ?></span> <b>Department of AIML</b>, Zeal College of Engineering and Research, Pune. All rights reserved.</span>
      </div>
      <div style="display:flex; align-items:center; gap:20px; font-size:0.75rem; color:var(--muted-dark);">
        <span>📧 Support: <a href="mailto:support.aimsa@zealeducation.com" style="color:var(--accent); text-decoration:none; font-weight:600;">support.aimsa@zealeducation.com</a></span>
        <span>📞 Call: <b>+91 20 6720 6000</b></span>
        <span style="color:var(--line-dark);">|</span>
        <a href="#" onclick="alert('Privacy Policy: All student membership data is stored securely in MySQL database and kept confidential.')" style="color:inherit; text-decoration:none; font-weight:600;">Privacy Policy</a>
        <a href="#" onclick="alert('Terms &amp; Conditions: AIMSA portal usage is governed by institute academic guidelines.')" style="color:inherit; text-decoration:none; font-weight:600;">Terms &amp; Conditions</a>
        <span style="color:var(--line-dark);">|</span>
        <span>Version: <b>v2.5.0 (MySQL Powered)</b></span>
      </div>
    </footer>

  </div>
</div>

<!-- ── MEMBERSHIP STATUS DRAWER ── -->
<div class="drawer" id="membershipDrawer">
  <div class="drawer-header">
    <div class="drawer-title">AIMSA Membership Details</div>
    <button class="drawer-close" onclick="closeDrawer('membershipDrawer')">&times;</button>
  </div>

  <!-- Digital Membership Card Preview -->
  <div style="background:linear-gradient(135deg, var(--navy-950), var(--navy-700)); border-radius:18px; padding:24px; color:#fff; position:relative; overflow:hidden; border:1px solid var(--line); box-shadow:0 12px 30px rgba(8,23,51,0.3);">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.15); padding-bottom:12px; margin-bottom:16px;">
      <div style="display:flex; align-items:center; gap:8px;">
        <img src="images/aimsa_logo.jpg" alt="Logo" style="width:28px; height:28px; border-radius:50%; object-fit:cover;">
        <span style="font-family:var(--ff-display); font-size:0.88rem; font-weight:700; color:#fff;">AIMSA Digital Pass</span>
      </div>
      <span class="membership-status" id="drawerMembershipBadge">● Active</span>
    </div>

    <div style="margin-bottom:14px;">
      <div style="font-size:0.7rem; font-family:var(--ff-mono); color:var(--accent-soft); text-transform:uppercase; letter-spacing:0.12em;">Student Member</div>
      <div style="font-family:var(--ff-display); font-size:1.3rem; font-weight:700;" id="drawerStudentName">Arjun Patil</div>
      <div style="font-size:0.8rem; color:var(--muted); font-family:var(--ff-mono);" id="drawerZPRN">ZPRN: 125UAM1005</div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.75rem; font-family:var(--ff-mono); background:rgba(255,255,255,0.06); padding:10px 14px; border-radius:10px; margin-bottom:16px;">
      <div><span>Branch:</span> <b id="drawerBranch" style="color:#fff;">AI & ML</b></div>
      <div><span>Batch:</span> <b id="drawerBatch" style="color:#fff;">2026</b></div>
      <div><span>Member Since:</span> <b style="color:#fff;">June 2024</b></div>
      <div><span>Valid Until:</span> <b style="color:#4ade80;">May 31, 2027</b></div>
    </div>

    <!-- Barcode Simulation -->
    <div style="background:#fff; padding:8px; border-radius:8px; text-align:center;">
      <div style="font-family:'Courier New', monospace; font-weight:900; font-size:1.1rem; letter-spacing:0.3em; color:#000;" id="drawerBarcode">||| ||||| |||| ||||| |||</div>
      <div style="font-size:0.65rem; color:#666; font-family:var(--ff-mono); margin-top:2px;" id="drawerCardID">ID: AIMSA-2026-1005</div>
    </div>
  </div>

  <!-- Membership Benefits list -->
  <div style="margin-top:10px;">
    <div style="font-family:var(--ff-display); font-size:0.95rem; font-weight:700; margin-bottom:10px;">Member Privileges &amp; Access</div>
    <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Priority Event Registration</b><span>Free access to AI workshops, hackathons &amp; guest lectures.</span></div></div>
    <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Verified Certificates</b><span>Instant digital certificate generation for completed events.</span></div></div>
    <div class="list-item"><div class="list-dot"></div><div class="list-text"><b>Departmental Lab Access</b><span>Access to High-Performance Computing &amp; GPU Labs.</span></div></div>
  </div>

  <!-- Renewal / Status Button -->
  <button class="btn btn-primary" style="margin-top:auto; width:100%;" id="renewMembershipDrawerBtn" onclick="triggerMembershipRenewal()">⚡ Renew / Confirm Active Status in DB</button>
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
  <button class="btn btn-primary" style="width:100%; margin-top:10px;" id="savePasswordBtn">Update Password in Database</button>
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
    <label>ZPRN / Student PRN</label>
    <input type="text" id="profileZPRN" placeholder="125UAM1005">
  </div>
  <div class="form-group">
    <label>Contact Number</label>
    <input type="text" id="profilePhone" placeholder="+91 XXXXX XXXXX">
  </div>
  <div class="form-group">
    <label>Academic Batch / Year</label>
    <select id="profileYear">
      <option value="2026">2026 (3rd Year)</option>
      <option value="2027">2027 (2nd Year)</option>
      <option value="2025">2025 (4th Year)</option>
      <option value="2028">2028 (1st Year)</option>
    </select>
  </div>
  <div class="form-group">
    <label>Department / Branch</label>
    <select id="profileBranch">
      <option value="AI & ML">AI & ML</option>
      <option value="Computer Science">Computer Science</option>
      <option value="Data Science">Data Science</option>
      <option value="Information Technology">Information Technology</option>
    </select>
  </div>
  <button class="btn btn-primary" style="margin-top:10px; width:100%;" id="saveProfileBtn">Save Profile Changes to DB</button>
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
    <label>Attach Certificate / Proof (PDF / Image)</label>
    <input type="file" id="achFile" accept="image/*,.pdf" style="padding:4px;">
  </div>
  <button class="btn btn-primary" style="margin-top:10px; width:100%;" id="submitAchievementBtn">Submit Achievement</button>
</div>

<!-- ── ALL UPCOMING EVENTS DRAWER ── -->
<div class="drawer" id="allEventsDrawer">
  <div class="drawer-header">
    <div class="drawer-title">All Approved Department Events</div>
    <button class="drawer-close" onclick="closeDrawer('allEventsDrawer')">&times;</button>
  </div>
  <div id="allEventsDrawerList" style="display:flex; flex-direction:column; gap:16px;">
    <!-- Rendered via JS -->
  </div>
</div>

<!-- ── CERTIFICATE PREVIEW MODAL ── -->
<div class="drawer" id="certModal">
  <div class="drawer-header">
    <div class="drawer-title">Official Certificate Preview</div>
    <button class="drawer-close" onclick="closeDrawer('certModal')">&times;</button>
  </div>
  <div id="certPreviewCard" style="background:linear-gradient(135deg, #081733, #0c2148); padding:32px; border-radius:20px; color:#fff; text-align:center; border:2px solid var(--accent); position:relative; box-shadow:0 20px 50px rgba(0,0,0,0.5);">
    <div style="font-family:var(--ff-mono); font-size:0.65rem; letter-spacing:0.2em; color:var(--accent-soft); text-transform:uppercase; margin-bottom:12px;">ZEAL EDUCATION SOCIETY · DEPT. OF AI & ML</div>
    <div style="font-family:var(--ff-display); font-size:1.5rem; font-weight:800; color:#fff; margin-bottom:6px;">CERTIFICATE OF EXCELLENCE</div>
    <div style="font-size:0.8rem; color:var(--muted); margin-bottom:24px;">THIS ACKNOWLEDGES THAT</div>
    <div style="font-family:var(--ff-display); font-size:1.45rem; font-weight:700; color:#4ade80; margin-bottom:12px;" id="certStudentNameDisplay">Arjun Patil</div>
    <div style="font-size:0.9rem; color:#e2e8f0; line-height:1.5; margin-bottom:24px;" id="certEventNameDisplay">has successfully completed all requirements for Tech Symposium 2026.</div>
    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid rgba(255,255,255,0.15); padding-top:18px; font-family:var(--ff-mono); font-size:0.7rem; color:var(--accent-soft);">
      <span id="certCodeDisplay">ID: CERT-2026-001</span>
      <span>Verified by HOD</span>
    </div>
  </div>
  <button class="btn btn-primary" style="margin-top:20px; width:100%;" onclick="window.print()">🖨️ Print / Save PDF Certificate</button>
</div>

<script>
// Sidebar Toggle
const sidebar = document.getElementById('sidebar'), overlay = document.getElementById('sidebarOverlay'), hamburger = document.getElementById('hamburgerBtn');

if (hamburger) {
  hamburger.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
  });
}

if (overlay) {
  overlay.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
    closeAllDrawers();
  });
}

function openDrawer(id) {
  closeAllDrawers();
  const d = document.getElementById(id);
  if (d) {
    d.classList.add('open');
    overlay.classList.add('open');
  } else {
    console.error('Drawer element not found:', id);
  }
}

function closeDrawer(id) {
  const d = document.getElementById(id);
  if (d) d.classList.remove('open');
  overlay.classList.remove('open');
}

function closeAllDrawers() {
  document.querySelectorAll('.drawer').forEach(d => d.classList.remove('open'));
  if (overlay) overlay.classList.remove('open');
}

function scrollToSection(id) {
  const el = document.getElementById(id);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    el.style.transition = 'outline 0.3s ease';
    el.style.outline = '2px solid var(--accent)';
    setTimeout(() => { el.style.outline = 'none'; }, 2000);
  }
}

// Live Clock Engine
function updateLiveClock() {
  const now = new Date();
  const dateStr = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
  const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
  document.querySelectorAll('.liveClockText').forEach(el => el.textContent = `${dateStr} · ${timeStr}`);
  document.querySelectorAll('.liveDateText').forEach(el => el.textContent = now.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }));
  document.querySelectorAll('.currentYearText').forEach(el => el.textContent = now.getFullYear());
}
updateLiveClock();
setInterval(updateLiveClock, 1000);

// User Session state synced with PHP session & MySQL
let currentUser = <?php echo json_encode($sessionUser); ?> || JSON.parse(sessionStorage.getItem('current_user')) || {
  email: 'student@zealeducation.com',
  name: 'Arjun Patil',
  role: 'Student Member',
  branch: 'AI & ML',
  batch: '2026',
  membershipStatus: 'Active',
  zprn: '125UAM1005'
};

let dashboardDataCache = null;

// Fetch Dashboard Data from MySQL database via AJAX
async function fetchDashboardData() {
  try {
    const res = await fetch(`ajax/student_actions.php?action=getDashboardData&email=${encodeURIComponent(currentUser.email)}`);
    const data = await res.json();
    if (data.status === 'success') {
      dashboardDataCache = data;
      currentUser = {...currentUser, ...data.user};
      sessionStorage.setItem('current_user', JSON.stringify(currentUser));

      renderProfileHeader();
      renderMembershipCard();
      renderStats();
      renderRegisteredEvents();
      renderUpcomingEvents();
      renderCalendar();
      renderCertificates();
      renderAchievements();
      renderNotificationsList();
    }
  } catch (e) {
    console.error('Failed to load dashboard data from MySQL:', e);
  }
}

// 1. Render User Profile & Headers
function renderProfileHeader() {
  const firstName = (currentUser.name || 'Student').split(' ')[0];
  document.getElementById('greetingTitle').innerHTML = `Hey, ${firstName}! 👋`;
  document.getElementById('sidebarUserName').textContent = currentUser.name || 'Student Member';
  document.getElementById('headerUserName').textContent = currentUser.name || 'Student Member';
  document.getElementById('headerUserRole').textContent = currentUser.role || 'Student Member';

  const initials = (currentUser.name || 'SM').split(' ').map(n=>n[0]).join('').toUpperCase().substring(0, 2);
  
  if (currentUser.photograph) {
    document.getElementById('sidebarAvatarContainer').innerHTML = `<img src="${currentUser.photograph}" style="width:100%; height:100%; object-fit:cover;">`;
    document.getElementById('headerUserAvatar').innerHTML = `<img src="${currentUser.photograph}" style="width:100%; height:100%; object-fit:cover;">`;
    document.getElementById('photoPreviewContainer').innerHTML = `<img src="${currentUser.photograph}" style="width:100%; height:100%; object-fit:cover;">`;
  } else {
    document.getElementById('sidebarAvatarContainer').innerHTML = `<div class="in">${initials}</div>`;
    document.getElementById('headerUserAvatar').textContent = initials;
    document.getElementById('avatarInitials').textContent = initials;
  }
}

// 2. Render Membership Card & Drawer Info
function renderMembershipCard() {
  const zprn = currentUser.zprn || '125UAM1005';
  const name = currentUser.name || 'Arjun Patil';
  const branch = currentUser.branch || 'AI & ML';
  const batch = currentUser.batch || '2026';
  const status = currentUser.membershipStatus || 'Active';

  document.getElementById('cardStudentName').textContent = name;
  document.getElementById('cardStudentID').textContent = `Member ID · ${zprn} · ${branch} (Batch ${batch})`;

  // Card badge
  const badge = document.getElementById('cardMembershipBadge');
  const expiry = document.getElementById('cardMembershipExpiry');

  // Drawer Info elements
  document.getElementById('drawerStudentName').textContent = name;
  document.getElementById('drawerZPRN').textContent = `ZPRN: ${zprn}`;
  document.getElementById('drawerBranch').textContent = branch;
  document.getElementById('drawerBatch').textContent = batch;
  document.getElementById('drawerBarcode').textContent = `||| ||||| ${zprn} ||||| |||`;
  document.getElementById('drawerCardID').textContent = `ID: AIMSA-${batch}-${zprn}`;
  const drawerBadge = document.getElementById('drawerMembershipBadge');

  if (status === 'Active') {
    badge.textContent = '● Active Member';
    badge.style.color = '#4ade80';
    badge.style.borderColor = 'rgba(34,197,94,.3)';
    badge.style.background = 'rgba(34,197,94,.15)';
    expiry.textContent = 'Valid until: May 31, 2027';

    if (drawerBadge) {
      drawerBadge.textContent = '● Active';
      drawerBadge.style.color = '#4ade80';
    }
  } else {
    badge.textContent = '● Membership Expired';
    badge.style.color = '#f87171';
    badge.style.borderColor = 'rgba(239,68,68,.3)';
    badge.style.background = 'rgba(239,68,68,.15)';
    expiry.textContent = 'Click to Renew Membership for 2026-27';

    if (drawerBadge) {
      drawerBadge.textContent = '● Expired';
      drawerBadge.style.color = '#f87171';
    }
  }
}

// Membership renewal trigger function
window.triggerMembershipRenewal = async function() {
  if (confirm('Renew / confirm your AIMSA student membership for the 2026-27 academic term?')) {
    const formData = new FormData();
    formData.append('action', 'renewMembership');
    formData.append('email', currentUser.email);

    const res = await fetch('ajax/student_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert('Membership status updated to ACTIVE in database!');
      closeDrawer('membershipDrawer');
      fetchDashboardData();
    } else {
      alert(data.message || 'Failed to update membership');
    }
  }
};

// 3. Render Stats Cards
function renderStats() {
  if (!dashboardDataCache || !dashboardDataCache.stats) return;
  const s = dashboardDataCache.stats;
  document.getElementById('statEventsAttendedVal').textContent = s.events_attended;
  document.getElementById('statUpcomingVal').textContent = s.upcoming_events;
  document.getElementById('statCertificatesVal').textContent = s.certificates;
  document.getElementById('statAchievementsVal').textContent = s.achievements;
}

// 4. Render Registered Events List & Table
function renderRegisteredEvents() {
  if (!dashboardDataCache) return;
  const regs = dashboardDataCache.registered_events || [];

  const listContainer = document.getElementById('myRegisteredList');
  if (regs.length === 0) {
    listContainer.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:10px 0;">No registered events currently. Click "Browse Events" to join!</p>`;
  } else {
    listContainer.innerHTML = regs.map(r => `
      <div class="list-item">
        <div class="list-dot"></div>
        <div class="list-text">
          <b>${r.event_name}</b>
          <span>${r.event_date || 'Scheduled'} · ${r.location || 'Campus'} · <span class="badge badge-blue">Registered</span> <span style="color:#ef4444; cursor:pointer; margin-left:12px; font-weight:600;" onclick="cancelEventRegistration(${r.event_id}, '${r.event_name}')">Cancel</span></span>
        </div>
      </div>
    `).join('');
  }

  // Render Table
  const tbody = document.getElementById('registeredTableBody');
  const allEvents = dashboardDataCache.upcoming_events || [];

  if (allEvents.length === 0) {
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--muted-dark); padding:15px;">No events listed</td></tr>`;
  } else {
    tbody.innerHTML = allEvents.map(e => {
      const isReg = regs.some(r => r.event_id == e.id || r.event_name.toLowerCase() === e.title.toLowerCase());
      return `
        <tr>
          <td><b>${e.title}</b></td>
          <td>${e.event_date}</td>
          <td>Attendee</td>
          <td>
            ${isReg ? `<span class="badge badge-blue">Registered</span> <button class="btn btn-ghost" style="padding:2px 8px; font-size:0.7rem; color:#ef4444;" onclick="cancelEventRegistration(${e.id}, '${e.title}')">Cancel</button>` 
                    : `<button class="btn btn-primary" style="padding:4px 12px; font-size:0.75rem;" onclick="registerForEvent(${e.id}, '${e.title}')">Register</button>`}
          </td>
        </tr>
      `;
    }).join('');
  }
}

// Register for event AJAX
window.registerForEvent = async function(eventId, eventName) {
  const formData = new FormData();
  formData.append('action', 'registerEvent');
  formData.append('email', currentUser.email);
  formData.append('event_id', eventId);
  formData.append('event_name', eventName);

  const res = await fetch('ajax/student_actions.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.status === 'success') {
    alert(data.message);
    fetchDashboardData();
  } else {
    alert(data.message || 'Failed to register');
  }
};

// Cancel Registration AJAX
window.cancelEventRegistration = async function(eventId, eventName) {
  if (confirm(`Are you sure you want to cancel your registration for ${eventName}?`)) {
    const formData = new FormData();
    formData.append('action', 'cancelRegistration');
    formData.append('email', currentUser.email);
    formData.append('event_id', eventId);
    formData.append('event_name', eventName);

    const res = await fetch('ajax/student_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      fetchDashboardData();
    }
  }
};

// 5. Render Upcoming Events
function renderUpcomingEvents() {
  if (!dashboardDataCache) return;
  const events = dashboardDataCache.upcoming_events || [];
  const regs = dashboardDataCache.registered_events || [];

  const list = document.getElementById('upcomingEventsList');
  if (events.length === 0) {
    list.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:10px 0;">No approved upcoming events scheduled right now.</p>`;
  } else {
    list.innerHTML = events.slice(0, 3).map(e => {
      const isReg = regs.some(r => r.event_id == e.id || r.event_name.toLowerCase() === e.title.toLowerCase());
      return `
        <div class="list-item">
          <div class="list-dot" style="background:${isReg ? '#3E8BFF' : '#22c55e'};"></div>
          <div class="list-text">
            <b>${e.title}</b>
            <span>${e.event_date} · ${e.location} · ${isReg ? '<b style="color:var(--accent);">Registered</b>' : `<a href="#" onclick="registerForEvent(${e.id}, '${e.title}'); return false;" style="color:var(--accent); font-weight:600;">Register Now →</a>`}</span>
          </div>
        </div>
      `;
    }).join('');
  }

  // Populate All Events Drawer List
  const drawerList = document.getElementById('allEventsDrawerList');
  if (events.length === 0) {
    drawerList.innerHTML = `<p style="font-size:0.85rem; color:var(--muted-dark);">No upcoming approved events available currently.</p>`;
  } else {
    drawerList.innerHTML = events.map(e => {
      const isReg = regs.some(r => r.event_id == e.id || r.event_name.toLowerCase() === e.title.toLowerCase());
      return `
        <div style="background:var(--paper); border:1px solid var(--line-dark); border-radius:12px; padding:16px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
            <b style="font-size:0.95rem; font-family:var(--ff-display);">${e.title}</b>
            <span class="badge ${isReg ? 'badge-blue' : 'badge-green'}">${isReg ? 'Registered' : 'Open'}</span>
          </div>
          <p style="font-size:0.82rem; color:var(--muted-dark); margin-bottom:10px;">${e.description || 'Departmental event organized by AIMSA.'}</p>
          <div style="font-family:var(--ff-mono); font-size:0.75rem; color:var(--navy-900); margin-bottom:12px;">
            📅 Date: <b>${e.event_date}</b> | 📍 Venue: <b>${e.location}</b> | 👥 Regs: <b>${e.registrations_count || 0}</b>
          </div>
          ${isReg ? `<button class="btn btn-ghost" style="width:100%; color:#ef4444;" onclick="cancelEventRegistration(${e.id}, '${e.title}')">Cancel Registration</button>`
                  : `<button class="btn btn-primary" style="width:100%;" onclick="registerForEvent(${e.id}, '${e.title}')">Register for Event</button>`}
        </div>
      `;
    }).join('');
  }
}

// 6. Dynamic Calendar Generator
function renderCalendar() {
  if (!dashboardDataCache) return;
  const events = dashboardDataCache.upcoming_events || [];
  const grid = document.getElementById('calendarGrid');

  const now = new Date();
  const year = now.getFullYear();
  const month = now.getMonth();
  const todayDate = now.getDate();

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  let offset = firstDay === 0 ? 6 : firstDay - 1;

  let html = `
    <div class="cal-header">MON</div><div class="cal-header">TUE</div><div class="cal-header">WED</div>
    <div class="cal-header">THU</div><div class="cal-header">FRI</div><div class="cal-header">SAT</div><div class="cal-header">SUN</div>
  `;

  for (let i = 0; i < offset; i++) {
    html += `<div class="cal-day cal-empty"></div>`;
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    const dayEvt = events.find(e => e.event_date === dateStr);

    let classes = 'cal-day';
    if (d === todayDate) classes += ' cal-today';
    if (dayEvt) classes += ' cal-event';

    const titleAttr = dayEvt ? `title="${dayEvt.title} (${dayEvt.location})"` : '';
    const clickAttr = dayEvt ? `onclick="alert('Event on ${dateStr}: ${dayEvt.title}\\nVenue: ${dayEvt.location}')"` : '';

    html += `<div class="${classes}" ${titleAttr} ${clickAttr}>${d}</div>`;
  }

  grid.innerHTML = html;
}

// 7. Render Certificates
function renderCertificates() {
  if (!dashboardDataCache) return;
  const certs = dashboardDataCache.certificates || [];
  const grid = document.getElementById('certificatesGrid');

  if (!certs || certs.length === 0) {
    grid.innerHTML = `
      <div style="grid-column:1/-1; padding:28px 20px; text-align:center; background:var(--paper); border-radius:14px; border:1px dashed var(--line-dark); margin:6px 0;">
        <div style="font-size:2.2rem; margin-bottom:8px;">📜</div>
        <div style="font-family:var(--ff-display); font-weight:700; font-size:0.95rem; color:var(--navy-950); margin-bottom:4px;">No Certificates Issued Yet</div>
        <p style="font-size:0.8rem; color:var(--muted-dark); max-width:420px; margin:0 auto; line-height:1.4;">
          No certificates have been issued to your account so far. Complete registered departmental events and workshops to earn verified digital certificates.
        </p>
      </div>
    `;
  } else {
    grid.innerHTML = certs.map(c => `
      <div class="cert-card">
        <h4>${c.event_name}</h4>
        <span>${c.type} · Code: <b>${c.cert_code}</b></span>
        <div class="cert-download" onclick="openCertModal('${c.cert_code}', '${c.event_name}', '${c.student_name}')">↓ View / Download PDF</div>
      </div>
    `).join('');
  }
}

window.openCertModal = function(code, eventName, studentName) {
  document.getElementById('certCodeDisplay').textContent = `ID: ${code}`;
  document.getElementById('certEventNameDisplay').textContent = `has successfully participated in ${eventName}.`;
  document.getElementById('certStudentNameDisplay').textContent = studentName || currentUser.name;
  openDrawer('certModal');
};

// 8. Render Achievements Stack
function renderAchievements() {
  if (!dashboardDataCache) return;
  const achs = dashboardDataCache.achievements || [];
  const stack = document.getElementById('achievementsStack');

  if (achs.length === 0) {
    stack.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark);">No achievements submitted yet. Click "+ Submit New" to add your achievement!</p>`;
  } else {
    stack.innerHTML = achs.map(a => `
      <div class="achievement-badge">
        <div class="ach-icon"><svg viewBox="0 0 24 24"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg></div>
        <div class="ach-info"><b>${a.title}</b><span>${a.category} · <b style="color:${a.status === 'Approved' ? '#22c55e' : '#f59e0b'};">${a.status}</b></span></div>
      </div>
    `).join('');
  }
}

// 9. Render Notifications List
function renderNotificationsList() {
  if (!dashboardDataCache) return;
  const notifs = dashboardDataCache.notifications || [];
  const list = document.getElementById('notificationsList');
  document.getElementById('sidebarNotifBadge').textContent = notifs.length;

  if (notifs.length === 0) {
    list.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:10px 0;">No active alerts.</p>`;
  } else {
    list.innerHTML = notifs.slice(0, 5).map(n => `
      <div class="list-item">
        <div class="list-dot" style="background:${n.indicator === 'red' ? '#ef4444' : n.indicator === 'yellow' ? '#fbbf24' : '#22c55e'};"></div>
        <div class="list-text">
          <b>${n.title}</b>
          <span>${n.text}</span>
        </div>
      </div>
    `).join('');
  }
}

window.clearNotifications = function() {
  document.getElementById('notificationsList').innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:10px 0;">All notifications marked as read.</p>`;
  document.getElementById('sidebarNotifBadge').textContent = '0';
};

// Open notifications helper
window.openNotifications = function() {
  const notifCard = document.getElementById('notifications');
  if (notifCard) {
    notifCard.scrollIntoView({ behavior: 'smooth' });
    notifCard.style.outline = '2px solid var(--accent)';
    setTimeout(() => { notifCard.style.outline = 'none'; }, 2000);
  }
};

// Profile Update AJAX Save
document.getElementById('saveProfileBtn').addEventListener('click', async () => {
  const name = document.getElementById('profileName').value.trim();
  const zprn = document.getElementById('profileZPRN').value.trim();
  const phone = document.getElementById('profilePhone').value.trim();
  const batch = document.getElementById('profileYear').value;
  const branch = document.getElementById('profileBranch').value;
  const photoInput = document.getElementById('profilePhotoInput');

  if (!name) {
    alert('Please enter your Full Name.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'updateProfile');
  formData.append('email', currentUser.email);
  formData.append('name', name);
  formData.append('zprn', zprn);
  formData.append('phone', phone);
  formData.append('batch', batch);
  formData.append('branch', branch);

  if (photoInput.files[0]) {
    formData.append('profilePhoto', photoInput.files[0]);
  }

  try {
    const res = await fetch('ajax/student_actions.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.status === 'success') {
      alert(data.message);
      closeDrawer('profileDrawer');
      fetchDashboardData();
    } else {
      alert(data.message || 'Failed to update profile');
    }
  } catch (e) {
    alert('Error updating profile in database');
  }
});

// Change Password AJAX Save
document.getElementById('savePasswordBtn').addEventListener('click', async () => {
  const curr = document.getElementById('currPassword').value;
  const newp = document.getElementById('newPassword').value;
  const conf = document.getElementById('confirmNewPassword').value;

  if (!curr || !newp || !conf) {
    alert('Please fill out all password fields.');
    return;
  }
  if (newp !== conf) {
    alert('New passwords do not match.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'changePassword');
  formData.append('email', currentUser.email);
  formData.append('current_password', curr);
  formData.append('new_password', newp);

  const res = await fetch('ajax/student_actions.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.status === 'success') {
    alert(data.message);
    closeDrawer('changePasswordDrawer');
  } else {
    alert(data.message);
  }
});

// Submit Achievement AJAX Save
document.getElementById('submitAchievementBtn').addEventListener('click', async () => {
  const cat = document.getElementById('achCategory').value;
  const title = document.getElementById('achTitle').value.trim();
  const desc = document.getElementById('achDescription').value.trim();
  const achFile = document.getElementById('achFile');

  if (!title || !desc) {
    alert('Please fill out title and description for your achievement.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'submitAchievement');
  formData.append('email', currentUser.email);
  formData.append('category', cat);
  formData.append('title', title);
  formData.append('description', desc);

  if (achFile.files[0]) {
    formData.append('achFile', achFile.files[0]);
  }

  const res = await fetch('ajax/student_actions.php', { method: 'POST', body: formData });
  const data = await res.json();
  if (data.status === 'success') {
    alert(data.message);
    closeDrawer('achievementDrawer');
    fetchDashboardData();
  } else {
    alert(data.message || 'Failed to submit achievement');
  }
});

// Profile Dropdown Toggle
window.toggleProfileDropdown = function() {
  const d = document.getElementById('profileDropdown');
  d.style.display = d.style.display === 'none' ? 'block' : 'none';
};
document.addEventListener('click', (e) => {
  const wrapper = document.getElementById('profileMenuWrapper');
  if (wrapper && !wrapper.contains(e.target)) {
    document.getElementById('profileDropdown').style.display = 'none';
  }
});

// Populate Profile Drawer inputs on open
function populateProfileDrawerInputs() {
  document.getElementById('profileName').value = currentUser.name || '';
  document.getElementById('profileEmail').value = currentUser.email || '';
  document.getElementById('profileZPRN').value = currentUser.zprn || '';
  document.getElementById('profilePhone').value = currentUser.phone || '';
  document.getElementById('profileYear').value = currentUser.batch || '2026';
  document.getElementById('profileBranch').value = currentUser.branch || 'AI & ML';
}

// Search bar input filtering
document.getElementById('headerSearchInput').addEventListener('input', (e) => {
  const query = e.target.value.toLowerCase().trim();
  document.querySelectorAll('.list-item, .achievement-badge, .cert-card, tr').forEach(el => {
    if (query === '') {
      el.style.display = '';
    } else {
      const text = el.textContent.toLowerCase();
      el.style.display = text.includes(query) ? '' : 'none';
    }
  });
});

// Language Switch
window.changeLanguage = function() {
  const lang = document.getElementById('langSelect').value;
  if (lang === 'mr') {
    alert('पोर्टलची भाषा यशस्वीरीत्या मराठीमध्ये बदलली आहे. (Portal language switched to Marathi)');
  } else {
    alert('Portal language switched to English.');
  }
};

// Initial Page Load
fetchDashboardData();
</script>
</body>
</html>
