<?php


namespace app\core;


class Database
{
    public \PDO $pdo;

    /**
     * Database constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';

        $this->pdo = new \PDO($dsn, $username, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    }

    public function prepare($sql, $data)
    {
        $statement = $this->pdo->prepare($sql);
        $statement = $this->bindValue($statement, $data);
        return $statement;
    }

    public function bindValue($statement, $data, $prefix = '')
    {
        if ($data) {
            foreach ($data as $field) {
                $statement->bindValue(":$prefix$field->name", $field->getDbValue());
            }
        }
        return $statement;
    }

    public function insertIntoTable(string $tableName, array $data, array $onDuplicateKeyUpdateColumns = [])
    {
        $columns = array_map(fn($k) => "$k->name", $data);
        $placeholders = array_map(fn($k) => ":$k->name", $data);
        $onDuplicateKeyStatement = count($onDuplicateKeyUpdateColumns) ? implode(", ", array_map(
            fn($k) => "$k->name=VALUES($k->name)", $onDuplicateKeyUpdateColumns)) : "id=id";
        $statement = $this->prepare("INSERT INTO $tableName (" . implode(", ", $columns) . ")
                                    VALUES (" . implode(", ", $placeholders) . ")
                                    ON DUPLICATE KEY UPDATE $onDuplicateKeyStatement", $data);
        $statement->execute();
    }

    public function selectFromTableSearchArray(string $tableName, array $searchQuery = null, array $columns = null, string $extra = "")
    {
        if ($columns && $columns[0]=="COUNT"){
            $columnKeys = ["COUNT(*) COUNT"];
        } else {
            $columnKeys = $columns ? array_map(fn($obj) => "$obj->name", $columns) : ["*"];
        }
        $searchQueryKeys = $searchQuery ? array_map(fn($obj) => "$obj->name=:$obj->name", $searchQuery) : ["1"];
        $statement = $this->prepare("SELECT " . implode(", ", $columnKeys) . " FROM $tableName 
                                    WHERE " . implode(" AND ", $searchQueryKeys) . " $extra", $searchQuery);
        $statement->execute();
        return $statement;
    }

    public function selectObject(string $tableName, array $searchQuery = null, array $columns = null, string $extra = "")
    {
        $statement = $this->selectFromTableSearchArray($tableName, $searchQuery, $columns, $extra);
        return $statement->fetchObject();
    }

    public function selectCount(string $tableName, array $searchQuery = null, string $extra = "")
    {
        $statement = $this->selectFromTableSearchArray($tableName, $searchQuery, ["COUNT"], $extra);
        return $statement->fetchObject();
    }

    public function selectResult(string $tableName, array $searchQuery = null, array $columns = null, string $extra = "")
    {
        $statement = $this->selectFromTableSearchArray($tableName, $searchQuery, $columns, $extra);
        return $statement->fetchAll();
    }

    public function updateTable(string $tableName, array $searchQuery = null, $data = null)
    {
        if ($data) {
            $setValues = array_map(fn($obj) => "$obj->name=:set_$obj->name", $data);
            $searchQueryKeys = $searchQuery ? array_map(fn($obj) => "$obj->name=:search_$obj->name", $searchQuery) : ["1"];
            $sql = "UPDATE $tableName SET " . implode(", ", $setValues) . " WHERE " . implode(" AND ", $searchQueryKeys);
            $statement = $this->pdo->prepare($sql);
            $statement = $this->bindValue($statement, $searchQuery, "search_");
            $statement = $this->bindValue($statement, $data, "set_");
            $statement->execute();

        }


    }

    public function deleteFromTable(string $tableName, array $searchQuery)
    {
        $searchQueryKeys = $searchQuery ? array_map(fn($obj) => "$obj->name=:$obj->name", $searchQuery) : ["1"];
        $statement = $this->prepare("DELETE FROM $tableName 
                                    WHERE " . implode(" AND ", $searchQueryKeys), $searchQuery);
        $statement->execute();
        return $statement;

    }
}