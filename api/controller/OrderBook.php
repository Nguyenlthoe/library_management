<?php
require_once (ROOT . "/api/model/OrderBook.php");
// nếu url chưa có dấu "/" thì thêm vào đầu.
if (strpos($url, "/") !== 0) {
    $url = "/$url";
}
if($url == "/orderbook" && $_SERVER['REQUEST_METHOD'] == 'POST'){
    if($user == "admin"){
        $input = json_decode(file_get_contents('php://input'), true);
        $input['createdAt'] = null;
        if(array_key_exists('listBookId', $input)){
            if(count($input['listBookId']) != 0){
                $newOrder = new OrderBook($input['readerId'], $input['createdAt'],
                    $input['expiryAt'], "Đang mượn");
                $newOrder->insertBook($connect, $input['listBookId']);
            } else {
                Response::returnResponse(400, "book not empty!!");
            }
        }
    } else {
        Response::returnResponse(500, "Access denied!!");
    }
} else if($url == "/orderbook" && $_SERVER['REQUEST_METHOD'] == 'GET'){
    $input = json_decode(file_get_contents('php://input'), true);
    if($user == "admin"){
        if(array_key_exists('status', $input)){
            $ORDERBOOK = new OrderBook(null, null,null, null);
            $status = $input['status'];
            $amount = 10;
            $index = 0;
            if(array_key_exists('amount', $input)){
                $amount = $input['amount'];
            }
            if(array_key_exists('index', $input)){
                $index = $input['index'];
            }
            $orderbook = $ORDERBOOK->getListOrder($connect, $status, $amount, $index);
            Response::returnInfo(200, "ok", $orderbook);
        } else {
            Response::returnResponse(500, "Missing status in orderbook");
        }
    }
} else if (preg_match("/orderbook\/(\d+)/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'GET'){
    $orderId = $matches[1];
    $ORDER = new OrderBook(null,null,null,null);
    $order = $ORDER->getDetail($connect, $orderId);
    if($user == "admin"){
        Response::returnInfo(201, "ok", $order);
    } else if ($user == "reader" && $userId == $order['orderbook']['reader_id']){
        Response::returnInfo(201, "ok", $order);
    } else {
        Response::returnResponse(500, "Access denied!");
    }
} else if (preg_match("/orderbook\/(\d+)/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'PUT'){
    $input = json_decode(file_get_contents('php://input'), true);
    if($user == "admin"){
        $ORDER = new OrderBook(null,null,null,null);
        if(array_key_exists('status', $input)){
            $purpose = 'status';
            $change = $input['status'];
        } else if (array_key_exists('expiryAt', $input)){
            $purpose = 'expiryAt';
            $change = $input['expiryAt'];
        } else {
            Response::returnResponse(500, "Missing purpose!!");
            exit();
        }
        $ORDER->updateOrder($connect, $matches[1], $purpose, $change);
        Response::returnResponse(500, "ok");
    } else {
        Response::returnResponse(500, "Access denied!!");
    }
}