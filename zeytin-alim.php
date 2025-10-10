<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<?php
// Veritabanı bağlantısı ve veri çekme
include 'config.php';

// Müşterileri getir
$musteriler = $db->query("SELECT * FROM tbl_musteri WHERE durum = 1 ORDER BY adSoyad")->fetchAll(PDO::FETCH_ASSOC);

// Zeytin türlerini getir
$turler = $db->query("SELECT * FROM tbl_zeytin_turleri WHERE durum = 1 ORDER BY turAdi")->fetchAll(PDO::FETCH_ASSOC);

// Zeytin tiplerini getir
$tipler = $db->query("SELECT * FROM tbl_zeytin_tipleri WHERE durum = 1 ORDER BY turId, tipAdi")->fetchAll(PDO::FETCH_ASSOC);

// Telefon formatlama fonksiyonu
function formatPhone($phone) {
    if (empty($phone)) return '';
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($cleanPhone) === 10) {
        return substr($cleanPhone, 0, 3) . ' ' . substr($cleanPhone, 3, 3) . ' ' . substr($cleanPhone, 6, 2) . ' ' . substr($cleanPhone, 8, 2);
    }
    return $phone;
}
?>

<head>
    <title>Atak Elektrik - Zeytin Alım</title>
    <?php include 'layouts/head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <?php include 'layouts/head-style.php'; ?>
    
    <style>
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #495057;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .select2-container--default .select2-selection--single {
            height: 45px;
            border: 1px solid #ced4da;
            border-radius: 6px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 43px;
            padding-left: 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 43px;
        }
        .quick-add-btn {
            margin-bottom: 15px;
        }
        .customer-info-card {
            border-left: 4px solid #28a745;
            background: white;
            display: none;
        }
        .customer-info-card.show {
            display: block;
        }
        .phone-input {
            direction: ltr;
            text-align: left;
        }
        .urun-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .urun-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        .urun-sira {
            background: #007bff;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            font-size: 16px;
        }
        .btn-remove-urun {
            background: #dc3545;
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        .btn-remove-urun:hover {
            background: #c82333;
            transform: scale(1.1);
        }
        .toplam-odeme {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border: 2px solid #2196f3;
            border-radius: 10px;
            padding: 20px;
            margin-top: 25px;
        }
        .toplam-tutar {
            font-size: 28px;
            font-weight: bold;
            color: #1976d2;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .form-control {
            border-radius: 6px;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .btn {
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 500;
        }
        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
        }
    </style>
</head>

<?php include 'layouts/body.php'; ?>

<div id="layout-wrapper">
    <?php include 'layouts/menu.php'; ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <?php
                $maintitle = "Zeytin Yönetimi";
                $title = "Zeytin Alım İşlemi";
                ?>
                <?php include 'layouts/breadcrumb.php'; ?>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0"><i class="bx bx-shopping-bag me-2"></i>Zeytin Alım Formu - Çoklu Ürün</h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="islem.php" method="post" id="zeytinAlimForm">
                                    <input type="hidden" name="urunler" id="urunlerData">
                                    
                                    <!-- Müşteri Bilgileri Bölümü -->
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="bx bx-user me-2"></i>Müşteri Bilgileri
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-outline-success quick-add-btn" data-bs-toggle="modal" data-bs-target="#hizliMusteriEkleModal">
                                                    <i class="bx bx-plus-circle me-1"></i> Hızlı Müşteri Ekle
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Müşteri Seçin <span class="text-danger">*</span></label>
                                                    <select class="form-control select2-musteri" name="musteriId" id="musteriSelect" required>
                                                        <option value="">Müşteri seçin veya arama yapın...</option>
                                                        <?php foreach($musteriler as $musteri): ?>
                                                        <option value="<?= $musteri['musteriId'] ?>" 
                                                                data-phone="<?= $musteri['phone'] ?>" 
                                                                data-email="<?= $musteri['email'] ?>">
                                                            <?= htmlspecialchars($musteri['adSoyad']) ?> - <?= formatPhone($musteri['phone']) ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Alım Tarihi <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" name="alisTarihi" value="<?= date('Y-m-d') ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Müşteri Bilgi Kartı -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card customer-info-card" id="customerInfoCard">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <small class="text-muted">Telefon:</small>
                                                                <div id="customerPhone" class="fw-semibold">-</div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <small class="text-muted">E-posta:</small>
                                                                <div id="customerEmail" class="fw-semibold">-</div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <small class="text-muted">Toplam Alım:</small>
                                                                <div id="customerTotal" class="fw-semibold">-</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Zeytin Ürünleri Bölümü -->
                                    <div class="form-section">
                                        <div class="section-title d-flex justify-content-between align-items-center">
                                            <span><i class="bx bx-package me-2"></i>Zeytin Ürünleri</span>
                                            <button type="button" class="btn btn-success" onclick="addUrun()">
                                                <i class="bx bx-plus me-1"></i> Ürün Ekle
                                            </button>
                                        </div>
                                        
                                        <!-- Ürün Listesi -->
                                        <div id="urunListesi">
                                            <!-- Ürünler buraya dinamik olarak eklenecek -->
                                        </div>
                                        
                                        <!-- Toplam Ödeme -->
                                        <div class="toplam-odeme">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <h6 class="mb-2">TOPLAM ÖDEME</h6>
                                                    <div class="toplam-tutar" id="genelToplamTutar">0.00 TL</div>
                                                </div>
                                                <div class="col-md-6 text-end">
                                                    <div class="mb-2">
                                                        <strong>Toplam Miktar:</strong> 
                                                        <span id="genelToplamMiktar" class="fw-bold">0.00</span> kg
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Ürün Sayısı:</strong> 
                                                        <span id="urunSayisi" class="fw-bold">0</span> adet
                                                    </div>
                                                    <div>
                                                        <strong>Ortalama Fiyat:</strong> 
                                                        <span id="ortalamaFiyat" class="fw-bold">0.00</span> TL/kg
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ödeme Bilgileri Bölümü -->
                                    <div class="form-section">
                                        <div class="section-title">
                                            <i class="bx bx-credit-card me-2"></i>Ödeme Bilgileri
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Ödeme Durumu</label>
                                                    <select class="form-control" name="odemeDurumu" id="odemeDurumu">
                                                        <option value="odenmedi">Ödenmedi</option>
                                                        <option value="kismi_odendi">Kısmen Ödendi</option>
                                                        <option value="odenmis">Ödendi</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Açıklama</label>
                                                    <textarea class="form-control" name="aciklama" rows="3" placeholder="Alım ile ilgili notlar..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Form Butonları -->
                                    <div class="d-flex flex-wrap gap-3 mt-4">
                                        <button type="submit" name="zeytinAlimEkle" class="btn btn-success waves-effect btn-label waves-light">
                                            <i class="bx bx-save label-icon"></i> Tüm Alımları Kaydet
                                        </button>
                                        <button type="reset" class="btn btn-secondary waves-effect" onclick="resetForm()">
                                            <i class="bx bx-reset me-1"></i> Formu Temizle
                                        </button>
                                        <a href="alimList.php" class="btn btn-outline-primary waves-effect">
                                            <i class="bx bx-list-ul me-1"></i> Alım Listesi
                                        </a>
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

<!-- Hızlı Müşteri Ekleme Modal -->
<div class="modal fade" id="hizliMusteriEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hızlı Müşteri Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="hizliMusteriForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="adSoyad" id="hizliAdSoyad" required placeholder="Müşteri adı ve soyadı">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control phone-input" name="phone" id="hizliPhone" placeholder="5xx xxx xx xx" required>
                        <div class="form-text">10 haneli telefon numarası giriniz (5xxxxxxxxx)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-posta</label>
                        <input type="email" class="form-control" name="email" id="hizliEmail" placeholder="musteri@ornek.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adres</label>
                        <textarea class="form-control" name="adres" id="hizliAdres" rows="2" placeholder="Müşteri adresi"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-plus me-1"></i> Hızlı Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'layouts/vendor-scripts.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Global değişkenler
const zeytinTipleri = <?= json_encode($tipler, JSON_UNESCAPED_UNICODE) ?>;
let urunSayac = 0;

$(document).ready(function() {
    // Select2 başlatma
    initSelect2();
    
    // Event listener'ları başlat
    initEventListeners();
    
    // İlk ürünü ekle
    addUrun();
});

// Select2 başlatma
function initSelect2() {
    $('.select2-musteri').select2({
        placeholder: "Müşteri seçin veya arama yapın...",
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Müşteri bulunamadı. <button type='button' class='btn btn-sm btn-outline-success ms-2' onclick='openQuickAdd()'>Yeni Müşteri Ekle</button>";
            }
        },
        escapeMarkup: function(markup) {
            return markup;
        }
    });
}

