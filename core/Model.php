<?php 

namespace Core;

use Core\Database;
use PDO;

abstract class Model {
    protected static $table;
    protected $connection;

    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    public static function get()
    {
        $instance = new static();
        $query = "SELECT * FROM " . static::$table;
        $stmt = $instance->connection->query($query);
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    // Insert a new record
    public function insert()
    {
        $properties = get_object_vars($this);
        unset($properties['connection']); // Remove connection property

        $columns = implode(", ", array_keys($properties));
        $placeholders = ":" . implode(", :", array_keys($properties));
        $query = "INSERT INTO " . static::$table . " ($columns) VALUES ($placeholders)";

        $stmt = $this->connection->prepare($query);

        foreach ($properties as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    // Update an existing record
    public function update($primaryKey)
    {
        $properties = get_object_vars($this);
        unset($properties['connection']); // Remove connection property

        $setClause = [];
        foreach ($properties as $key => $value) {
            $setClause[] = "$key = :$key";
        }
        $setClauseString = implode(", ", $setClause);

        $query = "UPDATE " . static::$table . " SET $setClauseString WHERE id = :id";
        $stmt = $this->connection->prepare($query);

        foreach ($properties as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->bindValue(":id", $primaryKey);

        return $stmt->execute();
    }

    // Delete a record
    public function delete($primaryKey)
    {
        $query = "DELETE FROM " . static::$table . " WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(":id", $primaryKey);

        return $stmt->execute();
    }

    public static function find($id)
    {
        $instance = new static();
        $stmt = $instance->connection->prepare("SELECT * FROM " . static::$table . " WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchObject(static::class);
    }
}
