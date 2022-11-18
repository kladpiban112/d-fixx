<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "../../core/config.php";
require_once ABSPATH."/checklogin.php";
require_once ABSPATH."/functions.php";
require_once ABSPATH."/PasswordHash.php";
require_once ABSPATH."/resize-class.php";

$act = filter_input(INPUT_POST, 'act', FILTER_SANITIZE_STRING);
$repairid = filter_input(INPUT_POST, 'repairid', FILTER_SANITIZE_STRING);
$personid = filter_input(INPUT_POST, 'personid', FILTER_SANITIZE_STRING);

$org_id = filter_input(INPUT_POST, 'org_id', FILTER_SANITIZE_STRING);
$cid = filter_input(INPUT_POST, 'cid', FILTER_SANITIZE_STRING);
$prename = filter_input(INPUT_POST, 'prename', FILTER_SANITIZE_STRING);
$fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
$lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);

$repairdate = filter_input(INPUT_POST, 'repairdate', FILTER_SANITIZE_STRING);
$repairdate = date_saveto_db($repairdate);
$repair_type = filter_input(INPUT_POST, 'repair_type', FILTER_SANITIZE_STRING); // ประเภทการรับบริการ
$repair_title = filter_input(INPUT_POST, 'repair_title', FILTER_SANITIZE_STRING); // 
$repair_desc = filter_input(INPUT_POST, 'repair_desc', FILTER_SANITIZE_STRING); // 
$eq_id = filter_input(INPUT_POST, 'eq_id', FILTER_SANITIZE_STRING); // 
$eq_name = filter_input(INPUT_POST, 'eq_name', FILTER_SANITIZE_STRING); // 
$eq_code = filter_input(INPUT_POST, 'eq_code', FILTER_SANITIZE_STRING); // 
$eq_others = filter_input(INPUT_POST, 'eq_others', FILTER_SANITIZE_STRING); // 
$repair_warranty = filter_input(INPUT_POST, 'repair_warranty', FILTER_SANITIZE_STRING); // 

$approvedate = filter_input(INPUT_POST, 'approvedate', FILTER_SANITIZE_STRING);
$approvedate = date_saveto_db($approvedate);
$approve_username = filter_input(INPUT_POST, 'approve_username', FILTER_SANITIZE_STRING);

$returndate = filter_input(INPUT_POST, 'returndate', FILTER_SANITIZE_STRING);
$returndate = date_saveto_db($returndate);
$return_username = filter_input(INPUT_POST, 'return_username', FILTER_SANITIZE_STRING);

$repair_place = filter_input(INPUT_POST, 'repair_place', FILTER_SANITIZE_STRING); // 
$repair_out = filter_input(INPUT_POST, 'repair_out', FILTER_SANITIZE_STRING); // 
$repairoutdate = filter_input(INPUT_POST, 'repairoutdate', FILTER_SANITIZE_STRING); // 
$repairoutdate = date_saveto_db($repairoutdate);

if($repair_out == 'O'){
    // เลขที่ส่งซ่อมภายนอก
$repair_outcode = getRepairoutcode($org_id,$repairoutdate,$repairid);  
}

$flag = '1';
$now = date("Y-m-d H:i:s");


