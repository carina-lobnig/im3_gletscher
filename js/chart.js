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
            label: 'Glestcherfläche km2',
            data: daten.map(r => r.glacier_area_km2),
            borderColor: 'rgb(6 57 68)',
            backgroundColor: 'rgb(17 84 96)',
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
                title: { display: true, text: 'Glestcherfläche km2' },
                grid: { drawOnChartArea: false }
            }
        }
    }
};

new Chart(document.getElementById('chart'), config);



// Grafik 2
const data2 = {
    labels: daten.map(r => r.year),
    datasets: [
        {
            label: 'Gletscherfläche km2',
            data: daten.map(r => r.glacier_area_km2),
            borderColor: 'rgb(220, 60, 60)',
            backgroundColor: 'rgba(220, 60, 60, 0.5)',
            yAxisID: 'y',
        },
        {
            label: 'Meeresspiegel',
            data: daten.map(r => r.sea_level),
            borderColor: 'rgb(6 57 68)',
            backgroundColor: 'rgb(17 84 96)',
            yAxisID: 'y1',
        }
    ]
};

const config2 = {
    type: 'line',
    data: data2,
    options: {
        responsive: true,
        plugins: {
            title: { display: true, text: 'Gletscherfläche und Meeresspiegel' }
        },
        scales: {
            y: {
                title: { display: true, text: 'Fläche (km²)' }
            }
        }
    }
};

new Chart(document.getElementById('chart2'), config2);