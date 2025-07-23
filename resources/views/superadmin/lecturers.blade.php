@extends('layouts.superadmin')

@section('title', 'Lecturers')
@section('page-title', 'Lecturers')
@section('page-description', 'Manage all lecturers by department')

@section('content')
<div class="max-w-6xl mx-auto w-full px-2 py-3">
    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6 max-w-5xl mx-auto w-full">
        <div class="relative bg-white/90 shadow-xl border border-green-200 rounded-2xl p-4 flex flex-col items-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl min-w-[180px]">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 mb-2 shadow-inner">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>
            </div>
            <div class="text-2xl font-extrabold text-green-700 mb-0.5" id="kpi-lecturers">-</div>
            <div class="text-sm font-semibold text-green-600 tracking-wide">Total</div>
        </div>
        <div class="relative bg-white/90 shadow-xl border border-blue-200 rounded-2xl p-4 flex flex-col items-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl min-w-[180px]">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 mb-2 shadow-inner">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>
            </div>
            <div class="text-2xl font-extrabold text-blue-700 mb-0.5" id="kpi-active">-</div>
            <div class="text-sm font-semibold text-blue-600 tracking-wide">Active</div>
        </div>
        <div class="relative bg-white/90 shadow-xl border border-purple-200 rounded-2xl p-4 flex flex-col items-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl min-w-[180px]">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-purple-100 mb-2 shadow-inner">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>
            </div>
            <div class="text-2xl font-extrabold text-purple-700 mb-0.5" id="kpi-inactive">-</div>
            <div class="text-sm font-semibold text-purple-600 tracking-wide">Inactive</div>
        </div>
        <div class="relative bg-white/90 shadow-xl border border-orange-200 rounded-2xl p-4 flex flex-col items-center transition-transform duration-200 hover:scale-105 hover:shadow-2xl min-w-[180px]">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-100 mb-2 shadow-inner">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg>
            </div>
            <div class="text-xl font-extrabold text-orange-700 mb-0.5" id="kpi-upload">-</div>
            <div class="text-sm font-semibold text-orange-600 tracking-wide">Upload</div>
        </div>
    </div>
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between bg-green-500 rounded-lg px-6 py-3 shadow mb-6">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-1">Lecturers Management</h2>
            <p class="text-white text-base opacity-90">Upload, add, and manage all lecturers by department</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="flex items-center px-5 py-2.5 bg-blue-100 text-blue-700 font-semibold rounded-lg border border-blue-200 hover:bg-blue-200 transition text-base shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Download Template
            </button>
            <button class="flex items-center px-5 py-2.5 bg-green-100 text-green-700 font-semibold rounded-lg border border-green-200 hover:bg-green-200 transition text-base shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v16m8-8H4"/></svg>
                Upload
            </button>
            <button class="flex items-center px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition text-base">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add
            </button>
        </div>
    </div>
    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow p-2 sm:p-3 mb-3 w-full">
        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">#</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Staff ID</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Email</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Department</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Title</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 uppercase text-xs tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($lecturers as $index => $lecturer)
                    <tr class="even:bg-gray-50 hover:bg-green-50 transition-colors duration-200 align-middle">
                        <td class="px-4 py-3 font-semibold text-gray-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $lecturer->name }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $lecturer->staff_id }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $lecturer->email }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $lecturer->department }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $lecturer->title }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $lecturer->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">
                                {{ ucfirst($lecturer->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button class="inline-flex items-center px-2.5 py-1.5 bg-blue-500 text-white rounded-lg text-xs font-semibold hover:bg-blue-600 shadow-sm mr-2" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                Edit
                            </button>
                            <button class="inline-flex items-center px-2.5 py-1.5 bg-red-500 text-white rounded-lg text-xs font-semibold hover:bg-red-600 shadow-sm" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchLecturerStats();
});

function fetchLecturerStats() {
    axios.get('/api/superadmin/lecturers/stats')
        .then(res => {
            const stats = res.data;
            document.getElementById('kpi-lecturers').textContent = stats.total ?? '-';
            document.getElementById('kpi-active').textContent = stats.active ?? '-';
            document.getElementById('kpi-inactive').textContent = stats.inactive ?? '-';
            document.getElementById('kpi-upload').textContent = stats.last_upload ? new Date(stats.last_upload).toLocaleString() : '-';
        })
        .catch(() => {
            document.getElementById('kpi-lecturers').textContent = '-';
            document.getElementById('kpi-active').textContent = '-';
            document.getElementById('kpi-inactive').textContent = '-';
            document.getElementById('kpi-upload').textContent = '-';
        });
}
</script> 