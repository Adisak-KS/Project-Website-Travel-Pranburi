 <!-- Topbar Start -->
 <div class="container-fluid bg-light pt-3 d-none d-lg-block">
     <div class="container">
         <div class="row">
             <div class="col-lg-6 text-center text-lg-left mb-2 mb-lg-0">
                 <div class="d-inline-flex align-items-center">
                     <!-- Email -->
                     <?php foreach ($contact as $result) { ?>
                         <?php if ($result["ct_id"] == 1 && !empty($result["ct_detail"])) { ?>
                             <p>
                                 <i class="fa-solid fa-envelope"></i>
                                 <span><?php echo $result["ct_detail"]; ?></span>
                             </p>
                         <?php } ?>
                     <?php } ?>

                     <!-- Separator -->
                     <?php
                        $hasEmail = false;
                        $hasPhone = false;
                        foreach ($contact as $result) {
                            if ($result["ct_id"] == 1 && !empty($result["ct_detail"])) {
                                $hasEmail = true;
                            }
                            if ($result["ct_id"] == 2 && !empty($result["ct_detail"])) {
                                $hasPhone = true;
                            }
                        }
                        if ($hasEmail && $hasPhone) { ?>
                         <p class="text-body px-3">|</p>
                     <?php } ?>

                     <!-- Phone Number -->
                     <?php foreach ($contact as $result) { ?>
                         <?php if ($result["ct_id"] == 2 && !empty($result["ct_detail"])) { ?>
                             <p>
                                 <i class="fa-solid fa-phone-flip"></i>
                                 <span><?php echo $result["ct_detail"]; ?></span>
                             </p>
                         <?php }  ?>
                     <?php } ?>
                 </div>
             </div>

             <div class="col-lg-6 text-center text-lg-right">
                 <div class="d-inline-flex align-items-center">
                     <!-- Social Media Links -->
                     <?php
                        $socialMediaIds = [3, 4, 5, 6, 7, 8];
                        $icons = ["fa-brands fa-facebook-f", "fa-brands fa-x-twitter", "fa-brands fa-linkedin-in", "fa-brands fa-instagram", "fa-brands fa-youtube", "fa-solid fa-globe"];
                        ?>
                     <?php foreach ($contact as $result) { ?>
                         <?php if (in_array($result["ct_id"], $socialMediaIds) && !empty($result["ct_detail"])) { ?>
                             <a class="text-primary px-3 mb-1" target="_blank" href="<?php echo $result["ct_detail"]; ?>">
                                 <i class="<?php echo $icons[$result["ct_id"] - 3]; ?>"></i>
                             </a>
                         <?php } ?>
                     <?php } ?>
                 </div>
             </div>
         </div>

     </div>
 </div>