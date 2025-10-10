<?php 

if(!isset($_SESSION)){ 
  session_start();
}
try{
 
   // $db=new PDO("mysql:host=localhost;dbname=kodlasof_drahmetberber",'kodlasof_drahmetberber','NGB88.F.gx7q');
    $db=new PDO("mysql:host=localhost;dbname=db_zeytin;charset=utf8",'root','');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
}
catch(PDOException $e){

  echo $e->getMessage();

}



?>