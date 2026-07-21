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

function openModal(){ 
  if (overlay) {
    overlay.classList.add('show'); 
    roleStep.style.display='block'; 
    formStep.classList.remove('show'); 
    forgotStep.style.display='none';
  }
}
function closeModal(){ if (overlay) overlay.classList.remove('show'); }

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
    formStep.classList.remove('show');
    roleStep.style.display='block';
  });
}

const forgotLink = document.getElementById('forgotLink');
if (forgotLink) {
  forgotLink.addEventListener('click', (e) => {
    e.preventDefault();
    formStep.classList.remove('show');
    forgotStep.style.display = 'flex';
  });
}

const backToLogin = document.getElementById('backToLogin');
if (backToLogin) {
  backToLogin.addEventListener('click', () => {
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

// Initial simulated users database
if(!localStorage.getItem('aimsa_users')) {
  localStorage.setItem('aimsa_users', JSON.stringify([
    {email: 'hod@zealeducation.com', password: 'Password123', name: 'Dr. Anjali Sharma', role: 'HOD'},
    {email: 'faculty@zealeducation.com', password: 'Password123', name: 'Prof. Meera Nair', role: 'Faculty Coordinator'},
    {email: 'president@zealeducation.com', password: 'Password123', name: 'Karan Mehta', role: 'Association President'},
    {email: 'committee@zealeducation.com', password: 'Password123', name: 'Riya Desai', role: 'Committee Member'},
    {email: 'student@zealeducation.com', password: 'Password123', name: 'Arjun Patil', role: 'Student Member'}
  ]));
}

// Initial simulated notifications database
if(!localStorage.getItem('aimsa_notifications')) {
  localStorage.setItem('aimsa_notifications', JSON.stringify([
    {title: 'Membership Approved', text: 'Dr. Anjali Sharma verified your credentials.', indicator: 'green', recipient: 'student@zealeducation.com', time: '1 hr ago', email: true},
    {title: 'New Event Published', text: 'Prof. Meera Nair proposed: Tech Symposium 2026.', indicator: 'green', recipient: 'all', time: '2 hrs ago', email: true},
    {title: 'Registration Successful', text: 'Successfully registered for Hackathon 2026.', indicator: 'green', recipient: 'student@zealeducation.com', time: '4 hrs ago', email: true},
    {title: 'Event Reminder', text: 'Reminder: AI Workshop Series starts on Aug 3.', indicator: 'yellow', recipient: 'all', time: 'Yesterday', email: true},
    {title: 'Attendance Confirmation', text: 'Marked Present for Tech Symposium 2025.', indicator: 'green', recipient: 'student@zealeducation.com', time: '2 days ago', email: true},
    {title: 'Certificate Available', text: 'Volunteer Certificate for Hackathon 2025 generated.', indicator: 'green', recipient: 'student@zealeducation.com', time: '3 days ago', email: true},
    {title: 'Achievement Approved', text: 'Research Paper submission approved by Faculty.', indicator: 'green', recipient: 'student@zealeducation.com', time: '1 week ago', email: true},
    {title: 'New Announcement', text: 'Executive Committee applications are now open.', indicator: 'green', recipient: 'all', time: '1 week ago', email: true},
    {title: 'Profile Updated', text: 'Successfully updated profile photograph & branch.', indicator: 'green', recipient: 'student@zealeducation.com', time: '2 weeks ago', email: true},
    {title: 'Password Changed Successfully', text: 'Portal login password updated.', indicator: 'green', recipient: 'student@zealeducation.com', time: '2 weeks ago', email: true}
  ]));
}

// Login verification with session storage
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

    const users = JSON.parse(localStorage.getItem('aimsa_users'));
    const user = users.find(u => u.email.toLowerCase() === emailVal.toLowerCase() && u.password === pwdVal && u.role === selectedRole);

    if(user) {
      // Store user session
      sessionStorage.setItem('current_user', JSON.stringify(user));
      if(document.getElementById('keepSignedIn').checked) {
        localStorage.setItem('keep_signed_in_user', JSON.stringify(user));
      }
      const target = roleDashboards[selectedRole];
      if(target){ window.location.href = target; }
    } else {
      alert('Invalid Email ID or Password for the selected role. Please use the simulated default credentials (e.g. email: student@zealeducation.com, password: Password123).');
    }
  });
}

// Registration Flow
const registerSubmitBtn = document.getElementById('registerSubmitBtn');
if (registerSubmitBtn) {
  registerSubmitBtn.addEventListener('click', () => {
    const selectedRole = roleChip.textContent.trim();
    const nameVal = document.getElementById('regName').value.trim();
    const idVal = document.getElementById('regID').value.trim();
    const emailVal = document.getElementById('regEmail').value.trim();
    const pwdVal = document.getElementById('regPassword').value;
    const confPwdVal = document.getElementById('regConfirmPassword').value;

    if(!nameVal || !idVal || !emailVal || !pwdVal || !confPwdVal) {
      alert('Please fill out all fields to register.');
      return;
    }

    if(pwdVal !== confPwdVal) {
      alert('Passwords do not match.');
      return;
    }

    const users = JSON.parse(localStorage.getItem('aimsa_users'));
    if(users.some(u => u.email.toLowerCase() === emailVal.toLowerCase())) {
      alert('A user with this Email ID already exists.');
      return;
    }

    // Create new user (Student)
    const newUser = {
      email: emailVal,
      password: pwdVal,
      name: nameVal,
      role: selectedRole,
      studentId: idVal,
      membershipStatus: 'Pending',
      membershipRenewed: false,
      photograph: ''
    };

    users.push(newUser);
    localStorage.setItem('aimsa_users', JSON.stringify(users));

    // Auto sign in simulation
    sessionStorage.setItem('current_user', JSON.stringify(newUser));
    alert('Registration successful! Click OK to go to your student dashboard.');
    window.location.href = roleDashboards[selectedRole];
  });
}

// Forgot Password Flow
const forgotSubmitBtn = document.getElementById('forgotSubmitBtn');
if (forgotSubmitBtn) {
  forgotSubmitBtn.addEventListener('click', () => {
    const emailVal = document.getElementById('forgotEmail').value.trim();
    if(!emailVal) {
      alert('Please enter your college email ID.');
      return;
    }
    
    // Push a password changed successfully notification
    const records = JSON.parse(localStorage.getItem('aimsa_notifications')) || [];
    records.push({
      title: 'Password Changed Successfully',
      text: 'Your secure portal access credentials were changed.',
      indicator: 'green',
      recipient: emailVal,
      time: 'Just now',
      email: true
    });
    localStorage.setItem('aimsa_notifications', JSON.stringify(records));

    alert(`A secure password reset link has been successfully generated and sent to: ${emailVal}`);
    forgotStep.style.display = 'none';
    formStep.classList.add('show');
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
