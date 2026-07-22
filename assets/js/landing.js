// Password show/hide helper
function togglePasswordVisibility(fieldId) {
  const field = document.getElementById(fieldId);
  if (field.type === "password") {
    field.type = "text";
  } else {
    field.type = "password";
  }
}

// Password strength meter logic
document.getElementById('regPassword').addEventListener('input', (e) => {
  const pwd = e.target.value;
  let score = 0;
  if (pwd.length > 5) score++;
  if (/[A-Z]/.test(pwd)) score++;
  if (/[0-9]/.test(pwd)) score++;
  if (/[^A-Za-z0-9]/.test(pwd)) score++;

  const bar = document.getElementById('pwdStrengthBar');
  const label = document.getElementById('pwdStrengthLabel');

  if (pwd.length === 0) {
    bar.style.width = '0%';
    bar.style.backgroundColor = '#dc2626';
    label.textContent = 'Too Weak';
  } else if (score <= 1) {
    bar.style.width = '25%';
    bar.style.backgroundColor = '#dc2626';
    label.textContent = 'Weak';
  } else if (score === 2) {
    bar.style.width = '50%';
    bar.style.backgroundColor = '#ea580c';
    label.textContent = 'Medium';
  } else if (score === 3) {
    bar.style.width = '75%';
    bar.style.backgroundColor = '#3E8BFF';
    label.textContent = 'Strong';
  } else {
    bar.style.width = '100%';
    bar.style.backgroundColor = '#22c55e';
    label.textContent = 'Very Secure';
  }
});

// mobile nav toggle
const hamburger = document.getElementById('hamburgerBtn');
const links = document.querySelector('nav.links');
if (hamburger && links) {
  hamburger.addEventListener('click', ()=>{
    const open = links.style.display === 'flex';
    links.style.display = open ? 'none' : 'flex';
    links.style.cssText += open ? 'display:none;' : 'display:flex; position:absolute; top:64px; left:0; right:0; flex-direction:column; background:rgba(8,23,51,.98); padding:24px; gap:20px; border-bottom:1px solid var(--line);';
  });
}

// scroll reveal
const revealEls = document.querySelectorAll('.reveal');
if (revealEls.length > 0) {
  const io = new IntersectionObserver((entries)=>{
    entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target);} });
    },{threshold:.15});
  revealEls.forEach(el=>io.observe(el));
}

// login modal
const overlay = document.getElementById('modalOverlay');
const roleStep = document.getElementById('roleStep');
const formStep = document.getElementById('formStep');
const forgotStep = document.getElementById('forgotStep');
const roleChip = document.getElementById('roleChip');

// Tabs for login vs register
const tabLogin = document.getElementById('tabLogin');
const tabRegister = document.getElementById('tabRegister');
const signInSection = document.getElementById('signInSection');
const registerSection = document.getElementById('registerSection');

if (tabLogin && tabRegister) {
  tabLogin.addEventListener('click', () => {
    tabLogin.style.borderBottomColor = 'var(--accent)';
    tabLogin.style.fontWeight = '700';
    tabLogin.style.color = 'var(--navy-950)';
    tabRegister.style.borderBottomColor = 'transparent';
    tabRegister.style.fontWeight = '500';
    tabRegister.style.color = 'var(--muted-dark)';
    signInSection.style.display = 'flex';
    registerSection.style.display = 'none';
  });

  tabRegister.addEventListener('click', () => {
    tabRegister.style.borderBottomColor = 'var(--accent)';
    tabRegister.style.fontWeight = '700';
    tabRegister.style.color = 'var(--navy-950)';
    tabLogin.style.borderBottomColor = 'transparent';
    tabLogin.style.fontWeight = '500';
    tabLogin.style.color = 'var(--muted-dark)';
    registerSection.style.display = 'flex';
    signInSection.style.display = 'none';
  });
}

function clearAuthInputs() {
  ['loginEmail', 'loginPassword', 'regName', 'regEmail', 'regPassword', 'regConfirmPassword', 'forgotEmail', 'forgotOtpInput', 'forgotNewPassword', 'forgotConfirmPassword'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = '';
  });

  const step1 = document.getElementById('otpStep1');
  const step2 = document.getElementById('otpStep2');
  const step3 = document.getElementById('otpStep3');
  if (step1) step1.style.display = 'flex';
  if (step2) step2.style.display = 'none';
  if (step3) step3.style.display = 'none';
}

