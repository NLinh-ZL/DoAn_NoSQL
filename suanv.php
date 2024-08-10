<?php
require_once "inc/header.php";
?>
<!-- =============================================================================================== -->
<?php
require_once "class/API.php";
require_once "class/BuuCuc.php";

$idNV = isset($_GET['id']) ? (int) trim($_GET['id']) : null;

$nhanVien = BuuCuc::getNhanVienById1($pdo, $idNV);
if ($nhanVien) {
    // Điền thông tin vào các biến để sử dụng trong form
    $hoten = $nhanVien['hoTen'];
    $gioitinh = $nhanVien['gioiTinh'];
    $email = $nhanVien['email'];
    $ngaysinh = $nhanVien['ngaySinh'];
    $sdt = $nhanVien['SDT'];
    $diachi = $nhanVien['diaChi']['diaChi'];
    $thanhpho = $nhanVien['diaChi']['thanhPho'];
    $quan = $nhanVien['diaChi']['quan'];
    $phuong = $nhanVien['diaChi']['phuong'];
    $duong = $nhanVien['diaChi']['duong'];
    $cccd = $nhanVien['CCCD'];
    $chucvu = $nhanVien['chucVu'];
    // $buucuc = $nhanVien['idBC'];
}


// Tìm vị trí của "Tân Hòa Đông" trong chuỗi địa chỉ
$pos = strpos($diachi, $duong);

if ($pos !== false) {
    // Lấy phần đầu của chuỗi trước "Tân Hòa Đông"
    $partBefore = substr($diachi, 0, $pos);
} else {
    // Nếu không tìm thấy "Tân Hòa Đông" trong chuỗi, trả về toàn bộ địa chỉ
    $partBefore = $diachi;
}

$sonha= $partBefore;

$ngaysinhDate = $ngaysinh->toDateTime();
// Định dạng lại thành Y-m-d để có thể dùng cho input type="date"
$ngaysinhFormatted = $ngaysinhDate->format('Y-m-d');

