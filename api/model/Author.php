<?php

class Author extends model{
    protected $name;
    protected $information;

    /**
     * @param $name
     * @param $infomation
     */
    public function __construct($name, $information)
    {
        $this->table_name = strtolower(get_class($this));
        $this->name = $name;
        $this->information = $information;
    }
    public function insertAuthor($connect, $bookId){
        $condition = "name = '{$this->name}' and information = '{$this->information}'";
        $authorId = $this->find($connect, $condition);
        if(!$authorId){
            $input = array($this->name, $this->information);
            $authorId = $this->create($connect, $input);
        } else {
            $authorId = $authorId['id'];
        }
        $sql = "INSERT INTO own VALUES (" . $authorId . ", " . $bookId['bookId'] .");";
        $statement = $connect->prepare($sql);
        try {
            $statement->execute();
        } catch (PDOException $e) {
            Response::returnResponse(500, $e->getMessage());
            exit();
        }
    }
}