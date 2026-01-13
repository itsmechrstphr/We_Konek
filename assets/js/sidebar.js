/**
 * We Konek - Enhanced Sidebar JavaScript
 * Modern drawer sidebar with smooth animations and interactions
 */

document.addEventListener('DOMContentLoaded', function() {
  initializeSidebar();
  handleResponsiveLayout();
  enhanceSidebarAccessibility();
  persistSidebarState();
  initializeTooltips();
  highlightCurrentPage();
});

/**
 * Initialize Sidebar Functionality
 */
function initializeSidebar() {
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarClose = document.getElementById('sidebarClose');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const mainContent = document.getElementById('mainContent');

  if (!sidebar || !sidebarToggle) {
    console.warn('Sidebar elements not found');
    return;
  }

  sidebarToggle.addEventListener('click', function(e) {
    e.stopPropagation();
    toggleSidebar();
  });

  if (sidebarClose) {
    sidebarClose.addEventListener('click', function() {
      closeSidebar();
    });
  }

  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', function() {
      closeSidebar();
    });
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && sidebar.classList.contains('active')) {
      closeSidebar();
    }
  });

  const navLinks = document.querySelectorAll('.nav-link');
  navLinks.forEach(link => {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 1024) {
        setTimeout(() => {
          closeSidebar();
        }, 250);
      }
    });
  });

  addHoverEffects();
}

/**
 * Toggle Sidebar Open/Close
 */
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const mainContent = document.getElementById('mainContent');

  if (!sidebar) return;

  const isActive = sidebar.classList.contains('active');

  if (window.innerWidth > 1024) {
    sidebar.classList.toggle('collapsed');
    if (mainContent) {
      mainContent.classList.toggle('sidebar-collapsed');
    }
    
    saveState('collapsed', sidebar.classList.contains('collapsed'));
  } else {
    if (isActive) {
      closeSidebar();
    } else {
      openSidebar();
    }
  }

  if (sidebarToggle) {
    sidebarToggle.classList.toggle('active');
  }

  updateAriaExpanded();
}

/**
 * Open Sidebar
 */
function openSidebar() {
  const sidebar = document.getElementById('sidebar');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const sidebarToggle = document.getElementById('sidebarToggle');

  if (sidebar) {
    sidebar.classList.add('active');
  }

  if (sidebarOverlay) {
    sidebarOverlay.classList.add('active');
  }

  if (sidebarToggle) {
    sidebarToggle.classList.add('active');
  }

  if (window.innerWidth <= 1024) {
    document.body.style.overflow = 'hidden';
  }

  saveState('active', true);
  updateAriaExpanded();
}

/**
 * Close Sidebar
 */
function closeSidebar() {
  const sidebar = document.getElementById('sidebar');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const sidebarToggle = document.getElementById('sidebarToggle');

  if (sidebar) {
    sidebar.classList.remove('active');
  }

  if (sidebarOverlay) {
    sidebarOverlay.classList.remove('active');
  }

  if (sidebarToggle) {
    sidebarToggle.classList.remove('active');
  }

  document.body.style.overflow = '';

  saveState('active', false);
  updateAriaExpanded();
}

/**
 * Handle Responsive Layout Changes
 */
function handleResponsiveLayout() {
  let resizeTimer;

  window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
      const sidebar = document.getElementById('sidebar');
      const sidebarOverlay = document.getElementById('sidebarOverlay');
      const mainContent = document.getElementById('mainContent');

      if (window.innerWidth > 1024) {
        if (sidebarOverlay) {
          sidebarOverlay.classList.remove('active');
        }
        sidebar.classList.remove('active');
        document.body.style.overflow = '';

        const wasCollapsed = loadState('collapsed');
        if (wasCollapsed) {
          sidebar.classList.add('collapsed');
          if (mainContent) {
            mainContent.classList.add('sidebar-collapsed');
          }
        }
      } else {
        sidebar.classList.remove('collapsed');
        if (mainContent) {
          mainContent.classList.remove('sidebar-collapsed');
        }
      }

      updateAriaExpanded();
    }, 250);
  });
}

/**
 * Enhance Sidebar Accessibility
 */
function enhanceSidebarAccessibility() {
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const navLinks = document.querySelectorAll('.nav-link');

  if (sidebar) {
    sidebar.setAttribute('role', 'navigation');
    sidebar.setAttribute('aria-label', 'Main navigation');
  }

  if (sidebarToggle) {
    sidebarToggle.setAttribute('aria-expanded', 'false');
    sidebarToggle.setAttribute('aria-controls', 'sidebar');
  }

  navLinks.forEach((link, index) => {
    link.setAttribute('tabindex', '0');

    link.addEventListener('keydown', function(e) {
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        const next = navLinks[index + 1];
        if (next) next.focus();
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prev = navLinks[index - 1];
        if (prev) prev.focus();
      } else if (e.key === 'Home') {
        e.preventDefault();
        navLinks[0].focus();
      } else if (e.key === 'End') {
        e.preventDefault();
        navLinks[navLinks.length - 1].focus();
      } else if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        link.click();
      }
    });
  });

  if (window.innerWidth <= 1024) {
    trapFocusInSidebar();
  }
}

