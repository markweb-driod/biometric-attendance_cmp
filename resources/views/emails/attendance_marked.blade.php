<!DOCTYPE html>
<html>
<head>
    <title>Attendance Marked</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px;">
        <h2 style="color: #047857;">Attendance Confirmation</h2>
        
        <p>Dear <strong>{{ $studentName }}</strong>,</p>
        
        <p>This is to confirm that your attendance has been successfully marked for the following session:</p>
        
        <div style="background-color: #f9fafb; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>Course:</strong> {{ $courseCode }} - {{ $courseTitle }}</p>
            <p style="margin: 5px 0;"><strong>Session ID:</strong> <span style="font-family: monospace; background: #e5e7eb; padding: 2px 5px; border-radius: 4px;">{{ $sessionCode }}</span></p>
            <p style="margin: 5px 0;"><strong>Time Marked:</strong> {{ \Carbon\Carbon::parse($capturedAt)->format('F j, Y, g:i A') }}</p>
            <p style="margin: 5px 0;"><strong>Status:</strong> <span style="color: #047857; font-weight: bold;">Present</span></p>
        </div>
        
        <p>You may leave the class now.</p>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #6b7280;">This is an automated message from the NSUK Biometric Attendance System.</p>
    </div>
</body>
</html>
