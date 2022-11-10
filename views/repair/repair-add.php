<?php
error_reporting(0);
$personid = filter_input(INPUT_GET, 'personid', FILTER_SANITIZE_STRING);
$personid_enc = filter_input(INPUT_GET, 'personid', FILTER_SANITIZE_STRING);
$repairid = filter_input(INPUT_GET, 'repairid', FILTER_SANITIZE_STRING);
$repairid_enc = filter_input(INPUT_GET, 'repairid', FILTER_SANITIZE_STRING);
$act = filter_input(INPUT_GET, 'act', FILTER_SANITIZE_STRING);
$personid = base64_decode($personid);
$repairid = base64_decode($repairid);
$action = base64_decode($act);
if($action == "edit"){
	$txt_title = "แก้ไข";
	$action = $action;

	$stmt_data = $conn->prepare ("SELECT p.*,o.org_name FROM ".DB_PREFIX."person_main p 
	LEFT JOIN ".DB_PREFIX."org_main o ON p.org_id = o.org_id 
    WHERE p.oid = '$personid'  LIMIT 1");
    $stmt_data->execute();	
    $row_person = $stmt_data->fetch(PDO::FETCH_ASSOC);
  
   $sql_service = "SELECT s.* FROM ".DB_PREFIX."repair_main s 
	LEFT JOIN ".DB_PREFIX."person_main p ON s.person_id = p.oid 
    WHERE s.repair_id = '$repairid' AND s.flag != '0'  LIMIT 1";
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
        <div class="ribbon-target bg-danger" style="top: 10px; right: -2px;">1</div>
        <h3 class="card-title">
            <i class="fas fa-user-cog"></i>&nbsp;<?php echo $txt_title;?>รายการแจ้งซ่อม
        </h3>
        <div class="card-toolbar">
            <div class="example-tools justify-content-center">
                <a href="dashboard.php?module=<?php echo $module;?>"
                    class="btn btn-defalse btn-sm font-weight-bold mr-2" title="ย้อนกลับ"><i class="fa fa-chevron-left"
                        title="ย้อนกลับ"></i></a>
            </div>
        </div>
    </div>


    <form class="form" enctype="multipart/form-data">
        <input type="hidden" class="form-control" name="act" id="act" value="<?php echo $action;?>" />
        <input type="hidden" class="form-control" name="repairid" id="repairid" value="<?php echo $repairid;?>" />
        <input type="hidden" class="form-control" name="personid" id="personid" value="<?php echo $personid;?>" />
        <input type="hidden" class="form-control" name="repair_inout" id="repair_inout" value="I" />
        <input type="hidden" class="form-control" name="org_id" id="org_id" value="<?php echo $logged_org_id;?>" />
        <div class="card-body">


            <div class="row">
                <div class="col-lg-9">

                    <span><i class="far fa-bookmark"></i> ข้อมูลแจ้งซ่อม</span>
                    <hr>
                    <div class="form-group row">

                        <div class="col-lg-2">
                            <label>วันที่แจ้งซ่อม</label>
                            <?php if($action == 'add'){?>
                            <input type="text" class="form-control" name="repairdate" id="repairdate"
                                placeholder="วันที่แจ้งซ่อม"
                                value="<?php echo date('d').'/'.date('m').'/'.(date('Y')+543);?>"
                                data-date-language="th-th" maxlength="10" />
                            <?php } else { ?>
                            <input type="text" class="form-control" name="repairdate" id="repairdate"
                                placeholder="วันที่แจ้งซ่อม"
                                value="<?php echo date_db_2form($row_service['repair_date']);?>"
                                data-date-language="th-th" maxlength="10" />
                            <?php } ?>
                            <span class="form-text text-muted"></span>

                        </div>

                        <div class="col-lg-3">
                            <label>ประเภทรับบริการ</label>
                            <select class="form-control " name="repair_type" id="repair_type">
                                <?php
				
					$stmt_user_role = $conn->prepare("SELECT * FROM ".DB_PREFIX."repair_type  ");
					$stmt_user_role->execute();		
					while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC))
						{
						$id_selected = $row['repair_typeid'];
						$title_selected = stripslashes($row['repair_typetitle']);
						?>
                                <option value="<?php echo $id_selected;?>"
                                    <?php if($row_service['repair_type'] == $id_selected ){echo "selected";} ?>>
                                    <?php echo $title_selected;?></option>
                                <?php
						}
					?>
                            </select>
                        </div>

                        <div class="col-lg-2">
                            <label>สถานที่ซ่อม</label>
                            <select class="form-control " name="repair_place" id="repair_place">
                                <?php
				
					$stmt_user_role = $conn->prepare("SELECT * FROM ".DB_PREFIX."repair_place  ");
					$stmt_user_role->execute();		
					while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC))
						{
						$id_selected = $row['place_id'];
						$title_selected = stripslashes($row['place_title']);
						?>
                                <option value="<?php echo $id_selected;?>"
                                    <?php if($row_service['repair_place'] == $id_selected ){echo "selected";} ?>>
                                    <?php echo $title_selected;?></option>
                                <?php
						}
					?>
                            </select>
                        </div>

                        <!-- <div class="col-lg-5">
				<label>หน่วยงาน</label>
				<select class="form-control " name="org_id" id="org_id">
                    
                    <?php
					if($logged_user_role_id == '1'){
                        //$conditions = " ";
                        $conditions = " AND org_id = '$logged_org_id' ";
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
						<option value="<?php echo $id_selected;?>" <?php if($row_service['org_id'] == $id_selected ){echo "selected";} ?>><?php echo $title_selected;?></option>
						<?php
						}
					?>
				</select>
			</div> -->

                    </div>
                    <span><i class="far fa-user"></i> ข้อมูลลูกค้า</span>
                    <hr>

                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>ประเภทบุคคล</label>
                            <div class="col-9 col-form-label">
                                <div class="radio-inline">
                                    <label class="radio radio-success">
                                        <input type="radio" id="person" name="person_type" value="1" <?php if(($row_person['person_type'] == '1') OR ($row_person['person_type'] == '')){ 
                                        echo "checked='checked' ";}?> />
                                        <span></span>
                                        บุคคล
                                    </label>
                                    <label class="radio radio-success">
                                        <input type="radio" id="company" name="person_type" value="2" <?php if($row_person['person_type'] == '2'){ 
                                        echo "checked='checked' ";}?> />
                                        <span></span>
                                        หน่วยงาน
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <label>เลขบัตรประชาชน/เลขผู้เสียภาษี</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="เลขบัตรประชาชน/เลขผู้เสียภาษี"
                                    name="cid" id="cid" maxlength="13" value="<?php echo $row_person['cid'];?>" />
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="button" id="cidSearch"><i
                                            class="fas fa-search"></i></button>
                                </div>
                            </div>
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
                                <option value="<?php echo $id_selected;?>"
                                    <?php if($row_person['prename'] == $id_selected ){echo "selected";} ?>>
                                    <?php echo $title_selected;?></option>
                                <?php
                            }
                          ?>
                            </select>

                        </div>
                        <div class="col-lg-5">
                            <label>ชื่อ</label>
                            <input type="text" class="form-control" name="fname" id="fname" placeholder="ชื่อ"
                                value="<?php echo $row_person['fname'];?>" />

                        </div>
                        <div class="col-lg-5">
                            <label>สกุล</label>
                            <input type="text" class="form-control" name="lname" id="lname" placeholder="สกุล"
                                value="<?php echo $row_person['lname'];?>" />

                        </div>

                    </div>


                    <div class="form-group row">
                        <div class="col-lg-9">
                            <label>ชื่อบริษัท/หน่วยงาน</label>
                            <input type="text" class="form-control" name="comp_name" id="comp_name"
                                placeholder="ชื่อบริษัท/หน่วยงาน" value="<?php echo $row_person['comp_name'];?>" />
                        </div>

                        <div class="col-lg-3">
                            <label>เลขทะเบียนพานิชย์</label>
                            <input type="text" class="form-control" name="comp_code" id="comp_code"
                                placeholder="เลขทะเบียนพานิชย์" value="<?php echo $row_person['comp_code'];?>" />
                        </div>

                    </div>


                    <div class="form-group row">

                        <div class="col-lg-4">
                            <label>โทรศัพท์</label>
                            <input type="text" class="form-control" name="telephone" id="telephone"
                                placeholder="โทรศัพท์" value="<?php echo $row_person['telephone'];?>" maxlength="10" />
                            <span class="form-text text-muted">หมายเลขโทรศัพท์ 10 หลัก</span>

                        </div>


                        <div class="col-lg-8">
                            <label>อีเมลล์</label>
                            <input type="text" class="form-control" name="email" id="email" placeholder="อีเมลล์"
                                value="<?php echo $row_person['email'];?>" />
                            <span class="form-text text-muted"></span>

                        </div>
                        <!--
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

                        -->


                    </div>

                    <span><i class="fas fa-house-user"></i> ที่อยู่ :</span>
                    <hr>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>บ้านเลขที่</label>
                            <input type="text" class="form-control" name="house" id="house" placeholder="บ้านเลขที่"
                                value="<?php echo $row_person['house'];?>" />

                        </div>
                        <div class="col-lg-4">
                            <label>หมู่บ้าน/ชุมชน</label>
                            <input type="text" class="form-control" name="community" id="community"
                                placeholder="หมู่บ้าน/ชุมชน" value="<?php echo $row_person['community'];?>" />

                        </div>



                        <div class="col-lg-3">
                            <label>ถนน</label>
                            <input type="text" class="form-control" name="road" id="road" placeholder="ถนน"
                                value="<?php echo $row_person['road'];?>" />

                        </div>

                        <div class="col-lg-2">
                            <label>หมู่ที่</label>
                            <select class="form-control " name="village" id="village">
                                <option value="" <?php if($row_person['village'] == "0"){ echo "selected";}?>>0</option>

                                <?php for ($n_vil = 1; $n_vil <= 99; $n_vil++) { 
									$n_vil_data = str_pad($n_vil,2,"0",STR_PAD_LEFT);
									?>
                                <option value="<?php echo $n_vil_data;?>"
                                    <?php if($row_person['village'] == $n_vil_data){ echo "selected";}?>>
                                    <?php echo $n_vil;?></option>
                                <?php } ?>


                            </select>
                        </div>

                    </div>

                    <input type="hidden" class="form-control" name="txt_ampur" id="txt_ampur"
                        value="<?php echo $row_person['ampur'];?>" />
                    <input type="hidden" class="form-control" name="txt_tambon" id="txt_tambon"
                        value="<?php echo $row_person['tambon'];?>" />
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
                                <option value="<?php echo $id;?>"
                                    <?php if($row_person['changwat'] == $id){ echo "selected";}?>><?php echo $name;?>
                                </option>
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




                </div>
                <!--col-->


                <div class="col-lg-3 border-x-0 border-x-md border-y border-y-md-0">

                    <div class="form-group row">
                        <div class="col-lg-12">

                            <div class="symbol symbol-50 symbol-lg-150 ">


                                <?php if($row_person['img_profile'] == ""){?>
                                <img id="img" src="uploads/no-image.jpg" alt="image" />
                                <?php }else{?>
                                <img id="img" src="uploads/person/<?php echo $row_person['img_profile'];?>"
                                    alt="image" />
                                <?php   } ?>


                                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                    data-action="remove" data-toggle="tooltip" title="ลบรูปถ่าย">
                                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                                </span>
                            </div>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>รูปถ่ายผู้แจ้งซ่อม</label>
                            <input type="file" class="form-control" name="img_profile" id="img_profile"
                                placeholder="รูปถ่ายผู้แจ้งซ่อม" />
                            <span class="form-text text-muted">.jpg .png เท่านั้น</span>
                        </div>
                    </div>


                </div>


            </div>
            <!--col-->
        </div>
        <!--row-->



        <div class="card-footer">
            <div class="row">
                <div class="col-lg-6">
                    <button type="button" class="btn btn-primary mr-2" id="btnSave"><i class="fa fa-save"
                            title="บันทึก"></i> บันทึก</button>
                    <button type="button" class="btn btn-warning" onclick="javascript:history.back()"><i
                            class="fa fa-chevron-left" title="ย้อนกลับ"></i> </button>
                    <?php if($action == "edit"){?>
                    <a href="dashboard.php?module=repair&page=repair-add-data&repairid=<?php echo $repairid_enc;?>&personid=<?php echo $personid_enc;?>&act=<?php echo base64_encode('edit');?>"
                        type="button" class="btn btn-warning" o><i class="fa fa-chevron-right" title="ถัดไป"></i> </a>
                    <?php } ?>
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
$(document).ready(function() {
    'use strict';

    getoptselect_amphur();
    getoptselect_tambon();
});


