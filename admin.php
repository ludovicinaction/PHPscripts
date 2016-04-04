<?php
//ini_set("display_errors",0);error_reporting(0);
header('content-type: text/html; charset=utf-8');

session_start();
require_once 'core/SPDO_admin.class.php';
require_once 'core/blog/classes/articles.class.php';
require_once 'core/Securite.class.php';
require_once 'core/admin/admin.class.php';
require_once 'core/CommunDbRequest.trait.php';
//Products, paypal
require_once 'core/Product/classes/Product.class.php';
require_once 'core/Product/classes/ProductContext.class.php';
require_once 'core/Product/classes/StrategyProduct.interface.php';
require_once 'core/Product/classes/TicketProduct.class.php';
require_once 'core/Product/classes/Order.class.php';
require_once 'core/Product/classes/Paypal.class.php';

define('SITE_ROOT', realpath(dirname(__FILE__)));

setlocale(LC_TIME, "fr_FR", "fr_FR@euro", "fr", "FR", "fra_fra", "fra");
?><!doctype html>
<html lang="fr">
    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- *** CSS *** -->
        <link rel="stylesheet" href="css2/cssgeneral-s1.css">
        <!-- Gestion des boutons validation formulaires -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="css2/jquery.floating-social-share.min.css" />	

        <script type="text/css">	
        textarea.cke_source {
        white-space: pre-wrap;
        }
    </script>

    <!-- JS -->
    <script src="ckeditor/ckeditor.js"></script>
    <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>

</head>
<body>
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="navbar navbar-default navbar-fixed-top">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">Back-office </a>
                    </div>
                    <div class="navbar-collapse collapse"> 

                        <ul class="nav navbar-nav">
                            <li><a href="admin.php" class="glyphicon glyphicon-home"></a></li>
                            <?php                          
                            $oMetaArt = new Articles;

                            $oAdmin = new Admin();  
                            $aLang = $oAdmin -> getSetting();
                            $lang = $aLang['language'];

                            //get menu translations
                            $aItems = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'MENU');
                            $aItProd= $oAdmin->getItemTransation('PRODUCT', 'BACK', $lang, 'MAIN_MENU');
                            $lib_conf = $aItems[$lang]['lib_config'];
                            $lib_set_cat = $aItems[$lang]['lib_set_cat'];
                            $lib_crea_post = $aItems[$lang]['lib_crea_post'];
                            $lib_adm_post = $aItems[$lang]['lib_adm_post'];
                            $lib_adm_com = $aItems[$lang]['lib_adm_com'];                            
                            $lib_trans = $aItems[$lang]['lib_translation'];
                            

                            echo "<li><a href='#'>Settings</a>";
                                echo "<ul class='dropdown-menu'>";
                                    echo "<li><a href='admin.php?p=trans&c=init'>$lib_trans</a></li>";
                                    echo "<li><a href='admin.php?p=paypal'>Paypal</a></li>";
                                echo "</ul>";
                            echo "</li>";


                            echo "<li><a href='#'>Blog</a>";
                                echo "<ul class='dropdown-menu'>";                                    
                                    echo "<li><a href='admin.php?p=gest_art&a=config&c=init'>$lib_conf</a></li>";
                                    echo "<li><a href='admin.php?p=gest_art&a=gest_cat&c=init'>$lib_set_cat</a></li>";
                                    echo "<li role='separator' class='divider'></li>";
                                    echo "<li><a href='admin.php?p=gest_art&a=creer'>$lib_crea_post</a></li>";
                                    echo "<li><a href='admin.php?p=gest_art&a=modif'>$lib_adm_post</a></li>";
                                    echo "<li role='separator' class='divider'></li>";
                                    echo "<li><a href='admin.php?p=gest_art&a=gest_com&c=init'>$lib_adm_com</a></li>";
                            
                                echo "</ul>";
                            echo "</li>";
                            echo "<li><a href='#'>{$aItProd[$lang]['lib_product']}</a>";
                            echo "<ul class='dropdown-menu'>";
                                echo "<li><a href='admin.php?p=product&a=create_cat'>{$aItProd[$lang]['lib_creat_cat_menu']}</a></li>";
                                echo "<li><a href='admin.php?p=product&a=create_prod'>{$aItProd[$lang]['lib_create_product']}</a></li>";    
                                echo "<li role='separator' class='divider'></li>";
                                echo "<li><a href='admin.php?p=product&a=order_admin'>{$aItProd[$lang]['lib_order_management']}</a></li>";
                                    
                              ?>         
                                    <li><a href="#">Page 2-3</a></li>
                                </ul>
                            </li>					
                        </ul>

                    <?php include 'core/connect.inc.php'; // *** User connection administration  ?>

                    </div>	
                </div>
            </div>
        </div>		

        <?php

