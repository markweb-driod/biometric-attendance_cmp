

<?php $__env->startSection('title', 'Manage Venues'); ?>
<?php $__env->startSection('page-title', 'Manage Venues'); ?>
<?php $__env->startSection('page-description', 'Create and manage lecture venues with geo-fencing'); ?>

<?php $__env->startSection('content'); ?>
<!-- Flash Messages -->
<?php if(session('success')): ?>
<div id="flash-success" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span><?php echo e(session('success')); ?></span>
    <button onclick="closeFlash('flash-success')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div id="flash-error" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span><?php echo e(session('error')); ?></span>
    <button onclick="closeFlash('flash-error')" class="ml-2 text-white hover:text-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manage Venues</h1>
            <p class="text-sm text-gray-500 mt-1">Create and manage lecture venues with geo-fencing</p>
        </div>
        <div class="flex gap-2 mt-4 sm:mt-0">
            <button onclick="openModal()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-full shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add New Venue
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-6" id="stats-cards">
        <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
            <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas fa-map-marker-alt"></i></div>
            <div class="text-3xl font-extrabold mb-1 z-10"><?php echo e($venues->count()); ?></div>
            <div class="text-sm font-semibold uppercase tracking-wider z-10">Total Venues</div>
        </div>
        <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
            <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas fa-check-circle"></i></div>
            <div class="text-3xl font-extrabold mb-1 z-10"><?php echo e($venues->where('is_active', true)->count()); ?></div>
            <div class="text-sm font-semibold uppercase tracking-wider z-10">Active Venues</div>
        </div>
        <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
            <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas fa-building"></i></div>
            <div class="text-3xl font-extrabold mb-1 z-10"><?php echo e($venues->whereNotNull('department_id')->count()); ?></div>
            <div class="text-sm font-semibold uppercase tracking-wider z-10">Department Specific</div>
        </div>
        <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl shadow-lg p-5 flex flex-col items-center justify-center text-white relative overflow-hidden">
            <div class="absolute right-3 top-3 opacity-20 text-5xl"><i class="fas fa-globe"></i></div>
            <div class="text-3xl font-extrabold mb-1 z-10"><?php echo e($venues->whereNull('department_id')->count()); ?></div>
            <div class="text-sm font-semibold uppercase tracking-wider z-10">Global Venues</div>
        </div>
    </div>
    <script>const faScript=document.createElement('script');faScript.src='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js';faScript.crossOrigin='anonymous';document.head.appendChild(faScript);</script>

    <!-- Search Filter -->
    <div class="bg-white rounded-2xl shadow border border-gray-100 p-6 mb-8 flex flex-col md:flex-row md:items-center gap-4">
        <div class="flex-1 flex gap-2">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" id="searchInput" placeholder="Search venues..." class="block w-full pl-12 pr-3 py-3 border border-gray-200 rounded-xl bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
            </div>
        </div>
        <div class="flex gap-2 flex-1">
            <select id="statusFilter" class="block w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>
</div>

<!-- Venues Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-7" id="venuesGrid">
    <?php $__empty_1 = true; $__currentLoopData = $venues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="relative bg-white border-l-4 border-green-500 shadow-xl rounded-2xl p-6 flex flex-col justify-between hover:shadow-2xl transition-shadow duration-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex-shrink-0 w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-gray-900"><?php echo e($venue->name); ?></h3>
                    <p class="text-sm text-gray-600">Radius: <?php echo e($venue->radius); ?>km</p>
                    <div class="flex gap-2 mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-50 text-blue-700">
                            <?php echo e($venue->department ? $venue->department->name : 'Global'); ?>

                        </span>
                    </div>
                </div>
            </div>
            <div class="text-xs text-gray-500 mb-3 font-mono">
                <div>Lat: <?php echo e(number_format($venue->latitude, 6)); ?></div>
                <div>Lng: <?php echo e(number_format($venue->longitude, 6)); ?></div>
            </div>
            <div class="flex items-center justify-between mt-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold <?php echo e($venue->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                        <?php echo e($venue->is_active ? 'Active' : 'Inactive'); ?>

                    </span>
                </div>
                <div class="flex gap-2">
                    <button onclick="editVenue('<?php echo e($venue->id); ?>')" class="bg-gray-200 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-gray-300 transition">Edit</button>
                    <button onclick="deleteVenue('<?php echo e($venue->id); ?>')" class="bg-red-100 text-red-700 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-red-200 transition">Delete</button>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-3 text-center text-gray-400 py-8">No venues found.</div>
    <?php endif; ?>
</div>
</div>