$idBC=BuuCuc::getIdBuuCucByNhanVienId($pdo, $idNV);

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
$ngaysinhError = '';
$sdtError = '';
$diachiError = '';
$thanhphoError = '';
$quanError = '';
$phuongError = '';
$duongError = '';
$sonhaError = '';
$cccdError = '';
$chucvuError = '';
// $buucucError = '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoten = $_POST['hoten'];
    $gioitinh = $_POST['gioitinh'];
    $email = $_POST['email'];
    $ngaysinh = $_POST['ngaysinh'];
    $sdt = $_POST['sdt'];
    $diachi = $_POST['diachi'];
    $thanhpho = $_POST['thanhpho'];
    $quan = $_POST['quan'];
    $phuong = $_POST['phuong'];
    $duong = $_POST['duong'];
    $sonha = $_POST['sonha'];
    $cccd = $_POST['cccd'];
    $chucvu = $_POST['chucvu'];
    // $buucuc = $_POST['buucuc'];

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
    } elseif (BuuCuc::isEmailExists1($pdo, $email)) {
        $emailError = 'Email đã tồn tại';
    }


    // Kiểm tra ngày sinh
    if (empty($ngaysinh)) {
        $ngaysinhError = 'Hãy nhập ngày sinh';
    } else {
        // Chuyển đổi ngày sinh từ chuỗi thành đối tượng DateTime
        $ngaySinhDate = new DateTime($ngaysinh);

        // Lấy ngày hiện tại
        $ngayHienTai = new DateTime();

        // Tính toán tuổi
        $tuoi = $ngayHienTai->diff($ngaySinhDate)->y;

        // Kiểm tra tuổi
        if ($tuoi < 18 || $tuoi > 100) {
            $ngaysinhError = 'Tuổi của bạn phải lớn hơn 18 và nhỏ hơn 100';
        }
    }

    // Kiểm tra số điện thoại
    if (empty($sdt)) {
        $sdtError = 'Hãy nhập số điện thoại';
    } elseif (!preg_match("/^\d{10}$/", $sdt)) {
        $sdtError = 'Số điện thoại phải là 10 số';
    }

    // Kiểm tra địa chỉ
    if (empty($diachi)) {
        $diachiError = 'Hãy nhập địa chỉ cụ thể';
    } else {
        //API googlemap
        $switchLocation = geocodeAddress($diachi, $googleApiKey);
        if ($switchLocation === false) {
            $diachiError = "Địa chỉ nhân viên bạn nhập không tồn tại";
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

    // Kiểm tra số điện thoại
    if (empty($cccd)) {
        $sdtError = 'Hãy nhập số điện thoại';
    } elseif (!preg_match("/^\d{12}$/", $cccd)) {
        $cccdError = 'Căn cước công dẫn phải là 12 số';
    } elseif (BuuCuc::isCCCDExists1($pdo, $cccd)) {
        $cccdError = 'CCCD đã tồn tại';
    }

    if (empty($chucvu)) {
        $chucvuError = 'Hãy chọn chức vụ';
    }


    // Nếu không có lỗi nào, thêm khách hàng mới vào MongoDB
    if (
        empty($cccdError) && empty($chucvuError) && empty($hotenError) && empty($gioitinhError)
        && empty($emailError) && empty($ngaysinhError) && empty($sdtError) && empty($diachiError) && empty($thanhphoError)
        && empty($quanError) && empty($phuongError) && empty($duongError)
    ) {
        BuuCuc::updateNhanVien(
            $pdo,
            $idBC, // idBC
            $idNV, // idNV
            $hoten, // hoTen
            $gioitinh, // gioiTinh
            $ngaysinh, // ngaySinh
            [
                'diaChi' => $diachi,
                'thanhPho' => $thanhpho,
                'quan' => $quan,
                'phuong' => $phuong,
                'duong' => $duong
            ], // diaChi
            $sdt, // SDT
            $cccd, // CCCD
            $email, // email
            $chucvu // chucVu
        );
        header("Location: quanlynv.php");
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
                                    <h2>Sửa nhân viên</h2>
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
                                        <label class="form-label my-1" style="font-size: 16px;">Số nhà:</label>
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
                                        <input type="date" id="ngaysinh" name="ngaysinh" value="<?= $ngaysinhFormatted ?>" placeholder="Ngày sinh">
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
                                        <label class="form-label my-1" style="font-size: 16px;">CCCD:</label>
                                        <input type="text" id="cccd" name="cccd" value="<?= $cccd ?>" placeholder="Căn cước công dân">
                                        <span class="text-danger"><?= $cccdError ?></span>
                                    </div>
                                </div>

                                <!-- vị trí chọn BuuCuc -->


                                <div class="col-lg-12 col-md-12">
                                    <div class="select-items">
                                        <label class="form-label my-1" style="font-size: 16px; display: block;">Chức vụ:</label>
                                        <select name="chucvu" id="chucvu" class="form-select">
                                            <option value="" <?= isset($chucvu) && $chucvu == '' ? 'selected' : '' ?>>Chọn chức vụ</option>
                                            <option value="Quản lý" <?= isset($chucvu) && $chucvu == 'Quản lý' ? 'selected' : '' ?>>Quản lý</option>
                                            <option value="Nhân viên" <?= isset($chucvu) && $chucvu == 'Nhân viên' ? 'selected' : '' ?>>Nhân viên</option>
                                            <option value="Shipper" <?= isset($chucvu) && $chucvu == 'Shipper' ? 'selected' : '' ?>>Shipper</option>
                                        </select>
                                        <span class="text-danger"><?= $chucvuError ?></span>
                                    </div>
                                </div>

                                <!-- Button -->
                                <div class="col-lg-12">
                                    <button name="submit" class="submit-btn">Sửa nhân viên</button>
                                </div>
                                <div class="col-lg-12">
                                    <a class="my-2 btn bg-primary" href="doimknv.php?id=<?= $idNV ?>">Đổi mật khẩu</a>
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