<?php
/**
 * Trait "Fichier" : Gestion des fichiers
 * @category COMMON
 * @author Ludovic <ludovicinaction@yahoo.fr>
 **/
trait Fichiers{
	
	/**
	  * Déplacement d'un fichier dans le dossier "/tmp"
	  * @param string nom du fichier à déplacer 
	  */
	public function DeplacerFichier($fichier){

		//Si le fichier image existe mais qu'il y a une erreur de chargement alors on déplace fichier dans tmp	
		if (isset($_FILES[$fichier]) && ($_FILES[$fichier]['error'] == UPLOAD_ERR_OK)) {
			$newPath = SITE_ROOT . '/tmp/' . basename($_FILES[$fichier]['name']);
			if (move_uploaded_file($_FILES[$fichier]['tmp_name'], $newPath)) {
				$_SESSION['img'] = $newPath;
			} 
			else{
				 $this -> AfficheAlert('danger', 'Problème de déplacement fichier', 'Le Fichier image ne peut pas être déplacé', 'admin.php?p=gest_art&a=modif');
				}
		} 
		else{
				$this -> AfficheAlert('danger', 'Téléchargement fichier', 'Le Fichier image ne peut pas être téléchargé', 'admin.php?p=gest_art&a=modif');
			}			
	}
	
	
}