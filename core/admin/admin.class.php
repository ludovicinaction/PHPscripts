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
		$this->getSetting();
	} 



 /**
  * Get général setting
  *
  * @return array settings values
  */
 public function getSetting(){
	$sReq = "SELECT * FROM adm_common";
	$result = SPDO::getInstance()->query($sReq);
	$sTrans = $result->fetch(PDO::FETCH_ASSOC);
	self::$lang = $sTrans['language'];
	return $sTrans;
 }


 /**
  * Home select input values feed
  *
  * @param string The name column to distinct
  * @return array The distincts values
  */
public function selectDistinctOptionSelect($sTable){
	$sReq = "SELECT distinct($sTable) FROM adm_translation order by 1 ";
 	$result = SPDO::getInstance()->query($sReq);
 	$aDistinct = $result->fetchAll(PDO::FETCH_ASSOC);
 	return $aDistinct;


}

 /**
  * General setting save
  *
  * @param string $lang language
  * @param string $host website adress
  * @param string $smtp SMTP send mail
  * @param int $port port send mail
  * @param string $email_send Send mail adress
  */
public function save_setting($lang, $host, $smtp, $port, $email_send)
{
	self::$lang = $lang;

	$sReq = "UPDATE adm_common SET language =:lang, websitehost = :webhost, smtp_sendmail= :smtp, port_sendmail = :port_sendmail, email_sendmail = :email_sendmail";

    $aData = array(array ('type'=>PDO::PARAM_STR, ':lang'=>"'$lang'")
    	, array ('type'=>PDO::PARAM_STR, ':lang'=>$lang)
    	, array ('type'=>PDO::PARAM_STR, ':webhost'=>$host)
    	, array ('type'=>PDO::PARAM_STR, ':smtp'=>$smtp)
    	, array ('type'=>PDO::PARAM_STR, ':port_sendmail'=>$port)
    	, array ('type'=>PDO::PARAM_STR, ':email_sendmail'=>$email_send)
    	);
    $aItems = $this->getItemTransation('BLOG', 'BACK', $lang, 'HOME');
    
    $this -> executeDbQuery($sReq, $aData, '', 'admin.php', true);  

    $this->getSetting();
}


 /**
  * Translations get
  *
  * @return array all transaction
  */
 public function getAllTranslations(){
 	$sReq = "SELECT * FROM adm_translation order by description";
 	$result = SPDO::getInstance()->query($sReq);
 	$aTrans = $result->fetchAll(PDO::FETCH_ASSOC);
 	return $aTrans;

 }

 /**
  * Transactions research sort by :
  *
  * @param string $search_mod module filtering parameter
  * @param string $search_lang langauge filtering parameter
  * @param string $search_office back or front filtering parameter
  * @param string $search_type Type filtering parameter
  * @return array filtred translation
  */
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

 /**
  * Filter the input values (for translation page) and put it in $_SESSION variables
  *
  */
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

 /**
  * Feed array with filtered values (Transalation page)
  *
  * @param type comentaire_ici
  * @return type comentaire_ici
  */
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



 /**
  * Update Translations in database
  */
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

	$aData = array(
		array('type'=>PDO::PARAM_STR, ':module'=>$module)
		, array('type'=>PDO::PARAM_STR, ':lang'=>$lang)
		, array('type'=>PDO::PARAM_STR, ':office'=>$office)
		, array('type'=>PDO::PARAM_STR, ':type'=>$type)
		, array('type'=>PDO::PARAM_STR, ':description'=>$desc)
		, array('type'=>PDO::PARAM_STR, ':translation'=>$trans));

	$aItems = $this->getItemTransation('BLOG', 'BACK', $lang, 'MSG_TRANS');
	$this -> executeDbQuery($sReq, $aData, $aItems[$lang]['msg_update_confirm'], 'admin.php?p=trans&a=adm_trans&c=init', true); 

}	


 /**
  * Insert translation data in database.
  *
  */
public function CreateTranslation(){
	$aVar = $this->supplyVariables();

	$sReq = "INSERT INTO adm_translation (module, lang, office, type, description, texte) VALUES (:module, :lang, :office, :type, :description, :translation)";

	$aData = array(array ('type'=>PDO::PARAM_STR, ':module'=>$aVar['module'])
		, array ('type'=>PDO::PARAM_STR, ':lang'=>$aVar['lang'])
		, array ('type'=>PDO::PARAM_STR, ':office'=>$aVar['office'])
		, array ('type'=>PDO::PARAM_STR, ':type'=>$aVar['type'])
		, array ('type'=>PDO::PARAM_STR, ':description'=>$aVar['desc'])
		, array ('type'=>PDO::PARAM_STR, ':translation'=>$aVar['translation']));

	$this -> executeDbQuery($sReq, $aData, '', 'admin.php?p=trans&a=adm_trans&c=init', true); 

}


}