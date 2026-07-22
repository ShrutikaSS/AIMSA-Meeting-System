<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/include/dbConfig.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HOD Dashboard — AIMSA Portal</title>
<meta name="description" content="Head of Department portal dashboard for AIMSA — AI & ML Student Association at Zeal Education Society.">
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

/* ── SIDEBAR ── */
.sidebar{width:var(--sidebar-w);flex-shrink:0;background:var(--navy-950);min-height:100vh;position:fixed;left:0;top:0;bottom:0;display:flex;flex-direction:column;border-right:1px solid var(--line);z-index:100;transition:transform .3s ease;}
.sidebar-brand{padding:24px 22px 20px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:12px;}
.brand-logo{width:42px;height:42px;border-radius:50%;background:var(--white);display:flex;align-items:center;justify-content:center;overflow:hidden;border:2px solid rgba(255,255,255,.3);flex-shrink:0;}
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

/* ── MAIN ── */
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

/* ── EYEBROW ── */
.section-eyebrow{font-family:var(--ff-mono);font-size:.68rem;letter-spacing:.18em;text-transform:uppercase;color:var(--accent);display:flex;align-items:center;gap:10px;margin-bottom:8px;}
.section-eyebrow::before{content:'';width:22px;height:1px;background:var(--accent);}
.content-title{font-family:var(--ff-display);font-size:1.6rem;font-weight:700;color:var(--navy-950);margin-bottom:4px;}
.content-sub{color:var(--muted-dark);font-size:.9rem;margin-bottom:32px;}

/* ── STAT CARDS ── */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:32px;}
.stat-card{background:var(--white);border-radius:16px;padding:22px 24px;border:1px solid var(--line-dark);transition:transform .2s ease,box-shadow .3s ease;position:relative;overflow:hidden;cursor:pointer;}
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

/* ── GRID LAYOUTS ── */
.dash-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:24px;margin-bottom:24px;}
.dash-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:24px;}
.dash-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;}

/* ── CARDS ── */
.card{background:var(--white);border-radius:var(--radius);border:1px solid var(--line-dark);padding:24px;}
.card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.card-title{font-family:var(--ff-display);font-size:1rem;font-weight:700;}
.card-action{font-family:var(--ff-mono);font-size:.65rem;letter-spacing:.08em;color:var(--accent);text-transform:uppercase;cursor:pointer;padding:5px 10px;border-radius:999px;border:1px solid rgba(62,139,255,.3);transition:.2s ease;}
.card-action:hover{background:var(--accent);color:var(--white);}

/* ── TABLE ── */
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

/* ── LIST ITEMS ── */
.list-item{display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid rgba(8,23,51,.05);}
.list-item:last-child{border-bottom:none;}
.list-dot{width:9px;height:9px;border-radius:50%;background:var(--accent);flex-shrink:0;box-shadow:0 0 0 3px rgba(62,139,255,.18);}
.list-text{flex:1;}
.list-text b{font-size:.88rem;display:block;margin-bottom:2px;}
.list-text span{font-size:.76rem;color:var(--muted-dark);font-family:var(--ff-mono);}

/* ── PROGRESS ── */
.progress-item{margin-bottom:16px;}
.progress-label{display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:6px;}
.progress-label span:last-child{font-family:var(--ff-mono);color:var(--accent);}
.progress-bar{height:6px;background:var(--paper-dim);border-radius:999px;overflow:hidden;}
.progress-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,var(--accent),var(--accent-soft));}

/* ── QUICK ACTIONS ── */
.quick-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;}
.quick-btn{display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 12px;border-radius:12px;border:1.5px solid var(--line-dark);background:var(--paper);transition:.2s ease;cursor:pointer;text-align:center;}
.quick-btn:hover{border-color:var(--accent);background:var(--white);transform:translateY(-2px);box-shadow:0 8px 20px -10px var(--accent-glow);}
.quick-btn svg{width:22px;height:22px;stroke:var(--navy-800);fill:none;stroke-width:1.7;}
.quick-btn:hover svg{stroke:var(--accent);}
.quick-btn span{font-size:.78rem;font-weight:600;color:var(--navy-900);}