if($act == 'add'){

}else if($act == 'edit'){



	$query = "UPDATE ".DB_PREFIX."repair_main SET repair_title = ?, repair_desc = ?,edit_date = ?,edit_users = ?,eq_id = ? ,eq_name = ?,eq_code = ?,eq_others = ?,approve_date = ? ,approve_username = ?, return_date = ? ,return_username = ?,repair_place = ?,repair_warranty = ? WHERE repair_id = ? LIMIT 1"; 
	$stmt = $conn->prepare($query);

	$stmt->bindParam(1, $repair_title, PDO::PARAM_STR);
	$stmt->bindParam(2, $repair_desc);
	$stmt->bindParam(3, $now, PDO::PARAM_STR);
	$stmt->bindParam(4, $logged_user_id, PDO::PARAM_STR);
	$stmt->bindParam(5, $eq_id, PDO::PARAM_STR);
	$stmt->bindParam(6, $eq_name, PDO::PARAM_STR);
	$stmt->bindParam(7, $eq_code, PDO::PARAM_STR);
    $stmt->bindParam(8, $eq_others, PDO::PARAM_STR);
	$stmt->bindParam(9, $approvedate, PDO::PARAM_STR);
	$stmt->bindParam(10, $approve_username, PDO::PARAM_STR);
	$stmt->bindParam(11, $returndate, PDO::PARAM_STR);
    $stmt->bindParam(12, $return_username, PDO::PARAM_STR);
    $stmt->bindParam(13, $repair_place, PDO::PARAM_STR);
    $stmt->bindParam(14, $repair_warranty,PDO::PARAM_STR);
	$stmt->bindParam(15, $repairid, PDO::PARAM_INT);
	$stmt->execute();

if(count($_FILES["files"]['name']) != 0){
    for($f=0;$f<count($_FILES["files"]['name']);$f++){

        $f_name = $_FILES['files']['name'][$f];
        $f_ext = strtolower(substr(strrchr($f_name, '.'), 1));
        $f_size = $_FILES['files']['size'][$f];

        $file_code = random_code();
        $file_name_new = $file_code."-".$_FILES['files']['name'][$f];

        $file_name_new = RewriteFile($file_name_new); 
        if (($f_ext == "jpg") && ($f_ext == "jpeg") && ($f_ext == "gif") && ($f_ext == "png")) 
        {

            move_uploaded_file($_FILES["files"]["tmp_name"], "../../uploads/temp/".$file_name_new);


        // create big image
        $resizeObj = new resize("../../uploads/temp/".$file_name_new); 
        $resizeObj -> resizeImage(1080, 900, 'auto'); // (options: exact, portrait, landscape, auto, crop) 
        $resizeObj -> saveImage("../../uploads/repair/".$file_name_new);
        
        @unlink ("../../uploads/temp/".$file_name_new);

        }else{
            move_uploaded_file($_FILES["files"]["tmp_name"][$f], "../../uploads/repair/".$file_name_new);

        }

        if($f_name != ""){
        $query = "INSERT INTO ".DB_PREFIX."repair_files (file_id, repair_id, file_oldname, file_name,file_ext,file_size,file_status) VALUES (NULL, ?, ?, ?, ?, ?, '1')"; 
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $repairid, PDO::PARAM_INT);
        $stmt->bindParam(2, $f_name, PDO::PARAM_STR);
        $stmt->bindParam(3, $file_name_new, PDO::PARAM_STR);
        $stmt->bindParam(4, $f_ext, PDO::PARAM_STR);
        $stmt->bindParam(5, $f_size, PDO::PARAM_INT);
        $stmt->execute();
        }



    }
}

if(count($_FILES["doc"]['name']) != 0){
    for($f=0;$f<count($_FILES["doc"]['name']);$f++){

        $f_name = $_FILES['doc']['name'][$f];
        $f_ext = strtolower(substr(strrchr($f_name, '.'), 1));
        $f_size = $_FILES['doc']['size'][$f];

        $file_code = random_code();
        $file_name_new = $file_code."-".$_FILES['doc']['name'][$f];

        $file_name_new = RewriteFile($file_name_new); 

        move_uploaded_file($_FILES["doc"]["tmp_name"][$f], "../../uploads/repair/".$file_name_new);



        if($f_name != ""){
        $query = "INSERT INTO ".DB_PREFIX."repair_document (file_id, repair_id, file_oldname, file_name,file_ext,file_size,file_status) VALUES (NULL, ?, ?, ?, ?, ?, '1')"; 
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $repairid, PDO::PARAM_INT);
        $stmt->bindParam(2, $f_name, PDO::PARAM_STR);
        $stmt->bindParam(3, $file_name_new, PDO::PARAM_STR);
        $stmt->bindParam(4, $f_ext, PDO::PARAM_STR);
        $stmt->bindParam(5, $f_size, PDO::PARAM_INT);
        $stmt->execute();
        }



    }
}

	
	



	$person_oid_enc = base64_encode($personid);
	$repair_oid_enc = base64_encode($repairid);


			$act_enc = base64_encode('edit');
			$msg = "success";
			echo json_encode(['code'=>200, 'msg'=>$msg,'personid'=>$person_oid_enc,'repairid'=>$repair_oid_enc,'act'=>$act_enc,'s'=>$_FILES["files"]]);



}
			
			
			
			?>


