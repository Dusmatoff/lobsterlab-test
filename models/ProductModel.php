<?php

class ProductModel {
    private $conn;

    public function __construct(\PDO $conn) {
        $this->conn = $conn;
    }

    public function importProductsFromCSV($fileName) {
        try {
            $query = "LOAD DATA LOCAL INFILE '$fileName' 
                      INTO TABLE products 
                      FIELDS TERMINATED BY ',' 
                      LINES TERMINATED BY '\n' 
                      IGNORE 1 LINES 
                      (id, @title, @price)
                      SET title = TRIM(@title), price = TRIM(@price)";

            $this->conn->query($query);

            //Создаём временную таблицу
            $this->conn->query('CREATE TEMPORARY TABLE temporary_products LIKE products;');

            //Импортируем данные в временную таблицу
            $this->conn->query(
                "LOAD DATA LOCAL INFILE '$fileName' 
              INTO TABLE temporary_products 
              FIELDS TERMINATED BY ',' 
              LINES TERMINATED BY '\n' 
              IGNORE 1 LINES 
              (id, title, price)"
            );

            //Копируем данные в рабочую таблицу из временной таблицы
            $this->conn->query(
                "SHOW COLUMNS FROM products;
            INSERT INTO products
            SELECT * FROM temporary_products
            ON DUPLICATE KEY UPDATE title = TRIM(VALUES(title)), price = TRIM(VALUES(price));"
            );

            //Удаляем временную таблицу
            $this->conn->query('DROP TEMPORARY TABLE temporary_products;');

            return true;
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }
}