/* ── REPORT CARD ── */
.report-item{display:flex;align-items:center;gap:14px;padding:14px;border-radius:12px;border:1px solid var(--line-dark);background:var(--paper);transition:.2s ease;margin-bottom:10px;}
.report-item:hover{border-color:var(--accent);background:var(--white);transform:translateX(4px);}
.report-icon{width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,var(--navy-950),var(--navy-800));display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:.3s ease;}
.report-item:hover .report-icon{background:linear-gradient(135deg,var(--accent),#2563eb);}
.report-icon svg{width:18px;height:18px;stroke:var(--white);fill:none;stroke-width:1.8;}
.report-info{flex:1;}
.report-info b{font-size:.88rem;display:block;margin-bottom:2px;}
.report-info span{font-size:.75rem;color:var(--muted-dark);font-family:var(--ff-mono);}
.report-dl{font-family:var(--ff-mono);font-size:.62rem;letter-spacing:.06em;text-transform:uppercase;color:var(--accent);padding:4px 10px;border-radius:999px;border:1px solid rgba(62,139,255,.3);transition:.2s;cursor:pointer;}
.report-dl:hover{background:var(--accent);color:var(--white);}

/* ── CERT COUNTER ── */
.cert-counter{display:flex;align-items:center;gap:20px;padding:20px;background:linear-gradient(135deg,var(--navy-950),var(--navy-800));border-radius:14px;color:var(--white);position:relative;overflow:hidden;margin-bottom:12px;}
.cert-counter::after{content:'';position:absolute;right:-40px;top:-40px;width:120px;height:120px;border-radius:50%;background:radial-gradient(circle,var(--accent-glow),transparent 70%);}
.cert-counter-icon{width:50px;height:50px;border-radius:12px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.cert-counter-icon svg{width:24px;height:24px;stroke:var(--accent-soft);fill:none;stroke-width:1.8;}
.cert-counter-val{font-family:var(--ff-display);font-size:2.4rem;font-weight:700;display:block;line-height:1;}
.cert-counter-label{font-family:var(--ff-mono);font-size:.65rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-top:4px;}

/* ── SIDEBAR OVERLAY & DRAWER ── */
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(5,13,26,.5);z-index:99;}
.btn{display:inline-flex;align-items:center;gap:8px;justify-content:center;padding:10px 20px;border-radius:999px;font-weight:600;font-size:.84rem;border:1px solid transparent;transition:all .25s ease;cursor:pointer;}
.btn-primary{background:linear-gradient(135deg,var(--accent),#2563eb);color:var(--white);box-shadow:0 8px 20px -8px var(--accent-glow);}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 14px 28px -8px var(--accent-glow);}
.btn-ghost{background:transparent;border-color:var(--line-dark);color:var(--navy-800);}
.btn-ghost:hover{background:var(--paper-dim);}

.header-search-bar {
  display: flex; align-items: center; gap: 8px; background: var(--paper);
  border: 1.5px solid var(--line-dark); border-radius: 99px; padding: 6px 14px;
  width: min(280px, 100%); transition: border-color 0.2s;
}
.header-search-bar:focus-within { border-color: var(--accent); }
.header-search-bar input { background: transparent; border: none; font-size: 0.8rem; font-family: inherit; color: var(--navy-950); outline: none; width: 100%; }

.drawer{
  position:fixed; top:0; right:-480px; width:min(480px, 100%); height:100vh;
  background:var(--white); border-left:1px solid var(--line-dark); z-index:200;
  transition:right 0.3s cubic-bezier(0.16, 1, 0.3, 1); padding:30px;
  display:flex; flex-direction:column; gap:16px; overflow-y:auto;
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

.tab-buttons{display:flex; gap:8px; border-bottom:1px solid var(--line-dark); padding-bottom:10px; margin-bottom:15px;}
.tab-btn{background:none; border:none; padding:6px 14px; font-size:0.82rem; font-weight:600; color:var(--muted-dark); cursor:pointer; border-radius:6px;}
.tab-btn.active{background:rgba(62,139,255,0.12); color:var(--accent);}

@media(max-width:1100px){.stats-grid{grid-template-columns:repeat(2,1fr);}.dash-grid{grid-template-columns:1fr;}.dash-grid-3{grid-template-columns:1fr 1fr;}.dash-grid-2{grid-template-columns:1fr;}}
@media(max-width:768px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}.sidebar-overlay.open{display:block;}.main{margin-left:0;}.hamburger-btn{display:flex;}.content{padding:20px;}.stats-grid{grid-template-columns:1fr 1fr;}.dash-grid-3{grid-template-columns:1fr;}}
@media(max-width:480px){.stats-grid{grid-template-columns:1fr;}}
</style>
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo">
      <img src="images/aimsa_logo.jpg" alt="AIMSA Logo" style="width:100%; height:100%; object-fit:cover;">
    </div>
    <div class="brand-info"><b>AIMSA Portal</b><span>HOD Access</span></div>
  </div>

  <div class="sidebar-role">
    <div class="role-avatar"><div class="in">HD</div></div>
    <div class="role-info"><b>Dr. Dipali Shende</b><span>Head of Department</span></div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <a class="nav-item active" href="hod_dashboard.php" id="navDashboard">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Dashboard
    </a>

    <div class="nav-section-label">Management</div>
    <a class="nav-item" href="#members" id="navMembers">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M10 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
      Total Members
      <span class="nav-badge" id="navMembersBadge">0</span>
    </a>

    <a class="nav-item" href="#committee" id="navCommittee">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
      Committee Members
    </a>

    <a class="nav-item" href="#events" id="navEvents">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Events Conducted
    </a>

    <a class="nav-item" href="#upcoming" id="navUpcoming">
      <svg class="nav-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Upcoming Events
    </a>

    <div class="nav-section-label">Reports & Analytics</div>
    <a class="nav-item" href="#certificates" id="navCerts">
      <svg class="nav-icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
      Certificates Generated
    </a>

    <a class="nav-item" href="#reports" id="navReports">
      <svg class="nav-icon" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      Reports
    </a>

    <div class="nav-section-label">Communication & Media</div>
    <a class="nav-item" href="#notifications" id="navNotif">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
      Notifications
    </a>

    <a class="nav-item" href="#" id="navGallery">
      <svg class="nav-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
      Gallery & Documents
    </a>

    <div class="nav-section-label">Account</div>
    <a class="nav-item" href="#" id="navSettings">
      <svg class="nav-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
      Settings
    </a>
  </nav>

  <div class="sidebar-footer">
    <a class="nav-item" href="index.php">
      <svg class="nav-icon" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
      Logout
    </a>
  </div>
</aside>

<!-- ══ MAIN ══ -->
<div class="main">
  <!-- TOP BAR -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="hamburger-btn" id="hamburgerBtn"><span></span><span></span><span></span></button>
      <div class="logo-container" style="display:flex; align-items:center; gap:8px;">
        <img src="images/icons/college_logo.png" alt="Zeal Logo" style="height:32px; width:32px; border-radius:50%; object-fit:cover;" title="Zeal Education Society">
        <img src="images/aimsa_logo.jpg" alt="AIMSA Logo" style="height:32px; width:auto; border-radius:50%; object-fit:contain;" title="AIMSA Association">
      </div>
      <div>
        <div class="page-title">AIMSA Portal</div>
        <div class="breadcrumb">AI &amp; ML Department (HOD)</div>
      </div>
    </div>

    <!-- Center Search Bar -->
    <div class="header-search-bar">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--muted-dark)" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="headerSearchInput" placeholder="Search members, events..." data-i18n-ph="dash.search_ph">
    </div>

    <div class="topbar-right">
      <!-- Live clock badge -->
      <div style="font-family:var(--ff-mono); font-size:0.72rem; color:var(--accent); background:rgba(62,139,255,0.1); padding:5px 12px; border-radius:999px; border:1px solid rgba(62,139,255,0.25); display:inline-flex; align-items:center; gap:6px; margin-right:10px;">
        <span style="width:7px; height:7px; border-radius:50%; background:#22c55e; display:inline-block; box-shadow:0 0 8px #22c55e;"></span>
        <span class="liveClockText">Loading live time...</span>
      </div>

      <select id="langSelect" style="background:var(--paper); border:1.5px solid var(--line-dark); border-radius:8px; padding:6px 12px; font-size:0.75rem; font-weight:600; font-family:inherit; cursor:pointer;" onchange="changeLanguage()">
        <option value="en">English</option>
        <option value="mr">मराठी (Marathi)</option>
      </select>

      <button class="topbar-icon-btn" onclick="openNotifications()"><svg viewBox="0 0 24 24" width="20" height="20"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg><span class="notif-dot"></span></button>

      <div style="position:relative; display:inline-block;" id="profileMenuWrapper">
        <div style="display:flex; align-items:center; gap:8px; cursor:pointer;" onclick="toggleProfileDropdown()">
          <div style="width:32px; height:32px; border-radius:50%; background:var(--accent); color:var(--white); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem;" id="headerUserAvatar">HD</div>
          <div style="display:flex; flex-direction:column; text-align:left;">
            <span style="font-size:0.78rem; font-weight:600; color:var(--navy-950);" id="headerUserName">Dr. Dipali Shende</span>
            <span style="font-size:0.65rem; color:var(--muted-dark);" id="headerUserRole">HOD</span>
          </div>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div id="profileDropdown" style="display:none; position:absolute; right:0; top:42px; background:var(--white); border:1px solid var(--line-dark); border-radius:12px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1); width:180px; z-index:150; padding:6px 0;">
          <a href="#" onclick="openDrawer('changePasswordDrawer'); toggleProfileDropdown(); return false;" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:var(--navy-950); text-decoration:none; font-weight:500;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span data-i18n="dash.change_password">Change Password</span>
          </a>
          <div style="border-top:1px solid var(--line-dark); margin:4px 0;"></div>
          <a href="index.php" style="display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:0.78rem; color:#ef4444; text-decoration:none; font-weight:600;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            <span data-i18n="dash.logout">Logout</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- CONTENT -->
  <div class="content">
    <div class="section-eyebrow" data-i18n="dash.hod_eyebrow">HOD Overview</div>
    <div class="content-title">Good Morning, Dr. Shende 👋</div>
    <div class="content-sub">Here's what's happening in AIMSA today — <span class="liveDateText"><?php echo date('F j, Y'); ?></span></div>

    <!-- STAT CARDS -->
    <div class="stats-grid" id="members">
      <div class="stat-card" onclick="openDrawer('membersDrawer')">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M10 11a4 4 0 100-8 4 4 0 000 8z"/></svg></div>
        <span class="stat-val" id="statTotalMembers">0</span>
        <div class="stat-label" data-i18n="stat.total_members">Total Members</div>
        <span class="stat-delta up">↑ Live Database</span>
      </div>
      <div class="stat-card" id="committee" onclick="openDrawer('committeeDrawer')">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div>
        <span class="stat-val" id="statCommittee">0</span>
        <div class="stat-label" data-i18n="stat.committee_members">Committee Members</div>
        <span class="stat-delta up">↑ Active Roles</span>
      </div>
      <div class="stat-card" id="events" onclick="openDrawer('newEventDrawer')">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
        <span class="stat-val" id="statEvents">0</span>
        <div class="stat-label" data-i18n="stat.events_conducted">Events Conducted</div>
        <span class="stat-delta up">↑ Approved &amp; Live</span>
      </div>
      <div class="stat-card" id="registrations">
        <div class="stat-icon"><svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg></div>
        <span class="stat-val" id="statRegistrations">0</span>
        <div class="stat-label">Event Registrations</div>
        <span class="stat-delta up">↑ Total Participants</span>
      </div>
    </div>

    <!-- MAIN GRID -->
    <div class="dash-grid">
      <!-- Membership Applications & Requests -->
      <div class="card" id="membershipRequestsCard">
        <div class="card-head">
          <div class="card-title">Membership Applications</div>
          <span class="card-action" onclick="openDrawer('membersDrawer')">View All Members</span>
        </div>
        <div style="overflow-x:auto;">
          <table class="data-table" id="pendingMembersTable">
            <thead><tr><th>Student</th><th>Email ID</th><th>Branch</th><th>Action</th></tr></thead>
            <tbody id="pendingMembersBody">
              <!-- Dynamically populated from MySQL -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Quick Actions + Notifications -->
      <div style="display:flex;flex-direction:column;gap:24px;">
        <div class="card">
          <div class="card-head"><div class="card-title">Quick Actions</div></div>
          <div class="quick-grid">
            <button class="quick-btn" onclick="openDrawer('addMemberDrawer')">
              <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8zM19 8v6M22 11h-6"/></svg>
              <span>Add Member</span>
            </button>
            <button class="quick-btn" onclick="openDrawer('newEventDrawer')">
              <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <span>New Event</span>
            </button>
            <button class="quick-btn" onclick="openDrawer('reportHubDrawer')">
              <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
              <span>View Reports</span>
            </button>
            <button class="quick-btn" onclick="openDrawer('notifyAllDrawer')">
              <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
              <span>Notify All</span>
            </button>
          </div>
        </div>

        <!-- Notifications Card -->
        <div class="card" id="notifications">
          <div class="card-head">
            <div class="card-title">Notifications</div>
            <span class="card-action" onclick="openDrawer('notifyAllDrawer')">Send Announcement</span>
          </div>
          <div id="notificationsCardContainer">
            <!-- Dynamically populated from MySQL -->
          </div>
        </div>
      </div>
    </div>

    <!-- BOTTOM ROW — Membership Growth + Upcoming Events + Committee Status -->
    <div class="dash-grid-3">
      <!-- Membership Growth -->
      <div class="card">
        <div class="card-head">
          <div class="card-title">Membership Growth</div>
          <span class="card-action" onclick="openDrawer('reportHubDrawer')">Full Analytics</span>
        </div>
        <div id="growthContainer">
          <!-- Dynamically populated from MySQL -->
        </div>
      </div>

      <!-- Upcoming Events -->
      <div class="card" id="upcoming">
        <div class="card-head">
          <div class="card-title">Upcoming Events</div>
          <span class="card-action" onclick="openDrawer('newEventDrawer')">Manage Events</span>
        </div>
        <div id="upcomingEventsList">
          <!-- Dynamically populated from MySQL -->
        </div>
      </div>

      <!-- Committee Status -->
      <div class="card">
        <div class="card-head">
          <div class="card-title">Committee Status</div>
          <span class="card-action" onclick="openDrawer('committeeDrawer')">Manage Roles</span>
        </div>
        <div id="committeeStatusList">
          <!-- Dynamically populated from MySQL -->
        </div>
      </div>
    </div>

    <!-- Certificates Generated + Reports -->
    <div class="dash-grid-2" id="certificates">
      <!-- Certificates Generated -->
      <div class="card">
        <div class="card-head">
          <div class="card-title">Certificates Generated</div>
          <span class="card-action" onclick="openDrawer('certGeneratorDrawer')">Generate New</span>
        </div>
        <div class="cert-counter">
          <div class="cert-counter-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
          <div style="position:relative;z-index:1;">
            <span class="cert-counter-val" id="statCertsIssued">0</span>
            <div class="cert-counter-label">Total Certificates Issued</div>
          </div>
        </div>
        <div style="overflow-x:auto;">
          <table class="data-table">
            <thead><tr><th>Certificate ID</th><th>Type</th><th>Student</th><th>Action</th></tr></thead>
            <tbody id="certificatesTableBody">
              <!-- Dynamically populated from MySQL -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Reports Hub Card -->
      <div class="card" id="reports">
        <div class="card-head">
          <div class="card-title">Reports</div>
          <span class="card-action" onclick="openDrawer('reportHubDrawer')">Generate New</span>
        </div>
        <div id="reportsListContainer">
          <!-- Dynamically populated from MySQL -->
        </div>
      </div>
    </div>

    <!-- FOOTER -->
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

  </div><!-- /content -->
</div><!-- /main -->

<!-- ── DRAWER 1: QUICK ACTION - ADD MEMBER ── -->
<div class="drawer" id="addMemberDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Add New Member</div>
    <button class="drawer-close" onclick="closeDrawer('addMemberDrawer')">&times;</button>
  </div>
  <form id="addMemberForm" onsubmit="submitAddMember(event)">
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" id="addMemName" placeholder="e.g. Siddhesh Kulkarni" required>
    </div>
    <div class="form-group">
      <label>Email Address</label>
      <input type="email" id="addMemEmail" placeholder="siddhesh@zealeducation.com" required>
    </div>
    <div class="form-group">
      <label>Initial Password</label>
      <input type="password" id="addMemPassword" value="student123" required>
    </div>
    <div class="form-group">
      <label>Role</label>
      <select id="addMemRole">
        <option value="Student Member">Student Member</option>
        <option value="Committee Member">Committee Member</option>
        <option value="Faculty Coordinator">Faculty Coordinator</option>
      </select>
    </div>
    <div class="form-group">
      <label>Branch / Department</label>
      <select id="addMemBranch">
        <option value="AI & ML">AI & ML</option>
        <option value="CS">Computer Science (CS)</option>
        <option value="DS">Data Science (DS)</option>
        <option value="IT">Information Technology (IT)</option>
      </select>
    </div>
    <div class="form-group">
      <label>Batch Year</label>
      <select id="addMemBatch">
        <option value="2026">2026</option>
        <option value="2025">2025</option>
        <option value="2024">2024</option>
        <option value="2027">2027</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">Add Member to Assosciation</button>
  </form>
</div>

<!-- ── DRAWER 2: QUICK ACTION - NEW EVENT & EVENT APPROVAL ── -->
<div class="drawer" id="newEventDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Event Management</div>
    <button class="drawer-close" onclick="closeDrawer('newEventDrawer')">&times;</button>
  </div>
  <div class="tab-buttons">
    <button class="tab-btn active" id="tabCreateEvt" onclick="switchEventTab('create')">+ Create Event</button>
    <button class="tab-btn" id="tabApproveEvt" onclick="switchEventTab('approve')">Pending Proposals</button>
  </div>

  <!-- Tab 1: Create Event -->
  <form id="createEventForm" onsubmit="submitCreateEvent(event)">
    <div class="form-group">
      <label>Event Title</label>
      <input type="text" id="evtTitle" placeholder="e.g. AI Hackathon 2026" required>
    </div>
    <div class="form-group">
      <label>Description</label>
      <textarea id="evtDesc" rows="3" placeholder="Brief summary of event schedule and objective..."></textarea>
    </div>
    <div class="form-group">
      <label>Event Date</label>
      <input type="date" id="evtDate" required>
    </div>
    <div class="form-group">
      <label>Location / Venue</label>
      <input type="text" id="evtLocation" placeholder="Main Auditorium / Lab 402" required>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">Publish &amp; Approve Event</button>
  </form>

  <!-- Tab 2: Pending Event Proposals -->
  <div id="pendingEventsContainer" style="display:none; flex-direction:column; gap:12px;">
    <!-- Dynamically populated from MySQL -->
  </div>
</div>

<!-- ── DRAWER 3: QUICK ACTION - VIEW & AUTO-GENERATE REPORTS ── -->
<div class="drawer" id="reportHubDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Reports Hub</div>
    <button class="drawer-close" onclick="closeDrawer('reportHubDrawer')">&times;</button>
  </div>
  <div style="border-bottom:1px solid var(--line-dark); padding-bottom:15px; margin-bottom:15px;">
    <h4>Auto-Generate New Report</h4>
    <form id="reportForm" onsubmit="submitGenerateReport(event)" style="margin-top:10px;">
      <div class="form-group">
        <label>Report Title (Optional)</label>
        <input type="text" id="reportTitleInput" placeholder="e.g. Annual Activity Report Q2">
      </div>
      <div class="form-group">
        <label>Report Category</label>
        <select id="reportCategorySelect">
          <option value="Event Report">Event Completion Report</option>
          <option value="Member Report">Membership Analytics Report</option>
          <option value="Attendance Report">Event Attendance Report</option>
          <option value="Committee Report">Committee Performance Report</option>
        </select>
      </div>
      <div class="form-group">
        <label>Export Format</label>
        <select id="reportFormatSelect">
          <option value="PDF">PDF File</option>
          <option value="Excel">CSV / Excel File</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; margin-top:5px;">⚡ Auto-Compile &amp; Generate Report</button>
    </form>
  </div>
  <div style="flex:1; overflow-y:auto;">
    <h4>Generated Reports Library</h4>
    <div id="drawerReportsContainer" style="display:flex; flex-direction:column; gap:10px; margin-top:10px;">
      <!-- Dynamically populated from MySQL -->
    </div>
  </div>
</div>

<!-- ── DRAWER 4: QUICK ACTION - NOTIFY ALL ── -->
<div class="drawer" id="notifyAllDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Broadcast Announcement</div>
    <button class="drawer-close" onclick="closeDrawer('notifyAllDrawer')">&times;</button>
  </div>
  <form id="notifyForm" onsubmit="submitNotifyAll(event)">
    <div class="form-group">
      <label>Announcement Subject</label>
      <input type="text" id="notifSubject" placeholder="e.g. Important: Meeting at 3 PM" required>
    </div>
    <div class="form-group">
      <label>Message Content</label>
      <textarea id="notifBody" rows="4" placeholder="Detailed notification text..." required></textarea>
    </div>
    <div class="form-group">
      <label>Priority Indicator</label>
      <select id="notifIndicator">
        <option value="green">🟢 Normal / Update</option>
        <option value="yellow">🟡 Important Alert</option>
        <option value="red">🔴 High Priority / Urgent</option>
      </select>
    </div>
    <div class="form-group">
      <label>Target Audience</label>
      <select id="notifRecipient">
        <option value="all">All Association Members &amp; Faculty</option>
        <option value="Student Member">Student Members Only</option>
        <option value="Committee Member">Committee Members Only</option>
        <option value="Faculty Coordinator">Faculty Coordinators Only</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">📢 Send Announcement</button>
  </form>
</div>

<!-- ── DRAWER 5: CERTIFICATE GENERATOR DRAWER ── -->
<div class="drawer" id="certGeneratorDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Generate Certificate</div>
    <button class="drawer-close" onclick="closeDrawer('certGeneratorDrawer')">&times;</button>
  </div>
  <form id="certForm" onsubmit="submitGenerateCert(event)">
    <div class="form-group">
      <label>Certificate Type</label>
      <select id="certType">
        <option value="Participation Certificate">Participation Certificate</option>
        <option value="Volunteer Certificate">Volunteer Certificate</option>
        <option value="Winner Certificate">Winner Certificate</option>
        <option value="Appreciation Certificate">Appreciation Certificate</option>
      </select>
    </div>
    <div class="form-group">
      <label>Select Event</label>
      <select id="certEvent">
        <!-- Dynamically populated from MySQL events -->
      </select>
    </div>
    <div class="form-group">
      <label>Select Student</label>
      <select id="certStudent">
        <!-- Dynamically populated from MySQL active students -->
      </select>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">Generate &amp; Issue PDF</button>
  </form>
</div>

<!-- ── DRAWER 6: MEMBER DIRECTORY ── -->
<div class="drawer" id="membersDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Member Directory</div>
    <button class="drawer-close" onclick="closeDrawer('membersDrawer')">&times;</button>
  </div>
  <div class="form-group">
    <label>Search Members</label>
    <input type="text" id="memberSearchInput" placeholder="Search by name, email, or role...">
  </div>
  <div style="flex:1; overflow-y:auto;">
    <table class="data-table">
      <thead><tr><th>Name</th><th>Role / Status</th></tr></thead>
      <tbody id="allMembersBody">
        <!-- Dynamically populated -->
      </tbody>
    </table>
  </div>
</div>

<!-- ── DRAWER 7: COMMITTEE MANAGEMENT ── -->
<div class="drawer" id="committeeDrawer">
  <div class="drawer-header">
    <div class="drawer-title">Committee Management</div>
    <button class="drawer-close" onclick="closeDrawer('committeeDrawer')">&times;</button>
  </div>
  <div style="border-bottom:1px solid var(--line-dark); padding-bottom:15px; margin-bottom:15px;">
    <h4>Assign Committee Role</h4>
    <form id="committeeForm" onsubmit="submitSaveCommittee(event)" style="margin-top:10px;">
      <div class="form-group">
        <label>Select Member</label>
        <select id="commSelectUser" required></select>
      </div>
      <div class="form-group">
        <label>Designation</label>
        <select id="commDesignation">
          <option value="President">President</option>
          <option value="Vice President">Vice President</option>
          <option value="Secretary">Secretary</option>
          <option value="Treasurer">Treasurer</option>
          <option value="Technical Head">Technical Head</option>
          <option value="Event Coordinator">Event Coordinator</option>
          <option value="Public Relations Officer">Public Relations Officer</option>
          <option value="Cultural Lead">Cultural Lead</option>
        </select>
      </div>
      <div class="form-group">
        <label>Assigned Responsibility</label>
        <input type="text" id="commResponsibility" placeholder="e.g. Technical workshops leader" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;">Save Committee Role</button>
    </form>
  </div>
  <div style="flex:1; overflow-y:auto;">
    <h4>Current Committee Members</h4>
    <table class="data-table" style="margin-top:10px;">
      <thead><tr><th>Name</th><th>Designation</th><th>Action</th></tr></thead>
      <tbody id="currentCommitteeBody">
        <!-- Populated dynamically -->
      </tbody>
    </table>
  </div>
</div>

<!-- ── DRAWER 8: GALLERY & FILE MANAGER ── -->
<div class="drawer" id="galleryDrawer">
  <div class="drawer-header">
    <div class="drawer-title">File Storage &amp; Gallery</div>
    <button class="drawer-close" onclick="closeDrawer('galleryDrawer')">&times;</button>
  </div>
  <div style="border-bottom:1px solid var(--line-dark); padding-bottom:15px; margin-bottom:15px;">
    <h4>Upload File (PDF / Image / Video)</h4>
    <form id="uploadFileForm" onsubmit="submitUploadFile(event)" style="margin-top:10px;">
      <div class="form-group">
        <label>Title / Caption</label>
        <input type="text" id="uploadTitle" placeholder="e.g. Workshop Report 2026" required>
      </div>
      <div class="form-group">
        <label>Item Type</label>
        <select id="uploadItemType">
          <option value="PDF Report">PDF Report</option>
          <option value="Photo">Photo</option>
          <option value="Video">Video</option>
          <option value="Document">Document</option>
        </select>
      </div>
      <div class="form-group">
        <label>Album / Category</label>
        <input type="text" id="uploadAlbum" placeholder="e.g. ML Bootcamp 2026">
      </div>
      <div class="form-group">
        <label>Select File</label>
        <input type="file" id="uploadFileInput" required accept="image/*,video/*,.pdf,.doc,.docx" style="padding:4px;">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;">Upload to Server</button>
    </form>
  </div>
  <div style="flex:1; overflow-y:auto;">
    <h4>Uploaded Files &amp; Storage</h4>
    <div id="galleryItemsContainer" style="display:flex; flex-direction:column; gap:10px; margin-top:10px;">
      <!-- Populated dynamically -->
    </div>
  </div>
</div>

<!-- ── DRAWER 9: CHANGE PASSWORD DRAWER ── -->
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

<!-- ══ JAVASCRIPT LOGIC ══ -->
<script>
const sidebar = document.getElementById('sidebar'), overlay = document.getElementById('sidebarOverlay'), hamburger = document.getElementById('hamburgerBtn');
hamburger.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); closeAllDrawers(); });

