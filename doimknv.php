<?php
require_once "inc/header.php";
?>
<!-- =============================================================================================== -->
<?php
require_once "class/API.php";
require_once "class/BuuCuc.php";

$idNV = isset($_GET['id']) ? (int) trim($_GET['id']) : null;

$nhanVien = BuuCuc::getNhanVienById($pdo, $idNV);

$idBC = BuuCuc::getIdBuuCucByNhanVienId($pdo, $idNV);


// Khởi tạo các biến lỗi và dữ liệu người dùng
$passcError = '';
$passError = '';
$passcfError = '';

$email = '';
$passc = '';
$pass = '';
$passcf = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $passc = $_POST['passc'];
    $pass = $_POST['pass'];
    $passcf = $_POST['passcf'];

    $loginResultNv = BuuCuc::isValid($pdo, $email, $passc);
    // Kiểm tra mật khẩu cũ
    // if (empty($passc)||empty($email)) {
    //     $passcError = 'Hãy nhập thông tin tài khoản cũ';
    // } else
    if ($loginResultNv === false) {
        $passcError = "Thông tin đăng nhập của bạn sai";
    }

    // Kiểm tra mật khẩu
    if (empty($pass)) {
        $passError = 'Hãy nhập mật khẩu';
    } elseif (!preg_match("/^.{8,}$/", $pass)) {
        $passError = "Mật khẩu phải có ít nhất 8 ký tự";
    }

    // Kiểm tra xác nhận mật khẩu
    if (empty($passcf)) {
        $passcfError = 'Hãy nhập lại mật khẩu';
    } elseif ($pass != $passcf) {
        $passcfError = "Mật khẩu nhập lại không khớp";
    }
    // var_dump($idBC);
    // echo ("<br>");
    // var_dump($idNV);
    // echo ("<br>");
    // var_dump($passcfError);
    // Nếu không có lỗi nào, thêm khách hàng mới vào MongoDB
    if (
        empty($passcError)&&empty($passError) && empty($passcfError)
    ) {
        
        $rq=BuuCuc::updatePassword($pdo, $idBC, $idNV, $passcf);
        echo $rq;
        header("Location: suanv.php?id=$idNV");   
        exit;
    }
}
?>

<style>
    .contact-form-area .contact-form-wrapper form.contact-form .nice-select.open .list {
        max-height: 200px;
        overflow-y: auto;
    }
</style>

<main>
    <!--? contact-form start -->
    <section class="contact-form-area section-bg  pt-115 pb-120 fix" data-background="inc/assets/img/gallery/section_bg02.jpg">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Contact wrapper -->
                <div class="col-xl-8 col-lg-9">
                    <div class="contact-form-wrapper">
                        <!-- From tittle -->
                        <div class="row">
                            <div class="col-lg-12">
                                <!-- Section Tittle -->
                                <div class="section-tittle mb-50 text-center">
                                    <h2>Đổi mật khẩu nhân viên</h2>
                                </div>
                            </div>
                        </div>
                        <!-- form -->
                        <form action="#" class="contact-form" method="post">
                            <div class="row ">
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <label class="form-label my-3" style="font-size: 25px; font-weight:900; color: #2c234d;">Email<sup>*</sup></label>
                                        <input type="text" id="email" name="email" value="<?= $email ?>" placeholder="Email">
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Mật khẩu cũ:</label>
                                        <input type="password" id="passc" name="passc" value="<?= $passc ?>" placeholder="Mật khẩu">
                                        <span class="text-danger"><?= $passcError ?></span>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Mật khẩu mới:</label>
                                        <input type="password" id="pass" name="pass" value="<?= $pass ?>" placeholder="Mật khẩu">
                                        <span class="text-danger"><?= $passError ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Nhập lại mật khẩu mới:</label>
                                        <input type="password" id="passcf" name="passcf" value="<?= $passcf ?>" placeholder="Nhập lại mật khẩu">
                                        <span class="text-danger"><?= $passcfError ?></span>
                                    </div>
                                </div>

                                <!-- Button -->
                                <div class="col-lg-12">
                                    <button name="submit" class="submit-btn">Đổi mật khẩu</button>
                                </div>
                                <div class="col-lg-12">
                                    <a class="my-2 btn bg-primary" href="suanv.php?id=<?= $idNV ?>">Sửa thông tin</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contact-form end -->
</main>


<!-- =============================================================================================== -->
<?php
require_once "inc/footer.php";
?>