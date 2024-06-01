<!-- ค่าสุ่มที่ถูกเพิ่มเข้าไปในรหัสผ่านก่อนที่จะทำการเข้ารหัส -->
<?php
$salt1 = "89896100416646846994";
$salt2 = "13587423569568375388";

$_SESSION["salt1"] = $salt1;
$_SESSION["salt2"] = $salt2;


// // เพิ่มเกลียวกับ ID เพื่อความปลอดภัย
// $saltedId = $salt1 .   $originalId . $salt2;
// // เข้ารหัสข้อมูลโดยใช้ Base64
// $base64Encoded = base64_encode($saltedId);
