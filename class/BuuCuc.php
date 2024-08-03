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
        $role = "0";

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
    public static function isEmailExists($pdo, $email)
    {
        // Chọn collection
        $collection = $pdo->selectCollection('BuuCuc');

        // Tìm tài liệu có email trùng khớp trong nhân viên của từng bưu cục
        $document = $collection->findOne(['nhanVien.email' => $email]);

        // Trả về true nếu tìm thấy tài liệu, ngược lại false
        return $document !== null;
    }

    // Phương thức để kiểm tra email đã tồn tại chưa
    public static function isCCCDExists($pdo, $CCCD)
    {
        // Chọn collection
        $collection = $pdo->selectCollection('BuuCuc');

        // Tìm tài liệu có CCCD trùng khớp trong nhân viên của từng bưu cục
        $document = $collection->findOne(['nhanVien.CCCD' => $CCCD]);

        // Trả về true nếu tìm thấy tài liệu, ngược lại false
        return $document !== null;
    }

    // Phương thức để xác thực người dùng
    public static function isValid($pdo, $email, $password)
    {
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
    public static function getUser($pdo, $email, $password)
    {
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

    // // Hàm lấy danh sách nhân viên với phân trang
    // public static function getNhanVienList($pdo)
    // {
    //     $collection = $pdo->selectCollection('BuuCuc');

    //     $buuCucs = $collection->find([], ['projection' => ['nhanVien' => 1]]);

    //     $allNhanViens = [];

    //     // Duyệt qua từng bưu cục để lấy danh sách nhân viên
    //     foreach ($buuCucs as $buuCuc) {
    //         if (isset($buuCuc['nhanVien'])) {
    //             foreach ($buuCuc['nhanVien'] as $nhanVien) {
    //                 $allNhanViens[] = $nhanVien;
    //             }
    //         }
    //     }
    //     return $allNhanViens;
    // }

    public static function getNhanVienList($pdo, $page = 1, $limit, $search = '', $sortColumn , $sortOrder) {
        $collection = $pdo->BuuCuc;
    
        $skip = ($page - 1) * $limit;
    
        $filter = [];
        if ($search) {
            $filter['nhanVien.hoTen'] = new MongoDB\BSON\Regex($search, 'i');
        }
    
        // Kiểm tra nếu filter không có điều kiện tìm kiếm nào, không cần lọc
        if (empty($filter)) {
            $filter = new stdClass(); // Bộ lọc mặc định không lọc gì
        }
    
        $sort = [];
        if (in_array($sortColumn, ['idNV', 'hoTen', 'chucVu'])) {
            $sort['nhanVien.' . $sortColumn] = ($sortOrder === 'asc') ? 1 : -1;
        } else {
            $sort['nhanVien.idNV'] = 1; // Sắp xếp mặc định theo ID
        }
    
        $pipeline = [
            ['$unwind' => '$nhanVien'], // Tách nhanVien ra thành các tài liệu riêng biệt
            ['$match' => $filter], // Áp dụng bộ lọc tìm kiếm
            ['$sort' => $sort], // Sắp xếp
            ['$skip' => $skip], // Bỏ qua các tài liệu không cần thiết
            ['$limit' => $limit], // Giới hạn số tài liệu
            ['$group' => ['_id' => '$_id', 'nhanVien' => ['$push' => '$nhanVien']]] // Tạo lại mảng nhân viên
        ];
    
        // Kiểm tra pipeline trước khi thực hiện
        // var_dump($pipeline);
    
        return $collection->aggregate($pipeline, ['typeMap' => ['array' => 'array']])->toArray();
    }
    
    
    
    public static function getTotalPages($pdo, $limit, $search = '') {
        $collection = $pdo->BuuCuc;
    
        $filter = [];
        if ($search) {
            $filter['nhanVien.hoTen'] = new MongoDB\BSON\Regex($search, 'i');
        }
    
        // Kiểm tra nếu filter không có điều kiện tìm kiếm nào, không cần lọc
        if (empty($filter)) {
            $filter = new stdClass(); // Bộ lọc mặc định không lọc gì
        }
    
        $pipeline = [
            ['$unwind' => '$nhanVien'], // Tách nhanVien ra thành các tài liệu riêng biệt
            ['$match' => $filter], // Áp dụng bộ lọc tìm kiếm
            ['$count' => 'total'] // Đếm số tài liệu
        ];
    
        // Kiểm tra pipeline trước khi thực hiện
        // var_dump($pipeline);
    
        $result = $collection->aggregate($pipeline)->toArray();
        $totalDocuments = isset($result[0]['total']) ? $result[0]['total'] : 0;
        return ceil($totalDocuments / $limit);
    }
    
    
    
    
    

    
    public static function getNhanVienById($pdo, $idNV)
    {
        $collection = $pdo->BuuCuc;
        // Truy vấn để tìm nhân viên với idNV cụ thể
        $query = [
            'nhanVien.idNV' => (int)$idNV
        ];

        // Sử dụng aggregation pipeline để lọc ra nhân viên có idNV cụ thể
        $pipeline = [
            ['$unwind' => '$nhanVien'],
            ['$match' => $query],
            ['$project' => [
                '_id' => 0,
                'idBC' => 1,
                'tenBC' => 1,
                'diaChi' => 1,
                'nhanVien' => 1
            ]]
        ];

        $cursor = $collection->aggregate($pipeline);
        $result = $cursor->toArray();

        // Kiểm tra nếu tìm thấy nhân viên
        if (count($result) > 0) {
            return $result[0]['nhanVien'];
        } else {
            return null; // Không tìm thấy nhân viên
        }
    }

    // Hàm lấy tất cả nhân viên giao hàng
    public static function getAllDeliveryStaff($pdo)
    {
        $collection = $pdo->BuuCuc;
        $cursor = $collection->find(
            [], // Tìm tất cả các tài liệu trong BuuCuc
            [
                'projection' => ['nhanVien' => 1], // Chỉ lấy trường nhanVien
            ]
        );

        $deliveryStaff = [];

        foreach ($cursor as $document) {
            foreach ($document['nhanVien'] as $staff) {
                if ($staff['chucVu'] == 'Nhân viên giao hàng') {
                    $deliveryStaff[] = $staff;
                }
            }
        }

        return $deliveryStaff;
    }
}
