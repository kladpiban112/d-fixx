<?php
error_reporting(0);
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

$act = filter_input(INPUT_GET, 'act', FILTER_SANITIZE_STRING);
$personid = base64_decode($id);
$action = base64_decode($act);
if($action == "edit"){
	$txt_title = "แก้ไข";
	$action = $action;

	$stmt_data = $conn->prepare ("SELECT p.*,o.org_name FROM ".DB_PREFIX."staff_main p 
	LEFT JOIN ".DB_PREFIX."org_main o ON p.org_id = o.org_id 
  WHERE p.oid = '$personid'  LIMIT 1");
  $stmt_data->execute();	
  $row_person = $stmt_data->fetch(PDO::FETCH_ASSOC);
  
   $sql_service = "SELECT s.* FROM ".DB_PREFIX."service_main s 
	LEFT JOIN ".DB_PREFIX."person_main p ON s.person_id = p.oid 
  WHERE s.service_id = '$serviceid' AND s.flag != '0'  LIMIT 1";
  $stmt_service = $conn->prepare ($sql_service);
  $stmt_service->execute();	
	$row_service = $stmt_service->fetch(PDO::FETCH_ASSOC);

}else{
	$txt_title = "เพิ่ม";
	$action = "add";
}
?>



		<!--begin::Card-->
		<div class="card card-custom gutter-b example example-compact">
			<div class="card-header ribbon ribbon-right">
      <div class="ribbon-target bg-primary" style="top: 10px; right: -2px;">1</div>
				<h3 class="card-title">
        <i class="fas fa-user-tag"></i>&nbsp;<?php echo $txt_title;?>ข้อมูลช่าง
				</h3>
				<div class="card-toolbar">
					<div class="example-tools justify-content-center">
						<a href="dashboard.php?module=<?php echo $module;?>&page=staff" class="btn btn-defalse btn-sm font-weight-bold mr-2" title="ย้อนกลับ"><i class="fa fa-chevron-left" title="ย้อนกลับ" ></i></a>
					</div>
				</div>
			</div>


<form class="form" enctype="multipart/form-data" >
<input type="hidden" class="form-control"  name="act" id="act" value="<?php echo $action;?>"/>
<input type="hidden" class="form-control"  name="personid" id="personid" value="<?php echo $personid;?>"/>
	<div class="card-body">

	
	<div class="row">
	<div class="col-lg-9">

<span><i class="far fa-user"></i> ข้อมูลช่าง</span>
  <hr>      

  <div class="form-group row">
			<div class="col-lg-4">
				<label>เลขบัตรประชาชน</label>
				
							<input type="text" class="form-control" placeholder="เลขบัตรประชาชน 13 หลัก"  name="cid" id="cid" maxlength="13" value="<?php echo $row_person['cid'];?>"/>
              <span class="form-text text-muted">13 หลัก</span>
							
			</div>
      <div class="col-lg-6">
				<label>หน่วยงาน</label>
				<select class="form-control " name="org_id" id="org_id">
                    <option value="">ระบุ</option>
                    <?php
					if($logged_user_role_id == '1'){
						$conditions = " ";
					}else{
						$conditions = " AND org_id = '$logged_org_id' ";
					}
					$stmt_user_role = $conn->prepare("SELECT * FROM ".DB_PREFIX."org_main WHERE flag = 1  $conditions  ORDER BY org_id ASC");
					$stmt_user_role->execute();		
					while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC))
						{
						$id_selected = $row['org_id'];
						$title_selected = stripslashes($row['org_name']);
						?>
						<option value="<?php echo $id_selected;?>" <?php if($row_person['org_id'] == $id_selected ){echo "selected";} ?>><?php echo $title_selected;?></option>
						<?php
						}
					?>
				</select>
			</div>

		</div>

  <div class="form-group row">
      <div class="col-lg-2">
				<label>คำนำหน้า</label>
            <select class="form-control " name="prename" id="prename">
                        <option value="">ระบุ</option>
                        <?php
                          $stmt_user_role = $conn->prepare("SELECT * FROM ".DB_PREFIX."cprename  ORDER BY id_prename ASC");
                          $stmt_user_role->execute();		
                          while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC))
                            {
                            $id_selected = $row['id_prename'];
                            $title_selected = stripslashes($row['prename']);
                            ?>
                            <option value="<?php echo $id_selected;?>" <?php if($row_person['prename'] == $id_selected ){echo "selected";} ?>><?php echo $title_selected;?></option>
                            <?php
                            }
                          ?>
            </select>
				
			</div>
			<div class="col-lg-5">
				<label>ชื่อ</label>
				<input type="text" class="form-control"  name="sfname" id="sfname" placeholder="ชื่อ" value="<?php echo $row_person['sfname'];?>"/>
				
			</div>
			<div class="col-lg-5">
				<label>สกุล</label>
				<input type="text" class="form-control"  name="slname" id="slname" placeholder="สกุล" value="<?php echo $row_person['slname'];?>"/>
				
			</div>
			
		</div>


		<div class="form-group row">

    <div class="col-lg-3">
				<label>ชื่อเล่น</label>
				<input type="text" class="form-control"  name="nickname" id="nickname" placeholder="ชื่อเล่น" value="<?php echo $row_person['nickname'];?>"/>
				
			</div>


    <div class="col-lg-2">
				<label>เพศ</label>
            <select class="form-control " name="sex" id="sex">
                        <option value="">ระบุ</option>
                        <?php
                          $stmt_user_role = $conn->prepare("SELECT * FROM ".DB_PREFIX."csex  ORDER BY sex ASC");
                          $stmt_user_role->execute();		
                          while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC))
                            {
                            $id_selected = $row['sex'];
                            $title_selected = stripslashes($row['sexname']);
                            ?>
                            <option value="<?php echo $id_selected;?>" <?php if($row_person['sex'] == $id_selected ){echo "selected";} ?>><?php echo $title_selected;?></option>
                            <?php
                            }
                          ?>
            </select>
				
			</div>

    <div class="col-lg-3">
				<label>วันเดือนปีเกิด</label>
				<input type="text" class="form-control"  name="birthdate" id="birthdate" placeholder="วันเดือนปีเกิด" value="<?php echo date_db_2form($row_person['birthdate']);?>"  data-date-language="th-th" maxlength="10"/>
				<span class="form-text text-muted"></span>
				
			</div>

    <div class="col-lg-4">
				<label>โทรศัพท์</label>
				<input type="text" class="form-control"  name="telephone" id="telephone" placeholder="โทรศัพท์" value="<?php echo $row_person['telephone'];?>" maxlength="10"/>
				<span class="form-text text-muted">หมายเลขโทรศัพท์ 10 หลัก</span>
				
			</div>
			

		</div>

    <span><i class="fas fa-calendar"></i> ข้อมูลการปฏิบัติงาน :</span>
