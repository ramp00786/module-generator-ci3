/*
Name: JavaScript and JQuery Library
Work: This will help to generate module
Author: Tulsiram Kushwah
Version: 1.0
Created Date: 12 May 2022


*/

//--Globle Variable
var error_msg = '';
//--Globle Variable


//--This function will return bootstrap alert in html format
function getAlert(type, icon, heading, msg) {
	return '<div id="alert" class="alert alert-'+type+' alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="icon '+icon+'"></i> '+heading+'</h4><span id="alert_msg">'+msg+'</span></div>';
}


function getOffset(el) {
  const rect = el.getBoundingClientRect();
  return {
    left: rect.left + window.scrollX,
    top: rect.top + window.scrollY
  };
}





/*
	--------------------------------------------------------------------------------
	This is function to find closest DOM element and retern variation of result
	Please do not modify it


	Function (nearestElement) arguments explanation

	getClosestItem('1', '2', '3', '4', '5', '6', '7 = optional');

	1 => selector(start point)
	2 => Class/ID (. or #)
	3 => Class/ID name
	4 => Direction to find value (up/down)
	5 => Action (get/put/append/prepend/toggle/remove/..etc)
	6 => val/html
	7 => If param 5 = 'put' then need to pass put_value as argument 7th

	GET example: getClosestItem(this, '.', 'module_name', 'up', 'get', 'val');
	Put example: getClosestItem(this, '.', 'module_name', 'up', 'put', 'val', 'hello');

*/
function nearestElement(self_refrence, item_identifier,item_identifier_name, direction, return_type, return_item, put_val = ''){
	var element_position = $(self_refrence).offset();
	
	var target_position = element_position.top;
	var last_refrence = '';
	
	if(return_type == 'get'){
		var inputsTagsArray = ['input', 'select'];
	}
	else if(return_type == 'put' || return_type == 'append'){
		var inputsTagsArray = ['input'];
	}


	$(item_identifier+item_identifier_name).each(function(element) {
		var tagName = $(this).prop("tagName");
		/*console.log($(this).html());
		console.log($(this).offset().top);*/
		if(return_item == 'val'){
			if(jQuery.inArray(tagName.toLowerCase(), inputsTagsArray) != -1) {
			   var cr_e_pos = $(this).offset().top;				   	    
			   if(direction == 'up'){
			    	if(cr_e_pos < target_position){
				    	last_refrence = $(this);
				    }
				    else{
				    	return false;
				   }
			   }
			   else if(direction == 'down'){
			    	if(cr_e_pos >= target_position){
				    	last_refrence = $(this);
				    	return false;
				    }
				    else{
				    	
				   }
			   }

			    			    
			} else {
			    //console.log("input do nothing");
			}
		}
		else{
			if(jQuery.inArray(tagName.toLowerCase(), inputsTagsArray) != -1) {
			    //console.log("html do nothing");
			} else {

				 var cr_e_pos = $(this).offset().top;
			    if(direction == 'up'){
			    	if(cr_e_pos < target_position){			    		
				    	last_refrence = $(this);
				    }
				    else{				    	
				    	return false;
				   }
			   }
			   else if(direction == 'down'){			   	
			    	if(cr_e_pos >= target_position){
				    	last_refrence = $(this);
				    	return false;
				    }
				    else{
				    	//console.log('next');
				   }
			   }
			} 
		}
	});

	if(last_refrence.length > 0){
		if(return_type == 'get'){
			if(return_item == 'val'){
				//console.log(last_refrence.val());
				return last_refrence.val();
			}
			else if(return_item == 'html'){
				//console.log(last_refrence.html());
				return last_refrence.html();
			}
		}
		else if(return_type == 'put'){
			if(return_item == 'val'){
				//console.log(last_refrence.val(put_val));
				last_refrence.val(put_val);
			}
			else if(return_item == 'html'){
				//console.log(last_refrence.html());
				last_refrence.html(put_val);
			}
		}
		else if(return_type == 'append'){
			if(return_item == 'append'){				
				last_refrence.append(put_val);
			}
		}
	}
	else{
		console.log('Element not found');
	}	
	
}
//---./-------------




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
		var menu_id = $('#menu_name').val();
		var url = base_url+ci_controller+'/old_check';
		$.post(url, {module_name: module_name, slug:slug, menu_id:menu_id}, function(res){
			if(res == ''){
				$('#alert').remove();
				$('#for_module_name').css('color', '#00a65a');
				$('#module_name').css('border-color', '#00a65a');
				$('#module_name').attr('readonly', 'readonly');

				$('#for_slug').css('color', '#00a65a');
				$('#slug').css('border-color', '#00a65a');
				$('#slug').attr('readonly', 'readonly');
				

				
				$('#next_btn').html('<button type="button" class="btn btn-success pull-right add_more_field" onclick="addField($(this))">Add More Input</button><div class="clearfix"></div>');

				//--click here
				$('.add_more_field').click();


				$('#fotter_btns').css('display', 'block')
			}
			else{
				var alert = getAlert('danger', 'fa fa-warning', 'Already exists.!', res);
				$('#resp').html(alert);
				$('#for_module_name').css('color', '#dd4b39');
				$('#module_name').css('border-color', '#dd4b39');
			}			
		});
	}
}








