<?php

session_start();
require_once '../../core/config.php';
require_once ABSPATH.'/checklogin.php';
require_once ABSPATH.'/functions.php';

$exc_name = 'rpt-repair-main'; // ชื่อ files excel
$strExcelFileName = $exc_name.'.xls'; // ชื่อไฟล์ excel

header("Content-Type: application/x-msexcel; name=\"$strExcelFileName\"");
header("Content-Disposition: inline; filename=\"$strExcelFileName\"");
header('Pragma:no-cache');

//$repairdate = filter_input(INPUT_GET, 'repairdate', FILTER_SANITIZE_STRING);
//$repairdate_ymd = date_saveto_db($repairdate);
$startdate = filter_input(INPUT_GET, 'startdate', FILTER_SANITIZE_STRING);
$startdate_ymd = date_saveto_db($startdate);

$enddate = filter_input(INPUT_GET, 'enddate', FILTER_SANITIZE_STRING);
$enddate_ymd = date_saveto_db($enddate);
$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);

if($search != ""){
    $search_data = " AND p.cid LIKE '%$search%' OR p.fname LIKE '%$search%'  ";
}
if(($startdate_ymd != "") AND ($enddate_ymd != "")){
    $repairdate_data = " AND u.repair_date BETWEEN '$startdate_ymd'  AND  '$enddate_ymd' ";
}else if(($startdate_ymd != "") AND ($enddate_ymd == "")){
    $repairdate_data = " AND u.repair_date >= '$startdate_ymd'  ";
}

// if($repairdate_ymd != ""){
//     $repairdate_data = " AND u.repair_date = '$repairdate_ymd'  ";
// }
if($status != ""){
    $status_data = " AND u.repair_status = '$status'  ";
}
error_reporting(0);
?>


