<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <title>Zeytin Yönetim Programı - Müşteri Düzenle</title>
    <?php include 'layouts/head.php'; ?>
    <?php include 'layouts/head-style.php'; ?>
    
    <style>
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #1c84ee;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
        .customer-info-card {
            border-left: 4px solid #33c38e;
        }
        .form-actions {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 15px 0;
            border-top: 1px solid #dee2e6;
            margin-top: 30px;
        }
    </style>
</head>
<?php
// Bildirimleri kontrol et
if(isset($_GET['durum'])) {
    $durum = $_GET['durum'];
    $mesaj = $_GET['mesaj'] ?? '';
    
    if($durum == 'ok') {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>' . htmlspecialchars($mesaj) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
    } elseif($durum == 'hata') {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-2"></i>' . htmlspecialchars($mesaj) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
    }
}
?>
<?php include 'layouts/body.php'; ?>

<div id="layout-wrapper">
    <?php include 'layouts/menu.php'; ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <!-- start page title -->
                <?php
                $maintitle = "Müşteri Yönetimi";
                $title = "Müşteri Düzenle";
                ?>
                <?php include 'layouts/breadcrumb.php'; ?>
                <!-- end page title -->

                <?php
                // Müşteri ID kontrolü
                if(!isset($_GET['id']) || empty($_GET['id'])) {
                    echo '<div class="alert alert-danger">Müşteri ID belirtilmemiş!</div>';
                    exit;
                }

                $musteriId = intval($_GET['id']);

                // Müşteri bilgilerini getir
                $sorgu = $db->prepare("SELECT * FROM tbl_musteri WHERE musteriId = ?");
                $sorgu->execute([$musteriId]);
                $musteri = $sorgu->fetch(PDO::FETCH_ASSOC);

                if(!$musteri) {
                    echo '<div class="alert alert-danger">Müşteri bulunamadı!</div>';
                    exit;
                }

                // Müşteri istatistiklerini getir
                $istatistikSorgu = $db->prepare("
                    SELECT 
                        COUNT(z.alisId) as toplamAlim,
                        COALESCE(SUM(z.miktar), 0) as toplamMiktar,
                        COALESCE(SUM(z.toplamTutar), 0) as toplamTutar,
                        COALESCE(SUM(CASE WHEN z.odemeDurumu IN ('odenmedi', 'kismi_odendi') THEN z.toplamTutar ELSE 0 END), 0) as bekleyenTutar
                    FROM tbl_zeytin_alis z 
                    WHERE z.musteriId = ? AND z.durum = 1
                ");
                $istatistikSorgu->execute([$musteriId]);
                $istatistik = $istatistikSorgu->fetch(PDO::FETCH_ASSOC);
                ?>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="bx bx-edit me-2"></i>Müşteri Düzenle: <?php echo htmlspecialchars($musteri['adSoyad']); ?>
                                </h5>
                                <a href="musteriList.php" class="btn btn-light">
                                    <i class="bx bx-arrow-back me-1"></i> Listeye Dön
                                </a>
                            </div>
                            <div class="card-body p-4">

                                <!-- Müşteri İstatistikleri -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card customer-info-card">
                                            <div class="card-body">
                                                <div class="row text-center">
                                                    <div class="col-md-3 border-end">
                                                        <h4 class="text-primary mb-1"><?php echo $istatistik['toplamAlim']; ?></h4>
                                                        <small class="text-muted">Toplam Alım</small>
                                                    </div>
                                                    <div class="col-md-3 border-end">
                                                        <h4 class="text-success mb-1"><?php echo number_format($istatistik['toplamMiktar'], 2); ?> kg</h4>
                                                        <small class="text-muted">Toplam Miktar</small>
                                                    </div>
                                                    <div class="col-md-3 border-end">
                                                        <h4 class="text-info mb-1"><?php echo number_format($istatistik['toplamTutar'], 0); ?> TL</h4>
                                                        <small class="text-muted">Toplam Tutar</small>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <h4 class="text-danger mb-1"><?php echo number_format($istatistik['bekleyenTutar'], 0); ?> TL</h4>
                                                        <small class="text-muted">Bekleyen Ödeme</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form action="islem.php" method="post" id="musteriDuzenleForm">
                                    <input type="hidden" name="musteriId" value="<?php echo $musteriId; ?>">
                                    
                                    <!-- Temel Bilgiler Bölümü -->
                                    <div class="form-section">
                                        <div class="section-title">Temel Bilgiler</div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="adSoyad" class="form-label required-field">Ad Soyad</label>
                                                    <input type="text" class="form-control" id="adSoyad" name="adSoyad" 
                                                           value="<?php echo htmlspecialchars($musteri['adSoyad']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="tcKimlik" class="form-label">TC Kimlik No</label>
                                                    <input type="text" class="form-control" id="tcKimlik" name="tcKimlik" 
                                                           value="<?php echo htmlspecialchars($musteri['tcKimlik']); ?>" maxlength="11">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="musteriTipi" class="form-label">Müşteri Tipi</label>
                                                    <select class="form-control" id="musteriTipi" name="musteriTipi">
                                                        <option value="bireysel" <?php echo $musteri['musteriTipi'] == 'bireysel' ? 'selected' : ''; ?>>Bireysel</option>
                                                        <option value="kurumsal" <?php echo $musteri['musteriTipi'] == 'kurumsal' ? 'selected' : ''; ?>>Kurumsal</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="durum" class="form-label">Durum</label>
                                                    <select class="form-control" id="durum" name="durum">
                                                        <option value="1" <?php echo $musteri['durum'] == 1 ? 'selected' : ''; ?>>Aktif</option>
                                                        <option value="0" <?php echo $musteri['durum'] == 0 ? 'selected' : ''; ?>>Pasif</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- İletişim Bilgileri Bölümü -->
                                    <div class="form-section">
                                        <div class="section-title">İletişim Bilgileri</div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label required-field">Telefon</label>
                                                    <input type="text" class="form-control" id="phone" name="phone" 
                                                           value="<?php echo htmlspecialchars($musteri['phone']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">E-posta</label>
                                                    <input type="email" class="form-control" id="email" name="email" 
                                                           value="<?php echo htmlspecialchars($musteri['email']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Adres Bilgileri Bölümü -->
                                    <div class="form-section">
                                        <div class="section-title">Adres Bilgileri</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="adres" class="form-label">Adres</label>
                                                    <textarea class="form-control" id="adres" name="adres" rows="3"><?php echo htmlspecialchars($musteri['adres']); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="il" class="form-label">İl</label>
                                                    <input type="text" class="form-control" id="il" name="il" 
                                                           value="<?php echo htmlspecialchars($musteri['il']); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="ilce" class="form-label">İlçe</label>
                                                    <input type="text" class="form-control" id="ilce" name="ilce" 
                                                           value="<?php echo htmlspecialchars($musteri['ilce']); ?>">
                                                </div>
                                            </div>
                                           
                                        </div>
                                    </div>

                                    <!-- Ek Bilgiler Bölümü -->
                                    <div class="form-section">
                                        <div class="section-title">Ek Bilgiler</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="aciklama" class="form-label">Açıklama</label>
                                                    <textarea class="form-control" id="aciklama" name="aciklama" rows="4"><?php echo htmlspecialchars($musteri['aciklama']); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- İşlem Butonları -->
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex flex-wrap gap-2 justify-content-between">
                                                    <div>
                                                        <button type="submit" name="musteriDuzenle" class="btn btn-success waves-effect btn-label waves-light">
                                                            <i class="bx bx-check label-icon"></i> Değişiklikleri Kaydet
                                                        </button>
                                                        <button type="reset" class="btn btn-secondary waves-effect">
                                                            <i class="bx bx-reset"></i> Sıfırla
                                                        </button>
                                                    </div>
                                                    <div>
                                                        <a href="musteri-listesi.php" class="btn btn-light waves-effect">
                                                            <i class="bx bx-x"></i> İptal
                                                        </a>
                                                        <button type="button" class="btn btn-danger waves-effect" onclick="confirmDelete()">
                                                            <i class="bx bx-trash"></i> Müşteriyi Sil
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php include 'layouts/footer.php'; ?>
    </div>
</div>

<!-- Silme Onay Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Müşteri Silme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bx bx-trash display-4 text-danger mb-3"></i>
                    <h5>"<?php echo htmlspecialchars($musteri['adSoyad']); ?>" adlı müşteriyi silmek istediğinizden emin misiniz?</h5>
                    <p class="text-muted">Bu işlem geri alınamaz! Müşteriye ait tüm bilgiler silinecektir.</p>
                    
                    <?php if($istatistik['toplamAlim'] > 0): ?>
                    <div class="alert alert-warning">
                        <i class="bx bx-error"></i> Bu müşterinin <strong><?php echo $istatistik['toplamAlim']; ?> adet</strong> zeytin alım kaydı bulunmaktadır. 
                        Müşteriyi silemezsiniz, ancak durumunu pasif yapabilirsiniz.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <?php if($istatistik['toplamAlim'] == 0): ?>
                <a href="islem.php?musteriSil=<?php echo $musteriId; ?>" class="btn btn-danger">Evet, Sil</a>
                <?php else: ?>
                <button type="button" class="btn btn-warning" onclick="makeInactive()">Durumu Pasif Yap</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/vendor-scripts.php'; ?>

<!-- Form Mask -->
<script src="assets/libs/imask/imask.min.js"></script>

<script>
// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Telefon numarası maskesi
    var phoneMask = IMask(
        document.getElementById('phone'),
        {
            mask: '+90 (000) 000 00 00'
        }
    );

    // TC Kimlik maskesi
    var tcMask = IMask(
        document.getElementById('tcKimlik'),
        {
            mask: '00000000000'
        }
    );

    // Posta kodu maskesi
    var postaKoduMask = IMask(
        document.getElementById('postaKodu'),
        {
            mask: '00000'
        }
    );

    // Form doğrulama
    document.getElementById('musteriDuzenleForm').addEventListener('submit', function(e) {
        let isValid = true;
        const requiredFields = this.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            showAlert('Lütfen zorunlu alanları doldurunuz!', 'danger');
        }
    });

    // Input değişikliklerinde hata durumunu temizle
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
});

