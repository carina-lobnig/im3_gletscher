import { daten } from './script.js';

Chart.defaults.font.family = '"avenir", sans-serif';
Chart.defaults.font.size = 13;
Chart.defaults.font.weight = 400;
Chart.defaults.color = '#011518FF';

// passt  Beschriftungen an
// Parameter: chart = das Chart-Objekt, breite = aktuelle Breite des Charts in Pixel
function mobilAnpassen(chart, breite) {
    const klein = breite < 500;

    // --- LEGENDE (die Erklärung der Linien: "Temperaturänderung", "Gletscherfläche") ---

    // Schreibweise: bedingung ? wertWennWahr : wertWennFalsch
    // Auf kleinen Bildschirmen steht die Legende unten, sonst oben.
    // So nimmt sie dem Diagramm nicht die Höhe weg.
    chart.options.plugins.legend.position = klein ? 'bottom' : 'top';

    // Breite des farbigen Kästchens vor dem Legendentext:
    // 12 px auf dem Handy, 40 px auf grossen Bildschirmen
    chart.options.plugins.legend.labels.boxWidth = klein ? 12 : 40;

    // Schriftgrösse des Legendentextes: 11 px klein, 13 px gross
    chart.options.plugins.legend.labels.font = { size: klein ? 11 : 13 };

    // chart.options.scales enthält alle Achsen (x, y, y1).
    // Object.values(...) macht daraus eine Liste, forEach geht jede Achse einzeln durch.
    // "s" steht dabei jeweils für die aktuelle Achse.
    Object.values(chart.options.scales).forEach(s => {

        // Achsentitel (z. B. "Temperaturänderung (°C)") nur anzeigen, wenn NICHT klein.
        // "!klein" heisst "nicht klein". Die Prüfung "if (s.title)" ist nötig,
        // weil die x-Achse keinen Titel hat und sonst ein Fehler käme.
        if (s.title) s.title.display = !klein;

        // Schriftgrösse der Zahlen/Beschriftungen an der Achse (die "Ticks")
        s.ticks.font = { size: klein ? 10 : 13 };
    });

    // --- NUR DIE X-ACHSE (Jahreszahlen) ---

    // Maximale Drehung der Jahreszahlen in Grad.
    // 0 = waagrecht, 50 = bis zu 50° schräg erlaubt.
    // Auf dem Handy sollen sie gerade stehen, weil schräge Zahlen viel Höhe brauchen.
    chart.options.scales.x.ticks.maxRotation = klein ? 0 : 50;

    // Maximale Anzahl Jahreszahlen, die angezeigt werden.
    // Chart.js lässt dann automatisch Zahlen weg und zeigt nur 5 statt bis zu 10.
    chart.options.scales.x.ticks.maxTicksLimit = klein ? 5 : 10;
}

const data = {
    labels: daten.map(r => r.year),
    datasets: [
        {
            label: 'Temperaturänderung (°C)',
            data: daten.map(r => r.temperature_change_c),
            borderColor: 'rgb(236 189 146)',
            backgroundColor: 'rgb(215 134 17)',
            yAxisID: 'y',
        },
        {
            label: 'Glestcherfläche km2',
            data: daten.map(r => r.glacier_area_km2),
            borderColor: 'rgb(214 230 234)',
            backgroundColor: 'rgb(109 158 166)',
            yAxisID: 'y1',
        }
    ]
};

const config = {
    type: 'line',
    data: data,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        elements: { point: { radius: 0 } },              // keine Punkte, Linie wird lesbar
        onResize: (chart, size) => mobilAnpassen(chart, size.width),
        plugins: {
            legend: { labels: {} }
        },
        scales: {
            x: { ticks: {} },
            y: {
                type: 'linear',
                position: 'left',
                title: { display: true, text: 'Temperaturänderung (°C)' },
                ticks: {}
            },
            y1: {
                type: 'linear',
                position: 'right',
                title: { display: true, text: 'Gletscherfläche km2' },
                grid: { drawOnChartArea: false },
                ticks: {}
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
            label: 'Beitrag Gletscherschwund zum Meeresspiegelanstieg',
            // mm -> cm: durch 10 teilen, null bleibt null
            data: daten.map(r =>
                // Für jede Zeile (r) aus der Datenbank wird ein Wert berechnet.
                // Ist der Wert "null" (kein Messwert für dieses Jahr) ...
                r.mmsle_cumsum === null
                    ? null                  // ... bleibt er null, im Diagramm entsteht eine Lücke
                    : r.mmsle_cumsum / 10   // ... sonst: mm durch 10 teilen = cm
            ),
            borderColor: 'rgb(214 230 234)',
            backgroundColor: 'rgb(109 158 166)',
            yAxisID: 'y',
        },
        {
            label: 'Meeresspiegel',
            data: daten.map(r => r.sea_level),
            borderColor: 'rgb(34 113 141)',
            backgroundColor: 'rgb(21 100 128)',
            yAxisID: 'y',
        }
    ]
};

const config2 = {
    type: 'line',
    data: data2,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        elements: { point: { radius: 0 } },              // keine Punkte, Linie wird lesbar
        onResize: (chart, size) => mobilAnpassen(chart, size.width),
        plugins: {
            legend: { labels: {} }
        },
        scales: {
            x: { ticks: {} },
            y: {
                type: 'linear',
                position: 'left',
                title: { display: true, text: 'Temperaturänderung (°C)' },
                ticks: {}
            }
        }
    }
};

new Chart(document.getElementById('chart2'), config2);