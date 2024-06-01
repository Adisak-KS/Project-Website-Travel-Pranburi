<?php
require_once("db/connect.php");
$titlePage = "ข่าวทั้งหมด";

try {
    if (isset($_GET["id"])) {
        $base64Encoded = $_GET["id"];

        $_SESSION["base64Encoded"] = $_GET["id"];
        // นำ base64Encoded มาเก็บใน session และเก็บใน
        $base64Decoded = base64_decode($base64Encoded);

        $salt1 = $_SESSION["salt1"];
        $salt2 = $_SESSION["salt2"];

        // แยกส่วน salt1, ID ที่ไม่เข้ารหัส, และ salt2
        $salt1Length = mb_strlen($salt1, 'UTF-8');
        $salt2Length = mb_strlen($salt2, 'UTF-8');

        $salt1 = substr($base64Decoded, 0, $salt1Length);
        $saltedId = substr($base64Decoded, $salt1Length, -$salt2Length);
        $salt2 = substr($base64Decoded, -$salt2Length);

        // สร้างค่า originalId โดยตัดทิ้ง salt ทั้งสองด้าน
        $originalId = str_replace([$salt1, $salt2], '', $saltedId);

        $nstId = $originalId;

        // แสดงสถานที่ท่งเที่ยวทั้งหมด ตามประเภท ที่มี ns_status = 1 และ nst_status = 1 
        $sql = "SELECT  pbr_news.ns_id, 
                        pbr_news.ns_title, 
                        pbr_news.nst_id, 
                        pbr_news.ns_cover, 
                        pbr_news.ns_status, 
                        pbr_news.time, 
                        pbr_news_type.nst_name 
                FROM pbr_news
                LEFT JOIN pbr_news_type ON pbr_news.nst_id = pbr_news_type.nst_id
                WHERE pbr_news.nst_id = :nst_id  AND pbr_news.ns_status = 1 AND pbr_news_type.nst_status = 1
                ORDER BY pbr_news.time DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":nst_id", $nstId);
        $stmt->execute();
        $newsAll = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // แสดงสถานที่ท่งเที่ยวทั้งหมด ที่มี ns_status = 1 และ nst_status = 1
        $sql = "SELECT  pbr_news.ns_id, 
                pbr_news.ns_title, 
                pbr_news.nst_id, 
                pbr_news.ns_cover, 
                pbr_news.ns_status, 
                pbr_news.time, 
                pbr_news_type.nst_name 
        FROM pbr_news
        LEFT JOIN pbr_news_type ON pbr_news.nst_id = pbr_news_type.nst_id
        WHERE pbr_news.ns_status = 1 AND pbr_news_type.nst_status = 1
        ORDER BY pbr_news.time DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $newsAll = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    // ประเภทข่าว และจำนวนข่าว
    $sql = "SELECT  pbr_news_type.nst_id, 
                    pbr_news_type.nst_name, 
                    COUNT(pbr_news.nst_id) AS news_count
            FROM pbr_news_type
            LEFT JOIN pbr_news ON pbr_news_type.nst_id = pbr_news.nst_id AND pbr_news.ns_status = 1
            WHERE pbr_news_type.nst_status = 1 AND pbr_news.ns_status =1
            GROUP BY pbr_news_type.nst_id, pbr_news_type.nst_name";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $newsTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // ข่าวล่าสุด 3 รายการล่าสุด
    $sql = "SELECT  pbr_news.ns_id, 
                    pbr_news.ns_title, 
                    pbr_news.nst_id, 
                    pbr_news.ns_cover, 
                    pbr_news.ns_status, 
                    pbr_news.time, 
                    pbr_news_type.nst_name 
            FROM pbr_news
            LEFT JOIN pbr_news_type ON pbr_news.nst_id = pbr_news_type.nst_id
            WHERE pbr_news.ns_status = 1 
            AND pbr_news_type.nst_status = 1
            GROUP BY pbr_news.ns_id
            ORDER BY pbr_news.time DESC
            LIMIT 3";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $newsLatest = $stmt->fetchAll(PDO::FETCH_ASSOC);
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


    <!-- Header Start -->
    <div class="container-fluid page-header">
        <div class="container">
            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 400px">

                <?php if (isset($_GET["id"])  && !empty($newsAll)) { ?>
                    <h3 class="text-white">ประเภทข่าว</h3>
                    <h3 class="display-4 text-white text-uppercase"><?php echo $newsAll[0]["nst_name"]; ?></h3>
                    <div class="d-inline-flex text-white">
                        <p class="m-0 text-uppercase"><a class="text-white" href="index.php">หน้าหลัก</a></p>
                        <i class="fa fa-angle-double-right pt-1 px-3"></i>
                        <p class="m-0 text-uppercase"><?php echo $newsAll[0]["nst_name"]; ?></p>
                    </div>
                <?php } else { ?>
                    <h3 class="display-4 text-white text-uppercase">ข่าวทั้งหมด</h3>
                    <div class="d-inline-flex text-white">
                        <p class="m-0 text-uppercase"><a class="text-white" href="index.php">หน้าหลัก</a></p>
                        <i class="fa fa-angle-double-right pt-1 px-3"></i>
                        <p class="m-0 text-uppercase">ข่าวทั้งหมด</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- Header End -->


    <!-- Search Start -->
    <div class="container-fluid booking mt-5">
        <div class="container pb-5">
            <div class="bg-light shadow" style="padding: 30px;">
                <form action="search_news_show.php" method="get">
                    <div class="row align-items-center" style="min-height: 60px;">

                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 mb-md-0">
                                        <input type="text" name="search" class="form-control px-4" style="height: 47px;" placeholder="ระบุ คำค้นหาข่าว ที่ต้องการ">
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

    <!-- Blog Start -->
    <div class="container-fluid ">
        <div class="container ">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row pb-3">
                        <?php if ($newsAll) { ?>
                            <?php foreach ($newsAll as $news) { ?>
                                <div class="col-md-6 mb-4 pb-2">
                                    <div class="blog-item">
                                        <div class="position-relative">
                                            <img class="img-fluid w-100" style="height: 220px;" src="uploads/img_news/<?php echo $news["ns_cover"]; ?>" alt="">
                                            <div class="blog-date">
                                                <?php
                                                $timestamp = $news["time"];
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
                                                $originalId = $news["nst_id"];
                                                require_once("include/salt.php");   // รหัส Salte 
                                                $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                                $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                                ?>

                                                <a class="text-primary text-uppercase text-decoration-none" href="news_show.php?id=<?php echo $base64Encoded ?>">
                                                    <?php
                                                    $nst_name = $news["nst_name"];
                                                    if (mb_strlen($nst_name, 'UTF-8') > 15) {
                                                        echo mb_substr($nst_name, 0, 15, 'UTF-8') . '...';
                                                    } else {
                                                        echo $nst_name;
                                                    }
                                                    ?>
                                                </a>
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

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="alert alert-warning text-center w-100" role="alert">
                                <h4 class="alert-heading mt-2">ไม่พบข่าว /ประชาสัมพันธ์!</h4>
                                <i class="fa-solid fa-magnifying-glass fa-5x my-5"></i>
                                <p>ไม่พบข่าว ขออภัยในความไม่สะดวก</p>
                                <hr>

                                <div class="mt-1 d-flex justify-content-between">
                                    <a href="index.php" class="btn btn-primary">
                                        <i class="fa-solid fa-arrow-left"></i>
                                        <span>กลับหน้าหลัก</span>
                                    </a>
                                    <a href="news_show.php" class="btn btn-primary">
                                        <span>ข่าวทั้งหมด</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-lg-4 mt-5 mt-lg-0">
                    <!-- Category List -->
                    <div class="mb-5">
                        <h4 class="text-uppercase mb-4">ประเภทข่าว</h4>
                        <div class="bg-white" style="padding: 30px;">
                            <ul class="list-inline m-0">
                                <?php if ($newsTypes) { ?>
                                    <?php foreach ($newsTypes as $type) { ?>

                                        <li class="mb-3 d-flex justify-content-between align-items-center">
                                            <?php
                                            $originalId = $type["nst_id"];
                                            require_once("include/salt.php");   // รหัส Salte 
                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                            ?>

                                            <a href="news_show.php?id=<?php echo $base64Encoded ?>" class="text-dark">
                                                <i class="fa fa-angle-right text-primary mr-2"></i>
                                                <span>
                                                    <?php
                                                    $nst_name = $type["nst_name"];
                                                    if (mb_strlen($nst_name, 'UTF-8') > 20) {
                                                        echo mb_substr($nst_name, 0, 20, 'UTF-8') . '...';
                                                    } else {
                                                        echo $nst_name;
                                                    }
                                                    ?>
                                                </span>
                                            </a>

                                            <span class="badge badge-primary badge-pill"><?php echo $type["news_count"] ?></span>
                                        </li>

                                    <?php } ?>
                                <?php } else { ?>
                                    <?php for ($i = 0; $i < 3; $i++) { ?>
                                        <li class="mb-3 d-flex justify-content-between align-items-center">
                                            <a class="text-dark" href="#">
                                                <i class="fa fa-angle-right text-primary mr-2"></i>
                                                <span>ประเภทข่าว</span>
                                            </a>
                                            <span class="badge badge-primary badge-pill">131</span>
                                        </li>

                                    <?php }  ?>
                                <?php }  ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Recent Post -->
                    <div class="mb-5">
                        <h4 class="text-uppercase mb-4">ข่าวล่าสุด</h4>
                        <?php if ($newsLatest) { ?>
                            <?php foreach ($newsLatest as $news) { ?>
                                <?php
                                $originalId = $news["ns_id"];
                                require_once("include/salt.php");   // รหัส Salte 
                                $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                ?>

                                <a href="news_detail.php?id=<?php echo $base64Encoded; ?>" class="d-flex align-items-center text-decoration-none bg-white mb-3">
                                    <img class="img-fluid" style="width: 100px; height:100px" src="uploads/img_news/<?php echo $news["ns_cover"] ?>" alt="">
                                    <div class="pl-3">
                                        <h6 class="m-1">
                                            <span class="text-dark">
                                                <?php
                                                $ns_title = $news["ns_title"];
                                                if (mb_strlen($ns_title, 'UTF-8') > 25) {
                                                    echo mb_substr($ns_title, 0, 25, 'UTF-8') . '...';
                                                } else {
                                                    echo $ns_title;
                                                }
                                                ?>
                                            </span>
                                        </h6>

                                        <?php
                                        $timestamp = strtotime($news["time"]);
                                        $formatted_date = date("d M Y", $timestamp);
                                        ?>
                                        <p><small><?php echo $formatted_date; ?></small></p>
                                    </div>
                                </a>
                            <?php } ?>
                        <?php } else { ?>
                            <?php for ($i = 0; $i < 3; $i++) { ?>
                                <a class="d-flex align-items-center text-decoration-none bg-white mb-3" href="">
                                    <img class="img-fluid" src="img/blog-100x100.jpg" alt="">
                                    <div class="pl-3">
                                        <h6 class="m-1">หัวข้อข่าว</h6>
                                        <small>Jan 01, 2050</small>
                                    </div>
                                </a>
                            <?php } ?>
                        <?php } ?>
                    </div>
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