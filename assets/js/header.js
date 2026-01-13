document.addEventListener('DOMContentLoaded', function() {
  // Get DOM elements
  const profileBtn = document.querySelector('.profile-btn');
  const dropdownMenu = document.querySelector('.dropdown-menu');
  const siteHeader = document.querySelector('.site-header');
  const profileAvatar = document.querySelector('.profile-avatar');
  
  // Toggle dropdown menu
  if (profileBtn && dropdownMenu) {
    profileBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      toggleDropdown();
    });
    
    // Also toggle on avatar click
    if (profileAvatar) {
      profileAvatar.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleDropdown();
      });
    }
  }
  
  function toggleDropdown() {
    const isActive = dropdownMenu.classList.contains('active');
    
    if (isActive) {
      closeDropdown();
    } else {
      openDropdown();
    }
  }
  
  function openDropdown() {
    dropdownMenu.classList.add('active');
    profileBtn.classList.add('active');
    
    // Add smooth entrance animation
    dropdownMenu.style.animation = 'fadeInDown 0.3s ease';
  }
  
  function closeDropdown() {
    dropdownMenu.classList.remove('active');
    profileBtn.classList.remove('active');
  }
  
  // Close dropdown when clicking outside
  document.addEventListener('click', function(e) {
    if (dropdownMenu && !dropdownMenu.contains(e.target) && 
        !profileBtn.contains(e.target) && 
        !profileAvatar.contains(e.target)) {
      closeDropdown();
    }
  });
  
  // Close dropdown on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && dropdownMenu.classList.contains('active')) {
      closeDropdown();
      profileBtn.focus();
    }
  });
  
  // Header scroll effect
  let lastScrollTop = 0;
  const scrollThreshold = 10;
  
  window.addEventListener('scroll', function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > scrollThreshold) {
      siteHeader.classList.add('scrolled');
    } else {
      siteHeader.classList.remove('scrolled');
    }
    
    lastScrollTop = scrollTop;
  }, { passive: true });
  
  // Dropdown keyboard navigation
  if (dropdownMenu) {
    const dropdownItems = dropdownMenu.querySelectorAll('.dropdown-item');
    
    dropdownMenu.addEventListener('keydown', function(e) {
      const focusedItem = document.activeElement;
      const currentIndex = Array.from(dropdownItems).indexOf(focusedItem);
      
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        const nextIndex = currentIndex < dropdownItems.length - 1 ? currentIndex + 1 : 0;
        dropdownItems[nextIndex].focus();
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prevIndex = currentIndex > 0 ? currentIndex - 1 : dropdownItems.length - 1;
        dropdownItems[prevIndex].focus();
      } else if (e.key === 'Home') {
        e.preventDefault();
        dropdownItems[0].focus();
      } else if (e.key === 'End') {
        e.preventDefault();
        dropdownItems[dropdownItems.length - 1].focus();
      }
    });
  }
  
  // Add smooth hover effect to avatar
  if (profileAvatar) {
    profileAvatar.addEventListener('mouseenter', function() {
      this.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
    });
    
    profileAvatar.addEventListener('mouseleave', function() {
      this.style.transition = 'all 0.3s ease';
    });
  }
  
  // Preload animation for smoother interactions
  function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }
  
  // Respect user's motion preferences
  if (prefersReducedMotion()) {
    document.documentElement.style.setProperty('--transition', 'none');
    document.documentElement.style.setProperty('--transition-fast', 'none');
  }
});