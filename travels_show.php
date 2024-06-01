<?php
require_once("db/connect.php");
$titlePage = "สถานที่ท่องเที่ยวทั้งหมด";

try {

    // เมื่อมีการกดปุ่มที่ประภทสถานที่ท่องเที่ยว
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

        $tvtId = $originalId;

        $sql = "SELECT  pbr_travel.tv_id, 
                    pbr_travel.tv_name, 
                    pbr_travel.tvt_id, 
                    pbr_travel.tv_cover, 
                    pbr_travel.tv_status, 
                    pbr_travel.time, 
                    pbr_travel_type.tvt_name 
            FROM pbr_travel
            LEFT JOIN pbr_travel_type ON pbr_travel.tvt_id = pbr_travel_type.tvt_id
            WHERE pbr_travel.tv_status = 1 AND pbr_travel_type.tvt_status = 1 AND  pbr_travel.tvt_id = :tvt_id 
            ORDER BY pbr_travel.time DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tvt_id", $tvtId);
        $stmt->execute();
        $travels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // แสดงสถานที่ท่งเที่ยวทั้งหมด ที่มี tv_status = 1 และ tvt_status = 1
        $sql = "SELECT  pbr_travel.tv_id, 
                    pbr_travel.tv_name, 
                    pbr_travel.tvt_id, 
                    pbr_travel.tv_cover, 
                    pbr_travel.tv_status, 
                    pbr_travel.time, 
                    pbr_travel_type.tvt_name 
            FROM pbr_travel
            LEFT JOIN pbr_travel_type ON pbr_travel.tvt_id = pbr_travel_type.tvt_id
            WHERE pbr_travel.tv_status = 1 AND pbr_travel_type.tvt_status = 1
            ORDER BY pbr_travel.time DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $travels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }





    // ประเภทสถานที่ท่องเที่ยว และจำนวนสถานที่ท่องเที่ยว
    $sql = "SELECT  pbr_travel_type.tvt_id, 
                    pbr_travel_type.tvt_name, 
                    COUNT(pbr_travel.tvt_id) AS travel_count
            FROM pbr_travel_type
            LEFT JOIN pbr_travel ON pbr_travel_type.tvt_id = pbr_travel.tvt_id AND pbr_travel.tv_status = 1
            WHERE pbr_travel_type.tvt_status = 1 AND pbr_travel.tv_status = 1
            GROUP BY pbr_travel_type.tvt_id, pbr_travel_type.tvt_name";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $travelTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
            ORDER BY RAND()
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


    <!-- Header Start -->
    <div class="container-fluid page-header">
        <div class="container">
            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 400px">
                <?php if (isset($_GET["id"])  && !empty($travels)) { ?>
                    <h3 class="text-white">ประเภทสถานที่ท่องเที่ยว</h3>
                    <h3 class="display-4 text-white text-uppercase"><?php echo $travels[0]["tvt_name"]; ?></h3>
                    <div class="d-inline-flex text-white">
                        <p class="m-0 text-uppercase"><a class="text-white" href="index.php">หน้าหลัก</a></p>
                        <i class="fa fa-angle-double-right pt-1 px-3"></i>
                        <p class="m-0 text-uppercase"><?php echo $travels[0]["tvt_name"]; ?></p>
                    </div>
                <?php } else { ?>
                    <h3 class="display-4 text-white text-uppercase">สถานที่ท่องเที่ยวทั้งหมด</h3>
                    <div class="d-inline-flex text-white">
                        <p class="m-0 text-uppercase"><a class="text-white" href="index.php">หน้าหลัก</a></p>
                        <i class="fa fa-angle-double-right pt-1 px-3"></i>
                        <p class="m-0 text-uppercase">สถานที่ท่องเที่ยวทั้งหมด</p>
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


    <!-- Blog Start -->
    <div class="container-fluid ">
        <div class="container ">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row pb-3">
                        <?php if ($travels) { ?>
                            <?php foreach ($travels as $travel) { ?>
                                <div class="col-md-6 mb-4 pb-2">
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
                                                <a class="text-primary text-uppercase text-decoration-none" href="">Admin</a>
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
                            <div class="alert alert-warning text-center w-100" role="alert">
                                <h4 class="alert-heading mt-2">ไม่พบสถานที่ท่องเที่ยว!</h4>
                                <i class="fa-solid fa-magnifying-glass fa-5x my-5"></i>
                                <p>ไม่พบสถานที่ท่องเที่ยว ขออภัยในความไม่สะดวก</p>
                                <hr>

                                <div class="mt-1 d-flex justify-content-between">
                                    <a href="index.php" class="btn btn-primary">
                                        <i class="fa-solid fa-arrow-left"></i>
                                        <span>กลับหน้าหลัก</span>
                                    </a>
                                    <a href="travels_show.php" class="btn btn-primary">
                                        <span>สถานที่ท่องเที่ยวทั้งหมด</span>
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
                        <h4 class="text-uppercase mb-4">ประเภทสถานที่ท่องเที่ยว</h4>
                        <div class="bg-white" style="padding: 30px;">
                            <ul class="list-inline m-0">
                                <?php if ($travelTypes) { ?>
                                    <?php foreach ($travelTypes as $type) { ?>

                                        <li class="mb-3 d-flex justify-content-between align-items-center">
                                            <?php
                                            $originalId = $type["tvt_id"];
                                            require_once("include/salt.php");   // รหัส Salte 
                                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                            ?>

                                            <a class="text-dark" href="travels_show.php?id=<?php echo $base64Encoded ?>">
                                                <i class="fa fa-angle-right text-primary mr-2"></i>
                                                <span><?php echo $type["tvt_name"] ?></span>
                                            </a>

                                            <span class="badge badge-primary badge-pill"><?php echo $type["travel_count"] ?></span>
                                        </li>

                                    <?php } ?>
                                <?php } else { ?>
                                    <?php for ($i = 0; $i < 3; $i++) { ?>
                                        <li class="mb-3 d-flex justify-content-between align-items-center">
                                            <a class="text-dark" href="#">
                                                <i class="fa fa-angle-right text-primary mr-2"></i>
                                                <span>ประเภทข่าว</span>
                                            </a>
                                            <span class="badge badge-primary badge-pill">150</span>
                                        </li>
                                    <?php } ?>
                                <?php } ?>

                            </ul>
                        </div>
                    </div>

                    <!-- Recent Post -->
                    <div class="mb-5">
                        <h4 class="text-uppercase mb-4">สถานที่ท่องเที่ยวแนะนำ</h4>
                        <?php if ($travelLatest) { ?>
                            <?php foreach ($travelLatest as $travel) { ?>
                                <?php
                                $originalId = $travel["tv_id"];
                                require_once("include/salt.php");   // รหัส Salte 
                                $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                ?>

                                <a href="travel_detail.php?id=<?php echo $base64Encoded; ?>" class="d-flex align-items-center text-decoration-none bg-white mb-3">
                                    <img class="img-fluid" style="width: 100px; height:100px" src="uploads/img_travel/<?php echo $travel["tv_cover"] ?>" alt="">
                                    <div class="pl-3">
                                        <h6 class="m-1">
                                            <span class="text-dark">
                                                <?php
                                                $tv_name = $travel["tv_name"];
                                                if (mb_strlen($tv_name, 'UTF-8') > 25) {
                                                    echo mb_substr($tv_name, 0, 25, 'UTF-8') . '...';
                                                } else {
                                                    echo $tv_name;
                                                }
                                                ?>
                                            </span>
                                        </h6>

                                        <?php
                                        $timestamp = strtotime($travel["time"]);
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
                                        <h6 class="m-1">ชื่อสถานที่ท่องเที่ยว</h6>
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