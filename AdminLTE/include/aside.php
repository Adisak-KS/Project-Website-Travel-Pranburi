<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link">

        <!-- Logo  -->
        <?php
        $foundLogo = false; // สร้างตัวแปรเพื่อเก็บสถานะว่ามีการตรงเงื่อนไขหรือไม่
        foreach ($settings as $setting) {
            if ($setting["st_id"] == 3 && !empty($setting["st_detail"])) {
                echo '<img src="../uploads/img_web_setting/' . $setting["st_detail"] . '" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">';
                $foundLogo = true; // ตั้งค่าตัวแปรเป็น true เมื่อพบรูปภาพที่ตรงเงื่อนไข
                break; // หยุดลูปเมื่อพบรูปภาพที่ตรงเงื่อนไข
            }
        }
        // ถ้าไม่มีการตรงเงื่อนไข
        if (!$foundLogo) {
            echo '<img src="../uploads/img_web_setting/default_logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">';
        }
        ?>


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
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <!-- Profile Admin  -->
                <?php if (empty($use["adm_profile"])) { ?>
                    <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                <?php } else { ?>
                    <img src="../uploads/profile_admin/<?php echo $use["adm_profile"] ?>" class="img-circle elevation-2" alt="User Image">
                <?php } ?>
            </div>
            <div class="info">
                <!-- Username Admin  -->
                <?php if (empty($use["adm_username"])) { ?>
                    <a href="#" class="d-block">Alexander Pierce</a>
                <?php } else { ?>
                    <a href="#" class="d-block">@<?php echo $use["adm_username"]; ?></a>
                <?php } ?>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="ค้นหาเมนู" aria-label="ค้นหาเมนู">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <h6 class="text-white pt-2">ระบบ</h6>
                <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-home"></i>
                        <p>หน้าหลัก</p>
                    </a>
                </li>
                <h6 class="text-white pt-2">บุคล</h6>
                <li class="nav-item">
                    <a href="admin_show.php" class="nav-link">
                        <i class="fas fa-user-shield"></i>
                        <p>ผู้ดูแลระบบ</p>
                    </a>
                </li>


                <h6 class="text-white pt-2">สถานที่ท่องเที่ยว</h6>
                <li class="nav-item">
                    <a href="travel_type_show.php" class="nav-link">
                        <i class="fas fa-atlas"></i>
                        <p>ประเภทสถานที่ท่องเที่ยว</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="travel_show.php" class="nav-link">
                        <i class="fas fa-map-marked"></i>
                        <p>สถานที่ท่องเที่ยว</p>
                    </a>
                </li>


                <h6 class="text-white pt-2">ประชาสัมพันธ์</h6>
                <li class="nav-item">
                    <a href="news_type_show.php" class="nav-link">
                        <i class="fas fa-atlas"></i>
                        <p>ประเภทข่าว</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="news_show.php" class="nav-link">
                        <i class="fas fa-newspaper"></i>
                        <p>ข่าว</p>
                    </a>
                </li>


                <h6 class="text-white pt-2">อื่น ๆ</h6>
                <li class="nav-item">
                    <a href="contact_show.php" class="nav-link">
                        <i class="fas fa-phone-alt"></i>
                        <p>ข้อมูลติดต่อ</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="setting_show.php" class="nav-link">
                        <i class="fas fa-cogs"></i>
                        <p>ตั้งค่าเว็บไซต์</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt" style="transform: rotate(180deg);"></i>
                        <p>ออกจากระบบ</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>