 document.addEventListener('DOMContentLoaded', () => {
      const profileBtn = document.getElementById('profile-btn');
      const profileDropdown = document.getElementById('profile-dropdown');

      // Toggle dropdown when clicking the profile button
      profileBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevents the click from triggering the window event listener below immediately
        profileDropdown.classList.toggle('hidden');
      });

      // Close dropdown if user clicks anywhere else on the screen
      window.addEventListener('click', (e) => {
        if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
          profileDropdown.classList.add('hidden');
        }
      });
    });