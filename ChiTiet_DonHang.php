<?php ob_start();
require_once "inc/header1.php";
require_once "class/Database.php";
require "class/VanDon.php";
require "class/BuuCuc.php";


//Tạo số trang
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10;
// $offset = ($page - 1) * $limit;

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

$total_pages_TongDon = VanDon::countAll($pdo, $limit, null, $idKhachHang);
$total_pages_GiaoThanhCong = VanDon::countAll($pdo, $limit, 'Giao hàng thành công', $idKhachHang);
$total_pages_HuyGiao = VanDon::countAll($pdo, $limit, 'Hủy giao', $idKhachHang);
$total_pages_DangGiao = VanDon::countAll($pdo, $limit, 'Đang giao', $idKhachHang);

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
// $deliveryStaff = BuuCuc::getAllDeliveryStaff($pdo);

// $Nhanvien = isset($_GET['idNhanVien']) ? BuuCuc::getNhanVienById($pdo, $_GET['idNhanVien']) : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idVD = $_POST['idVD'];
    $idVDArray = explode(',', $idVD);

    // $vanDon = vanDon::getvanDonByIdVD($pdo, $idVD);

    // $us = KhachHang::getUserById($pdo, $vanDon['idKhachHang']);
    // $hoTenGui = $us['hoTen'];

    // if (isset($vanDon['tinhChatHang']) && $vanDon['tinhChatHang'] instanceof MongoDB\Model\BSONArray) {
    //     // Chuyển BSONArray thành mảng PHP
    //     $tinhChatHangArray = $vanDon['tinhChatHang']->getArrayCopy();
    // } else {
    //     // Mảng rỗng nếu không có dữ liệu
    //     $tinhChatHangArray = [];
    // }
}

ob_end_flush();
?>
<link href="./demo.css" rel="stylesheet" />

