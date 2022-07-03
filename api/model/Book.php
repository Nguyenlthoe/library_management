<?php
require_once (ROOT. "/library/model.php");

class Book extends model{
    protected $id;
    protected $name;
    protected $category;
    protected $amount;
    protected $price;
    protected $description;
    protected $image;

    /**
     * @param $name
     * @param $category
     * @param $amount
     * @param $price
     * @param $description
     * @param $image
     */
    public function init($name, $category, $amount, $price, $description, $image){
        $this->name = $name;
        $this->category = $category;
        $this->amount = $amount;
        $this->price = $price;
        $this->description = $description;
        $this->image = $image;
    }

    /**
     * @param $connect
     * @param $listAuthor
     * @return void
     */
    public function insertNewBook($connect, $listAuthor){
        $inputBook = array($this->name, $this->amount, $this->category, $this->price, $this->description, $this->image);
        $bookId['bookId'] = $this->create($connect, $inputBook);
        foreach ($listAuthor as $item){
            $item->insertAuthor($connect, $bookId);
        }
        return $bookId;
    }

    /**
     * @param $id
     * @return void
     */
    public function setId($id){
        $this->id = $id;
    }
    public function getAuthor($connect){
        $sql = "SELECT name, information from own 
            inner join author on own.author_id = author.id
            where book_id = '{$this->id}';";
        $statement = $connect->prepare($sql);
        try {
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            return $statement->fetchAll();
        } catch (Exception $e){
            Response::returnResponse("500", "Get author failed!!");
            return false;
        }
    }

    /**
     * @param $connect
     * @param $keyword
     * @return void
     * Tìm kiếm sách bằng từ khóa
     */
    public function findBook($connect, $keyword){
        $sql = "SELECT * from {$this->table_name} where category like '%{$keyword}%'
                       or name like '%{$keyword}%'";
        $statement = $connect->prepare($sql);
        $books = null;
        try {
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $books = $statement->fetchAll();
            $sql = "SELECT * from book where id in 
                         (select book_id from own inner join author on own.author_id = author.id
                                where author.name like '%{$keyword}%');";
            $statement = $connect->prepare($sql);
            $statement->execute();
            $books = array_merge($books, $statement->fetchAll());
        } catch (Exception $e){
            Response::returnResponse(500, "some thing wrong");
        }
        return $books;
    }
}