 <?php
/**
 * Affichage HTML du formulaire de publication d'un commentaire.
 * @package BLOG
 * @category Vue du module "Blog"
 */

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// init translation
$sLang = filter_var($_SESSION['lang'], FILTER_SANITIZE_STRING);

//Items Translation
$aItems = $oAdmin->getItemTransation('BLOG', 'FRONT', $sLang, 'FORM1');

if (!$bFormNew){

	if (isset($aArticle['id_com'])) $id_com = $aArticle['id_com'];

	if (isset($id)) $id_art = $id; 

	if ($bCom) $nom_com = $val['nom_com'];
	else $nom_com = $repval['nom_rep'];
}	


	
if ($bFormNew) {
	echo '<form enctype=\'multipart/form-data\' action=blog.php?id=' . $_GET['id'] . '&new=1 method=\'post\' class=\'col-md-6 col-lg-6 well\'>';
}
else{	
	$id_com = $val['id_com'];

	$id_art = $aArticle['art']['id_art'];

	echo "<form enctype='multipart/form-data' action='blog.php?id=$id_art&rep=$id_com' method='post' class='col-md-12 col-lg-12 well'>";

	}

	if (!$bFormNew) echo "<legend> {$aItems[$sLang]['txt_frm_res']} $nom_com</legend>";
	
	//formulaire
	echo "<div class='control-group'>";				
		echo "<label class='control-label'> {$aItems[$sLang]['lib_input_name']} </label>";
		echo "<div class='controls'>";	
			echo "<input type='text' name='nom' maxlength='50' class='form-control' required />";
			echo "<p class='help-block'></p>";				
		echo "</div>";	
	echo "</div>";	
	
	//Photo
	echo"<div class='form-group'>";
		echo"<label for='texte'> {$aItems[$sLang]['lib_input_picture']} </label>";
		echo"<input type='file' name='imagefichier'>";
	echo "</div>";
	
	// Email
	echo "<div class='control-group'>";
		echo "<label class='control-label'>Email : </label>";
		echo "<div class='controls'>";				
			echo "<input type='email' required type='text' name='mail' maxlength='50' class='form-control'  />";				
			echo "<p class='help-block'></p>";					
		echo "</div>";	
	echo "</div>";	
	
	//Site internet
	echo "<div class='form-group'>";
		echo "<label for='texte'> {$aItems[$sLang]['lib_input_web']} </label>";
		echo "<div class='input-group col-md-6'>";
			echo "<span class='input-group-addon'>W.W.W</span>";
			echo "<input type='text' name='siteweb' maxlength='50' class='form-control'>";
		echo "</div>";	
	echo "</div>";
	
	//Texte du commentaire 
	echo "<div class='form-group'>";
		echo "<label type='texte'>Message : </label>";
		echo "<textarea name='contenu' maxlength='3000' rows='5' class='form-control'></textarea>";
	echo "</div>";
	
	//ref_rep caché
	if (!$bFormNew){ //gestion du ref_rep uniquement pour les formulaires de réponses
		if(!$bCom) {
			$ref = $repval['ref_rep'];
			echo "<input type='hidden' name='ref_rep' value='$ref'>";
		}
	}	
	
	//Bouton submit
	echo "<button class='btn btn-primary' type='submit'> {$aItems[$sLang]['lib_btt_val']} </button>";
echo "</form>";



