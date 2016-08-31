<?php

if (!defined('PDO::ATTR_DRIVER_NAME')) {
	die('Unable to connecto to database, please install PDO for php to conitnue.');
}else{
	include_once('db/database.php');
}

$db = new DB();

if(isset($_REQUEST['action'])){
	doAction($_REQUEST['action']);
}else{
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Order Tracker</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/2.26.4/css/uikit.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/2.26.4/css/components/notify.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/2.26.4/css/components/datepicker.min.css">
    <script src="//code.jquery.com/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/uikit/2.26.4/js/uikit.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/uikit/2.26.4/js/components/notify.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/uikit/2.26.4/js/components/datepicker.min.js"></script>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/orders.js" ></script>
</head>
<body <?php if(!empty(getOption('background image'))): ?> style="background: url(<?php echo getOption('background image');?>); background-size: cover; background-attachment: fixed;" <?php endif; ?> >
	<div class="outer-edge">
		<h1 class="uk-text-center main-title">Order Tracker</h1>
		<div id="main-cont" class="uk-grid uk-grid-small uk-grid-width-1-1 uk-grid-width-medium-1-4">
		<?php getAllOrders(); ?>
		</div>
	</div>
	<div class="new-form" style="text-align:right;">
	<button class="uk-button" onclick="javascript: $('#add-orders-form').toggle();"><i class="uk-icon uk-icon-plus-circle"></i></button>
	<form class="uk-form" style="display:none; min-width:250px;" id="add-orders-form">
		<fieldset  data-uk-margin>
			<legend>New Order</legend>
			<div class="uk-grid">
				<div class="uk-width-1-1 uk-width-medium-1-2">
					<div class="uk-form-row">
						<input type="text" placeholder="Name" name="name" id="name" style="width:100%;"/>
					</div>
					<div class="uk-form-row">
						<input type="email" placeholder="Email" name="email" id="email" style="width:100%;"/>
					</div>
					<div class="uk-form-row">
						<input type="text" placeholder="Date" name="eventDate" id="eventDate" data-uk-datepicker style="width:100%;"/>
					</div>
					<div class="uk-form-row">
						<input type="text" placeholder="Total Price" name="total" id="total" style="width:100%;"/>
					</div>
					<div class="uk-form-row">
						<input type="text" placeholder="Invoice ID" name="invoiceId" id="invoiceId" style="width:100%;"/>
					</div>
					<div class="uk-form-row">
						<input type="text" placeholder="Location" name="location" id="location" style="width:100%;"/>
					</div>
					<div class="uk-form-row">
						<select id="deliveryType" name="deliverType" style="width:100%;">
							<option>Delivery (free)</option>
							<option>Delivery (paid)</option>
							<option>Pickup</option>
						</select>
					</div>
					<div class="uk-form-row">
						<p><input type="checkbox" name="depositPaid" id="depositPaid" value="yes" style="width:100%;"/> Deposit has been paid</p>
						<p><input type="checkbox" name="totalPaid" id="totalPaid" value="yes" style="width:100%;"/> Total has been paid</p>
					</div>
					<div class="uk-form-row">
						<input type="submit" class="uk-button uk-button-primary" value="Add orders" />
						<input type="hidden" name="action" value="newOrder" />
					</div>
				</div>
				<div class="uk-width-1-1 uk-width-medium-1-2">
					<div class="uk-form-row">
						<textarea placeholder="Details" name="details" id="details" cols="25" rows="5" style="width:100%;"></textarea>
					</div>
					<div class="uk-form-row">
						<textarea placeholder="Items (comma separated)" name="items" id="items" cols="25" rows="15" style="width:100%;"></textarea>
					</div>
					
				</div>
			</div>
		</fieldset>
	</form>
	</div>
</body>
</html>
<?php
}



function doAction($action){
	switch($action){
		case "newOrder":
			neworder();
			break;

		case "editOrder":
			editOrder();
			break;

		case "payDeposit":
			payDeposit();
			break;

		case "payTotal":
			payTotal();
			break;

		case "getAllOrders":
			getAllOrders();
			break;

		case "cancelOrder":
			cancelOrder($_POST['ID']);
			break;

		case "completeOrder":
			completeOrder($_POST['ID']);
			break;
	}
}

