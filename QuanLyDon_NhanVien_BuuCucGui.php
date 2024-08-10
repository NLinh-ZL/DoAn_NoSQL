<?php ob_start();
require_once "inc/header1.php";
require_once "class/Database.php";
require "class/VanDon.php";
require "class/BuuCuc.php";
require_once "class/API.php";

//Tạo số trang
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10;
// $offset = ($page - 1) * $limit;

$buucuc = BuuCuc::getAllBuuCuc($pdo);
$NhanVienDangNhap = BuuCuc::getNhanVienById($pdo,  isset($_SESSION['logged_id']) ?  $_SESSION['logged_id'] : null);
// $db = new Database();
// $database = $db->getConnect();
$collection = $pdo->VanDon; // Chọn bộ sưu tập 'VanDon'

// $idKhachHang = isset($_SESSION['logged_iduser']) ? $_SESSION['logged_iduser']: 0;
if (isset($_SESSION['logged_id'])) {
    $idKhachHang = $_SESSION['logged_id'];
}
// Kiểm tra bộ sưu tập
if ($collection === null) {
    die("Bộ sưu tập không tồn tại: VanDon");
}

// Tìm các đơn hàng của khách hàng có idKhachHang là 4

$active = isset($_GET['active']) && $_GET['active'] !== '' ? $_GET['active'] : null;

// var_dump($active);
$total_pages_CanXacNhan = VanDon::countAllBuuCuc($pdo, $NhanVienDangNhap['idBC'], $limit, 'Vận chuyển');
$total_pages_CanGiao = VanDon::countAllBuuCuc($pdo, $NhanVienDangNhap['idBC'], $limit, 'Đơn cần giao');
$total_pages_GiaoThanhCong = VanDon::countAllBuuCuc($pdo, $NhanVienDangNhap['idBC'], $limit, 'Chuyển đến bưu cục');
$total_pages_HuyGiao = VanDon::countAllBuuCuc($pdo, $NhanVienDangNhap['idBC'], $limit, 'Hủy giao hàng');

// $totalDocuments = VanDon::countTotalDocuments($pdo);

// $idKhachHang = 4;
$customerOrdersCount = $collection->countDocuments(['idKhachHang' => $idKhachHang]);
// $TongDon = $collection->find(['idKhachHang' => $idKhachHang]);
// $GiaoThanhCong = $collection->find(['idKhachHang' => $idKhachHang, 'tinhTrang' => 'Giao hàng thành công']);
// $HuyGiao = $collection->find(['idKhachHang' => $idKhachHang, 'tinhTrang' => 'Hủy giao']);
// $DangGiao = $collection->find(['idKhachHang' => $idKhachHang, 'tinhTrang' => 'Đang giao']);
// $TongDon = VanDon::getAllpage($pdo, $limit, $offset);
// $GiaoThanhCong = VanDon::getAllpage($pdo, $limit, $offset, 'Giao hàng thành công');
// $HuyGiao = VanDon::getAllpage($pdo, $limit, $offset, 'Hủy giao');
// $DangGiao = VanDon::getAllpage($pdo, $limit, $offset, 'Đang giao');
// Lấy nhân viên giao hàng
$deliveryStaff = BuuCuc::getAllDeliveryStaff($pdo, $NhanVienDangNhap['idBC']);

$Nhanvien = isset($_GET['idNhanVien']) ? BuuCuc::getNhanVienById($pdo, $_GET['idNhanVien']) : null;

