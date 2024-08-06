<?php
require_once "inc/header.php";
?>
<!-- =============================================================================================== -->
<?php
if(isset($_SESSION['logged_role']) && $_SESSION['logged_role'] === "0") {
?>

<!-- =============================================================================================== -->

<?php
require_once "class/KhachHang.php";
require_once "class/VanDon.php";
require_once "class/API.php";

$vietnamProvinces = [
    'An Giang', 'Bạc Liêu', 'Bắc Cạn', 'Bắc Giang', 'Bắc Ninh', 'Bình Dương',
    'Bình Phước', 'Bình Thuận', 'Bình Định', 'Cà Mau', 'Cần Thơ', 'Cao Bằng',
    'Đà Nẵng', 'Đắk Lắk', 'Đắk Nông', 'Đồng Nai', 'Đồng Tháp', 'Gia Lai',
    'Hà Giang', 'Hà Nội', 'Hà Nam', 'Hà Tĩnh', 'Hải Dương', 'Hải Phòng',
    'Hậu Giang', 'Hòa Bình', 'Hồ Chí Minh', 'Hưng Yên', 'Khánh Hòa',
    'Kiên Giang', 'Kon Tum', 'Lai Châu', 'Lâm Đồng', 'Lạng Sơn', 'Long An',
    'Nam Định', 'Nghệ An', 'Ninh Bình', 'Ninh Thuận', 'Phú Thọ', 'Phú Yên',
    'Quảng Bình', 'Quảng Nam', 'Quảng Ngãi', 'Quảng Ninh', 'Quảng Trị',
    'Sóc Trăng', 'Sơn La', 'Tây Ninh', 'Thái Bình', 'Thái Nguyên',
    'Thừa Thiên Huế', 'Tiền Giang', 'Trà Vinh', 'Vĩnh Long', 'Vĩnh Phúc',
    'Yên Bái'
];



$idus = $_SESSION['logged_id'];

$us = KhachHang::getUserById($pdo, $idus);

// Lấy thông tin khách hàng từ biến $us
$idKHGui = $us['idKH'];
$hoTenGui = $us['hoTen'];
$gioiTinhGui = $us['gioiTinh'];
$ngaySinhGui = $us['ngaySinh'];
$diaChiGui = $us['diaChi'];
$SDTGui = $us['SDT'];
$emailGui = $us['email'];

// Lấy chi tiết địa chỉ
$diachi_fullGui = $diaChiGui['diaChi'];
$thanhPhoGui = $diaChiGui['thanhPho'];
$quanGui = $diaChiGui['quan'];
$phuongGui = $diaChiGui['phuong'];
$duongGui = $diaChiGui['duong'];





$tienThuHo = 0;
$hoTenNguoiNhan = $thoiGianHenLay = $sdtNguoiNhan = $diaChiNguoiNhan = '';
$sonha = $thanhPho = $quan = $phuong = $duong = '';
$thoiGianHenGiao = $loaiHangHoa = $tienThuHo = $ghiChu = '';
$nguoiTraCuoc = $loaiVanChuyen = '';
$tinhChatHangHoaDacBiet = [];
$items = [];


$thoiGianHenLayError = $sdtNguoiNhanError = $diaChiNguoiNhanError = '';
$sonhaError = $thanhPhoError = $quanError = $phuongError = $duongError = '';
$thoiGianHenGiaoError = $loaiHangHoaError = '';
$nguoiTraCuocError = $loaiVanChuyenError = '';
$hoTenNguoiNhanError = $tinhChatHangHoaDacBietError = '';
$itemError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Nhận dữ liệu từ các trường trong biểu mẫu
    $thoiGianHenLay = $_POST['tghenlay'];
    $hoTenNguoiNhan = $_POST['hoten'];
    $sdtNguoiNhan = $_POST['sdt'];
    $diaChiNguoiNhan = $_POST['diachi'];
    $thanhPho = $_POST['thanhpho'];
    $quan = $_POST['quan'];
    $phuong = $_POST['phuong'];
    $duong = $_POST['duong'];
    $sonha = $_POST['sonha'];
    $thoiGianHenGiao = $_POST['tghengiao'];
    $loaiHangHoa = isset($_POST['loaiHangHoa']) ? $_POST['loaiHangHoa'] : '';
    $tienThuHo = $_POST['thuho'];
    $ghiChu = $_POST['ghichu'];
    $nguoiTraCuoc = isset($_POST['ngtracuoc']) ? $_POST['ngtracuoc'] : '';
    $loaiVanChuyen = isset($_POST['loaivanchuyen']) ? $_POST['loaivanchuyen'] : '';

    if (!empty($_POST['giaTriCao'])) $tinhChatHangHoaDacBiet[] = $_POST['giaTriCao'];
    if (!empty($_POST['deVo'])) $tinhChatHangHoaDacBiet[] = $_POST['deVo'];
    if (!empty($_POST['nguyenKhoi'])) $tinhChatHangHoaDacBiet[] = $_POST['nguyenKhoi'];
    if (!empty($_POST['quaKho'])) $tinhChatHangHoaDacBiet[] = $_POST['quaKho'];
    if (!empty($_POST['chatLong'])) $tinhChatHangHoaDacBiet[] = $_POST['chatLong'];
    if (!empty($_POST['pin'])) $tinhChatHangHoaDacBiet[] = $_POST['pin'];
    if (!empty($_POST['hangLanh'])) $tinhChatHangHoaDacBiet[] = $_POST['hangLanh'];

    // Xử lý các mặt hàng
    $tongTrongLuong = 0;
    for ($i = 1; isset($_POST["tenhang$i"]); $i++) {
        $tenhang = $_POST["tenhang$i"];
        $soluong = $_POST["soluong$i"];
        $trongluong = $_POST["trongluong$i"];
        $giatien = isset($_POST["giatien$i"]) ? $_POST["giatien$i"] : 0;

        if (empty($tenhang) || empty($soluong) || empty($trongluong)) {
            $itemError = "Nhập thiếu dữ liệu về hàng hóa";
        } else {
            $tongTrongLuong += $trongluong;
        }

        // Chỉ thêm vào mảng $items nếu không có lỗi
        if (!empty($tenhang) && !empty($soluong) && !empty($trongluong)) {
            $items[] = [
                'tenHang' => $tenhang,
                'soLuong' => $soluong,
                'trongLuong' => $trongluong,
                'giaTien' => $giatien
            ];
        }
    }



    // Kiểm tra các trường dữ liệu
    if (empty($thoiGianHenLay)) {
        $thoiGianHenLayError = "Thời gian hẹn lấy không được để trống";
    }

    if (empty($hoTenNguoiNhan)) {
        $hoTenNguoiNhanError = "Họ tên người nhận không được để trống";
    }

    if (empty($sdtNguoiNhan)) {
        $sdtNguoiNhanError = "Số điện thoại người nhận không được để trống";
    } elseif (!preg_match("/^\d{10}$/", $sdtNguoiNhan)) {
        $sdtNguoiNhanError = 'Số điện thoại không hợp lệ';
    }

    if (empty($diaChiNguoiNhan)) {
        $diaChiNguoiNhanError = "Địa chỉ người nhận không được để trống";
    } else {
        //API googlemap
        $switchLocation = geocodeAddress($diaChiNguoiNhan, $googleApiKey);
        if ($switchLocation === false) {
            $diaChiNguoiNhanError = "Địa chỉ người nhận bạn nhập không tồn tại";
        }
    }

    if (empty($thanhPho)) {
        $thanhPhoError = "Thành phố không được để trống";
    }

    if (empty($quan)) {
        $quanError = "Quận không được để trống";
    }

    if (empty($phuong)) {
        $phuongError = "Phường không được để trống";
    }

    if (empty($duong)) {
        $duongError = "Đường không được để trống";
    }

    if (empty($sonha)) {
        $sonhaError = "Số nhà không được để trống";
    }

    if (empty($thoiGianHenGiao)) {
        $thoiGianHenGiaoError = "Thời gian hẹn giao không được để trống";
    }

    if (empty($loaiHangHoa)) {
        $loaiHangHoaError = "Loại hàng hóa không được để trống";
    }

    if (empty($nguoiTraCuoc)) {
        $nguoiTraCuocError = "Người trả cước không được để trống";
    }

    if (empty($loaiVanChuyen)) {
        $loaiVanChuyenError = "Loại vận chuyển không được để trống";
    }

    // Kiểm tra đặc tính hàng hóa đặc biệt
    // if (empty($tinhChatHangHoaDacBiet)) {
    //     $tinhChatHangHoaDacBietError = "Phải chọn ít nhất một tính chất hàng hóa.";
    // }



    // Nếu không có lỗi, tiến hành xử lý lưu trữ
    if (
        empty($thoiGianHenLayError) && empty($sdtNguoiNhanError) &&
        empty($diaChiNguoiNhanError) && empty($thanhPhoError) && empty($quanError) &&
        empty($phuongError) && empty($duongError) && empty($thoiGianHenGiaoError) &&
        empty($loaiHangHoaError) && empty($nguoiTraCuocError) && empty($hoTenNguoiNhanError) &&
        empty($loaiVanChuyenError) && empty($tinhChatHangHoaDacBietError) && empty($itemError)
    ) {

        //======================================= API =========================================================
        // Tìm bưu cục gần nhất

        $result = findNearestAddress($diachi_fullGui, $mongoUri, $dbName, $collectionName, $googleApiKey);
        if ($result['nearestAddress']) {
            $diachibcnear = $result['nearestAddress']['diaChi'];
            $tenbcnear = $result['nearestAddress']['tenBC'];
            $idbcnear = $result['nearestAddress']['idBC'];
            $tt = "Chờ xác nhận";
            $quyTrinhVC[] = [
                'trangthai' => $tt,
                'idBC' => $idbcnear,
                'tenBC' => $tenbcnear,
                'diachiBC' => $diachibcnear
            ];
        }

        //======================================= API =========================================================

        $kieuvc = VanDon::xacDinhKieuVanChuyen($thanhPhoGui, $thanhPho);
        $cuoc = VanDon::tinhCuocShip($tongTrongLuong, $kieuvc, $loaiVanChuyen);
        // $ketQua = VanDon::themDonHang(
        //     $pdo,
        //     $idus,
        //     $thoiGianHenLay,
        //     $hoTenNguoiNhan,
        //     $sdtNguoiNhan,
        //     $diaChiNguoiNhan,
        //     $thanhPho,
        //     $quan,
        //     $phuong,
        //     $duong,
        //     $thoiGianHenGiao,
        //     $loaiHangHoa,
        //     $items,
        //     $tinhChatHangHoaDacBiet,
        //     $nguoiTraCuoc,
        //     $cuoc,
        //     $tienThuHo,
        //     $loaiVanChuyen,
        //     $ghiChu,
        //     $quyTrinhVC
        // );

        $_SESSION['orderData'] = [
            'thoiGianHenLay' => $thoiGianHenLay,
            'hoTenNguoiNhan' => $hoTenNguoiNhan,
            'sdtNguoiNhan' => $sdtNguoiNhan,
            'diaChiNguoiNhan' => $diaChiNguoiNhan,
            'thanhPho' => $thanhPho,
            'quan' => $quan,
            'phuong' => $phuong,
            'duong' => $duong,
            'sonha' => $sonha,
            'thoiGianHenGiao' => $thoiGianHenGiao,
            'loaiHangHoa' => $loaiHangHoa,
            'tinhChatHangHoaDacBiet' => $tinhChatHangHoaDacBiet,
            'nguoiTraCuoc' => $nguoiTraCuoc,
            'loaiVanChuyen' => $loaiVanChuyen,
            'ghiChu' => $ghiChu,
            'items' => $items,
            'cuoc' => $cuoc,
            'tienThuHo' => $tienThuHo,
            'quyTrinhVC' => $quyTrinhVC
        ];
        header("Location: xacnhan.php");
        exit;
    }
}

