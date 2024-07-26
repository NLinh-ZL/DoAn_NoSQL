<?php
require 'vendor/autoload.php'; // Sử dụng Composer để tự động tải thư viện MongoDB

class Database {
    public function getConnect() {
        $host = "localhost";
        $port = 27017; // Cổng mặc định của MongoDB
        $dbname = "ViettelPost"; // Tên cơ sở dữ liệu MongoDB

        // Khởi tạo đối tượng MongoDB\Client
        $client = new MongoDB\Client("mongodb://$host:$port");
        
        // Chọn cơ sở dữ liệu
        $database = $client->selectDatabase($dbname);

        return $database;
    }
}

?>