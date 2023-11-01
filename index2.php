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

    //Создаём временную таблицу
    $conn->query('CREATE TEMPORARY TABLE temporary_products LIKE products;');

    //Импортируем данные в временную таблицу
    $conn->query(
        "LOAD DATA LOCAL INFILE '$fileName' 
              INTO TABLE temporary_products 
              FIELDS TERMINATED BY ',' 
              LINES TERMINATED BY '\n' 
              IGNORE 1 LINES 
              (id, title, price);"
    );

    //Копируем данные в рабочую таблицу из временной таблицы
    $conn->query(
        "SHOW COLUMNS FROM products;
            INSERT INTO products
            SELECT * FROM temporary_products
            ON DUPLICATE KEY UPDATE title = VALUES(title), price = VALUES(price);"
    );

    //Удаляем временную таблицу
    //$conn->query('DROP TEMPORARY TABLE temporary_products;');

    $endTime = microtime(true);

    $elapsedTime = $endTime - $startTime;

    echo "Время добавления данных: $elapsedTime секунд.";
} catch (\PDOException $e) {
    echo "Ошибка при импорте данных: " . $e->getMessage();
}
