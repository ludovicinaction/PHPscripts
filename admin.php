<?php

header('content-type: text/html; charset=utf-8');

session_start();
require_once 'core/SPDO_admin.class.php';
require_once 'core/blog/classes/articles.class.php';
require_once 'core/Securite.class.php';
require_once 'core/admin/admin.class.php';
require_once 'core/CommunDbRequest.trait.php';

define('SITE_ROOT', realpath(dirname(__FILE__)));

setlocale(LC_TIME, "fr_FR", "fr_FR@euro", "fr", "FR", "fra_fra", "fra");
?>
<!doctype html>
<html lang="fr">
    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
<?php
$oMetaArt = new Articles;
?>

        <!-- *** CSS *** -->
        <link rel="stylesheet" href="css2/cssgeneral-s1.css">
        <!-- Gestion des boutons validation formulaires -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="css2/jquery.floating-social-share.min.css" />	

        <script type="text/css" />	
        textarea.cke_source {
        white-space: pre-wrap;
        }
    </script>

    <!-- JS -->
    <script src="ckeditor/ckeditor.js"></script>

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
                            $oAdmin = new Admin();  
                            $aLang = $oAdmin -> getSetting();
                            $lang = $aLang['language'];
                            $aItems = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'MENU');
                            $lib_conf = $aItems[$lang]['lib_config'];
                            $lib_set_cat = $aItems[$lang]['lib_set_cat'];
                            $lib_crea_post = $aItems[$lang]['lib_crea_post'];
                            $lib_adm_post = $aItems[$lang]['lib_adm_post'];
                            $lib_adm_com = $aItems[$lang]['lib_adm_com'];                            
                            $lib_trans = $aItems[$lang]['lib_translation'];
                            echo "<li><a href='admin.php?p=trans&c=init'>$lib_trans</a></li>";
                            echo "<li><a href='#''>Blog</a>";
                                echo "<ul class='dropdown-menu'>";                                    
                                    echo "<li><a href='admin.php?p=gest_art&a=config'>$lib_conf</a></li>";
                                    echo "<li><a href='admin.php?p=gest_art&a=gest_cat&c=init'>$lib_set_cat</a></li>";
                                    echo "<li role='separator' class='divider'></li>";
                                    echo "<li><a href='admin.php?p=gest_art&a=creer'>$lib_crea_post</a></li>";
                                    echo "<li><a href='admin.php?p=gest_art&a=modif'>$lib_adm_post</a></li>";
                                    echo "<li role='separator' class='divider'></li>";
                                    echo "<li><a href='admin.php?p=gest_art&a=gest_com&c=init'>$lib_adm_com</a></li>";
                                    ?>
                                </ul>
                            </li>
                            <li><a href="#">Page 2</a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Page 2-1</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="#">Page 2-2</a></li>
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


       

       /*
         * *****************************
         * "BLOG" MODULE ADMINISTATION
         * *****************************
         */

