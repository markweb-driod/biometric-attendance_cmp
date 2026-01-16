<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Registration Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .success-icon {
            text-align: center;
            margin: 20px 0;
            font-size: 48px;
        }
        .message {
            background-color: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .info-item {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
            display: inline-block;
            width: 150px;
        }
        .info-value {
            color: #111827;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #10b981;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
        .note {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Face Registration Successful</h1>
        </div>
        
        <div class="content">
            <div class="success-icon">🎉</div>
            
            <p>Dear <?php echo e($student->user->full_name ?? 'Student'); ?>,</p>
            
            <div class="message">
                <p><strong>Congratulations!</strong> Your face registration has been completed successfully. You can now use the biometric attendance system to mark your attendance.</p>
            </div>
            
            <div class="info-box">
                <div class="info-item">
                    <span class="info-label">Full Name:</span>
                    <span class="info-value"><?php echo e($student->user->full_name ?? 'N/A'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Matric Number:</span>
                    <span class="info-value"><?php echo e($student->matric_number ?? 'N/A'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Registration Date:</span>
                    <span class="info-value"><?php echo e(now()->format('l, F j, Y \a\t g:i A')); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value" style="color: #10b981; font-weight: bold;">✓ Active</span>
                </div>
            </div>
            
            <h3 style="color: #059669; margin-top: 30px;">What's Next?</h3>
            <ul style="line-height: 1.8;">
                <li>You can now mark your attendance using face recognition</li>
                <li>Simply present your face to the camera during attendance sessions</li>
                <li>Your attendance will be automatically recorded</li>
            </ul>
            
            <div class="note">
                <strong>📌 Important Note:</strong> This face registration is for security purposes. Make sure to attend classes regularly and mark your attendance on time.
            </div>
            
            <p style="margin-top: 30px;">If you have any questions or concerns, please contact the Department of Computer Science, NSUK.</p>
            
            <p>Best regards,<br>
            <strong>Biometric Attendance System</strong><br>
            Department of Computer Science<br>
            Nasarawa State University, Keffi</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; <?php echo e(date('Y')); ?> NSUK Biometric Attendance System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\emails\face-registration-success.blade.php ENDPATH**/ ?>