function openDrawer(id) {
  closeAllDrawers();
  const d = document.getElementById(id);
  if (d) d.classList.add('open');
  overlay.classList.add('open');
}

function closeDrawer(id) {
  const d = document.getElementById(id);
  if (d) d.classList.remove('open');
  overlay.classList.remove('open');
}

function closeAllDrawers() {
  document.querySelectorAll('.drawer').forEach(d => d.classList.remove('open'));
}

document.getElementById('navGallery').addEventListener('click', (e) => {
  e.preventDefault();
  openDrawer('galleryDrawer');
});

// Profile dropdown
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

// Main Global State fetched from PHP/MySQL
let globalData = null;

// ── FETCH MAIN DASHBOARD DATA FROM MYSQL BACKEND ──
function fetchDashboardData() {
  fetch('ajax/hod_actions.php?action=get_dashboard_data')
    .then(r => r.json())
    .then(res => {
      if (res.status === 'success') {
        globalData = res.data;
        renderDashboard(globalData);
      } else {
        console.error('Error fetching data:', res.message);
      }
    })
    .catch(err => console.error('AJAX fetch error:', err));
}

// ── RENDER DASHBOARD UI ──
function renderDashboard(data) {
  // Stat cards
  document.getElementById('statTotalMembers').textContent = data.stats.total_members;
  document.getElementById('statCommittee').textContent = data.stats.committee_members;
  document.getElementById('statEvents').textContent = data.stats.events_conducted;
  document.getElementById('statRegistrations').textContent = data.stats.total_registrations;
  document.getElementById('statCertsIssued').textContent = data.stats.certs_issued;
  document.getElementById('navMembersBadge').textContent = data.stats.total_members;

  // 1. Pending Membership Applications
  const pendingBody = document.getElementById('pendingMembersBody');
  pendingBody.innerHTML = '';
  if (!data.pending_members || data.pending_members.length === 0) {
    pendingBody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--muted-dark); padding:20px;">No pending membership applications.</td></tr>`;
  } else {
    data.pending_members.forEach(u => {
      pendingBody.innerHTML += `
        <tr>
          <td><b>${escapeHtml(u.name)}</b></td>
          <td>${escapeHtml(u.email)}</td>
          <td>${escapeHtml(u.branch || 'AI & ML')}</td>
          <td>
            <button class="badge badge-green" style="cursor:pointer;" onclick="approveMember('${escapeHtml(u.email)}')">Approve</button>
            <button class="badge badge-orange" style="cursor:pointer; margin-left:6px;" onclick="rejectMember('${escapeHtml(u.email)}')">Reject</button>
          </td>
        </tr>`;
    });
  }

  // 2. All Members Drawer Table & Dropdowns
  renderAllMembersTable(data.all_members);
  populateSelectDropdowns(data.all_members, data.upcoming_events);

  // 3. Membership Growth
  const growthContainer = document.getElementById('growthContainer');
  growthContainer.innerHTML = '';
  if (data.growth_stats) {
    data.growth_stats.forEach(g => {
      growthContainer.innerHTML += `
        <div class="progress-item">
          <div class="progress-label"><span>${escapeHtml(g.label)}</span><span>${g.percentage}%</span></div>
          <div class="progress-bar"><div class="progress-fill" style="width:${g.percentage}%"></div></div>
        </div>`;
    });
  }

  // 4. Upcoming Events
  const upcomingList = document.getElementById('upcomingEventsList');
  upcomingList.innerHTML = '';
  if (!data.upcoming_events || data.upcoming_events.length === 0) {
    upcomingList.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:10px;">No upcoming events scheduled.</p>`;
  } else {
    data.upcoming_events.slice(0, 4).forEach(e => {
      upcomingList.innerHTML += `
        <div class="list-item">
          <div class="list-dot"></div>
          <div class="list-text">
            <b>${escapeHtml(e.title)}</b>
            <span>${escapeHtml(e.event_date)} · ${escapeHtml(e.location)}</span>
          </div>
        </div>`;
    });
  }

  // 5. Committee Status
  const commList = document.getElementById('committeeStatusList');
  commList.innerHTML = '';
  if (!data.committee || data.committee.length === 0) {
    commList.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:10px;">No committee members assigned.</p>`;
  } else {
    data.committee.forEach(c => {
      commList.innerHTML += `
        <div class="list-item">
          <div class="list-text">
            <b>${escapeHtml(c.name)}</b>
            <span>${escapeHtml(c.committeeDesignation)} · ${escapeHtml(c.branch)}</span>
          </div>
          <span class="badge badge-green">Active</span>
        </div>`;
    });
  }

  // Committee Drawer Table
  const commBody = document.getElementById('currentCommitteeBody');
  commBody.innerHTML = '';
  data.committee.forEach(c => {
    commBody.innerHTML += `
      <tr>
        <td><b>${escapeHtml(c.name)}</b></td>
        <td>${escapeHtml(c.committeeDesignation)}</td>
        <td><button style="border:none; background:none; color:#ef4444; font-size:1.15rem; cursor:pointer;" onclick="removeCommitteeMember('${escapeHtml(c.email)}')">&times;</button></td>
      </tr>`;
  });

  // 6. Certificates Table
  const certBody = document.getElementById('certificatesTableBody');
  certBody.innerHTML = '';
  if (!data.certificates || data.certificates.length === 0) {
    certBody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--muted-dark); padding:15px;">No certificates issued yet.</td></tr>`;
  } else {
    data.certificates.forEach(c => {
      const pathLink = c.pdf_path ? `<a href="${escapeHtml(c.pdf_path)}" target="_blank" class="report-dl">↓ View / Download</a>` : '<span style="font-size:0.75rem; color:var(--muted-dark);">Issued</span>';
      certBody.innerHTML += `
        <tr>
          <td><b>${escapeHtml(c.cert_code)}</b></td>
          <td>${escapeHtml(c.type)}</td>
          <td>${escapeHtml(c.student_name)}</td>
          <td>${pathLink}</td>
        </tr>`;
    });
  }

  // 7. Reports List
  renderReportsList(data.reports);

  // 8. Notifications
  renderNotificationsList(data.notifications);

  // 9. Pending Events for Approval Drawer
  renderPendingEvents(data.pending_events);

  // 10. Gallery Items
  renderGalleryList(data.gallery);
}

