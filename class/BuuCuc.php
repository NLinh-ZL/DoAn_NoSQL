<?php

class BuuCuc
{
    //=================================
    //Bưu cục
    //=================================
    // Hàm để lấy ra tất cả bưu cục
    public static function getAllBuuCuc($pdo)
    {
        // Chọn collection (tương tự như bảng trong SQL)
        $collection = $pdo->selectCollection('BuuCuc');

        // Truy vấn tất cả các tài liệu trong collection
        $cursor = $collection->find();

        // Chuyển kết quả cursor sang mảng
        $buuCucs = iterator_to_array($cursor);

        return $buuCucs;
    }
    //=================================
    //Nhân viên
    //=================================
    // Hàm để tạo idNV tự động
    public static function autoId($pdo)
    {
        $collection = $pdo->selectCollection('BuuCuc');

        // Tìm tài liệu có nhanVien.idNV lớn nhất
        $latestDoc = $collection->aggregate([
            ['$unwind' => '$nhanVien'],
            ['$sort' => ['nhanVien.idNV' => -1]],
            ['$limit' => 1],
            ['$project' => ['idNV' => '$nhanVien.idNV']]
        ])->toArray();

        // Sinh idNV mới dựa trên giá trị lớn nhất hiện có
        if (!empty($latestDoc) && isset($latestDoc[0]['idNV'])) {
            $latestId = (int) $latestDoc[0]['idNV'];
            $newId = $latestId + 1;
        } else {
            $newId = 1; // Nếu không có tài liệu nào, bắt đầu từ 1
        }

        return $newId; // Giữ giá trị là kiểu số
    }

    // Phương thức để thêm nhân viên mới
    public static function addNhanVien($pdo, $idBC, $hoTen, $gioiTinh, $ngaySinh, $diaChi, $SDT, $CCCD, $email, $chucVu, $password)
    {
        // Chọn collection (tương tự như bảng trong SQL)
        $collection = $pdo->selectCollection('BuuCuc');

        $idNV = self::autoId($pdo);
        $role="0";

        $ngaySinhDate = new DateTime($ngaySinh);
        $ngaySinhMongo = new MongoDB\BSON\UTCDateTime($ngaySinhDate->getTimestamp() * 1000);

        // Tạo một mảng đại diện cho tài liệu sẽ được chèn
        $document = [
            'idNV' => $idNV,
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
            'CCCD' => $CCCD,
            'email' => $email,
            'chucVu' => $chucVu,
            'password' => $password,
            'role' => $role
        ];

        // Tìm bưu cục có idBC và thêm nhân viên vào mảng nhanVien
        $updateResult = $collection->updateOne(
            ['idBC' => $idBC],
            ['$push' => ['nhanVien' => $document]]
        );

        return $updateResult->getModifiedCount() > 0;
    }

    // Phương thức để kiểm tra email đã tồn tại chưa
    public static function isEmailExists($pdo, $email) {
        // Chọn collection
        $collection = $pdo->selectCollection('BuuCuc');

        // Tìm tài liệu có email trùng khớp trong nhân viên của từng bưu cục
        $document = $collection->findOne(['nhanVien.email' => $email]);

        // Trả về true nếu tìm thấy tài liệu, ngược lại false
        return $document !== null;
    }

    // Phương thức để xác thực người dùng
    public static function isValid($pdo, $email, $password) {
        // Chọn collection
        $collection = $pdo->selectCollection('BuuCuc');

        // Tìm tài liệu nhân viên với email
        $buuCuc = $collection->findOne(['nhanVien.email' => $email]);

        // Kiểm tra mật khẩu
        if ($buuCuc) {
            foreach ($buuCuc['nhanVien'] as $nv) {
                if ($nv['email'] == $email && password_verify($password, $nv['password'])) {
                    return true;
                }
            }
        }
        return false;
    }

    // Phương thức để lấy thông tin người dùng
    public static function getUser($pdo, $email, $password) {
        // Chọn collection
        $collection = $pdo->selectCollection('BuuCuc');

        // Tìm tài liệu nhân viên với email
        $buuCuc = $collection->findOne(['nhanVien.email' => $email]);

        // Kiểm tra mật khẩu và trả về thông tin nhân viên nếu đúng
        if ($buuCuc) {
            foreach ($buuCuc['nhanVien'] as $nv) {
                if ($nv['email'] == $email && password_verify($password, $nv['password'])) {
                    return $nv;
                }
            }
        }
        return null;
    }
}