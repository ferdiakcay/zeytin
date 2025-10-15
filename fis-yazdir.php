<?php
include 'config.php';

$alisNo = $_GET['alisNo'] ?? '';

if(empty($alisNo)) {
    die("Alış numarası belirtilmedi!");
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
    die("Alış bulunamadı!");
}

// İlk üründen genel bilgileri al
$alim = $urunler[0];

// Toplam tutarı hesapla
$genelToplam = 0;
$genelMiktar = 0;
$urunSayisi = count($urunler);

foreach($urunler as $urun) {
    $genelToplam += $urun['toplamTutar'];
    $genelMiktar += $urun['miktar'];
}

// Ödeme durumu Türkçe
$odemeDurumu = [
    'odenmedi' => 'ÖDENMEDİ',
    'kismi_odendi' => 'KISMEN ÖDENDİ', 
    'odenmis' => 'ÖDENDİ'
][$alim['odemeDurumu']] ?? $alim['odemeDurumu'];

// Telefon formatlama
function formatPhone($phone) {
    if (empty($phone)) return '';
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($cleanPhone) === 10) {
        return substr($cleanPhone, 0, 3) . ' ' . substr($cleanPhone, 3, 3) . ' ' . substr($cleanPhone, 6, 2) . ' ' . substr($cleanPhone, 8, 2);
    }
    return $phone;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alış Fişi - <?= $alisNo ?></title>
    <style>
        /* Thermal Yazıcı Stili - 80mm genişlik */
        @media print {
            @page {
                margin: 0;
                padding: 0;
                size: 80mm auto;
            }
            
            body {
                margin: 0;
                padding: 3mm;
                font-family: 'Courier New', monospace;
                font-size: 10px;
                line-height: 1.1;
                width: 74mm;
                background: white;
            }
            
            .no-print {
                display: none !important;
            }
            
            .fis-container {
                width: 100%;
                padding: 4px;
                page-break-inside: avoid;
            }
            
            .urun-item {
                margin: 3px 0;
                padding: 2px 0;
                border-bottom: 1px dotted #666;
            }
        }
        
        @media screen {
            body {
                font-family: Arial, sans-serif;
                max-width: 300px;
                margin: 20px auto;
                padding: 15px;
                border: 1px solid #ddd;
                background: #f9f9f9;
            }
            
            .fis-container {
                background: white;
                padding: 15px;
                border: 2px solid #333;
                border-radius: 5px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            
            .urun-item {
                margin: 8px 0;
                padding: 6px 0;
                border-bottom: 1px dashed #ddd;
            }
        }
        
        .fis-header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #000;
        }
        
        .fis-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        
        .fis-subtitle {
            font-size: 10px;
            margin-bottom: 2px;
        }
        
        .fis-line {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        
        .fis-label {
            font-weight: bold;
        }
        
        .fis-value {
            text-align: right;
        }
        
        .fis-divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
            padding: 2px 0;
        }
        
        .fis-total {
            font-weight: bold;
            font-size: 12px;
            background: #f8f9fa;
            padding: 6px;
            margin: 8px 0;
            text-align: center;
            border: 1px solid #000;
            border-radius: 3px;
        }
        
        .fis-footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px dashed #000;
            font-size: 8px;
        }
        
        .urun-header {
            font-weight: bold;
            background: #e9ecef;
            padding: 2px 4px;
            margin-bottom: 1px;
            border-radius: 2px;
            font-size: 9px;
        }
        
        .urun-detay {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            margin: 1px 0;
        }
        
        .urun-toplam {
            font-weight: bold;
            text-align: right;
            margin-top: 1px;
            font-size: 9px;
            color: #d63384;
        }
        
        .coklu-badge {
            background: #dc3545;
            color: white;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 8px;
            margin-left: 5px;
        }
        
        .button-group {
            text-align: center;
            margin: 15px 0;
        }
        
        .btn {
            padding: 8px 15px;
            margin: 3px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-print {
            background: #007bff;
            color: white;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
        }
        
        .btn-list {
            background: #198754;
            color: white;
        }
        
        .summary-line {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            margin: 1px 0;
            background: #f8f9fa;
            padding: 2px 4px;
            border-radius: 2px;
        }
        
        .barcode {
            text-align: center;
            margin: 8px 0;
            font-family: 'Libre Barcode 39', monospace;
            font-size: 20px;
        }
    </style>
    
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
</head>
<body>
    <div class="fis-container">
        <!-- Fiş Başlığı -->
        <div class="fis-header">
            <div class="fis-title">ATAK ELEKTRİK</div>
            <div class="fis-subtitle">ZEYTİN ALIM FİŞİ</div>
            <div class="fis-subtitle"><?= date('d.m.Y H:i:s') ?></div>
            <?php if($urunSayisi > 1): ?>
            <div class="fis-subtitle">
                ÇOKLU ÜRÜN <span class="coklu-badge"><?= $urunSayisi ?> ÜRÜN</span>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Alış Bilgileri -->
        <div class="fis-line">
            <div class="fis-label">Alış No:</div>
            <div class="fis-value"><?= $alisNo ?></div>
        </div>
        
        <div class="fis-line">
            <div class="fis-label">Tarih:</div>
            <div class="fis-value"><?= date('d.m.Y', strtotime($alim['alisTarihi'])) ?></div>
        </div>
        
        <div class="fis-line">
            <div class="fis-label">Saat:</div>
            <div class="fis-value"><?= date('H:i', strtotime($alim['kayitTarihi'])) ?></div>
        </div>
        
        <div class="fis-divider"></div>
        
        <!-- Müşteri Bilgileri -->
        <div class="fis-line">
            <div class="fis-label">Müşteri:</div>
            <div class="fis-value"><?= htmlspecialchars($alim['adSoyad']) ?></div>
        </div>
        
        <div class="fis-line">
            <div class="fis-label">Telefon:</div>
            <div class="fis-value"><?= formatPhone($alim['phone']) ?></div>
        </div>
        
        <?php if(!empty($alim['email'])): ?>
        <div class="fis-line">
            <div class="fis-label">E-posta:</div>
            <div class="fis-value"><?= htmlspecialchars($alim['email']) ?></div>
        </div>
        <?php endif; ?>
        
        <?php if(!empty($alim['adres'])): ?>
        <div class="fis-line">
            <div class="fis-label">Adres:</div>
            <div class="fis-value" style="text-align: left;"><?= htmlspecialchars($alim['adres']) ?></div>
        </div>
        <?php endif; ?>
        
        <div class="fis-divider"></div>
        
        <!-- Ürün Listesi -->
        <div class="fis-line">
            <div class="fis-label">Ürünler:</div>
            <div class="fis-value"><?= $urunSayisi ?> adet</div>
        </div>
        
        <?php foreach($urunler as $index => $urun): ?>
        <div class="urun-item">
            <div class="urun-header">
                ÜRÜN <?= $index + 1 ?>: <?= htmlspecialchars($urun['turAdi']) ?> - <?= htmlspecialchars($urun['tipAdi']) ?>
            </div>
            
            <div class="urun-detay">
                <span>Miktar:</span>
                <span><?= number_format($urun['miktar'], 2) ?> <?= $urun['birim'] ?></span>
            </div>
            
            <div class="urun-detay">
                <span>Birim Fiyat:</span>
                <span><?= number_format($urun['birimFiyat'], 2) ?> TL</span>
            </div>
            
            <div class="urun-toplam">
                Toplam: <?= number_format($urun['toplamTutar'], 2) ?> TL
            </div>
            
            <?php if(!empty($urun['urunAciklama'])): ?>
            <div class="urun-detay" style="font-style: italic;">
                <span>Not:</span>
                <span><?= htmlspecialchars($urun['urunAciklama']) ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        
        <div class="fis-divider"></div>
        
        <!-- Özet Bilgiler -->
        <div class="summary-line">
            <span>Toplam Miktar:</span>
            <span><?= number_format($genelMiktar, 2) ?> kg</span>
        </div>
        
        <div class="summary-line">
            <span>Ürün Sayısı:</span>
            <span><?= $urunSayisi ?> adet</span>
        </div>
        
        <?php if($genelMiktar > 0): ?>
        <div class="summary-line">
            <span>Ortalama Fiyat:</span>
            <span><?= number_format($genelToplam / $genelMiktar, 2) ?> TL/kg</span>
        </div>
        <?php endif; ?>
        
        <!-- Toplam Tutar -->
        <div class="fis-total">
            GENEL TOPLAM: <?= number_format($genelToplam, 2) ?> TL
        </div>
        
        <!-- Ödeme Durumu -->
        <div class="fis-line">
            <div class="fis-label">Ödeme Durumu:</div>
            <div class="fis-value"><?= $odemeDurumu ?></div>
        </div>
        
        <!-- Genel Açıklama -->
        <?php if(!empty($alim['aciklama'])): ?>
        <div class="fis-divider"></div>
        <div class="fis-line">
            <div class="fis-label">Açıklama:</div>
            <div class="fis-value" style="text-align: left;"><?= htmlspecialchars($alim['aciklama']) ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Barkod -->
        <div class="barcode">
            *<?= $alisNo ?>*
        </div>
        
        <!-- Fiş Alt Bilgisi -->
        <div class="fis-footer">
            <div>*** TEŞEKKÜR EDERİZ ***</div>
            <div>İyi Günler Dileriz</div>
            <div>ATAK ELEKTRİK</div>
        </div>
    </div>
    
    <!-- Butonlar (Sadece ekranda görünür) -->
    <div class="button-group no-print">
        <button class="btn btn-print" onclick="window.print()">
            🖨️ Fişi Yazdır
        </button>
        <a href="zeytin-alim.php" class="btn btn-back">
            ➕ Yeni Alım
        </a>
        <a href="alimList.php" class="btn btn-list">
            📋 Alım Listesi
        </a>
    </div>

    <script>
    // Sayfa yüklendiğinde otomatik yazdır
    window.addEventListener('load', function() {
        // Otomatik yazdırmayı açmak için aşağıdaki satırın yorumunu kaldırın
        // setTimeout(() => { window.print(); }, 500);
    });
    
    // Klavye kısayolları
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
        if (e.key === 'Escape') {
            window.location.href = 'zeytin-alim.php';
        }
    });
    </script>
</body>
</html>