// Event listener'ları başlat
function initEventListeners() {
    // Müşteri seçimi değiştiğinde
    $('#musteriSelect').on('change', function() {
        updateCustomerInfo();
    });

    // Telefon formatlama
    $('#hizliPhone').on('input', function(e) {
        formatPhoneInput(e);
    });

    // Hızlı müşteri ekleme formu
    $('#hizliMusteriForm').on('submit', function(e) {
        e.preventDefault();
        addQuickCustomer();
    });

    // Dinamik event listener'lar
    $(document).on('change', '.tip-select', function() {
        const urunId = $(this).closest('.urun-item').attr('id');
        updateUrunFiyat(urunId);
    });

    $(document).on('input', '.miktar-input', function() {
        const urunId = $(this).closest('.urun-item').attr('id');
        calculateUrunToplam(urunId);
    });
}

// Müşteri bilgilerini güncelle
function updateCustomerInfo() {
    const selectedOption = $('#musteriSelect').find('option:selected');
    const phone = selectedOption.data('phone');
    const email = selectedOption.data('email');
    const musteriId = $('#musteriSelect').val();

    if (musteriId) {
        $('#customerPhone').text(formatPhoneDisplay(phone));
        $('#customerEmail').text(email || '-');
        $('#customerInfoCard').addClass('show');
    } else {
        $('#customerInfoCard').removeClass('show');
    }
}

