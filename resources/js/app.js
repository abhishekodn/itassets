import './bootstrap';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
Chart.defaults.font.family = "Figtree, ui-sans-serif, system-ui, sans-serif";
Chart.defaults.color = '#6b7280';
Chart.defaults.borderColor = '#f3f4f6';

function initCharts() {
    document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        if (canvas._chartInstance) {
            canvas._chartInstance.destroy();
        }

        const config = JSON.parse(canvas.dataset.chart);
        canvas._chartInstance = new Chart(canvas, config);
    });
}

document.addEventListener('DOMContentLoaded', initCharts);
document.addEventListener('livewire:navigated', initCharts);
