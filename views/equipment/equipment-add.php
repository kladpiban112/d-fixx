<?php
error_reporting(0);

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
$act = filter_input(INPUT_GET, 'act', FILTER_SANITIZE_STRING);
$oid = base64_decode($id);
$action = base64_decode($act);
if($action == "edit"){
	$txt_title = "แก้ไข";
	$action = $action;

	$stmt_data = $conn->prepare ("SELECT u.*,o.org_name FROM ".DB_PREFIX."equipment_main u 
	LEFT JOIN ".DB_PREFIX."org_main o ON u.org_id = o.org_id 
	WHERE u.oid = '$oid' AND u.flag != '0'  LIMIT 1");
	$stmt_data->execute();	
	$row_data = $stmt_data->fetch(PDO::FETCH_ASSOC);


}else{
	$txt_title = "เพิ่ม";
	$action = "add";
}
//echo $d = date("Y-m-d");

?>
<script src="https://kit.fontawesome.com/09f98e4a32.js" crossorigin="anonymous"></script>
		<!--begin::Card-->
		<div class="card card-custom gutter-b example example-compact">
			<div class="card-header">
				<h3 class="card-title">
        <i class="fa-solid fa-box"></i>&nbsp;<?php echo $txt_title;?>อุปกรณ์ 
				</h3>
				<div class="card-toolbar">
					<div class="example-tools justify-content-center">
						<a href="dashboard.php?module=<?php echo $module;?>" class="btn btn-defalse btn-sm font-weight-bold mr-2" title="ย้อนกลับ"><i class="fa fa-chevron-left" title="ย้อนกลับ" ></i></a>
					</div>
				</div>
			</div>


<form class="form" enctype="multipart/form-data" >
<input type="hidden" class="form-control"  name="act" id="act" value="<?php echo $action;?>"/>
<input type="hidden" class="form-control"  name="oid" id="oid" value="<?php echo $oid;?>"/>
	<div class="card-body">

	
	<div class="row">
	<div class="col-lg-9">

		<div class="form-group row">
			<div class="col-lg-3">
				<label>รหัสอุปกรณ์</label>
				<input type="text" class="form-control"  name="eq_codes" id="eq_codes" placeholder="ระบบจะสร้างรหัสให้อัติโนมัติ" value="<?php echo $row_data['eq_codes'];?>" readonly="true"/>
				<span class="form-text text-muted">ระบบจะสร้างรหัสให้อัติโนมัติ</span>
			</div>

      <div class="col-lg-3">
				<label>รหัสครุภัณฑ์</label>
				<input type="text" class="form-control"  name="eq_number" id="eq_number" placeholder="รหัสครุภัณฑ์" value="<?php echo $row_data['eq_number'];?>"/>
				<span class="form-text text-muted">รหัสครุภัณฑ์จากกองทุน</span>
			</div>

      </div>


      <div class="form-group row">
			<div class="col-lg-3">
				<label>ประเภทอุปกรณ์</label>
				<select class="form-control " name="eq_typeid" id="eq_typeid">
                    <option value="">ระบุ</option>
                    <?php
					if($logged_user_role_id == '1'){
						$conditions = " ";
					}else{
						$conditions = " AND org_id = '$logged_org_id' ";
					}
					$stmt_user_role = $conn->prepare("SELECT * FROM ".DB_PREFIX."equipment_type WHERE flag = '1'  ORDER BY eq_order ASC");
					$stmt_user_role->execute();		
					while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC))
						{
						$id_selected = $row['eq_typeid'];
						$title_selected = stripslashes($row['eq_typename']);
						?>
						<option value="<?php echo $id_selected;?>" <?php if($row_data['eq_typeid'] == $id_selected ){echo "selected";} ?>><?php echo $title_selected;?></option>
						<?php
						}
					?>
				</select>
				
			</div>

      <div class="col-lg-3">
				<label>ระบุ</label>
				<input type="text" class="form-control"  name="eq_typeother" id="eq_typeother" placeholder="ระบุอื่นๆ" value="<?php echo $row_data['eq_typeother'];?>"/>
				
			</div>

			<div class="col-lg-4">
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
						$title_selected = stripslashes($row['org_shortname']);
						?>
						<option value="<?php echo $id_selected;?>" <?php if($row_data['org_id'] == $id_selected ){echo "selected";} ?>><?php echo $title_selected;?></option>
						<?php
						}
					?>
				</select>
			</div>
			
		</div>


		<div class="form-group row">
			<div class="col-lg-12">
				<label>ชื่ออุปกรณ์</label>
				<input type="text" class="form-control"  name="eq_names" id="eq_names" placeholder="ชื่ออุปกรณ์" value="<?php echo $row_data['eq_names'];?>"/>
				<span class="form-text text-muted"></span>
			</div>
		</div>


    <div class="form-group row">
			

			<div class="col-lg-2">
				<label>วันที่รับ</label>
				<input type="text" class="form-control"  name="receive_date" id="receive_date" placeholder="วันที่รับ" value="<?php echo date_db_2form($row_data['receive_date']);?>"   data-date-language="th-th" maxlength="10" />
				
			</div>

			<div class="col-lg-4">
						<label>ได้รับจาก</label>
						<select class="form-control "  name="receive_type" id="receive_type">
						<?php
					
					$stmt_user_role = $conn->prepare("SELECT * FROM ".DB_PREFIX."receive_type ORDER BY rec_id ASC");
					$stmt_user_role->execute();		
					while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC))
						{
						$id_selected = $row['rec_id'];
						$title_selected = stripslashes($row['rec_title']);
						?>
						<option value="<?php echo $id_selected;?>" <?php if($row_data['receive_id'] == $id_selected ){echo "selected";} ?>><?php echo $title_selected;?></option>
						<?php
						}
					?>
						</select>
					</div>

          <div class="col-lg-6">
				<label>ระบุ(หน่วยงานที่รับ)</label>
				<input type="text" class="form-control"  name="receive_typeother" id="receive_typeother" placeholder="ระบุอื่นๆ" value="<?php echo $row_data['receive_other'];?>"/>
				
			</div>
			
		</div>

		<div class="form-group row">
			<div class="col-lg-12">
				<label>รายละเอียด</label>
				<!--<textarea class="form-control" name="kt-ckeditor-1" id="kt-ckeditor-1" row="8" ><?php echo $row_data['eq_desc'];?></textarea>-->
				<textarea rows="" class="form-control editor" name="eq_desc" id="eq_desc"><?php echo $row_data['eq_desc'];?></textarea>
			</div>
			
		</div>

		<hr>

				<div class="form-group row">
					<div class="col-lg-12">
						<label>รูปถ่ายอุปกรณ์(เพิ่มเติม)</label>
						<input type="file" name="files[]" id="filer_example2" class="form-control " multiple="multiple">
						<span class="form-text text-muted">.jpg .png เท่านั้น</span>
					</div>
				</div>

				<div class="form-group row">

				<?php 

									$sql_files = "SELECT * FROM ".DB_PREFIX."equipment_files WHERE oid = '$oid' AND file_status = '1' ORDER BY file_id ASC ";
									$stmt_files = $conn->prepare ($sql_files);
									$stmt_files->execute();
																	
									while ($row_files = $stmt_files->fetch(PDO::FETCH_ASSOC)){
                    $file_id = $row_files['file_id'];
										$file_name = $row_files['file_name'];
										?>

					<div class="col-lg-3">
					
					<div class="symbol symbol-150 mr-3">
					<img src="uploads/equipment/<?php echo $file_name;?>" alt="image" class="img img-responsive"/>
					<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="ลบรูปภาพ">
                                    <a href="#" onclick='confirm_delete(<?php echo $file_id; ?>)'><i class="ki ki-bold-close icon-xs text-muted"></i></a>
                                </span>
    </div>
					</div>

					<?php } 
									?>


				</div>



		</div><!--col-->

		
		<div class="col-lg-3 border-x-0 border-x-md border-y border-y-md-0">

		        <div class="form-group row">
					<div class="col-lg-12">
					
								<div class="symbol symbol-50 symbol-lg-150 ">
									<?php if($row_data['eq_img'] == ""){?>
										<img src="uploads/no-image.jpg" alt="image"/>
											<?php }else{?>
										<img src="uploads/equipment/<?php echo $row_data['eq_img'];?>" alt="image"/>
                                	<?php   } ?>
									<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove">
                                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                                </span>
								</div>	
					</div>
				</div>


				<div class="form-group row">
					<div class="col-lg-12">
						<label>รูปถ่ายอุปกรณ์</label>
						<input type="file" class="form-control"  name="img_profile" id="img_profile" placeholder="รูปโปรไฟล์"/>
						<span class="form-text text-muted">.jpg .png เท่านั้น</span>
					</div>
				</div>

				
					<div class="form-group row">
					<div class="col-lg-12">
						<label>สถานะใช้งาน</label>
						<select class="form-control "  name="eq_status" id="eq_status">
							<?php
					
					$stmt_user_role = $conn->prepare("SELECT * FROM ".DB_PREFIX."equipment_status ORDER BY status_id ASC");
					$stmt_user_role->execute();		
					while ($row = $stmt_user_role->fetch(PDO::FETCH_ASSOC))
						{
						$id_selected = $row['status_id'];
						$title_selected = stripslashes($row['status_title']);
						?>
						<option value="<?php echo $id_selected;?>" <?php if($row_data['flag'] == $id_selected ){echo "selected";} ?>><?php echo $title_selected;?></option>
						<?php
						}
					?>


						</select>
					</div>
				</div>


				

				
				</div>


		</div><!--col-->
		</div><!--row-->



	<div class="card-footer">
		<div class="row">
			<div class="col-lg-6">
				<button type="button" class="btn btn-primary mr-2" id="btnSaveEquipment"><i class="fa fa-save" title="ย้อนกลับ" ></i> บันทึก</button>
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

