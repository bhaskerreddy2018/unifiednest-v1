<?php
/**
 * Database Class
 * Uses Medoo as ORM
 */

// require_once BASE_PATH . '/vendor/Medoo.php';
require_once BASE_PATH . '/vendor/catfan/medoo/src/Medoo.php';
use Medoo\Medoo;

class Database {
    private static $instance = null;
    private $db;

    /**
     * Constructor - creates a new database connection using Medoo
     */
    private function __construct() {
        try {
            $this->db = new Medoo([
                'type' => 'mysql',
                'host' => DB_HOST,
                'database' => DB_NAME,
                'username' => DB_USER,
                'password' => DB_PASS,
                'charset' => DB_CHARSET,
                'collation' => 'utf8mb4_general_ci',
                'port' => 3306,
                'option' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            ]);
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    /**
     * Singleton pattern implementation
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get Medoo instance
     * @return Medoo
     */
    public function getConnection() {
        return $this->db;
    }

    /**
     * Select records from a table
     * @param string $table Table name
     * @param array|string $columns Column selection
     * @param array $where Where conditions
     * @return array
     */
    public function select($table, $columns = "*", $where = []) {
        return $this->db->select($table, $columns, $where);
    }

    /**
     * Get a single record
     * @param string $table Table name
     * @param array|string $columns Column selection
     * @param array $where Where conditions
     * @return array|null
     */
    public function get($table, $columns = "*", $where = []) {
        return $this->db->get($table, $columns, $where);
    }

    /**
     * Insert a record
     * @param string $table Table name
     * @param array $data Data to insert
     * @return int|boolean
     */
    public function insert($table, $data) {
        $this->db->insert($table, $data);
        return $this->db->id();
    }

    /**
     * Update records
     * @param string $table Table name
     * @param array $data Data to update
     * @param array $where Where conditions
     * @return int
     */
    public function update($table, $data, $where) {
        return $this->db->update($table, $data, $where);
    }

    /**
     * Delete records
     * @param string $table Table name
     * @param array $where Where conditions
     * @return int
     */
    public function delete($table, $where) {
        return $this->db->delete($table, $where);
    }

    /**
     * Count records
     * @param string $table Table name
     * @param array $where Where conditions
     * @return int
     */
    public function count($table, $where = []) {
        return $this->db->count($table, $where);
    }

    /**
     * Execute raw SQL queries
     * @param string $query SQL query
     * @param array $params Parameters
     * @return array|null
     */
    public function query($query, $params = []) {
        return $this->db->query($query, $params)->fetchAll();
    }

    /**
     * Begin a transaction
     */
    public function beginTransaction() {
        $this->db->pdo->beginTransaction();
    }

    /**
     * Commit a transaction
     */
    public function commit() {
        $this->db->pdo->commit();
    }

    /**
     * Rollback a transaction
     */
    public function rollBack() {
        $this->db->pdo->rollBack();
    }

    /**
     * Get the last inserted ID
     * @return int
     */
    public function lastInsertId() {
        return $this->db->id();
    }

    /**
     * Get debug information
     * @return array
     */
    public function debug() {
        return $this->db->debug();
    }

    /**
     * Check if records exist
     * @param string $table Table name
     * @param array $where Where conditions
     * @return bool
     */
    public function has($table, $where = []) {
        return $this->db->has($table, $where);
    }
} 