<?php

$config = include 'config.php';
require_once 'models/ProductModel.php';
require_once 'controllers/ProductController.php';
require_once 'views/ImportView.php';

$dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['database'];

try {
    $conn = new \PDO($dsn, $config['userName'], $config['password'], [
        \PDO::MYSQL_ATTR_LOCAL_INFILE => true,
    ]);
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $productModel = new ProductModel($conn);
    $productController = new ProductController($productModel);
    $view = new ImportView();

    $fileName = 'products.csv';
    $startTime = microtime(true);
    $success = $productController->importProducts($fileName);
    $endTime = microtime(true);
    $elapsedTime = $endTime - $startTime;

    $view->showImportResult($success, $elapsedTime);
} catch (\PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
}
