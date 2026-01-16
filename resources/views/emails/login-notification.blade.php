<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Login Detected</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .alert-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-box {
            background: #f3f4f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #10b981;
        }
        .session-details {
            background: #eff6ff;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #3b82f6;
        }
        .session-details strong {
            color: #1e40af;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 10px 10px;
            font-size: 12px;
            color: #6b7280;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 New Login Detected</h1>
        <p style="margin: 0;">NSUK Biometric Attendance System</p>
    </div>

    <div class="content">
        <h2>Security Alert</h2>
        <p>Dear Administrator,</p>
        <p>A new login has been detected on the NSUK Biometric Attendance System. Please review the session details below:</p>

        <div class="alert-box">
            <strong>⚠️ New Session Started:</strong>
            <p>A user has successfully logged into the system.</p>
        </div>

        <div class="session-details">
            <h3 style="margin-top: 0;">User Information:</h3>
            <p><strong>User Type:</strong> {{ ucfirst($session->user_type) }}</p>
            <p><strong>Full Name:</strong> {{ $session->full_name }}</p>
            <p><strong>Identifier:</strong> {{ $session->identifier }}</p>
            @if($session->department_name)
                <p><strong>Department:</strong> {{ $session->department_name }}</p>
            @endif
        </div>

        <div class="session-details">
            <h3 style="margin-top: 0;">Session Details:</h3>
            <p><strong>Login Time:</strong> {{ $session->login_at->format('F j, Y \a\t g:i A') }}</p>
            <p><strong>Session ID:</strong> {{ substr($session->session_id, 0, 20) }}...</p>
            <p><strong>Status:</strong> <span class="status-badge status-active">ACTIVE</span></p>
        </div>

        <div class="session-details">
            <h3 style="margin-top: 0;">Device & Location Information:</h3>
            <p><strong>IP Address:</strong> {{ $session->ip_address }}</p>
            <p><strong>Device Type:</strong> {{ ucfirst($session->device_type ?? 'Unknown') }}</p>
            <p><strong>Operating System:</strong> {{ $session->os ?? 'Unknown' }}</p>
            <p><strong>Browser:</strong> {{ $session->browser ?? 'Unknown' }}</p>
            @if($session->country)
                <p><strong>Location:</strong> {{ $session->city ?? '' }}{{ $session->city ? ', ' : '' }}{{ $session->country }}</p>
            @endif
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">What You Should Do:</h3>
            <ol>
                <li>Verify that this login is expected and legitimate</li>
                <li>Check if the IP address and location match the user's usual pattern</li>
                <li>If suspicious, terminate the session immediately through the admin dashboard</li>
                <li>Contact the user directly if the login seems unauthorized</li>
            </ol>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">Quick Actions:</h3>
            <p>You can view all active sessions and terminate any suspicious session from the Session Monitoring dashboard.</p>
        </div>

        <div class="alert-box">
            <strong>🔒 Security Reminder:</strong>
            <p>If this login appears to be unauthorized or suspicious, please take immediate action to secure the account.</p>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated email from the NSUK Biometric Attendance System.</p>
        <p>Department of Computer Science | Nasarawa State University, Keffi</p>
        <p style="margin-top: 10px; color: #9ca3af;">
            You are receiving this notification because you are an active system administrator. If you have concerns about this login, please investigate immediately.
        </p>
    </div>
</body>
</html>

