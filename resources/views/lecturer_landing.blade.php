@extends('layout')

@section('content')
<!-- Header -->
<header class="w-full bg-white border-b border-green-200 shadow-sm flex items-center justify-between px-4 py-2 fixed top-0 left-0 z-30" style="height:56px;">
    <div class="flex items-center gap-3">
        <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK Logo" class="h-10 w-10 object-contain rounded-full">
        <div class="flex flex-col leading-tight">
            <span class="text-base font-bold text-green-700">Department of Computer Science</span>
            <span class="text-xs text-gray-500">Nasarawa State University, Keffi</span>
                        </div>
                    </div>
    <a href="mailto:cs@nsuk.edu.ng" class="text-green-700 text-sm font-semibold hover:underline">Contact</a>
</header>

<!-- Main Content -->
<div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-green-50 to-green-100 pt-8 pb-8 px-2 relative overflow-hidden">
    <!-- Subtle SVG Illustration Background -->
    <svg class="absolute top-0 left-1/2 -translate-x-1/2 opacity-20 w-[600px] h-[300px] pointer-events-none select-none" viewBox="0 0 600 300" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="300" cy="100" rx="280" ry="80" fill="#34d399" fill-opacity="0.15"/>
        <ellipse cx="400" cy="200" rx="180" ry="60" fill="#10b981" fill-opacity="0.10"/>
                                    </svg>
    <div class="w-full max-w-lg mx-auto z-10">
        <div class="bg-white rounded-2xl shadow-xl border border-green-100 p-10 sm:p-14">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-green-700 mb-1">Lecturer Login</h1>
                <p class="text-gray-600 text-base">Sign in to access your dashboard</p>
                                </div>
            <form id="lecturerLoginForm" class="space-y-6">
                @csrf
                            <div>
                    <label for="staff_id" class="block text-base font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                        Staff ID
                    </label>
                    <div class="relative">
                        <input type="text" id="staff_id" name="staff_id" class="w-full px-5 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 text-lg" placeholder="Enter your staff ID" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-base font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    Password
                                </label>
                                <div class="relative">
                        <input type="password" id="password" name="password" class="w-full px-5 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 text-lg" placeholder="Enter your password" required>
                        <button type="button" id="passwordToggle" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 focus:outline-none bg-transparent border-none">
                            <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                <button type="submit" class="w-full py-3 px-6 bg-green-600 text-white text-xl font-bold rounded-lg hover:bg-green-700 transition-all duration-200 shadow flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                    Sign In
                            </button>
                        </form>
        </div>
    </div>
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Lecturer login script loaded');
    
    // Password toggle functionality
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeSlashIcon = document.getElementById('eyeSlashIcon'); // This element is no longer present in the new HTML
    
    passwordToggle.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle icons
        if (type === 'text') {
            eyeIcon.classList.add('hidden');
            // eyeSlashIcon.classList.remove('hidden'); // This element is no longer present
        } else {
            eyeIcon.classList.remove('hidden');
            // eyeSlashIcon.classList.add('hidden'); // This element is no longer present
        }
    });
    
    const form = document.getElementById('lecturerLoginForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Form submitted');
        
        const formData = new FormData(form);
        const data = {
            staff_id: formData.get('staff_id'),
            password: formData.get('password')
        };
        
        console.log('Sending request with:', data);
        
        // Show loading state
        const button = form.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        button.innerHTML = `
            <span class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Signing In...
            </span>
        `;
        button.disabled = true;
        
        // Make API call
        axios.post('/api/validate-lecturer', data)
            .then(function (response) {
                console.log('Response:', response.data);
                
                if (response.data.success) {
                    console.log('Login successful, preparing redirect...');
                    
                    // Show success message
                    showToast('Login successful! Redirecting...', 'success');
                    
                    // Store lecturer data in session/localStorage if needed
                    if (response.data.data && response.data.data.lecturer) {
                        localStorage.setItem('lecturer', JSON.stringify(response.data.data.lecturer));
                        console.log('Lecturer data stored:', response.data.data.lecturer);
                    }
                    
                    // Force redirect to dashboard
                    console.log('Attempting redirect to /lecturer/dashboard...');
                    
                    // Immediate redirect attempt
                    setTimeout(() => {
                        console.log('Executing redirect...');
                        window.location.href = '/lecturer/dashboard';
                    }, 500);
                    
                } else {
                    console.error('Login failed:', response.data.message);
                    showToast(response.data.message || 'Login failed', 'error');
                }
            })
            .catch(function (error) {
                console.error('Error:', error);
                console.error('Error response:', error.response);
                showToast('Login failed. Please try again.', 'error');
            })
            .finally(function () {
                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
            });
    });
    
    // Helper function to show toast messages
    function showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white font-medium ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-blue-500'
        }`;
        toast.textContent = message;
        
        // Add to page
        document.body.appendChild(toast);
        
        // Remove after 3 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 3000);
    }
});

// Test redirect function
function testRedirect() {
    console.log('Testing redirect...');
    alert('Testing redirect to /lecturer/dashboard');
    window.location.href = '/lecturer/dashboard';
}
</script>