<!-- Add/Edit Venue Modal -->
<div id="venueModal" class="fixed inset-0 bg-gray-700 bg-opacity-40 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    <div class="relative w-full max-w-md mx-auto p-6 border border-gray-200 shadow-2xl rounded-2xl bg-white">
        <div class="mt-2">
            <h3 class="text-xl font-bold text-gray-900 mb-4" id="modalTitle">Add New Venue</h3>
            <form id="venueForm" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="venueId" id="venueId">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Venue Name</label>
                    <input type="text" name="name" id="venueName" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="e.g., Main Lecture Hall" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="number" step="any" name="latitude" id="venueLatitude" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="8.5456" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="number" step="any" name="longitude" id="venueLongitude" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="7.7123" required>
                    </div>
                </div>

                <button type="button" onclick="useCurrentLocation()" class="w-full px-3 py-2 bg-blue-100 text-blue-700 text-sm font-semibold rounded-lg hover:bg-blue-200 transition">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    Use Current Location
                </button>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Radius (km)</label>
                    <input type="number" step="0.01" name="radius" id="venueRadius" value="0.1" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" required>
                    <p class="text-xs text-gray-500 mt-1">Students must be within this distance</p>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="venueActive" checked class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"><span id="modalSubmitText">Add Venue</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
let editingVenueId = null;
let allVenues = <?php echo json_encode($venues, 15, 512) ?>;

function showToast(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));
}

function openModal(editId = null) {
    document.getElementById('venueModal').classList.remove('hidden');
    
    if (editId) {
        editingVenueId = editId;
        const venue = allVenues.find(v => v.id == editId);
        if (venue) {
            document.getElementById('modalTitle').textContent = 'Edit Venue';
            document.getElementById('modalSubmitText').textContent = 'Update Venue';
            document.getElementById('venueId').value = venue.id;
            document.getElementById('venueName').value = venue.name;
            document.getElementById('venueLatitude').value = venue.latitude;
            document.getElementById('venueLongitude').value = venue.longitude;
            document.getElementById('venueRadius').value = venue.radius;
            document.getElementById('venueActive').checked = venue.is_active;
        }
    } else {
        editingVenueId = null;
        document.getElementById('modalTitle').textContent = 'Add New Venue';
        document.getElementById('modalSubmitText').textContent = 'Add Venue';
        document.getElementById('venueForm').reset();
        document.getElementById('venueId').value = '';
    }
}

function closeModal() {
    document.getElementById('venueModal').classList.add('hidden');
}

function editVenue(venueId) {
    openModal(venueId);
}

function deleteVenue(venueId) {
    const venue = allVenues.find(v => v.id == venueId);
    const venueName = venue ? venue.name : 'this venue';
    
    if (confirm(`Are you sure you want to delete "${venueName}"?`)) {
        axios.delete(`/lecturer/venues/${venueId}`, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>' }
        })
        .then(() => {
            showToast('Venue deleted successfully');
            window.location.reload();
        })
        .catch(() => showToast('Failed to delete venue', 'error'));
    }
}

function useCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                document.getElementById('venueLatitude').value = position.coords.latitude.toFixed(6);
                document.getElementById('venueLongitude').value = position.coords.longitude.toFixed(6);
                showToast('Location captured successfully!');
            },
            (error) => {
                showToast('Failed to get location. Please enable location services.', 'error');
            }
        );
    } else {
        showToast('Geolocation is not supported by this browser.', 'error');
    }
}

document.getElementById('venueForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const data = {
        name: document.getElementById('venueName').value,
        latitude: parseFloat(document.getElementById('venueLatitude').value),
        longitude: parseFloat(document.getElementById('venueLongitude').value),
        radius: parseFloat(document.getElementById('venueRadius').value),
        is_active: document.getElementById('venueActive').checked ? 1 : 0,
    };
    
    let url = '/lecturer/venues';
    let method = 'post';
    
    if (editingVenueId) {
        url = `/lecturer/venues/${editingVenueId}`;
        method = 'put';
    }
    
    console.log('Submitting venue:', { editingVenueId, url, method, data });
    
    axios[method](url, data, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>' }
    })
    .then(() => {
        showToast(editingVenueId ? 'Venue updated successfully' : 'Venue created successfully');
        closeModal();
        window.location.reload();
    })
    .catch((error) => {
        console.error('Venue save error:', error);
        const message = error.response?.data?.message || 'Failed to save venue';
        showToast(message, 'error');
    });
});

// Close modal when clicking outside
document.getElementById('venueModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lecturer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\lecturer\venues\index.blade.php ENDPATH**/ ?>