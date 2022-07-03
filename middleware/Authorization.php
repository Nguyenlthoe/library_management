<?php
//require_once (ROOT . "/library/Response.php");
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$header = getallheaders();
$user = "user";
$userId = null;
if (!empty($header['Authorization'])) {
    $jwt = $header['Authorization'];
    try {
        $decode_data = JWT::decode($jwt, new Key(JWT_KEY, JWT_ALG));

        if (!$decode_data->is_admin) {
            $user = "reader";
            $userId = $decode_data->id;
        } else {
            $user = "admin";
        }
    } catch (Exception $e) {
//        Response::returnResponse(401, $e->getMessage());
    }
}
