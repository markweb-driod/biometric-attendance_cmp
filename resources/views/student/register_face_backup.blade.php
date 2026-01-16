@extends('layout')
@section('content')
  <div class="w-full max-w-sm md:max-w-md mx-auto p-0 sm:p-4 mt-4 mb-6">
    <div class="bg-white rounded-2xl shadow-lg border-t-4 border-green-400 px-3 py-4 sm:px-5 sm:py-6 flex flex-col items-center w-full" style="min-height:unset;height:auto;box-shadow:0 2px 16px rgba(34,197,94,0.08);">
      <div class="flex items-center justify-between w-full mb-4">
        <h2 class="text-2xl font-extrabold text-green-800 tracking-tight text-center flex-1">Register Your Face</h2>
        <a href="/" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors duration-200 flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Back to Login
        </a>
      </div>
      <p id="matric-instruction" class="text-green-700 text-base text-center mb-2">Enter your matric number to begin face registration.</p>
      <p class="text-green-700 text-xs mb-2 text-center">Tip: Make sure your face is well-lit and clearly visible before capturing.</p>
      <p class="text-red-700 text-xs mb-2 text-center">Tip: DO NOT UNDER ANY CIRCUMSTANE VALIDATE A PROFILE THAT IS NOT YOURS.</p>

      <div id="error-message" class="hidden mb-3 px-4 py-2 bg-red-100 text-red-700 rounded-lg text-center font-semibold"></div>
      <form id="matric-form" class="space-y-4 w-full" autocomplete="off">
        <div class="mb-3"><br>
          <label class="block mb-1 font-semibold text-green-900" for="matric_number">Matric Number</label>
          <input type="text" name="matric_number" id="matric_number" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-green-500" required autofocus />
        </div>
        <br>
        <button type="submit" id="validate-btn" class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white text-lg font-bold px-4 py-3 rounded-xl shadow-lg transition mt-2" aria-label="Validate">
          <span id="validate-spinner" class="hidden"><svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg></span>
          <span id="validate-text">Validate</span>
        </button>
      </form>
      <form id="register-face-form" class="space-y-4 w-full hidden" enctype="multipart/form-data" method="POST" action="{{ route('student.register-face') }}">
        @csrf
        <input type="hidden" name="matric_number" id="register_matric_number">
        <div id="modal-student-details" class="mb-3 p-2 bg-green-50 rounded text-green-900 text-sm"></div>
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-green-900">Capture Reference Photo</label>
          <div class="border-2 border-green-200 rounded-xl p-2 bg-green-50 flex flex-col items-center">
            <div id="register-webcam-container" class="w-56 h-40 sm:w-64 sm:h-48 rounded overflow-hidden mb-2 flex items-center justify-center bg-green-50">
              <video id="register-webcam" autoplay playsinline class="w-full h-full object-contain rounded" style="aspect-ratio:4/3; background-color: #f0fdf4; filter: brightness(1.3) contrast(1.1);"></video>
            </div>
            <canvas id="register-snapshot" class="hidden"></canvas>
            <div id="register-photo-preview" class="mb-2 w-56 h-40 sm:w-64 sm:h-48 flex flex-col items-center hidden"></div>
            <div class="flex flex-col sm:flex-row gap-2 w-full">
              <button type="button" id="register-capture-photo" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl mb-2 transition text-sm sm:text-base">Capture Photo</button>
              <button type="button" id="register-retake-photo" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl mb-2 transition text-sm sm:text-base hidden">Retake Photo</button>
            </div>
          </div>
        </div>
        <input type="hidden" name="reference_image" id="reference_image_input">
        <div class="flex gap-3 mt-4">
          <a href="/" class="flex-1 flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-lg font-bold px-4 py-4 rounded-xl shadow-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Login
          </a>
          <button type="submit" class="flex-1 flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white text-lg font-bold px-4 py-4 rounded-xl shadow-lg transition" id="register-face-btn" disabled>Register Face</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var matricForm = document.getElementById('matric-form');
  var registerForm = document.getElementById('register-face-form');
  var errorMessage = document.getElementById('error-message');
  var registerMatricInput = document.getElementById('register_matric_number');
  var webcamStream = null;
  var webcam = document.getElementById('register-webcam');
  var snapshot = document.getElementById('register-snapshot');
  var capturePhotoBtn = document.getElementById('register-capture-photo');
  var retakePhotoBtn = document.getElementById('register-retake-photo');
  var photoPreview = document.getElementById('register-photo-preview');
  var webcamContainer = document.getElementById('register-webcam-container');
  var registerFaceBtn = document.getElementById('register-face-btn');
  var referenceInput = document.getElementById('reference_image_input');

  matricForm.addEventListener('submit', function(e) {
    e.preventDefault();
    errorMessage.classList.add('hidden');
    var matric = document.getElementById('matric_number').value.trim();
    var validateBtn = document.getElementById('validate-btn');
    var validateSpinner = document.getElementById('validate-spinner');
    var validateText = document.getElementById('validate-text');
    validateBtn.disabled = true;
    validateSpinner.classList.remove('hidden');
    validateText.classList.add('hidden');
    fetch('/api/student/validate-matric', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ matric_number: matric })
    })
    .then(res => res.json())
    .then(data => {
      console.log('Matric validation response:', data);
      validateBtn.disabled = false;
      validateSpinner.classList.add('hidden');
      validateText.classList.remove('hidden');
      
      if (data.success && data.face_registration_enabled) {
        matricForm.classList.add('hidden');
        registerForm.classList.remove('hidden');
        registerMatricInput.value = matric;
        // Show student details
        const detailsHtml =
          `<div class='mb-1'><span class='font-bold'>Name:</span> ${data.student?.full_name || data.student?.name || ''}</div>` +
          `<div class='mb-1'><span class='font-bold'>Matric Number:</span> ${data.student?.matric_number || ''}</div>` +
          (data.student?.academic_level ? `<div><span class='font-bold'>Level:</span> ${data.student.academic_level}</div>` : '');
        document.getElementById('modal-student-details').innerHTML = detailsHtml;
        // Start webcam
        navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
          webcam.srcObject = stream;
          webcamStream = stream;
        });
        document.getElementById('matric-instruction').style.display = 'none';
      } else if (data.already_registered) {
        // Show already registered message with student details
        const alreadyRegisteredHtml = `
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <div class="flex items-center mb-2">
              <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
              <h3 class="text-lg font-semibold text-yellow-800">Face Already Registered</h3>
            </div>
            <p class="text-yellow-700 mb-3">${data.message}</p>
            <div class="bg-white rounded p-3 mb-3">
              <div class='mb-1'><span class='font-bold'>Name:</span> ${data.student?.full_name || ''}</div>
              <div class='mb-1'><span class='font-bold'>Matric Number:</span> ${data.student?.matric_number || ''}</div>
              <div class='mb-1'><span class='font-bold'>Level:</span> ${data.student?.academic_level || ''}</div>
            </div>
            <div class="flex gap-2">
              <a href="/" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition">Back to Login</a>
              <a href="/student/attendance-capture" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition">Mark Attendance</a>
            </div>
          </div>
        `;
        document.getElementById('modal-student-details').innerHTML = alreadyRegisteredHtml;
        matricForm.classList.add('hidden');
        registerForm.classList.remove('hidden');
        registerForm.querySelector('button[type="submit"]').style.display = 'none';
        document.getElementById('matric-instruction').style.display = 'none';
      } else {
        errorMessage.textContent = data.message || 'Matric number not found or face registration not enabled.';
        errorMessage.classList.remove('hidden');
      }
    })
    .catch(() => {
      errorMessage.textContent = 'Network error. Please try again.';
      errorMessage.classList.remove('hidden');
      validateBtn.disabled = false;
      validateSpinner.classList.add('hidden');
      validateText.classList.remove('hidden');
    });
  });

  registerForm.addEventListener('submit', function(e) {
    // Show toast on success
    setTimeout(function() {
      showToast('Face registration successful!', 'success');
    }, 200);
  });

  capturePhotoBtn.addEventListener('click', function() {
    snapshot.width = webcam.videoWidth;
    snapshot.height = webcam.videoHeight;
    snapshot.getContext('2d').drawImage(webcam, 0, 0);
    const captured = snapshot.toDataURL('image/jpeg');
    // Check brightness
    if (isImageTooDark(snapshot)) {
      errorMessage.textContent = 'Your photo is too dark. Please move to a brighter area and try again.';
      errorMessage.classList.remove('hidden');
      return;
    }
    photoPreview.innerHTML = '<img src="' + captured + '" class="rounded w-full" style="max-height:200px;" />';
    referenceInput.value = captured;
    webcamContainer.classList.add('hidden');
    photoPreview.classList.remove('hidden');
    capturePhotoBtn.classList.add('hidden');
    retakePhotoBtn.classList.remove('hidden');
    registerFaceBtn.disabled = false;
  });

  retakePhotoBtn.addEventListener('click', function() {
    referenceInput.value = '';
    photoPreview.innerHTML = '';
    photoPreview.classList.add('hidden');
    webcamContainer.classList.remove('hidden');
    capturePhotoBtn.classList.remove('hidden');
    retakePhotoBtn.classList.add('hidden');
    registerFaceBtn.disabled = true;
  });
});

function isImageTooDark(canvas) {
  const ctx = canvas.getContext('2d');
  const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
  let total = 0;
  for (let i = 0; i < imageData.data.length; i += 4) {
    total += (imageData.data[i] + imageData.data[i+1] + imageData.data[i+2]) / 3;
  }
  const avg = total / (canvas.width * canvas.height);
  return avg < 60; // Adjust threshold as needed
}

function showToast(msg, type = 'success') {
  let toast = document.createElement('div');
  toast.textContent = msg;
  toast.className = 'fixed top-6 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-xl shadow-lg text-white font-bold text-lg ' + (type === 'success' ? 'bg-green-600' : 'bg-red-600');
  document.body.appendChild(toast);
  setTimeout(() => { toast.remove(); }, 3000);
}
</script>
@endsection 