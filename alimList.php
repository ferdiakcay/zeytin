<?php include 'layouts/session.php'; ?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <title>Zeytin Yönetimi - Alım Listesi</title>
    <?php include 'layouts/head.php'; ?>
    <?php include 'layouts/head-style.php'; ?>
    
    <style>
        .table-actions {
            white-space: nowrap;
            width: 120px;
        }
        .search-box {
            max-width: 300px;
        }
        .status-odenmedi { background-color: #ffebee; }
        .status-kismi_odendi { background-color: #fff3e0; }
        .status-odenmis { background-color: #e8f5e8; }
        .print-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        .print-btn:hover {
            background: #218838;
        }
        .fis-small {
            font-size: 11px;
            color: #666;
        }
        .coklu-badge {
            background: #dc3545;
            color: white;
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 10px;
            margin-left: 5px;
        }
        .alis-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .alis-row:hover {
            background-color: #f8f9fa !important;
        }
        .urun-count {
            background: #007bff;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            margin-left: 5px;
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
                $maintitle = "Zeytin Yönetimi";
                $title = "Alım Listesi";
                ?>
                <?php include 'layouts/breadcrumb.php'; ?>
                <!-- end page title -->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Zeytin Alım Kayıtları</h5>
                                <div class="d-flex gap-2">
                                    <div class="search-box">
                                        <input type="text" class="form-control" id="searchInput" placeholder="Müşteri veya alış no ara...">
                                    </div>
                                    <a href="zeytin-alim.php" class="btn btn-light">
                                        <i class="bx bx-plus me-1"></i> Yeni Alım
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
                                            <button type="button" class="btn btn-outline-success" data-filter="odenmis">
                                                Ödenmiş <span class="badge bg-success ms-1" id="countOdenmis">0</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" data-filter="kismi_odendi">
                                                Kısmen <span class="badge bg-warning ms-1" id="countKismi">0</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" data-filter="odenmedi">
                                                Ödenmemiş <span class="badge bg-danger ms-1" id="countOdenmedi">0</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-sort me-1"></i> Sırala
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" data-sort="date-desc">En yeni</a></li>
                                                <li><a class="dropdown-item" href="#" data-sort="date-asc">En eski</a></li>
                                                <li><a class="dropdown-item" href="#" data-sort="tutar-desc">En yüksek tutar</a></li>
                                                <li><a class="dropdown-item" href="#" data-sort="tutar-asc">En düşük tutar</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="alisTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">#</th>
                                                <th>Alış No / Tarih</th>
                                                <th>Müşteri</th>
                                                <th>Ürünler</th>
                                                <th>Toplam Miktar</th>
                                                <th>Toplam Tutar</th>
                                                <th>Ödeme Durumu</th>
                                                <th width="140">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include 'config.php';
                                            
                                            // Alış numaralarına göre gruplanmış alım listesini çek
                                            $sorgu = $db->prepare("
                                                SELECT 
                                                    z.alisNo,
                                                    z.alisTarihi,
                                                    z.kayitTarihi,
                                                    z.odemeDurumu,
                                                    z.aciklama,
                                                    m.adSoyad,
                                                    m.phone,
                                                    COUNT(z.alisId) as urun_sayisi,
                                                    SUM(z.miktar) as toplam_miktar,
                                                    SUM(z.toplamTutar) as toplam_tutar,
                                                    GROUP_CONCAT(CONCAT(zt.turAdi, ' - ', ztp.tipAdi) SEPARATOR ' | ') as urunler
                                                FROM tbl_zeytin_alis z
                                                LEFT JOIN tbl_musteri m ON z.musteriId = m.musteriId
                                                LEFT JOIN tbl_zeytin_tipleri ztp ON z.tipId = ztp.tipId
                                                LEFT JOIN tbl_zeytin_turleri zt ON ztp.turId = zt.turId
                                                WHERE z.durum = 1
                                                GROUP BY z.alisNo
                                                ORDER BY z.kayitTarihi DESC
                                            ");
                                            $sorgu->execute();
                                            $say = 1;
                                            
                                            $toplamAlim = 0;
                                            $odenmisAlim = 0;
                                            $kismiAlim = 0;
                                            $odenmediAlim = 0;
                                            
                                            while($alis = $sorgu->fetch(PDO::FETCH_ASSOC)) {
                                                $toplamAlim++;
                                                
                                                // Ödeme durumu sayıları
                                                switch($alis['odemeDurumu']) {
                                                    case 'odenmis': $odenmisAlim++; break;
                                                    case 'kismi_odendi': $kismiAlim++; break;
                                                    case 'odenmedi': $odenmediAlim++; break;
                                                }
                                                
                                                $odemeDurumuClass = [
                                                    'odenmedi' => 'danger',
                                                    'kismi_odendi' => 'warning', 
                                                    'odenmis' => 'success'
                                                ][$alis['odemeDurumu']] ?? 'secondary';
                                                
                                                $odemeDurumuText = [
                                                    'odenmedi' => 'Ödenmedi',
                                                    'kismi_odendi' => 'Kısmen Ödendi',
                                                    'odenmis' => 'Ödendi'
                                                ][$alis['odemeDurumu']] ?? $alis['odemeDurumu'];
                                                
                                                // Ürün listesini kısalt
                                                $urunler = $alis['urunler'];
                                                if (strlen($urunler) > 50) {
                                                    $urunler = substr($urunler, 0, 50) . '...';
                                                }
                                            ?>
                                            <tr class="status-<?= $alis['odemeDurumu'] ?> alis-row" 
                                                data-status="<?= $alis['odemeDurumu'] ?>" 
                                                data-name="<?= htmlspecialchars($alis['adSoyad']) ?>"
                                                data-alisno="<?= $alis['alisNo'] ?>"
                                                data-date="<?= strtotime($alis['kayitTarihi']) ?>"
                                                data-tutar="<?= $alis['toplam_tutar'] ?>"
                                                onclick="showAlimDetail('<?= $alis['alisNo'] ?>')">
                                                <td><?= $say++ ?></td>
                                                <td>
                                                    <div class="fw-bold text-primary"><?= $alis['alisNo'] ?></div>
                                                    <div class="fis-small"><?= date('d.m.Y', strtotime($alis['alisTarihi'])) ?></div>
                                                    <div class="fis-small text-muted"><?= date('H:i', strtotime($alis['kayitTarihi'])) ?></div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?= htmlspecialchars($alis['adSoyad']) ?></div>
                                                    <div class="fis-small text-muted"><?= htmlspecialchars($alis['phone']) ?></div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">
                                                        <?= $alis['urun_sayisi'] ?> Ürün
                                                        <span class="urun-count"><?= $alis['urun_sayisi'] ?></span>
                                                    </div>
                                                    <div class="fis-small"><?= htmlspecialchars($urunler) ?></div>
                                                    <?php if(!empty($alis['aciklama'])): ?>
                                                    <div class="fis-small text-muted"><?= htmlspecialchars($alis['aciklama']) ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?= number_format($alis['toplam_miktar'], 2) ?> kg</div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-primary"><?= number_format($alis['toplam_tutar'], 2) ?> TL</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $odemeDurumuClass ?>"><?= $odemeDurumuText ?></span>
                                                </div>
                                                </td>
                                                <td class="table-actions" onclick="event.stopPropagation();">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-primary print-btn" 
                                                                onclick="printFis('<?= $alis['alisNo'] ?>')"
                                                                data-bs-toggle="tooltip" title="Fiş Yazdır">
                                                            <i class="bx bx-printer"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-info" 
                                                                onclick="showAlimDetail('<?= $alis['alisNo'] ?>')"
                                                                data-bs-toggle="tooltip" title="Detay Göster">
                                                            <i class="bx bx-show"></i>
                                                        </button>
                                                        <!--
                                                        <button type="button" class="btn btn-outline-warning" 
                                                                onclick="editAlim('<?= $alis['alisNo'] ?>')"
                                                                data-bs-toggle="tooltip" title="Düzenle">
                                                            <i class="bx bx-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deleteAlim('<?= $alis['alisNo'] ?>', '<?= htmlspecialchars($alis['adSoyad']) ?>')"
                                                                data-bs-toggle="tooltip" title="Sil">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                        -->
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            
                                            <?php if($toplamAlim == 0): ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    <i class="bx bx-package display-4 d-block mb-2"></i>
                                                    Henüz alım kaydı bulunmamaktadır.
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- İstatistikler -->
                                <div class="row mt-3">
                                    <div class="col-sm-12 col-md-6">
                                        <div class="dataTables_info">
                                            Toplam <strong><?= $toplamAlim ?></strong> alış kaydı | 
                                            <span class="text-success"><?= $odenmisAlim ?> ödenmiş</span> | 
                                            <span class="text-warning"><?= $kismiAlim ?> kısmen</span> | 
                                            <span class="text-danger"><?= $odenmediAlim ?> ödenmemiş</span>
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

<!-- Alım Detay Modal -->
<div class="modal fade" id="alimDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alış Detayları - <span id="modalAlisNo"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="alimDetailContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                    <p class="mt-2">Yükleniyor...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" id="printDetailBtn">
                    <i class="bx bx-printer me-1"></i> Fiş Yazdır
                </button>
            </div>
        </div>
    </div>
</div>

<?php include 'layouts/vendor-scripts.php'; ?>

<script>
// Global değişkenler
let currentAlisNo = '';

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Sayıları güncelle
    document.getElementById('countAll').textContent = '<?= $toplamAlim ?>';
    document.getElementById('countOdenmis').textContent = '<?= $odenmisAlim ?>';
    document.getElementById('countKismi').textContent = '<?= $kismiAlim ?>';
    document.getElementById('countOdenmedi').textContent = '<?= $odenmediAlim ?>';
    
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
        const rows = document.querySelectorAll('#alisTable tbody tr');
        rows.forEach(row => {
            if (filter === 'all') {
                row.style.display = '';
            } else {
                row.style.display = row.getAttribute('data-status') === filter ? '' : 'none';
            }
        });
    });
});

// Arama işlemi
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#alisTable tbody tr');
    
    rows.forEach(row => {
        const customerName = row.getAttribute('data-name').toLowerCase();
        const alisNo = row.getAttribute('data-alisno').toLowerCase();
        if (customerName.includes(searchTerm) || alisNo.includes(searchTerm)) {
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
    const tbody = document.querySelector('#alisTable tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        let aValue, bValue;
        
        switch(sortType) {
            case 'date-desc':
                aValue = parseInt(a.getAttribute('data-date'));
                bValue = parseInt(b.getAttribute('data-date'));
                return bValue - aValue;
                
            case 'date-asc':
                aValue = parseInt(a.getAttribute('data-date'));
                bValue = parseInt(b.getAttribute('data-date'));
                return aValue - bValue;
                
            case 'tutar-desc':
                aValue = parseFloat(a.getAttribute('data-tutar'));
                bValue = parseFloat(b.getAttribute('data-tutar'));
                return bValue - aValue;
                
            case 'tutar-asc':
                aValue = parseFloat(a.getAttribute('data-tutar'));
                bValue = parseFloat(b.getAttribute('data-tutar'));
                return aValue - bValue;
        }
    });
    
    // Sıralanmış satırları tekrar ekle
    rows.forEach(row => tbody.appendChild(row));
}

// Alım detayını göster
function showAlimDetail(alisNo) {
    currentAlisNo = alisNo;
    document.getElementById('modalAlisNo').textContent = alisNo;
    
    // Modal'ı aç
    const modal = new bootstrap.Modal(document.getElementById('alimDetailModal'));
    modal.show();
    
    // Detayları yükle
    loadAlimDetail(alisNo);
}

// Alım detaylarını yükle
function loadAlimDetail(alisNo) {
    fetch(`ajax.php?islem=alimDetay&alisNo=${alisNo}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            document.getElementById('alimDetailContent').innerHTML = data;
            
            // Yazdır butonunu etkinleştir
            document.getElementById('printDetailBtn').onclick = function() {
                printFis(alisNo);
            };
        })
        .catch(error => {
            console.error('Hata:', error);
            document.getElementById('alimDetailContent').innerHTML = `
                <div class="alert alert-danger">
                    <h5>Hata!</h5>
                    <p>Alım detayları yüklenirken bir hata oluştu.</p>
                    <p class="mb-0">${error.message}</p>
                </div>
            `;
        });
}

// Fiş yazdırma
function printFis(alisNo) {
    // Yeni pencerede fiş sayfasını aç
    const printWindow = window.open(`fis-yazdir.php?alisNo=${alisNo}`, '_blank');
    
    // Yazdırma butonuna tıklanmasını bekle
    setTimeout(() => {
        if (printWindow) {
            printWindow.print();
        }
    }, 500);
}

// Alım düzenle
function editAlim(alisNo) {
    window.location.href = `alim-duzenle.php?alisNo=${alisNo}`;
}

// Alım sil
function deleteAlim(alisNo, musteriAdi) {
    if(confirm(`"${musteriAdi}" adlı müşteriye ait "${alisNo}" numaralı alışı silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz ve tüm ürün kayıtları silinecektir!`)) {
        window.location.href = `islem.php?alimSil=${alisNo}`;
    }
}

// Modal kapandığında
document.getElementById('alimDetailModal').addEventListener('hidden.bs.modal', function () {
    currentAlisNo = '';
    document.getElementById('alimDetailContent').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yükleniyor...</span>
            </div>
            <p class="mt-2">Yükleniyor...</p>
        </div>
    `;
});
</script>

</body>
</html>