function openModal(){ 
  if (overlay) {
    clearAuthInputs();
    overlay.classList.add('show'); 
    roleStep.style.display='block'; 
    formStep.classList.remove('show'); 
    forgotStep.style.display='none';
  }
}
function closeModal(){ 
  if (overlay) {
    clearAuthInputs();
    overlay.classList.remove('show'); 
  }
}

const openLoginBtn2 = document.getElementById('openLoginBtn2');
if (openLoginBtn2) openLoginBtn2.addEventListener('click', openModal);

const footerLoginLink = document.getElementById('footerLoginLink');
if (footerLoginLink) footerLoginLink.addEventListener('click', (e)=>{e.preventDefault(); openModal();});

const modalCloseBtn = document.getElementById('modalCloseBtn');
if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeModal);

if (overlay) {
  overlay.addEventListener('click', (e)=>{ if(e.target === overlay) closeModal(); });
}

document.querySelectorAll('.role-card').forEach(card=>{
  card.addEventListener('click', ()=>{
    clearAuthInputs();
    const role = card.getAttribute('data-role');
    roleChip.textContent = role;
    roleStep.style.display='none';
    formStep.classList.add('show');
    // Hide register tab for staff, show only for Student Member
    if(role !== 'Student Member') {
      tabRegister.style.display = 'none';
      tabLogin.click();
    } else {
      tabRegister.style.display = 'block';
    }
  });
});

const backToRoles = document.getElementById('backToRoles');
if (backToRoles) {
  backToRoles.addEventListener('click', ()=>{
    clearAuthInputs();
    formStep.classList.remove('show');
    roleStep.style.display='block';
  });
}

const forgotLink = document.getElementById('forgotLink');
if (forgotLink) {
  forgotLink.addEventListener('click', (e) => {
    e.preventDefault();
    clearAuthInputs();
    formStep.classList.remove('show');
    forgotStep.style.display = 'flex';
  });
}

const backToLogin = document.getElementById('backToLogin');
if (backToLogin) {
  backToLogin.addEventListener('click', () => {
    clearAuthInputs();
    forgotStep.style.display = 'none';
    formStep.classList.add('show');
  });
}

// Role → dashboard page mapping (Updated to PHP)
const roleDashboards = {
  'HOD': 'hod_dashboard.php',
  'Faculty Coordinator': 'faculty_dashboard.php',
  'Association President': 'president_dashboard.php',
  'Committee Member': 'committee_dashboard.php',
  'Student Member': 'student_dashboard.php'
};

// Database authentication - Login verification
const loginSubmitBtn = document.getElementById('loginSubmitBtn');
if (loginSubmitBtn) {
  loginSubmitBtn.addEventListener('click', ()=>{
    const selectedRole = roleChip.textContent.trim();
    const emailVal = document.getElementById('loginEmail').value.trim();
    const pwdVal = document.getElementById('loginPassword').value;

    if(!emailVal || !pwdVal) {
      alert('Please enter college email ID and password.');
      return;
    }

    loginSubmitBtn.disabled = true;
    loginSubmitBtn.textContent = 'Verifying Credentials...';

    const formData = new FormData();
    formData.append('action', 'login');
    formData.append('email', emailVal);
    formData.append('password', pwdVal);
    formData.append('role', selectedRole);

    fetch('ajax/auth.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      loginSubmitBtn.disabled = false;
      loginSubmitBtn.textContent = 'Secure Login →';

      if(data.status === 'success') {
        sessionStorage.setItem('current_user', JSON.stringify(data.user));
        if(document.getElementById('keepSignedIn').checked) {
          localStorage.setItem('keep_signed_in_user', JSON.stringify(data.user));
        }
        window.location.href = data.redirect || roleDashboards[selectedRole];
      } else {
        alert(data.message || 'Login failed.');
      }
    })
    .catch(err => {
      loginSubmitBtn.disabled = false;
      loginSubmitBtn.textContent = 'Secure Login →';
      alert('Server error during login. Please try again.');
      console.error(err);
    });
  });
}

