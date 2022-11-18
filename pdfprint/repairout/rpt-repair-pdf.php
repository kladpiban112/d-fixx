<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$max = '';
$sum_spare_price = 0.0;
// add Lib mPDF to projectr
require_once '../../core/config.php';
require_once '../../vendor/autoload.php';
require_once '../../core/functions.php';

ob_start();
$datenow = date('Y-m-d');
$dtnow = date('Y-m-d H:i:s');

$personid = filter_input(INPUT_GET, 'personid', FILTER_SANITIZE_STRING);
$repairid = filter_input(INPUT_GET, 'repairid', FILTER_SANITIZE_STRING);
$act = filter_input(INPUT_GET, 'act', FILTER_SANITIZE_STRING);
$personid = base64_decode($personid);
$repairid = base64_decode($repairid);
$action = base64_decode($act);
if ($action == 'view') {
    $txt_title = 'ดูข้อมูล';
    $action = $action;

    $stmt_data = $conn->prepare('SELECT p.*,o.org_name,pr.prename FROM '.DB_PREFIX.'person_main p 
    LEFT JOIN '.DB_PREFIX.'org_main o ON p.org_id = o.org_id 
    LEFT JOIN '.DB_PREFIX."cprename pr ON p.prename = pr.id_prename
    WHERE p.oid = '$personid'  LIMIT 1");
    $stmt_data->execute();
    $row_person = $stmt_data->fetch(PDO::FETCH_ASSOC);

    $fullname = $row_person['prename'].''.$row_person['fname'].' '.$row_person['lname'];
    $cid = $row_person['cid'];
    $telephone = $row_person['telephone'];
    $person_type = $row_person['person_type'];  // 1 บุคคล 2 บริษัท
    $comp_name = $row_person['comp_name'];
    $org_code = $row_person['org_code'];


    $sql_service = 'SELECT s.*,t.repair_typetitle FROM '.DB_PREFIX.'repair_main s 
    LEFT JOIN '.DB_PREFIX.'person_main p ON s.person_id = p.oid
    LEFT JOIN  '.DB_PREFIX."repair_type t ON s.repair_type = t.repair_typeid
    WHERE s.repair_id = '$repairid' AND s.flag != '0'  LIMIT 1";
    $stmt_service = $conn->prepare($sql_service);
    $stmt_service->execute();
    $row_service = $stmt_service->fetch(PDO::FETCH_ASSOC);

    $repairdate = date_db_2form($row_service['repair_date']);
    $repair_code = $row_service['repair_code'];
    $repair_typetitle = $row_service['repair_typetitle'];

    $approve_date = date_db_2form($row_service['approve_date']);
    $approve_username = $row_service['approve_username'];
    $user_add = $row_service['add_users'];

    $return_date = date_db_2form($row_service['return_date']);
    $return_username = $row_service['return_username'];
} else {
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../print-style.css">

    <style>

    </style>
    <title>ใบแจ้งซ่อม</title>
    <style>
    body {
        font-family: 'angsana';
        font-size: 16pt;
    }

    p {
        margin: 0pt;
    }

    table.items {
        border: 0.1mm solid #000000;
    }

    td {
        vertical-align: top;
    }

    .items td {
        border-left: 0.1mm solid #000000;
        border-right: 0.1mm solid #000000;
    }

    table thead td {
        background-color: #EEEEEE;
        text-align: center;
        border: 0.1mm solid #000000;
        font-variant: small-caps;
    }

    td.blanktotal {
        background-color: #EEEEEE;
        border: 0.1mm solid #000000;
        background-color: #FFFFFF;
        border: 0mm none #000000;
        border-top: 0.1mm solid #000000;
        border-right: 0.1mm solid #000000;
    }

    td.totals {
        text-align: right;
        border-bottom: 0.1mm solid #000000;
        border-top: 0.1mm solid #000000;
    }

    td.cost {
        text-align: "."right;
    }
    </style>

</head>

<body>

    <!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
<tr>
<td width="50%" style="color:#0000BB; ">
<span style="font-weight: bold; font-size: 20pt;">  <h4 class=" ">ใบแจ้งซ่อมภายนอก <br>
                            <p class=" ">เลขที่ : <?php echo $repair_code; echo $personid;  ?></p>
                        </h4>  </span>
</td>

<td width="15%" style="color:#0000BB; text-align: right;">
  <a href="#">
  <?php
                            $orglogo = getOrgLogo($row_person['org_id']);
                            if ($orglogo == '') {?>
                                <img src="../../assets/images/logo.png" alt="image" width="50" />
                                <?php } else {?>
                                <img src="../../uploads/logo/<?php echo $orglogo; ?>" alt="image" width="80">
                                <?php   } ?>
                            </a>
</td>
<td width="35%" style="color:#0000BB; ">

                           
                           <span>
                                <h4><?php echo getOrgName($row_person['org_id']); ?></h4>
                                <span><h5><?php echo getOrgAddr($row_person['org_id']); ?> <?php echo getOrgPostcode($row_person['org_id']); ?><h5/></span>
                                <span><h4>โทรศัพท์ <?php echo getOrgTelephone($row_person['org_id']); ?><h4/><p></p>
                                <h4>เลขประจำตัวผู้เสียภาษี  <?php echo getOrgTax($row_person['org_id']); ?><h4/></span>
                            </span>
</td>
</tr>
</table>
</htmlpageheader>
<htmlpagefooter name="myfooter">
<div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
Page {PAGENO} of {nb}
</div>
<div style="font-size: 9pt; text-align: right; padding-top: 2mm; ">
จัดเตรียมโดย / Prepared by DFIX Corp.
</div>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<br/>
    <hr class="border-bottom w-100">

    <table width="100%" style="font-size: 14pt; border-collapse: collapse;" cellpadding="3">
        <tr>
            <td width="50%" style="border: 0 mm solid #888888; ">
                <span style="font-size: 14pt; color: #555555;">ผู้แจ้ง:</span>

                 <br><?php echo $fullname; ?></br>
                    <br>เลขที่ลูกค้า : <?php echo $org_code; ?></br>
                    <br>หน่วยงาน : <?php echo $comp_name; ?></br>
                    <br>ที่อยู่ : <?php  echo getPersonAddr($personid); ?></br>
                    <br>โทรศัพท์ : <?php echo $telephone; ?></br>
                    <br>เลขที่บัตรประชาชน/เลขผู้เสียภาษี : <?php echo $cid; ?></br>

            </td>

            <td width="20%" style="border: 0 mm solid #888888;">
                <span style="font-size: 14pt; color: #555555;">วันที่แจ้ง :</span>

                <br><?php echo $repairdate; ?></br><br>
                <span style="font-size: 14pt; color: #555555;">ประเภทการแจ้ง</span>
                <br><?php echo $repair_typetitle; ?></br>
            </td>

            <td width="30%" style="border: 0 mm solid #888888;">
                <span style="font-size: 14pt; color: #555555;">ผู้รับแจ้ง:</span>

                <br><?php echo getUsername($user_add); ?></br>
                <br>ผู้อนุมัติดำเนินการซ่อม</br>
                <br>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</br>
                <br><?php echo $approve_username; ?></br>
                <br>วันที่อนุมัติแจ้งซ่อม</br>
                <br><?php echo $approve_date; ?></br>
            </td>

        </tr>
    </table>

    <!-- start div -->




    <!-- begin: Invoice-->


    <!-- begin: Invoice body-->



    <?php
                    //$repairid = filter_input(INPUT_POST, 'repairid', FILTER_SANITIZE_STRING);

                    $conditions = " AND u.repair_id = '$repairid' ";
                    $stmt_data = $conn->prepare('SELECT u.*,s.spare_name,s.spare_code,t.unit_title
                    FROM '.DB_PREFIX.'repair_spare u 
                    LEFT JOIN  '.DB_PREFIX.'spare_main s ON u.spare_id = s.spare_id
                    LEFT JOIN '.DB_PREFIX."cunit t ON u.spare_unit = t.unit_id
                    WHERE u.flag != '0' AND u.status_out = 'O' $conditions 
                    ORDER BY u.oid ASC
                    $max");
                    $stmt_data->execute();
                    $numb_rows = $stmt_data->rowCount();
                    ?>
    <hr>
    <span style="font-size: 14pt;">
        รายการอะไหล่ที่ใช้
    </span>
    <!-- <table width="100%" style="border:1px solid #000000; margin-top: 13px !important"> -->
    <table class="items" width="100%" style="font-size: 14pt; border-collapse: collapse;" cellpadding="8">
        <thead>
            <tr>
                <td>ลำดับ</td>
                <td>รหัส</td>
                <td>อะไหล่/เครื่องมือ</td>
                <td>จำนวน</td>
                <td>หน่วย</td>
                <td>ราคา(บาท)</td>
            </tr>
        </thead>
        <tbody>

            <?php
    if ($numb_rows > 0) {
        $i = 0;
        while ($row = $stmt_data->fetch(PDO::FETCH_ASSOC)) {
            ++$i;
            $oid = $row['oid'];
            $oid_enc = base64_encode($oid);
            $spare_id = $row['spare_id'];
            $spare_code = $row['spare_code'];
            $spare_name = $row['spare_name'];
            $spare_quantity = $row['spare_quantity'];
            $unit_title = $row['unit_title'];
            $spare_price = $row['spare_price'];
            $sum_spare_price += $spare_price * $spare_quantity;

            if ($spare_id == '0') {
                $spare_name_show = $row['spare_other'];
            } else {
                $spare_name_show = $row['spare_name'];
            } ?>
            <tr>
                <td style="text-align: center;">
                    <?php echo $i; ?></td>
                <td style="text-align: center;"><?php echo $spare_code; ?></td>
                <td><?php echo $spare_name_show; ?>
                </td>
                <td style="text-align: center;">
                    <?php echo $spare_quantity; ?></td>
                <td style="text-align: center;">
                    <?php echo $unit_title; ?></td>
                <td class="cost">
                    <?php echo number_format($spare_price, 2); ?></td>
            </tr>

            <?php
        } // end while
    } else {?>
            <tr>
                <td class="text-center" height="50px" colspan="6">ไม่มีข้อมูล</td>
            </tr>
            <?php }
            ?>

        </tbody>
        <tfooter>
            <tr>
                <td style="text-align: center;" class="blanktotal" colspan="5">
                    รวม</td>

                <td class="totals">
                    <?php echo number_format($sum_spare_price, 2); ?></td>
            </tr>
        </tfooter>
    </table>

    <?php
                    //$repairid = filter_input(INPUT_POST, 'repairid', FILTER_SANITIZE_STRING);

                    $conditions = " AND u.repair_id = '$repairid' ";
                    $stmt_data = $conn->prepare('SELECT u.*,s.status_title,st.sfname,st.slname,st.nickname
                    FROM '.DB_PREFIX.'repair_status u 
                    LEFT JOIN  '.DB_PREFIX.'repair_status_type s ON u.status_id = s.status_typeid
                    LEFT JOIN  '.DB_PREFIX."staff_main st ON u.staff_id = st.oid 
                    WHERE u.flag != '0' AND u.status_out = 'O'  $conditions 
                    ORDER BY u.oid ASC
                    $max");
                    $stmt_data->execute();
                    $numb_rows = $stmt_data->rowCount();
                    ?>
    <br>
    <span style="font-size: 14pt;">รายละเอียดการซ่อม</span>
    <table class="items" width="100%" style="font-size: 14pt; border-collapse: collapse;" cellpadding="8">
        <thead>
            <tr>
                <td>ลำดับ</td>
                <td>วันที่</td>
                <td>สถานะการซ่อม</td>
                <td>ผู้ซ่อม</td>
                <td>รายละเอียด</td>

            </tr>
        </thead>
        <tbody>

            <?php
    if ($numb_rows > 0) {
        $i = 0;
        while ($row = $stmt_data->fetch(PDO::FETCH_ASSOC)) {
            ++$i;
            $oid = $row['oid'];
            $oid_enc = base64_encode($oid);
            $status_date = date_db_2form($row['status_date']);
            $status_title = $row['status_title'];
            $status_desc = $row['status_desc'];
            $staff_name = $row['prename_title'].$row['sfname'].' '.$row['slname'].' ('.$row['nickname'].')';

            $stmt_detail = $conn->prepare("SELECT GROUP_CONCAT(s.sfname,' ',s.slname) AS gstaff_name,GROUP_CONCAT(s.oid) AS gstaff_id
                FROM ".DB_PREFIX.'repair_staff u 
                LEFT JOIN  '.DB_PREFIX."staff_main s ON u.staff_id = s.oid
                WHERE u.status_id = '$oid' ");
            $stmt_detail->execute();
            $row_detail = $stmt_detail->fetch(PDO::FETCH_ASSOC);
            $gstaff_name = str_replace(',', '</br>', $row_detail['gstaff_name']);
            $gstaff_id = $row_detail['gstaff_id'];
            $gstaff_id_exp = explode(',', $gstaff_id); ?>




            <tr>
                <td class="text-center" width="20px"><?php echo $i; ?></td>

                <td><?php echo $status_date; ?></td>
                <td><?php echo $status_title; ?></td>
                <td><?php echo $gstaff_name; ?></td>
                <td><?php echo $status_desc; ?></td>


            </tr>

            <?php
        } // end while
    } else {?>
            <tr>
                <td class="text-center" height="50px" colspan="5">ไม่มีข้อมูล</td>
            </tr>
            <?php }
            ?>

        </tbody>
    </table>

    <br>






    <!-- end: Invoice body-->




    <!-- begin: Invoice footer-->
    <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
        <div class="col-md-9">
            หมายเหตุ :
            <?php
                if ($return_date != '') {
                    echo 'วันที่รับคืน : '.$return_date;
                    echo ' ผู้รับคืน : '.$return_username;
                }

                ?>
        </div>
    </div>
    <!-- end: Invoice footer-->




    <!-- end::Card-->

    <!-- end div -->
</body>

</html>

<?php

$html = ob_get_contents();

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font' => 'angsana',
    'default_font_size' => 14,
    'tempDir' => __DIR__.'/tmp',
    'margin_left' => 20,
    'margin_right' => 15,
    'margin_top' => 30, //48
    'margin_bottom' => 25,
    'margin_header' => 10,
    'margin_footer' => 10,
     ]);
     $mpdf->SetProtection(['print']);
     $mpdf->SetTitle('DFix Corp. - Repair invoices');
     $mpdf->SetAuthor('DFix Corp.');

$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);
ob_end_clean();
$mpdf->Output();

?>