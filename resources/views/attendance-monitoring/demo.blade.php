@extends('layouts.app')

@section('title', 'Attendance Chart Demo')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Demo Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Interactive Bar Chart Component Demo</h1>
            <p class="mt-2 text-gray-600">Demonstrating daily/weekly attendance comparison with hover tooltips and drill-down functionality</p>
            
            <!-- Feature List -->
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">Component Features:</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>✓ Interactive bar charts with Chart.js integration</li>
                    <li>✓ Daily/Weekly attendance comparison toggle</li>
                    <li>✓ Hover tooltips with detailed information</li>
                    <li>✓ Click drill-down functionality with modal details</li>
                    <li>✓ Responsive design for mobile devices</li>
                    <li>✓ Loading states and error handling</li>
                    <li>✓ Summary statistics and trend indicators</li>
                </ul>
            </div>
        </div>

        <!-- Demo Charts Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- Primary Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <x-attendance-monitoring.charts.attendance-bar-chart 
                    :chart-id="'demo-chart-1'"
                    :title="'Primary Attendance Chart'"
                    :height="400"
                    :chart-type="'daily'"
                />
            </div>

            <!-- Secondary Chart (Weekly) -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <x-attendance-monitoring.charts.attendance-bar-chart 
                    :chart-id="'demo-chart-2'"
                    :title="'Weekly Attendance Overview'"
                    :height="400"
                    :chart-type="'weekly'"
                />
            </div>

            <!-- Compact Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <x-attendance-monitoring.charts.attendance-bar-chart 
                    :chart-id="'demo-chart-3'"
                    :title="'Compact View'"
                    :height="300"
                    :show-filters="false"
                    :chart-type="'daily'"
                />
            </div>

            <!-- Mobile Optimized Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <x-attendance-monitoring.charts.attendance-bar-chart 
                    :chart-id="'demo-chart-4'"
                    :title="'Mobile Optimized'"
                    :height="300"
                    :chart-type="'weekly'"
                />
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">How to Test the Component</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Interactive Features:</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• <strong>Hover:</strong> Move mouse over bars to see detailed tooltips</li>
                        <li>• <strong>Click:</strong> Click on any bar to open drill-down modal</li>
                        <li>• <strong>Toggle:</strong> Use Daily/Weekly buttons to switch views</li>
                        <li>• <strong>Responsive:</strong> Resize window to test mobile layout</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Technical Details:</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• <strong>Chart.js:</strong> v4.4.0 with full feature set</li>
                        <li>• <strong>API:</strong> /api/attendance-monitoring/chart-data</li>
                        <li>• <strong>Responsive:</strong> Tailwind CSS breakpoints</li>
                        <li>• <strong>Accessibility:</strong> Keyboard navigation support</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- API Test Section -->
        <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">API Testing</h3>
            <div class="flex flex-wrap gap-4">
                <button 
                    onclick="testAPI('daily')" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200"
                >
                    Test Daily API
                </button>
                <button 
                    onclick="testAPI('weekly')" 
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200"
                >
                    Test Weekly API
                </button>
                <button 
                    onclick="refreshAllCharts()" 
                    class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-200"
                >
                    Refresh All Charts
                </button>
            </div>
            <div id="api-results" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                <pre id="api-output" class="text-sm text-gray-800 whitespace-pre-wrap"></pre>
            </div>
        </div>
    </div>
</div>

<script>
    // API Testing Functions
    async function testAPI(type) {
        const resultsDiv = document.getElementById('api-results');
        const outputPre = document.getElementById('api-output');
        
        resultsDiv.classList.remove('hidden');
        outputPre.textContent = 'Loading...';
        
        try {
            const response = await fetch(`/api/attendance-monitoring/chart-data?type=${type}&chart=bar`);
            const data = await response.json();
            
            outputPre.textContent = `API Response (${type}):\n` + JSON.stringify(data, null, 2);
        } catch (error) {
            outputPre.textContent = `Error: ${error.message}`;
        }
    }
    
    function refreshAllCharts() {
        if (window.attendanceCharts) {
            Object.values(window.attendanceCharts).forEach(chart => {
                if (chart && typeof chart.refresh === 'function') {
                    chart.refresh();
                }
            });
            
            const outputPre = document.getElementById('api-output');
            const resultsDiv = document.getElementById('api-results');
            resultsDiv.classList.remove('hidden');
            outputPre.textContent = 'All charts refreshed successfully!';
        }
    }
    
    // Log chart initialization for debugging
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Demo page loaded. Available charts:', Object.keys(window.attendanceCharts || {}));
    });
</script>
@endsection