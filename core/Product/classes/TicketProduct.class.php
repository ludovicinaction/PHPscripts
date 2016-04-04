<?php
 /**
  * Ticket product implementation.
  *
  */
class TicketProduct implements StrategyProduct{
  use CommunDbRequest, MessageAlert;

  private $aDataTicket;

 /**
  * Store data product in data base : Common informations and prices.
  *
  */
	public function InsertProductData(){
	    
    $sReqProd = "INSERT INTO products (name, description, id_cat) VALUES (:name, :description, :id_cat)";
    $name =  $_SESSION['products']['name'];
    $desc = $_SESSION['products']['desc'];
    $id_cat = $_SESSION['products']['cat'] ;

    $sReqPrice = "INSERT INTO products_prices (name, incl_tax, id_product) VALUES (:pricename, :incl, :id_prod)";
    $iNbPrices = count($_SESSION['prices']['name_prices']);

    // begin transaction
    SPDO::getInstance()->beginTransaction();
    try{

      // Insert product
      $insert_prod = SPDO::getInstance()->prepare($sReqProd);
      $insert_prod->bindParam(':name', $name);
      $insert_prod->bindParam(':description', $desc);
      $insert_prod->bindParam('id_cat', $id_cat);
      $insert_prod->execute();

      // Insert prices
      $lastIdProd = SPDO::getInstance()->lastInsertId();
      $insert_prices = SPDO::getInstance()->prepare($sReqPrice);
      for ($i=0 ; $i < $iNbPrices ; $i++){
        $insert_prices->bindParam(':pricename', $_SESSION['prices']['name_prices'][$i]);
        $insert_prices->bindParam(':incl', $_SESSION['prices']['prices'][$i]);
        $insert_prices->bindParam(':id_prod', $lastIdProd);
        $insert_prices->execute();
      }

      //commit
      SPDO::getInstance()->commit();      

    } catch(Excption $e){
      SPDO::getInstance()->rollback();
      unset($_SESSION['products']);
      unset($_SESSION['prices']);
      $this->DisplayResultRqt(false, 'admin.php?p=product&a=create_prod', '', '');
    }		
    
    $this->DisplayResultRqt(true, 'admin.php?p=product&a=create_prod', '', '');    
    unset($_SESSION['products']);
    unset($_SESSION['prices']);

	}

