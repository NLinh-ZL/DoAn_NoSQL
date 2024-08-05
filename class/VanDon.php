<?php

class VanDon
{

    public static function getAllpage($database, $limit, $offset, $tinhtrang = null, $idKhachHang)
    {
        $collection = $database->VanDon; // Chọn bộ sưu tập 'VanDon'

        if ($collection === null) {
            die("Bộ sưu tập không tồn tại: VanDon");
        }

        // Điều kiện tìm kiếm
        $query = [];
        if ($tinhtrang !== null) {
            $query['tinhTrang'] = $tinhtrang;
        }

        $query['idKhachHang'] = (int)$idKhachHang;


        // Tìm dữ liệu với phân trang
        $options = [
            'limit' => $limit,
            'skip' => $offset,
            'sort' => ['ngayTao' => 1] // Sắp xếp theo ngày tạo giảm dần (hoặc bạn có thể thay đổi theo nhu cầu)
        ];

        $cursor = $collection->find($query, $options); // Lấy dữ liệu từ bộ sưu tập
        $results = [];

        foreach ($cursor as $document) {
            $results[] = $document;
        }

        return $results;
    }


    public static function countAll($database, $limit, $tinhtrang = null, $idKhachHang)
    {
        $collection = $database->VanDon; // Chọn bộ sưu tập 'VanDon'

        if ($collection === null) {
            die("Bộ sưu tập không tồn tại: VanDon");
        }

        // Điều kiện tìm kiếm
        $query = [];
        if ($tinhtrang !== null) {
            $query['tinhTrang'] = $tinhtrang;
        }

        $query['idKhachHang'] = (int)$idKhachHang;
        // Đếm tổng số phần tử với điều kiện lọc
        $count = $collection->countDocuments($query);

        $total_pages = ceil($count / $limit);

        return $total_pages;
    }

    public static function XacNhanVD_BuuCuc($database, $idVD, $trangthai, $idNhanVien, $idBC, $tenBC, $diachiBC)
    {
        $collection = $database->VanDon; // Chọn bộ sưu tập 'VanDon'

        if ($collection === null) {
            die("Bộ sưu tập không tồn tại: VanDon");
        }

        // Quy trình mới cần thêm
        $quyTrinhMoi = [
            'trangthai' => $trangthai,
            'idNV' => $idNhanVien,
            'idBC' => $idBC,
            'tenBC' => $tenBC,
            'diachiBC' => $diachiBC
        ];

        // Tìm và cập nhật vận đơn với idVD cụ thể
        $result = $collection->updateOne(
            ['idVD' => $idVD],
            ['$push' => ['quyTrinhVC' => $quyTrinhMoi]]
        );

        // Kiểm tra kết quả cập nhật
        if ($result->getModifiedCount() == 1) {
            return "Thêm quy trình vận chuyển thành công!";
        } else {
            return "Thêm quy trình vận chuyển thất bại hoặc vận đơn không tồn tại!";
        }
    }

