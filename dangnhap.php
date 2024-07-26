<?php 
    require_once "inc/header.php";
?>
<!-- =============================================================================================== -->

<?php 
require_once "class/KhachHang.php";

    $emailError = '';
    $passError = '';
    $loginError='';

    $email = '';
    $pass = '';
    $us='';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $pass = $_POST['pass'];

        // Kiểm tra email
        if (empty($email)) {
            $emailError = 'Hãy nhập email';
        }

        // Kiểm tra mật khẩu
        if (empty($pass)) {
            $passError = 'Hãy nhập mật khẩu';
        }

        // Nếu không có lỗi nào, thêm khách hàng mới vào MongoDB
        if (empty($emailError) && empty($passError)) {
            
            $loginResult = KhachHang::isValid($pdo, $email, $pass);
            if ($loginResult === true) {
                $us = KhachHang::getUser($pdo, $email, $pass);
                $_SESSION['logged_us'] = $us;
                $_SESSION['logged_role'] = $us['role'];
                $_SESSION['logged_name'] = $us['hoTen'];

                // if ($_SESSION['logged_role'] == "1") {
                //     header("Location: admin.php");
                //     exit;
                // } else if($_SESSION['logged_role'] == "0") {
                //     header("Location: index.php");
                //     exit;
                // }
            } else {
                $loginError = 'Sai email hoặc mật khẩu';
            }
        }
    }
?>

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
                                    <h2>Đăng nhập</h2>
                                </div>
                            </div>
                        </div>
                        <!-- form -->
                        <form action="#" class="contact-form" method="post">
                            <div class="row ">
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <label class="form-label my-3" style="font-size: 25px; font-weight:900; color: #2c234d;">Email<sup>*</sup></label>
                                        <span class="text-danger"><?= $emailError ?></span>
                                        <input type="text" id="email" name="email" value="<?= $email ?>" placeholder="Email">
                                        
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <label class="form-label my-3" style="font-size: 25px; font-weight:900; color: #2c234d;">Mật khẩu<sup>*</sup></label>
                                        <span class="text-danger"><?= $passError ?></span>
                                        <input type="password" id="pass" name="pass" value="<?= $pass ?>" placeholder="Mật khẩu">
                                    </div>
                                </div>
                                <span class="text-danger"><?= $loginError ?></span>
                                <!-- Button -->
                                <div class="col-lg-12">
                                    <button name="submit" class="submit-btn">Đăng nhập</button>
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