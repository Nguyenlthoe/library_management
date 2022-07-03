<?php

class Database{
    /**
     * Tạo kết nối tới Database
     * @return false|mysqli|void
     */
    public function connectDB()
    {
        try {
            $connect = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));
            return $connect;
        }
        catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}