<?php
include_once __DIR__ . '/include/header.php';
include_once __DIR__ . '/navbar.php';
?>

<!-- ============ HERO ============ -->
<section class="hero">
  <div class="glow-sphere glow-sphere-1"></div>
  <div class="glow-sphere glow-sphere-2"></div>
  <div class="node-field" id="nodeField"></div>
  <div class="container hero-inner">
    <div class="hero-logos">
      <div class="logo-badge">
        <img src="images/icons/college_logo.png" alt="Zeal Education Society">
      </div>
      <span class="plus">+</span>
      <div class="logo-badge">
        <img src="images/aimsa_logo.jpg" alt="AIMSA Association">
      </div>
    </div>
    <span class="tagline" data-i18n="hero.tagline">ZEAL EDUCATION SOCIETY — DEPT. OF AI &amp; ML</span>
    <h1 class="shimmer-text" data-i18n="hero.title">Welcome to the AIMSA Student Portal</h1>
    <p class="lead" data-i18n="hero.lead">One home for every student, committee member, faculty coordinator and administrator of the AI &amp; ML Student Association — meetings, achievements, announcements and more, all in one place.</p>
    <div class="hero-ctas">
      <button class="btn btn-primary" id="openLoginBtn2" data-i18n="hero.login_btn">Login to Portal →</button>
      <a href="#about" class="btn btn-ghost" data-i18n="hero.explore_btn">Explore the Association</a>
    </div>
    <div class="hero-stats reveal-zoom">
      <div class="stat"><b class="counter-num" data-target="120">0</b><b>+</b><span data-i18n="hero.stat.members">Active Members</span></div>
      <div class="stat"><b class="counter-num" data-target="18">0</b><span data-i18n="hero.stat.events">Events Hosted</span></div>
      <div class="stat"><b class="counter-num" data-target="9">0</b><span data-i18n="hero.stat.roles">Committee Roles</span></div>
      <div class="stat"><b class="counter-num" data-target="1996">0</b><span data-i18n="hero.stat.est">Institute Est.</span></div>
    </div>
  </div>
</section>

<!-- ============ ABOUT ============ -->
<section class="about" id="about">
  <div class="container">
    <div class="about-grid">
      <div class="about-text reveal-left">
        <span class="section-tag">About Us</span>
        <h2 style="margin-bottom:20px; color:var(--navy-950);">Who We Are</h2>
        <p>The Artificial Intelligence &amp; Machine Learning Student Association (AIMSA) is the official student body of the AIML Department, bringing together students, faculty coordinators and industry mentors to build a thriving technical community on campus.</p>
        <p>From workshops and hackathons to research circles and industry meet-ups, AIMSA exists to turn curiosity about intelligent systems into real skill, real projects and real impact — for our students and for society.</p>
        <a href="#contact" class="btn btn-dark" style="margin-top:10px;">Get in touch</a>
      </div>
      <div class="vm-stack reveal-right">
        <div class="vm-card">
          <span class="mark">Vision</span>
          <p>To be a premier institute in technical education by imparting academic excellence, research, social, and entrepreneurial attitude.</p>
        </div>
        <div class="vm-card">
          <span class="mark">Mission</span>
          <p>The institute focuses on achieving academic excellence, fostering a research culture for societal needs, promoting community engagement, and encouraging entrepreneurship.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ EXECUTIVE COMMITTEE ============ -->
