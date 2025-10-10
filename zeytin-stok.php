<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <title>Zeytin Yönetim Programı - Zeytin Stok</title>
    <?php include 'layouts/head.php'; ?>
    <?php include 'layouts/head-style.php'; ?>
    
    <style>
        .stok-low { background-color: #ffebee; }
        .stok-medium { background-color: #fff3e0; }
        .stok-high { background-color: #e8f5e8; }
    </style>
</head>

<?php include 'layouts/body.php'; ?>

<div id="layout-wrapper">
    <?php include 'layouts/menu.php'; ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <!-- start page title -->
                <?php
                $maintitle = "Zeytin Yönetimi";
                $title = "Zeytin Stok Durumu";
                ?>
                <?php include 'layouts/breadcrumb.php'; ?>
                <!-- end page title -->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">Zeytin Stok Durumu</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Tür</th>
                                                <th>Tip</th>
                                                <th>Miktar</th>
                                                <th>Birim</th>
                                                <th>Son Güncelleme</th>
                                                <th>Durum</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sorgu = $db->prepare("SELECT s.*, zt.turAdi, ztp.tipAdi, ztp.birim 
                                                                 FROM tbl_zeytin_stok s
                                                                 LEFT JOIN tbl_zeytin_tipleri ztp ON s.tipId = ztp.tipId
                                                                 LEFT JOIN tbl_zeytin_turleri zt ON ztp.turId = zt.turId
                                                                 ORDER BY s.stokId DESC");
                                            $sorgu->execute();
                                            $say = 1;
                                            while($stok = $sorgu->fetch(PDO::FETCH_ASSOC)) {
                                                // Stok durumuna göre CSS sınıfı belirle
                                                $stokClass = '';
                                                if($stok['miktar'] == 0) {
                                                    $stokClass = 'stok-low';
                                                    $durum = '<span class="badge bg-danger">Stok Yok</span>';
                                                } else if($stok['miktar'] < 100) {
                                                    $stokClass = 'stok-medium';
                                                    $durum = '<span class="badge bg-warning">Az Stok</span>';
                                                } else {
                                                    $stokClass = 'stok-high';
                                                    $durum = '<span class="badge bg-success">Yeterli Stok</span>';
                                                }
                                            ?>
                                            <tr class="<?php echo $stokClass; ?>">
                                                <td><?php echo $say++; ?></td>
                                                <td><?php echo $stok['turAdi']; ?></td>
                                                <td><?php echo $stok['tipAdi']; ?></td>
                                                <td><?php echo number_format($stok['miktar'], 2); ?></td>
                                                <td><?php echo $stok['birim']; ?></td>
                                                <td><?php echo date('d.m.Y H:i', strtotime($stok['sonGuncelleme'])); ?></td>
                                                <td><?php echo $durum; ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php include 'layouts/footer.php'; ?>
    </div>
</div>

<?php include 'layouts/vendor-scripts.php'; ?>

</body>
</html>