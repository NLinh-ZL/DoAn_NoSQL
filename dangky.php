<?php 
    require_once "inc/header.php";
?>
<!-- =============================================================================================== -->
<?php 
    require_once "class/KhachHang.php";

    // Khởi tạo các biến lỗi và dữ liệu người dùng
    $hotenError = '';
    $gioitinhError = '';
    $emailError = '';
    $passError = '';
    $passcfError = '';
    $ngaysinhError = '';
    $sdtError = '';
    $diachiError = '';
    $thanhphoError = '';
    $quanError = '';
    $phuongError = '';
    $duongError = '';

    $hoten = '';
    $gioitinh = '';
    $email = '';
    $pass = '';
    $passcf = '';
    $ngaysinh = '';
    $sdt = '';
    $diachi = '';
    $thanhpho = '';
    $quan = '';
    $phuong = '';
    $duong = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $hoten = $_POST['hoten'];
        $gioitinh = $_POST['gioitinh'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $passcf = $_POST['passcf'];
        $ngaysinh = $_POST['ngaysinh'];
        $sdt = $_POST['sdt'];
        $diachi = $_POST['diachi'];
        $thanhpho = $_POST['thanhpho'];
        $quan = $_POST['quan'];
        $phuong = $_POST['phuong'];
        $duong = $_POST['duong'];

        // Kiểm tra họ tên
        if (empty($hoten)) {
            $hotenError = 'Hãy nhập họ tên';
        }

        // Kiểm tra giới tính
        if (empty($gioitinh)) {
            $gioitinhError = 'Hãy chọn giới tính';
        }

        // Kiểm tra email
        if (empty($email)) {
            $emailError = 'Hãy nhập email';
        } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $emailError = 'Email không hợp lệ';
        } elseif (KhachHang::isEmailExists($pdo, $email)) {
            $emailError = 'Email đã tồn tại';
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

        // Kiểm tra ngày sinh
        if (empty($ngaysinh)) {
            $ngaysinhError = 'Hãy nhập ngày sinh';
        }

        // Kiểm tra số điện thoại
        if (empty($sdt)) {
            $sdtError = 'Hãy nhập số điện thoại';
        } elseif (!preg_match("/^\d{10}$/", $sdt)) {
            $sdtError = 'Số điện thoại không hợp lệ';
        }

        // Kiểm tra địa chỉ
        if (empty($diachi)) {
            $diachiError = 'Hãy nhập địa chỉ cụ thể';
        }
        if (empty($thanhpho)) {
            $thanhphoError = 'Hãy nhập thành phố';
        }
        if (empty($quan)) {
            $quanError = 'Hãy nhập quận';
        }
        if (empty($phuong)) {
            $phuongError = 'Hãy nhập phường';
        }
        if (empty($duong)) {
            $duongError = 'Hãy nhập đường';
        }

        // Nếu không có lỗi nào, thêm khách hàng mới vào MongoDB
        if (empty($hotenError) && empty($gioitinhError) && empty($emailError) && empty($passError) && empty($passcfError) && empty($ngaysinhError) && empty($sdtError) && empty($diachiError) && empty($thanhphoError) && empty($quanError) && empty($phuongError) && empty($duongError)) {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            
            // Thêm khách hàng mới vào MongoDB
            KhachHang::addKhachHang($pdo, $hoten, $gioitinh, $ngaysinh, [
                'diachi' => $diachi,
                'thanhpho' => $thanhpho,
                'quan' => $quan,
                'phuong' => $phuong,
                'duong' => $duong
            ], $sdt, $email, $hashed_pass);

            header("Location: dangnhap.php");
            exit;
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
                                    <h2>Đăng ký</h2>
                                </div>
                            </div>
                        </div>
                        <!-- form -->
                        <form action="#" class="contact-form" method="post">
                            <div class="row ">
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <span class="text-danger"><?= $hotenError ?></span>
                                        <input type="text" id="hoten" name="hoten" value="<?= $hoten ?>" placeholder="Họ và tên">
                                        
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <span class="text-danger"><?= $diachiError ?></span>
                                        <input type="text" id="diachi" name="diachi" value="<?= $diachi ?>" placeholder="Địa chỉ">
                                        
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6">
                                    <div class="input-form">
                                        <span class="text-danger"><?= $thanhphoError ?></span>
                                        <input type="text" id="thanhpho" name="thanhpho" value="<?= $thanhpho ?>" placeholder="Thành phố">
                                        
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="input-form">
                                        <span class="text-danger"><?= $quanError ?></span>
                                        <input type="text" id="quan" name="quan" value="<?= $quan ?>" placeholder="Quận">
                                        
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="input-form">
                                        <span class="text-danger"><?= $phuongError ?></span>
                                        <input type="text" id="phuong" name="phuong" value="<?= $phuong ?>" placeholder="Phường">
                                        
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="input-form">
                                        <span class="text-danger"><?= $duongError ?></span>
                                        <input type="text" id="duong" name="duong" value="<?= $duong ?>" placeholder="Đường">
                                        
                                    </div>
                                </div>
                                
                                <div class="col-lg-6 col-md-6">
                                    <div class="select-items">
                                        <span class="text-danger"><?= $gioitinhError ?></span>
                                        <select name="gioitinh" id="gioitinh">
                                            <option value="">Giới Tính</option>
                                            <option value="Nam">Nam</option>
                                            <option value="Nữ">Nữ</option>
                                        </select>
                                        
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="input-form">
                                        <span class="text-danger"><?= $ngaysinhError ?></span>
                                        <input type="date" id="ngaysinh" name="ngaysinh" value="<?= $ngaysinh ?>" placeholder="Ngày sinh">
                                        
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <span class="text-danger"><?= $sdtError ?></span>
                                        <input type="text" id="sdt" name="sdt" value="<?= $sdt ?>" placeholder="Số điện thoại">
                                        
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <span class="text-danger"><?= $emailError ?></span>
                                        <input type="text" id="email" name="email" value="<?= $email ?>" placeholder="Email">
                                        
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <span class="text-danger"><?= $passError ?></span>
                                        <input type="password" id="pass" name="pass" value="<?= $pass ?>" placeholder="Mật khẩu">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <span class="text-danger"><?= $passcfError ?></span>
                                        <input type="password" id="passcf" name="passcf" value="<?= $passcf ?>" placeholder="Nhập lại mật khẩu">
                                    </div>
                                </div>
                                
                                <!-- Button -->
                                <div class="col-lg-12">
                                    <button name="submit" class="submit-btn">Đăng ký</button>
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