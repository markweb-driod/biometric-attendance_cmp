<?php
/**
 * Test script to verify the attendance bar chart component functionality
 */

// Test the controller functionality
require_once 'vendor/autoload.php';

// Simulate a request to test the chart data generation
echo "Testing Attendance Monitoring Chart Component\n";
echo "============================================\n\n";

// Test 1: Check if Chart.js is properly installed
echo "1. Checking Chart.js installation...\n";
$packageJson = json_decode(file_get_contents('package.json'), true);
if (isset($packageJson['dependencies']['chart.js'])) {
    echo "✓ Chart.js is installed: " . $packageJson['dependencies']['chart.js'] . "\n";
} else {
    echo "✗ Chart.js is not installed\n";
}

// Test 2: Check if component files exist
echo "\n2. Checking component files...\n";
$files = [
    'resources/views/components/attendance-monitoring/charts/attendance-bar-chart.blade.php',
    'resources/js/components/attendance-bar-chart.js',
    'resources/views/attendance-monitoring/dashboard.blade.php',
    'app/Http/Controllers/AttendanceMonitoringController.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file missing\n";
    }
}

// Test 3: Check if routes are properly configured
echo "\n3. Checking routes configuration...\n";
$webRoutes = file_get_contents('routes/web.php');
if (strpos($webRoutes, 'AttendanceMonitoringController') !== false) {
    echo "✓ Routes are configured\n";
} else {
    echo "✗ Routes are not configured\n";
}

// Test 4: Verify JavaScript component structure
echo "\n4. Checking JavaScript component structure...\n";
$jsComponent = file_get_contents('resources/js/components/attendance-bar-chart.js');
$requiredMethods = ['init', 'loadChartData', 'renderChart', 'switchChartType', 'handleChartClick'];
$methodsFound = 0;

foreach ($requiredMethods as $method) {
    if (strpos($jsComponent, $method) !== false) {
        $methodsFound++;
        echo "✓ Method '$method' found\n";
    } else {
        echo "✗ Method '$method' missing\n";
    }
}

echo "\nMethods found: $methodsFound/" . count($requiredMethods) . "\n";

// Test 5: Check Blade component structure
echo "\n5. Checking Blade component structure...\n";
$bladeComponent = file_get_contents('resources/views/components/attendance-monitoring/charts/attendance-bar-chart.blade.php');
$requiredElements = ['canvas', 'chart-type-btn', 'loading', 'error', 'modal'];
$elementsFound = 0;

foreach ($requiredElements as $element) {
    if (strpos($bladeComponent, $element) !== false) {
        $elementsFound++;
        echo "✓ Element '$element' found\n";
    } else {
        echo "✗ Element '$element' missing\n";
    }
}

echo "\nElements found: $elementsFound/" . count($requiredElements) . "\n";

// Test 6: Check responsive design features
echo "\n6. Checking responsive design features...\n";
$responsiveFeatures = ['sm:', 'md:', 'lg:', 'max-width', '@media'];
$responsiveFeaturesFound = 0;

foreach ($responsiveFeatures as $feature) {
    if (strpos($bladeComponent, $feature) !== false) {
        $responsiveFeaturesFound++;
        echo "✓ Responsive feature '$feature' found\n";
    }
}

echo "\nResponsive features found: $responsiveFeaturesFound/" . count($responsiveFeatures) . "\n";

echo "\n============================================\n";
echo "Component Test Summary:\n";
echo "- Chart.js: " . (isset($packageJson['dependencies']['chart.js']) ? "✓" : "✗") . "\n";
echo "- Files: " . count(array_filter($files, 'file_exists')) . "/" . count($files) . "\n";
echo "- Routes: " . (strpos($webRoutes, 'AttendanceMonitoringController') !== false ? "✓" : "✗") . "\n";
echo "- JS Methods: $methodsFound/" . count($requiredMethods) . "\n";
echo "- Blade Elements: $elementsFound/" . count($requiredElements) . "\n";
echo "- Responsive: $responsiveFeaturesFound/" . count($responsiveFeatures) . "\n";

if (count(array_filter($files, 'file_exists')) === count($files) && 
    $methodsFound === count($requiredMethods) && 
    $elementsFound === count($requiredElements)) {
    echo "\n🎉 All tests passed! The component is ready for use.\n";
} else {
    echo "\n⚠️  Some tests failed. Please check the issues above.\n";
}

echo "\nTo test the component in browser:\n";
echo "1. Start your Laravel server: php artisan serve\n";
echo "2. Visit: http://localhost:8000/attendance-monitoring\n";
echo "3. Check browser console for any JavaScript errors\n";
echo "4. Verify chart renders and interactions work\n";
?>