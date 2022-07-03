<?php

class Response {
    public static function returnResponse($status, $message){
        http_response_code($status);
        echo json_encode([
            "status" => $status,
            "message" => $message,
            "time" => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]
        ]);
    }
    public static function returnInfo($status, $message,$data){
        http_response_code($status);
        $smt = array(
            "status" => $status,
            "message" => $message,
            "time" => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
        $jsondata = array_merge($data, $smt);
        echo json_encode($jsondata);
    }
    public static function returnData($status, $message,$data){
        http_response_code($status);
        echo json_encode([
            "data" => $data,
            "status" => $status,
            "message" => $message,
            "time" => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]
        ]);
    }
}