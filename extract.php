<?php

// Kopfzeile lesen
$handle = fopen(__DIR__ . '/data/IM3_Daten.csv', 'r'); // damit php das csv immer findet

$header = fgetcsv($handle, null, ';', '"', ''); // damit zeile um zeile gelesen wird
$header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]); // BOM entfernen
$header = array_map('trim', $header); // entfernt Leerzeichen um Spaltennamen

// Zeilen lesen
$rawData = []; // leere Liste, wird noch gefüllt
while (($row = fgetcsv($handle, null, ';', '"', '')) !== false) { // liest zeile für zeile, bis ans ende
    if ($row[0] === '') {
        continue; // leere Zeile überspringen
    }
    $rawData[] = array_combine($header, $row); // datensatz pro zeile erstellen
}
fclose($handle); // schliesst datei wieder

return $rawData;