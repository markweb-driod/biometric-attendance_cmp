@extends('layout')
@section('content')
  <!-- Decorative background shapes -->
  <div class="absolute top-0 left-0 w-32 h-32 bg-green-100 rounded-full opacity-30 -z-10 blur-2xl" style="transform: translate(-40%,-40%)"></div>
  <div class="absolute bottom-0 right-0 w-40 h-40 bg-green-200 rounded-full opacity-20 -z-10 blur-2xl" style="transform: translate(40%,40%)"></div>
  <div class="absolute top-1/2 left-1/2 w-72 h-72 bg-green-50 rounded-full opacity-10 -z-10 blur-3xl" style="transform: translate(-50%,-50%)"></div>
  <div class="w-full max-w-xs sm:max-w-sm mx-auto p-0 sm:p-4 mt-4">
    <!-- Card -->
    <div class="bg-gradient-to-br from-white via-green-50 to-green-100 rounded-3xl shadow-2xl border-t-4 border-green-400 px-3 py-4 sm:px-5 sm:py-6 flex flex-col items-center w-full" style="min-height:unset;height:auto;">
      <!-- Stepper -->
      <div class="flex items-center gap-2 mb-6 w-full justify-center">
        <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
        <span class="text-green-700 font-semibold text-sm">Step 1 of 2: Validate Details</span>
      </div>
      <h2 class="text-l font-extrabold text-green-800 mb-2 tracking-tight text-center">Student Attendance System</h2>
      <p class="text-green-700 text-sm text-center mb-6 font-normal">Welcome! Please enter your details to mark attendance.</p>
      <div id="error-message" class="hidden mb-4 px-4 py-2 bg-red-100 text-red-700 rounded-lg text-center font-semibold"></div>
      <form id="fetch-form" class="space-y-6 w-full" autocomplete="off">
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-green-900" for="matric_number">Matric Number</label>
          <input type="text" name="matric_number" id="matric_number" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-green-500" required autofocus aria-label="Matric Number" />
        </div>
        <div class="mb-4">
          <label class="block mb-1 font-semibold text-green-900" for="attendance_code">Attendance Code</label>
          <input type="text" name="attendance_code" id="attendance_code" class="w-full border-2 border-green-200 rounded-xl px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-green-500" required aria-label="Attendance Code" />
        </div>
        <button type="submit" id="validate-btn" class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white text-base font-bold px-4 py-2 rounded-lg shadow-md transition mt-2" aria-label="Validate Attendance">
          <span id="validate-spinner" class="hidden"><svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg></span>
          <span id="validate-text">Validate</span>
        </button>
    </form>
      <div class="mt-6 w-full text-center">
        <a href="#" class="text-green-700 underline text-xs hover:text-green-900">Need help?</a>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div id="details-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-start justify-center z-50 hidden transition-all">
  <div class="bg-white rounded-3xl shadow-2xl border border-green-200 w-full max-w-full sm:max-w-md lg:max-w-lg xl:max-w-xl p-2 sm:p-6 lg:p-8 animate-fade-in mx-2 my-12 flex flex-col overflow-y-auto max-h-[90vh]">
    <!-- Stepper in Modal -->
    <div class="flex items-center gap-2 mb-4 w-full justify-center">
      <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div>
      <span class="text-green-700 font-semibold text-base">Step 2 of 2: Submit Attendance</span>
    </div>
    <h3 class="text-lg sm:text-xl font-extrabold text-green-800 mb-2">Confirm Details</h3>
    <div id="student-details" class="bg-green-50 border border-green-100 rounded-xl p-3 sm:p-4 mb-4 text-green-900 text-sm sm:text-base"></div>
    <div class="border-t border-green-100 my-3"></div>
    <div class="mb-4">
      <label class="block mb-1 font-semibold text-green-900">Capture Photo</label>
      <div class="border-2 border-green-200 rounded-xl p-2 bg-green-50 flex flex-col items-center">
        <div class="w-56 h-40 sm:w-64 sm:h-48 rounded overflow-hidden mb-2 flex items-center justify-center bg-green-50" id="webcam-container">
          <video id="webcam" autoplay playsinline class="w-full h-full object-contain rounded" style="aspect-ratio:4/3; background-color: #f0fdf4;"></video>
        </div>
        <canvas id="snapshot" class="hidden"></canvas>
        <div id="photo-preview" class="mb-2 w-56 h-40 sm:w-64 sm:h-48 flex flex-col items-center"></div>
        <div class="flex flex-col sm:flex-row gap-2 w-full">
          <button type="button" id="capture-photo" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl mb-2 transition text-sm sm:text-base"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Capture Photo</button>
          <button type="button" id="retake-photo" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl mb-2 transition text-sm sm:text-base hidden"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>Retake Photo</button>
        </div>
      </div>
    </div>
    <div class="flex flex-col sm:flex-row justify-between gap-2 mt-6">
      <button type="button" id="back-to-validate" class="w-full sm:w-auto px-4 py-2 border border-green-300 bg-white hover:bg-green-50 text-green-700 font-semibold rounded-xl text-sm sm:text-base transition">Back</button>
      <button type="button" id="proceed-capture" class="w-full sm:w-auto px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl text-sm sm:text-base transition">Submit Attendance</button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var fetchForm = document.getElementById('fetch-form');
    var errorMessage = document.getElementById('error-message');
    var detailsModal = document.getElementById('details-modal');
    var studentDetails = document.getElementById('student-details');
    var closeModalBtn = document.getElementById('close-modal');
    var proceedCaptureBtn = document.getElementById('proceed-capture');
    var matricInput = document.getElementById('matric_number');
    var codeInput = document.getElementById('attendance_code');
    function showToast(msg, type = 'success') {
      let toast = document.createElement('div');
      toast.textContent = msg;
      toast.className = 'fixed top-6 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-xl shadow-lg text-white font-bold text-lg ' + (type === 'success' ? 'bg-green-600' : 'bg-red-600');
      document.body.appendChild(toast);
      setTimeout(() => { toast.remove(); }, 3000);
    }

    fetchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        errorMessage.classList.add('hidden');
        var matric = matricInput.value.trim();
        var code = codeInput.value.trim();
        var validateBtn = document.getElementById('validate-btn');
        var validateSpinner = document.getElementById('validate-spinner');
        var validateText = document.getElementById('validate-text');
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
        .then(function(res) { return res.json().then(function(data) { return { ok: res.ok, data: data }; }); })
        .then(function(result) {
            validateBtn.disabled = false;
            validateSpinner.classList.add('hidden');
            validateText.classList.remove('hidden');
            if (result.ok && result.data.success) {
                studentDetails.innerHTML =
            '<div class="mb-2"><span class="font-bold">Name:</span> ' + result.data.student.name + '</div>' +
            '<div class="mb-2"><span class="font-bold">Matric Number:</span> ' + result.data.student.matric_number + '</div>' +
            '<div><span class="font-bold">Class:</span> ' + result.data.classroom.name + ' (' + result.data.classroom.code + ')</div>';
                detailsModal.classList.remove('hidden');
            } else {
                errorMessage.textContent = result.data.message || 'Invalid details.';
                errorMessage.classList.remove('hidden');
          if ((result.data.message || '').toLowerCase().includes('not active')) {
            showToast('Attendance session is not active. Please ask your lecturer for the latest code.', 'error');
          }
            }
        })
        .catch(function(err) {
            console.error('Fetch details error:', err);
            let msg = 'Network error. Please try again.';
            if (err && err.message) msg += ' (' + err.message + ')';
            errorMessage.textContent = msg + ' If this persists, check your internet connection or contact admin.';
            errorMessage.classList.remove('hidden');
        });
    });

    let capturedImage = null;
    let webcamStream = null;
    const webcam = document.getElementById('webcam');
    const snapshot = document.getElementById('snapshot');
    const capturePhotoBtn = document.getElementById('capture-photo');
    const retakePhotoBtn = document.getElementById('retake-photo');
    const photoPreview = document.getElementById('photo-preview');
    const webcamContainer = document.getElementById('webcam-container');

    function enableProceedBtn(enable) {
        proceedCaptureBtn.disabled = !enable;
        proceedCaptureBtn.classList.toggle('opacity-50', !enable);
    }
    enableProceedBtn(false);

    capturePhotoBtn.addEventListener('click', function() {
        if (!webcamStream) return;
        snapshot.width = webcam.videoWidth;
        snapshot.height = webcam.videoHeight;
        snapshot.getContext('2d').drawImage(webcam, 0, 0);
        capturedImage = snapshot.toDataURL('image/jpeg');
        photoPreview.innerHTML = '<img src="' + capturedImage + '" class="rounded w-full" style="max-height:200px;" />';
        webcamContainer.classList.add('hidden');
        capturePhotoBtn.classList.add('hidden');
        retakePhotoBtn.classList.remove('hidden');
        enableProceedBtn(true);
    });

    retakePhotoBtn.addEventListener('click', function() {
        capturedImage = null;
        photoPreview.innerHTML = '';
        webcamContainer.classList.remove('hidden');
        capturePhotoBtn.classList.remove('hidden');
        retakePhotoBtn.classList.add('hidden');
        enableProceedBtn(false);
    });

    // Use MutationObserver to detect when modal is shown and start webcam
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                if (!detailsModal.classList.contains('hidden') && !webcamStream) {
                    navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
                        webcam.srcObject = stream;
                        webcamStream = stream;
                    }).catch(function() {
                        photoPreview.innerHTML = '<span class="text-red-600">Unable to access webcam.</span>';
                        capturePhotoBtn.disabled = true;
                    });
                }
            }
        });
    });
    observer.observe(detailsModal, { attributes: true });

    closeModalBtn && closeModalBtn.addEventListener('click', function() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
        enableProceedBtn(false);
        capturedImage = null;
        photoPreview.innerHTML = '';
        webcamContainer.classList.remove('hidden');
        capturePhotoBtn.classList.remove('hidden');
        retakePhotoBtn.classList.add('hidden');
    });

    proceedCaptureBtn.addEventListener('click', function() {
      if (!capturedImage) {
        showToast('Please capture a photo first.', 'error');
        return;
      }
        var matric = matricInput.value.trim();
        var code = codeInput.value.trim();
      fetch('{{ url('api/student/capture-attendance') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
        body: JSON.stringify({ matric_number: matric, attendance_code: code, image: capturedImage })
        })
        .then(function(res) { return res.json().then(function(data) { return { ok: res.ok, data: data }; }); })
        .then(function(result) {
            if (result.ok && result.data.success) {
          showToast('Attendance captured successfully!', 'success');
                detailsModal.classList.add('hidden');
                setTimeout(function() {
                    window.location.href = '/';
                }, 1200); // 1.2s delay so user sees the toast
            } else {
          showToast(result.data.message || 'Failed to capture attendance.', 'error');
            }
        })
        .catch(function(err) {
          console.error('Attendance capture error:', err);
          let msg = 'Network error. Please try again.';
          if (err && err.message) msg += ' (' + err.message + ')';
          showToast(msg + ' If this persists, check your internet connection or contact admin.', 'error');
        });
    });
});
</script>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill attendance code from URL and make read-only if present
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code');
    const codeInput = document.getElementById('attendance_code');
    const matricInput = document.getElementById('matric_number');
    if (code && codeInput) {
        codeInput.value = code;
        codeInput.readOnly = true;
        codeInput.classList.add('bg-gray-100', 'cursor-not-allowed');
        codeInput.setAttribute('aria-readonly', 'true');
        codeInput.setAttribute('tabindex', '-1');
        if (matricInput) matricInput.focus();
    }

    // Spinner for Validate button
    const fetchForm = document.getElementById('fetch-form');
    const validateBtn = document.getElementById('validate-btn');
    const validateSpinner = document.getElementById('validate-spinner');
    const validateText = document.getElementById('validate-text');
    if (fetchForm && validateBtn && validateSpinner && validateText) {
        fetchForm.addEventListener('submit', function(e) {
            validateBtn.disabled = true;
            validateSpinner.classList.remove('hidden');
            validateText.classList.add('opacity-50');
        });
    }
});
</script>
@endpush 