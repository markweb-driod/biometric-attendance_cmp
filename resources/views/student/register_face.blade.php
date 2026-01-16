@extends('layout')
@section('content')
@section('wrapper_class', 'w-full max-w-lg mx-auto mt-4')
@section('wrapper_style', '')
<style>
/* Custom Scrollbar Styling - Fix Purple Color */
::-webkit-scrollbar {
  width: 12px;
  height: 12px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 6px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 6px;
  border: 2px solid #f1f5f9;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Firefox scrollbar */
* {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f1f5f9;
}

/* Clean, Professional Background */
body {
  background: linear-gradient(to bottom, #f8fafc 0%, #f1f5f9 100%);
  background-attachment: fixed;
  min-height: 100vh;
}

/* Quality indicator styles */
.quality-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  background: white;
  border: 2px solid #e5e7eb;
}

.quality-indicator.poor {
  border-color: #ef4444;
  background: #fef2f2;
  color: #991b1b;
}

.quality-indicator.good {
  border-color: #10b981;
  background: #ecfdf5;
  color: #065f46;
}

.quality-indicator.excellent {
  border-color: #059669;
  background: #d1fae5;
  color: #064e3b;
}

.quality-icon {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

/* Progress steps */
.progress-step {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  font-weight: 600;
  font-size: 0.875rem;
  background: white;
  border: 2px solid #d1d5db;
  color: #6b7280;
}

.progress-step.active {
  background: #008000;
  border-color: #008000;
  color: white;
}

.progress-step.completed {
  background: #008000;
  border-color: #008000;
  color: white;
}

.progress-line {
  width: 40px;
  height: 2px;
  background: #e5e7eb;
}

.progress-line.active {
  background: #008000;
}

/* Face detection oval overlay */
.face-guide {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 200px;
  height: 260px;
  border: 3px solid #d1d5db;
  border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
  pointer-events: none;
  transition: all 0.3s ease;
}

.face-guide.aligned {
  border-color: #10b981;
  box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
}

.face-guide.misaligned {
  border-color: #f59e0b;
  box-shadow: 0 0 20px rgba(245, 158, 11, 0.3);
}

.quality-score-bar {
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.quality-score-fill {
  height: 100%;
  background: linear-gradient(to right, #10b981, #059669);
}

/* Notification Toast Styles */
.notification-toast {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  min-width: 300px;
  max-width: 500px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  opacity: 0;
  transform: translateX(400px);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.notification-toast.show {
  opacity: 1;
  transform: translateX(0);
}

.notification-toast.notification-success {
  border-left: 4px solid #10b981;
}

.notification-toast.notification-error {
  border-left: 4px solid #ef4444;
}

.notification-content {
  display: flex;
  align-items: center;
  padding: 16px 20px;
  gap: 12px;
}

.notification-icon {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-success .notification-icon {
  background-color: #d1fae5;
  color: #10b981;
}

.notification-error .notification-icon {
  background-color: #fee2e2;
  color: #ef4444;
}

.notification-message {
  flex: 1;
  font-size: 14px;
  line-height: 1.5;
  color: #111827;
  font-weight: 500;
}

.notification-close {
  flex-shrink: 0;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 4px;
  color: #6b7280;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: all 0.2s;
}

.notification-close:hover {
  background-color: #f3f4f6;
  color: #111827;
}

@media (max-width: 640px) {
  .notification-toast {
    top: 10px;
    right: 10px;
    left: 10px;
    min-width: auto;
    max-width: none;
  }
  
  .notification-toast.show {
    transform: translateY(0);
  }
  
  .notification-toast {
    transform: translateY(-100px);
  }
}

/* Camera container overlay */
.camera-overlay {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

/* Clean Card/Modal Styles */
.card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
  border: 1px solid #e5e7eb;
  transition: box-shadow 0.2s ease;
}

.card:hover {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
}

/* Responsive Styles */
@media (max-width: 640px) {
  .card {
    padding: 1rem !important;
    border-radius: 10px;
  }
  
  /* Better label styling on mobile */
  label {
    font-size: 0.875rem !important;
    font-weight: 500 !important;
    color: #374151 !important;
    margin-bottom: 0.5rem !important;
    display: block;
    line-height: 1.5;
  }
  
  /* Input field styling */
  input[type="text"],
  input[type="email"],
  input[type="password"] {
    font-size: 16px !important; /* Prevents zoom on iOS */
    padding: 0.75rem !important;
    width: 100%;
    box-sizing: border-box;
  }
}
</style>

<div class="w-full py-4 px-3 sm:px-4 md:px-6">
  <div class="max-w-lg mx-auto">
    
    <!-- Header -->
    <div style="text-align: center; margin-bottom: 32px; padding-top: 80px;">
      <!-- Logo Container -->
      <div style="width: 80px; height: 80px; background: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); border: 3px solid #e8f5e9;">
        <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK Logo" style="width: 55px; height: 55px; object-fit: contain;">
      </div>
      <!-- Title -->
      <h1 style="font-size: 1.75rem; font-weight: 800; color: #111827; margin: 0 0 8px 0; letter-spacing: -0.025em;">Face Registration</h1>
      <p style="color: #6b7280; font-size: 0.9rem; max-width: 280px; margin: 0 auto;">Complete your biometric profile to enable attendance tracking</p>
    </div>

    <!-- Progress Steps -->
    <div class="flex items-center justify-center gap-1.5 sm:gap-2 mb-4">
      <div class="progress-step active" id="step-1">1</div>
      <div class="progress-line" id="line-1"></div>
      <div class="progress-step" id="step-2">2</div>
      <div class="progress-line" id="line-2"></div>
      <div class="progress-step" id="step-3">3</div>
    </div>

    <!-- Error Message -->
    <div id="error-message" class="hidden mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
      <div class="flex items-center gap-3">
        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <span id="error-text" class="text-red-700 font-medium"></span>
        <button id="retry-camera-btn" class="ml-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition hidden">Retry Camera</button>
      </div>
    </div>

    <!-- Step 1: Matric Validation -->
    <div id="step1-card" class="card p-4 sm:p-6 mb-4">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Step 1: Enter Your Matric Number</h2>
      
      <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
          </svg>
          <div>
            <p class="text-sm text-blue-900 font-medium">Make sure you're in good lighting</p>
            <p class="text-xs text-blue-700 mt-1">Remove glasses and ensure your face is clearly visible</p>
          </div>
        </div>
      </div>

      <form id="matric-form" autocomplete="off">
        @csrf
        <label class="block text-sm font-medium text-gray-700 mb-2 sm:mb-1.5" for="matric_number">Matric Number</label>
        <input 
            type="text" 
            name="matric_number" 
            id="matric_number" 
            class="w-full border-2 border-gray-300 rounded-lg px-3 py-2.5 sm:px-4 sm:py-3 text-base focus:outline-none focus:border-green-600 focus:ring-2 focus:ring-green-100 transition-colors" 
            placeholder="e.g., NSUK/2019/1234"
            required 
            autofocus 
          />
  
          <div class="mb-5"></div>
        <button type="submit" id="validate-btn" class="w-full mt-4 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-[1.02]">
          <span id="validate-text">Continue</span>
          <span id="validate-spinner" class="hidden ml-2"><svg class="animate-spin h-5 w-5 text-white inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg></span>
        </button>
        <div class="mb-5"></div>
        <a href="#" id="return-to-capture-link" class="text-green-700 font-semibold underline text-sm mt-6 block text-center">&larr; Return to Capture</a>
      </form>
    
    </div>

    <!-- Step 2: Capture Photo -->
    <div id="step2-card" class="card p-4 sm:p-6 mb-4 hidden">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Step 2: Capture Your Face</h2>
      
      <!-- Student Info -->
      <div id="student-info" class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg"></div>

      <!-- Quality Checklist -->
      <div id="quality-checklist" class="mb-4 space-y-2">
        <div class="quality-indicator" id="lighting-indicator">
          <div class="quality-icon">●</div>
          <span>Checking lighting...</span>
        </div>
        <div class="quality-indicator" id="centering-indicator">
          <div class="quality-icon">●</div>
          <span>Checking face position...</span>
        </div>
        <div class="quality-indicator hidden" id="distance-indicator">
          <div class="quality-icon">●</div>
          <span>Checking distance...</span>
        </div>
      </div>

      <!-- Overall Quality Score -->
      <div class="mb-3">
        <div class="flex items-center justify-between mb-1.5">
          <span class="text-sm font-semibold text-gray-700">Overall Quality</span>
          <span class="text-sm font-bold" id="quality-score-text">0%</span>
        </div>
        <div class="quality-score-bar">
          <div class="quality-score-fill" id="quality-score-fill" style="width: 0%"></div>
        </div>
      </div>

      <!-- Camera Container -->
      <div class="relative bg-gray-100 rounded-lg overflow-hidden mb-3" style="height: 280px;">
        <div id="register-webcam-container" class="relative w-full h-full">
          <video id="register-webcam" autoplay playsinline class="w-full h-full object-cover"></video>
          <div class="camera-overlay">
            <div class="face-guide" id="face-guide"></div>
          </div>
        </div>
        <canvas id="register-snapshot" class="hidden"></canvas>
        <div id="register-photo-preview" class="relative w-full h-full hidden">
          <img id="preview-image" src="" class="w-full h-full object-cover" />
        </div>
      </div>

      <!-- Capture Buttons -->
      <div class="flex gap-3">
        <button type="button" id="register-capture-photo" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
          Capture Photo
        </button>
        <button type="button" id="register-retake-photo" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition hidden">
          Retake Photo
        </button>
      </div>
    </div>

    <!-- Step 3: Confirm Registration -->
    <div id="step3-card" class="card p-4 sm:p-6 hidden">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Step 3: Complete Registration</h2>
      
      <form id="register-face-form" method="POST" action="{{ route('student.register-face') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="matric_number" id="register_matric_number">
        <input type="hidden" name="reference_image" id="reference_image_input">
        
        <p class="text-gray-600 mb-6">Review your photo above and click the button below to complete your registration.</p>

        <button type="submit" id="register-face-btn" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-6 py-4 rounded-lg shadow-sm transition-colors">
          <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Complete Registration
        </button>
      </form>
    </div>

  </div>
</div>

@push('scripts')
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
  // DOM Elements
  const matricForm = document.getElementById('matric-form');
  const step1Card = document.getElementById('step1-card');
  const step2Card = document.getElementById('step2-card');
  const step3Card = document.getElementById('step3-card');
  const registerForm = document.getElementById('register-face-form');
  
  const webcam = document.getElementById('register-webcam');
  const snapshot = document.getElementById('register-snapshot');
  const capturePhotoBtn = document.getElementById('register-capture-photo');
  const retakePhotoBtn = document.getElementById('register-retake-photo'); // Fixed ID
  const webcamContainer = document.getElementById('register-webcam-container');
  const photoPreview = document.getElementById('register-photo-preview');
  const previewImage = document.getElementById('preview-image');
  const registerFaceBtn = document.getElementById('register-face-btn'); // Fixed ID
  const referenceInput = document.getElementById('reference_image_input');
  const studentInfo = document.getElementById('student-info');
  
  const errorMessage = document.getElementById('error-message');
  const errorText = document.getElementById('error-text');
  const retryBtn = document.getElementById('retry-camera-btn');
  
  const faceGuide = document.getElementById('face-guide');
  const qualityScoreFill = document.getElementById('quality-score-fill');
  const qualityScoreText = document.getElementById('quality-score-text');
  
  // Quality indicators
  const lightingIndicator = document.getElementById('lighting-indicator');
  const centeringIndicator = document.getElementById('centering-indicator');
  const distanceIndicator = document.getElementById('distance-indicator'); // Kept for layout compatibility but hidden/unused if needed
  
  // AI Settings
  const aiSettings = @json($currentSettings ?? []);
  const enableAI = aiSettings.enable_browser_face_detection !== false;
  const confidenceThreshold = parseFloat(aiSettings.browser_face_confidence_threshold || 0.5);
  const allowLoose = aiSettings.browser_face_allow_loose_alignment !== false;

  let webcamStream = null;
  let detectionInterval = null;
  let isModelLoaded = false;

  // Initialize AI Models
  async function loadModels() {
    if (!enableAI) {
        console.log('AI detection disabled, falling back to manual checks');
        return;
    }
    
    try {
        console.log('Loading Face API models...');
        // Load only SSD Mobilenet for detection (faster/robust)
        await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
        await faceapi.nets.faceLandmark68Net.loadFromUri('/models'); // Enabled for liveness check
        isModelLoaded = true;
        console.log('Face API models loaded');
    } catch (err) {
        console.error('Failed to load models:', err);
        // Fallback to non-AI or show error?
        // We will proceed but enableAI might fail gracefully
    }
  }

  // Load models on page load
  loadModels();

  // Camera functions
  function startCamera() {
    errorMessage.classList.add('hidden');
    retryBtn.classList.add('hidden');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      errorText.textContent = 'Your browser does not support camera access.';
      errorMessage.classList.remove('hidden');
      return;
    }

    navigator.mediaDevices.getUserMedia({ 
      video: { 
        width: { ideal: 640 },
        height: { ideal: 480 },
        facingMode: 'user' 
      } 
    })
    .then(stream => {
      webcam.srcObject = stream;
      webcamStream = stream;
      
      // Start detection loop
      startDetection();
    })
    .catch(err => {
      console.error('Camera error:', err);
      let errorMsg = 'Unable to access camera. ';
      
      if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
        errorMsg += 'Please allow camera access and refresh the page.';
      } else if (err.name === 'NotFoundError') {
        errorMsg += 'No camera found.';
      } else if (err.name === 'NotReadableError') {
        errorMsg += 'Camera is being used by another application.';
        retryBtn.classList.remove('hidden');
      } else {
        errorMsg += 'Please check your camera permissions.';
      }
      
      errorText.textContent = errorMsg;
      errorMessage.classList.remove('hidden');
    });
  }

  function stopCamera() {
    if (webcamStream) {
      webcamStream.getTracks().forEach(track => track.stop());
      webcamStream = null;
      webcam.srcObject = null;
    }
    if (detectionInterval) {
      clearInterval(detectionInterval);
      detectionInterval = null;
    }
  }

      let blinkDetected = false;
      let captureState = 'waiting_for_blink'; // waiting_for_blink, waiting_for_open, captured

      // Smart Capture Logic variables
      let consecutiveOpenFrames = 0;
      
      // Detection Loop
      function startDetection() {
        if (detectionInterval) clearInterval(detectionInterval);
        
        detectionInterval = setInterval(async () => {
          if (webcam.paused || webcam.ended || !isModelLoaded) {
             if (!enableAI) checkImageQualityManual();
             return;
          }

          if (enableAI) {
              const options = new faceapi.SsdMobilenetv1Options({ minConfidence: confidenceThreshold });
              // Upgrade to use Landmarks
              const detection = await faceapi.detectSingleFace(webcam, options).withFaceLandmarks();
              
              if (detection) {
                  const { x, y, width, height } = detection.detection.box;
                  const landmarks = detection.landmarks;
                  const leftEye = landmarks.getLeftEye();
                  const rightEye = landmarks.getRightEye();
                  
                  // Calculate Eye Aspect Ratio (EAR) approximation (Height / Width)
                  const leftEAR = getEyeOpenness(leftEye);
                  const rightEAR = getEyeOpenness(rightEye);
                  const avgEAR = (leftEAR + rightEAR) / 2;
                  
                  // Thresholds
                  const BLINK_THRESHOLD = 0.2; // Below this is closed
                  const OPEN_THRESHOLD = 0.3;  // Above this is open
                  
                  // Bounding Box Constraints (from previous step)
                  const videoWidth = webcam.videoWidth;
                  const videoHeight = webcam.videoHeight;
                  const centerX = x + width / 2;
                  const centerY = y + height / 2;
                  // Constraints (Stricter Centering)
                  const minFaceSize = Math.min(videoWidth, videoHeight) * 0.20;
                  const maxFaceSize = Math.min(videoWidth, videoHeight) * 0.60;
                  
                  // Strict centering: Center of face within 42%-58% of screen width
                  const isCenteredX = centerX > videoWidth * 0.42 && centerX < videoWidth * 0.58;
                  const isCenteredY = centerY > videoHeight * 0.35 && centerY < videoHeight * 0.65;
                  const isCentered = isCenteredX && isCenteredY;
                  
                  const isFullyInFrame = x > 0 && y > 0 && (x + width) < videoWidth && (y + height) < videoHeight;
                  const isSizeOK = width > minFaceSize && width < maxFaceSize;

                  if (isCentered && isFullyInFrame && isSizeOK) {
                      // State Machine
                      if (captureState === 'waiting_for_blink') {
                          handleFaceDetected(detection.detection, 'Blink to Capture');
                          if (avgEAR < BLINK_THRESHOLD) {
                              captureState = 'waiting_for_open';
                              updateIndicator(lightingIndicator, 'good', 'Blink Detected!');
                          }
                      } else if (captureState === 'waiting_for_open') {
                          handleFaceDetected(detection.detection, 'Open Eyes Wide');
                          if (avgEAR > OPEN_THRESHOLD) {
                              consecutiveOpenFrames++;
                              if (consecutiveOpenFrames > 2) { // Wait for stable open eyes
                                  captureState = 'captured';
                                  capturePhotoBtn.click(); // Auto capture!
                              }
                          } else {
                              consecutiveOpenFrames = 0;
                          }
                      }
                      
                  } else {
                      // Reset state if position bad
                      captureState = 'waiting_for_blink';
                      consecutiveOpenFrames = 0;

                      let reason = 'Position face in center';
                      if (!isSizeOK) reason = width < minFaceSize ? 'Move closer' : 'Move back';
                      if (!isFullyInFrame) reason = 'Face cut off, center your face';
                      if (!isCentered) reason = 'Center your face';
                      
                      handleFaceConstraintFail(reason);
                  }
              } else {
                  handleNoFace();
                  captureState = 'waiting_for_blink';
              }
          } else {
              checkImageQualityManual();
          }
        }, 100); // Check faster (100ms) for blink detection
      }
      
      function getEyeOpenness(eyePoints) {
          // Simple approximation: Height / Width of eye box
          let maxY = eyePoints[0].y, minY = eyePoints[0].y;
          let maxX = eyePoints[0].x, minX = eyePoints[0].x;
          
          eyePoints.forEach(p => {
              if(p.y > maxY) maxY = p.y;
              if(p.y < minY) minY = p.y;
              if(p.x > maxX) maxX = p.x;
              if(p.x < minX) minX = p.x;
          });
          
          const height = maxY - minY;
          const width = maxX - minX;
          return height / width;
      }
      
      // Update handleFaceDetected to accept custom status message
      function handleFaceDetected(detection, message = 'Face detected') {
          const score = Math.round(detection.score * 100);
          updateIndicator(lightingIndicator, 'excellent', message);
          updateIndicator(centeringIndicator, 'good', 'Position OK');
          qualityScoreFill.style.width = score + '%';
          qualityScoreText.textContent = score + '%';
          faceGuide.className = 'face-guide aligned';
          // capturePhotoBtn.disabled = false; // Disable manual capture to enforce logic? Or allow override?
          // Let's allow manual capture still, but auto-capture is the cool feature.
          capturePhotoBtn.disabled = false; 
      }

  function handleFaceConstraintFail(reason) {
      updateIndicator(lightingIndicator, 'excellent', 'Face detected'); // Face is detected but position bad
      updateIndicator(centeringIndicator, 'poor', reason);
      
      qualityScoreFill.style.width = '30%';
      qualityScoreText.textContent = '30%';
      
      faceGuide.className = 'face-guide misaligned';
      capturePhotoBtn.disabled = true;
  }

  function handleNoFace() {
      updateIndicator(lightingIndicator, 'poor', 'No face detected');
      updateIndicator(centeringIndicator, 'poor', 'Position face in frame');
      
      qualityScoreFill.style.width = '0%';
      qualityScoreText.textContent = '0%';
      
      faceGuide.className = 'face-guide misaligned';
      capturePhotoBtn.disabled = true;
  }

  // Fallback Manual Check (from original code)
  function checkImageQualityManual() {
     // Re-implement simplified brightness/centering check if AI disabled
     // ... (Keep simplified version or just basic checks)
     // For now, let's just use basic brightness
     const canvas = document.createElement('canvas');
     canvas.width = webcam.videoWidth;
     canvas.height = webcam.videoHeight;
     const ctx = canvas.getContext('2d');
     ctx.drawImage(webcam, 0, 0);
     const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
     const brightness = calculateBrightness(imageData);
     
     if(brightness > 80 && brightness < 220) {
         updateIndicator(lightingIndicator, 'good', 'Lighting OK');
         capturePhotoBtn.disabled = false;
         qualityScoreFill.style.width = '70%';
         qualityScoreText.textContent = '70%';
     } else {
         updateIndicator(lightingIndicator, 'poor', 'Adjust lighting');
         capturePhotoBtn.disabled = true;
         qualityScoreFill.style.width = '30%';
         qualityScoreText.textContent = '30%';
     }
  }
  
  function calculateBrightness(imageData) {
    let total = 0;
    const data = imageData.data;
    for (let i = 0; i < data.length; i += 4) {
      total += (data[i] + data[i+1] + data[i+2]) / 3;
    }
    return total / (imageData.width * imageData.height);
  }

  function updateIndicator(indicator, status, text) {
    indicator.className = `quality-indicator ${status}`;
    indicator.querySelector('span').textContent = text;
    
    const icon = indicator.querySelector('.quality-icon');
    if (status === 'excellent') {
      icon.textContent = '✓';
      icon.className = 'quality-icon bg-green-600 text-white';
    } else if (status === 'good') {
      icon.textContent = '○';
      icon.className = 'quality-icon bg-green-100 text-green-600';
    } else {
      icon.textContent = '✕';
      icon.className = 'quality-icon bg-red-100 text-red-600';
    }
  }

  function updateProgress(step) {
    for (let i = 1; i <= 3; i++) {
      const stepEl = document.getElementById('step-' + i);
      const lineEl = document.getElementById('line-' + i);
      if(stepEl && lineEl) { // Check existence
          if (i < step) {
            stepEl.className = 'progress-step completed';
            lineEl.className = 'progress-line active';
          } else if (i === step) {
            stepEl.className = 'progress-step active';
          } else {
            stepEl.className = 'progress-step';
          }
      } else if (stepEl && i === 3) { // Last step no line
           if (i < step) {
            stepEl.className = 'progress-step completed';
          } else if (i === step) {
            stepEl.className = 'progress-step active';
          } else {
            stepEl.className = 'progress-step';
          }
      }
    }
  }

  // Matric form submission
  matricForm.addEventListener('submit', function(e) {
    e.preventDefault();
    errorMessage.classList.add('hidden');
    const matric = document.getElementById('matric_number').value.trim();
    const validateBtn = document.getElementById('validate-btn');
    const validateSpinner = document.getElementById('validate-spinner');
    const validateText = document.getElementById('validate-text');
    
    validateBtn.disabled = true;
    validateSpinner.classList.remove('hidden');
    validateText.classList.add('hidden');
    
    fetch('/api/student/validate-matric', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('#matric-form input[name="_token"]').value
      },
      body: JSON.stringify({ matric_number: matric })
    })
    .then(res => res.json())
    .then(data => {
      validateBtn.disabled = false;
      validateSpinner.classList.add('hidden');
      validateText.classList.remove('hidden');
      
      if (data.success && data.face_registration_enabled) {
        // Show student info
        studentInfo.innerHTML = `
          <div class="flex items-center gap-2 font-semibold text-green-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            ${data.student.full_name}
          </div>
          <div class="text-sm text-green-700 ml-7">${data.student.department}</div>
        `;
        
        document.getElementById('register_matric_number').value = matric;
        
        step1Card.classList.add('hidden');
        step2Card.classList.remove('hidden');
        updateProgress(2);
        startCamera();
      } else {
        errorText.textContent = data.message || 'Validation failed';
        errorMessage.classList.remove('hidden');
      }
    })
    .catch(err => {
      console.error(err);
      validateBtn.disabled = false;
      validateSpinner.classList.add('hidden');
      validateText.classList.remove('hidden');
      errorText.textContent = 'An error occurred. Please try again.';
      errorMessage.classList.remove('hidden');
    });
  });

  // Capture Photo
  capturePhotoBtn.addEventListener('click', function() {
    // Capture high quality frame (from original video stream resolution)
    snapshot.width = webcam.videoWidth;
    snapshot.height = webcam.videoHeight;
    const ctx = snapshot.getContext('2d');
    ctx.drawImage(webcam, 0, 0);
    
    const dataUrl = snapshot.toDataURL('image/jpeg', 0.9);
    previewImage.src = dataUrl;
    referenceInput.value = dataUrl;
    
    webcamContainer.classList.add('hidden');
    photoPreview.classList.remove('hidden');
    capturePhotoBtn.classList.add('hidden');
    retakePhotoBtn.classList.remove('hidden');
    
    stopCamera();
    
    // Auto proceed to step 3? Or allow review?
    // Let's show step 3 below
    step3Card.classList.remove('hidden');
    updateProgress(3);
    
    // Smooth scroll to step 3
    step3Card.scrollIntoView({ behavior: 'smooth' });
  });
  
  retakePhotoBtn.addEventListener('click', function() {
    photoPreview.classList.add('hidden');
    webcamContainer.classList.remove('hidden');
    capturePhotoBtn.classList.remove('hidden');
    retakePhotoBtn.classList.add('hidden');
    
    step3Card.classList.add('hidden');
    updateProgress(2);
    
    startCamera();
  });

  // Form submission with AJAX
  registerForm.addEventListener('submit', function(e) {
    e.preventDefault();
    stopCamera();
    
    const submitBtn = document.getElementById('register-face-btn');
    const originalText = submitBtn.innerHTML;
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
      <svg class="w-5 h-5 inline-block mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
      </svg>
      Registering...
    `;
    
    // Get form data
    const formData = new FormData(registerForm);
    
    // Send AJAX request
    fetch(registerForm.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': registerForm.querySelector('input[name="_token"]').value,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Show success notification
        showNotification('success', data.message || 'Face registered successfully! Email and SMS notifications have been sent to you.');
        
        // Reset form and steps
        setTimeout(() => {
          registerForm.reset();
          step1Card.classList.remove('hidden');
          step2Card.classList.add('hidden');
          step3Card.classList.add('hidden');
          updateProgress(1);
          
          // Reset camera and photo preview
          photoPreview.classList.add('hidden');
          webcamContainer.classList.remove('hidden');
          capturePhotoBtn.classList.remove('hidden');
          retakePhotoBtn.classList.add('hidden');
          
          // Hide student info
          const studentInfo = document.getElementById('student-info');
          if (studentInfo) {
            studentInfo.classList.add('hidden');
          }
        }, 3000);
      } else {
        // Show error notification
        showNotification('error', data.message || 'Registration failed. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('error', 'An error occurred. Please try again.');
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    });
  });
  
  // Notification function
  function showNotification(type, message) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notif => notif.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification-toast notification-${type}`;
    
    const icon = type === 'success' 
      ? '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
      : '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
    
    notification.innerHTML = `
      <div class="notification-content">
        <div class="notification-icon">${icon}</div>
        <div class="notification-message">${message}</div>
        <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
      notification.classList.add('show');
    }, 100);
    
    // Auto remove after 5 seconds for success, 7 seconds for error
    setTimeout(() => {
      notification.classList.remove('show');
      setTimeout(() => notification.remove(), 300);
    }, type === 'success' ? 5000 : 7000);
  }

  // Cleanup
  window.addEventListener('beforeunload', function() {
    stopCamera();
  });

  retryBtn.addEventListener('click', function() {
    startCamera();
  });
});
document.addEventListener('DOMContentLoaded', function() {
  // ...existing code...
  const returnToCapture = document.getElementById('return-to-capture-link');
  if (returnToCapture) {
    returnToCapture.addEventListener('click', function(e) {
      e.preventDefault();
      document.getElementById('step3-card').classList.add('hidden');
      document.getElementById('step2-card').classList.remove('hidden');
      document.getElementById('photo-preview').classList.add('hidden');
      document.getElementById('register-webcam-container').classList.remove('hidden');
      document.getElementById('register-capture-photo').classList.remove('hidden');
      document.getElementById('register-retake-photo').classList.add('hidden');
      updateProgress(2);
      if (typeof startCamera === 'function') startCamera();
    });
  }
});
</script>
@endsection
