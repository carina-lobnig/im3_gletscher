<?php

// damit inhalt als json gelesen / gespeichert wird
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/config.php';
function normalizeYear(array $row): array // zahlen aus text machen
{
    return [
        'year' => (int)$row['year'],
        'temperature_change_c' => (float)$row['temperature_change_c'],
        'glacier_area_km2' => $row['glacier_area_km2'] === null ? null : (float)$row['glacier_area_km2'],
        'mmsle' => $row['mmsle'] === null ? null : (float)$row['mmsle'],
        'mmsle_cumsum' => $row['mmsle_cumsum'] === null ? null : (float)$row['mmsle_cumsum'],
        'sea_level' => $row['sea_level'] === null ? null : (float)$row['sea_level'],
    ];
}
$from = trim($_GET['from'] ?? '');
$to = trim($_GET['to'] ?? '');

try { // mit db verbinden
    $pdo = new PDO($dsn, $username, $password, $options);

    // nur vereinbarte felder aus datenvertrag lesen
    $sql = 'SELECT year,
                   temperature_change AS temperature_change_c,
                   glacier_area_km2,
                   mmsle,
                   mmsle_cumsum,
                   sea_level
            FROM klimadaten';

    // filter anhängen (falls frontend braucht ;))
    $conditions = [];
    $params = [];

    if (ctype_digit($from)) {
        $conditions[] = 'year >= :from';
        $params['from'] = (int)$from;
    }
    if (ctype_digit($to)) {
        $conditions[] = 'year <= :to';
        $params['to'] = (int)$to;
    }
    if ($conditions !== []) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY year';

    // ausführen und als json ausgeben
    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    $rows = $statement->fetchAll();

    $data = array_map('normalizeYear', $rows);

    echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
} catch (Throwable $error) {

    // fehler werden im server-log ausgegeben
    http_response_code(500);
    error_log('unload.php: ' . $error->getMessage());

    // error wird angezeigt
    echo json_encode([
        'error' => 'Daten konnten nicht geladen werden.',
    ]);
}