<section class="section-dark" id="committee">
  <div class="glow-sphere glow-sphere-3"></div>
  <div class="node-field" id="nodeField2"></div>
  <div class="container">
    <div class="section-head on-dark reveal">
      <span class="section-tag on-dark">Executive Committee</span>
      <h2>Leading This Year's Chapter</h2>
      <p>Meet the students steering AIMSA's events, outreach and technical direction this academic year.</p>
    </div>
    <div class="card-grid reveal">
      <div class="pop-card">
        <div class="avatar-ring"><div class="in">HD</div></div>
        <span class="role">HOD</span>
        <h4>Head of Department</h4>
        <p>Oversees the department and provides strategic direction and approval for AIMSA's activities.</p>
      </div>
      <div class="pop-card">
        <div class="avatar-ring"><div class="in">FC</div></div>
        <span class="role">Faculty Coordinator</span>
        <h4>Faculty Coordinator</h4>
        <p>Guides the committee, approves activities and liaises with the department.</p>
      </div>
      <div class="pop-card">
        <div class="avatar-ring"><div class="in">AP</div></div>
        <span class="role">President</span>
        <h4>Association President</h4>
        <p>Sets the annual roadmap and represents AIMSA to the department and college administration.</p>
      </div>
      <div class="pop-card">
        <div class="avatar-ring"><div class="in">VP</div></div>
        <span class="role">Vice President</span>
        <h4>Vice President</h4>
        <p>Coordinates cross-committee activities and steps in to lead events and reviews.</p>
      </div>
      <div class="pop-card">
        <div class="avatar-ring"><div class="in">CH</div></div>
        <span class="role">Cultural Head</span>
        <h4>Cultural Head</h4>
        <p>Plans cultural events, celebrations and student engagement activities.</p>
      </div>
      <div class="pop-card">
        <div class="avatar-ring"><div class="in">TH</div></div>
        <span class="role">Technical Head</span>
        <h4>Technical Head</h4>
        <p>Leads workshops, hackathons and technical project tracks for members.</p>
      </div>
      <div class="pop-card">
        <div class="avatar-ring"><div class="in">SM</div></div>
        <span class="role">Social Media Head</span>
        <h4>Social Media Head</h4>
        <p>Owns AIMSA's photography, design and social media presence.</p>
      </div>
      <div class="pop-card">
        <div class="avatar-ring"><div class="in">SH</div></div>
        <span class="role">Sports Head</span>
        <h4>Sports Head</h4>
        <p>Organizes sports events and manages participation in inter-college tournaments.</p>
      </div>
      <div class="pop-card">
        <div class="avatar-ring"><div class="in">TR</div></div>
        <span class="role">Treasurer</span>
        <h4>Treasurer</h4>
        <p>Manages the association's budget, sponsorships and event funding.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ MEETINGS & EVENTS ============ -->
