<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Successful</title>
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
        .success-box {
            background: #d1fae5;
            border-left: 4px solid #10b981;
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
        .security-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
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
        <h1>✅ Password Reset Successful</h1>
        <p style="margin: 0;">NSUK Biometric Attendance System</p>
    </div>

    <div class="content">
        @if($isSuperadminNotification)
            <h2>Security Notification: Password Reset Completed</h2>
            <p>Dear Administrator,</p>
            <p>A password has been successfully reset for a user account. Please review the details below:</p>
        @else
            <h2>Password Successfully Reset</h2>
            <p>Hello {{ $userData['name'] }},</p>
            <p>Your password has been successfully reset for the NSUK Biometric Attendance System.</p>
        @endif

        <div class="success-box">
            <strong>✅ Success:</strong>
            <p>The password reset process has been completed successfully.</p>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">Account Details:</h3>
            <div class="user-info">
                <p><strong>User Type:</strong> {{ ucfirst($userType) }}</p>
                <p><strong>Identifier:</strong> {{ $identifier }}</p>
                <p><strong>User Name:</strong> {{ $userData['name'] }}</p>
                <p><strong>User Email:</strong> {{ $userData['email'] }}</p>
            </div>
            <p><strong>Password Changed At:</strong> {{ $changedAt->format('F j, Y \a\t g:i A') }}</p>
        </div>

        @if(!$isSuperadminNotification)
        <div class="info-box">
            <h3 style="margin-top: 0;">Next Steps:</h3>
            <ol>
                <li>You can now log in with your new password</li>
                <li>Make sure to use a strong, unique password</li>
                <li>Keep your password secure and do not share it with anyone</li>
            </ol>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/login') }}" class="button">Login to Your Account</a>
        </div>
        @endif

        <div class="security-box">
            <strong>🔒 Security Recommendations:</strong>
            <ul>
                <li>Use a strong password that includes letters, numbers, and special characters</li>
                <li>Never share your password with anyone</li>
                <li>If you notice any suspicious activity, contact the administrator immediately</li>
                <li>Consider enabling two-factor authentication for additional security</li>
            </ul>
        </div>

        @if($isSuperadminNotification)
        <div class="info-box">
            <h3 style="margin-top: 0;">Administrator Notes:</h3>
            <p>This password reset was completed successfully. The user can now log in with their new password.</p>
            <p>If this change seems unexpected or suspicious, consider contacting the user to verify the change.</p>
        </div>
        @endif

        @if(!$isSuperadminNotification)
        <div class="security-box">
            <strong>⚠️ Important:</strong>
            <p>If you did not request this password change, please contact the system administrator immediately. Your account may have been compromised.</p>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>This is an automated email from the NSUK Biometric Attendance System.</p>
        <p>Department of Computer Science | Nasarawa State University, Keffi</p>
        <p style="margin-top: 10px; color: #9ca3af;">
            If you have any concerns about this password change, please contact the system administrator immediately.
        </p>
    </div>
</body>
</html>

