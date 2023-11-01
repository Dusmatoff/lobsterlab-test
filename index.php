<?php
/*
 Структура таблицы

    CREATE TABLE products (
        id INT PRIMARY KEY,
        title VARCHAR(255),
        price DECIMAL(10, 2)
    );
 */
$startTime = microtime(true);

$host = 'localhost';
$userName = 'root';
$password = 'root';
$database = 'lobsterlab';

try {
    $fileName = 'products.csv';

    $conn = new \PDO("mysql:host=$host;dbname=$database", $userName, $password, array(
        \PDO::MYSQL_ATTR_LOCAL_INFILE => true,
    ));
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $query = "LOAD DATA LOCAL INFILE '$fileName' 
              INTO TABLE products 
              FIELDS TERMINATED BY ',' 
              LINES TERMINATED BY '\n' 
              IGNORE 1 LINES 
              (id, title, price)";

    $stmt = $conn->query($query);

    $endTime = microtime(true);

    $elapsedTime = $endTime - $startTime;

    echo "Время добавления данных: $elapsedTime секунд.";
} catch (\PDOException $e) {
    if ($e->getCode() == '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
        echo "Дубликаты данных не добавлены.";
    } else {
        echo "Ошибка при импорте данных: " . $e->getMessage();
    }
}