// Database authentication - Registration Flow
const registerSubmitBtn = document.getElementById('registerSubmitBtn');
if (registerSubmitBtn) {
  registerSubmitBtn.addEventListener('click', () => {
    const selectedRole = roleChip.textContent.trim();
    const nameVal = document.getElementById('regName').value.trim();
    const emailVal = document.getElementById('regEmail').value.trim();
    const pwdVal = document.getElementById('regPassword').value;
    const confPwdVal = document.getElementById('regConfirmPassword').value;

    if(!nameVal || !emailVal || !pwdVal || !confPwdVal) {
      alert('Please fill out all fields to register.');
      return;
    }

    if(pwdVal !== confPwdVal) {
      alert('Passwords do not match.');
      return;
    }

    registerSubmitBtn.disabled = true;
    registerSubmitBtn.textContent = 'Creating Account...';

    const formData = new FormData();
    formData.append('action', 'register');
    formData.append('name', nameVal);
    formData.append('email', emailVal);
    formData.append('password', pwdVal);
    formData.append('role', selectedRole);

    fetch('ajax/auth.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      registerSubmitBtn.disabled = false;
      registerSubmitBtn.textContent = 'Create Account →';

      if(data.status === 'success') {
        sessionStorage.setItem('current_user', JSON.stringify(data.user));
        alert(data.message || 'Registration successful! Account is active.');
        window.location.href = data.redirect || roleDashboards[selectedRole];
      } else {
        alert(data.message || 'Registration failed.');
      }
    })
    .catch(err => {
      registerSubmitBtn.disabled = false;
      registerSubmitBtn.textContent = 'Create Account →';
      alert('Server error during registration. Please try again.');
      console.error(err);
    });
  });
}

// Database authentication - Forgot Password OTP Multi-Step Flow
const sendOtpBtn = document.getElementById('sendOtpBtn');
if (sendOtpBtn) {
  sendOtpBtn.addEventListener('click', () => {
    const emailVal = document.getElementById('forgotEmail').value.trim();
    if(!emailVal) {
      alert('Please enter your college email ID.');
      return;
    }

    sendOtpBtn.disabled = true;
    sendOtpBtn.textContent = 'Sending OTP...';

    const formData = new FormData();
    formData.append('action', 'send_otp');
    formData.append('email', emailVal);

    fetch('ajax/auth.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      sendOtpBtn.disabled = false;
      sendOtpBtn.textContent = 'Send OTP Code →';

      if(data.status === 'success') {
        const banner = document.getElementById('otpNoticeBanner');
        if(banner) {
          banner.innerHTML = `<strong>OTP Sent!</strong> A 6-digit verification code has been dispatched to <b>${data.masked_email}</b>. Please check your inbox and spam folder (valid for 5 minutes).`;
        }
        document.getElementById('otpStep1').style.display = 'none';
        document.getElementById('otpStep2').style.display = 'flex';
      } else {
        alert(data.message || 'Failed to send OTP.');
      }
    })
    .catch(err => {
      sendOtpBtn.disabled = false;
      sendOtpBtn.textContent = 'Send OTP Code →';
      alert('Server error while sending OTP. Please try again.');
      console.error(err);
    });
  });
}

const verifyOtpBtn = document.getElementById('verifyOtpBtn');
if (verifyOtpBtn) {
  verifyOtpBtn.addEventListener('click', () => {
    const emailVal = document.getElementById('forgotEmail').value.trim();
    const otpVal = document.getElementById('forgotOtpInput').value.trim();

    if(!otpVal || otpVal.length !== 6) {
      alert('Please enter the 6-digit OTP code.');
      return;
    }

    verifyOtpBtn.disabled = true;
    verifyOtpBtn.textContent = 'Verifying...';

    const formData = new FormData();
    formData.append('action', 'verify_otp');
    formData.append('email', emailVal);
    formData.append('otp', otpVal);

    fetch('ajax/auth.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      verifyOtpBtn.disabled = false;
      verifyOtpBtn.textContent = 'Verify OTP Code →';

      if(data.status === 'success') {
        document.getElementById('otpStep2').style.display = 'none';
        document.getElementById('otpStep3').style.display = 'flex';
      } else {
        alert(data.message || 'OTP verification failed.');
      }
    })
    .catch(err => {
      verifyOtpBtn.disabled = false;
      verifyOtpBtn.textContent = 'Verify OTP Code →';
      alert('Server error during OTP verification. Please try again.');
      console.error(err);
    });
  });
}

const resendOtpBtn = document.getElementById('resendOtpBtn');
if (resendOtpBtn) {
  resendOtpBtn.addEventListener('click', () => {
    if (sendOtpBtn) sendOtpBtn.click();
  });
}

