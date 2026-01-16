<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - NSUK Biometric Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="/fonts/montserrat/montserrat.css" rel="stylesheet">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 via-white to-green-100 min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="w-full bg-white/80 backdrop-blur-md shadow-lg border-b border-green-200 px-2 sm:px-8 py-2 sm:py-4 rounded-b-2xl z-30" x-data="{ open: false }">
        <div class="flex flex-row items-center justify-between w-full">
            <div class="flex items-center flex-shrink-0">
                <a href="/" class="flex items-center gap-2 sm:gap-4 group">
                    <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK Logo" class="h-8 w-8 sm:h-12 sm:w-12 object-contain rounded-full shadow mr-2 sm:mr-4 group-hover:scale-105 transition-transform duration-300">
                    <div class="flex flex-col">
                        <span class="text-base sm:text-xl font-extrabold text-green-700 leading-tight tracking-tight">Department of Computer Science</span>
                        <span class="text-[10px] sm:text-sm text-gray-600 -mt-1 font-medium">Nasarawa State University, Keffi</span>
                    </div>
                </a>
            </div>
            <!-- Hamburger for mobile -->
            <button @click="open = !open" class="sm:hidden ml-auto p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-300" aria-label="Open Menu">
                <svg x-show="!open" class="w-7 h-7 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                <svg x-show="open" class="w-7 h-7 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <!-- Nav links -->
            <div :class="{'block': open, 'hidden': !open}" class="absolute left-2 right-2 top-14 sm:static sm:flex flex-col sm:flex-row items-center gap-2 sm:gap-10 bg-white/95 sm:bg-transparent shadow-lg sm:shadow-none rounded-xl sm:rounded-none py-2 sm:py-0 mt-2 sm:mt-0 text-center text-base sm:text-lg font-semibold text-gray-800 transition-all duration-200 z-40" x-cloak>
                <a href="/" class="px-3 py-2 text-green-700 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200">Home</a>
                <a href="mailto:cs@nsuk.edu.ng" class="px-3 py-2 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200">Contact</a>
            </div>
        </div>
    </nav>

           <!-- Main Content -->
           <main class="flex-1 flex min-h-screen">
               <!-- Left Side - Login Details (Hidden on mobile, shown on desktop) -->
               <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-green-600 via-green-700 to-green-800 relative overflow-hidden">
                   <!-- Background Pattern -->
                   <div class="absolute inset-0 opacity-10">
                       <div class="absolute top-20 left-20 w-32 h-32 bg-white rounded-full animate-blob"></div>
                       <div class="absolute bottom-20 right-20 w-24 h-24 bg-white rounded-full animate-blob animation-delay-2000"></div>
                       <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-40 h-40 bg-white rounded-full animate-blob animation-delay-4000"></div>
                   </div>
                   
                   <!-- Content -->
                   <div class="relative z-10 flex flex-col justify-center px-12 py-16">
                       <div class="mb-6">
                           <div class="flex justify-center mb-4">
                               <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                                   <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK Logo" class="h-10 w-10 object-contain">
                               </div>
                           </div>
                           <h1 class="text-2xl font-bold text-white mb-3 text-center" style="font-family: 'Montserrat', sans-serif;">
                               NSUK Biometric Attendance System
                           </h1>
                           <p class="text-base text-green-100 leading-relaxed text-center">
                               Secure, efficient, and modern attendance management for the Department of Computer Science.
                           </p>
                       </div>

                       <!-- Features List -->
                       <div class="space-y-3 mb-6">
                           <div class="flex items-center text-green-100">
                               <svg class="w-5 h-5 mr-3 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                               </svg>
                               <span class="text-sm">Facial Recognition Technology</span>
                           </div>
                           <div class="flex items-center text-green-100">
                               <svg class="w-5 h-5 mr-3 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                               </svg>
                               <span class="text-sm">Real-time Attendance Tracking</span>
                           </div>
                       </div>

                       <!-- Login Instructions (Demo Credentials) -->
                       <div class="bg-white bg-opacity-15 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
                           <div class="flex items-center mb-4">
                               <svg class="w-5 h-5 text-green-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0117 8z"></path>
                               </svg>
                               <h3 class="text-base font-bold text-white" style="font-family: 'Montserrat', sans-serif;">Demo Credentials</h3>
                           </div>
                           <div class="space-y-3">
                               <!-- Superadmin Card -->
                               <div class="bg-white/10 hover:bg-white/15 rounded-xl p-3 border border-white/20 transition-all duration-200">
                                   <div class="flex items-center justify-between mb-1">
                                       <div class="flex items-center">
                                            <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center mr-2">
                                                <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                </svg>
                                            </div>
                                            <span class="font-bold text-white text-sm">Superadmin</span>
                                       </div>
                                       <span class="text-xs text-green-200">Password: 123456</span>
                                   </div>
                                   <div class="flex items-center text-xs bg-white/5 rounded-lg px-2 py-1.5 cursor-pointer hover:bg-white/10" onclick="document.querySelector('input[name=identifier]').value = 'admin@cmp.com'; document.querySelector('input[name=password]').value = '123456';">
                                       <code class="text-green-200 font-mono">admin@cmp.com</code>
                                   </div>
                               </div>

                               <!-- HOD Card -->
                               <div class="bg-white/10 hover:bg-white/15 rounded-xl p-3 border border-white/20 transition-all duration-200">
                                   <div class="flex items-center justify-between mb-1">
                                       <div class="flex items-center">
                                            <div class="w-8 h-8 bg-orange-500/20 rounded-lg flex items-center justify-center mr-2">
                                                <svg class="w-4 h-4 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                                </svg>
                                            </div>
                                            <span class="font-bold text-white text-sm">HOD</span>
                                       </div>
                                       <span class="text-xs text-green-200">Password: password123</span>
                                   </div>
                                   <div class="flex items-center text-xs bg-white/5 rounded-lg px-2 py-1.5 cursor-pointer hover:bg-white/10" onclick="document.querySelector('input[name=identifier]').value = 'HOD001'; document.querySelector('input[name=password]').value = 'password123';">
                                       <code class="text-green-200 font-mono">HOD001</code>
                                   </div>
                               </div>

                               <!-- Lecturer Card -->
                               <div class="bg-white/10 hover:bg-white/15 rounded-xl p-3 border border-white/20 transition-all duration-200">
                                   <div class="flex items-center justify-between mb-1">
                                       <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center mr-2">
                                                <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            <span class="font-bold text-white text-sm">Lecturer</span>
                                       </div>
                                       <span class="text-xs text-green-200">Password: password123</span>
                                   </div>
                                   <div class="flex items-center text-xs bg-white/5 rounded-lg px-2 py-1.5 cursor-pointer hover:bg-white/10" onclick="document.querySelector('input[name=identifier]').value = 'LEC001'; document.querySelector('input[name=password]').value = 'password123';">
                                       <code class="text-green-200 font-mono">LEC001</code>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>

               <!-- Right Side - Login Modal (Full width on mobile, half on desktop) -->
               <div class="w-full lg:w-1/2 flex items-start justify-center px-4 sm:px-8 py-12 bg-gray-50 lg:bg-white">
                   <div class="w-full max-w-md mt-8 lg:mt-12">
                       <!-- Mobile Header (Hidden on desktop) -->
                       <div class="lg:hidden text-center mb-8">
                           <div class="flex justify-center mb-4">
                               <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg flex items-center justify-center">
                                   <img src="/images/cropped-nsuk_logo-300x300-1.png" alt="NSUK Logo" class="h-8 w-8 object-contain">
                               </div>
                           </div>
                           <h1 class="text-2xl font-bold text-gray-800 mb-2" style="font-family: 'Montserrat', sans-serif;">NSUK Biometric System</h1>
                           <p class="text-gray-600 text-sm">Department of Computer Science</p>
                       </div>
                       <!-- Login Modal Header (Hidden on mobile) -->
                       <div class="hidden lg:block text-center mb-8">
                           <div class="flex justify-center mb-4">
                               <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg flex items-center justify-center">
                                   <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                   </svg>
                               </div>
                           </div>
                           <h1 class="text-3xl font-bold text-gray-800 mb-2" style="font-family: 'Montserrat', sans-serif;">Welcome Back</h1>
                           <p class="text-gray-600">Sign in to your account</p>
                       </div>

                       <!-- Login Modal -->
                       <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
                           @if($errors->any())
                               <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm text-center">
                                   <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                   </svg>
                                   @foreach($errors->all() as $error)
                                       {{ $error }}
                                   @endforeach
                               </div>
                           @endif

                            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                                    @csrf
                                
                                <!-- Identifier Field -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email or Staff ID</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <input type="text" name="identifier"
                                               id="identifierInput"
                                               class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base transition-all duration-200"
                                               placeholder="admin@cmp.com or LEC123456" autofocus required>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">Enter your email address (Superadmin) or Staff ID (Lecturer/HOD).</p>
                                </div>

                                <!-- Password Field -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                        <input type="password" name="password" id="passwordInput" 
                                               class="w-full pl-10 pr-12 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base transition-all duration-200" 
                                               placeholder="Enter your password" required>
                                        <button type="button" onclick="togglePassword()" 
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-green-600 focus:outline-none transition-colors duration-200 p-1">
                                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-.274.832-.67 1.613-1.176 2.316M15.54 15.54A5.978 5.978 0 0112 17c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.042-3.362"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Inline form error (hidden by default) -->
                                <div id="formError" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm text-center"></div>

                                <!-- Login Button -->
                                <div>
                                    <button type="submit" id="loginButton"
                                            class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg text-base disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span id="loginText">Sign In</span>
                                        <span id="loadingSpinner" class="hidden">
                                            <svg class="animate-spin w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Signing In...
                                        </span>
                                        <svg id="loginIcon" class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <!-- Forgot Password Link -->
                                <div class="mt-4 text-center">
                                    <a href="{{ route('password.forgot') }}" class="text-sm text-green-600 hover:text-green-700 font-semibold transition-colors duration-200 hover:underline">
                                        Forgot Password?
                                    </a>
                                </div>
                            </form>

                           <!-- Mobile Login Instructions (Hidden on desktop) -->
                           <div class="lg:hidden mt-6 p-4 bg-green-50 border border-green-200 rounded-xl shadow-sm">
                               <div class="flex items-center mb-4">
                                   <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                   </svg>
                                   <h3 class="text-sm font-bold text-green-800">Login Instructions</h3>
                               </div>
                               <div class="space-y-3">
                                   <!-- Superadmin Card (Mobile) -->
                                   <div class="cursor-pointer bg-white/60 hover:bg-white/80 rounded-lg p-3 border border-green-200 transition-colors" onclick="document.querySelector('input[name=identifier]').value = 'admin@cmp.com'; document.querySelector('input[name=password]').value = '123456';">
                                       <div class="flex items-center mb-1">
                                           <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center mr-2">
                                               <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                               </svg>
                                           </div>
                                           <span class="font-bold text-green-800 text-sm">Superadmin</span>
                                       </div>
                                       <p class="text-xs text-green-700 mb-1">Use your email address</p>
                                       <div class="flex items-center text-xs bg-white/50 rounded px-2 py-1">
                                           <svg class="w-3 h-3 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                           </svg>
                                           <code class="text-green-700 font-mono text-xs">admin@cmp.com</code>
                                       </div>
                                   </div>
                                   <!-- Lecturer Card (Mobile) -->
                                   <div class="cursor-pointer bg-white/60 hover:bg-white/80 rounded-lg p-3 border border-green-200 transition-colors" onclick="document.querySelector('input[name=identifier]').value = 'LEC123456'; document.querySelector('input[name=password]').value = 'password123';">
                                       <div class="flex items-center mb-1">
                                           <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center mr-2">
                                               <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                               </svg>
                                           </div>
                                           <span class="font-bold text-green-800 text-sm">Lecturer</span>
                                       </div>
                                       <p class="text-xs text-green-700 mb-1">Use your staff ID starting with LEC</p>
                                       <div class="flex items-center text-xs bg-white/50 rounded px-2 py-1">
                                           <svg class="w-3 h-3 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                           </svg>
                                           <code class="text-green-700 font-mono text-xs">LEC123456</code>
                                       </div>
                                   </div>
                                   <!-- HOD Card (Mobile) -->
                                   <div class="cursor-pointer bg-white/60 hover:bg-white/80 rounded-lg p-3 border border-green-200 transition-colors" onclick="document.querySelector('input[name=identifier]').value = 'HOD001'; document.querySelector('input[name=password]').value = 'password123';">
                                       <div class="flex items-center mb-1">
                                           <div class="w-8 h-8 bg-orange-500/20 rounded-lg flex items-center justify-center mr-2">
                                               <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                               </svg>
                                           </div>
                                           <span class="font-bold text-green-800 text-sm">HOD</span>
                                       </div>
                                       <p class="text-xs text-green-700 mb-1">Use your staff ID starting with HOD</p>
                                       <div class="flex items-center text-xs bg-white/50 rounded px-2 py-1">
                                           <svg class="w-3 h-3 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                           </svg>
                                           <code class="text-green-700 font-mono text-xs">HOD001</code>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </main>

    <!-- Footer -->
    <footer class="w-full bg-white/90 backdrop-blur-sm border-t border-green-200 py-4 text-center text-sm text-gray-600 font-medium" style="font-family: 'Montserrat', sans-serif;">
        <div class="flex items-center justify-center gap-2 mb-2">
            <div class="w-6 h-6 bg-[#008000] rounded-full flex items-center justify-center text-white text-xs font-bold">N</div>
            <span class="font-semibold text-[#008000]">Nasarawa State University, Keffi</span>
        </div>
        <div>&copy; {{ date('Y') }} All rights reserved. | Department of Computer Science</div>
        <div class="mt-2 text-xs text-gray-500">
            Developed and Powered by <a href="https://wa.me/2349153390947" target="_blank" class="text-green-600 hover:text-green-800 transition-colors">Cybershrine Technologies</a>
        </div>
    </footer>

           <script>
           function togglePassword() {
               const input = document.getElementById('passwordInput');
               const icon = document.getElementById('eyeIcon');
               if (input.type === 'password') {
                   input.type = 'text';
                   icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.042-3.362m1.664-2.486A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.422 5.568M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
               } else {
                   input.type = 'password';
                   icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-.274.832-.67 1.613-1.176 2.316M15.54 15.54A5.978 5.978 0 0112 17c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.042-3.362" />`;
               }
           }

           // Add client-side validation and loading state to login form
           document.addEventListener('DOMContentLoaded', function() {
               const form = document.querySelector('form');
               const loginButton = document.getElementById('loginButton');
               const loginText = document.getElementById('loginText');
               const loadingSpinner = document.getElementById('loadingSpinner');
               const loginIcon = document.getElementById('loginIcon');
               const formError = document.getElementById('formError');

               function showError(message, focusSelector) {
                   formError.innerText = message;
                   formError.classList.remove('hidden');
                   loginButton.disabled = false;
                   loginText.classList.remove('hidden');
                   loadingSpinner.classList.add('hidden');
                   loginIcon.classList.remove('hidden');
                   if (focusSelector) {
                       const el = form.querySelector(focusSelector);
                       if (el) el.focus();
                   }
               }

               form.addEventListener('submit', function(e) {
                   e.preventDefault();
                   formError.classList.add('hidden');
                   formError.innerText = '';

                   const identifier = (form.querySelector('input[name="identifier"]') || { value: '' }).value.trim();
                   const password = (form.querySelector('input[name="password"]') || { value: '' }).value.trim();

                   if (!identifier) { showError('Please enter your Email or Staff ID.', 'input[name="identifier"]'); return; }
                   if (!password) { showError('Please enter your password.', 'input[name="password"]'); return; }

                   // Passed validation: show loading state and submit
                   loginButton.disabled = true;
                   loginText.classList.add('hidden');
                   loadingSpinner.classList.remove('hidden');
                   loginIcon.classList.add('hidden');

                   form.submit();
               });
           });
           </script>
</body>
</html>
