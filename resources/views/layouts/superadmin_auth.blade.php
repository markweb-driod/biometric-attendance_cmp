<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Superadmin Login') - NSUK Biometric Attendance</title>
    <meta name="description" content="Superadmin authentication for NSUK Biometric Attendance">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <!-- Local fonts -->
    <link href="/fonts/montserrat/montserrat.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Montserrat', sans-serif; }</style>
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-green-50 via-white to-gray-100 min-h-screen flex flex-col">
    @yield('content')
    @stack('scripts')
</body>
</html>