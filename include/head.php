<?php
require_once("db/connect.php");

try {
    // ข้อมูลการตั้งค่า
    $sql = "SELECT * 
            FROM pbr_setting";
    $stmt = $conn->query($sql);
    $stmt->execute();
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // ข้อมูลติดต่อ
    $sql = "SELECT *
            FROM pbr_contact
            WHERE ct_status = 1 AND ct_detail IS NOT NULL AND ct_detail != ''";
    $stmt = $conn->query($sql);
    $stmt->execute();
    $contact = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>

<meta charset="utf-8">

<!-- Website Name  -->
<?php
foreach ($settings as $setting) {
    // ตรวจสอบว่า $setting["st_id"] เท่ากับ 1 หรือไม่
    if ($setting["st_id"] == 1) {
        // ถ้า $setting["st_id"] เท่ากับ 1 ตรวจสอบค่าว่างเปล่าของ $setting["st_detail"]
        $webName = !empty($setting["st_detail"]) ? $setting["st_detail"] : "ชื่อเว็บไซต์";
        // กำหนดค่าให้กับ <title>
        echo "<title>$titlePage | $webName</title>";
        // เมื่อพบ $setting["st_id"] เท่ากับ 1 ให้หยุดการทำงานของลูป foreach
        break;
    }
}
?>

<!-- Favicon -->
<?php
$foundFavicon = false; // สร้างตัวแปรเพื่อเก็บสถานะว่ามีการตรงเงื่อนไขหรือไม่
foreach ($settings as $setting) {
    if ($setting["st_id"] == 2 && !empty($setting["st_detail"])) {
        echo '<link rel="shortcut icon" href="uploads/img_web_setting/' . $setting["st_detail"] . '" type="image/x-icon">';
        $foundFavicon = true; // ตั้งค่าตัวแปรเป็น true เมื่อพบรูปภาพที่ตรงเงื่อนไข
        break; // หยุดลูปเมื่อพบรูปภาพที่ตรงเงื่อนไข
    }
}
// ถ้าไม่มีการตรงเงื่อนไข
if (!$foundFavicon) {
    echo '<link rel="shortcut icon" href="uploads/img_web_setting/default_favicon" type="image/x-icon">';
}
?>


<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta content="Free HTML Templates" name="keywords">
<meta content="Free HTML Templates" name="description">


<!-- Google Font: Kanit -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Libraries Stylesheet -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

<!-- Customized Bootstrap Stylesheet -->
<link href="css/style.css" rel="stylesheet">