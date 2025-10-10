<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <title>Zeytin Yönetim Programı - Müşteri Listesi</title>
    <?php include 'layouts/head.php'; ?>
    <?php include 'layouts/head-style.php'; ?>
    
    <style>
        .table-actions {
            white-space: nowrap;
            width: 120px;
        }
        .customer-status-active {
            background-color: #e8f5e8;
        }
        .customer-status-inactive {
            background-color: #ffebee;
        }
        .search-box {
            max-width: 300px;
        }
    </style>
</head>

<?php include 'layouts/body.php'; ?>

<div id="layout-wrapper">
    <?php include 'layouts/menu.php'; ?>

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <!-- start page title -->
                <?php
                $maintitle = "Müşteri Yönetimi";
                $title = "Müşteri Listesi";
                ?>
                <?php include 'layouts/breadcrumb.php'; ?>
                <!-- end page title -->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Müşteriler</h5>
                                <div class="d-flex gap-2">
                                    <div class="search-box">
                                        <input type="text" class="form-control" id="searchInput" placeholder="Müşteri ara...">
                                    </div>
                                    <a href="musteriEkle.php" class="btn btn-light">
                                        <i class="bx bx-plus me-1"></i> Yeni Müşteri
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                
                                <!-- Filtreleme Butonları -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary active" data-filter="all">
                                                Tümü <span class="badge bg-primary ms-1" id="countAll">0</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-success" data-filter="active">
                                                Aktif <span class="badge bg-success ms-1" id="countActive">0</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" data-filter="inactive">
                                                Pasif <span class="badge bg-danger ms-1" id="countInactive">0</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-sort me-1"></i> Sırala
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" data-sort="name-asc">Ada göre (A-Z)</a></li>
                                                <li><a class="dropdown-item" href="#" data-sort="name-desc">Ada göre (Z-A)</a></li>
                                                <li><a class="dropdown-item" href="#" data-sort="date-desc">En yeni</a></li>
                                                <li><a class="dropdown-item" href="#" data-sort="date-asc">En eski</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="customersTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">#</th>
                                                <th>Müşteri Bilgileri</th>
                                                <th>İletişim</th>
                                                <th>Adres</th>
                                                <th>Zeytin Bilgileri</th>
                                                <th>Durum</th>
                                                <th width="120">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Müşteri listesini çek
                                            $sorgu = $db->prepare("
                                                SELECT m.*, 
                                                       COUNT(z.alisId) as toplamAlim,
                                                       COALESCE(SUM(z.miktar), 0) as toplamMiktar,
                                                       COALESCE(SUM(z.toplamTutar), 0) as toplamTutar
                                                FROM tbl_musteri m
                                                LEFT JOIN tbl_zeytin_alis z ON m.musteriId = z.musteriId AND z.durum = 1
                                                GROUP BY m.musteriId
                                                ORDER BY m.musteriId DESC
                                            ");
                                            $sorgu->execute();
                                            $say = 1;
                                            
                                            $toplamMusteri = 0;
                                            $aktifMusteri = 0;
                                            $pasifMusteri = 0;
                                            
                                            while($musteri = $sorgu->fetch(PDO::FETCH_ASSOC)) {
                                                $toplamMusteri++;
                                                if($musteri['durum'] == 1) {
                                                    $aktifMusteri++;
                                                } else {
                                                    $pasifMusteri++;
                                                }
                                                
                                                $durumClass = $musteri['durum'] == 1 ? 'customer-status-active' : 'customer-status-inactive';
                                                $durumBadge = $musteri['durum'] == 1 ? 'success' : 'danger';
                                                $durumText = $musteri['durum'] == 1 ? 'Aktif' : 'Pasif';
                                                
                                                // Telefon formatlama
                                                $telefon = $musteri['phone'];
                                                if(strlen($telefon) == 10) {
                                                    $telefon = '0' . substr($telefon, 0, 3) . ' ' . substr($telefon, 3, 3) . ' ' . substr($telefon, 6, 2) . ' ' . substr($telefon, 8, 2);
                                                }
                                                
                                                // Adres kısaltma
                                                $adres = $musteri['adres'];
                                                if(strlen($adres) > 50) {
                                                    $adres = substr($adres, 0, 50) . '...';
                                                }
                                            ?>
                                            <tr class="<?php echo $durumClass; ?>" data-status="<?php echo $musteri['durum']; ?>" data-name="<?php echo htmlspecialchars($musteri['adSoyad']); ?>">
                                                <td><?php echo $say++; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-xs">
                                                                <span class="avatar-title bg-soft-primary rounded-circle text-primary">
                                                                    <?php echo strtoupper(substr($musteri['adSoyad'], 0, 1)); ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-2">
                                                            <h6 class="mb-0"><?php echo htmlspecialchars($musteri['adSoyad']); ?></h6>
                                                            <?php if(!empty($musteri['tcKimlik'])): ?>
                                                            <small class="text-muted">TC: <?php echo $musteri['tcKimlik']; ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if(!empty($musteri['phone'])): ?>
                                                    <div><i class="bx bx-phone text-primary me-1"></i> <?php echo $telefon; ?></div>
                                                    <?php endif; ?>
                                                    <?php if(!empty($musteri['email'])): ?>
                                                    <div><i class="bx bx-envelope text-success me-1"></i> <?php echo htmlspecialchars($musteri['email']); ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if(!empty($musteri['adres'])): ?>
                                                    <div class="text-truncate" title="<?php echo htmlspecialchars($musteri['adres']); ?>">
                                                        <i class="bx bx-map text-warning me-1"></i> <?php echo htmlspecialchars($adres); ?>
                                                    </div>
                                                    <?php endif; ?>
                                                    <?php if(!empty($musteri['il']) || !empty($musteri['ilce'])): ?>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($musteri['il']); ?><?php echo !empty($musteri['ilce']) ? '/' . htmlspecialchars($musteri['ilce']) : ''; ?>
                                                    </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <div class="mb-1">
                                                            <span class="badge bg-soft-primary"><?php echo $musteri['toplamAlim']; ?> alım</span>
                                                        </div>
                                                        <div class="text-muted small">
                                                            <div><?php echo number_format($musteri['toplamMiktar'], 2); ?> kg</div>
                                                            <div><?php echo number_format($musteri['toplamTutar'], 0); ?> TL</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $durumBadge; ?>"><?php echo $durumText; ?></span>
                                                    <div class="text-muted small mt-1">
                                                        <?php echo date('d.m.Y', strtotime($musteri['kayitTarihi'])); ?>
                                                    </div>
                                                </td>
                                                <td class="table-actions">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-primary" 
                                                                data-bs-toggle="tooltip" title="Detay"
                                                                onclick="viewCustomer(<?php echo $musteri['musteriId']; ?>)">
                                                            <i class="bx bx-show"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-warning" 
                                                                data-bs-toggle="tooltip" title="Düzenle"
                                                                onclick="editCustomer(<?php echo $musteri['musteriId']; ?>)">
                                                            <i class="bx bx-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                data-bs-toggle="tooltip" title="Sil"
                                                                onclick="deleteCustomer(<?php echo $musteri['musteriId']; ?>, '<?php echo htmlspecialchars($musteri['adSoyad']); ?>')">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            
                                            <?php if($toplamMusteri == 0): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    <i class="bx bx-user-x display-4 d-block mb-2"></i>
                                                    Henüz kayıtlı müşteri bulunmamaktadır.
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Sayfalama -->
                                <div class="row mt-3">
                                    <div class="col-sm-12 col-md-6">
                                        <div class="dataTables_info" id="datatable_info">
                                            Toplam <strong><?php echo $toplamMusteri; ?></strong> müşteri bulundu
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php include 'layouts/footer.php'; ?>
    </div>
</div>

<!-- Müşteri Detay Modal -->
<div class="modal fade" id="customerDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Müşteri Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="customerDetailContent">
                <!-- Detaylar buraya yüklenecek -->
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/vendor-scripts.php'; ?>

<script>
// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Sayıları güncelle
    document.getElementById('countAll').textContent = '<?php echo $toplamMusteri; ?>';
    document.getElementById('countActive').textContent = '<?php echo $aktifMusteri; ?>';
    document.getElementById('countInactive').textContent = '<?php echo $pasifMusteri; ?>';
    
    // Tooltip'leri etkinleştir
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Filtreleme işlemi
document.querySelectorAll('[data-filter]').forEach(button => {
    button.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');
        
        // Aktif butonu güncelle
        document.querySelectorAll('[data-filter]').forEach(btn => {
            btn.classList.remove('active');
        });
        this.classList.add('active');
        
        // Tabloyu filtrele
        const rows = document.querySelectorAll('#customersTable tbody tr');
        rows.forEach(row => {
            if (filter === 'all') {
                row.style.display = '';
            } else if (filter === 'active') {
                row.style.display = row.getAttribute('data-status') === '1' ? '' : 'none';
            } else if (filter === 'inactive') {
                row.style.display = row.getAttribute('data-status') === '0' ? '' : 'none';
            }
        });
    });
});