function getAllOrders(){
	global $db;
	$res = $db->multiRowQuery("SELECT * FROM orders WHERE COMPLETED=0 ORDER BY EventDate;");
	foreach($res as $orders){
		$isTotalPaid = false;

		$classes = $orders['CANCELLED'] == 1 ? ' order-cancelled' : '';
		$classes .= $orders['CANCELLED'] == 1 ? ' order-cancelled' : '';


		echo '<div class="orders-single-outer" id="orders-'.$orders['ID'].'"><div class="uk-panel orders-single '.$classes.'">';
		echo '<div class="rem-EventDate">'.$orders['EventDate'].'</div>';
		echo '<div class="rem-Name"><a href="mailto:'.$orders['Email'].'">'.$orders["Name"].'</a></div>';
		foreach($orders as $key => $value){
			switch($key){
				case "ID":
				case "MODIFIED_ON":
				case "CREATED_ON":
				case "Name":
				case "EventDate":
				case "CANCELLED":
				case "COMPLETED":
					break;

				case "TotalPaid":
					if((int)$value == 0){ $totalPaid = '<b style="color:#d83030;">Nope</b>'; }else{ $isTotalPaid = true; $totalPaid = "<b>Yas</b>";}
					echo '<div class="rem-'.$key.'">Total paid: '.$totalPaid.'</div>';
					break;

				case "DepositPaid":
					$depositPaid = json_decode($value);
					if($depositPaid && !empty($depositPaid->date)){
						echo '<div class="rem-'.$key.'">Deposit: Paid on '.$depositPaid->date.'</div>';
					}else{
						echo '<div class="rem-'.$key.'">Deposit: <b style="color:#d83030;">Still Outstanding.</b></div>';
					}
					break;

				case "Items":
					$items = json_decode($value);
					if($items && count($items)){
						echo '<div class="rem-'.$key.'"><ul class="items">';
						foreach($items as $item) echo "<li>$item</li>";
						echo '</ul></div>';
					}
					break;

				case "TotalBy":
					if($isTotalPaid === false){
						echo '<div class="rem-'.$key.'"> Need total By: <span style="color:#d83030;font-size:16px;">'.$value.'</span> <a href="mailto:'.$orders['Email'].'"><i class="uk-icon uk-icon-envelope-square"></i></a></div>';
					}
					break;

				default:
					echo '<div class="rem-'.$key.'">'.$value.'</div>';
					break;
			}
		}
		echo '<div id="action-buttons"><button class="uk-button payDeposit" data-orders-id="'.$orders['ID'].'" title="Pay Deposit"><i class="uk-icon uk-icon-dollar"></i></button>  <button class="uk-button payTotal" data-orders-id="'.$orders['ID'].'" title="Pay Total"><i class="uk-icon uk-icon-dollar"></i><i class="uk-icon uk-icon-dollar"></i></button>  <button class="uk-button editorders" data-orders-id="'.$orders['ID'].'" title="Edit orders"><i class="uk-icon uk-icon-edit"></i></button>  <button class="uk-button uk-button-success completeOrder" data-orders-id="'.$orders['ID'].'" title="Complete order"><i class="uk-icon uk-icon-check"></i></button>  <button class="uk-button uk-button-danger cancelOrder" data-orders-id="'.$orders['ID'].'" title="Cancel order"><i class="uk-icon uk-icon-ban"></i></button></div>
		</div></div>';
	}
}