$('#birthdate').datepicker({
    autoclose: true
});

$('#repairdate').datepicker({
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



function getoptselect_amphur() {

    var changwatcode = $("#changwat").val();
    var ampur = $("#txt_ampur").val();
    $.ajax({
        type: "POST",
        url: "core/fn-get-ampur.php",
        //dataType: "json",
        data: {
            changwatcode: changwatcode,
            ampur: ampur
        },
        success: function(data) {

            $("#ampur").empty();
            $("#ampur").append(data);
        } // success
    });
}

function getoptselect_tambon() {

    var changwatcode = $("#changwat").val();
    var ampur = $("#txt_ampur").val();
    var ampurcode = $("#ampur").val();
    var tambon = $("#txt_tambon").val();
    $.ajax({
        type: "POST",
        url: "core/fn-get-tambon.php",
        //dataType: "json",
        data: {
            changwatcode: changwatcode,
            ampurcode: ampurcode,
            ampur: ampur,
            tambon: tambon
        },
        success: function(data) {

            $("#tambon").empty();
            $("#tambon").append(data);
        } // success
    });

}



function lineAlert(action) {
    if (action == 'add') {
        var org_id = $("#org_id").val();

        $.ajax({
            type: "POST",
            url: "core/repair/repair-add-notify.php",
            //dataType: "json",
            data: {
                org_id: org_id
            },
            success: function(data) {

                //$("#tambon").empty();
                //$("#tambon").append(data);
            } // success
        });
    }
}

function getoptselect_amphur_selected(changwatcode, ampur, tambon) {

    $.ajax({
        type: "POST",
        url: "core/fn-get-ampur.php",
        //dataType: "json",
        data: {
            changwatcode: changwatcode,
            ampur: ampur
        },
        success: function(data) {

            $("#ampur").empty();
            $("#ampur").append(data);

        } // success
    });
}

function getoptselect_tambon_selected(changwatcode, ampurcode, tambon) {

    var ampur = $("#txt_ampur").val();

    $.ajax({
        type: "POST",
        url: "core/fn-get-tambon.php",
        //dataType: "json",
        data: {
            changwatcode: changwatcode,
            ampurcode: ampurcode,
            ampur: ampur,
            tambon: tambon
        },
        success: function(data) {

            $("#tambon").empty();
            $("#tambon").append(data);
        } // success
    });

}



$('#btnSave').click(function(e) {
    e.preventDefault();
    if ($('#repairdate').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุวันที่แจ้งซ่อม',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#repair_type').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุประเภทรับบริการ',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#org_id').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุหน่วยงาน',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#cid').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุเลขบัตรประชาชน/เลขผู้เสียภาษี',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#fname').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุชื่อ',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#lname').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุนามสกุล',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#telephone').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุหมายเลขโทรศัพท์',
            showConfirmButton: false,
            timer: 1000
        });
    } else {

        var data = new FormData(this.form);
        $.ajax({
            type: "POST",
            url: "core/repair/repair-add.php",
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
                            lineAlert(data.action);
                            window.location.replace(
                                "dashboard.php?module=repair&page=repair-add-data&personid=" +
                                data.personid + "&repairid=" + data.repairid + "&act=" +
                                data.act);

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

            },
            error: function(jqXHR, exception) {
                console.log(jqXHR);
                // Your error handling logic here..
            } // success
        });
    }
}); //  click

