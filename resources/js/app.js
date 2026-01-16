import './bootstrap';

// Import Chart.js for attendance monitoring charts
import { Chart, registerables } from 'chart.js';

// Register Chart.js components globally
Chart.register(...registerables);

// Make Chart available globally for components that need it
window.Chart = Chart;

// Import and make AttendanceBarChart available globally
import { AttendanceBarChart } from './components/attendance-bar-chart.js';
window.AttendanceBarChart = AttendanceBarChart;