<div class="container">
    <div class="page-inner">
        <div class="card">
            <div class="card-body" style="padding: 40px;">

                <div class="row row-demo-grid">
                    <div class="col">
                        <div class="card">

                            <div class="card-body" style="margin-top: 20px;margin-bottom: 20px;">
                                <h2>Mã phiếu gửi</h2>
                                <h4>(Tra nhiều bill bằng cách thêm dấu phẩy giữa các bill)</h4>
                                <form action="ChiTiet_DonHang.php" method="post">
                                    <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <button type="submit" class="btn btn-search pe-1">
                                                    <i class="fa fa-search search-icon"></i>
                                                </button>
                                            </div>
                                            <input type="text" name="idVD" placeholder="VD: 12345,67899" class="form-control" />
                                        </div>
                                    </nav>
                                    <div class="mt-3">
                                        <button type="submit" class="btn" style="margin-top: 15px;background-color: #dc3545; color: white;">Submit<i class="icon-arrow-right" style="padding: 10px;"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <img src="https://viettelpost.vn/viettelpost-iframe/assets/images/tracking-img.svg" alt="" style="margin-left: 150px;">
                    </div>
                </div>
            </div>

        </div>
        <?php 
         if (isset($idVD)&& $idVD!='') : ?>
            <?php foreach ($idVDArray as $id) : ?>
                <?php $vanDon = vanDon::getvanDonByIdVD($pdo, $idVD);

                $us = KhachHang::getUserById($pdo, $vanDon['idKhachHang']);
                $hoTenGui = $us['hoTen'];

                if (isset($vanDon['tinhChatHang']) && $vanDon['tinhChatHang'] instanceof MongoDB\Model\BSONArray) {
                    // Chuyển BSONArray thành mảng PHP
                    $tinhChatHangArray = $vanDon['tinhChatHang']->getArrayCopy();
                } else {
                    // Mảng rỗng nếu không có dữ liệu
                    $tinhChatHangArray = [];
                }  ?>
                <div class="row" style="margin-bottom: 30px;">

                    <div class="col-lg-8">

                        <form class="form-contact contact_form" action="#">

                            <div class="card form-group box-sender">
                                <div class="col-12 text-center">
                                    <h1>Chi tiết vận đơn</h1>
                                </div>
                                <div class="card-body">

                                    <div class="row no-gutters">
                                        <table class="table table-bordered mt-4" style="border: 2px solid black;">
                                            <thead>
                                                <tr>
                                                    <th style="border: 2px solid black;">Tên hàng hóa</th>
                                                    <th style="border: 2px solid black;">Số lượng</th>
                                                    <th style="border: 2px solid black;">Trọng lượng</th>
                                                    <th style="border: 2px solid black;">Giá trị</th>
                                                </tr>
                                            </thead>
                                            <tbody style="font-size: 18px; font-weight:600; color:tomato">
                                                <?php foreach ($vanDon['hangHoa'] as $item) : ?>
                                                    <tr>
                                                        <td style="border: 2px solid black;"><?= $item['tenHang'] ?></td>
                                                        <td style="border: 2px solid black;"><?= $item['soLuong'] ?></td>
                                                        <td style="border: 2px solid black;"><?= $item['trongLuong'] ?> gr</td>
                                                        <td style="border: 2px solid black;"><?= number_format($item['giaTien'], 0, ',', '.') ?> VNĐ</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Id vận đơn:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $id  ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Tên người gửi:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $hoTenGui  ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Thời gian hẹn lấy:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['thoiGianHenLay'] ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Họ tên người nhận:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['nguoiNhan']['hoTen'] ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Số điện thoại người nhận:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['nguoiNhan']['SDT'] ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Địa chỉ người nhận:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['nguoiNhan']['diaChi']['diaChi'] ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Thời gian hẹn giao:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['thoiGianHenGiao'] ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Người trả cước:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['ngTraCuoc'] ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Loại hàng hóa:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['loaiHang'] ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Tính chất hàng hóa:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;">
                                            <?= !empty($tinhChatHangArray) ? implode(' - ', $tinhChatHangArray) : 'Không có' ?>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Loại vận chuyển:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['loaiVanChuyen'] ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Tình trạng cước:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['tinhTrangCuoc'] ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Ghi chú:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['ghiChu'] ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Ngày tạo:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['ngayTao']->toDateTime()->format('Y-m-d H:i:s') ?></div>
                                    </div>
                                    <hr>
                                    <?php if ($vanDon['tinhTrang'] !== 'Giao hàng thất bại') : ?>
                                        <div class="row no-gutters">
                                            <div class="col-6" style="font-size: 20px; font-weight:700;">Ngày Nhận:</div>
                                            <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['ngayNhan']->toDateTime()->format('Y-m-d H:i:s') ?></div>
                                        </div>
                                        <hr>
                                    <?php endif; ?>


                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Tình trạng đơn:</div>
                                        <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $vanDon['tinhTrang'] ?></div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Tiền thu hộ:</div>
                                        <div class="col-6" style="font-size: 20px; font-weight:700; color: orangered;"><?= number_format($vanDon['thuHo'], 0, ',', '.') ?> VNĐ</div>
                                    </div>
                                    <hr>

                                    <div class="row no-gutters">
                                        <div class="col-6" style="font-size: 20px; font-weight:700;">Tiền cước:</div>
                                        <div class="col-6" style="font-size: 20px; font-weight:700; color: orangered;"><?= number_format($vanDon['tienCuoc'], 0, ',', '.') ?> VNĐ</div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="col-lg-4">
                        <div class="card form-group box-sender">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12" style="font-size: 20px; font-weight:700;">Quy trình vận chuyển:</div>
                                    <?php foreach ($vanDon['quyTrinhVC'] as $index => $qt) : ?>
                                        <div class="col-12  border p-2 border rounded mt-2">
                                            <div class="d-flex justify-content-between align-items-center p-2 ">
                                                <div class="font-weight-bold">Trạng thái:</div>
                                                <div><?= htmlspecialchars($qt['trangthai']) ?></div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center p-2 ">
                                                <div class="font-weight-bold">Tên bưu cục:</div>
                                                <div><?= htmlspecialchars($qt['tenBC']) ?></div>
                                            </div>
                                            <div class="justify-content-between align-items-center p-2 ">
                                                <div class="font-weight-bold">Địa chỉ bưu cục:</div>
                                                <div>&nbsp;&nbsp;&nbsp;&nbsp;<?= htmlspecialchars($qt['diachiBC']) ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

            <?php endforeach; ?>

        <?php endif ?>

    </div>


</div>
</div>
</div>

<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>


</body>

</html>