<hr>
      <div class="form-group row">

            <div class="col-lg-3">
              <label>วันที่เริ่มทำงาน</label>
              <input type="text" class="form-control"  name="startdate" id="startdate" placeholder="วันที่เริ่มทำงาน" value="<?php echo date_db_2form($row_person['startdate']);?>"  data-date-language="th-th" maxlength="10"/>
            </div>

            <div class="col-lg-2">
				<label>สถานะการทำงาน</label>
            <select class="form-control " name="flag" id="flag">
                        <option value="">ระบุ</option>
                        <option value="1" <?php if($row_person['flag'] == '1' ){echo "selected";} ?>>ปฏิบัติงาน</option>
                        <option value="2" <?php if($row_person['flag'] == '2' ){echo "selected";} ?>>ลาออก</option>
                        <option value="0" <?php if($row_person['flag'] == '0' ){echo "selected";} ?>>ลบข้อมูล</option>
            </select>
				
			</div>


            <div class="col-lg-3">
              <label>วันที่สิ้นสุดทำงาน</label>
              <input type="text" class="form-control"  name="outdate" id="outdate" placeholder="วันที่สิ้นสุดทำงาน" value="<?php echo date_db_2form($row_person['outdate']);?>"  data-date-language="th-th" maxlength="10"/>
            </div>
      </div>
      <div class="form-group row">
                                            <div class="col-lg-12">
                                              <label>เหตุผลการสิ้นสุดการทำงาน</label>
                                              <textarea class="form-control"  name="out_desc" id="out_desc"><?php echo $row_person['out_desc'];?></textarea>
                                            </div>
                                            </div>


   <span><i class="fas fa-house-user"></i> ที่อยู่ :</span>
