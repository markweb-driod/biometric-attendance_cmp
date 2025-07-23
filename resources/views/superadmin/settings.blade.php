@extends('layouts.superadmin')

@section('title', 'Settings')
@section('page-title', 'Settings')
@section('page-description', 'Manage your profile and system settings')

@section('content')
<div class="w-full px-2 py-10 space-y-12">
    <!-- Top Row: Profile + Change Password -->
    <div class="flex flex-col md:flex-row gap-4 w-full">
        <section aria-labelledby="profile-heading" class="flex-[1_1_0%] min-w-0 w-full bg-white rounded-2xl shadow-xl border border-green-200 p-10 mb-0 transition hover:shadow-2xl">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 bg-green-200 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-green-600 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14a4 4 0 100-8 4 4 0 000 8zm0 2c-2.21 0-4 1.79-4 4h8c0-2.21-1.79-4-4-4z" /></svg>
                </div>
                <h2 id="profile-heading" class="text-2xl font-extrabold text-green-800 tracking-tight">Profile</h2>
            </div>
            <form class="space-y-8">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition" value="Superadmin">
                    </div>
                    <div class="flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition" value="superadmin@nsuk.edu.ng">
                    </div>
                </div>
                <div class="flex justify-end mt-2"><button class="px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">Update Profile</button></div>
        </form>
        </section>
        <section aria-labelledby="password-heading" class="flex-[1_1_0%] min-w-0 w-full bg-white rounded-2xl shadow-xl border border-blue-200 p-10 mb-0 transition hover:shadow-2xl">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-600 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17v-6m0 0V7m0 4h.01" /></svg>
                </div>
                <h2 id="password-heading" class="text-2xl font-extrabold text-blue-800 tracking-tight">Change Password</h2>
            </div>
            <form class="space-y-8">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base transition">
                    </div>
                    <div class="flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base transition">
                    </div>
                    <div class="flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base transition">
                    </div>
    </div>
                <div class="flex justify-end mt-2"><button class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">Change Password</button></div>
        </form>
        </section>
    </div>
    <!-- Divider -->
    <div class="border-t border-gray-200"></div>
    <!-- Face Verification Config Section -->
    <section aria-labelledby="face-heading" class="bg-white rounded-2xl shadow-xl border border-green-200 p-10 mt-8">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-12 h-12 bg-green-200 rounded-full flex items-center justify-center">
                <svg class="w-7 h-7 text-green-600 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 01-8 0m8 0a4 4 0 00-8 0m8 0V8a4 4 0 00-8 0v4m8 0v4a4 4 0 01-8 0v-4" /></svg>
            </div>
            <h2 id="face-heading" class="text-2xl font-extrabold text-green-800 tracking-tight">Face Verification Settings</h2>
        </div>
        @if(session('success'))
            <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded text-center font-semibold">
                {{ session('success') }}
            </div>
        @endif
        <form method="POST" action="{{ route('superadmin.face-config.update') }}" class="space-y-8" id="face-config-form">
            @csrf
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1 min-w-0">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Face Provider</label>
                    <select name="face_provider" id="face_provider_select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition">
                        <option value="faceplusplus" {{ (isset($provider) && $provider == 'faceplusplus') ? 'selected' : '' }}>Face++</option>
                        <option value="aws" {{ (isset($provider) && $provider == 'aws') ? 'selected' : '' }}>AWS Rekognition</option>
                        <option value="azure" {{ (isset($provider) && $provider == 'azure') ? 'selected' : '' }}>Azure Face API</option>
                    </select>
                </div>
                <!-- Face++ Fields -->
                <div class="flex-1 min-w-0 provider-fields" id="faceplusplus-fields">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Face++ API Key</label>
                    <input type="text" name="faceplusplus_api_key" id="faceplusplus_api_key" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition" value="{{ $apiKey ?? '' }}" placeholder="Face++ API Key">
                    <label class="block text-sm font-medium text-gray-700 mb-1 mt-2">Face++ API Secret</label>
                    <input type="text" name="faceplusplus_api_secret" id="faceplusplus_api_secret" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition" value="{{ $apiSecret ?? '' }}" placeholder="Face++ API Secret">
                    <button type="button" id="testFacePPBtn" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">Test Connection</button>
                </div>
                <!-- AWS Rekognition Fields -->
                <div class="flex-1 min-w-0 provider-fields hidden" id="aws-fields">
                    <label class="block text-sm font-medium text-gray-700 mb-1">AWS Access Key ID</label>
                    <input type="text" name="aws_access_key_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition" placeholder="AWS Access Key ID">
                    <label class="block text-sm font-medium text-gray-700 mb-1 mt-2">AWS Secret Access Key</label>
                    <input type="text" name="aws_secret_access_key" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition" placeholder="AWS Secret Access Key">
                    <label class="block text-sm font-medium text-gray-700 mb-1 mt-2">AWS Region</label>
                    <input type="text" name="aws_region" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition" placeholder="e.g. us-east-1">
                </div>
                <!-- Azure Face API Fields -->
                <div class="flex-1 min-w-0 provider-fields hidden" id="azure-fields">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Azure Face API Key</label>
                    <input type="text" name="azure_face_api_key" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition" placeholder="Azure Face API Key">
                    <label class="block text-sm font-medium text-gray-700 mb-1 mt-2">Azure Endpoint</label>
                    <input type="text" name="azure_face_endpoint" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-base transition" placeholder="https://<region>.api.cognitive.microsoft.com">
                </div>
            </div>
            <div class="flex justify-end mt-2"><button class="px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">Save Face Settings</button></div>
        </form>
        <script>
        function showProviderFields() {
            var provider = document.getElementById('face_provider_select').value;
            document.getElementById('faceplusplus-fields').classList.toggle('hidden', provider !== 'faceplusplus');
            document.getElementById('aws-fields').classList.toggle('hidden', provider !== 'aws');
            document.getElementById('azure-fields').classList.toggle('hidden', provider !== 'azure');
        }
        document.getElementById('face_provider_select').addEventListener('change', showProviderFields);
        showProviderFields();
        </script>
    </section>
    <!-- Face Registration Bulk Enable/Disable Section -->
    <section aria-labelledby="face-reg-bulk-heading" class="bg-white rounded-2xl shadow-xl border border-green-200 p-10 mt-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-green-200 rounded-full flex items-center justify-center">
                <svg class="w-7 h-7 text-green-600 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h2 id="face-reg-bulk-heading" class="text-2xl font-extrabold text-green-800 tracking-tight">Bulk Face Registration Control</h2>
        </div>
        <div class="flex items-center gap-4 mb-4">
            <span id="faceRegStatusBadge" class="inline-flex items-center px-4 py-1 rounded-full font-semibold text-white bg-gray-400 text-base">Loading status...</span>
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <button id="enableAllFaceRegBtn" type="button" class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">Enable Face Registration for All Students</button>
            <button id="disableAllFaceRegBtn" type="button" class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">Disable Face Registration for All Students</button>
        </div>
        <!-- Custom Confirmation Modal -->
        <div id="faceRegConfirmModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
          <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full flex flex-col items-center">
            <h3 class="text-xl font-bold text-green-800 mb-4" id="faceRegConfirmTitle">Confirm Action</h3>
            <p class="text-gray-700 mb-6 text-center" id="faceRegConfirmMsg">Are you sure?</p>
            <div class="flex gap-4 w-full justify-center">
              <button id="faceRegConfirmCancel" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">Cancel</button>
              <button id="faceRegConfirmOk" class="px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">Yes, Proceed</button>
            </div>
          </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
          function updateFaceRegStatusBadge(status) {
            const badge = document.getElementById('faceRegStatusBadge');
            if (!badge) {
              console.error('faceRegStatusBadge element not found');
              return;
            }
            if (status === 'all_enabled') {
              badge.textContent = 'Face Registration: Enabled for All';
              badge.className = 'inline-flex items-center px-4 py-1 rounded-full font-semibold text-white bg-green-600 text-base';
            } else if (status === 'all_disabled') {
              badge.textContent = 'Face Registration: Disabled for All';
              badge.className = 'inline-flex items-center px-4 py-1 rounded-full font-semibold text-white bg-red-600 text-base';
            } else {
              badge.textContent = 'Face Registration: Partially Enabled';
              badge.className = 'inline-flex items-center px-4 py-1 rounded-full font-semibold text-white bg-yellow-500 text-base';
            }
          }
          function fetchFaceRegStatus() {
            axios.get('/superadmin/students/face-registration-status')
              .then(res => updateFaceRegStatusBadge(res.data.status))
              .catch((err) => {
                updateFaceRegStatusBadge('partial');
                console.error('Failed to fetch face registration status', err);
              });
          }
          fetchFaceRegStatus();

          function showToast(msg, type = 'success') {
            let toast = document.createElement('div');
            toast.textContent = msg;
            toast.className = 'fixed top-6 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-xl shadow-lg text-white font-bold text-lg ' + (type === 'success' ? 'bg-green-600' : 'bg-red-600');
            document.body.appendChild(toast);
            setTimeout(() => { toast.remove(); }, 3000);
          }

          // Modal logic
          let faceRegModalAction = null;
          const modal = document.getElementById('faceRegConfirmModal');
          const modalTitle = document.getElementById('faceRegConfirmTitle');
          const modalMsg = document.getElementById('faceRegConfirmMsg');
          const modalOk = document.getElementById('faceRegConfirmOk');
          const modalCancel = document.getElementById('faceRegConfirmCancel');

          function showFaceRegModal(action, title, msg) {
            faceRegModalAction = action;
            modalTitle.textContent = title;
            modalMsg.textContent = msg;
            modal.classList.remove('hidden');
          }
          function hideFaceRegModal() {
            modal.classList.add('hidden');
            faceRegModalAction = null;
          }
          modalCancel.onclick = hideFaceRegModal;
          modalOk.onclick = function() {
            if (faceRegModalAction) faceRegModalAction();
            hideFaceRegModal();
          };
          // Dismiss modal on background click
          modal.onclick = function(e) { if (e.target === modal) hideFaceRegModal(); };

          document.getElementById('enableAllFaceRegBtn').onclick = function(e) {
              e.preventDefault();
              showFaceRegModal(
                function() {
                  axios.post('/superadmin/students/enable-face-registration-all')
                    .then(() => {
                      showToast('Face registration enabled for all students', 'success');
                      fetchFaceRegStatus();
                    })
                    .catch(() => showToast('Failed to enable face registration for all.', 'error'));
                },
                'Enable Face Registration',
                'Are you sure you want to enable face registration for ALL students?'
              );
          };
          document.getElementById('disableAllFaceRegBtn').onclick = function(e) {
              e.preventDefault();
              showFaceRegModal(
                function() {
                  axios.post('/superadmin/students/disable-face-registration-all')
                    .then(() => {
                      showToast('Face registration disabled for all students', 'success');
                      fetchFaceRegStatus();
                    })
                    .catch(() => showToast('Failed to disable face registration for all.', 'error'));
                },
                'Disable Face Registration',
                'Are you sure you want to disable face registration for ALL students?'
              );
          };
        });
        </script>
    </section>
    <!-- System Settings Section -->
    <section aria-labelledby="system-heading" class="bg-white rounded-2xl shadow-xl border border-orange-200 p-10">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-12 h-12 bg-orange-200 rounded-full flex items-center justify-center">
                <svg class="w-7 h-7 text-orange-600 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /></svg>
            </div>
            <h2 id="system-heading" class="text-2xl font-extrabold text-orange-800 tracking-tight">System Settings</h2>
        </div>
        <form class="space-y-8">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1 min-w-0">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Attendance Session Duration (minutes)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-base transition" value="60">
                </div>
                <div class="flex-1 min-w-0">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Absentees Alert Threshold</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-base transition" value="10">
                </div>
            </div>
            <div class="flex justify-end mt-2"><button class="px-6 py-2 bg-orange-600 text-white rounded-lg font-semibold hover:bg-orange-700 transition">Save Settings</button></div>
        </form>
    </section>
</div>
@endsection 