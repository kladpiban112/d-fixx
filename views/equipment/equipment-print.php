
<?php
error_reporting(0);
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
$oid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
//$serviceid = filter_input(INPUT_GET, 'serviceid', FILTER_SANITIZE_STRING);
$act = filter_input(INPUT_GET, 'act', FILTER_SANITIZE_STRING);
$id = base64_decode($id);
//$serviceid = base64_decode($serviceid);
$action = base64_decode($act);
if($action == "view"){
	$txt_title = "ดูข้อมูล";
	$action = $action;

	$stmt_data = $conn->prepare ("SELECT u.*,o.org_name,o.org_shortname,t.eq_typename,s.status_title,s.status_color,r.rec_title FROM ".DB_PREFIX."equipment_main u 
    LEFT JOIN ".DB_PREFIX."org_main o ON u.org_id = o.org_id
    LEFT JOIN ".DB_PREFIX."equipment_type t ON u.eq_typeid = t.eq_typeid
    LEFT JOIN ".DB_PREFIX."equipment_status s ON u.flag = s.status_id
    LEFT JOIN ".DB_PREFIX."receive_type r ON u.receive_id = r.rec_id
    WHERE u.flag != '0' AND u.oid = '$id' 
    ORDER BY u.oid DESC LIMIT 1");
    $stmt_data->execute();	
    $row_person = $stmt_data->fetch(PDO::FETCH_ASSOC);

    $eq_names = $row_person['eq_names'];
    $eq_codes = $row_person['eq_codes'];
    $receive_date = date_db_2form($row_person['receive_date']);

                $eq_typename = $row_person['eq_typename'];
                $eq_typeid = $row_person['eq_typeid'];
                if($eq_typeid == 1){
                    $eq_typeother = "<br>(".$row_person['eq_typeother'].")";  
                }else{
                    $eq_typeother = "";
                }
                $rec_title = $row_person['rec_title'];
                $receive_id = $row_person['receive_id'];
                if($receive_id == 4){
                    $receive_other = "<br>(".$row_person['receive_other'].")";  
                }else{
                    $receive_other = "";
                }
  

}else{
	
}
?>


		

		<!-- begin::Card-->
<div class="card card-custom overflow-hidden">
    <div class="card-body p-0">
    <div id="printableArea">
        <!-- begin: Invoice-->
        <!-- begin: Invoice header-->
        <div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
            <div class="col-md-9">
                <div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
                    <h4 class=" mb-10">ข้อมูลอุปกรณ์</h4>
                    <div class="d-flex flex-column align-items-md-end px-0">
                        <!--begin::Logo-->
                        <a href="#" class="mb-5">
                            <?php 
                            $orglogo = getOrgLogo($row_person['org_id']);
                            if($orglogo == ""){?>
                                <img src="assets/images/logo.png" alt="image"  width="50"/>
                            <?php }else{?>
                                <img src="uploads/logo/<?php echo $orglogo;?>" alt="image" width="50">
                                <?php   } ?>
                        </a>
                        <!--end::Logo-->
                        <span class=" d-flex flex-column align-items-md-end opacity-70">
                            <h5><?php echo getOrgName($row_person['org_id']);?></h5>
                            <span><?php echo getOrgAddr($row_person['org_id']);?></span>
                            <span>โทรศัพท์ <?php echo getOrgTelephone($row_person['org_id']);?></span>
                        </span>
                    </div>
                </div>
                <div class="border-bottom w-100"></div>
                <div class="d-flex justify-content-between pt-6">
                    <div class="d-flex flex-column flex-root">
                        <span class="font-weight-bolder mb-2"><?php echo $eq_names;?></span>
                        <span class="opacity-70">รหัสอุปกรณ์ : <?php echo $eq_codes;?></span>
                        <span class="opacity-70">ประเภท : <?php echo  $eq_typename; echo $eq_typeother?></span>
                    </div>
               
                    <div class="d-flex flex-column flex-root">
                        <span class="font-weight-bolder mb-2">วันที่รับ</span>
                        <span class="opacity-70 mb-2"><?php echo $receive_date;?></span>
                        <span class="opacity-70">ได้รับจาก : <?php echo $rec_title; echo $receive_other;?></span>
                    </div>

                    <div class="d-flex flex-column flex-root">
                        <span class="font-weight-bolder mb-2"><img src="uploads/qrcode/<?php echo $id;?>.png" alt="image"/></span>
                        <h5 class="text-center"><?php echo $eq_code;?></h5>
                    </div>

                </div>
            </div>

            


        </div>
        <!-- end: Invoice header-->

        <div class="row justify-content-center py-8 px-8 py-md-5 px-md-0">
            <div class="col-md-9">

            <div class="row justify-content-center py-8 px-8 py-md-0 px-md-0">
				<?php 

									$sql_files = "SELECT * FROM ".DB_PREFIX."equipment_files WHERE oid = '$id' AND file_status = '1' ORDER BY file_id ASC ";
									$stmt_files = $conn->prepare ($sql_files);
									$stmt_files->execute();
																	
									while ($row_files = $stmt_files->fetch(PDO::FETCH_ASSOC)){
                                         $file_id = $row_files['file_id'];
										$file_name = $row_files['file_name'];
										?>

					<div class="col-lg-3">
					
					<div class="symbol symbol-150 mr-3">
					<img src="uploads/equipment/<?php echo $file_name;?>" alt="image" class="img img-responsive"/>
					
                    
                    </div>
					</div>

					<?php } 
									?>


				</div>

            </div>
        </div>   

        <!-- begin: Invoice body-->
        <div class="row justify-content-center py-8 px-8 py-md-5 px-md-0">
            <div class="col-md-9">
            <p>ข้อมูลการยืม</p>
                <div class="table-responsive">
                <?php
                    //$conditions = " AND u.service_id = '$serviceid' ";
                    $stmt_data = $conn->prepare ("SELECT sd.s_oid,s.service_date,pr.prename,p.fname,p.lname ,st.service_title
                    FROM ".DB_PREFIX."service_data sd 
                    LEFT JOIN ".DB_PREFIX."service_main s ON sd.service_id = s.service_id
                    LEFT JOIN ".DB_PREFIX."person_main p ON s.person_id = p.oid
                    LEFT JOIN ".DB_PREFIX."cprename pr ON p.prename = pr.id_prename
                    LEFT JOIN ".DB_PREFIX."service_type st ON s.service_type = st.service_typeid
                    WHERE sd.flag = '1' AND s.flag='1' AND sd.eq_id = '$id' 
                    ORDER BY s.service_id DESC
                    $max");
                    $stmt_data->execute();		
                    ?>

<table class="table table-bordered table-strip" id="" style="margin-top: 1px !important">
    <thead>
    <tr>
                        <th class="text-center">ลำดับ</th>
                        <th>ชื่อ-สกุล ผู้ยืม</th>
                        <th>ใช้บริการ</th>
                        <th>วันที่ใช้บริการ</th>
                        
    </tr>
    </thead>
    <tbody>

    <?php

            $i  = 0;
            while ($row = $stmt_data->fetch(PDO::FETCH_ASSOC))
            {
                $i++;
                $s_oid = $row['s_oid'];

                $service_date = date_db_2form($row['service_date']);
                $service_title = $row['service_title'];
                $fullname = $row['prename'].$row['fname']." ".$row['lname'];
   

                ?>


            
 
                <tr>
                            <td class="text-center" width="50px"><?php echo $i;?></td>
                            <td class="text-left" width="200px"><?php echo $fullname;?></td>
                            <td width="130px"><?php echo $service_title;?></td>
                            <td width="130px"><?php echo $service_date;?></td>
  
                      
                            
                    
                </tr>

                <?php 
            } // end while
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
                    <button type="button" class="btn btn-secondary btn-sm" onclick="javascript:history.back()" ><i class="fa fa-chevron-left" title="ย้อนกลับ" ></i> ย้อนกลับ</button>
                    <button type="button" class="btn btn-success btn-sm font-weight-bold" onclick="printDiv('printableArea')"><i class="fa fa-print" title="" ></i> พิมพ์เอกสาร</button>
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
