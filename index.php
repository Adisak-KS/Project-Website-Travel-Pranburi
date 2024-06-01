<?php
$titlePage = "หน้าแรก";

require_once("db/connect.php");
try {
    // Slide ภาพสถานที่ท่องเที่ยว
    $sql = "SELECT  pbr_travel.tv_id, 
                    pbr_travel.tv_name, 
                    pbr_travel.tvt_id, 
                    pbr_travel.tv_cover, 
                    pbr_travel.tv_status, 
                    pbr_travel_type.tvt_name 
            FROM pbr_travel
            LEFT JOIN pbr_travel_type ON pbr_travel.tvt_id = pbr_travel_type.tvt_id
            WHERE pbr_travel.tv_status = 1 AND pbr_travel_type.tvt_status = 1
            ORDER BY RAND()";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // สถานที่ท่องเที่ยวยอดนิยม 6 แห่ง เรียงตาม ยอดเข้าชม
    $sql = "SELECT  pbr_travel.tv_id, 
                    pbr_travel.tv_name, 
                    pbr_travel.tvt_id, 
                    pbr_travel.tv_cover, 
                    pbr_travel.tv_status, 
                    pbr_travel_type.tvt_name,
                    SUM(pbr_travel_views.tvv_view) AS total_views
            FROM pbr_travel
            LEFT JOIN pbr_travel_type ON pbr_travel.tvt_id = pbr_travel_type.tvt_id
            LEFT JOIN pbr_travel_views ON pbr_travel.tv_id = pbr_travel_views.tv_id
            WHERE pbr_travel.tv_status = 1 
            AND pbr_travel_type.tvt_status = 1
            GROUP BY pbr_travel.tv_id, pbr_travel.tv_name, pbr_travel.tvt_id, pbr_travel.tv_cover, pbr_travel.tv_status, pbr_travel_type.tvt_name
            ORDER BY total_views DESC
            LIMIT 6";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $travelPopular = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // ข่าวประชาสัมพันธ์ เรียงจาก ล่าสุด จำนวน 6 รายการ
    $sql = "SELECT pbr_news.*, 
                    pbr_news_type.nst_name
            FROM pbr_news
            LEFT JOIN pbr_news_type ON pbr_news.nst_id = pbr_news_type.nst_id
            WHERE pbr_news.ns_status = 1 
            AND pbr_news_type.nst_status = 1
            ORDER BY pbr_news.time DESC
            LIMIT 6";
    $stmt = $conn->prepare($sql);
    $stmt = $conn->query($sql);
    $stmt->execute();
    $newsLatest = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // สถานที่ท่องเที่ยวล่าสุด 3 รายการล่าสุด
    $sql = "SELECT  pbr_travel.tv_id, 
                    pbr_travel.tv_name, 
                    pbr_travel.tvt_id, 
                    pbr_travel.tv_cover, 
                    pbr_travel.tv_status, 
                    pbr_travel.time, 
                    pbr_travel_type.tvt_name
            FROM pbr_travel
            LEFT JOIN pbr_travel_type ON pbr_travel.tvt_id = pbr_travel_type.tvt_id
            WHERE pbr_travel.tv_status = 1 
            AND pbr_travel_type.tvt_status = 1
            GROUP BY pbr_travel.tv_id
            ORDER BY pbr_travel.time DESC
            LIMIT 3";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $travelLatest = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("include/head.php") ?>
</head>

<body>
    <!-- Topbar Start -->
    <?php require_once("include/topbar.php") ?>
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <?php require_once("include/navbar.php") ?>
    <!-- Navbar End -->


    <!-- Carousel Start -->
    <div class="container-fluid p-0">
        <!-- มีข้อมูล  -->
        <?php if ($slides) { ?>
            <div id="header-carousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $firstSlide = true;
                    ?>
                    <?php foreach ($slides as $slide) { ?>
                        <?php
                        $activeClass = $firstSlide ? 'active' : '';
                        $firstSlide = false;
                        ?>
                        <style>
                            .carousel-item img {
                                object-fit: cover;
                                width: 100%;
                                height: 900px;
                            }

                            @media (max-width: 768px) {
                                .carousel-item img {
                                    height: 500px;
                                    /* Adjust height for smaller screens */
                                }
                            }
                        </style>

                        <div class="carousel-item <?php echo $activeClass ?>">
                            <img class="w-100" src="uploads/img_travel/<?php echo $slide["tv_cover"] ?>" alt="Image">
                            <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                                <div class="p-3" style="max-width: 900px;">
                                    <h4 class="text-white text-uppercase mb-md-3"><?php echo $slide["tvt_name"]; ?></h4>
                                    <h1 class="display-3 text-white mb-md-4"><?php echo $slide["tv_name"] ?></h1>

                                    <?php
                                    $originalId = $slide["tv_id"];
                                    require_once("include/salt.php");   // รหัส Salte 
                                    $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                    $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                    ?>

                                    <a href="travel_detail.php?id=<?php echo $base64Encoded; ?>" class="btn btn-primary py-md-3 px-md-5 mt-2">อ่านเพิ่มเติม</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
                <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
                    <div class="btn btn-dark" style="width: 45px; height: 45px;">
                        <span class="carousel-control-prev-icon mb-n2"></span>
                    </div>
                </a>
                <a class="carousel-control-next" href="#header-carousel" data-slide="next">
                    <div class="btn btn-dark" style="width: 45px; height: 45px;">
                        <span class="carousel-control-next-icon mb-n2"></span>
                    </div>
                </a>
            </div>
        <?php } else { ?>
            <div id="header-carousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img class="w-100" src="img/carousel-1.jpg" alt="Image">
                        <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                            <div class="p-3" style="max-width: 900px;">
                                <h4 class="text-white text-uppercase mb-md-3">ประเภทสถานที่ท่องเที่ยว</h4>
                                <h1 class="display-3 text-white mb-md-4">ชื่อสถานที่ท่องเที่ยว</h1>
                                <a href="" class="btn btn-primary py-md-3 px-md-5 mt-2">อ่านเพิ่มเติม</a>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="w-100" src="img/carousel-2.jpg" alt="Image">
                        <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                            <div class="p-3" style="max-width: 900px;">
                                <h4 class="text-white text-uppercase mb-md-3">ประเภทสถานที่ท่องเที่ยว</h4>
                                <h1 class="display-3 text-white mb-md-4">ชื่อสถานที่ท่องเที่ยว</h1>
                                <a href="" class="btn btn-primary py-md-3 px-md-5 mt-2">อ่านเพิ่มเติม</a>
                            </div>
                        </div>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
                    <div class="btn btn-dark" style="width: 45px; height: 45px;">
                        <span class="carousel-control-prev-icon mb-n2"></span>
                    </div>
                </a>
                <a class="carousel-control-next" href="#header-carousel" data-slide="next">
                    <div class="btn btn-dark" style="width: 45px; height: 45px;">
                        <span class="carousel-control-next-icon mb-n2"></span>
                    </div>
                </a>
            </div>

        <?php } ?>

    </div>
    <!-- Carousel End -->


    <!-- Search Start -->
    <div class="container-fluid booking mt-5">
        <div class="container pb-5">
            <div class="bg-light shadow" style="padding: 30px;">
                <form action="search_travel_show.php" method="get">
                    <div class="row align-items-center" style="min-height: 60px;">

                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 mb-md-0">
                                        <input type="text" name="search" class="form-control px-4" style="height: 47px;" placeholder="ระบุ คำค้นหา ที่ต้องการ">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-block" type="submit" style="height: 47px; margin-top: -2px;">
                                <span>ค้นหา</span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Search End -->

    <!-- Destination Start -->
    <div class="container-fluid">
        <div class="container pb-3">
            <div class="text-center mb-3 pb-3">
                <h4 class="text-primary text-uppercase">แนะนำ</h4>
                <h1>สถานที่ท่องเที่ยวยอดนิยม</h1>
            </div>
            <div class="row">
                <?php if ($travelPopular) { ?>
                    <?php foreach ($travelPopular as $travel) { ?>

                        <div class="col-lg-4 col-md-6 mb-4">
                            <?php
                            $originalId = $travel["tv_id"];
                            require_once("include/salt.php");   // รหัส Salte 
                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                            ?>

                            <a href="travel_detail.php?id=<?php echo $base64Encoded; ?>">
                                <div class="destination-item position-relative overflow-hidden mb-2">
                                    <img class="img-fluid w-100" style="height: 235px;" src="uploads/img_travel/<?php echo $travel["tv_cover"]; ?>" alt="">
                                    <div class="destination-overlay text-white text-decoration-none text-center">
                                        <h5 class="text-white">
                                            <?php
                                            $tv_name = $travel["tv_name"];
                                            if (mb_strlen($tv_name, 'UTF-8') > 25) {
                                                echo mb_substr($tv_name, 0, 25, 'UTF-8') . '...';
                                            } else {
                                                echo $tv_name;
                                            }
                                            ?>
                                        </h5>
                                        <span>
                                            <?php
                                            $tvt_name = $travel["tvt_name"];
                                            if (mb_strlen($tvt_name, 'UTF-8') > 25) {
                                                echo mb_substr($tvt_name, 0, 25, 'UTF-8') . '...';
                                            } else {
                                                echo $tvt_name;
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>

                    <?php } ?>
                <?php } else { ?>
                    <?php for ($i = 0; $i < 6; $i++) { ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="destination-item position-relative overflow-hidden mb-2">
                                <img class="img-fluid" src="img/destination-1.jpg" alt="">
                                <a class="destination-overlay text-white text-decoration-none" href="">
                                    <h5 class="text-white">ชื่อสถานที่ท่องเที่ยว</h5>
                                    <span>ประเภท</span>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- Destination Start -->


    <!-- Service Start -->
    <div class="container-fluid py-3">
        <div class="container pt-3 pb-3">
            <div class="text-center mb-3 pb-3">
                <h4 class="text-primary text-uppercase">บริการ</h4>
                <h1>สถานที่ท่องเที่ยว & ข่าวสาร & ที่พัก</h1>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-item bg-white text-center mb-2 py-5 px-4">
                        <i class="fa fa-2x fa-route mx-auto mb-4"></i>
                        <h5 class="mb-2">สถานที่ท่องเที่ยว</h5>
                        <p class="m-0">เราได้รวบรวมสถานที่ท่องเที่ยวในอำเภอปราณบุรี จังหวัดประจวบคีรีขันธ์ มาให้เลือกชมอยู่ในเว็บไซต์นี้</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-item bg-white text-center mb-2 py-5 px-4">
                        <i class="fa fa-2x fa-ticket-alt mx-auto mb-4"></i>
                        <h5 class="mb-2">ข่าว/ประชาสัมพันธ์</h5>
                        <p class="m-0">ข่าว / ประชาสัมพันธ์ เกี่ยวกับงาน กิจกรรมต่างๆ ที่เกิดขึ้นใน อำเภอปราณบุรี จังหวัดประจวบคีรีขันธ์</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="service-item bg-white text-center mb-2 py-5 px-4">
                        <i class="fa fa-2x fa-hotel mx-auto mb-4"></i>
                        <h5 class="mb-2">ที่พัก</h5>
                        <p class="m-0">สถานที่พักผ่อนที่หลากหลายใน อำเภอปราณบุรี จังหวัดประจวบคีรีขันธ์ เช่น โรงแรม รีสอร์ท เป็นต้น</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->


    <!-- Packages Start -->
    <div class="container-fluid py-3">
        <div class="container pt-3 pb-3">
            <div class="text-center mb-3 pb-3">
                <h4 class="text-primary text-uppercase">ข่าว / ประชาสัมพันธ์</h4>
                <h1>ติดตามได้ที่นี้</h1>
            </div>
            <div class="row">
                <?php if ($newsLatest) { ?>
                    <?php foreach ($newsLatest as $news) { ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="package-item bg-white mb-2">
                                <div class="d-flex justify-content-center align-items-center">
                                    <img class="img-fluid w-100" style="height: 235px;" src="uploads/img_news/<?php echo $news["ns_cover"]; ?>" alt="">
                                </div>
                                <div class="p-4">
                                    <div class="d-flex justify-content-between mb-3">

                                        <?php
                                        $originalId = $news["nst_id"];
                                        require_once("include/salt.php");   // รหัส Salte 
                                        $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                        $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                        ?>

                                        <a href="news_show.php?id=<?php echo $base64Encoded ?>" class="text-primary text-uppercase text-decoration-none">
                                            <small class="m-0">
                                                <i class="fa-solid fa-book-atlas text-primary mr-2"></i>
                                                <?php
                                                $nst_name = $news["nst_name"];
                                                if (mb_strlen($nst_name, 'UTF-8') > 15) {
                                                    echo mb_substr($nst_name, 0, 15, 'UTF-8') . '...';
                                                } else {
                                                    echo $nst_name;
                                                }
                                                ?>
                                            </small>
                                        </a>
                                        <small class="m-0"><i class="fa fa-calendar-alt text-primary mr-2"></i><?php echo $news["time"] ?></small>
                                        <!-- <small class="m-0"><i class="fa fa-user text-primary mr-2"></i>ยอดเข้าชม</small> -->
                                    </div>

                                    <?php
                                    $originalId = $news["ns_id"];
                                    require_once("include/salt.php");   // รหัส Salte 
                                    $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                    $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                    ?>
                                    <a class="h5 text-decoration-none" href="news_detail.php?id=<?php echo $base64Encoded; ?>">
                                        <?php
                                        $ns_title = $news["ns_title"];
                                        if (mb_strlen($ns_title, 'UTF-8') > 25) {
                                            echo mb_substr($ns_title, 0, 25, 'UTF-8') . '...';
                                        } else {
                                            echo $ns_title;
                                        }
                                        ?>
                                    </a>
                                    <div class="border-top mt-4 pt-4">
                                        <div class="d-flex justify-content-between">
                                            <a href="news_detail.php?id=<?php echo $base64Encoded; ?>" class="btn btn-primary rounded-pill w-100">
                                                <span> อ่านเพิ่มเติม</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <?php for ($i = 0; $i < 6; $i++) { ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="package-item bg-white mb-2">
                                <img class="img-fluid" src="img/package-1.jpg" alt="">
                                <div class="p-4">
                                    <div class="d-flex justify-content-between mb-3">
                                        <small class="m-0"><i class="fa fa-map-marker-alt text-primary mr-2"></i>ประเภทข่าว</small>
                                        <small class="m-0"><i class="fa fa-calendar-alt text-primary mr-2"></i>วันประกาศ</small>
                                        <!-- <small class="m-0"><i class="fa fa-user text-primary mr-2"></i>ยอดเข้าชม</small> -->
                                    </div>
                                    <a class="h5 text-decoration-none" href="">หัวข้อข่าว</a>
                                    <div class="border-top mt-4 pt-4">
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-primary rounded-pill w-100">อ่านเพิ่มเติม</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- Packages End -->

    <!-- Blog Start -->
    <div class="container-fluid py-3">
        <div class="container pt-3 pb-3">
            <div class="text-center mb-3 pb-3">
                <h4 class="text-primary text-uppercase">สถานที่ท่องเที่ยวใหม่</h4>
                <h1>สถานที่ท่องเที่ยวล่าสุด</h1>
            </div>
            <div class="row pb-3">
                <?php if ($travelLatest) { ?>
                    <?php foreach ($travelLatest as $travel) { ?>

                        <div class="col-lg-4 col-md-6 mb-4 pb-2">
                            <div class="blog-item">
                                <div class="position-relative">
                                    <img class="img-fluid w-100" style="height: 220px;" src="uploads/img_travel/<?php echo $travel["tv_cover"]; ?>" alt="">
                                    <div class="blog-date">
                                        <?php
                                        $timestamp = $travel["time"];
                                        $day = date("d", strtotime($timestamp));
                                        $month = date("M", strtotime($timestamp));
                                        ?>
                                        <h6 class="font-weight-bold mb-n1"><?php echo $day; ?></h6>
                                        <small class="text-white text-uppercase"><?php echo $month; ?></small>
                                    </div>
                                </div>
                                <div class="bg-white p-4">
                                    <div class="d-flex mb-2">
                                        <p class="text-primary text-uppercase text-decoration-none">Admin</p>
                                        <span class="text-primary px-2">|</span>

                                        <?php
                                        $originalId = $travel["tvt_id"];
                                        require_once("include/salt.php");   // รหัส Salte 
                                        $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                        $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                        ?>

                                        <a class="text-primary text-uppercase text-decoration-none" href="travels_show.php?id=<?php echo $base64Encoded ?>">
                                            <?php
                                            $tvt_name = $travel["tvt_name"];
                                            if (mb_strlen($tvt_name, 'UTF-8') > 20) {
                                                echo mb_substr($tvt_name, 0, 20, 'UTF-8') . '...';
                                            } else {
                                                echo $tvt_name;
                                            }
                                            ?>
                                        </a>
                                    </div>

                                    <?php
                                    $originalId = $travel["tv_id"];
                                    require_once("include/salt.php");   // รหัส Salte 
                                    $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                    $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                    ?>
                                    <a class="h5 m-0 text-decoration-none" href="travel_detail.php?id=<?php echo $base64Encoded ?>">
                                        <?php
                                        $tv_name = $travel["tv_name"];
                                        if (mb_strlen($tv_name, 'UTF-8') > 25) {
                                            echo mb_substr($tv_name, 0, 25, 'UTF-8') . '...';
                                        } else {
                                            echo $tv_name;
                                        }
                                        ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <?php for ($i = 0; $i < 3; $i++) { ?>
                        <div class="col-lg-4 col-md-6 mb-4 pb-2">
                            <div class="blog-item">
                                <div class="position-relative">
                                    <img class="img-fluid w-100" src="img/blog-1.jpg" alt="">
                                    <div class="blog-date">
                                        <h6 class="font-weight-bold mb-n1">01</h6>
                                        <small class="text-white text-uppercase">Jan</small>
                                    </div>
                                </div>
                                <div class="bg-white p-4">
                                    <div class="d-flex mb-2">
                                        <a class="text-primary text-uppercase text-decoration-none" href="">Admin</a>
                                        <span class="text-primary px-2">|</span>
                                        <a class="text-primary text-uppercase text-decoration-none" href="">ประเภทสถานที่ท่องเที่ยว</a>
                                    </div>
                                    <a class="h5 m-0 text-decoration-none" href="">ชื่อสถานที่ท่องเที่ยว</a>
                                </div>
                            </div>
                        </div>

                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- Blog End -->

    <!-- Footer  -->
    <?php require_once("include/footer.php"); ?>

    <!-- JavaScript Libraries -->
    <?php require_once("include/libraries.php"); ?>
</body>

</html>