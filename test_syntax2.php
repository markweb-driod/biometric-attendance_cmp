<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate paginator
class MockPaginator {
    public function currentPage() { return 1; }
    public function lastPage() { return 10; }
    public function firstItem() { return 1; }
    public function lastItem() { return 15; }
    public function total() { return 150; }
    public function previousPageUrl() { return null; }
    public function nextPageUrl() { return '/page/2'; }
}

$auditLogs = new MockPaginator();

// Test without @json first
try {
    $result = ($auditLogs && method_exists($auditLogs, 'currentPage')) ? [
        'current_page' => $auditLogs->currentPage(),
        'last_page' => $auditLogs->lastPage(),
        'from' => $auditLogs->firstItem(),
        'to' => $auditLogs->lastItem(),
        'total' => $auditLogs->total(),
        'prev_page_url' => $auditLogs->previousPageUrl(),
        'next_page_url' => $auditLogs->nextPageUrl()
    ] : [
        'current_page' => 1,
        'last_page' => 1,
        'from' => 1,
        'to' => 1,
        'total' => 0,
        'prev_page_url' => null,
        'next_page_url' => null
    ];
    
    echo "✅ Array syntax is valid!\n";
    print_r($result);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Now test with json_encode
try {
    $json = json_encode($result);
    echo "\n✅ JSON encoding works!\n";
    echo $json . "\n";
} catch (Exception $e) {
    echo "❌ JSON Error: " . $e->getMessage() . "\n";
}