// Silme onayı
function confirmDelete() {
    var modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
}

// Durumu pasif yap
function makeInactive() {
    document.getElementById('durum').value = '0';
    document.getElementById('musteriDuzenleForm').submit();
}

// Alert gösterimi
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.page-content .container-fluid').prepend(alertDiv);
    
    // 5 saniye sonra alert'i kaldır
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Form sıfırlama işlemi
document.querySelector('button[type="reset"]').addEventListener('click', function() {
    if(confirm('Formu sıfırlamak istediğinizden emin misiniz? Tüm değişiklikler kaybolacak!')) {
        // Orijinal değerlere dön
        setTimeout(() => {
            document.getElementById('adSoyad').value = '<?php echo htmlspecialchars($musteri['adSoyad']); ?>';
            document.getElementById('tcKimlik').value = '<?php echo htmlspecialchars($musteri['tcKimlik']); ?>';
            document.getElementById('musteriTipi').value = '<?php echo $musteri['musteriTipi']; ?>';
            document.getElementById('durum').value = '<?php echo $musteri['durum']; ?>';
            document.getElementById('phone').value = '<?php echo htmlspecialchars($musteri['phone']); ?>';
            document.getElementById('email').value = '<?php echo htmlspecialchars($musteri['email']); ?>';
            document.getElementById('adres').value = '<?php echo htmlspecialchars($musteri['adres']); ?>';
            document.getElementById('il').value = '<?php echo htmlspecialchars($musteri['il']); ?>';
            document.getElementById('ilce').value = '<?php echo htmlspecialchars($musteri['ilce']); ?>';
            document.getElementById('postaKodu').value = '<?php echo htmlspecialchars($musteri['postaKodu']); ?>';
            document.getElementById('aciklama').value = '<?php echo htmlspecialchars($musteri['aciklama']); ?>';
            
            showAlert('Form orijinal değerlere sıfırlandı!', 'info');
        }, 100);
    }
});
</script>

</body>
</html>