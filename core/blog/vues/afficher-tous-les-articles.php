<?php
/**
 * Affichage HTML de tous les articles.
 * en fonction de la résolution de l'écran
 * @package BLOG
 * @category Vue du module "Blog"
 */

//Récupération des paramétrages d'affichage des vignettes en fonction de la résolution de l'écran
$oArticles->lecture_config();
$iXs = (int) $oArticles->aff_xs;
$iSm = (int) $oArticles->aff_sm;
$iMd = (int) $oArticles->aff_md;
$iLg = (int) $oArticles->aff_lg;

echo "<div class='row'>";

foreach ($aArticles as $ligne => $val){	
	$vignette = $val['vignette_art'];
	echo "<div class='col-xs-$iXs col-sm-$iSm col-md-$iMd col-lg-$iLg'>";
		echo "<div class='thumbnail'>";						
			echo '<a href=\'blog.php?id=' . $val['id_art'] . '\'><img src="data:image/jpeg;base64,'. base64_encode($vignette) .'" class=\'img-rounded\' /></a>';			
			echo "<div class='caption'>";
				echo '<h6><b>' . $val['titre_art'] . '</b></h6>';
				// Pour le "résumer" : on affiche uniquement les 90 permiers caractères entier.				
				$resume = $val['resum_art'];
				if (strlen($resume) >90 ) {
					$pos=mb_strpos($resume, ' ', 90); 
					echo '<h6><i>' . substr($resume, 0, $pos ) . '...</i></h6>';
				}
				else echo '<h6><i>' . $resume . '...</i></h6>';
				echo "<span class='label label-info'>";
					echo utf8_encode($val['date_crea_art']);
				echo "</span>";
				echo '<a href=\'blog.php?id=' . $val['id_art'] . '\' class=\'pull-right\'><span class=\'glyphicon glyphicon-search\'></span> Lire la suite</a>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
echo "</div>";	//row