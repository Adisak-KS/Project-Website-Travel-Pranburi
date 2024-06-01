<?php
// เริ่ม session
session_start();

// ลบ session ทั้งหมด
session_unset();

// ทำลาย session
session_destroy();

// ส่งผู้ใช้กลับไปยังหน้า index
header("Location: login_form.php");
exit;