 /**
  * Memorise data ticket product in session
  *
  * @param array Common data product (name, description, category)
  * @param array Specific data of ticket category (here, the prices).
  */
  public function memorizeDataInSession($aData='', $aSpecificData){
    unset($_SESSION['products']);
    unset($_SESSION['prices']);
    $aPrices = array();

    $_SESSION['products']['name'] = $aData->product_name;
    $_SESSION['products']['desc'] = $aData->product_desc;
    $_SESSION['products']['cat'] = $aData->product_cat;

    foreach ($aSpecificData as $key => $value) {
      $nameprice_found = strpos($key, 'name_price');
      $price_found = strpos($key, 'price');
      
      
      if ($nameprice_found === 0) $aPrices['name_prices'][] = filter_var($value, FILTER_SANITIZE_STRING);
      if ($price_found === 0) $aPrices['prices'][] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
    $_SESSION['prices'] = $aPrices;

  }


  public function readAllProducts(){
    $aResult = SPDO::getInstance()->query('select p.id_prod, p.name, p.description from products p inner join product_categories c where p.id_cat=c.id_cat and c.cat_name=\'Ticket\'');
    return $aResult->fetchAll(PDO::FETCH_ASSOC);
  }


  public function readOneProduct($id){
    $sReq = "SELECT * FROM products WHERE id_prod=$id";

    $aResult = SPDO::getInstance()->query($sReq);
    return $aResult->fetch(PDO::FETCH_ASSOC);
  }

  public function readPrices($id_prod){
    $sReq = "select * from products_prices where id_product=$id_prod";
    $aResult = SPDO::getInstance()->query($sReq);
    return $aResult->fetchAll(PDO::FETCH_ASSOC);
  }




public function PaypalSetExpressChekout($oPaypal){
// *** config "setExpressCheckOut" and redirection to paypal ***    

  if (isset($_SESSION['tickets'])) unset($_SESSION['tickets']);
  $totalOrder = number_format($_SESSION['total_order'], 2, '.', '');
  $aProducts = array();
  $i=0;

  // "return URL" and "cancel URL" for paypal response
  $sHostUrl = $oPaypal->getHostUrl();
  $return_url = $sHostUrl . 'buy_ticket.php?c=checkout';
  $cancel_url = $sHostUrl . 'buy_ticket.php?c=cancel';

  $params = array(
    'RETURNURL' => $return_url,
    'CANCELURL' => $cancel_url,
    'PAYMENTREQUEST_0_AMT' => $totalOrder,
    'PAYMENTREQUEST_0_CURRENCYCODE' => $oPaypal->money_code,
    'PAYMENTREQUEST_0_ITEMAMT' => $totalOrder,
    );

  $aTicket = array();

  //filtering $_POST and keep book with seat number > 0
  foreach($_POST as $key => $value){
    // "name" item
    if (strpos($key, 'name') === 0){
      $p = (int) substr($key, 4, 1);
      $nbseat = "nbseats$p";
      // if nb seat > 0, keep 'name' value
      if (intval($_POST[$nbseat]) > 0) { $aTickets[$i]['name'] = $value; }
    }
    // if 'nb seat' and > 0 then keep 'nbseat' value
    elseif(strpos($key, 'nbseats') === 0  && intval($_POST[$nbseat]) > 0) $aTickets[$i]['nbseat'] = intval($value); 
    // if 'price' and seat> 0 then keep 'price' value
    elseif(strpos($key, 'price') === 0 && intval($_POST[$nbseat]) > 0) {
      $val = strtr($value, "," , ".");
      $val_filter = floatval(filter_var($val, FILTER_VALIDATE_FLOAT));
      $aTickets[$i]['price'] = $val_filter;
      $i += 1;
      }
  }

//var_dump($aTickets );

   //store values in session 
   $_SESSION['tickets'] = $aTickets; 

   foreach($aTickets as $k => $ticket){
    $params["L_PAYMENTREQUEST_0_NAME$k"] = $ticket['name'];
    $params["L_PAYMENTREQUEST_0_DESC$k"] = '';
    $params["L_PAYMENTREQUEST_0_AMT$k"] = $ticket['price'];
    $params["L_PAYMENTREQUEST_0_QTY$k"] = $ticket['nbseat'];
  }

  // SetExpressCheckout
  $response = $oPaypal->request('SetExpressCheckout', $params);

  if($response){
    $paypalUrl = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=' . $response['TOKEN'];
  }else{          
    $paypalUrl = $aSettingsProduct['cancel_url'];
  }

  //Re-direction to paypal (for get and do ExpressCheckOut)
  header('Location:'.$paypalUrl); 

}





public function PaypalGetAndDoExpressCheckout($oPaypal){
  //Secure $_GET. If 'TOKEN' don't exist (malicious act or problem) then redirection
  if (!isset($_GET['token'])){
    header('Location:'.'http://localhost/PHPScripts_website/live_demo/buy_ticket.php?c=cancel'); 
  }

  // *** Checkout process (get and do expresscheckout)      
  $totalOrder = number_format($_SESSION['total_order'], 2, '.', '');

  // GetExpressCheckoutDetails (get orders, buyer, transaction informations)
  $response = $oPaypal->request('GetExpressCheckoutDetails', array(
    'TOKEN' => $_GET['token']
  ));

  //Store buyer information
  $oPaypal->RecordPaypalPayer($response);       

  // Store order
  $oOrder = new Order();

  //Record order
  $id_cat = 1;
  $sIdOrder = $oOrder->RecordOrder($response, $id_cat);

  if($response){
    // if checkout success
    if($response['CHECKOUTSTATUS'] == 'PaymentActionCompleted'){
      die('This payment has already been validated');
    }
  }else{
    $oPaypal->recordErrorOrder($oPaypal->errors, $sIdOrder);
    die();
  }

  // DoExpressCheckoutPayment setting
  $params = array(
    'TOKEN' => $response['TOKEN'],
    'PAYERID'=> $response['PAYERID'],
    'PAYMENTREQUEST_0_PAYMENTACTION'=>'Sale',
    'PAYMENTREQUEST_0_AMT' => $response['AMT'],
    'PAYMENTREQUEST_0_CURRENCYCODE' => $oPaypal->money_code,
    'PAYMENTREQUEST_0_ITEMAMT' => $response['ITEMAMT'],
  );

  foreach($_SESSION['tickets'] as $k => $ticket){
    // Send again order informations to paypal for the history buyer purchases.
    $params["L_PAYMENTREQUEST_0_NAME$k"] = $ticket['name'];
    $params["L_PAYMENTREQUEST_0_DESC$k"] = '';
    $params["L_PAYMENTREQUEST_0_AMT$k"] = $ticket['price'];
    $params["L_PAYMENTREQUEST_0_QTY$k"] = $ticket['nbseat'];
  }

  // DoExpressCheckoutPayment
  $response = $oPaypal->request('DoExpressCheckoutPayment',$params );

  if($response){
    // DoExpressCheckoutPayment is success then update order in database ('Transaction ID', 'paymentstatus'...=
    $oOrder->updateOrder($sIdOrder, $response);

    if ($response['PAYMENTINFO_0_PAYMENTSTATUS'] == 'Completed'){
      // Send mail confirmation (with order information and ticket pdf link). A FAIRE
      echo "<br>the transaction n°{$response['PAYMENTINFO_0_TRANSACTIONID']} was successful";
    }  
  }else{
    $oPaypal->recordErrorOrder($oPaypal->errors, $sIdOrder);
  }

  if (isset($_SESSION['tickets'])) unset($_SESSION['tickets']);

}





} // end class