// Arama işlemi
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#customersTable tbody tr');
    
    rows.forEach(row => {
        const customerName = row.getAttribute('data-name').toLowerCase();
        if (customerName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Sıralama işlemi
document.querySelectorAll('[data-sort]').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        const sortType = this.getAttribute('data-sort');
        sortTable(sortType);
    });
});

function sortTable(sortType) {
    const tbody = document.querySelector('#customersTable tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        let aValue, bValue;
        
        switch(sortType) {
            case 'name-asc':
                aValue = a.getAttribute('data-name').toLowerCase();
                bValue = b.getAttribute('data-name').toLowerCase();
                return aValue.localeCompare(bValue);
                
            case 'name-desc':
                aValue = a.getAttribute('data-name').toLowerCase();
                bValue = b.getAttribute('data-name').toLowerCase();
                return bValue.localeCompare(aValue);
                
            case 'date-desc':
                aValue = parseInt(a.cells[0].textContent);
                bValue = parseInt(b.cells[0].textContent);
                return aValue - bValue; // ID'ye göre sırala (en yeni üstte)
                
            case 'date-asc':
                aValue = parseInt(a.cells[0].textContent);
                bValue = parseInt(b.cells[0].textContent);
                return bValue - aValue; // ID'ye göre sırala (en eski üstte)
        }
    });
    
    // Sıralanmış satırları tekrar ekle
    rows.forEach(row => tbody.appendChild(row));
}

// Müşteri detayını görüntüle
function viewCustomer(musteriId) {
    fetch(`ajax.php?islem=musteriDetay&musteriId=${musteriId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('customerDetailContent').innerHTML = data;
            var modal = new bootstrap.Modal(document.getElementById('customerDetailModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Hata:', error);
            alert('Müşteri detayları yüklenirken bir hata oluştu.');
        });
}

// Müşteri düzenle
function editCustomer(musteriId) {
    window.location.href = `musteri-duzenle.php?id=${musteriId}`;
}

// Müşteri sil
function deleteCustomer(musteriId, musteriAdi) {
    if(confirm(`"${musteriAdi}" adlı müşteriyi silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz!`)) {
        window.location.href = `islem.php?musteriSil=${musteriId}`;
    }
}
</script>

</body>
</html>