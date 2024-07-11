function getAlert(type, icon, heading, msg) {
	return '<div id="alert" class="alert alert-'+type+' alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="icon '+icon+'"></i> '+heading+'</h4>'+msg+'</div>';
}



function getClosestItem(self_refrence, item_identifier,item_identifier_name, direction, return_type, return_item, parent_count = 1){

	var return_data = 'dddd';
	var total_div = $('body div').length;


	if(parent_count == total_div){
		return false;
	}

	if(parent_count == 1){
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
			if(return_val != null){
				//console.log(return_val);
				return_data = return_val;
				return false;
			}
		}
	}
	else{
		var par = $(self_refrence);
		
		for(k = 1; k < parent_count; k++){
			par = par.parent();
		}

		if(direction == 'up'){	
			callValue = par.find(item_identifier+item_identifier_name+':last');
		}
		else if(direction == 'down'){
			callValue = par.find(item_identifier+item_identifier_name+':first');
		}

		if(return_type == 'get'){
			if(return_item == 'val'){
				return_val = callValue.val();
			}
			else if(return_item == 'html'){
				return_val = callValue.html();
			}
		}
		if(return_val != null){			
			console.log(return_val);
			return_data = return_val;
			return false;
		}

	}	
	if(return_val == null){
		parent_count++;
		getClosestItem(self_refrence, item_identifier,item_identifier_name, direction, return_type, return_item, parent_count);
	}
	else{

	}

	return return_data;


}



$('.save_and_next').click(function(e){

	var input_value = getClosestItem(this, '.', 'module_name', 'up', 'get', 'val');

	console.log(input_value);

	/*var module_name = $(this).parent().siblings('.form-group').find('.module_name').val();
	var slug = $(this).parent().siblings('.form-group').find('.slug').val();
	var url = base_url+ci_controller+'/old_check';

	$.post(url, {module_name:module_name}, function(res){ 
		
		if(res != 'ok'){
			var alert = getAlert('danger', 'fa fa-warning', 'Already exists.!', "'Module Name = "+module_name+" ' is already exists");
			$('#resp').append(alert);
		}
	});


	$.post(url, {slug:slug}, function(res){
		if(res != 'ok'){
			var alert = getAlert('danger', 'fa fa-warning', 'Already exists.!', "'Slug = "+slug+" ' is already exists");
			$('#resp').append(alert);
		}
	});*/

});



$('#add_model').on('hidden.bs.modal', function (e) {
	$("#add_model_body").load(location.href + " #add_model_body");
});

