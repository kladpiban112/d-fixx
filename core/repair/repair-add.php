<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "../../core/config.php";
require_once ABSPATH."/checklogin.php";
require_once ABSPATH."/functions.php";
require_once ABSPATH."/PasswordHash.php";
require_once ABSPATH."/resize-class.php";
require_once ABSPATH."/BarcodeQR.php";

$act = filter_input(INPUT_POST, 'act', FILTER_SANITIZE_STRING);
$repairid = filter_input(INPUT_POST, 'repairid', FILTER_SANITIZE_STRING);
$personid = filter_input(INPUT_POST, 'personid', FILTER_SANITIZE_STRING);
$repair_inout = filter_input(INPUT_POST, 'repair_inout', FILTER_SANITIZE_STRING);

$org_id = filter_input(INPUT_POST, 'org_id', FILTER_SANITIZE_STRING);
$cid = filter_input(INPUT_POST, 'cid', FILTER_SANITIZE_STRING);
$prename = filter_input(INPUT_POST, 'prename', FILTER_SANITIZE_STRING);
$fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
$lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
$sex = filter_input(INPUT_POST, 'sex', FILTER_SANITIZE_STRING);
$telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
$birthdate = filter_input(INPUT_POST, 'birthdate', FILTER_SANITIZE_STRING);
$birthdate = date_saveto_db($birthdate);

$house = filter_input(INPUT_POST, 'house', FILTER_SANITIZE_STRING);
$community = filter_input(INPUT_POST, 'community', FILTER_SANITIZE_STRING);
$village = filter_input(INPUT_POST, 'village', FILTER_SANITIZE_STRING);
$road = filter_input(INPUT_POST, 'road', FILTER_SANITIZE_STRING);
$changwat = filter_input(INPUT_POST, 'changwat', FILTER_SANITIZE_STRING);
$ampur = filter_input(INPUT_POST, 'ampur', FILTER_SANITIZE_STRING);
$tambon = filter_input(INPUT_POST, 'tambon', FILTER_SANITIZE_STRING);


$repairdate = filter_input(INPUT_POST, 'repairdate', FILTER_SANITIZE_STRING);
$repairdate = date_saveto_db($repairdate);
$repair_type = filter_input(INPUT_POST, 'repair_type', FILTER_SANITIZE_STRING); // ประเภทการรับบริการ

