@extends('layouts.superadmin')

@section('title', 'Client API Documentation')
@section('page-title', 'Client API Documentation')

@section('content')
<div class="w-full px-2 py-10 space-y-6">
    <!-- Header with Download Option -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 text-white">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold mb-2">Exam Eligibility API</h1>
                <p class="text-green-100">Complete integration guide for client applications</p>
            </div>
            <button onclick="downloadDocumentation()" class="bg-white text-green-700 px-6 py-3 rounded-lg font-semibold hover:bg-green-50 transition shadow-lg">
                📥 Download PDF
            </button>
        </div>
    </div>

    <!-- Quick Start Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-base mr-3">1</span>
            Quick Start
        </h2>
        <div class="space-y-4">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                <h4 class="font-semibold text-blue-900 mb-2">🔑 Getting Your API Key</h4>
                <ol class="list-decimal ml-5 space-y-1 text-sm text-gray-700">
                    <li>Contact the system administrator to request an API key</li>
                    <li>You'll receive a unique API Key and Secret</li>
                    <li>Store these securely - they cannot be retrieved again</li>
                </ol>
            </div>
            <div class="bg-green-50 border-l-4 border-green-500 p-4">
                <h4 class="font-semibold text-green-900 mb-2">🚀 Your First Request</h4>
                <pre class="bg-gray-800 text-green-400 p-4 rounded-lg overflow-x-auto text-sm"><code>curl -X GET "{{ url('api/v1/eligibility/student/STU001') }}" \
  -H "X-API-Key: your_api_key_here"</code></pre>
            </div>
        </div>
    </div>

    <!-- Authentication Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-base mr-3">2</span>
            Authentication
        </h2>
        <p class="text-gray-700 mb-4">Every API request requires your API key for authentication. Include it in one of two ways:</p>
        
        <div class="grid md:grid-cols-2 gap-4 mb-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold mb-2 flex items-center">
                    <span class="text-blue-600 mr-2">📋</span>Header Method (Recommended)
                </h4>
                <pre class="bg-gray-50 p-3 rounded text-sm overflow-x-auto"><code>X-API-Key: your_api_key_here</code></pre>
                <p class="text-xs text-gray-600 mt-2">Most secure method</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold mb-2 flex items-center">
                    <span class="text-purple-600 mr-2">🔗</span>Query Parameter
                </h4>
                <pre class="bg-gray-50 p-3 rounded text-sm overflow-x-auto"><code>?api_key=your_api_key_here</code></pre>
                <p class="text-xs text-gray-600 mt-2">Alternative method</p>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-900">
                <strong>⚠️ Security Tip:</strong> Never expose your API key in client-side JavaScript or public repositories. 
                Always use server-side requests or environment variables.
            </p>
        </div>
    </div>

    <!-- Base URL Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-base mr-3">3</span>
            Base URL & Versioning
        </h2>
        <p class="text-gray-700 mb-4">All API endpoints are prefixed with the base URL and version:</p>
        <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm">
            {{ url('api/v1/') }}
        </div>
        <p class="text-sm text-gray-600 mt-2">
            Current API version: <span class="font-semibold">v1</span> | Status: <span class="text-green-600 font-semibold">● Active</span>
        </p>
    </div>

    <!-- API Endpoints Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-base mr-3">4</span>
            API Endpoints
        </h2>
        
        <div class="space-y-6">
            <!-- Health Check -->
            <div class="border border-gray-200 rounded-lg p-5 hover:border-green-400 transition">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">GET</span>
                        <h3 class="inline-block ml-3 font-bold text-lg">Health Check</h3>
                    </div>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">No Auth Required</span>
                </div>
                <p class="text-gray-600 mb-3">Verify API status and current academic term</p>
                <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm overflow-x-auto mb-3"><code>{{ url('api/v1/eligibility/health') }}</code></pre>
                <details class="mt-3">
                    <summary class="cursor-pointer text-sm font-semibold text-blue-600 hover:text-blue-800">View Response Example</summary>
                    <pre class="bg-gray-50 p-3 rounded mt-2 text-sm overflow-x-auto"><code>{
  "status": "healthy",
  "timestamp": "2024-12-10T10:30:00Z",
  "service": "eligibility-api",
  "version": "1.0.0",
  "current_semester": "First",
  "current_academic_year": "2024/2025"
}</code></pre>
                </details>
            </div>

            <!-- Check Single Student -->
            <div class="border border-gray-200 rounded-lg p-5 hover:border-green-400 transition">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">GET</span>
                        <h3 class="inline-block ml-3 font-bold text-lg">Check Student Eligibility</h3>
                    </div>
                    <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">Auth Required</span>
                </div>
                <p class="text-gray-600 mb-3">Get eligibility for a student (current semester)</p>
                <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm overflow-x-auto mb-2"><code>{{ url('api/v1/eligibility/student/{matricNumber}') }}</code></pre>
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Query: detailed (boolean)</span>
                </div>
                <details class="mt-3">
                    <summary class="cursor-pointer text-sm font-semibold text-blue-600 hover:text-blue-800">View Response Example</summary>
                    <pre class="bg-gray-50 p-3 rounded mt-2 text-sm overflow-x-auto"><code>{
  "success": true,
  "eligible": true,
  "student": "STU001",
  "student_name": "John Doe",
  "department": "Computer Science",
  "semester": "First",
  "academic_year": "2024/2025",
  "courses": [
    {
      "course_code": "CSC101",
      "course_name": "Introduction to Programming",
      "is_eligible": true,
      "attendance_rate": 85,
      "required_rate": 75
    }
  ]
}</code></pre>
                </details>
                <details class="mt-2">
                    <summary class="cursor-pointer text-sm font-semibold text-green-600 hover:text-green-800">Code Examples</summary>
                    <div class="mt-2 space-y-2">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-1">JavaScript (Fetch):</p>
                            <pre class="bg-gray-900 text-green-400 p-2 rounded text-xs overflow-x-auto"><code>fetch('{{ url('api/v1/eligibility/student/STU001') }}', {
  headers: { 'X-API-Key': 'your_api_key' }
})
.then(res => res.json())
.then(data => console.log(data));</code></pre>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-1">PHP:</p>
                            <pre class="bg-gray-900 text-green-400 p-2 rounded text-xs overflow-x-auto"><code>$response = file_get_contents('{{ url('api/v1/eligibility/student/STU001') }}', 
  false, stream_context_create([
  'http' => [
    'method' => 'GET',
    'header' => 'X-API-Key: your_api_key'
  ]
]));</code></pre>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-1">Python:</p>
                            <pre class="bg-gray-900 text-green-400 p-2 rounded text-xs overflow-x-auto"><code>import requests
