<!-- ============ FOOTER ============ -->
<footer>
  <div class="container">
    <div class="footer-top" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:30px; padding-bottom:30px; border-bottom:1px solid var(--line);">
      <div class="footer-brand" style="display:flex; gap:14px; align-items:center; max-width:360px;">
        <div class="logo-badge" style="width:52px;height:52px; display:flex; align-items:center; justify-content:center; background:var(--white); border-radius:50%;">
          <img src="images/aimsa_logo.jpg" alt="AIMSA Logo">
        </div>
        <div>
          <b style="color:var(--white); font-family:var(--ff-display); font-size:1.1rem;">Department of AIML</b>
          <p style="font-size:0.75rem; color:var(--muted); margin-top:4px; line-height:1.4;">Zeal College of Engineering and Research, Pune.</p>
        </div>
      </div>
      <div class="footer-cols" style="display:flex; gap:40px; flex-wrap:wrap;">
        <div class="footer-col">
          <h5>Explore</h5>
          <a href="#about">About</a>
          <a href="#committee">Committee</a>
          <a href="#meetings">Meetings</a>
          <a href="#gallery">Gallery</a>
        </div>
        <div class="footer-col">
          <h5>Portal</h5>
          <a href="#" id="footerLoginLink">Login</a>
          <a href="#contact">Help Desk</a>
          <a href="#announcements">Announcements</a>
        </div>
        <div class="footer-col">
          <h5>Support &amp; Contact</h5>
          <a href="mailto:support.aimsa@zealeducation.com" style="color:var(--accent);">support.aimsa@zealeducation.com</a>
          <a href="tel:+912067206000">+91 20 6720 6000</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; padding-top:20px; font-size:0.78rem; color:var(--muted); gap:12px;">
      <span>© 2026 <b>Department of AIML</b>, Zeal College of Engineering and Research, Pune. All rights reserved.</span>
      <div style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
        <a href="#" onclick="alert('Privacy Policy: All membership data is kept strictly confidential within Zeal Society.')" style="color:inherit; text-decoration:none;">Privacy Policy</a>
        <a href="#" onclick="alert('Terms &amp; Conditions: AIMSA portal usage is governed by college guidelines.')" style="color:inherit; text-decoration:none;">Terms &amp; Conditions</a>
        <span style="color:var(--line);">|</span>
        <span>Version: <b>v2.1.0</b></span>
        <span>Last Updated: <b>July 21, 2026</b></span>
      </div>
    </div>
  </div>
</footer>

