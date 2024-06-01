<?php
require_once("../db/connect.php");

if (isset($_POST["btn-edit"])) {
    $ctId = $_POST["ct_id"];
    $ctStatus = $_POST["ct_status"];

    // ดึงค่าจากคำขอ POST
    $ctEmail = isset($_POST["ct_email"]) ? $_POST["ct_email"] : '';
    $ctTel = isset($_POST["ct_tel"]) ? $_POST["ct_tel"] : '';
    $ctAddress = isset($_POST["ct_address"]) ? $_POST["ct_address"] : '';
    $ctUrl = isset($_POST["ct_url"]) ? $_POST["ct_url"] : '';


    $locationError = "Location: contact_show.php";
    $locationSuccess = "Location: contact_show.php";

    // Function to message error and redirect
    function messageError($message, $locationError)
    {
        $_SESSION["error"] = $message;
        header($locationError);
        exit;
    }

    // อีเมล
    if (!empty($ctEmail)) {
        $ctEmail = $_POST["ct_email"];

        // ตรวจสอบ Email
        if (!filter_var($ctEmail, FILTER_VALIDATE_EMAIL)) {
            messageError("รูปแบบอีเมลไม่ถูกต้อง", $locationError);
        } elseif (mb_strlen($ctEmail, 'UTF-8') < 10 || mb_strlen($ctEmail, 'UTF-8') > 70) {
            messageError("อีเมลต้องมี 10-70 ตัวอักษร", $locationError);
        }


        $sql = "UPDATE pbr_contact
                SET ct_detail = :ct_detail,
                    ct_status = :ct_status,
                    time = NOW()
                WHERE ct_id = :ct_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ct_detail", $ctEmail);
        $stmt->bindParam(":ct_status", $ctStatus);
        $stmt->bindParam(":ct_id", $ctId);
        $stmt->execute();
    }

    // เบอร์โทร
    if (!empty($ctTel)) {
        $ctTel = $_POST["ct_tel"];

        // ตรวจสอบ เบอร์โทรศัพท์
        if (!preg_match('/^(0|\+|\()[0-9()+\-\s]*$/', $ctTel)) {
            messageError("เบอร์โทรต้องเริ่มต้นด้วย 0, +, ( และสามารถใช้ตัวเลข, เครื่องหมายวงเล็บ, เครื่องหมายลบ และช่องว่างเท่านั้น", $locationError);
        } elseif (mb_strlen($ctTel, 'UTF-8') < 9 || mb_strlen($ctTel, 'UTF-8') > 50) {
            messageError("เบอร์โทรต้องมี 9-50 ตัว", $locationError);
        }


        $sql = "UPDATE pbr_contact
                SET ct_detail = :ct_detail,
                    ct_status = :ct_status,
                    time = NOW()
                WHERE ct_id = :ct_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ct_detail", $ctTel);
        $stmt->bindParam(":ct_status", $ctStatus);
        $stmt->bindParam(":ct_id", $ctId);
        $stmt->execute();
    }

    // ที่อยู่
    if (!empty($ctAddress)) {
        $ctTel = $_POST["ct_tel"];

        // ตรวจสอบ ที่อยู่

        if (mb_strlen($ctAddress, 'UTF-8') > 255) {
            messageError("ที่อยู่ติดต่อ ต้องไม่เกิน 255 ตัวอักษร", $locationError);
        }


        $sql = "UPDATE pbr_contact
                SET ct_detail = :ct_detail,
                    ct_status = :ct_status,
                    time = NOW()
                WHERE ct_id = :ct_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ct_detail", $ctAddress);
        $stmt->bindParam(":ct_status", $ctStatus);
        $stmt->bindParam(":ct_id", $ctId);
        $stmt->execute();
    }

    // url
    if (!empty($ctUrl)) {
        $ctUrl = $_POST["ct_url"];

        // ตรวจสอบ URL
        if (!filter_var($ctUrl, FILTER_VALIDATE_URL)) {
            messageError("URL ไม่ถูกต้อง", $locationError);
        } elseif (mb_strlen($ctUrl, 'UTF-8') > 255) {
            messageError("URL ต้องไม่เกิน 255 ตัวอักษร", $locationError);
        }



        $sql = "UPDATE pbr_contact
                SET ct_detail = :ct_detail,
                    ct_status = :ct_status,
                    time = NOW()
                WHERE ct_id = :ct_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ct_detail", $ctUrl);
        $stmt->bindParam(":ct_status", $ctStatus);
        $stmt->bindParam(":ct_id", $ctId);
        $stmt->execute();
    }

    $_SESSION["success"] = "แก้ไขข้อมูลติดต่อสำเร็จ";
    header($locationSuccess);
} else {
    header("Location: contact_show.php");
    exit;
}
