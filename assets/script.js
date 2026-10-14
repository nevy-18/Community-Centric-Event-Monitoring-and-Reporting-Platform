


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



    document.getElementById('loginForm').addEventListener('submit', event => {

      event.preventDefault(); clearErrors();

      const email = document.getElementById('loginEmail');

      const password = document.getElementById('loginPassword');

      if (!validEmail(email.value.trim()) || !password.value) {

        showError('loginError', 'Enter a valid email address and password.', [!validEmail(email.value.trim()) ? 'loginEmail' : '', !password.value ? 'loginPassword' : ''].filter(Boolean));

        return;

      }

      showToast('Login form is ready', 'This page is a front-end prototype. Connect it to your PHP login endpoint to verify credentials securely.', true);

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



    document.getElementById('signupForm').addEventListener('submit', event => {

      event.preventDefault(); clearErrors();

      const fullName = document.getElementById('fullName');

      const email = document.getElementById('signupEmail');

      const password = document.getElementById('signupPassword');

      const confirmPassword = document.getElementById('confirmPassword');

      const agree = document.getElementById('agreeTerms');

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

      // Prototype only: intentionally does not save data or grant access.

      showToast('Account request form completed', 'This is a UI prototype. Connect it to your PHP backend to save the request as pending approval in MySQL.');

      document.getElementById('signupForm').reset();

      chapterField.classList.remove('hidden');

      chapterName.required = true;

    });



    document.getElementById('forgotPassword').addEventListener('click', () => {

      showToast('Password recovery', 'Connect this control to your account recovery flow or contact your YTR administrator.', true);

    });
