<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
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
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #10b981;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .user-info {
            background: #eff6ff;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #3b82f6;
        }
        .user-info strong {
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔒 Password Reset Request</h1>
        <p style="margin: 0;">NSUK Biometric Attendance System</p>
    </div>

    <div class="content">
        @if($isSuperadminNotification)
            <h2>Security Alert: Password Reset Attempt</h2>
            <p>Dear Administrator,</p>
            <p>A password reset request has been initiated for a user account. Please review the details below:</p>
        @else
            <h2>Password Reset Requested</h2>
            <p>Hello {{ $userData['name'] }},</p>
            <p>You have requested to reset your password for the NSUK Biometric Attendance System.</p>
        @endif

        <div class="alert-box">
            <strong>⚠️ Security Notice:</strong>
            @if($isSuperadminNotification)
                <p>This is a notification email. The password reset OTP has been sent to the user's registered contact method.</p>
            @else
                <p>If you did not request this password reset, please ignore this email and contact the administrator immediately.</p>
            @endif
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">Request Details:</h3>
            <div class="user-info">
                <p><strong>User Type:</strong> {{ ucfirst($userType) }}</p>
                <p><strong>Identifier:</strong> {{ $identifier }}</p>
                <p><strong>User Name:</strong> {{ $userData['name'] }}</p>
                <p><strong>User Email:</strong> {{ $userData['email'] }}</p>
            </div>
            <p><strong>Request Time:</strong> {{ $notifiedAt->format('F j, Y \a\t g:i A') }}</p>
            @if($ipAddress)
            <p><strong>IP Address:</strong> {{ $ipAddress }}</p>
            @endif
        </div>

        @if(!$isSuperadminNotification)
        <div class="info-box">
            <h3 style="margin-top: 0;">Next Steps:</h3>
            <ol>
                <li>Check your {{ $userType == 'superadmin' ? 'email' : 'email or phone' }} for the 6-digit OTP code</li>
                <li>Enter the OTP on the verification page</li>
                <li>Set your new password</li>
            </ol>
            <p><strong>Note:</strong> The OTP will expire in 15 minutes.</p>
        </div>
        @endif

        @if($isSuperadminNotification)
        <div class="info-box">
            <h3 style="margin-top: 0;">Administrator Action:</h3>
            <p>Please monitor this password reset attempt. If this seems suspicious, you may want to:</p>
            <ul>
                <li>Contact the user to verify the request</li>
                <li>Check system logs for any unusual activity</li>
                <li>Review recent login attempts from this IP address</li>
            </ul>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>This is an automated email from the NSUK Biometric Attendance System.</p>
        <p>Department of Computer Science | Nasarawa State University, Keffi</p>
        <p style="margin-top: 10px; color: #9ca3af;">
            If you have any concerns, please contact the system administrator immediately.
        </p>
    </div>
</body>
</html>

