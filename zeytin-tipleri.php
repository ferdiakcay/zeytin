<?php
include 'layouts/session.php';
include 'layouts/head-main.php';
?>

<head>
    <title>Atak Elektrik - Zeytin Tipleri</title>
    <?php include 'layouts/head.php'; ?>
    <?php include 'layouts/head-style.php'; ?>
    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                $title = "Zeytin Tipleri";
                ?>
                <?php include 'layouts/breadcrumb.php'; ?>
                <!-- end page title -->

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="card-title mb-0">Zeytin Tipleri</h5>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#tipEkleModal">
                                            <i class="bx bx-plus me-1"></i> Yeni Tip Ekle
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">

                                <table id="tiplerTable" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tip Adı</th>
                                            <th>Tür</th>
                                            <th>Birim Fiyat (TL)</th>
                                            <th>Birim</th>
                                            <th>Durum</th>
                                            <th>Oluşturulma Tarihi</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $tipSorgu = $db->prepare("
                                            SELECT t.*, zt.turAdi 
                                            FROM tbl_zeytin_tipleri t 
                                            LEFT JOIN tbl_zeytin_turleri zt ON t.turId = zt.turId 
                                            ORDER BY zt.turAdi, t.tipAdi
                                        ");
                                        $tipSorgu->execute();
                                        $sira = 1;
                                        
                                        while($tip = $tipSorgu->fetch(PDO::FETCH_ASSOC)) {
                                            $durumBadge = $tip['durum'] == 1 ? 
                                                '<span class="badge bg-success">Aktif</span>' : 
                                                '<span class="badge bg-danger">Pasif</span>';
                                            
                                            echo '<tr>';
                                            echo '<td>' . $sira . '</td>';
                                            echo '<td>' . htmlspecialchars($tip['tipAdi']) . '</td>';
                                            echo '<td>' . htmlspecialchars($tip['turAdi']) . '</td>';
                                            echo '<td>' . number_format($tip['birimFiyat'], 2) . ' TL</td>';
                                            echo '<td>' . htmlspecialchars($tip['birim']) . '</td>';
                                            echo '<td>' . $durumBadge . '</td>';
                                            echo '<td>' . date('d.m.Y H:i', strtotime($tip['created_at'])) . '</td>';
                                            echo '<td>';
                                            echo '<button type="button" class="btn btn-sm btn-outline-primary edit-tip" 
                                                    data-tipid="' . $tip['tipId'] . '" 
                                                    data-tipadi="' . htmlspecialchars($tip['tipAdi']) . '" 
                                                    data-turid="' . $tip['turId'] . '" 
                                                    data-birimfiyat="' . $tip['birimFiyat'] . '" 
                                                    data-birim="' . htmlspecialchars($tip['birim']) . '" 
                                                    data-durum="' . $tip['durum'] . '">
                                                    <i class="bx bx-edit"></i> Düzenle
                                                  </button>';
                                            echo '<button type="button" class="btn btn-sm btn-outline-danger ms-1 delete-tip" 
                                                    data-tipid="' . $tip['tipId'] . '" 
                                                    data-tipadi="' . htmlspecialchars($tip['tipAdi']) . '">
                                                    <i class="bx bx-trash"></i> Sil
                                                  </button>';
                                            echo '</td>';
                                            echo '</tr>';
                                            $sira++;
                                        }
                                        ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php include 'layouts/footer.php'; ?>
    </div>
</div>

<!-- Tip Ekle Modal -->
<div class="modal fade" id="tipEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Zeytin Tipi Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tipEkleForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tür Seçin <span class="text-danger">*</span></label>
                        <select class="form-control select2-tur" name="turId" id="turId" required>
                            <option value="">Tür Seçin</option>
                            <?php
                            $turSorgu = $db->prepare("SELECT * FROM tbl_zeytin_turleri WHERE durum = 1 ORDER BY turAdi");
                            $turSorgu->execute();
                            while($tur = $turSorgu->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="'.$tur['turId'].'">'.$tur['turAdi'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tip Adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tipAdi" id="tipAdi" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Birim Fiyat (TL) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="birimFiyat" id="birimFiyat" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Birim <span class="text-danger">*</span></label>
                                <select class="form-control" name="birim" id="birim" required>
                                    <option value="">Birim Seçin</option>
                                    <option value="kg">kg</option>
                                    <option value="ton">ton</option>
                                    <option value="litre">litre</option>
                                    <option value="kutu">kutu</option>
                                    <option value="paket">paket</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select class="form-control" name="durum">
                            <option value="1">Aktif</option>
                            <option value="0">Pasif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-save me-1"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tip Düzenle Modal -->
<div class="modal fade" id="tipDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Zeytin Tipi Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tipDuzenleForm">
                <input type="hidden" name="tipId" id="editTipId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tür Seçin <span class="text-danger">*</span></label>
                        <select class="form-control select2-tur-edit" name="turId" id="editTurId" required>
                            <option value="">Tür Seçin</option>
                            <?php
                            $turSorgu = $db->prepare("SELECT * FROM tbl_zeytin_turleri WHERE durum = 1 ORDER BY turAdi");
                            $turSorgu->execute();
                            while($tur = $turSorgu->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="'.$tur['turId'].'">'.$tur['turAdi'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tip Adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tipAdi" id="editTipAdi" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Birim Fiyat (TL) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="birimFiyat" id="editBirimFiyat" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Birim <span class="text-danger">*</span></label>
                                <select class="form-control" name="birim" id="editBirim" required>
                                    <option value="">Birim Seçin</option>
                                    <option value="kg">kg</option>
                                    <option value="ton">ton</option>
                                    <option value="litre">litre</option>
                                    <option value="kutu">kutu</option>
                                    <option value="paket">paket</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select class="form-control" name="durum" id="editDurum">
                            <option value="1">Aktif</option>
                            <option value="0">Pasif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-edit me-1"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'layouts/vendor-scripts.php'; ?>
<script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // DataTable'ı başlat
    $('#tiplerTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json'
        },
        order: [[0, 'asc']]
    });

    // Select2'yi başlat
    $('.select2-tur').select2({
        placeholder: "Tür seçin...",
        dropdownParent: $('#tipEkleModal')
    });

    $('.select2-tur-edit').select2({
        placeholder: "Tür seçin...",
        dropdownParent: $('#tipDuzenleModal')
    });

    // Tip ekleme formu
    $('#tipEkleForm').on('submit', function(e) {
        e.preventDefault();
        addTip();
    });

    // Tip düzenleme butonları
    $(document).on('click', '.edit-tip', function() {
        var tipId = $(this).data('tipid');
        var tipAdi = $(this).data('tipadi');
        var turId = $(this).data('turid');
        var birimFiyat = $(this).data('birimfiyat');
        var birim = $(this).data('birim');
        var durum = $(this).data('durum');
        
        $('#editTipId').val(tipId);
        $('#editTipAdi').val(tipAdi);
        $('#editTurId').val(turId).trigger('change');
        $('#editBirimFiyat').val(birimFiyat);
        $('#editBirim').val(birim);
        $('#editDurum').val(durum);
        
        $('#tipDuzenleModal').modal('show');
    });

    // Tip düzenleme formu
    $('#tipDuzenleForm').on('submit', function(e) {
        e.preventDefault();
        updateTip();
    });

    // Tip silme butonları
    $(document).on('click', '.delete-tip', function() {
        var tipId = $(this).data('tipid');
        var tipAdi = $(this).data('tipadi');
        
        if(confirm(tipAdi + ' tipini silmek istediğinize emin misiniz?\n\nBu işlem geri alınamaz!')) {
            deleteTip(tipId);
        }
    });
});

