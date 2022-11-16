		<!--begin::Card-->
		<div class="card card-custom gutter-b example example-compact">
		    <div class="card-header">
		        <h3 class="card-title">
		            DASHBOARD
		        </h3>
		        <div class="card-toolbar">
		            <!--<div class="example-tools justify-content-center">
						<span class="example-toggle" data-toggle="tooltip" title="View code"></span>
						<span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
					</div>-->
		        </div>
		    </div>


		    <div class="card-body">

		        <?php
    error_reporting(0);
    $today_date = date("Y-m-d");
    $today = date("d")."/".date("m")."/".(date("Y")+543);
    $repair_today = "#";
    if($logged_user_role_id == '1'){
        $conditions = " ";
        }else{

        $conditions = " AND u.org_id = '$logged_org_id' ";

        }

    $numb_service_today = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."repair_main u WHERE u.flag != '0' AND u.repair_date = '$today_date' $conditions  ")->fetchColumn();//แจ้งซ่อมวันนี้

    $numb_service = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."repair_main u WHERE u.flag != '0' $conditions ")->fetchColumn();//รายการซ่อมทั้งหมด

    $numb_add_today = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."repair_main u WHERE u.flag != '0' AND repair_status = '1' $conditions ")->fetchColumn();//รอซ่อม

    $numb_bid = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."repair_main u WHERE u.flag != '0' AND repair_status = '2' $conditions ")->fetchColumn();//เสนอราคา

    $numb_begin_work = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."repair_main u WHERE u.flag != '0' AND repair_status = '3' $conditions ")->fetchColumn();//กำลังซ่อม

    $numb_pause = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."repair_main u WHERE u.flag != '0' AND repair_status = '4' $conditions ")->fetchColumn();//พักการซ่อม

    $numb_add_out = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."repair_main u WHERE u.flag != '0' AND repair_status = '5'  $conditions ")->fetchColumn();//ส่งซ่อมภายนอก

    $numb_add_come = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."repair_main u WHERE u.flag != '0' AND repair_status = '6' $conditions ")->fetchColumn();//ส่งกลับจากภายนอก

    $numb_cancel = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."repair_main u WHERE u.flag != '0' AND repair_status = '7' $conditions ")->fetchColumn();//ยกเลิกการซ่อม

    $numb_finish_repair = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."repair_main u WHERE u.flag != '0' AND repair_status = '8' $conditions ")->fetchColumn();//ซ่อมเสร็จ

    $numb_close_work = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."repair_main u WHERE u.flag != '0' AND repair_status = '9' $conditions ")->fetchColumn();//ปิดงานซ่อม
    
    // $numb_come_out = $conn->query("SELECT COUNT(1) FROM ".FB_PREFIX."repair_main u WHERE u.flag != '0' AND repair_status = '6' $conditions ")->fetchColumn();
    // $numb_pause = $conn->query("SELECT COUNT(1) FROM ".FB_PREFIX."repair_main u WHERE u.flag != '0' AND repair_status = '4' $conditions ")->fetchColumn();
    if( $numb_service_today != '0'){$repair_today = "dashboard.php?act=&module=repair&page=main&startdate=".$today."&enddate=&status=&search=";}
    //$numb_equipment = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."equipment_main s WHERE s.flag != '0' $conditions  ")->fetchColumn();
    //$numb_donate = $conn->query("SELECT COUNT(1) FROM ".DB_PREFIX."donate_main s WHERE s.flag != '0'  $conditions  ")->fetchColumn();
    ?>

		        <script src="https://unpkg.com/boxicons@2.1.2/dist/boxicons.js"></script>
		        <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>

		        <div class="row">
		            <div class="col-xl-2">
		                <a href="<?php echo $repair_today ?>"
		                    class="text-dark text-hover-primary font-weight-bold font-size-lg mt-3">
		                    <div class="card card-custom gutter-b bg-warning" style="height: 150px">
		                        <div class="card-body">
		                            <img src="./assets/images/event.png" alt="event" style="width:45x;height:45px;">
		                            <!-- <span class="svg-icon svg-icon-3x svg-icon-success"><i class='bx bx-calendar bx-lg'></i></span> -->
		                            <!-- แจ้งซ่อมวันนี้ -->
		                            <div class="text-dark font-weight-bolder font-size-h2 mt-3">
		                                <?php echo $numb_service_today;?>
		                            </div>
		                            <strong>แจ้งซ่อมวันนี้</strong>
		                        </div>
		                    </div>
		                </a>
		            </div>

		            <div class="col-xl-2">
		                <a href="././dashboard.php?module=repair&page=main"
		                    class="text-dark text-hover-primary font-weight-bold font-size-lg mt-3">
		                    <div class="card card-custom gutter-b bg-warning" style="height: 150px">
		                        <div class="card-body">
		                            <img src="./assets/images/checklist.png" alt="checklist" style="width:45x;height:45px;">
		                            <!-- <span class="svg-icon svg-icon-3x svg-icon-success"><i class='bx bxs-collection bx-lg'></i></span> -->
		                            <!-- รายการซ่อมทั้งหมด -->
		                            <div class="text-dark font-weight-bolder font-size-h2 mt-3"><?php echo $numb_service;?>
		                            </div>
		                            <strong>รายการซ่อมทั้งหมด</strong>
		                        </div>
		                    </div>
		                </a>
		            </div>

		            <div class="col-xl-2">
		                <a href="././dashboard.php?act=&module=repair&page=main&startdate=&enddate=&status=1&search="
		                    class="text-dark text-hover-primary font-weight-bold font-size-lg mt-3">
		                    <div class="card card-custom gutter-b bg-success" style="height: 150px">
		                        <div class="card-body">
		                            <img src="./assets/images/technical.png" alt="technical" style="width:45x;height:45px;">
		                            <!-- <span class="svg-icon svg-icon-3x svg-icon-success "><i class='bx bx-message-alt-check bx-lg'></i></span> -->
		                            <!-- รอซ่อม -->
		                            <div class="text-dark font-weight-bolder font-size-h2 mt-3"><?php echo $numb_add_today;?>
		                            </div>
		                            <strong>รอซ่อม</strong>
		                        </div>
		                    </div>
		                </a>
		            </div>

		            <div class="col-xl-2">
		                <a href="././dashboard.php?act=&module=repair&page=main&startdate=&enddate=&status=2&search="
		                    class="text-dark text-hover-primary font-weight-bold font-size-lg mt-3">
		                    <div class="card card-custom gutter-b bg-success" style="height: 150px">
		                        <div class="card-body">
		                            <img src="./assets/images/money.png" alt="money" style="width:45x;height:45px;">
		                            <!-- <span class="svg-icon svg-icon-3x svg-icon-success "><i class='bx bx-message-alt-check bx-lg'></i></span> -->
		                            <!-- เสนอราคา -->
		                            <div class="text-dark font-weight-bolder font-size-h2 mt-3"><?php echo $numb_bid;?></div>

		                            <strong>เสนอราคา</strong>
		                        </div>
		                    </div>
		                </a>
		            </div>

		            <div class="col-xl-2">
		                <a href="././dashboard.php?act=&module=repair&page=main&startdate=&enddate=&status=3&search="
		                    class="text-dark text-hover-primary font-weight-bold font-size-lg mt-3">
		                    <div class="card card-custom gutter-b bg-success" style="height: 150px">
		                        <div class="card-body">
		                            <img src="./assets/images/tools.png" alt="tools" style="width:45x;height:45px;">
		                            <!-- <span class="svg-icon svg-icon-3x svg-icon-success"><i class='bx bxs-cog bx-lg'></i></span> -->
		                            <!-- กำลังซ่อม -->
		                            <div class="text-dark font-weight-bolder font-size-h2 mt-3"><?php echo $numb_begin_work;?>
		                            </div>
		                            <strong>กำลังซ่อม</strong>
		                        </div>
		                    </div>
		                </a>
		            </div>

		            <div class="col-xl-2">
		                <a href="././dashboard.php?act=&module=repair&page=main&startdate=&enddate=&status=4&search="
		                    class="text-dark text-hover-primary font-weight-bold font-size-lg mt-3">
		                    <div class="card card-custom gutter-b bg-success" style="height: 150px">
		                        <div class="card-body">
		                            <img src="./assets/images/pause.png" alt="pause" style="width:45x;height:45px;">
		                            <!-- <span class="svg-icon svg-icon-3x svg-icon-success"><i class='bx bxs-cog bx-lg'></i></span> -->
		                            <!-- พักการซ่อม -->
		                            <div class="text-dark font-weight-bolder font-size-h2 mt-3"><?php echo $numb_pause;?>
		                            </div>
		                            <strong>พักการซ่อม</strong>
		                        </div>
		                    </div>
		                </a>
		            </div>

		            <div class="col-xl-2">
		                <a href="././dashboard.php?act=&module=repair&page=main&startdate=&enddate=&status=5&search="
		                    class="text-dark text-hover-primary font-weight-bold font-size-lg mt-3">
		                    <div class="card card-custom gutter-b bg-success" style="height: 150px">
		                        <div class="card-body">
		                            <img src="./assets/images/delivery-truck-go.png" alt="delivery-truck-go"
		                                style="width:50x;height:50px;">
		                            <!-- <span class="svg-icon svg-icon-3x svg-icon-success"><i class='bx bxs-car-mechanic bx-lg'></i></span> -->
		                            <!-- ส่งซ่อมภายนอก -->
		                            <div class="text-dark font-weight-bolder font-size-h2 mt-3"><?php echo $numb_add_out;?>
		                            </div>
		                            <strong>ส่งซ่อมภายนอก</strong>
		                        </div>
		                    </div>
		                </a>
		            </div>


		            <div class="col-xl-2">
		                <a href="././dashboard.php?act=&module=repair&page=main&startdate=&enddate=&status=6&search="
		                    class="text-dark text-hover-primary font-weight-bold font-size-lg mt-3">
		                    <div class="card card-custom gutter-b bg-success" style="height: 150px">
		                        <div class="card-body">
		                            <img src="./assets/images/package.png" alt="package" style="width:50px;height:50px;">
		                            <!-- <span class="svg-icon svg-icon-3x svg-icon-success"><i class='bx bxs-car-mechanic bx-lg'></i></span> -->
		                            <!-- ส่งกลับจากภายนอก -->
		                            <div class="text-dark font-weight-bolder font-size-h2 mt-3"><?php echo $numb_add_come;?>
		                            </div>
		                            <strong>ส่งกลับจากภายนอก</strong>
		                        </div>
		                    </div>
		                </a>
		            </div>

		            <div class="col-xl-2">
		                <a href="././dashboard.php?act=&module=repair&page=main&startdate=&enddate=&status=7&search="
		                    class="text-dark text-hover-primary font-weight-bold font-size-lg mt-3">
		                    <div class="card card-custom gutter-b bg-success" style="height: 150px">
		                        <div class="card-body">
		                            <img src="./assets/images/cross.png" alt="cross" style="width:45x;height:45px;">
		                            <!-- <span class="svg-icon svg-icon-3x svg-icon-success"><i class='bx bxs-select-multiple bx-lg'></i></span> -->
		                            <!-- ยกเลิกการซ่อม -->
		                            <div class="text-dark font-weight-bolder font-size-h2 mt-3"><?php echo $numb_cancel;?>
		                            </div>
		                            <strong>ยกเลิกการซ่อม</strong>
		                        </div>
		                    </div>
		                </a>
		            </div>

		            <div class="col-xl-2">
		                <a href="././dashboard.php?act=&module=repair&page=main&startdate=&enddate=&status=8&search="
		                    class="text-dark text-hover-primary font-weight-bold font-size-lg mt-3">
		                    <div class="card card-custom gutter-b bg-success" style="height: 150px">
		                        <div class="card-body">
		                            <img src="./assets/images/checked.png" alt="checked" style="width:45x;height:45px;">
		                            <!-- <span class="svg-icon svg-icon-3x svg-icon-success"><i class='bx bxs-select-multiple bx-lg'></i></span> -->
		                            <!-- ซ่อมเสร็จ -->
		                            <div class="text-dark font-weight-bolder font-size-h2 mt-3">
		                                <?php echo $numb_finish_repair;?>
		                            </div>
		                            <strong>ซ่อมเสร็จ</strong>
		                        </div>
		                    </div>
		                </a>
		            </div>

		            <div class="col-xl-2">
		                <a href="././dashboard.php?act=&module=repair&page=main&startdate=&enddate=&status=9&search="
		                    class="text-dark text-hover-primary font-weight-bold font-size-lg mt-3">
		                    <div class="card card-custom gutter-b bg-success" style="height: 150px">
		                        <div class="card-body">
		                            <img src="./assets/images/completed-task.png" alt="completed-task"
		                                style="width:50px;height:50px;">
		                            <!-- <span class="svg-icon svg-icon-3x svg-icon-success"><i class='bx bx-calendar bx-lg'></i></span> -->
		                            <!-- ปิดงานซ่อม -->
		                            <div class="text-dark font-weight-bolder font-size-h2 mt-3"><?php echo $numb_close_work;?>
		                            </div>
		                            <strong>ปิดงานซ่อม</strong>
		                        </div>
		                    </div>
		                </a>
		            </div>









		            <!-- <div class="col-xl-2">
                                    <div class="card card-custom gutter-b" style="height: 150px">
                                        <div class="card-body">
                                            <span class="svg-icon svg-icon-3x svg-icon-success"><i class="fas fa-user-clock fa-3x text-success"></i></span>
                                            <div class="text-dark font-weight-bolder font-size-h2 mt-3"><?php//echo $numb_service_today;?></div>
                                            <a href="#" class="text-muted text-hover-primary font-weight-bold font-size-lg mt-1">รายการรอรับคืน</a>
                                        </div>
                                    </div>
                            </div> -->

		            <!--<div class="col-xl-3">
                                    <div class="card card-custom gutter-b" style="height: 150px">
                                        <div class="card-body">
                                            <span class="svg-icon svg-icon-3x svg-icon-success"><i class="fas fa-wheelchair fa-3x text-success"></i></span>
                                            <div class="text-dark font-weight-bolder font-size-h2 mt-3"></div>
                                            <a href="#" class="text-muted text-hover-primary font-weight-bold font-size-lg mt-1">จำนวนกายอุปกรณ์</a>
                                        </div>
                                    </div>
                            </div>

                            <div class="col-xl-3">
                                    <div class="card card-custom gutter-b" style="height: 150px">
                                        <div class="card-body">
                                            <span class="svg-icon svg-icon-3x svg-icon-success"><i class="fas fa-boxes fa-3x text-success"></i></span>
                                            <div class="text-dark font-weight-bolder font-size-h2 mt-3"></div>
                                            <a href="#" class="text-muted text-hover-primary font-weight-bold font-size-lg mt-1">จำนวนรับบริจาค</a>
                                        </div>
                                    </div>
                            </div>-->
		        </div>


		        <div class="row">

		            <div class="col-lg-12 col-xxl-12">
		                <!--begin::Advance Table Widget 1-->
		                <div class="card card-custom card-stretch gutter-b">
		                    <!--begin::Header-->
		                    <div class="card-header border-0 py-5 bg-secondary">
		                        <h3 class="card-title align-items-start flex-column ">
		                            <span class="card-label font-weight-bolder text-dark"><i
		                                    class="fas fa-users-cog"></i>&nbsp;รายการแจ้งซ่อมบันทึกล่าสุด</span>
		                            <span class="text-muted mt-3 font-weight-bold font-size-sm"><a
		                                    href="dashboard.php?module=repair&page=main">ทั้งหมด <?php echo $numb_service;?>
		                                    รายการ</a></span>
		                        </h3>
		                        <div class="card-toolbar">
		                            <a href="?module=repair&page=repair-add"
		                                class="btn btn-success btn-sm font-weight-bolder font-size-sm">
		                                <i class="fas fa-plus-circle"></i></span>บันทึกแจ้งซ่อม
		                            </a>
		                        </div>
		                    </div>
		                    <!--end::Header-->

		                    <!--begin::Body-->
		                    <div class="card-body py-0 bg-light">
		                        <?php 
    //$numb_data = $conn->query("SELECT count(1) FROM ".DB_PREFIX."service_main s  WHERE s.flag != '0' $conditions  ")->fetchColumn();

    
        
    $stmt_data = $conn->prepare ("SELECT u.*,p.*,us.name,rp.*,rs.staff_id,sm.*,rt.status_date,o.org_shortname ,t.repair_typetitle,if(e.eq_code IS NOT NULL ,e.eq_code,u.eq_code) AS eq_code, if(e.eq_name IS NOT NULL ,e.eq_name,u.eq_name) AS eq_name,st.status_title,pm.cost,pm.cost_payment,pm.cost_success
    FROM ".DB_PREFIX."repair_main u 
    LEFT JOIN ".DB_PREFIX."org_main o ON u.org_id = o.org_id 
    LEFT JOIN ".DB_PREFIX."repair_type t ON u.repair_type = t.repair_typeid
    LEFT JOIN ".DB_PREFIX."person_main p ON u.person_id = p.oid
    LEFT JOIN ".DB_PREFIX."cprename pr ON p.prename = pr.id_prename
    LEFT JOIN ".DB_PREFIX."equipment_main e ON u.eq_id = e.oid
    LEFT JOIN ".DB_PREFIX."repair_status_type st ON u.repair_status = st.status_typeid
    LEFT JOIN ".DB_PREFIX."repair_payment pm ON u.repair_id = pm.repair_id
	LEFT JOIN ".DB_PREFIX."users us ON u.add_users = us.user_id
	LEFt JOIN ".DB_PREFIX."repair_place rp ON u.repair_place = rp.place_id
	LEFT JOIN  ".DB_PREFIX."(SELECT * FROM repair_staff WHERE add_date IN (SELECT max(add_date) FROM repair_staff GROUP BY service_id )) AS rs ON u.repair_id = rs.service_id
	LEFT JOIN  ".DB_PREFIX."staff_main sm ON rs.staff_id = sm.oid
	LEFT JOIN  ".DB_PREFIX."(SELECT * FROM repair_status WHERE add_date IN (SELECT max(add_date) FROM repair_status GROUP BY repair_id )) AS rt ON u.repair_id = rt.repair_id

    WHERE u.flag != 0  $conditions
    ORDER BY u.repair_id DESC LIMIT 10");
    $stmt_data->execute();		
	

    ?>
		                        <!--begin::Table-->
		                        <div class="table-responsive">
		                            <table class="table table-bordered table-hover table-strip" id="tbData"
		                                style="margin-top: 13px !important; min-height: 200px;">
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
		                                        <th>ชื่อลูกค้า</th>
		                                        <th>วันที่ออกปฏิบัติงาน</th>
		                                        <th>ผู้ปฏิบัติงาน</th>
		                                        <th>สถานะ</th>

		                                        <th class="text-center">จัดการ</th>
		                                    </tr>
		                                </thead>
		                                <tbody>

		                                    <?php

            $i  = 0;
            while ($row = $stmt_data->fetch(PDO::FETCH_ASSOC))
            {
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
                $telephone = $row['telephone'];
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
                $comp_name = $row['comp_name'];
				$user_name = $row['name'];
				$status_typeid = $row['repair_status'];
				$place_name = $row['place_title'];
				$staff_name = $row['sfname'].' '.$row['slname'].'';
				$status_date = date_db_2form($row['status_date']);
				$place_id = $row['place_id'];

				
				$d1=strtotime($row['repair_date']);
				$d2=ceil((time()-$d1)/60.5/60/24);


				
				


                $repair_status = $row['repair_status'];
                $repair_inout = $row['repair_inout'];
                if($repair_status != '5'){
                    $repair_inout_show = "<i class='fas fa-tools text-success'></i>";
                }else{
                    $repair_inout_show = "<i class='fas fa-truck text-danger'></i>";
                }

                $return_date = date_db_2form($row['return_date']);

                if(($repair_status == '9') AND ($return_date != "")){

                    $return_status = "<i class='fas fa-check-circle text-success'></i>";

                }else{
                    $return_status = "";
                }
                    $stmt_spare = $conn->prepare ("SELECT SUM(u.spare_price) AS sum_price
                    ,GROUP_CONCAT(s.spare_name,' ',u.spare_quantity,' ',t.unit_title,' ราคา ',u.spare_price,' บาท') AS detail
                    FROM ".DB_PREFIX."repair_spare u 
                    LEFT JOIN  ".DB_PREFIX."spare_main s ON u.spare_id = s.spare_id
                    LEFT JOIN ".DB_PREFIX."cunit t ON u.spare_unit = t.unit_id
                    WHERE u.flag != '0' AND u.repair_id = '$repairid'
                    ");
                    $stmt_spare->execute();	
                    $row_spare = $stmt_spare->fetch(PDO::FETCH_ASSOC);

                    $sum_price = $row_spare['sum_price'];

                    $cost = $row['cost'];
                    $cost_payment = $row['cost_payment'];
                    $cost_success = $row['cost_success'];
                ?>

		                                    <tr>

		                                        <td class="text-center"><?php echo $i; ?></td>


		                                        <td class="text-center">
		                                            <a
		                                                href="dashboard.php?module=repair&page=repair-add-data&repairid=<?php echo $repairid_enc;?>&personid=<?php echo $personid_enc;?>&act=<?php echo base64_encode('edit');?>">
		                                                <?php echo $repair_code;?>
		                                            </a>
		                                        </td>
		                                        <td><?php echo $repair_date;?></td>
		                                        <td class="text-center">

		                                            <?php 
												if($d2 < 7){  ?>
		                                            <h4><span class="badge bg-success"><?php echo  $d2." วัน" ;?></span></h4>
		                                            <?php }
												elseif ( $d2 >7 && $d2 == 15 ) { ?>
		                                            <h4><span class="badge bg-warning"><?php echo  $d2." วัน" ;?></span></h4>
		                                            <?php }
												elseif ( $d2 >15 ) { ?>
		                                            <h4><span class="badge bg-danger"><?php echo  $d2." วัน" ;?></span></h4>
		                                            <?php }
													?>

		                                        </td>
		                                        <td><?php echo $repair_typetitle;?> </br> <?php 
												if($place_id == 1){  ?>
		                                            <span class="badge bg-success"><?php echo  $place_name ;?></span>
		                                            <?php }
												elseif ( $place_id == 2 ) { ?>
		                                            <span class="badge bg-warning"><?php echo  $place_name ;?></span>
		                                            <?php }
															?>
		                                        </td>
		                                        <td><?php echo $comp_name; ?></td>
		                                        <td><?php echo $eq_name;?></br><small>รหัส : <?php echo $eq_code;?></small>
		                                        </td>
		                                        <td><?php echo $repair_title;?></td>
		                                        <td><?php echo $fullname;?></br><small>เบอร์ติดต่อ :
		                                                <?php echo $telephone;?></small>
		                                        </td>
		                                        <td class="text-center"><?php echo $status_date ; ?></td>
		                                        <td><?php echo $staff_name; ?></td>



		                                        <td class="text-center"> <?php 
												if($status_typeid == 8 || $status_typeid ==9 ){  ?>

		                                            <h4><span class="badge bg-success"> <?php echo $status_title; ?></span>
		                                            </h4>
		                                            <?php }
												elseif ( $status_typeid < 7  ) { ?>
		                                            <h4><span class="badge bg-warning"> <?php echo $status_title; ?></span>
		                                            </h4>
		                                            <?php }
												elseif ( $status_typeid == 7  ) { ?>
		                                            <h4><span class="badge bg-danger"> <?php echo $status_title; ?></span>
		                                            </h4>
		                                            <?php }
													?>
		                                        </td>



		                                        </a>

		                                        <td class="text-center">
		                                            <!--begin::Dropdown-->
		                                            <div class="dropdown">
		                                                <a href="#" class="btn btn-clean btn-icon" data-toggle="dropdown">
		                                                    <i class="ki ki-bold-more-hor font-size-md"></i>
		                                                </a>
		                                                <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
		                                                    <!--begin::Navigation-->

		                                                    <?php 
                                    if($repair_inout == 'I'){

                                        ?>
		                                                    <ul class="navi navi-hover py-1">
		                                                        <li class="navi-item">
		                                                            <a href="dashboard.php?module=repair&page=repair-print&personid=<?php echo $personid_enc;?>&repairid=<?php echo $repairid_enc;?>&act=<?php echo base64_encode('view');?>"
		                                                                class="navi-link">
		                                                                <span class="navi-icon"><i
		                                                                        class="fas fa-clipboard-list"></i></span>
		                                                                <span class="navi-text">ใบแจ้งซ่อม</span>
		                                                            </a>
		                                                        </li>

		                                                        <li class="navi-item">
		                                                            <a href="dashboard.php?module=repair&page=repair-add&repairid=<?php echo $repairid_enc;?>&personid=<?php echo $personid_enc;?>&act=<?php echo base64_encode('edit');?>"
		                                                                class="navi-link">
		                                                                <span class="navi-icon"><i
		                                                                        class="fas fa-edit"></i></span>
		                                                                <span class="navi-text">แก้ไขข้อมูลผู้แจ้งซ่อม</span>
		                                                            </a>
		                                                        </li>

		                                                        <li class="navi-item">
		                                                            <a href="dashboard.php?module=repair&page=repair-add-data&repairid=<?php echo $repairid_enc;?>&personid=<?php echo $personid_enc;?>&act=<?php echo base64_encode('edit');?>"
		                                                                class="navi-link">
		                                                                <span class="navi-icon"><i
		                                                                        class="fas fa-cogs"></i></span>
		                                                                <span class="navi-text">บันทึกผลการซ่อม</span>
		                                                            </a>
		                                                        </li>

		                                                        <li class="navi-separator my-3"></li>

		                                                        <li class="navi-item">
		                                                            <a href="#" class="navi-link"
		                                                                onclick='delRepairMain(<?php echo $repairid; ?>)'>
		                                                                <span class="navi-icon"><i
		                                                                        class="fas fa-trash"></i></span>
		                                                                <span class="navi-text">ยกเลิกรายการ</span>
		                                                            </a>
		                                                        </li>
		                                                    </ul>
		                                                    <?php }else {?>

		                                                    <ul class="navi navi-hover py-1">
		                                                        <li class="navi-item">
		                                                            <a href="dashboard.php?module=repairout&page=repairout-print&personid=<?php echo $personid_enc;?>&repairid=<?php echo $repairid_enc;?>&act=<?php echo base64_encode('view');?>"
		                                                                class="navi-link">
		                                                                <span class="navi-icon"><i
		                                                                        class="fas fa-clipboard-list"></i></span>
		                                                                <span class="navi-text">ใบส่งซ่อมภายนอก</span>
		                                                            </a>
		                                                        </li>

		                                                        <li class="navi-item">
		                                                            <a href="dashboard.php?module=repairout&page=repairout-add&repairid=<?php echo $repairid_enc;?>&personid=<?php echo $personid_enc;?>&act=<?php echo base64_encode('edit');?>"
		                                                                class="navi-link">
		                                                                <span class="navi-icon"><i
		                                                                        class="fas fa-edit"></i></span>
		                                                                <span class="navi-text">แก้ไขข้อมูลผู้แจ้งซ่อม</span>
		                                                            </a>
		                                                        </li>

		                                                        <li class="navi-item">
		                                                            <a href="dashboard.php?module=repairout&page=repairout-add-data&repairid=<?php echo $repairid_enc;?>&personid=<?php echo $personid_enc;?>&act=<?php echo base64_encode('edit');?>"
		                                                                class="navi-link">
		                                                                <span class="navi-icon"><i
		                                                                        class="fas fa-cogs"></i></span>
		                                                                <span class="navi-text">บันทึกผลการซ่อมภายนอก</span>
		                                                            </a>
		                                                        </li>

		                                                        <li class="navi-separator my-3"></li>

		                                                        <li class="navi-item">
		                                                            <a href="#" class="navi-link"
		                                                                onclick='delRepairMain(<?php echo $repairid; ?>)'>
		                                                                <span class="navi-icon"><i
		                                                                        class="fas fa-trash"></i></span>
		                                                                <span class="navi-text">ยกเลิกรายการ</span>
		                                                            </a>
		                                                        </li>
		                                                    </ul>


		                                                    <?php }?>
		                                                    <!--end::Navigation-->
		                                                </div>
		                                            </div>
		                                            <!--end::Dropdown-->
		                                        </td>

		                                    </tr>
		                                    <?php 
            } // end while
            ?>
		                                </tbody>
		                            </table>
		                        </div>
		                        <!--end::Table-->
		                    </div>
		                    <!--end::Body-->
		                </div>
		                <!--end::Advance Table Widget 1-->
		            </div>
		        </div>

		    </div>


		</div>
		<!--end::Card-->


		<script>
function delRepairMain(id) {
    Swal.fire({
        title: 'แน่ใจนะ?',
        text: "ต้องการยกเลิกรายการ",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ใช่, ต้องการยกเลิกรายการ !'
    }).then((result) => {
        if (result.value) { //Yes
            $.post("core/repair/repair-del.php", {
                id: id
            }, function(result) {
                //  $("test").html(result);
                // console.log(result.code);
                location.reload();
            });
        }
    })
}
		</script>