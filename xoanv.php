<?php
require_once "inc/header.php";
?>
<!-- =============================================================================================== -->
<?php
require_once "class/BuuCuc.php";
$idNV = isset($_GET['id']) ? (int) trim($_GET['id']) : null;

$nhanVien = BuuCuc::getNhanVienById1($pdo, $idNV);
$hoten = $nhanVien['hoTen'];
if (isset($_POST['confirm']) && $_POST['confirm'] == 'yes') {
    $idBC=BuuCuc::getIdBuuCucByNhanVienId($pdo, $idNV);
    BuuCuc::deleteNhanVien($pdo, $idBC, $idNV);
    header("Location: quanlynv.php");
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
                <div class="large-text">XÁC NHẬN XÓA</div>
            </div>
            <div class="col-lg-12">
                <form class="form-contact contact_form" method="POST" action="#">
                    <div class="card form-group box-sender">
                        <div class="card-header text-center">
                            Bạn có chắc chắn muốn xóa nhân viên <?= $hoten ?>
                        </div>
                        <div class="card-body">
                            <div class="form-group mt-3">
                                <div class="row">
                                    <div class="col-6">
                                        <button type="submit" name="confirm" value="yes" class="btn btn-dark mt-3 w-100" style="font-size: 20px; font-weight:900;">XÓA</button>
                                    </div>
                                    <div class="col-6">
                                        <a href="quanlynv.php" class="btn btn-danger mt-3 w-100" style="font-size: 20px; font-weight:900;">HỦY</a>
                                    </div>
                                </div>
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