const forgotSubmitBtn = document.getElementById('forgotSubmitBtn');
if (forgotSubmitBtn) {
  forgotSubmitBtn.addEventListener('click', () => {
    const emailVal = document.getElementById('forgotEmail').value.trim();
    const newPwdVal = document.getElementById('forgotNewPassword').value;
    const confPwdVal = document.getElementById('forgotConfirmPassword').value;

    if(!emailVal || !newPwdVal || !confPwdVal) {
      alert('Please fill out your new password fields.');
      return;
    }

    if(newPwdVal !== confPwdVal) {
      alert('New passwords do not match.');
      return;
    }

    forgotSubmitBtn.disabled = true;
    forgotSubmitBtn.textContent = 'Updating Password...';

    const formData = new FormData();
    formData.append('action', 'forgot_password');
    formData.append('email', emailVal);
    formData.append('new_password', newPwdVal);

    fetch('ajax/auth.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      forgotSubmitBtn.disabled = false;
      forgotSubmitBtn.textContent = 'Update Password →';

      if(data.status === 'success') {
        alert(data.message);
        clearAuthInputs();
        forgotStep.style.display = 'none';
        formStep.classList.add('show');
      } else {
        alert(data.message || 'Password update failed.');
      }
    })
    .catch(err => {
      forgotSubmitBtn.disabled = false;
      forgotSubmitBtn.textContent = 'Update Password →';
      alert('Server error during password update. Please try again.');
      console.error(err);
    });
  });
}

// generative node field background for hero + committee section
function buildNodeField(id, count){
  const el = document.getElementById(id);
  if(!el) return;
  const w = 1200, h = 700;
  const pts = [];
  for(let i=0;i<count;i++){
    pts.push({x: Math.random()*w, y: Math.random()*h});
  }
  let lines = '';
  for(let i=0;i<pts.length;i++){
    for(let j=i+1;j<pts.length;j++){
      const dx = pts[i].x-pts[j].x, dy = pts[i].y-pts[j].y;
      const dist = Math.sqrt(dx*dx+dy*dy);
      if(dist < 190){
        lines += `<line class="node-line" x1="${pts[i].x}" y1="${pts[i].y}" x2="${pts[j].x}" y2="${pts[j].y}"/>`;
      }
    }
  }
  let dots = '';
  pts.forEach((p,i)=>{
    dots += `<circle class="node-dot ${i%3===0?'pulse':''}" cx="${p.x}" cy="${p.y}" r="${i%3===0?2.4:1.6}"/>`;
  });
  el.innerHTML = `<svg viewBox="0 0 ${w} ${h}" preserveAspectRatio="xMidYMid slice">${lines}${dots}</svg>`;
}
buildNodeField('nodeField', 26);
buildNodeField('nodeField2', 18);

// ---------- subtle 3D interactions (cursor tilt + scroll depth) ----------
const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if(canHover && !reduceMotion){

  // gentle cursor-tilt on cards — small angles, kept subtle for a professional feel
  function attachTilt(selector, maxDeg){
    document.querySelectorAll(selector).forEach(card=>{
      card.addEventListener('mousemove', (e)=>{
        const r = card.getBoundingClientRect();
        const px = (e.clientX - r.left) / r.width - 0.5;
        const py = (e.clientY - r.top) / r.height - 0.5;
        const rotY = (px * maxDeg).toFixed(2);
        const rotX = (-py * maxDeg).toFixed(2);
        card.style.transform = `translateY(-6px) rotateX(${rotX}deg) rotateY(${rotY}deg)`;
      });
      card.addEventListener('mouseleave', ()=>{ card.style.transform = ''; });
    });
  }
  attachTilt('.pop-card', 6);
  attachTilt('.ach-card', 6);
  attachTilt('.contact-card', 4);
  attachTilt('.vm-card', 4);
  attachTilt('.role-card', 4);
  attachTilt('.gal-tile', 4);

  // gentle scroll-driven depth on the hero — logos and background drift at different rates
  const heroEl = document.querySelector('.hero');
  const heroInner = document.querySelector('.hero-inner');
  const heroLogos = document.querySelector('.hero-logos');
  const nodeFieldEl = document.getElementById('nodeField');
  let ticking = false;

  function updateScrollDepth(){
    const heroHeight = heroEl ? heroEl.offsetHeight : 800;
    const y = Math.min(window.scrollY, heroHeight);
    const progress = y / heroHeight; // 0 to 1 across the hero
    if(heroInner) heroInner.style.transform = `translateY(${y*0.12}px) rotateX(${(progress*4).toFixed(2)}deg)`;
    if(heroLogos) heroLogos.style.transform = `translateY(${y*0.22}px)`;
    if(nodeFieldEl) nodeFieldEl.style.transform = `translateY(${y*0.15}px)`;
    ticking = false;
  }
  window.addEventListener('scroll', ()=>{
    if(!ticking){ requestAnimationFrame(updateScrollDepth); ticking = true; }
  }, {passive:true});
}

