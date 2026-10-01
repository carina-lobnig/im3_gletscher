<?php

$rawRows = include __DIR__ . '/extract.php';


function toNumberOrNull($value): ?float
{
    $value = trim((string) $value);
    if ($value === '' || strtolower($value) === 'null') {
        return null;                       // «wir wissen es nicht», nicht 0
    }
    $value = str_replace(',', '.', $value); // Dezimalkomma -> Punkt
    if (!is_numeric($value)) {
        return null;
    }
    return (float) $value;
}

$audit = [ // zähler fürs protokoll starten (bei 0)
    'input_rows' => 0,
    'invalid_rows' => 0,
    'duplicate_years' => 0,
    'missing_glacier_area' => 0,
    'missing_mmsle' => 0,
    'missing_mmsle_cumsum' => 0,
    'missing_sea_level' => 0,
];

$seenYears = [];
$transformedRows = []; // hier werden bereinigte zahlen abgespeichert

foreach ($rawRows as $raw) { // jede zeile wird gezählt
    $audit['input_rows']++;

    $values = array_values($raw);

    $yearRaw = trim((string) ($values[0] ?? ''));
    $temperature = toNumberOrNull($values[1] ?? null);

    if (!is_numeric($yearRaw) || $temperature === null) { // zeilen ohne jahr und temp werden aussortiert
        $audit['invalid_rows']++;
        continue;
    }
    $year = (int) $yearRaw;

    if (isset($seenYears[$year])) { // jedes jahr wirkich nur einmal übernehmen
        $audit['duplicate_years']++;
        continue;
    }
    $seenYears[$year] = true;

    $row = [ // umbenennen und bereinigen
        'year' => $year,
        'temperature_change_c' => $temperature,
        'glacier_area_km2' => toNumberOrNull($values[2] ?? null),
        'mmsle' => toNumberOrNull($values[3] ?? null),
        'mmsle_cumsum' => toNumberOrNull($values[4] ?? null),
        'sea_level' => toNumberOrNull($values[5] ?? null),
    ];

// fehlende werte zählen
    if ($row['glacier_area_km2'] === null) { $audit['missing_glacier_area']++; }
    if ($row['mmsle'] === null)            { $audit['missing_mmsle']++; }
    if ($row['mmsle_cumsum'] === null)     { $audit['missing_mmsle_cumsum']++; }
    if ($row['sea_level'] === null)        { $audit['missing_sea_level']++; }

    $transformedRows[] = $row;
}

// nach jahr aufsteigend sortieren
usort($transformedRows, function (array $a, array $b): int {
    return $a['year'] <=> $b['year'];
});

$audit['output_rows'] = count($transformedRows);

// ergebnisse zurpckgeben
return [
    'question' => 'Wie wirkt sich die steigende Temperatur auf die Gletschermasse weltweit aus und welche Folgen hat dies auf den Meeresspiegel?',
    'data' => $transformedRows,
    'audit' => $audit,
];



