<?php
error_reporting(0);
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
$eq_typeid = filter_input(INPUT_GET, 'eq_typeid', FILTER_SANITIZE_STRING);
if($search != ""){
    $search_data = " AND u.eq_codes LIKE '%$search%' OR u.eq_names LIKE '%$search%'  ";
}
if($eq_typeid != ""){
    $eq_typeid_data = " AND u.eq_typeid = '$eq_typeid'  ";
}

?>
<script src="https://kit.fontawesome.com/09f98e4a32.js" crossorigin="anonymous"></script>
		<!--begin::Card-->
		<div class="card card-custom gutter-b example example-compact">
			<div class="card-header">
				<h3 class="card-title">
                <i class="fa-solid fa-box"></i>&nbsp;คลังอุปกรณ์ 
				</h3>
				<div class="card-toolbar">
					<div class="example-tools justify-content-center">
						<a href="dashboard.php?module=equipment&page=equipment-add" class="btn btn-success btn-sm font-weight-bold mr-2" title=""><i class="fa fa-plus-circle" title="เพิ่มผู้ใช้" data-toggle="tooltip"></i>เพิ่มอุปกรณ์</a>
					</div>
				</div>
			</div>



	<div class="card-body">


    <form class="form" enctype="multipart/form-data" method="GET">
    <input type="hidden" class="form-control"  name="act"  value="<?php echo $action;?>"/>
    <input type="hidden" class="form-control"  name="module"  value="<?php echo $module;?>"/>
    <input type="hidden" class="form-control"  name="page"  value="main"/>

    <div class="form-group row">


            <div class="col-lg-3">
                <label>ประเภทอุปกรณ์</label>

                <select class="form-control " name="eq_typeid" id="eq_typeid">
                    <option value="">ทั้งหมด</option>
                    <?php
					if($logged_user_role_id == '1'){
						$conditions = " ";
					}else{
						$conditions = " AND org_id = '$logged_org_id' ";
					}
					$stmt_user_role = $conn->prepare("SELECT * FROM ".DB_PREFIX."equipment_type  WHERE flag = '1'  ORDER BY eq_order ASC");
					$stmt_user_role->execute();		
					while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC))
						{
						$id_selected = $row['eq_typeid'];
						$title_selected = stripslashes($row['eq_typename']);
						?>
						<option value="<?php echo $id_selected;?>" <?php if($eq_typeid == $id_selected ){echo "selected";} ?>><?php echo $title_selected;?></option>
						<?php
						}
					?>
				</select>
             </div>

             <div class="col-lg-4">
				<label>รหัส/ชื่ออุปกรณ์</label>
				<div class="input-group">
							<input type="text" class="form-control" placeholder="รหัส/ชื่ออุปกรณ์"  name="search" id="search"  value="<?php echo $search;?>"/>
							<div class="input-group-append">
								<button class="btn btn-primary" type="submit" ><i class="fas fa-search"></i></button>
							</div>
						</div>
			</div>

            </div>

    </form> 

    <?php

    if($logged_user_role_id == '1'){
        $conditions = " ";
    }else{
        $conditions = " AND u.org_id = '$logged_org_id' ";
    }


    $numb_data = $conn->query("SELECT count(1) FROM ".DB_PREFIX."equipment_main u  WHERE u.flag != '0' $conditions $search_data $eq_typeid_data ")->fetchColumn();

  
        if (!(isset($pagenum))) { $pagenum = 1; }
        if ($numb_data==0) { echo "No Data"; }
        else{
        $page_rows = 10;
        $last = ceil($numb_data/$page_rows);

        if ($pagenum < 1)
        {
        $pagenum = 1;
        }
        elseif ($pagenum > $last)
        {
        $pagenum = $last;
        }

        //สำหรับจัดการหน้า
        $numb_data = $numb_data;
        if($numb_data<=$page_rows)	{
            $Num_Pages =1;
        }else if(($numb_data % $page_rows)==0){
            $Num_Pages =($numb_data/$page_rows) ;
        }else{
            $Num_Pages =($numb_data/$page_rows)+1;
            $Num_Pages = (int)$Num_Pages;
        }

        $max = ' LIMIT ' .($pagenum - 1) * $page_rows .',' .$page_rows;		
        $stmt_data = $conn->prepare ("SELECT u.*,o.org_name,o.org_shortname,t.eq_typename,s.status_title,s.status_color,r.rec_title FROM ".DB_PREFIX."equipment_main u 
        LEFT JOIN ".DB_PREFIX."org_main o ON u.org_id = o.org_id
        LEFT JOIN ".DB_PREFIX."equipment_type t ON u.eq_typeid = t.eq_typeid
        LEFT JOIN ".DB_PREFIX."equipment_status s ON u.flag = s.status_id
        LEFT JOIN ".DB_PREFIX."receive_type r ON u.receive_id = r.rec_id
        WHERE u.flag != '0' $conditions $search_data $eq_typeid_data
        ORDER BY u.oid DESC
        $max");
        $stmt_data->execute();		


    ?>



<div class="table-responsive">
	<table class="table table-bordered table-hover table-strip" id="tbData" style="margin-top: 13px !important">
    <thead>
    <tr>
                        <th class="text-center">ลำดับ</th>
                        <th>รูปอุปกรณ์</th>
                        <th>qrcode</th>
                        <th>รหัสอุปกรณ์</th>
                        <th>รหัสครุภัณฑ์</th>
                        <th>ชื่ออุปกรณ์</th>
                        <th class="text-center">ประเภท</th>
                        <th class="text-center">ประเภทการได้มา</th>
                        <th>หน่วยงาน</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center">จัดการ</th>	
    </tr>
    </thead>
    <tbody>
            
            <?php

            $i  = 0;
            while ($row = $stmt_data->fetch(PDO::FETCH_ASSOC))
            {
                $i++;
                $oid = $row['oid'];
                $oid_enc = base64_encode($oid);
                $eq_names = $row['eq_names'];
                $eq_desc = $row['eq_desc'];
                $eq_codes = $row['eq_codes'];
                $eq_number = $row['eq_number'];
                $org_shortname = $row['org_shortname'];
                $eq_typename = $row['eq_typename'];
                $eqtypeid = $row['eq_typeid'];
                if($eqtypeid == 1){
                    $eq_typeother = "<br>(".$row['eq_typeother'].")";  
                }else{
                    $eq_typeother = "";
                }
                $receive_date = date_db_2form($row['receive_date']);
                $eq_img = $row['eq_img'];
                $status_title = $row['status_title'];
                $status_color = $row['status_color'];
                $rec_title = $row['rec_title'];

                $receive_id = $row['receive_id'];
                if($receive_id == 4){
                    $receive_other = "<br>(".$row['receive_other'].")";  
                }else{
                    $receive_other = "";
                }

                ?>
                <tr>
                            <td class="text-center"><?php echo $i;?></td>
                            <td class="text-center"><div class="symbol symbol-50 symbol-lg-60">
                            <?php if($eq_img == ""){?>
                                         <img src="uploads/no-image.jpg" alt="image"/>
                                   
                            <?php }else{?>
                                        <img src="uploads/equipment/<?php echo $eq_img;?>" alt="image"/>
                                        
                                <?php   } ?>
                </div></td>

                <td class="text-center"><a href="dashboard.php?module=equipment&page=equipment-print&id=<?php echo $oid_enc;?>&act=<?php echo base64_encode('view');?>" > <div class="symbol symbol-50 symbol-lg-60">
                            
                                         
                <img src="uploads/qrcode/<?php echo $oid;?>.png" alt="image"/>
                            
                </div></a></td>
                            <td><?php echo $eq_codes;?></br><small> วันที่รับ : <?php echo $receive_date;?></small></td>
                            <td><?php echo $eq_number;?></td>
                            <td><?php echo $eq_names;?></td>
                            <td ><?php 
                            echo $eq_typename;
                            echo $eq_typeother;
                            ?></td>
                            <td ><?php echo $rec_title;
                            echo $receive_other;?></td>
                            <td><?php echo $org_shortname;?></td>
                            
                            <td class="text-center"><span class="label label-lg label-light-<?php echo $status_color;?> label-inline"><?php echo $status_title;?></span></td>
                            <td class="text-center">
                            <!--begin::Dropdown-->
                                <div class="dropdown">
                                    <a href="#" class="btn btn-clean btn-icon" data-toggle="dropdown">
                                        <i class="ki ki-bold-more-hor font-size-md"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                                        <!--begin::Navigation-->
                                    <ul class="navi navi-hover py-1">

                                         <li class="navi-item">
                                            <a href="dashboard.php?module=equipment&page=equipment-print&id=<?php echo $oid_enc;?>&act=<?php echo base64_encode('view');?>" class="navi-link">
                                                <span class="navi-icon"><i class="fas fa-clipboard-list"></i></span>
                                                <span class="navi-text">รายละเอียด</span>
                                            </a>
                                        </li>

                                        <li class="navi-item">
                                            <a href="dashboard.php?module=equipment&page=equipment-add&id=<?php echo $oid_enc;?>&act=<?php echo base64_encode('edit');?>" class="navi-link">
                                                <span class="navi-icon"><i class="fas fa-edit"></i></span>
                                                <span class="navi-text">แก้ไข</span>
                                            </a>
                                        </li>

                                        <li class="navi-item">
                                            <a href="core/equipment/equipment-gen-qrcode.php?oid=<?php echo $oid_enc;?>&eq_code=<?php echo $eq_code;?>&act=<?php echo base64_encode('genqrcode');?>" class="navi-link">
                                                <span class="navi-icon"><i class="fas fa-qrcode"></i></span>
                                                <span class="navi-text">สร้าง qrcode ใหม่</span>
                                            </a>
                                        </li>



                                        <li class="navi-separator my-3"></li>

                                        <li class="navi-item">
                                            <a href="#" class="navi-link" onclick='confirm_eq_delete(<?php echo $oid; ?>)'>
                                                <span class="navi-icon"><i class="fas fa-trash"></i></span>
                                                <span class="navi-text">ลบ</span>
                                            </a>
                                        </li> 
                                    </ul>
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
  

<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div class="d-flex flex-wrap py-2 mr-3">


<?php 
$p = 4;	//	กำหนดช่วงตัวเลขทางซ้าย และ ขวา ของหน้าที่ถูกเลือก
$Prev_Page = $pagenum-1;
$Next_Page = $pagenum+1;
$page_link = "dashboard.php?module=$module&page=main&search=$search&eq_typeid=$eq_typeid&pagenum";

if($pagenum==1)		//	กรณีอยู่หน้า 1 หรือยังไม่เลือกหน้า
{
    echo "<a class='btn btn-icon btn-sm border-0 btn-hover-primary active mr-2 my-1' href='$page_link=$pagenum'>$pagenum</a>";
            for($a=1;$a<=$p;$a++)
            {
                    if($pagenum+$a<$Num_Pages)
                    {
                    echo  "<a class='btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1' href='$page_link=".($pagenum+$a)."' >".($pagenum+$a)."</a>";
                    }
            }
    if($Num_Pages==2)
    {
        echo "<a class='btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1' href ='$page_link=$Num_Pages'>$Num_Pages</a> ";		// แสดงหน้าสุดท้าย
        echo " <a class='btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1' href ='$page_link=$Next_Page' title='หน้าถัดไป'><i class='ki ki-bold-arrow-next icon-xs'></i></a> ";		//	แสดงเครื่องหมาย >>
    }
}
else		// กรณีอยู่หน้าอื่นๆ
{
    echo "<a  class='btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1' href ='$page_link=$Prev_Page' title='หน้าก่อนหน้า'><i class='ki ki-bold-arrow-back icon-xs'></i></a> ";		//	แสดงเครื่องหมาย <<
    echo "<a  class='btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1' href ='$page_link=1'>1</a> ";		//	แสดงหมายเลข 1
}

if($pagenum==2 )	//	ถ้าอยู่หน้า 2
{
    echo "<a  class='btn btn-icon btn-sm border-0 btn-hover-primary active mr-2 my-1' href='$page_link=$pagenum'>$pagenum</a>";
        for($a=1;$a<=$p;$a++)
        {
            if($pagenum+$a<$Num_Pages)
            {		
                echo  "<a  class='btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1' href='$page_link=".($pagenum+$a)."' >".($pagenum+$a)."</a>";
            }
        }		
}

if($pagenum>2 && $pagenum<$Num_Pages)	//	ถ้าอยู่หน้ามากกว่า 2 แต่น้อยกว่าหน้าสุดท้าย
{
    for($a=$p;$a>=1;$a--)		//	หา $p หน้าด้านซ้าย
    {
        if($pagenum-$a>1)	//	$p หน้าด้านซ้ายต้องมากกว่า 1
        {
            echo  "<a  class='btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1' href='$page_link=".($pagenum-$a)." '>".($pagenum-$a)."</a>";
        }
    }
    echo "<a  class='btn btn-icon btn-sm border-0 btn-hover-primary active mr-2 my-1' href='$page_link=$pagenum'>$pagenum</a>";		//	หน้าปัจจุบัน
    for($a=1;$a<=$p;$a++)		//	หา $p หน้าด้านขวา
    {
        if($pagenum+$a<$Num_Pages)		//	$p หน้าด้านขวาต้องน้อยกว่าหน้าสุดท้าย
        {
            echo  "<a  class='btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1' href='$page_link=".($pagenum+$a)."' >".($pagenum+$a)."</a>";
        }
    }
}

if($pagenum==$Num_Pages && $Num_Pages!=1 && $Num_Pages!=2)		//	ถ้าเลือกหน้าสุดท้าย
{
    for($a=$p;$a>=1;$a--)		//	หา $p หน้าด้านซ้าย
    {
        if($pagenum-$a>1)
        {
        echo  "<a  class='btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1' href='$page_link=".($pagenum-$a)." '>".($pagenum-$a)."</a>";
        }
    }
    echo "<a  class='btn btn-icon btn-sm border-0 btn-hover-primary active mr-2 my-1' href='$page_link=$pagenum'>$pagenum</a>";		//	แสดงหน้าปัจจุบัน
}

else if($Num_Pages!=1 && $Num_Pages!=2)	//	กรณีไม่ได้เลือกหน้าสุดท้าย
{
    echo "<a  class='btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1' href ='$page_link=$Num_Pages'>$Num_Pages</a> ";		// แสดงหน้าสุดท้าย
    echo " <a  class='btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1' href ='$page_link=$Next_Page' title='หน้าถัดไป'><i class='ki ki-bold-arrow-next icon-xs'></i></a> ";		//	แสดงเครื่องหมาย >>
}


?>



    </div>

    <div class="d-flex align-items-center py-3">
        <span class="text-muted">หน้า <?php echo $pagenum;?> / <?php echo $last;?> </span>
    </div>
</div>


            
<?php
					} // end if
					?>
		
	</div>
	<div class="card-footer">
		<div class="row">
			
		</div>
	</div>


</div>
		<!--end::Card-->



<script>

function confirm_eq_delete(id) {
                    Swal.fire({
                        title: 'แน่ใจนะ?',
                        text: "ต้องการลบข้อมูล",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        cancelButtonText: 'ยกเลิก',
                        confirmButtonText: 'ใช่, ต้องการลบข้อมูล !'
                    }).then((result) => {
                        if (result.value) { //Yes
                            $.post("core/equipment/equipment-del.php", {id: id}, function(result){
                                //  $("test").html(result);
                                // console.log(result.code);
                                location.reload();
                            });
                        }
                    })
            }


</script>

