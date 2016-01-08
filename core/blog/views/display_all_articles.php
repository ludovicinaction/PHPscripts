<?php
/**
 * HTML display all items depending on the screen resolution
 * @package BLOG
 * @category BLOG module view
 */

$aItemsForm = $oAdmin->getItemTransation('BLOG', 'FRONT', $lang, 'HOME');

$iXs = (int) $aConfigValues['aff_xs'];
$iSm = (int) $aConfigValues['aff_sm'];
$iMd = (int) $aConfigValues['aff_md'];
$iLg = (int) $aConfigValues['aff_lg'];

echo "<div class='row'>";

foreach ($aArticles as $ligne => $val){	
	if ($lang == 'FR') $sDate = utf8_encode($val['date_crea_art']);
	elseif ($lang == 'EN') $sDate = date('d F', strtotime($val['date_crea_art']));

	$vignette = $val['vignette_art'];
	echo "<div class='col-xs-$iXs col-sm-$iSm col-md-$iMd col-lg-$iLg'>";
		echo "<div class='thumbnail'>";						
			echo '<a href=\'blog.php?id=' . $val['id_art'] . '\'><img src="data:image/jpeg;base64,'. base64_encode($vignette) .'" class=\'img-rounded\' /></a>';			
			echo "<div class='caption'>";
				echo '<h6><b>' . $val['titre_art'] . '</b></h6>';
				//For the "summary": we see only the first 90 characters around.				
				$resume = $val['resum_art'];
				if (strlen($resume) >90 ) {
					$pos=mb_strpos($resume, ' ', 90); 
					echo '<h6><i>' . substr($resume, 0, $pos ) . '...</i></h6>';
				}
				else echo '<h6><i>' . $resume . '...</i></h6>';
				echo "<span class='label label-info'>";
					echo $sDate;
				echo "</span>";				
				echo '<a href=\'blog.php?id=' . $val['id_art'] . '\' class=\'pull-right\'><span class=\'glyphicon glyphicon-search\'></span> ' . $aItemsForm[$lang]['txt_read_more'] . '</a>';

			echo '</div>';
		echo '</div>';
	echo '</div>';
}
echo "</div>";	//row