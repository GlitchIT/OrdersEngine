$(document).ready(function(){

	$('#add-orders-form').submit(function(e){
		e.preventDefault();
		
		$.ajax({
			method: "POST",
			data: $('#add-orders-form').serialize(),
			dataType:"json"
		}).done(function(data){
			if(data.error){
				UIkit.notify(data.error,{status:'danger'});
			}else if(data.success){
				$('#add-orders-form').hide();
				UIkit.notify(data.success,{status:'success'});
				refreshOrders();
			}
		});
	});

	setTriggers();


});

function refreshOrders(){
	$('#main-cont').hide('600').html('');
	$.ajax({
		method:"POST",
		data:{
			action:"getAllOrders"
		}
	}).done(function(data){
		$('#main-cont').html(data).show('600');
		setTriggers();
	});
}

function setTriggers(){

	$('.payDeposit').click(function(){
		var id = $(this).attr('data-orders-id');
		$.ajax({
			method:"POST",
			data:{
				action:"payDeposit",
				ID:id
			},
			dataType:"json"
		}).done(function(data){
			if(data.error){
				UIkit.notify(data.error,{status:'danger'});
			}else if(data.success){
				UIkit.notify(data.success,{status:'success'});
				refreshOrders();
			}
		});
	});

	$('.payTotal').click(function(){
		var id = $(this).attr('data-orders-id');
		$.ajax({
			method:"POST",
			data:{
				action:"payTotal",
				ID:id
			},
			dataType:"json"
		}).done(function(data){
			if(data.error){
				UIkit.notify(data.error,{status:'danger'});
			}else if(data.success){
				UIkit.notify(data.success,{status:'success'});
				refreshOrders();
			}
		});
	});
}