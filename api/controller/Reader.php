<?php
require_once(ROOT . "/api/model/Reader.php");

// nếu url chưa có dấu "/" thì thêm vào đầu.
if (strpos($url, "/") !== 0) {
    $url = "/$url";
}

use \Firebase\JWT\JWT;

$READER = new Reader(null, null, null, null, null, null);

/**
 * Đăng nhập
 */
if ($url == "/reader/sign_in" && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $sql = "SELECT * FROM {$READER->getTableName()} WHERE telephone=:telephone;";

    $statement = $connect->prepare($sql);
    $statement->bindValue(":telephone", $input['telephone']);
    try {
        $statement->execute();
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "status" => 500,
            "message" => $e->getMessage()
        ]);
        exit();
    }

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if(!$result){
        Response::returnResponse(401, "Telephone is not true!!");
        exit();
    }
    if (password_verify($input['password'], $result['password'])) {
        // Tạo JWT
        $payload = [
            "iss" => "localhost",
            "iat" => time(),
            "exp" => time() + 86400,
            "aud" => "myusers",
            "id" => $result['id'],
            "is_admin" => false
        ];

        $jwt = JWT::encode($payload, JWT_KEY, JWT_ALG);
        Response::returnInfo(200, "created", array("jwt" => $jwt, "user_id" => $result['id']));
    } else {
        Response::returnResponse(401, "Wrong telephone or password!!");
    }
} else if ($url == "/reader" && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if($user != "admin"){
        Response::returnResponse(500, "Access denied!!");
        exit();
    }
    // nếu số điện thoại đã tồn tại thì báo lỗi
    $reader = $READER->find($connect, "telephone='{$input['telephone']}'");
    if ($reader) {
        Response::returnResponse(409, "Telephone already exists!!");
        exit();
    }
    $reader = $READER->find($connect, "email='{$input['email']}' or cccd = '{$input['cccd']}'");
    if ($reader) {
        Response::returnResponse(409, "Email or cccd already exists!!");
        exit();
    }
    // mã hoá mật khẩu
    $input['password'] = password_hash($input['telephone'], PASSWORD_DEFAULT);
    $readerId= $READER->create($connect, $input);

    if ($readerId) {
        $payload = [
            "iss" => "localhost",
            "iat" => time(),
            "exp" => time() + 86400,
            "aud" => "myusers",
            "id" => $readerId,
            "is_admin" => false
        ];
        $jwt = JWT::encode($payload, JWT_KEY, JWT_ALG);
        Response::returnInfo(201, "created", array("jwt" => $jwt, "user_id" => $readerId));
    }
} else if ($url == "/reader" && $_SERVER['REQUEST_METHOD'] == 'GET'){
    $input = json_decode(file_get_contents('php://input'), true);
    if(array_key_exists("telephone", $input)){
        $reader = new Reader(null, null, $input['telephone'], "null", "null", "null");
        $reader = $reader->findReader($connect);
        unset($reader['password']);
        Response::returnInfo(200, "ok", $reader);
    } else {
        Response::returnResponse(501,"Telephone is not setted");
    }
}