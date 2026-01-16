<?php

$envPath = __DIR__ . '/../.env';
$lines = file($envPath, FILE_IGNORE_NEW_LINES);
$cleanLines = [];

foreach ($lines as $line) {
    if (trim($line) === '') {
        $cleanLines[] = '';
        continue;
    }

    // Remove lines starting with FACEPLUSPLUS
    if (strpos($line, 'FACEPLUSPLUS') === 0) {
        continue;
    }

    // Check for merged lines (e.g., APP_KEY=...=FACEPLUSPLUS...)
    $pos = strpos($line, 'FACEPLUSPLUS');
    if ($pos !== false) {
        // Keep the part before FACEPLUSPLUS
        $line = substr($line, 0, $pos);
    }
    
    if (trim($line) !== '') {
        $cleanLines[] = $line;
    }
}

// Rebuild content
$content = implode(PHP_EOL, $cleanLines);

// Append keys securely
$content .= PHP_EOL;
$content .= 'FACEPLUSPLUS_API_KEY=rX82_Rb07t4IbeSjxZ8p4JLFOhivEAHj' . PHP_EOL;
$content .= 'FACEPLUSPLUS_API_SECRET=5KAGtOvZo4ri9hdZ-OtR_0IP3muQzkn1' . PHP_EOL;

file_put_contents($envPath, $content);
echo "ENV_FIXED";
