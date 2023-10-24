<?php
class DataBaseConn
{
    private $connection;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $this->connection = new mysqli('127.0.0.1', 'root', '', 'auth_db');

        if ($this->connection->connect_error) {
            die("Połączenie z bazą danych nie powiodło się: " . $this->connection->connect_error);
        }
    }

    public function select($table, $columns = "*", $where = "")
    {
        $query = "SELECT $columns FROM $table" . ($where ? " WHERE $where" : "");
        $result = $this->connection->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function insert($table, $data)
    {
        $columns = implode(", ", array_keys($data));
        $values = "'" . implode("', '", $data) . "'";
        $query = "INSERT INTO $table ($columns) VALUES ($values)";
        return $this->connection->query($query);
    }

    public function update($table, $data, $where)
    {
        $set = "";
        foreach ($data as $column => $value) {
            $set .= "$column = '$value',";
        }
        $set = rtrim($set, ',');
        $query = "UPDATE $table SET $set WHERE $where";
        return $this->connection->query($query);
    }

    public function delete($table, $where)
    {
        $query = "DELETE FROM $table WHERE $where";
        return $this->connection->query($query);
    }
}
?>