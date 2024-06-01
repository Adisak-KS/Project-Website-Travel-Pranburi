<?php
require_once("../db/connect.php");
$titlePage = "จัดการข้อมูลส่วนตัว";

try {
  $admId = $_SESSION["adm_id"];

  $sql = "SELECT adm_id
          FROM pbr_admin
          WHERE adm_id = :adm_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":adm_id", $admId);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$result) {
    header("Location: index.php");
    exit;
  }
} catch (PDOException $e) {
  echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require_once("include/head.php") ?>
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">

    <!-- Navbar -->
    <?php require_once("include/navbar.php") ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php require_once("include/aside.php") ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <?php require_once("include/page_header.php") ?>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <?php
          $originalId = $result["adm_id"];
          require_once("include/salt.php");   // รหัส Salte 
          $saltedId = $salt1 . $originalId . $salt2; // นำ salt มารวมกับ id เพื่อความปลอดภัย
          $base64Encoded = base64_encode($saltedId); // เข้ารหัสข้อมูลโดยใช้ Base64
          ?>

          <div class="row">
            <div class="col-md-3 col-sm-6 col-12 mx-4">
              <a href="acc_profile_edit_form.php?id=<?php echo $base64Encoded ?>">
                <div class="info-box shadow-lg">
                  <span class="info-box-icon bg-primary">
                    <i class="fa-solid fa-user-gear"></i>
                  </span>
                  <div class="info-box-content">
                    <span class="info-box-number text-black">จัดการข้อมูลส่วนตัว</span>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-md-3 col-sm-6 col-12 mx-4">
              <a href="acc_account_edit_form.php?id=<?php echo $base64Encoded ?>">
                <div class="info-box shadow-lg">
                  <span class="info-box-icon bg-info"><i class="far fa-envelope"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-number text-black">จัดการข้อมูลบัญชี</span>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-md-3 col-sm-6 col-12 mx-4">
              <a href="acc_password_edit_form.php?id=<?php echo $base64Encoded ?>">
                <div class="info-box shadow-lg">
                  <span class="info-box-icon bg-warning">
                    <i class="fa-solid fa-key text-white"></i>
                  </span>
                  <div class="info-box-content">
                    <span class="info-box-number text-black">จัดการรหัสผ่าน</span>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <!-- /.row -->
          <!-- Main row -->
          <div class="row">
            <!-- Left col -->
            <section class="col-lg-7 connectedSortable">
            </section>
            <!-- right col -->
          </div>
          <!-- /.row (main row) -->
        </div><!-- /.container-fluid -->
      </section>

      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <?php require_once("include/footer.php") ?>

  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <?php require_once("include/script.php") ?>
</body>

</html>
<?php require_once("../include/sweetalert2.php") ?>