if (isset($_GET['action']) && isset($_GET['idVD'])) {
    $action = $_GET['action'];
    $idVD = $_GET['idVD'];


    if ($action == 'xacnhan') {

        VanDon::XacNhanVD_BuuCuc($pdo, $idVD, 'Đơn cần giao', $NhanVienDangNhap['idNV'], $NhanVienDangNhap['idBC'], $NhanVienDangNhap['tenBC'], $NhanVienDangNhap['diaChi']);
        header("Location: QuanLyDon_NhanVien_BuuCucGui.php");
        exit();
    }
    if (isset($Nhanvien)) {
        if ($action == 'xacNhanGiao') {
            VanDon::XacNhanVD_BuuCuc($pdo, $idVD, 'Đang giao', $Nhanvien['idNV'], $Nhanvien['idBC'], $Nhanvien['tenBC'], $Nhanvien['diaChi']);
            // header("Location: QuanLyDon_NhanVien.php?active=LayThanhCong&page=" . urlencode($page));
            // exit();
        }
    } else if ($action == 'HuyGiao') {
        VanDon::LayHang_Shipper($pdo, $idVD, 'Giao hàng thất bại', $NhanVienDangNhap['idNV'], $NhanVienDangNhap['idBC'], $NhanVienDangNhap['hoTen'], $NhanVienDangNhap['tenBC'], $NhanVienDangNhap['diaChi']);
        VanDon::updateTrangThaiVanDon($pdo, $idVD, 'Giao hàng thất bại');
    } else if ($action == 'chuyenhang') {
        if (isset($_GET['idBC'])) {
            $idBC = $_GET['idBC'];
            $BuuCuc = BuuCuc::getBuuCucById($pdo, $idBC);

            VanDon::VanChuyen($pdo, $idVD, 'Vận chuyển', $BuuCuc['idBC'], $NhanVienDangNhap['hoTen'], $BuuCuc['tenBC'], $BuuCuc['diaChi']);
        }
    }
}

$buucucDiachiArray = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['BuuCuc_diachi'])) {

    foreach ($_POST['BuuCuc_diachi'] as $diachi) {
        $buucucDiachiArray[] = $diachi;
    }
}
// $buucucDiachiArray = isset($_POST['BuuCuc_diachi']) ? $_POST['BuuCuc_diachi'] : [];
var_dump("$page");
print_r($buucucDiachiArray);
ob_end_flush();
?>
<link href="./demo.css" rel="stylesheet" />