// Ürün ekleme fonksiyonu
function addUrun() {
    urunSayac++;
    const urunId = 'urun_' + urunSayac;
    
    const urunHTML = `
    <div class="urun-item" id="${urunId}">
        <div class="urun-header">
            <div class="d-flex align-items-center">
                <div class="urun-sira">${urunSayac}</div>
                <h6 class="mb-0 text-primary">Ürün ${urunSayac}</h6>
            </div>
            ${urunSayac > 1 ? `
            <button type="button" class="btn-remove-urun" onclick="removeUrun('${urunId}')" title="Ürünü Sil">
                <i class="bx bx-trash"></i>
            </button>` : ''}
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Zeytin Türü <span class="text-danger">*</span></label>
                    <select class="form-control tur-select" name="turId[]" onchange="loadTips(this, '${urunId}')" required>
                        <option value="">Tür Seçin</option>
                        <?php foreach($turler as $tur): ?>
                        <option value="<?= $tur['turId'] ?>"><?= htmlspecialchars($tur['turAdi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Zeytin Tipi <span class="text-danger">*</span></label>
                    <select class="form-control tip-select" name="tipId[]" id="tipSelect_${urunId}" required disabled>
                        <option value="">Önce tür seçin</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Miktar <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" class="form-control miktar-input" name="miktar[]" 
                           placeholder="0.00" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Birim</label>
                    <input type="text" class="form-control birim-input" id="birim_${urunId}" readonly>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Birim Fiyat (TL)</label>
                    <input type="number" step="0.01" class="form-control fiyat-input" id="fiyat_${urunId}" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">Toplam Tutar (TL)</label>
                    <input type="number" step="0.01" class="form-control toplam-input" id="toplam_${urunId}" readonly>
                    <div class="form-text text-success fw-semibold" id="tutarYaziyla_${urunId}"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Ürün Açıklaması</label>
                    <input type="text" class="form-control" name="urunAciklama[]" placeholder="Bu ürün için not...">
                </div>
            </div>
        </div>
    </div>
    `;
    
    $('#urunListesi').append(urunHTML);
    updateUrunSayisi();
}

// Ürün silme fonksiyonu
function removeUrun(urunId) {
    if ($('.urun-item').length <= 1) {
        showAlert('En az bir ürün eklenmiş olmalıdır!', 'warning');
        return;
    }
    
    if (confirm('Bu ürünü silmek istediğinizden emin misiniz?')) {
        $('#' + urunId).remove();
        updateUrunSayisi();
        updateSiraNumaralari();
        calculateGenelToplam();
        showAlert('Ürün başarıyla silindi!', 'success');
    }
}