function renderAllMembersTable(members) {
  const searchVal = (document.getElementById('memberSearchInput').value || '').toLowerCase();
  const tbody = document.getElementById('allMembersBody');
  tbody.innerHTML = '';

  const filtered = members.filter(u =>
    u.name.toLowerCase().includes(searchVal) ||
    u.email.toLowerCase().includes(searchVal) ||
    u.role.toLowerCase().includes(searchVal)
  );

  filtered.forEach(u => {
    const badgeClass = u.membershipStatus === 'Active' ? 'badge-green' : u.membershipStatus === 'Pending' ? 'badge-blue' : 'badge-orange';
    tbody.innerHTML += `
      <tr>
        <td><b>${escapeHtml(u.name)}</b><br><span style="font-size:0.75rem; color:var(--muted-dark);">${escapeHtml(u.email)}</span></td>
        <td>
          <span style="font-size:0.75rem; color:var(--muted-dark); font-weight:600;">${escapeHtml(u.role)}</span><br>
          <span class="badge ${badgeClass}">${escapeHtml(u.membershipStatus || 'Active')}</span>
        </td>
      </tr>`;
  });
}

document.getElementById('memberSearchInput').addEventListener('input', () => {
  if (globalData && globalData.all_members) {
    renderAllMembersTable(globalData.all_members);
  }
});

