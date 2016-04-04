<?php

 /**
  * Context for Product Stragety design Pattern
  *
  * @param string sProductType Product type for context
  */
class ProductContext{
  private $product;

  public function __construct($sProductType){
  	switch ($sProductType){
  		case 'Ticket':
  			$this->product = new TicketProduct();
  			break;
  		case 'Hifi':	// Just for example (not implemented)
  			$this->product = new HifiProduct();
  			break;
  	}
  }

  //Methods for client uses
  public function createProduct(){
  	return $this->product->InsertProductData();
  }

  public function memorizeData($aData='', $aSpecificData){
    return $this->product->memorizeDataInSession($aData, $aSpecificData);

  }

  public function readProducts(){
    return $this->product->readAllProducts();
  }

  public function readProduct($id){
    return $this->product->readOneProduct($id);
  }  

/*
  public function readOrderDetails($product, $id_order){
    return $this->product->getOrderDetails($product, $id_order);
  }
*/

  public function readAllPrices($id_product){
   return $this->product->readPrices($id_product);
  }

  public function SetExpressChekout($oPaypal){
    return $this->product->PaypalSetExpressChekout($oPaypal);
  }

  public function GetAndDoExpressCheckout($oPaypal){
    return $this->product->PaypalGetAndDoExpressCheckout($oPaypal);
  }
}