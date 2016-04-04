<?php
/* TO DO :
- Utiliser "L_PAYMENTREQUEST_0_DESC1" pour mettre le nom du produit
- Regarder les remarques importantes pour DoExpress.
- Refactoring : 
  - au niveau de la création 'Order' et 'Payer', réfléchir dans le cas des autres type de produits (Strategy?).
  - dans class Paypal, gérer les erreurs curl   .
- Ajouter message d'erreur multi-langue.  
*/


header( 'content-type: text/html; charset=utf-8' );

session_start();
require_once 'core/SPDO.class.php';
require_once 'core/admin/admin.class.php';

//Products
require_once 'core/Product/classes/Product.class.php';
require_once 'core/Product/classes/ProductContext.class.php';
require_once 'core/Product/classes/StrategyProduct.interface.php';
require_once 'core/Product/classes/TicketProduct.class.php';
require_once 'core/Product/classes/Paypal.class.php';
require_once 'core/Product/classes/Order.class.php';

    $id_prod  = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $c        = filter_input(INPUT_GET, 'c', FILTER_SANITIZE_STRING);

  // Get administration setting
  $oAdmin = new Admin();   
  $aSet = $oAdmin -> getSetting();
  $lang = $aSet['language'];


  setlocale(LC_TIME, "fr_FR", "fr_FR@euro", "fr", "FR", "fra_fra", "fra");
  if ($c != 'pay'){
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  
  <!-- *** CSS *** -->
  <link rel="stylesheet" href="css2/cssgeneral-s1.css">
  <!-- Gestion des boutons validation formulaires -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css2/jquery.floating-social-share.min.css" />
  
  <!-- *** JS *** -->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="addons/js/respond.js"></script>
    <script src="addons/js/html5shiv.min.js"></script>
    <![endif]-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">
      <!--#include virtual="includes/menu.html" -->
      </div>
    </div>

  <?php }
    //Seach paypal settings
    $sReq = "SELECT * FROM adm_paypal";
    $select = SPDO::getInstance()->query($sReq);
    $aSet = $select->fetch(PDO::FETCH_ASSOC);
  
    // initialize Paypal configuration.
    $oPaypal = new Paypal($aSet['user'], $aSet['pwd'], $aSet['signature'], $aSet['prod'], $aSet['version'], $aSet['endpoint'], $aSet['money']);

    //Inialize strategy for 'Ticket' products
    $oTicket = new ProductContext('Ticket');
    $aDataTickets = $oTicket->readProducts();

    if (!isset($c)) include 'core/Product/view/front-display-all-shows.php';
    else{
      if ($c == 'select'){
        // user select a show for booking it.
        $aDataShow = $oTicket->readProduct($id_prod);
        $aDataPrices = $oTicket->readAllPrices($id_prod);
        include 'core/Product/view/front-display-one-show.php';
      }
      elseif ($c == 'pay') $oTicket->SetExpressChekout($oPaypal);
      elseif ($c == 'checkout') $oTicket->GetAndDoExpressCheckout($oPaypal);
      elseif ($c == 'cancel'){
        echo "A problem has occurred please contact the merchant site";
        if (isset($_SESSION['tickets'])) unset($_SESSION['tickets']);
      }
    }// end else.

  ?>



  </div>  
</body>
</html>