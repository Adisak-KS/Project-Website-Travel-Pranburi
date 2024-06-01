 <!-- Footer Start -->
 <div class="container-fluid bg-dark text-white-50 py-5 px-sm-3 px-lg-5" style="margin-top: 90px;">
     <div class="row pt-5">
         <div class="col-lg-3 col-md-6 mb-5">
             <a href="" class="navbar-brand">
                 <h1 class="text-primary">
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
             <p>เว็บไซต์สำหรับแนะนำสถานที่ท่องเที่ยวและประชาสัมพันธ์ข่าวสารต่าง ๆ</p>
             <?php if (isset($contact["ct_id"]) && ($contact["ct_id"] != 1 && $contact["ct_id"] != 2 && $contact["ct_id"] != 9)) { ?>
                 <h6 class="text-white text-uppercase mt-4 mb-3">ติดตามเราได้ที่ :</h6>
                 <div class="d-flex justify-content-start">

                     <!-- Social Media Links -->
                     <?php
                        $socialMediaIds = [3, 4, 5, 6, 7, 8];
                        $icons = ["fa-brands fa-facebook-f", "fa-brands fa-x-twitter", "fa-brands fa-linkedin-in", "fa-brands fa-instagram", "fa-brands fa-youtube", "fa-solid fa-globe"];
                        ?>
                     <?php foreach ($contact as $result) { ?>
                         <?php if (in_array($result["ct_id"], $socialMediaIds) && !empty($result["ct_detail"])) { ?>
                             <a class="btn btn-outline-primary btn-square mr-2" target="_blank" href="<?php echo $result["ct_detail"]; ?>">
                                 <i class="<?php echo $icons[$result["ct_id"] - 3]; ?>"></i>
                             </a>
                         <?php } ?>
                     <?php } ?>

                 </div>
             <?php } ?>

         </div>
         <div class="col-lg-3 col-md-6 mb-5">
             <h5 class="text-white text-uppercase mb-4">บริการ</h5>
             <div class="d-flex flex-column justify-content-start">
                 <a class="text-white-50 mb-2" href="index.php"><i class="fa fa-angle-right mr-2"></i>หน้าแรก</a>
                 <a class="text-white-50 mb-2" href="news_show.php"><i class="fa fa-angle-right mr-2"></i>ข่าว / ประชาสัมพันธ์</a>
                 <a class="text-white-50 mb-2" href="travels_show.php"><i class="fa fa-angle-right mr-2"></i>สถานที่ท่องเที่ยว</a>
             </div>
         </div>
         <div class="col-lg-3 col-md-6 mb-5">
             <h5 class="text-white text-uppercase mb-4">บริการ</h5>
             <div class="d-flex flex-column justify-content-start">
                 <a class="text-white-50 mb-2" href="index.php"><i class="fa fa-angle-right mr-2"></i>หน้าแรก</a>
                 <a class="text-white-50 mb-2" href="news_show.php"><i class="fa fa-angle-right mr-2"></i>ข่าว / ประชาสัมพันธ์</a>
                 <a class="text-white-50 mb-2" href="travels_show.php"><i class="fa fa-angle-right mr-2"></i>สถานที่ท่องเที่ยว</a>
             </div>
         </div>
         <div class="col-lg-3 col-md-6 mb-5">
             <?php if ($contact) { ?>
                 <h5 class="text-white text-uppercase mb-4">ติดต่อเรา</h5>
                 <!-- Social Media Links -->
                 <?php
                    $socialMediaIds = [1, 2];
                    $icons = ["fa-solid fa-envelope", "fa-solid fa-phone-flip"];
                    ?>
                 <?php foreach ($contact as $result) { ?>
                     <?php if (in_array($result["ct_id"], $socialMediaIds) && !empty($result["ct_detail"])) { ?>
                         <p>
                             <i class="<?php echo $icons[$result["ct_id"] - 1]; ?>"></i>
                             <span><?php echo $result["ct_detail"]; ?></span>
                         </p>
                     <?php } ?>
                 <?php } ?>

                 <?php foreach ($contact as $result) { ?>
                     <?php if ($result["ct_id"] == 9 && !empty($result["ct_detail"])) { ?>
                         <p>
                             <i class="fa-solid fa-location-dot"></i>
                             <span><?php echo $result["ct_detail"]; ?></span>
                         </p>
                     <?php } ?>
                 <?php } ?>

                 <!-- <h6 class="text-white text-uppercase mt-4 mb-3" style="letter-spacing: 5px;">Newsletter</h6>
                 <div class="w-100">
                     <div class="input-group">
                         <input type="text" class="form-control border-light" style="padding: 25px;" placeholder="Your Email">
                         <div class="input-group-append">
                             <button class="btn btn-primary px-3">Sign Up</button>
                         </div>
                     </div>
                 </div> -->
             <?php } ?>
         </div>

     </div>
 </div>
 <div class="container-fluid bg-dark text-white border-top py-4 px-sm-3 px-md-5" style="border-color: rgba(256, 256, 256, .1) !important;">
     <div class="row">
         <div class="col-lg-6 text-center text-md-left mb-3 mb-md-0">
             <p class="m-0 text-white-50">Copyright &copy; <a href="#">Domain</a>. All Rights Reserved.</a>
             </p>
         </div>
         <div class="col-lg-6 text-center text-md-right">
             <p class="m-0 text-white-50">Designed by <a href="https://htmlcodex.com">HTML Codex</a>
             </p>
         </div>
     </div>
 </div>
 <!-- Footer End -->


 <!-- Back to Top -->
 <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>