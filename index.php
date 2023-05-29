<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0" />
	<title>Biq Zur Store - Магазин кодов активации ОФД. Купить коды ОФД.</title>
	<meta name="description" content="Biq Zur Store — это интернет-магазин по продаже кодов активации ОФД. Продавец: ООО 'ИТ-ГРУПП'. Телефон: +7 (952) 722-95-09. Электронная почта: mail@itaurum.ru."/>
	<meta name="Keywords" content="Biq Zur Store, Biqzurstore, подписка, ОФД, оператор фискальных данных"> 
	<meta property="og:title" content="Biq Zur Store - Магазин кодов активации ОФД. Купить коды ОФД."/>
	<meta property="og:type" content="website" />
	<meta property="og:description" content="Biq Zur Store — это интернет-магазин по продаже кодов активации ОФД. У нас Вы можете купить коды ОФД по выгодным ценам."/>
	<meta property="og:image" content="https://biqzurstore.ru/img/15895520-351463195235301-445470282630225072-o.png"/>
	<meta property="og:image:width" content="1200"/>
	<meta property="og:image:height" content="630"/>
	<meta property="og:url" content="https://biqzurstore.ru"/>
	<link rel="icon" href="https://biqzurstore.ru/favicon/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="style/normalize.css" />
	<link rel="stylesheet" type="text/css" href="style/style.css" />
	<link rel="stylesheet" type="text/css" href="style/stylemean.css" />
	<link rel="stylesheet" type="text/css" href="style/stylemobile.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
