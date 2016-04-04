<?php
 /**
  * Product classe base.
  *
  * @param array $aData Common product informations
  */
class Product{
 use MessageAlert, CommunDbRequest;
 private $aDataCat;
 public $product_name;
 public $product_desc;
 public $product_cat;

 public function __construct($aData=null){
 	$this->product_name = $this->FormattingName($aData['name_prod']);
 	$this->product_desc = $this->FormattingDesc($aData['desc_prod']);
 	$this->product_cat	= $this->FormattingCat($aData['id_cat']);	
 }


// PRODUCT


private function FormattingName($sName){
	$name = ucfirst(filter_var($sName, FILTER_SANITIZE_STRING));
	return $name;
}

private function FormattingDesc($sDesc){
	$desc = ucfirst(filter_var($sDesc, FILTER_SANITIZE_STRING));
	return $desc;

}

private function FormattingCat($iCat){
	return filter_var($iCat, FILTER_SANITIZE_NUMBER_INT);
}


// ORDERS


 /**
  * return payer informations (first/last name,adress, email...) and order details 
  *
  * @param int id order
  * @return array ['order details'] and ['payer'] arrays
  */
public function getOrderDetails($id_order){
  $sReq = "SELECT * FROM products_ordersdetails WHERE id_order=$id_order";
  $select = SPDO::getInstance()->query($sReq);
  $aResult['order_details'] = $select->fetchAll(PDO::FETCH_ASSOC);

  $aPayerData = $this->ReadOneOrder($id_order);

  $aPayerData = $this->getPayerInformations($aPayerData['id_payer']);

  $aResult['payer'] = $aPayerData;
  return $aResult;
}




public function ReadOrders($id_cat){
	$sReq = "SELECT * FROM products_orders WHERE product_cat=$id_cat order by date_order desc";
	$select = SPDO::getInstance()->query($sReq);
	try{
		$aResult = $select->fetchAll(PDO::FETCH_ASSOC);
	}catch(PDOException $e){
		$aResult = false;
		echo $e->getMessage();
	}

	return $aResult;
}


private function ReadOneOrder($id_order){
  $sReq = "SELECT * FROM products_orders WHERE id_order=$id_order";
  $select = SPDO::getInstance()->query($sReq);
  return $select->fetch(PDO::FETCH_ASSOC);
}

public function getPayerInformations($id_payer){
	$sReq = "SELECT * FROM products_payer WHERE id_payer=$id_payer";
	$select = SPDO::getInstance()->query($sReq);
	try{
		$aResult = $select->fetch(PDO::FETCH_ASSOC);
	}catch(Exception $e){
		$aResult = false;
		echo $e->getMessage();
	}
	return $aResult;
}


// CATEGORIES


  public function getDataCategories(){
    return $this->selectDataCategories();
  }

  private function selectDataCategories(){
    $aResult = SPDO::getInstance()->query('select * from product_categories');
    return $this->aDataCat = $aResult->fetchAll(PDO::FETCH_ASSOC);

  }

  public function selectOneCategory($id_cat){
    $aResult = SPDO::getInstance()->query("select * from product_categories where id_cat=$id_cat");
    return $select = $aResult->fetch(PDO::FETCH_ASSOC);
  }

   public function CreateCategory($sCat){
    $name_cat = filter_var($sCat, FILTER_SANITIZE_STRING);
    $sReq = 'insert into product_categories (cat_name) VALUES (:name_cat)';
    $aData = array(array('type'=>PDO::PARAM_STR, ':name_cat'=>$name_cat));
    $this -> executeDbQuery($sReq, $aData, '', 'admin.php?p=product&a=create_cat&c=init', true);
   }


   public function UpdateCategory($id_cat, $name_cat){
    $id  = filter_var($id_cat, FILTER_SANITIZE_NUMBER_INT);
        $name = filter_var ($name_cat, FILTER_SANITIZE_STRING);

    $sReq = 'UPDATE product_categories set cat_name = :name_cat where id_cat=' . $id;
    $aBindVar = array(array('type'=>PDO::PARAM_STR, ':name_cat'=>$name));
    $this -> executeDbQuery($sReq, $aBindVar, '', 'admin.php?p=product&a=create_cat&c=init', true);

   }


}//end class