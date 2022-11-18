<?php
error_reporting(0);
$personid = filter_input(INPUT_GET, 'personid', FILTER_SANITIZE_STRING);
$repairid = filter_input(INPUT_GET, 'repairid', FILTER_SANITIZE_STRING);
$act = filter_input(INPUT_GET, 'act', FILTER_SANITIZE_STRING);
$personid = base64_decode($personid);
$repairid = base64_decode($repairid);
$personid_enc = filter_input(INPUT_GET, 'personid', FILTER_SANITIZE_STRING);
$repairid_enc = filter_input(INPUT_GET, 'repairid', FILTER_SANITIZE_STRING);
$action = base64_decode($act);
if ($action == 'edit') {
    $txt_title = 'แก้ไข';
    $action = $action;

    $stmt_data = $conn->prepare('SELECT p.*,o.org_name FROM '.DB_PREFIX.'person_main p 
	LEFT JOIN '.DB_PREFIX."org_main o ON p.org_id = o.org_id 
    WHERE p.oid = '$personid'  LIMIT 1");
    $stmt_data->execute();
    $row_person = $stmt_data->fetch(PDO::FETCH_ASSOC);

    $sql_service = 'SELECT s.*,qts.qt_statusname,st.* FROM '.DB_PREFIX.'repair_main s 
	LEFT JOIN '.DB_PREFIX.'person_main p ON s.person_id = p.oid 
    LEFT JOIN '.DB_PREFIX."repair_quotation_status qts ON s.qt_status = qts.qt_statusid
    LEFT JOIN ".DB_PREFIX."repair_status_type st ON s.repair_status = st.status_typeid

    WHERE s.repair_id = '$repairid' AND s.flag != '0'  LIMIT 1";
    $stmt_service = $conn->prepare($sql_service);
    $stmt_service->execute();
    $row_service = $stmt_service->fetch(PDO::FETCH_ASSOC);
    $qt_status = $row_service['qt_status']; 
    $status_typeid = $row_service['repair_status'];
    $status_title = $row_service['status_title'];



    $stmt_qt = $conn->prepare('SELECT u.* FROM '.DB_PREFIX."repair_quotation u 
        WHERE u.flag != '0' AND u.repair_id = '$repairid' 
        ORDER BY u.oid DESC LIMIT 1");
    $stmt_qt->execute();

    $row_qt = $stmt_qt->fetch(PDO::FETCH_ASSOC);
    $qtapprovedate = date_db_2form($row_qt['qt_approvedate']);
    $qtapproveusers = $row_qt['qt_approveusers'];
} else {
    $txt_title = 'เพิ่ม';
    $action = 'add';
}

?>


<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
    <div class="card-header ribbon ribbon-right">
        <div class="ribbon-target bg-danger" style="top: 10px; right: -2px;">2</div>
        <h3 class="card-title">
            <i class="fas fa-list"></i>&nbsp;&nbsp; <?php echo $txt_title; ?>รายละเอียดแจ้งซ่อม เลขที่ :
            <?php echo $row_service['repair_code']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          
            สถานะปัจุบัน :   &nbsp;&nbsp;
            <?php 
                    if($status_typeid == 8 || $status_typeid ==9 ){  ?>
                                                            
                        <span class="badge bg-success">  <?php echo $status_title; ?></span><?php }
                    elseif ( $status_typeid < 7  ) { ?>
                        <span class="badge bg-warning"> <?php echo $status_title; ?></span><?php }
                    elseif ( $status_typeid == 7  ) { ?>
                        <span class="badge bg-danger"> <?php echo $status_title; ?></span><?php }
            ?> 

        </h3>
       
        
        
        <div class="card-toolbar">
            
            <div class="example-tools justify-content-center">
                <a href="dashboard.php?module=<?php echo $module; ?>"
                    class="btn btn-defalse btn-sm font-weight-bold mr-2" title="ย้อนกลับ"><i class="fa fa-chevron-left"
                        title="ย้อนกลับ"></i></a>
            </div>
        </div>
    </div>


    <form class="form" enctype="multipart/form-data">
        <input type="hidden" class="form-control" name="act" id="act" value="<?php echo $action; ?>" />
        <input type="hidden" class="form-control" name="repairid" id="repairid" value="<?php echo $repairid; ?>" />
        <input type="hidden" class="form-control" name="personid" id="personid" value="<?php echo $personid; ?>" />
        <input type="hidden" class="form-control" name="org_id" id="org_id"
            value="<?php echo $row_service['org_id']; ?>" />

            
        <div class="card-body">


            <div class="row">
                <div class="col-lg-7">

                    <?php
  if ($qt_status == '1') {
      ?>

                    <div class="alert alert-custom alert-notice alert-light-danger fade show" role="alert">
                        <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="alert-text">รออนุมัติงานซ่อม</div>
                        <div class="alert-close">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="ki ki-close"></i></span>
                            </button>
                        </div>
                    </div>

                    <?php
  } elseif ($qt_status == '2') { ?>

                    <div class="alert alert-custom alert-notice alert-light-success fade show" role="alert">
                        <div class="alert-icon"><i class="fas fa-check"></i></div>
                        <div class="alert-text">อนุมัติงานซ่อม</div>
                        <div class="alert-close">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="ki ki-close"></i></span>
                            </button>
                        </div>
                    </div>

                    <?php } else { ?>

                    <div class="alert alert-custom alert-notice alert-light-danger fade show" role="alert">
                        <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="alert-text">รอเสนอราคา<?php //echo $row_service['qt_statusname'];?></div>
                        <div class="alert-close">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="ki ki-close"></i></span>
                            </button>
                        </div>
                    </div>

                    <?php }
  ?>

                    <span><i class="far fa-user"></i> ข้อมูลผู้แจ้งซ่อม</span>
                    <hr>

                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>เลขบัตรประชาชน</label>

                            <input type="text" class="form-control" placeholder="เลขบัตรประชาชน 13 หลัก" name="cid"
                                id="cid" maxlength="13" value="<?php echo $row_person['cid']; ?>" disabled />


                        </div>

                        <div class="col-lg-2">
                            <label>คำนำหน้า</label>
                            <select class="form-control " name="prename" id="prename" disabled>
                                <option value="">ระบุ</option>
                                <?php
                          $stmt_user_role = $conn->prepare('SELECT * FROM '.DB_PREFIX.'cprename  ORDER BY id_prename ASC');
                          $stmt_user_role->execute();
                          while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC)) {
                              $id_selected = $row['id_prename'];
                              $title_selected = stripslashes($row['prename']); ?>
                                <option value="<?php echo $id_selected; ?>" <?php if ($row_person['prename'] == $id_selected) {
                                  echo 'selected';
                              } ?>><?php echo $title_selected; ?></option>
                                <?php
                          }
                          ?>
                            </select>

                        </div>
                        <div class="col-lg-3">
                            <label>ชื่อ</label>
                            <input type="text" class="form-control" name="fname" id="fname" placeholder="ชื่อ"
                                value="<?php echo $row_person['fname']; ?>" disabled />

                        </div>
                        <div class="col-lg-4">
                            <label>สกุล</label>
                            <input type="text" class="form-control" name="lname" id="lname" placeholder="สกุล"
                                value="<?php echo $row_person['lname']; ?>" disabled />

                        </div>

                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>หน่วยงาน/บริษัท</label>
                            <input type="text" class="form-control" name="comp_name" id="comp_name"
                                placeholder="หน่วยงาน/บริษัท" value="<?php echo $row_person['comp_name']; ?>"
                                disabled />

                        </div>


                    </div>


                    <span><i class="far fa-bookmark"></i> ข้อมูลแจ้งซ่อม</span>
                    <hr>
                    <div class="form-group row">

                        <div class="col-lg-3">
                            <label>เลขที่แจ้งซ่อม</label>
                            <input type="text" class="form-control" name="repair_code" id="repair_code" placeholder=""
                                value="<?php echo $row_service['repair_code']; ?>" disabled />
                            <span class="form-text text-muted"></span>

                        </div>

                        <div class="col-lg-2">
                            <label>วันที่แจ้งซ่อม</label>
                            <input type="text" class="form-control" name="repairdate" id="repairdate"
                                placeholder="วันที่รับบริการ"
                                value="<?php echo date_db_2form($row_service['repair_date']); ?>"
                                data-date-language="th-th" maxlength="10" disabled />
                            <span class="form-text text-muted"></span>

                        </div>

                        <div class="col-lg-3">
                            <label>ประเภทการซ่อม</label>
                            <select class="form-control " name="repair_type" id="repair_type">
                                <?php

                    $stmt_user_role = $conn->prepare('SELECT * FROM '.DB_PREFIX.'repair_type  ');
                    $stmt_user_role->execute();
                    while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC)) {
                        $id_selected = $row['repair_typeid'];
                        $title_selected = stripslashes($row['repair_typetitle']); ?>
                                <option value="<?php echo $id_selected; ?>" <?php if ($row_service['repair_type'] == $id_selected) {
                            echo 'selected';
                        } ?>><?php echo $title_selected; ?></option>
                                <?php
                    }
                    ?>
                            </select>
                        </div>
                        <!--<div class="col-lg-4">
				<label>หน่วยงาน</label>
				<select class="form-control " name="org_id" id="org_id" disabled>
                    <option value="">ระบุ</option>
                    <?php
                    if ($logged_user_role_id == '1') {
                        $conditions = ' ';
                    } else {
                        $conditions = " AND org_id = '$logged_org_id' ";
                    }
                    $stmt_user_role = $conn->prepare('SELECT * FROM '.DB_PREFIX."org_main WHERE flag = 1  $conditions  ORDER BY org_id ASC");
                    $stmt_user_role->execute();
                    while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC)) {
                        $id_selected = $row['org_id'];
                        $title_selected = stripslashes($row['org_shortname']); ?>
						<option value="<?php echo $id_selected; ?>" <?php if ($row_service['org_id'] == $id_selected) {
                            echo 'selected';
                        } ?>><?php echo $title_selected; ?></option>
						<?php
                    }
                    ?>
				</select>
      </div>-->
                        <div class="col-lg-4">
                            <label>สถานที่ซ่อม</label>
                            <select class="form-control " name="repair_place" id="repair_place">
                                <?php

                    $stmt_user_role = $conn->prepare('SELECT * FROM '.DB_PREFIX.'repair_place  ');
                    $stmt_user_role->execute();
                    while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC)) {
                        $id_selected = $row['place_id'];
                        $title_selected = stripslashes($row['place_title']); ?>
                                <option value="<?php echo $id_selected; ?>" <?php if ($row_service['repair_place'] == $id_selected) {
                            echo 'selected';
                        } ?>><?php echo $title_selected; ?></option>
                                <?php
                    }
                    ?>
                            </select>
                        </div>
                    </div>

                    <!-- <div class="form-group row">
        <div class="col-lg-3">
				<label>รูปแบบการซ่อม</label>
                    <select class="form-control " name="repair_out" id="repair_out" >

            <option value="I" <?php if ($row_service['repair_out'] == 'I') {
                        echo 'selected';
                    } ?>>ซ่อมเอง</option>
            <option value="O" <?php if ($row_service['repair_out'] == 'O') {
                        echo 'selected';
                    } ?>>ส่งซ่อมภายนอก</option>
						
                    </select>
        </div>

        <div class="col-lg-3">
				<label>วันที่ส่งซ่อมภายนอก</label>
				<input type="text" class="form-control"  name="repairoutdate" id="repairoutdate" placeholder="วันที่ส่งซ่อมภายนอก" value="<?php echo date_db_2form($row_service['repair_outdate']); ?>"  data-date-language="th-th" maxlength="10" />
				<span class="form-text text-muted"></span>
				
      </div>
      
      <div class="col-lg-3">
				<label>เลขที่ส่งซ่อมภายนอก</label>
				<input type="text" class="form-control"  name="repair_outcode" id="repair_outcode" placeholder="" value="<?php echo $row_service['repair_outcode']; ?>"  disabled />
				<span class="form-text text-muted"></span>
				
			</div>



    </div> -->
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>ประกันสินค้า</label>
                            <select class="form-control " name="repair_warranty" id="repair_warranty">
                            <option value="" <?php if ($row_service['repair_warranty'] == '') {
                        echo 'selected';
                    } ?>>ระบุ</option>

                                <option value="0" <?php if ($row_service['repair_warranty'] == '0') {
                        echo 'selected';
                    } ?>>ไม่มีประกัน</option>
                                <option value="1" <?php if ($row_service['repair_warranty'] == '1') {
                        echo 'selected';
                    } ?>>มีประกัน</option>

                            </select>
                        </div>
                    </div>

                    <span><i class="fas fa-user-cog"></i> รายละเอียดการแจ้งซ่อม</span>
                    <hr>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>เรื่องแจ้งซ่อม</label>
                            <input type="text" class="form-control" placeholder="" name="repair_title" id="repair_title"
                                value="<?php echo $row_service['repair_title']; ?>" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>คลังอุปกรณ์</label>
                            <select class="form-control " name="eq_id" id="eq_id">
                                <option value="">ระบุ</option>

                                <?php
              if ($logged_user_role_id == '1') {
                  $conditions = ' ';
              } else {
                  $conditions = " AND org_id = '$logged_org_id' ";
              }
              $stmt_user_role = $conn->prepare('SELECT * FROM '.DB_PREFIX."equipment_main WHERE flag != 0  $conditions  ");
              $stmt_user_role->execute();
              while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC)) {
                  $id_selected = $row['oid'];
                  $title_selected = stripslashes($row['eq_names']); ?>
                                <option value="<?php echo $id_selected; ?>" <?php if ($row_service['eq_id'] == $id_selected) {
                      echo 'selected';
                  } ?>><?php echo $title_selected; ?></option>
                                <?php
              }
              ?>
                                <option value="0" <?php if ($row_service['eq_id'] == 0) {
                  echo 'selected';
              } ?>>อุปกรณ์อื่น ๆ ระบุ</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label>ยี่ห้ออุปกรณ์</label>
                            <input type="text" class="form-control" placeholder="" name="eq_name" id="eq_name"
                                value="<?php echo $row_service['eq_name']; ?>" />
                        </div>
                        <div class="col-lg-4">
                            <label>Model/SN</label>
                            <input type="text" class="form-control" placeholder="" name="eq_code" id="eq_code"
                                value="<?php echo $row_service['eq_code']; ?>" />
                        </div>

                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>รายละเอียด/อาการแจ้งซ่อม</label>
                            <textarea rows="" class="form-control editor" name="repair_desc"
                                id="repair_desc"><?php echo $row_service['repair_desc']; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>อุปกรณ์ที่นำมาด้วย</label>
                            <input type="text" class="form-control" placeholder="" name="eq_others" id="eq_others"
                                value="<?php echo $row_service['eq_others']; ?>" />
                        </div>
                    </div>

                    <span class=""><i class="fas fa-camera"></i> อัพโหลดรูปถ่ายอุปกรณ์แจ้งซ่อม</span>
                    <hr>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>เลือกรูปถ่าย</label>
                            <input type="file" name="files[]" id="filer_example2" class="form-control "
                                multiple="multiple">
                            <span class="form-text text-muted">.jpg .png เท่านั้น</span>
                        </div>
                    </div>
                    
                    <span class=""><i class="far fa-file-pdf"></i> อัพโหลดเอกสารเพิ่มเติม</span>
                    <hr>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>เลือกไฟล์เอกสาร</label>
                            <input type="file" name="doc[]" id="filer_example1" class="form-control "
                                multiple="multiple">
                            <span class="form-text text-muted">.jpg .png .pdf .doc .docx เท่านั้น</span>
                        </div>
                    </div>


                </div>
                <!--col-->


                <div class="col-lg-5 border-x-0 border-x-md border-y border-y-md-0">
                    <div class="form-group row">
                        <!-- <div class="col-lg-4">
            <label>วันที่อนุมัติซ่อม</label>
            <input type="text" class="form-control"  name="approvedate" id="approvedate" placeholder="วันที่อนุมัติซ่อม" value="<?php echo date_db_2form($row_service['approve_date']); ?>"  data-date-language="th-th" maxlength="10" />
            </div>
            <div class="col-lg-8">
            <label>ผู้อนุมัติซ่อม</label>
            <input type="text" class="form-control"  name="approve_username" id="approve_username" placeholder="" value="<?php echo $row_service['approve_username']; ?>"   />
            </div> -->


                        <div class="col-lg-4">
                            <label>วันที่อนุมัติซ่อม</label>
                            <input type="text" class="form-control" name="qtapprovedate" id="qtapprovedate"
                                data-date-language="th-th" maxlength="10" placeholder=""
                                value="<?php echo $qtapprovedate; ?>" disabled />
                        </div>

                        <div class="col-lg-8">
                            <label>ผู้อนุมัติซ่อม</label>
                            <select class="form-control " name="qtapproveusers" id="qtapproveusers" disabled>

                                <option value="">ระบุ</option>
                                <?php

                                                            $stmt_user_role = $conn->prepare('SELECT s.* FROM '.DB_PREFIX."users s 
                                                            WHERE s.org_id = '$logged_org_id' AND s.active = '1'  ");
                                                            $stmt_user_role->execute();
                                                            while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC)) {
                                                                $id_selected_ap = $row['user_id'];
                                                                $title_selected_ap = stripslashes($row['name']); ?>
                                <option value="<?php echo $id_selected_ap; ?>" <?php if ($qtapproveusers == $id_selected_ap) {
                                                                    echo 'selected';
                                                                } ?>><?php echo $title_selected_ap; ?></option>
                                <?php
                                                            }
                                                            ?>

                            </select>

                        </div>

                    </div>

                    <span><i class="fas fa-dollar-sign"></i> ค่าซ่อม
                        <!--<a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalAddCost"><i class="far fa-plus-square"></i> บันทึกค่าซ่อม</a>-->
                    </span>
                    <hr>
                    <div id="cost_detail"></div>

                    <span><i class="fas fa-pen-square text-warning"></i> บันทึกรายละเอียดการซ่อม </span>
                    <hr>
                    

                    <span><i class="fas fa-cog"></i> อะไหล่ <a href="#" class="btn btn-sm btn-primary"
                            data-toggle="modal" data-target="#modalAddSpare"><i class="far fa-plus-square"></i>
                            เลือกอะไหล่</a></span>
                    <hr>
                    <div id="spare_detail"></div>

                    <span><i class="fas fa-list"></i> สถานะการซ่อม <a href="#" class="btn btn-sm btn-primary"
                            data-toggle="modal" data-target="#modalAddStatus"><i class="far fa-plus-square"></i>
                            บันทึกสถานะการซ่อม</a></span>
                    <hr>
                    <div id="status_detail"></div>

                    <span id="logistic"><i class="fas fa-truck"></i> การขนส่ง <a href="#" class="btn btn-sm btn-primary"
                            data-toggle="modal" data-target="#modalAddLogistic"><i class="far fa-plus-square"></i>
                            บันทึกการขนส่ง</a></span>
                    <hr id="logistic_detail_hr">
                    <div id="logistic_detail"></div>   

                    <span id="jobout"><i class="fas fa-truck"></i> เลขที่ใบส่งซ่อมภายนอก <a href="#" class="btn btn-sm btn-primary"
                            data-toggle="modal" data-target="#modalAddjobout"><i class="far fa-plus-square"></i>
                            บันทึกเลขที่ส่งซ่อม</a></span>
                    <hr id="่job">
                    <div id="jobout_detail"></div>   

                    <span><i class="fas fa-user-check"></i> ข้อมูลการรับคืน <a target="_blank" href="././pdfprint/return_in/rpt-return-pdf.php?personid=<?php echo $personid_enc; ?>&repairid=<?php echo $repairid_enc; ?>&act=<?php echo base64_encode('view'); ?>" class="btn btn-sm btn-primary"
                            ><i class="far fa-plus-square"></i>
                            พิมพ์ใบรับคืน</a></span>
                    <hr></span>

                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label>วันที่รับคืน</label>
                            <input type="text" class="form-control" name="returndate" id="returndate"
                                placeholder="วันที่รับคืน"
                                value="<?php echo date_db_2form($row_service['return_date']); ?>"
                                data-date-language="th-th" maxlength="10" />
                        </div>
                        <div class="col-lg-8">
                            <label>ผู้รับคืน</label>
                            <input type="text" class="form-control" name="return_username" id="return_username"
                                placeholder="ผู้รับคืน" value="<?php echo $row_service['return_username']; ?>" />
                        </div>

                    </div>


                   
                    <span><i class="far fa-file-pdf"></i> เอกสารเพิ่มเติม </span>
                    <hr>

                    <div class="form-group row">

                    <?php

                    $sql_files = 'SELECT * FROM '.DB_PREFIX."repair_document WHERE repair_id = '$repairid' AND file_status = '1' ORDER BY file_id ASC ";
                    $stmt_files = $conn->prepare($sql_files);
                    $stmt_files->execute();

                    while ($row_files = $stmt_files->fetch(PDO::FETCH_ASSOC)) {
                        $file_id = $row_files['file_id'];
                        $file_name = $row_files['file_name']; 
                        $file_oldname = $row_files['file_oldname']; 
                        ?>

                    <div class="col-lg-12">

                    <!--begin::Item-->
                    <div class="mb-6">
                                <!--begin::Content-->
                                <div class="d-flex align-items-center flex-grow-1">
                                    <!--begin::Checkbox-->
                                    <label class="mr-4">
                                    <a href="uploads/repair/<?php echo $file_name; ?>" target="_blank">
                                        <span class="label label-lg label-light-primary label-inline font-weight-bold py-2"><i class="fas fa-file-invoice"></i></span>
                                        </a>
                                        <span></span>
                                    </label>
                                    <!--end::Checkbox-->

                                    <!--begin::Section-->
                                    <div class="d-flex flex-wrap align-items-center justify-content-between w-100">
                                        <!--begin::Info-->
                                        <div class="d-flex flex-column align-items-cente py-2 w-75">
                                            <!--begin::Title-->
                                            <a href="uploads/repair/<?php echo $file_name; ?>" target="_blank" class="text-dark-75 font-weight-bold text-hover-primary font-size-lg mb-1">
                                                <?php echo $file_oldname;?>
                                            </a>
                                            <!--end::Title-->

                                            <!--begin::Data-->
                                        
                                            <!--end::Data-->
                                        </div>
                                        <!--end::Info-->

                                        <!--begin::Label-->
                                        <a href="#" onclick='confirm_delete_file(<?php echo $file_id; ?>)' title="ลบไฟล์">
                                        <span class="label label-lg label-light-danger label-inline font-weight-bold py-4"><i class="fas fa-trash text-danger"></i></span>
                                        </a>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Section-->
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Item-->



                    
                    </div>

                    <?php
                    }
                    ?>


                    </div>


                    <span><i class="fas fa-camera"></i> รูปถ่ายอุปกรณ์แจ้งซ่อม </span>
                    <hr>

                    <div class="form-group row">

                        <?php

                          $sql_files = 'SELECT * FROM '.DB_PREFIX."repair_files WHERE repair_id = '$repairid' AND file_status = '1' ORDER BY file_id ASC ";
                          $stmt_files = $conn->prepare($sql_files);
                          $stmt_files->execute();

                          while ($row_files = $stmt_files->fetch(PDO::FETCH_ASSOC)) {
                              $file_id = $row_files['file_id'];
                              $file_name = $row_files['file_name']; ?>

                        <div class="col-lg-6">
                        
                            <div class="symbol symbol-150 mr-3">
                            <a href="uploads/repair/<?php echo $file_name; ?>" data-toggle="lightbox">
                                <img src="uploads/repair/<?php echo $file_name; ?>" alt="image" class="img-fluid"/>
                          </a>
                                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                    data-action="remove" data-toggle="tooltip" title="ลบรูปภาพ">
                                    <a href="#" onclick='confirm_delete(<?php echo $file_id; ?>)'><i
                                            class="ki ki-bold-close icon-xs text-muted"></i></a>
                                </span>
                             </a>
                            </div>
                    
                        </div>

                        <?php
                          }
                          ?>
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
                            class="fa fa-chevron-left" title="ย้อนกลับ"></i></button>
                </div>
                <div class="col-lg-6 text-right">
                    <!--<button type="reset" class="btn btn-danger">Delete</button>-->
                </div>
            </div>
        </div>
    </form>

