/**
 * Optimized Attendance Capture JavaScript
 * Performance-focused with minimal dependencies
 */

class OptimizedAttendanceCapture {
    constructor() {
        this.isProcessing = false;
        this.camera = null;
        this.stream = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.preloadResources();
        this.optimizePerformance();
    }

    bindEvents() {
        // Form submission with debouncing
        const form = document.getElementById('fetch-form');
        if (form) {
            form.addEventListener('submit', this.debounce(this.handleFormSubmit.bind(this), 300));
        }

        // Input validation with throttling
        const inputs = document.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('input', this.throttle(this.validateInput.bind(this), 500));
        });

        // Camera capture
        const captureBtn = document.getElementById('capture-btn');
        if (captureBtn) {
            captureBtn.addEventListener('click', this.handleCapture.bind(this));
        }
    }

    preloadResources() {
        // Preload critical images and fonts
        const preloadLinks = [
            { href: '/css/optimized-landing.css', as: 'style' },
            { href: '/js/optimized-attendance.js', as: 'script' }
        ];

        preloadLinks.forEach(link => {
            const linkEl = document.createElement('link');
            linkEl.rel = 'preload';
            linkEl.href = link.href;
            linkEl.as = link.as;
            document.head.appendChild(linkEl);
        });
    }

    optimizePerformance() {
        // Use requestIdleCallback for non-critical tasks
        if ('requestIdleCallback' in window) {
            requestIdleCallback(() => {
                this.initializeCamera();
                this.setupServiceWorker();
            });
        } else {
            setTimeout(() => {
                this.initializeCamera();
                this.setupServiceWorker();
            }, 100);
        }
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        
        if (this.isProcessing) return;
        
        this.isProcessing = true;
        this.showLoading('validate-btn', 'validate-spinner', 'validate-text');

        try {
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            // Optimized validation
            const validationResult = await this.validateStudent(data);
            
            if (validationResult.success) {
                this.showDetailsModal(validationResult.data);
            } else {
                this.showError(validationResult.message);
            }
        } catch (error) {
            this.showError('Network error. Please try again.');
            console.error('Validation error:', error);
        } finally {
            this.isProcessing = false;
            this.hideLoading('validate-btn', 'validate-spinner', 'validate-text');
        }
    }

    async validateStudent(data) {
        const response = await fetch('/api/student/validate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        return await response.json();
    }

    showDetailsModal(data) {
        const modal = document.getElementById('details-modal');
        const studentDetails = document.getElementById('student-details');
        
        if (modal && studentDetails) {
            studentDetails.innerHTML = `
                <div class="space-y-2">
                    <div><strong>Name:</strong> ${data.student.full_name}</div>
                    <div><strong>Matric:</strong> ${data.student.matric_number}</div>
                    <div><strong>Class:</strong> ${data.classroom.class_name}</div>
                    <div><strong>Course:</strong> ${data.classroom.course_code}</div>
                </div>
            `;
            
            modal.classList.remove('hidden');
            modal.classList.add('fade-in');
            
            // Store data for submission
            this.studentData = data;
        }
    }

    async handleCapture() {
        if (this.isProcessing) return;
        
        this.isProcessing = true;
        this.showLoading('capture-btn', 'capture-spinner', 'capture-text');

        try {
            const imageData = await this.captureImage();
            const result = await this.submitAttendance(imageData);
            
            if (result.success) {
                this.showSuccess('Attendance captured successfully!');
                this.closeModal();
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            this.showError('Failed to capture attendance. Please try again.');
            console.error('Capture error:', error);
        } finally {
            this.isProcessing = false;
            this.hideLoading('capture-btn', 'capture-spinner', 'capture-text');
        }
    }

    async captureImage() {
        if (!this.camera) {
            await this.initializeCamera();
        }

        return new Promise((resolve, reject) => {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            
            canvas.width = this.camera.videoWidth;
            canvas.height = this.camera.videoHeight;
            
            context.drawImage(this.camera, 0, 0);
            
            canvas.toBlob(blob => {
                if (blob) {
                    const reader = new FileReader();
                    reader.onload = () => resolve(reader.result);
                    reader.onerror = reject;
                    reader.readAsDataURL(blob);
                } else {
                    reject(new Error('Failed to capture image'));
                }
            }, 'image/jpeg', 0.8);
        });
    }

    async submitAttendance(imageData) {
        const data = {
            matric_number: this.studentData.student.matric_number,
            attendance_code: this.studentData.session.code,
            image: imageData
        };

        const response = await fetch('/api/student/capture-attendance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        return await response.json();
    }

    async initializeCamera() {
        try {
            const video = document.getElementById('camera-video');
            if (!video) return;

            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { 
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'user'
                }
            });

            video.srcObject = this.stream;
            this.camera = video;
            
            return new Promise(resolve => {
                video.onloadedmetadata = () => {
                    video.play();
                    resolve();
                };
            });
        } catch (error) {
            console.error('Camera initialization failed:', error);
            this.showError('Camera access denied. Please allow camera access.');
        }
    }

    setupServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('Service Worker registered:', registration);
                })
                .catch(error => {
                    console.log('Service Worker registration failed:', error);
                });
        }
    }

    // Utility functions
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    validateInput(e) {
        const input = e.target;
        const value = input.value.trim();
        
        // Basic validation
        if (input.hasAttribute('required') && !value) {
            input.classList.add('border-red-500');
            return false;
        }
        
        input.classList.remove('border-red-500');
        return true;
    }

    showLoading(buttonId, spinnerId, textId) {
        const button = document.getElementById(buttonId);
        const spinner = document.getElementById(spinnerId);
        const text = document.getElementById(textId);
        
        if (button) button.disabled = true;
        if (spinner) spinner.classList.remove('hidden');
        if (text) text.textContent = 'Processing...';
    }

    hideLoading(buttonId, spinnerId, textId) {
        const button = document.getElementById(buttonId);
        const spinner = document.getElementById(spinnerId);
        const text = document.getElementById(textId);
        
        if (button) button.disabled = false;
        if (spinner) spinner.classList.add('hidden');
        if (text) text.textContent = 'Submit';
    }

    showError(message) {
        const errorEl = document.getElementById('error-message');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
            setTimeout(() => errorEl.classList.add('hidden'), 5000);
        }
    }

    showSuccess(message) {
        // Create success notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    closeModal() {
        const modal = document.getElementById('details-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('fade-in');
        }
        
        // Stop camera
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new OptimizedAttendanceCapture();
});

// Performance monitoring
if ('performance' in window) {
    window.addEventListener('load', () => {
        setTimeout(() => {
            const perfData = performance.getEntriesByType('navigation')[0];
            console.log('Page load time:', perfData.loadEventEnd - perfData.loadEventStart, 'ms');
        }, 0);
    });
}
