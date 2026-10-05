import { daten } from './script.js';

const data = {
    labels: daten.map(r => r.year),
    datasets: [
        {
            label: 'Temperaturänderung (°C)',
            data: daten.map(r => r.temperature_change_c),
            borderColor: 'rgb(220, 60, 60)',
            backgroundColor: 'rgba(220, 60, 60, 0.5)',
            yAxisID: 'y',
        },
        {
            label: 'Meeresspiegel (mm)',
            data: daten.map(r => r.sea_level),
            borderColor: 'rgb(40, 110, 200)',
            backgroundColor: 'rgba(40, 110, 200, 0.5)',
            yAxisID: 'y1',
        }
    ]
};

const config = {
    type: 'line',
    data: data,
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            title: { display: true, text: 'Temperatur und Meeresspiegel 1970–2024' }
        },
        scales: {
            y: {
                type: 'linear',
                position: 'left',
                title: { display: true, text: 'Temperaturänderung (°C)' }
            },
            y1: {
                type: 'linear',
                position: 'right',
                title: { display: true, text: 'Meeresspiegel (mm)' },
                grid: { drawOnChartArea: false }
            }
        }
    }
};

new Chart(document.getElementById('chart'), config);