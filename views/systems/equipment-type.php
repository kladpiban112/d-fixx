
		<!--begin::Card-->
		<div class="card card-custom gutter-b example example-compact">
			<div class="card-header">
				<h3 class="card-title">
                <i class="fab fa-buffer"></i>&nbsp;ประเภทอุปกรณ์ 
				</h3>
				<div class="card-toolbar">
					<div class="example-tools justify-content-center">
						<a href="dashboard.php?module=systems&page=equipment-type-add" class="btn btn-success btn-sm font-weight-bold mr-2" title="เพิ่มประเภทอุปกรณ์"><i class="fa fa-plus-circle" title="เพิ่มประเภทอุปกรณ์" data-toggle="tooltip"></i>เพิ่มประเภทอุปกรณ์</a>
					</div>
				</div>
			</div>



	<div class="card-body">

    <?php
error_reporting(0);

    $numb_data = $conn->query("SELECT count(1) FROM ".DB_PREFIX."equipment_type u  ")->fetchColumn();

  
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
        $stmt_data = $conn->prepare ("SELECT u.* FROM ".DB_PREFIX."equipment_type u  $max");
        $stmt_data->execute();		


    ?>



<div class="table-responsive">
	<table class="table table-bordered table-hover table-strip" id="tbData" style="margin-top: 13px !important">
    <thead>
    <tr>
                        <th class="text-center">ลำดับ</th>
                        <th>ประเภท</th>
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
                $eq_typeid = $row['eq_typeid'];
                $eq_typeid_enc = base64_encode($row['eq_typeid']);
                $eq_typename = $row['eq_typename'];
                $flag = $row['flag'];
                

                ?>
                <tr>
                            <td class="text-center" width="100px"><?php echo $i;?></td>
                            
                            <td><?php echo $eq_typename;?></td>
                            <td class="text-center" width="100px">
                            <?php if($flag == '1'){?>
                                <span class="label label-lg label-light-success label-inline">เปิดใช้งาน</span>
                            <?php }else if($flag == '0'){?>
                                <span class="label label-lg label-light-danger label-inline">ปิดใช้งาน</span>
                                <?php }else{}?>
                            
                            </td>
                            <td class="text-center" width="100px">
                            <!--begin::Dropdown-->
                                <div class="dropdown">
                                    <a href="#" class="btn btn-clean btn-icon" data-toggle="dropdown">
                                        <i class="ki ki-bold-more-hor font-size-md"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                                        <!--begin::Navigation-->
                                    <ul class="navi navi-hover py-1">
                                        <li class="navi-item">
                                            <a href="dashboard.php?module=<?php echo $module;?>&page=equipment-type-add&eqtypeid=<?php echo $eq_typeid_enc;?>&act=<?php echo base64_encode('edit');?>" class="navi-link">
                                                <span class="navi-icon"><i class="fas fa-edit"></i></span>
                                                <span class="navi-text">แก้ไข</span>
                                            </a>
                                        </li>

                                        <!--<li class="navi-separator my-3"></li>

                                        <li class="navi-item">
                                            <a href="#" class="navi-link">
                                                <span class="navi-icon"><i class="fas fa-trash"></i></span>
                                                <span class="navi-text">ลบ</span>
                                            </a>
                                        </li>  -->
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
$page_link = "dashboard.php?module=$module&page=$page&search=$search&pagenum";

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



