<?php
/*
 Структура таблицы

    CREATE TABLE products (
        id INT PRIMARY KEY,
        title VARCHAR(255),
        price DECIMAL(10, 2)
    );
 */

$config = include 'config.php';
$startTime = microtime(true);

try {
    $fileName = 'products.csv';
    $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['database'];

    $conn = new \PDO($dsn, $config['userName'], $config['password'], [
        \PDO::MYSQL_ATTR_LOCAL_INFILE => true,
    ]);
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    /*
     * Этот вариант подходит, если мы импортируем данные только первый раз.
     * При повторном импорте, обновить продукт не получится. Можно посмотреть второй вариант
     */
    $query = "LOAD DATA LOCAL INFILE '$fileName' 
              INTO TABLE products 
              FIELDS TERMINATED BY ',' 
              LINES TERMINATED BY '\n' 
              IGNORE 1 LINES 
              (id, @title, @price)
              SET title = TRIM(@title), price = TRIM(@price)";

    $conn->query($query);

    $endTime = microtime(true);

    $elapsedTime = $endTime - $startTime;

    echo "Время добавления данных: $elapsedTime секунд.";
} catch (\PDOException $e) {
    echo "Ошибка при импорте данных: " . $e->getMessage();
}
