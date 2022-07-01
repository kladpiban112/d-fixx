<?php
error_reporting(0);
$eqtypeid = filter_input(INPUT_GET, 'eqtypeid', FILTER_SANITIZE_STRING);
$act = filter_input(INPUT_GET, 'act', FILTER_SANITIZE_STRING);
$eqtypeid = base64_decode($eqtypeid);
$action = base64_decode($act);
if($action == "edit"){
	$txt_title = "แก้ไข";
	$action = $action;

	$stmt_data = $conn->prepare ("SELECT * FROM ".DB_PREFIX."equipment_type u
	WHERE u.eq_typeid = '$eqtypeid' LIMIT 1");
	$stmt_data->execute();	
	$row = $stmt_data->fetch(PDO::FETCH_ASSOC);


}else{
	$txt_title = "เพิ่ม";
	$action = "add";
}
?>

		<!--begin::Card-->
		<div class="card card-custom gutter-b example example-compact">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fab fa-buffer"></i>&nbsp;<?php echo $txt_title;?>ประเภทกายอุปกรณ์ 
				</h3>
				<div class="card-toolbar">
					<div class="example-tools justify-content-center">
						<a href="dashboard.php?module=<?php echo $module;?>" class="btn btn-defalse btn-sm font-weight-bold mr-2" title="ย้อนกลับ"><i class="fa fa-chevron-left" title="ย้อนกลับ" ></i></a>
					</div>
				</div>
			</div>


<form class="form" enctype="multipart/form-data" >
<input type="hidden" class="form-control"  name="act" id="act" value="<?php echo $action;?>"/>
<input type="hidden" class="form-control"  name="eq_typeid" id="eq_typeid" value="<?php echo $eqtypeid;?>"/>
	<div class="card-body">
	<div class="row">
	<div class="col-lg-12">
		<div class="form-group row">
			<div class="col-lg-9">
				<label>ประเภทกายอุปกรณ์</label>
				<input type="text" class="form-control"  name="eq_typename" id="eq_typename" placeholder="ประเภทกายอุปกรณ์" value="<?php echo $row['eq_typename'];?>"/>
				
			</div>
					<div class="col-lg-3">
						<label>สถานะใช้งาน</label>
						<select class="form-control "  name="flag" id="flag">
							<option value="1" <?php if($row['flag'] == '1' ){echo "selected";} ?>>เปิดใช้งาน</option>
							<option value="0" <?php if($row['flag'] == '0' ){echo "selected";} ?>>ปิดใช้งาน</option>
						</select>
					</div>
				</div>


		</div>
		</div>



		</div><!--row-->



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
$('#btnSave').click(function(e){
        e.preventDefault();
        if ($('#eq_typename').val().length == ""){
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาระบุประเภท',
                    showConfirmButton: false,
                    timer: 1000
                    });
        }else{
		var eq_typeid = $("#eq_typeid").val();
        var eq_typename = $("#eq_typename").val();
		var flag = $("#flag").val();
        

		var data = new FormData(this.form);
        $.ajax({
            type: "POST",
            url: "core/systems/equipment-type-add.php",
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
                    window.location.replace("dashboard.php?module=systems&page=equipment-type");
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