<div class="container">
    <div class="page-inner">

        <div class="row">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Quản lý vận đơn</h4>

                        <style>
                            .form-row {
                                display: flex;
                                align-items: center;
                            }

                            .form-group {
                                display: flex;
                                align-items: center;
                                /* Thay đổi khoảng cách giữa các phần tử nếu cần */
                            }

                            .form-group label {
                                margin-right: 10px;
                                /* Thay đổi khoảng cách giữa nhãn và phần tử select */
                            }


                            .nav-pills.nav-secondary .nav-link.active {
                                background: #fff5f7;
                                /* Màu nền */
                                border: 1px solid #fff5f7;
                                /* Màu chữ */
                                position: relative;
                                /* Để sử dụng ::before */

                            }

                            .nav-pills.nav-secondary .nav-link.active::before {
                                content: "";
                                position: absolute;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 5px;
                                /* Độ dày của viền trên */
                                background: #dc3545;
                                /* Màu của viền trên */
                            }
                        </style>

                        <div class="form-row">
                            <div class="form-group">
                                <div class="input-icon">
                                    <input type="text" class="form-control" placeholder="Search for..." />
                                    <span class="input-icon-addon">
                                        <i class="fa fa-search"></i>
                                    </span>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="exampleFormControlSelect1">Người trả cước:</label>
                                <select class="form-select" id="exampleFormControlSelect1">
                                    <option>tất cả</option>
                                    <option>Người gửi</option>
                                    <option>Người nhận</option>
                                </select>
                            </div>
                            <div id="date-range-picker" date-rangepicker class="flex items-center">
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                        </svg>
                                    </div>
                                    <input id="datepicker-range-start" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date start">

                                </div>
                                <span class="mx-4 text-gray-500">to</span>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                        </svg>
                                    </div>
                                    <input id="datepicker-range-end" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date end">
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills nav-secondary" style=" background: #f8f8fa; margin-bottom:15px" id="pills-tab" role="tablist">
                            <li class="nav-itemw flex-fill text-center">
                                <a class="nav-link <?= ($active == null || $active == 'ChoXacNhan')  ? 'active' : '' ?>" style="padding: 20px; margin: 0px;  " id="pills-home-tab" data-bs-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">
                                    <h5 style="color: #000;">
                                        <svg fill="none" style="margin-bottom:2px;" height="21" viewBox="0 0 18 21" width="18" xmlns="http://www.w3.org/2000/svg">
                                            <path clip-rule="evenodd" d="M7 0.5H11C12.1046 0.5 13 1.39543 13 2.5C13 3.60457 12.1046 4.5 11 4.5H7C5.89543 4.5 5 3.60457 5 2.5C5 1.39543 5.89543 0.5 7 0.5ZM3.50733 2.53003C3.50247 2.60272 3.5 2.67607 3.5 2.74999C3.5 4.54491 4.95507 5.99999 6.75 5.99999H11.25C13.0449 5.99999 14.5 4.54491 14.5 2.74999C14.5 2.67607 14.4975 2.60272 14.4927 2.53003C16.4694 2.77282 18 4.45766 18 6.49999V16.5C18 18.7091 16.2091 20.5 14 20.5H4C1.79086 20.5 0 18.7091 0 16.5V6.49999C0 4.45766 1.53062 2.77282 3.50733 2.53003ZM4.25 8.5C4.25 8.08579 4.58579 7.75 5 7.75H13C13.4142 7.75 13.75 8.08579 13.75 8.5C13.75 8.91421 13.4142 9.25 13 9.25H5C4.58579 9.25 4.25 8.91421 4.25 8.5ZM5 11.75C4.58579 11.75 4.25 12.0858 4.25 12.5C4.25 12.9142 4.58579 13.25 5 13.25H13C13.4142 13.25 13.75 12.9142 13.75 12.5C13.75 12.0858 13.4142 11.75 13 11.75H5ZM4.25 16.5C4.25 16.0858 4.58579 15.75 5 15.75H9C9.41421 15.75 9.75 16.0858 9.75 16.5C9.75 16.9142 9.41421 17.25 9 17.25H5C4.58579 17.25 4.25 16.9142 4.25 16.5Z" fill="#CECECE" fill-rule="evenodd"></path>
                                        </svg>
                                        Đơn hàng cần xác nhận
                                    </h5>
                                    <!-- <h6 style="color: #000;"><?= ($customerOrdersCount = $collection->countDocuments(['idKhachHang' => $idKhachHang, 'quyTrinhVC.trangthai' => 'Chờ xác nhận'])) ?> đơn hàng</h6> -->
                                </a>
                            </li>
                            <li class="nav-itemw flex-fill text-center">
                                <a class="nav-link <?= $active === 'CanGiao'  ? 'active' : '' ?>" style="padding: 20px;margin: 0px;" id="pills-contact-tab" data-bs-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">
                                    <h5 style="color: #000;">
                                        <svg fill="none" style="margin-bottom:2px;" height="22" viewBox="0 0 21 22" width="21" xmlns="http://www.w3.org/2000/svg">
                                            <path clip-rule="evenodd" d="M21 10.4999C21 16.0227 16.5228 20.4999 11 20.4999C10.4477 20.4999 10 20.0522 10 19.4999V16.4999C10 15.3953 9.10457 14.4999 8 14.4999H3C1.89543 14.4999 1 13.6044 1 12.4999V10.4999C1 4.97703 5.47715 0.499878 11 0.499878C16.5228 0.499878 21 4.97703 21 10.4999ZM11 3.74988C10.5858 3.74988 10.25 4.08566 10.25 4.49988V8.64526C9.51704 8.94195 9 9.66053 9 10.4999C9 11.6044 9.89543 12.4999 11 12.4999C11.2934 12.4999 11.572 12.4367 11.8231 12.3232C11.8588 12.3981 11.9077 12.4682 11.9697 12.5302L13.4697 14.0302C13.7626 14.3231 14.2374 14.3231 14.5303 14.0302C14.8232 13.7373 14.8232 13.2624 14.5303 12.9695L13.0303 11.4695C12.9683 11.4076 12.8982 11.3587 12.8233 11.3229C12.9368 11.0719 13 10.7933 13 10.4999C13 9.66053 12.483 8.94195 11.75 8.64526V4.49988C11.75 4.08566 11.4142 3.74988 11 3.74988ZM0.25 20.4999C0.25 20.0857 0.585786 19.7499 1 19.7499H7C7.41421 19.7499 7.75 20.0857 7.75 20.4999C7.75 20.9141 7.41421 21.2499 7 21.2499H1C0.585786 21.2499 0.25 20.9141 0.25 20.4999ZM1 16.7499C0.585786 16.7499 0.25 17.0857 0.25 17.4999C0.25 17.9141 0.585786 18.2499 1 18.2499H5C5.41421 18.2499 5.75 17.9141 5.75 17.4999C5.75 17.0857 5.41421 16.7499 5 16.7499H1Z" fill="#CECECE" fill-rule="evenodd"></path>
                                        </svg>

                                        Đơn hàng cần giao
                                    </h5>
                                    <!-- <h6 style="color: #000;"><?= ($customerOrdersCount = $collection->countDocuments(['idKhachHang' => $idKhachHang, 'quyTrinhVC.trangthai' => 'Đang lấy'])) ?> đơn hàng</h6> -->
                                </a>
                            </li>
                            <li class="nav-itemw flex-fill text-center">
                                <a class="nav-link <?= $active === 'GiaoThanhCong'  ? 'active' : '' ?>" style="padding: 20px; margin: 0px;" id="pills-profile-tab" data-bs-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">
                                    <h5 style="color: #000;">
                                        <svg fill="none" style="margin-bottom:2px;" height="25" viewBox="0 0 24 25" width="24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.6 5.81L11.95 2.77C11.35 2.45 10.64 2.45 10.04 2.77L4.39998 5.81C3.98998 6.04 3.72998 6.48 3.72998 6.96C3.72998 7.45 3.97998 7.89 4.39998 8.11L10.05 11.15C10.35 11.31 10.68 11.39 11 11.39C11.32 11.39 11.66 11.31 11.95 11.15L17.6 8.11C18.01 7.89 18.27 7.45 18.27 6.96C18.27 6.48 18.01 6.04 17.6 5.81Z" fill="#CECECE"></path>
                                            <path d="M9.12 12.21L3.87 9.59C3.46 9.38 3 9.41 2.61 9.64C2.23 9.88 2 10.29 2 10.74V15.7C2 16.56 2.48 17.33 3.25 17.72L8.5 20.34C8.68 20.43 8.88 20.48 9.08 20.48C9.31 20.48 9.55 20.41 9.76 20.29C10.14 20.05 10.37 19.64 10.37 19.19V14.23C10.36 13.37 9.88 12.6 9.12 12.21Z" fill="#CECECE"></path>
                                            <path d="M20.0001 10.74V13.2C19.5201 13.06 19.0101 13 18.5001 13C17.1401 13 15.8101 13.47 14.7601 14.31C13.3201 15.44 12.5001 17.15 12.5001 19C12.5001 19.49 12.5601 19.98 12.6901 20.45C12.5401 20.43 12.3901 20.37 12.2501 20.28C11.8701 20.05 11.6401 19.64 11.6401 19.19V14.23C11.6401 13.37 12.1201 12.6 12.8801 12.21L18.1301 9.59C18.5401 9.38 19.0001 9.41 19.3901 9.64C19.7701 9.88 20.0001 10.29 20.0001 10.74Z" fill="#CECECE"></path>
                                            <path d="M21.98 16.17C21.16 15.16 19.91 14.52 18.5 14.52C17.44 14.52 16.46 14.89 15.69 15.51C14.65 16.33 14 17.6 14 19.02C14 19.86 14.24 20.66 14.65 21.34C14.92 21.79 15.26 22.18 15.66 22.5H15.67C16.44 23.14 17.43 23.52 18.5 23.52C19.64 23.52 20.67 23.1 21.46 22.4C21.81 22.1 22.11 21.74 22.35 21.34C22.76 20.66 23 19.86 23 19.02C23 17.94 22.62 16.94 21.98 16.17ZM20.76 18.46L18.36 20.68C18.22 20.81 18.03 20.88 17.85 20.88C17.66 20.88 17.47 20.81 17.32 20.66L16.21 19.55C15.92 19.26 15.92 18.78 16.21 18.49C16.5 18.2 16.98 18.2 17.27 18.49L17.87 19.09L19.74 17.36C20.04 17.08 20.52 17.1 20.8 17.4C21.09 17.71 21.07 18.18 20.76 18.46Z" fill="#CECECE"></path>
                                        </svg>

                                        Giao thành công
                                    </h5>
                                    <!-- <h6 style="color: #000;"><?= ($customerOrdersCount = $collection->countDocuments(['idKhachHang' => $idKhachHang, 'quyTrinhVC.trangthai' => 'Lấy thành công'])) ?> đơn hàng</h6> -->
                                </a>
                            </li>
                           
                            <li class="nav-itemw flex-fill text-center">
                                <a class="nav-link <?= $active === 'HuyGiao'  ? 'active' : '' ?>" style="padding: 20px;margin: 0px;" id="pills-huy-tab" data-bs-toggle="pill" href="#pills-huy" role="tab" aria-controls="pills-huy" aria-selected="false">
                                    <h5 style="color: #000;">
                                        <svg fill="none" style="margin-bottom:2px;" height="25" viewBox="0 0 24 25" width="24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.6005 5.81L11.9505 2.77C11.3505 2.45 10.6405 2.45 10.0405 2.77L4.40047 5.81C3.99047 6.04 3.73047 6.48 3.73047 6.96C3.73047 7.45 3.98047 7.89 4.40047 8.11L10.0505 11.15C10.3505 11.31 10.6805 11.39 11.0005 11.39C11.3205 11.39 11.6605 11.31 11.9505 11.15L17.6005 8.11C18.0105 7.89 18.2705 7.45 18.2705 6.96C18.2705 6.48 18.0105 6.04 17.6005 5.81Z" fill="#CECECE"></path>
                                            <path d="M9.12 12.21L3.87 9.59C3.46 9.38 3 9.41 2.61 9.64C2.23 9.88 2 10.29 2 10.74V15.7C2 16.56 2.48 17.33 3.25 17.72L8.5 20.34C8.68 20.43 8.88 20.48 9.08 20.48C9.31 20.48 9.55 20.41 9.76 20.29C10.14 20.05 10.37 19.64 10.37 19.19V14.23C10.36 13.37 9.88 12.6 9.12 12.21Z" fill="#CECECE"></path>
                                            <path d="M19.9996 10.74V13.2C19.5196 13.06 19.0096 13 18.4996 13C17.1396 13 15.8096 13.47 14.7596 14.31C13.3196 15.44 12.4996 17.15 12.4996 19C12.4996 19.49 12.5596 19.98 12.6896 20.45C12.5396 20.43 12.3896 20.37 12.2496 20.28C11.8696 20.05 11.6396 19.64 11.6396 19.19V14.23C11.6396 13.37 12.1196 12.6 12.8796 12.21L18.1296 9.59C18.5396 9.38 18.9996 9.41 19.3896 9.64C19.7696 9.88 19.9996 10.29 19.9996 10.74Z" fill="#CECECE"></path>
                                            <path d="M21.6804 15.82C20.7904 14.93 19.6104 14.48 18.4404 14.5C17.3104 14.51 16.1804 14.96 15.3204 15.82C14.7204 16.41 14.3304 17.15 14.1404 17.92C14.0304 18.34 13.9904 18.77 14.0204 19.2V19.25C14.0204 19.32 14.0304 19.38 14.0404 19.46C14.0404 19.46 14.0404 19.46 14.0504 19.47V19.5C14.1404 20.48 14.5604 21.43 15.3204 22.18C16.4804 23.34 18.1104 23.73 19.5804 23.36C20.0204 23.25 20.4504 23.07 20.8504 22.83C21.1504 22.66 21.4304 22.44 21.6804 22.18C22.4304 21.43 22.8604 20.48 22.9504 19.49C22.9604 19.49 22.9604 19.47 22.9604 19.46C22.9804 19.39 22.9804 19.31 22.9804 19.24C22.9804 19.23 22.9904 19.21 22.9904 19.19C23.0504 17.98 22.6104 16.74 21.6804 15.82ZM20.2304 20.71C19.9404 21 19.4704 21 19.1704 20.71L18.5104 20.05L17.8304 20.73C17.5304 21.03 17.0604 21.03 16.7704 20.73C16.4704 20.44 16.4704 19.97 16.7704 19.67L17.4504 18.99L16.7904 18.33C16.5004 18.03 16.5004 17.56 16.7904 17.27C17.0904 16.97 17.5604 16.97 17.8604 17.27L18.5104 17.93L19.1404 17.29C19.4404 17 19.9104 17 20.2104 17.29C20.5004 17.59 20.5004 18.06 20.2104 18.36L19.5704 18.99L20.2304 19.64C20.5304 19.94 20.5304 20.41 20.2304 20.71Z" fill="#CECECE"></path>
                                        </svg>

                                        Hủy Giao
                                    </h5>
                                    <!-- <h6 style="color: #000;"><?= ($customerOrdersCount = $collection->countDocuments(['idKhachHang' => $idKhachHang, 'quyTrinhVC.trangthai' => 'Hủy giao'])) ?> đơn hàng</h6> -->
                                </a>
                            </li>



                        </ul>
                        <div class="tab-content mt-2 mb-3" id="pills-tabContent" style="border-top: 3px solid #dc3545;">

                         


                            <div class="tab-pane fade  <?= ($active == null || $active == 'ChoXacNhan')  ? 'show active' : '';
                                                        $page = $active !== 'ChoXacNhan' ? 1 : ($_GET['page']); ?> " id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">STT</th>
                                            <th scope="col">Mã vận đơn</th>

                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Tên người nhận</th>

                                            <th scope="col">Loại hàng</th>
                                            <th scope="col">Khối lượng</th>
                                            <th scope="col">Tên Bưu cục gửi</th>
                                            <th scope="col">Xác nhận</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $STT = 1;
                                        $offset = ($page - 1) * $limit;
                                        $ChoXacNhan = VanDon::getDonHangTrangThai($pdo, $NhanVienDangNhap['idBC'], $limit, $offset, 'Vận chuyển');
                                        foreach ($ChoXacNhan as $order) :
                                            $tongKhoiLuong = 0;
                                            foreach ($order['hangHoa'] as $hang) {
                                                $tongKhoiLuong += $hang['trongLuong'];
                                            }
                                        ?>
                                            <tr>
                                                <th scope="row"><?= $STT++ ?></th>
                                                <td><?= $order['idVD'] ?></td>

                                                <td><?= $order['ngayTao']->toDateTime()->format('d-m-Y H:i:s') ?></td>
                                                <td><?= $order['nguoiNhan']['hoTen'] ?></td>

                                                <td><?= $order['loaiHang'] ?></td>
                                                <td><?= $tongKhoiLuong ?> kg</td>
                                                <td><?= $order['lastStatus']['tenBC'] ?> </td>
                                                <td><a href="QuanLyDon_NhanVien_BuuCucGui.php?active=CanGiao&action=xacnhan&idVD=<?= $order['idVD'] ?>">
                                                        <button class="btn btn-success ">
                                                            <span class="btn-label  ">
                                                                <i class="fa fa-check "></i>
                                                            </span>

                                                        </button>
                                                    </a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-end">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="QuanLyDon_NhanVien.php?page=<?= $page - 1 ?>&active=ChoXacNhan">Previous</a>
                                        </li>
                                        <?php for ($i = 1; $i <= $total_pages_CanXacNhan; $i++) : ?>
                                            <li class="page-item  <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="QuanLyDon_NhanVien.php?page=<?= $i ?>&active=ChoXacNhan"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?= $page >= $total_pages_CanXacNhan ? 'disabled' : '' ?>">
                                            <a class="page-link" href="QuanLyDon_NhanVien.php?page=<?= $page + 1 ?>&active=ChoXacNhan">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                            <div class="tab-pane fade   <?= $active === 'CanGiao'  ? 'show active' : '';
                                 $page = $active !== 'CanGiao' ? 1 : ($_GET['page']); ?>" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">STT</th>
                                            <th scope="col">Mã vận đơn</th>

                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Tên người nhận</th>
                                            <th scope="col">Ngày nhận</th>
                                            <th scope="col">Loại hàng</th>
                                            <th scope="col">Khối lượng</th>
                                            <th scope="col">
                                                <div class="btn-group dropdown">
                                                    <button class="btn  dropdown-toggle" type="button" data-bs-toggle="dropdown" style="color:#dc3545 ;font-size: .95rem; text-transform: uppercase; letter-spacing: 1px; padding: 12px 24px !important; border-bottom-width: 1px; font-weight: 600;">
                                                        <?= isset($Nhanvien) && isset($_GET['idNhanVien']) ? $Nhanvien['hoTen'] : ' Nhân viên giao ' ?>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            <a class="dropdown-item" href="QuanLyDon_NhanVien_BuuCucGui.php?page=<?= $page?>&active=CanGiao" style="font-size: .95rem; text-transform: uppercase; letter-spacing: 1px; padding: 12px 24px !important; border-bottom-width: 1px; font-weight: 600;">bỏ chọn</a>

                                                            <?php foreach ($deliveryStaff as $Staff) : ?>
                                                                <a class="dropdown-item" href="QuanLyDon_NhanVien_BuuCucGui.php?page=<?= $page?>&active=CanGiao&idNhanVien=<?= $Staff['idNV'] ?>" style="font-size: .95rem; text-transform: uppercase; letter-spacing: 1px; padding: 12px 24px !important; border-bottom-width: 1px; font-weight: 600;"><?= $Staff['hoTen'] ?></a>
                                                            <?php endforeach; ?>


                                                        </li>
                                                    </ul>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $STT = 1;
                                        $offset = ($page - 1) * $limit;

                                        $DangGiao = VanDon::getDonHangTrangThai($pdo, $NhanVienDangNhap['idBC'], $limit, $offset, 'Đơn cần giao');
                                        foreach ($DangGiao as $order) :
                                            $tongKhoiLuong = 0;
                                            foreach ($order['hangHoa'] as $hang) {
                                                $tongKhoiLuong += $hang['trongLuong'];
                                            }
                                        ?>
                                            <tr>
                                                <th scope="row"><?= $STT++ ?></th>
                                                <td><?= $order['idVD'] ?></td>
                                                <td><?= $order['ngayTao']->toDateTime()->format('Y-m-d H:i:s') ?></td>
                                                <td><?= $order['nguoiNhan']['hoTen'] ?></td>
                                                <td><?= $order['thoiGianHenGiao'] ?></td>
                                                <td><?= $order['loaiHang'] ?></td>
                                                <td><?= $tongKhoiLuong ?> kg</td>

                                                <td><a <?= !isset($_GET['idNhanVien']) ? 'class="disabled"' : '' ?> href="QuanLyDon_NhanVien_BuuCucGui.php?page=<?=$page?>&action=xacNhanGiao&active=CanGiao&idNhanVien=<?= $Nhanvien['idNV'] ?>&idVD=<?= $order['idVD'] ?>">
                                                        <button class="btn btn-success <?= !isset($_GET['idNhanVien']) ? 'disabled' : '' ?>">
                                                            <span class="btn-label  ">
                                                                <i class="fa fa-check "></i>
                                                            </span>

                                                        </button>
                                                    </a>    </td>

                                              <!-- Xuất trạng thái quy trình cuối cùng -->
                                            </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-end">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="QuanLyDon_NhanVien.php?page=<?= $page - 1 ?>&active=CanGiao">Previous</a>
                                        </li>
                                        <?php for ($i = 1; $i <= $total_pages_CanGiao; $i++) : ?>
                                            <li class="page-item  <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="QuanLyDon_NhanVien.php?page=<?= $i ?>&active=CanGiao"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?= $page >= $total_pages_CanGiao ? 'disabled' : '' ?>">
                                            <a class="page-link" href="QuanLyDon_NhanVien.php?page=<?= $page + 1 ?>&active=CanGiao">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                            <div class="tab-pane fade  <?= $active === 'GiaoThanhCong'  ? 'show active' : '';
                                                        $page = $active !== 'GiaoThanhCong' ? 1 : ($_GET['page']); ?> " id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">STT</th>
                                            <th scope="col">Mã vận đơn</th>

                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Tên người nhận</th>
                                            <th scope="col">Tiền cước</th>
                                            <th scope="col">Loại hàng</th>
                                            <th scope="col">Khối lượng</th>
                                            <th scope="col">Xem chi tiết</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $STT = 1;
                                        $offset = ($page - 1) * $limit;;
                                        $GiaoThanhCong = VanDon::getDonHangTrangThai($pdo, $NhanVienDangNhap['idBC'], $limit, $offset, 'Giao hàng thành công');
                                        foreach ($GiaoThanhCong as $order) :
                                            $tongKhoiLuong = 0;
                                            foreach ($order['hangHoa'] as $hang) {
                                                $tongKhoiLuong += $hang['trongLuong'];
                                            }
                                        ?>
                                            <tr>
                                                <th scope="row"><?= $STT++ ?></th>
                                                <td><?= $order['idVD'] ?></td>

                                                <td><?= $order['ngayTao']->toDateTime()->format('Y-m-d H:i:s') ?></td>
                                                <td><?= $order['nguoiNhan']['hoTen'] ?></td>
                                                <td><?= number_format($order['tienCuoc'], 0, ',', '.') ?>vnđ</td>
                                                <td><?= $order['loaiHang'] ?></td>
                                                <td><?= $tongKhoiLuong ?> kg</td>

                                                <td><a href="QuanLyDon_NhanVien.php?page=<?= $page  ?>&action=nhanhang&active=LayThanhCong&idVD=<?= $order['idVD'] ?>">
                                                        <button class="btn btn-success " style="margin: 5px;">
                                                            Xem chi tiết
                                                        </button>
                                                    </a>
                                                   
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <!-- <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-end">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="QuanLyDon_NhanVien.php?page=<?= $page - 1 ?>&active=LayThanhCong">Previous</a>
                                        </li>
                                        <?php for ($i = 1; $i <= $total_pages_ChuyenDenBC; $i++) : ?>
                                            <li class="page-item  <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="QuanLyDon_NhanVien.php?page=<?= $i ?>&active=LayThanhCong"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?= $page >= $total_pages_ChuyenDenBC ? 'disabled' : '' ?>">
                                            <a class="page-link" href="QuanLyDon_NhanVien.php?page=<?= $page + 1 ?>&active=LayThanhCong ">Next</a>
                                        </li>
                                    </ul>
                                </nav> -->
                            </div>
                        
                            <!-- Huy giao ----------------------------------------------------------------------------------------------- -->
                            <div class="tab-pane fade  <?= $active === 'HuyGiao'  ? 'show active' : '';
                                                        $page = $active !== 'HuyGiao' ? 1 : ($_GET['page']); ?>" id="pills-huy" role="tabpanel" aria-labelledby="pills-huy-tab">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">STT</th>
                                            <th scope="col">Mã vận đơn</th>

                                            <th scope="col">Ngày tạo</th>
                                            <th scope="col">Tên người nhận</th>
                                            <th scope="col">Ngày nhận</th>
                                            <th scope="col">Loại hàng</th>
                                            <th scope="col">Khối lượng</th>
                                            <th scope="col">Tình trạng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $STT = 1;
                                        $offset = ($page - 1) * $limit;

                                        $Huygiao = VanDon::getDonHangTrangThai($pdo, $NhanVienDangNhap['idBC'], $limit, $offset, 'Giao hàng thất bại');
                                        foreach ($Huygiao as $order) :
                                            $tongKhoiLuong = 0;
                                            foreach ($order['hangHoa'] as $hang) {
                                                $tongKhoiLuong += $hang['trongLuong'];
                                            }
                                        ?>
                                            <tr>
                                                <th scope="row"><?= $STT++ ?></th>
                                                <td><?= $order['idVD'] ?></td>

                                                <td><?= $order['ngayTao']->toDateTime()->format('Y-m-d H:i:s') ?></td>
                                                <td><?= $order['nguoiNhan']['hoTen'] ?></td>
                                                <td><?= $order['thoiGianHenGiao'] ?></td>
                                                <td><?= $order['loaiHang'] ?></td>
                                                <td><?= $tongKhoiLuong ?> kg</td>
                                                <td><?= $order['tinhTrang'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-end">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="QuanLyDon.php?page=<?= $page - 1 ?>&active=HuyGiao">Previous</a>
                                        </li>
                                        <?php for ($i = 1; $i <= $total_pages_HuyGiao; $i++) : ?>
                                            <li class="page-item  <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="QuanLyDon.php?page=<?= $i ?>&active=HuyGiao"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?= $page >= $total_pages_HuyGiao ? 'disabled' : '' ?>">
                                            <a class="page-link" href="QuanLyDon.php?page=<?= $page + 1 ?>&active=HuyGiao">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
</div>
</div>

<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>


</body>

</html>