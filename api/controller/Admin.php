<?php
require_once (ROOT . "/api/model/Admin.php");
use \Firebase\JWT\JWT;

// nếu url chưa có dấu "/" thì thêm vào đầu.
if (strpos($url, "/") !== 0) {
    $url = "/$url";
}

$ADMIN = new Admin();
if ($url == "/admin" && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if($user == "admin"){
        if($input['telephone'] && $input['password'] && $input['rank']){
            // nếu tồn tại số điện thoại thì báo lỗi
            $admin = $ADMIN->find($connect, "telephone='{$input['telephone']}'");
            if ($admin) {
                Response::returnResponse(409, "Admin already exists!");
                exit();
            }
            // mã hoá mật khẩu
            $input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
            $adminId= $ADMIN->create($connect, $input);

            if ($adminId) {
                $payload = [
                    "iss" => "localhost",
                    "iat" => time(),
                    "exp" => time() + 86400,
                    "aud" => "myadmins",
                    "id" => $adminId,
                    "is_admin" => true
                ];

                $jwt = JWT::encode($payload, JWT_KEY, JWT_ALG);

                http_response_code(201);
                echo json_encode([
                    "jwt" => $jwt,
                    "admin" => $adminId,
                    "status" => "201",
                    "message" => "created",
                    "time" => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]
                ]);

            }
        } else {
            Response::returnResponse(500, "Telephone or password or rank not null");
        }
    } else {
        Response::returnResponse(500, "Access denied!");
    }

} else if ($url == "/admin/sign_in" && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $sql = "SELECT * FROM {$ADMIN->getTableName()} WHERE telephone=:telephone;";

    $statement = $connect->prepare($sql);
    $statement->bindValue(":telephone", $input['telephone']);
    try {
        $statement->execute();
    } catch (PDOException $e) {
        Response::returnResponse(500, $e->getMessage());
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
            "is_admin" => true
        ];

        $jwt = JWT::encode($payload, JWT_KEY, JWT_ALG);

        http_response_code(200);
        echo json_encode([
            "jwt" => $jwt,
            "admin_id" => $result['id'],
            "status" => "200",
            "message" => "ok",
            "time" => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]
        ]);
    } else {
        Response::returnResponse(401, "Wrong password!!");
    }
}