<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns="http://www.w3.org/TR/REC-html40">
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
    <!--<strong>&nbsp;</strong><br>-->
    <div id="SiXhEaD_Excel" align=center x:publishsource="Excel">

        <!-- begin::Card-->
        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <div id="printableArea">
                    <!-- begin: Invoice-->
                    <!-- begin: Invoice header-->
                    <div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
                        <div class="col-md-9">


                        </div>
                    </div>
                    <!-- end: Invoice header-->

                    <!-- begin: Invoice body-->
                    <div class="row justify-content-center py-8 px-8 py-md-5 px-md-0">
                        <div class="col-md-9">

                            <?php
                    //$conditions = " AND s.payment_id = '$paymentid' ";

                    if($logged_user_role_id == '1'){
                        $conditions = " ";
                    }else{
                        $conditions = " AND o.org_id = '$logged_org_id' ";
                    }
                

                    $stmt_data = $conn->prepare("SELECT u.*,p.*,o.org_shortname ,rs.staff_id,sm.*,rt.status_date,t.repair_typetitle,if(e.eq_code IS NOT NULL ,e.eq_code,u.eq_code) AS eq_code, if(e.eq_name IS NOT NULL ,e.eq_name,u.eq_name) AS eq_name,st.status_title
                    FROM ".DB_PREFIX."repair_main u 
                    LEFT JOIN ".DB_PREFIX."org_main o ON u.org_id = o.org_id 
                    LEFT JOIN ".DB_PREFIX."repair_type t ON u.repair_type = t.repair_typeid
                    LEFT JOIN ".DB_PREFIX."person_main p ON u.person_id = p.oid
                    LEFT JOIN ".DB_PREFIX."cprename pr ON p.prename = pr.id_prename
                    LEFT JOIN ".DB_PREFIX."equipment_main e ON u.eq_id = e.oid
                    LEFT JOIN ".DB_PREFIX."repair_status_type st ON u.repair_status = st.status_typeid
                    LEFT JOIN  ".DB_PREFIX."(SELECT * FROM repair_staff WHERE add_date IN (SELECT max(add_date) FROM repair_staff GROUP BY service_id )) AS rs ON u.repair_id = rs.service_id
                    LEFT JOIN  ".DB_PREFIX."staff_main sm ON rs.staff_id = sm.oid
                    LEFT JOIN  ".DB_PREFIX."(SELECT * FROM repair_status WHERE add_date IN (SELECT max(add_date) FROM repair_status GROUP BY repair_id )) AS rt ON u.repair_id = rt.repair_id



                    WHERE u.flag != 0  $conditions $search_data  $repairdate_data $status_data
                    ORDER BY u.repair_id DESC
                    $max");
                    $stmt_data->execute();
                    $numb_data = $stmt_data->columnCount(); // จำนวน column

                    echo "<table x:str border=1 cellpadding=0 cellspacing=1 width=100% style='border-collapse:collapse'>";
                    if ($numb_data > 0) {
                        ?>


                            <thead>

                                <tr>
                                    <th class="text-center">ลำดับ</th>
                                    <th>เลขที่แจ้งซ่อม</th>
                                    <th>วันที่แจ้งซ่อม</th>
                                    <th>ผ่านมาแล้ว</th>
                                    <th>ประเภทแจ้งซ่อม</th>
                                    <th>สถานที่</th>
                                    <th>อุปกรณ์</th>
                                    <th>อาการแจ้งซ่อม</th>
                                    <th>ผู้แจ้ง</th>
                                    <th>วันที่ออกปฏิบัติงาน</th>
                                    <th>สถานะซ่อม</th>
                                    <th>ผู้ปฏิบัติงาน</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php

            $i = 0;
                        while ($row = $stmt_data->fetch(PDO::FETCH_ASSOC)) {
                            $i++;
                            $repair_code = $row['repair_code'];
                            $repairid = $row['repair_id'];
                            $repairid_enc = base64_encode($repairid);
                            $personid = $row['person_id'];
                            $personid_enc = base64_encode($personid);

                            $prename = $row['prename_title'];
                            $fname = $row['fname'];
                            $lname = $row['lname'];
                            $fullname = $prename.$fname." ".$lname;
                            $cid = $row['cid'];
                            $org_name = $row['org_name'];
                            $org_shortname = $row['org_shortname'];
                            $birthdate = date_db_2form($row['birthdate']);
                            $img_profile = $row['img_profile'];

                            $repair_date = date_db_2form($row['repair_date']);
                            $repairtype = $row['repair_type'];
                            $repair_typetitle = $row['repair_typetitle'];
                            $repair_title = $row['repair_title'];
                            $eq_name = $row['eq_name'];
                            $eq_id = $row['eq_id'];
                            $eq_code = $row['eq_code'];
                            $status_title = $row['status_title'];

                            $repair_status = $row['repair_status'];
                            $staff_name = $row['sfname'].' '.$row['slname'].'';
                            $d1=strtotime($row['repair_date']);
				            $d2=ceil((time()-$d1)/60.5/60/24);
                            $comp_name = $row['comp_name'];
                            $status_date = date_db_2form($row['status_date']);




                            $return_date = date_db_2form($row['return_date']);

                            if(($repair_status == '3') AND ($return_date != "")){

                                $return_status = "รับคืนแล้ว";

                            }else{
                                $return_status = "";
                            }

                            $stmt_spare = $conn->prepare ("SELECT SUM(u.spare_price) AS sum_price
                            ,GROUP_CONCAT(s.spare_name,' ',u.spare_quantity,' ',t.unit_title,' ราคา ',u.spare_price,' บาท') AS spare_detail
                            FROM ".DB_PREFIX."repair_spare u 
                            LEFT JOIN  ".DB_PREFIX."spare_main s ON u.spare_id = s.spare_id
                            LEFT JOIN ".DB_PREFIX."cunit t ON u.spare_unit = t.unit_id
                            WHERE u.flag != '0' AND u.repair_id = '$repairid'
                            ");
                            $stmt_spare->execute();	
                            $row_spare = $stmt_spare->fetch(PDO::FETCH_ASSOC);

                            $sum_price = $row_spare['sum_price'];
                            $spare_detail = $row_spare['spare_detail'];

                 ?>




                                <tr>
                                    <td class="text-center"><?php echo $i;?></td>
                                    <td class="text-center"><?php echo $repair_code;?></td>
                                    <td><?php echo $repair_date;?></td>
                                    <td><?php echo  $d2." วัน" ;?></td>
                                    <td><?php echo $repair_typetitle;?></td>
                                    <td><?php echo $comp_name;?></td>
                                    <td><?php echo $eq_name;?></br><small>รหัส : <?php echo $eq_code;?></small></td>
                                    <td><?php echo $repair_title;?></td>
                                    <td><?php echo $fullname;?></br><small>เลขบัตร : <?php echo $cid;?></small></td>
                                    <td class="text-center"><?php echo $status_date; ?></td>
                                    <td><?php echo $status_title;?></td>
                                    <td><?php echo $staff_name;?></td>
                                </tr>

                                <?php
                        } // end while
            ?>



                                <tr>
                                    <td colspan="10" style="text-align: right;">วันที่พิมพ์
                                        <?php echo date("d/m/Y H:i:s");?></td>
                                </tr>
                            </tbody>
                            <?php
                    } else {
                        echo '<tr><td>No Results found!</td></tr>';
                    }
  ?>
                            </table>



                        </div>
                    </div>
                    <!-- end: Invoice body-->

                    <!-- begin: Invoice footer-->
                    <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
                        <div class="col-md-9">
                            หมายเหตุ :
                        </div>
                    </div>
                    <!-- end: Invoice footer-->

                </div>



                <!-- end: Invoice-->
            </div>
        </div>
        <!-- end::Card-->


















    </div>


</body>

</html>