$(document).ready(function() {


    var eq_typeid = $("#eq_typeid").val();
    if(eq_typeid == '1' ){
        
    }else{
      $("#eq_typeother").attr('disabled','disabled');
        //$("#eq_typeother").val('');
    }

    $('#eq_typeid').change(function(e){
        e.preventDefault();
        var eq_typeid = $("#eq_typeid").val();

        if(eq_typeid == '1' ){
          $('#eq_typeother').prop('disabled', false);
        }else{
            
            $("#eq_typeother").attr('disabled','disabled');
            //$("#eq_typeother").val('');
        }
  
      }); //

      var receive_type = $("#receive_type").val();
    if(receive_type == '4' || receive_type == '5'  ){
        
    }else{
      $("#receive_typeother").attr('disabled','disabled');
        //$("#eq_typeother").val('');
    }

    $('#receive_type').change(function(e){
        e.preventDefault();
        var receive_type = $("#receive_type").val();

        if(receive_type == '4' || receive_type == '5' ){
          $('#receive_typeother').prop('disabled', false);
        }else{
            
            $("#receive_typeother").attr('disabled','disabled');
            //$("#eq_typeother").val('');
        }
  
      }); //


});



$('#receive_date').datepicker({
        autoclose: true
});


 //Example 2
 $('#filer_example2').filer({
        limit: 5,
        maxSize: 10,
        //extensions: ['jpg', 'jpeg', 'png', 'gif','pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar'],
		extensions: ['jpg', 'jpeg', 'png', 'gif','pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar'],
        changeInput: true,
        showThumbs: true,
        addMore: true
    });


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
						['noembed'],
						['foreColor', 'backColor'],
						['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
					],
							
				plugins: {
						upload: {
							serverPath: '<?php echo ADMIN_URL;?>/assets/plugins/trumbowyg/texteditor-upload.php',
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
            },
            da: {
              table: 'Indsæt tabel',
              tableAddRow: 'Tilføj række',
              tableAddColumn: 'Tilføj kolonne',
              tableDeleteRow: 'Slet række',
              tableDeleteColumn: 'Slet kolonne',
              tableDestroy: 'Slet tabel',
              error: 'Fejl'
            },
            de: {
              table: 'Tabelle einfügen',
              tableAddRow: 'Zeile hinzufügen',
              tableAddColumn: 'Spalte hinzufügen',
              tableDeleteRow: 'Zeile löschen',
              tableDeleteColumn: 'Spalte löschen',
              tableDestroy: 'Tabelle löschen',
              error: 'Error'
            },
            sk: {
                table: 'Vytvoriť tabuľky',
                tableAddRow: 'Pridať riadok',
                tableAddColumn: 'Pridať stĺpec',
                error: 'Chyba'
            },
            fr: {
                table: 'Insérer un tableau',
                tableAddRow: 'Ajouter des lignes',
                tableAddColumn: 'Ajouter des colonnes',
                tableDeleteRow: 'Effacer la ligne',
                tableDeleteColumn: 'Effacer la colonne',
                tableDestroy: 'Effacer le tableau',
                error: 'Erreur'
            },
            cs: {
                table: 'Vytvořit příkaz Table',
                tableAddRow: 'Přidat řádek',
                tableAddColumn: 'Přidat sloupec',
                error: 'Chyba'
            },
            ru: {
                table: 'Вставить таблицу',
                tableAddRow: 'Добавить строку',
                tableAddColumn: 'Добавить столбец',
                tableDeleteRow: 'Удалить строку',
                tableDeleteColumn: 'Удалить столбец',
                tableDestroy: 'Удалить таблицу',
                error: 'Ошибка'
            },
            ja: {
                table: '表の挿入',
                tableAddRow: '行の追加',
                tableAddColumn: '列の追加',
                error: 'エラー'
            },
            tr: {
                table: 'Tablo ekle',
                tableAddRow: 'Satır ekle',
                tableAddColumn: 'Kolon ekle',
                error: 'Hata'
            },
            zh_tw: {
              table: '插入表格',
              tableAddRow: '加入行',
              tableAddColumn: '加入列',
              tableDeleteRow: '刪除行',
              tableDeleteColumn: '刪除列',
              tableDestroy: '刪除表格',
              error: '錯誤'
            },
            id: {
                table: 'Sisipkan tabel',
                tableAddRow: 'Sisipkan baris',
                tableAddColumn: 'Sisipkan kolom',
                tableDeleteRow: 'Hapus baris',
                tableDeleteColumn: 'Hapus kolom',
                tableDestroy: 'Hapus tabel',
                error: 'Galat'
            },
            pt_br: {
                table: 'Inserir tabela',
                tableAddRow: 'Adicionar linha',
                tableAddColumn: 'Adicionar coluna',
                tableDeleteRow: 'Deletar linha',
                tableDeleteColumn: 'Deletar coluna',
                tableDestroy: 'Deletar tabela',
                error: 'Erro'
            }
        },

        plugins: {
            table: {
                init: function (t) {
                    t.o.plugins.table = $.extend(true, {}, defaultOptions, t.o.plugins.table || {});

                    var buildButtonDef = {
                        fn: function () {
                          t.saveRange();

                          var btnName = 'table';

                          var dropdownPrefix = t.o.prefix + 'dropdown',
                              dropdownOptions = { // the dropdown
                              class: dropdownPrefix + '-' + btnName + ' ' + dropdownPrefix + ' ' + t.o.prefix + 'fixed-top'
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
                          if (t.$box.find("." + t.o.prefix + "table-button").hasClass(t.o.prefix + 'active-button')) {
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
                          table.find("tr:nth-of-type("+(i+1)+")").find("td:nth-of-type("+(j+1)+")").addClass('active');
                        }
                      }

                      // set label
                      table.next('center').html((colIndex+1) + "x" + (rowIndex+1));
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

                      fn: function () {
                        t.saveRange();

                        var node = t.doc.getSelection().focusNode;
                        var table = $(node).closest('table');

                        if(table.length > 0) {
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

                      fn: function () {
                        t.saveRange();

                        var node = t.doc.getSelection().focusNode;
                        var table = $(node).closest('table');

                        if(table.length > 0) {
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

                      fn: function () {
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

                      fn: function () {
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

                      fn: function () {
                        t.saveRange();

                        var node = t.doc.getSelection().focusNode,
                            table = $(node).closest('table'),
                            td = $(node).closest('td'),
                            cellIndex = td.index();

                        $(table).find('tr').each(function() {
                          $(this).find('td:eq('+cellIndex+')').remove();
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

		                             
<script>
/*jQuery(document).ready(function() {
   // KTCkeditor.init();
});
var KTCkeditor = function () {
    // Private functions
    var demos = function () {
        ClassicEditor
            .create( document.querySelector( '#kt-ckeditor-1' ) )
            .then( editor => {
                console.log( editor );
            } )
            .catch( error => {
                console.error( error );
            } );
    }

    return {
        // public functions
        init: function() {
            demos();
        }
    };
}(); */


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
                            $.post("core/equipment/equipment-photo-del.php", {id: id}, function(result){
                                //  $("test").html(result);
                                // console.log(result.code);
                                location.reload();
                            });
                        }
                    })
            }


$('#btnSaveEquipment').click(function(e){
        e.preventDefault();
        if ($('#eq_typeid').val().length == ""){
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาระบุประเภทอุปกรณ์',
                    showConfirmButton: false,
                    timer: 1000
                    });
        }else if (($('#eq_typeid').val() == "1") && ($('#eq_typeother').val().length == "")){
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาระบุประเภทอุปกรณ์อื่นๆ',
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
        }else if (($('#receive_type').val() == "4") && ($('#receive_typeother').val().length == "")){
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาระบุหน่วยงานที่รับ',
                    showConfirmButton: false,
                    timer: 1000
                    });
        }else if ($('#eq_name').val().length == ""){
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาระบุชื่อการอุปกรณ์',
                    showConfirmButton: false,
                    timer: 1000
                    });
        }else {

		var data = new FormData(this.form);
        $.ajax({
            type: "POST",
            url: "core/equipment/equipment-add.php",
            dataType: "json",
			data: data,
			processData: false,
            contentType: false,
            /*data: {
                fullname:fullname,
				username:username,
                password:password,
                act:act,
				cid:cid,
				shortname:shortname,
				org_id:org_id,
				user_role:user_role,
				user_status:user_status
                },*/
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
                    window.location.replace("dashboard.php?module=equipment");
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



<script>
$(document).ready(function () {
    'use strict';
	
	// ------------------------------------------------------- //
    // Text editor (WYSIWYG)
    // ------------------------------------------------------ //
	
						
}); 
</script>


                    <!--
					<script src="assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js?v=7.0.6"></script>
                        
                            <script src="assets/js/pages/crud/forms/editors/ckeditor-classic.js?v=7.0.6"></script>
                        -->