    public static function getDonHangTrangThai($database,$idBuuCuc,$limit,$offset,$tinhtrang = null)
    {
        $collection = $database->VanDon;
        // Truy vấn tìm các đơn hàng có trạng thái "Chờ xác nhận" trong quy trình vận chuyển
        $query = [
            'quyTrinhVC.0.trangthai' => $tinhtrang,
            'quyTrinhVC.idBC' => $idBuuCuc
        ];

        $options = [
            'limit' => $limit,
            'skip' => $offset,
            'projection' => ['_id' => 0] // Không lấy trường _id, có thể điều chỉnh theo nhu cầu
        ];

        $cursor = $collection->find($query, $options);
        $results = [];

        foreach ($cursor as $document) {
            $results[] = $document;
        }

        return $results;
    }
    // -------------------------------------------------------------------------------------------------------------------------------------
    public static function xacDinhKieuVanChuyen($tinhGui, $tinhNhan)
    {
        // Định nghĩa các khu vực của Việt Nam
        $vietnamRegions = [
            'Bắc Bộ' => [
                'Hà Nội', 'Hà Giang', 'Cao Bằng', 'Bắc Cạn', 'Tuyên Quang', 'Lào Cai',
                'Điện Biên', 'Lai Châu', 'Sơn La', 'Yên Bái', 'Hòa Bình', 'Thái Nguyên',
                'Lạng Sơn', 'Quảng Ninh', 'Bắc Giang', 'Phú Thọ', 'Vĩnh Phúc', 'Bắc Ninh',
                'Hải Dương', 'Hải Phòng', 'Hưng Yên', 'Thái Bình', 'Hà Nam', 'Nam Định',
                'Ninh Bình'
            ],
            'Trung Bộ' => [
                'Thanh Hóa', 'Nghệ An', 'Hà Tĩnh', 'Quảng Bình', 'Quảng Trị', 'Thừa Thiên Huế',
                'Đà Nẵng', 'Quảng Nam', 'Quảng Ngãi', 'Bình Định', 'Phú Yên', 'Khánh Hòa',
                'Ninh Thuận', 'Bình Thuận', 'Kon Tum', 'Gia Lai', 'Đắk Lắk', 'Đắk Nông',
                'Lâm Đồng'
            ],
            'Nam Bộ' => [
                'Bình Phước', 'Tây Ninh', 'Bình Dương', 'Đồng Nai', 'Bà Rịa - Vũng Tàu', 'Hồ Chí Minh',
                'Long An', 'Tiền Giang', 'Bến Tre', 'Trà Vinh', 'Vĩnh Long', 'Đồng Tháp',
                'An Giang', 'Kiên Giang', 'Cần Thơ', 'Hậu Giang', 'Sóc Trăng', 'Bạc Liêu',
                'Cà Mau'
            ]
        ];

        // Xác định khu vực của tỉnh gửi
        $khuVucGui = '';
        foreach ($vietnamRegions as $region => $provinces) {
            if (in_array($tinhGui, $provinces)) {
                $khuVucGui = $region;
                break;
            }
        }

        // Xác định khu vực của tỉnh nhận
        $khuVucNhan = '';
        foreach ($vietnamRegions as $region => $provinces) {
            if (in_array($tinhNhan, $provinces)) {
                $khuVucNhan = $region;
                break;
            }
        }

        // Xác định loại vận chuyển
        if ($tinhGui === $tinhNhan) {
            return 'noi_tinh';
        } elseif ($khuVucGui === $khuVucNhan) {
            return 'noi_mien';
        } else {
            return 'lien_mien';
        }
    }

    public static function tinhCuocShip($trongLuong, $kieuVanChuyen, $loaiVanChuyen)
    {
        // Bảng giá cơ bản
        $cuocPhi = [
            'noi_tinh' => [11000, 12000, 16000, 2000],
            'noi_mien' => [11000, 14000, 18000, 5000],
            'lien_mien' => [12000, 20000, 30000, 10000]
        ];

        // Xác định cước phí cơ bản dựa vào trọng lượng và kiểu vận chuyển
        if ($trongLuong <= 500) {
            $phiCoBan = $cuocPhi[$kieuVanChuyen][0];
        } elseif ($trongLuong <= 1000) {
            $phiCoBan = $cuocPhi[$kieuVanChuyen][1];
        } elseif ($trongLuong <= 2000) {
            $phiCoBan = $cuocPhi[$kieuVanChuyen][2];
        } else {
            // Tính phần vượt quá 2000 gram, mỗi 500 gram thêm phí
            $phiCoBan = $cuocPhi[$kieuVanChuyen][2];
            $trongLuongDu = $trongLuong - 2000;
            $phiVuotQua = ceil($trongLuongDu / 500) * $cuocPhi[$kieuVanChuyen][3];
            $phiCoBan += $phiVuotQua;
        }

        // Tính phí dựa trên loại vận chuyển
        if ($loaiVanChuyen === 'Nhanh') {
            $phiCuoiCung = $phiCoBan + 15000;
        } elseif ($loaiVanChuyen === 'Hỏa tốc') {
            $phiCuoiCung = $phiCoBan * 5;
        } else {
            $phiCuoiCung = $phiCoBan;
        }

        return (int)$phiCuoiCung;
    }

