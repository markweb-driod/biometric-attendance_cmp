<!-- View Face Image Modal -->
<div id="view-face-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full flex flex-col items-center">
    <h3 class="text-xl font-bold text-green-800 mb-4">Student Face Image</h3>
    <img id="view-face-img" src="" alt="Face Image" class="rounded-xl w-64 h-48 object-contain border border-green-200 mb-4" />
    <button class="px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition" onclick="closeModal('view-face-modal')">Close</button>
  </div>
</div>
<!-- Update/Re-register Face Modal (Webcam) -->
<div id="update-face-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full flex flex-col items-center">
    <h3 class="text-xl font-bold text-green-800 mb-4">Re-register Face Image</h3>
    <div id="update-face-details" class="mb-4 w-full text-center text-green-900 font-semibold"></div>
    <div class="w-64 h-48 rounded overflow-hidden mb-2 flex items-center justify-center bg-green-50" id="webcam-container">
      <video id="webcam" autoplay playsinline class="w-full h-full object-contain rounded" style="aspect-ratio:4/3; background-color: #f0fdf4;"></video>
    </div>
    <canvas id="snapshot" class="hidden"></canvas>
    <div id="photo-preview" class="mb-2 w-64 h-48 flex flex-col items-center"></div>
    <div class="flex gap-2 w-full mb-4">
      <button type="button" id="capture-photo" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition">Capture</button>
      <button type="button" id="retake-photo" class="flex-1 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl transition hidden">Retake</button>
    </div>
    <div class="flex gap-2 w-full">
      <button type="button" id="save-face-btn" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition" disabled>Save</button>
      <button type="button" class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold rounded-xl transition" onclick="closeModal('update-face-modal')">Cancel</button>
    </div>
  </div>
</div>
<!-- Delete Confirmation Modal -->
<div id="delete-face-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full flex flex-col items-center">
    <h3 class="text-xl font-bold text-red-700 mb-4">Delete Face Image?</h3>
    <p class="mb-6 text-gray-700 text-center">Are you sure you want to delete this student's face image? This action cannot be undone.</p>
    <div class="flex gap-4 w-full justify-center">
      <button id="confirm-delete-btn" class="px-6 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">Yes, Delete</button>
      <button class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition" onclick="closeModal('delete-face-modal')">Cancel</button>
    </div>
  </div>
</div>
<!-- Enable/Disable Confirmation Modal -->
<div id="toggle-face-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full flex flex-col items-center">
    <h3 id="toggle-face-title" class="text-xl font-bold mb-4"></h3>
    <p id="toggle-face-msg" class="mb-6 text-gray-700 text-center"></p>
    <div class="flex gap-4 w-full justify-center">
      <button id="confirm-toggle-btn" class="px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">Yes, Proceed</button>
      <button class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition" onclick="closeModal('toggle-face-modal')">Cancel</button>
    </div>
  </div>
</div>
<!-- Toast Notification -->
<div id="toast" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-xl shadow-lg text-white font-bold text-lg hidden"></div>
<script>
function showToast(msg, type = 'success') {
  let toast = document.getElementById('toast');
  toast.textContent = msg;
  toast.className = 'fixed top-6 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-xl shadow-lg text-white font-bold text-lg ' + (type === 'success' ? 'bg-green-600' : 'bg-red-600');
  toast.classList.remove('hidden');
  setTimeout(() => { toast.classList.add('hidden'); }, 3000);
}
function closeModal(id) {
  document.getElementById(id).classList.add('hidden');
}
</script> 