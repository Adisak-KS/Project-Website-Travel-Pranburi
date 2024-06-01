<?php
require_once("db/connect.php");
$titlePage = "รายละเอียดสถานที่ท่องเที่ยว";


// แสดงข้อมูล travel ตาม tv_id
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

    $tvId = $originalId;

    try {

        $sql = "SELECT pbr_travel.*, 
                        pbr_travel_type.tvt_name, 
                        SUM(pbr_travel_views.tvv_view) AS total_views
                FROM pbr_travel
                LEFT JOIN pbr_travel_type ON pbr_travel.tvt_id = pbr_travel_type.tvt_id
                INNER JOIN pbr_travel_views ON pbr_travel.tv_id = pbr_travel_views.tv_id
                WHERE pbr_travel.tv_id = :tv_id
                GROUP BY pbr_travel.tv_id, pbr_travel_type.tvt_name";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tv_id", $tvId);
        $stmt->execute();
        $detail = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($detail) {

            $tvId = $detail["tv_id"];
            $tvtId = $detail["tvt_id"];
            $tvvView = 1;

            // ให้นับจำนวนการเข้าชม
            $sql = "INSERT INTO pbr_travel_views (tv_id, tvt_id, tvv_view)
                    VALUES (:tv_id, :tvt_id, :tvv_view)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":tv_id", $tvId);
            $stmt->bindParam(":tvt_id", $tvtId);
            $stmt->bindParam(":tvv_view", $tvvView);
            $stmt->execute();
        } else {
            header("Location: index.php");
            exit();
        }

        // ประเภทสถานที่ท่องเที่ยว และจำนวนสถานที่ท่องเที่ยว
        $sql = "SELECT  pbr_travel_type.tvt_id, 
                        pbr_travel_type.tvt_name, 
                        COUNT(pbr_travel.tvt_id) AS travel_count
                FROM pbr_travel_type
                LEFT JOIN pbr_travel ON pbr_travel_type.tvt_id = pbr_travel.tvt_id AND pbr_travel.tv_status = 1
                WHERE pbr_travel_type.tvt_status = 1
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
                AND pbr_travel.tv_id != :tv_id
                GROUP BY pbr_travel.tv_id
                ORDER BY RAND()
                LIMIT 3";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":tv_id", $tvId);
        $stmt->execute();
        $travelLatest = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <h3 class="display-4 text-white text-uppercase"><?php echo $detail["tv_name"]; ?></h3>
                <p class="text-white">รายละเอียดสถานที่ท่องเที่ยว</p>
                <div class="d-inline-flex text-white">
                    <p class="m-0 text-uppercase"><a class="text-white" href="index.php">หน้าหลัก</a></p>
                    <i class="fa fa-angle-double-right pt-1 px-3"></i>
                    <p class="m-0 text-uppercase">รายละเอียดสถานที่ท่องเที่ยว</p>
                </div>
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
                                        <?php if (empty($search)) { ?>
                                            <input type="text" name="search" class="form-control px-4" style="height: 47px;" placeholder="ระบุ คำค้นหา ที่ต้องการ">
                                        <?php } else { ?>
                                            <input type="text" name="search" value="<?php echo $search ?>" class="form-control px-4" style="height: 47px;" placeholder="ระบุ คำค้นหา ที่ต้องการ">
                                        <?php } ?>
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
                                <img class="img-fluid w-100 bg-light" style="height: 500px;" src="uploads/img_travel/<?php echo $detail["tv_cover"] ?>" alt="">
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
                                $originalId = $detail["tvt_id"];
                                require_once("include/salt.php");   // รหัส Salte 
                                $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                ?>
                                <a href="travels_show.php?id=<?php echo $base64Encoded; ?>" class="text-primary text-uppercase text-decoration-none"><span class="text-dark">ประเภทสถานที่ท่องเที่ยว :</span>

                                    <?php
                                    $tvt_name = $detail["tvt_name"];
                                    if (mb_strlen($tvt_name, 'UTF-8') > 30) {
                                        echo mb_substr($tvt_name, 0, 30, 'UTF-8') . '...';
                                    } else {
                                        echo $tvt_name;
                                    }
                                    ?>
                                </a>
                            </div>

                            <h2 class="mb-3 mt-3 text-center text-primary"><?php echo $detail["tv_name"]; ?></h2>
                            <p><?php echo $detail["tv_detail"]; ?></p>

                            <!-- Video Youtube  -->
                            <?php if (!empty($detail["tv_video"])) { ?>
                                <hr>
                                <h4 class="text-primary mt-3 mb-2">บรรยาการสถานที่ท่องเที่ยว</h4>
                                <div class="text-center pt-3 embed-responsive embed-responsive-16by9">
                                    <!-- Show Video Youtube  -->
                                    <?php echo  $detail["tv_video"]; ?>
                                </div>
                            <?php } ?>

                            <!-- Google Map  -->
                            <?php if (!empty($detail["tv_location"])) { ?>
                                <hr>
                                <h4 class="text-primary mt-3 mb-2">ตำแหน่งสถานที่ท่องเที่ยว</h4>
                                <div class="text-center pt-3 embed-responsive embed-responsive-16by9">
                                    <!-- Show Google Map  -->
                                    <?php echo  $detail["tv_location"]; ?>
                                </div>
                            <?php } ?>

                            <!-- Go Back  -->
                            <hr>
                            <div class="mt-5 d-flex justify-content-between">
                                <a href="index.php" class="btn  btn-primary">
                                    <i class="fa-solid fa-arrow-left"></i>
                                    <span>กลับหน้าหลัก</span>
                                </a>
                                <a href="travels_show.php" class="btn  btn-primary">
                                    <span>สถานที่ท่องเที่ยวทั้งหมด</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                    <!-- Blog Detail End -->

                    <!-- Comment List Start -->
                    <!-- <div class="bg-white" style="padding: 30px; margin-bottom: 30px;">
                        <h4 class="text-uppercase mb-4" style="letter-spacing: 5px;">3 Comments</h4>
                        <div class="media mb-4">
                            <img src="img/user.jpg" alt="Image" class="img-fluid mr-3 mt-1" style="width: 45px;">
                            <div class="media-body">
                                <h6><a href="">John Doe</a> <small><i>01 Jan 2045</i></small></h6>
                                <p>Diam amet duo labore stet elitr invidunt ea clita ipsum voluptua, tempor labore
                                    accusam ipsum et no at. Kasd diam tempor rebum magna dolores sed sed eirmod ipsum.
                                    Gubergren clita aliquyam consetetur sadipscing, at tempor amet ipsum diam tempor
                                    consetetur at sit.</p>
                                <button class="btn btn-sm btn-outline-primary">Reply</button>
                            </div>
                        </div>
                        <div class="media">
                            <img src="img/user.jpg" alt="Image" class="img-fluid mr-3 mt-1" style="width: 45px;">
                            <div class="media-body">
                                <h6><a href="">John Doe</a> <small><i>01 Jan 2045</i></small></h6>
                                <p>Diam amet duo labore stet elitr invidunt ea clita ipsum voluptua, tempor labore
                                    accusam ipsum et no at. Kasd diam tempor rebum magna dolores sed sed eirmod ipsum.
                                    Gubergren clita aliquyam consetetur sadipscing, at tempor amet ipsum diam tempor
                                    consetetur at sit.</p>
                                <button class="btn btn-sm btn-outline-primary">Reply</button>
                                <div class="media mt-4">
                                    <img src="img/user.jpg" alt="Image" class="img-fluid mr-3 mt-1" style="width: 45px;">
                                    <div class="media-body">
                                        <h6><a href="">John Doe</a> <small><i>01 Jan 2045</i></small></h6>
                                        <p>Diam amet duo labore stet elitr invidunt ea clita ipsum voluptua, tempor
                                            labore accusam ipsum et no at. Kasd diam tempor rebum magna dolores sed sed
                                            eirmod ipsum. Gubergren clita aliquyam consetetur sadipscing, at tempor amet
                                            ipsum diam tempor consetetur at sit.</p>
                                        <button class="btn btn-sm btn-outline-primary">Reply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- Comment List End -->

                    <!-- Comment Form Start -->
                    <!-- <div class="bg-white mb-3" style="padding: 30px;">
                        <h4 class="text-uppercase mb-4" style="letter-spacing: 5px;">Leave a comment</h4>
                        <form>
                            <div class="form-group">
                                <label for="name">Name *</label>
                                <input type="text" class="form-control" id="name">
                            </div>
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" class="form-control" id="email">
                            </div>
                            <div class="form-group">
                                <label for="website">Website</label>
                                <input type="url" class="form-control" id="website">
                            </div>

                            <div class="form-group">
                                <label for="message">Message *</label>
                                <textarea id="message" cols="30" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="form-group mb-0">
                                <input type="submit" value="Leave a comment" class="btn btn-primary font-weight-semi-bold py-2 px-3">
                            </div>
                        </form>
                    </div> -->
                    <!-- Comment Form End -->
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
                        <h4 class="text-uppercase mb-4">ประเภทสถานที่ท่องเที่ยว</h4>
                        <div class="bg-white" style="padding: 30px;">
                            <ul class="list-inline m-0">
                                <?php foreach ($travelTypes as $type) { ?>
                                    <li class="mb-3 d-flex justify-content-between align-items-center">
                                        <?php
                                        $originalId = $type["tvt_id"];
                                        require_once("include/salt.php");   // รหัส Salte 
                                        $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                                        $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                                        ?>

                                        <a class="text-dark"  href="travels_show.php?id=<?php echo $base64Encoded ?>">
                                            <i class="fa fa-angle-right text-primary mr-2"></i>
                                            <span>
                                                <?php
                                                $tvt_name = $type["tvt_name"];
                                                if (mb_strlen($tvt_name, 'UTF-8') > 20) {
                                                    echo mb_substr($tvt_name, 0, 20, 'UTF-8') . '...';
                                                } else {
                                                    echo $tvt_name;
                                                }
                                                ?>
                                            </span>
                                        </a>
                                        <span class="badge badge-primary badge-pill"><?php echo $type["travel_count"] ?></span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Recent Post -->
                    <div class="mb-5">
                        <h4 class="text-uppercase mb-4">สถานที่ท่องเที่ยวแนะนำ</h4>
                        <?php foreach ($travelLatest as $travel) { ?>
                            <?php
                            $originalId = $travel["tv_id"];
                            require_once("include/salt.php");   // รหัส Salte 
                            $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
                            $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
                            ?>

                            <a href="travel_detail.php?id=<?php echo $base64Encoded; ?>" class="d-flex align-items-center text-decoration-none bg-white mb-3">
                                <img class="img-fluid" style=" width:100px; height: 100px;" src="uploads/img_travel/<?php echo $travel["tv_cover"] ?>" alt="">
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