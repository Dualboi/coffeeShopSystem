<?php
// Class to manage the database connection
class Database
{
    private static $instance = null;
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "coffee_shop_system";
    public $conn;

      // Private constructor to prevent instantiation from outside
    private function __construct() {
        // Create connection
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Public method to get the instance of the database connection
    public static function getInstance() {
        // Check if an instance already exists
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    // Method to return the connection object
    public function getConnection() {
        return $this->conn;
    }
}
?>