import { Chart, registerables } from 'chart.js';

// Register Chart.js components
Chart.register(...registerables);

export class AttendanceBarChart {
    constructor(config) {
        this.chartId = config.chartId;
        this.initialType = config.initialType || 'daily';
        this.height = config.height || 400;
        this.apiEndpoint = config.apiEndpoint;
        this.enableDrillDown = config.enableDrillDown || true;
        this.responsive = config.responsive !== false;
        
        this.chart = null;
        this.currentData = null;
        this.currentType = this.initialType;
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.loadChartData();
    }
    
    setupEventListeners() {
        // Chart type toggle buttons
        const typeButtons = document.querySelectorAll(`[data-chart-id="${this.chartId}"]`);
        typeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const newType = e.target.dataset.chartType;
                if (newType !== this.currentType) {
                    this.switchChartType(newType);
                }
            });
        });
        
        // Window resize handler for responsiveness
        if (this.responsive) {
            window.addEventListener('resize', this.debounce(() => {
                if (this.chart) {
                    this.chart.resize();
                }
            }, 250));
        }
    }
    
    async loadChartData(type = this.currentType) {
        try {
            this.showLoading();
            
            const response = await fetch(`${this.apiEndpoint}?type=${type}&chart=bar`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            this.currentData = data;
            this.renderChart(data);
            this.updateChartInfo(data);
            
        } catch (error) {
            console.error('Error loading chart data:', error);
            this.showError();
        }
    }
    
    renderChart(data) {
        const canvas = document.getElementById(this.chartId);
        const ctx = canvas.getContext('2d');
        
        // Destroy existing chart if it exists
        if (this.chart) {
            this.chart.destroy();
        }
        
        const chartConfig = this.getChartConfig(data);
        this.chart = new Chart(ctx, chartConfig);
        
        this.hideLoading();
        this.showChart();
    }
    
    getChartConfig(data) {
        const isMobile = window.innerWidth < 640;
        
        return {
            type: 'bar',
            data: {
                labels: data.labels || [],
                datasets: data.datasets || []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: isMobile ? 'bottom' : 'top',
                        labels: {
                            usePointStyle: true,
                            padding: isMobile ? 15 : 20,
                            font: {
                                size: isMobile ? 11 : 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            title: (context) => {
                                const label = context[0].label;
                                return this.currentType === 'daily' ? 
                                    `Date: ${label}` : 
                                    `Week: ${label}`;
                            },
                            label: (context) => {
                                const value = context.parsed.y;
                                const percentage = Math.round(value);
                                return `${context.dataset.label}: ${percentage}%`;
                            },
                            afterBody: (context) => {
                                if (context.length > 0) {
                                    const dataIndex = context[0].dataIndex;
                                    const additionalInfo = data.additionalInfo?.[dataIndex];
                                    if (additionalInfo) {
                                        return [
                                            '',
                                            `Total Students: ${additionalInfo.totalStudents}`,
                                            `Present: ${additionalInfo.present}`,
                                            `Absent: ${additionalInfo.absent}`
                                        ];
                                    }
                                }
                                return [];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: isMobile ? 10 : 12
                            },
                            maxRotation: isMobile ? 45 : 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            font: {
                                size: isMobile ? 10 : 12
                            },
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                onClick: (event, elements) => {
                    if (this.enableDrillDown && elements.length > 0) {
                        const element = elements[0];
                        const dataIndex = element.index;
                        this.handleChartClick(dataIndex);
                    }
                },
                onHover: (event, elements) => {
                    event.native.target.style.cursor = 
                        elements.length > 0 && this.enableDrillDown ? 'pointer' : 'default';
                }
            }
        };
    }
    
    switchChartType(newType) {
        // Update button states
        const buttons = document.querySelectorAll(`[data-chart-id="${this.chartId}"]`);
        buttons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.chartType === newType);
        });
        
        this.currentType = newType;
        this.loadChartData(newType);
    }
    
    handleChartClick(dataIndex) {
        if (!this.currentData || !this.currentData.drillDownData) {
            return;
        }
        
        const drillDownData = this.currentData.drillDownData[dataIndex];
        if (!drillDownData) {
            return;
        }
        
        this.showDrillDownModal(drillDownData);
    }
    
    showDrillDownModal(data) {
        const modal = document.getElementById(`${this.chartId}-modal`);
        const title = document.getElementById(`${this.chartId}-modal-title`);
        const content = document.getElementById(`${this.chartId}-modal-content`);
        
        title.textContent = `Detailed View - ${data.title}`;
        
        content.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Attendance Summary</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Students:</span>
                            <span class="font-medium">${data.totalStudents}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Present:</span>
                            <span class="font-medium text-green-600">${data.present}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Absent:</span>
                            <span class="font-medium text-red-600">${data.absent}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Late:</span>
                            <span class="font-medium text-yellow-600">${data.late || 0}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="text-gray-600">Attendance Rate:</span>
                            <span class="font-semibold text-blue-600">${Math.round(data.attendanceRate)}%</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Class Details</h4>
                    <div class="space-y-2">
                        ${data.subjects ? data.subjects.map(subject => `
                            <div class="flex justify-between">
                                <span class="text-gray-600">${subject.name}:</span>
                                <span class="font-medium">${Math.round(subject.attendanceRate)}%</span>
                            </div>
                        `).join('') : '<p class="text-gray-500">No subject details available</p>'}
                    </div>
                </div>
            </div>
            
            ${data.studentList ? `
                <div class="mt-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Student List</h4>
                    <div class="bg-white border rounded-lg overflow-hidden">
                        <div class="max-h-64 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    ${data.studentList.map(student => `
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">${student.name}</td>
                                            <td class="px-4 py-2 text-sm">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                    student.status === 'present' ? 'bg-green-100 text-green-800' :
                                                    student.status === 'late' ? 'bg-yellow-100 text-yellow-800' :
                                                    'bg-red-100 text-red-800'
                                                }">
                                                    ${student.status}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-500">${student.time || '-'}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            ` : ''}
        `;
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    updateChartInfo(data) {
        const infoContainer = document.getElementById(`${this.chartId}-info`);
        const totalSessions = document.getElementById(`${this.chartId}-total-sessions`);
        const avgAttendance = document.getElementById(`${this.chartId}-avg-attendance`);
        const trend = document.getElementById(`${this.chartId}-trend`);
        
        if (data.summary) {
            totalSessions.textContent = data.summary.totalSessions || '-';
            avgAttendance.textContent = data.summary.averageAttendance ? 
                `${Math.round(data.summary.averageAttendance)}%` : '-';
            
            const trendValue = data.summary.trend || 0;
            const trendText = trendValue > 0 ? `+${trendValue}%` : 
                            trendValue < 0 ? `${trendValue}%` : 'Stable';
            const trendColor = trendValue > 0 ? 'text-green-600' : 
                              trendValue < 0 ? 'text-red-600' : 'text-gray-600';
            
            trend.textContent = trendText;
            trend.className = `font-semibold ${trendColor}`;
            
            infoContainer.classList.remove('hidden');
        }
    }
    
    showLoading() {
        document.getElementById(`${this.chartId}-loading`).classList.remove('hidden');
        document.getElementById(`${this.chartId}-container`).classList.add('hidden');
        document.getElementById(`${this.chartId}-error`).classList.add('hidden');
        document.getElementById(`${this.chartId}-info`).classList.add('hidden');
    }
    
    hideLoading() {
        document.getElementById(`${this.chartId}-loading`).classList.add('hidden');
    }
    
    showChart() {
        document.getElementById(`${this.chartId}-container`).classList.remove('hidden');
    }
    
    showError() {
        document.getElementById(`${this.chartId}-loading`).classList.add('hidden');
        document.getElementById(`${this.chartId}-container`).classList.add('hidden');
        document.getElementById(`${this.chartId}-error`).classList.remove('hidden');
        document.getElementById(`${this.chartId}-info`).classList.add('hidden');
    }
    
    // Utility function for debouncing
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Public method to refresh chart data
    refresh() {
        this.loadChartData(this.currentType);
    }
    
    // Public method to destroy chart
    destroy() {
        if (this.chart) {
            this.chart.destroy();
            this.chart = null;
        }
    }
}

// Global functions for modal and retry functionality
window.closeChartModal = function(chartId) {
    const modal = document.getElementById(`${chartId}-modal`);
    modal.classList.add('hidden');
    document.body.style.overflow = '';
};

window.retryChartLoad = function(chartId) {
    if (window.attendanceCharts && window.attendanceCharts[chartId]) {
        window.attendanceCharts[chartId].refresh();
    }
};

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed') && event.target.classList.contains('inset-0')) {
        const chartId = event.target.id.replace('-modal', '');
        if (chartId) {
            window.closeChartModal(chartId);
        }
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const openModal = document.querySelector('[id$="-modal"]:not(.hidden)');
        if (openModal) {
            const chartId = openModal.id.replace('-modal', '');
            window.closeChartModal(chartId);
        }
    }
});