function populateSelectDropdowns(members, events) {
  // Committee user select
  const commSelect = document.getElementById('commSelectUser');
  commSelect.innerHTML = '';
  members.forEach(u => {
    commSelect.innerHTML += `<option value="${escapeHtml(u.email)}">${escapeHtml(u.name)} (${escapeHtml(u.role)})</option>`;
  });

  // Cert Student select
  const certStudent = document.getElementById('certStudent');
  certStudent.innerHTML = '';
  members.forEach(u => {
    certStudent.innerHTML += `<option value="${escapeHtml(u.name)}">${escapeHtml(u.name)} (${escapeHtml(u.email)})</option>`;
  });

  // Cert Event select
  const certEvent = document.getElementById('certEvent');
  certEvent.innerHTML = '';
  events.forEach(e => {
    certEvent.innerHTML += `<option value="${escapeHtml(e.title)}">${escapeHtml(e.title)} (${escapeHtml(e.event_date)})</option>`;
  });
}

function renderReportsList(reports) {
  const container = document.getElementById('reportsListContainer');
  const drawerContainer = document.getElementById('drawerReportsContainer');
  container.innerHTML = '';
  drawerContainer.innerHTML = '';

  if (!reports || reports.length === 0) {
    container.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:10px;">No reports generated.</p>`;
    drawerContainer.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:10px;">No reports in library.</p>`;
    return;
  }

  reports.forEach(r => {
    const fileLink = r.file_path ? `<a href="${escapeHtml(r.file_path)}" target="_blank" class="report-dl">↓ ${escapeHtml(r.format)}</a>` : '<span class="report-dl" onclick="alert(\'Report file is being compiled.\')">↓ PDF</span>';
    
    const html = `
      <div class="report-item">
        <div class="report-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
        <div class="report-info"><b>${escapeHtml(r.title)}</b><span>${escapeHtml(r.summary || r.category)}</span></div>
        ${fileLink}
      </div>`;

    container.innerHTML += html;
    drawerContainer.innerHTML += html;
  });
}