</div>
<!--end::Card-->


<!--begin::Modal-->
<div class="modal fade" id="modalAddSpare" tabindex="-1" role="dialog" aria-labelledby="modalAddSpare"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="far fa-plus-square"></i> เลือกอะไหล่ที่ใช้ไป
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">

                <form class="form" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" class="form-control" name="act" id="act" value="add" />
                    <input type="hidden" class="form-control" name="repairid" id="repairid"
                        value="<?php echo $repairid; ?>" />
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label>อะไหล่</label>
                            <select class="form-control " name="spare_id" id="spare_id">
                                <option value="">ระบุ</option>
                                <?php
                                                            if ($logged_user_role_id == '1') {
                                                                $conditions = ' ';
                                                            } else {
                                                                $conditions = " AND s.org_id = '$logged_org_id' ";
                                                            }
                                                            $stmt_user_role = $conn->prepare('SELECT s.* FROM '.DB_PREFIX."spare_main s 
                                                            
                                                            WHERE s.flag = 1  $conditions  ORDER BY s.spare_id ASC");
                                                            $stmt_user_role->execute();
                                                            while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC)) {
                                                                $id_selected = $row['spare_id'];
                                                                $title_selected = stripslashes($row['spare_name']); ?>
                                <option value="<?php echo $id_selected; ?>"><?php echo $title_selected; ?></option>
                                <?php
                                                            }
                                                            ?>
                                <option value="0">อื่น ๆ</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label>จำนวน</label>
                            <input type="text" class="form-control" name="spare_quantity" id="spare_quantity"
                                placeholder="" value="" />
                        </div>

                        <div class="col-lg-2">
                            <label>หน่วย</label>
                            <select class="form-control " name="spare_unit" id="spare_unit">

                                <option value="">ระบุ</option>

                            </select>

                        </div>

                        <div class="col-lg-2">
                            <label>ราคา(บาท)</label>
                            <input type="text" class="form-control" name="spare_price" id="spare_price" placeholder=""
                                value="0" />
                        </div>



                    </div>

                    <div class="form-group row" id="spare_oth">
                        <div class="col-lg-6">
                            <label>ระบุอะไหล่อื่น ๆ</label>
                            <input type="text" class="form-control" name="spare_other" id="spare_other"
                                placeholder="ระบุอะไหล่อื่น ๆ" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>หมายเหตุ</label>
                            <textarea class="form-control" name="spare_desc" id="spare_desc"></textarea>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-success mr-2" id="btnAddSpare"><i
                                    class="far fa-save"></i> บันทึก</button>
                        </div>

                    </div>



            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal"><i
                        class="far fa-times-circle"></i> ปิด</button>
            </div>
        </div>
    </div>