    public static function themDonHang($pdo, $idKHGui, $thoiGianHenLay, $hoTenNguoiNhan, $sdtNguoiNhan, $diaChiNguoiNhan, $thanhPho, $quan, $phuong, $duong, $thoiGianHenGiao, $loaiHangHoa, $items, $tinhChatHangHoaDacBiet, $nguoiTraCuoc, $cuoc, $tienThuHo, $loaiVanChuyen, $ghiChu, $quyTrinhVC)
    {
        $collection = $pdo->VanDon;

        $idVD = self::autoIdVD($pdo);

        $vanDonData = [
            'idVD' => $idVD, // Hoặc tạo mã phiếu gửi theo cách của bạn
            'idKhachHang' => $idKHGui,
            'thoiGianHenLay' => $thoiGianHenLay,
            'nguoiNhan' => [
                'hoTen' => $hoTenNguoiNhan,
                'SDT' => $sdtNguoiNhan,
                'diaChi' => [
                    'diaChi' => $diaChiNguoiNhan,
                    'thanhPho' => $thanhPho,
                    'quan' => $quan,
                    'phuong' => $phuong,
                    'duong' => $duong
                ]
            ],
            'thoiGianHenGiao' => $thoiGianHenGiao,
            'loaiHang' => $loaiHangHoa,
            'hangHoa' => $items,
            'tinhChatHang' => $tinhChatHangHoaDacBiet,
            'ngTraCuoc' => $nguoiTraCuoc,
            'tienCuoc' => $cuoc,
            'tinhTrangCuoc' => 'Chưa thanh toán',
            'thuHo' => $tienThuHo,
            'loaiVanChuyen' => $loaiVanChuyen,
            'ghiChu' => $ghiChu,
            'ngayTao' => new MongoDB\BSON\UTCDateTime(), // Ngày giờ hiện tại
            'tinhTrang' => 'Chưa giao', // Hoặc trạng thái mặc định của bạn
            'quyTrinhVC' => $quyTrinhVC,
        ];

        try {
            $result = $collection->insertOne($vanDonData);

            if ($result->getInsertedCount() == 1) {
                return "Đơn hàng đã được lưu thành công!";
            } else {
                return "Có lỗi xảy ra khi lưu đơn hàng.";
            }
        } catch (Exception $e) {
            return "Lỗi: " . $e->getMessage();
        }
    }

    // Hàm để tạo idKH tự động
    public static function autoIdVD($pdo)
    {
        $collection = $pdo->selectCollection('VanDon');

        // Tìm tài liệu có idKH lớn nhất
        $latestDoc = $collection->findOne([], ['sort' => ['idVD' => -1]]);

        // Sinh idKH mới dựa trên giá trị lớn nhất hiện có
        if ($latestDoc && isset($latestDoc['idVD'])) {
            $latestId = (int) $latestDoc['idVD'];
            $newId = $latestId + 1;
        } else {
            $newId = 1; // Nếu không có tài liệu nào, bắt đầu từ 1
        }

        return $newId; // Giữ giá trị là kiểu số
    }

    public static function getVanDonByIdVD($pdo, $idVD) {
        // Kết nối đến cơ sở dữ liệu và collection
        $collection = $pdo->selectCollection('VanDon');
    
        // Tìm vận đơn theo idVD
        $result = $collection->findOne(['idVD' => (int) $idVD]);
    
        if ($result) {
            return $result;
        } else {
            return null; // Hoặc xử lý trường hợp không tìm thấy
        }
    }
}