function addField(self){

	//---Field count--
	var field_count = $('#field_count').val();
	var new_field_count = Number(field_count)+1;
	$('#field_count').val(new_field_count);
	//---Field count--

	var url = base_url+ci_controller+'/new_field';
	$.post(url, {field_count:new_field_count}, function(res){
		nearestElement(self, '.', 'fields_data', 'down', 'append', 'append', res);
		//$('#fields_data').append(res);
	});
}

$('body').on('click', '.input_type', function(e){
	var input_row_type = $(this).val();
	var row_count = nearestElement(this, '.', 'this-row', 'down', 'get', 'val');	
	if(input_row_type == 'HTML'){		
		addField_inner($(this), row_count);
	}
	else if(input_row_type == "Database"){			
		addDBField_inner($(this), row_count);
	}
});

function addField_up(self){

	//---Field count--
	var field_count = $('#field_count_up').val();
	var new_field_count = Number(field_count)+1;
	$('#field_count_up').val(new_field_count);
	//---Field count--

	var url = base_url+ci_controller+'/new_field';
	$.post(url, {field_count:new_field_count}, function(res){
		nearestElement(self, '.', 'fields_data', 'down', 'append', 'append', res);
		//$('#fields_data').append(res);
	});
}

$('body').on('click', '.input_type', function(e){
	var input_row_type = $(this).val();
	var row_count = nearestElement(this, '.', 'this-row', 'down', 'get', 'val');	
	if(input_row_type == 'HTML'){		
		addField_inner($(this), row_count);
	}
	else if(input_row_type == "Database"){			
		addDBField_inner($(this), row_count);
	}
});





function addField_inner(self, row_count){
	//---Field count--
	/*var field_count = $('#field_count').val();
	var new_field_count = Number(field_count)+1;
	$('#field_count').val(new_field_count);*/
	//---Field count--


	var url = base_url+ci_controller+'/new_field_inner';
	$.post(url, {field_count:row_count}, function(res){
		nearestElement(self, '.', 'field_row_main', 'down', 'put', 'html', res);		
	});
}


function addDBField_inner(self, row_count){
	
	//---Field count--
	/*var field_count = $('#field_count').val();
	var new_field_count = Number(field_count)+1;
	$('#field_count').val(new_field_count);*/
	//---Field count--


	var url = base_url+ci_controller+'/new_db_field_inner';
	$.post(url, {field_count:row_count}, function(res){
		nearestElement(self, '.', 'field_row_main', 'down', 'put', 'html', res);		
	});
}


