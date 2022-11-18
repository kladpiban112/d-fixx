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

$act = filter_input(INPUT_POST, 'act', FILTER_SANITIZE_STRING);
$eq_id = filter_input(INPUT_POST, 'oid', FILTER_SANITIZE_STRING);
$eq_codes = filter_input(INPUT_POST, 'eq_codes', FILTER_SANITIZE_STRING);
$eq_number = filter_input(INPUT_POST, 'eq_number', FILTER_SANITIZE_STRING);
$eq_typeid = filter_input(INPUT_POST, 'eq_typeid', FILTER_SANITIZE_STRING);
$eq_typeother = filter_input(INPUT_POST, 'eq_typeother', FILTER_SANITIZE_STRING);
$org_id = filter_input(INPUT_POST, 'org_id', FILTER_SANITIZE_STRING);
$eq_names = filter_input(INPUT_POST, 'eq_names', FILTER_SANITIZE_STRING);
$eq_desc = filter_input(INPUT_POST, 'eq_desc');
//$eq_desc = $_POST['kt-ckeditor-1'];
$eq_status = filter_input(INPUT_POST, 'eq_status', FILTER_SANITIZE_STRING);
$receive_type = filter_input(INPUT_POST, 'receive_type', FILTER_SANITIZE_STRING);
$receive_other = filter_input(INPUT_POST, 'receive_typeother', FILTER_SANITIZE_STRING);

