<?php

namespace App\Services;

class ChartDataService
{
    /**
     * Format course performance data for Chart.js
     */
    public function formatCoursePerformanceChart($data, $filters = [])
    {
        // If no data or empty data array, return empty chart config
        if (empty($data) || !is_array($data)) {
            return $this->getEmptyChartConfig('Course Performance Over Time');
        }
        
        $weeks = range(1, 14);
        $datasets = [];
        $colors = $this->generateColors(count($data));
        
        foreach ($data as $index => $course) {
            $dataset = [
                'label' => ($course['course_code'] ?? 'Unknown') . ' - ' . ($course['lecturer_name'] ?? 'Unknown'),
                'data' => array_fill(0, 14, 0), // Initialize with 0 for all weeks
                'backgroundColor' => $colors[$index]['bg'] ?? 'rgba(59, 130, 246, 0.8)',
                'borderColor' => $colors[$index]['border'] ?? 'rgba(59, 130, 246, 1)',
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.1
            ];
            
            // Fill in actual data for each week
            if (isset($course['weeks']) && is_array($course['weeks'])) {
            foreach ($course['weeks'] as $weekData) {
                    $weekIndex = ($weekData['week'] ?? 1) - 1; // Convert to 0-based index
                if ($weekIndex >= 0 && $weekIndex < 14) {
                        $dataset['data'][$weekIndex] = $weekData['attendance_rate'] ?? 0;
                    }
                }
            }
            
            $datasets[] = $dataset;
        }
        
        return [
            'type' => 'line',
            'data' => [
                'labels' => array_map(function($week) {
                    return 'Week ' . $week;
                }, $weeks),
                'datasets' => $datasets
            ],
            'options' => $this->getChartOptions('Course Performance Over Time', 'Weeks', 'Attendance Rate (%)')
        ];
    }

    /**
     * Format student attendance data for Chart.js
     */
    public function formatStudentAttendanceChart($data, $filters = [])
    {
        $weeks = range(1, 14);
        $datasets = [];
        $colors = $this->generateColors(count($data));
        
        foreach ($data as $index => $course) {
            $dataset = [
                'label' => ($course['course_code'] ?? 'Unknown') . ' (' . ($course['enrolled_students'] ?? 0) . ' students)',
                'data' => array_fill(0, 14, 0), // Initialize with 0 for all weeks
                'backgroundColor' => $colors[$index]['bg'] ?? 'rgba(59, 130, 246, 0.8)',
                'borderColor' => $colors[$index]['border'] ?? 'rgba(59, 130, 246, 1)',
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.1
            ];
            
            // Fill in actual data for each week
            if (isset($course['weeks']) && is_array($course['weeks'])) {
            foreach ($course['weeks'] as $weekData) {
                    $weekIndex = ($weekData['week'] ?? 1) - 1; // Convert to 0-based index
                if ($weekIndex >= 0 && $weekIndex < 14) {
                        $dataset['data'][$weekIndex] = $weekData['attendance_percentage'] ?? 0;
                    }
                }
            }
            
            $datasets[] = $dataset;
        }
        
        return [
            'type' => 'line',
            'data' => [
                'labels' => array_map(function($week) {
                    return 'Week ' . $week;
                }, $weeks),
                'datasets' => $datasets
            ],
            'options' => $this->getChartOptions('Student Attendance Over Time', 'Weeks', 'Attendance Percentage (%)')
        ];
    }

