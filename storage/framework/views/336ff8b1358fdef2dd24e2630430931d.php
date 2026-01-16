<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'chartId' => 'attendance-bar-chart',
    'title' => 'Attendance Chart',
    'height' => 400,
    'showFilters' => true,
    'chartType' => 'daily' // daily or weekly
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'chartId' => 'attendance-bar-chart',
    'title' => 'Attendance Chart',
    'height' => 400,
    'showFilters' => true,
    'chartType' => 'daily' // daily or weekly
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="attendance-bar-chart-container">
    <!-- Chart Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2 sm:mb-0"><?php echo e($title); ?></h3>
        
        <?php if($showFilters): ?>
        <!-- Chart Type Toggle -->
        <div class="flex items-center space-x-2">
            <div class="bg-gray-100 rounded-lg p-1 flex">
                <button 
                    type="button" 
                    class="chart-type-btn px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200 active"
                    data-chart-type="daily"
                    data-chart-id="<?php echo e($chartId); ?>"
                >
                    Daily
                </button>
                <button 
                    type="button" 
                    class="chart-type-btn px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200"
                    data-chart-type="weekly"
                    data-chart-id="<?php echo e($chartId); ?>"
                >
                    Weekly
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Chart Loading State -->
    <div id="<?php echo e($chartId); ?>-loading" class="flex items-center justify-center bg-gray-50 rounded-lg" style="height: <?php echo e($height); ?>px;">
        <div class="text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
            <p class="text-gray-500 text-sm">Loading chart data...</p>
        </div>
    </div>

    <!-- Chart Error State -->
    <div id="<?php echo e($chartId); ?>-error" class="hidden flex items-center justify-center bg-red-50 rounded-lg border border-red-200" style="height: <?php echo e($height); ?>px;">
        <div class="text-center">
            <div class="text-red-500 mb-2">
                <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-red-700 font-medium mb-2">Unable to load chart data</p>
            <button 
                onclick="retryChartLoad('<?php echo e($chartId); ?>')" 
                class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition-colors duration-200"
            >
                Retry
            </button>
        </div>
    </div>

    <!-- Chart Container -->
    <div id="<?php echo e($chartId); ?>-container" class="hidden">
        <canvas 
            id="<?php echo e($chartId); ?>" 
            class="w-full"
            style="height: <?php echo e($height); ?>px;"
            data-chart-type="<?php echo e($chartType); ?>"
        ></canvas>
    </div>

    <!-- Chart Legend and Info -->
    <div id="<?php echo e($chartId); ?>-info" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div class="text-center">
                <div class="font-semibold text-gray-900" id="<?php echo e($chartId); ?>-total-sessions">-</div>
                <div class="text-gray-600">Total Sessions</div>
            </div>
            <div class="text-center">
                <div class="font-semibold text-green-600" id="<?php echo e($chartId); ?>-avg-attendance">-</div>
                <div class="text-gray-600">Average Attendance</div>
            </div>
            <div class="text-center">
                <div class="font-semibold text-blue-600" id="<?php echo e($chartId); ?>-trend">-</div>
                <div class="text-gray-600">Trend</div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Drill-down Modal -->
<div id="<?php echo e($chartId); ?>-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900" id="<?php echo e($chartId); ?>-modal-title">Detailed View</h3>
                    <button 
                        onclick="closeChartModal('<?php echo e($chartId); ?>')"
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="<?php echo e($chartId); ?>-modal-content" class="space-y-4">
                    <!-- Modal content will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize chart when AttendanceBarChart class is available
        function initChart() {
            if (typeof AttendanceBarChart !== 'undefined') {
                const chartConfig = {
                    chartId: '<?php echo e($chartId); ?>',
                    initialType: '<?php echo e($chartType); ?>',
                    height: <?php echo e($height); ?>,
                    apiEndpoint: '/api/attendance-monitoring/chart-data',
                    enableDrillDown: true,
                    responsive: true
                };
                
                window.attendanceCharts = window.attendanceCharts || {};
                window.attendanceCharts['<?php echo e($chartId); ?>'] = new AttendanceBarChart(chartConfig);
            } else {
                // Retry after a short delay if class not yet loaded
                setTimeout(initChart, 100);
            }
        }
        
        initChart();
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .chart-type-btn.active {
        @apply bg-white text-blue-600 shadow-sm;
    }
    
    .chart-type-btn:not(.active) {
        @apply text-gray-600 hover:text-gray-900;
    }
    
    .attendance-bar-chart-container canvas {
        cursor: pointer;
    }
    
    @media (max-width: 640px) {
        .attendance-bar-chart-container {
            font-size: 0.875rem;
        }
        
        .chart-type-btn {
            @apply px-2 py-1 text-xs;
        }
    }
</style>
<?php $__env->stopPush(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\components\attendance-monitoring\charts\attendance-bar-chart.blade.php ENDPATH**/ ?>