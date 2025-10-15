<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <title>Zeytin Yönetim Programı - Zeytin Stok</title>
    <?php include 'layouts/head.php'; ?>
    <?php include 'layouts/head-style.php'; ?>
    
    <style>
        .stok-critical { background-color: #ffebee; }      /* 0 stok */
        .stok-low { background-color: #fff3e0; }          /* 0-50 kg */
        .stok-medium { background-color: #e3f2fd; }       /* 50-200 kg */
        .stok-high { background-color: #e8f5e8; }         /* 200+ kg */
        .stok-negative { background-color: #fce4ec; border-left: 4px solid #e91e63; } /* Negatif stok */
        .progress { height: 8px; margin-top: 5px; }
        .stok-badge { font-size: 11px; }
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

                <!-- Stok Özet Kartları -->
                <div class="row mb-4">
                    <?php
                    // Toplam stok istatistikleri
                    $toplamStokSorgu = $db->prepare("
                        SELECT 
                            COUNT(*) as toplam_urun,
                            SUM(CASE WHEN miktar <= 0 THEN 1 ELSE 0 END) as kritik_urun,
                            SUM(CASE WHEN miktar > 0 AND miktar <= 50 THEN 1 ELSE 0 END) as az_stok_urun,
                            SUM(CASE WHEN miktar > 50 AND miktar <= 200 THEN 1 ELSE 0 END) as orta_stok_urun,
                            SUM(CASE WHEN miktar > 200 THEN 1 ELSE 0 END) as yuksek_stok_urun,
                            SUM(miktar) as toplam_miktar,
                            SUM(CASE WHEN miktar < 0 THEN miktar ELSE 0 END) as negatif_miktar
                        FROM tbl_zeytin_stok
                    ");
                    $toplamStokSorgu->execute();
                    $stokIstatistik = $toplamStokSorgu->fetch(PDO::FETCH_ASSOC);
                    ?>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card card-h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Toplam Stok</span>
                                        <h4 class="mb-3 text-primary">
                                            <?php echo number_format($stokIstatistik['toplam_miktar'], 2); ?> 
                                            <small class="text-muted">kg</small>
                                        </h4>
                                        <div class="text-nowrap">
                                            <span class="badge bg-soft-primary text-primary">
                                                <?php echo $stokIstatistik['toplam_urun']; ?> ürün
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-soft-primary rounded">
                                                <i class="bx bx-package font-size-20 text-primary"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Kritik Stok</span>
                                        <h4 class="mb-3 text-danger">
                                            <?php echo $stokIstatistik['kritik_urun']; ?> 
                                            <small class="text-muted">ürün</small>
                                        </h4>
                                        <div class="text-nowrap">
                                            <span class="badge bg-soft-danger text-danger">
                                                <?php echo number_format($stokIstatistik['negatif_miktar'], 2); ?> kg
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-soft-danger rounded">
                                                <i class="bx bx-error font-size-20 text-danger"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Az Stok</span>
                                        <h4 class="mb-3 text-warning">
                                            <?php echo $stokIstatistik['az_stok_urun']; ?> 
                                            <small class="text-muted">ürün</small>
                                        </h4>
                                        <div class="text-nowrap">
                                            <span class="badge bg-soft-warning text-warning">
                                                0-50 kg arası
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-soft-warning rounded">
                                                <i class="bx bx-trending-down font-size-20 text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Yeterli Stok</span>
                                        <h4 class="mb-3 text-success">
                                            <?php echo $stokIstatistik['yuksek_stok_urun'] + $stokIstatistik['orta_stok_urun']; ?> 
                                            <small class="text-muted">ürün</small>
                                        </h4>
                                        <div class="text-nowrap">
                                            <span class="badge bg-soft-success text-success">
                                                50+ kg
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-soft-success rounded">
                                                <i class="bx bx-trending-up font-size-20 text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detaylı Stok Tablosu -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Detaylı Stok Durumu</h5>
                                <div>
                                    <button class="btn btn-light btn-sm" onclick="window.location.reload()">
                                        <i class="bx bx-refresh me-1"></i> Yenile
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">#</th>
                                                <th>Tür</th>
                                                <th>Tip</th>
                                                <th>Miktar</th>
                                                <th>Birim</th>
                                                <th>Stok Durumu</th>
                                                <th>Son Güncelleme</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sorgu = $db->prepare("SELECT s.*, zt.turAdi, ztp.tipAdi, ztp.birim 
                                                                 FROM tbl_zeytin_stok s
                                                                 LEFT JOIN tbl_zeytin_tipleri ztp ON s.tipId = ztp.tipId
                                                                 LEFT JOIN tbl_zeytin_turleri zt ON ztp.turId = zt.turId
                                                                 ORDER BY s.miktar ASC, zt.turAdi, ztp.tipAdi");
                                            $sorgu->execute();
                                            $say = 1;
                                            
                                            while($stok = $sorgu->fetch(PDO::FETCH_ASSOC)) {
                                                $miktar = floatval($stok['miktar']);
                                                
                                                // Stok durumuna göre CSS sınıfı ve badge belirle
                                                if($miktar < 0) {
                                                    $stokClass = 'stok-negative';
                                                    $durumBadge = 'bg-danger';
                                                    $durumText = 'NEGATİF STOK!';
                                                    $progressClass = 'bg-danger';
                                                    $progressWidth = 0;
                                                } else if($miktar == 0) {
                                                    $stokClass = 'stok-critical';
                                                    $durumBadge = 'bg-danger';
                                                    $durumText = 'STOK YOK';
                                                    $progressClass = 'bg-danger';
                                                    $progressWidth = 0;
                                                } else if($miktar <= 50) {
                                                    $stokClass = 'stok-low';
                                                    $durumBadge = 'bg-warning';
                                                    $durumText = 'AZ STOK';
                                                    $progressClass = 'bg-warning';
                                                    $progressWidth = ($miktar / 50) * 100;
                                                } else if($miktar <= 200) {
                                                    $stokClass = 'stok-medium';
                                                    $durumBadge = 'bg-info';
                                                    $durumText = 'ORTA STOK';
                                                    $progressClass = 'bg-info';
                                                    $progressWidth = ($miktar / 200) * 100;
                                                } else {
                                                    $stokClass = 'stok-high';
                                                    $durumBadge = 'bg-success';
                                                    $durumText = 'YETERLİ STOK';
                                                    $progressClass = 'bg-success';
                                                    $progressWidth = 100;
                                                }
                                                
                                                // Progress bar width sınırla
                                                $progressWidth = min($progressWidth, 100);
                                            ?>
                                            <tr class="<?php echo $stokClass; ?>">
                                                <td><?php echo $say++; ?></td>
                                                <td><strong><?php echo htmlspecialchars($stok['turAdi']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($stok['tipAdi']); ?></td>
                                                <td>
                                                    <div class="fw-bold <?php echo $miktar < 0 ? 'text-danger' : ''; ?>">
                                                        <?php echo number_format($miktar, 2); ?>
                                                    </div>
                                                    <?php if($miktar > 0): ?>
                                                    <div class="progress">
                                                        <div class="progress-bar <?php echo $progressClass; ?>" 
                                                             role="progressbar" 
                                                             style="width: <?php echo $progressWidth; ?>%"
                                                             aria-valuenow="<?php echo $progressWidth; ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="10000">
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $stok['birim']; ?></td>
                                                <td>
                                                    <span class="badge stok-badge <?php echo $durumBadge; ?>">
                                                        <?php echo $durumText; ?>
                                                    </span>
                                                    <?php if($miktar < 0): ?>
                                                    <div class="mt-1">
                                                        <small class="text-danger">
                                                            <i class="bx bx-error"></i> Acil stok takviyesi gerekli!
                                                        </small>
                                                    </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('d.m.Y H:i', strtotime($stok['sonGuncelleme'])); ?>
                                                    </small>
                                                </td>
                                               
                                            </tr>
                                            <?php } ?>
                                            
                                            <?php if($say == 1): ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    <i class="bx bx-package display-4 d-block mb-2"></i>
                                                    Henüz stok kaydı bulunmamaktadır.
                                                </td>
                                            </tr>
                                            <?php endif; ?>
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

<!-- Stok Güncelleme Modal -->
<div class="modal fade" id="stokGuncelleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stok Güncelle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="stokGuncelleContent">
                <!-- İçerik JavaScript ile yüklenecek -->
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/vendor-scripts.php'; ?>

<script>
// Stok güncelleme fonksiyonu
function stokGuncelle(stokId, urunAdi) {
    // Basit bir prompt ile stok güncelleme
    const yeniMiktar = prompt(`"${urunAdi}" ürünü için yeni stok miktarını girin:`, "0");
    
    if (yeniMiktar !== null && !isNaN(parseFloat(yeniMiktar))) {
        // AJAX ile stok güncelleme
        fetch(`islem.php?islem=stokGuncelle&stokId=${stokId}&miktar=${parseFloat(yeniMiktar)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Stok başarıyla güncellendi!');
                    window.location.reload();
                } else {
                    alert('Stok güncelleme başarısız: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                alert('Stok güncelleme sırasında bir hata oluştu.');
            });
    }
}

// Stok hareketleri fonksiyonu
function stokHareketleri(tipId) {
    window.open(`stok-hareketleri.php?tipId=${tipId}`, '_blank');
}

// Tooltip'leri başlat
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

</body>
</html>