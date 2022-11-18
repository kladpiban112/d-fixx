<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "../../core/config.php";
require_once ABSPATH."/checklogin.php";
require_once ABSPATH."/functions.php";
require_once ABSPATH."/PasswordHash.php";
require_once ABSPATH."/resize-class.php";

//require_once ABSPATH."/qrcode-gen.php";
require_once ABSPATH."/BarcodeQR.php";

$act = filter_input(INPUT_GET, 'act', FILTER_SANITIZE_STRING);
$eq_id = filter_input(INPUT_GET, 'oid', FILTER_SANITIZE_STRING);
$eq_codes = filter_input(INPUT_GET, 'eq_codes', FILTER_SANITIZE_STRING);
$now = date("Y-m-d H:i:s");
$act = base64_decode($act);

if($act == 'genqrcode'){
//create text QR code
//$url = "https://repair.cqc-songkhlapao.com/qrcode/";

// set BarcodeQR object
$qr = new BarcodeQR();

// create URL QR code
$eq_id_enc = $eq_id;
$qr->url(ADMIN_URL."/views/qrcode/index.php?eqid=$eq_id_enc&eqcode=$eq_codes");

// display new QR code image
$eq_id = base64_decode($eq_id_enc);
$qr->draw(300, "../../uploads/qrcode/$eq_id");


//echo "success";


?>

<script langquage='javascript'>
window.history.back();
</script>



<?php

}
			
			
			
			?>


