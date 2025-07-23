<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Superadmin Login') - NSUK Biometric Attendance</title>
    <meta name="description" content="Superadmin authentication for NSUK Biometric Attendance">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Montserrat', sans-serif; }</style>
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-green-50 via-white to-gray-100 min-h-screen flex flex-col">
    @yield('content')
    @stack('scripts')
</body>
</html> 