</div>
</form>
<!--end::Modal-->


<!--begin::Modal-->
<div class="modal fade" id="modalAddStatus" tabindex="-1" role="dialog" aria-labelledby="modalAddStatus"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="far fa-plus-square"></i> สถานะการซ่อม</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">

                <form class="form" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" class="form-control" name="repairid" id="repairid"
                        value="<?php echo $repairid; ?>" />
                    <div class="form-group row">
                        <div class="col-lg-2">
                            <label>วันที่ทำรายการ</label>
                            <input type="text" class="form-control" name="statusdate" id="statusdate"
                                data-date-language="th-th" maxlength="10" placeholder="" value="<?php echo date('d').'/'.date('m').'/'.(date('Y')+543);?>" />
                        </div>
                        <div class="col-lg-4">
                            <label>สถานะการซ่อม</label>
                            <select class="form-control " name="status_id" id="status_id">
                                <option value="">ระบุ</option>
                                <?php
                                                        $stmt_user_role = $conn->prepare('SELECT s.* FROM '.DB_PREFIX.'repair_status_type s ');
                                                        $stmt_user_role->execute();
                                                        while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC)) {
                                                            $id_selected = $row['status_typeid'];
                                                            $title_selected = stripslashes($row['status_title']); ?>
                                <option value="<?php echo $id_selected; ?>"><?php echo $title_selected; ?></option>
                                <?php
                                                        }
                                                        ?>
                            </select>
                        </div>




                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>ช่าง</label>
                            <div class="row">
                                <?php

                                              $orgid = $row_service['org_id'];
                                                  if ($logged_user_role_id == '1') {
                                                      $conditions = " AND org_id = '$orgid' ";
                                                  } else {
                                                      $conditions = " AND org_id = '$orgid' ";
                                                  }

                                                $stmt_user_role = $conn->prepare('SELECT * FROM '.DB_PREFIX."staff_main s  WHERE s.flag ='1'  $conditions  ");
                                                $stmt_user_role->execute();
                                                while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC)) {
                                                    $role_id_selected = $row['oid'];
                                                    $role_title_selected = ' '.stripslashes($row['sfname']).' '.stripslashes($row['slname']).' ('.stripslashes($row['nickname']).')'; ?>

                                <div class="col-lg-3">


                                    <label class="checkbox checkbox-lg">
                                        <input type="checkbox" name="staffs[]"
                                            value="<?php echo $role_id_selected; ?>" />
                                        <span></span>
                                        <?php echo ' '.$role_title_selected; ?>
                                    </label>
                                </div>


                                <?php
                                                }
                                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>รายละเอียด</label>
                            <textarea class="form-control editor" name="status_desc" id="status_desc"></textarea>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-success mr-2" id="btnAddStatus"><i
                                    class="far fa-save"></i> บันทึก</button>
                        </div>

                    </div>



            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal"><i
                        class="far fa-times-circle"></i> ปิด</button>
            </div>
        </div>
    </div>
