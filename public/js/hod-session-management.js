/**
 * HOD Session Management
 * Handles session timeout warnings and auto-logout functionality
 */

class HODSessionManager {
    constructor(options = {}) {
        this.warningTime = options.warningTime || 5; // Minutes before expiry to show warning
        this.sessionTimeout = options.sessionTimeout || 60; // Total session timeout in minutes
        this.checkInterval = options.checkInterval || 60000; // Check every minute
        this.lastActivity = Date.now();
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.startActivityTracking();
        this.startSessionCheck();
    }
    
    bindEvents() {
        // Track user activity
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        events.forEach(event => {
            document.addEventListener(event, () => {
                this.updateActivity();
            }, true);
        });
        
        // Handle AJAX session expiry responses
        if (typeof $ !== 'undefined') {
            $(document).ajaxError((event, xhr, settings) => {
                if (xhr.status === 401 && xhr.responseJSON && xhr.responseJSON.error === 'Session expired') {
                    this.handleSessionExpiry(xhr.responseJSON);
                }
            });
        }
    }
    
    updateActivity() {
        this.lastActivity = Date.now();
    }
    
    startActivityTracking() {
        // Send periodic activity updates to server
        setInterval(() => {
            if (this.isActive()) {
                this.pingServer();
            }
        }, 5 * 60 * 1000); // Every 5 minutes
    }
    
    startSessionCheck() {
        setInterval(() => {
            this.checkSessionStatus();
        }, this.checkInterval);
    }
    
    isActive() {
        const inactiveTime = (Date.now() - this.lastActivity) / 1000 / 60; // Minutes
        return inactiveTime < this.sessionTimeout;
    }
    
    checkSessionStatus() {
        const inactiveTime = (Date.now() - this.lastActivity) / 1000 / 60; // Minutes
        const timeUntilExpiry = this.sessionTimeout - inactiveTime;
        
        if (timeUntilExpiry <= this.warningTime && timeUntilExpiry > 0) {
            this.showWarning(Math.ceil(timeUntilExpiry));
        } else if (timeUntilExpiry <= 0) {
            this.handleSessionExpiry({
                message: 'Your session has expired due to inactivity.',
                redirect: '/hod/login'
            });
        }
    }
    
    showWarning(minutesLeft) {
        // Remove existing warning
        this.hideWarning();
        
        // Create warning modal/notification
        const warning = document.createElement('div');
        warning.id = 'session-warning';
        warning.className = 'fixed top-4 right-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-lg z-50';
        warning.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm">
                        <strong>Session Warning:</strong> Your session will expire in ${minutesLeft} minute${minutesLeft !== 1 ? 's' : ''}. 
                        <button onclick="hodSessionManager.extendSession()" class="underline font-medium">Click here to extend</button>
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="hodSessionManager.hideWarning()" class="text-yellow-400 hover:text-yellow-600">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(warning);
        
        // Auto-hide after 10 seconds
        setTimeout(() => {
            this.hideWarning();
        }, 10000);
    }
    
    hideWarning() {
        const warning = document.getElementById('session-warning');
        if (warning) {
            warning.remove();
        }
    }
    
    extendSession() {
        this.updateActivity();
        this.hideWarning();
        this.pingServer();
    }
    
    pingServer() {
        // Send a simple request to keep session alive
        if (typeof fetch !== 'undefined') {
            fetch('/hod/api/ping', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            }).catch(() => {
                // Ignore errors - session might already be expired
            });
        }
    }
    
    handleSessionExpiry(data) {
        // Show expiry message
        alert(data.message || 'Your session has expired. Please log in again.');
        
        // Redirect to login
        window.location.href = data.redirect || '/hod/login';
    }
}

// Initialize session manager when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Get configuration from meta tags or use defaults
    const sessionTimeout = parseInt(document.querySelector('meta[name="hod-session-timeout"]')?.getAttribute('content')) || 60;
    
    window.hodSessionManager = new HODSessionManager({
        sessionTimeout: sessionTimeout,
        warningTime: 5,
        checkInterval: 60000
    });
});