// ---------- Dynamic Real-Time Date & Time Clock Engine ----------
function updateLiveClock() {
  const now = new Date();
  
  const optionsDate = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
  const dateStr = now.toLocaleDateString('en-US', optionsDate);
  const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
  
  const clockTextEls = document.querySelectorAll('.liveClockText');
  clockTextEls.forEach(el => {
    el.textContent = `${dateStr} · ${timeStr}`;
  });

  const liveDateEls = document.querySelectorAll('.liveDateText');
  liveDateEls.forEach(el => {
    el.textContent = now.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
  });

  const currentYearEls = document.querySelectorAll('.currentYearText');
  currentYearEls.forEach(el => {
    el.textContent = now.getFullYear();
  });
}

setInterval(updateLiveClock, 1000);
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', updateLiveClock);
} else {
  updateLiveClock();
}

// ---------- Multi-Language (i18n) Engine: English & Marathi ----------
const i18nDictionary = {
  mr: {
    // Navigation
    "nav.about": "आमच्याबद्दल",
    "nav.committee": "कार्यकारिणी",
    "nav.meetings": "बैठका",
    "nav.achievements": "यश",
    "nav.gallery": "गॅलरी",
    "nav.announcements": "सूचना",
    "nav.contact": "संपर्क",
    "nav.login": "लॉगिन",

    // Hero Section
    "hero.tagline": "झील एज्युकेशन सोसायटी — एआय आणि एमएल विभाग",
    "hero.title": "AIMSA विद्यार्थी पोर्टलवर आपले स्वागत आहे",
    "hero.lead": "एआय आणि एमएल स्टुडंट असोसिएशनच्या सर्व विद्यार्थ्यांसाठी, समिती सदस्यांसाठी आणि शिक्षकांसाठी एकच व्यासपीठ.",
    "hero.login_btn": "पोर्टलवर लॉगिन करा →",
    "hero.explore_btn": "असोसिएशन बद्दल जाणून घ्या",
    "hero.stat.members": "सक्रिय सदस्य",
    "hero.stat.events": "आयोजित कार्यक्रम",
    "hero.stat.roles": "समिती भूमिका",
    "hero.stat.est": "स्थापना वर्ष",

    // Section Titles
    "section.about": "आमच्याबद्दल",
    "section.who_we_are": "आम्ही कोण आहोत",
    "section.committee": "कार्यकारिणी समिती",
    "section.meetings": "आगामी बैठका",
    "section.achievements": "अलीकडील यश",
    "section.gallery": "फोटो गॅलरी",
    "section.announcements": "सूचना",
    "section.contact": "संपर्क आणि मदत",

    // Dashboards Common
    "dash.search_ph": "सदस्य, कार्यक्रम, उपस्थिती शोधा...",
    "dash.change_password": "पासवर्ड बदला",
    "dash.logout": "लॉगआउट",
    "dash.support": "मदत केंद्र:",
    "dash.call": "फोन:",

    // Dashboard Titles & Eyebrows
    "dash.hod_eyebrow": "विभागप्रमुख पुनरावलोकन",
    "dash.hod_title": "शुभ प्रभात, डॉ. शिंदे 👋",
    "dash.hod_sub": "आज AIMSA मध्ये काय चालले आहे — ",
    
    "dash.faculty_eyebrow": "शिक्षक समन्वयक",
    "dash.faculty_title": "स्वागत आहे, प्रा. नायर 👋",
    "dash.faculty_sub": "तुमचे शिक्षक मार्गदर्शन पोर्टल — ",

    "dash.president_eyebrow": "असोसिएशन अध्यक्ष",
    "dash.president_title": "नमस्कार, अध्यक्ष! ⭐",
    "dash.president_sub": "AIMSA नेतृत्व डॅशबोर्ड — ",

    "dash.committee_eyebrow": "समिती सदस्य",
    "dash.committee_title": "नमस्कार, समिती सदस्य! 👋",
    "dash.committee_sub": "तांत्रिक समिती · तुमचे कार्य पुनरावलोकन — ",

    "dash.student_eyebrow": "विद्यार्थी सदस्य",
    "dash.student_title": "नमस्कार, विद्यार्थी सदस्य! 👋",
    "dash.student_sub": "तुमचा AIMSA प्रवास एका दृष्टीक्षेपात — ",

    // Stats Labels
    "stat.total_members": "एकूण सदस्य",
    "stat.committee_members": "समिती सदस्य",
    "stat.events_conducted": "आयोजित कार्यक्रम",
    "stat.assigned_events": "नियुक्त कार्यक्रम",
    "stat.attendance_rate": "उपस्थितीचे प्रमाण",
    "stat.unread_notifs": "नवीन सूचना",
    "stat.reports_filed": "दाखल अहवाल",

    // Common Buttons
    "btn.secure_login": "सुरक्षित लॉगिन →",
    "btn.create_account": "खाते तयार करा →",
    "btn.send_otp": "ओटीपी पाठवा →",
    "btn.verify_otp": "ओटीपी प्रविष्ट करा →",
    "btn.update_password": "पासवर्ड अद्ययावत करा →",
    "btn.close": "बंद करा"
  },
  en: {
    // Navigation
    "nav.about": "About",
    "nav.committee": "Committee",
    "nav.meetings": "Meetings",
    "nav.achievements": "Achievements",
    "nav.gallery": "Gallery",
    "nav.announcements": "Announcements",
    "nav.contact": "Contact",
    "nav.login": "Login",

    // Hero Section
    "hero.tagline": "ZEAL EDUCATION SOCIETY — DEPT. OF AI & ML",
    "hero.title": "Welcome to the AIMSA Student Portal",
    "hero.lead": "One home for every student, committee member, faculty coordinator and administrator of the AI & ML Student Association.",
    "hero.login_btn": "Login to Portal →",
    "hero.explore_btn": "Explore the Association",
    "hero.stat.members": "Active Members",
    "hero.stat.events": "Events Hosted",
    "hero.stat.roles": "Committee Roles",
    "hero.stat.est": "Institute Est.",

    // Section Titles
    "section.about": "About Us",
    "section.who_we_are": "Who We Are",
    "section.committee": "Executive Committee",
    "section.meetings": "Upcoming Meetings",
    "section.achievements": "Recent Achievements",
    "section.gallery": "Photo Gallery",
    "section.announcements": "Announcements",
    "section.contact": "Contact & Support",

    // Dashboards Common
    "dash.search_ph": "Search members, events...",
    "dash.change_password": "Change Password",
    "dash.logout": "Logout",
    "dash.support": "Support:",
    "dash.call": "Call:",

    // Dashboard Titles & Eyebrows
    "dash.hod_eyebrow": "HOD Overview",
    "dash.hod_title": "Good Morning, Dr. Shende 👋",
    "dash.hod_sub": "Here's what's happening in AIMSA today — ",
    
    "dash.faculty_eyebrow": "Faculty Coordinator",
    "dash.faculty_title": "Welcome, Prof. Nair 👋",
    "dash.faculty_sub": "Your faculty oversight portal — ",

    "dash.president_eyebrow": "Association President",
    "dash.president_title": "Hey, Karan! ⭐",
    "dash.president_sub": "AIMSA leadership dashboard — ",

    "dash.committee_eyebrow": "Committee Member",
    "dash.committee_title": "Hello, Riya! 👋",
    "dash.committee_sub": "Technical Committee · Your activity overview — ",

    "dash.student_eyebrow": "Student Member",
    "dash.student_title": "Hey, Arjun! 👋",
    "dash.student_sub": "Your AIMSA journey at a glance — ",

    // Stats Labels
    "stat.total_members": "Total Members",
    "stat.committee_members": "Committee Members",
    "stat.events_conducted": "Events Conducted",
    "stat.assigned_events": "Assigned Events",
    "stat.attendance_rate": "Attendance Rate",
    "stat.unread_notifs": "Unread Notifications",
    "stat.reports_filed": "Event Reports Filed",

    // Common Buttons
    "btn.secure_login": "Secure Login →",
    "btn.create_account": "Create Account →",
    "btn.send_otp": "Send OTP Code →",
    "btn.verify_otp": "Verify OTP Code →",
    "btn.update_password": "Update Password →",
    "btn.close": "Close"
  }
};

