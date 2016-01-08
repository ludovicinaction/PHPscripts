<?php
/**
 *  "File" Trait : Files methods
 * @category COMMON
 * @author Ludovic <ludovicinaction@yahoo.fr>
 **/
trait Fichiers{
	
	/**
	  * Move file into "/tmp" folder
	  * @param string File name 
	  */
	public function FileMove($fichier){

		// If the image file exists but there is an error loading then moves tmp file
		if (isset($_FILES[$fichier]) && ($_FILES[$fichier]['error'] == UPLOAD_ERR_OK)) {
			$newPath = SITE_ROOT . '/tmp/' . basename($_FILES[$fichier]['name']);
			if (move_uploaded_file($_FILES[$fichier]['tmp_name'], $newPath)) {
				$_SESSION['img'] = $newPath;
			} 
			else{
				 $this -> DisplayAlert('danger', 'Probl�me de d�placement fichier', 'Le Fichier image ne peut pas �tre d�plac�', 'admin.php?p=gest_art&a=modif');
				}
		} 
		else{
				$this -> DisplayAlert('danger', 'T�l�chargement fichier', 'Le Fichier image ne peut pas �tre t�l�charg�', 'admin.php?p=gest_art&a=modif');
			}			
	}
	
	
}