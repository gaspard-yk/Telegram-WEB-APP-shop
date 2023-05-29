<?php
$invoice_id = $_POST['invoice_id'];
$user_id = $_POST['user_id'];
$cartst = $_POST['cart'];
$cart = json_decode($cartst, true);
$email = $_POST['email'];
$phone = $_POST['phone'];
$itn = $_POST['itn'];
$from = $_POST['from'];

$db = new SQLite3('test.db');

$st =1;
//pre checkout
foreach ($cart as $value) {
	$sel_pr = $db->query("SELECT p_price, p_quantity FROM products WHERE p_code ='" . $value['product_id'] . "'");
	$products = $sel_pr->fetchArray(SQLITE3_ASSOC);
	if ($value['count'] > $products['p_quantity']) {
		$response = (object) array('status' => false, 'err' => "Количество товаров изменилось. Перезагрузите страницу.");
			$st =0;
		// array("0", "Товар '" . $value['name'] . "' отсутствует в количестве '" . $value['count'] . "' шт.");
		// echo json_encode($response);
	} else if ($value['cost'] != $products['p_price']) {
		$response = (object) array('status' => false, 'err' => "Цена на товары изменилась. Перезагрузите страницу.");
			$st =0;
		// = array("0", "Цена на товар '" . $value["name"] . "' изменилась");
		// echo json_encode($response);
	}
}
if($st ==1){
	// $rows = $db->query("SELECT COUNT(*) as count FROM invoices WHERE id = '" . $invoice_id . "' ");
	// $row = $rows->fetchArray();
	// $count = intval($row['count']);
	// if ($count != 0) {
	// 	$db->exec("UPDATE invoices SET goods='" . $cartst . "',email='" . $email . "',phone='" . $phone . "'   WHERE id='" . $invoice_id . "'  ");
	// 	if ($itn != null) {
	// 		$db->exec("UPDATE invoices SET itn='" . $itn . "' WHERE id='" . $invoice_id . "'  ");
	// 	}
	// } 
	// else {
		if ($itn != null || $itn=='') $itn=0;
		if ($from != 'true') $from='false';
		$db->exec("INSERT INTO invoices (id,user_id, goods, email, phone,itn,from_telegram) VALUES ('" . $invoice_id . "','" . $user_id . "','" . $cartst . "', '" . $email . "','" . $phone . "',
																																	'" . $itn . "','" . $from . "')");
		// if ($itn != null) {
		// 	$db->exec("UPDATE invoices SET itn='" . $itn . "' WHERE id='" . $invoice_id . "'  ");
		// // }
		// // if ($from == 'true') {
		// 	$db->exec("UPDATE invoices SET from_telegram='" . $from . "' WHERE id='" . $invoice_id . "'  ");
		// }
	// // }
	$response =  array('status' => true);
}
$db->close();
echo json_encode($response);
?>