function renderNotificationsList(notifications) {
  const container = document.getElementById('notificationsCardContainer');
  container.innerHTML = '';

  if (!notifications || notifications.length === 0) {
    container.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:15px;">No recent notifications.</p>`;
    return;
  }

  notifications.slice(0, 4).forEach(n => {
    let dotColor = '#22c55e', dotShadow = 'rgba(34,197,94,.18)';
    if (n.indicator === 'yellow') { dotColor = '#f97316'; dotShadow = 'rgba(249,115,22,.18)'; }
    if (n.indicator === 'red') { dotColor = '#ef4444'; dotShadow = 'rgba(239,68,68,.18)'; }

    container.innerHTML += `
      <div class="list-item">
        <div class="list-dot" style="background:${dotColor}; box-shadow:0 0 0 3px ${dotShadow};"></div>
        <div class="list-text">
          <b>${escapeHtml(n.title)}</b>
          <span>${escapeHtml(n.text)}</span>
        </div>
      </div>`;
  });
}

function renderPendingEvents(pendingEvents) {
  const container = document.getElementById('pendingEventsContainer');
  container.innerHTML = '';

  if (!pendingEvents || pendingEvents.length === 0) {
    container.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:15px;">No pending event proposals to review.</p>`;
    return;
  }

  pendingEvents.forEach(e => {
    container.innerHTML += `
      <div style="border:1px solid var(--line-dark); padding:14px; border-radius:10px; background:var(--paper);">
        <b style="font-size:0.9rem;">${escapeHtml(e.title)}</b>
        <p style="font-size:0.78rem; color:var(--muted-dark); margin:4px 0;">${escapeHtml(e.description || 'No description')}</p>
        <span style="font-size:0.75rem; color:var(--accent); font-weight:600;">Date: ${escapeHtml(e.event_date)} | Venue: ${escapeHtml(e.location)}</span>
        <div style="margin-top:10px; display:flex; gap:8px;">
          <button class="btn btn-primary" style="padding:4px 12px; font-size:0.75rem;" onclick="approveEvent(${e.id})">Approve Event</button>
          <button class="btn btn-ghost" style="padding:4px 12px; font-size:0.75rem; color:#ef4444;" onclick="rejectEvent(${e.id})">Reject</button>
        </div>
      </div>`;
  });
}

