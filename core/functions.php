<?php
//=========================================================================================
//GENERAL FUNCTIONS
//=========================================================================================

function MakeValidURL($url) 
	{
	if($url)
		{	
		if(substr($url, 0, 7)!="http://" and substr($url, 0, 8)!="https://") $url = "http://".$url;		
		return $url;
		}
	else
		return "";	
}


function random_code()
	{
	$cod1 = md5(uniqid(rand(), true));
	$cod2 = substr($cod1, 0, 8);
	return $cod2;
	}


function random_code_captcha()
	{
	$length = 6;	
	$i = 0;
	$rand_string = '';
	$possible_letters = '23456789bcdfghjkmnpqrstvwxyz';
	while ($i < $length) 
		{ 
	    $rand_string .= substr($possible_letters, mt_rand(0, strlen($possible_letters)-1), 1);
	    $i++;
		}
	return $rand_string	;
	}


function RewriteUrl ($string){
	$diacritics_table = array(
        'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj', 'Ž'=>'Z', 'ž'=>'z', 'C'=>'C', 'c'=>'c', 'C'=>'C', 'c'=>'c', 'ć'=>'c', 'Ć'=>'C',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'ą'=>'a', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ę'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'Ł'=>'L', 'ł'=>'l', 
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ě'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'Ó'=>'O', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'R'=>'R', 'r'=>'r', 'č'=>'c', 'ť'=>'t', 'Č'=>'C', 'ö'=>'o', 'ş'=>'s', 'ı'=>'i', 'ń'=>'n', 
		'ğ'=>'g', 'ü'=>'u', 'ș'=>'s', 'ț'=>'t', 'ă'=>'a', 'Ă'=>'A', 'Ș'=>'S', 'Ț'=>'T', 'Ğ'=>'G', 'İ'=>'I', 
		'Ş'=>'s', 'İ'=>'I', 'İ'=>'I', 'İ'=>'I', 'İ'=>'I', 'İ'=>'I', 'İ'=>'I', 'İ'=>'I', 'İ'=>'I', 'İ'=>'I', 'Ż'=>'Z', 'ż'=>'z'
    );
	
	$string = str_replace("\'", "", $string);
	$string2 = strtr($string, $diacritics_table);
    $return = strtolower(trim(preg_replace("/[^0-9a-zA-Z]+/", "-", $string2),"-"));
	if($return=="") $return = random_code();
	return $return;
} 


function Secure ($string){
	$string = addslashes(htmlspecialchars($string, ENT_QUOTES));
	$string = strip_tags($string);
	return trim($string);
}


function RewriteFile ($string){
    return strtolower(trim(preg_replace("/[^0-9a-zA-Z.]+/", "-", $string),"-"));
}


function getActiveContentStatusID()
{
	global $conn;
	$sql = "SELECT id FROM ".DB_PREFIX."content_status WHERE status = 'active' LIMIT 1";
	$rs = $conn->query($sql);
	$row = $rs->fetch_assoc();
	$activeContentStatusID = $row['id'];
	return $activeContentStatusID;
}


function getPendingContentStatusID()
{
	global $conn;
	$sql = "SELECT id FROM ".DB_PREFIX."content_status WHERE status = 'pending' LIMIT 1";
	$rs = $conn->query($sql);
	$row = $rs->fetch_assoc();
	$pendingContentStatusID = $row['id'];
	return $pendingContentStatusID;
}


function getDraftContentStatusID()
{
	global $conn;
	$sql = "SELECT id FROM ".DB_PREFIX."content_status WHERE status = 'draft' LIMIT 1";
	$rs = $conn->query($sql);
	$row = $rs->fetch_assoc();
	$draftContentStatusID = $row['id'];
	return $draftContentStatusID;
}


	
function getAdminRoleId (){
	global $conn;	
	$stmt = $conn->prepare ("SELECT role_id FROM ".DB_PREFIX."users_roles WHERE role = 'admin' LIMIT 1");
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$admin_role_id = $row['role_id'];
	return (int)$admin_role_id;
	$stmt->closeCursor();	
}

function getUserRoleId (){
	global $conn;
	$stmt = $conn->prepare ("SELECT role_id FROM ".DB_PREFIX."users_roles WHERE role = 'user' LIMIT 1");
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$user_role_id = $row['role_id'];
	return (int)$user_role_id;
	$stmt->closeCursor();
}



function checkIfValueInList($value, $list) {
	$list_items = explode(',',$list);
	if (in_array($value, $list_items)) {
	  return 1;
	} else {
	  return 0;
	}
}



// *******************************************************************************
// DATE and TIME functions
// *******************************************************************************

function Now()
	{
	$now = date("Y-m-d H:i:s");
	return $now;
	}

function DateFormat_form($date)
	{
	$date = new DateTime($date);
	return $date->format('m/d/Y');
	}


function DateFormat($date)
	{
	$date_format = "M d Y";

	if($date=='0000-00-00')
		return "-";
	else
		{			
	    date_default_timezone_set('Asia/Bangkok');
		$datetime = date_create($date);
		return $datetime->format($date_format);		
		}
	}


function DateTimeFormat($date)
	{
	$date_format = "M d Y";
	if($date=='0000-00-00 00:00:00')
		return "-";
	else
		{		
	    date_default_timezone_set('Asia/Bangkok');
		$datetime = date_create($date);
		return $datetime->format($date_format.', H:i');		
		}
	}


function TimeFormat($date)
	{
    date_default_timezone_set('Asia/Bangkok');
	$datetime = date_create($date);
	return $datetime->format('H:i');		
	}
	

