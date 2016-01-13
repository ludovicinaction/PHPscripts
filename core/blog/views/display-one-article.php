
<?php
/**
 * Affichage HTML d'un article
 * @package BLOG
 * @category Vue du module "Blog"
 */
$aArticle = $oArticles->getPostData();

$vignette = $aArticle['art']['vignette_art'];

if ($lang == 'FR') $sDate = utf8_encode($aArticle['art']['date_crea_art']);
elseif ($lang == 'EN') $sDate = date('d F', strtotime($aArticle['art']['date_crea_art']));

echo "<div class='row bottomArticle' >";
	echo "<div class='col-sm-1 col-md-1 col-lg-1 '></div>";
	echo "<article class='col-sm-10 col-md-10 col-lg-10'>";		
	echo '<p class=\'text-center\'><img src="data:image/jpeg;base64,'. base64_encode($vignette) .'" class=\'img-rounded\' /></p>';		
	
	echo "<h1><span class='text-left label label-info glyphicon glyphicon-time'>" . ' ' .  $sDate . '</span> ' . $aArticle['art']['titre_art'] . '</h1>';
	
	echo '<h5><em>'. $aArticle['art']['resum_art'] . '</em></h5>';
	
        $chaine = $aArticle['art']['contenu'];
        $chaine_aff = htmlspecialchars_decode($chaine);
        echo '<br /><br />' . $chaine_aff;
        
	echo "</article>";		

	echo "<div class='col-sm-1 col-md-1 col-lg-1 '></div>";		

echo "</div>";        





	