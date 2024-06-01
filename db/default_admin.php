<?php

require_once("connect.php");

try {


    $folderUploads = '../uploads/profile_admin/'; // Folder to store files
    $fileDefault = 'default.png'; // Default image file
    $filePath = $folderUploads . $fileDefault;
    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    $allowedExtensions = ['png', 'jpg', 'jpeg'];
    $maxFileSize = 2 * 1024 * 1024; // Max file size (2 MB)

    function generateUniqueAdminDefault($extension, $folder)
    {
        do {
            $fileName = 'profile_' . uniqid() . bin2hex(random_bytes(10)) . time() . '.' . $extension;
        } while (file_exists($folder . $fileName));
        return $fileName;
    }


    // check Super Admin (Default)
    $sql = "SELECT adm_id
            FROM pbr_admin
            WHERE adm_id = 1
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $check =  $stmt->fetch();

    if (!$check) {

        // Super Admin (Default)
        $admId = 1;
        $admFname = "ผู้ดูแลระบบ";
        $admLname = "สูงสุด";
        $admUsername = "superAdmin";
        $hashAdmPassword = password_hash("superAdmin1", PASSWORD_DEFAULT);
        $admEmail = "superAdmin1@gmail.com";
        $admStatus = 1;

        $newProfile = generateUniqueAdminDefault($fileExtension, $folderUploads);
        $targetFilePath = $folderUploads . $newProfile;

        // Copy default image to new file
        if (copy($filePath, $targetFilePath)) {

            // Inser Super Admin (Default)
            $sql = "INSERT pbr_admin(adm_id, adm_profile, adm_fname, adm_lname, adm_username, adm_password, adm_email, adm_status)
                    VALUES (:adm_id, :adm_profile, :adm_fname, :adm_lname, :adm_username, :adm_password, :adm_email, :adm_status)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":adm_id", $admId);
            $stmt->bindParam(":adm_profile", $newProfile);
            $stmt->bindParam(":adm_fname", $admFname);
            $stmt->bindParam(":adm_lname", $admLname);
            $stmt->bindParam(":adm_username", $admUsername);
            $stmt->bindParam(":adm_password", $hashAdmPassword);
            $stmt->bindParam(":adm_email", $admEmail);
            $stmt->bindParam(":adm_status", $admStatus);
            $stmt->execute();
        }
    }


    // check Admin (Default)
    // นับจำนวนแถวในตาราง pbr_admin โดยไม่นับแถวที่มี adm_id = 1
    $sql = "SELECT COUNT(*) AS total_admin
            FROM pbr_admin
            WHERE adm_id != 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalAdmin = $result['total_admin'];


    // ไม่มี Admin ใน Database (ไม่รวม Id 1 Super Admin)
    if (!$totalAdmin) {
        // Admin (Default)
        $admId = 2;
        $admFname = "Admin";
        $admLname = "One";
        $admUsername = "Admin111";
        $hashAdmPassword = password_hash("Admin111", PASSWORD_DEFAULT);
        $admEmail = "admin111@gmail.com";
        $admStatus = 1;

        $newProfile = generateUniqueAdminDefault($fileExtension, $folderUploads);
        $targetFilePath = $folderUploads . $newProfile;
        // Copy default image to new file
        if (copy($filePath, $targetFilePath)) {

            // Inser Super Admin (Default)
            $sql = "INSERT pbr_admin(adm_id, adm_profile, adm_fname, adm_lname, adm_username, adm_password, adm_email, adm_status)
            VALUES (:adm_id, :adm_profile, :adm_fname, :adm_lname, :adm_username, :adm_password, :adm_email, :adm_status)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":adm_id", $admId);
            $stmt->bindParam(":adm_profile", $newProfile);
            $stmt->bindParam(":adm_fname", $admFname);
            $stmt->bindParam(":adm_lname", $admLname);
            $stmt->bindParam(":adm_username", $admUsername);
            $stmt->bindParam(":adm_password", $hashAdmPassword);
            $stmt->bindParam(":adm_email", $admEmail);
            $stmt->bindParam(":adm_status", $admStatus);
            $stmt->execute();
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