/**
 * Update ARIA Expanded Attribute
 */
function updateAriaExpanded() {
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');

  if (!sidebar || !sidebarToggle) return;

  const isExpanded = sidebar.classList.contains('active') || 
                     (window.innerWidth > 1024 && !sidebar.classList.contains('collapsed'));
  
  sidebarToggle.setAttribute('aria-expanded', isExpanded.toString());
}

/**
 * Trap Focus Within Sidebar (Mobile)
 */
function trapFocusInSidebar() {
  const sidebar = document.getElementById('sidebar');

  document.addEventListener('keydown', function(e) {
    if (!sidebar.classList.contains('active')) return;

    const focusableElements = sidebar.querySelectorAll(
      'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
    );
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];

    if (e.key === 'Tab') {
      if (e.shiftKey) {
        if (document.activeElement === firstElement) {
          e.preventDefault();
          lastElement.focus();
        }
      } else {
        if (document.activeElement === lastElement) {
          e.preventDefault();
          firstElement.focus();
        }
      }
    }
  });
}

/**
 * Persist Sidebar State
 */
function persistSidebarState() {
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');

  if (window.innerWidth > 1024) {
    const wasCollapsed = loadState('collapsed');
    if (wasCollapsed) {
      sidebar.classList.add('collapsed');
      if (mainContent) {
        mainContent.classList.add('sidebar-collapsed');
      }
    }
  }

  updateAriaExpanded();
}

/**
 * Save State
 */
function saveState(key, value) {
  try {
    const stateKey = `sidebar_${key}`;
    const stateValue = JSON.stringify(value);
    const tempStorage = {};
    tempStorage[stateKey] = stateValue;
  } catch (error) {
    console.warn('Could not save sidebar state:', error);
  }
}

/**
 * Load State
 */
function loadState(key) {
  try {
    return false;
  } catch (error) {
    console.warn('Could not load sidebar state:', error);
    return false;
  }
}

/**
 * Highlight Current Page
 */
function highlightCurrentPage() {
  const currentPath = window.location.pathname;
  const navLinks = document.querySelectorAll('.nav-link');

  navLinks.forEach(link => {
    const linkPath = new URL(link.href, window.location.origin).pathname;
    const currentPage = currentPath.split('/').pop();
    const linkPage = linkPath.split('/').pop();
    
    if (currentPage === linkPage) {
      link.classList.add('active');
      link.setAttribute('aria-current', 'page');
    } else {
      link.classList.remove('active');
      link.removeAttribute('aria-current');
    }
  });
}

/**
 * Initialize Tooltips
 */
function initializeTooltips() {
  const sidebar = document.getElementById('sidebar');
  const navLinks = document.querySelectorAll('.nav-link');

  navLinks.forEach(link => {
    const tooltipText = link.getAttribute('title') || link.querySelector('.nav-text')?.textContent;
    
    link.addEventListener('mouseenter', function() {
      if (sidebar.classList.contains('collapsed') && window.innerWidth > 1024) {
        const tooltip = createTooltip(tooltipText);
        this.appendChild(tooltip);
        
        setTimeout(() => {
          tooltip.style.opacity = '1';
          tooltip.style.transform = 'translateY(-50%) translateX(0)';
        }, 10);
      }
    });

    link.addEventListener('mouseleave', function() {
      const tooltip = this.querySelector('.nav-tooltip');
      if (tooltip) {
        tooltip.style.opacity = '0';
        tooltip.style.transform = 'translateY(-50%) translateX(-8px)';
        setTimeout(() => tooltip.remove(), 150);
      }
    });
  });
}

/**
 * Create Tooltip Element
 */
function createTooltip(text) {
  const tooltip = document.createElement('div');
  tooltip.className = 'nav-tooltip';
  tooltip.textContent = text;
  tooltip.setAttribute('role', 'tooltip');
  return tooltip;
}

/**
 * Add Hover Effects
 */
function addHoverEffects() {
  const navLinks = document.querySelectorAll('.nav-link');
  const logoutBtn = document.querySelector('.logout-btn');

  [...navLinks, logoutBtn].forEach(element => {
    if (!element) return;
    
    element.addEventListener('click', function(e) {
      const ripple = document.createElement('span');
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size / 2;
      const y = e.clientY - rect.top - size / 2;

      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = x + 'px';
      ripple.style.top = y + 'px';
      ripple.classList.add('ripple');
      ripple.style.cssText += `
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        pointer-events: none;
        animation: ripple-animation 0.6s ease-out;
      `;

      this.style.position = 'relative';
      this.style.overflow = 'hidden';
      this.appendChild(ripple);

      setTimeout(() => ripple.remove(), 600);
    });
  });
}

const style = document.createElement('style');
style.textContent = `
  @keyframes ripple-animation {
    from {
      transform: scale(0);
      opacity: 1;
    }
    to {
      transform: scale(2);
      opacity: 0;
    }
  }
`;
document.head.appendChild(style);

window.toggleSidebar = toggleSidebar;
window.openSidebar = openSidebar;
window.closeSidebar = closeSidebar;