response = requests.get(
  '{{ url('api/v1/eligibility/student/STU001') }}',
  headers={'X-API-Key': 'your_api_key'}
)
print(response.json())</code></pre>
                        </div>
                    </div>
                </details>
            </div>

            <!-- Check Specific Course -->
            <div class="border border-gray-200 rounded-lg p-5 hover:border-green-400 transition">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">GET</span>
                        <h3 class="inline-block ml-3 font-bold text-lg">Check Course Eligibility</h3>
                    </div>
                    <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">Auth Required</span>
                </div>
                <p class="text-gray-600 mb-3">Get eligibility for a student's specific course</p>
                <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm overflow-x-auto mb-2"><code>{{ url('api/v1/eligibility/student/{matricNumber}/course/{courseId}') }}</code></pre>
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Query: detailed</span>
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Query: semester</span>
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Query: academic_year</span>
                </div>
                <details class="mt-3">
                    <summary class="cursor-pointer text-sm font-semibold text-blue-600 hover:text-blue-800">View Response Example</summary>
                    <pre class="bg-gray-50 p-3 rounded mt-2 text-sm overflow-x-auto"><code>{
  "success": true,
  "course": {
    "code": "CSC101",
    "name": "Introduction to Programming",
    "is_eligible": true,
    "attendance_rate": 85,
    "total_sessions": 20,
    "attended_sessions": 17,
    "threshold": 75
  }
}</code></pre>
                </details>
            </div>

            <!-- Get All Courses -->
            <div class="border border-gray-200 rounded-lg p-5 hover:border-green-400 transition">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">GET</span>
                        <h3 class="inline-block ml-3 font-bold text-lg">Get All Student Courses</h3>
                    </div>
                    <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">Auth Required</span>
                </div>
                <p class="text-gray-600 mb-3">Get eligibility for all courses of a student</p>
                <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm overflow-x-auto mb-2"><code>{{ url('api/v1/eligibility/student/{matricNumber}/courses') }}</code></pre>
            </div>

            <!-- Bulk Check -->
            <div class="border border-gray-200 rounded-lg p-5 hover:border-green-400 transition">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-semibold">POST</span>
                        <h3 class="inline-block ml-3 font-bold text-lg">Bulk Eligibility Check</h3>
                    </div>
                    <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">Auth Required</span>
                </div>
                <p class="text-gray-600 mb-3">Check eligibility for multiple students at once (max 100)</p>
                <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm overflow-x-auto mb-2"><code>{{ url('api/v1/eligibility/bulk') }}</code></pre>
                <details class="mt-3">
                    <summary class="cursor-pointer text-sm font-semibold text-blue-600 hover:text-blue-800">View Request Example</summary>
                    <pre class="bg-gray-50 p-3 rounded mt-2 text-sm overflow-x-auto"><code>POST {{ url('api/v1/eligibility/bulk') }}