echo "<br><br><br><br><br>";

        $oAdmin = new Admin();  
       
        $aSet = $oAdmin -> getSetting();
        $lang = $aSet['language'];
        $website = $aSet['websitehost'];

        $aItems = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'HOME');

       // $_GET filtering variables
       // p:page ( module name )
       $p       = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
            // a: action ( submenu )    
            $a       = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_STRING);
            // c: Choice (display, valid, delete)
            $c       = filter_input(INPUT_GET, 'c', FILTER_SANITIZE_STRING);
            // eng :Registration Application / conf : recording validation
            $eng     = filter_input(INPUT_GET, 'eng', FILTER_SANITIZE_STRING);
            $conf    = filter_input(INPUT_GET, 'conf', FILTER_SANITIZE_STRING);
            //id (id_art or id_com or id_cat...)      
            $id      = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $valid   = filter_input(INPUT_GET, 'valid', FILTER_SANITIZE_STRING);
       
            // t: type (comment 'com' or answer 'rep')
            $t       = filter_input(INPUT_GET, 't', FILTER_SANITIZE_STRING);
       
            $v       = filter_input(INPUT_GET, 'v', FILTER_SANITIZE_NUMBER_INT);

        //Filter $_POST values
        $p_lang      = filter_input(INPUT_POST, 'opt-lang', FILTER_SANITIZE_STRING);
        $p_host      = filter_input(INPUT_POST, 'hostname', FILTER_SANITIZE_STRING);
        //translations
        $search_module = filter_input(INPUT_POST, 'search_module', FILTER_SANITIZE_STRING);
        $search_lang = filter_input(INPUT_POST, 'search_lang', FILTER_SANITIZE_STRING);
        $search_office = filter_input(INPUT_POST, 'search_office', FILTER_SANITIZE_STRING);
        $search_type = filter_input(INPUT_POST, 'search_type', FILTER_SANITIZE_STRING);

     
        if (isset ($_POST['val_update'])) $bUpdate = filter_input(INPUT_POST,'val_update', FILTER_VALIDATE_BOOLEAN);
        else $bUpdate = FALSE;

        if ( !isset($p) && $_SESSION['loginOK'] === true ) {
            if ($bUpdate == FALSE) include 'core/admin/view/home-view.php';

            if ($bUpdate) $oAdmin->save_setting($p_lang, $p_host);
        }   
        elseif (isset($p) && 'gest_art' === $p && $_SESSION['loginOK'] === true) {
            
            $oArticles = new Articles;
            // Get message with translation
            $aMsgPost = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'MSG_POSTS_ADMIN'); 
            
            //"BLOG" Menu => Viewing post management table
            if (isset($a) && 'modif' == $a) {
                
                // If no item is chosen then display management table
                if (!isset($id)) {

                    // Search All posts without pagination
                    $oArticles->ReadAllArticles('admin', 0);

                    // Display form
                    if ('yes' != isset($eng) && ('modif' == isset($a))) {
                        include 'core/blog/views/admin-blog-articles.php';
                    }
                }

                //  *** POSTS MODIFICATION Mode *** 

                if (isset($id)) {

                    if (!isset($eng)) { // Does not display the form after having validated.
                        $oArticles->ReadOneArticle($id);
                        $aArticle = $oArticles->getPostData();
                        include 'core/blog/views/form-create-article.php';
                    }
   
                    // Save data post and request confirmation of registration after validation post
                    if ((isset($eng) && 'yes' == $eng) && (!isset($conf))) {
                        $oArticles->SaveArticlesData();
                        $btOk = 'admin.php?p=gest_art&a=modif&id=' . $id . '&eng=yes&conf=yes';
                        $oArticles->RequestConfirmation('modif', $aMsgPost[$lang]['msg_update_confirm'], $btOk, 'admin.php?p=gest_art', $lang);                                                                    
                    }

                    // If confirmation recording then save base.  
                    if (isset($conf) && 'yes' == $conf) {                
                       $bSauveOK = $oArticles->SaveArticle('modif', $aMsgPost[$lang]['msg_result_ok'], $aMsgPost[$lang]['msg_result_ko']);
                    }               
                }
            } // Post administration end
            elseif (isset($a) && 'lire' === $a) {

                // *** DISPLAY POST Mode ***

                $oArticles->ReadOneArticle($id);
                include 'core/blog/views/display-one-article.php';
                exit;
            } elseif (isset($a) && 'creer' == $a) {

                // *** CREATE POST MODE ***	

                if (!isset($eng)) {
                    include 'core/blog/views/form-create-article.php';
                }

                if (isset($eng) && (!isset($conf))) {
                    // Data backup only after validation form
                    $oArticles->SaveArticlesData();
                    $oArticles->RequestConfirmation('creer', $aMsgPost[$lang]['msg_creat_confirm'], 'admin.php?p=gest_art&a=creer&eng=yes&conf=yes', 'admin.php?p=gest_art&a=modif', $lang);
                } elseif (isset($eng) && isset($conf)) {
                    $bSauveOK = $oArticles->SaveArticle('creer', $aMsgPost[$lang]['msg_result_ok'], $aMsgPost[$lang]['msg_result_ko']);
                }
            } elseif (isset($a) && 'supp' == $a) {

                // 'DELETE POST' mode	
                if (!isset($conf)) {
					$sToken = $oSecure->create_token();
					
                    $btOk = 'admin.php?p=gest_art&a=supp&id=' . $id . '&conf=yes&token='.$_SESSION['token'];
                    $oArticles->RequestConfirmation('supp', $aMsgPost[$lang]['msg_delete_confirm'], $btOk, 'admin.php?p=gest_art&a=modif', $lang);
                } else {
                    $req = 'delete from blog_articles where id_art=' . $id;
                    $oAdmin->DeleteInformation($req, 'admin.php?p=gest_art&a=modif');
                }
            }
            
            // *** "Configuration" Submenu ***

            // Configuration form display
            elseif (isset($a) && 'config' === $a) {

                if (isset($eng)) {                    
                    $oArticles->SaveConfig();
                    $oArticles->ReadBlogConfig();  
                }else{
                    $oArticles->ReadBlogConfig(); 
                }         
                include 'core/blog/views/form-setting-blog.php';
            }   // config

            // *** Menu "Comments administration" ***
            elseif (isset($a) && 'gest_com' === $a  ){
                $aMsgCmt = array();
                $aMsgCmt = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'MSG_COMMENTS_ADMIN'); 
                // Submenu initial
                if ( isset($c) && 'init' === $c ){
                    $aComm = $oArticles->ReadAllComments();
                    include 'core/blog/views/admin-blog-comments.php';
                // Delete comments
                }elseif ( isset($c) && 'delete' === $c ) {
                    if(!isset($valid)) {
                        $sToken = $oSecure->create_token();
                        $btOk = 'admin.php?p=gest_art&a=gest_com&id=' . $id . '&t='. $t . '&c=delete&valid&token=' . $_SESSION['token'];
                        $oArticles->RequestConfirmation('supp', $aMsgCmt[$lang]['msg_delete_confirm'], $btOk, 'admin.php?p=gest_art&a=gest_com&c=init', $lang);
                    }
                    else{ // Delete confirm
                        if ($t == 'com') $req = 'delete from blog_comments where id_com=' . $id;
                        elseif ($t == 'rep') $req = 'delete from blog_reply where id_rep=' . $id;
                        $oAdmin->DeleteInformation($req, 'admin.php?p=gest_art&a=gest_com&c=init');
                    }        
                }
                // Display a comment
                elseif ( isset($c) && 'display' === $c){
                    $aComm =  $oArticles -> ReadOneComment($id, $t);
                    include 'core/blog/views/display-one-comments.php';
                }
                // Comments validation
                elseif (isset ($c) && 'valid' === $c){
                    $msg_email_ok = $aMsgCmt[$lang]['msg_cmt_email_ok'];
                    $msg_email_ko = $aMsgCmt[$lang]['msg_cmt_email_ko'];
                    if ( !isset($eng) ) $oArticles -> ConfirmValidateComments($id, $t, $v, $msg_email_ok, $msg_email_ko);
                    else $oArticles -> ValidateComment($id, $t, $aMsgCmt[$lang]['lib_resultOK'], $aMsgCmt[$lang]['lib_resultKO']);
                }
            } 
                
            // *** Submenu "Category admnistration" ***    
            elseif (isset($a) && 'gest_cat' === $a){
                $aMsg = array();
                $aMsg = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'MSG_CAT_ADMIN'); 
                $nom_cat = filter_input(INPUT_POST, 'nom_cat', FILTER_SANITIZE_STRING);
                if (isset($nom_cat)) $_SESSION['nom_cat'] = $nom_cat;

                if ( (!isset ($valid)) ) {
                    $aCat = $oArticles -> getCategoryData();
                    include 'core/blog/views/admin-blog-category.php';
                }   
                else{
                    if (isset($c) && 'update' === $c){
                        if ( isset($valid) && 'no' === $valid ){
                            $btOk = "admin.php?p=gest_art&a=gest_cat&c=update&valid=yes";
                            $oArticles->RequestConfirmation('modif', $aMsg[$lang]['msg_update_confirm'], $btOk, 'admin.php?p=gest_art&a=gest_cat&c=init', $lang);                        
                        }
                        else $oArticles -> UpdateCategory();      
                    }
                    elseif (isset($c) && 'add' === $c){
                        if ( isset($valid) && 'no' === $valid ){
                            $btOk = "admin.php?p=gest_art&a=gest_cat&c=add&valid=yes";
                            $oArticles->RequestConfirmation('modif', $aMsg[$lang]['msg_creat_confirm'], $btOk, 'admin.php?p=gest_art&a=gest_cat&c=init', $lang);
                        }
                        else $oArticles -> CreateCategory();
                    }
                    elseif (isset($c) && 'delete' === $c){
                        if ( isset ($valid) && 'no' === $valid ){
                            $sToken = $oSecure->create_token();
                            $btOk = 'admin.php?p=gest_art&a=gest_cat&id=' . $id . '&t='. $t . '&c=delete&valid&token=' . $_SESSION['token'];        
                            $oArticles->RequestConfirmation('supp', $aMsg[$lang]['msg_delete_confirm'], $btOk, 'admin.php?p=gest_art&a=gest_cat&c=init', $lang);                            
                        }
                        else{
                            $sReq = 'delete from blog_cat_article where id_cat=' . $id;
                            $oAdmin -> DeleteInformation($sReq, 'admin.php?p=gest_art&a=gest_cat&c=init');
                        }
                    }                            
                }
            }  
        } // End BLOG module
        elseif (isset($p) && 'trans' === $p && $_SESSION['loginOK'] === true){
            //  *** TRANSLATION MENU ***
            $aMsg = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'MSG_TRANS'); 
            

            if ( (!isset ($valid)) ) {

                if (isset($c) && 'search' === $c){
                    $aTrans = $oAdmin->getSearchTranslations($search_module, $search_lang, $search_office, $search_type);
                }
                else{
                    $aTrans = $oAdmin->getAllTranslations();
                }
              
                include 'core/admin/view/adm-translation.php';
            }
            else{
                $oAdmin->filterPostValues();

                if (isset($c) && 'update' === $c){                    
                    if ( isset($valid) && 'no' === $valid ){
                        $btOk = "admin.php?p=trans&a=adm_trans&c=update&valid=yes";
                        $oAdmin->RequestConfirmation('modif', $aMsg[$lang]['msg_update_confirm'], $btOk, 'admin.php?p=trans&a=adm_trans&c=init', $lang);
                    }
                    else $oAdmin -> UpdateTranslation();      
                }
                elseif (isset($c) && 'add' === $c){
                    if ( isset($valid) && 'no' === $valid ){
                        $btOk = "admin.php?p=trans&a=adm_trans&c=add&valid=yes";
                        $oAdmin->RequestConfirmation('modif', $aMsg[$lang]['msg_creat_confirm'], $btOk, 'admin.php?p=trans&a=adm_trans&c=init', $lang );
                    }
                    else{
                        $oAdmin->CreateTranslation();
                    }    
                }
                elseif (isset($c) && 'delete' === $c) {
                    $sReq = 'delete from adm_translation where id=' . $id;
                    if ( isset ($valid) && 'no' === $valid ){
                        $sToken = $oSecure->create_token();
                        $btOk = 'admin.php?p=trans&a=adm_trans&id=' . $id . '&t='. $t . '&c=delete&valid&token=' . $_SESSION['token'];  
                        $oAdmin->RequestConfirmation('supp',  $aMsg[$lang]['msg_delete_confirm'], $btOk, 'admin.php?p=transt&a=adm_trans&c=init', $lang);
                    }
                    else{    
                        $oAdmin-> DeleteInformation($sReq, 'admin.php?p=trans&a=adm_trans&c=init');
                    }    

                }    

            }

        }// fin traduction

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

