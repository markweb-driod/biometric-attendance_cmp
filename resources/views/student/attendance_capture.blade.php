@extends('layout')
@section('content')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in { animation: fadeIn 0.5s ease-out; }
    
    @keyframes pulse-ring {
        0% { transform: scale(0.95); opacity: 1; }
        50% { transform: scale(1); opacity: 0.7; }
        100% { transform: scale(0.95); opacity: 1; }
    }
    .pulse-ring { animation: pulse-ring 2s ease-in-out infinite; }
</style>

<!-- Main Container -->
<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50">
    
    <!-- Staff Login Button -->
    <div class="fixed top-4 right-4 z-50">
        <a href="{{ route('login') }}" class="flex items-center space-x-2 bg-white/90 backdrop-blur-md px-4 py-2.5 rounded-xl shadow-lg border border-green-100 text-green-700 font-bold hover:bg-green-50 hover:scale-105 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
            <span>Staff Login</span>
        </a>
    </div>

    <!-- Step 1: Student Details Form -->
    <div id="step-1" class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md fade-in">
            <!-- PWA Install Button -->
            <div id="install-container" class="hidden mb-6 text-center">
                <button id="install-btn" class="bg-white text-green-600 px-4 py-2 rounded-full shadow-md text-sm font-semibold hover:bg-green-50 transition-colors flex items-center justify-center mx-auto space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Install App</span>
                </button>
            </div>

            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl border-4 border-green-50 animate-fade-in-down">
                    <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK Logo" class="w-16 h-16 object-contain">
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Mark Attendance</h1>
                <p class="text-gray-600 text-sm md:text-base">Enter your details to continue</p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-3xl shadow-2xl p-6 md:p-8">
                <!-- Error Message -->
                <div id="error-message" class="hidden mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-red-700 font-medium" id="error-text"></span>
                    </div>
                </div>

                <!-- Demo Info (Helper) -->


                <form id="fetch-form" class="space-y-6">
                    <!-- Matric Number -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Matric Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input type="text" id="matric_number" class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-lg" placeholder="Enter your matric number" required autofocus>
                        </div>
                    </div>

                    <!-- Attendance Code -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Attendance Code</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0117 8z"></path>
                                </svg>
                            </div>
                            <input type="text" id="attendance_code" class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all text-lg uppercase" placeholder="Enter code" required>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="validate-btn" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 flex items-center justify-center space-x-2">
                        <span id="validate-spinner" class="hidden">
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                        </span>
                        <span id="validate-text">Continue to Photo Capture</span>
                    </button>
                </form>

                <!-- Help Link -->
                <div class="mt-6 text-center">
                    <a href="#" class="text-sm text-green-600 hover:text-green-800 font-medium">Need help? Contact support</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Photo Capture -->
    <div id="step-2" class="hidden min-h-screen flex flex-col">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200 p-4">
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <button id="back-btn" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Capture Your Photo</h2>
                        <p class="text-sm text-gray-500" id="student-name-display">Verify your identity</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">Step 2 of 2</div>
                </div>
            </div>
        </div>

        <!-- Camera Section -->
        <div class="flex-1 flex items-center justify-center p-4 bg-gray-900">
            <div class="w-full max-w-2xl">
                <!-- Camera Container -->
                <div class="relative bg-black rounded-2xl overflow-hidden shadow-2xl">
                    <!-- Live Camera Feed -->
                    <div id="webcam-container" class="relative">
                        <video id="webcam" autoplay playsinline class="w-full aspect-[4/3] object-cover"></video>
                        
                        <!-- Face Guide Overlay -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-64 h-80 border-4 border-white/30 rounded-full pulse-ring"></div>
                        </div>

                        <!-- Quality Indicators -->
                        <div class="absolute top-4 right-4 space-y-2">
                            <div id="lighting-indicator" class="flex items-center space-x-2 bg-black/70 backdrop-blur-sm text-white px-3 py-2 rounded-lg">
                                <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                                <span class="text-sm font-medium">Lighting</span>
                            </div>
                            <div id="quality-indicator" class="flex items-center space-x-2 bg-black/70 backdrop-blur-sm text-white px-3 py-2 rounded-lg">
                                <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                <span class="text-sm font-medium">Quality</span>
                            </div>
                        </div>

                        <!-- Camera Instructions -->
                        <div class="absolute bottom-4 left-4 right-4">
                            <div class="bg-black/70 backdrop-blur-sm text-white px-4 py-3 rounded-xl">
                                <p class="text-sm font-medium text-center">Position your face in the oval guide</p>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Preview -->
                    <div id="photo-preview" class="hidden relative">
                        <img id="preview-image" class="w-full aspect-[4/3] object-cover">
                        <div class="absolute top-4 right-4">
                            <div class="bg-green-500 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="font-semibold">Photo Captured!</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex space-x-4">
                    <button id="capture-photo" class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 flex items-center justify-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Capture Photo</span>
                    </button>
                    
                    <button id="retake-photo" class="hidden flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 flex items-center justify-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Retake Photo</span>
                    </button>
                </div>

                <!-- Submit Button -->
                <button id="proceed-capture" disabled class="mt-4 w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    Submit Attendance
                </button>

                <!-- Tips -->
                <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-xl p-4">
                    <div class="flex items-start space-x-3 text-white">
                        <svg class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm">
                            <p class="font-semibold mb-2">📸 Photo Tips:</p>
                            <ul class="space-y-1 text-gray-300">
                                <li>• Look directly at the camera with a neutral expression</li>
                                <li>• Ensure good lighting on your face</li>
                                <li>• Remove glasses if possible</li>
                                <li>• Keep your face centered in the oval guide</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Step 3: Success Modal -->
    <div id="step-3" class="hidden min-h-screen flex items-center justify-center p-4 bg-green-600">
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-sm w-full text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Attendance Marked!</h2>
            <p class="text-gray-600 mb-6" id="success-course-name">Course Name</p>
            
            <div class="bg-green-50 rounded-xl p-4 mb-6">
               <p class="text-sm font-semibold text-green-800">You may leave the class now.</p>
               <p class="text-xs text-green-600 mt-2">📧 A confirmation email has been sent to you.</p>
               <p class="text-xs text-green-600 mt-1" id="success-time">Time: 12:00 PM</p>
            </div>

            <button onclick="window.location.href='/student'" class="w-full bg-gray-900 text-white font-bold py-3 px-6 rounded-xl hover:bg-gray-800 transition-colors">
                Back to Home
            </button>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div id="success-toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl transform translate-x-full transition-transform duration-300 z-50 hidden">
    <div class="flex items-center space-x-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="font-semibold">Success!</span>
    </div>
