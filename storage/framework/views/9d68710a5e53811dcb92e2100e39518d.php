<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Grade Report - <?php echo e($classroom->class_name); ?></title>
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
        <p><strong><?php echo e($classroom->class_name); ?></strong></p>
        <p><?php echo e($classroom->course->course_name ?? 'N/A'); ?> (<?php echo e($classroom->course->course_code ?? 'N/A'); ?>)</p>
        <p>Generated on: <?php echo e(date('F d, Y h:i A')); ?></p>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <span><strong>Lecturer:</strong> <?php echo e($classroom->lecturer->user->full_name ?? 'N/A'); ?></span>
            <span><strong>Total Students:</strong> <?php echo e(count($grades)); ?></span>
        </div>
        <div class="info-row">
            <span><strong>Academic Year:</strong> <?php echo e($classroom->academic_year ?? 'N/A'); ?></span>
            <span><strong>Semester:</strong> <?php echo e($classroom->semester->name ?? 'N/A'); ?></span>
        </div>
    </div>
    
    <div class="summary-box">
        <h3 style="margin-top: 0;">Grade Distribution</h3>
        <div class="summary-grid">
            <?php
                $distribution = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0];
                foreach ($grades as $item) {
                    if (isset($distribution[$item['grade']])) {
                        $distribution[$item['grade']]++;
                    }
                }
            ?>
            <?php $__currentLoopData = $distribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="summary-item">
                    <div class="label">Grade <?php echo e($grade); ?></div>
                    <div class="value grade-<?php echo e(strtolower($grade)); ?>"><?php echo e($count); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
            <?php $__empty_1 = true; $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item['student']->matric_number); ?></td>
                    <td><?php echo e($item['student']->user->full_name ?? 'N/A'); ?></td>
                    <td><?php echo e($item['attendance']['total_sessions']); ?></td>
                    <td><?php echo e($item['attendance']['total_present']); ?></td>
                    <td><?php echo e($item['attendance']['percentage']); ?>%</td>
                    <td class="grade-<?php echo e(strtolower($item['grade'])); ?>"><?php echo e($item['grade']); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No students found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>Grading Scale</strong></p>
        <p>
            <?php $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade => $threshold): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                Grade <?php echo e($grade); ?>: ≥ <?php echo e($threshold); ?>%<?php echo e(!$loop->last ? ' | ' : ''); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </p>
        <p style="margin-top: 20px;">
            &copy; <?php echo e(date('Y')); ?> Computer Science Department, Nasarawa State University, Keffi<br>
            Powered by NSUK Biometric Attendance System
        </p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\lecturer\grading\pdf.blade.php ENDPATH**/ ?>