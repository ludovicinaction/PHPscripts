<?php


/**
 * Classe Admin
 * Back-office administration
 * @package ADMIN
 * @author Ludovic <ludovicinaction@yahoo.fr>
 * @version 1.2
 */
class Admin{
	use MessageAlert, CommunDbRequest;

	static $lang;

	public function __construct(){
		$this->getLanguageSetting();
	} 

/**
  * Translate one item depending on the module, the office (back or front), language and type
  * @param string $module name module
  * @param string $office ('FRONT' or 'BACK')
  * @param string $lang ('FR' or 'EN' - Français or English)
  * @param string $type item designation
  * @return array text and descritions items translation
  * 
  */
 public function getItemTransation($module, $office, $lang, $type){
 	
 	$sReq = "SELECT description, texte FROM adm_translation WHERE module='$module' and office='$office' and lang='$lang' and type='$type'";
 	$result = SPDO::getInstance()->query($sReq);
 	$aTrans = $result->fetchAll(PDO::FETCH_ASSOC);


	foreach ($aTrans as $key=>$value) {
	 	foreach ($aTrans[$key] as $cle => $valeur) { 		
	 		if ($cle =='description') {
	 			$aItems[$lang][$valeur] = '';
	 			$desc = $valeur; 
	 		}
	 		elseif($cle == 'texte') $aItems[$lang][$desc] = $valeur;
	 	}
		
	}
 	return $aItems;


 }

 public function getLanguageSetting(){
	$sReq = "SELECT * FROM adm_common";
	$result = SPDO::getInstance()->query($sReq);
	$sTrans = $result->fetch(PDO::FETCH_ASSOC);
	//$this->lang = $sTrans['language'];
	self::$lang = $sTrans['language'];
	return $sTrans;
 }

public function selectDistinctOptionSelect($sTable){
	$sReq = "SELECT distinct($sTable) FROM adm_translation order by 1 ";
 	$result = SPDO::getInstance()->query($sReq);
 	$aDistinct = $result->fetchAll(PDO::FETCH_ASSOC);
 	return $aDistinct;


}


public function setLanguageSetting($lang){
	//$this->lang = $lang;
	self::$lang = $lang
;	$sReq = 'UPDATE adm_common SET language=' . "'$lang'";
    $aData = array (':lang'=>"'$lang'");
    $aItems = $this->getItemTransation('BLOG', 'BACK', $lang, 'HOME');
    $this -> updateInformation($sReq, $aData, $aItems[$lang]['alert_msg_confirm'], 'admin.php');  

    $this->getLanguageSetting();
}



