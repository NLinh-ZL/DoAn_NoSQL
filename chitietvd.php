<?php
require_once "inc/header1.php";
?>
<!-- =============================================================================================== -->
<?php
require_once "class/VanDon.php";
require_once "class/KhachHang.php";

if (!isset($_GET["id"])) {
    die("Cần cung cấp thông tin vận đơn!!!");
}
$idVD = $_GET["id"];

$vanDon = vanDon::getvanDonByIdVD($pdo, $idVD);

$idus = $_SESSION['logged_id'];

$us = KhachHang::getUserById($pdo, $vanDon['idKhachHang']);
$hoTenGui = $us['hoTen'];

if (isset($vanDon['tinhChatHang']) && $vanDon['tinhChatHang'] instanceof MongoDB\Model\BSONArray) {
    // Chuyển BSONArray thành mảng PHP
    $tinhChatHangArray = $vanDon['tinhChatHang']->getArrayCopy();
} else {
    // Mảng rỗng nếu không có dữ liệu
    $tinhChatHangArray = [];
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
    <div class="container" style="margin:100px">

        <div class="row">

            <div class="col-lg-8">

                <form class="form-contact contact_form" action="#">

                    <div class="card form-group box-sender">
                        <div class="col-12 text-center">
                            <div class="large-text">Chi tiết vận đơn</div>
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
                                <div class="col-6" style="font-size: 18px; font-weight:600;"><?= $idVD  ?></div>
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
    </div>

    </div>
</section>


<!-- =============================================================================================== -->
<?php
require_once "inc/footer.php";
?>