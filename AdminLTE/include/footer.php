<footer class="main-footer">
    <!-- Default to the left -->
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.

    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
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
    </div>
</footer>