?>


<main>
    <style>
        .large-text {
            font-size: 65px;
            font-weight: 900;
        }

        /* Media Query cho màn hình nhỏ hơn 768px */
        @media (max-width: 768px) {
            .large-text {
                font-size: 60px;
            }
        }

        /* Media Query cho màn hình nhỏ hơn 576px */
        @media (max-width: 576px) {
            .large-text {
                font-size: 45px;
            }
        }

        /* Ghi đè chiều rộng của nice-select */
        .nice-select {
            width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Ghi đè chiều rộng của danh sách */
        .nice-select .list {
            width: 100% !important;
            box-sizing: border-box !important;
        }

        hr {
            border-bottom: 1px solid #eceff8;
            border-top: 0 none;
            margin: 10px 0;
            padding: 0;
        }

        .form-contact textarea {
            border-radius: 0px;
            height: auto !important;
        }

        .nice-select.open .list {
            max-height: 200px;
            /* Hoặc bất kỳ chiều cao tối đa nào bạn muốn */
            overflow-y: auto;
            /* Hiển thị thanh cuộn dọc khi nội dung vượt quá chiều cao tối đa */
        }

        /* .boxed-btn {
            font-family: "Tahoma", sans-serif;
        } */
    </style>
    <section class="contact-section" style="background-color: #F8F8FF">
        <div class="container">

            <div class="row">
                <div class="col-12 text-center">
                    <div class="large-text">PHIẾU GỬI</div>
                </div>
                <div class="col-lg-12">
                    <form class="form-contact contact_form" method="POST" action="#">
                        <div class="row">
                            <div class="col-md-6">

                                <!-- Card người gửi -->
                                <div class="card form-group box-sender">
                                    <div class="card-header">
                                        <div class="float-right" style="font-size: 20px; font-weight:600;">Người gửi</div>
                                    </div>
                                    <div class="card-body">
                                        <label class="form-label my-1" style="font-size: 16px;">Tên người gửi:</label>
                                        <input class="form-control" style="color: orange; font-size:16px; font-weight:600;" type="text" value="<?= $hoTenGui ?> " disabled>

                                        <div class="form-group">
                                            <label class="form-label my-1" style="font-size: 16px; display: block; margin-bottom: 8px;">Thời gian hẹn lấy:</label>
                                            <select style="display: block;" name="tghenlay" id="tghenlay">
                                                <option value="">Chọn thời gian</option>
                                                <option value="Sáng" <?= isset($thoiGianHenLay) && $thoiGianHenLay == 'Sáng' ? 'selected' : '' ?>>Sáng (7h30 - 12h00)</option>
                                                <option value="Chiều" <?= isset($thoiGianHenLay) && $thoiGianHenLay == 'Chiều' ? 'selected' : '' ?>>Chiều (13h30 - 18h00)</option>
                                                <option value="Tối" <?= isset($thoiGianHenLay) && $thoiGianHenLay == 'Tối' ? 'selected' : '' ?>>Tối (18h30 - 21h00)</option>
                                                <option value="Cả ngày" <?= isset($thoiGianHenLay) && $thoiGianHenLay == 'Cả ngày' ? 'selected' : '' ?>>Cả ngày</option>
                                            </select>
                                            <span class="text-danger"><?= $thoiGianHenLayError ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card người nhận -->
                                <div class="card form-group box-sender">
                                    <div class="card-header">
                                        <div class="float-right" style="font-size: 20px; font-weight:600;">Người nhận</div>
                                    </div>
                                    <div class="card-body">
                                        <label class="form-label my-1" style="font-size: 16px;">Họ tên:</label>
                                        <input class="form-control" type="text" name="hoten" id="hoten" placeholder="Họ tên" value="<?= $hoTenNguoiNhan ?>">
                                        <span class="text-danger"><?php echo "$hoTenNguoiNhanError </br>" ?></span>

                                        <label class="form-label my-1" style="font-size: 16px;">Điện thoại:</label>
                                        <input class="form-control" type="text" name="sdt" id="sdt" placeholder="Số điện thoại" value="<?= $sdtNguoiNhan ?>">
                                        <span class="text-danger"><?php echo "$sdtNguoiNhanError </br>" ?></span>

                                        <label class="form-label my-1" style="font-size: 16px;">Địa chỉ:</label>
                                        <input class="form-control" style="border: 1px solid #ff5f13;" type="text" name="diachi_display" id="diachi_display" placeholder="Hãy nhập các trường bên dưới, để tạo ra địa chỉ" value="<?= $diaChiNguoiNhan ?>" disabled>
                                        <input type="hidden" name="diachi" id="diachi" value="<?= $diaChiNguoiNhan ?>">
                                        <span class="text-danger"><?= $diaChiNguoiNhanError ?></span>

                                        <div class="row">
                                            <div class="col-6 my-1">
                                                <!-- <input class="form-control" type="text" name="thanhpho" id="thanhpho" placeholder="Tỉnh/ Thành Phố" value="">
                                                <span class="text-danger"><?= $thanhPhoError ?></span> -->

                                                <div class="select-items">
                                                    <select id="thanhpho" name="thanhpho" size="5">
                                                        <option value="" selected>Chọn tỉnh</option>
                                                        <?php foreach ($vietnamProvinces as $province) : ?>
                                                            <option value="<?= $province ?>" <?= $province === $thanhPho ? 'selected' : '' ?>><?= $province ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <span class="text-danger"><?= $thanhPhoError ?></span>
                                                </div>
                                            </div>
                                            <div class="col-6 my-1">
                                                <input class="form-control" type="text" name="quan" id="quan" placeholder="Quận/ Huyện" value="<?= $quan ?>">
                                                <span class="text-danger"><?= $quanError ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4 my-1">
                                                <input class="form-control" type="text" name="phuong" id="phuong" placeholder="Xã/ Phường" value="<?= $phuong ?>">
                                                <span class="text-danger"><?= $phuongError ?></span>
                                            </div>
                                            <div class="col-4 my-1">
                                                <input class="form-control" type="text" name="duong" id="duong" placeholder="Đường" value="<?= $duong ?>">
                                                <span class="text-danger"><?= $duongError ?></span>
                                            </div>
                                            <div class="col-4 my-1">
                                                <input class="form-control" type="text" name="sonha" id="sonha" placeholder="Số nhà" value="<?= $sonha ?>">
                                                <span class="text-danger"><?= $sonhaError ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label my-1" style="font-size: 16px; display: block; margin-bottom: 8px;">Thời gian hẹn giao:</label>
                                            <select style="display: block;" name="tghengiao" id="tghengiao">
                                                <option value="">Chọn thời gian</option>
                                                <option value="Sáng" <?= isset($thoiGianHenGiao) && $thoiGianHenGiao == 'Sáng' ? 'selected' : '' ?>>Sáng (7h30 - 12h00)</option>
                                                <option value="Chiều" <?= isset($thoiGianHenGiao) && $thoiGianHenGiao == 'Chiều' ? 'selected' : '' ?>>Chiều (13h30 - 18h00)</option>
                                                <option value="Tối" <?= isset($thoiGianHenGiao) && $thoiGianHenGiao == 'Tối' ? 'selected' : '' ?>>Tối (18h30 - 21h00)</option>
                                                <option value="Cả ngày" <?= isset($thoiGianHenGiao) && $thoiGianHenGiao == 'Cả ngày' ? 'selected' : '' ?>>Cả ngày</option>
                                            </select>
                                            <span class="text-danger"><?= $thoiGianHenGiaoError ?></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <!-- Card thông tin hàng hóa -->
                                <div class="card form-group box-sender">
                                    <div class="card-header">
                                        <div class="float-right" style="font-size: 20px; font-weight:600;">Thông tin hàng hóa</div>
                                    </div>
                                    <div class="card-body ">
                                        <div style="font-size: 17px; font-weight:600;">Loại hàng hóa</div>
                                        <div class="d-flex">
                                            <div class="form-check m-3">
                                                <input class="form-check-input" type="radio" name="loaiHangHoa" id="loaiHangHoa" value="Bưu kiện" <?php if (isset($loaiHangHoa) && $loaiHangHoa == 'Bưu kiện') echo 'checked'; ?> style="width: 15px; height: 15px;">
                                                <label class="form-check-label" for="Bưu kiện" style="font-size: 15px;">
                                                    Bưu kiện
                                                </label>
                                            </div>
                                            <div class="form-check m-3">
                                                <input class="form-check-input" type="radio" name="loaiHangHoa" id="loaiHangHoa" value="Tài liệu" <?php if (isset($loaiHangHoa) && $loaiHangHoa == 'Tài liệu') echo 'checked'; ?> style="width: 15px; height: 15px;">
                                                <label class="form-check-label" for="Tài liệu" style="font-size: 15px;">
                                                    Tài liệu
                                                </label>
                                            </div>
                                        </div>
                                        <span class="text-danger"><?= $loaiHangHoaError ?></span>

                                        <hr>

                                        <div id="items">
                                            <div class="item">
                                                <label class="form-label my-1" style="font-size: 16px;">Tên hàng 1:</label>
                                                <input class="form-control" type="text" name="tenhang1" id="tenhang1" placeholder="Tên Hàng" value="">


                                                <div class="row">
                                                    <div class="col-4 my-1">
                                                        <input class="form-control soluong" type="number" name="soluong1" id="soluong1" placeholder="Số lượng" value="" required min="0" step="0.01">
                                                        <div class="invalid-feedback">Vui lòng nhập một số hợp lệ.</div>
                                                    </div>
                                                    <div class="col-4 my-1">
                                                        <input class="form-control trongluong" type="number" name="trongluong1" id="trongluong1" placeholder="Trọng lượng (g)" value="" required min="0" step="0.01">
                                                        <div class="invalid-feedback">Vui lòng nhập một số hợp lệ.</div>
                                                    </div>
                                                    <div class="col-4 my-1">
                                                        <input class="form-control giatien" type="number" name="giatien1" id="giatien1" placeholder="Giá trị hàng (đ)" value="" required min="0" step="0.01" oninput="tinhTongTien()">
                                                        <div class="invalid-feedback">Vui lòng nhập một số hợp lệ.</div>
                                                    </div>
                                                </div>

                                                <span class="text-danger"><?= $itemError ?></span>
                                            </div>
                                        </div>
                                        <hr>

                                        <div id="addItem" class="btn btn-dark mt-3">Thêm Hàng</div>
                                        <div id="removeItem" class="btn btn-dark mt-3">Xóa</div>

                                        <hr>

                                        <div class="row no-gutters">
                                            <div class="col-9">Tổng giá trị:</div>
                                            <div class="col-3"><span id="tongTien"></span></div>
                                        </div>

                                        <hr>
                                        <div style="font-size: 17px; font-weight:600; padding-bottom:10px;">Tính chất hàng hóa đặc biệt</div>
                                        <div class="row no-gutters">
                                            <div class="col-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="giaTriCao" id="giaTriCao" value="Giá trị cao" <?php if (isset($tinhChatHangHoaDacBiet) && in_array('Giá trị cao', $tinhChatHangHoaDacBiet)) echo 'checked'; ?>>
                                                    <label class="form-check-label" for="giaTriCao">Giá trị cao</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="deVo" id="deVo" value="Dễ vỡ" <?php if (isset($tinhChatHangHoaDacBiet) && in_array('Dễ vỡ', $tinhChatHangHoaDacBiet)) echo 'checked'; ?>>
                                                    <label class="form-check-label" for="deVo">Dễ vỡ</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="nguyenKhoi" id="nguyenKhoi" value="Nguyên khối" <?php if (isset($tinhChatHangHoaDacBiet) && in_array('Nguyên khối', $tinhChatHangHoaDacBiet)) echo 'checked'; ?>>
                                                    <label class="form-check-label" for="nguyenKhoi">Nguyên khối</label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="quaKho" id="quaKho" value="Quá khổ" <?php if (isset($tinhChatHangHoaDacBiet) && in_array('Quá khổ', $tinhChatHangHoaDacBiet)) echo 'checked'; ?>>
                                                    <label class="form-check-label" for="quaKho">Quá khổ</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="chatLong" id="chatLong" value="Chất lỏng" <?php if (isset($tinhChatHangHoaDacBiet) && in_array('Chất lỏng', $tinhChatHangHoaDacBiet)) echo 'checked'; ?>>
                                                    <label class="form-check-label" for="chatLong">Chất lỏng</label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="pin" id="pin" value="Từ tính, Pin" <?php if (isset($tinhChatHangHoaDacBiet) && in_array('Từ tính, Pin', $tinhChatHangHoaDacBiet)) echo 'checked'; ?>>
                                                    <label class="form-check-label" for="pin">Từ tính, Pin</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="hangLanh" id="hangLanh" value="HangLanh" <?php if (isset($tinhChatHangHoaDacBiet) && in_array('Hàng lạnh', $tinhChatHangHoaDacBiet)) echo 'checked'; ?>>
                                                    <label class="form-check-label" for="hangLanh">Hàng lạnh</label>
                                                </div>
                                            </div>
                                            <span class="text-danger"><?= $tinhChatHangHoaDacBietError ?></span>
                                        </div>

                                    </div>
                                </div>

                                <!-- Card thông tin hàng hóa -->
                                <div class="card form-group box-sender">
                                    <div class="card-body">
                                        <div class="row no-gutters">
                                            <div class="col-6">
                                                <div style="font-size: 17px; font-weight:600;">Tiền thu hộ</div>
                                                <!-- <div class="form-check m-2">
                                                    <input class="form-check-input" type="checkbox" id="bangtienhang" value="bangtienhang">
                                                    <label class="form-check-label" for="bangtienhang">Thu hộ bằng tiền hàng</label>
                                                </div> -->
                                                <input class="form-control my-2" type="text" name="thuho" id="thuho" placeholder="Tiền thu hộ" value="">

                                                <label class="form-label my-1" style="font-size: 16px;">Ghi chú:</label>
                                                <textarea class="form-control" style="height:auto;" name="ghichu" id="ghichu" rows="3" placeholder="Nhập ghi chú tại đây..."><?= $ghiChu ?></textarea>
                                            </div>

                                            <div class="col-6" style="padding-left: 20px;">
                                                <div style="font-size: 17px; font-weight:600;">Người trả cước</div>
                                                <div class="d-flex">
                                                    <div class="form-check m-3">
                                                        <input class="form-check-input" type="radio" name="ngtracuoc" id="ngtracuoc" value="Người gửi" <?php if (isset($nguoiTraCuoc) && $nguoiTraCuoc == 'Người gửi') echo 'checked'; ?> style="width: 15px; height: 15px;">
                                                        <label class="form-check-label" for="ngtracuoc" style="font-size: 15px;">
                                                            Người gửi
                                                        </label>
                                                    </div>
                                                    <div class="form-check m-3">
                                                        <input class="form-check-input" type="radio" name="ngtracuoc" id="ngtracuoc" value="Người nhận" <?php if (isset($nguoiTraCuoc) && $nguoiTraCuoc == 'Người nhận') echo 'checked'; ?> style="width: 15px; height: 15px;">
                                                        <label class="form-check-label" for="ngtracuoc" style="font-size: 15px;">
                                                            Người nhận
                                                        </label>
                                                    </div>
                                                </div>
                                                <span class="text-danger"><?= $nguoiTraCuocError ?></span>

                                                <div style="font-size: 17px; font-weight:600;">Loại vận chuyển</div>
                                                <div>
                                                    <div class="form-check m-2">
                                                        <input class="form-check-input" type="radio" name="loaivanchuyen" id="loaivanchuyen" value="Bình thường" <?php if (isset($loaiVanChuyen) && $loaiVanChuyen == 'Bình thường') echo 'checked'; ?> style="width: 15px; height: 15px;">
                                                        <label class="form-check-label" for="loaivanchuyen" style="font-size: 15px;">
                                                            Bình thường
                                                        </label>
                                                    </div>
                                                    <div class="form-check m-2">
                                                        <input class="form-check-input" type="radio" name="loaivanchuyen" id="loaivanchuyen" value="Nhanh" <?php if (isset($loaiVanChuyen) && $loaiVanChuyen == 'Nhanh') echo 'checked'; ?> style="width: 15px; height: 15px;">
                                                        <label class="form-check-label" for="loaivanchuyen" style="font-size: 15px;">
                                                            Nhanh
                                                        </label>
                                                    </div>
                                                    <div class="form-check m-2">
                                                        <input class="form-check-input" type="radio" name="loaivanchuyen" id="loaivanchuyen" value="Hỏa tốc" <?php if (isset($loaiVanChuyen) && $loaiVanChuyen == 'Hỏa tốc') echo 'checked'; ?> style="width: 15px; height: 15px;">
                                                        <label class="form-check-label" for="loaivanchuyen" style="font-size: 15px;">
                                                            Hỏa tốc
                                                        </label>
                                                    </div>
                                                </div>
                                                <span class="text-danger"><?= $loaiVanChuyenError ?></span>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- <div class="card form-group box-sender">
                            <div class="card-header">
                                Cước phí
                            </div>
                            <div class="card-body">
                                <div class="row no-gutters">
                                    <div class="col-9" style="font-size: 20px; font-weight:700;">Tiền cước:</div>
                                    <div class="col-3" style="font-size: 20px; font-weight:700;">Nội dung 2</div>
                                </div>


                                <div class="row no-gutters">
                                    <div class="col-9" style="font-size: 20px; font-weight:700;">Thu hộ:</div>
                                    <div class="col-3" style="font-size: 20px; font-weight:700;"><span id="tongTien1"></span></div>
                                </div>
                            </div>
                        </div> -->

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-dark mt-3 w-100" style="font-size: 20px; font-weight:900;">TẠO PHIẾU</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>

</main>

<script>
    let itemCount = 1;
    document.getElementById('addItem').addEventListener('click', function() {
        itemCount++;
        let newItem = `
                <div class="item">
                    <label class="form-label my-1" style="font-size: 16px;">Tên hàng ${itemCount}:</label>
                    <input class="form-control" type="text" name="tenhang${itemCount}" id="tenhang${itemCount}" placeholder="Tên Hàng" value="">

                    <div class="row">
                        <div class="col-4 my-1">
                            <input class="form-control soluong" type="number" name="soluong${itemCount}" id="soluong${itemCount}" placeholder="Số lượng" value="" required min="0" step="0.01">
                            <div class="invalid-feedback">Vui lòng nhập một số hợp lệ</div>
                        </div>
                        <div class="col-4 my-1">
                            <input class="form-control trongluong" type="number" name="trongluong${itemCount}" id="trongluong${itemCount}" placeholder="Trọng lượng (g)" value="" required min="0" step="0.01">
                            <div class="invalid-feedback">Vui lòng nhập một số hợp lệ</div>
                        </div>
                        <div class="col-4 my-1">
                            <input class="form-control giatien" type="number" name="giatien${itemCount}" id="giatien${itemCount}" placeholder="Giá trị hàng (đ)" value="" required min="0" step="0.01" oninput="tinhTongTien()">
                            <div class="invalid-feedback">Vui lòng nhập một số hợp lệ</div>
                        </div>
                    </div>
                </div>
            `;
        document.getElementById('items').insertAdjacentHTML('beforeend', newItem);
        // Gọi hàm attachInputEvent sau khi thêm phần tử mới
        attachInputEvent();
        validateInputs();
    });

    document.getElementById('removeItem').addEventListener('click', function() {
        let items = document.querySelectorAll('#items .item');
        if (items.length > 1) {
            items[items.length - 1].remove();
            tinhTongTien();
        } else {
            alert('Phải có ít nhất một mục hàng.');
        }
    });


    function validateInputs() {
        document.querySelectorAll('.giatien, .trongluong, .soluong').forEach(element => {
            element.addEventListener('input', function() {
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                } else {
                    this.classList.add('is-invalid');
                }
            });
        });
    }


    validateInputs();




    function tinhTongTien() {
        let tongTien = 0;
        let giatienElements = document.querySelectorAll('.giatien');

        giatienElements.forEach(element => {
            let giatien = parseFloat(element.value);
            if (!isNaN(giatien)) {
                tongTien += giatien;
            }
        });

        tongTien = Math.round(tongTien);

        document.getElementById('tongTien').textContent = tongTien + ' VND';
        // document.getElementById('tongTien1').textContent = tongTien + ' VND';
        document.getElementById('thuho').value = tongTien;
    }

    // Gọi hàm tinhTongTien mỗi khi giá trị trong input thay đổi
    document.querySelectorAll('.giatien').forEach(element => {
        element.addEventListener('input', tinhTongTien);
    });

    // Hàm để gắn sự kiện input cho các phần tử có class giatien
    function attachInputEvent() {
        document.querySelectorAll('.giatien').forEach(element => {
            element.removeEventListener('input', tinhTongTien); // Đảm bảo không gắn sự kiện nhiều lần
            element.addEventListener('input', tinhTongTien);
        });
    }
    attachInputEvent();


    document.addEventListener('DOMContentLoaded', function() {
        // Hàm cập nhật địa chỉ chi tiết
        function updateAddress() {
            var thanhpho = document.getElementById('thanhpho').value;
            var quan = document.getElementById('quan').value;
            var phuong = document.getElementById('phuong').value;
            var duong = document.getElementById('duong').value;
            var sonha = document.getElementById('sonha').value;

            // Tạo địa chỉ từ các trường nhập
            var address = `${sonha} ${duong}, ${phuong}, ${quan}, ${thanhpho}`;

            // Cập nhật giá trị vào trường địa chỉ chi tiết
            document.getElementById('diachi').value = address;
            document.getElementById('diachi_display').value = address;
        }

        // Lắng nghe sự thay đổi trên các trường nhập
        document.getElementById('thanhpho').addEventListener('change', updateAddress);
        document.getElementById('quan').addEventListener('input', updateAddress);
        document.getElementById('phuong').addEventListener('input', updateAddress);
        document.getElementById('duong').addEventListener('input', updateAddress);
        document.getElementById('sonha').addEventListener('input', updateAddress);
    });
</script>

<!-- =============================================================================================== -->
<?php
} else {
    header("Location: dangky.php");
    exit;
}
?>

<!-- =============================================================================================== -->
<?php
require_once "inc/footer.php";
?>