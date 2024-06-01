<?php
require_once("../db/connect.php");

$titlePage = "หน้าหลัก";

try {
  $sql = "SELECT SUM(tvv_view) AS travel_views
          FROM pbr_travel_views";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $travelViews = $result['travel_views'];


  $sql = "SELECT SUM(nsv_view) AS news_views
          FROM pbr_news_views";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $newsViews = $result['news_views'];

  
} catch (PDOException $e) {
  echo $e->getMessage();
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require_once("include/head.php") ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?php echo number_format($travelViews) ?></h3>

                  <p>ยอดเข้าชมสถานที่ท่องเที่ยว</p>
                </div>
                <div class="icon">
                <i class="fa-solid fa-eye"></i>
                </div>
                <a href="chart_travel_views.php" class="small-box-footer">ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?php echo number_format($newsViews) ?></h3>

                  <p>ยอดเข้าชมข่าว</p>
                </div>
                <div class="icon">
                <i class="fa-solid fa-eye"></i>
                </div>
                <a href="chart_news_views.php" class="small-box-footer">ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-warning">
                <div class="inner">
                <h3><?php echo number_format($travelViews) ?></h3>

                  <p>รายงานยอดเข้าชมสถานที่ท่องเที่ยว</p>
                </div>
                <div class="icon">
                  <i class="fa-solid fa-receipt"></i>
                </div>
                <a href="report_travel_views.php" class="small-box-footer">ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                <div class="inner">
                <h3><?php echo number_format($newsViews) ?></h3>

                  <p>รายงานยอดเข้าชมข่าว</p>
                </div>
                <div class="icon">
                  <i class="fa-solid fa-receipt"></i>
                </div>
                <a href="report_news_views.php" class="small-box-footer">ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
          <!-- /.row -->

        </div><!-- /.container-fluid -->
      </div>
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