// Sıra numaralarını güncelle
function updateSiraNumaralari() {
    $('.urun-item').each(function(index) {
        const siraNo = index + 1;
        $(this).find('.urun-sira').text(siraNo);
        $(this).find('h6').text('Ürün ' + siraNo);
    });
}

// Türlere göre tipleri yükle
function loadTips(selectElement, urunId) {
    const turId = $(selectElement).val();
    const tipSelect = $('#tipSelect_' + urunId);
    
    if (!turId) {
        tipSelect.html('<option value="">Önce tür seçin</option>').prop('disabled', true);
        resetUrunFields(urunId);
        return;
    }

    // Tür ID'sine göre tipleri filtrele
    const filteredTipler = zeytinTipleri.filter(tip => tip.turId == turId);
    
    let options = '<option value="">Tip Seçin</option>';
    
    if (filteredTipler.length > 0) {
        filteredTipler.forEach(tip => {
            const fiyat = parseFloat(tip.birimFiyat).toFixed(2);
            options += `<option value="${tip.tipId}" data-birim="${tip.birim}" data-fiyat="${fiyat}">
                ${tip.tipAdi} - ${fiyat} TL/${tip.birim}
            </option>`;
        });
        tipSelect.html(options).prop('disabled', false);
    } else {
        tipSelect.html('<option value="">Bu türe ait tip bulunamadı</option>').prop('disabled', true);
    }
    
    resetUrunFields(urunId);
}

// Ürün fiyatını güncelle
function updateUrunFiyat(urunId) {
    const tipSelect = $('#tipSelect_' + urunId);
    const selectedOption = tipSelect.find('option:selected');
    const birim = selectedOption.data('birim');
    const fiyat = selectedOption.data('fiyat');
    
    if (birim && fiyat) {
        $('#birim_' + urunId).val(birim);
        $('#fiyat_' + urunId).val(fiyat);
        calculateUrunToplam(urunId);
    } else {
        resetUrunFields(urunId);
    }
}

// Ürün toplamını hesapla
function calculateUrunToplam(urunId) {
    const miktar = parseFloat($('#' + urunId + ' .miktar-input').val()) || 0;
    const fiyat = parseFloat($('#fiyat_' + urunId).val()) || 0;
    const toplam = miktar * fiyat;
    
    $('#toplam_' + urunId).val(toplam.toFixed(2));
    
    if (toplam > 0) {
        $('#tutarYaziyla_' + urunId).text(numberToTurkishWords(toplam) + ' TL');
    } else {
        $('#tutarYaziyla_' + urunId).text('');
    }
    
    calculateGenelToplam();
}

// Genel toplamı hesapla
function calculateGenelToplam() {
    let genelToplam = 0;
    let genelMiktar = 0;
    let urunCount = 0;
    
    $('.urun-item').each(function() {
        const toplam = parseFloat($(this).find('.toplam-input').val()) || 0;
        const miktar = parseFloat($(this).find('.miktar-input').val()) || 0;
        
        if (toplam > 0) {
            genelToplam += toplam;
            genelMiktar += miktar;
            urunCount++;
        }
    });
    
    const ortalamaFiyat = genelMiktar > 0 ? (genelToplam / genelMiktar) : 0;
    
    $('#genelToplamTutar').text(genelToplam.toFixed(2) + ' TL');
    $('#genelToplamMiktar').text(genelMiktar.toFixed(2));
    $('#ortalamaFiyat').text(ortalamaFiyat.toFixed(2));
    updateUrunSayisi();
}

// Ürün sayısını güncelle
function updateUrunSayisi() {
    const count = $('.urun-item').length;
    $('#urunSayisi').text(count);
}

// Ürün alanlarını sıfırla
function resetUrunFields(urunId) {
    $('#birim_' + urunId).val('');
    $('#fiyat_' + urunId).val('');
    $('#toplam_' + urunId).val('');
    $('#tutarYaziyla_' + urunId).text('');
    $('#' + urunId + ' .miktar-input').val('');
}

// Sayıyı Türkçe yazıya çevirme
function numberToTurkishWords(number) {
    // Basit versiyon - geliştirilebilir
    return number.toFixed(2).replace('.', ' virgül ');
}

