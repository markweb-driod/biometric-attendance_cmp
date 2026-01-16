@extends('layouts.hod')
@section('title', 'Setup Two-Factor Authentication')
@section('content')
<div class="max-w-md mx-auto p-6 mt-8 bg-white rounded-lg shadow border border-blue-100">
    <h1 class="text-xl font-bold mb-4">Setup Two-Factor Authentication</h1>
    
    <div class="mb-4">
        <p class="text-sm text-gray-700 mb-4">
            Scan the QR code below with your authenticator app (Google Authenticator, Authy, etc.) to enable two-factor authentication.
        </p>
        
        <div class="flex justify-center mb-4">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code" class="border border-gray-300 p-2">
        </div>
        
        <div class="mb-4 p-3 bg-gray-50 rounded border">
            <p class="text-xs text-gray-600 mb-2">Can't scan? Enter this code manually:</p>
            <p class="font-mono text-sm text-center">{{ chunk_split($secret, 4, ' ') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('hod.two-factor.confirm') }}">
        @csrf
        <label for="two_factor_code" class="block text-sm font-semibold mb-1">Enter 6-digit code from your app</label>
        <input name="two_factor_code" id="two_factor_code" type="text" class="w-full border px-3 py-2 rounded mb-2" maxlength="6" autofocus required inputmode="numeric" pattern="[0-9]{6}">
        @error('two_factor_code')
            <div class="text-red-700 text-xs mb-2">{{ $message }}</div>
        @enderror
        <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Enable Two-Factor Authentication
        </button>
    </form>
    
    <div class="mt-4">
        <a href="{{ route('hod.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</div>
@endsection

