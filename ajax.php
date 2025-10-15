<?php
include 'config.php';

// Hata ayıklama modu
error_reporting(E_ALL);
ini_set('display_errors', 1);

// JSON header
header('Content-Type: application/json; charset=utf-8');

// CORS izinleri (gerekirse)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Hızlı müşteri ekleme
if(isset($_POST['islem']) && $_POST['islem'] == 'hizliMusteriEkle') {
    $response = [];
    
    try {
        // Verileri al ve temizle
        $adSoyad = trim($_POST['adSoyad'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $adres = trim($_POST['adres'] ?? '');
        
        // Validasyon
        if (empty($adSoyad)) {
            throw new Exception('Ad soyad alanı zorunludur!');
        }
        
        // Telefon numarasını temizle
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Telefon validasyonu
        if (strlen($phone) !== 10 || !preg_match('/^5[0-9]{9}$/', $phone)) {
            throw new Exception('Geçersiz telefon numarası! 10 haneli ve 5 ile başlamalıdır.');
        }
        
        // Aynı telefon numarası var mı kontrol et
        $sorgu = $db->prepare("SELECT COUNT(*) as sayi FROM tbl_musteri WHERE phone = ?");
        $sorgu->execute([$phone]);
        $kontrol = $sorgu->fetch(PDO::FETCH_ASSOC);
        
        if($kontrol['sayi'] > 0) {
            throw new Exception('Bu telefon numarası zaten kayıtlı!');
        }
        
        // E-posta validasyonu
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Geçersiz e-posta adresi!');
        }
        
        // Müşteriyi ekle
        $sorgu = $db->prepare("INSERT INTO tbl_musteri SET 
                            adSoyad = ?,
                            phone = ?,
                            email = ?,
                            adres = ?,
                            musteriTipi = 'bireysel',
                            durum = 1,
                            kayitTarihi = NOW()");
        
        $ekle = $sorgu->execute([$adSoyad, $phone, $email, $adres]);
        
        if($ekle) {
            $musteriId = $db->lastInsertId();
            
            $response = [
                'success' => true,
                'message' => 'Müşteri başarıyla eklendi',
                'data' => [
                    'musteriId' => $musteriId,
                    'adSoyad' => $adSoyad,
                    'phone' => $phone
                ]
            ];
        } else {
            throw new Exception('Müşteri eklenirken veritabanı hatası oluştu!');
        }
        
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}


//detay 
if(isset($_GET['islem']) && $_GET['islem'] == 'alimDetay') {
    $alisNo = $_GET['alisNo'] ?? '';
    
    if(empty($alisNo)) {
        echo '<div class="alert alert-danger">Alış numarası belirtilmedi!</div>';
        exit;
    }
    
    // Alış numarasına göre tüm ürünleri getir
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
    
    // Toplamları hesapla
    $genelToplam = 0;
    $genelMiktar = 0;
    foreach($urunler as $urun) {
        $genelToplam += $urun['toplamTutar'];
        $genelMiktar += $urun['miktar'];
    }
    
    $odemeDurumu = [
        'odenmedi' => 'ÖDENMEDİ',
        'kismi_odendi' => 'KISMEN ÖDENDİ', 
        'odenmis' => 'ÖDENDİ'
    ][$alim['odemeDurumu']] ?? $alim['odemeDurumu'];
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
                    <?php if(!empty($alim['adres'])): ?>
                    <p><strong>Adres:</strong> <?= htmlspecialchars($alim['adres']) ?></p>
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
                    <p><strong>Alış Tarihi:</strong> <?= date('d.m.Y', strtotime($alim['alisTarihi'])) ?></p>
                    <p><strong>Kayıt Tarihi:</strong> <?= date('d.m.Y H:i:s', strtotime($alim['kayitTarihi'])) ?></p>
                    <p><strong>Ödeme Durumu:</strong> 
                        <span class="badge bg-<?= $alim['odemeDurumu'] == 'odenmis' ? 'success' : ($alim['odemeDurumu'] == 'kismi_odendi' ? 'warning' : 'danger') ?>">
                            <?= $odemeDurumu ?>
                        </span>
                    </p>
                    <?php if(!empty($alim['aciklama'])): ?>
                    <p><strong>Açıklama:</strong> <?= htmlspecialchars($alim['aciklama']) ?></p>
                    <?php endif; ?>
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
}


// Müşteri istatistikleri
if(isset($_POST['islem']) && $_POST['islem'] == 'musteriIstatistik') {
    $response = [];
    
    try {
        $musteriId = intval($_POST['musteriId'] ?? 0);
        
        if ($musteriId <= 0) {
            throw new Exception('Geçersiz müşteri ID!');
        }
        
        $sorgu = $db->prepare("
            SELECT 
                COUNT(z.alisId) as toplamAlim,
                COALESCE(SUM(z.miktar), 0) as toplamMiktar,
                COALESCE(SUM(z.toplamTutar), 0) as toplamTutar
            FROM tbl_zeytin_alis z 
            WHERE z.musteriId = ? AND z.durum = 1
        ");
        $sorgu->execute([$musteriId]);
        $istatistik = $sorgu->fetch(PDO::FETCH_ASSOC);
        
        $response = [
            'success' => true,
            'toplamAlim' => $istatistik['toplamAlim'],
            'toplamMiktar' => number_format($istatistik['toplamMiktar'], 2),
            'toplamTutar' => number_format($istatistik['toplamTutar'], 0)
        ];
        
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Tipleri getir
if(isset($_POST['islem']) && $_POST['islem'] == 'tipleriGetir') {
    $turId = intval($_POST['turId'] ?? 0);
    
    try {
        $sorgu = $db->prepare("SELECT * FROM tbl_zeytin_tipleri WHERE turId = ? AND durum = 1 ORDER BY tipAdi");
        $sorgu->execute([$turId]);
        
        $html = '<option value="">Tip Seçin</option>';
        
        while($tip = $sorgu->fetch(PDO::FETCH_ASSOC)) {
            $html .= '<option value="'.$tip['tipId'].'" data-birim="'.$tip['birim'].'" data-fiyat="'.$tip['birimFiyat'].'">';
            $html .= htmlspecialchars($tip['tipAdi']).' ('.number_format($tip['birimFiyat'], 2).' TL/'.$tip['birim'].')';
            $html .= '</option>';
        }
        
        echo $html;
        
    } catch (Exception $e) {
        echo '<option value="">Veritabanı hatası</option>';
    }
    exit;
}

// Tip bilgisi getir
if(isset($_POST['islem']) && $_POST['islem'] == 'tipBilgisiGetir') {
    $response = [];
    
    try {
        $tipId = intval($_POST['tipId'] ?? 0);
        
        if ($tipId <= 0) {
            throw new Exception('Geçersiz tip ID!');
        }
        
        $sorgu = $db->prepare("SELECT birimFiyat, birim FROM tbl_zeytin_tipleri WHERE tipId = ?");
        $sorgu->execute([$tipId]);
        
        if($sorgu->rowCount() > 0) {
            $tip = $sorgu->fetch(PDO::FETCH_ASSOC);
            $response = [
                'birimFiyat' => floatval($tip['birimFiyat']),
                'birim' => $tip['birim']
            ];
        } else {
            throw new Exception('Tip bulunamadı!');
        }
        
    } catch (Exception $e) {
        $response = ['error' => $e->getMessage()];
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Müşteri detayı için AJAX işlemi (GET)
if(isset($_GET['islem']) && $_GET['islem'] == 'musteriDetay') {
    $musteriId = intval($_GET['musteriId'] ?? 0);
    
    try {
        $sorgu = $db->prepare("
            SELECT m.*, 
                   COUNT(z.alisId) as toplamAlim,
                   COALESCE(SUM(z.miktar), 0) as toplamMiktar,
                   COALESCE(SUM(z.toplamTutar), 0) as toplamTutar,
                   COALESCE(SUM(CASE WHEN z.odemeDurumu IN ('odenmedi', 'kismi_odendi') THEN z.toplamTutar ELSE 0 END), 0) as bekleyenTutar
            FROM tbl_musteri m
            LEFT JOIN tbl_zeytin_alis z ON m.musteriId = z.musteriId AND z.durum = 1
            WHERE m.musteriId = ?
            GROUP BY m.musteriId
        ");
        $sorgu->execute([$musteriId]);
        $musteri = $sorgu->fetch(PDO::FETCH_ASSOC);
        
        if($musteri) {
            echo '
            <div class="row">
                <div class="col-md-6">
                    <h6>Kişisel Bilgiler</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Ad Soyad:</strong></td><td>'.htmlspecialchars($musteri['adSoyad']).'</td></tr>
                        '.($musteri['tcKimlik'] ? '<tr><td><strong>TC Kimlik:</strong></td><td>'.$musteri['tcKimlik'].'</td></tr>' : '').'
                        <tr><td><strong>Durum:</strong></td><td><span class="badge bg-'.($musteri['durum'] == 1 ? 'success' : 'danger').'">'.($musteri['durum'] == 1 ? 'Aktif' : 'Pasif').'</span></td></tr>
                        <tr><td><strong>Kayıt Tarihi:</strong></td><td>'.date('d.m.Y H:i', strtotime($musteri['kayitTarihi'])).'</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>İletişim Bilgileri</h6>
                    <table class="table table-sm">
                        '.($musteri['phone'] ? '<tr><td><strong>Telefon:</strong></td><td>'.$musteri['phone'].'</td></tr>' : '').'
                        '.($musteri['email'] ? '<tr><td><strong>E-posta:</strong></td><td>'.htmlspecialchars($musteri['email']).'</td></tr>' : '').'
                        '.($musteri['il'] ? '<tr><td><strong>İl:</strong></td><td>'.htmlspecialchars($musteri['il']).'</td></tr>' : '').'
                        '.($musteri['ilce'] ? '<tr><td><strong>İlçe:</strong></td><td>'.htmlspecialchars($musteri['ilce']).'</td></tr>' : '').'
                    </table>
                </div>
            </div>
            ';
            
            if($musteri['adres']) {
                echo '
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Adres</h6>
                        <div class="border p-2 rounded">'.htmlspecialchars($musteri['adres']).'</div>
                    </div>
                </div>
                ';
            }
            
            if($musteri['aciklama']) {
                echo '
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Açıklama</h6>
                        <div class="border p-2 rounded">'.htmlspecialchars($musteri['aciklama']).'</div>
                    </div>
                </div>
                ';
            }
            
            echo '
            <div class="row mt-4">
                <div class="col-12">
                    <h6>Zeytin Alım Özeti</h6>
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-primary">'.$musteri['toplamAlim'].'</h4>
                                <small class="text-muted">Toplam Alım</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-success">'.number_format($musteri['toplamMiktar'], 2).' kg</h4>
                                <small class="text-muted">Toplam Miktar</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-info">'.number_format($musteri['toplamTutar'], 0).' TL</h4>
                                <small class="text-muted">Toplam Tutar</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-danger">'.number_format($musteri['bekleyenTutar'], 0).' TL</h4>
                                <small class="text-muted">Bekleyen Ödeme</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ';
        } else {
            echo '<div class="alert alert-danger">Müşteri bulunamadı!</div>';
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Hata: ' . $e->getMessage() . '</div>';
    }
    exit;
}

// Test endpoint
if(isset($_GET['islem']) && $_GET['islem'] == 'test') {
    echo "<h3>AJAX Test Sayfası</h3>";
    
    // Veritabanı bağlantısı testi
    if (!$db) {
        echo "❌ Veritabanı bağlantısı BAŞARISIZ<br>";
    } else {
        echo "✅ Veritabanı bağlantısı BAŞARILI<br>";
    }
    
    // POST verilerini göster
    echo "<h4>POST Verileri:</h4>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    // GET verilerini göster
    echo "<h4>GET Verileri:</h4>";
    echo "<pre>" . print_r($_GET, true) . "</pre>";
    
    exit;
}

// Eğer hiçbir işlem eşleşmezse
echo json_encode([
    'error' => 'Geçersiz işlem',
    'received_data' => $_POST
], JSON_UNESCAPED_UNICODE);
?>