function newOrder(){
	global $db;
	// check all fields werer filled out and we got the data
	if(!empty($_POST['name'])) $name = $_POST['name']; else die('{"error":"no name provided"}');
	if(!empty($_POST['email'])) $email = $_POST['email']; else die('{"error":"no email provided"}');
	if(!empty($_POST['details'])) $details = $_POST['details']; else die('{"error":"no details provided"}');
	if(!empty($_POST['eventDate'])) $eventDate = $_POST['eventDate']; else die('{"error":"no event date provided"}');
	if(!empty($_POST['total'])) $total = $_POST['total']; else die('{"error":"no total provided"}');
	if(!empty($_POST['invoiceId'])) $invoiceId = $_POST['invoiceId']; else die('{"error":"no invoice ID provided"}');
	if(!empty($_POST['location'])) $location = $_POST['location']; else die('{"error":"no location provided"}');
	if(!empty($_POST['deliverType'])) $deliveryType = $_POST['deliverType']; else die('{"error":"no delivery type provided"}');
	if(!empty($_POST['items'])) $items = $_POST['items']; else die('{"error":"no items provided"}');

	// calculate the 14 days prior to event
	$totalBy = new DateTime($eventDate);
	$totalBy->sub(new DateInterval('P14D'));

	// format the items to json
	$items = json_encode(explode(',',$items));

	$sql = "INSERT INTO orders (Name,Email,Details,EventDate,TotalPrice,InvoiceID,Location,deliveryType,Items,TotalBy) VALUES ('$name','$email','$details','$eventDate','$total','$invoiceId','$location','$deliveryType','$items','".$totalBy->format('Y-m-d')."');";

	if($db->execQuery($sql)){
		echo '{"success":"successfully added '.$invoiceId.' for '.$name.'"}';
	}else{
		echo '{"error":"coudn\'t add orders, please try again"}';
	}
}


function payDeposit(){
	global $db;
	// check we have an ID to run against
	if(!empty($_POST['ID'])) $ID = $_POST['ID']; else die('{"error":"no ID provided"}');

	// set the actual data for the db
	$actualValue = '{"paid":1,"date":"'.date('d/m/Y').'"}';

	$sql = "UPDATE orders SET DepositPaid='$actualValue' WHERE ID=$ID;";
	if($db->execQuery($sql)){
		echo '{"success":"successfully added deposit payment for orders '.$ID.'"}';
	}else{
		echo '{"error":"coudn\'t update orders '.$ID.', please try again"}';
	}
}

function payTotal(){
	global $db;
	// check we have an ID to run against
	if(!empty($_POST['ID'])) $ID = $_POST['ID']; else die('{"error":"no ID provided"}');
	$orders = $db->singleRowQuery("SELECT DepositPaid,TotalPaid FROM orders WHERE ID=$ID;");
	$sql = $orders['DepositPaid'] == '{"paid":0,"date":""}' ? "UPDATE orders SET TotalPaid=1 , DepositPaid='{\"paid\":1,\"date\":\"".date('d/m/Y')."\"}' WHERE ID=$ID;" : "UPDATE orders SET TotalPaid=1 WHERE ID=$ID;";

	if($db->execQuery($sql)){
		echo '{"success":"successfully added deposit payment for orders '.$ID.'"}';
	}else{
		echo '{"error":"coudn\'t update orders '.$ID.', please try again"}';
	}
}

function getOption($name){
	global $db;
	return $db->singleColQuery("SELECT value FROM options WHERE name='$name' LIMIT 1;");
}

function setOption($name,$value){
	global $db;
	return $db->execQuery("INSERT INTO options (`name`,`value`) VALUES ('$name','$value') ON DUPLICATE KEY UPDATE `value`='$value';");
}


function completeOrder($ID){
	global $db;
	if($db->execQuery("UPDATE orders SET `COMPLETED`=1 WHERE `ID`=$ID;")){
		echo '{"success":"successfully added deposit payment for orders '.$ID.'"}';
	}else{
		echo '{"error":"coudn\'t complete order '.$ID.', please try again"}';
	}
}

function cancelOrder($ID){
	global $db;
	if($db->execQuery("UPDATE orders SET `CANCELLED`=1 WHERE `ID`=$ID;")){
		echo '{"success":"successfully added deposit payment for orders '.$ID.'"}';
	}else{
		echo '{"error":"coudn\'t cancel order '.$ID.', please try again"}';
	}
}