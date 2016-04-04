<?php

interface StrategyProduct{
  public function InsertProductData();

  public function memorizeDataInSession($aData, $aSpecificData);

  public function readAllProducts();

  public function readOneProduct($id);

  //public function getOrderDetails($prod, $id);

  public function readPrices($id_prod);

  public function PaypalSetExpressChekout($oPaypal);

  public function PaypalGetAndDoExpressCheckout($oPaypal);
}