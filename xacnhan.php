<?php
require_once "inc/header.php";
?>
<!-- =============================================================================================== -->
<?php
require_once "class/VanDon.php";
require_once "class/KhachHang.php";

if (!isset($_SESSION['orderData'])) {
    header("Location: index.php");
    exit;
}

$orderData = $_SESSION['orderData'];

$idus = $_SESSION['logged_id'];

$us = KhachHang::getUserById($pdo, $idus);
$hoTenGui = $us['hoTen'];

if($orderData['nguoiTraCuoc']=='Người gửi')
{
    $ttc="Đã thanh toán";
}else if($orderData['nguoiTraCuoc']=='Người nhận'){
    $ttc= "Chưa thanh toán";
}

// Kiểm tra xác nhận
if (isset($_POST['confirm']) && $_POST['confirm'] == 'yes') {

    // Lưu dữ liệu vào cơ sở dữ liệu
    $result = VanDon::themDonHang(
        $pdo,
        $_SESSION['logged_id'],
        $orderData['thoiGianHenLay'],
        $orderData['hoTenNguoiNhan'],
        $orderData['sdtNguoiNhan'],
        $orderData['diaChiNguoiNhan'],
        $orderData['thanhPho'],
        $orderData['quan'],
        $orderData['phuong'],
        $orderData['duong'],
        $orderData['thoiGianHenGiao'],
        $orderData['loaiHangHoa'],
        $orderData['items'],
        $orderData['tinhChatHangHoaDacBiet'],
        $orderData['nguoiTraCuoc'],
        $orderData['cuoc'],
        $orderData['tienThuHo'],
        $orderData['loaiVanChuyen'],
        $orderData['ghiChu'],
        $orderData['quyTrinhVC'],
        $ttc
    );

    // Xóa dữ liệu khỏi session sau khi đã lưu
    unset($_SESSION['orderData']);

    // Chuyển hướng đến trang thanh toán
    // header("Location: thanhtoan.php");
    // exit;
} elseif (isset($_GET['confirm']) && $_GET['confirm'] == 'no') {
    // Nếu nhấn nút hủy, xóa dữ liệu khỏi session và trở về trang tạo đơn
    unset($_SESSION['orderData']);
    header("Location: taodon.php");
    exit;
}
?>

<style>
    .large-text {
        font-size: 50px;
        font-weight: 900;
    }

    /* Media Query cho màn hình nhỏ hơn 768px */
    @media (max-width: 768px) {
        .large-text {
            font-size: 40px;
        }
    }

    /* Media Query cho màn hình nhỏ hơn 576px */
    @media (max-width: 576px) {
        .large-text {
            font-size: 35px;
        }
    }

    hr {
        border-bottom: 2px solid #eceff8;
        border-top: 0 none;
        margin: 5px 0;
        padding: 0;
    }
</style>

<section class="contact-section" style="background-color: #F8F8FF">
    <div class="container">

        <div class="row">
            <div class="col-12 text-center">
                <div class="large-text">XÁC NHẬN</div>
            </div>
            <div class="col-lg-12">
                <form class="form-contact contact_form" method="POST" action="#">

                    <div class="card form-group box-sender">
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
                                        <?php foreach ($orderData['items'] as $item) : ?>
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
                                <div class="col-6" style="font-size: 20px; font-weight:700;">Tên người gửi:</div>
                                <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $hoTenGui  ?></div>
                            </div>
                            <hr>

                            <div class="row no-gutters">
                                <div class="col-6" style="font-size: 20px; font-weight:700;">Họ tên người nhận:</div>
                                <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $orderData['hoTenNguoiNhan'] ?></div>
                            </div>
                            <hr>

                            <div class="row no-gutters">
                                <div class="col-6" style="font-size: 20px; font-weight:700;">Số điện thoại người nhận:</div>
                                <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $orderData['sdtNguoiNhan'] ?></div>
                            </div>
                            <hr>

                            <div class="row no-gutters">
                                <div class="col-6" style="font-size: 20px; font-weight:700;">Địa chỉ người nhận:</div>
                                <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $orderData['diaChiNguoiNhan'] ?></div>
                            </div>
                            <hr>

                            <div class="row no-gutters">
                                <div class="col-6" style="font-size: 20px; font-weight:700;">Người trả cước:</div>
                                <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $orderData['nguoiTraCuoc'] ?></div>
                            </div>
                            <hr>

                            <div class="row no-gutters">
                                <div class="col-6" style="font-size: 20px; font-weight:700;">Loại vận chuyển:</div>
                                <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $orderData['loaiVanChuyen'] ?></div>
                            </div>
                            <hr>

                            <div class="row no-gutters">
                                <div class="col-6" style="font-size: 20px; font-weight:700;">Tiền thu hộ:</div>
                                <div class="col-6" style="font-size: 18px; font-weight:600;"><?= number_format($orderData['tienThuHo'], 0, ',', '.') ?> VNĐ</div>
                            </div>
                            <hr>

                            <div class="row no-gutters">
                                <div class="col-6" style="font-size: 20px; font-weight:700;">Tiền cước:</div>
                                <div class="col-6" style="font-size: 18px; font-weight:600;"><?= number_format($orderData['cuoc'], 0, ',', '.') ?> VNĐ</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <div class="row">
                            <div class="col-6">
                                <button type="submit" name="confirm" value="yes" class="btn btn-dark mt-3 w-100" style="font-size: 20px; font-weight:900;">XÁC NHẬN TẠO PHIẾU</button>
                            </div>
                            <div class="col-6">
                                <a href="?confirm=no" class="btn btn-danger mt-3 w-100" style="font-size: 20px; font-weight:900;">HỦY</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    </div>
</section>


<!-- =============================================================================================== -->
<?php
require_once "inc/footer.php";
?>