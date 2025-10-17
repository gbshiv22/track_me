// Track Me App - Complete JavaScript
console.log('Track Me App loaded successfully');

// Initialize Alpine.js-like functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing Track Me app');
    
    // Initialize dropdowns
    initializeDropdowns();
    
    // Initialize mobile menu
    initializeMobileMenu();
    
    // Initialize any other components
    initializeComponents();
});

function initializeDropdowns() {
    // Simple dropdown functionality without Alpine.js
    const dropdownButtons = document.querySelectorAll('[data-dropdown-toggle]');
    
    dropdownButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = document.getElementById(this.getAttribute('data-dropdown-toggle'));
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[data-dropdown-toggle]')) {
            const openDropdowns = document.querySelectorAll('.dropdown-menu:not(.hidden)');
            openDropdowns.forEach(dropdown => {
                dropdown.classList.add('hidden');
            });
        }
    });
}

function initializeMobileMenu() {
    // Mobile menu toggle functionality
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    const closeIcon = document.getElementById('close-icon');

    console.log('Mobile menu initialization - elements found:', {
        mobileMenuButton: !!mobileMenuButton,
        mobileMenu: !!mobileMenu,
        menuIcon: !!menuIcon,
        closeIcon: !!closeIcon
    });

    if (mobileMenuButton && mobileMenu && menuIcon && closeIcon) {
        console.log('Mobile menu elements found - adding event listener');
        
        mobileMenuButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isOpen = !mobileMenu.classList.contains('hidden');
            console.log('Mobile menu clicked, isOpen:', isOpen);
            console.log('Mobile menu classes before:', mobileMenu.className);
            
            if (isOpen) {
                console.log('Closing mobile menu');
                mobileMenu.classList.add('hidden');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            } else {
                console.log('Opening mobile menu');
                mobileMenu.classList.remove('hidden');
                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            }
            
            console.log('Mobile menu classes after:', mobileMenu.className);
            console.log('Menu icon classes:', menuIcon.className);
            console.log('Close icon classes:', closeIcon.className);
        });
        
        console.log('Mobile menu event listener added successfully');
    } else {
        console.log('Mobile menu elements not found - missing:', {
            mobileMenuButton: !mobileMenuButton,
            mobileMenu: !mobileMenu,
            menuIcon: !menuIcon,
            closeIcon: !closeIcon
        });
    }
}

function initializeUserDropdown() {
    // User dropdown toggle functionality
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');

    if (userMenuButton && userMenu) {
        console.log('User menu elements found');
        
        userMenuButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isOpen = !userMenu.classList.contains('hidden');
            console.log('User menu clicked, isOpen:', isOpen);
            
            if (isOpen) {
                userMenu.classList.add('hidden');
            } else {
                userMenu.classList.remove('hidden');
            }
        });
    } else {
        console.log('User menu elements not found');
    }
}

function initializeComponents() {
    // Initialize user dropdown
    initializeUserDropdown();
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const userMenuContainer = document.getElementById('user-menu-container');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const userMenu = document.getElementById('user-menu');
        
        // Close user dropdown if clicking outside
        if (userMenuContainer && !userMenuContainer.contains(event.target)) {
            if (userMenu) {
                userMenu.classList.add('hidden');
            }
        }
        
        // Close mobile menu if clicking outside
        if (mobileMenuButton && mobileMenu && 
            !mobileMenuButton.contains(event.target) && 
            !mobileMenu.contains(event.target)) {
            mobileMenu.classList.add('hidden');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');
            if (menuIcon) menuIcon.classList.remove('hidden');
            if (closeIcon) closeIcon.classList.add('hidden');
        }
    });
    
    console.log('Components initialized');
}

// Utility functions
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} fade-in`;
    alertDiv.textContent = message;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem;
        background: ${type === 'error' ? '#dc2626' : type === 'success' ? '#16a34a' : '#2563eb'};
        color: white;
        border-radius: 0.5rem;
        z-index: 1000;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        max-width: 400px;
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            alertDiv.remove();
        }, 300);
    }, 5000);
}

// Geolocation helper
function getCurrentLocation() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('Geolocation is not supported by this browser'));
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                resolve({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    timestamp: new Date().toISOString()
                });
            },
            (error) => {
                reject(error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            }
        );
    });
}

// AJAX helper
function makeRequest(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    };
    
    const finalOptions = { ...defaultOptions, ...options };
    
    return fetch(url, finalOptions)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        });
}

// Form validation helper
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('border-red-500');
            isValid = false;
        } else {
            input.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}

// Loading state helper
function setLoadingState(element, isLoading) {
    if (isLoading) {
        element.disabled = true;
        element.classList.add('opacity-50', 'cursor-not-allowed');
        if (element.tagName === 'BUTTON') {
            const originalText = element.textContent;
            element.setAttribute('data-original-text', originalText);
            element.textContent = 'Loading...';
        }
    } else {
        element.disabled = false;
        element.classList.remove('opacity-50', 'cursor-not-allowed');
        if (element.tagName === 'BUTTON' && element.hasAttribute('data-original-text')) {
            element.textContent = element.getAttribute('data-original-text');
            element.removeAttribute('data-original-text');
        }
    }
}

// Export functions for global use
window.TrackMe = {
    showAlert,
    getCurrentLocation,
    makeRequest,
    validateForm,
    setLoadingState,
    initializeComponents
};

// Simple Alpine.js alternative for basic interactivity
window.Alpine = {
    data: function(callback) {
        return callback();
    }
};

// Add some basic CSS animations
const style = document.createElement('style');
style.textContent = `
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .slide-in {
        animation: slideIn 0.3s ease-in-out;
    }
    
    @keyframes slideIn {
        from { transform: translateX(-100%); }
        to { transform: translateX(0); }
    }
    
    .dropdown-menu {
        transition: all 0.2s ease-in-out;
    }
    
    .border-red-500 {
        border-color: #dc2626 !important;
    }
`;
document.head.appendChild(style);
