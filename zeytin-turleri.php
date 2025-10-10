<?php
include 'layouts/session.php';
include 'layouts/head-main.php';
?>

<head>
    <title>Zeytin Yönetimi - Zeytin Türleri</title>
    <?php include 'layouts/head.php'; ?>
    <?php include 'layouts/head-style.php'; ?>
    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
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
                $title = "Zeytin Türleri";
                ?>
                <?php include 'layouts/breadcrumb.php'; ?>
                <!-- end page title -->

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="card-title mb-0">Zeytin Türleri</h5>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#turEkleModal">
                                            <i class="bx bx-plus me-1"></i> Yeni Tür Ekle
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">

                                <table id="turlerTable" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tür Adı</th>
                                            <th>Durum</th>
                                            <th>Oluşturulma Tarihi</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $turSorgu = $db->prepare("SELECT * FROM tbl_zeytin_turleri ORDER BY turAdi");
                                        $turSorgu->execute();
                                        $sira = 1;
                                        
                                        while($tur = $turSorgu->fetch(PDO::FETCH_ASSOC)) {
                                            $durumBadge = $tur['durum'] == 1 ? 
                                                '<span class="badge bg-success">Aktif</span>' : 
                                                '<span class="badge bg-danger">Pasif</span>';
                                            
                                            echo '<tr>';
                                            echo '<td>' . $sira . '</td>';
                                            echo '<td>' . htmlspecialchars($tur['turAdi']) . '</td>';
                                            echo '<td>' . $durumBadge . '</td>';
                                            echo '<td>' . date('d.m.Y H:i', strtotime($tur['created_at'])) . '</td>';
                                            echo '<td>';
                                            echo '<button type="button" class="btn btn-sm btn-outline-primary edit-tur" data-turid="' . $tur['turId'] . '" data-turadi="' . htmlspecialchars($tur['turAdi']) . '" data-durum="' . $tur['durum'] . '">
                                                    <i class="bx bx-edit"></i> Düzenle
                                                  </button>';
                                            echo '<button type="button" class="btn btn-sm btn-outline-danger ms-1 delete-tur" data-turid="' . $tur['turId'] . '" data-turadi="' . htmlspecialchars($tur['turAdi']) . '">
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

<!-- Tür Ekle Modal -->
<div class="modal fade" id="turEkleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Zeytin Türü Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="turEkleForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tür Adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="turAdi" id="turAdi" required>
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

<!-- Tür Düzenle Modal -->
<div class="modal fade" id="turDuzenleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Zeytin Türü Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="turDuzenleForm">
                <input type="hidden" name="turId" id="editTurId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tür Adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="turAdi" id="editTurAdi" required>
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

<script>
$(document).ready(function() {
    // DataTable'ı başlat
    $('#turlerTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json'
        },
        order: [[0, 'asc']]
    });

    // Tür ekleme formu
    $('#turEkleForm').on('submit', function(e) {
        e.preventDefault();
        addTur();
    });

    // Tür düzenleme butonları
    $(document).on('click', '.edit-tur', function() {
        var turId = $(this).data('turid');
        var turAdi = $(this).data('turadi');
        var durum = $(this).data('durum');
        
        $('#editTurId').val(turId);
        $('#editTurAdi').val(turAdi);
        $('#editDurum').val(durum);
        
        $('#turDuzenleModal').modal('show');
    });

    // Tür düzenleme formu
    $('#turDuzenleForm').on('submit', function(e) {
        e.preventDefault();
        updateTur();
    });

    // Tür silme butonları
    $(document).on('click', '.delete-tur', function() {
        var turId = $(this).data('turid');
        var turAdi = $(this).data('turadi');
        
        if(confirm(turAdi + ' türünü silmek istediğinize emin misiniz?\n\nBu işlem geri alınamaz!')) {
            deleteTur(turId);
        }
    });
});

// Yeni tür ekleme
function addTur() {
    var formData = {
        turAdi: $('#turAdi').val().trim(),
        durum: $('select[name="durum"]').val()
    };

    if (!formData.turAdi) {
        alert('Lütfen tür adı giriniz!');
        return;
    }

    $.ajax({
        url: 'ajax_zeytin.php',
        type: 'POST',
        data: {islem: 'turEkle', ...formData},
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                alert('Tür başarıyla eklendi!');
                $('#turEkleModal').modal('hide');
                $('#turEkleForm')[0].reset();
                location.reload(); // Sayfayı yenile
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function() {
            alert('Tür eklenirken bir hata oluştu!');
        }
    });
}

// Tür güncelleme
function updateTur() {
    var formData = {
        turId: $('#editTurId').val(),
        turAdi: $('#editTurAdi').val().trim(),
        durum: $('#editDurum').val()
    };

    if (!formData.turAdi) {
        alert('Lütfen tür adı giriniz!');
        return;
    }

    $.ajax({
        url: 'ajax_zeytin.php',
        type: 'POST',
        data: {islem: 'turGuncelle', ...formData},
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                alert('Tür başarıyla güncellendi!');
                $('#turDuzenleModal').modal('hide');
                location.reload(); // Sayfayı yenile
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function() {
            alert('Tür güncellenirken bir hata oluştu!');
        }
    });
}

// Tür silme
function deleteTur(turId) {
    $.ajax({
        url: 'ajax_zeytin.php',
        type: 'POST',
        data: {islem: 'turSil', turId: turId},
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                alert('Tür başarıyla silindi!');
                location.reload(); // Sayfayı yenile
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function() {
            alert('Tür silinirken bir hata oluştu!');
        }
    });
}
</script>
</body>
</html>