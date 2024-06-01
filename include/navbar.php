<div class="container-fluid position-relative nav-bar p-0">
    <div class="container-lg position-relative p-0 px-lg-3" style="z-index: 9;">
        <nav class="navbar navbar-expand-lg bg-light navbar-light shadow-lg py-3 py-lg-0 pl-3 pl-lg-5">
            <a href="index.php" class="navbar-brand">
                <h1 class="m-0 text-primary">
                    <!-- Web name  -->
                    <?php
                    foreach ($settings as $setting) {
                        // ตรวจสอบว่า $setting["st_id"] เท่ากับ 1 หรือไม่
                        if ($setting["st_id"] == 1) {
                            // ถ้า $setting["st_id"] เท่ากับ 1 ตรวจสอบค่าว่างของ $setting["st_detail"]
                            $webName = !empty($setting["st_detail"]) ? $setting["st_detail"] : "ชื่อเว็บไซต์";
                            // กำหนดค่าให้กับ web name
                            echo $webName;
                            // เมื่อพบ $setting["st_id"] เท่ากับ 1 ให้หยุดการทำงานของลูป foreach
                            break;
                        }
                    }
                    ?>
                </h1>
            </a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                <div class="navbar-nav ml-auto py-0">
                    <a href="index.php" class="nav-item nav-link active">หน้าหลัก</a>
                    <a href="news_show.php" class="nav-item nav-link">ข่าว/ประชาสัมพันธ์</a>
                    <a href="travels_show.php" class="nav-item nav-link">สถานที่ท่องเที่ยวทั้งหมด</a>
                    <!-- <a href="package.html" class="nav-item nav-link">ติดต่อเรา</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu border-0 rounded-0 m-0">
                            <a href="blog.html" class="dropdown-item">Blog Grid</a>
                            <a href="single.html" class="dropdown-item">Blog Detail</a>
                            <a href="destination.html" class="dropdown-item">Destination</a>
                            <a href="guide.html" class="dropdown-item">Travel Guides</a>
                            <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                        </div>
                    </div>
                    <a href="contact.html" class="nav-item nav-link">Contact</a> -->
                </div>
            </div>
        </nav>
    </div>
</div>