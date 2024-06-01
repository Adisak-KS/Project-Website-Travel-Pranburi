<?php
require_once("../db/connect.php");

try {
    $sql = "SELECT * 
            FROM pbr_setting";
    $stmt = $conn->query($sql);
    $stmt->execute();
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

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
        echo '<link rel="shortcut icon" href="../uploads/img_web_setting/' . $setting["st_detail"] . '" type="image/x-icon">';
        $foundFavicon = true; // ตั้งค่าตัวแปรเป็น true เมื่อพบรูปภาพที่ตรงเงื่อนไข
        break; // หยุดลูปเมื่อพบรูปภาพที่ตรงเงื่อนไข
    }
}
// ถ้าไม่มีการตรงเงื่อนไข
if (!$foundFavicon) {
    echo '<link rel="shortcut icon" href="../uploads/img_web_setting/default_favicon" type="image/x-icon">';
}
?>


<!-- Google Font: Kanit -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<!-- Font Awesome Icons V.6.5.2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Theme style -->
<link rel="stylesheet" href="dist/css/adminlte.min.css">

<!-- Data Table  -->
<link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/searchpanes/2.3.1/css/searchPanes.bootstrap5.min.css" rel="stylesheet">

<!-- Data Table Export -->
<link href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css" rel="stylesheet">
 
