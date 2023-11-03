<?php

class ProductModel
{
    private $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function importProductsFromCSV(string $fileName)
    {
        try {
            // Открываем транзакцию
            $this->conn->beginTransaction();

            // Получаем названия столбцов из CSV-файла
            $columns = $this->getColumnsFromCSV($fileName);

            // Подготавливаем динамический SQL-запрос
            $columnNames = implode(', ', $columns);
            $placeholders = implode(', ', array_fill(0, count($columns), '?'));

            /* Множественная вставка
             * Если написать INSERT INTO, может возникнуть ошибка, если такой id уже есть. Поэтому используется REPLACE INTO
             */
            $sql = "REPLACE INTO products ($columnNames) VALUES ($placeholders)";
            $stmt = $this->conn->prepare($sql);

            foreach ($this->readDataFromCSV($fileName) as $data) {
                if ($data[0] !== 'id') {
                    $stmt->execute($data);
                }
            }

            // Подтверждаем транзакцию
            $this->conn->commit();

            return true;
        } catch (\PDOException $e) {
            // Если возникает ошибка, откатываем транзакцию
            $this->conn->rollBack();
            echo "Ошибка: " . $e->getMessage();
            return $e->getMessage();
        }
    }

    public function readDataFromCSV(string $fileName)
    {
        $handle = fopen($fileName, 'r');

        while (($data = fgetcsv($handle, 1000)) !== false) {
            yield $data;
        }

        fclose($handle);
    }

    public function getColumnsFromCSV(string $fileName)
    {
        $fileData = file($fileName,FILE_SKIP_EMPTY_LINES);
        if (!$fileData) {
            throw new \Exception('CSV file is empty');
        }

        $csv = array_map('str_getcsv', $fileData);
        return array_shift($csv);
    }
}
