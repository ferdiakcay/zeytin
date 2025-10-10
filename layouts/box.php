<?php
// ZEYTİN İSTATİSTİKLERİ
// Toplam zeytin alım miktarı
$sorgu = $db->prepare("SELECT COALESCE(SUM(miktar), 0) as toplamMiktar FROM tbl_zeytin_alis WHERE durum = 1");
$sorgu->execute();
$zeytinToplam = $sorgu->fetch(PDO::FETCH_ASSOC);
$toplamZeytinMiktar = floatval($zeytinToplam['toplamMiktar']);

// Toplam zeytin alım tutarı
$sorgu = $db->prepare("SELECT COALESCE(SUM(toplamTutar), 0) as toplamTutar FROM tbl_zeytin_alis WHERE durum = 1");
$sorgu->execute();
$zeytinTutar = $sorgu->fetch(PDO::FETCH_ASSOC);
$toplamZeytinTutar = floatval($zeytinTutar['toplamTutar']);

// Toplam müşteri sayısı (zeytin alan)
$sorgu = $db->prepare("SELECT COUNT(DISTINCT musteriId) as toplamMusteri FROM tbl_zeytin_alis WHERE durum = 1");
$sorgu->execute();
$musteriSayi = $sorgu->fetch(PDO::FETCH_ASSOC);
$toplamZeytinMusteri = intval($musteriSayi['toplamMusteri']);

// Toplam zeytin türü sayısı
$sorgu = $db->prepare("SELECT COUNT(*) as toplamTur FROM tbl_zeytin_turleri WHERE durum = 1");
$sorgu->execute();
$turSayi = $sorgu->fetch(PDO::FETCH_ASSOC);
$toplamZeytinTur = intval($turSayi['toplamTur']);

// Bu ayki zeytin alım miktarı
$sorgu = $db->prepare("SELECT COALESCE(SUM(miktar), 0) as ayMiktar FROM tbl_zeytin_alis WHERE durum = 1 AND MONTH(alisTarihi) = MONTH(CURRENT_DATE()) AND YEAR(alisTarihi) = YEAR(CURRENT_DATE())");
$sorgu->execute();
$ayZeytin = $sorgu->fetch(PDO::FETCH_ASSOC);
$aylikZeytinMiktar = floatval($ayZeytin['ayMiktar']);

// Bu ayki zeytin alım tutarı
$sorgu = $db->prepare("SELECT COALESCE(SUM(toplamTutar), 0) as ayTutar FROM tbl_zeytin_alis WHERE durum = 1 AND MONTH(alisTarihi) = MONTH(CURRENT_DATE()) AND YEAR(alisTarihi) = YEAR(CURRENT_DATE())");
$sorgu->execute();
$ayTutar = $sorgu->fetch(PDO::FETCH_ASSOC);
$aylikZeytinTutar = floatval($ayTutar['ayTutar']);

// Bugünkü zeytin alım miktarı
$sorgu = $db->prepare("SELECT COALESCE(SUM(miktar), 0) as gunMiktar FROM tbl_zeytin_alis WHERE durum = 1 AND alisTarihi = CURDATE()");
$sorgu->execute();
$gunZeytin = $sorgu->fetch(PDO::FETCH_ASSOC);
$gunlukZeytinMiktar = floatval($gunZeytin['gunMiktar']);

// Bugünkü zeytin alım tutarı
$sorgu = $db->prepare("SELECT COALESCE(SUM(toplamTutar), 0) as gunTutar FROM tbl_zeytin_alis WHERE durum = 1 AND alisTarihi = CURDATE()");
$sorgu->execute();
$gunTutar = $sorgu->fetch(PDO::FETCH_ASSOC);
$gunlukZeytinTutar = floatval($gunTutar['gunTutar']);

// Bugünkü alım sayısı
$sorgu = $db->prepare("SELECT COUNT(*) as gunAlim FROM tbl_zeytin_alis WHERE durum = 1 AND alisTarihi = CURDATE()");
$sorgu->execute();
$gunAlim = $sorgu->fetch(PDO::FETCH_ASSOC);
$gunlukAlimSayisi = intval($gunAlim['gunAlim']);

// Ödenmemiş alımlar
$sorgu = $db->prepare("SELECT COALESCE(SUM(toplamTutar), 0) as odenmemis FROM tbl_zeytin_alis WHERE durum = 1 AND odemeDurumu IN ('odenmedi', 'kismi_odendi')");
$sorgu->execute();
$odenmemis = $sorgu->fetch(PDO::FETCH_ASSOC);
$toplamOdenmemis = floatval($odenmemis['odenmemis']);

