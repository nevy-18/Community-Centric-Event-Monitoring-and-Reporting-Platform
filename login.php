<!DOCTYPE html>

<html lang="en">

<head>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#122b4a" />
  <title>YTR | Sign in</title>
    <link rel="stylesheet" href="assets/style.css" />
    <script>
      // Clear previous session when returning to login page
      localStorage.removeItem('ytr_user');
      localStorage.removeItem('ytr_role');
      localStorage.removeItem('ytr_chapter_id');
      sessionStorage.clear();
      document.cookie = "ytr_role=; path=/; max-age=0;";
      document.cookie = "ytr_chapter_id=; path=/; max-age=0;";
      document.cookie = "ytr_user_id=; path=/; max-age=0;";
    </script>
</head>

<body>

  <main class="page-shell">

    <aside class="brand-panel" aria-label="Youth on the Rock introduction">

      <div class="brand-lockup">

        <div class="brand-mark" aria-hidden="true">

          <img src="assets/images/LOGO.jpg" alt="" id="LOGO">

        </div>

        <div><div class="brand-name">YOUTH ON THE ROCK</div><div class="brand-subtitle">Community platform</div></div>

      </div>
      
      <section class="hero-copy">
        <div class="eyebrow">One community. One mission.</div>

        <h1>Changing a nation by changing a <span>generation.</span></h1>

        <p>A shared space for chapters to coordinate events, follow poster requests, submit reports, and stay connected with the YTR community.</p>

        <div class="feature-list">

          <div class="feature"><span class="feature-icon"><svg viewBox="0 0 24 24" fill="none"><rect x="3.5" y="5" width="17" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 3.5v3M16 3.5v3M4 9h16M8 13h3M8 16h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><span>Coordinate events across partner chapters</span></div>

          <div class="feature"><span class="feature-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M5 3.5h10l4 4V20a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 5 20V3.5Z" stroke="currentColor" stroke-width="1.8"/><path d="M14.5 3.8v4h4M8 12h8M8 16h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><span>Track poster requests and event reports</span></div>

          <div class="feature"><span class="feature-icon"><svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3.3" stroke="currentColor" stroke-width="1.8"/><path d="M2.8 20c.5-3.4 2.7-5.2 6.2-5.2s5.7 1.8 6.2 5.2M16 5.2a3.3 3.3 0 0 1 0 6.3M17 15c2.5.3 3.9 2 4.2 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span><span>Keep National and chapter teams aligned</span></div>

        </div>
        
      </section>

      <footer class="brand-footer"><span>© 2026 Youth on the Rock</span><span>Serve • Connect • Grow</span></footer>

    </aside>



    <section class="form-panel">

      <div class="auth-wrap">

        <div class="mobile-brand">

          <div class="brand-mark" aria-hidden="true"><svg viewBox="0 0 32 32" fill="none"><path d="M16 3.5 28 10.3v11.4L16 28.5 4 21.7V10.3L16 3.5Z" stroke="currentColor" stroke-width="2.2"/><path d="M16 9v14M9 16h14" stroke="currentColor" stroke-width="3.3" stroke-linecap="round"/></svg></div>

          <div><div class="brand-name">YOUTH ON THE ROCK</div><div class="brand-subtitle">Community platform</div></div>

        </div>

        <header class="welcome">

          <h2 id="welcomeTitle">Welcome back</h2>

          <p id="welcomeSubtitle">Sign in to continue to your YTR workspace.</p>

        </header>

        <div class="tab-switch" role="tablist" aria-label="Account access">

          <button type="button" class="tab-button" id="loginTab" role="tab" aria-selected="true" aria-controls="loginForm"><svg viewBox="0 0 24 24" fill="none" style="width: 15px; height: 15px; display: inline-block; vertical-align: middle; margin-right: 6px;"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>Log in</button>

          <button type="button" class="tab-button" id="signupTab" role="tab" aria-selected="false" aria-controls="signupForm"><svg viewBox="0 0 24 24" fill="none" style="width: 15px; height: 15px; display: inline-block; vertical-align: middle; margin-right: 6px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M8.5 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM20 8v6M23 11h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>Sign up</button>

        </div>



        <section class="form-card" id="loginPanel">

          <form id="loginForm" novalidate>

            <h3 class="form-heading">Log in to your account</h3>

            <p class="form-hint">Use the email address connected to your approved YTR account.</p>

            <div class="field full">

              <label for="loginEmail">Email address</label>

              <input id="loginEmail" name="email" type="email" autocomplete="username" placeholder="you@example.com" required />

            </div>

            <div class="field full">

              <label for="loginPassword">Password</label>

              <div class="input-wrap"><input class="password-input" id="loginPassword" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required /><button class="show-password" type="button" data-toggle-password="loginPassword" aria-label="Show password">Show</button></div>

            </div>

            <div class="form-row">

              <label class="check-label"><input type="checkbox" id="rememberMe" /> Remember me</label>

              <button type="button" class="text-link" id="forgotPassword">Forgot password?</button>

            </div>

            <p class="form-error" id="loginError" role="alert"></p>

            <button class="primary-button" type="submit">Log in <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg></button>

            <p class="privacy-note">Access is available to <strong>approved YTR accounts</strong>. New accounts must be reviewed by an administrator.</p>

          </form>

        </section>



        <section class="form-card hidden" id="signupPanel">

          <form id="signupForm" novalidate>

            <h3 class="form-heading">Request an account</h3>

            <p class="form-hint">Submit your details for review. Your account will remain pending until an administrator approves it.</p>

            <div class="field-grid">

              <div class="field">

                <label for="fullName">Full name</label>

                <input id="fullName" name="full_name" type="text" autocomplete="name" placeholder="Your full name" required />

              </div>

              <div class="field">

                <label for="signupEmail">Email address</label>

                <input id="signupEmail" name="email" type="email" autocomplete="email" placeholder="you@example.com" required />

              </div>

              <div class="field">

                <label for="requestedRole">Requested role</label>

                <select id="requestedRole" name="requested_role" required>

                  <option value="" selected disabled>Select your role / position</option>

                  <optgroup label="NATIONAL">
                    <option value="Executive Director">Executive Director</option>
                    <option value="Regional Director (National)">Regional Director (National)</option>
                    <option value="National Youth Officer">National Youth Officer</option>
                  </optgroup>

                  <optgroup label="CHAPTER">
                    <option value="Chapter Coordinator">Chapter Coordinator</option>
                    <option value="Chapter President">Chapter President</option>
                    <option value="Regional Director (Chapter)">Regional Director (Chapter)</option>
                  </optgroup>

                </select>

              </div>

              <div class="field" id="chapterField">

                <label for="chapterName">Partner Chapter</label>

                <select id="chapterName" name="chapter_id">

                  <option value="1" selected>Laguna Chapter</option>

                </select>

              </div>

              <div class="field">

                <label for="signupPassword">Create password</label>

                <div class="input-wrap"><input class="password-input" id="signupPassword" name="password" type="password" autocomplete="new-password" minlength="8" placeholder="At least 8 characters" required /><button class="show-password" type="button" data-toggle-password="signupPassword" aria-label="Show password">Show</button></div>

              </div>

              <div class="field">

                <label for="confirmPassword">Confirm password</label>

                <div class="input-wrap"><input class="password-input" id="confirmPassword" name="confirm_password" type="password" autocomplete="new-password" minlength="8" placeholder="Re-enter password" required /><button class="show-password" type="button" data-toggle-password="confirmPassword" aria-label="Show password">Show</button></div>

              </div>

            </div>

            <label class="check-label terms-label"><input type="checkbox" id="agreeTerms" required class="terms-checkbox" /> <span>I confirm these details are accurate and understand that account access requires administrator approval.</span></label>

            <p class="form-error" id="signupError" role="alert"></p>

            <button class="primary-button" type="submit">Submit account request <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg></button>

            <div class="approval-note"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3 20 6v5.5c0 4.5-3.1 7.7-8 9.5-4.9-1.8-8-5-8-9.5V6l8-3Z" stroke="currentColor" stroke-width="1.7"/><path d="m8.5 12 2.2 2.2 4.8-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg><span><strong>Admin approval required.</strong> Selecting a role submits a request only; it does not grant administrator privileges automatically.</span></div>

          </form>

        </section>

        <p class="privacy-note">Youth on the Rock · Community-Centric Event Monitoring and Reporting Platform</p>

      </div>

    </section>

  </main>

  <!-- Forgot Password Recovery Modal -->
  <div class="auth-modal-overlay hidden" id="forgotPasswordModal" role="dialog" aria-modal="true" aria-labelledby="forgotModalTitle">
    <div class="auth-modal-card">
      <div class="auth-modal-header">
        <div class="auth-modal-icon">
          <svg viewBox="0 0 24 24" fill="none" width="22" height="22">
            <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div>
          <h3 class="auth-modal-title" id="forgotModalTitle">Reset Account Password</h3>
          <p class="auth-modal-subtitle">Submit your email address. The National Administration can issue a temporary password for your account.</p>
        </div>
        <button type="button" class="auth-modal-close" id="btnCloseForgotModal" aria-label="Close modal">&times;</button>
      </div>

      <form id="forgotPasswordForm" class="auth-modal-form" novalidate>
        <div class="field full">
          <label for="forgotEmail">Registered Email Address</label>
          <input type="email" id="forgotEmail" name="email" placeholder="name@ytr.org" autocomplete="email" required />
        </div>

        <div class="field full">
          <label for="forgotNotes">Additional Note (Optional)</label>
          <textarea id="forgotNotes" name="notes" rows="2" placeholder="e.g. Locked out of device, please provide temporary password" class="auth-modal-textarea"></textarea>
        </div>

        <div class="auth-modal-info">
          <svg viewBox="0 0 24 24" fill="none" width="16" height="16">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/>
            <path d="M12 16v-4m0-4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <span>Once submitted, your request will appear in the National Admin reset queue. The National Admin will provide a temporary password for your account.</span>
        </div>

        <p class="form-error" id="forgotError" role="alert"></p>
        <div class="form-success hidden" id="forgotSuccess" role="status"></div>

        <div class="auth-modal-actions">
          <button type="button" class="btn-cancel" id="btnCancelForgotModal">Cancel</button>
          <button type="submit" class="primary-button" id="btnSubmitForgot" style="min-height: 42px; width: auto; padding: 0 20px;">
            <span>Submit Request</span>
            <svg viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="toast hidden" id="toast" role="status" aria-live="polite"></div>



    <script src="assets/api-client.js"></script>
    <script src="assets/script.js" defer></script>
</body>

</html>