</div>
</form>
<!--end::Modal-->


<!--begin::Modal-->
<div class="modal fade" id="modalAddCost" tabindex="-1" role="dialog" aria-labelledby="modalAddCost" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="far fa-plus-square"></i> บันทึกค่าซ่อม</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">

                <form class="form" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" class="form-control" name="repairid" id="repairid"
                        value="<?php echo $repairid; ?>" />
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label>ค่าซ่อม(บาท)</label>
                            <input type="number" class="form-control" name="cost" id="cost" placeholder="" value="" />
                        </div>

                    </div>


                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>รายละเอียด</label>
                            <textarea class="form-control" name="cost_note" id="cost_note"></textarea>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-success mr-2" id="btnAddCost"><i
                                    class="far fa-save"></i> บันทึก</button>
                        </div>

                    </div>



            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal"><i
                        class="far fa-times-circle"></i> ปิด</button>
            </div>
        </div>
    </div>
</div>
</form>
<!--end::Modal-->
<!--begin::Modal ขนส่ง-->
<div class="modal fade" id="modalAddLogistic" tabindex="-1" role="dialog" aria-labelledby="modalAddLogistic" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="far fa-plus-square"></i> สถานะการขนส่ง</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form class="form" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" class="form-control" name="repairid" id="repairid"
                        value="<?php echo $repairid; ?>" />
                    <div class="form-group row">
                        <div class="col-lg-2">
                            <label>วันที่ทำรายการ</label>
                            <input type="text" class="form-control" name="logisticdate" id="logisticdate" data-date-language="th-th" maxlength="10" placeholder="" value="<?php echo date('d').'/'.date('m').'/'.(date('Y')+543);?>" />
                        </div>

                        <div class="col-lg-4">
                            <label>สถานะการขนส่ง</label>
                            <select class="form-control " name="logisticstatus" id="logisticstatus">
                                <option value="">ระบุ</option>
                                <?php
                                    $stmt_user_role = $conn->prepare('SELECT s.* FROM '.DB_PREFIX.'repair_logistic_type s ');
                                    $stmt_user_role->execute();
                                    while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC)) {
                                        $id_selected = $row['logistic_typeid'];
                                        $title_selected = stripslashes($row['logistic_title']); ?>
                                <option value="<?php echo $id_selected; ?>"><?php echo $title_selected; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label>เลขที่ส่ง</label>
                            <input type="text" class="form-control" name="logisticno" id="logisticno" placeholder=""
                                value="" />
                        </div>
                        <div class="col-lg-4">
                            <label>ขนส่งโดย</label>

                            <select class="form-control " name="logisticby" id="logisticby">
                                <option value="">ระบุ</option>
                                <?php
                                    $stmt_user_role = $conn->prepare('SELECT s.* FROM '.DB_PREFIX.'repair_logistic_transport s ');
                                    $stmt_user_role->execute();
                                    while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC)) {
                                        $id_selected = $row['logistic_transport_id'];
                                        $title_selected = stripslashes($row['logistic_transport_title']); ?>
                                <option value="<?php echo $id_selected; ?>"><?php echo $title_selected; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>รายละเอียด</label>
                            <textarea class="form-control editor" name="logistic_desc" id="logistic_desc"></textarea>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-success mr-2" id="btnAddLogistic"><i
                                    class="far fa-save"></i> บันทึก</button>
                        </div>

                    </div>

            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal"><i
                        class="far fa-times-circle"></i> ปิด</button>
            </div>
        </div>
    </div>
</div>
</form>
<!--end::Modal-->
<!--begin::Modal ส่งซ่อมภายนอก-->
<div class="modal fade" id="modalAddjobout" tabindex="-1" role="dialog" aria-labelledby="modalAddjobout" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="far fa-plus-square"></i> เลขที่ส่งซ่อมภายนอก</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form class="form" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" class="form-control" name="repairid" id="repairid"
                        value="<?php echo $repairid; ?>" />
                    <div class="form-group row">
                        <div class="col-lg-2">
                            <label>วันที่ทำรายการ</label>
                            <input type="text" class="form-control" name="job_date" id="job_date" data-date-language="th-th" maxlength="10" placeholder="" value="<?php echo date('d').'/'.date('m').'/'.(date('Y')+543);?>" />
                        </div>

                        <div class="col-lg-4">
                            <label>สถานที่ส่งซ่อม</label>
                            <input type="text" class="form-control" name="job_name" id="job_name" placeholder=""
                                value="" />
                        </div>
                        <div class="col-lg-4">
                            <label>เลขที่ส่งซ่อม</label>
                            <input type="text" class="form-control" name="jobticket_id" id="jobticket_id" placeholder=""
                                value="" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>รายละเอียด</label>
                            <textarea class="form-control editor" name="job_desc" id="job_desc"></textarea>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-success mr-2" id="btnAddjobout"><i
                                    class="far fa-save"></i> บันทึก</button>
                        </div>

                    </div>

            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal"><i
                        class="far fa-times-circle"></i> ปิด</button>
            </div>
        </div>
    </div>
</div>
</form>
<!--end::Modal-->
<script>
$(document).on('click', '[data-toggle="lightbox"]', function(event) {
  event.preventDefault();
  $(this).ekkoLightbox();
});
$(document).ready(function() {
    'use strict';
    check_data_repairout();
    loaddata_spare_data();
    loaddata_status_data();
    loaddata_cost_data();
    loaddata_logistic_data();
    loaddata_jobout();

    $('#spare_oth').hide();

    var eq_id = $("#eq_id").val();
    if (eq_id == '0') {

    } else if (eq_id == '') {

    } else {
        $("#eq_name").attr('disabled', 'disabled');
        $("#eq_code").attr('disabled', 'disabled');
    }

    $('#eq_id').change(function(e) {
        e.preventDefault();
        var eq_id = $("#eq_id").val();

        if (eq_id == '0') {
            $('#eq_name').prop('disabled', false);
            $('#eq_code').prop('disabled', false);
        } else {
            $("#eq_name").attr('disabled', false);
            $("#eq_code").attr('disabled', false);
            //$("#eq_typeother").val('');
        }

    }); //


});

//Example 2
$('#filer_example2').filer({
    limit: 10,
    maxSize: 10,
    //extensions: ['jpg', 'jpeg', 'png', 'gif','pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar'],
    extensions: ['jpg', 'jpeg', 'png', 'gif'],
    changeInput: true,
    showThumbs: true,
    addMore: true
});


$('#repairoutdate').datepicker({
    autoclose: true
});

$('#repairdate').datepicker({
    autoclose: true
});

$('#approvedate').datepicker({
    autoclose: true
});

$('#statusdate').datepicker({
    autoclose: true
});

$('#returndate').datepicker({
    autoclose: true
});
$('#logisticdate').datepicker({
    autoclose: true
});
$('#joboutdate').datepicker({
    autoclose: true
});

$('#spare_id').change(function(e) {
    e.preventDefault();
    var spare_id = $("#spare_id").val();
    //alert(spare_id);
    if (spare_id === '99') {
        $('#spare_oth').show();
    } else {
        $('#spare_oth').val('').hide();
    }
    // if (spare_id == '99') {
    //     $('#spare_oth').prop('disabled', false);
    // } else {
    //     $("#spare_oth").attr('disabled', 'disabled');
    // }
    $.ajax({
        type: "POST",
        url: "core/spare/fn-spare-unit-other.php",
        data: {
            spare_id: spare_id
        },
        success: function(data) {
            $("#spare_unit").html(data);

        } // success
    });
}); //  



function loaddata_spare_data() {
    var repairid = $("#repairid").val();
    var personid = $("#personid").val();
    $.ajax({
        type: "POST",
        url: "views/repair/repair-add-data-spare.php",
        //dataType: "json",
        data: {
            repairid: repairid,
            personid: personid
        },
        success: function(data) {
            $("#spare_detail").empty(); //add preload
            $("#spare_detail").append(data);

        } // success
    });
}

function loaddata_status_data() {
    var repairid = $("#repairid").val();
    var personid = $("#personid").val();
    $.ajax({
        type: "POST",
        url: "views/repair/repair-add-data-status.php",
        //dataType: "json",
        data: {
            repairid: repairid,
            personid: personid
        },
        success: function(data) {
            $("#status_detail").empty(); //add preload
            $("#status_detail").append(data);
        } // success
    });
}

function loaddata_logistic_data() {
    var repairid = $("#repairid").val();
    var personid = $("#personid").val();
    
    $.ajax({
        type: "POST",
        url: "views/repairout/repairout-add-data-logistic.php",
        //dataType: "json",
        data: {
            repairid: repairid,
            personid: personid
        },
        success: function(data) {
            $("#logistic_detail").empty(); //add preload
            $("#logistic_detail").append(data);

        } // success
    });
}

function loaddata_cost_data() {
    var repairid = $("#repairid").val();
    var personid = $("#personid").val();
    $.ajax({
        type: "POST",
        url: "views/repair/repair-add-data-cost.php",
        //dataType: "json",
        data: {
            repairid: repairid,
            personid: personid
        },
        success: function(data) {
            $("#cost_detail").empty(); //add preload
            $("#cost_detail").append(data);
        } // success
    });
}
function loaddata_jobout() {
    var repairid = $("#repairid").val();
    var personid = $("#personid").val();
    $.ajax({
        type: "POST",
        url: "views/repair/repair-add-jobout.php",
        //dataType: "json",
        data: {
            repairid: repairid,
            personid: personid
        },
        success: function(data) {
            $("#jobout_detail").empty(); //add preload
            $("#jobout_detail").append(data);
        } // success
    });
}
function check_data_repairout() {
    var repairid = $("#repairid").val();
    $.ajax({
        type: "POST",
        url: "core/repair/check_data_repairout.php",
        //dataType: "json",
        data: {
            repairid: repairid
        },
        success: function(data) {
            if(data == 0){
                $("#logistic").hide();
                $("#logistic_detail").hide();
                $("#logistic_detail_hr").hide();
                $("#jobout").hide();
                $("#job").hide();
                $("#jobout_detail").hide();
            }else{
                $("#logistic").show();
                $("#logistic_detail").show();
                $("#logistic_detail_hr").show();
                $("#job").show();
                $("#jobout").show();
                $("#jobout_detail").show();
            }
        } // success
    });
}

</script>


<script>
function confirm_delete(id) {
    Swal.fire({
        title: 'แน่ใจนะ?',
        text: "ต้องการลบรูปภาพ",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ใช่, ต้องการลบรูปภาพ!'
    }).then((result) => {
        if (result.value) { //Yes
            $.post("core/repair/repair-photo-del.php", {
                id: id
            }, function(result) {
                //  $("test").html(result);
                // console.log(result.code);
                location.reload();
            });
        }
    })
}



$('#btnSave').click(function(e) {
    e.preventDefault();
    if ($('#repair_type').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุประเภทรับบริการ',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#repair_title').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุเรื่องแจ้งซ่อม',
            showConfirmButton: false,
            timer: 1000
        });
    } else {

        var data = new FormData(this.form);
        $.ajax({
            type: "POST",
            url: "core/repair/repair-add-data.php",
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
            },error: function (jqXHR, exception) {
                console.log(jqXHR);
                // Your error handling logic here..
            } // success
        });

    }

}); //  click