 public function getAllTranslations(){
 	$sReq = "SELECT * FROM adm_translation order by description";
 	$result = SPDO::getInstance()->query($sReq);
 	$aTrans = $result->fetchAll(PDO::FETCH_ASSOC);
 	return $aTrans;

 }


public function getSearchTranslations($search_mod, $search_lang, $search_office, $search_type){
	$where = '';

	if ($search_mod != 'ALL') $where = "module='$search_mod'";

	if ($search_lang != 'ALL' && $where !='') $where .= " and lang='$search_lang'";
	elseif ($search_lang != 'ALL' && $where =='') $where = "lang='$search_lang'";

	if($search_office != 'ALL' & $where !='') $where .= " and office='$search_office'";
	elseif ($search_office != 'ALL' & $where =='') $where = "office='$search_office'";

	if($search_type != 'ALL' & $where !='') $where .= " and type='$search_type'";
	elseif ($search_type != 'ALL' & $where =='') $where = "type='$search_type'";

	if ($where == '') $sReq = "SELECT * FROM adm_translation order by description";
	else $sReq = "SELECT * FROM adm_translation WHERE $where order by description";

	$result = SPDO::getInstance()->query($sReq);
 	$aTrans = $result->fetchAll(PDO::FETCH_ASSOC);
 	return $aTrans;
}

public function filterPostValues(){

	$module = filter_input(INPUT_POST, 'module', FILTER_SANITIZE_STRING);
 	if (isset($module)) $_SESSION['module'] = $module; 	

	$lang = filter_input(INPUT_POST, 'lang', FILTER_SANITIZE_STRING);
 	if (isset($lang)) $_SESSION['lang_post'] = $lang; 	

	$office = filter_input(INPUT_POST, 'office', FILTER_SANITIZE_STRING);
 	if (isset($office)) $_SESSION['office'] = $office; 	

	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
 	if (isset($type)) $_SESSION['type'] = $type;

	$desc = filter_input(INPUT_POST, 'desc', FILTER_SANITIZE_STRING);
 	if (isset($desc)) $_SESSION['desc'] = $desc; 	

	$translation = filter_input(INPUT_POST, 'translation', FILTER_SANITIZE_STRING);
 	if (isset($translation)) $_SESSION['translation'] = $translation; 	 	
}

private function supplyVariables(){
	$aVar['module'] 		= $_SESSION['module'];
	$aVar['lang'] 			= $_SESSION['lang_post'];
	$aVar['office'] 		= $_SESSION['office']; 			
	$aVar['type'] 			= $_SESSION['type'];
	$aVar['desc'] 			= $_SESSION['desc'];
	$aVar['translation']	= $_SESSION['translation'];
	if (isset($_SESSION['id_trans'])) $aVar['id_trans'] = filter_var($_SESSION['id_trans'], FILTER_SANITIZE_NUMBER_INT);
	return $aVar;
}


public function UpdateTranslation(){
	$aVar = $this->supplyVariables();
	$module = $aVar['module'];
	$lang 	= $aVar['lang'];
	$office	= $aVar['office'];
	$type 	= $aVar['type'];
	$desc 	= $aVar['desc'];
	$trans 	= $aVar['translation'];

	$id_trans = $aVar['id_trans']; 

	$sReq = 'UPDATE adm_translation set module = :module, lang=:lang, office=:office, type=:type, description=:description, texte=:translation where id=' . $id_trans;
	$aData = array (':module'=>$module, ':lang'=>$lang, ':office'=>$office, ':type'=>$type, ':description'=>$desc, ':translation'=>$trans);

	$aItems = $this->getItemTransation('BLOG', 'BACK', $lang, 'MSG_TRANS');
	$this -> updateInformation($sReq, $aData, $aItems[$lang]['msg_update_confirm'], 'admin.php?p=trans&a=adm_trans&c=init'); 

}	



public function CreateTranslation(){
	$aVar = $this->supplyVariables();

	$sReq = "INSERT INTO adm_translation (module, lang, office, type, description, texte) VALUES (:module, :lang, :office, :type, :description, :translation)";
	$Insert = SPDO::getInstance()->prepare($sReq);
	
	$Insert -> bindValue(':module', $aVar['module']);
	$Insert -> bindValue(':lang', $aVar['lang']);
	$Insert -> bindValue(':office', $aVar['office']);
	$Insert -> bindValue(':type', $aVar['type']);	
	$Insert -> bindValue(':description', $aVar['desc']);
	$Insert -> bindValue(':translation', $aVar['translation']);	

	try {
		 $resultOK = $Insert->execute();
	} catch(PDOException $e){
		 echo $e->getMessage();
	}
	
	/*
	$aMsg = $this->getItemTransation('BLOG', 'BACK', $this->lang, 'MSG_DB_RESULT');
	if ($resultOK) $this->AfficherResultatRqt($resultOK, 'admin.php?p=trans&a=adm_trans&c=init', $aMsg[$this->lang]['ok_return'], '');
	else $this->AfficherResultatRqt($resultOK, 'admin.php?p=trans&a=adm_trans&c=init', '', $aMsg[$this->lang]['ko_return']);
	*/

	$aMsg = $this->getItemTransation('BLOG', 'BACK', Admin::$lang, 'MSG_DB_RESULT');
	if ($resultOK) $this->AfficherResultatRqt($resultOK, 'admin.php?p=trans&a=adm_trans&c=init', $aMsg[Admin::$lang]['ok_return'], '');
	else $this->AfficherResultatRqt($resultOK, 'admin.php?p=trans&a=adm_trans&c=init', '', $aMsg[Admin::$lang]['ko_return']);

}


}