<?php

header('content-type: text/html; charset=utf-8');

session_start();
require_once 'core/SPDO_admin.class.php';
require_once 'core/blog/classes/articles.class.php';
require_once 'core/Securite.class.php';

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
        <!-- Gestion des boultons validation formulaires -->
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
                        <a class="navbar-brand" href="#">Back-office</a>
                    </div>
                    <div class="navbar-collapse collapse"> 

                        <ul class="nav navbar-nav">
                            <li><a href="admin.php" class="glyphicon glyphicon-home"></a></li>

                            <li><a href="#">Blog</a>
                                <ul class="dropdown-menu">
                                    <li><a href="admin.php?p=gest_art&a=config">Configuration</a></li>
                                    <li><a href="admin.php?p=gest_art&a=gest_cat&c=init">Gestion des catégories</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="admin.php?p=gest_art&a=creer">Créer article</a></li>
                                    <li><a href="admin.php?p=gest_art&a=modif">Gestion des articles</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="admin.php?p=gest_art&a=gest_com&c=init">Gestion des commentaires</a></li>
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

                    <?php include 'core/connect.inc.php'; // *** Gestion de la connexion utilisateur  ?>

                    </div>	
                </div>
            </div>
        </div>		

        <?php


       

       /*
         * ***********************
         * GESTION MODULE "BLOG"
         * *********************** 
         */

       //Filtrage des variables $_GET
       // p:page (module général)
       $p       = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
            // a: action (sous menu)    
            $a       = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_STRING);
            // c: Choix (display, valid, delete)
            $c       = filter_input(INPUT_GET, 'c', FILTER_SANITIZE_STRING);
            // eng :Demande d'enregistrement / conf : Confirmation d'enrgistrement
            $eng     = filter_input(INPUT_GET, 'eng', FILTER_SANITIZE_STRING);
            $conf    = filter_input(INPUT_GET, 'conf', FILTER_SANITIZE_STRING);
            //id (id_art or id_com or id_cat...)      
            $id      = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $valid   = filter_input(INPUT_GET, 'valid', FILTER_SANITIZE_STRING);
       
            // t: type (commentaire 'com' ou réponses 'rep')
            $t       = filter_input(INPUT_GET, 't', FILTER_SANITIZE_STRING);
       
            $v       = filter_input(INPUT_GET, 'v', FILTER_SANITIZE_NUMBER_INT);
       
            echo "<div class='margintop70'></div>";


        if (isset($p) && 'gest_art' === $p && $_SESSION['loginOK'] === true) {
            
            $oArticles = new Articles;

            //Menu "GESTION ARTICLE" => Affichage du tableau de gestion des articles
            if (isset($a) && 'modif' == $a) {

                //Si aucun article n'a été choisi alors afficher tableau de gestion
                if (!isset($id)) {

                    //Recherche de tous les articles sans pagination
                    $aArticles = $oArticles->LireLesArticles('admin', 0);

                    //Lecture des catégories	
                    $alistCat = $oArticles->LireCategories();

                    //Afficher formulaire
                    if ('yes' != isset($eng) && ('modif' == isset($a))) {
                        include 'core/blog/vues/adm-gestion-blog.php';
                    }
                }

                //Mode ' *** MODIFICATION DES ARTICLES *** '

                if (isset($id)) {

                    if (!isset($eng)) { // On n'affiche pas le formulaire aprés l'avoir validé.
                        $aArticle = $oArticles->LireUnArticle($id);
                        include 'core/blog/vues/formulaire_creation_article.php';
                    }
   
                    //Enregistrer données article en session et Demande confirmation aprés validation d'enregistrement de l'article
                    if ((isset($eng) && 'yes' == $eng) && (!isset($conf))) {
                        $oArticles->sauvDonneesArticle();
                        $btOk = 'admin.php?p=gest_art&a=modif&id=' . $id . '&eng=yes&conf=yes';
                        $oArticles->DemanderConfirmation('modif', 'Confirmez-vous la mise à jour ?', $btOk, 'admin.php?p=gest_art');
                    }

                    //Si confirmation d'enregistrement alors sauvegarder en base.   
                    if (isset($conf) && 'yes' == $conf) {
                       $bSauveOK = $oArticles->SauveArticle('modif');
                    }               
                }
            } // fin Menu "gestion article"
            elseif (isset($a) && 'lire' === $a) {

                //Mode ' *** LECTURE DE L'ARTICLE ***'

                $aArticle = $oArticles->LireUnArticle($id);
                include 'core/blog/vues/afficher-un-article.php';
                exit;
            } elseif (isset($a) && 'creer' == $a) {

                //Mode ' *** CREATION ARTICLE ***'	

                if (!isset($eng)) {
                    include 'core/blog/vues/formulaire_creation_article.php';
                }

                if (isset($eng) && (!isset($conf))) {
                    //Sauvegarde des données uniquement aprés validation formulaire
                    $oArticles->sauvDonneesArticle();
                    $oArticles->DemanderConfirmation('creer', 'Confirmez-vous la création de l\'article ?', 'admin.php?p=gest_art&a=creer&eng=yes&conf=yes', 'admin.php?p=gest_art&a=modif');
                } elseif (isset($eng) && isset($conf)) {
                    $bSauveOK = $oArticles->SauveArticle('creer');
                }
            } elseif (isset($a) && 'supp' == $a) {

                //Mode 'SUPPRESSION ARTICLE'	
                if (!isset($conf)) {
					$sToken = $oSecure->creation_jeton();
					
                    $btOk = 'admin.php?p=gest_art&a=supp&id=' . $id . '&conf=yes&token='.$_SESSION['token'];
                    $oArticles->DemanderConfirmation('supp', 'Confirmez-vous la suppression de l\'article ?', $btOk, 'admin.php?p=gest_art&a=modif');
                } else {
                    $req = 'delete from articles where id_art=' . $id;
                    $oArticles->SupprimerInformation($req, 'admin.php?p=gest_art&a=modif');
                }
            }
            
            // *** Sous-menu "Configuration" ***

            // Affichage formulaire de configuration:
            elseif (isset($a) && 'config' === $a) {

                if (isset($eng)) {                    
                    $oArticles->sauv_config();
                    $oArticles->lecture_config();  
                }else{
                    $oArticles->lecture_config(); 
                }         
                include 'core/blog/vues/formulaire-config-blog.php';
            }   // config

            // *** Menu "Gestion des commentaires" ***
            elseif (isset($a) && 'gest_com' === $a  ){
                
                // sous Menu initial
                if ( isset($c) && 'init' === $c ){
                    $aComm = $oArticles->LireTousLesCommentaires();
                    include 'core/blog/vues/adm-gestion-commentaires.php';
                // Suppression d'un commentaire    
                }elseif ( isset($c) && 'delete' === $c ) {
                    if(!isset($valid)) {
                        $sToken = $oSecure->creation_jeton();
                        $btOk = 'admin.php?p=gest_art&a=gest_com&id=' . $id . '&t='. $t . '&c=delete&valid&token=' . $_SESSION['token'];
                        $oArticles->DemanderConfirmation('supp', 'Confirmez-vous la suppression du commentaire ?', $btOk, 'admin.php?p=gest_art&a=gest_com&c=init');
                    }
                    else{ // Confirmation suppression
                        if ($t == 'com') $req = 'delete from commentaires_blog where id_com=' . $id;
                        elseif ($t == 'rep') $req = 'delete from commentaires_rep where id_rep=' . $id;
                        $oArticles->SupprimerInformation($req, 'admin.php?p=gest_art&a=gest_com&c=init');
                    }        
                }
                // Affichage un commentaire
                elseif ( isset($c) && 'display' === $c){
                    $aComm =  $oArticles -> LireCommentaire($id, $t);
                    include 'core/blog/vues/afficher-un-commentaire.php';
                }
                // Valider un commentaire
                elseif (isset ($c) && 'valid' === $c){
                    if ( !isset($eng) ) $oArticles -> ConfirmValideCommentaire($id, $t, $v);
                    else $oArticles -> ValiderCommentaire($id, $t);
                }
            } 
                
            // *** Menu "Gestion des catégories" ***    
            elseif (isset($a) && 'gest_cat' === $a){
            
                $nom_cat = filter_input(INPUT_POST, 'nom_cat', FILTER_SANITIZE_STRING);
                if (isset($nom_cat)) $_SESSION['nom_cat'] = $nom_cat;

                if ( (!isset ($valid)) ) {
                    $aCat = $oArticles -> LireCategories();
                    include 'core/blog/vues/adm-gestion-categories.php';
                }   
                else{
                    if (isset($c) && 'update' === $c){
                        if ( isset($valid) && 'no' === $valid ){
                            $btOk = "admin.php?p=gest_art&a=gest_cat&c=update&valid=yes";
                            $oArticles->DemanderConfirmation('modif', 'Confirmez-vous la modification de la catégorie ?', $btOk, 'admin.php?p=gest_art&a=gest_cat&c=init');
                        }
                        else $oArticles -> UpdateCategory();      
                    }
                    elseif (isset($c) && 'add' === $c){
                        if ( isset($valid) && 'no' === $valid ){
                            $btOk = "admin.php?p=gest_art&a=gest_cat&c=add&valid=yes";
                            $oArticles->DemanderConfirmation('modif', 'Confirmez-vous la création de la catégorie ?', $btOk, 'admin.php?p=gest_art&a=gest_cat&c=init');
                        }
                        else $oArticles -> CreateCategory();
                    }
                    elseif (isset($c) && 'delete' === $c){
                        if ( isset ($valid) && 'no' === $valid ){
                            $sToken = $oSecure->creation_jeton();
                            $btOk = 'admin.php?p=gest_art&a=gest_cat&id=' . $id . '&t='. $t . '&c=delete&valid&token=' . $_SESSION['token'];        
                            $oArticles->DemanderConfirmation('supp', 'Confirmez-vous la suppression de la catégorie ?', $btOk, 'admin.php?p=gest_art&a=gest_cat&c=init');                            
                        }
                        else{
                            $sReq = 'delete from cat_article where id_cat=' . $id;
                            $oArticles -> SupprimerInformation($sReq, 'admin.php?p=gest_art&a=gest_cat&c=init');
                        }
                    }                            
                }



            }  


        } // Fin Module blog
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