function getNextFld(input_type, field_count, self) {
	if(input_type == 'SELECT-BOX'){
		var url = base_url+ci_controller+'/select_box_op';
		$.post(url, {field_count:field_count}, function(res){
			nearestElement(self, '.', 'select_box_inner', 'down', 'put', 'html', res);
		});
		
	}
	else{
		$('#select_box_inner_'+field_count).html('');
	}
}



function getNextFldDBChild(input_type, field_count, self, parent_id){
	if(input_type == 'SELECT-BOX'){
		var url = base_url+ci_controller+'/select_box_op_db_child';
		$.post(url, {field_count:field_count, parent_id: parent_id}, function(res){
			nearestElement(self, '.', 'select_box_inner', 'down', 'put', 'html', res);
		});
		
	}
	else if(input_type == 'CHECKBOX'){
		var url = base_url+ci_controller+'/select_box_check_db_child';
		$.post(url, {field_count:field_count, parent_id: parent_id}, function(res){
			nearestElement(self, '.', 'select_box_inner', 'down', 'put', 'html', res);
		});
	}
	else if(input_type == 'RADIO'){
		var url = base_url+ci_controller+'/select_box_radio_db_child';
		$.post(url, {field_count:field_count, parent_id: parent_id}, function(res){
			nearestElement(self, '.', 'select_box_inner', 'down', 'put', 'html', res);
		});
	}
}



function getNextFldDB(input_type, field_count, self){
	if(input_type == 'SELECT-BOX'){
		var url = base_url+ci_controller+'/select_box_op_db';
		$.post(url, {field_count:field_count}, function(res){
			nearestElement(self, '.', 'select_box_inner', 'down', 'put', 'html', res);
		});
		
	}
	else if(input_type == 'CHECKBOX'){
		var url = base_url+ci_controller+'/select_box_check_db';
		$.post(url, {field_count:field_count}, function(res){
			nearestElement(self, '.', 'select_box_inner', 'down', 'put', 'html', res);
		});
	}
	else if(input_type == 'RADIO'){
		var url = base_url+ci_controller+'/select_box_radio_db';
		$.post(url, {field_count:field_count}, function(res){
			nearestElement(self, '.', 'select_box_inner', 'down', 'put', 'html', res);
		});
	}
}


function getDbTableFieldsMain(table_name, field_count, ch, self) {
	var url = base_url+ci_controller+'/get_columns_from_table';
	$.post(url, {table_name:table_name}, function(res){
		var option = '<option selected>No</option><option>Yes</option>';
		nearestElement(self, '.', 'module_column', 'down', 'put', 'html', res);
		nearestElement(self, '.', 'module_child_yew_no', 'down', 'put', 'html', option);
		nearestElement(self, '.', 'module_child_more', 'down', 'put', 'html', '');

		/*$('#module_column_'+field_count+'_ch_'+ch).html(res);
		$('#module_child_yew_no_'+field_count+'_ch_'+ch).val('No');
		$('#module_child_'+field_count+'_ch_'+ch).html('');*/
	});
}


function getModuleChild(need_child, field_count, ch, self) {

	

	if(need_child == 'Yes'){
		//---Field count--
		var field_count = $('#field_count').val();
		var new_field_count = Number(field_count)+1;
		$('#field_count').val(new_field_count);
		//---Field count--

		var url = base_url+ci_controller+'/get_child_modules';
		var module_parent_name = nearestElement(self, '.', 'field_name', 'up', 'get', 'val');
		var module_parent = nearestElement(self, '.', 'module_name_row', 'up', 'get', 'val');	 
		console.log(module_parent);

		$.post(url, {field_count:new_field_count, module_parent:module_parent, parent_name:module_parent_name, parent_row_num: field_count }, function(res){
			console.log(res);
			console.log(self);			
			nearestElement(self, '.', 'module_child_more', 'down', 'put', 'html', res);	
				
	});
	}
	else{
		nearestElement(self, '.', 'module_child_more', 'down', 'put', 'html', '');
		
	}
}

