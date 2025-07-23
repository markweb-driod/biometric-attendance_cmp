@extends('layouts.superadmin')

@section('title', 'Attendance Audit')
@section('page-title', 'Attendance Audit')
@section('page-description', 'Review all attendance attempts, including denied ones, with location and status.')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-6">
    <h2 class="text-2xl font-bold mb-4">Attendance Attempts (Geo-Fencing)</h2>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6" id="stats-cards">
        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Total Attempts</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-total">-</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Present</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-present">-</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Denied</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-denied">-</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Unique Students</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-unique">-</div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mb-2"><svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" /></svg></div>
            <div class="text-xs text-gray-500">Suspected Spoofers</div>
            <div class="text-xl font-bold text-gray-900" id="kpi-spoofers">-</div>
        </div>
    </div>
    <div class="mb-4 flex justify-end">
        <a href="{{ route('superadmin.attendance.audit.export') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition">Export CSV</a>
    </div>
    <div class="overflow-x-auto bg-white rounded-xl shadow p-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Session</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Map</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $deniedCounts = [];
                    foreach ($attendances as $a) {
                        $sid = $a->student->id ?? null;
                        if ($a->status === 'denied' && $sid) {
                            $deniedCounts[$sid] = ($deniedCounts[$sid] ?? 0) + 1;
                        }
                    }
                @endphp
                @foreach($attendances as $attendance)
                @php
                    $sid = $attendance->student->id ?? null;
                    $spoofing = ($sid && ($deniedCounts[$sid] ?? 0) >= 3);
                @endphp
                <tr @if($spoofing) style="background:#fff7e6;" @endif>
                    <td class="px-3 py-2 whitespace-nowrap">
                        {{ $attendance->student->full_name ?? '-' }}<br>
                        <span class="text-xs text-gray-400">{{ $attendance->student->matric_number ?? '' }}</span>
                        @if($spoofing)
                            <span class="ml-2 px-2 py-1 bg-red-600 text-white text-xs rounded">SUSPECTED SPOOFING</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        {{ $attendance->classroom->class_name ?? '-' }}<br>
                        <span class="text-xs text-gray-400">{{ $attendance->classroom->course_code ?? '' }}</span>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        {{ $attendance->attendanceSession->code ?? '-' }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        {{ $attendance->captured_at ? \Carbon\Carbon::parse($attendance->captured_at)->format('Y-m-d H:i:s') : '-' }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        @if($attendance->status === 'present')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Present</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Denied</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-xs">
                        Lat: {{ $attendance->latitude ?? '-' }}<br>
                        Lng: {{ $attendance->longitude ?? '-' }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        @if($attendance->latitude && $attendance->longitude && $attendance->attendanceSession && $attendance->attendanceSession->latitude && $attendance->attendanceSession->longitude)
                        <div id="map-{{ $attendance->id }}" style="width:180px; height:120px;"></div>
                        <span class="text-xs text-gray-400">Session: {{ $attendance->attendanceSession->latitude }}, {{ $attendance->attendanceSession->longitude }}</span>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var map = L.map('map-{{ $attendance->id }}', { zoomControl: false, attributionControl: false }).setView([{{ $attendance->latitude }}, {{ $attendance->longitude }}], 17);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19
                            }).addTo(map);
                            L.marker([{{ $attendance->latitude }}, {{ $attendance->longitude }}]).addTo(map)
                                .bindPopup('Student Location').openPopup();
                            @if($attendance->attendanceSession->latitude && $attendance->attendanceSession->longitude && $attendance->attendanceSession->radius)
                                L.circle([
                                    {{ $attendance->attendanceSession->latitude }},
                                    {{ $attendance->attendanceSession->longitude }}
                                ], {
                                    color: 'blue',
                                    fillColor: '#3b82f6',
                                    fillOpacity: 0.1,
                                    radius: {{ $attendance->attendanceSession->radius }}
                                }).addTo(map).bindPopup('Allowed Area');
                                L.marker([
                                    {{ $attendance->attendanceSession->latitude }},
                                    {{ $attendance->attendanceSession->longitude }}
                                ], {icon: L.icon({iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png', iconSize: [20, 20]})}).addTo(map).bindPopup('Session Center');
                            @endif
                        });
                        </script>
                        @else
                        <span class="text-xs text-gray-400">No location</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $attendances->links() }}</div>
    </div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route('superadmin.attendance.audit.stats') }}')
        .then(res => res.json())
        .then(stats => {
            document.getElementById('kpi-total').textContent = stats.total ?? '-';
            document.getElementById('kpi-present').textContent = stats.present ?? '-';
            document.getElementById('kpi-denied').textContent = stats.denied ?? '-';
            document.getElementById('kpi-unique').textContent = stats.unique_students ?? '-';
            document.getElementById('kpi-spoofers').textContent = stats.suspected_spoofers ?? '-';
        });
});
</script>
@endsection 