// Yeni tip ekleme
function addTip() {
    var formData = {
        turId: $('#turId').val(),
        tipAdi: $('#tipAdi').val().trim(),
        birimFiyat: $('#birimFiyat').val(),
        birim: $('#birim').val(),
        durum: $('select[name="durum"]').val()
    };

    // Validasyon
    if (!formData.turId) {
        alert('Lütfen tür seçiniz!');
        return;
    }

    if (!formData.tipAdi) {
        alert('Lütfen tip adı giriniz!');
        return;
    }

    if (!formData.birimFiyat || formData.birimFiyat <= 0) {
        alert('Lütfen geçerli bir birim fiyat giriniz!');
        return;
    }

    if (!formData.birim) {
        alert('Lütfen birim seçiniz!');
        return;
    }

    $.ajax({
        url: 'ajax_zeytin.php',
        type: 'POST',
        data: {islem: 'tipEkle', ...formData},
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                alert('Tip başarıyla eklendi!');
                $('#tipEkleModal').modal('hide');
                $('#tipEkleForm')[0].reset();
                $('.select2-tur').val('').trigger('change');
                location.reload();
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function() {
            alert('Tip eklenirken bir hata oluştu!');
        }
    });
}

// Tip güncelleme
function updateTip() {
    var formData = {
        tipId: $('#editTipId').val(),
        turId: $('#editTurId').val(),
        tipAdi: $('#editTipAdi').val().trim(),
        birimFiyat: $('#editBirimFiyat').val(),
        birim: $('#editBirim').val(),
        durum: $('#editDurum').val()
    };

    // Validasyon
    if (!formData.turId) {
        alert('Lütfen tür seçiniz!');
        return;
    }

    if (!formData.tipAdi) {
        alert('Lütfen tip adı giriniz!');
        return;
    }

    if (!formData.birimFiyat || formData.birimFiyat <= 0) {
        alert('Lütfen geçerli bir birim fiyat giriniz!');
        return;
    }

    if (!formData.birim) {
        alert('Lütfen birim seçiniz!');
        return;
    }

    $.ajax({
        url: 'ajax_zeytin.php',
        type: 'POST',
        data: {islem: 'tipGuncelle', ...formData},
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                alert('Tip başarıyla güncellendi!');
                $('#tipDuzenleModal').modal('hide');
                location.reload();
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function() {
            alert('Tip güncellenirken bir hata oluştu!');
        }
    });
}

// Tip silme
function deleteTip(tipId) {
    $.ajax({
        url: 'ajax_zeytin.php',
        type: 'POST',
        data: {islem: 'tipSil', tipId: tipId},
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                alert('Tip başarıyla silindi!');
                location.reload();
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function() {
            alert('Tip silinirken bir hata oluştu!');
        }
    });
}
</script>
</body>
</html>