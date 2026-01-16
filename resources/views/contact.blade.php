@extends('layout')
@section('content')
<div class="max-w-xl mx-auto mt-1 bg-white rounded-xl shadow-lg p-8 border border-green-100">
    <h1 class="text-3xl font-bold text-green-700 mb-4">Contact Us</h1>
    <p class="text-gray-700 mb-6">Reach out to the Department of Computer Science, Nasarawa State University, Keffi. We welcome your questions, feedback, and inquiries.</p>
    <form class="space-y-5">
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
            <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 text-base" required>
        </div>
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 text-base" required>
        </div>
        <div>
            <label for="message" class="block text-sm font-semibold text-gray-700 mb-1">Message</label>
            <textarea id="message" name="message" rows="4" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 text-base" required></textarea>
        </div>
        <button type="submit" class="w-full py-3 px-6 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition-all duration-200 shadow">Send Message</button>
    </form>
    <div class="mt-8 text-gray-600 text-sm">
        <div><strong>Email:</strong> <a href="mailto:ashimotechie@gmail.com" class="text-green-600 hover:underline">ashimotechie@gmail.com</a></div>
        <div><strong>Phone:</strong> <a href="tel:+2349163779477" class="text-green-600 hover:underline">09163779477</a></div>
        <div><strong>Address:</strong> Department of Computer Science, Nasarawa State University, Keffi, Nigeria</div>
    </div>
</div>
@endsection 