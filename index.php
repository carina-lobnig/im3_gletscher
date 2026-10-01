<?php
$result = include __DIR__ . '/transform.php';

header('Content-Type: text/plain; charset=utf-8');

echo $result['question'] . "\n\n";

echo "AUDIT\n";
print_r($result['audit']);

echo "\nDATEN\n";
print_r($result['data']);