function xDaysAgo($days)
{
	return date("Y-m-d", strtotime("-$days day"));	
}


//=========================================================================================
//DATABASE FUNCTIONS
//=========================================================================================
function addSettings ($name, $value)
{
	global $conn;
	$stmt = $conn->prepare("SELECT id FROM ".DB_PREFIX."settings WHERE name = ? LIMIT 1");
	$stmt->execute([$name]);
	$exist = $stmt->fetchColumn();

	if($exist==0 and $value)
		{
			// insert
			$stmt = $conn->prepare("INSERT INTO ".DB_PREFIX."settings (id, name, value) VALUES (NULL, ?, ?)");
			$stmt->execute([$name, $value]);
		}
	else
		{
			// update	
			$stmt = $conn->prepare("UPDATE ".DB_PREFIX."settings SET value = ? WHERE name = ? LIMIT 1");
			$stmt->execute([$value, $name]);
		}
}



function addSettingsNotUnique ($name, $value)
{
	global $conn;
	// insert
	$stmt = $conn->prepare("INSERT INTO ".DB_PREFIX."settings (id, name, value) VALUES (NULL, ?, ?)");
	$stmt->execute([$name, $value]);
}

function addUsersExtraUnique ($user_id, $name, $value)
{
	global $conn;		
	$stmt = $conn->prepare("SELECT id FROM ".DB_PREFIX."users_extra WHERE name = ? AND user_id = ? LIMIT 1");
	$stmt->execute([$name, $user_id]);
	$exist = $stmt->fetchColumn();
	
	if($value!="")
	{
		if($exist==0)
			{
				$stmt = $conn->prepare("INSERT INTO ".DB_PREFIX."users_extra (id, user_id, name, value, description) VALUES (NULL, ?, ?, ?, ?)");
				// $stmt->bindParam(1, $user_id);
				// $stmt->bindParam(2, $name);
				// $stmt->bindParam(3, $value);
				// $stmt->execute();
				$stmt->execute([$user_id, $name, $value,'']);
			}
		else
			{
				$stmt = $conn->prepare("UPDATE ".DB_PREFIX."users_extra SET value = ? WHERE name = ? AND user_id = ? LIMIT 1");
				$stmt->execute([$value, $name, $user_id]);
			}
	}
}


