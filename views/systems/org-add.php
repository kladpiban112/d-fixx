<?php
error_reporting(0);
$orgid = filter_input(INPUT_GET, 'orgid', FILTER_SANITIZE_STRING);
$act = filter_input(INPUT_GET, 'act', FILTER_SANITIZE_STRING);
$orgid = base64_decode($orgid);
$action = base64_decode($act);
if($action == "edit"){
	$txt_title = "แก้ไข";
	$action = $action;

	$stmt_data = $conn->prepare ("SELECT * FROM ".DB_PREFIX."org_main u
	WHERE u.org_id = '$orgid' LIMIT 1");
	$stmt_data->execute();	
	$row = $stmt_data->fetch(PDO::FETCH_ASSOC);

	if($row['latitude']==""){
		$latitude = '0.00000';
	}else{
		$latitude = $row['latitude'];
	}
	if($row['longitude']==""){
		$longitude = '0.00000';
	}else{
		$longitude = $row['longitude'];
	}



}else{
	$txt_title = "เพิ่ม";
	$action = "add";
	$latitude = '0.00000';
	$longitude = '0.00000';
}
?>

		<!--begin::Card-->
		<div class="card card-custom gutter-b example example-compact">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-building"></i>&nbsp;<?php echo $txt_title;?>หน่วยงาน 
				</h3>
				<div class="card-toolbar">
					<div class="example-tools justify-content-center">
						<a href="#" onclick="javascript:history.back()" class="btn btn-defalse btn-sm font-weight-bold mr-2"  title="ย้อนกลับ"><i class="fa fa-chevron-left" title="ย้อนกลับ" ></i></a>
					</div>
				</div>
			</div>


<form class="form" enctype="multipart/form-data" >
<input type="hidden" class="form-control"  name="act" id="act" value="<?php echo $action;?>"/>
<input type="hidden" class="form-control"  name="org_id" id="org_id" value="<?php echo $orgid;?>"/>
	<div class="card-body">
	<div class="row">
	<div class="col-lg-12">
		
		<div class="form-group row">
			<div class="col-lg-6">
				<label>ชื่อหน่วยงาน</label>
				<input type="text" class="form-control"  name="org_name" id="org_name" placeholder="ชื่อหน่วยงาน" value="<?php echo $row['org_name'];?>"/>
				
			</div>
			<div class="col-lg-4">
				<label>ชื่อย่อ</label>
				<input type="text" class="form-control"  name="org_shortname" id="org_shortname" placeholder="ชื่อย่อ" value="<?php echo $row['org_shortname'];?>"/>
				
			</div>
					<div class="col-lg-2">
						<label>สถานะใช้งาน</label>
						<select class="form-control "  name="flag" id="flag">
							<option value="1" <?php if($row['flag'] == '1' ){echo "selected";} ?>>เปิดใช้งาน</option>
							<option value="0" <?php if($row['flag'] == '0' ){echo "selected";} ?>>ปิดใช้งาน</option>
						</select>
					</div>
				</div>
		

		<div class="form-group row">
			<div class="col-lg-6">
				<label>โทรศัพท์</label>
				<input type="text" class="form-control"  name="org_telephone" id="org_telephone" placeholder="โทรศัพท์" value="<?php echo $row['org_telephone'];?>"/>
				
			</div>
			<div class="col-lg-3">
				<label>Latitude</label>
				<input type="text" class="form-control"  name="latitude" id="latitude" placeholder="" value="<?php echo $latitude;?>"/>
				
			</div>
			<div class="col-lg-3">
				<label>Longitude</label>
				<input type="text" class="form-control"  name="longitude" id="longitude" placeholder="" value="<?php echo $longitude;?>"/>
				
			</div>
					
		</div>

		<input type="hidden" class="form-control"  name="txt_ampur" id="txt_ampur" value="<?php echo $row['org_ampur'];?>"/>
        <input type="hidden" class="form-control"  name="txt_tambon" id="txt_tambon" value="<?php echo $row['org_tambon'];?>"/>
    <div class="form-group row">

	<div class="col-lg-3">
				<label>ที่อยู่</label>
				<input type="text" class="form-control"  name="org_address" id="org_address" placeholder="โทรศัพท์" value="<?php echo $row['org_address'];?>"/>
				
			</div>

    <div class="col-lg-3">
				<label>จังหวัด</label>
            <select class="form-control " name="changwat" id="changwat">
                        
                        <?php
                                                            $stmt = $conn->prepare ("SELECT * FROM cchangwat c ");
                                                            $stmt->execute();
                                                            echo "<option value=''>-ระบุ-</option>";
                                                            while ($row_c = $stmt->fetch(PDO::FETCH_OBJ)){
                                                            $id = $row_c->changwatcode;
                                                            $name = $row_c->changwatname; ?>
                                                            <option value="<?php echo $id;?>" <?php if($row['org_changwat'] == $id){ echo "selected";}?>><?php echo $name;?></option>
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

	  <div class="form-group row">
			<div class="col-lg-3">
				<label>โลโก้</label>
			
				<input type="file" name="image" class="form-control" >
				
			</div>

					
		</div>


		</div>
		</div><!--row-->
		</div>



	<div class="card-footer">
		<div class="row">
			<div class="col-lg-6">
				<button type="button" class="btn btn-primary mr-2" id="btnSave"><i class="fa fa-save" title="ย้อนกลับ" ></i> บันทึก</button>
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


$('#btnSave').click(function(e){
        e.preventDefault();
        if ($('#org_name').val().length == ""){
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาระบุชื่อหน่วยงาน',
                    showConfirmButton: false,
                    timer: 1000
                    });
        }else{
		var org_id = $("#org_id").val();
        var org_name = $("#org_name").val();
		var flag = $("#flag").val();
        

		var data = new FormData(this.form);
        $.ajax({
            type: "POST",
            url: "core/systems/org-add.php",
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
                    window.location.replace("dashboard.php?module=systems&page=org-main");
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
            } // success
        });

        }
    
      }); //  click

</script>