<?php
$id_order = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$id_cat = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
$id_category = filter_input(INPUT_GET, 'id_cat', FILTER_SANITIZE_NUMBER_INT);

//  PRODUCT CATEGORY MANAGEMENT SUBMENU

if (isset($a) && 'create_cat' === $a){
	$sInitLink = 'admin.php?p=product&a=create&c=init';

  $oCat = new Product;
	$name_cat = filter_input(INPUT_POST, 'name_cat', FILTER_SANITIZE_STRING);

	if (isset($name_cat)) $_SESSION['name_cat'] = $name_cat;


	if ( (!isset ($valid)) ) {
		$aCat = $oCat->getDataCategories();
		include 'core/Product/view/product-category-view.php';
	}
	else{
		// Create category choice
		if (isset($c) && 'add' === $c){
			if ( isset($valid) && 'no' === $valid ){
            $btOk = "admin.php?p=product&a=create_cat&c=add&valid=yes";
            $oCat->RequestConfirmation('modif', 'Confirmez-vous nouvelle categories ?', $btOk, $sInitLink, $lang);
        	}else{
        		$oCat->createCategory($_SESSION['name_cat']);
        	}
		}
		// Delete category choice
		elseif (isset($c) && 'delete' == $c) {

            if (!isset($conf)) {
					$sToken = $oSecure->create_token();				
                    $btOk = 'admin.php?p=product&a=create_cat&c=delete&valid=yes&id=' . $id . '&conf=yes&token=' . $_SESSION['token'];
                    $oCat->RequestConfirmation('supp', 'Confirmer suppression ?', $btOk, $sInitLink, $lang);
                } else {

                    $req = 'delete from product_categories where id_cat=' . $id;
                    $oCat->DeleteInformation($req, 'admin.php?p=product&a=create_cat&c=init');
                }
        }
        // Update category choice
        if (isset($c) && 'update' === $c){
        	if ( isset($valid) && 'no' === $valid ){
            	$btOk = "admin.php?p=product&a=create_cat&c=update&valid=yes&id=$id";
                $oCat->RequestConfirmation('modif', 'confirmez-vous modification ?', $btOk, $sInitLink, $lang);                        
            }
            else {
                $oCat -> UpdateCategory($id, $_SESSION['name_cat']);         
            }
        }    
	}	
//  CREATE PRODUCT SUBMENU    
}elseif (isset($a) && 'create_prod' === $a){
  $aMsgPost = $oAdmin->getItemTransation('PRODUCT', 'BACK', $lang, 'CREATE_PROD'); 

  $oCat = new Product;
  $aCat = $oCat->getDataCategories();
  $aDataProd = array();

  if (!isset($conf)){ 
    if (isset($_SESSION['products'])){
      $prod_name = $_SESSION['products']['name'];
      $prod_desc = $_SESSION['products']['desc'];
     }
     else{
      $prod_name = '';
      $prod_desc = '';
     } 
  }

    if (isset($c) && 'add_product' === $c ){

      $oTicket = new ProductContext('Ticket');

      if (!isset($conf)){ 
        $oProduct = new Product($_POST);

        $oTicket->memorizeData($oProduct, $_POST);
        $btOk = 'admin.php?p=product&a=create_prod&c=add_product&conf=yes';
        $oProduct->RequestConfirmation('modif', $aMsgPost[$lang]['lib_create_confirm'], $btOk, 'admin.php?p=product&a=create_prod', $lang);
        }else{
          //Create confirmation
          $oTicket->createProduct();
        }
    }
    else
    {
      include 'core/Product/view/create-product-view.php';
    }         
// ORDER MANAGEMENT
}elseif(isset($a) && 'order_admin' === $a){
  $aMsgPost = $oAdmin->getItemTransation('PRODUCT', 'BACK', $lang, 'ORDER_MANAGEMENT');

  $oProd = new Product();
  
  if (!isset($c) || $c === 'select_cat') include 'core/Product/view/back-orders-display.php';
  elseif ($c === 'details'){
    // read order detail (for the product category selected)
    $aDetailsData = $oProd->getOrderDetails($id_order);
    include 'core/Product/view/back-order-details-display.php';
  }



}


?>
        
<script>
    $(function (){
        $('.my-form #add_price').click(function(){
            var n = $('.text-box').length + 1;
            var input_name_price = '<input type="text" name="name_price' + n + '" value="" id="name_price' + n + '" />';
            var input_price = '<input type="text" name="price' + n + '" value="" id="price' + n + '" />';
            var remove_button = "<button id='remove_button' class='class=btn btn-xs btn-danger' type='submit'> <span class='glyphicon glyphicon-trash'></span></button>";

            var box_html = $('<p class="text-box"> ' + input_name_price + ' ' + input_price + ' ' + remove_button + '</p>');

            box_html.hide();
            $('.my-form p.text-box:last').after(box_html);
            box_html.fadeIn('slow');
            return false;
        });

        $('.my-form').on('click', '#remove_button', function(){
            $(this).parent().fadeOut("slow", function() {
                $(this).remove();
            });
            return false;
        });
    });      

</script> 

  