$('#cidSearch').click(function(e) {
    e.preventDefault();
    var data = new FormData(this.form);
    $.ajax({
        type: "POST",
        url: "core/repair/person-get-data.php",
        dataType: "json",
        data: data,
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.code == "200") {
                Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    .then((value) => {
                        $('#fname').val(data.data['fname']);
                        $('#prename').val(data.data['prename']);
                        $('#lname').val(data.data['lname']);
                        $('#comp_name').val(data.data['comp_name']);
                        $('#comp_code').val(data.data['comp_code']);
                        $('#telephone').val(data.data['telephone']);
                        $('#email').val(data.data['email']);
                        $('#house').val(data.data['house']);
                        $('#community').val(data.data['community']);
                        $('#road').val(data.data['road']);
                        $('#village').val(data.data['village']);
                        $("#changwat").val(data.data['changwat']).change();
                        if (data.data['img_profile'] != null) {
                            $("#img").attr("src", "uploads/person/" + data.data['img_profile']);
                        } else {
                            $("#img").attr("src", "uploads/no-image.jpg")
                        }
                        getoptselect_tambon_selected(data.data['changwat'], data.data['ampur'],
                            data.data['tambon']);
                        getoptselect_amphur_selected(data.data['changwat'], data.data['ampur'],
                            data.data['tambon']);
                    });
            } else if (data.code == "404") {
                //swal("ไม่สามารถบันทึกข้อมูลได้ กรุณาลองอีกครั้ง")
                Swal.fire({
                        icon: 'error',
                        title: 'ไม่มีข้อมูล',
                        text: 'กรุณาลองใหม่อีกครั้ง'
                    })
                    .then((value) => {
                        //liff.closeWindow();
                    });
            }

        },
        error: function(jqXHR, exception) {
            console.log(jqXHR);
            // Your error handling logic here..
        } // success
    });
}); //  click
</script>