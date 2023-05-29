<?php
// $sql = $db->query("SELECT email,phone,itn FROM invoices WHERE id LIKE '$invoice_id%'");
// $row = $sql->fetchArray(SQLITE3_ASSOC);
// $db->close();
// echo "message";

$user_id = $_POST['user_id'];
$db = new SQLITE3("test.db");

$invoice_id = uniqid();
$rows = $db->query("SELECT COUNT(*) as count FROM invoices WHERE id = '" . $invoice_id . "' ");
$row = $rows->fetchArray();
$count = intval($row['count']);
while ($count != 0) {
   $invoice_id = uniqid();
   $rows = $db->query("SELECT COUNT(*) as count FROM invoices WHERE id = '" . $invoice_id . "' ");
   $row = $rows->fetchArray();
   $count = intval($row['count']);
   }


$rows = $db->query("SELECT COUNT(*) as count FROM invoices WHERE user_id LIKE '$user_id' ");
$row = $rows->fetchArray(SQLITE3_ASSOC);
$count = intval($row['count']);
if ($count != 0){
   $sql = $db->query("SELECT email,phone,itn FROM invoices WHERE id LIKE '$id%'");
   while ($row = $sql->fetchArray(SQLITE3_ASSOC)) {
      $data = $row;
   }
   $response =  (object)array('id' => $invoice_id, 'email' => $data["email"], 'phone' => $data["phone"], 'itn' => $data["itn"]);
   }
else{
   $response = (object) array('id' => $invoice_id);
   } 
$db->close();
echo json_encode($response);





// $invoice_id = $_POST['user_id'];
// $db = new SQLITE3("test.db");
// if ($invoice_id == 'bqs') {
//    $invoice_id = uniqid();
//    $rows = $db->query("SELECT COUNT(*) as count FROM invoices WHERE id = '" . $invoice_id . "' ");
//    $row = $rows->fetchArray();
//    $count = intval($row['count']);
//    while ($count != 0) {
//       $invoice_id = uniqid();
//       $rows = $db->query("SELECT COUNT(*) as count FROM invoices WHERE id = '" . $invoice_id . "' ");
//       $row = $rows->fetchArray();
//       $count = intval($row['count']);
//    }
//    $response = (object) array('id' => $invoice_id);
// } else {
//    $id=$invoice_id.(uniqid());
//    $rows = $db->query("SELECT COUNT(*) as count FROM invoices WHERE id = '" . $id . "' ");
//    $row = $rows->fetchArray();
//    $count = intval($row['count']);
//    while ($count != 0) {
//       $id=$invoice_id.(uniqid());
//       $rows = $db->query("SELECT COUNT(*) as count FROM invoices WHERE id = '" . $id . "' ");
//       $row = $rows->fetchArray();
//       $count = intval($row['count']);
//    }
//    $rows = $db->query("SELECT COUNT(*) as count FROM invoices WHERE id LIKE '$id%' ");
//    $row = $rows->fetchArray(SQLITE3_ASSOC);
//    $count = intval($row['count']);
//    if ($count != 0){
//       $sql = $db->query("SELECT email,phone,itn FROM invoices WHERE id LIKE '$id%'");
//       while ($row = $sql->fetchArray(SQLITE3_ASSOC)) {
//          $data = $row;
//       }
//       // $db->close();
//       // echo json_encode($data);
//       // $data = $sql->fetchArray(SQLITE3_ASSOC);
//       $response =  (object)array('id' => $id, 'email' => $data["email"], 'phone' => $data["phone"], 'itn' => $data["itn"]);
//    }
//    else{
//       $response = (object) array('id' => $id);
//    }
   // $rows = $db->query("SELECT COUNT(*) as count FROM invoices WHERE id LIKE '$invoice_id%' ");
   // $row = $rows->fetchArray(SQLITE3_ASSOC);
   // $count = intval($row['count']);
   // if ($count == 0) {
   //    $invoice_id .= '0001';
   //    $response = (object) array('id' => $invoice_id);
   // } else {
   //    $sql = $db->query("SELECT email,phone,itn FROM invoices WHERE id LIKE '$invoice_id%'");
   //    while ($row = $sql->fetchArray(SQLITE3_ASSOC)) {
   //       $data = $row;
   //    }
   //    // $db->close();
   //    // echo json_encode($data);
   //    // $data = $sql->fetchArray(SQLITE3_ASSOC);
   //    if ($count < 9) {
   //       $invoice_id .= '000' . ($count + 1);
   //    } elseif ($count < 99) { //rows['COUNT()']>=10 && 
   //       $invoice_id .=  '00' . ($count + 1);
   //    } elseif ($count < 999) { //rows['COUNT()']>=100 && 
   //       $invoice_id .= '0' . ($count + 1);
   //    } else {
   //       $invoice_id .= ($conut + 1);
   //    }
   //    $response =  (object)array('id' => $invoice_id, 'email' => $data["email"], 'phone' => $data["phone"], 'itn' => $data["itn"]);
   // }
// }
// $db->close();
// echo json_encode($response);
?>