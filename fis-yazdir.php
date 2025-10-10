<?php
include 'config.php';

$alisId = intval($_GET['alisId']);

// Alım bilgilerini getir
$sorgu = $db->prepare("
    SELECT 
        z.*,
        m.adSoyad,
        m.phone,
        zt.turAdi,
        ztp.tipAdi,
        ztp.birim
    FROM tbl_zeytin_alis z
    LEFT JOIN tbl_musteri m ON z.musteriId = m.musteriId
    LEFT JOIN tbl_zeytin_tipleri ztp ON z.tipId = ztp.tipId
    LEFT JOIN tbl_zeytin_turleri zt ON ztp.turId = zt.turId
    WHERE z.alisId = ?
");
$sorgu->execute([$alisId]);
$alim = $sorgu->fetch(PDO::FETCH_ASSOC);

if(!$alim) {
    die("Alım bulunamadı!");
}

// Ödeme durumu Türkçe
$odemeDurumu = [
    'odenmedi' => 'ÖDENMEDİ',
    'kismi_odendi' => 'KISMEN ÖDENDİ', 
    'odenmis' => 'ÖDENDİ'
][$alim['odemeDurumu']] ?? $alim['odemeDurumu'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiş Yazdır</title>
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
                padding: 5mm;
                font-family: 'Courier New', monospace;
                font-size: 12px;
                line-height: 1.2;
                width: 70mm;
                background: white;
            }
            
            .no-print {
                display: none !important;
            }
            
            .fis-container {
                width: 100%;
                border: 1px dashed #ccc;
                padding: 8px;
                page-break-inside: avoid;
            }
        }
        
        @media screen {
            body {
                font-family: Arial, sans-serif;
                max-width: 300px;
                margin: 20px auto;
                padding: 20px;
                border: 1px solid #ddd;
                background: #f9f9f9;
            }
            
            .fis-container {
                background: white;
                padding: 20px;
                border: 2px solid #333;
                border-radius: 5px;
            }
        }
        
        .fis-header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px dashed #000;
        }
        
        .fis-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .fis-subtitle {
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .fis-line {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            padding: 2px 0;
        }
        
        .fis-label {
            font-weight: bold;
            flex: 1;
        }
        
        .fis-value {
            flex: 2;
            text-align: right;
        }
        
        .fis-divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
            padding: 5px 0;
        }
        
        .fis-total {
            font-weight: bold;
            font-size: 14px;
            background: #f0f0f0;
            padding: 8px;
            margin: 10px 0;
            text-align: center;
            border: 1px solid #000;
        }
        
        .fis-footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px dashed #000;
            font-size: 10px;
        }
        
        .button-group {
            text-align: center;
            margin: 20px 0;
        }
        
        .btn {
            padding: 10px 20px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-print {
            background: #007bff;
            color: white;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
        }
        
        .barcode {
            text-align: center;
            margin: 10px 0;
            font-family: 'Libre Barcode 39', monospace;
            font-size: 24px;
        }
    </style>
    
    <!-- Barkod fontu -->
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
</head>
<body>
    <div class="fis-container">
        <!-- Fiş Başlığı -->
        <div class="fis-header">
            <div class="fis-title">Zeytin Yönetim Programı</div>
            <div class="fis-subtitle">Zeytin Alım Fişi</div>
            <div class="fis-subtitle"><?= date('d.m.Y H:i:s') ?></div>
        </div>
        
        <!-- Alım Bilgileri -->
        <div class="fis-line">
            <div class="fis-label">Fiş No:</div>
            <div class="fis-value"><?= str_pad($alim['alisId'], 6, '0', STR_PAD_LEFT) ?></div>
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
            <div class="fis-value"><?= htmlspecialchars($alim['phone']) ?></div>
        </div>
        
        <div class="fis-divider"></div>
        
        <!-- Zeytin Bilgileri -->
        <div class="fis-line">
            <div class="fis-label">Zeytin Türü:</div>
            <div class="fis-value"><?= htmlspecialchars($alim['turAdi']) ?></div>
        </div>
        
        <div class="fis-line">
            <div class="fis-label">Zeytin Tipi:</div>
            <div class="fis-value"><?= htmlspecialchars($alim['tipAdi']) ?></div>
        </div>
        
        <div class="fis-line">
            <div class="fis-label">Miktar:</div>
            <div class="fis-value"><?= number_format($alim['miktar'], 2) ?> <?= $alim['birim'] ?></div>
        </div>
        
        <div class="fis-line">
            <div class="fis-label">Birim Fiyat:</div>
            <div class="fis-value"><?= number_format($alim['birimFiyat'], 2) ?> TL</div>
        </div>
        
        <div class="fis-divider"></div>
        
        <!-- Toplam Tutar -->
        <div class="fis-total">
            TOPLAM TUTAR: <?= number_format($alim['toplamTutar'], 2) ?> TL
        </div>
        
        <!-- Ödeme Durumu -->
        <div class="fis-line">
            <div class="fis-label">Ödeme:</div>
            <div class="fis-value"><?= $odemeDurumu ?></div>
        </div>
        
        <!-- Açıklama -->
        <?php if(!empty($alim['aciklama'])): ?>
        <div class="fis-divider"></div>
        <div class="fis-line">
            <div class="fis-label">Açıklama:</div>
            <div class="fis-value"><?= htmlspecialchars($alim['aciklama']) ?></div>
        </div>
        <?php endif; ?>
        
        <!-- Barkod -->
        <div class="barcode">
            *<?= str_pad($alim['alisId'], 6, '0', STR_PAD_LEFT) ?>*
        </div>
        
        <!-- Fiş Alt Bilgisi -->
        <div class="fis-footer">
            <div>*** TEŞEKKÜR EDERİZ ***</div>
            <div>İyi Günler Dileriz</div>
            <div>www.kodlasoft.com</div>
        </div>
    </div>
    
    <!-- Butonlar (Sadece ekranda görünür) -->
    <div class="button-group no-print">
        <button class="btn btn-print" onclick="window.print()">
            🖨️ Fişi Yazdır
        </button>
        <a href="zeytin-alim.php" class="btn btn-back">
            ↩️ Yeni Alım
        </a>
        <a href="musteriList.php" class="btn btn-back">
            👥 Müşteri Listesi
        </a>
    </div>

    <script>
    // Sayfa yüklendiğinde otomatik yazdır (isteğe bağlı)
    window.addEventListener('load', function() {
        // Otomatik yazdırmayı açmak için aşağıdaki satırın yorumunu kaldırın
        // window.print();
    });
    
    // Yazdırma sonrası yönlendirme
    window.onafterprint = function() {
        // Yazdırma tamamlandıktan sonra yapılacak işlemler
        console.log('Fiş yazdırıldı');
    };
    </script>
</body>
</html>