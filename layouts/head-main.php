<?php // include 'layouts/config.php'; ?>
<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/zeytin/config.php'; 
 $sorgu=$db->prepare("SELECT * FROM sirket where sirket_id=?");
 $sorgu->execute(array(1));
 $sorguCompany=$sorgu->fetch(PDO::FETCH_ASSOC) ?>
<?php
// include language configuration file based on selected language
$lang = "tr";
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
}
if (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = "tr";
}

require_once("assets/lang/" . $lang . ".php");
?>
<!DOCTYPE html>

<html lang="<?php echo $lang ?>">