</head>
<body>
<script src="https://telegram.org/js/telegram-web-app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
<!--Подключаем скрипт от телеграм-->
<?php
require 'vendor/autoload.php';
use function PHPSTORM_META\type;
use Dotenv\Dotenv;
if (!empty($_GET)) : ?>
	<?php if ($_GET['Success'] && $_GET['ErrorCode'] == '0') :
		$id = $_GET['OrderId'];
		$date = $_GET['TranDate'];
		if(empty($date))$date=date('Y-m-d\TH:i:s');
		$paymentId = $_GET['PaymentId'];

		$db = new SQLite3('test.db');
		$db->exec("UPDATE invoices SET date='" . $date . "' , payment_status=0 WHERE id='" . $id . "'");
		$row = $db->query("SELECT * FROM invoices WHERE id='".$id."'");
		$result = $row->fetchArray(SQLITE3_ASSOC);



		//save changes
		$goods = json_decode($result['goods'], true);
		foreach ($goods as $value) {
			$row = $db->query("SELECT p_quantity FROM products WHERE p_code ='" .$value['product_id']. "'");
			$products = $row->fetchArray(SQLITE3_ASSOC);
			$new_quantity = $products['p_quantity'] - $value['count'];
			$db->exec("UPDATE products SET p_quantity='" . $new_quantity . "'  WHERE p_code='" .$value['product_id']. "'");
		}
		$result['paymentId'] = $paymentId;
		$json = json_encode($result);
		$json = str_replace('\\', '', $json);   
		$json = str_replace('"[', '[', $json);
		$json = str_replace(']"', ']', $json);

		$dotenv = Dotenv::createImmutable(__DIR__);
		$dotenv->load();
		$username = $_ENV ['username'];
		$password = $_ENV ['password'];
		$url = $_ENV ['send_url'];

		$options = array(
			'http' => array(
				'header' => "Authorization: Basic " . base64_encode("$username:$password"),
				'method'  => 'POST',
				'content' => $json
			)
		);
		$context  = stream_context_create($options);
		$resp = file_get_contents($url, false, $context);
      $codes_arr = json_decode($resp, true);


		if($result['from_telegram']=='true'){
			$st="https://api.telegram.org/bot".$_ENV ['bot_token']."/sendMessage?chat_id=";
			$ch=$result['user_id'];
			$st.=$ch."&text=";
			$st.="Заказ ".$codes_arr["id"]." оплачен.%0A";
			$last_product_name='';
			foreach ($codes_arr["goods"] as $product) {
				if($product["product_id"]==$last_product_name){
					$st.="Код активации: ".$product["sn"]."%0A";
				}
				else{
					$st.="Товар ".$product["product_id"]."%0A"."Код активации: ".$product["sn"]."%0A";
					$last_product_name=$product["product_id"];
				}
			}
			$response = file_get_contents($st);
		}
		// $result = ($db->querySingle("SELECT * FROM invoices WHERE id='21294816870001'", true));

		// // Create a stream
		// $opts = array(
		//    'http' => array(
		//       'method' => "GET",
		//       'header' => "Authorization: Basic " . base64_encode("$username:$password")
		//    )
		// );
		// $context = stream_context_create($opts);
		// // Open the file using the HTTP headers set above
		// $file = file_get_contents($remote_url, false, $context);
		// $arr = json_decode($file, true);

	?>
		<style>
			body{
				height:auto;
			}
			.Success_payment_btn {
					display:none;
			}
			a {
				text-decoration: none;
				color: white;
			}
			@media screen and (min-width: 100px) and (max-width: 480px) {
				.Success_payment_block {
					box-shadow: 10px 10px 40px #434040;
					background-color: rgb(255, 255, 255);
					margin-top: 3vh;
					padding-top: 1vh;
					padding-bottom: 5vh;
					margin-left: 5vw;
					width: 90vw;
					border-radius: 10px;
					text-align: center;
				}

				.Success_payment_btn {
					display:inline;
					padding: 1%;
					width: 90%;
					border-radius: 10px;
					border: inset #000000;
					color: white;
					background-color: #4CAF50;
					font-size: 5vw;
					text-align: center;
				}

				.Success_payment {
					font-size: 6vw;
				}
			}
		</style>

		<!-- <body> -->
			<script>
				window.tg = window.Telegram.WebApp; //получаем объект webapp телеграма 
				tg.expand(); //расширяем на все окно
				tg.MainButton.text = "Отправить коды активации в телеграм чат"; //изменяем текст кнопки
				tg.MainButton.color = '#4CAF50' // цвет кнопки 
				tg.MainButton.textColor = "#FFFFFF"; // цвет текста
				// tg.MainButton.show() can use in android

				// if (tg.initDataUnsafe.user == undefined) {	
				// 	window.invoice_id = 'bqs';
				// } else {
				// 	window.invoice_id = tg.initDataUnsafe.user.id;
				// }
				// invoice_id = createinvoiveid();



				// tg.MainButton.onclick=f1();
				id = (<?php echo json_encode($id) ?>);
				Telegram.WebApp.onEvent('mainButtonClicked', function() {
					td.send(id)
				})


				Cookies.set('cart', '')
				// if (window.cart != undefined) {
				// 	for (pr_id in cart) {
				// 		delete(cart[pr_id])
				// 		document.getElementById(`product ${pr_id}`).remove();
				// 		document.getElementById('c' + pr_id).value = 1;
				// 		document.getElementById('c' + pr_id).style.display = 'none';
				// 	}
				// 	cart_sumprice = 0;
				// 	document.getElementById('cart_sumprice_value').innerHTML = cart_sumprice.toFixed(2);
				// 	document.getElementById('menu_cart_btn').innerHTML = 'Корзина ';
				// 	document.getElementById('tinkoffPayRow_amount').value = cart_sumprice;
				// }
			</script>



			<div class="Success_payment_block">
				<p class="Success_payment">Оплачено</p>
				<p class="Success_payment">Благодарим за покупку</p>
				<p class="Success_payment">Коды для активации отправлены на вашу почту</p>
				<p class="Success_payment">Отправьте боту команду /my_invoices, чтобы получить коды активации в телеграме</p>
				<!-- <button id="Success_payment_tgbtn" class= 'Success_payment_btn' onclick="">Отправить коды активации в телеграм чат</button> -->
				<button id="Success_payment_btn" class='Success_payment_btn' onclick=""><a href="http://biqzurstore.ru/">Вернуться в магазин</a></button>
				<!-- <a href="https://biqzurstore.ru/"> -->
			</div>


			<script>
				// document.querySelector("t-close-frame-desktop").onclick = function(){
				// 	location.reload()
				// };
			// if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
			// 	document.getElementById('Success_payment_tgbtn').style.display = 'none'
			// }
			// else{
			// 	document.getElementById('Success_payment_btn').style.display = 'none'
			// }
		</script>

		</body>

































	<?php else :
		$err_message = $_GET['Message'];
		$err_code = $_GET['ErrorCode'];
	?>
		<style>
			a {
				text-decoration: none;
				color: white;
			}
			@media screen and (min-width: 100px) and (max-width: 480px) {
				.Success_payment_block {
					box-shadow: 10px 10px 40px #434040;
					background-color: rgb(255, 255, 255);
					margin-top: 3vh;
					padding-top: 1vh;
					padding-bottom: 5vh;
					margin-left: 5vw;
					width: 90vw;
					border-radius: 10px;
					text-align: center;
				}

				.Success_payment_btn {
					padding: 1%;
					width: 90%;
					border-radius: 10px;
					border: inset #000000;
					color: white;
					background-color: #4CAF50;
					font-size: 5vw;
					text-align: center;
				}

				.Success_payment {
					font-size: 6vw;
				}
			}
		</style>
		<script>
			window.tg = window.Telegram.WebApp; //получаем объект webapp телеграма 
			tg.expand(); //расширяем на все окно
			tg.MainButton.text = "Что-то"; //изменяем текст кнопки
			tg.MainButton.color = '#4CAF50' // цвет кнопки 
			tg.MainButton.textColor = "#FFFFFF"; // цвет текста
			// if (tg.initDataUnsafe.user == undefined) {
			// 	window.invoice_id = 'bqs';
			// } else {
			// 	window.invoice_id = tg.initDataUnsafe.user.id;
			// }
			// invoice_id = createinvoiveid();
			tg.MainButton.show()
			// tg.MainButton.onclick=f1();
			Telegram.WebApp.onEvent('mainButtonClicked', function() {
				td.send()
			})
		</script>

		<!-- <body> -->
			<div class="Success_payment_block">
				<p class="Success_payment" id="sp_err_code">Ошибка </p>
				<p class="Success_payment" id="sp_err_mes"></p>
				<p class="Success_payment">Что-то еще</p>
				<!-- <button id="Success_payment_tgbtn" class= 'Success_payment_btn' onclick="">Отправить коды активации в телеграм чат</button> -->
				<button id="Success_payment_btn" class='Success_payment_btn' onclick=""><a href="http://biqzurstore.ru/">Вернуться в магазин</a></button>
				<!-- <a href="https://biqzurstore.ru/"> -->
			</div>
		</body>
		<script>
			document.getElementById('sp_err_mes').innerHTML = (<?php echo json_encode($err_message) ?>);
			document.getElementById('sp_err_code').innerHTML += (<?php echo json_encode($err_code) ?>);
		</script>
	<?php endif; ?>































<?php else :
	$db = new SQLITE3("test.db");
	$sql = "SELECT * FROM products";
	$result = $db->query($sql);
	$myrow = array();
	$i = 0;
	while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
		$myrow[$i] = $row;
		$i++;
	}
	$db->close();
	session_start();   
   $userid = session_id();
