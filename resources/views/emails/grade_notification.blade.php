<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Grade Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #16a34a;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 5px 5px;
        }
        .grade-box {
            background-color: white;
            border: 2px solid #16a34a;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .grade {
            font-size: 48px;
            font-weight: bold;
            color: #16a34a;
        }
        .stats {
            margin: 20px 0;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Attendance Grade Notification</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $student->user->full_name ?? 'Student' }},</p>
        
        <p>This is to inform you of your attendance grade for <strong>{{ $classroom->class_name }}</strong> ({{ $classroom->course->course_name ?? 'N/A' }}).</p>
        
        <div class="grade-box">
            <div class="grade">{{ $grade }}</div>
            <p style="margin: 0; color: #6b7280;">Your Grade</p>
        </div>
        
        <div class="stats">
            <div class="stat-row">
                <span><strong>Total Sessions:</strong></span>
                <span>{{ $attendance['total_sessions'] }}</span>
            </div>
            <div class="stat-row">
                <span><strong>Sessions Attended:</strong></span>
                <span>{{ $attendance['total_present'] }}</span>
            </div>
            <div class="stat-row">
                <span><strong>Attendance Percentage:</strong></span>
                <span>{{ $attendance['percentage'] }}%</span>
            </div>
        </div>
        
        <p>If you have any questions about your grade, please contact your lecturer.</p>
        
        <p>Best regards,<br>
        <strong>Computer Science Department</strong><br>
        Nasarawa State University, Keffi</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from the NSUK Biometric Attendance System.</p>
        <p>&copy; {{ date('Y') }} Nasarawa State University, Keffi. All rights reserved.</p>
    </div>
</body>
</html>
