<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <title>Zeytin Yönetim Programı Mühendislik Otomasyon - Müşteri Kaydı</title>
    <?php include 'layouts/head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <?php include 'layouts/head-style.php'; ?>
    
    <style>
        .required-field::after {
            content: " *";
            color: red;
        }
        .form-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
        }
    </style>
</head>

<?php include 'layouts/body.php'; ?>

<!-- Begin page -->
<div id="layout-wrapper">

    <?php include 'layouts/menu.php'; ?>

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <!-- start page title -->
                <?php
                $maintitle = "Müşteri Yönetimi";
                $title = "Yeni Müşteri Ekle";
                ?>
                <?php include 'layouts/breadcrumb.php'; ?>
                <!-- end page title -->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">Müşteri Bilgileri</h5>
                            </div>
                            <div class="card-body p-4">

                                <form action="islem.php" method="post">
                                    <div class="form-section">
                                        <div class="section-title">Temel Bilgiler</div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="adSoyad" class="form-label required-field">Ad Soyad</label>
                                                    <input class="form-control" type="text" name="adSoyad" id="adSoyad" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label required-field">Telefon</label>
                                                    <input class="form-control" type="text" name="phone" id="phone" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">E-posta</label>
                                                    <input type="email" class="form-control" name="email">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="tcKimlik" class="form-label">TC Kimlik No</label>
                                                    <input type="text" class="form-control" name="tcKimlik" maxlength="11">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-section">
                                        <div class="section-title">Adres Bilgileri</div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="adres" class="form-label">Adres</label>
                                                    <textarea class="form-control" name="adres" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="il" class="form-label">İl</label>
                                                            <input type="text" class="form-control" name="il">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="ilce" class="form-label">İlçe</label>
                                                            <input type="text" class="form-control" name="ilce">
                                                        </div>
                                                    </div>
                                                </div>
                                               
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-section">
                                        <div class="section-title">Ek Bilgiler</div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="aciklama" class="form-label">Açıklama</label>
                                                    <textarea class="form-control" name="aciklama" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                               
                                                <div class="mb-3">
                                                    <label for="durum" class="form-label">Durum</label>                                                    
                                                    <select class="form-control" name="durum" >
                                                        <option value="1">Aktif</option>
                                                        <option value="0">Pasif</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="submit" name="musteriEkle" class="btn btn-success waves-effect btn-label waves-light">
                                            <i class="bx bx-save label-icon"></i> Müşteriyi Kaydet
                                        </button>
                                        <button type="reset" class="btn btn-secondary waves-effect">
                                            Formu Temizle
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <?php include 'layouts/footer.php'; ?>
    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->

<?php //include 'layouts/right-sidebar.php'; ?>

<?php include 'layouts/vendor-scripts.php'; ?>
<!-- form mask -->
<script src="assets/libs/imask/imask.min.js"></script>

<!-- form mask init -->
<script src="assets/js/pages/form-mask.init.js"></script>

<script>
    // Telefon numarası formatlama
    var phoneMask = IMask(
        document.getElementById('phone'),
        {
            mask: '+90 (000) 000 00 00'
        }
    );
    
    // TC Kimlik numarası formatlama
    var tcMask = IMask(
        document.querySelector('input[name="tcKimlik"]'),
        {
            mask: '00000000000'
        }
    );
    
    // Posta kodu formatlama
    var postaKoduMask = IMask(
        document.querySelector('input[name="postaKodu"]'),
        {
            mask: '00000'
        }
    );
</script>

</body>
</html>