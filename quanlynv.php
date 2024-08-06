<?php
require_once "inc/header.php";
?>
<!-- =============================================================================================== -->
<?php
require_once "class/BuuCuc.php";




$idus = $_SESSION['logged_id'];

// Lấy tham số từ URL
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Số lượng nhân viên trên mỗi trang
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'idNV';
$sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'asc';

// Lấy danh sách nhân viên
$danhSachNhanVien = BuuCuc::getNhanVienList($pdo, $page, $limit, $search, $sortColumn, $sortOrder);

// Tính tổng số trang
$totalPages = BuuCuc::getTotalPages($pdo, $limit, $search);
echo $totalPages;
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

    .btn-disabled {
        cursor: not-allowed;
        opacity: 0.65;
    }

    .btn-disabled:hover {
        background-color: #6c757d;
        color: #fff;
    }
</style>

<section class="contact-section" style="background-color: #F8F8FF">
    <div class="container">

        <div class="row">
            <div class="col-12 text-center">
                <div class="large-text">Quản lý nhân viên</div>
            </div>
            <div class="col-lg-12">
                <div class="card form-group box-sender">
                    <div class="card-header">


                        <div class="row">
                            <!-- Tìm kiếm nhân viên -->
                            <div class="col-lg-6 col-md-6 mb-3">
                                <div class="card p-3 ">
                                    <form id="search-employee" class="contact-form" method="GET">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <input type="text" id="search" name="search" class="form-control me-2" value="<?= $search ?>" placeholder="Nhập tên nhân viên muốn tìm">
                                            </div>
                                            <div class="col my-2">
                                                <button type="submit" class="btn btn-dark">Tìm kiếm</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Sắp xếp trường -->
                            <div class="col-lg-6 col-md-6 mb-3">
                                <div class="card p-3">
                                    <form id="sort-school" class="contact-form" method="GET">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <select name="sortColumn" id="sortColumn" class="form-select">
                                                    <option value="" <?= isset($sortColumn) && $sortColumn == '' ? 'selected' : '' ?>>Cột sắp xếp</option>
                                                    <option value="idNV" <?= isset($sortColumn) && $sortColumn == 'idNV' ? 'selected' : '' ?>>ID</option>
                                                    <option value="hoTen" <?= isset($sortColumn) && $sortColumn == 'hoTen' ? 'selected' : '' ?>>Họ tên</option>
                                                    <option value="chucVu" <?= isset($sortColumn) && $sortColumn == 'chucVu' ? 'selected' : '' ?>>Chức vụ</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <select name="sortOrder" id="sortOrder" class="form-select">
                                                    <option value="" <?= isset($sortOrder) && $sortOrder == '' ? 'selected' : '' ?>>Kiểu sắp xếp</option>
                                                    <option value="asc" <?= isset($sortOrder) && $sortOrder == 'asc' ? 'selected' : '' ?>>Tăng</option>
                                                    <option value="desc" <?= isset($sortOrder) && $sortOrder == 'desc' ? 'selected' : '' ?>>Giảm</option>
                                                </select>
                                            </div>
                                            <div class="col my-2">
                                                <button type="submit" class="btn btn-dark w-100">Sắp xếp</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>





                    </div>
                    <div class="card-body">

                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" style="border: 2px solid black;">
                                    <thead>
                                        <tr>
                                            <th style="border: 2px solid black; text-align: center;">ID</th>
                                            <th style="border: 2px solid black; text-align: center;">Họ tên</th>
                                            <th style="border: 2px solid black; text-align: center;">Chức vụ</th>
                                            <th style="border: 2px solid black; text-align: center;">Giới tính</th>
                                            <th style="border: 2px solid black; text-align: center;">Thành phố</th>
                                            <th style="border: 2px solid black; text-align: center;">Ngày sinh</th>
                                            <th style="border: 2px solid black; text-align: center;">Thêm</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 18px; font-weight: 600; color: tomato;">
                                        <?php foreach ($danhSachNhanVien as $Items) : ?>
                                            <?php foreach ($Items->nhanVien as $item) : ?>
                                                <tr>
                                                    <td style="border: 2px solid black; text-align: center;"><?php echo htmlspecialchars($item->idNV); ?></td>
                                                    <td style="border: 2px solid black; text-align: center;"><?php echo htmlspecialchars($item->hoTen); ?></td>
                                                    <td style="border: 2px solid black; text-align: center;"><?php echo htmlspecialchars($item->chucVu); ?></td>
                                                    <td style="border: 2px solid black; text-align: center;"><?php echo htmlspecialchars($item->gioiTinh); ?></td>
                                                    <td style="border: 2px solid black; text-align: center;"><?php echo htmlspecialchars($item->diaChi->thanhPho); ?></td>
                                                    <td style="border: 2px solid black; text-align: center;">
                                                        <?php
                                                        $date = $item->ngaySinh->toDateTime(); // Chuyển đổi sang DateTime
                                                        echo htmlspecialchars($date->format('d/m/Y'));
                                                        ?>
                                                    </td>
                                                    <td style="border: 2px solid black; text-align: center;">
                                                        <a class="btn bg-primary" href="suanv.php?id=<?= $item->idNV ?>">Sửa</a>

                                                        <?php if ($idus === $item->idNV || $item->chucVu === "Quản lý") : ?>
                                                            <button class="btn btn-secondary btn-disabled" disabled>Xóa</button>
                                                        <?php else : ?>
                                                            <a class="btn bg-danger" href="xoanv.php?id=<?= htmlspecialchars($item->idNV) ?>">Xóa</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <!-- Phân trang -->
                                <div class="d-flex justify-content-center mt-4">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination">
                                            <?php if ($page > 1) : ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="quanlynv.php?page=1&search=<?= urlencode($search) ?>&sortColumn=<?= urlencode($sortColumn) ?>&sortOrder=<?= urlencode($sortOrder) ?>" aria-label="First">
                                                        <span aria-hidden="true">&laquo;&laquo;</span>
                                                    </a>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="quanlynv.php?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&sortColumn=<?= urlencode($sortColumn) ?>&sortOrder=<?= urlencode($sortOrder) ?>" aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                    <a class="page-link" href="quanlynv.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&sortColumn=<?= urlencode($sortColumn) ?>&sortOrder=<?= urlencode($sortOrder) ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($page < $totalPages) : ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="quanlynv.php?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&sortColumn=<?= urlencode($sortColumn) ?>&sortOrder=<?= urlencode($sortOrder) ?>" aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                                <li class="page-item">
                                                    <a class="page-link" href="quanlynv.php?page=<?= $totalPages ?>&search=<?= urlencode($search) ?>&sortColumn=<?= urlencode($sortColumn) ?>&sortOrder=<?= urlencode($sortOrder) ?>" aria-label="Last">
                                                        <span aria-hidden="true">&raquo;&raquo;</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
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
</section>

<!-- =============================================================================================== -->
<?php
require_once "inc/footer.php";
?>