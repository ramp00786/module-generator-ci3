
function getAlert(type, icon, heading, msg) {
	return '<div id="alert" class="alert alert-'+type+' alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="icon '+icon+'"></i> '+heading+'</h4>'+msg+'</div>';
}

function saveAndNext(){
	var module_name = $('#module_name').val();
	var slug = $('#slug').val();
	if(module_name == ''){
		var alert = getAlert('danger', 'fa fa-warning', 'Required', "'Module Name' is mandatory");
		$('#resp').html(alert);
		$('#for_module_name').css('color', '#dd4b39');
		$('#module_name').css('border-color', '#dd4b39');
	}
	else if(slug == ''){
		$('#for_module_name').css('color', '#00a65a');
		$('#module_name').css('border-color', '#00a65a');
		$('#module_name').attr('readonly', 'readonly');
		var alert = getAlert('danger', 'fa fa-warning', 'Required', "'Slug' is mandatory");
		$('#resp').html(alert);
		$('#for_slug').css('color', '#dd4b39');
		$('#slug').css('border-color', '#dd4b39');
	}
	else{
		var url = base_url+'Modules/old_check';
		$.post(url, {module_name: module_name, slug:slug}, function(res){
			if(res == 'ok'){
				$('#alert').remove();
				$('#for_module_name').css('color', '#00a65a');
				$('#module_name').css('border-color', '#00a65a');
				$('#module_name').attr('readonly', 'readonly');

				$('#for_slug').css('color', '#00a65a');
				$('#slug').css('border-color', '#00a65a');
				$('#slug').attr('readonly', 'readonly');
				
				addField();
				$('#next_btn').html('<button type="button" class="btn btn-success pull-right" onclick="addField()">Add More Input</button><div class="clearfix"></div>');
				$('#fotter_btns').css('display', 'block')
			}
			else{
				var alert = getAlert('danger', 'fa fa-warning', 'Already exists.!', "'Module Name = "+module_name+" ' is already exists");
				$('#resp').html(alert);
				$('#for_module_name').css('color', '#dd4b39');
				$('#module_name').css('border-color', '#dd4b39');
			}			
		});
	}
}

function addField(type = ''){

	if(type !=''){
		//---Field count--
		var field_count = $('#field_count_'+type).val();
		var new_field_count = Number(field_count)+1;
		$('#field_count_'+type).val(new_field_count);
		//---Field count--

		var url = base_url+'Modules/new_field';
		$.post(url, {field_count:new_field_count}, function(res){
			$('#fields_data_'+type).append(res);
		});
	}
	else{
		//---Field count--
		var field_count = $('#field_count').val();
		var new_field_count = Number(field_count)+1;
		$('#field_count').val(new_field_count);
		//---Field count--

		var url = base_url+'Modules/new_field';
		$.post(url, {field_count:new_field_count}, function(res){
			$('#fields_data').append(res);
		});
	}
	
}

function addAtr(input_row_id) {
	
	//---child_count--
	var child_count = $('#field_count_'+input_row_id+'_child_count').val();
	var new_child_count = Number(child_count)+1;
	$('#field_count_'+input_row_id+'_child_count').val(new_child_count);
	//---child_count--
	var url = base_url+'Modules/add_attr';
	$.post(url, {input_row_id:input_row_id, child_count:new_child_count}, function(res){
		$('#more_atr_'+input_row_id).append(res);
		$('#more_atr_'+input_row_id).focus();
		
				
	});
}

function remAtr(input_row_id, child_id) {	
	$('#field_'+input_row_id+'_child_'+child_id).remove();
}

function parentRemove(input_row_id) {	
	$('#row_'+input_row_id).remove();
}


function getNextFld(input_type, field_count) {
	if(input_type == 'SELECT-BOX'){
		var url = base_url+'Modules/select_box_op';
		$.post(url, {field_count:field_count}, function(res){
			$('#select_box_inner_'+field_count).html(res);
		});
		
	}
	else{
		$('#select_box_inner_'+field_count).html('');
	}
}

function selectBoxOptions(type, field_count) {
	if(type == 'Database'){
		var url = base_url+'Modules/select_box_db';
	}
	else{
		var url = base_url+'Modules/select_box_op_only';
	}	
	$.post(url, {field_count:field_count}, function(res){
		$('#select_db_info_'+field_count).html(res);
	});
}


function getDbTableFields(table_name, field_count) {
	var url = base_url+'Modules/get_columns_from_table';
	$.post(url, {table_name:table_name}, function(res){
		$('#select_box_table_column_'+field_count).html(res);
	});
}

function addOp(field_count) {

	var op_count = $('#field_count_'+field_count+'_option_count').val();
	var new_op_count = Number(op_count)+1;
	$('#field_count_'+field_count+'_option_count').val(new_op_count);

	var url = base_url+'Modules/select_box_op_only_count';
	$.post(url, {field_count:field_count, op_count:new_op_count}, function(res){
		$('#select_db_info_'+field_count).append(res);
	});
	
}

function removeOp(field_count, op_count) {
	$('#field_'+field_count+'_option_'+op_count).remove();
}

