<?php
// ajax_zeytin.php
session_start();
require_once 'config.php';

if(isset($_POST['islem'])) {
    switch($_POST['islem']) {
        
        // TÜR İŞLEMLERİ
        case 'turEkle':
            $turAdi = trim($_POST['turAdi']);
            $durum = $_POST['durum'];
            
            try {
                $checkSorgu = $db->prepare("SELECT COUNT(*) FROM tbl_zeytin_turleri WHERE turAdi = ?");
                $checkSorgu->execute([$turAdi]);
                $exists = $checkSorgu->fetchColumn();
                
                if($exists > 0) {
                    echo json_encode(['success' => false, 'message' => 'Bu isimde bir tür zaten mevcut!']);
                    exit;
                }
                
                $sorgu = $db->prepare("INSERT INTO tbl_zeytin_turleri (turAdi, durum, created_at) VALUES (?, ?, NOW())");
                $sorgu->execute([$turAdi, $durum]);
                
                echo json_encode(['success' => true, 'message' => 'Tür başarıyla eklendi']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Tür eklenirken hata: ' . $e->getMessage()]);
            }
            break;
            
        case 'turGuncelle':
            $turId = $_POST['turId'];
            $turAdi = trim($_POST['turAdi']);
            $durum = $_POST['durum'];
            
            try {
                $checkSorgu = $db->prepare("SELECT COUNT(*) FROM tbl_zeytin_turleri WHERE turAdi = ? AND turId != ?");
                $checkSorgu->execute([$turAdi, $turId]);
                $exists = $checkSorgu->fetchColumn();
                
                if($exists > 0) {
                    echo json_encode(['success' => false, 'message' => 'Bu isimde başka bir tür zaten mevcut!']);
                    exit;
                }
                
                $sorgu = $db->prepare("UPDATE tbl_zeytin_turleri SET turAdi = ?, durum = ?, updated_at = NOW() WHERE turId = ?");
                $sorgu->execute([$turAdi, $durum, $turId]);
                
                echo json_encode(['success' => true, 'message' => 'Tür başarıyla güncellendi']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Tür güncellenirken hata: ' . $e->getMessage()]);
            }
            break;
            
        case 'turSil':
            $turId = $_POST['turId'];
            
            try {
                $checkSorgu = $db->prepare("SELECT COUNT(*) FROM tbl_zeytin_tipleri WHERE turId = ?");
                $checkSorgu->execute([$turId]);
                $hasTips = $checkSorgu->fetchColumn();
                
                if($hasTips > 0) {
                    echo json_encode(['success' => false, 'message' => 'Bu türe ait tipler bulunuyor. Önce tipleri silmelisiniz!']);
                    exit;
                }
                
                $sorgu = $db->prepare("DELETE FROM tbl_zeytin_turleri WHERE turId = ?");
                $sorgu->execute([$turId]);
                
                echo json_encode(['success' => true, 'message' => 'Tür başarıyla silindi']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Tür silinirken hata: ' . $e->getMessage()]);
            }
            break;
            
        // TİP İŞLEMLERİ
        case 'tipEkle':
            $turId = $_POST['turId'];
            $tipAdi = trim($_POST['tipAdi']);
            $birimFiyat = $_POST['birimFiyat'];
            $birim = $_POST['birim'];
            $durum = $_POST['durum'];
            
            try {
                // Aynı türde aynı isimde tip var mı kontrol et
                $checkSorgu = $db->prepare("SELECT COUNT(*) FROM tbl_zeytin_tipleri WHERE turId = ? AND tipAdi = ?");
                $checkSorgu->execute([$turId, $tipAdi]);
                $exists = $checkSorgu->fetchColumn();
                
                if($exists > 0) {
                    echo json_encode(['success' => false, 'message' => 'Bu türde aynı isimde bir tip zaten mevcut!']);
                    exit;
                }
                
                $sorgu = $db->prepare("INSERT INTO tbl_zeytin_tipleri (turId, tipAdi, birimFiyat, birim, durum, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $sorgu->execute([$turId, $tipAdi, $birimFiyat, $birim, $durum]);
                
                echo json_encode(['success' => true, 'message' => 'Tip başarıyla eklendi']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Tip eklenirken hata: ' . $e->getMessage()]);
            }
            break;
            
        case 'tipGuncelle':
            $tipId = $_POST['tipId'];
            $turId = $_POST['turId'];
            $tipAdi = trim($_POST['tipAdi']);
            $birimFiyat = $_POST['birimFiyat'];
            $birim = $_POST['birim'];
            $durum = $_POST['durum'];
            
            try {
                // Aynı türde aynı isimde başka tip var mı kontrol et (kendisi hariç)
                $checkSorgu = $db->prepare("SELECT COUNT(*) FROM tbl_zeytin_tipleri WHERE turId = ? AND tipAdi = ? AND tipId != ?");
                $checkSorgu->execute([$turId, $tipAdi, $tipId]);
                $exists = $checkSorgu->fetchColumn();
                
                if($exists > 0) {
                    echo json_encode(['success' => false, 'message' => 'Bu türde aynı isimde başka bir tip zaten mevcut!']);
                    exit;
                }
                
                $sorgu = $db->prepare("UPDATE tbl_zeytin_tipleri SET turId = ?, tipAdi = ?, birimFiyat = ?, birim = ?, durum = ?, updated_at = NOW() WHERE tipId = ?");
                $sorgu->execute([$turId, $tipAdi, $birimFiyat, $birim, $durum, $tipId]);
                
                echo json_encode(['success' => true, 'message' => 'Tip başarıyla güncellendi']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Tip güncellenirken hata: ' . $e->getMessage()]);
            }
            break;
            
        case 'tipSil':
            $tipId = $_POST['tipId'];
            
            try {
                // Bu tipe ait alımlar var mı kontrol et
                $checkSorgu = $db->prepare("SELECT COUNT(*) FROM tbl_zeytin_alis WHERE tipId = ?");
                $checkSorgu->execute([$tipId]);
                $hasAlim = $checkSorgu->fetchColumn();
                
                if($hasAlim > 0) {
                    echo json_encode(['success' => false, 'message' => 'Bu tipe ait alım kayıtları bulunuyor. Önce alım kayıtlarını silmelisiniz!']);
                    exit;
                }
                
                $sorgu = $db->prepare("DELETE FROM tbl_zeytin_tipleri WHERE tipId = ?");
                $sorgu->execute([$tipId]);
                
                echo json_encode(['success' => true, 'message' => 'Tip başarıyla silindi']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Tip silinirken hata: ' . $e->getMessage()]);
            }
            break;
            
        // ZEYTİN ALIM FORMUNDA KULLANILACAK AJAX İSTEKLERİ
        case 'tipleriGetir':
            $turId = $_POST['turId'];
            $sorgu = $db->prepare("SELECT * FROM tbl_zeytin_tipleri WHERE turId = ? AND durum = 1 ORDER BY tipAdi");
            $sorgu->execute([$turId]);
            $tipler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<option value="">Tip Seçin</option>';
            foreach($tipler as $tip) {
                echo '<option value="'.$tip['tipId'].'">'.$tip['tipAdi'].'</option>';
            }
            break;
            
        case 'tipBilgisiGetir':
            $tipId = $_POST['tipId'];
            $sorgu = $db->prepare("SELECT birimFiyat, birim FROM tbl_zeytin_tipleri WHERE tipId = ?");
            $sorgu->execute([$tipId]);
            $tip = $sorgu->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'birimFiyat' => $tip['birimFiyat'],
                'birim' => $tip['birim']
            ]);
            break;
    }
}
?>