// Form gönderilmeden önce kontrol
$('#zeytinAlimForm').on('submit', function(e) {
    const urunler = [];
    let hasError = false;
    
    // Müşteri kontrolü
    if (!$('#musteriSelect').val()) {
        showAlert('Lütfen bir müşteri seçin!', 'warning');
        e.preventDefault();
        return;
    }
    
    // Ürün kontrolleri
    $('.urun-item').each(function(index) {
        const turId = $(this).find('.tur-select').val();
        const tipId = $(this).find('.tip-select').val();
        const miktar = $(this).find('.miktar-input').val();
        
        if (!turId || !tipId || !miktar || parseFloat(miktar) <= 0) {
            hasError = true;
            showAlert(`Ürün ${index + 1} için tüm alanları doğru şekilde doldurun!`, 'danger');
            return false;
        }
        
        urunler.push({
            turId: turId,
            tipId: tipId,
            miktar: miktar,
            aciklama: $(this).find('input[name="urunAciklama[]"]').val()
        });
    });
    
    if (hasError || urunler.length === 0) {
        e.preventDefault();
        showAlert('Lütfen en az bir geçerli ürün ekleyin!', 'warning');
        return;
    }
    
    // Ürün verilerini hidden input'a ekle
    $('#urunlerData').val(JSON.stringify(urunler));
    showAlert('Form gönderiliyor...', 'info');
});

// Formu sıfırla
function resetForm() {
    if(confirm('Formu sıfırlamak istediğinizden emin misiniz? Tüm ürünler silinecek!')) {
        $('#urunListesi').empty();
        $('#musteriSelect').val('').trigger('change');
        $('#odemeDurumu').val('odenmedi');
        $('textarea[name="aciklama"]').val('');
        $('#customerInfoCard').removeClass('show');
        urunSayac = 0;
        addUrun();
        calculateGenelToplam();
        showAlert('Form sıfırlandı!', 'info');
    }
}

// Hızlı müşteri ekleme
function addQuickCustomer() {
    const formData = {
        adSoyad: $('#hizliAdSoyad').val(),
        phone: $('#hizliPhone').val().replace(/\s/g, ''),
        email: $('#hizliEmail').val(),
        adres: $('#hizliAdres').val()
    };
    
    if (!formData.adSoyad || !formData.phone) {
        showAlert('Ad soyad ve telefon alanları zorunludur!', 'warning');
        return;
    }
    
    if (formData.phone.replace(/\D/g, '').length !== 10) {
        showAlert('Geçerli bir telefon numarası giriniz!', 'warning');
        return;
    }
    
    // AJAX ile müşteri ekleme
    $.ajax({
        url: 'islem.php',
        type: 'POST',
        data: {
            action: 'hizliMusteriEkle',
            ...formData
        },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.success) {
                    $('#hizliMusteriEkleModal').modal('hide');
                    $('#hizliMusteriForm')[0].reset();
                    
                    // Yeni müşteriyi select'e ekle
                    const newOption = new Option(
                        formData.adSoyad + ' - ' + formatPhoneDisplay(formData.phone),
                        result.musteriId,
                        false,
                        true
                    );
                    $('#musteriSelect').append(newOption).trigger('change');
                    
                    showAlert('Müşteri başarıyla eklendi!', 'success');
                } else {
                    showAlert(result.message || 'Müşteri eklenirken hata oluştu!', 'danger');
                }
            } catch (e) {
                showAlert('Sunucu yanıtı işlenirken hata oluştu!', 'danger');
            }
        },
        error: function() {
            showAlert('Sunucu ile bağlantı kurulamadı!', 'danger');
        }
    });
}

// Yardımcı fonksiyonlar
function formatPhoneDisplay(phone) {
    if (!phone) return '-';
    const cleanPhone = phone.replace(/\D/g, '');
    if (cleanPhone.length === 10) {
        return cleanPhone.replace(/(\d{3})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4');
    }
    return phone;
}

function formatPhoneInput(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 10) value = value.substring(0, 10);
    if (value.length > 0) {
        value = value.replace(/(\d{3})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4');
    }
    e.target.value = value;
}

function showAlert(message, type) {
    const alertClass = {
        'success': 'alert-success',
        'danger': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <strong>${message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    $('#zeytinAlimForm').prepend(alertHTML);
    
    // 5 saniye sonra alert'i kaldır
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}

function openQuickAdd() {
    $('#hizliMusteriEkleModal').modal('show');
}
</script>

</body>
</html>