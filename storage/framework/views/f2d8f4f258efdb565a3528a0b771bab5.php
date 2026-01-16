<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($chartTitle); ?> - Full Screen</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(to bottom, #f0fdf4, #dcfce7);
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-l-4 border-green-500">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-green-800"><?php echo e($chartTitle); ?></h1>
                    <p class="text-sm text-gray-600 mt-1">Full Screen View - Filter and analyze your data</p>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <button onclick="window.close()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Close
                    </button>
                    <button onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Filters</h3>
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="academic_level" class="block text-sm font-medium text-gray-700 mb-1">Academic Level</label>
                    <select id="academic_level" name="academic_level" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Levels</option>
                        <?php $__currentLoopData = $filterOptions['academic_levels'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e($filters['academic_level'] == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <select id="semester" name="semester" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Semesters</option>
                        <?php $__currentLoopData = $filterOptions['semesters'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e($filters['semester'] == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                    <select id="academic_year" name="academic_year" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Years</option>
                        <?php $__currentLoopData = $filterOptions['academic_years'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e($filters['academic_year'] == $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium">
                        Apply Filters
                    </button>
                    <button type="button" id="clearFilters" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Clear
                    </button>
                </div>
            </form>
        </div>

        <!-- Chart Container -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800"><?php echo e($chartTitle); ?></h2>
                <button id="toggleChartType" class="px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition text-sm font-medium">
                    Switch to Bar View
                </button>
            </div>
            <div class="relative" style="height: calc(100vh - 400px); min-height: 500px;">
                <canvas id="fullscreenChartCanvas" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Initialize chart
        let chartInstance = null;
        const chartConfig = <?php echo json_encode($chartData); ?>;
        
        // Update chart type config
        if (chartConfig.options) {
            chartConfig.options.maintainAspectRatio = true;
        }
        
        const canvas = document.getElementById('fullscreenChartCanvas');
        chartInstance = new Chart(canvas, chartConfig);
        
        // Toggle chart type
        document.getElementById('toggleChartType').addEventListener('click', function() {
            const currentType = chartInstance.config.type;
            const newType = currentType === 'line' ? 'bar' : 'line';
            
            chartInstance.config.type = newType;
            chartInstance.update();
            
            this.textContent = newType === 'line' ? 'Switch to Bar View' : 'Switch to Line View';
        });
        
        // Filter form handling
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams(formData);
            window.location.href = window.location.pathname + '?' + params.toString();
        });
        
        // Clear filters
        document.getElementById('clearFilters').addEventListener('click', function() {
            document.getElementById('filterForm').reset();
            window.location.href = window.location.pathname;
        });
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\hod\monitoring\chart-fullscreen.blade.php ENDPATH**/ ?>