$person_type = filter_input(INPUT_POST, 'person_type', FILTER_SANITIZE_STRING);
$comp_name = filter_input(INPUT_POST, 'comp_name', FILTER_SANITIZE_STRING);
$comp_code = filter_input(INPUT_POST, 'comp_code', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
$repair_place = filter_input(INPUT_POST, 'repair_place', FILTER_SANITIZE_STRING);

$flag = '1';
$now = date("Y-m-d H:i:s");

$func_orgprofile = getOrgProfile($org_id);
$line_token_key = $func_orgprofile['org_line_token'];



$exist_person = "";
if($act == 'add'){

// check for duplicate email
$stmt = $conn->prepare("SELECT * FROM ".DB_PREFIX."person_main WHERE cid = ? AND org_id = ?   ");
$stmt->execute([$cid,$org_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
//$exist_person = $stmt->fetchColumn();
$exist_person = $stmt->rowCount();

if($exist_person!=0){

	
	$personid = $row['oid'];
	
	$query = "UPDATE ".DB_PREFIX."person_main SET cid = ?, org_id = ?, prename = ?, fname = ?, lname = ?, sex = ?,birthdate = ?,telephone = ?,house = ?,community = ?,road = ?,village = ?,tambon = ?,ampur = ?,changwat = ?,flag = ?,edit_date = ?,edit_users = ?,person_type = ? , comp_name = ? ,comp_code = ? ,email = ? WHERE oid = ? LIMIT 1"; 
	$stmt = $conn->prepare($query);
	$stmt->bindParam(1, $cid, PDO::PARAM_STR);
	$stmt->bindParam(2, $org_id, PDO::PARAM_STR);
	$stmt->bindParam(3, $prename, PDO::PARAM_STR);
	$stmt->bindParam(4, $fname);
	$stmt->bindParam(5, $lname, PDO::PARAM_STR);
	$stmt->bindParam(6, $sex, PDO::PARAM_INT);
	$stmt->bindParam(7, $birthdate, PDO::PARAM_STR);
	$stmt->bindParam(8, $telephone, PDO::PARAM_STR);
	$stmt->bindParam(9, $house, PDO::PARAM_STR);
	$stmt->bindParam(10, $community, PDO::PARAM_STR);
	$stmt->bindParam(11, $road, PDO::PARAM_STR);
	$stmt->bindParam(12, $village, PDO::PARAM_STR);
	$stmt->bindParam(13, $tambon, PDO::PARAM_STR);
	$stmt->bindParam(14, $ampur, PDO::PARAM_STR);
	$stmt->bindParam(15, $changwat, PDO::PARAM_STR);
	$stmt->bindParam(16, $flag, PDO::PARAM_STR);
	$stmt->bindParam(17, $now, PDO::PARAM_STR);
	$stmt->bindParam(18, $logged_user_id, PDO::PARAM_STR);
	$stmt->bindParam(19, $person_type, PDO::PARAM_STR);
	$stmt->bindParam(20, $comp_name, PDO::PARAM_STR);
	$stmt->bindParam(21, $comp_code, PDO::PARAM_STR);
	$stmt->bindParam(22, $email, PDO::PARAM_STR);
	$stmt->bindParam(23, $personid, PDO::PARAM_INT);
	$stmt->execute();

	// AVATAR
		if($_FILES['img_profile']['name'])
		{
		$f = $_FILES['img_profile']['name'];
		$ext = strtolower(substr(strrchr($f, '.'), 1));
		if (($ext!= "jpg") && ($ext != "jpeg") && ($ext != "gif") && ($ext != "png")) 
			{
			}

		else
			{
			$image_code = random_code();
			$image = $image_code."-".$_FILES['img_profile']['name'];
			$image = RewriteFile($image);
			move_uploaded_file($_FILES["img_profile"]["tmp_name"], "../../uploads/temp/".$image);

			// create avatar image
			$resizeObj = new resize("../../uploads/temp/".$image); 
			$resizeObj -> resizeImage(200, 200, 'crop'); // (options: exact, portrait, landscape, auto, crop) 
			$resizeObj -> saveImage("../../uploads/person/".$image);
			
			@unlink ("../../uploads/temp/".$image);
			$sql = "UPDATE ".DB_PREFIX."person_main SET img_profile = ? WHERE oid = ? LIMIT 1"; 
			$conn->prepare($sql)->execute([$image, $personid]);
			}
		}

		$person_oid_enc = base64_encode($personid);

		$repair_code = getRepaircode($org_id,$repairdate);  
		$repair_code = "I".$repair_code;
		$query = "INSERT INTO ".DB_PREFIX."repair_main (repair_id,person_id,org_id, repair_date,repair_code, repair_type,flag,add_date,add_users,repair_place,repair_inout) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?,?,?,?)"; 
		$stmt = $conn->prepare($query);
		$stmt->bindParam(1, $personid, PDO::PARAM_STR);
		$stmt->bindParam(2, $org_id, PDO::PARAM_STR);
		$stmt->bindParam(3, $repairdate, PDO::PARAM_STR);
		$stmt->bindParam(4, $repair_code, PDO::PARAM_STR);
		$stmt->bindParam(5, $repair_type, PDO::PARAM_STR);
		$stmt->bindParam(6, $flag, PDO::PARAM_STR);
		$stmt->bindParam(7, $now, PDO::PARAM_STR);
		$stmt->bindParam(8, $logged_user_id, PDO::PARAM_STR);
		$stmt->bindParam(9, $repair_place, PDO::PARAM_STR);
		$stmt->bindParam(10, $repair_inout, PDO::PARAM_STR);
		$stmt->execute();

		$service_oid = $conn->lastInsertId(); // last inserted ID
		$service_oid_enc = base64_encode($service_oid);

		$flag_status = '1';
		$now = date("Y-m-d H:i:s");
		$status_out = 'I';
		$status_id = '1';

		
		$query_s = "INSERT INTO ".DB_PREFIX."repair_status (oid,repair_id,status_date,status_id,flag,add_date,add_users,status_out) VALUES (NULL,?,?,?,?,?,?,?)"; 
		$stmt_s = $conn->prepare($query_s);
		$stmt_s->bindParam(1, $service_oid, PDO::PARAM_STR);
		$stmt_s->bindParam(2, $now, PDO::PARAM_STR);
		$stmt_s->bindParam(3, $status_id, PDO::PARAM_STR);
		$stmt_s->bindParam(4, $flag_status, PDO::PARAM_STR);
		$stmt_s->bindParam(5, $now, PDO::PARAM_STR);
		$stmt_s->bindParam(6, $logged_user_id, PDO::PARAM_STR);
		$stmt_s->bindParam(7, $status_out, PDO::PARAM_STR);
		$stmt_s->execute();
		addRepairStatus($service_oid);

        // line notify
		//$txt_newjob = "มีงานซ่อมเข้าใหม่ ตรวจสอบที่ ".ADMIN_URL;
		//line_text($txt_newjob,$line_token_key);

		// set BarcodeQR object
		// $qr = new BarcodeQR();

		// // create URL QR code
		// // $eq_id_enc = base64_encode($eq_id);
		// $qr->url(ADMIN_URL."/public/qrcode-repair/index.php?serviceid=$service_oid_enc");

		// // display new QR code image
		// $qr->draw(300, "../../uploads/qrcode-repair/$service_oid");


	
		$act_enc = base64_encode('edit');
		$msg = "success";
		echo json_encode(['code'=>200, 'msg'=>$exist_person,'personid'=>$person_oid_enc,'repairid'=>$service_oid_enc,'act'=>$act_enc,'action'=>$act]);

}else{


$query = "INSERT INTO ".DB_PREFIX."person_main (oid, cid, org_id, prename, fname, lname, sex,birthdate,telephone,house,community,road,village,tambon,ampur,changwat,flag,add_date,add_users,person_type,comp_name,comp_code,email) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,?,?,?,?,?)"; 
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $cid, PDO::PARAM_STR);
$stmt->bindParam(2, $org_id, PDO::PARAM_STR);
$stmt->bindParam(3, $prename, PDO::PARAM_STR);
$stmt->bindParam(4, $fname);
$stmt->bindParam(5, $lname, PDO::PARAM_STR);
$stmt->bindParam(6, $sex, PDO::PARAM_INT);
$stmt->bindParam(7, $birthdate, PDO::PARAM_STR);
$stmt->bindParam(8, $telephone, PDO::PARAM_STR);
$stmt->bindParam(9, $house, PDO::PARAM_STR);
$stmt->bindParam(10, $community, PDO::PARAM_STR);
$stmt->bindParam(11, $road, PDO::PARAM_STR);
$stmt->bindParam(12, $village, PDO::PARAM_STR);
$stmt->bindParam(13, $tambon, PDO::PARAM_STR);
$stmt->bindParam(14, $ampur, PDO::PARAM_STR);
$stmt->bindParam(15, $changwat, PDO::PARAM_STR);
$stmt->bindParam(16, $flag, PDO::PARAM_STR);
$stmt->bindParam(17, $now, PDO::PARAM_STR);
$stmt->bindParam(18, $logged_user_id, PDO::PARAM_STR);
$stmt->bindParam(19, $person_type, PDO::PARAM_STR);
$stmt->bindParam(20, $comp_name, PDO::PARAM_STR);
$stmt->bindParam(21, $comp_code, PDO::PARAM_STR);
$stmt->bindParam(22, $email, PDO::PARAM_STR);
$stmt->execute();

$person_oid = $conn->lastInsertId(); // last inserted ID
$person_oid_enc = base64_encode($person_oid);

$repair_code = getRepaircode($org_id,$repairdate);  
$repair_code = "I".$repair_code; 
$query = "INSERT INTO ".DB_PREFIX."repair_main (repair_id,person_id,org_id, repair_date,repair_code, repair_type,flag,add_date,add_users,repair_place,repair_inout) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?,?,?,?)"; 
		$stmt = $conn->prepare($query);
		$stmt->bindParam(1, $person_oid, PDO::PARAM_STR);
		$stmt->bindParam(2, $org_id, PDO::PARAM_STR);
		$stmt->bindParam(3, $repairdate, PDO::PARAM_STR);
		$stmt->bindParam(4, $repair_code, PDO::PARAM_STR);
		$stmt->bindParam(5, $repair_type, PDO::PARAM_STR);
		$stmt->bindParam(6, $flag, PDO::PARAM_STR);
		$stmt->bindParam(7, $now, PDO::PARAM_STR);
		$stmt->bindParam(8, $logged_user_id, PDO::PARAM_STR);
		$stmt->bindParam(9, $repair_place, PDO::PARAM_STR);
		$stmt->bindParam(10, $repair_inout, PDO::PARAM_STR);
		$stmt->execute();
		addRepairStatus($service_oid);

		$service_oid = $conn->lastInsertId(); // last inserted ID
		$service_oid_enc = base64_encode($service_oid);

		$flag_status = '1';
		$now = date("Y-m-d H:i:s");
		$status_out = 'I';
		$status_id = '1';

		
		$query_s = "INSERT INTO ".DB_PREFIX."repair_status (oid,repair_id,status_date,status_id,flag,add_date,add_users,status_out) VALUES (NULL,?,?,?,?,?,?,?)"; 
		$stmt_s = $conn->prepare($query_s);
		$stmt_s->bindParam(1, $service_oid, PDO::PARAM_STR);
		$stmt_s->bindParam(2, $now, PDO::PARAM_STR);
		$stmt_s->bindParam(3, $status_id, PDO::PARAM_STR);
		$stmt_s->bindParam(4, $flag_status, PDO::PARAM_STR);
		$stmt_s->bindParam(5, $now, PDO::PARAM_STR);
		$stmt_s->bindParam(6, $logged_user_id, PDO::PARAM_STR);
		$stmt_s->bindParam(7, $status_out, PDO::PARAM_STR);
		$stmt_s->execute();
    // line notify
	//$txt_newjob = "มีงานซ่อมเข้าใหม่ ตรวจสอบที่ ".ADMIN_URL;
	//line_text($txt_newjob,$line_token_key);

// set BarcodeQR object
$qr = new BarcodeQR();

// create URL QR code
// $eq_id_enc = base64_encode($eq_id);
$qr->url(ADMIN_URL."/public/qrcode-repair/index.php?serviceid=$service_oid_enc");

// display new QR code image
$qr->draw(300, "../../uploads/qrcode-repair/$service_oid");



// AVATAR
if($_FILES['img_profile']['name'])
	{
	$f = $_FILES['img_profile']['name'];
	$ext = strtolower(substr(strrchr($f, '.'), 1));
	if (($ext!= "jpg") && ($ext != "jpeg") && ($ext != "gif") && ($ext != "png")) 
		{
		}

	else
		{
		$image_code = random_code();
		$image = $image_code."-".$_FILES['img_profile']['name'];
		$image = RewriteFile($image);
		move_uploaded_file($_FILES["img_profile"]["tmp_name"], "../../uploads/temp/".$image);

		// create avatar image
		$resizeObj = new resize("../../uploads/temp/".$image); 
		$resizeObj -> resizeImage(200, 200, 'crop'); // (options: exact, portrait, landscape, auto, crop) 
		$resizeObj -> saveImage("../../uploads/person/".$image);
		
		@unlink ("../../uploads/temp/".$image);
		$sql = "UPDATE ".DB_PREFIX."person_main SET img_profile = ? WHERE oid = ? LIMIT 1"; 
		$conn->prepare($sql)->execute([$image, $person_oid]);
		}
	}

	


			$act_enc = base64_encode('edit');
			$msg = "success";
			echo json_encode(['code'=>200, 'msg'=>$exist_person,'personid'=>$person_oid_enc,'repairid'=>$service_oid_enc,'act'=>$act_enc,'action'=>$act]);
		  
	}	
			
	
	
}else if($act == 'edit'){


$query = "UPDATE ".DB_PREFIX."person_main SET cid = ?, org_id = ?, prename = ?, fname = ?, lname = ?, sex = ?,birthdate = ?,telephone = ?,house = ?,community = ?,road = ?,village = ?,tambon = ?,ampur = ?,changwat = ?,flag = ?,edit_date = ?,edit_users = ?,person_type = ? , comp_name = ? ,comp_code = ? ,email = ? WHERE oid = ? LIMIT 1"; 
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $cid, PDO::PARAM_STR);
$stmt->bindParam(2, $org_id, PDO::PARAM_STR);
$stmt->bindParam(3, $prename, PDO::PARAM_STR);
$stmt->bindParam(4, $fname);
$stmt->bindParam(5, $lname, PDO::PARAM_STR);
$stmt->bindParam(6, $sex, PDO::PARAM_INT);
$stmt->bindParam(7, $birthdate, PDO::PARAM_STR);
$stmt->bindParam(8, $telephone, PDO::PARAM_STR);
$stmt->bindParam(9, $house, PDO::PARAM_STR);
$stmt->bindParam(10, $community, PDO::PARAM_STR);
$stmt->bindParam(11, $road, PDO::PARAM_STR);
$stmt->bindParam(12, $village, PDO::PARAM_STR);
$stmt->bindParam(13, $tambon, PDO::PARAM_STR);
$stmt->bindParam(14, $ampur, PDO::PARAM_STR);
$stmt->bindParam(15, $changwat, PDO::PARAM_STR);
$stmt->bindParam(16, $flag, PDO::PARAM_STR);
$stmt->bindParam(17, $now, PDO::PARAM_STR);
$stmt->bindParam(18, $logged_user_id, PDO::PARAM_STR);
$stmt->bindParam(19, $person_type, PDO::PARAM_STR);
$stmt->bindParam(20, $comp_name, PDO::PARAM_STR);
$stmt->bindParam(21, $comp_code, PDO::PARAM_STR);
$stmt->bindParam(22, $email, PDO::PARAM_STR);
$stmt->bindParam(23, $personid, PDO::PARAM_INT);
$stmt->execute();


$query = "UPDATE ".DB_PREFIX."repair_main SET org_id = ?, repair_date = ?, repair_type = ?,flag = ?,edit_date = ?,edit_users = ?,repair_place = ?  WHERE repair_id = ? LIMIT 1"; 
$stmt = $conn->prepare($query);

$stmt->bindParam(1, $org_id, PDO::PARAM_STR);
$stmt->bindParam(2, $repairdate, PDO::PARAM_STR);
$stmt->bindParam(3, $repair_type, PDO::PARAM_STR);
$stmt->bindParam(4, $flag, PDO::PARAM_STR);
$stmt->bindParam(5, $now, PDO::PARAM_STR);
$stmt->bindParam(6, $logged_user_id, PDO::PARAM_STR);
$stmt->bindParam(7, $repair_place, PDO::PARAM_STR);
$stmt->bindParam(8, $repairid, PDO::PARAM_INT);
$stmt->execute();



// AVATAR
if($_FILES['img_profile']['name'])
	{
	$f = $_FILES['img_profile']['name'];
	$ext = strtolower(substr(strrchr($f, '.'), 1));
	if (($ext!= "jpg") && ($ext != "jpeg") && ($ext != "gif") && ($ext != "png")) 
		{
		}

	else
		{
		$image_code = random_code();
		$image = $image_code."-".$_FILES['img_profile']['name'];
		$image = RewriteFile($image);
		move_uploaded_file($_FILES["img_profile"]["tmp_name"], "../../uploads/temp/".$image);

		// create avatar image
		$resizeObj = new resize("../../uploads/temp/".$image); 
		$resizeObj -> resizeImage(200, 200, 'crop'); // (options: exact, portrait, landscape, auto, crop) 
		$resizeObj -> saveImage("../../uploads/person/".$image);
		
		@unlink ("../../uploads/temp/".$image);
		$sql = "UPDATE ".DB_PREFIX."person_main SET img_profile = ? WHERE oid = ? LIMIT 1"; 
		$conn->prepare($sql)->execute([$image, $personid]);
		}
	}

	

	$person_oid_enc = base64_encode($personid);
	$service_oid_enc = base64_encode($repairid);


			$act_enc = base64_encode('edit');
			$act_enc = base64_encode('edit');
			$msg = "success";
			echo json_encode(['code'=>200, 'msg'=>$exist_person,'personid'=>$person_oid_enc,'repairid'=>$service_oid_enc,'act'=>$act_enc,'action'=>$act]);
			



}
			
			
			
			?>