<section class="meetings" id="meetings">
  <div class="container">
    <div class="section-head reveal">
      <span class="section-tag">Upcoming Events &amp; Meetings</span>
      <h2 style="color:var(--navy-950);">What's Next on the Calendar</h2>
      <p>Committee syncs, workshops and general body meetings — click any event to view complete details &amp; description.</p>
    </div>
    <div class="meet-list reveal" id="landingMeetingsList">
      <div class="meet-row" style="cursor:pointer;" onclick="showEventDetailsFromElement(this)"
           data-title="General Body Meeting — Semester Kickoff" 
           data-desc="Welcome all AIMSA department members to the official semester kickoff general body meeting. Agenda includes annual roadmap, event calendar reveal, and committee introductions." 
           data-date="<?php echo date('M d, Y', strtotime('+2 days')); ?>" 
           data-time="4:00 PM" 
           data-venue="AIML Seminar Hall" 
           data-audience="All Members" 
           data-category="General Body" 
           data-organizer="Association President">
        <div class="meet-date"><span class="d"><?php echo date('d', strtotime('+2 days')); ?></span><span class="m"><?php echo date('M · D', strtotime('+2 days')); ?></span></div>
        <div class="meet-info"><h4>General Body Meeting — Semester Kickoff</h4><span>4:00 PM · AIML Seminar Hall</span></div>
        <span class="meet-badge">All Members</span>
      </div>
      <div class="meet-row" style="cursor:pointer;" onclick="showEventDetailsFromElement(this)"
           data-title="Executive Committee Sync" 
           data-desc="Internal sync for all executive committee members to review event logistics, budget allocation, and upcoming technical workshops." 
           data-date="<?php echo date('M d, Y', strtotime('+7 days')); ?>" 
           data-time="2:30 PM" 
           data-venue="Faculty Coordination Room" 
           data-audience="Committee Only" 
           data-category="Executive Sync" 
           data-organizer="Faculty Coordinator">
        <div class="meet-date"><span class="d"><?php echo date('d', strtotime('+7 days')); ?></span><span class="m"><?php echo date('M · D', strtotime('+7 days')); ?></span></div>
        <div class="meet-info"><h4>Executive Committee Sync</h4><span>2:30 PM · Faculty Coordination Room</span></div>
        <span class="meet-badge">Committee Only</span>
      </div>
      <div class="meet-row" style="cursor:pointer;" onclick="showEventDetailsFromElement(this)"
           data-title="Workshop Planning — ML Bootcamp" 
           data-desc="Technical team sync to curate hands-on ML &amp; Deep Learning tracks, dataset repositories, and speaker schedules for ML Bootcamp Batch 4." 
           data-date="<?php echo date('M d, Y', strtotime('+14 days')); ?>" 
           data-time="11:00 AM" 
           data-venue="Lab 3, AIML Block" 
           data-audience="Technical Team" 
           data-category="Workshop Track" 
           data-organizer="Technical Head">
        <div class="meet-date"><span class="d"><?php echo date('d', strtotime('+14 days')); ?></span><span class="m"><?php echo date('M · D', strtotime('+14 days')); ?></span></div>
        <div class="meet-info"><h4>Workshop Planning — ML Bootcamp</h4><span>11:00 AM · Lab 3, AIML Block</span></div>
        <span class="meet-badge">Technical Team</span>
      </div>
      <div class="meet-row" style="cursor:pointer;" onclick="showEventDetailsFromElement(this)"
           data-title="Budget Review with Faculty Coordinator" 
           data-desc="Core committee meeting with Faculty Coordinator and HOD to finalize budget allocation for flagship symposium and certificate distributions." 
           data-date="<?php echo date('M d, Y', strtotime('+21 days')); ?>" 
           data-time="1:00 PM" 
           data-venue="HOD Office" 
           data-audience="Core Team" 
           data-category="Budget Sync" 
           data-organizer="Dr. Dipali Shende (HOD)">
        <div class="meet-date"><span class="d"><?php echo date('d', strtotime('+21 days')); ?></span><span class="m"><?php echo date('M · D', strtotime('+21 days')); ?></span></div>
        <div class="meet-info"><h4>Budget Review with Faculty Coordinator</h4><span>1:00 PM · HOD Office</span></div>
        <span class="meet-badge">Core Team</span>
      </div>
    </div>
  </div>
</section>

<!-- ============ ACHIEVEMENTS ============ -->
<section class="achievements" id="achievements">
  <div class="glow-sphere glow-sphere-4"></div>
  <div class="container">
    <div class="section-head on-dark reveal">
      <span class="section-tag on-dark">Recent Achievements</span>
      <h2>Milestones Worth Celebrating</h2>
      <p>A snapshot of what our members and teams have accomplished recently.</p>
    </div>
    <div class="ach-grid reveal">
      <div class="ach-card">
        <div class="trophy"><svg viewBox="0 0 24 24"><path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 01-10 0V4zM7 5H4v2a3 3 0 003 3M17 5h3v2a3 3 0 01-3 3"/></svg></div>
        <h4>Smart India Hackathon — Finalists</h4>
        <p>Team NeuralNova represented AIMSA at the national finals with an AI-based crop advisory system.</p>
        <span class="ach-tag">June 2026</span>
      </div>
      <div class="ach-card">
        <div class="trophy"><svg viewBox="0 0 24 24"><path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 01-10 0V4zM7 5H4v2a3 3 0 003 3M17 5h3v2a3 3 0 01-3 3"/></svg></div>
        <h4>Best Paper Award — ICAIML 2026</h4>
        <p>Final-year students published and presented research on efficient vision transformers.</p>
        <span class="ach-tag">May 2026</span>
      </div>
      <div class="ach-card">
        <div class="trophy"><svg viewBox="0 0 24 24"><path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 01-10 0V4zM7 5H4v2a3 3 0 003 3M17 5h3v2a3 3 0 01-3 3"/></svg></div>
        <h4>Inter-College AI Quiz — Champions</h4>
        <p>AIMSA's quiz team took first place across 14 participating colleges in Pune.</p>
        <span class="ach-tag">April 2026</span>
      </div>
    </div>
  </div>
</section>