echo "<br><br><br><br><br>";

        // Initialise admin settings
        $oAdmin = new Admin();  

        $aSet = $oAdmin -> getSetting();
        $lang = $aSet['language'];
        $website = $aSet['websitehost'];
        $smtp = $aSet['smtp_sendmail'];
        $port = $aSet['port_sendmail'];
        $email_send = $aSet['email_sendmail'];

        ini_set('SMTP', $smtp);
        ini_set('smtp_port', $port);
        ini_set('sendmail_from', $email_send);

        $aItems = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'HOME');

       // $_GET filtering variables
       // p:page ( module name )
       $p       = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
            // a: action ( submenu )    
            $a       = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_STRING);
            // c: Choice (display, valid, delete)
            $c       = filter_input(INPUT_GET, 'c', FILTER_SANITIZE_STRING);
            
            // category criterion
            $cat    = filter_input(INPUT_POST, 'cat', FILTER_SANITIZE_NUMBER_INT);
            $begindate = filter_input(INPUT_POST, 'begindate', FILTER_SANITIZE_STRING);
            $enddate = filter_input(INPUT_POST, 'enddate', FILTER_SANITIZE_STRING);
            // eng :Registration Application / conf : recording validation
            $eng     = filter_input(INPUT_GET, 'eng', FILTER_SANITIZE_STRING);
            $conf    = filter_input(INPUT_GET, 'conf', FILTER_SANITIZE_STRING);
            //id (id_art or id_com or id_cat...)      
            $id      = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $valid   = filter_input(INPUT_GET, 'valid', FILTER_SANITIZE_STRING);
       
            // t: type (comment 'com' or answer 'rep')
            $t       = filter_input(INPUT_GET, 't', FILTER_SANITIZE_STRING);
       
            $v       = filter_input(INPUT_GET, 'v', FILTER_SANITIZE_NUMBER_INT);

        //Filter $_POST home setting values
        $p_lang      = filter_input(INPUT_POST, 'opt-lang', FILTER_SANITIZE_STRING);
        $p_host      = filter_input(INPUT_POST, 'hostname', FILTER_SANITIZE_STRING);
        $p_smtp      = filter_input(INPUT_POST, 'smtp', FILTER_SANITIZE_STRING);
        $p_port      = filter_input(INPUT_POST, 'port', FILTER_SANITIZE_NUMBER_INT);
        $p_mailsend  = filter_input(INPUT_POST, 'sendmail', FILTER_SANITIZE_STRING);


        // get home settings status     
        if (isset ($_POST['val_update'])) $bUpdate = filter_input(INPUT_POST,'val_update', FILTER_VALIDATE_BOOLEAN);
        else $bUpdate = FALSE;

        // display or save home setting
        if ( !isset($p) && $_SESSION['loginOK'] === true ) {
            if ($bUpdate == FALSE) include 'core/admin/view/home-view.php';

            if ($bUpdate) $oAdmin->save_setting($p_lang, $p_host, $p_smtp, $p_port, $p_mailsend);
        }   
        
        // PRODUCT MENU
        if (isset($p) && 'product' === $p && $_SESSION['loginOK'] === TRUE){
            include 'core/includes/product_admin_controler.php';
        }

        // BLOG MENU
        if (isset($p) && 'gest_art' === $p && $_SESSION['loginOK'] === true) {
            include 'core/includes/blog_admin_controler.php';
        }    

        // SETTINGS / TRANSLATION MENU
        if (isset($p) && 'trans' === $p && $_SESSION['loginOK'] === true){
            include 'core/includes/translation_admin_controler.php';
        }

        // SETTINGS / PAYPAL MENU    
        if (isset($p) && 'paypal' === $p && $_SESSION['loginOK'] === true){
            include 'core/includes/paypal_admin_controler.php';
        }

        ?>



    </div> <!-- container -->



    <!-- JavaScript
      ================================================== -->
    <script>
        CKEDITOR.replace('texte_article');
    </script>	

    <!-- mediaquery -->
    <script src="addons/js/css3-mediaqueries.min.js"></script>
    <!-- jquery -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <!-- SmartMenus jQuery plugin -->
    <script type="text/javascript" src="addons/js/jquery.smartmenus.min.js"></script>
    <!-- SmartMenus jQuery Bootstrap Addon -->
    <script type="text/javascript" src="addons/bootstrap/jquery.smartmenus.bootstrap.js"></script>
    <!-- bootstrap JS -->
    <!--[if lt IE 9]>		
    <script type="text/javascript" src="css/bootstrap/js/bootstrap.min.js"></script>	
    <![endif]-->

    <!-- Gestion des boultons validation formulaires -->
    <script src="addons/js/jqBootstrapValidation.js"></script>
    <script>
        $(function () {
            $("input,select,textarea").not("[type=submit]").jqBootstrapValidation();
        });
    </script>	

</body>
</html>