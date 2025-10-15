<?php
// ajax-simple.php
include 'config.php';

$islem = $_GET['islem'] ?? '';
$alisNo = $_GET['alisNo'] ?? '';

if($islem == 'alimDetay' && !empty($alisNo)) {
    
    $sorgu = $db->prepare("
        SELECT 
            za.*,
            m.adSoyad,
            m.phone,
            m.email,
            m.adres,
            zt.turAdi,
            ztp.tipAdi,
            ztp.birim
        FROM tbl_zeytin_alis za
        LEFT JOIN tbl_musteri m ON za.musteriId = m.musteriId
        LEFT JOIN tbl_zeytin_tipleri ztp ON za.tipId = ztp.tipId
        LEFT JOIN tbl_zeytin_turleri zt ON ztp.turId = zt.turId
        WHERE za.alisNo = ?
        ORDER BY za.alisId
    ");
    $sorgu->execute([$alisNo]);
    $urunler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($urunler) === 0) {
        echo '<div class="alert alert-warning">Alış bulunamadı!</div>';
        exit;
    }
    
    $alim = $urunler[0];
    $genelToplam = 0;
    $genelMiktar = 0;
    foreach($urunler as $urun) {
        $genelToplam += $urun['toplamTutar'];
        $genelMiktar += $urun['miktar'];
    }
    ?>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Müşteri Bilgileri</h6>
                </div>
                <div class="card-body">
                    <p><strong>Ad Soyad:</strong> <?= htmlspecialchars($alim['adSoyad']) ?></p>
                    <p><strong>Telefon:</strong> <?= htmlspecialchars($alim['phone']) ?></p>
                    <?php if(!empty($alim['email'])): ?>
                    <p><strong>E-posta:</strong> <?= htmlspecialchars($alim['email']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Alış Bilgileri</h6>
                </div>
                <div class="card-body">
                    <p><strong>Alış No:</strong> <code><?= $alisNo ?></code></p>
                    <p><strong>Alış Tarihi:</strong> <?= date('d.m.Y', strtotime($alim['alisTarihi'])) ?></p>
                    <p><strong>Kayıt Tarihi:</strong> <?= date('H:i:s', strtotime($alim['kayitTarihi'])) ?></p>
                    <p><strong>Ödeme Durumu:</strong> 
                        <span class="badge bg-<?= $alim['odemeDurumu'] == 'odenmis' ? 'success' : ($alim['odemeDurumu'] == 'kismi_odendi' ? 'warning' : 'danger') ?>">
                            <?= $alim['odemeDurumu'] == 'odenmis' ? 'Ödendi' : ($alim['odemeDurumu'] == 'kismi_odendi' ? 'Kısmen Ödendi' : 'Ödenmedi') ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0">Ürünler (<?= count($urunler) ?> adet)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Zeytin Türü</th>
                            <th>Zeytin Tipi</th>
                            <th>Miktar</th>
                            <th>Birim Fiyat</th>
                            <th>Toplam Tutar</th>
                            <th>Açıklama</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($urunler as $index => $urun): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($urun['turAdi']) ?></td>
                            <td><?= htmlspecialchars($urun['tipAdi']) ?></td>
                            <td><?= number_format($urun['miktar'], 2) ?> <?= $urun['birim'] ?></td>
                            <td><?= number_format($urun['birimFiyat'], 2) ?> TL</td>
                            <td class="fw-bold text-primary"><?= number_format($urun['toplamTutar'], 2) ?> TL</td>
                            <td><?= !empty($urun['urunAciklama']) ? htmlspecialchars($urun['urunAciklama']) : '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="3" class="text-end"><strong>TOPLAM:</strong></td>
                            <td><strong><?= number_format($genelMiktar, 2) ?> kg</strong></td>
                            <td></td>
                            <td><strong><?= number_format($genelToplam, 2) ?> TL</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php
} else {
    echo '<div class="alert alert-danger">Geçersiz istek!</div>';
}
?>