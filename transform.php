<?php
$rawRows = include __DIR__ . '/extract.php';

$rawRows = include __DIR__ . '/extract.php';
var_dump($rawRows);
exit;

// Text aus der CSV -> Zahl oder null
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

$audit = [
    'input_rows' => 0,
    'invalid_rows' => 0,
    'duplicate_years' => 0,
    'missing_glacier_area' => 0,
    'missing_mmsle' => 0,
    'missing_mmsle_cumsum' => 0,
    'missing_sea_level' => 0,
    'output_rows' => 0,
];

$seenYears = [];
$transformedRows = [];

foreach ($rawRows as $raw) {
    $audit['input_rows']++;

    // Position statt der langen Spaltennamen aus der CSV
    $values = array_values($raw);

    $yearRaw = trim((string) ($values[0] ?? ''));
    $temperature = toNumberOrNull($values[1] ?? null);

    // Pflichtfelder prüfen: erst zählen, dann continue
    if (!is_numeric($yearRaw) || $temperature === null) {
        $audit['invalid_rows']++;
        continue;
    }
    $year = (int) $yearRaw;

    // Deduplizieren: jedes Jahr nur einmal
    if (isset($seenYears[$year])) {
        $audit['duplicate_years']++;
        continue;
    }
    $seenYears[$year] = true;

    // Umbenennen auf den Datenvertrag + Zahlen bereinigen
    $row = [
        'year' => $year,
        'temperature_change_c' => $temperature,
        'glacier_area_km2' => toNumberOrNull($values[2] ?? null),
        'mmsle' => toNumberOrNull($values[3] ?? null),
        'mmsle_cumsum' => toNumberOrNull($values[4] ?? null),
        'sea_level' => toNumberOrNull($values[5] ?? null),
    ];

    // Fehlende Werte ins Audit
    if ($row['glacier_area_km2'] === null) { $audit['missing_glacier_area']++; }
    if ($row['mmsle'] === null)            { $audit['missing_mmsle']++; }
    if ($row['mmsle_cumsum'] === null)     { $audit['missing_mmsle_cumsum']++; }
    if ($row['sea_level'] === null)        { $audit['missing_sea_level']++; }

    $transformedRows[] = $row;
}

// Sortieren nach Jahr
usort($transformedRows, function (array $a, array $b): int {
    return $a['year'] <=> $b['year'];
});

$audit['output_rows'] = count($transformedRows);

// Rückgabe ist ein PHP-Array, kein JSON
return [
    'question' => 'Wie wirkt sich die steigende Temperatur auf die Gletschermasse weltweit aus und welche Folgen hat dies auf den Meeresspiegel?',
    'data' => $transformedRows,
    'audit' => $audit,
];



