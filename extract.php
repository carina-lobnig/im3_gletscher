<?php

$handle = fopen('data/IM3_Daten.csv', 'r');
// Kopfzeile = Spaltennamen
$header = array_map('trim', fgetcsv($handle, null, ',', '"', ''));
$attacks = [];
while (($row = fgetcsv($handle, null, ',', '"', '')) !== false) {
    if ($row[0] === '') {
        continue; // leere Zeile überspringen
    }
    $attacks[] = array_combine($header, $row);
}

fclose($handle);
return $attacks;

print_r($attacks);