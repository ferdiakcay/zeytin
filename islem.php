<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
date_default_timezone_set('Europe/Istanbul');
include 'config.php';


//token üretme
function generateHighEntropyToken(int $entropyBits = 256): string
{
    $bytes = $entropyBits / 8;
    $data = random_bytes($bytes);
    
    // Base64URL formatına çevir (URL güvenli)
    $token = strtr(base64_encode($data), '+/', '-_');
    return rtrim($token, '=');
}

// islem.php dosyasına eklenecek kod
if(isset($_POST['musteriEkle'])) {
    // Form verilerini al
    $adSoyad = $_POST['adSoyad'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $tcKimlik = $_POST['tcKimlik'];
    $adres = $_POST['adres'];
    $il = $_POST['il'];
    $ilce = $_POST['ilce'];   
    $aciklama = $_POST['aciklama'];
    $musteriTipi = 'bireysel';//$_POST['musteriTipi'];
    $durum = $_POST['durum'];
    
    // Veritabanına ekleme sorgusu
    $sorgu = $db->prepare("INSERT INTO tbl_musteri SET
        adSoyad = ?,
        phone = ?,
        email = ?,
        tcKimlik = ?,
        adres = ?,
        il = ?,
        ilce = ?,       
        aciklama = ?,
        musteriTipi = ?,
        durum = ?");
    
    $ekle = $sorgu->execute(array(
        $adSoyad, $phone, $email, $tcKimlik, $adres, 
        $il, $ilce, $aciklama, $musteriTipi, $durum
    ));
    
    if($ekle) {
        echo "<script>alert('Müşteri başarıyla eklendi.'); window.location.href='musteriList.php';</script>";
    } else {
        echo "<script>alert('Müşteri eklenirken bir hata oluştu.'); window.history.back();</script>";
    }
}



// Zeytin Türü Ekleme
if(isset($_POST['zeytinTuruEkle'])) {
    $turAdi = $_POST['turAdi'];
    $aciklama = $_POST['aciklama'];
    
    $sorgu = $db->prepare("INSERT INTO tbl_zeytin_turleri SET turAdi = ?, aciklama = ?");
    $ekle = $sorgu->execute([$turAdi, $aciklama]);
    
    if($ekle) {
        header("Location: zeytin-turleri.php?durum=ok");
    } else {
        header("Location: zeytin-turleri.php?durum=no");
    }
}

// Zeytin Tipi Ekleme
if(isset($_POST['zeytinTipiEkle'])) {
    $turId = $_POST['turId'];
    $tipAdi = $_POST['tipAdi'];
    $birimFiyat = $_POST['birimFiyat'];
    $birim = $_POST['birim'];
    $aciklama = $_POST['aciklama'];
    
    $sorgu = $db->prepare("INSERT INTO tbl_zeytin_tipleri SET turId = ?, tipAdi = ?, birimFiyat = ?, birim = ?, aciklama = ?");
    $ekle = $sorgu->execute([$turId, $tipAdi, $birimFiyat, $birim, $aciklama]);
    
    // Stok tablosuna da ekle
    if($ekle) {
        $tipId = $db->lastInsertId();
        $stokSorgu = $db->prepare("INSERT INTO tbl_zeytin_stok SET tipId = ?, miktar = 0");
        $stokSorgu->execute([$tipId]);
        header("Location: zeytin-tipleri.php?durum=ok");
    } else {
        header("Location: zeytin-tipleri.php?durum=no");
    }
}

// Zeytin Alım Ekleme
/* if(isset($_POST['zeytinAlimEkle'])) {
    $musteriId = $_POST['musteriId'];
    $tipId = $_POST['tipId'];
    $miktar = $_POST['miktar'];
    $birimFiyat = $_POST['birimFiyat'];
    $toplamTutar = $_POST['toplamTutar'];
    $alisTarihi = $_POST['alisTarihi'];
    $odemeDurumu = $_POST['odemeDurumu'];
    $aciklama = $_POST['aciklama'];
    
    $sorgu = $db->prepare("INSERT INTO tbl_zeytin_alis SET 
        musteriId = ?, tipId = ?, miktar = ?, birimFiyat = ?, toplamTutar = ?, 
        alisTarihi = ?, odemeDurumu = ?, aciklama = ?");
    $ekle = $sorgu->execute([$musteriId, $tipId, $miktar, $birimFiyat, $toplamTutar, $alisTarihi, $odemeDurumu, $aciklama]);
    
    // Stok güncelleme
    if($ekle) {
         $alisId = $db->lastInsertId();
        $stokSorgu = $db->prepare("UPDATE tbl_zeytin_stok SET miktar = miktar + ? WHERE tipId = ?");
        $stokSorgu->execute([$miktar, $tipId]);
        // Fiş yazdırma sayfasına yönlendir
            header("Location: fis-yazdir.php?alisId=" . $alisId);
      //  header("Location: zeytin-alim.php?durum=ok");
    } else {
        header("Location: zeytin-alim.php?durum=no");
    }
} */



// Hızlı Müşteri Ekleme (AJAX için)
if(isset($_POST['action']) && $_POST['action'] == 'hizliMusteriEkle') {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $adSoyad = trim($_POST['adSoyad'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $adres = trim($_POST['adres'] ?? '');
        
        // Validasyon
        if(empty($adSoyad) || empty($phone)) {
            throw new Exception("Ad soyad ve telefon alanları zorunludur!");
        }
        
        // Telefon numarasını temizle
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if(strlen($cleanPhone) != 10) {
            throw new Exception("Geçerli bir telefon numarası giriniz (10 haneli)!");
        }
        
        // E-posta validasyonu
        if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Geçersiz e-posta adresi!");
        }
        
        // Müşteri ekleme sorgusu
        $sorgu = $db->prepare("INSERT INTO tbl_musteri SET 
            adSoyad = ?,
            phone = ?,
            email = ?,
            adres = ?,
            musteriTipi = 'bireysel',
            durum = 1,
            kayitTarihi = NOW()");
        
        $ekle = $sorgu->execute([$adSoyad, $cleanPhone, $email, $adres]);
        
        if($ekle) {
            $musteriId = $db->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'message' => 'Müşteri başarıyla eklendi!',
                'musteriId' => $musteriId,
                'musteriAdi' => $adSoyad,
                'phone' => $cleanPhone
            ]);
        } else {
            throw new Exception("Müşteri eklenirken veritabanı hatası oluştu!");
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}






// Çoklu zeytin alım ekleme işlemi
if(isset($_POST['zeytinAlimEkle'])) {
    $musteriId = intval($_POST['musteriId']);
    $alisTarihi = $_POST['alisTarihi'];
    $odemeDurumu = $_POST['odemeDurumu'];
    $aciklama = $_POST['aciklama'];
    $urunler = json_decode($_POST['urunler'], true);
    
    try {
        $db->beginTransaction();
        
        // Alış No oluştur (AL202412150001 formatında)
        $alisNo = 'AL' . date('Ymd') . sprintf('%04d', rand(1000, 9999));
        
        // Alış No'nun benzersiz olduğundan emin ol
        $checkSorgu = $db->prepare("SELECT COUNT(*) FROM tbl_zeytin_alis WHERE alisNo = ?");
        $checkSorgu->execute([$alisNo]);
        while($checkSorgu->fetchColumn() > 0) {
            $alisNo = 'AL' . date('Ymd') . sprintf('%04d', rand(1000, 9999));
            $checkSorgu->execute([$alisNo]);
        }
        
        $alisId = null;
        
        foreach($urunler as $index => $urun) {
            $tipId = intval($urun['tipId']);
            $miktar = floatval($urun['miktar']);
            $urunAciklama = $urun['aciklama'] ?? '';
            
            // Tip bilgilerini getir
            $tipSorgu = $db->prepare("SELECT birimFiyat, birim FROM tbl_zeytin_tipleri WHERE tipId = ?");
            $tipSorgu->execute([$tipId]);
            $tip = $tipSorgu->fetch(PDO::FETCH_ASSOC);
            
            if(!$tip) {
                throw new Exception("Geçersiz tip ID: " . $tipId);
            }
            
            $birimFiyat = floatval($tip['birimFiyat']);
            $toplamTutar = $miktar * $birimFiyat;
            
            // Alımı veritabanına kaydet (alisNo ile birlikte)
            $sorgu = $db->prepare("INSERT INTO tbl_zeytin_alis SET 
                                alisNo = ?,
                                musteriId = ?,
                                tipId = ?,
                                miktar = ?,
                                birimFiyat = ?,
                                toplamTutar = ?,
                                alisTarihi = ?,
                                odemeDurumu = ?,
                                aciklama = ?,
                                urunAciklama = ?,
                                durum = 1,
                                kayitTarihi = NOW()");
            
            $ekle = $sorgu->execute([
                $alisNo, $musteriId, $tipId, $miktar, $birimFiyat, $toplamTutar, 
                $alisTarihi, $odemeDurumu, $aciklama, $urunAciklama
            ]);
            
            if(!$ekle) {
                throw new Exception("Ürün kaydedilirken hata oluştu!");
            }
            
            // İlk alım ID'sini sakla (fiş için)
            if($alisId === null) {
                $alisId = $db->lastInsertId();
            }
            
            // STOK GÜNCELLEME - BASİT VE ETKİLİ VERSİYON
            // Önce bu tipId için stok kaydı var mı kontrol et
            $stokKontrol = $db->prepare("SELECT stokId, miktar FROM tbl_zeytin_stok WHERE tipId = ?");
            $stokKontrol->execute([$tipId]);
            $mevcutStok = $stokKontrol->fetch(PDO::FETCH_ASSOC);
            
            if($mevcutStok) {
                // Stok kaydı varsa GÜNCELLE
                $yeniMiktar = floatval($mevcutStok['miktar']) + $miktar;
                $stokGuncelle = $db->prepare("UPDATE tbl_zeytin_stok SET miktar = ?, sonGuncelleme = NOW() WHERE tipId = ?");
                $stokGuncelleSonuc = $stokGuncelle->execute([$yeniMiktar, $tipId]);
                
                if(!$stokGuncelleSonuc) {
                    throw new Exception("Stok güncellenirken hata oluştu! TipID: " . $tipId);
                }
                
                error_log("Stok güncellendi - TipID: $tipId, Eski: " . $mevcutStok['miktar'] . ", Eklenen: $miktar, Yeni: $yeniMiktar");
            } else {
                // Stok kaydı yoksa YENİ EKLE
                $stokEkle = $db->prepare("INSERT INTO tbl_zeytin_stok (tipId, miktar, sonGuncelleme) VALUES (?, ?, NOW())");
                $stokEkleSonuc = $stokEkle->execute([$tipId, $miktar]);
                
                if(!$stokEkleSonuc) {
                    throw new Exception("Stok kaydı oluşturulurken hata oluştu! TipID: " . $tipId);
                }
                
                error_log("Yeni stok kaydı oluşturuldu - TipID: $tipId, Miktar: $miktar");
            }
        }
        
        $db->commit();
        
        // Başarı mesajı ve yönlendirme
        header("Location: fis-yazdir.php?alisNo=" . $alisNo . "&durum=success");
        exit;
        
    } catch(PDOException $e) {
        $db->rollBack();
        error_log("Stok Hatası (PDO): " . $e->getMessage());
        header("Location: zeytin-alim.php?durum=hata&mesaj=Veritabanı hatası: " . $e->getMessage());
        exit;
    } catch(Exception $e) {
        $db->rollBack();
        error_log("Stok Hatası (Genel): " . $e->getMessage());
        header("Location: zeytin-alim.php?durum=hata&mesaj=" . $e->getMessage());
        exit;
    }
}

// Silme işlemleri
if(isset($_GET['zeytinTuruSil'])) {
    $turId = $_GET['zeytinTuruSil'];
    $sorgu = $db->prepare("DELETE FROM tbl_zeytin_turleri WHERE turId = ?");
    $sorgu->execute([$turId]);
    header("Location: zeytin-turleri.php");
}

if(isset($_GET['zeytinTipiSil'])) {
    $tipId = $_GET['zeytinTipiSil'];
    $sorgu = $db->prepare("DELETE FROM tbl_zeytin_tipleri WHERE tipId = ?");
    $sorgu->execute([$tipId]);
    header("Location: zeytin-tipleri.php");
}

//tema değiş
if(isset($_GET['tema'])=='degis'){
    
    $sorgu=$db->prepare("SELECT tema FROM sirket where sirket_id=?");
    $sorgu->execute(array(1));
    $sorguCompany=$sorgu->fetch(PDO::FETCH_ASSOC);
  
    if($sorguCompany["tema"]== 1){
  
      $tema=0;
    }
    else{
    
      $tema=1;
    }
  
  
    $kaydet=$db->prepare("UPDATE sirket SET      
    tema=:tema  
    WHERE sirket_id=1
    ");
    $insert=$kaydet->execute(array(          
        'tema' =>  $tema    
        
        
        ));
  
    
   
   if($insert){ 
    Header("Location:index.php");
        }
  else{    
  Header("Location:index.php");
  } 
  
}



// Müşteri silme işlemi
if(isset($_GET['musteriSil'])) {
    $musteriId = intval($_GET['musteriSil']);
    
    // Müşterinin zeytin alımı var mı kontrol et
    $sorgu = $db->prepare("SELECT COUNT(*) as alimSayisi FROM tbl_zeytin_alis WHERE musteriId = ? AND durum = 1");
    $sorgu->execute([$musteriId]);
    $alim = $sorgu->fetch(PDO::FETCH_ASSOC);
    
    if($alim['alimSayisi'] > 0) {
        header("Location: musteriList.php?durum=hata&mesaj=Müşterinin zeytin alım kaydı bulunduğu için silinemez!");
        exit;
    }
    
    $sorgu = $db->prepare("DELETE FROM tbl_musteri WHERE musteriId = ?");
    $sil = $sorgu->execute([$musteriId]);
    
    if($sil) {
        header("Location: musteriList.php?durum=ok&mesaj=Müşteri başarıyla silindi!");
    } else {
        header("Location: musteriList.php?durum=hata&mesaj=Müşteri silinirken bir hata oluştu!");
    }
    exit;
}




// Müşteri Düzenleme İşlemi
if(isset($_POST['musteriDuzenle'])) {
    $musteriId = intval($_POST['musteriId']);
    $adSoyad = trim($_POST['adSoyad']);
    $tcKimlik = trim($_POST['tcKimlik']);
    $musteriTipi = $_POST['musteriTipi'];
    $durum = intval($_POST['durum']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $adres = trim($_POST['adres']);
    $il = trim($_POST['il']);
    $ilce = trim($_POST['ilce']);
    $postaKodu = trim($_POST['postaKodu']);
    $aciklama = trim($_POST['aciklama']);
    
    // Telefon numarasını temizle (sadece rakamlar)
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // TC Kimlik kontrolü (11 haneli mi)
    if(!empty($tcKimlik) && strlen($tcKimlik) != 11) {
        header("Location: musteri-duzenle.php?id=" . $musteriId . "&durum=hata&mesaj=TC Kimlik numarası 11 haneli olmalıdır!");
        exit;
    }
    
    // E-posta kontrolü
    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: musteri-duzenle.php?id=" . $musteriId . "&durum=hata&mesaj=Geçersiz e-posta adresi!");
        exit;
    }
    
    try {
        $sorgu = $db->prepare("UPDATE tbl_musteri SET 
                            adSoyad = ?,
                            tcKimlik = ?,
                            musteriTipi = ?,
                            durum = ?,
                            phone = ?,
                            email = ?,
                            adres = ?,
                            il = ?,
                            ilce = ?,                           
                            aciklama = ?,
                            guncellemeTarihi = NOW()
                            WHERE musteriId = ?");
        
        $guncelle = $sorgu->execute([
            $adSoyad, $tcKimlik, $musteriTipi, $durum, $phone, 
            $email, $adres, $il, $ilce, $aciklama, $musteriId
        ]);
        
        if($guncelle) {
            header("Location: musteri-duzenle.php?id=" . $musteriId . "&durum=ok&mesaj=Müşteri bilgileri başarıyla güncellendi!");
        } else {
            header("Location: musteri-duzenle.php?id=" . $musteriId . "&durum=hata&mesaj=Müşteri güncellenirken bir hata oluştu!");
        }
        
    } catch(PDOException $e) {
        header("Location: musteri-duzenle.php?id=" . $musteriId . "&durum=hata&mesaj=Veritabanı hatası: " . $e->getMessage());
    }
    exit;
}

// Müşteri Silme İşlemi (Liste sayfası için de geçerli)
if(isset($_GET['musteriSil'])) {
    $musteriId = intval($_GET['musteriSil']);
    
    // Müşterinin zeytin alımı var mı kontrol et
    $sorgu = $db->prepare("SELECT COUNT(*) as alimSayisi FROM tbl_zeytin_alis WHERE musteriId = ? AND durum = 1");
    $sorgu->execute([$musteriId]);
    $alim = $sorgu->fetch(PDO::FETCH_ASSOC);
    
    if($alim['alimSayisi'] > 0) {
        header("Location: musteri-listesi.php?durum=hata&mesaj=Müşterinin zeytin alım kaydı bulunduğu için silinemez!");
        exit;
    }
    
    $sorgu = $db->prepare("DELETE FROM tbl_musteri WHERE musteriId = ?");
    $sil = $sorgu->execute([$musteriId]);
    
    if($sil) {
        header("Location: musteri-listesi.php?durum=ok&mesaj=Müşteri başarıyla silindi!");
    } else {
        header("Location: musteri-listesi.php?durum=hata&mesaj=Müşteri silinirken bir hata oluştu!");
    }
    exit;
}


// Stok güncelleme işlemi
if(isset($_GET['islem']) && $_GET['islem'] == 'stokGuncelle') {
    include 'config.php';
    
    $stokId = intval($_GET['stokId']);
    $miktar = floatval($_GET['miktar']);
    
    try {
        $sorgu = $db->prepare("UPDATE tbl_zeytin_stok SET miktar = ?, sonGuncelleme = NOW() WHERE stokId = ?");
        $sorgu->execute([$miktar, $stokId]);
        
        echo json_encode(['success' => true, 'message' => 'Stok güncellendi']);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?>