$('#btnAddSpare').click(function(e) {
    e.preventDefault();
    if ($('#repairid').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาทำรายการ',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#spare_id').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุอะไหล่/อุปกรณ์',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#spare_quantity').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุจำนวน',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#spare_unit').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุหน่วย',
            showConfirmButton: false,
            timer: 1000
        });
    } else {

        var data = new FormData(this.form);
        $.ajax({
            type: "POST",
            url: "core/repair/repair-add-data-spare.php",
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
                            $('#spare_id').val('');
                            $('#spare_quantity').val('');
                            $('#spare_unit').val('');
                            $('#spare_price').val('0');
                            $('#spare_desc').val('');
                            loaddata_spare_data();

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

// del spare
function delSpareData(id) {
    Swal.fire({
        title: 'แน่ใจนะ?',
        text: "ต้องการยกเลิกรายการ !",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ใช่, ต้องการยกเลิกรายการ!'
    }).then((result) => {
        if (result.value) { //Yes
            $.post("core/repair/repair-del-data-spare.php", {
                id: id
            }, function(result) {
                loaddata_spare_data();
            });
        }
    })
}

// del jobout
function deljoboutData(id) {
    Swal.fire({
        title: 'แน่ใจนะ?',
        text: "ต้องการยกเลิกรายการ !",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ใช่, ต้องการยกเลิกรายการ!'
    }).then((result) => {
        if (result.value) { //Yes
            $.post("core/repairout/repairout-del-jobout.php", {
                id: id
            }, function(result) {
                loaddata_jobout();
            });
        }
    })
}

$('#btnAddStatus').click(function(e) {
    e.preventDefault();
    if ($('#repairid').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาทำรายการ',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#statusdate').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุวันที่',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#status_id').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุสถานะ',
            showConfirmButton: false,
            timer: 1000
        });
    } else {
        var data = new FormData(this.form);
        $.ajax({
            type: "POST",
            url: "core/repair/repair-add-data-status.php",
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
                            $('#status_id').val('');
                            $('#statusdate').val('');
                            $('#staff_id').val('');
                            $('#status_desc').val('');
                            loaddata_status_data();
                        });
                        check_data_repairout();
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





$('#btnAddCost').click(function(e) {
    e.preventDefault();
    if ($('#repairid').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาทำรายการ',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#cost').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุค่าซ่อม',
            showConfirmButton: false,
            timer: 1000
        });
    } else {

        var data = new FormData(this.form);
        $.ajax({
            type: "POST",
            url: "core/repair/repair-add-data-cost.php",
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
                            $('#cost').val('');
                            loaddata_cost_data();

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
                } else if (data.code == "401") {
                    //swal("ไม่สามารถบันทึกข้อมูลได้ กรุณาลองอีกครั้ง")
                    Swal.fire({
                            icon: 'error',
                            title: 'ไม่สามารถบันทึกข้อมูลซ้ำได้',
                            text: 'กรุณาแก้ไขค่าซ่อม'
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




// del status
function delStatusData(id) {
    Swal.fire({
        title: 'แน่ใจนะ?',
        text: "ต้องการยกเลิกรายการ !",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ใช่, ต้องการยกเลิกรายการ!'
    }).then((result) => {
        if (result.value) { //Yes
            $.post("core/repair/repair-del-data-status.php", {
                id: id
            }, function(result) {
                location.reload();
                // loaddata_status_data();
            });
        }
    })
}
// add logistic
$('#btnAddLogistic').click(function(e) {
    e.preventDefault();
    if ($('#repairid').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาทำรายการ',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#logisticdate').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุวันที่',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#logisticstatus').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุสถานะ',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#logisticno').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุเลขส่ง',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#logisticby').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุส่งโดย',
            showConfirmButton: false,
            timer: 1000
        });
    } else {

        var data = new FormData(this.form);
        $.ajax({
            type: "POST",
            url: "core/repairout/repairout-add-data-logistic.php",
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
                            $('#logisticstatus').val('');
                            $('#logisticdate').val('');
                            $('#logisticno').val('');
                            $('#logisticby').val('');
                            $('#logistic_desc').val('');
                            loaddata_logistic_data();

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

// add jobout
$('#btnAddjobout').click(function(e) {
    e.preventDefault();
    if ($('#repairid').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาทำรายการ',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#jobticket_id').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุเลขที่ส่งซ่อม',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#job_date').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุวันที่',
            showConfirmButton: false,
            timer: 1000
        });
    } else if ($('#job_name').val().length == "") {
        Swal.fire({
            icon: 'error',
            title: 'กรุณาระบุสถานที่ส่งซ่อม',
            showConfirmButton: false,
            timer: 1000
        });
    } else {
        var data = new FormData(this.form);
        $.ajax({
            type: "POST",
            url: "core/repairout/repairout-add-jobout.php",
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
                            $('#job_name').val('');
                            $('#jobticket_id').val('');
                            $('#job_desc').val('');
                            loaddata_jobout();
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

function confirm_delete_file(id) {
    console.log(id);
    Swal.fire({
        title: 'แน่ใจนะ?',
        text: "ต้องการลบรูปภาพ",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ใช่, ต้องการลบรูปภาพ!'
    }).then((result) => {
        if (result.value) { //Yes
            $.post("core/repairout/repairout-docx-del.php", {
                id: id
            }, function(result) {
                //  $("test").html(result);
                // console.log(result.code);
                location.reload();
            });
        }
    })
}


$('.editor').trumbowyg({
    removeformatPasted: true,
    lang: 'th',
    autogrow: true,
    btnsDef: {
        // Create a new dropdown
        image: {
            dropdown: ['insertImage', 'noembed'],
            ico: 'insertImage'
        }
    },
    btns: [
        ['viewHTML'],
        ['undo', 'redo'],
        ['formatting'],
        'btnGrp-semantic',
        ['link'],
        ['table'],
        ['image'],
        'btnGrp-justify',
        'btnGrp-lists',
        ['horizontalRule'],
        ['removeformat'],
        ['fullscreen'],
        ['foreColor', 'backColor'],
        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
    ],
    plugins: {
        upload: {
            serverPath: '<?php echo ADMIN_URL; ?>/assets/plugins/trumbowyg/texteditor-upload.php',
            fileFieldName: 'image'
        }
    },
    table: {
        // Some table plugin options, see details below
    }

});


var defaultOptions = {
    rows: 8,
    columns: 8,
    styler: 'table'
};

$.extend(true, $.trumbowyg, {
    langs: {
        en: {
            table: 'Insert table',
            tableAddRow: 'Add row',
            tableAddColumn: 'Add column',
            tableDeleteRow: 'Delete row',
            tableDeleteColumn: 'Delete column',
            tableDestroy: 'Delete table',
            error: 'Error'
        }

    },

    plugins: {
        table: {
            init: function(t) {
                t.o.plugins.table = $.extend(true, {}, defaultOptions, t.o.plugins.table || {});

                var buildButtonDef = {
                    fn: function() {
                        t.saveRange();

                        var btnName = 'table';

                        var dropdownPrefix = t.o.prefix + 'dropdown',
                            dropdownOptions = { // the dropdown
                                class: dropdownPrefix + '-' + btnName + ' ' + dropdownPrefix + ' ' +
                                    t.o.prefix + 'fixed-top'
                            };
                        dropdownOptions['data-' + dropdownPrefix] = btnName;
                        var $dropdown = $('<div/>', dropdownOptions);

                        if (t.$box.find("." + dropdownPrefix + "-" + btnName).length === 0) {
                            t.$box.append($dropdown.hide());
                        } else {
                            $dropdown = t.$box.find("." + dropdownPrefix + "-" + btnName);
                        }

                        // clear dropdown
                        $dropdown.html('');

                        // when active table show AddRow / AddColumn
                        if (t.$box.find("." + t.o.prefix + "table-button").hasClass(t.o.prefix +
                                'active-button')) {
                            $dropdown.append(t.buildSubBtn('tableAddRow'));
                            $dropdown.append(t.buildSubBtn('tableAddColumn'));
                            $dropdown.append(t.buildSubBtn('tableDeleteRow'));
                            $dropdown.append(t.buildSubBtn('tableDeleteColumn'));
                            $dropdown.append(t.buildSubBtn('tableDestroy'));
                        } else {
                            var tableSelect = $('<table></table>');
                            for (var i = 0; i < t.o.plugins.table.rows; i += 1) {
                                var row = $('<tr></tr>').appendTo(tableSelect);
                                for (var j = 0; j < t.o.plugins.table.columns; j += 1) {
                                    $('<td></td>').appendTo(row);
                                }
                            }
                            tableSelect.find('td').on('mouseover', tableAnimate);
                            tableSelect.find('td').on('mousedown', tableBuild);

                            $dropdown.append(tableSelect);
                            $dropdown.append($('<center>1x1</center>'));
                        }

                        t.dropdown(btnName);
                    }
                };

                var tableAnimate = function(column_event) {
                    var column = $(column_event.target),
                        table = column.parents('table'),
                        colIndex = this.cellIndex,
                        rowIndex = this.parentNode.rowIndex;

                    // reset all columns
                    table.find('td').removeClass('active');

                    for (var i = 0; i <= rowIndex; i += 1) {
                        for (var j = 0; j <= colIndex; j += 1) {
                            table.find("tr:nth-of-type(" + (i + 1) + ")").find("td:nth-of-type(" + (j +
                                1) + ")").addClass('active');
                        }
                    }

                    // set label
                    table.next('center').html((colIndex + 1) + "x" + (rowIndex + 1));
                };

                var tableBuild = function(column_event) {
                    t.saveRange();

                    var tabler = $('<table></table>');
                    if (t.o.plugins.table.styler) {
                        tabler.attr('class', t.o.plugins.table.styler);
                    }

                    var column = $(column_event.target),
                        colIndex = this.cellIndex,
                        rowIndex = this.parentNode.rowIndex;

                    for (var i = 0; i <= rowIndex; i += 1) {
                        var row = $('<tr></tr>').appendTo(tabler);
                        for (var j = 0; j <= colIndex; j += 1) {
                            $('<td></td>').appendTo(row);
                        }
                    }

                    t.range.deleteContents();
                    t.range.insertNode(tabler[0]);
                    t.$c.trigger('tbwchange');
                };

                var addRow = {
                    title: t.lang['tableAddRow'],
                    text: t.lang['tableAddRow'],
                    ico: 'row-below',

                    fn: function() {
                        t.saveRange();

                        var node = t.doc.getSelection().focusNode;
                        var table = $(node).closest('table');

                        if (table.length > 0) {
                            var row = $('<tr></tr>');
                            // add columns according to current columns count
                            for (var i = 0; i < table.find('tr')[0].childElementCount; i += 1) {
                                $('<td></td>').appendTo(row);
                            }
                            // add row to table
                            row.appendTo(table);
                        }

                        return true;
                    }
                };

                var addColumn = {
                    title: t.lang['tableAddColumn'],
                    text: t.lang['tableAddColumn'],
                    ico: 'col-right',

                    fn: function() {
                        t.saveRange();

                        var node = t.doc.getSelection().focusNode;
                        var table = $(node).closest('table');

                        if (table.length > 0) {
                            $(table).find('tr').each(function() {
                                $(this).find('td:last').after('<td></td>');
                            });
                        }

                        return true;
                    }
                };

                var destroy = {
                    title: t.lang['tableDestroy'],
                    text: t.lang['tableDestroy'],
                    ico: 'table-delete',

                    fn: function() {
                        t.saveRange();

                        var node = t.doc.getSelection().focusNode,
                            table = $(node).closest('table');

                        table.remove();

                        return true;
                    }
                };

                var deleteRow = {
                    title: t.lang['tableDeleteRow'],
                    text: t.lang['tableDeleteRow'],
                    ico: 'row-delete',

                    fn: function() {
                        t.saveRange();

                        var node = t.doc.getSelection().focusNode,
                            row = $(node).closest('tr');

                        row.remove();

                        return true;
                    }
                };

                var deleteColumn = {
                    title: t.lang['tableDeleteColumn'],
                    text: t.lang['tableDeleteColumn'],
                    ico: 'col-delete',

                    fn: function() {
                        t.saveRange();

                        var node = t.doc.getSelection().focusNode,
                            table = $(node).closest('table'),
                            td = $(node).closest('td'),
                            cellIndex = td.index();

                        $(table).find('tr').each(function() {
                            $(this).find('td:eq(' + cellIndex + ')').remove();
                        });

                        return true;
                    }
                };

                t.addBtnDef('table', buildButtonDef);
                t.addBtnDef('tableAddRow', addRow);
                t.addBtnDef('tableAddColumn', addColumn);
                t.addBtnDef('tableDeleteRow', deleteRow);
                t.addBtnDef('tableDeleteColumn', deleteColumn);
                t.addBtnDef('tableDestroy', destroy);
            }
        }
    }
});
</script>