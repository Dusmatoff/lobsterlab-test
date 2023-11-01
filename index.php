<?php

require_once "config.php";
require_once "database.php";
require_once "CsvImporter.php";

$db = new Database($host, $username, $password, $database);
$csvImporter = new CsvImporter($db, $csvFile);

if ($db->isTableEmpty()) {
    echo "Таблица пустая.";
} else {
    $elapsedTime = $csvImporter->importData();
    echo "Данные успешно импортированы в базу.";
    echo "Время добавления данных: $elapsedTime секунд.";
}
