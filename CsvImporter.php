<?php 

class CsvImporter
{
    private $db;
    private $csvFile;

    public function __construct($db, $csvFile)
    {
        $this->db = $db;
        $this->csvFile = $csvFile;
    }

    public function importData()
    {
        $startTime = microtime(true);
        $query = "LOAD DATA LOCAL INFILE '$this->csvFile' 
                  REPLACE INTO TABLE products 
                  FIELDS TERMINATED BY ',' 
                  LINES TERMINATED BY '\n' 
                  IGNORE 1 LINES 
                  (id, title, price)";
        $stmt = $this->db->getConnection()->query($query);
        $endTime = microtime(true);
        $elapsedTime = $endTime - $startTime;

        return $elapsedTime;
    }
}