let isApplyingLanguage = false;
let observer = null;

function applyLanguage(lang) {
  if (isApplyingLanguage) return;
  isApplyingLanguage = true;
  if (observer) {
    observer.disconnect();
  }

  try {
    const selectedLang = lang === 'mr' ? 'mr' : 'en';
    localStorage.setItem('aimsa_lang', selectedLang);
    sessionStorage.setItem('aimsa_lang', selectedLang);

    // Sync all language dropdowns across page and topbar
    document.querySelectorAll('#langSelect').forEach(sel => {
      sel.value = selectedLang;
    });

    const dict = i18nDictionary[selectedLang] || i18nDictionary.en;

    // 1. Translate elements with data-i18n attributes
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.getAttribute('data-i18n');
      if (dict[key]) {
        el.textContent = dict[key];
      }
    });

    // 2. Translate placeholders with data-i18n-ph
    document.querySelectorAll('[data-i18n-ph]').forEach(el => {
      const key = el.getAttribute('data-i18n-ph');
      if (dict[key]) {
        el.placeholder = dict[key];
      }
    });

    // 3. Dynamic Full-Page Marathi Map
    if (selectedLang === 'mr') {
      const marathiFullMap = {
        // Navigation & Header
        "About": "आमच्याबद्दल",
        "Committee": "कार्यकारिणी",
        "Meetings": "बैठका",
        "Achievements": "यश",
        "Gallery": "गॅलरी",
        "Announcements": "सूचना",
        "Contact": "संपर्क",
        "Login": "लॉगिन",
        "Register": "नोंदणी करा",
        "Sign In": "साइन इन",
        "Logout": "लॉगआउट",
        "Overview": "डॅशबोर्ड",
        "Dashboard": "डॅशबोर्ड",
        "Members": "सदस्य",
        "Events": "कार्यक्रम",
        "Attendance": "उपस्थिती",
        "Certificates": "प्रमाणपत्रे",
        "Reports": "अहवाल",
        "Settings": "सेटिंग्ज",
        "Change Password": "पासवर्ड बदला",

        // Eyebrows & Titles
        "HOD Overview": "विभागप्रमुख पुनरावलोकन",
        "Faculty Coordinator": "शिक्षक समन्वयक",
        "Association President": "असोसिएशन अध्यक्ष",
        "Committee Member": "समिती सदस्य",
        "Student Member": "विद्यार्थी सदस्य",
        "Technical Committee": "तांत्रिक समिती",
        "AIMSA Student Portal": "AIMSA विद्यार्थी पोर्टल",
        "Welcome to the AIMSA Student Portal": "AIMSA विद्यार्थी पोर्टलवर आपले स्वागत आहे",
        "Explore the Association": "असोसिएशन बद्दल जाणून घ्या",
        "Login to Portal →": "पोर्टलवर लॉगिन करा →",

        // Stats Labels
        "Total Members": "एकूण सदस्य",
        "Committee Members": "समिती सदस्य",
        "Events Conducted": "आयोजित कार्यक्रम",
        "New Registrations": "नवीन नोंदणी",
        "Assigned Events": "नियुक्त कार्यक्रम",
        "Attendance Rate": "उपस्थितीचे प्रमाण",
        "Event Reports Filed": "दाखल अहवाल",
        "Unread Notifications": "नवीन सूचना",
        "Active Members": "सक्रिय सदस्य",
        "Events Hosted": "आयोजित कार्यक्रम",
        "Committee Roles": "समिती भूमिका",
        "Institute Est.": "स्थापना वर्ष",

        // Modals & Drawers
        "Select Your Role": "तुमची भूमिका निवडा",
        "Head of Department (HOD)": "विभागप्रमुख (HOD)",
        "Reset Password": "पासवर्ड रिसेट करा",
        "College Email ID": "कॉलेज ईमेल आयडी",
        "Enter 6-Digit OTP": "६-अंकी ओटीपी प्रविष्ट करा",
        "New Password": "नवीन पासवर्ड",
        "Confirm New Password": "नवीन पासवर्डची पुष्टी करा",
        "Send OTP Code →": "ओटीपी कोड पाठवा →",
        "Verify OTP Code →": "ओटीपी पडताळणी करा →",
        "Update Password →": "पासवर्ड अद्ययावत करा →",
        "Create Account →": "खाते तयार करा →",
        "Secure Login →": "सुरक्षित लॉगिन →",
        "Full Name": "पूर्ण नाव",

        // Tables & Form Labels
        "Member Name": "सदस्याचे नाव",
        "Email ID": "ईमेल आयडी",
        "Role": "भूमिका",
        "Branch": "शाखा",
        "Batch": "बॅच",
        "Membership Status": "सदस्यत्व स्थिती",
        "Designation": "पदनाम",
        "Action": "कृती",
        "Date": "दिनांक",
        "Time": "वेळ",
        "Venue": "स्थळ",
        "Topic": "विषय",
        "Description": "वर्णन",

        // Footers
        "Privacy Policy": "गोपनीयता धोरण",
        "Terms & Conditions": "अटी व शर्ती",
        "Support:": "मदत केंद्र:"
      };

      const targetSelectors = 'a, button, span, div.section-eyebrow, div.stat-label, th, div.drawer-title, h1, h2, h3, h4, label, p.sub, nav.links a, .nav-item, .sidebar-link';
      document.querySelectorAll(targetSelectors).forEach(el => {
        if (el.children.length === 0) {
          const txt = el.textContent.trim();
          if (marathiFullMap[txt]) {
            el.textContent = marathiFullMap[txt];
          }
        }
      });

      document.querySelectorAll('#headerSearchInput, .search-input').forEach(el => {
        el.placeholder = "सदस्य, कार्यक्रम, उपस्थिती शोधा...";
      });
    } else {
      // Revert to English
      const englishRevertMap = {
        "आमच्याबद्दल": "About",
        "कार्यकारिणी": "Committee",
        "बैठका": "Meetings",
        "यश": "Achievements",
        "गॅलरी": "Gallery",
        "सूचना": "Announcements",
        "संपर्क": "Contact",
        "लॉगिन": "Login",
        "नोंदणी करा": "Register",
        "साइन इन": "Sign In",
        "लॉगआउट": "Logout",
        "डॅशबोर्ड": "Dashboard",
        "सदस्य": "Members",
        "कार्यक्रम": "Events",
        "उपस्थिती": "Attendance",
        "प्रमाणपत्रे": "Certificates",
        "अहवाल": "Reports",
        "सेटिंग्ज": "Settings",
        "पासवर्ड बदला": "Change Password",
        "विभागप्रमुख पुनरावलोकन": "HOD Overview",
        "शिक्षक समन्वयक": "Faculty Coordinator",
        "असोसिएशन अध्यक्ष": "Association President",
        "समिती सदस्य": "Committee Member",
        "विद्यार्थी सदस्य": "Student Member",
        "एकूण सदस्य": "Total Members",
        "समिती सदस्य": "Committee Members",
        "आयोजित कार्यक्रम": "Events Conducted",
        "नवीन नोंदणी": "New Registrations",
        "नियुक्त कार्यक्रम": "Assigned Events",
        "उपस्थितीचे प्रमाण": "Attendance Rate",
        "दाखल अहवाल": "Event Reports Filed",
        "नवीन सूचना": "Unread Notifications",
        "सदस्याचे नाव": "Member Name",
        "ईमेल आयडी": "Email ID",
        "भूमिका": "Role",
        "शाखा": "Branch",
        "बॅच": "Batch",
        "सदस्यत्व स्थिती": "Membership Status",
        "पदनाम": "Designation",
        "कृती": "Action",
        "दिनांक": "Date",
        "वेळ": "Time",
        "स्थळ": "Venue",
        "विषय": "Topic",
        "गोपनीयता धोरण": "Privacy Policy",
        "अटी व शर्ती": "Terms & Conditions"
      };

      const targetSelectors = 'a, button, span, div.section-eyebrow, div.stat-label, th, div.drawer-title, h1, h2, h3, h4, label, p.sub, nav.links a, .nav-item, .sidebar-link';
      document.querySelectorAll(targetSelectors).forEach(el => {
        if (el.children.length === 0) {
          const txt = el.textContent.trim();
          if (englishRevertMap[txt]) {
            el.textContent = englishRevertMap[txt];
          }
        }
      });

      document.querySelectorAll('#headerSearchInput, .search-input').forEach(el => {
        el.placeholder = "Search members, events...";
      });
    }
  } finally {
    isApplyingLanguage = false;
    if (document.body && observer) {
      observer.observe(document.body, { childList: true, subtree: true });
    }
  }
}

window.changeLanguage = function() {
  const sel = document.getElementById('langSelect');
  const lang = sel ? sel.value : (localStorage.getItem('aimsa_lang') || 'en');
  applyLanguage(lang);
};

// Delegated change listener for any langSelect on any dashboard/page
document.addEventListener('change', (e) => {
  if (e.target && e.target.id === 'langSelect') {
    applyLanguage(e.target.value);
  }
});

// Auto-observe dynamic DOM changes (modals, drawers, tables) and apply active language
observer = new MutationObserver(() => {
  if (isApplyingLanguage) return;
  const currentLang = localStorage.getItem('aimsa_lang') || 'en';
  if (currentLang === 'mr') {
    applyLanguage('mr');
  }
});

if (document.body) {
  observer.observe(document.body, { childList: true, subtree: true });
}

// Initialize language on DOM ready & page load
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    applyLanguage(localStorage.getItem('aimsa_lang') || 'en');
  });
} else {
  applyLanguage(localStorage.getItem('aimsa_lang') || 'en');
}