Content-Type: application/json

{
  "matric_numbers": ["STU001", "STU002", "STU003"],
  "semester": "First",
  "academic_year": "2024/2025",
  "detailed": false
}</code></pre>
                </details>
            </div>

            <!-- Check with Semester -->
            <div class="border border-gray-200 rounded-lg p-5 hover:border-green-400 transition">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">GET</span>
                        <h3 class="inline-block ml-3 font-bold text-lg">Check with Specific Semester</h3>
                    </div>
                    <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">Auth Required</span>
                </div>
                <p class="text-gray-600 mb-3">Get eligibility for a specific semester/academic year</p>
                <pre class="bg-gray-900 text-green-400 p-3 rounded text-sm overflow-x-auto mb-2"><code>{{ url('api/v1/eligibility/student/{matricNumber}/semester/{semester}/academic-year/{academicYear}') }}</code></pre>
            </div>
        </div>
    </div>

    <!-- Rate Limiting Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-base mr-3">5</span>
            Rate Limiting
        </h2>
        <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                <div class="text-3xl font-bold text-blue-700 mb-1">60</div>
                <div class="text-sm text-blue-900 font-semibold">Requests per Minute</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200">
                <div class="text-3xl font-bold text-green-700 mb-1">1,000</div>
                <div class="text-sm text-green-900 font-semibold">Requests per Hour</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                <div class="text-3xl font-bold text-purple-700 mb-1">∞</div>
                <div class="text-sm text-purple-900 font-semibold">Daily Limit</div>
            </div>
        </div>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <p class="text-sm text-red-900">
                <strong>⚠️ Rate Limit Exceeded Response:</strong>
            </p>
            <pre class="bg-red-900 text-red-100 p-3 rounded mt-2 text-sm overflow-x-auto"><code>HTTP 429 Too Many Requests
{
  "success": false,
  "error": "rate_limit_exceeded",
  "message": "Rate limit exceeded. Please try again later.",
  "retry_after": 45,
  "current_usage": {
    "minute": 60,
    "hour": 245
  }
}</code></pre>
        </div>
    </div>

    <!-- Error Handling Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-base mr-3">6</span>
            Error Handling
        </h2>
        <div class="space-y-4">
            <div class="border border-red-200 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <span class="bg-red-600 text-white px-2 py-1 rounded text-xs font-bold mr-2">401</span>
                    <span class="font-semibold text-red-900">Unauthorized</span>
                </div>
                <p class="text-sm text-gray-700 mb-2">Missing, invalid, or inactive API key</p>
                <pre class="bg-red-900 text-red-100 p-2 rounded text-xs overflow-x-auto"><code>{
  "success": false,
  "error": "invalid_api_key",
  "message": "Invalid API key provided."
}</code></pre>
            </div>
            <div class="border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <span class="bg-yellow-600 text-white px-2 py-1 rounded text-xs font-bold mr-2">404</span>
                    <span class="font-semibold text-yellow-900">Not Found</span>
                </div>
                <p class="text-sm text-gray-700 mb-2">Student or course not found</p>
                <pre class="bg-yellow-900 text-yellow-100 p-2 rounded text-xs overflow-x-auto"><code>{
  "success": false,
  "error": "student_not_found",
  "message": "Student not found or inactive."
}</code></pre>
            </div>
            <div class="border border-red-200 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <span class="bg-red-700 text-white px-2 py-1 rounded text-xs font-bold mr-2">500</span>
                    <span class="font-semibold text-red-900">Server Error</span>
                </div>
                <p class="text-sm text-gray-700 mb-2">Internal server error</p>
                <pre class="bg-red-900 text-red-100 p-2 rounded text-xs overflow-x-auto"><code>{
  "success": false,
  "error": "server_error",
  "message": "An error occurred while processing your request."
}</code></pre>
            </div>
        </div>
    </div>

    <!-- Best Practices Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-green-200 p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-base mr-3">7</span>
            Best Practices
        </h2>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                <h4 class="font-semibold text-green-900 mb-2">✅ Do</h4>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span>Store API keys in environment variables</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span>Implement retry logic for rate limits</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span>Cache responses to reduce API calls</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span>Handle all error responses gracefully</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span>Use detailed=true sparingly for performance</span>
                    </li>
                </ul>
            </div>
            <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                <h4 class="font-semibold text-red-900 mb-2">❌ Don't</h4>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start">
                        <span class="text-red-600 mr-2">✗</span>
                        <span>Expose API keys in client-side code</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-red-600 mr-2">✗</span>
                        <span>Make unnecessary repeated requests</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-red-600 mr-2">✗</span>
                        <span>Ignore rate limit headers</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-red-600 mr-2">✗</span>
                        <span>Commit API keys to version control</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-red-600 mr-2">✗</span>
                        <span>Make blocking requests in main thread</span>
                    </li>
                </ul>
        </div>
    </div>
</div>

    <!-- Support Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-xl p-6 text-white">
        <h2 class="text-2xl font-bold mb-4 flex items-center">
            <span class="text-3xl mr-3">💬</span>
            Need Help?
        </h2>
        <p class="mb-4 text-blue-100">If you encounter issues or need assistance integrating our API:</p>
        <ul class="space-y-2 text-blue-100">
            <li>📧 Email: technical-support@university.edu</li>
            <li>📞 Phone: +1 (555) 123-4567</li>
            <li>⏰ Support Hours: Monday-Friday, 9:00 AM - 5:00 PM</li>
        </ul>
    </div>
</div>

<script>
function downloadDocumentation() {
    // Create a downloadable version of the documentation
    const content = document.querySelector('.w-full').innerHTML;
    const blob = new Blob(['<!DOCTYPE html><html><head><meta charset="UTF-8"><title>API Documentation</title><style>body{font-family:sans-serif;padding:20px}pre{background:#1e293b;color:#84cc16;padding:15px;border-radius:8px;overflow-x:auto}.bg-gray-900{background:#1e293b}.text-green-400{color:#84cc16}</style></head><body>' + content + '</body></html>'], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'api-documentation.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endsection