// En çok zeytin alan müşteri
$sorgu = $db->prepare("SELECT m.adSoyad, COALESCE(SUM(z.miktar), 0) as toplamMiktar 
                      FROM tbl_zeytin_alis z 
                      JOIN tbl_musteri m ON z.musteriId = m.musteriId 
                      WHERE z.durum = 1 
                      GROUP BY z.musteriId 
                      ORDER BY toplamMiktar DESC 
                      LIMIT 1");
$sorgu->execute();
$enCokAlan = $sorgu->fetch(PDO::FETCH_ASSOC);
$enCokAlanMusteri = $enCokAlan['adSoyad'] ?? 'Henüz yok';
$enCokAlanMiktar = floatval($enCokAlan['toplamMiktar'] ?? 0);

// En popüler zeytin türü
$sorgu = $db->prepare("SELECT zt.turAdi, COALESCE(SUM(z.miktar), 0) as toplamMiktar 
                      FROM tbl_zeytin_alis z 
                      JOIN tbl_zeytin_tipleri ztp ON z.tipId = ztp.tipId 
                      JOIN tbl_zeytin_turleri zt ON ztp.turId = zt.turId 
                      WHERE z.durum = 1 
                      GROUP BY zt.turId 
                      ORDER BY toplamMiktar DESC 
                      LIMIT 1");
$sorgu->execute();
$populerTur = $sorgu->fetch(PDO::FETCH_ASSOC);
$populerTurAdi = $populerTur['turAdi'] ?? 'Henüz yok';
$populerTurMiktar = floatval($populerTur['toplamMiktar'] ?? 0);

// Aktif zeytin tipleri sayısı
$sorgu = $db->prepare("SELECT COUNT(*) as toplamTip FROM tbl_zeytin_tipleri WHERE durum = 1");
$sorgu->execute();
$tipSayi = $sorgu->fetch(PDO::FETCH_ASSOC);
$toplamZeytinTip = intval($tipSayi['toplamTip']);

// Ortalama zeytin fiyatı
$ortalamaFiyat = $toplamZeytinMiktar > 0 ? $toplamZeytinTutar / $toplamZeytinMiktar : 0;

// Bugünün tarihi
$bugunTarih = date('d.m.Y');
?>

<div class="row">
    <!-- Bugünkü Alım -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Bugünkü Alım</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?php echo number_format($gunlukZeytinMiktar, 2); ?>">0</span> 
                            <small class="text-muted">kg</small>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-primary text-primary">
                                <?php echo number_format($gunlukZeytinTutar, 0); ?> TL
                            </span>
                            <span class="ms-1 text-muted font-size-13"><?php echo $gunlukAlimSayisi; ?> alım</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary rounded">
                                <i class="bx bx-calendar-check font-size-20 text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toplam Zeytin Miktarı -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Toplam Zeytin Miktarı</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?php echo number_format($toplamZeytinMiktar, 2); ?>">0</span> 
                            <small class="text-muted">kg</small>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-success text-success">
                                +<?php echo number_format($aylikZeytinMiktar, 2); ?> kg
                            </span>
                            <span class="ms-1 text-muted font-size-13">Bu ay</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-success rounded">
                                <i class="bx bx-package font-size-20 text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toplam Zeytin Tutarı -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Toplam Zeytin Tutarı</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?php echo number_format($toplamZeytinTutar, 0); ?>">0</span> 
                            <small class="text-muted">TL</small>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-info text-info">
                                +<?php echo number_format($aylikZeytinTutar, 0); ?> TL
                            </span>
                            <span class="ms-1 text-muted font-size-13">Bu ay</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-info rounded">
                                <i class="bx bx-money font-size-20 text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toplam Müşteri Sayısı -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Zeytin Alımı Yapan Müşteriler</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?php echo $toplamZeytinMusteri; ?>">0</span> 
                            <small class="text-muted">kişi</small>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-warning text-warning">
                                <?php echo htmlspecialchars(mb_substr($enCokAlanMusteri, 0, 15)) . (mb_strlen($enCokAlanMusteri) > 15 ? '...' : ''); ?>
                            </span>
                            <span class="ms-1 text-muted font-size-13">En çok alan</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-warning rounded">
                                <i class="bx bx-user font-size-20 text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- İkinci Satır İstatistikler -->
<div class="row mt-3">
    <!-- Ödenmemiş Tutarlar -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Ödenmemiş Tutarlar</span>
                        <h4 class="mb-3 text-danger">
                            <span class="counter-value" data-target="<?php echo number_format($toplamOdenmemis, 0); ?>">0</span> 
                            <small class="text-muted">TL</small>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-danger text-danger">Bekleyen</span>
                            <span class="ms-1 text-muted font-size-13">Ödeme</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-danger rounded">
                                <i class="bx bx-time-five font-size-20 text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ortalama Fiyat -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Ortalama Kg Fiyatı</span>
                        <h4 class="mb-3 text-primary">
                            <span class="counter-value" data-target="<?php echo number_format($ortalamaFiyat, 2); ?>">0</span> 
                            <small class="text-muted">TL/kg</small>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-primary text-primary">Ortalama</span>
                            <span class="ms-1 text-muted font-size-13">Birim fiyat</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary rounded">
                                <i class="bx bx-line-chart font-size-20 text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zeytin Türleri -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Zeytin Türleri</span>
                        <h4 class="mb-3 text-info">
                            <span class="counter-value" data-target="<?php echo $toplamZeytinTur; ?>">0</span> 
                            <small class="text-muted">tür</small>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-info text-info">
                                <?php echo htmlspecialchars(mb_substr($populerTurAdi, 0, 12)) . (mb_strlen($populerTurAdi) > 12 ? '...' : ''); ?>
                            </span>
                            <span class="ms-1 text-muted font-size-13">En popüler</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-info rounded">
                                <i class="bx bx-leaf font-size-20 text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zeytin Tipleri -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Zeytin Tipleri</span>
                        <h4 class="mb-3 text-success">
                            <span class="counter-value" data-target="<?php echo $toplamZeytinTip; ?>">0</span> 
                            <small class="text-muted">tip</small>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-success text-success">Çeşitlilik</span>
                            <span class="ms-1 text-muted font-size-13">Toplam tip</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-success rounded">
                                <i class="bx bx-category font-size-20 text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Günlük Detay Tablosu -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Bugünkü Alımlar (<?php echo $bugunTarih; ?>)</h5>
                <span class="badge bg-light text-primary">
                    <i class="bx bx-sync me-1"></i> Güncel
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Müşteri</th>
                                <th>Zeytin Türü/Tipi</th>
                                <th>Miktar</th>
                                <th>Birim Fiyat</th>
                                <th>Toplam Tutar</th>
                                <th>Ödeme Durumu</th>
                                <th>Zaman</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sorgu = $db->prepare("
                                SELECT 
                                    z.alisId,
                                    z.miktar,
                                    z.birimFiyat,
                                    z.toplamTutar,
                                    z.odemeDurumu,
                                    z.kayitTarihi,
                                    m.adSoyad,
                                    zt.turAdi,
                                    ztp.tipAdi
                                FROM tbl_zeytin_alis z
                                LEFT JOIN tbl_musteri m ON z.musteriId = m.musteriId
                                LEFT JOIN tbl_zeytin_tipleri ztp ON z.tipId = ztp.tipId
                                LEFT JOIN tbl_zeytin_turleri zt ON ztp.turId = zt.turId
                                WHERE z.durum = 1 AND z.alisTarihi = CURDATE()
                                ORDER BY z.kayitTarihi DESC
                            ");
                            $sorgu->execute();
                            
                            $say = 1;
                            $toplamBugunMiktar = 0;
                            $toplamBugunTutar = 0;
                            
                            if($sorgu->rowCount() > 0) {
                                while($alim = $sorgu->fetch(PDO::FETCH_ASSOC)) {
                                    $toplamBugunMiktar += $alim['miktar'];
                                    $toplamBugunTutar += $alim['toplamTutar'];
                                    
                                    $odemeDurumuClass = [
                                        'odenmedi' => 'danger',
                                        'kismi_odendi' => 'warning', 
                                        'odenmis' => 'success'
                                    ][$alim['odemeDurumu']] ?? 'secondary';
                                    
                                    $odemeDurumuText = [
                                        'odenmedi' => 'Ödenmedi',
                                        'kismi_odendi' => 'Kısmen Ödendi',
                                        'odenmis' => 'Ödendi'
                                    ][$alim['odemeDurumu']] ?? $alim['odemeDurumu'];
                            ?>
                            <tr>
                                <td><?php echo $say++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($alim['adSoyad']); ?></strong>
                                </td>
                                <td>
                                    <small class="text-muted"><?php echo htmlspecialchars($alim['turAdi'] ?? '-'); ?></small>
                                    <div><?php echo htmlspecialchars($alim['tipAdi'] ?? '-'); ?></div>
                                </td>
                                <td><?php echo number_format($alim['miktar'], 2); ?> kg</td>
                                <td><?php echo number_format($alim['birimFiyat'], 2); ?> TL</td>
                                <td>
                                    <strong><?php echo number_format($alim['toplamTutar'], 2); ?> TL</strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $odemeDurumuClass; ?>">
                                        <?php echo $odemeDurumuText; ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo date('H:i', strtotime($alim['kayitTarihi'])); ?>
                                    </small>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else { 
                            ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bx bx-package display-4 d-block mb-2"></i>
                                    Bugün henüz zeytin alımı yapılmamıştır.
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <?php if($sorgu->rowCount() > 0): ?>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Bugün Toplam:</th>
                                <th><?php echo number_format($toplamBugunMiktar, 2); ?> kg</th>
                                <th>-</th>
                                <th><?php echo number_format($toplamBugunTutar, 2); ?> TL</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Son 7 Gün Özeti -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">Son 7 Gün Alım Özeti</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Tarih</th>
                                <th>Alım Sayısı</th>
                                <th>Toplam Miktar (kg)</th>
                                <th>Toplam Tutar (TL)</th>
                                <th>Ortalama Kg Fiyatı (TL)</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sorgu = $db->prepare("
                                SELECT 
                                    alisTarihi,
                                    COUNT(*) as alimSayisi,
                                    COALESCE(SUM(miktar), 0) as toplamMiktar,
                                    COALESCE(SUM(toplamTutar), 0) as toplamTutar,
                                    COALESCE(AVG(birimFiyat), 0) as ortalamaFiyat
                                FROM tbl_zeytin_alis 
                                WHERE durum = 1 AND alisTarihi >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                                GROUP BY alisTarihi
                                ORDER BY alisTarihi DESC
                            ");
                            $sorgu->execute();
                            
                            if($sorgu->rowCount() > 0) {
                                while($gunluk = $sorgu->fetch(PDO::FETCH_ASSOC)) {
                                    $tarih = date('d.m.Y', strtotime($gunluk['alisTarihi']));
                                    $bugun = date('Y-m-d');
                                    $durumBadge = $gunluk['alisTarihi'] == $bugun ? 'bg-success' : 'bg-secondary';
                                    $durumText = $gunluk['alisTarihi'] == $bugun ? 'Bugün' : 'Geçmiş';
                            ?>
                            <tr>
                                <td><strong><?php echo $tarih; ?></strong></td>
                                <td><?php echo $gunluk['alimSayisi']; ?> alım</td>
                                <td><?php echo number_format(floatval($gunluk['toplamMiktar']), 2); ?> kg</td>
                                <td><?php echo number_format(floatval($gunluk['toplamTutar']), 0); ?> TL</td>
                                <td><?php echo number_format(floatval($gunluk['ortalamaFiyat']), 2); ?> TL</td>
                                <td><span class="badge <?php echo $durumBadge; ?>"><?php echo $durumText; ?></span></td>
                            </tr>
                            <?php 
                                }
                            } else { 
                            ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Son 7 güne ait alım kaydı bulunmamaktadır.</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
.counter-value {
    font-size: 1.5rem;
    font-weight: 600;
}
.card-h-100 {
    height: calc(100% - 1rem);
}
</style>

<script>
// Counter animasyonu
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter-value');
    
    counters.forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-target').replace(',', ''));
        const count = parseFloat(counter.innerText.replace(',', '')) || 0;
        const duration = 1500;
        const steps = 60;
        const step = target / steps;
        let current = count;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                counter.innerText = target.toLocaleString('tr-TR', {
                    minimumFractionDigits: counter.getAttribute('data-target').includes('.') ? 2 : 0,
                    maximumFractionDigits: counter.getAttribute('data-target').includes('.') ? 2 : 0
                });
                clearInterval(timer);
            } else {
                counter.innerText = current.toLocaleString('tr-TR', {
                    minimumFractionDigits: counter.getAttribute('data-target').includes('.') ? 2 : 0,
                    maximumFractionDigits: counter.getAttribute('data-target').includes('.') ? 2 : 0
                });
            }
        }, duration / steps);
    });
});
</script>