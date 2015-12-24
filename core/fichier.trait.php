<?php
/**
 * Trait "Fichier" : Gestion des fichiers
 * @category COMMON
 * @author Ludovic <ludovicinaction@yahoo.fr>
 **/
trait Fichiers{
	
	/**
	  * D�placement d'un fichier dans le dossier "/tmp"
	  * @param string nom du fichier � d�placer 
	  */
	public function DeplacerFichier($fichier){

		//Si le fichier image existe mais qu'il y a une erreur de chargement alors on d�place fichier dans tmp	
		if (isset($_FILES[$fichier]) && ($_FILES[$fichier]['error'] == UPLOAD_ERR_OK)) {
			$newPath = SITE_ROOT . '/tmp/' . basename($_FILES[$fichier]['name']);
			if (move_uploaded_file($_FILES[$fichier]['tmp_name'], $newPath)) {
				$_SESSION['img'] = $newPath;
			} 
			else{
				 $this -> AfficheAlert('danger', 'Probl�me de d�placement fichier', 'Le Fichier image ne peut pas �tre d�plac�', 'admin.php?p=gest_art&a=modif');
				}
		} 
		else{
				$this -> AfficheAlert('danger', 'T�l�chargement fichier', 'Le Fichier image ne peut pas �tre t�l�charg�', 'admin.php?p=gest_art&a=modif');
			}			
	}
	
	
}