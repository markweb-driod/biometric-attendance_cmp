<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Grade Report - {{ $classroom->class_name }}</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #16a34a;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            color: #16a34a;
            font-size: 28px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background-color: #16a34a;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .grade-a { color: #16a34a; font-weight: bold; }
        .grade-b { color: #3b82f6; font-weight: bold; }
        .grade-c { color: #eab308; font-weight: bold; }
        .grade-d { color: #f97316; font-weight: bold; }
        .grade-f { color: #ef4444; font-weight: bold; }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #16a34a;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background-color: #15803d;
        }
        
        .summary-box {
            background-color: #f0fdf4;
            border: 1px solid #16a34a;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            text-align: center;
        }
        
        .summary-item {
            padding: 10px;
            background-color: white;
            border-radius: 3px;
        }
        
        .summary-item .label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .summary-item .value {
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Report</button>
    
    <div class="header">
        <h1>Attendance Grade Report</h1>
        <p><strong>{{ $classroom->class_name }}</strong></p>
        <p>{{ $classroom->course->course_name ?? 'N/A' }} ({{ $classroom->course->course_code ?? 'N/A' }})</p>
        <p>Generated on: {{ date('F d, Y h:i A') }}</p>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <span><strong>Lecturer:</strong> {{ $classroom->lecturer->user->full_name ?? 'N/A' }}</span>
            <span><strong>Total Students:</strong> {{ count($grades) }}</span>
        </div>
        <div class="info-row">
            <span><strong>Academic Year:</strong> {{ $classroom->academic_year ?? 'N/A' }}</span>
            <span><strong>Semester:</strong> {{ $classroom->semester->name ?? 'N/A' }}</span>
        </div>
    </div>
    
    <div class="summary-box">
        <h3 style="margin-top: 0;">Grade Distribution</h3>
        <div class="summary-grid">
            @php
                $distribution = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0];
                foreach ($grades as $item) {
                    if (isset($distribution[$item['grade']])) {
                        $distribution[$item['grade']]++;
                    }
                }
            @endphp
            @foreach($distribution as $grade => $count)
                <div class="summary-item">
                    <div class="label">Grade {{ $grade }}</div>
                    <div class="value grade-{{ strtolower($grade) }}">{{ $count }}</div>
                </div>
            @endforeach
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>S/N</th>
                <th>Matric Number</th>
                <th>Full Name</th>
                <th>Total Sessions</th>
                <th>Attended</th>
                <th>Attendance %</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grades as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['student']->matric_number }}</td>
                    <td>{{ $item['student']->user->full_name ?? 'N/A' }}</td>
                    <td>{{ $item['attendance']['total_sessions'] }}</td>
                    <td>{{ $item['attendance']['total_present'] }}</td>
                    <td>{{ $item['attendance']['percentage'] }}%</td>
                    <td class="grade-{{ strtolower($item['grade']) }}">{{ $item['grade'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No students found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>Grading Scale</strong></p>
        <p>
            @foreach($rules as $grade => $threshold)
                Grade {{ $grade }}: ≥ {{ $threshold }}%{{ !$loop->last ? ' | ' : '' }}
            @endforeach
        </p>
        <p style="margin-top: 20px;">
            &copy; {{ date('Y') }} Computer Science Department, Nasarawa State University, Keffi<br>
            Powered by NSUK Biometric Attendance System
        </p>
    </div>
</body>
</html>
