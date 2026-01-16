@extends('layouts.superadmin')
@section('title', 'Two-Factor Authentication')
@section('content')
<div class="max-w-sm mx-auto p-5 mt-16 bg-white rounded-lg shadow border border-green-100"
     x-data="{ isLoading: false }"
     x-init="
         @if(session('success'))
            setTimeout(() => { window.showSuccessModal ? window.showSuccessModal('{{ session('success') }}') : alert('{{ session('success') }}'); }, 300);
         @endif
         @if(session('error'))
            setTimeout(() => { window.showErrorModal ? window.showErrorModal('{{ session('error') }}') : alert('{{ session('error') }}'); }, 300);
         @endif
         @if($errors->has('code'))
            setTimeout(() => { window.showErrorModal ? window.showErrorModal('{{ $errors->first('code') }}', 'Two-Factor Authentication') : alert('{{ $errors->first('code') }}') }, 300);
         @endif
     "
>
    <h1 class="text-lg font-bold mb-4">Two-Factor Authentication</h1>
    <form method="POST" action="{{ route('superadmin.2fa.verify') }}"
          @submit.prevent="isLoading = true; $el.submit();">
        @csrf
        <label for="code" class="block text-sm font-semibold mb-1">Enter 6-digit code</label>
        <input name="code" id="code" type="text" class="w-full border px-3 py-2 rounded mb-2" maxlength="6" autofocus required inputmode="numeric" pattern="[0-9]{6}">
        @error('code')
            <div class="text-red-700 text-xs mb-2">{{ $message }}</div>
        @enderror
        <button type="submit" class="w-full py-2 bg-green-600 text-white rounded hover:bg-green-700 flex justify-center items-center disabled:opacity-60" :disabled="isLoading">
            <template x-if="isLoading">
                <svg class="animate-spin h-5 w-5 mr-2 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
            </template>
            <span x-text="isLoading ? 'Verifying...' : 'Verify'"></span>
        </button>
    </form>
    <noscript>
        @if(session('success'))<div class="mt-4 p-2 bg-green-100 border border-green-200 rounded text-green-900">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="mt-4 p-2 bg-red-100 border border-red-200 rounded text-red-900">{{ session('error') }}</div>@endif
    </noscript>
</div>
@endsection