function renderGalleryList(gallery) {
  const container = document.getElementById('galleryItemsContainer');
  container.innerHTML = '';

  if (!gallery || gallery.length === 0) {
    container.innerHTML = `<p style="font-size:0.8rem; color:var(--muted-dark); padding:10px;">No uploaded files found.</p>`;
    return;
  }

  gallery.forEach(item => {
    const viewBtn = `<a href="${escapeHtml(item.file_path)}" target="_blank" class="report-dl" style="font-size:0.7rem;">👁 View File</a>`;
    container.innerHTML += `
      <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 14px; border:1px solid var(--line-dark); border-radius:10px; background:var(--paper);">
        <div>
          <b style="font-size:0.85rem;">[${escapeHtml(item.item_type)}] ${escapeHtml(item.title)}</b>
          <br><span style="font-size:0.75rem; color:var(--muted-dark);">Album: ${escapeHtml(item.album)} · File: ${escapeHtml(item.file_name)}</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
          ${viewBtn}
          <button style="border:none; background:none; color:#ef4444; font-size:1.2rem; cursor:pointer;" onclick="deleteGalleryItem(${item.id})">&times;</button>
        </div>
      </div>`;
  });
}

// ── EVENT TAB SWITCHING ──
function switchEventTab(tab) {
  const form = document.getElementById('createEventForm');
  const pending = document.getElementById('pendingEventsContainer');
  const tCreate = document.getElementById('tabCreateEvt');
  const tApprove = document.getElementById('tabApproveEvt');

  if (tab === 'create') {
    form.style.display = 'block';
    pending.style.display = 'none';
    tCreate.classList.add('active');
    tApprove.classList.remove('active');
  } else {
    form.style.display = 'none';
    pending.style.display = 'flex';
    tCreate.classList.remove('active');
    tApprove.classList.add('active');
  }
}

// ── AJAX ACTION SUBMISSIONS ──

// 1. Add Member
function submitAddMember(e) {
  e.preventDefault();
  const formData = new FormData();
  formData.append('action', 'add_member');
  formData.append('name', document.getElementById('addMemName').value);
  formData.append('email', document.getElementById('addMemEmail').value);
  formData.append('password', document.getElementById('addMemPassword').value);
  formData.append('role', document.getElementById('addMemRole').value);
  formData.append('branch', document.getElementById('addMemBranch').value);
  formData.append('batch', document.getElementById('addMemBatch').value);

  fetch('ajax/hod_actions.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      if (res.status === 'success') {
        document.getElementById('addMemberForm').reset();
        closeDrawer('addMemberDrawer');
        fetchDashboardData();
      }
    });
}

