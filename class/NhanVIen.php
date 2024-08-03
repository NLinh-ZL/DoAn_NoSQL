<?php

class NhanVien
{

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
?>