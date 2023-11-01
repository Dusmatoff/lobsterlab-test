<?php
class Database
{
    private $conn;

    public function __construct($host, $username, $password, $database)
    {
        try {
            $this->conn = new \PDO("mysql:host=$host;dbname=$database", $username, $password, array(
                \PDO::MYSQL_ATTR_LOCAL_INFILE => true,
            ));
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Ошибка подключения к базе данных: " . $e->getMessage();
            die();
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function isTableEmpty()
    {
        $query = "SELECT COUNT(*) FROM products";
        $stmt = $this->conn->query($query);
        $rowCount = $stmt->fetchColumn();
        return ($rowCount === 0);
    }
}
