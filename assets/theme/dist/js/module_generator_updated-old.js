

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
		var url = base_url+ci_controller+'/old_check';
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

		var url = base_url+ci_controller+'/new_field';
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

		var url = base_url+ci_controller+'/new_field';
		$.post(url, {field_count:new_field_count}, function(res){
			$('#fields_data').append(res);
		});
	}
	
}









function addFieldAtr(input_row_id) {
	
	//---child_count--
	var child_count = $('#field_count_'+input_row_id+'_child_count').val();
	var new_child_count = Number(child_count)+1;
	$('#field_count_'+input_row_id+'_child_count').val(new_child_count);
	//---child_count--
	var url = base_url+ci_controller+'/add_attr';
	$.post(url, {input_row_id:input_row_id, child_count:new_child_count}, function(res){
		
		$('#more_atr_'+input_row_id).append(res);
		$('#more_atr_'+input_row_id).focus();
		
				
	});
}

function remFieldAtr(input_row_id, child_id) {	
	$('#field_'+input_row_id+'_child_'+child_id).remove();
}

function parentRemove(input_row_id) {	
	$('#row_'+input_row_id).remove();
}


function getNextFld(input_type, field_count) {
	if(input_type == 'SELECT-BOX'){
		var url = base_url+ci_controller+'/select_box_op';
		$.post(url, {field_count:field_count}, function(res){
			$('#select_box_inner_'+field_count).html(res);
		});
		
	}
	else{
		$('#select_box_inner_'+field_count).html('');
	}
}


function getNextFldDB(input_type, field_count, ch = ''){

	if(input_type == 'SELECT-BOX'){
		var url = base_url+ci_controller+'/select_box_op_db';
		$.post(url, {field_count:field_count}, function(res){
			if(ch !=''){
				$('#select_box_inner_'+field_count+'_ch_'+ch).html(res);
			}
			else{
				$('#select_box_inner_'+field_count).html(res);
			}
			
		});
		
	}
	else if(input_type == 'CHECKBOX'){
		var url = base_url+ci_controller+'/select_box_check_db';
		$.post(url, {field_count:field_count}, function(res){
			if(ch !=''){
				$('#select_box_inner_'+field_count+'_ch_'+ch).html(res);
			}
			else{
				$('#select_box_inner_'+field_count).html(res);
			}
		});
	}
	else if(input_type == 'RADIO'){
		var url = base_url+ci_controller+'/select_box_radio_db';
		$.post(url, {field_count:field_count}, function(res){
			if(ch !=''){
				$('#select_box_inner_'+field_count+'_ch_'+ch).html(res);
			}
			else{
				$('#select_box_inner_'+field_count).html(res);
			}
		});
	}
}

function selectBoxOptions(type, field_count) {
	if(type == 'Database'){
		var url = base_url+ci_controller+'/select_box_db';
	}
	else{
		var url = base_url+ci_controller+'/select_box_op_only';
	}	
	$.post(url, {field_count:field_count}, function(res){
		$('#select_db_info_'+field_count).html(res);
	});
}


function getDbTableFields(table_name, field_count) {
	var url = base_url+ci_controller+'/get_columns_from_table';
	$.post(url, {table_name:table_name}, function(res){
		$('#select_box_table_column_'+field_count).html(res);
	});
}

function addOp(field_count) {

	var op_count = $('#field_count_'+field_count+'_option_count').val();
	var new_op_count = Number(op_count)+1;
	$('#field_count_'+field_count+'_option_count').val(new_op_count);

	var url = base_url+ci_controller+'/select_box_op_only_count';
	$.post(url, {field_count:field_count, op_count:new_op_count}, function(res){
		$('#select_db_info_'+field_count).append(res);
	});
	
}

function removeOp(field_count, op_count) {
	$('#field_'+field_count+'_option_'+op_count).remove();
}


function getInputType(input_row_type, row_count) {
	if(input_row_type == 'HTML'){
		addField_inner(row_count);
	}
	else{
		addDBField_inner(row_count);
	}
}

function addField_inner(row_count){


	var url = base_url+ci_controller+'/new_field_inner';
	$.post(url, {field_count:row_count}, function(res){
		$('#field_row_main_'+row_count).html(res);
	});
}

