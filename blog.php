<?php

header( 'content-type: text/html; charset=utf-8' );

session_start();
require_once 'core/SPDO.class.php';
require_once 'core/blog/classes/articles.class.php';
require_once 'core/utilitaires.class.php';
require_once 'core/Pagination.class.php';
require_once 'core/admin/admin.class.php';

setlocale(LC_TIME, "fr_FR", "fr_FR@euro", "fr", "FR", "fra_fra", "fra");
?>
<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
        
	<?php
	// Get administration setting
    $oAdmin = new Admin();      
    $aSet = $oAdmin -> getSetting();
    $smtp = $aSet['smtp_sendmail'];
    $port = $aSet['port_sendmail'];
    $email_send = $aSet['email_sendmail'];
 	
 	$oPagination = new Pagination();
    $lang = $aSet['language'];
    $host = $aSet['websitehost'];

    ini_set('SMTP', $smtp);
    ini_set('smtp_port', $port);
    ini_set('sendmail_from', $email_send);

	// Filtering $_GET et $_POST variables
	$id 	= filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
	$cat 	= filter_input(INPUT_GET, 'cat', FILTER_SANITIZE_NUMBER_INT);
	$new	= filter_input(INPUT_GET, 'new', FILTER_SANITIZE_STRING);
	$rep	= filter_input(INPUT_GET, 'rep', FILTER_SANITIZE_STRING);
	// $_POST variables
	$nom 	= filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
	$mail 	= filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_URL);
	$siteweb= filter_input(INPUT_POST, 'siteweb', FILTER_SANITIZE_URL);
	$contenu= filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
	
		$oMetaArt = new Articles;
		$oUtil = new Utilitaires;
		
		// Meta-data
		if (isset($id)) {
			$aMetaData = $oMetaArt->ReadMetaData($_GET['id']);
			$oUtil->DisplayPageMetaData($aMetaData['titre_art'], $aMetaData['resum_art'], $aMetaData['keywords_art'] );
			unset($oMetaArt);
			unset($oUtil);
		}
		// Default meta-data
		elseif (!isset($_GET['id'])){
			$oUtil->DisplayPageMetaData('Présentation des articles', 'Description de la page', 'php, html, css, Mysql' );
		}
	?>

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
		 
 <?php


    $oArticles = new Articles;

	if (!isset($id)){
	    // Reading categories and combo display for selecting categories
	    $alistCat = $oArticles->getCategoryData();
		include 'core/blog/views/form-categories.php';    
	}
	
	if (isset($cat)) {
		// If a category is specified then display the articles in this category
		$oArticles->ReadAllArticles('util', $cat);	
		$aConfigValues = $oArticles->getConfigValues();
		include 'core/blog/views/display_all_articles.php';
		
		// Pagination
		$nbTotArt = $aConfigValues['total_nbr_display'];
		$nbrPerPage = $aConfigValues['art_page'];
		$oPagination->DisplayPagination($nbTotArt, $nbrPerPage, 'blog.php', "cat=$cat");
	}
	elseif ((!isset($cat) OR $cat == 'Tous') AND !isset($id)) {
		// If no selected category or 'All' is selected then display all articles (all categories)	
		$aArticles = $oArticles->ReadAllArticles('util', 0);

		$aConfigValues = $oArticles->getConfigValues();
		include 'core/blog/views/display_all_articles.php';

		// Result Pagination
		$nbTotArt = $aConfigValues['total_nbr_display'];
		$nbrPerPage = $aConfigValues['art_page'];
  		$oPagination->DisplayPagination($nbTotArt, $nbrPerPage, 'blog.php', '');   
	}
	// otherwise if a post is displaying
	elseif (isset($id)){	
		// Recording a comment or response (if entry form)
		$aMsg = $oAdmin->getItemTransation('BLOG', 'FRONT', $lang, 'MSG_COMMENTS_PUBLISH'); 

		if (isset($new)){
			// Registration new comment.			
			$oArticles->RecordNewComm($nom, $mail, $siteweb, $contenu, $id, 'new', $aMsg, $host);
		}
		
		if (isset($rep)){
			// Recording a response to a comment.
			$oArticles->RecordNewComm($nom, $mail, $siteweb, $contenu, $rep, 'rep', $aMsg, $host);
		}

		// Article display
		if ( (!isset($rep)) && (!isset($new))  ){

			$aArticle = $oArticles->ReadOneArticle($id);
			include 'core/blog/views/display-one-article.php';
				
			// Displaying comments
			$aComm = $oArticles->ReadComments($id);
			include 'core/blog/views/display-comments.php';		
		}				
	} 
    unset($oArticles);
?>


  <!-- JavaScript
    ================================================== -->
	<!-- jquery -->
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> -->
	<!-- mediaquery -->
	<script src="addons/js/css3-mediaqueries.min.js"></script>
	
	<!-- Gestion des boutons validation formulaires -->
	 <script src="addons/js/jqBootstrapValidation.js"></script>
	 <script>
        $(function() {
            $("input,select,textarea").not("[type=submit]").jqBootstrapValidation();
        });
    </script>


<script type="text/javascript" src="addons/js/jquery.floating-social-share.min.js"></script>

<?php if (isset($id)) { ?>
<script>
$("body").floatingSocialShare({
    place: "top-left", // alternatively top-right
    counter: false, // set to false for hiding the counters of buttons
    buttons: ["facebook", "twitter", "google-plus", "linkedin", "envelope"], // all of the currently avalaible social buttons
    title: document.title, // your title, default is current page's title
    url: window.location.href,  // your url, default is current page's url
    text: "Partager sur ", // the title of a tags
    description: $('meta[name="description"]').attr("content"), // your description, default is current page's description
    popup_width: 400, // the sharer popup width, default is 400px
    popup_height: 300 // the sharer popup height, default is 300px
});
</script>
<?php } ?>

</div>	
</body>
</html>