<hr>
		<div class="form-group row">
			<div class="col-lg-3">
				<label>บ้านเลขที่</label>
				<input type="text" class="form-control"  name="house" id="house" placeholder="บ้านเลขที่" value="<?php echo $row_person['house'];?>"/>
				
			</div>
			<div class="col-lg-4">
				<label>หมู่บ้าน/ชุมชน</label>
        <input type="text" class="form-control"  name="community" id="community" placeholder="หมู่บ้าน/ชุมชน" value="<?php echo $row_person['community'];?>"/>
				
			</div>

			

            <div class="col-lg-3">
				<label>ถนน</label>
             <input type="text" class="form-control"  name="road" id="road" placeholder="ถนน" value="<?php echo $row_person['road'];?>"/>
				
			</div>

            <div class="col-lg-2">
				<label>หมู่ที่</label>
				<select class="form-control " name="village" id="village">
                    <option value=""  <?php if($row_person['village'] == "0"){ echo "selected";}?>>0</option>
								
								<?php for ($n_vil = 1; $n_vil <= 99; $n_vil++) { 
									$n_vil_data = str_pad($n_vil,2,"0",STR_PAD_LEFT);
									?>
										<option value="<?php echo $n_vil_data;?>" <?php if($row_person['village'] == $n_vil_data){ echo "selected";}?>><?php echo $n_vil;?></option>
								<?php } ?>
								
                    
				</select>
			</div>
			
		</div>

        <input type="hidden" class="form-control"  name="txt_ampur" id="txt_ampur" value="<?php echo $row_person['ampur'];?>"/>
        <input type="hidden" class="form-control"  name="txt_tambon" id="txt_tambon" value="<?php echo $row_person['tambon'];?>"/>
    <div class="form-group row">

    <div class="col-lg-3">
				<label>จังหวัด</label>
            <select class="form-control " name="changwat" id="changwat">
                        
                        <?php
                                                            $stmt = $conn->prepare ("SELECT * FROM cchangwat c ");
                                                            $stmt->execute();
                                                            echo "<option value=''>-ระบุ-</option>";
                                                            while ($row = $stmt->fetch(PDO::FETCH_OBJ)){
                                                            $id = $row->changwatcode;
                                                            $name = $row->changwatname; ?>
                                                            <option value="<?php echo $id;?>" <?php if($row_person['changwat'] == $id){ echo "selected";}?>><?php echo $name;?></option>
                                                            <?php 
                                                            }
                                                        ?>
            </select>
				
			</div>

      <div class="col-lg-3">
				<label>อำเภอ</label>
            <select class="form-control " name="ampur" id="ampur">
                        <option value="">ระบุ</option>
            </select>
			</div>

      <div class="col-lg-3">
				<label>ตำบล</label>
            <select class="form-control " name="tambon" id="tambon">
                        <option value="">ระบุ</option>
            </select>
			</div>



      </div>

	


		</div><!--col-->

		
		<div class="col-lg-3 border-x-0 border-x-md border-y border-y-md-0">

		        <div class="form-group row">
					<div class="col-lg-12">
					
								<div class="symbol symbol-50 symbol-lg-150 ">
								

                                    <?php if($row_person['img_profile'] == ""){?>
                                         <img src="uploads/equipment/no-image.jpg" alt="image"/>
                                        <?php }else{?>
                                            <img src="uploads/staff/<?php echo $row_person['img_profile'];?>" alt="image"/>
                                        <?php   } ?>


									<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove">
                                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                                </span>
								</div>	
					</div>
				</div>


				<div class="form-group row">
					<div class="col-lg-12">
						<label>รูปถ่าย</label>
						<input type="file" class="form-control"  name="img_profile" id="img_profile" placeholder="รูปถ่ายผู้รับบริการ"/>
						<span class="form-text text-muted">.jpg .png เท่านั้น</span>
					</div>
				</div>


				</div>


		</div><!--col-->
		</div><!--row-->



	<div class="card-footer">
		<div class="row">
			<div class="col-lg-6">
				<button type="button" class="btn btn-primary mr-2" id="btnSavePerson"><i class="fa fa-save" title="ย้อนกลับ" ></i> บันทึก</button>
				<button type="button" class="btn btn-secondary" onclick="javascript:history.back()" ><i class="fa fa-chevron-left" title="ย้อนกลับ" ></i> ย้อนกลับ</button>
			</div>
			<div class="col-lg-6 text-right">
				<!--<button type="reset" class="btn btn-danger">Delete</button>-->
			</div>
		</div>
	</div>