$receive_date = filter_input(INPUT_POST, 'receive_date', FILTER_SANITIZE_STRING);
$receive_date = date_saveto_db($receive_date);
if($receive_date == ""){
	$receive_date = date("Y-m-d"); 
}
$eq_code_new = getEqcode($org_id,$receive_date);
$donorid = filter_input(INPUT_POST, 'donorid', FILTER_SANITIZE_STRING);
$serviceid = filter_input(INPUT_POST, 'serviceid', FILTER_SANITIZE_STRING);
if($act == 'add'){

// check for duplicate email
if($eq_code == ''){
	$exist_data = 0;
}else{
	$stmt = $conn->prepare("SELECT * FROM ".DB_PREFIX."equipment_main WHERE eq_codes = ?");
	$stmt->execute([$eq_code]);
	$exist_data = $stmt->fetchColumn();

}


if($exist_data!=0){
		$msg = "dup";
		echo json_encode(['code'=>404, 'msg'=>$msg]);
	}else{



$now = date("Y-m-d H:i:s");
//$ip_reg = $_SERVER['REMOTE_ADDR'];
//$host_reg = gethostbyaddr($_SERVER['REMOTE_ADDR']);
//$activation_code = md5(random_code());	

$query = "INSERT INTO ".DB_PREFIX."equipment_main (oid, eq_codes, eq_typeid,eq_typeother, eq_names, eq_desc, org_id, receive_id,receive_other,receive_date, flag,add_date,add_users,donor_id,eq_number) VALUES (NULL, ?, ?, ?, ?,?, ?, ?,?,?, 1, ?, ?,?,?)"; 
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $eq_code_new, PDO::PARAM_STR);
$stmt->bindParam(2, $eq_typeid, PDO::PARAM_STR);
$stmt->bindParam(3, $eq_typeother, PDO::PARAM_STR);
$stmt->bindParam(4, $eq_names, PDO::PARAM_STR);
$stmt->bindParam(5, $eq_desc);
$stmt->bindParam(6, $org_id, PDO::PARAM_STR);
$stmt->bindParam(7, $receive_type, PDO::PARAM_INT);
$stmt->bindParam(8, $receive_other, PDO::PARAM_STR);
$stmt->bindParam(9, $receive_date, PDO::PARAM_STR);
$stmt->bindParam(10, $now, PDO::PARAM_STR);
$stmt->bindParam(11, $logged_user_id, PDO::PARAM_STR);
$stmt->bindParam(12, $donorid, PDO::PARAM_STR);
$stmt->bindParam(13, $eq_number, PDO::PARAM_STR);
$stmt->execute();

$eq_id = $conn->lastInsertId(); // last inserted ID


//create text QR code
//$url = "https://repair.cqc-songkhlapao.com/qrcode/";

// set BarcodeQR object
$qr = new BarcodeQR();

// create URL QR code
$eq_id_enc = base64_encode($eq_id);
$qr->url(ADMIN_URL."/public/qrcode/index.php?eqid=$eq_id_enc&eqcode=$eq_code_new");

// display new QR code image
$qr->draw(300, "../../uploads/qrcode/$eq_id");





// มาจากการรับบริจาค
if($serviceid != ''){

// check for duplicate email
$stmt = $conn->prepare("SELECT * FROM ".DB_PREFIX."donate_data WHERE eq_id = ? AND service_id = ?  AND flag = '1'  ");
$stmt->execute([$eq_id,$serviceid]);
$exist_data = $stmt->fetchColumn();

if($exist_data!=0){
		//$msg = "dup";
		//echo json_encode(['code'=>404, 'msg'=>$msg]);
}else{



$now = date("Y-m-d H:i:s");
$flag = 1;
$query = "INSERT INTO ".DB_PREFIX."donate_data (s_oid, eq_id, service_id,flag,add_date,add_users) VALUES (NULL, ?, ?, ?,?,?)"; 
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $eq_id, PDO::PARAM_STR);
$stmt->bindParam(2, $serviceid, PDO::PARAM_STR);
$stmt->bindParam(3, $flag, PDO::PARAM_STR);
$stmt->bindParam(4, $now, PDO::PARAM_STR);
$stmt->bindParam(5, $logged_user_id, PDO::PARAM_STR);
$stmt->execute();



//$person_oid = $conn->lastInsertId(); // last inserted ID
//$person_oid_enc = base64_encode($person_oid);


//$flag = "5"; // ยืมใช้งาน
//$query = "UPDATE ".DB_PREFIX."equipment_main SET flag = ? WHERE oid = ? LIMIT 1"; 
//$stmt = $conn->prepare($query);
//$stmt->bindParam(1, $flag, PDO::PARAM_STR);
//$stmt->bindParam(2, $eqid, PDO::PARAM_STR);
//$stmt->execute();




			//$act_enc = base64_encode('edit');
			//$msg = "success";
			//echo json_encode(['code'=>200, 'msg'=>$msg,'personid'=>$person_oid_enc,'serviceid'=>$service_oid_enc,'act'=>$act_enc]);
		  
	}	



}

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
		$resizeObj -> saveImage("../../uploads/equipment/".$image);
		
		@unlink ("../../uploads/temp/".$image);
		$sql = "UPDATE ".DB_PREFIX."equipment_main SET eq_img = ? WHERE oid = ? LIMIT 1"; 
		$conn->prepare($sql)->execute([$image, $eq_id]);
		}
	}



			$msg = "success";
			echo json_encode(['code'=>200, 'msg'=>$msg]);
		  
	}	
			
	
	
}else if($act == 'edit'){



	
$now = date("Y-m-d H:i:s");
//$ip_reg = $_SERVER['REMOTE_ADDR'];
//$host_reg = gethostbyaddr($_SERVER['REMOTE_ADDR']);
//$activation_code = md5(random_code());	

$query = "UPDATE ".DB_PREFIX."equipment_main SET  eq_codes = ?, eq_typeid =? ,eq_typeother = ? , eq_names=?, eq_desc = ?, org_id = ?, receive_id = ?,receive_other=?,receive_date = ?, flag =?,edit_date =?,edit_users =?,eq_number = ? WHERE oid = ? LIMIT 1"; 
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $eq_codes, PDO::PARAM_STR);
$stmt->bindParam(2, $eq_typeid, PDO::PARAM_STR);
$stmt->bindParam(3, $eq_typeother, PDO::PARAM_STR);
$stmt->bindParam(4, $eq_names, PDO::PARAM_STR);
$stmt->bindParam(5, $eq_desc);
$stmt->bindParam(6, $org_id, PDO::PARAM_STR);
$stmt->bindParam(7, $receive_type, PDO::PARAM_INT);
$stmt->bindParam(8, $receive_other, PDO::PARAM_STR);
$stmt->bindParam(9, $receive_date, PDO::PARAM_STR);
$stmt->bindParam(10, $eq_status, PDO::PARAM_STR);
$stmt->bindParam(11, $now, PDO::PARAM_STR);
$stmt->bindParam(12, $logged_user_id, PDO::PARAM_STR);
$stmt->bindParam(13, $eq_number, PDO::PARAM_STR);
$stmt->bindParam(14, $eq_id, PDO::PARAM_STR);
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
				$resizeObj -> saveImage("../../uploads/equipment/".$image);
				
				@unlink ("../../uploads/temp/".$image);
				$sql = "UPDATE ".DB_PREFIX."equipment_main SET eq_img = ? WHERE oid = ? LIMIT 1"; 
				$conn->prepare($sql)->execute([$image, $eq_id]);
			}
		}


		for($f=0;$f<count($_FILES["files"]);$f++){

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
			$resizeObj -> saveImage("../../uploads/equipment/".$file_name_new);
			
			@unlink ("../../uploads/temp/".$file_name_new);
	
			}else{
				move_uploaded_file($_FILES["files"]["tmp_name"][$f], "../../uploads/equipment/".$file_name_new);
	
			}
	
			if($f_name != ""){
			$query = "INSERT INTO ".DB_PREFIX."equipment_files (file_id, oid, file_oldname, file_name,file_ext,file_size,file_status) VALUES (NULL, ?, ?, ?, ?, ?, '1')"; 
			$stmt = $conn->prepare($query);
			$stmt->bindParam(1, $eq_id, PDO::PARAM_INT);
			$stmt->bindParam(2, $f_name, PDO::PARAM_STR);
			$stmt->bindParam(3, $file_name_new, PDO::PARAM_STR);
			$stmt->bindParam(4, $f_ext, PDO::PARAM_STR);
			$stmt->bindParam(5, $f_size, PDO::PARAM_INT);
			$stmt->execute();
			}
	
	
	
	}


	$msg = "success";
			echo json_encode(['code'=>200, 'msg'=>$msg]);



}
			
			
			
			?>


