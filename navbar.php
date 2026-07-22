<!-- ============ NAV ============ -->
<header class="nav">
  <div class="container nav-inner" style="display:flex; align-items:center; justify-content:space-between; gap:16px;">
    <div class="brand" style="flex-shrink:0;">
      <div class="logos">
        <!-- College Logo -->
        <img src="images/icons/college_logo.png" alt="Zeal Logo" style="height:32px; width:32px; border-radius:50%; object-fit:cover;" title="Zeal Education Society">
        <!-- AIML Dept Logo -->
        <img src="images/aimsa_logo.jpg" alt="AIMSA Logo" style="height:32px; width:auto; border-radius:50%; object-fit:contain;" title="AIMSA Association">
      </div>
      <div class="brand-text">
        <b>AIMSA</b>
        <span>AI &amp; ML Student Assoc.</span>
      </div>
    </div>
    <nav class="links" style="display:flex; align-items:center; gap:16px;">
      <a href="#about" data-i18n="nav.about">About</a>
      <a href="#committee" data-i18n="nav.committee">Committee</a>
      <a href="#meetings" data-i18n="nav.meetings">Meetings</a>
      <a href="#achievements" data-i18n="nav.achievements">Achievements</a>
      <a href="#gallery" data-i18n="nav.gallery">Gallery</a>
      <a href="#announcements" data-i18n="nav.announcements">Announcements</a>
      <a href="#contact" data-i18n="nav.contact">Contact</a>
    </nav>
    <div style="display:flex; align-items:center; gap:12px; flex-shrink:0; white-space:nowrap;">
      <select id="langSelect" style="background:rgba(255,255,255,0.12); color:var(--white); border:1px solid rgba(255,255,255,0.3); border-radius:8px; padding:5px 10px; font-size:0.75rem; font-weight:600; cursor:pointer; outline:none;" onchange="changeLanguage()">
        <option value="en" style="color:#000;">English</option>
        <option value="mr" style="color:#000;">मराठी (Marathi)</option>
      </select>
      <div style="font-family:var(--ff-mono, monospace); font-size:0.75rem; color:var(--accent-soft, #7fb0ff); background:rgba(62,139,255,0.12); padding:5px 12px; border-radius:999px; border:1px solid rgba(62,139,255,0.25); display:inline-flex; align-items:center; gap:6px; white-space:nowrap;">
        <span style="width:7px; height:7px; border-radius:50%; background:#22c55e; display:inline-block; box-shadow:0 0 8px #22c55e;"></span>
        <span class="liveClockText">Loading live time...</span>
      </div>
      <button class="hamburger" id="hamburgerBtn" aria-label="Menu"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>