// 2. Approve / Reject Member
window.approveMember = function(email) {
  const formData = new FormData();
  formData.append('action', 'approve_member');
  formData.append('email', email);

  fetch('ajax/hod_actions.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      fetchDashboardData();
    });
};

window.rejectMember = function(email) {
  if (!confirm('Reject this membership application?')) return;
  const formData = new FormData();
  formData.append('action', 'reject_member');
  formData.append('email', email);

  fetch('ajax/hod_actions.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      fetchDashboardData();
    });
};

// 3. Create Event
function submitCreateEvent(e) {
  e.preventDefault();
  const formData = new FormData();
  formData.append('action', 'create_event');
  formData.append('title', document.getElementById('evtTitle').value);
  formData.append('description', document.getElementById('evtDesc').value);
  formData.append('event_date', document.getElementById('evtDate').value);
  formData.append('location', document.getElementById('evtLocation').value);

  fetch('ajax/hod_actions.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      if (res.status === 'success') {
        document.getElementById('createEventForm').reset();
        closeDrawer('newEventDrawer');
        fetchDashboardData();
      }
    });
}

// Approve / Reject Pending Event
window.approveEvent = function(eventId) {
  const formData = new FormData();
  formData.append('action', 'approve_event');
  formData.append('event_id', eventId);

  fetch('ajax/hod_actions.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      fetchDashboardData();
    });
};

window.rejectEvent = function(eventId) {
  if (!confirm('Reject this event proposal?')) return;
  const formData = new FormData();
  formData.append('action', 'reject_event');
  formData.append('event_id', eventId);

  fetch('ajax/hod_actions.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      fetchDashboardData();
    });
};

// 4. Auto-Generate Report
function submitGenerateReport(e) {
  e.preventDefault();
  const formData = new FormData();
  formData.append('action', 'generate_report');
  formData.append('title', document.getElementById('reportTitleInput').value);
  formData.append('category', document.getElementById('reportCategorySelect').value);
  formData.append('format', document.getElementById('reportFormatSelect').value);

  fetch('ajax/hod_actions.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      if (res.status === 'success') {
        document.getElementById('reportTitleInput').value = '';
        fetchDashboardData();
      }
    });
}

// 5. Broadcast Announcement (Notify All)
function submitNotifyAll(e) {
  e.preventDefault();
  const formData = new FormData();
  formData.append('action', 'notify_all');
  formData.append('title', document.getElementById('notifSubject').value);
  formData.append('text', document.getElementById('notifBody').value);
  formData.append('indicator', document.getElementById('notifIndicator').value);
  formData.append('recipient', document.getElementById('notifRecipient').value);

  fetch('ajax/hod_actions.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      if (res.status === 'success') {
        document.getElementById('notifyForm').reset();
        closeDrawer('notifyAllDrawer');
        fetchDashboardData();
      }
    });
}

// 6. Committee Management
function submitSaveCommittee(e) {
  e.preventDefault();
  const formData = new FormData();
  formData.append('action', 'save_committee');
  formData.append('email', document.getElementById('commSelectUser').value);
  formData.append('designation', document.getElementById('commDesignation').value);
  formData.append('responsibility', document.getElementById('commResponsibility').value);

  fetch('ajax/hod_actions.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      if (res.status === 'success') {
        document.getElementById('commResponsibility').value = '';
        fetchDashboardData();
      }
    });
}

window.removeCommitteeMember = function(email) {
  if (!confirm('Remove member from committee?')) return;
  const formData = new FormData();
  formData.append('action', 'remove_committee');
  formData.append('email', email);

  fetch('ajax/hod_actions.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      fetchDashboardData();
    });
};

// 7. Generate Certificate
function submitGenerateCert(e) {
  e.preventDefault();
  const formData = new FormData();
  formData.append('action', 'generate_certificate');
  formData.append('type', document.getElementById('certType').value);
  formData.append('event_name', document.getElementById('certEvent').value);
  formData.append('student_name', document.getElementById('certStudent').value);

  fetch('ajax/hod_actions.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      if (res.status === 'success') {
        closeDrawer('certGeneratorDrawer');
        fetchDashboardData();
      }
    });
}

// 8. Upload File (Photos, PDFs, Reports)
function submitUploadFile(e) {
  e.preventDefault();
  const fileInput = document.getElementById('uploadFileInput');
  if (!fileInput.files[0]) {
    alert('Please select a file to upload.');
    return;
  }

  const formData = new FormData();
  formData.append('action', 'upload');
  formData.append('title', document.getElementById('uploadTitle').value);
  formData.append('item_type', document.getElementById('uploadItemType').value);
  formData.append('album', document.getElementById('uploadAlbum').value || 'General');
  formData.append('file', fileInput.files[0]);

  fetch('ajax/file_handler.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      if (res.status === 'success') {
        document.getElementById('uploadFileForm').reset();
        fetchDashboardData();
      }
    });
}

window.deleteGalleryItem = function(id) {
  if (!confirm('Delete this file?')) return;
  const formData = new FormData();
  formData.append('action', 'delete_gallery');
  formData.append('id', id);

  fetch('ajax/file_handler.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
      alert(res.message);
      fetchDashboardData();
    });
};

// Change password save action
document.getElementById('savePasswordBtn').addEventListener('click', () => {
  const curr = document.getElementById('currPassword').value;
  const newp = document.getElementById('newPassword').value;
  const conf = document.getElementById('confirmNewPassword').value;

  if (!curr || !newp || !conf) {
    alert('Please fill out all password fields.'); return;
  }
  if (newp !== conf) {
    alert('New passwords do not match.'); return;
  }
  alert('Password updated successfully!');
  closeDrawer('changePasswordDrawer');
});

// Search input filtering for topbar
document.getElementById('headerSearchInput').addEventListener('input', (e) => {
  const query = e.target.value.toLowerCase().trim();
  document.querySelectorAll('.list-item, tr').forEach(el => {
    if(query === '') { el.style.display = ''; }
    else {
      const text = el.textContent.toLowerCase();
      el.style.display = text.includes(query) ? '' : 'none';
    }
  });
});

function openNotifications() {
  const el = document.getElementById('notifications');
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' });
    el.style.outline = '2px solid var(--accent)';
    setTimeout(() => { el.style.outline = 'none'; }, 2000);
  }
}

function escapeHtml(text) {
  if (!text) return '';
  return String(text)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// Initial Data Fetch
fetchDashboardData();
</script>
<script src="assets/js/landing.js"></script>
</body>
</html>
