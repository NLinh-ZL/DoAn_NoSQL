<!doctype html>
<?php ob_start(); ?>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Transportation HTML-5 Template </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="inc/assets">

    <!-- CSS here -->
    <link rel="stylesheet" href="inc/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="inc/assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="inc/assets/css/slicknav.css">
    <link rel="stylesheet" href="inc/assets/css/flaticon.css">
    <link rel="stylesheet" href="inc/assets/css/animate.min.css">
    <link rel="stylesheet" href="inc/assets/css/magnific-popup.css">
    <link rel="stylesheet" href="inc/assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="inc/assets/css/themify-icons.css">
    <link rel="stylesheet" href="inc/assets/css/slick.css">
    <link rel="stylesheet" href="inc/assets/css/nice-select.css">
    <link rel="stylesheet" href="inc/assets/css/style.css">
</head>

<body>
    <?php
    session_start();
    require_once "class/Database.php";
    $db = new Database();
    $pdo = $db->getConnect();

    ?>

    <!--? Preloader Start -->
    <!-- <div id="preloader-active">
    <div class="preloader d-flex align-items-center justify-content-center">
        <div class="preloader-inner position-relative">
            <div class="preloader-circle"></div>
            <div class="preloader-img pere-text">
                <img src="inc/assets/img/logo/loder.jpg" alt="">
            </div>
        </div>
    </div>
</div> -->
    <!-- Preloader Start -->
    <header>
        <!-- Header Start -->
        <div class="header-area">
            <div class="main-header ">
                <div class="header-top d-none d-lg-block">
                    <div class="container">
                        <div class="col-xl-12">
                            <div class="row d-flex justify-content-between align-items-center">
                                <div class="header-info-left">
                                    <ul>
                                        <li>Phone: +99 (0) 101 0000 888</li>
                                        <li>Email: noreply@yourdomain.com</li>
                                    </ul>
                                </div>
                                <div class="header-info-right">
                                    <ul class="header-social">
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                        <li> <a href="#"><i class="fab fa-google-plus-g"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="header-bottom  header-sticky">
                    <div class="container">
                        <div class="row align-items-center">
                            <!-- Logo -->
                            <div class="col-xl-2 col-lg-2">
                                <div class="logo">
                                    <a href="index.php"><img src="https://viettelpost.vn/assets/images/logo-20210202.png" height="130" style="margin-top:20px;" alt=""></a>
                                </div>
                            </div>
                            <div class="col-xl-10 col-lg-10">
                                <div class="menu-wrapper  d-flex align-items-center justify-content-end">
                                    <!-- Main-menu -->
                                    <div class="main-menu d-none d-lg-block">
                                        <nav>
                                            <ul id="navigation">
                                                <?php if (isset($_SESSION['logged_name'])) : ?>

                                                    <?php if ($_SESSION['logged_role']==1 &&$_SESSION['logged_chucvu']=="Quản lý") : ?>
                                                        <li><a href="index.php">Trang chủ</a></li>
                                                        <li><a href="QuanLyDon.php">Quản lý Đơn</a></li>
                                                        <li><a href="#">Quản lý</a>
                                                            <ul class="submenu">
                                                                <li><a href="quanlynv.php">Quản Lý nhân viên</a></li>
                                                                <li><a href="themnv.php">Thêm nhân viên</a></li>
                                                            </ul>
                                                        </li>
                                                        <li><a href="#"><?= $_SESSION['logged_name']  ?></a>
                                                            <ul class="submenu">
                                                                <li><a href="dangxuat.php">Đăng xuất</a></li>
                                                            </ul>
                                                        </li>
                                                    <?php endif; ?>

                                                    <?php if ($_SESSION['logged_role']==1 &&$_SESSION['logged_chucvu']=="Nhân viên") : ?>
                                                        <li><a href="index.php">Trang chủ</a></li>
                                                        <li><a href="QuanLyDon.php">Quản lý Đơn</a></li>
                                                        <li><a href="#"><?= $_SESSION['logged_name']  ?></a>
                                                            <ul class="submenu">
                                                                <li><a href="dangxuat.php">Đăng xuất</a></li>
                                                            </ul>
                                                        </li>
                                                    <?php endif; ?>

                                                    <?php if ($_SESSION['logged_role']==0) : ?>
                                                        <li><a href="index.php">Trang chủ</a></li>
                                                        <li><a href="taodon.php">Tạo đơn</a></li>
                                                        <li><a href="#"><?= $_SESSION['logged_name']  ?></a>
                                                            <ul class="submenu">
                                                                <li><a href="dangxuat.php">Đăng xuất</a></li>
                                                            </ul>
                                                        </li>
                                                    <?php endif; ?>

                                                <?php else : ?>
                                                    <li><a href="index.php">Trang chủ</a></li>
                                                    <li><a href="taodon.php">Tạo đơn</a></li>
                                                    <li><a href="#">Tài khoản</a>
                                                        <ul class="submenu">
                                                            <li><a href="dangnhap.php">Đăng nhập</a></li>
                                                            <li><a href="dangky.php">Đăng ký</a></li>
                                                        </ul>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                            <!-- Mobile Menu -->
                            <div class="col-12">
                                <div class="mobile_menu d-block d-lg-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End -->
    </header>