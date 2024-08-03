<?php
require_once "inc/header.php";
?>
<!-- =============================================================================================== -->
<?php
require_once "class/KhachHang.php";
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
$sonhaError = '';

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
$sonha = '';

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
    $sonha = $_POST['sonha'];

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
    }else {
        // Chuyển đổi ngày sinh từ chuỗi thành đối tượng DateTime
        $ngaySinhDate = new DateTime($ngaysinh);
        
        // Lấy ngày hiện tại
        $ngayHienTai = new DateTime();
        
        // Tính toán tuổi
        $tuoi = $ngayHienTai->diff($ngaySinhDate)->y;
    
        // Kiểm tra tuổi
        if ($tuoi < 18) {
            $ngaysinhError = 'Tuổi của bạn phải lớn hơn 16';
        }
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
    } else {
        //API googlemap
        $switchLocation = geocodeAddress($diachi, $googleApiKey);
        if ($switchLocation === false) {
            $diachiError = "Địa chỉ người nhận bạn nhập không tồn tại";
        }
    }


    if (empty($thanhpho)) {
        $thanhphoError = 'Hãy chọn tỉnh';
    }
    if (empty($quan)) {
        $quanError = 'Hãy nhập quận/ huyện';
    }
    if (empty($phuong)) {
        $phuongError = 'Hãy nhập xã/ phường';
    }
    if (empty($duong)) {
        $duongError = 'Hãy nhập đường';
    }

    if (empty($sonha)) {
        $sonhaError = 'Hãy nhập số nhà';
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
                                    <h2>Đăng ký</h2>
                                </div>
                            </div>
                        </div>
                        <!-- form -->
                        <form action="#" class="contact-form" method="post">
                            <div class="row ">
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Họ tên:</label>
                                        <input type="text" id="hoten" name="hoten" value="<?= $hoten ?>" placeholder="Họ và tên">
                                        <span class="text-danger"><?= $hotenError ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Địa chỉ:</label>
                                        <input type="text" style="border: 1px solid #ff5f13;" id="diachi_display" name="diachi_display" placeholder="Hãy nhập các trường bên dưới, để tạo ra địa chỉ" value="<?= $diachi ?>" placeholder="Địa chỉ" disabled>
                                        <input type="hidden" name="diachi" id="diachi" value="<?= $diachi ?>">
                                        <span class="text-danger"><?= $diachiError ?></span>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6">
                                    <div class="select-items">
                                        <label class="form-label my-1" style="font-size: 16px; display: block;">Tỉnh:</label>
                                        <select id="thanhpho" name="thanhpho" size="5">
                                            <option value="" selected>Chọn tỉnh</option>
                                            <?php foreach ($vietnamProvinces as $province) : ?>
                                                <option value="<?= $province ?>" <?= $province === $thanhpho ? 'selected' : '' ?>><?= $province ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="text-danger"><?= $thanhphoError ?></span>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Quận/ Huyện:</label>
                                        <input type="text" id="quan" name="quan" value="<?= $quan ?>" placeholder="Quận/Huyện">
                                        <span class="text-danger"><?= $quanError ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Xã/ Phường:</label>
                                        <input type="text" id="phuong" name="phuong" value="<?= $phuong ?>" placeholder="Phường/Xã">
                                        <span class="text-danger"><?= $phuongError ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Đường:</label>
                                        <input type="text" id="duong" name="duong" value="<?= $duong ?>" placeholder="Đường">
                                        <span class="text-danger"><?= $duongError ?></span>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Đường:</label>
                                        <input type="text" id="sonha" name="sonha" value="<?= $sonha ?>" placeholder="Số nhà">
                                        <span class="text-danger"><?= $sonhaError ?></span>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6">
                                    <div class="select-items">
                                        <label class="form-label my-1" style="font-size: 16px; display: block;">Giới tính:</label>
                                        <select name="gioitinh" id="gioitinh" class="form-select">
                                            <option value="" <?= isset($gioitinh) && $gioitinh == '' ? 'selected' : '' ?>>Giới Tính</option>
                                            <option value="Nam" <?= isset($gioitinh) && $gioitinh == 'Nam' ? 'selected' : '' ?>>Nam</option>
                                            <option value="Nữ" <?= isset($gioitinh) && $gioitinh == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                        </select>
                                        <span class="text-danger"><?= $gioitinhError ?></span>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Ngày Sinh:</label>
                                        <input type="date" id="ngaysinh" name="ngaysinh" value="<?= $ngaysinh ?>" placeholder="Ngày sinh">
                                        <span class="text-danger"><?= $ngaysinhError ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Số điện thoại:</label>
                                        <input type="text" id="sdt" name="sdt" value="<?= $sdt ?>" placeholder="Số điện thoại">
                                        <span class="text-danger"><?= $sdtError ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Email:</label>
                                        <input type="text" id="email" name="email" value="<?= $email ?>" placeholder="Email">
                                        <span class="text-danger"><?= $emailError ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Mật khẩu:</label>
                                        <input type="password" id="pass" name="pass" value="<?= $pass ?>" placeholder="Mật khẩu">
                                        <span class="text-danger"><?= $passError ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-form">
                                        <label class="form-label my-1" style="font-size: 16px;">Nhập lại mật khẩu:</label>
                                        <input type="password" id="passcf" name="passcf" value="<?= $passcf ?>" placeholder="Nhập lại mật khẩu">
                                        <span class="text-danger"><?= $passcfError ?></span>
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

<script>
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
require_once "inc/footer.php";
?>