</div>

<div id="error-toast" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl transform translate-x-full transition-transform duration-300 z-50 hidden">
    <div class="flex items-center space-x-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        <span class="font-semibold" id="error-toast-text">Error occurred</span>
    </div>
    <!-- Recalibrate Prompt -->
    <button id="recalibrate-btn" onclick="requestLocation()" class="hidden mt-2 text-xs bg-white text-red-500 px-2 py-1 rounded font-bold hover:bg-gray-100">
       📍 Refresh Location
    </button>
</div>

<!-- Sync Queue Indicator -->
<div id="sync-queue" class="fixed bottom-4 right-4 bg-blue-600 text-white px-6 py-4 rounded-xl shadow-2xl transform translate-y-full transition-transform duration-300 z-50 hidden">
    <div class="flex items-center justify-between space-x-4">
        <div class="flex items-center space-x-3">
            <div class="relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span id="queue-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-blue-600">0</span>
            </div>
            <div>
                <p class="font-bold text-sm">Pending Uploads</p>
                <p class="text-xs text-blue-200">Saved on device</p>
            </div>
        </div>
        <button id="sync-btn" class="bg-white text-blue-600 px-3 py-1.5 rounded-lg text-sm font-bold hover:bg-blue-50 transition-colors">
            Sync Now
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');
    const fetchForm = document.getElementById('fetch-form');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const matricInput = document.getElementById('matric_number');
    const codeInput = document.getElementById('attendance_code');
    const validateBtn = document.getElementById('validate-btn');
    const validateSpinner = document.getElementById('validate-spinner');
    const validateText = document.getElementById('validate-text');
    const backBtn = document.getElementById('back-btn');
    const webcam = document.getElementById('webcam');
    const webcamContainer = document.getElementById('webcam-container');
    const photoPreview = document.getElementById('photo-preview');
    const previewImage = document.getElementById('preview-image');
    const capturePhotoBtn = document.getElementById('capture-photo');
    const retakePhotoBtn = document.getElementById('retake-photo');
    const proceedCaptureBtn = document.getElementById('proceed-capture');
    const lightingIndicator = document.getElementById('lighting-indicator');
    const qualityIndicator = document.getElementById('quality-indicator');
    const studentNameDisplay = document.getElementById('student-name-display');
    const successToast = document.getElementById('success-toast');
    const errorToast = document.getElementById('error-toast');
    const errorToastText = document.getElementById('error-toast-text');

    let capturedImage = null;
    let webcamStream = null;
    let isCaptured = false;
    let currentLocation = null;
    let studentData = null;

    // Check for query params (e.g. ?code=ABCD)
    const pageQueryParams = new URLSearchParams(window.location.search);
    const codeParam = pageQueryParams.get('code');
    if (codeParam) {
        codeInput.value = codeParam;
    }

    // Toast functions
    function showToast(message, type = 'success') {
        const toast = type === 'success' ? successToast : errorToast;
        if (type === 'error') errorToastText.textContent = message;
        
        toast.classList.remove('hidden', 'translate-x-full');
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }, 3000);
    }

    function showError(message) {
        errorText.textContent = message;
        errorMessage.classList.remove('hidden');
        setTimeout(() => errorMessage.classList.add('hidden'), 5000);
    }

    // Geolocation
    function requestLocation() {
        if (!navigator.geolocation) {
            showError('Geolocation is not supported by your browser');
            return;
        }

        const geoOptions = { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 };
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                currentLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
            },
            (error) => {
                console.warn('High accuracy geolocation failed, retrying with low accuracy...', error);
                
                // Fast-fail if we know it's a secure context issue
                if (error.code === 1 && window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
                     showError('Geolocation requires HTTPS. Please access via a secure connection.');
                     return;
                }

                // Retry with low accuracy
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        currentLocation = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        };
                    },
                    (innerError) => {
                         console.error('Geolocation error:', innerError);
                        let msg = 'Location access denied.';
                        
                        if (innerError.code === 1) { // PERMISSION_DENIED
                             if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
                                 msg = '<b>Location Error:</b> Browser requires <b>HTTPS</b> for geolocation. Please use https://';
                             } else {
                                 msg = 'Location permission denied. Please allow camera and location access.';
                             }
                        } else if (innerError.code === 3) {
                             msg = 'Location request timed out. Please check your GPS settings.';
                        }
                        
                        showError(msg);
                    },
                    { enableHighAccuracy: false, timeout: 20000, maximumAge: 0 }
                );
            },
            geoOptions
        );
    }

    requestLocation();

    // Form submission
    fetchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!currentLocation) {
            requestLocation();
            showError('Waiting for location... Please ensure location services are enabled.');
            return;
        }

        const matric = matricInput.value.trim();
        const code = codeInput.value.trim();

        validateBtn.disabled = true;
        validateSpinner.classList.remove('hidden');
        validateText.classList.add('hidden');

        fetch('{{ url('api/student/fetch-details') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ matric_number: matric, attendance_code: code })
        })
        .then(res => res.json())
        .then(data => {
            validateBtn.disabled = false;
            validateSpinner.classList.add('hidden');
            validateText.classList.remove('hidden');
            
            if (data.success) {
                studentData = data;
                studentNameDisplay.innerHTML = `${data.student.name}<br><span class="text-xs font-normal text-gray-500">${data.classroom.code} • ${data.session.session_time}</span>`;
                step1.classList.add('hidden');
                step2.classList.remove('hidden');
                startCamera();
            } else {
                if (data.requires_face_registration) {
                    showToast('Face registration required. Redirecting...', 'error');
                    setTimeout(() => {
                        window.location.href = data.redirect_to || '/student/register-face';
                    }, 2000);
                } else {
                    showError(data.message || 'Invalid details');
                }
            }
        })
        .catch(err => {
            console.error('Error:', err);
            validateBtn.disabled = false;
            validateSpinner.classList.add('hidden');
            validateText.classList.remove('hidden');
            showError('Network error. Please try again.');
        });
    });

    // Camera functions
    function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showToast('Camera not supported on this browser. Please use Chrome, Firefox, or Safari.', 'error');
            return;
        }

        if (!webcam) {
            showToast('Camera initialization failed. Please refresh the page.', 'error');
            return;
        }

        navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 1280 },
                height: { ideal: 960 },
                facingMode: 'user'
            } 
        })
        .then(stream => {
            webcam.srcObject = stream;
            webcamStream = stream;
            
            webcam.onloadedmetadata = () => {
                webcam.play().catch(err => console.error('Video play error:', err));
                simulateFaceDetection();
            };
        })
        .catch(err => {
            console.error('Camera error:', err);
            let errorMsg = 'Unable to access camera. ';
            
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                errorMsg += 'Please allow camera access in your browser settings.';
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                errorMsg += 'No camera found on your device.';
            } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                errorMsg += 'Camera is already in use by another application.';
            } else {
                errorMsg += 'Please check your camera permissions and try again.';
            }
            
            showToast(errorMsg, 'error');
        });
    }

    function simulateFaceDetection() {
        setInterval(() => {
            if (!isCaptured && webcamStream) {
                const lighting = Math.random() > 0.3 ? 'good' : 'poor';
                const quality = Math.random() > 0.2 ? 'good' : 'poor';
                
                const lightingDot = lightingIndicator.querySelector('.w-2');
                const qualityDot = qualityIndicator.querySelector('.w-2');
                
                if (lightingDot) {
                    lightingDot.className = `w-2 h-2 rounded-full ${lighting === 'good' ? 'bg-green-400' : 'bg-yellow-400'}`;
                }
                if (qualityDot) {
                    qualityDot.className = `w-2 h-2 rounded-full ${quality === 'good' ? 'bg-green-400' : 'bg-yellow-400'}`;
                }
            }
        }, 1000);
    }

    function capturePhoto() {
        if (!webcamStream) return;
        
        const canvas = document.createElement('canvas');
        canvas.width = webcam.videoWidth;
        canvas.height = webcam.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(webcam, 0, 0);
        
        // Capture photo without brightness validation
        capturedImage = canvas.toDataURL('image/jpeg', 0.92);
        previewImage.src = capturedImage;
        
        webcamContainer.classList.add('hidden');
        photoPreview.classList.remove('hidden');
        capturePhotoBtn.classList.add('hidden');
        retakePhotoBtn.classList.remove('hidden');
        
        proceedCaptureBtn.disabled = false;
        isCaptured = true;
        
        showToast('✅ Photo captured successfully!', 'success');
    }

    function calculateBrightness(imageData) {
        let total = 0;
        const data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            total += (data[i] + data[i + 1] + data[i + 2]) / 3;
        }
        return total / (imageData.width * imageData.height);
    }

    function retakePhoto() {
        capturedImage = null;
        previewImage.src = '';
        
        webcamContainer.classList.remove('hidden');
        photoPreview.classList.add('hidden');
        capturePhotoBtn.classList.remove('hidden');
        retakePhotoBtn.classList.add('hidden');
        
        proceedCaptureBtn.disabled = true;
        isCaptured = false;
    }

    // Event listeners
    capturePhotoBtn.addEventListener('click', capturePhoto);
    retakePhotoBtn.addEventListener('click', retakePhoto);

    backBtn.addEventListener('click', () => {
        step2.classList.add('hidden');
        step1.classList.remove('hidden');
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
        retakePhoto();
    });

    proceedCaptureBtn.addEventListener('click', function() {
        if (!capturedImage) {
            showToast('Please capture a photo first', 'error');
            return;
        }
        
        if (!currentLocation) {
            showToast('Location required. Please enable location services.', 'error');
            requestLocation();
            return;
        }

        const matric = matricInput.value.trim();
        const code = codeInput.value.trim();
        
        proceedCaptureBtn.disabled = true;
        proceedCaptureBtn.textContent = navigator.onLine ? 'Submitting...' : 'Saving...';
        
        const payload = {
            matric_number: matric,
            attendance_code: code,
            image: capturedImage,
            latitude: currentLocation.latitude,
            longitude: currentLocation.longitude
        };

        if (!navigator.onLine) {
            saveToQueue(payload);
            return;
        }

        fetch('{{ url('api/student/capture-attendance') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Success: Show Modal instead of Toast
                const step3 = document.getElementById('step-3');
                const successCourseName = document.getElementById('success-course-name');
                const successTime = document.getElementById('success-time');

                successCourseName.textContent = data.classroom ? `${data.classroom.code} - ${data.classroom.course_title}` : 'Attendance Captured';
                successTime.textContent = 'Time: ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                step2.classList.add('hidden');
                step3.classList.remove('hidden');
            } else {
                proceedCaptureBtn.disabled = false;
                proceedCaptureBtn.textContent = 'Submit Attendance';
                showToast(data.message || 'Failed to mark attendance', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            // If network error, offer to save
            if (!navigator.onLine) {
                saveToQueue(payload);
            } else {
                proceedCaptureBtn.disabled = false;
                proceedCaptureBtn.textContent = 'Submit Attendance';
                showToast('Network error. Please try again.', 'error');
            }
        });
    });

    // Auto-fill attendance code from URL
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code');
    if (code && codeInput) {
        codeInput.value = code;
        codeInput.readOnly = true;
        codeInput.classList.add('bg-gray-100', 'cursor-not-allowed');
        matricInput.focus();
    }

    // PWA Install Logic
    let deferredPrompt;
    const installContainer = document.getElementById('install-container');
    const installBtn = document.getElementById('install-btn');

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        installContainer.classList.remove('hidden');
    });

    installBtn.addEventListener('click', async () => {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        if (outcome === 'accepted') {
            installContainer.classList.add('hidden');
        }
        deferredPrompt = null;
    });

    window.addEventListener('appinstalled', () => {
        installContainer.classList.add('hidden');
        deferredPrompt = null;
    });

    // Offline Support Logic
    const syncQueue = document.getElementById('sync-queue');
    const queueCount = document.getElementById('queue-count');
    const syncBtn = document.getElementById('sync-btn');
    
    // IndexedDB Setup
    let db;
    const DB_NAME = 'AttendanceDB';
    const STORE_NAME = 'pending_attendance';
    
    const request = indexedDB.open(DB_NAME, 1);
    
    request.onerror = (event) => console.error("IndexedDB error:", event.target.error);
    
    request.onupgradeneeded = (event) => {
        db = event.target.result;
        if (!db.objectStoreNames.contains(STORE_NAME)) {
            db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
        }
    };
    
    request.onsuccess = (event) => {
        db = event.target.result;
        updateQueueUI();
    };

    function saveToQueue(data) {
        const transaction = db.transaction([STORE_NAME], 'readwrite');
        const store = transaction.objectStore(STORE_NAME);
        const record = {
            ...data,
            captured_at: new Date().toISOString(), // Save capture time
            timestamp: Date.now()
        };
        store.add(record);
        updateQueueUI();
        showToast('Saved to Outbox (Offline)', 'success');
        
        // Reset UI
        step2.classList.add('hidden');
        step1.classList.remove('hidden');
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
        retakePhoto();
        proceedCaptureBtn.disabled = false;
        proceedCaptureBtn.textContent = 'Submit Attendance';
    }

    function updateQueueUI() {
        if (!db) return;
        const transaction = db.transaction([STORE_NAME], 'readonly');
        const store = transaction.objectStore(STORE_NAME);
        const countRequest = store.count();
        
        countRequest.onsuccess = () => {
            const count = countRequest.result;
            queueCount.textContent = count;
            if (count > 0) {
                syncQueue.classList.remove('hidden', 'translate-y-full');
            } else {
                syncQueue.classList.add('translate-y-full');
            }
        };
    }

    async function syncPendingItems() {
        if (!navigator.onLine) {
            showToast('Still offline. Cannot sync.', 'error');
            return;
        }

        const transaction = db.transaction([STORE_NAME], 'readonly');
        const store = transaction.objectStore(STORE_NAME);
        const getAllRequest = store.getAll();
        const getAllKeysRequest = store.getAllKeys();

        getAllRequest.onsuccess = async () => {
            const items = getAllRequest.result;
            const keys = getAllKeysRequest.result;
            
            if (items.length === 0) return;

            syncBtn.disabled = true;
            syncBtn.textContent = 'Syncing...';

            let successCount = 0;

            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                const key = keys[i];

                try {
                    const response = await fetch('{{ url('api/student/capture-attendance') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(item)
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Delete from DB on success
                        const deleteTx = db.transaction([STORE_NAME], 'readwrite');
                        deleteTx.objectStore(STORE_NAME).delete(key);
                        successCount++;
                    }
                } catch (err) {
                    console.error('Sync error for item', key, err);
                }
            }

            syncBtn.disabled = false;
            syncBtn.textContent = 'Sync Now';
            updateQueueUI();
            
            if (successCount > 0) {
                showToast(`Synced ${successCount} records successfully!`, 'success');
            }
        };
    }

    syncBtn.addEventListener('click', syncPendingItems);

    function updateOnlineStatus() {
        const isOnline = navigator.onLine;
        const submitBtn = document.getElementById('proceed-capture');
        const validateBtn = document.getElementById('validate-btn');
        
        if (!isOnline) {
            showToast('You are offline. Attendance will be saved to device.', 'warning');
            if (submitBtn) {
                submitBtn.textContent = 'Save for Later (Offline)';
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                submitBtn.disabled = false; // Enable button for offline save
            }
        } else {
            showToast('You are back online!', 'success');
            if (submitBtn) {
                submitBtn.textContent = 'Submit Attendance';
                submitBtn.disabled = false;
            }
            // Auto-sync when coming online
            if (db) updateQueueUI();
        }
    }

    window.addEventListener('online', () => {
        updateOnlineStatus();
        syncPendingItems();
    });
    window.addEventListener('offline', updateOnlineStatus);

    // Check initial status
    if (!navigator.onLine) {
        updateOnlineStatus();
    }
});
</script>
@endsection