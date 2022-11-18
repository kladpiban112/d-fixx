<?php 
error_reporting(0);
$personid = filter_input(INPUT_GET, 'personid', FILTER_SANITIZE_STRING);
$repairid = filter_input(INPUT_GET, 'repairid', FILTER_SANITIZE_STRING);
$personid_enc = filter_input(INPUT_GET, 'personid', FILTER_SANITIZE_STRING);
$repairid_enc = filter_input(INPUT_GET, 'repairid', FILTER_SANITIZE_STRING);
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
} else {
}
?>



<!-- begin::Card-->
<div class="card card-custom overflow-hidden">
    <div class="card-body p-0">
        <div id="printableArea">
            <!-- begin: Invoice-->
            <!-- begin: Invoice header-->
            <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                <div class="col-md-9">
                    <div class="d-flex justify-content-between pb-5 pb-md-5 flex-column flex-md-row">
                        <h4 class=" ">ใบส่งซ่อมภายนอก <br>
                            <p class=" ">เลขที่ : <?php echo $repair_code; ?></p>
                        </h4>

                        <div class="d-flex flex-column align-items-md-end px-0">
                            <!--begin::Logo-->
                            <a href="#" class="mb-5">
                                <?php
                            $orglogo = getOrgLogo($row_person['org_id']);
                            if ($orglogo == '') {?>
                                <img src="assets/images/logo.png" alt="image" width="50" />
                                <?php } else {?>
                                <img src="uploads/logo/<?php echo $orglogo; ?>" alt="image" width="50">
                                <?php   } ?>
                            </a>
                            <!--end::Logo-->
                            <span class=" d-flex flex-column align-items-md-end opacity-70">
                                <h5><?php echo getOrgName($row_person['org_id']); ?></h5>
                                <span><?php echo getOrgAddr($row_person['org_id']); ?></span>
                                <span>โทรศัพท์ <?php echo getOrgTelephone($row_person['org_id']); ?></span>
                            </span>
                        </div>
                    </div>


                    <div class="border-bottom w-100"></div>
                    <div class="d-flex justify-content-between pt-6">
                        <div class="d-flex flex-column flex-root">
                            <span class="font-weight-bolder mb-2">ผู้แจ้ง</span>
                            <span class="opacity-70"><?php echo $fullname; ?></span>
                            <span class="opacity-70">เลขที่บัตรประชาชน : <?php echo $cid; ?></span>
                            <span class="opacity-70">โทรศัพท์ : <?php echo $telephone; ?></span>
                            <span class="opacity-70">ที่อยู่ : <?php  echo getPersonAddr($personid); ?></span>
                        </div>

                        <div class="d-flex flex-column flex-root">
                            <span class="font-weight-bolder mb-2">วันที่แจ้ง</span>
                            <span class="opacity-70 mb-2"><?php echo $repairdate; ?></span>
                            <span class="font-weight-bolder mb-2">ประเภทการแจ้ง</span>
                            <span class="opacity-70"><?php echo $repair_typetitle; ?></span>
                        </div>

                        <div class="d-flex flex-column flex-root">
                            <span class="font-weight-bolder mb-2">ผู้รับแจ้ง</span>
                            <span class="opacity-70 mb-2"><?php echo getUsername($user_add); ?></span>
                            <span class="font-weight-bolder mb-2">ผู้อนุมัติดำเนินการซ่อม</span>
                            <span
                                class="opacity-70">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</span>
                            <span class="opacity-70"><?php echo $approve_username; ?></span>
                            <span class="font-weight-bolder mb-2">วันที่อนุมัติแจ้งซ่อม</span>
                            <span class="opacity-70"><?php echo $approve_date; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end: Invoice header-->

            <!-- begin: Invoice body-->
            <div class="row justify-content-center py-8 px-8 py-md-5 px-md-0">
                <div class="col-md-9">
                    <div class="table-responsive">
                        <?php
                    //$repairid = filter_input(INPUT_POST, 'repairid', FILTER_SANITIZE_STRING);

                    $conditions = " AND u.repair_id = '$repairid' ";
                    $stmt_data = $conn->prepare('SELECT u.*,t.unit_title
                    FROM '.DB_PREFIX.'repair_spare u 
                    LEFT JOIN '.DB_PREFIX."cunit t ON u.spare_unit = t.unit_id
                    WHERE u.status_out = 'O' and u.flag != '0' $conditions 
                    ORDER BY u.oid ASC
                    $max");
                    $stmt_data->execute();
                    $numb_rows = $stmt_data->rowCount();
                    ?>
                        <span class="font-weight-bolder mb-2">รายการอะไหล่ที่ใช้</span>
                        <table class="table table-bordered table-strip" id="tbData" style="margin-top: 13px !important">
                            <thead>
                                <tr>
                                    <th class="text-center">ลำดับ</th>

                                    <th>อะไหล่/เครื่องมือ</th>
                                    <th class="text-right">จำนวน</th>
                                    <th>หน่วย</th>
                                    <th>ราคา(บาท)</th>
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

            $spare_name = $row['spare_name'];
            $spare_quantity = $row['spare_quantity'];
            $unit_title = $row['unit_title'];
            $spare_price = $row['spare_price'];
            $sum_spare_price += $spare_price; ?>
                                <tr>
                                    <td class="text-center" width="20px"><?php echo $i; ?></td>

                                    <td><?php echo $spare_name; ?></td>
                                    <td class="text-right"><?php echo $spare_quantity; ?></td>
                                    <td><?php echo $unit_title; ?></td>
                                    <td><?php echo $spare_price; ?></td>
                                </tr>

                                <?php
        } // end while
    } else {?>
                                <tr>
                                    <td class="text-center" height="50px" colspan="8">ไม่มีข้อมูล</td>
                                </tr>
                                <?php }
            ?>

                            </tbody>
                            <tfooter>
                                <tr>
                                    <td class="text-right" colspan="4">รวม</td>

                                    <td><?php echo $sum_spare_price; ?></td>
                                </tr>
                            </tfooter>
                        </table>


                    </div>


                    <div class="table-responsive">
                        <?php
                    //$repairid = filter_input(INPUT_POST, 'repairid', FILTER_SANITIZE_STRING);

                    $conditions = " AND u.repair_id = '$repairid' ";
                    $stmt_data = $conn->prepare('SELECT u.*,s.status_title,st.fname,st.lname,st.nickname
                    FROM '.DB_PREFIX.'repair_status u 
                    LEFT JOIN  '.DB_PREFIX.'repair_status_type s ON u.status_id = s.status_typeid
                    LEFT JOIN  '.DB_PREFIX."staff_main st ON u.staff_id = st.oid 
                    WHERE u.flag != '0' $conditions 
                    ORDER BY u.oid ASC
                    $max");
                    $stmt_data->execute();
                    $numb_rows = $stmt_data->rowCount();
                    ?>
                        <span class="font-weight-bolder mb-2">รายละเอียดการซ่อม</span>
                        <table class="table table-bordered table-hover table-strip" id="tbData"
                            style="margin-top: 13px !important">
                            <thead>
                                <tr>
                                    <th class="text-center">ลำดับ</th>
                                    <th width="100px">วันที่</th>
                                    <th width="180px">สถานะการซ่อม</th>
                                    <th width="200px">ผู้ซ่อม</th>
                                    <th>รายละเอียด</th>

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
            $staff_name = $row['prename_title'].$row['fname'].' '.$row['lname'].' ('.$row['nickname'].')';

            $stmt_detail = $conn->prepare("SELECT GROUP_CONCAT(s.fname,' ',s.lname) AS gstaff_name,GROUP_CONCAT(s.oid) AS gstaff_id
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
                                    <td class="text-center" height="50px" colspan="8">ไม่มีข้อมูล</td>
                                </tr>
                                <?php }
            ?>

                            </tbody>
                        </table>


                    </div>



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

        <!-- begin: Invoice action-->
        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
            <div class="col-md-9">
                <div class="d-flex justify-content-between">
                    <!--<button type="button" class="btn btn-light-primary font-weight-bold" onclick="window.print();">Download Invoice</button>
                    <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print();">Print Invoice</button>-->

                    <!--<button type="button" class="btn btn-light-primary font-weight-bold" onclick="printDiv('printableArea')">ดาวน์โหลดเอกสาร</button>-->
                    <button type="button" class="btn btn-secondary btn-sm" onclick="javascript:history.back()"><i
                            class="fa fa-chevron-left" title="ย้อนกลับ"></i> ย้อนกลับ</button>
                    <!-- <button type="button" class="btn btn-success btn-sm font-weight-bold" 
                        onclick="printDiv('printableArea')"><i class="fa fa-print" title=""></i> พิมพ์เอกสาร</button> -->
                        <a target="_blank"
                        href="././pdfprint/repairout/rpt-repair-pdf.php?personid=<?php echo $personid_enc; ?>&repairid=<?php echo $repairid_enc; ?>&act=<?php echo base64_encode('view'); ?>"
                        class="btn btn-success btn-sm font-weight-bold">
                        <i class="fas fa-print"></i>พิมพ์ใบแจ้งซ่อม
                    </a>
                </div>
            </div>
        </div>
        <!-- end: Invoice action-->

        <!-- end: Invoice-->
    </div>
</div>
<!-- end::Card-->
</div>
<!-- end::Card-->






<script>
$(document).ready(function() {
    'use strict';
});



function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
}
</script>