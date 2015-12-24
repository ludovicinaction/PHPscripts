<?php
/**
 * Affichage HTML du formulaire de création d'un article.
 * @package BLOG
 * @category Vue du module "Blog"
 */
if (isset($_GET['id'])){ 
	// *** Mode "Modification article" ***
	$bModif=true; 
	$id_art = $aArticle['art']['id_art'];
	$id_cat = $aArticle['art']['id_categorie'];
	$titre = htmlentities($aArticle['art']['titre_art'], ENT_QUOTES, 'utf-8');
	$vignette = $aArticle['art']['vignette_art'];
	$contenu = $aArticle['art']['contenu'];
	$resum_art =  htmlentities($aArticle['art']['resum_art'], ENT_QUOTES, 'utf-8');
	$keyswords = htmlentities($aArticle['art']['keywords_art'], ENT_QUOTES, 'utf-8');
	$date_pub = htmlentities($aArticle['art']['date_pub_art'], ENT_QUOTES, 'utf-8');
	$form_action = "<form enctype='multipart/form-data' action='admin.php?p=gest_art&a=modif&id=$id_art&eng=yes' method='post' class='col-md-12 col-lg-12 well'>";
}
else{ 
	// *** Mode "création article" ***
	$bModif=false;
	$id_cat='';
	$titre='';
	$contenu = '';
	$resum_art = '';
    $keyswords = '';
    $date_pub = '';
	$vignette = '';
	$form_action = "<form enctype='multipart/form-data' action='admin.php?p=gest_art&a=creer&eng=yes' method='post' class='col-md-12 col-lg-12 well'>";
}

$alistCat = $oArticles -> LireCategories();

echo "<div class='margintop70'></div>";
	
echo $form_action;
	//Catégorie 
	echo "<div class='control-group'>";
	echo "<label for='sel1'>Catégorie :</label>";
	echo "<select class='form-control' name='cat' maxlength='30'>";
	foreach ($alistCat as $ligne=>$val){
		$val_cat = $val['id_cat'];
		//echo "ID CAT : $id_cat et val_cat : $val_cat"	;
		if ($id_cat == $val['id_cat']) $selected = "selected='selected'";
		else $selected='';
		echo '<option value=\''.$val['id_cat'].'\' name=\'cat\' ' . $selected . ' >' . $val['nom_cat'] . '</option>';
	}
	echo "</select>";
	echo "</div>";
	
	//titre
	echo "<div class='control-group'>";
		echo "<label class='control-label'>Titre article</label>";
		echo "<div class='controls'>";
			echo '<input type=\'text\' value=\'' . $titre . '\' name=\'titre\' maxlength=\'70\' class=\'form-control\' required />';		
			echo "<p class='help-block'>Pas de guillemet dans le titre.</p>";
		echo "</div>";
	echo "</div>";

	//Image vignette
	echo "<div class='control-group'>";
		echo "<label class='texte'>Vignette</label>";
		echo "<input type='file' name='vignette' />";
		echo "<p class='help-block'></p>";
	echo "</div>";
	echo '<img src="data:image/jpeg;base64,'. base64_encode($vignette) .'" class=\'img-rounded\' />';
	
	//Description	
	echo "<div class='control-group'>";
		echo "<label class='control-label'>Description article</label>";
		echo "<div class='controls'>";
			echo '<input type=\'text\' value=\'' . $resum_art . '\' name=\'desc\' maxlength=\'170\' class=\'form-control\' required />';	
			echo "<p class='help-block'>Pas de guillemet dans la description</p>";
		echo "</div>";
	echo "</div>";

//Mots clés	
echo "<div class='control-group'>";
	echo "<label class='control-label'>Mots clé article</label>";
	echo "<div class='controls'>";					
		echo '<input type=\'text\' value=\'' . $keyswords . '\' name=\'keyword\' maxlength=\'200\' class=\'form-control\' />';
		echo "<p class='help-block'></p>";
	echo "</div>";		
echo "</div>";

//Date de publication
echo "<div class='control-group'>";
	echo "<label class='control-label'>Date de publication</label>";			
	echo "<div class='controls'>";
		echo '<input type=\'text\' value=\'' . $date_pub . '\' name=\'date_pub\' maxlength=\'10\' class=\'form-control\' />';
		echo "<p class='help-block'>Ne pas indiquer de date (JJ/MM/AAAA) pour une publication immédiate</p>";
	echo "</div>";
echo "</div>";

//Contenu de l'article
echo "<div class='form-group'>";			
	echo "<label type='texte'>Contenu article : </label>";
	echo "<textarea name='texte_article' id='texte_article' rows='20' maxlength='10000' class='form-control'>";
	echo $contenu;
	echo "</textarea>";
echo "</div>";			

echo "<button class='btn btn-primary' type='submit'>Publier article</button>";
echo "</form>";

	
	
		
		
	
	
	
	
	