<?php

Class Order{

 public function RecordOrder($aData, $id_cat){
	$payerId = $this->getPayerId($aData['PAYERID']);
	date_default_timezone_set('Europe/Paris');
  $dDate = date('Y-m-d H:i:s');

	$reqInsert = "INSERT INTO products_orders (date_order, chekoutstatus, id_payer, amt, product_cat) VALUES(:date_order, :status, :id_payer, :amt, :product_cat)";
	$insert = SPDO::getInstance()->prepare($reqInsert);
	$insert->bindParam(':date_order', $dDate);
	$insert->bindParam(':status', $aData['CHECKOUTSTATUS']);
	$insert->bindParam(':id_payer', $payerId['id_payer']);
	$insert->bindParam(':amt', $aData['PAYMENTREQUEST_0_AMT']);
  $insert->bindParam(':product_cat', $id_cat);
	try{
		$result = $insert->execute();
    $iId_Order = SPDO::getInstance()->lastInsertId();

	}catch(PDOException $e)	{
    echo $e->getMessage();
		$result = false;
    return $result;
	}

	if ($result){
		$this->RecordDetailsOrder($iId_Order, $aData);
    return $iId_Order;
	}
  	

 }



private function RecordDetailsOrder($id_order, $aData){
  $reqInsert = "INSERT INTO products_ordersdetails (quantity, price, name, id_order) VALUES (:quantity, :price, :name, :id_order)";
  $insert = SPDO::getInstance()->prepare($reqInsert);

  $qty=0;

  //get order line quantity
  foreach($aData as $key=>$values){
    if (strpos($key, 'L_NAME') === 0) $qty += 1;
  }

  for($i=0 ; $i<$qty ; $i++){
    $sQty = "L_QTY$i";
    $sPrice = "L_AMT$i";
    $sName = "L_NAME$i";

    $insert->bindParam(':quantity', $aData[$sQty]);
    $insert->bindParam(':price', $aData[$sPrice]);
    $insert->bindParam(':name', $aData[$sName]);
    $insert->bindParam(':id_order', $id_order);

    try{
      $result = $insert->execute();
      //var_dump($result);
    }catch(PDOException $e){
      echo $e->getMessage();
    }
  }  
  
}



public function getPayerId($paypalid){
  $sReq = "SELECT id_payer FROM products_payer WHERE id_paypal='$paypalid'";
  try{
    $aResult = SPDO::getInstance()->query($sReq);
    return $aResult->fetch(PDO::FETCH_ASSOC);
  }
  catch (PDOException $e){
    return 0;
  }
}


public function updateOrder($sIdOrder, $aData){
  $sReq = "UPDATE products_orders set id_transaction=:id_transaction, chekoutstatus=:status WHERE id_order=:id_order";

  $update = SPDO::getInstance()->prepare($sReq);
  $update->bindParam(':id_transaction', $aData['PAYMENTINFO_0_TRANSACTIONID']);
  $update->bindParam(':status', $aData['PAYMENTINFO_0_PAYMENTSTATUS']);
  $update->bindParam('id_order', $sIdOrder);
  try{
    $result = $update->execute();
  }catch(PDOException $e){
    echo $e->getMessage();
  }

}





}