</form>

</div>
		<!--end::Card-->

        <script>
$(document).ready(function () {
    'use strict';

    getoptselect_amphur();
	getoptselect_tambon();
						
}); 


$('#birthdate').datepicker({
        autoclose: true
});

$('#startdate').datepicker({
        autoclose: true
});

$('#outdate').datepicker({
        autoclose: true
});


$("#changwat").change(function() {
    $("#txt_ampur").val('');
    $("#txt_tambon").val('');
    getoptselect_amphur();
    getoptselect_tambon();
});


$("#ampur").change(function() {
    $("#txt_tambon").val('');
    getoptselect_tambon();
});






function getoptselect_amphur(){

    var changwatcode = $("#changwat").val();
    var ampur = $("#txt_ampur").val();
    $.ajax({
        type: "POST",
        url: "core/fn-get-ampur.php",
        //dataType: "json",
        data: {changwatcode:changwatcode,ampur:ampur},
        success: function(data) {
        
            $("#ampur").empty();
            $("#ampur").append(data);
        } // success
    });
}	

function getoptselect_tambon(){

var changwatcode = $("#changwat").val();
var ampur = $("#txt_ampur").val();
var ampurcode = $("#ampur").val();
var tambon = $("#txt_tambon").val();
    $.ajax({
        type: "POST",
        url: "core/fn-get-tambon.php",
        //dataType: "json",
        data: {changwatcode:changwatcode,ampurcode:ampurcode,ampur:ampur,tambon:tambon},
        success: function(data) {
        
            $("#tambon").empty();
            $("#tambon").append(data);
        } // success
    });

}	



</script>

		                             
<script>



$('#btnSavePerson').click(function(e){
        e.preventDefault();
        if ($('#cid').val().length == ""){
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาระบุเลขบัตรประชาชน',
                    showConfirmButton: false,
                    timer: 1000
                    });
        }else if ($('#org_id').val().length == ""){
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาระบุหน่วยงาน',
                    showConfirmButton: false,
                    timer: 1000
                    });
        }else if ($('#sfname').val().length == ""){
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาระบุชื่อ',
                    showConfirmButton: false,
                    timer: 1000
                    });
        }else if ($('#slname').val().length == ""){
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาระบุนามสกุล',
                    showConfirmButton: false,
                    timer: 1000
                    });
        }else if ($('#flag').val().length == ""){
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาระบุสถานะการทำงาน',
                    showConfirmButton: false,
                    timer: 1000
                    });
        }else {

		var data = new FormData(this.form);
        $.ajax({
            type: "POST",
            url: "core/repair/staff-add.php",
            dataType: "json",
			data: data,
			processData: false,
            contentType: false,
            success: function(data) {  
              if (data.code == "200") {
                Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ',
                showConfirmButton: false,
                timer: 1500
                })
                    .then((value) => {
                    //liff.closeWindow();
                    window.location.replace("dashboard.php?module=repair&page=staff");
                    
                }); 
                } else if (data.code == "404") {
                  //swal("ไม่สามารถบันทึกข้อมูลได้ กรุณาลองอีกครั้ง")
                   Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถบันทึกข้อมูลได้',
                    text: 'กรุณาลองใหม่อีกครั้ง'
                    })
                    .then((value) => {
                      //liff.closeWindow();
                  });
                }
            },error: function (jqXHR, exception) {
              console.log(jqXHR);
              // Your error handling logic here..
          } // success
        });
        }
      }); //  click

</script>