function addDBField_inner(row_count){	
	var url = base_url+ci_controller+'/new_db_field_inner';
	$.post(url, {field_count:row_count}, function(res){
		$('#fieldset_inner_'+row_count).html(res);
	});
}


function getDbTableFieldsMain(table_name, field_count, ch) {
	var url = base_url+ci_controller+'/get_columns_from_table';
	$.post(url, {table_name:table_name}, function(res){
		$('#module_column_'+field_count+'_ch_'+ch).html(res);
		$('#module_child_yew_no_'+field_count+'_ch_'+ch).val('No');
		$('#module_child_'+field_count+'_ch_'+ch).html('');
	});
}

function getModuleChild(need_child, field_count, ch) {

	var db_row_pc = $('#db_row_pc_'+field_count).val();
	var db_row_pc_new = Number(db_row_pc)+1;
	
	$('#db_row_pc_'+field_count).val(db_row_pc_new);

	if(need_child == 'Yes'){
		var url = base_url+ci_controller+'/get_child_modules';
		var module_parent = $('#module_name_row_'+field_count+'_ch_'+ch).val();
		$.post(url, {field_count:field_count, ch:db_row_pc_new, module_parent:module_parent}, function(res){		
		$('#module_child_'+field_count+'_ch_'+ch).append(res);

		addAtrCls(db_row_pc_new);
	});
	}
	else{
		$('#module_child_'+field_count+'_ch_'+ch).html('');
	}
}

function fieldsEnable(field_count, ch) {
	if($("#module_enable_"+field_count+'_ch_'+ch).prop('checked') == true){
		addAtr('module_name_row_'+field_count+'_ch_'+ch, 'required', 'required');
		addAtr('module_column_'+field_count+'_ch_'+ch, 'required', 'required');
		addAtr('module_field_type_'+field_count+'_ch_'+ch, 'required', 'required');
		addAtr('module_child_yew_no_'+field_count+'_ch_'+ch, 'required', 'required');
		remAtrCls(ch);
	}
	else{
		remAtr('module_name_row_'+field_count+'_ch_'+ch, 'required', 'required');
		remAtr('module_column_'+field_count+'_ch_'+ch, 'required', 'required');
		remAtr('module_field_type_'+field_count+'_ch_'+ch, 'required', 'required');
		remAtr('module_child_yew_no_'+field_count+'_ch_'+ch, 'required', 'required');
		addAtrCls(ch);
		
	}
}

function addAtr(input_id, attr_name, attr_value) {
	$('#'+ input_id).removeAttr('disabled');	
	$('#'+ input_id).attr(attr_name, attr_value);
}

function addAtrCls(class_name){
	$('.chid_row_'+class_name+' input').attr('disabled', 'disabled');
	$('.chid_row_'+class_name+' select').attr('disabled', 'disabled');
}

function remAtrCls(class_name){
	$('.chid_row_'+class_name+' input').removeAttr('disabled');
	$('.chid_row_'+class_name+' select').removeAttr('disabled');


}

function remAtr(input_id, attr_name) {	
	$('#'+ input_id).removeAttr(attr_name);
	$('#'+ input_id).attr('disabled', 'disabled');
}



function toggleDiv(div_id){
	$('#'+div_id).toggle(1000);
}



$('#add_model').on('hidden.bs.modal', function (e) {
	$("#add_model_body").load(location.href + " #add_model_body");
});






