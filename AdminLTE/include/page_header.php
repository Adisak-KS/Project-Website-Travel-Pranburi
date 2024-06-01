<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo $titlePage ?></h1>
            </div>
            <div class="col-sm-6">
                <?php if ($titlePage !== "หน้าหลัก") { ?>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active"><?php echo $titlePage ?></li>
                    </ol>
                <?php } ?>
            </div>
        </div>
    </div>
</div>