$(".treeview-menu a").on('click', function(event){
	console.log($(this).html());
	$(this).parent().addClass('active');
});
	