/*---------------Backup------------------*/
function findClosestItem(self_refrence, item_identifier,item_identifier_name, direction, return_type, return_item, put_val, parent_count = 1){

	
	

	//--Count total div in body tag
	var total_div = $('body div').length;
	if(parent_count == total_div){
		$('body').prepend('<input type="hidden" id="nearest_'+item_identifier_name+'" value="Not found">');
		return false;
	}
	//--Count parents of the dom
	if(parent_count == 0){ 
		var count = $(self_refrence).siblings().length;
		for(var i = 0; i < count; i++){
			var self_item = $(self_refrence);
			var callValue = '';
			var return_val = '';
			for(var j = 0; j < i; j++){ 
				if(direction == 'up'){				
					self_item = self_item.prev();
				}
				else if(direction == 'down'){
					self_item = self_item.next();
				}				
			}
			if(direction == 'up'){
				callValue = self_item.prev(item_identifier+item_identifier_name);
			}
			else if(direction == 'down'){
				callValue = self_item.next(item_identifier+item_identifier_name);
			}
			if(return_type == 'get'){
				if(return_item == 'val'){
					return_val = callValue.val();
				}
				else if(return_item == 'html'){
					return_val = callValue.html();
				}
			}
			else if(return_type == 'put'){
				if(return_item == 'val'){
					if(callValue.val() !=null){
						callValue.val(put_val);
						return_val = 'done';						
					}
					
				}
				else if(return_item == 'html'){
					if(callValue.html() !=null){
						callValue.html(put_val);
						return_val = 'done';						
					}
				}
			}	
			if(return_val != null){
				$('body').prepend('<input type="hidden" id="nearest_'+item_identifier_name+'" value="'+return_val+'">');
				return false;
			}
		}
	}
	else{ 
		var par = $(self_refrence);		
		for(k = 1; k < parent_count; k++){
			par = par.parent();
		}

		//--Direction
		if(direction == 'up'){	
			callValue = par.find(item_identifier+item_identifier_name+':last');			
		}
		else if(direction == 'down'){
			callValue = par.find(item_identifier+item_identifier_name+':first');
		}
		//--Check return type
		if(return_type == 'get'){
			if(return_item == 'val'){
				return_val = callValue.val();
			}
			else if(return_item == 'html'){
				return_val = callValue.html();
			}
		}
		else if(return_type == 'put'){
			if(return_item == 'val'){
				if(callValue.val() !=null){
					callValue.val(put_val);
					return_val = 'done';						
				}
				
			}
			else if(return_item == 'html'){				
				if(callValue.prop("tagName") == 'INPUT') {
					var numItems = par.find(item_identifier+item_identifier_name).length;		
					for(var tag = 1; tag <= numItems; tag++){						
						var virtualCallValue = par.find(item_identifier+item_identifier_name+':nth-child('+tag+')');
						if(virtualCallValue.prop("tagName") == 'INPUT'){

						}
						else{							
							virtualCallValue.html(put_val);
							return_val = 'done';
						}
					}
				}
				else{					
					if(callValue.html() !=null){
						callValue.html(put_val);
						//return_val = 'done';						
					}
				}
				
			}
		}		
		if(return_val !== undefined ){
			$('body').prepend('<input type="hidden" id="nearest_'+item_identifier_name+'" value="'+return_val+'">');			
			return false;
		}
	}
	//---Call to self for all parents until find the value
	if(return_val == null){		
		parent_count++;
		findClosestItem(self_refrence, item_identifier,item_identifier_name, direction, return_type, return_item, put_val, parent_count);
	}
}



function getClosestItem(self_refrence, item_identifier,item_identifier_name, direction, return_type, return_item, put_val = ''){
	//--Find closest value -> This function will create hidden input with value 
	//--------------Do not modify it-------------------
	findClosestItem(self_refrence, item_identifier,item_identifier_name, direction, return_type, return_item, put_val);
	//--------------Do not modify it-------------------

	//--Get value from hidden input 
	var input_value = $('#nearest_'+item_identifier_name).val();
	//--Removing hidden input
	$('#nearest_'+item_identifier_name).remove();
	//--Return hidden input value
	return input_value;
}






$('.save_and_next').click(function(e){
	//--Get closest value of the clicked button
	var module_name = nearestElement(this, '.', 'module_name', 'up', 'get', 'val');
	var slug = nearestElement(this, '.', 'slug', 'up', 'get', 'val');
	//--Get closest value of the clicked button
	
	//--Create dynamic url for ajax call
	var url = base_url+ci_controller+'/old_check';
	//--Create dynamic url for ajax call
	
	//--check module name and slug
	$.post(url, {module_name:module_name, slug:slug}, function(res){ 		
		if(res != ''){
			var alert = getAlert('danger', 'fa fa-warning', 'Failed!', res);
			$('#resp').html(alert);
		}
		else{
			$('#resp').html('');
			addField();
		}
	});
});


