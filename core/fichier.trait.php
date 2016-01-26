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
	public function FileMove($fichier, $module, $office){

		$aMsg = $this->getItemTransation($module, $office, Admin::$lang, 'ERROR_FILE_MOVE');

		// If the image file exists but there is an error loading then moves tmp file
		if (isset($_FILES[$fichier]) && ($_FILES[$fichier]['error'] == UPLOAD_ERR_OK)) {
			$newPath = SITE_ROOT . '/tmp/' . basename($_FILES[$fichier]['name']);
			if (move_uploaded_file($_FILES[$fichier]['tmp_name'], $newPath)) {
				$_SESSION['img'] = $newPath;
			} 
			else{
				 $this -> DisplayAlert('danger', $aMsg[$lang]['file_move_problem'], $aMsg[$lang]['file_canot_move'], 'admin.php?p=gest_art&a=modif');
				}
		} 
		else{
				$this -> DisplayAlert('danger', $aMsg[$lang]['download file'], $aMsg[$lang]['file_canot_download'], 'admin.php?p=gest_art&a=modif');
			}			
	}
	
	
}