<?php

class KhachHang {

    // Hàm để tạo idKH tự động
    public static function autoId($pdo) {
        $collection = $pdo->selectCollection('KhachHang');
        
        // Tìm tài liệu có idKH lớn nhất
        $latestDoc = $collection->findOne([], ['sort' => ['idKH' => -1]]);
        
        // Sinh idKH mới dựa trên giá trị lớn nhất hiện có
        if ($latestDoc && isset($latestDoc['idKH'])) {
            $latestId = (int) $latestDoc['idKH'];
            $newId = $latestId + 1;
        } else {
            $newId = 1; // Nếu không có tài liệu nào, bắt đầu từ 1
        }
        
        return $newId; // Giữ giá trị là kiểu số
    }

    // Phương thức để thêm khách hàng mới
    public static function addKhachHang($pdo, $hoTen, $gioiTinh, $ngaySinh, $diaChi, $SDT, $Email, $password) {
        // Chọn collection (tương tự như bảng trong SQL)
        $collection = $pdo->selectCollection('KhachHang');
        
        $idKH = self::autoId($pdo);
        $role="0";

        $ngaySinhDate = new DateTime($ngaySinh);
        $ngaySinhMongo = new MongoDB\BSON\UTCDateTime($ngaySinhDate->getTimestamp() * 1000);
        
        // Tạo một mảng đại diện cho tài liệu sẽ được chèn
        $document = [
            'idKH' => $idKH,
            'hoTen' => $hoTen,
            'gioiTinh' => $gioiTinh,
            'ngaySinh' => $ngaySinhMongo,
            'diaChi' => [
                'diaChi' => $diaChi['diachi'],
                'thanhPho' => $diaChi['thanhpho'],
                'quan' => $diaChi['quan'],
                'phuong' => $diaChi['phuong'],
                'duong' => $diaChi['duong']
            ],
            'SDT' => $SDT,
            'email' => $Email,
            'password' => $password,
            'role' => $role
        ];

        // Chèn tài liệu vào collection
        $collection->insertOne($document);

        return true;
    }

    // Phương thức để kiểm tra email đã tồn tại chưa
    public static function isEmailExists($pdo, $email) {
        // Chọn collection
        $collection = $pdo->selectCollection('KhachHang');
        
        // Tìm tài liệu có email trùng khớp
        $document = $collection->findOne(['email' => $email]);

        // Trả về true nếu tìm thấy tài liệu, ngược lại false
        return $document !== null;
    }

    public static function isValid($pdo, $email, $password) {
        $collection = $pdo->KhachHang;

        // Tìm tài liệu người dùng với email
        $kh = $collection->findOne(['email' => $email]);

        if ($kh && password_verify($password, $kh['password'])) {
            return true;
        } else {
            return false;
        }
    }

    public static function getUser($pdo, $email, $password) {
        $collection = $pdo->KhachHang;

        // Tìm tài liệu người dùng với email
        $kh = $collection->findOne(['email' => $email]);

        if ($kh && password_verify($password, $kh['password'])) {
            return $kh;
        } else {
            return null;
        }
    }

    public static function logout() {
        session_start();
        unset($_SESSION['logged_us']);
        unset($_SESSION['logged_role']);
        unset($_SESSION['logged_name']);
        unset($_SESSION['logged_id']);
        unset($_SESSION['logged_chucvu']);
        header("location: index.php"); 
        exit;
    }
    
    public static function getUserById($pdo, $idKH) {
        $collection = $pdo->KhachHang;
    
        // Tìm tài liệu người dùng với idKH
        $kh = $collection->findOne(['idKH' => $idKH]);
    
        return $kh ?: null;
    }
}
?>