<!-- ============ LOGIN MODAL ============ -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <button class="modal-close" id="modalCloseBtn"><svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg></button>

    <!-- STEP 1: Select Role -->
    <div id="roleStep">
      <h3>Login to AIMSA Portal</h3>
      <p class="sub">Select your role to continue to secure sign-in.</p>
      <div class="role-grid">
        <button class="role-card" data-role="HOD">
          <div class="ic"><svg viewBox="0 0 24 24"><path d="M12 2l8 4v6c0 5-3.4 8.4-8 10-4.6-1.6-8-5-8-10V6l8-4z"/></svg></div>
          <b>HOD</b><span>Full portal &amp; department oversight</span>
        </button>
        <button class="role-card" data-role="Faculty Coordinator">
          <div class="ic"><svg viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5zM3 9v6l9 5 9-5V9"/></svg></div>
          <b>Faculty Coordinator</b><span>Approvals &amp; guidance</span>
        </button>
        <button class="role-card" data-role="Association President">
          <div class="ic"><svg viewBox="0 0 24 24"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg></div>
          <b>Association President</b><span>Chapter leadership</span>
        </button>
        <button class="role-card" data-role="Committee Member">
          <div class="ic"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M10 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div>
          <b>Committee Member</b><span>Event &amp; ops access</span>
        </button>
        <button class="role-card" data-role="Student Member" style="grid-column:1/-1;">
          <div class="ic"><svg viewBox="0 0 24 24"><path d="M22 10L12 5 2 10l10 5 10-5zM6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"/></svg></div>
          <b>Student Member</b><span>Association updates &amp; events</span>
        </button>
      </div>
    </div>

    <!-- STEP 2: Login or Register Form -->
    <div id="formStep" class="login-form">
      <span class="back-link" id="backToRoles" style="cursor:pointer;">← Back to roles</span>
      <div class="selected-role-chip" id="roleChip">Student Member</div>

      <!-- Auth Action Tabs -->
      <div style="display:flex; border-bottom:1.5px solid var(--line-dark); margin-bottom:18px;">
        <button id="tabLogin" style="flex:1; padding:10px; background:none; border:none; border-bottom:2px solid var(--accent); font-weight:700; color:var(--navy-950);">Sign In</button>
        <button id="tabRegister" style="flex:1; padding:10px; background:none; border:none; border-bottom:2px solid transparent; font-weight:500; color:var(--muted-dark);">Register</button>
      </div>

      <!-- Sign In Fields -->
      <div id="signInSection" style="display:flex; flex-direction:column; gap:14px;">
        <div>
          <label>College Email ID</label>
          <input type="email" id="loginEmail" placeholder="you@zealeducation.com">
        </div>
        <div>
          <label>Password</label>
          <div style="position:relative;">
            <input type="password" id="loginPassword" placeholder="••••••••" style="padding-right:40px; width:100%; border:1.5px solid var(--line-dark); border-radius:10px; padding:12px 14px; font-size:.92rem;">
            <button type="button" class="toggle-password" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--muted-dark);" onclick="togglePasswordVisibility('loginPassword')">👁️</button>
          </div>
        </div>
        <div class="form-row-between">
          <label style="display:flex; align-items:center; gap:6px; font-weight:500;"><input type="checkbox" id="keepSignedIn" style="width:auto;"> Keep me signed in</label>
          <a href="#" id="forgotLink">Forgot password?</a>
        </div>
        <button class="btn btn-primary" style="width:100%; margin-top:6px; padding:13px;" id="loginSubmitBtn">Secure Login →</button>
      </div>

      <!-- Register Fields -->
      <div id="registerSection" style="display:none; flex-direction:column; gap:14px;">
        <div>
          <label>Full Name</label>
          <input type="text" id="regName" placeholder="John Doe" style="width:100%; border:1.5px solid var(--line-dark); border-radius:10px; padding:12px 14px; font-size:.92rem;">
        </div>
        <div>
          <label>Student/Faculty ID</label>
          <input type="text" id="regID" placeholder="ZES-AIML-2026-102" style="width:100%; border:1.5px solid var(--line-dark); border-radius:10px; padding:12px 14px; font-size:.92rem;">
        </div>
        <div>
          <label>College Email ID</label>
          <input type="email" id="regEmail" placeholder="you@zealeducation.com" style="width:100%; border:1.5px solid var(--line-dark); border-radius:10px; padding:12px 14px; font-size:.92rem;">
        </div>
        <div>
          <label>Password</label>
          <div style="position:relative;">
            <input type="password" id="regPassword" placeholder="••••••••" style="padding-right:40px; width:100%; border:1.5px solid var(--line-dark); border-radius:10px; padding:12px 14px; font-size:.92rem;">
            <button type="button" class="toggle-password" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--muted-dark);" onclick="togglePasswordVisibility('regPassword')">👁️</button>
          </div>
          <div id="pwdStrengthWrapper" style="margin-top:6px;">
            <div style="display:flex; justify-content:space-between; font-size:0.75rem; color:var(--muted-dark); margin-bottom:4px;">
              <span>Password Strength</span>
              <span id="pwdStrengthLabel">Too Weak</span>
            </div>
            <div style="height:6px; background:var(--paper-dim); border-radius:3px; overflow:hidden;">
              <div id="pwdStrengthBar" style="height:100%; width:0%; background:#dc2626; transition:all 0.3s ease;"></div>
            </div>
          </div>
        </div>
        <div>
          <label>Confirm Password</label>
          <input type="password" id="regConfirmPassword" placeholder="••••••••" style="width:100%; border:1.5px solid var(--line-dark); border-radius:10px; padding:12px 14px; font-size:.92rem;">
        </div>
        <button class="btn btn-primary" style="width:100%; margin-top:6px; padding:13px;" id="registerSubmitBtn">Create Account →</button>
      </div>

      <div class="secure-note"><svg viewBox="0 0 24 24"><path d="M12 2l8 4v6c0 5-3.4 8.4-8 10-4.6-1.6-8-5-8-10V6l8-4z"/></svg> Encrypted login · role-based access · auto session timeout</div>
    </div>

    <!-- STEP 3: Forgot Password Form -->
    <div id="forgotStep" style="display:none; flex-direction:column; gap:16px;">
      <span class="back-link" id="backToLogin" style="cursor:pointer; margin-bottom:8px;">← Back to sign in</span>
      <h3>Reset Password</h3>
      <p class="sub">Enter your college email ID. We will send a secure password reset link.</p>
      <div>
        <label>College Email ID</label>
        <input type="email" id="forgotEmail" placeholder="you@zealeducation.com" style="width:100%; border:1.5px solid var(--line-dark); border-radius:10px; padding:12px 14px; font-size:.92rem;">
      </div>
      <button class="btn btn-primary" style="width:100%; padding:13px;" id="forgotSubmitBtn">Send Reset Link →</button>
    </div>
  </div>
</div>
