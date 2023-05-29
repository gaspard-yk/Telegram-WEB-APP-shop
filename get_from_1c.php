<?php

use function PHPSTORM_META\type;
// use Dotenv\Dotenv;
// use Dotenv\Exception\InvalidPathException;
// $dotenv = new Dotenv\Dotenv(__DIR__);
// $dotenv->load();
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$username = $_ENV ['username'];
$password = $_ENV ['password'];
$remote_url = $_ENV ['get_url'];

// Create a stream
$opts = array(
   'http' => array(
      'method' => "GET",
      'header' => "Authorization: Basic " . base64_encode("$username:$password")
   )
);
$context = stream_context_create($opts);
// Open the file using the HTTP headers set above
$file = file_get_contents($remote_url, false, $context);
$arr = json_decode($file, true);

$db = new SQLite3('test.db');
$db->exec("DELETE  FROM products");

// // //Модуль создания таблицы в бд

// $db->exec("DROP TABLE invoices");
// // // // $db->exec("DELETE FROM invoices"); // where p_code=''
// $sql = "CREATE TABLE invoices(
// id TEXT PRIMARY KEY,
// user_id TEXT NOT NULL,
// goods TEXT NOT NULL,
// email TEXT NOT NULL,
// phone TEXT NOT NULL,
// itn INTEGER,
// date TEXT,
// from_telegram TEXT,
// payment_status INTEGER
// );";//


// $db->exec("DROP TABLE products");
// $sql = "CREATE TABLE products(
// p_code TEXT PRIMARY KEY,
// p_art TEXT,
// p_name TEXT NOT NULL,
// p_type TEXT NOT NULL,
// p_price REAL,
// p_quantity INTEGER,
// p_pic TEXT
// );";
// $res = $db->exec($sql);


//save to db
for ($i = 0, $size = count($arr); $i < $size; ++$i) {
   $db->exec("INSERT INTO products (p_code, 
                                    p_art, 
                                    p_name, 
                                    p_type, 
                                    p_price, 
                                    p_quantity) 
            VALUES (
            '" . $arr[$i]['Code'] . "', 
            '" . $arr[$i]['Art'] . "',
            '" . $arr[$i]['Name'] . "',
            '" . $arr[$i]['Type'] . "',
            '" . $arr[$i]['Price'] . "',
            '" . $arr[$i]['Quantity'] . "')");

if (array_key_exists('Pic', $arr[$i])) {
   $img = $arr[$i]['Pic']['Data'];
   $bin = base64_decode($img);
   $im = imageCreateFromString($bin);
   if (!$im) {
      die('Base64 value is not a valid image');
   }
   $img_file = "img/" . $arr[$i]['Pic']['Name'] . "";
   if ($arr[$i]['Pic']['Ext'] == 'png') {
      imagepng($im, $img_file, 9, PNG_ALL_FILTERS);
   } else {
      imagejpeg($im, $img_file, 10);
   }
   $db->exec("UPDATE products SET p_pic='img/". $arr[$i]['Pic']['Name'] ."' WHERE p_code='" . $arr[$i]['Code'] . "'");
}
}

// $st='art';
// $db->exec("INSERT INTO products (p_code, p_name, p_pic, p_type, p_price, p_quantity) VALUES ('CB-00000123456', '	Код активации099', 'img/15895520-351463195235301-445470282630225072-o.png','ОФД',1,100)");
// $db->exec("UPDATE invoices SET email='null@mail.ru' WHERE id='21294816870002'");
// $db->exec("ALTER TABLE  products RENAME COLUMN P_art TO p_art");

$db->close();
?>