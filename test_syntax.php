<?php

// Test the syntax
$auditLogs = null;

$test = @json(($auditLogs && method_exists($auditLogs, 'currentPage')) ? ['current_page' => $auditLogs->currentPage(), 'last_page' => $auditLogs->lastPage(), 'from' => $auditLogs->firstItem(), 'to' => $auditLogs->lastItem(), 'total' => $auditLogs->total(), 'prev_page_url' => $auditLogs->previousPageUrl(), 'next_page_url' => $auditLogs->nextPageUrl()] : ['current_page' => 1, 'last_page' => 1, 'from' => 1, 'to' => 1, 'total' => 0, 'prev_page_url' => null, 'next_page_url' => null]);

echo "Syntax is valid!\n";