function disabledAllChild(class_name){
	$('.'+class_name+' *').attr('disabled', 'disabled');
}

function fieldsEnable(row_id, self){
	if($(self).prop('checked') == true){
		$('.chid_row_'+row_id+' *').removeAttr('disabled');
	}
	else{
		disabledAllChild('chid_row_'+row_id);
	}
}

/*function fieldsEnable(field_count, ch) {
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
}*/

function addAtr(input_id, attr_name, attr_value) {
	$('#'+ input_id).removeAttr('disabled');	
	$('#'+ input_id).attr(attr_name, attr_value);
}

/*function addAtrCls(class_name){
	$('.chid_row_'+class_name+' input').attr('disabled', 'disabled');
	$('.chid_row_'+class_name+' select').attr('disabled', 'disabled');
}*/

function remAtrCls(class_name){
	$('.chid_row_'+class_name+' input').removeAttr('disabled');
	$('.chid_row_'+class_name+' select').removeAttr('disabled');


}

function remAtr(input_id, attr_name) {	
	$('#'+ input_id).removeAttr(attr_name);
	$('#'+ input_id).attr('disabled', 'disabled');
}







/*$('body').on('click', '.select_box_type', function(e){

});*/

function selectBoxOptions(type, field_count, self) {
	if(type == 'Database'){
		var url = base_url+ci_controller+'/select_box_db';
	}
	else{
		var url = base_url+ci_controller+'/select_box_op_only';
	}	
	$.post(url, {field_count:field_count}, function(res){
		nearestElement(self, '.', 'select_db_info', 'down', 'put', 'html', res);
	});
}



function addFieldAtr(input_row_id, self) {	
	
	var url = base_url+ci_controller+'/add_attr';
	$.post(url, {input_row_id:input_row_id}, function(res){
		nearestElement(self, '.', 'more_atr', 'down', 'append', 'append', res);
		/*$('#more_atr_'+input_row_id).append(res);
		$('#more_atr_'+input_row_id).focus();*/		
				
	});
}

function remFieldAtr(input_row_id, self) {	
	$(self).parent().parent().remove();
}







$('#add_model').on('hidden.bs.modal', function (e) {
	$("#add_model_body").load(location.href + " #add_model_body");
});




$(document).ajaxStart(function(){
	$('#resp').html('<h3 align="center">Loading...</h3>');	
    //$('#loading_modal').modal('show');
 }).ajaxStop(function(){
    //$('#loading_modal').modal('hide');
 });

 function toggleDiv(parent_row_id, child_class){
	$('.'+parent_row_id+' .'+child_class+':first').toggle(1000);
}


function getDbTableFields(table_name, field_count, self) {
	//console.log($('.select_box_table_column').offset().top);
	//console.log($(self).html());
	var url = base_url+ci_controller+'/get_columns_from_table';
	$.post(url, {table_name:table_name}, function(res){
		nearestElement(self, '.', 'select_box_table_column', 'down', 'put', 'html', res);
	});
}


function parentRemove(input_row_id) {	
	$('#row_'+input_row_id).remove();
}


function addOp(field_count, self) {

	var url = base_url+ci_controller+'/select_box_op_only_count';
	$.post(url, {field_count:field_count}, function(res){
		nearestElement(self, '.', 'select_db_info', 'up', 'append', 'append', res);
		//$('#select_db_info_'+field_count).append(res);
	});
	
}

function removeOp(field_count, self) {
	$(self).parent().parent().remove();
}



function renameChild(name, row_id){
	$('.module_child_class_'+row_id).val(name);
}

function resetUpdateForm(){
	alert();
	//edit_model_body
	//$("#edit_model_body").load(location.href + " #edit_model_body");
	$("#edit_model_body").load(window.location + " #edit_model_body");
}