<!-- ============ GALLERY ============ -->
<section class="gallery" id="gallery">
  <div class="container">
    <div class="section-head reveal">
      <span class="section-tag">Photo Gallery</span>
      <h2 style="color:var(--navy-950);">Moments From AIMSA</h2>
      <p>Highlights from workshops, guest talks and department events.</p>
    </div>
    <div class="gal-grid reveal">
      <div class="gal-tile g1"><span>ML Bootcamp 2026</span></div>
      <div class="gal-tile g2"><span>Guest Lecture</span></div>
      <div class="gal-tile g3"><span>Hackathon Night</span></div>
      <div class="gal-tile g4"><span>Orientation Day</span></div>
      <div class="gal-tile g5"><span>Committee Meet</span></div>
    </div>
  </div>
</section>

<!-- ============ ANNOUNCEMENTS ============ -->
<section class="announce" id="announcements">
  <div class="container">
    <div class="section-head on-dark reveal">
      <span class="section-tag on-dark">Announcements</span>
      <h2>Latest From the Association</h2>
    </div>
    <div class="announce-list reveal" id="landingAnnouncementsList">
      <div class="ann-item">
        <div class="ann-dot"></div>
        <div style="flex:1;"><h4>Registrations open — ML Bootcamp Batch 4</h4><span>Posted 2 days ago</span></div>
        <span class="ann-pin">Pinned</span>
      </div>
      <div class="ann-item">
        <div class="ann-dot"></div>
        <div><h4>Executive committee applications for next term open next week</h4><span>Posted 4 days ago</span></div>
      </div>
      <div class="ann-item">
        <div class="ann-dot"></div>
        <div><h4>Guest lecture on Generative AI by industry expert — Aug 2</h4><span>Posted 6 days ago</span></div>
      </div>
      <div class="ann-item">
        <div class="ann-dot"></div>
        <div><h4>Portal maintenance scheduled this weekend, 12 AM – 4 AM</h4><span>Posted 1 week ago</span></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ CONTACT / HELP ============ -->
<section class="contact" id="contact">
  <div class="container">
    <div class="section-head reveal">
      <span class="section-tag">Contact &amp; Support</span>
      <h2 style="color:var(--navy-950);">Get In Touch</h2>
      <p>Reach out to the association or the help desk for any portal-related assistance.</p>
    </div>
    <div class="contact-grid reveal">
      <div class="contact-card">
        <div class="contact-icon"><svg viewBox="0 0 24 24"><path d="M4 4h16v16H4z" opacity="0"/><path d="M3 6l9 7 9-7M4 5h16v14H4z"/></svg></div>
        <div><h4>Email</h4><p>aimsa.association@zealeducation.com</p></div>
      </div>
      <div class="contact-card">
        <div class="contact-icon"><svg viewBox="0 0 24 24"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3.1-8.7A2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .3 2 .6 3a2 2 0 01-.5 2L8 10a16 16 0 006 6l1.3-1.2a2 2 0 012-.5c1 .3 2 .5 3 .6a2 2 0 011.7 2.1z"/></svg></div>
        <div><h4>Phone</h4><p>+91 20 4567 8901 (Dept. Office)</p></div>
      </div>
      <div class="contact-card">
        <div class="contact-icon"><svg viewBox="0 0 24 24"><path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
        <div><h4>Location</h4><p>AIML Department, Zeal College Campus, Pune</p></div>
      </div>
      <div class="contact-card">
        <div class="contact-icon"><svg viewBox="0 0 24 24"><path d="M12 20h.01M12 16.5a2 2 0 10-2-2M12 3a9 9 0 100 18 9 9 0 000-18z"/></svg></div>
        <div><h4>Help Desk</h4><p>Login issues or account help? Raise a ticket, we respond within 24 hours.</p></div>
      </div>
      <div class="help-banner">
        <div>
          <h4>Need help with the portal?</h4>
          <p>Our student help desk is available on all working days, 10 AM – 5 PM.</p>
        </div>
        <a href="mailto:aimsa.helpdesk@zealeducation.com" class="btn btn-primary">Raise a Ticket</a>
      </div>
    </div>
  </div>
</section>

<?php
include_once __DIR__ . '/include/footer.php';
include_once __DIR__ . '/include/script.php';
?>
