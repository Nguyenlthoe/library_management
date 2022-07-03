<?php

class OrderBook extends model{
    protected $id;
    protected $readerId;
    protected $createdAt;
    protected $expiryAt;
    protected $status;

    /**
     * @param $readerId
     * @param $createAt
     * @param $ExpiryAt
     * @param $status
     */
    public function __construct($readerId, $createdAt, $expiryAt, $status)
    {
        $this->table_name = strtolower(get_class($this));
        $this->readerId = $readerId;
        $this->createdAt = $createdAt;
        $this->expiryAt = $expiryAt;
        $this->status = $status;
    }
    public function insertBook($connect, $listBookId){
        $this->id = $this->create($connect, array($this->createdAt, $this->expiryAt,
            $this->readerId, $this->status));
        $sql = "INSERT INTO listbookorder VALUES ('{$this->id}', :bookId);";
        $statement = $connect->prepare($sql);
        try {
            foreach ($listBookId as $item){
                $statement->bindParam(':bookId', $item);
                $statement->execute();
            }
            $sql = "UPDATE `book` set `amount` = amount - 1 where id = :id;";
            $statement = $connect->prepare($sql);
            foreach ($listBookId as $item){
                $statement->bindParam(':id', $item);
                $statement->execute();
            }
            Response::returnInfo(201, "Order created successfully",
                array("OrderId" => $this->id));
        } catch (Exception $e){
            Response::returnResponse( 500,"some thing false!!");
        }
    }

    /**
     * @return void
     */
    public function getDetail($connect, $id){
        if(!$this->find($connect, "id = {$id}")){
            Response::returnResponse(500, "order is not existed");
            exit();
        }
        $orderBook = $this->get($connect, $id);
        $sql = "SELECT book.id, book.name, book.category, book.image 
                FROM listbookorder inner join book on book.id = listbookorder.book_id
                where order_id = {$id};";
        $statement = $connect->prepare($sql);
        try {
            $statement->execute();
            $listBook = $statement->fetchAll(PDO::FETCH_ASSOC);
            return array("orderbook" => $orderBook, "listbook" => $listBook);
        } catch (Exception $e){
            Response::returnResponse(401, "Can not get list book");
            exit();
        }
    }

    /**
     * @param $connect
     * @param $status
     * @param $amount
     * @param $index
     * @return void
     */
    public function getListOrder($connect, $status, $amount, $index){
        $sql = null;
        if($status == null){
            $sql = "SELECT reader.name, orderbook.* 
            FROM reader inner join orderbook on reader.id = orderbook.reader_id
            ORDER BY createdAt DESC
            LIMIT {$index}, {$amount};";
        } else {
            $sql = "SELECT reader.name, orderbook.* 
            FROM reader inner join orderbook on reader.id = orderbook.reader_id
            where status = '{$status}'
            ORDER BY createdAt DESC
            LIMIT {$index}, {$amount};";
        }
        $statement = $connect->prepare($sql);
        try {
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e){
            Response::returnResponse(500, $e->getMessage());
            exit();
        }
    }
    public function updateOrder($connect,$id, $purpose, $change){
        if(!$this->find($connect, "id = {$id}")){
            Response::returnResponse(500, "order is not existed");
            exit();
        }
        if($purpose == 'status'){
            $sql = "UPDATE `orderbook` SET `status` = '{$change}' where `id` = {$id};";
        } else {
            $sql = "UPDATE `orderbook` SET `expiryAt` = '{$change}' where `id` = {$id};";
        }
        $statement = $connect->prepare($sql);
        try {
            $statement->execute();
        } catch (Exception $e){
            Response::returnResponse(500, "can not update");
            exit();
        }
        return true;
    }
}