?>

	<!-- <body> -->
		<script>
			window.range = (<?php echo json_encode($myrow) ?>);
		</script>

		<script >
			window.tg = window.Telegram.WebApp; //объект webapp телеграма 
			tg.expand(); //расширяем на все окно
			tg.MainButton.text = "Перейти к оплате"; //изменяем текст кнопки
			tg.MainButton.color = '#4CAF50' // цвет кнопки 
			tg.MainButton.textColor = "#FFFFFF"; // цвет текста
			// tg.MainButton.show()
			// tg.MainButton.onclick=f1();
			Telegram.WebApp.onEvent('mainButtonClicked', function() {
				tinkoffPayFunction(document.getElementById('tinkoffPayBTN'))
			})

			//creat id
			// console.log(Cookies.get('email'))
			// Cookies.set('name', 'value')




			async function create_id(id) {
				status = await $.ajax({
					url: 'createid.php',
					type: 'POST',
					data: {
						'user_id': id
					},
					success: function(data) {
						return data;
					},
					error: function() {
						return [false, 'err with database']
					}
				})
				response = JSON.parse(status)
				invoice_id = response.id;
				console.log(invoice_id)
				if (Object.keys(response).length > 1) {
					document.getElementById('tinkoffPayMail').value = response.email
					document.getElementById('tinkoffPayTel').value = response.phone
					if ('itn' in response) {
						if (check_itn(response.itn)) {
							document.getElementById('itn').value = response.itn
						} else {
							document.getElementById('itn').value = ''
						}
					}
				}
			}

			function save_cart_to_cookie() {
				cc = {}
				for (id in cart) {
					cc[id] = cart[id].count
				}
				Cookies.set('cart', JSON.stringify(cc), {
					expires: 14
				})
			}

			//увеличение количества товар
			const plusFunction = e => {
				e.stopPropagation()
				id = e.target.id
				// if(range[id]['count'] == 0){
				// 		return }
				if ((id in cart) === false) {
					cart[id] = {
						name: range[id]['name'],
						cost: range[id]['cost'],
						count: 1
					}
					cart_sumprice += cart[id].cost;
					document.getElementById('tinkoffPayRow_amount').value = cart_sumprice;
					document.getElementById('cart_sumprice_value').innerHTML = cart_sumprice.toFixed(2);
					document.getElementById('menu_cart_btn').innerHTML = 'Корзина ' + cart_sumprice.toFixed(0) + ' &#8381;';
					document.getElementById('c' + id).style.display = 'block';

					if (document.getElementById('full_card').style.display != 'none') {
						document.getElementById(`fc${id}`).style.display = 'block';
					}
					save_cart_to_cookie()
					return
				} else if (cart[id]['count'] == range[id]['count']) {
					return
				} else {
					cart[id]['count']++;
					document.getElementById('c' + id).value = cart[id]['count'];
					cart_sumprice += cart[id].cost;
					document.getElementById('tinkoffPayRow_amount').value = cart_sumprice;
					document.getElementById('cart_sumprice_value').innerHTML = cart_sumprice.toFixed(2);
					document.getElementById('menu_cart_btn').innerHTML = 'Корзина ' + cart_sumprice.toFixed(0) + ' &#8381;';
					if (document.getElementById('full_card').style.display != 'none') {
						document.getElementById(`fc${id}`).value = cart[id]['count'];
					}
					save_cart_to_cookie()
					// if (document.getElementById(`product ${id}`)) {
					// 	document.getElementById('q' + id).value = cart[id]['count'];
					// 	document.getElementById('s' + id).value = cart[id]['count'] * cart[id].cost;
					// }
				}
			}


			// const minusFunction =id=>{
			//    if ((id in cart) === false){
			//       return true;
			//    }
			//    else if (cart[id]['count']-1==0){
			//          deleteFunctiom(id);
			//          return true;
			//       }
			//    cart[id]['count']--;
			// 	document.getElementById('c'+id).value= cart[id]['count']; 
			// 	document.getElementById('q'+id).value= cart[id]['count']; 
			//    document.getElementById('s'+id).value= cart[id]['count']*cart[id].cost; 
			//    // var qty_el = document.getElementById('q'+id); var qty = qty_el.value; if( !isNaN( qty )) qty_el.value--;return false;
			// }


			// удаление товара
			// const deleteFunctiom = id=>{
			//    delete cart[id];
			//    // document.getElementById('q'+id).value=0; 
			// 	document.getElementById(`product ${id}`).remove();
			// 	document.getElementById('c'+id).style.display = 'none';
			// }

			function Delete_from_cart(id) {
				if (id == 'allcart') {
					Cookies.set('cart', '')
					for (pr_id in cart) {
						delete(cart[pr_id])
						document.getElementById(`product ${pr_id}`).remove();
						document.getElementById('c' + pr_id).value = 1;
						document.getElementById('c' + pr_id).style.display = 'none';
					}
					cart_sumprice = 0;
					document.getElementById('cart_sumprice_value').innerHTML = cart_sumprice.toFixed(2);
					document.getElementById('menu_cart_btn').innerHTML = 'Корзина ';
					document.getElementById('tinkoffPayRow_amount').value = cart_sumprice;
					$.fancybox.close(true)
				} else {
					document.getElementById(`product ${id}`).remove();
					document.getElementById('c' + id).style.display = 'none';
					document.getElementById('c' + id).value = 1;
					cart_sumprice -= cart[id].cost * cart[id].count;
					document.getElementById('tinkoffPayRow_amount').value = cart_sumprice;
					document.getElementById('cart_sumprice_value').innerHTML = cart_sumprice.toFixed(2);
					if (cart_sumprice == 0) {
						document.getElementById('menu_cart_btn').innerHTML = 'Корзина ';
					} else {
						document.getElementById('menu_cart_btn').innerHTML = 'Корзина ' + cart_sumprice.toFixed(0) + ' &#8381;';
					}
					delete(cart[id])
					save_cart_to_cookie()
				}
			}

			// document.onclick = event => {
			// 	if (event.target.classList.contains('plus')) {
			// 		console.log('+')
			// 		plusFunction(event.target.id);
			// 		return 
			// 		// console.log(cart)
			// 	}
			// 	if (event.target.classList.contains('product')) {
			// 		show_full_cart(event.target)
			// 		return
			// 		// console.log(cart)
			// 	}
			// 	if (event.target.classList.contains('minus')) {
			// 		minusFunction(event.target.id);
			// 		// console.log(cart)
			// 	}
			// 	if (event.target.classList.contains('del_btn')) {
			// 		Delete_from_cart(event.target.id);
			// 		// console.log(cart)
			// 	}
			// 	// if(event.target.classList.contains('show')){
			// 	// showCartFunction(cart);
			// 	// }
			// }

			function copy_contacs(cont) {
				if (cont == 'phone') {
					document.getElementById("phone").select();
				} else {
					document.getElementById("mail").select();
				}
				// text.select();    
				document.execCommand("copy");
				// alert("Скопировано");
			}

			function page2(type) {
				if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				document.getElementById('ul_menu').style.display='none'
				document.getElementById('main').style.display='inline'
				Array.prototype.forEach.call(document.querySelectorAll('.product'), product => {
						if (product.classList.contains(type.id)) {
							product.style.display = 'grid'
						}
						else {
							product.style.display = 'none'
						}
					})
				} 
				else{
					if (type.dataset.open=='true') {
					type.dataset.open='false'
					type.style.background = '#333'
					Array.prototype.forEach.call(document.querySelectorAll('.product'), product => {
						if (product.classList.contains(type.id)) {
							product.style.display = 'none'
						}
					})
					} else {	
					Array.prototype.forEach.call(document.querySelectorAll('.menu_btn'), btn => {
						btn.style.background = '#333'
						btn.dataset.open='false'

					})
					// document.getElementById('search').value;
					// type.setAttribute('open', 'true')
					type.dataset.open='true'
					type.style.background = '#4CAF50'
					Array.prototype.forEach.call(document.querySelectorAll('.product'), product => {
						if (product.classList.contains(type.id)) {
							product.style.display = 'grid'
						} else {
							product.style.display = 'none'
						}
					})
				}
				}
				// document.getElementById('page1').style.display='none';
				// if (type == 'ofd') {
				// 	if (document.getElementById('ofd').getAttribute('open') == 'false') // getComputedStyle(document.getElementById('ofd-type')).display == 'none') 
				// 	{
				// 		document.getElementById('ofd-type').style.display = 'inline-flex';
				// 		document.getElementById('ofd').style.background = '#4CAF50'
				// 		// document.getElementById('ofd').innerHTML = 'ОФД: &#8657';
				// 		document.getElementById('ofd').setAttribute('open', 'true')
				// 	} else {
				// 		document.getElementById('ofd-type').style.display = 'none';
				// 		document.getElementById('ofd').style.background = '#333'
				// 		// document.getElementById('ofd').innerHTML = 'ОФД: &#8659';
				// 		document.getElementById('ofd').setAttribute('open', 'false')
				// 	}
				// 	// document.getElementById('types').innerText='ofd types';
				// 	// let div = document.createElement('div')
				// 	// document.body.append(div);
				// } else {
				// 	if (document.getElementById('another').getAttribute('open') == 'false') { //getComputedStyle(document.getElementById('another-type')).display == 'none'
				// 		document.getElementById('another-type').style.display = 'inline-flex';
				// 		document.getElementById('another').style.background = '#4CAF50'
				// 		// document.getElementById('another').innerHTML = 'Прочее: &#8657';
				// 		document.getElementById('another').setAttribute('open', 'true')
				// 	} else {
				// 		document.getElementById('another-type').style.display = 'none';
				// 		document.getElementById('another').style.background = '#333'
				// 		// document.getElementById('another').innerHTML = 'Прочее: &#8659';
				// 		document.getElementById('another').setAttribute('open', 'false')
				// 	}

				// 	// let div = document.createElement('div')
				// 	// document.body.append(div);
				// }
			}

			function Show_cart() {
				tg.MainButton.show();
				// document.getElementById('tinkoffPayMail').value = 'uuu@mail.ru'
				// document.getElementById('tinkoffPayTel').value = '+79822060350'
				for (let i in cart) {

					let div = document.createElement('div');
					div.setAttribute('class', 'cart_product');
					div.setAttribute('id', `product ${i}`);

					let del = document.createElement('button'); //создаем кнопку
					del.setAttribute('class', 'del_btn')
					del.innerHTML = '&#10006;'
					del.setAttribute('id', `${i}`)
					del.setAttribute('onclick', `Delete_from_cart(this.id)`)
					//plus.id=`${range[i]['code']}`;//можно повесить обработку для каждой кнопки индивидуально здгы.onclick = function minusFunction(){}
					// minus.onclick = plusFunction(minus.id);
					div.appendChild(del); //добавляем 


					let image = document.createElement('img');
					image.src = `${range[i].image}`
					image.setAttribute('class', 'cart_product_img')
					div.appendChild(image);

					// let title = document.createElement('p'); //создаем еще параграф 
					// title.innerHTML = `${cart[i]['name']}`;
					// title.setAttribute('class', 'cart_product_title')
					// div.appendChild(title); //добавляем 

					// let plus = document.createElement('button'); //создаем кнопку
					// // plus.type = 'button';
					// plus.setAttribute('class', 'plus cart_product_plus')
					// plus.innerHTML='+'
					// plus.setAttribute('value', '+')
					// plus.setAttribute('id', `${i}`)
					// //plus.id=`${range[i]['code']}`;//можно повесить обработку для каждой кнопки индивидуально здгы.onclick = function minusFunction(){}
					// // minus.onclick = plusFunction(minus.id);
					// div.appendChild(plus); //добавляем 

					let quantity = document.createElement('input');
					quantity.type = 'text';
					quantity.setAttribute('class', 'cart_product_quantity')
					quantity.setAttribute('value', `${cart[i]['count']}`)
					quantity.setAttribute('id', `q${i}`)
					quantity.setAttribute('readonly', true)
					// quantity.setAttribute('style', "width: 25px")
					div.appendChild(quantity);

					// let minus = document.createElement('input'); //создаем кнопку
					// minus.type = 'button';
					// minus.setAttribute('class', 'minus  cart_product_minus')
					// minus.setAttribute('value', '-')
					// minus.setAttribute('id', `${i}`)
					// //minus.id=`${range[i]['code']}`;//можно повесить обработку для каждой кнопки индивидуально здгы.onclick = function minusFunction(){}
					// // minus.onclick = minusFunction(minus.id);
					// div.appendChild(minus);

					let sumprice = document.createElement('input'); //создаем еще параграф 
					sumprice.type = 'text';
					sumprice.setAttribute('class', 'cart_product_sumprice');
					sumprice.setAttribute('value', `${  cart[i]['cost']  * cart[i]['count']  }`);
					sumprice.setAttribute('id', `s${i}`)
					sumprice.setAttribute('readonly', true)
					div.appendChild(sumprice); //добавляем 

					document.getElementById('cart_goods').appendChild(div)

				}















				$.fancybox.open({
					padding: [0, 0, 0, 0],
					src: '#cart',
					type: 'inline',
					closeClickOutside: false,
					opts: {
						afterClose: function() {
							document.getElementById('cart_goods').innerHTML = '';
							// tg.MainButton.show();
							tg.MainButton.hide()
						}
					}
				});

				document.getElementById('err_msg').style.display = 'none';
				itn = document.getElementById('itn');
				itn.oninput = function() {
					if (check_itn(itn.value)) {
						document.getElementById('itn').style.borderColor = "black";
						document.getElementById('err_msg').style.display = 'none';
						Cookies.set('itn', itn.value, {
							expires: 14
						})
					} else {
						document.getElementById('itn').style.borderColor = "red";
						document.getElementById('err_msg').style.display = 'block';
					}
					// if (/[0-9]/.test(itn.value[itn.value.length])){
					// 	itn.value=itn.value.slice(0, itn.value.length-1) 
					// }
				}

				email = document.getElementById('tinkoffPayMail');
				email.oninput = function() {
					Cookies.set('email', email.value, {
						expires: 14
					})
				}
				phone = document.getElementById('tinkoffPayTel');
				phone.oninput = function() {
					Cookies.set('phone', phone.value, {
						expires: 14
					})
				}
			}

			function check_itn(itn) {
				if (itn.length == 0) { //input.value.length!=10 ||
					return true
				} else if (itn.length == 12) {
					contr_sum1 = parseInt(itn[0]) * 7 +
						parseInt(itn[1]) * 2 +
						parseInt(itn[2]) * 4 +
						parseInt(itn[3]) * 10 +
						parseInt(itn[4]) * 3 +
						parseInt(itn[5]) * 5 +
						parseInt(itn[6]) * 9 +
						parseInt(itn[7]) * 4 +
						parseInt(itn[8]) * 6 +
						parseInt(itn[9]) * 8;
					contr_ch1 = contr_sum1 % 11;
					if (contr_ch1 > 9) contr_ch1 = contr_ch1 % 10;
					contr_sum2 = parseInt(itn[0]) * 3 +
						parseInt(itn[1]) * 7 +
						parseInt(itn[2]) * 2 +
						parseInt(itn[3]) * 4 +
						parseInt(itn[4]) * 10 +
						parseInt(itn[5]) * 3 +
						parseInt(itn[6]) * 5 +
						parseInt(itn[7]) * 9 +
						parseInt(itn[8]) * 4 +
						parseInt(itn[9]) * 6 +
						parseInt(itn[10]) * 8;
					contr_ch2 = contr_sum2 % 11;
					if (contr_ch2 > 9) contr_ch2 = contr_ch2 % 10;

					if (contr_ch1 == itn[10] && contr_ch2 == itn[11]) {
						return true;
					}
				} else if (itn.length == 10) {
					contr_sum = parseInt(itn[0]) * 2 +
						parseInt(itn[1]) * 4 +
						parseInt(itn[2]) * 10 +
						parseInt(itn[3]) * 3 +
						parseInt(itn[4]) * 5 +
						parseInt(itn[5]) * 9 +
						parseInt(itn[6]) * 4 +
						parseInt(itn[7]) * 6 +
						parseInt(itn[8]) * 8;
					contr_ch = contr_sum % 11;
					if (contr_ch > 9) contr_ch = contr_ch % 10;
					if (contr_ch == itn[9]) {
						return true
					}
				} else {
					return false
				}
			}

			function Aboutus() {
				$.fancybox.open({
					padding: [0, 0, 0, 0],
					src: '#about',
					type: 'inline',
					closeClickOutside: false,
					opts: {
						afterClose: function() {}
					}
				});
			}

			async function pre_checkout_query(email, phone, itn) {
				if (tg.initDataUnsafe.user == undefined) {
					from = 'false'
				} else {
					from = 'true'
				}
				let cart_ar=[]
				for(id in cart){
					cart_ar.push({"product_id":id,"cost":cart[id]["cost"],"count":cart[id]["count"]})
				}

				if (window.tg.initDataUnsafe.user == undefined) {
					userid = (<?php echo json_encode($userid) ?>);
				} else {
					userid = tg.initDataUnsafe.user.id;
				}

				return await $.ajax({
					url: 'precheckout_query.php',
					//precheckout_query
					type: 'POST',
					data: {
						'invoice_id': invoice_id,
						'user_id':userid,
						'cart': JSON.stringify(cart_ar),
						'email': email,
						'phone': phone,
						'itn': itn,
						'from': from
					},
					success: function(data) {
						return data;
					},
					error: function() {
						return [false, 'err with database']
					}
				})
			}

			async function create_items_mas() {
				items = [];
				for (key in cart) {
					items.push({
						"Name": cart[key]['name'],
						"Price": cart[key]['cost'] + '00',
						"Quantity": cart[key]['count'],
						"Amount": cart[key]['cost'] * cart[key]['count'] + '00',
						"PaymentMethod": "full_prepayment",
						"PaymentObject": "commodity ",
						"Tax": "none"
					})
				}
				return items
			}

			async function tinkoffPayFunction(target) {
				if (Object.keys(cart).length == 0) {
					return;
				}
				if (document.getElementById('err_msg').style.display != "none") {
					return;
				}
				if (cart_sumprice == 0) {
					return;
				}
				// await createinvoiveid();
				let form = target.parentElement;
				let name = form.description.value || "Оплата";
				let amount = form.amount.value;
				let email = form.email.value;
				let phone = form.phone.value;
				let itn = document.getElementById('itn').value;

				if (amount && email && phone) {
					status = await pre_checkout_query(email, phone, itn);
					response = JSON.parse(status)
					if (!response.status) {
						alert(response.err)
					} else {
						// invoice_id = response[1]
						form.order.value = invoice_id;
						// console.log(form.order.value);
						$.fancybox.close(true)
						items = await create_items_mas();
						form.receipt.value = JSON.stringify({
							"Email": email,
							"Phone": phone,
							"EmailCompany": "mail@itaurum.ru",
							"Taxation": "usn_income_outcome",
							"Items": items
						});
						pay(form);
					}
				} else alert("Не все обязательные поля заполнены")
			}

			function get_cookies() {
				if (Cookies.get('email') != undefined) {
					document.getElementById('tinkoffPayMail').value = Cookies.get('email')
				}
				if (Cookies.get('phone') != undefined) {
					document.getElementById('tinkoffPayTel').value = Cookies.get('phone')
				}
				if (Cookies.get('itn') != undefined) {
					if (check_itn(Cookies.get('itn'))) {
						document.getElementById('itn').value = Cookies.get('itn')
					} else {
						document.getElementById('itn').value = ''
					}
				}
				if (Cookies.get('cart') != undefined) {
					if(Cookies.get('cart') != ''){
						cc = JSON.parse(Cookies.get('cart'))
					for (id in cc) {
						if (range[id].count != 0) {
							cart[id] = {
								name: range[id]['name'],
								cost: range[id]['cost'],
							}
							if (range[id].count < cc[id]) {
								cart[id].count = range[id].count
							} else {
								cart[id].count = cc[id]
							}
							cart_sumprice += cart[id].cost * cart[id].count;
							document.getElementById('c' + id).style.display = 'block';
							document.getElementById('c' + id).value = cart[id]['count'];
						}
					}
					document.getElementById('tinkoffPayRow_amount').value = cart_sumprice;
					document.getElementById('cart_sumprice_value').innerHTML = cart_sumprice.toFixed(2);
					document.getElementById('menu_cart_btn').innerHTML = 'Корзина ' + cart_sumprice.toFixed(0) + ' &#8381;';
					}
				}
			}

			function search_delete() {
				document.getElementById('search').value = '';
				document.getElementById("search_delete").style.display = 'none'
				// Array.prototype.forEach.call(document.querySelectorAll('.menu_btn'), btn => {
					// if (btn.dataset.open == 'true') {
						Array.prototype.forEach.call(document.querySelectorAll('.product'), product => {
							// if (product.classList.contains(btn.id)) {
								product.style.display = 'grid'
							// } else {
							// 	product.style.display = 'none'
							// }
						})
					// }
				// })
				// Array.prototype.forEach.call(document.querySelectorAll('.product'), product => {
				// 	product.style.display = 'none'
				// })
			}

			function show_full_card(id) {
				console.log(id)
				el = document.getElementById('full_card').children
				el[0].src = range[id]['image']
				el[1].innerHTML = range[id]['name']
				el[2].id = `fc${id}`
				if (id in cart) {
					el[2].value = cart[id]['count']
					el[2].style.display = 'block'
				}
				el[3].innerHTML = `Цена : ${range[id]['cost']}  &#8381`
				el[4].id = id




				$.fancybox.open({
					padding: [0, 0, 0, 0],
					src: '#full_card',
					type: 'inline',
					closeClickOutside: false,
					opts: {
						afterClose: function() {
							document.getElementById(`fc${id}`).value = 1;
							document.getElementById(`fc${id}`).style.display = 'none';
						}
					}
					// opts: {modal: true,}
				});
			}






			// window.addEventListener("unload", function(event) {
			// 	let date = new Date(Date.now() + 86400e3);
			// 	date = date.toUTCString();
			// 	document.cookie = "user=user999; expires=" + date;
			// 	//event.preventDefault();
			// 	event.returnValue = null; //"Any text"; //true; //false;
			// 	//return null; //"Any text"; //true; //false;
			// });
			// window.onbeforeunload = function() {
			// 	return "Данные не сохранены. Точно перейти?";
			// };
		</script>

		<div class="wrapper">
			<header>
			<div id="search_div">
					<!-- <div id="mobile_menu_btn" onclick="
									document.getElementById('ul_menu').style.display='inline'
									document.getElementById('main').style.display='none'
					"></div> -->
					<p id="glass">&#128270;</p>
					<input type="text" id="search" placeholder="Поиск...">
					<button id="search_delete" onclick="search_delete()">&#10006;</button>
				</div>
				<ul id='ul_menu'>
					<li>
						<button onclick="page2(this)" id="ofd" data-open='true' class="menu_btn">ОФД:</button>
					</li>
					<li>
						<button onclick="page2(this)" id="another1" data-open='false' class="menu_btn">Something:</button>
					</li>
					<li>
						<button onclick="page2(this)" id="another2" data-open='false' class="menu_btn">Something:</button>
					</li>
					<li>
						<button onclick="page2(this)" id="another3" data-open='false' class="menu_btn">Something:</button>
					</li>
					<li>
						<button onclick="page2(this)" id="another4" data-open='false' class="menu_btn">Something:</button>
					</li>
					<li>
						<button onclick="page2(this)" id="another5" data-open='false' class="menu_btn">Something:</button>
					</li>
				</ul>
				<button onclick="Show_cart()" class="menu_cart_btn" id="menu_cart_btn">Корзина </button>
				<!-- поиск -->
				<!-- <div id="search_div">
					<p id="glass">&#128270</p>
					<input type="text" id="search" placeholder="Поиск...">
					<button id="search_delete" onclick="search_delete()">&#10006</button>
				</div> -->
			</header>
			<div id="main">
				<div id="goods" class="page"></div>
				<!-- <div id="another-type" class="page"></div>
				<div id="search-type" class="page"></div> -->
			</div>
			<!-- <button onclick="Show_cart()" class="menu_cart_btn" id="menu_cart_btn">Корзина </button> -->
			<footer>
				<!-- <ul> -->
					<!-- <h1>Наши контакты:</h1> -->
					<!-- <li class="footer-li">
						<p class="contacs">Телефон: </p>
						<input type="tel" class="contacs" value="+7-952-722-95-09" onclick="copy_contacs('phone')" id="phone" readonly>
					</li>
					<li id="footer-li">
						<p class="contacs">Почта: </p>
						<input type="email" class="contacs" value="mail@itaurum.ru" onclick="copy_contacs('mail')" id="mail" readonly>
					</li> -->
					<!-- <li class="footer-li about"> -->
						<button onclick="Aboutus()" id="about_btn" class="contacs">Подробнее о нашем магазине</button>
					<!-- </li> -->
				<!-- </ul> -->
			</footer>
		</div>
		

		<div style="display: none " id="about">
			<p class="about_info">Продавец: ООО “ИТ-ГРУПП”</p>
			<p class="about_info">ИНН: 8601044695</p>
			<p class="about_info">ОГРН: 1118601001826</p>
			<p class="about_info">Режим работы: в рабочие дни с 07:00 до 17:00 по московскому времени без перерывов.</p>
			<p class="about_info">Адрес: 628011, Ханты-Мансийский Автономный округ - Югра ао, Ханты-Мансийск г, Крупской ул, дом 26, строение 1, офис 304</p>
			<p class="about_info">Телефон: +7 (952) 722-95-09</p>
			<p class="about_info">Электронная почта: mail@itaurum.ru</p>
			<noindex><a class="polkonf_btn" href="files/Соглашение_об_обработке_персональных_данных.pdf" rel='nofollow'>Политика конфиденциальности</a></noindex>
			<noindex><a class="polkonf_btn" href="files/Договор-оферта.pdf" rel='nofollow'>Договор-оферта</a></noindex>
			<noindex><a class="polkonf_btn" href="files/Условия_возврата.pdf" rel='nofollow'>Условия возврата</a></noindex>
		</div>


		<div style="display: none " id="cart">
			<button class="delete_all" onclick="Delete_from_cart('allcart')">Очистить</button>
			<h2>Корзина</h2>
			<ul  class='cart_header'>
				<li class='cart_title'>Товар</li>
				<li class='cart_quantity'>Количество</li>
				<li class='cart_sumprice'>Сумма</li>
			</ul>
			<div id="cart_goods" ></div>
			<hr>
			<!-- <div id="cart_sum_div"> -->
				<p id="cart_sum">Итого:  </p><div id="cart_sumprice_value">0.00</div>
			<!-- </div> -->
			<p class="itn_msg">Если Вам нужны закрывающие документы для бухгалтерии, заполните, пожалуйста, ИНН организации, и мы вышлем их Вам на электронную почту, а также, по возможности, по ЭДО. </p>
			<input type="number" placeholder="Ваш ИНН" id="itn" onkeyup="this.value = this.value.replace(/[^\d]/g,'');">
			<!-- onkeyup="this.value = this.value.replace(/[^\d]/g,'');" -->
			<p id="err_msg">Неправильный ИНН</p>
			<!-- <button onclick=Open_widget() class="btn">Купить</button> -->
			<script src="https://securepay.tinkoff.ru/html/payForm/js/tinkoff_v2.js"></script>
			<form name="TinkoffPayForm"><!-- onsubmit="pay(this); return false;"       placeholder="Телефон"   placeholder="E-mail"   -->
				<input class="tinkoffPayRow" type="hidden" name="terminalkey" value="1676878075970DEMO">
				<input class="tinkoffPayRow" type="hidden" name="frame" value="true">
				<input class="tinkoffPayRow" type="hidden" name="language" value="ru">
				<input class="tinkoffPayRow" type="hidden"  name="amount" id='tinkoffPayRow_amount'>
				<input class="tinkoffPayRow" type="hidden"   name="order" id='tinkoffPayRow_id'>
				<input class="tinkoffPayRow" type="hidden"   name="description">
				<input class="tinkoffPayRow" type="hidden"  name="name">
				<input class="tinkoffPayRow" type="email" placeholder="E-mail" id="tinkoffPayMail" name="email" required>
				<input class="tinkoffPayRow" type="tel" placeholder="Телефон" id="tinkoffPayTel" name="phone">
				<input class="tinkoffPayRow" type="hidden" name="receipt" value="">
				<input class="tinkoffPayRow btn" type="button" onclick="tinkoffPayFunction(this)" value="Оплатить" id='tinkoffPayBTN'>
			</form>
		</div>

		<div style="display: none " id="full_card" class="product_big">
			<img src="" class='product_img'>
			<p class="product_title"></p>
			<input type="text" class="product_count" value="1" readonly>
			<p class="product_price"></p>
			<input type="button" class="buy plus product_btn" value="Добавить" onclick="plusFunction(event)" id='1'>
		</div>



		<script>
			if (tg.initDataUnsafe.user != undefined) {
				document.getElementById('tinkoffPayBTN').style.display = 'none'
				document.getElementById('about_btn').style.display = 'none'
				// document.body.querySelector('footer').style.display = 'none'
			}






			if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				// код для мобильных устройств
				document.body.querySelector('footer').appendChild(document.getElementById('menu_cart_btn'))
				document.getElementById('ofd').dataset.open='false'
				document.getElementById('ofd').style.background = '#333'
			} 
			// else {
			// 	// код для обычных устройств
			// }

			function dbClick() {}
			document.body.addEventListener('dblclick', dbClick);



			window.cart = {};
			window.cart_sumprice = 0;
			for (let i in range) {
				if (range[i]['p_quantity'] != 0) {
					let div = document.createElement('div');
					if(range[i]['p_type'] == 'ОФД'){
						div.setAttribute('class', 'product ofd')
					}
					else{
						div.setAttribute('class', 'product another')
					}
					div.setAttribute('id', `${range[i]['p_code']}`)
					// div.setAttribute('class', 'product ofd')
					div.setAttribute('onclick', 'show_full_card(this.id)')

					let image = document.createElement('img');
					image.src = `${range[i]['p_pic']}`
					image.setAttribute('class', 'product_img')
					image.setAttribute('alt', `${range[i]['p_name']}`)
					// image.src = `data:image/png;base64,${range[i]['image']}`
					// image.setAttribute('width',300)
					// image.setAttribute('height',300)
					div.appendChild(image);


					let title = document.createElement('p'); //создаем еще параграф 
					title.innerText = `${range[i]['p_name']}`;
					title.setAttribute('class', 'product_title')
					div.appendChild(title); //добавляем 

					let count = document.createElement('input'); //count -> rest
					count.type = 'text';
					count.setAttribute('class', 'product_count')
					count.setAttribute('value', `1`)
					count.setAttribute('id', `c${range[i]['p_code']}`)
					count.setAttribute('readonly', true)
					div.appendChild(count); //добавляем 

					let price = document.createElement('p'); //создаем еще параграф 
					price.innerHTML = `${range[i]['p_price']}` + ' &#8381;';
					price.setAttribute('class', 'product_price')
					div.appendChild(price); //добавляем 


					let buy = document.createElement('input'); //создаем кнопку
					buy.type = 'button';
					buy.setAttribute('class', 'buy plus product_btn')
					buy.setAttribute('value', 'Добавить')
					buy.setAttribute('id', `${range[i]['p_code']}`)
					buy.setAttribute('onclick', 'plusFunction(event)')
					//plus.id=`${range[i]['code']}`;//можно повесить обработку для каждой кнопки индивидуально здгы.onclick = function minusFunction(){}
					// minus.onclick = plusFunction(minus.id);
					div.appendChild(buy); //добавляем 
					document.getElementById('goods').appendChild(div)
				}

				// let plus = document.createElement('input'); //создаем кнопку
				// plus.type = 'button';
				// plus.setAttribute('class', 'plus product_btn')
				// plus.setAttribute('value', '+')
				// plus.setAttribute('id', `${range[i]['id']}`)
				// //plus.id=`${range[i]['code']}`;//можно повесить обработку для каждой кнопки индивидуально здгы.onclick = function minusFunction(){}
				// // minus.onclick = plusFunction(minus.id);
				// div.appendChild(plus); //добавляем 

				// let minus = document.createElement('input'); //создаем кнопку
				// minus.type = 'button';
				// minus.setAttribute('class', 'minus product_btn')
				// minus.setAttribute('value', '-')
				// minus.setAttribute('id', `${range[i]['id']}`)
				// //minus.id=`${range[i]['code']}`;//можно повесить обработку для каждой кнопки индивидуально здгы.onclick = function minusFunction(){}
				// // minus.onclick = minusFunction(minus.id);
				// div.appendChild(minus);

				// let quantity = document.createElement('input');
				// quantity.type = 'number';
				// quantity.setAttribute('class', 'inputbox')
				// quantity.setAttribute('value', 0)
				// quantity.setAttribute('id', `c${range[i]['id']}`)
				// quantity.setAttribute('name', 'quantity')
				// quantity.setAttribute('readonly', true)
				// // quantity.setAttribute('style', "width: 25px")
				// div.appendChild(quantity);

				range[range[i]['p_code']] = {
					name: range[i]['p_name'],
					cost: range[i]['p_price'],
					count: range[i]['p_quantity'],
					image: range[i]['p_pic']
				}; //count->resr
				delete range[i];
			}


			// Initialize the agent at application startup.
			// const fpPromise = import('https://openfpcdn.io/fingerprintjs/v3')
			// 	.then(FingerprintJS => FingerprintJS.load())

			// // Get the visitor identifier when you need it.
			// fpPromise
			// 	.then(fp => fp.get())
			// 	.then(result => {
			// 		// This is the visitor identifier:
			// 		const visitorId = result.visitorId
			// 		console.log(visitorId)
			// 	})
			if (window.tg.initDataUnsafe.user == undefined) {
					id = (<?php echo json_encode($userid) ?>);
				} else {
					id = tg.initDataUnsafe.user.id;
				}
			get_cookies();
			create_id(id);



			search = document.getElementById('search');
			search.value = ''
			search.oninput = function() {
				if (search.value.length != 0) {
					document.getElementById("search_delete").style.display = 'inline'
				} else {
					search_delete()
				}

				if (search.value.length - (search.value.split(' ').length - 1) >= 2) {

					// Array.prototype.forEach.call(document.querySelectorAll('.menu_btn'), btn => {
					// 	btn.style.background = '#333'
					// 	btn.setAttribute('open', 'false')

					// })
					// let list = search.value.split(' ')
					Array.prototype.forEach.call(document.querySelectorAll('.product'), product => {

						for (str of search.value.split(' ')) {
							if (str.length >= 2) {
								if (product.childNodes[1].textContent.toUpperCase().indexOf(str.toUpperCase()) != -1) {
									product.style.display = 'grid'
									break //.cloneNode(true)
								} else {
									product.style.display = 'none'
								}
							}
						}
						// if (product.childNodes[1].textContent.toUpperCase().indexOf(search.value.toUpperCase()) != -1) {
						// 	product.style.display = 'grid' //.cloneNode(true)
						// } else {
						// 	product.style.display = 'none'
						// }
					})
					// document.getElementById('search-type').style.display = 'flex'
					// document.getElementsByClassName('page').foreach(page => {
					// 	page.style.display = 'none'
					// })
					// document.getElementsByClassName('product').forEach(product => {
					// 	console.log(product)

					// })
				}
			}
		</script>
	</body>
<?php endif; ?>

</html>
