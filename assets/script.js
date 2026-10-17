


const loginTab = document.getElementById('loginTab');

    const signupTab = document.getElementById('signupTab');

    const loginPanel = document.getElementById('loginPanel');

    const signupPanel = document.getElementById('signupPanel');

    const welcomeTitle = document.getElementById('welcomeTitle');

    const welcomeSubtitle = document.getElementById('welcomeSubtitle');

    const toast = document.getElementById('toast');

    let toastTimer;



    function showView(view) {

      const isLogin = view === 'login';

      loginPanel.classList.toggle('hidden', !isLogin);

      signupPanel.classList.toggle('hidden', isLogin);

      loginTab.setAttribute('aria-selected', String(isLogin));

      signupTab.setAttribute('aria-selected', String(!isLogin));

      welcomeTitle.textContent = isLogin ? 'Welcome back' : 'Join the YTR community';

      welcomeSubtitle.textContent = isLogin

        ? 'Sign in to continue to your YTR workspace.'

        : 'Create an account request to get started with your chapter.';

      clearErrors();

    }

    loginTab.addEventListener('click', () => showView('login'));

    signupTab.addEventListener('click', () => showView('signup'));



    document.querySelectorAll('[data-toggle-password]').forEach(button => {

      button.addEventListener('click', () => {

        const input = document.getElementById(button.dataset.togglePassword);

        const reveal = input.type === 'password';

        input.type = reveal ? 'text' : 'password';

        button.textContent = reveal ? 'Hide' : 'Show';

        button.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');

      });

    });



    function clearErrors() {

      document.querySelectorAll('.form-error').forEach(el => { el.textContent = ''; el.classList.remove('visible'); });

      document.querySelectorAll('[aria-invalid="true"]').forEach(el => el.removeAttribute('aria-invalid'));

    }

    function showError(id, message, fields = []) {

      const node = document.getElementById(id);

      node.textContent = message;

      node.classList.add('visible');

      fields.forEach(fieldId => document.getElementById(fieldId)?.setAttribute('aria-invalid', 'true'));

    }

    function showToast(title, message, isError = false) {

      clearTimeout(toastTimer);

      toast.className = 'toast' + (isError ? ' error' : '');

      toast.innerHTML = '<strong></strong><span></span>';

      toast.querySelector('strong').textContent = title;

      toast.querySelector('span').textContent = message;

      toastTimer = setTimeout(() => toast.classList.add('hidden'), 6500);

    }

    function validEmail(value) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value); }



    // Load real chapters from Python backend API
    async function loadChapters() {
      try {
        const result = window.YTR_API ? await YTR_API.chapters.getAll() : await (await fetch('http://127.0.0.1:5000/api/chapters')).json();
        if (result.status === 'success' && Array.isArray(result.data)) {
          chapterName.innerHTML = '<option value="" selected disabled>Select your chapter</option>';
          result.data.forEach(ch => {
            const opt = document.createElement('option');
            opt.value = ch.id;
            opt.textContent = ch.chapter_name + (ch.province ? ` (${ch.province})` : '');
            chapterName.appendChild(opt);
          });
        }
      } catch (err) {
        console.warn('Could not load chapters dynamically:', err);
      }
    }
    loadChapters();

    // Live Login Handler using Python Backend
    document.getElementById('loginForm').addEventListener('submit', async event => {
      event.preventDefault(); 
      clearErrors();

      const email = document.getElementById('loginEmail');
      const password = document.getElementById('loginPassword');
      const submitBtn = event.target.querySelector('button[type="submit"]');

      if (!validEmail(email.value.trim()) || !password.value) {
        showError('loginError', 'Enter a valid email address and password.', [!validEmail(email.value.trim()) ? 'loginEmail' : '', !password.value ? 'loginPassword' : ''].filter(Boolean));
        return;
      }

      try {
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';

        const result = window.YTR_API 
          ? await YTR_API.auth.login(email.value.trim(), password.value)
          : await (await fetch('http://127.0.0.1:5000/api/auth/login', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ email: email.value.trim(), password: password.value })
            })).json();

        if (result.status === 'success') {
          const user = result.data.user;
          if (user) {
            localStorage.setItem('ytr_user', JSON.stringify(user));
            localStorage.setItem('ytr_role', user.role);
            if (user.chapter_id) {
              localStorage.setItem('ytr_chapter_id', user.chapter_id);
            }
            // Set cookies for server-side PHP guards
            document.cookie = "ytr_role=" + encodeURIComponent(user.role) + "; path=/; max-age=86400";
            document.cookie = "ytr_chapter_id=" + encodeURIComponent(user.chapter_id || '') + "; path=/; max-age=86400";
            document.cookie = "ytr_user_id=" + encodeURIComponent(user.id) + "; path=/; max-age=86400";
          }
          const isChapterAdmin = (user && user.role === 'chapter_admin');
          const roleLabel = isChapterAdmin ? 'Chapter Administrator' : 'National Administrator';
          const targetUrl = isChapterAdmin ? 'ChapterDashboard.php' : 'Dashboard.php';

          showToast('Welcome back!', `Login successful as ${roleLabel}. Redirecting to ${isChapterAdmin ? 'Chapter Workspace' : 'National Dashboard'}...`);
          setTimeout(() => {
            window.location.href = targetUrl;
          }, 800);
        } else {
          showError('loginError', result.message || 'Login failed.');
        }
      } catch (err) {
        showError('loginError', 'Could not reach the Python backend API (http://127.0.0.1:5000). Please ensure it is running.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
      }
    });

    const requestedRole = document.getElementById('requestedRole');
    const chapterField = document.getElementById('chapterField');
    const chapterName = document.getElementById('chapterName');

    requestedRole.addEventListener('change', () => {
      const national = requestedRole.value === 'national_admin';
      chapterField.classList.toggle('hidden', national);
      chapterName.required = !national;
      if (national) chapterName.value = '';
    });

    // Live Registration (Account Request) Handler
    document.getElementById('signupForm').addEventListener('submit', async event => {
      event.preventDefault(); 
      clearErrors();

      const fullName = document.getElementById('fullName');
      const email = document.getElementById('signupEmail');
      const password = document.getElementById('signupPassword');
      const confirmPassword = document.getElementById('confirmPassword');
      const agree = document.getElementById('agreeTerms');
      const submitBtn = event.target.querySelector('button[type="submit"]');

      const errors = [];
      const invalid = [];

      if (!fullName.value.trim()) { errors.push('Enter your full name.'); invalid.push('fullName'); }
      if (!validEmail(email.value.trim())) { errors.push('Enter a valid email address.'); invalid.push('signupEmail'); }
      if (!requestedRole.value) { errors.push('Select the role you are requesting.'); invalid.push('requestedRole'); }
      if (requestedRole.value === 'chapter_admin' && !chapterName.value) { errors.push('Select your chapter.'); invalid.push('chapterName'); }
      if (password.value.length < 8) { errors.push('Your password must contain at least 8 characters.'); invalid.push('signupPassword'); }
      if (password.value !== confirmPassword.value) { errors.push('The passwords do not match.'); invalid.push('confirmPassword'); }
      if (!agree.checked) { errors.push('Confirm the account request statement.'); invalid.push('agreeTerms'); }

      if (errors.length) {
        showError('signupError', errors[0], invalid);
        return;
      }

      try {
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';

        const payload = {
          full_name: fullName.value.trim(),
          email: email.value.trim(),
          password: password.value,
          requested_role: requestedRole.value,
          chapter_id: chapterName.value || null
        };

        const result = window.YTR_API 
          ? await YTR_API.auth.register(payload)
          : await (await fetch('http://127.0.0.1:5000/api/auth/register', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload)
            })).json();

        if (result.status === 'success') {
          showToast('Account Request Submitted', result.message);
          document.getElementById('signupForm').reset();
          chapterField.classList.remove('hidden');
          chapterName.required = true;
          setTimeout(() => showView('login'), 2000);
        } else {
          showError('signupError', result.message || 'Registration failed.');
        }
      } catch (err) {
        showError('signupError', 'Could not reach the Python backend API (http://127.0.0.1:5000). Please ensure it is running.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
      }
    });



    document.getElementById('forgotPassword').addEventListener('click', () => {

      showToast('Password recovery', 'Connect this control to your account recovery flow or contact your YTR administrator.', true);

    });
