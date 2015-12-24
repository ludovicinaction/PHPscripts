<?php
/**
 * Affichage HTML des commentaires avec affichage coté client du formulaire de réponse.
 * @package BLOG
 * @category Vue du module "Blog"
 */
echo "<div class='row'>";
echo "<div class='col-sm-1 col-md-1 col-lg-1'></div>";
echo "<article class='col-sm-10 col-md-10 col-lg-10'>";

$bFormNew = false; //$bFormNew = true pour afficher le formulaire en bas de page

//Gestion de l'affichage du nombre de commentaire au singulier ou au pluriel (ajout du "s")
if ((int)$aArticle['somme-com']['somme'] > 1){
	echo '<br /><h2>' . $aArticle['somme-com']['somme'] .' commentaires :</h2>';
}elseif ((int)$aArticle['somme-com']['somme'] == 1) {
	echo '<br /><h2>' . $aArticle['somme-com']['somme'] .' commentaire :</h2>';	
}

foreach($aComm as $val)
{
	$id = $val['id_com'];
?>
	
	<script type="text/javascript">
	// On attend que la page soit chargée - pour les COM
	jQuery(document).ready(function()
	{
	   // On cache la zone de texte
	   jQuery('#toggle<?php echo $id; ?>').hide();
	   // toggle() lorsque le lien avec l'ID #toggler est cliqué
	   jQuery('a#toggler<?php echo $id; ?>').click(function()
	  {
		  jQuery('#toggle<?php echo $id; ?>').toggle(400);
		  return false;
	   });
	});
	</script>
	
<?php	
	

	if (isset($val['nom_com'])) $id_com = $val['nom_com'];
	
	if (isset($_GET['id'])) $id_art = $_GET['id']; 
	else echo "id_art n'est pas connu"; 
	
	echo "<div class='thumbnail'>";

		$image = $val['photo_com'];
		
		if (is_null($val['photo_com']))
			echo "<img class='img-blog pull-left' src='img/blog/photo_anonyme.jpg' />";
		else
			echo '<img class=\'img-blog pull-left\' src="data:image/jpeg;base64,'. base64_encode($image) .'" />';
		
		echo $val['nom_com'].'<br />' ;                        
                echo '<small><i>' . utf8_encode( strftime('%d %B %Y', strtotime($val['date_com'])) ) . '</i></small>';   
                echo '<p class=txt-blog>'.nl2br($val['texte_com']) . '</p>';
		echo "<a href='#toggle$id' class='btn btn-primary' id='toggler$id'>Répondre</a>";
	echo "</div>";
	
	echo "<div id='toggle$id' style='display:none;'>";
		$bCom = true;
		include __DIR__.'//formulaire-commentaires.php';	
	echo "</div>";


	/*
	 * Affichage les réponses correspondant à chaque commentaire 
	 */
	
	$pReponses = $oArticles->LireReponses($val['id_com'], 'util');
	
	$aReponses = $pReponses->fetchAll(PDO::FETCH_ASSOC);

	//On trie les données par rapport à la clé "Ref_rep"
	$aRepTrie = $oArticles->multi_sort($aReponses, $key = 'ref_rep');
	
	/*
	 *	Affichages des réponses
	 */

	foreach($aRepTrie as $repval)
	{	
		$rep = $repval['id_rep'];
		
?>

		<script type="text/javascript">
			// On attend que la page soit chargée - pour les REP
			jQuery(document).ready(function()
			{
			   // On cache la zone de texte
			   jQuery('#toggle_rep<?php echo $rep; ?>').hide();
			   // toggle() lorsque le lien avec l'ID #toggler est cliqué
			   jQuery('a#lien_rep<?php echo $rep; ?>').click(function()
			  {
				  jQuery('#toggle_rep<?php echo $rep; ?>').toggle(400);
				  return false;
			   });
			});
		</script>

		<?php
	
		$iNiv = strlen($repval['ref_rep']);
		
		echo "<div class='thumbnail thumbnail$iNiv'>";
		
		$image = $repval['photo_rep'];
		
		if (is_null($repval['photo_type']))
			echo "<img class='img-blog pull-left' src='img/blog/photo_anonyme.jpg' />";
		else
			echo '<img class=\'img-blog pull-left\' src="data:image/jpeg;base64,'. base64_encode($image) .'" />';
		
			echo $repval['nom_rep'] . '<br />';
			echo '<small><i>' . utf8_encode( strftime('%d %B %Y', strtotime($repval['date_rep'])) ) . '</i></small>';   
			echo '<br /><p class=txt-blog>'.nl2br($repval['texte_rep']) . '</p>';
			echo "<a href='#toggle_rep$rep' class='btn btn-primary' id='lien_rep$rep'>Répondre</a>";	
		echo "</div>";		

		echo "<div id='toggle_rep$rep' style='display:none;'>";
			$bCom = false;
			include __DIR__.'//formulaire-commentaires.php';	
		echo "</div>";	
	}
	
} // fin du foreach

echo "</article>";
echo "<div class='col-sm-1 col-md-1 col-lg-1'></div>";
echo "</div>";

//Affichage formulaire commentaires de bas de page
echo "<div class='row'>";
	echo "<div class='col-sm-1 col-md-1 col-lg-1'></div>";
	echo "<div class='col-sm-10 col-md-10 col-lg-10'>";
	echo "<br /><br /><h3>Vous désirez publier un commentaire :</h3>";
	$bFormNew = true;
	include 'core/blog/vues/formulaire-commentaires.php';
	echo "</div>";
	echo "<div class='col-sm-1 col-md-1 col-lg-1'></div>";
echo "</div>";
