<?php

class Model{
    protected $table_name;

    public function __construct(){
        $this->table_name = strtolower(get_class($this));
    }

    /**
     * @param string $table_name
     */
    public function setTableName($table_name)
    {
        $this->table_name = $table_name;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * Tạo 1 đối tượng mới
     * @param $connect
     * @param $input
     * @return mixed|string
     */
    function create($connect, $input)
    {
        $params = array();
        foreach ($input as $key => $value) {
            if($value == null){
                $params[] = "DEFAULT";
            } else {
                $params[] = "'" . $value . "'";
            }
        }

        $sql = "INSERT INTO $this->table_name
                VALUES (NULL, " . implode(', ', $params) . ");";
        $statement = $connect->prepare($sql);
        try {
            $statement->execute();
        } catch (PDOException $e) {
            Response::returnResponse(500, $e->getMessage());
            exit();
        }

        return $connect->lastInsertId();
    }
    /**
     * Lấy thông tin từ 1 đối tượng dựa trên id
     * @param $connect
     * @param $id
     * @return array|string[]
     */
    function get($connect, $id)
    {
        $sql = "SELECT * FROM {$this->table_name} WHERE id=$id;";
        $statement = $connect->prepare($sql);

        try {
            $statement->execute();
        } catch (PDOException $e) {
            Response::returnResponse(500, $e->getMessage());
            exit();
        }

        return $statement->fetch(PDO::FETCH_ASSOC);
    }
    function find($connect, $condition){
        $sql = "SELECT * FROM $this->table_name WHERE {$condition};";
        $statement = $connect->prepare($sql);

        try {
            $statement->execute();
        } catch (PDOException $e) {
            return false;
        }
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * @param $connect
     * @param $id
     * @param $input
     * @return void
     */
    function update($connect, $id, $input)
    {
        $params = array();
        foreach ($input as $key => $value) {
            $params[] = "$key=:$key";
        }

        $sql = "UPDATE $this->table_name SET "
            . implode(', ', $params)
            . " WHERE id=$id;";

        $statement = $connect->prepare($sql);
        try {
            $statement->execute($input);
        } catch (PDOException $e) {
            Response::returnResponse(500, $e->getMessage());
            exit();
        }
    }

    /**
     * Xoá một đối tượng
     * @param $connect
     * @param $id
     * @return void
     */
    function delete($connect, $id)
    {
        $sql = "DELETE FROM $this->table_name WHERE id=$id;";
        $statement = $connect->prepare($sql);

        try {
            $statement->execute();
        } catch (PDOException $e) {
            Response::returnResponse(500, $e->getMessage());
            exit();
        }
        return true;
    }
}