<?php

header('Content-Type: text/plain; charset=utf-8');

require __DIR__ . '/config.php'; // daten laden

$result = include __DIR__ . '/transform.php'; // holt ergebnis aus transform
$rows = $result['data'];

echo 'Der Transform liefert ' . count($rows) . " Zeilen.\n\n";

try { // verbindung zur datenbank mit pdo
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "Verbindung steht.\n\n";
} catch (PDOException $e) {
    exit('Verbindung fehlgeschlagen: ' . $e->getMessage() . "\n");
}

// damit alte stände gelöscht werden, damit nicht doppelt bei neu laden
$deleted = $pdo->exec('DELETE FROM klimadaten');
echo $deleted." alte Zeilen gelöscht.\n\n";

$insertYear = $pdo->prepare(
    'INSERT INTO klimadaten
     (year, temperature_change, glacier_area_km2, mmsle, mmsle_cumsum, sea_level)
     VALUES (:year, :temperature_change, :glacier_area_km2, :mmsle, :mmsle_cumsum, :sea_level)'
);

// alle zeilen werden geschrieben (55x in unserem fall)
foreach ($rows as $row) {
    $insertYear->execute([
        'year' => $row['year'],
        'temperature_change' => $row['temperature_change_c'],
        'glacier_area_km2' => $row['glacier_area_km2'],
        'mmsle' => $row['mmsle'],
        'mmsle_cumsum' => $row['mmsle_cumsum'],
        'sea_level' => $row['sea_level'],
    ]);
}

echo count($rows) . " Zeilen geschrieben.\n\n";

// prüft ob alle zeilen in db angekommen sind
$total = $pdo->query('SELECT COUNT(*) FROM klimadaten')->fetchColumn();
echo "In klimadaten stehen jetzt {$total} Zeilen.\n\n";

$check = $pdo->query( // kontrolle (stichprobe) aus verschiedenen jahren
    'SELECT year, temperature_change, glacier_area_km2, mmsle, mmsle_cumsum, sea_level
     FROM klimadaten
     WHERE year IN (1970, 1976, 1993, 2024)
     ORDER BY year'
);

echo "Jahr\tTemp °C\tGletscher km²\tmmsle\tmmsle kum.\tMeeresspiegel\n";

foreach ($check->fetchAll() as $year) { // pro jar eine zeile ausgeben
    echo $year['year'] . "\t"
        . $year['temperature_change'] . "\t"
        . ($year['glacier_area_km2'] ?? '–') . "\t" // zeigt - wenn der wert null ist
        . ($year['mmsle'] ?? '–') . "\t"
        . ($year['mmsle_cumsum'] ?? '–') . "\t"
        . ($year['sea_level'] ?? '–') . "\n";
}