    /**
     * Format course attendance data as bar chart (X-axis = Courses, Y-axis = Attendance %)
     */
    public function formatCourseAttendanceBarChart($data, $filters = [])
    {
        // If no data or empty data array, return empty chart config
        if (empty($data) || !is_array($data)) {
            return $this->getEmptyBarChartConfig('Course Attendance');
        }
        
        $labels = [];
        $attendanceData = [];
        $backgroundColors = [];
        $borderColors = [];
        
        foreach ($data as $course) {
            $courseCode = $course['course_code'] ?? 'Unknown';
            $courseName = $course['course_name'] ?? '';
            $label = $courseCode;
            if ($courseName && $courseName !== $courseCode) {
                $label .= ' - ' . (strlen($courseName) > 20 ? substr($courseName, 0, 20) . '...' : $courseName);
            }
            
            $labels[] = $label;
            $attendancePercent = $course['attendance_percentage'] ?? 0;
            $attendanceData[] = round($attendancePercent, 2);
            
            // Color based on performance
            $color = $this->getPerformanceColor($attendancePercent);
            $backgroundColors[] = $color['background'];
            $borderColors[] = $color['border'];
        }
        
        $timePeriod = $filters['time_period'] ?? 'all';
        $week = $filters['week'] ?? null;
        $timePeriodLabel = $this->getTimePeriodLabel($timePeriod, $week);
        
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Attendance Percentage (%)',
                        'data' => $attendanceData,
                        'backgroundColor' => $backgroundColors,
                        'borderColor' => $borderColors,
                        'borderWidth' => 2,
                        'borderRadius' => 4,
                        'borderSkipped' => false,
                    ]
                ]
            ],
            'options' => $this->getModernBarChartOptions('Course Attendance - ' . $timePeriodLabel, 'Courses', 'Attendance Percentage (%)')
        ];
    }
    
    /**
     * Get time period label
     */
    private function getTimePeriodLabel($timePeriod, $week = null)
    {
        $label = '';
        switch ($timePeriod) {
            case 'live':
                $label = 'Live View (Today)';
                break;
            case 'semester':
                $label = 'Current Semester';
                break;
            case 'all':
            default:
                $label = 'All Time';
                break;
        }
        
        if ($week) {
            $label .= ' - Week ' . $week;
        }
        
        return $label;
    }

    /**
     * Format performance distribution pie chart
     */
    public function formatPerformanceDistributionChart($analysis)
    {
        // If no analysis data, return empty chart config
        if (empty($analysis) || !is_array($analysis)) {
            return $this->getEmptyPieChartConfig('Performance Distribution');
        }
        
        $distribution = $analysis['attendance_distribution'] ?? $analysis['performance_distribution'] ?? [];
        
        $labels = [];
        $data = [];
        $backgroundColors = [];
        $borderColors = [];
        
        $colorMap = [
            'excellent' => ['bg' => 'rgba(34, 197, 94, 0.8)', 'border' => 'rgba(34, 197, 94, 1)'],
            'good' => ['bg' => 'rgba(59, 130, 246, 0.8)', 'border' => 'rgba(59, 130, 246, 1)'],
            'average' => ['bg' => 'rgba(251, 191, 36, 0.8)', 'border' => 'rgba(251, 191, 36, 1)'],
            'poor' => ['bg' => 'rgba(249, 115, 22, 0.8)', 'border' => 'rgba(249, 115, 22, 1)'],
            'critical' => ['bg' => 'rgba(239, 68, 68, 0.8)', 'border' => 'rgba(239, 68, 68, 1)']
        ];
        
        foreach ($distribution as $category => $count) {
            if ($count > 0) {
                $labels[] = ucfirst($category);
                $data[] = $count;
                $backgroundColors[] = $colorMap[$category]['bg'];
                $borderColors[] = $colorMap[$category]['border'];
            }
        }
        
        return [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'data' => $data,
                        'backgroundColor' => $backgroundColors,
                        'borderColor' => $borderColors,
                        'borderWidth' => 2
                    ]
                ]
            ],
            'options' => $this->getPieChartOptions('Performance Distribution')
        ];
    }

    /**
     * Format risk analysis chart with enhanced features
     */
    public function formatRiskAnalysisChart($analysis)
    {
        // If no analysis data, return empty chart config
        if (empty($analysis) || !is_array($analysis)) {
            return $this->getEmptyPieChartConfig('Risk Analysis');
        }
        
        $riskData = $analysis['risk_analysis'] ?? [];
        $highRisk = $riskData['high_risk'] ?? 0;
        $mediumRisk = $riskData['medium_risk'] ?? 0;
        $lowRisk = $riskData['low_risk'] ?? 0;
        $total = $highRisk + $mediumRisk + $lowRisk;
        
        // Calculate percentages for labels
        $highRiskPercent = $total > 0 ? round(($highRisk / $total) * 100, 1) : 0;
        $mediumRiskPercent = $total > 0 ? round(($mediumRisk / $total) * 100, 1) : 0;
        $lowRiskPercent = $total > 0 ? round(($lowRisk / $total) * 100, 1) : 0;
        
        // Enhanced labels with counts and percentages
        $labels = [
            'High Risk (' . $highRisk . ' - ' . $highRiskPercent . '%)',
            'Medium Risk (' . $mediumRisk . ' - ' . $mediumRiskPercent . '%)',
            'Low Risk (' . $lowRisk . ' - ' . $lowRiskPercent . '%)'
        ];
        
        return [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'data' => [$highRisk, $mediumRisk, $lowRisk],
                        'backgroundColor' => [
                            'rgba(239, 68, 68, 0.9)',  // Enhanced opacity
                            'rgba(251, 191, 36, 0.9)',
                            'rgba(34, 197, 94, 0.9)'
                        ],
                        'borderColor' => [
                            'rgba(239, 68, 68, 1)',
                            'rgba(251, 191, 36, 1)',
                            'rgba(34, 197, 94, 1)'
                        ],
                        'borderWidth' => 3,
                        'hoverBorderWidth' => 4,
                        'hoverOffset' => 8
                    ]
                ]
            ],
            'options' => $this->getEnhancedPieChartOptions('Risk Analysis', $total)
        ];
    }

    /**
     * Format lecturer performance bar chart
     */
    public function formatLecturerPerformanceChart($data)
    {
        // If no data or empty data array, return empty chart config
        if (empty($data) || !is_array($data)) {
            return $this->getEmptyChartConfig('Lecturer Performance');
        }
        
        $labels = [];
        $attendanceData = [];
        $punctualityData = [];
        $backgroundColors = [];
        $borderColors = [];
        
        foreach ($data as $lecturer) {
            $labels[] = $lecturer['lecturer_name'];
            $attendanceData[] = $lecturer['average_attendance_rate'];
            $punctualityData[] = $lecturer['punctuality_score'];
            
            // Color based on performance
            $color = $this->getPerformanceColor($lecturer['average_attendance_rate']);
            $backgroundColors[] = $color['background'];
            $borderColors[] = $color['border'];
        }
        
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Attendance Rate (%)',
                        'data' => $attendanceData,
                        'backgroundColor' => $backgroundColors,
                        'borderColor' => $borderColors,
                        'borderWidth' => 1,
                        'yAxisID' => 'y'
                    ],
                    [
                        'label' => 'Punctuality Score (%)',
                        'data' => $punctualityData,
                        'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                        'borderColor' => 'rgba(59, 130, 246, 1)',
                        'borderWidth' => 1,
                        'yAxisID' => 'y1'
                    ]
                ]
            ],
            'options' => $this->getBarChartOptions('Lecturer Performance', 'Lecturers', 'Percentage (%)')
        ];
    }

    /**
     * Format student performance bar chart (with aggregated view for high volume)
     */
    public function formatStudentPerformanceChart($data, $filters = [])
    {
        $viewMode = $filters['performance_view_mode'] ?? 'aggregated';
        $studentCount = is_array($data) ? count($data) : 0;
        
        // Use aggregated view for high volume or when explicitly requested
        if ($viewMode === 'aggregated' || $studentCount > 50) {
            return $this->formatStudentPerformanceAggregated($data);
        }
        
        // Individual view for smaller datasets
        $labels = [];
        $attendanceData = [];
        $backgroundColors = [];
        $borderColors = [];
        $maxStudents = 100; // Limit for individual view
        
        $displayData = array_slice($data, 0, $maxStudents);
        
        foreach ($displayData as $student) {
            $labels[] = $student['matric_number'] ?? 'Unknown';
            $attendanceData[] = $student['average_attendance_rate'] ?? 0;
            
            // Color based on performance
            $color = $this->getPerformanceColor($student['average_attendance_rate'] ?? 0);
            $backgroundColors[] = $color['background'];
            $borderColors[] = $color['border'];
        }
        
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Attendance Rate (%)',
                        'data' => $attendanceData,
                        'backgroundColor' => $backgroundColors,
                        'borderColor' => $borderColors,
                        'borderWidth' => 1,
                        'borderRadius' => 2
                    ]
                ]
            ],
            'options' => $this->getBarChartOptions('Student Performance', 'Students', 'Attendance Rate (%)', $studentCount > $maxStudents)
        ];
    }
    
    /**
     * Format student performance as aggregated buckets
     */
    private function formatStudentPerformanceAggregated($data)
    {
        $buckets = [
            'Excellent (90-100%)' => 0,
            'Good (80-89%)' => 0,
            'Average (70-79%)' => 0,
            'Poor (60-69%)' => 0,
            'Critical (<60%)' => 0
        ];
        
        foreach ($data as $student) {
            $rate = $student['average_attendance_rate'] ?? 0;
            if ($rate >= 90) {
                $buckets['Excellent (90-100%)']++;
            } elseif ($rate >= 80) {
                $buckets['Good (80-89%)']++;
            } elseif ($rate >= 70) {
                $buckets['Average (70-79%)']++;
            } elseif ($rate >= 60) {
                $buckets['Poor (60-69%)']++;
            } else {
                $buckets['Critical (<60%)']++;
            }
        }
        
        $labels = array_keys($buckets);
        $values = array_values($buckets);
        $colors = [
            ['bg' => 'rgba(34, 197, 94, 0.8)', 'border' => 'rgba(34, 197, 94, 1)'],
            ['bg' => 'rgba(59, 130, 246, 0.8)', 'border' => 'rgba(59, 130, 246, 1)'],
            ['bg' => 'rgba(251, 191, 36, 0.8)', 'border' => 'rgba(251, 191, 36, 1)'],
            ['bg' => 'rgba(249, 115, 22, 0.8)', 'border' => 'rgba(249, 115, 22, 1)'],
            ['bg' => 'rgba(239, 68, 68, 0.8)', 'border' => 'rgba(239, 68, 68, 1)']
        ];
        
        $backgroundColors = array_column($colors, 'bg');
        $borderColors = array_column($colors, 'border');
        
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Number of Students',
                        'data' => $values,
                        'backgroundColor' => $backgroundColors,
                        'borderColor' => $borderColors,
                        'borderWidth' => 2,
                        'borderRadius' => 4
                    ]
                ]
            ],
            'options' => $this->getBarChartOptions('Student Performance (Aggregated)', 'Performance Range', 'Number of Students')
        ];
    }

    /**
     * Generate colors for datasets
     */
    private function generateColors($count)
    {
        $colors = [];
        $baseColors = [
            ['bg' => 'rgba(59, 130, 246, 0.8)', 'border' => 'rgba(59, 130, 246, 1)'],
            ['bg' => 'rgba(16, 185, 129, 0.8)', 'border' => 'rgba(16, 185, 129, 1)'],
            ['bg' => 'rgba(245, 158, 11, 0.8)', 'border' => 'rgba(245, 158, 11, 1)'],
            ['bg' => 'rgba(239, 68, 68, 0.8)', 'border' => 'rgba(239, 68, 68, 1)'],
            ['bg' => 'rgba(139, 92, 246, 0.8)', 'border' => 'rgba(139, 92, 246, 1)'],
            ['bg' => 'rgba(236, 72, 153, 0.8)', 'border' => 'rgba(236, 72, 153, 1)'],
            ['bg' => 'rgba(6, 182, 212, 0.8)', 'border' => 'rgba(6, 182, 212, 1)'],
            ['bg' => 'rgba(34, 197, 94, 0.8)', 'border' => 'rgba(34, 197, 94, 1)']
        ];
        
        for ($i = 0; $i < $count; $i++) {
            $colors[] = $baseColors[$i % count($baseColors)];
        }
        
        return $colors;
    }

    /**
     * Get performance color based on rating
     */
    private function getPerformanceColor($rating)
    {
        if ($rating >= 90) {
            return ['background' => 'rgba(34, 197, 94, 0.8)', 'border' => 'rgba(34, 197, 94, 1)'];
        } elseif ($rating >= 80) {
            return ['background' => 'rgba(59, 130, 246, 0.8)', 'border' => 'rgba(59, 130, 246, 1)'];
        } elseif ($rating >= 70) {
            return ['background' => 'rgba(251, 191, 36, 0.8)', 'border' => 'rgba(251, 191, 36, 1)'];
        } elseif ($rating >= 60) {
            return ['background' => 'rgba(249, 115, 22, 0.8)', 'border' => 'rgba(249, 115, 22, 1)'];
        } else {
            return ['background' => 'rgba(239, 68, 68, 0.8)', 'border' => 'rgba(239, 68, 68, 1)'];
        }
    }

    /**
     * Get chart options for line charts
     */
    private function getChartOptions($title, $xLabel, $yLabel)
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => $title,
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold'
                    ]
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'top'
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false
                ]
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => $xLabel
                    ]
                ],
                'y' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => $yLabel
                    ],
                    'min' => 0,
                    'max' => 100
                ]
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false
            ]
        ];
    }

    /**
     * Get chart options for bar charts
     */
    private function getBarChartOptions($title, $xLabel, $yLabel, $truncated = false)
    {
        $options = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'animation' => [
                'duration' => 1000,
                'easing' => 'easeOutQuart'
            ],
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => $title . ($truncated ? ' (Showing first 100 students)' : ''),
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold'
                    ],
                    'padding' => 20
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 15
                    ]
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'padding' => 12,
                    'titleFont' => [
                        'size' => 14,
                        'weight' => 'bold'
                    ],
                    'bodyFont' => [
                        'size' => 13
                    ],
                    'callbacks' => []
                ]
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => $xLabel,
                        'font' => [
                            'size' => 13,
                            'weight' => 'bold'
                        ]
                    ],
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 45,
                        'font' => [
                            'size' => 11
                        ]
                    ],
                    'grid' => [
                        'display' => true,
                        'color' => 'rgba(0, 0, 0, 0.05)'
                    ]
                ],
                'y' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => $yLabel,
                        'font' => [
                            'size' => 13,
                            'weight' => 'bold'
                        ]
                    ],
                    'min' => 0,
                    'max' => strpos($yLabel, 'Number') !== false ? null : 100,
                    'ticks' => [
                        'stepSize' => 10
                    ],
                    'grid' => [
                        'display' => true,
                        'color' => 'rgba(0, 0, 0, 0.05)'
                    ]
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Punctuality Score (%)'
                    ],
                    'min' => 0,
                    'max' => 100,
                    'grid' => [
                        'drawOnChartArea' => false
                    ]
                ]
            ],
            'interaction' => [
                'mode' => 'index',
                'intersect' => false
            ]
        ];
        
        // Remove y1 scale if not needed
        if (strpos($yLabel, 'Number') === false) {
            unset($options['scales']['y1']);
        }
        
        return $options;
    }
    
    /**
     * Get modern bar chart options with enhanced styling
     */
    private function getModernBarChartOptions($title, $xLabel, $yLabel)
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'animation' => [
                'duration' => 1200,
                'easing' => 'easeOutQuart'
            ],
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => $title,
                    'font' => [
                        'size' => 18,
                        'weight' => 'bold',
                        'family' => "'Inter', 'Segoe UI', sans-serif"
                    ],
                    'padding' => 20,
                    'color' => '#1F2937'
                ],
                'legend' => [
                    'display' => false
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(17, 24, 39, 0.9)',
                    'titleColor' => '#fff',
                    'bodyColor' => '#fff',
                    'padding' => 14,
                    'titleFont' => [
                        'size' => 14,
                        'weight' => 'bold'
                    ],
                    'bodyFont' => [
                        'size' => 13
                    ],
                    'borderColor' => 'rgba(255, 255, 255, 0.1)',
                    'borderWidth' => 1,
                    'cornerRadius' => 8,
                    'callbacks' => []
                ]
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => $xLabel,
                        'font' => [
                            'size' => 14,
                            'weight' => 'bold'
                        ],
                        'color' => '#4B5563'
                    ],
                    'ticks' => [
                        'maxRotation' => 60,
                        'minRotation' => 45,
                        'font' => [
                            'size' => 11
                        ],
                        'color' => '#6B7280'
                    ],
                    'grid' => [
                        'display' => false
                    ]
                ],
                'y' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => $yLabel,
                        'font' => [
                            'size' => 14,
                            'weight' => 'bold'
                        ],
                        'color' => '#4B5563'
                    ],
                    'min' => 0,
                    'max' => 100,
                    'ticks' => [
                        'stepSize' => 10,
                        'font' => [
                            'size' => 11
                        ],
                        'color' => '#6B7280'
                    ],
                    'grid' => [
                        'display' => true,
                        'color' => 'rgba(0, 0, 0, 0.05)',
                        'drawBorder' => false
                    ]
                ]
            ],
            'interaction' => [
                'mode' => 'index',
                'intersect' => false
            ]
        ];
    }

    /**
     * Get chart options for pie charts
     */
    private function getPieChartOptions($title)
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => $title,
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold'
                    ]
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'right'
                ]
            ]
        ];
    }
    
    /**
     * Get enhanced pie chart options with improved tooltips and interactions
     */
    private function getEnhancedPieChartOptions($title, $total)
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true,
                'duration' => 1500,
                'easing' => 'easeOutQuart'
            ],
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => $title . ' (Total: ' . $total . ' students)',
                    'font' => [
                        'size' => 18,
                        'weight' => 'bold',
                        'family' => "'Inter', 'Segoe UI', sans-serif"
                    ],
                    'padding' => 20,
                    'color' => '#1F2937'
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 15,
                        'font' => [
                            'size' => 12
                        ],
                        'color' => '#374151'
                    ]
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(17, 24, 39, 0.95)',
                    'titleColor' => '#fff',
                    'bodyColor' => '#fff',
                    'padding' => 14,
                    'titleFont' => [
                        'size' => 14,
                        'weight' => 'bold'
                    ],
                    'bodyFont' => [
                        'size' => 13
                    ],
                    'borderColor' => 'rgba(255, 255, 255, 0.1)',
                    'borderWidth' => 1,
                    'cornerRadius' => 8,
                    'callbacks' => []
                ],
            ]
        ];
    }

    /**
     * Get empty chart configuration for when there's no data
     */
    private function getEmptyChartConfig($title)
    {
        return [
            'type' => 'line',
            'data' => [
                'labels' => ['No Data Available'],
                'datasets' => [
                    [
                        'label' => 'No Data',
                        'data' => [0],
                        'backgroundColor' => 'rgba(200, 200, 200, 0.8)',
                        'borderColor' => 'rgba(200, 200, 200, 1)',
                        'borderWidth' => 2
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => $title . ' - No Data Available',
                        'font' => [
                            'size' => 16,
                            'weight' => 'bold'
                        ]
                    ],
                    'legend' => [
                        'display' => false
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Get empty bar chart configuration for when there's no data
     */
    private function getEmptyBarChartConfig($title)
    {
        return [
            'type' => 'bar',
            'data' => [
                'labels' => ['No Data Available'],
                'datasets' => [
                    [
                        'label' => 'No Data',
                        'data' => [0],
                        'backgroundColor' => 'rgba(200, 200, 200, 0.8)',
                        'borderColor' => 'rgba(200, 200, 200, 1)',
                        'borderWidth' => 2
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => $title . ' - No Data Available',
                        'font' => [
                            'size' => 16,
                            'weight' => 'bold'
                        ]
                    ],
                    'legend' => [
                        'display' => false
                    ]
                ]
            ]
        ];
    }

    /**
     * Get empty pie chart configuration for when there's no data
     */
    private function getEmptyPieChartConfig($title)
    {
        return [
            'type' => 'pie',
            'data' => [
                'labels' => ['No Data Available'],
                'datasets' => [
                    [
                        'data' => [1],
                        'backgroundColor' => ['rgba(200, 200, 200, 0.8)'],
                        'borderColor' => ['rgba(200, 200, 200, 1)'],
                        'borderWidth' => 2
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => $title . ' - No Data Available',
                        'font' => [
                            'size' => 16,
                            'weight' => 'bold'
                        ]
                    ],
                    'legend' => [
                        'display' => false
                    ]
                ]
            ]
        ];
    }
}











