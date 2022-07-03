<?php
require_once (ROOT . "/api/model/Book.php");
require_once (ROOT . "/api/model/Author.php");
// nếu url chưa có dấu "/" thì thêm vào đầu.
if (strpos($url, "/") !== 0) {
    $url = "/$url";
}

if($url == "/book" && $_SERVER['REQUEST_METHOD'] == 'POST'){
    if($user == "admin"){
        $input = json_decode(file_get_contents('php://input'), true);
        $newBook = new Book();
        $newBook->init($input['name'], $input['category'],$input['amount'],$input['price'],
            $input['description'], $input['image']);
        $listAuthor = [];
        foreach ($input['author'] as $key => $value){
            $listAuthor[] = new Author($value['name'], $value['information']);
        }
        $data = $newBook->insertNewBook($connect, $listAuthor);
        Response::returnInfo(200, "created", $data);
    } else {
        Response::returnResponse(401, "Access denied!!");
    }
} else if ($url == "/book" && $_SERVER['REQUEST_METHOD'] == "GET"){
    $input = json_decode(file_get_contents('php://input'), true);
    if(array_key_exists('keyword')){
        $newBook = new Book();
        Response::returnData(201,"ok", $newBook->findBook($connect, $input['keyword']));
    } else {
        Response::returnResponse(500, "keywords not set");
    }

} else if (preg_match("/book\/(\d+)/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'GET'){
    $bookId = $matches[1];
    $book = new Book();
    $bookDetail = $book->get($connect, $bookId);
    echo "fff";
    if($book){
        $book->setId($bookId);
        $authors = $book->getAuthor($connect, $bookId);
        Response::returnInfo("200", "Ok", array("book" => $bookDetail,"authors" => $authors));
    } else {
        Response::returnResponse("500", "BookId is not exist");
    }
} else if (preg_match("/book\/(\d+)/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'DELETE'){
    if($user == "admin"){
        $bookId = $matches[1];
        $book = new Book();
        if($book->get($connect, $bookId)){
            if($book->delete($connect, $bookId)){
                Response::returnResponse("200", "delete sucessfull");
            } else {
                Response::returnResponse("500", "Delete failed!!");
            }
        } else {
            Response::returnResponse("500", "BookId is not exist");
        }
    }

} else if (preg_match("/book\/(\d+)/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'PUT'){
    $BOOK = new Book();
    $bookId = $matches[1];
    $book = $BOOK->get($connect, $bookId);
    $input = json_decode(file_get_contents("php://input"), true);
    if($book){
        $BOOK->update($connect, $bookId, $input);
        Response::returnResponse(200, "update ok");
    } else {
        Response::returnResponse(404, "Book not found!!");
    }
}
