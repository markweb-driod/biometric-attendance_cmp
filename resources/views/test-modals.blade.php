<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Test Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @include('components.modals')
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Modal System Test</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Alert Modals -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Alert Modals</h2>
                <div class="space-y-3">
                    <button onclick="showSuccessModal('Operation completed successfully!')" 
                            class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Success Alert
                    </button>
                    <button onclick="showErrorModal('An error occurred while processing your request.')" 
                            class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Error Alert
                    </button>
                    <button onclick="showWarningModal('This action may have unintended consequences.')" 
                            class="w-full bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                        Warning Alert
                    </button>
                    <button onclick="showInfoModal('Here is some important information for you.')" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Info Alert
                    </button>
                </div>
            </div>

            <!-- Confirmation Modals -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Confirmation Modals</h2>
                <div class="space-y-3">
                    <button onclick="confirmAction('Are you sure you want to proceed with this action?')" 
                            class="w-full bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                        Confirm Action
                    </button>
                    <button onclick="confirmDelete('Are you sure you want to delete this item? This action cannot be undone.')" 
                            class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Confirm Delete
                    </button>
                    <button onclick="confirm('Do you want to save your changes before leaving?')" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Generic Confirm
                    </button>
                </div>
            </div>

            <!-- Custom Modals -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Custom Modals</h2>
                <div class="space-y-3">
                    <button onclick="showModal('confirmModal', {
                        title: 'Custom Title',
                        message: 'This is a custom modal with different styling.',
                        type: 'info',
                        confirmText: 'Custom OK',
                        cancelText: 'Custom Cancel'
                    })" 
                            class="w-full bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                        Custom Modal
                    </button>
                    <button onclick="alert('This is using the enhanced alert function!')" 
                            class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Enhanced Alert
                    </button>
                    <button onclick="confirm('This is using the enhanced confirm function!')" 
                            class="w-full bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">
                        Enhanced Confirm
                    </button>
                </div>
            </div>
        </div>

        <!-- Test Results -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Test Results</h2>
            <div id="test-results" class="space-y-2">
                <p class="text-gray-600">Click the buttons above to test the modal functionality.</p>
            </div>
        </div>
    </div>

    <script>
        // Override the modal functions to show results
        const originalConfirm = window.confirm;
        const originalAlert = window.alert;
        
        window.confirm = function(message, options = {}) {
            addResult(`Confirm called: ${message}`);
            return originalConfirm(message, options);
        };
        
        window.alert = function(message, options = {}) {
            addResult(`Alert called: ${message}`);
            return originalAlert(message, options);
        };
        
        function addResult(text) {
            const results = document.getElementById('test-results');
            const p = document.createElement('p');
            p.className = 'text-sm text-green-600 bg-green-50 p-2 rounded';
            p.textContent = new Date().toLocaleTimeString() + ': ' + text;
            results.appendChild(p);
            results.scrollTop = results.scrollHeight;
        }
    </script>
</body>
</html>
