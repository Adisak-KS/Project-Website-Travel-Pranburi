<?php
require_once("db/connect.php");
$titlePage = "รายละเอียดข่าว";


// แสดงข้อมูล news ตาม ns_id
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

    $nsId = $originalId;

    try {

        $sql = "SELECT pbr_news.*, 
                        pbr_news_type.nst_name, 
                        SUM(pbr_news_views.nsv_view) AS total_views
                FROM pbr_news
                LEFT JOIN pbr_news_type ON pbr_news.nst_id = pbr_news_type.nst_id
                INNER JOIN pbr_news_views ON pbr_news.ns_id = pbr_news_views.ns_id
                WHERE pbr_news.ns_id = :ns_id
                GROUP BY pbr_news.ns_id, pbr_news_type.nst_name";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ns_id", $nsId, PDO::PARAM_INT);
        $stmt->execute();
        $detail = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($detail) {

            $nsId = $detail["ns_id"];
            $nstId = $detail["nst_id"];
            $nsvView = 1;

            // ให้นับจำนวนการเข้าชม
            $sql = "INSERT INTO pbr_news_views (ns_id, nst_id, nsv_view)
                    VALUES (:ns_id, :nst_id, :nsv_view)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":ns_id", $nsId);
            $stmt->bindParam(":nst_id", $nstId);
            $stmt->bindParam(":nsv_view", $nsvView);
            $stmt->execute();
        } else {
            header("Location: index.php");
            exit();
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
                AND pbr_news.ns_id != :ns_id
                GROUP BY pbr_news.ns_id
                ORDER BY RAND()
                LIMIT 3";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ns_id", $nsId);
        $stmt->execute();
        $newsLatest = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit();
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
                <h3 class="display-4 text-white text-uppercase"><?php echo $detail["ns_title"]; ?></h3>
                <p class="text-white">รายละเอียดข่าว/ประชาสัมพันธ์</p>
                <div class="d-inline-flex text-white">
                    <p class="m-0 text-uppercase"><a class="text-white" href="index.php">หน้าหลัก</a></p>
                    <i class="fa fa-angle-double-right pt-1 px-3"></i>
                    <p class="m-0 text-uppercase">รายละเอียดข่าว/ประชาสัมพันธ์</p>
                </div>
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
    <div class="container-fluid">
        <div class="container py-0">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Blog Detail Start -->
                    <div class="pb-3">
                        <div class="blog-item">
                            <div class="position-relative">
                                <img class="img-fluid w-100 bg-light" style="height: 500px;" src="uploads/img_news/<?php echo $detail["ns_cover"] ?>" alt="">
                                <div class="blog-date">
                                    <?php
                                    $timestamp = $detail["time"];
                                    $day = date("d", strtotime($timestamp));
                                    $month = date("M", strtotime($timestamp));
                                    ?>
                                    <h6 class="font-weight-bold mb-n1"></h6><?php echo $day ?></h6>
                                    <small class="text-white text-uppercase"><?php echo $month ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white mb-1" style="padding: 30px;">
                            <div class="d-flex">
                                <p class="text-primary text-uppercase text-decoration-none"> <span class="text-dark"> อัปเดทเมื่อ :</span><?php echo " " . $detail["time"] ?></p>
                                <span class="text-primary px-2">|</span>


                                <?php
                                $originalId = $detail["nst_id"];
                                require_once("include/salt.php");   // รหัส Salte 
                                $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                ?>

                                <a class="text-primary text-uppercase text-decoration-none" href="news_show.php?id=<?php echo $base64Encoded ?>">
                                    <span class="text-dark">ประเภทข่าว :</span>
                                    <?php
                                    $nst_name = $detail["nst_name"];
                                    if (mb_strlen($nst_name, 'UTF-8') > 30) {
                                        echo mb_substr($nst_name, 0, 30, 'UTF-8') . '...';
                                    } else {
                                        echo $nst_name;
                                    }
                                    ?>
                                </a>
                            </div>

                            <h2 class="mb-3 mt-3 text-center text-primary"><?php echo $detail["ns_title"]; ?></h2>
                            <p><?php echo $detail["ns_detail"]; ?></p>

                            <!-- Go Back  -->
                            <hr>
                            <div class="mt-5 d-flex justify-content-between">
                                <a href="index.php" class="btn  btn-primary">
                                    <i class="fa-solid fa-arrow-left"></i>
                                    <span>กลับหน้าหลัก</span>
                                </a>
                                <a href="news_show.php" class="btn btn-primary">
                                    <span>ข่าวทั้งหมด</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                    <!-- Blog Detail End -->
                </div>

                <div class="col-lg-4 mt-5 mt-lg-0">

                    <!-- Views -->
                    <div class="d-flex flex-column text-center bg-white mb-5 py-4 px-4">
                        <?php if ($detail["total_views"] < 10) { ?>
                            <i class="fa-solid fa-face-frown fa-5x text-danger"></i>
                        <?php } elseif ($detail["total_views"] < 50) { ?>
                            <i class="fa-solid fa-face-meh fa-5x text-info"></i>
                        <?php } elseif ($detail["total_views"] < 100) { ?>
                            <i class="fa-solid fa-face-laugh fa-5x text-warning"></i>
                        <?php } elseif ($detail["total_views"] > 100) { ?>
                            <i class="fa-solid fa-face-grin-stars fa-5x text-primary"></i>
                        <?php } ?>
                        <h5 class="mt-3">จำนวนการเข้าชม (ครั้ง)</h5>
                        <h4 class="text-primary"><?php echo number_format($detail["total_views"]) ?></h4>
                        <div class="d-flex justify-content-center">

                        </div>
                    </div>

                    <!-- Category List -->
                    <div class="mb-5">
                        <h4 class="text-uppercase mb-4">ประเภทข่าว</h4>
                        <div class="bg-white" style="padding: 30px;">
                            <ul class="list-inline m-0">
                                <?php foreach ($newsTypes as $type) { ?>
                                    <li class="mb-3 d-flex justify-content-between align-items-center">
                                        <?php
                                        $originalId = $type["nst_id"];
                                        require_once("include/salt.php");   // รหัส Salte 
                                        $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                        $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                        ?>

                                        <a class="text-dark" href="news_show.php?id=<?php echo $base64Encoded ?>">
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
                            </ul>
                        </div>
                    </div>

                    <!-- Recent Post -->
                    <div class="mb-5">
                        <h4 class="text-uppercase mb-4">ข่าวล่าสุด</h4>
                        <?php foreach ($newsLatest as $news) { ?>
                            <?php
                            $originalId = $news["ns_id"];
                            require_once("include/salt.php");   // รหัส Salte 
                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                            ?>

                            <a href="news_detail.php?id=<?php echo $base64Encoded; ?>" class="d-flex align-items-center text-decoration-none bg-white mb-3">
                                <img class="img-fluid" style=" width:100px; height: 100px;" src="uploads/img_news/<?php echo $news["ns_cover"] ?>" alt="">
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
                    </div>
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