<?php
require_once 'core/messageAlert.trait.php';

/**
 * Classe Images
 * Images management methods
 * @package COMMON
 * @author Ludovic <ludovicinaction@yahoo.fr>
 */
Class Images{
	use MessageAlert;
	
	/**
	  * Resize an image with a width and a maximum height
	  *
	  * @param string $sNomInput File name to resize
	  * @return array 'type_image'=>image type, 'ressource_img'=> Resource image.
	  */
	public function Redim($sNomInput, $max_width, $max_height){
		
		// récupération des données (type, tmp_name) selon si on est passé par une variable de sessions ou directement par $_FILES
		if ( isset($_SESSION[$sNomInput])) $aImage = $_SESSION[$sNomInput];
		else $aImage = $_FILES[$sNomInput];
		
		//Récupération de l'url du fichier qui a été déplacé dans le dossier 'tmp'
		$f = $aImage['tmp_name'];
		
		//Dans le cas de la session, on a déplacé le fichier donc on recupére le nouveau chemin contenu dans 'img'
		if (isset($_SESSION['img']))  $f = $_SESSION['img'];
		
		if (!is_file($f)) 
		$this -> DisplayAlert('danger', 'Problème de déplacement fichier', 'Le Fichier image ne peut pas être déplacé', 'admin.php?p=gest_art&a=modif');

		
		// Création de l'image depuis le fichier
		$type_img = strtolower($aImage['type']);
		switch($type_img)
		{
			case 'image/jpeg':
				$image = imagecreatefromjpeg($f);
				break;
			case 'image/png':
				$image = imagecreatefrompng($f);
				break;
			case 'image/gif':
				$image = imagecreatefromgif($f);
				break;
			default : exit;	
			/*default:
				exit('Unsupported type: '.$aImage['type']);
				*/
		}		

		// Sauvegardes des anciennes dimenssions
		$old_width  = imagesx($image);
		$old_height = imagesy($image);

		// Calcul des proportions
		$scale      = min($max_width/$old_width, $max_height/$old_height);

		// Calcul des nouvelles dimenssions
		$new_width  = ceil($scale*$old_width);
		$new_height = ceil($scale*$old_height);		
		
		// Création de la nouvelle image
		$new = imagecreatetruecolor($new_width, $new_height);

		// Re-dimentionner l'image
		imagecopyresampled($new, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);		
		
		//On récupére le contenu sans l'afficher.
		ob_start(); 
		imagejpeg($new, null, 72); 
		$content = ob_get_contents(); 
		ob_end_clean(); 		
		
		$aReturn = array('type_image'=>$type_img, 'ressource_img'=>$content);
		
		return $aReturn;
	}	
	
	
	
} // fin images