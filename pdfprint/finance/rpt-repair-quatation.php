<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// add Lib mPDF to projectr
require_once '../../core/config.php';
require_once '../../vendor/autoload.php';
require_once '../../core/functions.php';

ob_start();
$datenow = date('Y-m-d');
$dtnow = date('Y-m-d H:i:s');
$max = '';
$personid = filter_input(INPUT_GET, 'personid', FILTER_SANITIZE_STRING);
$repairid = filter_input(INPUT_GET, 'repairid', FILTER_SANITIZE_STRING);
$qtid = filter_input(INPUT_GET, 'qtid', FILTER_SANITIZE_STRING);
$act = filter_input(INPUT_GET, 'act', FILTER_SANITIZE_STRING);
$personid = base64_decode($personid);
$repairid = base64_decode($repairid);
$qtid = base64_decode($qtid);
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

    $sql_service = 'SELECT s.*,t.repair_typetitle,qt.qt_code,qt.qt_date,qt.qt_price,qt.qt_approvedate,qt.oid AS qt_id ,qt.qt_vat,qt.qt_vatprice,qt.qt_pricetotal,qt.qt_dayexp
    FROM '.DB_PREFIX.'repair_main s 
    LEFT JOIN '.DB_PREFIX.'person_main p ON s.person_id = p.oid
    LEFT JOIN  '.DB_PREFIX.'repair_type t ON s.repair_type = t.repair_typeid
    LEFT JOIN '.DB_PREFIX."repair_quotation qt ON s.repair_id = qt.repair_id AND qt.flag = '1'
    WHERE s.repair_id = '$repairid' AND qt.oid = '$qtid'   AND s.flag != '0'  LIMIT 1";
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

    $qt_id = $row_service['oid'];
    $qt_id_enc = base64_encode($qt_id);
    $qt_statusname = $row_service['qt_statusname'];
    $qt_date = date_db_2form($row_service['qt_date']);
    $qt_code = $row_service['qt_code'];
    $qt_price = $row_service['qt_price'];
    $qt_approvedate = date_db_2form($row_service['qt_approvedate']);
    $qt_vat = $row_service['qt_vat'];
    $qt_vatprice = $row_service['qt_vatprice'];
    $qt_pricetotal = $row_service['qt_pricetotal'];
    $qt_dayexp = $row_service['qt_dayexp'];
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
    <title>ใบเสนอราคา</title>
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

    td.detail {
        text-align: left;
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
<span style="font-weight: bold; font-size: 20pt;">  <h4 class=" ">ใบเสนอราคา / Quotation <br>
                            <p class=" ">เลขที่ : <?php echo $qt_code; ?></p>
                        </h4>  </span>
</td>

<td width="15%" style="color:#0000BB; text-align: right;">
  <a href="#">
  <?php
                            $orglogo = getOrgLogo($row_person['org_id']);
                            if ($orglogo == '') {?>
                                <img src="../../assets/images/logo.png" alt="image" width="80" />
                                <?php } else {?>
                                <img src="../../uploads/logo/<?php echo $orglogo; ?>" alt="image" width="80">
                                <?php   } ?>
                            </a>
</td>
<td width="35%" style="color:#0000BB; ">

                           
                            <span>
                                <h4><?php echo getOrgName($row_person['org_id']); ?></h4>
                                <span><?php echo getOrgAddr($row_person['org_id']); ?> </span>
                                <span>โทรศัพท์ <?php echo getOrgTelephone($row_person['org_id']); ?></span>
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
    <hr class="border-bottom w-100">

    <table width="100%" style="font-size: 14pt; border-collapse: collapse;" cellpadding="3">
        <tr>
            <td width="50%" style="border: 0 mm solid #888888; ">
                <span style="font-size: 14pt; color: #555555;">ผู้แจ้ง:</span>

                <br><?php echo $fullname; ?></br>
                <br>บริษัท : <?php echo $comp_name; ?></br>
                <br>เลขที่บัตรประชาชน/เลขผู้เสียภาษี : <?php echo $cid; ?></br>
                <br>โทรศัพท์ : <?php echo $telephone; ?></br>
                <br>ที่อยู่ : <?php  echo getPersonAddr($personid); ?></br>
            </td>

            <td width="20%" style="border: 0 mm solid #888888;">
                <span style="font-size: 14pt; color: #555555;">วันที่แจ้ง :</span>

                <br><?php echo $repairdate; ?></br><br>

            </td>

            <td width="30%" style="border: 0 mm solid #888888;">

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
                    WHERE u.flag != '0' AND u.status_out = 'I' $conditions 
                    ORDER BY u.oid ASC
                    $max");
                    $stmt_data->execute();
                    $numb_rows = $stmt_data->rowCount();
                    ?>
    <hr>
    <?php
                    //$repairid = filter_input(INPUT_POST, 'repairid', FILTER_SANITIZE_STRING);

                    $conditions = " AND u.repair_id = '$repairid' ";
                    $stmt_data = $conn->prepare('SELECT u.*,s.status_title,st.sfname,st.slname,st.nickname
                    FROM '.DB_PREFIX.'repair_status u 
                    LEFT JOIN  '.DB_PREFIX.'repair_status_type s ON u.status_id = s.status_typeid
                    LEFT JOIN  '.DB_PREFIX."staff_main st ON u.staff_id = st.oid 
                    WHERE u.flag != '0' AND u.status_out = 'I'  $conditions 
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
                <td>รายละเอียด</td>
                <td>จำนวน</td>
                <td>หน่วย</td>
                <td>ราคา/หน่วย</td>

            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="detail" style="text-align: center;">1.</td>
                <td style="text-align: left;">ค่าซ่อม</td>
                <td class="totals"><?php echo $qt_price; ?></td>
                <td style="text-align: center;" class="detail">1</td>
                <td class="cost"><?php echo $qt_price; ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td class="blanktotal" style="text-align: right;" colspan="4">รวมเงิน</td>
                <td class="totals"><?php echo $qt_price; ?></td>
            </tr>
            <tr>
                <td class="blanktotal" style="text-align: right;" colspan="4">ภาษีมูลค่าเพิ่ม <?php echo $qt_vat; ?>%
                </td>
                <td class="totals"><?php echo $qt_vatprice; ?></td>
            </tr>
            <tr>
                <td style="text-align: center;" class="blanktotal" colspan="3">(
                    <?php echo ThaiBahtConversion($qt_pricetotal); ?> )</td>
                <td class="blanktotal" style="text-align: right;">รวมเป็นเงินทั้งสิ้น</td>
                <td class="totals"><?php echo $qt_pricetotal; ?></td>
            </tr>
        </tfoot>
    </table>

    <br>






    <!-- end: Invoice body-->




    <!-- begin: Invoice footer-->
    <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
        <div class="col-md-9">
            หมายเหตุ : ใบเสนอราคานี้ สามารถใช้งานได้ภายใน <?php echo $qt_dayexp; ?> วัน นับจากวันที่เสนอราคา
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
     $mpdf->SetTitle('DFix Corp. - Repair Quatation');
     $mpdf->SetAuthor('DFix Corp.');
     $mpdf->SetWatermarkText('Repair Quatation');
     $mpdf->showWatermarkText = true;
     $mpdf->watermark_font = 'DejaVuSansCondensed';
     $mpdf->watermarkTextAlpha = 0.1;

$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);
ob_end_clean();
$mpdf->Output();

?>