function getUsersExtraUnique ($user_id, $name)
{
	global $conn;	
	$stmt = $conn->prepare("SELECT value FROM ".DB_PREFIX."users_extra WHERE name = ? AND user_id = ? LIMIT 1");
	$stmt->execute([$name, $user_id]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$value = stripslashes($row['value']);
	
	return $value;
}


function addTags ($content_id, $tags)
{
	global $conn;	
	// delete all content tags
	$sql = "DELETE FROM ".DB_PREFIX."tags WHERE content_id = '$content_id'";
	$rs = $conn->query($sql);
			
	// insert new tags		
	$tags = explode(",", $tags);
	for ($i = 0; $i < count($tags); ++$i)
	{
		$tag = trim($tags[$i]);
		$tag = addslashes($tag);
		$tag_permalink = RewriteUrl($tag);
	
		$query = "INSERT INTO ".DB_PREFIX."tags (id, content_id, tag, permalink) VALUES (NULL, '$content_id', '$tag', '$tag_permalink')"; 
		if($conn->query($query) === false) { trigger_error('Error: '.$conn->error, E_USER_ERROR); } 
		else { $last_inserted_id = $conn->insert_id; $affected_rows = $conn->affected_rows;	}
	} // end for
}




function getUserDetailsArray($user_id)
{
	global $conn;
	$UserDetailsArray = array();
	
	$stmt = $conn->prepare ("SELECT email, name, permalink, avatar, role_id, active, email_verified, last_activity,telephone,shortname FROM ".DB_PREFIX."users WHERE user_id = ? LIMIT 1");
	$stmt->execute([$user_id]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	$email = $row['email'];
	$name = stripslashes($row['name']);
	$permalink = $row['permalink'];
	$avatar = $row['avatar'];
	$role_id = $row['role_id'];
	$active = $row['active'];
	$email_verified = $row['email_verified'];
	$last_activity = $row['last_activity'];
	$telephone = $row['telephone'];
	$shortname = $row['shortname'];
	
	if(!$avatar) $user_avatar = "no_avatar.png";
	
	$stmt = $conn->prepare ("SELECT role, title FROM ".DB_PREFIX."users_roles WHERE role_id = ? LIMIT 1");
	$stmt->execute([$role_id]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
   	$role = stripslashes($row['role']);
	$role_title = stripslashes($row['title']);
	
	$stmt = $conn->prepare ("SELECT value FROM ".DB_PREFIX."users_extra WHERE user_id = ? AND name = ? LIMIT 1");
	
	$stmt->execute([$user_id, 'bio']);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
   	$bio = stripslashes($row['value']);
	
	$stmt->execute([$user_id, 'register_ip']);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
   	$register_ip = stripslashes($row['value']);
	
	$stmt->execute([$user_id, 'register_time']);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
   	$register_time = stripslashes($row['value']);
	
	$stmt->execute([$user_id, 'register_host']);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
   	$register_host = stripslashes($row['value']);
	
	$UserDetailsArray = array("email" => $email, "name" => $name, "permalink" => $permalink, "avatar" => $avatar, "role_id" => $role_id, "active" => $active, "email_verified" => $email_verified, "last_activity" => $last_activity, "role" => $role, "role_title" => $role_title, "bio" => $bio, "register_ip" => $register_ip, "register_time" => $register_time, "register_host" => $register_host, "telephone" => $telephone, "shortname" => $shortname);
		
	$stmt->closeCursor();		
	return $UserDetailsArray;		
}


function getCategDetailsArray($categ_id)
{
	global $conn;
	$CategDetailsArray = array();

	$sql_db = "SELECT title, permalink FROM ".DB_PREFIX."categories WHERE id = '$categ_id' LIMIT 1";
	$rs_db = $conn->query($sql_db);
	if($conn->query($sql_db) === false) {trigger_error('Error: '.$conn->error, E_USER_ERROR);} 
	$row = $rs_db->fetch_assoc();
	
	$categ_title = stripslashes($row['title']);
	$categ_permalink = stripslashes($row['permalink']);
						
	$CategDetailsArray = array("title" => $categ_title, "permalink" => $categ_permalink);
		
	return $CategDetailsArray;		
}




//**************************************************************************************************
// TIME DIFFERENCE IN HOURS AND MINUTES
function timeFromLastOrder($time)
{
	$now = date("Y-m-d H:i:s");
	$datetime1 = new DateTime($time);
	$datetime2 = new DateTime($now);
	$interval = $datetime1->diff($datetime2);
	return $interval->format('%h')." Hours ".$interval->format('%i')." Minutes";
} 


// By Teamtamweb
function chk_fsize($bytes) {

	if ($bytes < 1000 * 1024)

			  return number_format($bytes / 1024, 2) . " KB";

	  elseif ($bytes < 1000 * 1048576)

			  return number_format($bytes / 1048576, 2) . " MB";

	  elseif ($bytes < 1000 * 1073741824)

			  return number_format($bytes / 1073741824, 2) . " GB";

	  else

		  return number_format($bytes / 1099511627776, 2) . " TB";

   }

   function date_sub_thai($dateformat){
	if($dateformat != '0000-00-00' AND $dateformat != ''){
		$date_show = substr($dateformat,8,2)."/".substr($dateformat,5,2)."/".(substr($dateformat,0,4)+543);
	}else if($dateformat == '' ){
		$date_show = "-";
	}else{	
		$date_show = "-";
	}
 return $date_show ;    
}

function datetime_sub_thai($dateformat){
	if($dateformat != '0000-00-00 00:00:00' AND $dateformat != ''){
		$date_show = substr($dateformat,8,2)."/".substr($dateformat,5,2)."/".(substr($dateformat,0,4)+543)." ".substr($dateformat,11,8);
	}else if($dateformat == '' ){
		$date_show = "-";
	}else{	
		$date_show = "-";
	}
 return $date_show ;    
}

function sdatetime_sub_thai($dateformat){
	if($dateformat != '0000-00-00 00:00:00' AND $dateformat != ''){
		$totime = (substr($dateformat,11,2)*1);
		$timeselect_to = str_pad(($totime+1), 2, "0", STR_PAD_LEFT);
		$date_show = substr($dateformat,8,2)."/".substr($dateformat,5,2)."/".(substr($dateformat,2,2)+43)." ".substr($dateformat,11,5)."-".$timeselect_to.":00";
		
	}else if($dateformat == '' ){
		$date_show = "-";
	}else{	
		$date_show = "-";
	}
 return $date_show ;    
}


function getUserIP() {
	$client  = $_SERVER['HTTP_CLIENT_IP'];
	$forward = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];

	if(filter_var($client, FILTER_VALIDATE_IP)) {
		$ip = $client;
	} elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
		$ip = $forward;
	} else {
		$ip = $remote;
	}
	return $ip;
}


function getOS() { 
global $user_agent;
$os_platform    =   "Unknown OS Platform";
$os_array       =   array(
						'/windows nt 10/i'     =>  'Windows 10',
						'/windows nt 6.3/i'     =>  'Windows 8.1',
						'/windows nt 6.2/i'     =>  'Windows 8',
						'/windows nt 6.1/i'     =>  'Windows 7',
						'/windows nt 6.0/i'     =>  'Windows Vista',
						'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
						'/windows nt 5.1/i'     =>  'Windows XP',
						'/windows xp/i'         =>  'Windows XP',
						'/windows nt 5.0/i'     =>  'Windows 2000',
						'/windows me/i'         =>  'Windows ME',
						'/win98/i'              =>  'Windows 98',
						'/win95/i'              =>  'Windows 95',
						'/win16/i'              =>  'Windows 3.11',
						'/macintosh|mac os x/i' =>  'Mac OS X',
						'/mac_powerpc/i'        =>  'Mac OS 9',
						'/linux/i'              =>  'Linux',
						'/ubuntu/i'             =>  'Ubuntu',
						'/iphone/i'             =>  'iPhone',
						'/ipod/i'               =>  'iPod',
						'/ipad/i'               =>  'iPad',
						'/android/i'            =>  'Android',
						'/blackberry/i'         =>  'BlackBerry',
						'/webos/i'              =>  'Mobile'
					);
foreach ($os_array as $regex => $value) { 
	if (preg_match($regex, $user_agent)) {
		$os_platform    =   $value;
	}
}   
return $os_platform;
}

function getBrowser() {
global $user_agent;
$browser        =   "Unknown Browser";
$browser_array  =   array(
						'/msie/i'       =>  'Internet Explorer',
						'/firefox/i'    =>  'Firefox',
						'/safari/i'     =>  'Safari',
						'/chrome/i'     =>  'Chrome',
						'/edge/i'       =>  'Edge',
						'/opera/i'      =>  'Opera',
						'/netscape/i'   =>  'Netscape',
						'/maxthon/i'    =>  'Maxthon',
						'/konqueror/i'  =>  'Konqueror',
						'/mobile/i'     =>  'Handheld Browser'
					);
foreach ($browser_array as $regex => $value) { 
	if (preg_match($regex, $user_agent)) {
		$browser    =   $value;
	}
}
return $browser;
}

//แปลงค่าตัวเลขเป็นคำอ่านไทย
function ThaiBahtConversion($amount_number)
{
    $amount_number = number_format($amount_number, 2, ".","");
    //echo "<br/>amount = " . $amount_number . "<br/>";
    $pt = strpos($amount_number , ".");
    $number = $fraction = "";
    if ($pt === false) 
        $number = $amount_number;
    else
    {
        $number = substr($amount_number, 0, $pt);
        $fraction = substr($amount_number, $pt + 1);
    }
    
    //list($number, $fraction) = explode(".", $number);
    $ret = "";
    $baht = ReadNumber ($number);
    if ($baht != "")
        $ret .= $baht . "บาท";
    
    $satang = ReadNumber($fraction);
    if ($satang != "")
        $ret .=  $satang . "สตางค์";
    else 
        $ret .= "ถ้วน";
    //return iconv("UTF-8", "TIS-620", $ret);
    return $ret;
}

function ReadNumber($number)
{
    $position_call = array("แสน", "หมื่น", "พัน", "ร้อย", "สิบ", "");
    $number_call = array("", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า");
    $number = $number + 0;
    $ret = "";
    if ($number == 0) return $ret;
    if ($number > 1000000)
    {
        $ret .= ReadNumber(intval($number / 1000000)) . "ล้าน";
        $number = intval(fmod($number, 1000000));
    }
    
    $divider = 100000;
    $pos = 0;
    while($number > 0)
    {
        $d = intval($number / $divider);
        $ret .= (($divider == 10) && ($d == 2)) ? "ยี่" : 
            ((($divider == 10) && ($d == 1)) ? "" :
            ((($divider == 1) && ($d == 1) && ($ret != "")) ? "เอ็ด" : $number_call[$d]));
        $ret .= ($d ? $position_call[$pos] : "");
        $number = $number % $divider;
        $divider = $divider / 10;
        $pos++;
    }
    return $ret;
}




function bahtText(string $amount): string
{
    [$integer, $fraction] = explode('.', number_format(abs($amount), 2, '.', ''));

    $baht = convert($integer);
    $satang = convert($fraction);

    $output = $amount < 0 ? 'ลบ' : '';
    $output .= $baht ? $baht.'บาท' : '';
    $output .= $satang ? $satang.'สตางค์' : 'ถ้วน';

    return $baht.$satang === '' ? 'ศูนย์บาทถ้วน' : $output;
}

function convert(string $number): string
{
    $values = ['', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'];
    $places = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];
    $exceptions = ['หนึ่งสิบ' => 'สิบ', 'สองสิบ' => 'ยี่สิบ', 'สิบหนึ่ง' => 'สิบเอ็ด'];

    $output = '';

    foreach (str_split(strrev($number)) as $place => $value) {
        if ($place % 6 === 0 && $place > 0) {
            $output = $places[6].$output;
        }

        if ($value !== '0') {
            $output = $values[$value].$places[$place % 6].$output;
        }
    }

    foreach ($exceptions as $search => $replace) {
        $output = str_replace($search, $replace, $output);
    }

    return $output;
}


# ฟังก์ชั่นหาจำนวนวันที่ห่างกัน 2 จำนวน เอก
# Ex: getNumDay("yyyy-mm-dd","yyyy-mm-dd")
function getNumDay($d1,$d2){
	$dArr1    = preg_split("/-/", $d1);
	list($year1, $month1, $day1) = $dArr1;
	$Day1 =  mktime(0,0,0,$month1,$day1,$year1);

	$dArr2    = preg_split("/-/", $d2);
	list($year2, $month2, $day2) = $dArr2;
	$Day2 =  mktime(0,0,0,$month2,$day2,$year2);

	return round(abs( $Day2 - $Day1 ) / 86400 )+1;
}
# ฟังก์ชั่นแสดงเดือนไทย
$thai_day_arr=array("อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์");
$thai_month_arr=array(
    "0"=>"",
    "1"=>"มกราคม",
    "2"=>"กุมภาพันธ์",
    "3"=>"มีนาคม",
    "4"=>"เมษายน",
    "5"=>"พฤษภาคม",
    "6"=>"มิถุนายน",
    "7"=>"กรกฎาคม",
    "8"=>"สิงหาคม",
    "9"=>"กันยายน",
    "10"=>"ตุลาคม",
    "11"=>"พฤศจิกายน",
    "12"=>"ธันวาคม"
);
function thai_month($time){
    global $thai_day_arr,$thai_month_arr;
    //$thai_date_return="วัน".$thai_day_arr[date("w",$time)];
    //$thai_date_return.= "ที่ ".date("j",$time);
    //$thai_date_return.=" เดือน".$thai_month_arr[date("n",$time)];
    //$thai_date_return.= " พ.ศ.".(date("Yํ",$time)+543);
	//$thai_date_return.= "  ".date("H:i",$time)." น.";

	 $thai_date_return =" เดือน".$thai_month_arr[date("n",$time)];
    return $thai_date_return;
}


function month_to_thai_full($mm){
	switch ($mm) :
		 case  "01": return 'มกราคม';
		 case  "02": return 'กุมภาพันธ์';
		case  "03": return 'มีนาคม';
		case  "04": return 'เมษายน';
		case  "05": return 'พฤษภาคม';
		case  "06": return 'มิถุนายน';
		case  "07": return 'กรกฎาคม';
		case  "08": return 'สิงหาคม';
		case  "09": return 'กันยายน';
		case  "10": return 'ตุลาคม';
		case  "11": return 'พฤศจิกายน';
		case  "12": return 'ธันวาคม';
	endswitch;
}

function month_to_thai($mm){
	switch ($mm) :
		 case  "01": return 'ม.ค.';
		 case  "02": return 'ก.พ.';
		case  "03": return 'มี.ค.';
		case  "04": return 'เม.ษ.';
		case  "05": return 'พ.ค.';
		case  "06": return 'มิ.ย.';
		case  "07": return 'ก.ค.';
		case  "08": return 'ส.ค.';
		case  "09": return 'ก.ย.';
		case  "10": return 'ต.ค.';
		case  "11": return 'พ.ย.';
		case  "12": return 'ธ.ค.';
	endswitch;
}


/* CQC */


function date_saveto_db($date_param){
	if($date_param==""){
		$date_return = NULL;
	}else{
		$date_return = (substr($date_param,6,4)-543)."-".substr($date_param,3,2)."-".substr($date_param,0,2);
	}

	return $date_return;
}

function decimal_saveto_db($decimal_param){
	if($decimal_param==""){
		$decimal_return = NULL;
	}else{
		$decimal_return = $decimal_param;
	}
	return $decimal_return;
}

function date_db_2form($date_param){
	if($date_param==""){
		$date_return = NULL;
	}else{
		$date_return = substr($date_param,8,2)."/".substr($date_param,5,2)."/".(substr($date_param,0,4)+543);
	}

	return $date_return;
}


function getModuleName($module){
	global $conn;
	  if($module != ""){
		$stmt = $conn->prepare ("SELECT * FROM users_permiss WHERE module =  ? LIMIT 1");
			$stmt->execute([$module]);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$module_name = $row['m_name'];
	  }else  {
		$module_name = "";
	  }
	
	return $module_name;
}



function getEqcode($orgid,$datereceive)
		{
			global $conn;
			//$yearnow = date("Y");
			//$monthnow = date("m");
			//$yearnowthai = substr(($yearnow+543),2,2);

			//$datereceive;

			$yearnowthai =  (substr($datereceive,2,2)+43);
			$yearnowfull =  (substr($datereceive,0,4)+543);
			$monthnow = substr($datereceive,5,2);

			$stmt = $conn->prepare ("SELECT MAX(code_number) AS last_number FROM ".DB_PREFIX."equipment_code WHERE org_id = ? AND code_month = ? AND code_year = ? LIMIT 1 ");
			$stmt->execute([$orgid,$monthnow,$yearnowfull]);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$lastnumber = $row['last_number'];
			if($lastnumber == ''){
				$n = '1';
				$num = '0001';
			}else{
				$n = ($lastnumber+1);
				$num = str_pad(($lastnumber+1), 4, "0", STR_PAD_LEFT);
			}
			$stmt = $conn->prepare("INSERT INTO ".DB_PREFIX."equipment_code (org_id,code_month,code_year,code_number)VALUE(?,?,?,?)");
			$stmt->execute([$orgid,$monthnow,$yearnowfull, $n]);

			$orgid_new = str_pad($orgid, 4, "0", STR_PAD_LEFT);
			$eqcode = $orgid_new."-".$yearnowthai.$monthnow.$num;
			return $eqcode;
		}	

		function getOrgAddr($orgid){
			global $conn;
			  if($orgid != ""){
				$stmt = $conn->prepare ("SELECT o.*,c.changwatname,a.ampurname,t.tambonname
				FROM org_main o
				LEFT JOIN cchangwat c ON o.org_changwat = c.changwatcode
				LEFT JOIN campur a ON CONCAT(o.org_changwat,o.org_ampur) = a.ampurcodefull
				LEFT JOIN ctambon t ON CONCAT(o.org_changwat,o.org_ampur,o.org_tambon) = t.tamboncodefull
				WHERE o.org_id = ? ");
					$stmt->execute([$orgid]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$org_address = $row['org_address'];
					$changwatname = $row['changwatname'];
					$ampurname = $row['ampurname'];
					$tambonname = $row['tambonname'];
					$addr =  $org_address."&nbsp; ต.".$tambonname."&nbsp; อ.".$ampurname."&nbsp;&nbsp; จ.".$changwatname;
			  }else  {
				$addr = "";
			  }
			
			return $addr;
		}

		function getOrgName($orgid){
			global $conn;
			  if($orgid != ""){
				$stmt = $conn->prepare ("SELECT o.* FROM org_main o WHERE o.org_id = ? ");
					$stmt->execute([$orgid]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$org_name = $row['org_name'];
					
			  }else  {
				$org_name = "";
			  }
			
			return $org_name;
		}

		function getOrgTax($orgid){
			global $conn;
			  if($orgid != ""){
				$stmt = $conn->prepare ("SELECT o.* FROM org_main o WHERE o.org_id = ? ");
					$stmt->execute([$orgid]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$org_name = $row['Tax_id'];
					
			  }else  {
				$org_name = "";
			  }
			
			return $org_name;
		}
function getOrgPostcode($orgid){
			global $conn;
			  if($orgid != ""){
				$stmt = $conn->prepare ("SELECT o.* FROM org_main o WHERE o.org_id = ? ");
					$stmt->execute([$orgid]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$org_name = $row['post_code'];
					
			  }else  {
				$org_name = "";
			  }
			
			return $org_name;
		}
		function getOrgLogo($orgid){
			global $conn;
			  if($orgid != ""){
				$stmt = $conn->prepare ("SELECT o.* FROM org_main o WHERE o.org_id = ? ");
					$stmt->execute([$orgid]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$org_logo = $row['org_logo'];
					
			  }else  {
				$org_logo = "";
			  }
			
			return $org_logo;
		}

		function getUsername($userid){
			global $conn;
			  if($userid != ""){
				$stmt = $conn->prepare ("SELECT u.* FROM users u WHERE u.user_id = ? ");
					$stmt->execute([$userid]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$fullname = $row['name'];
					
			  }else  {
				$fullname = "";
			  }
			
			return $fullname;
		}


function addSpareReceive ($act, $spare_id,$spare_quantity,$spare_unit)
{
	global $conn;

	$stmt = $conn->prepare ("SELECT u.spare_unit_master,u.spare_unit_cal FROM ".DB_PREFIX."spare_main  u WHERE u.spare_id = ? ");
		$stmt->execute([$spare_id]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$spare_unit_master = $row['spare_unit_master'];
		$spare_unit_cal = $row['spare_unit_cal'];
		// แปลงจากหน่วยหลักเป็นหนวยรอง
		if($spare_unit == $spare_unit_master){

			$unit_cal = $spare_unit_cal;

		}else{
			$unit_cal = 1;
		}


	// insert
    if($act=="add"){

		$stmt = $conn->prepare ("SELECT u.* FROM ".DB_PREFIX."spare_main  u WHERE u.spare_id = ? ");
		$stmt->execute([$spare_id]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$spare_stock_current = $row['spare_stock'];
		$spare_quantity_add = $spare_stock_current + ($spare_quantity*$unit_cal);

		$stmt = $conn->prepare("UPDATE ".DB_PREFIX."spare_main  SET  spare_stock = ?  WHERE spare_id = ?  ");
		$stmt->execute([$spare_quantity_add, $spare_id]);

	}else if($act=="delete"){
		$stmt = $conn->prepare ("SELECT u.* FROM ".DB_PREFIX."spare_main  u WHERE u.spare_id = ? ");
		$stmt->execute([$spare_id]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$spare_stock_current = $row['spare_stock'];
		$spare_quantity_del = $spare_stock_current - ($spare_quantity*$unit_cal);

		$stmt = $conn->prepare("UPDATE ".DB_PREFIX."spare_main  SET  spare_stock = ?  WHERE spare_id = ?  ");
		$stmt->execute([$spare_quantity_del, $spare_id]);

	}
}


function getRepaircode($orgid,$datereceive)
		{
			global $conn;
			//$yearnow = date("Y");
			//$monthnow = date("m");
			//$yearnowthai = substr(($yearnow+543),2,2);

			//$datereceive;

			$yearnowthai =  (substr($datereceive,2,2)+43);
			$yearnowfull =  (substr($datereceive,0,4)+543);
			$monthnow = substr($datereceive,5,2);

			$stmt = $conn->prepare ("SELECT MAX(code_number) AS last_number FROM ".DB_PREFIX."repair_code WHERE org_id = ? AND code_month = ? AND code_year = ? LIMIT 1 ");
			$stmt->execute([$orgid,$monthnow,$yearnowfull]);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$lastnumber = $row['last_number'];
			if($lastnumber == ''){
				$n = '1';
				$num = '0001';
			}else{
				$n = ($lastnumber+1);
				$num = str_pad(($lastnumber+1), 4, "0", STR_PAD_LEFT);
			}
			$stmt = $conn->prepare("INSERT INTO ".DB_PREFIX."repair_code (org_id,code_month,code_year,code_number)VALUE(?,?,?,?)");
			$stmt->execute([$orgid,$monthnow,$yearnowfull, $n]);

			$orgid_new = str_pad($orgid, 4, "0", STR_PAD_LEFT);
			$eqcode = $orgid_new."-".$yearnowthai.$monthnow.$num;
			return $eqcode;
		}	


		function addRepairStatus($repairid){
			global $conn;
			  if($repairid != ""){
				    $stmt = $conn->prepare ("SELECT max(oid) AS max_id FROM repair_status  WHERE repair_id = ? AND flag = '1' ");
					$stmt->execute([$repairid]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$max_id = $row['max_id'];


					$stmt = $conn->prepare ("SELECT status_id FROM repair_status  WHERE oid = ? AND flag = '1' ");
					$stmt->execute([$max_id]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$status_id = $row['status_id'];

					$stmt = $conn->prepare("UPDATE ".DB_PREFIX."repair_main  SET  repair_status = ?  WHERE repair_id = ?  ");
		            $stmt->execute([$status_id, $repairid]);
					$org_name = "";

					
			  }else  {
				$org_name = "";
			  }
			
			return $org_name;
		}


		function getPersonAddr($personid){
			global $conn;
			  if($personid != ""){
				$stmt = $conn->prepare ("SELECT o.*,c.changwatname,a.ampurname,t.tambonname
				FROM person_main o
				LEFT JOIN cchangwat c ON o.changwat = c.changwatcode
				LEFT JOIN campur a ON CONCAT(o.changwat,o.ampur) = a.ampurcodefull
				LEFT JOIN ctambon t ON CONCAT(o.changwat,o.ampur,o.tambon) = t.tamboncodefull
				WHERE o.oid = ? ");
					$stmt->execute([$personid]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$house = $row['house'];
					$village = $row['village'];
					$changwatname = $row['changwatname'];
					$ampurname = $row['ampurname'];
					$tambonname = $row['tambonname'];
					$addr =  "".$house." ม.".$village." ต.".$tambonname." อ.".$ampurname." จ.".$changwatname;
			  }else  {
				$addr = "";
			  }
			
			return $addr;
		}


		function getDonorAddr($personid){
			global $conn;
			  if($personid != ""){
				$stmt = $conn->prepare ("SELECT o.*,c.changwatname,a.ampurname,t.tambonname
				FROM donor_main o
				LEFT JOIN cchangwat c ON o.changwat = c.changwatcode
				LEFT JOIN campur a ON CONCAT(o.changwat,o.ampur) = a.ampurcodefull
				LEFT JOIN ctambon t ON CONCAT(o.changwat,o.ampur,o.tambon) = t.tamboncodefull
				WHERE o.oid = ? ");
					$stmt->execute([$personid]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$house = $row['house'];
					$village = $row['village'];
					$changwatname = $row['changwatname'];
					$ampurname = $row['ampurname'];
					$tambonname = $row['tambonname'];
					$addr =  "".$house." ม.".$village." ต.".$tambonname." อ.".$ampurname." จ.".$changwatname;
			  }else  {
				$addr = "";
			  }
			
			return $addr;
		}


		function getOrgTelephone($orgid){
			global $conn;
			  if($orgid != ""){
				$stmt = $conn->prepare ("SELECT o.* FROM org_main o WHERE o.org_id = ? ");
					$stmt->execute([$orgid]);
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$org_telephone = $row['org_telephone'];
					
			  }else  {
				$org_telephone = "";
			  }
			
			return $org_telephone;
		}

		function getCaregivername($caregiverid)
        {
            global $conn;
            if ($caregiverid != '') {
                $stmt = $conn->prepare('SELECT u.* FROM caregiver_main u WHERE u.oid = ? ');
                $stmt->execute([$caregiverid]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $fullname = $row['fname'].' '.$row['lname'];
            } else {
                $fullname = '';
            }

            return $fullname;
        }
        //eak
        function chk_payment_status($pstatus)
        {
            switch ($pstatus) :
                case '0': return 'ไม่อนุมัติ';
            case '1': return 'อนุมัติ';
            case '7': return 'ส่งเบิก';
            case '9': return 'รอตรวจสอบ';
            endswitch;
		}
		


		// function getRepairoutcode($orgid,$repairoutdate,$repairid)
		// {
		// 	global $conn;

		// 	//$repairid = $repairid;
		// 	$yearnowthai =  (substr($repairoutdate,2,2)+43);
		// 	$yearnowfull =  (substr($repairoutdate,0,4)+543);
		// 	$monthnow = substr($repairoutdate,5,2);

		// 	$stmt_chk = $conn->prepare ("SELECT repair_outcode FROM ".DB_PREFIX."repair_main WHERE flag = '1' AND  repair_id  = ?  LIMIT 1  ");

		// 	$stmt_chk->execute([$repairid]);
		// 	$row_chk = $stmt_chk->fetch(PDO::FETCH_ASSOC);
		// 	$repair_outcode = $row_chk['repair_outcode'];
        //     if($repair_outcode == ""){
		// 	$stmt = $conn->prepare ("SELECT MAX(code_number) AS last_number FROM ".DB_PREFIX."repair_outcode WHERE org_id = ? AND code_month = ? AND code_year = ? LIMIT 1 ");
		// 	$stmt->execute([$orgid,$monthnow,$yearnowfull]);
		// 	$row = $stmt->fetch(PDO::FETCH_ASSOC);
		// 	$lastnumber = $row['last_number'];
		// 	if($lastnumber == ''){
		// 		$n = '1';
		// 		$num = '0001';
		// 	}else{
		// 		$n = ($lastnumber+1);
		// 		$num = str_pad(($lastnumber+1), 4, "0", STR_PAD_LEFT);
		// 	}
		// 	$stmt = $conn->prepare("INSERT INTO ".DB_PREFIX."repair_outcode (org_id,code_month,code_year,code_number)VALUE(?,?,?,?)");
		// 	$stmt->execute([$orgid,$monthnow,$yearnowfull, $n]);

		// 	$orgid_new = str_pad($orgid, 4, "0", STR_PAD_LEFT);
		// 	$outcode = "O-".$orgid_new."-".$yearnowthai.$monthnow.$num;
		// 	}else{
		// 			$outcode = $repair_outcode;
		// 		}
		// 	return $outcode;
		// }	


		function getRepairoutcode($orgid,$datereceive)
		{
			global $conn;
			//$yearnow = date("Y");
			//$monthnow = date("m");
			//$yearnowthai = substr(($yearnow+543),2,2);

			//$datereceive;

			$yearnowthai =  (substr($datereceive,2,2)+43);
			$yearnowfull =  (substr($datereceive,0,4)+543);
			$monthnow = substr($datereceive,5,2);

			$stmt = $conn->prepare ("SELECT MAX(code_number) AS last_number FROM ".DB_PREFIX."repair_outcode WHERE org_id = ? AND code_month = ? AND code_year = ? LIMIT 1 ");
			$stmt->execute([$orgid,$monthnow,$yearnowfull]);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$lastnumber = $row['last_number'];
			if($lastnumber == ''){
				$n = '1';
				$num = '0001';
			}else{
				$n = ($lastnumber+1);
				$num = str_pad(($lastnumber+1), 4, "0", STR_PAD_LEFT);
			}
			$stmt = $conn->prepare("INSERT INTO ".DB_PREFIX."repair_outcode (org_id,code_month,code_year,code_number)VALUE(?,?,?,?)");
			$stmt->execute([$orgid,$monthnow,$yearnowfull, $n]);

			$orgid_new = str_pad($orgid, 4, "0", STR_PAD_LEFT);
			$eqcode = $orgid_new."-".$yearnowthai.$monthnow.$num;
			return $eqcode;
		}	




function lastInvoiceNumber($invoicetype, $period){
    global $conn;

    $stmt = $conn->prepare("SELECT lastnumber FROM ".DB_PREFIX."payment_last_invoice_number WHERE invoicetype = ? and period = ? LIMIT 1");
    $stmt->execute([$invoicetype, $period]);
    $exist = $stmt->fetch(PDO::FETCH_ASSOC);

    $doc_year = substr($period,0,4)+543;
    $doc_year = substr($doc_year,-2);
    $doc_mm = substr($period,-2);

    if($exist  === false){
        $stmt = $conn->prepare("INSERT INTO ".DB_PREFIX."payment_last_invoice_number (invoicetype, period, lastnumber) VALUES (?, ?, 1)");
        $stmt->execute([$invoicetype, $period]);

        $lastnumber =  $doc_year.$doc_mm.'0001';
    }else{
        $number = $exist['lastnumber'] + 1;
        $stmt = $conn->prepare("UPDATE ".DB_PREFIX."payment_last_invoice_number SET lastnumber = ? WHERE invoicetype = ? and period =?  LIMIT 1");
        $stmt->execute([$number, $invoicetype, $period]);

        $numberpad = str_pad($number,4,"0",STR_PAD_LEFT);
        $lastnumber =  $doc_year.$doc_mm.$numberpad;
    }

    return $lastnumber;

}


function getRepairPayment($repairid){
    global $conn;
    $stmt = $conn->prepare("SELECT cost,cost_payment,cost_success  
	FROM ".DB_PREFIX."repair_payment WHERE repair_id = ?  AND flag = '1'  LIMIT 1");
    $stmt->execute([$repairid]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $rs = [
        'cost' => $result['cost'],
        'cost_payment' => $result['cost_payment'],
        'cost_success' => $result['cost_success'],
    ];
    return $rs;
}


function getOrgProfile($orgid){
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM ".DB_PREFIX."org_main WHERE org_id = ?  AND flag = '1'  LIMIT 1");
    $stmt->execute([$orgid]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $rs = [
        'org_name' => $result['org_name'],
        'org_shortname' => $result['org_shortname'],
        'org_line_token' => $result['org_line_token'],
    ];
    return $rs;
}


	// Line Notify 
	function line_text($text,$cfg_line_notify_key){
		$chOne = curl_init(); 
		curl_setopt( $chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify"); 
		// SSL USE 
		curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0); 
		curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0); 
		//POST 
		curl_setopt( $chOne, CURLOPT_POST, 1); 
		// Message 
		curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=".$text.""); 
		//ถ้าต้องการใส่รุป ให้ใส่ 2 parameter imageThumbnail และimageFullsize
		//curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=hi&imageThumbnail=http://www.wisadev.com/wp-content/uploads/2016/08/cropped-wisadevLogo.png&imageFullsize=http://www.wisadev.com/wp-content/uploads/2016/08/cropped-wisadevLogo.png"); 
		// follow redirects 
		curl_setopt( $chOne, CURLOPT_FOLLOWLOCATION, 1); 
		//ADD header array 
		$headers = array( 'Content-type: application/x-www-form-urlencoded', "Authorization: Bearer ".$cfg_line_notify_key, );
	  
	  //$headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer NqUOvyYjGiN3hY9ekUKbaieNLhBog4suIA1WOwcK1SA', );
	  
		curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers); 
		//RETURN 
		curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec( $chOne ); 
		//Check error 
		if(curl_error($chOne)) { echo 'error:' . curl_error($chOne); } 
		else { $result_ = json_decode($result, true); 
		echo "status : ".$result_['status']; echo "message : ". $result_['message']; } 
		//Close connect 
		curl_close( $chOne ); 
	  
	  }


	  function getQuotation($orgid,$dateqt)
		{
			global $conn;


			$yearnowthai =  (substr($dateqt,2,2)+43);
			$yearnowfull =  (substr($dateqt,0,4)+543);
			$monthnow = substr($dateqt,5,2);

			$stmt = $conn->prepare ("SELECT MAX(code_number) AS last_number FROM ".DB_PREFIX."repair_quotation_number WHERE org_id = ? AND code_month = ? AND code_year = ? LIMIT 1 ");
			$stmt->execute([$orgid,$monthnow,$yearnowfull]);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$lastnumber = $row['last_number'];
			if($lastnumber == ''){
				$n = '1';
				$num = '0001';
			}else{
				$n = ($lastnumber+1);
				$num = str_pad(($lastnumber+1), 4, "0", STR_PAD_LEFT);
			}
			$stmt = $conn->prepare("INSERT INTO ".DB_PREFIX."repair_quotation_number (org_id,code_month,code_year,code_number)VALUE(?,?,?,?)");
			$stmt->execute([$orgid,$monthnow,$yearnowfull, $n]);

			//$orgid_new = str_pad($orgid, 4, "0", STR_PAD_LEFT);
			$qtcode = "QT".$yearnowthai.$monthnow.$num;
			return $qtcode;
		}
	  