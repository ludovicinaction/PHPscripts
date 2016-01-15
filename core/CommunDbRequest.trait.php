<?php
 /**
  * Commun database request (Update, Insert, delete)
  *
  */
trait CommunDbRequest{



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




	 /**
	  * Delete information in data-base
	  *
	  * @param string $req Delete SQL request
	  * @param string $lien HTML link to return in initial page.
	  */
	 public function DeleteInformation($req, $lien){
		if (isset($_GET['token']) && isset($_SESSION['token']) && $_GET['token'] === $_SESSION['token']){
			
			$supp = SPDO::getInstance()->prepare($req);		
			
			 try {
				 $resultOK = $supp->execute();
			 } catch(CustomException $e){	 
			 //Sauvegarde code qui fonctionne
			 $err =  $e->getMessage();
			 //$dDateJour = date('Y-m-d H:i:s');
			 //error_log("\n" . $dDateJour . ' : [DB: query @'.$_SERVER['REQUEST_URI']."][$req]: $err", 3, "C:/wamp/www/magnetiseur-paca/tmp/php_logs/php_errors.log");			 
			}
			
			$aMsg = $this->getItemTransation('BLOG', 'BACK', Admin::$lang, 'MSG_DB_RESULT');
			if ($resultOK) $this->DisplayResultRqt($resultOK, $lien, $aMsg[Admin::$lang]['ok_return'], '');
			else $this->DisplayResultRqt($resultOK, $lien, '', $aMsg[Admin::$lang]['ko_return']);			

		}

	 }

	 /**
	  * Update information in data-base (insert/update query)
	  *
	  * @param string $sReq Update SQL request
	  * @param array $aData The list of data to be updated
	  * @param string $sMsg Specific message (not standard message, ex: "Operation performed successfully")
	  * @param string $slinkOK Link to return in initial page.
	  * @param boolean $bMsgResult true:display result DB; false : don't display message
	  */
public function executeDbQuery($sReq, $aData, $sMsg, $slinkOk, $bMsgResult=''){

	$update = SPDO::getInstance()->prepare($sReq);
/*
	foreach ($aData as $key => $value) {
		$update -> bindValue($key, $value);		
	}
*/
	//var_dump($sReq);
//var_dump($aData);

	foreach ($aData as $key => $value) {
		//var_dump($value);
		foreach ($value as $cle => $val) {
			if ($cle == 'type') {
				$type=$val;
				//echo "CLE = $cle --------- VAL = $val ----------- TYPE : $type <br>";
			}	
			elseif($cle != 'type') {
				$bindName = $cle;
				$bindValue = $val;

				if ($type != 2) $update -> bindValue($cle, $val, $type);
				elseif ($type == '' OR $type=2) $update -> bindValue($cle, $val);	
				//echo "CLE = $cle __________ VAL = $val _________ TYPE : $type <br>";
			}

		}

	}


	try {
		$resultOK = $update->execute();
	} catch(PDOException $e){
		echo $e->getMessage();
	}

	if ($bMsgResult) {
		$aMsg = $this->getItemTransation('BLOG', 'BACK', Admin::$lang, 'MSG_DB_RESULT');
		if ($resultOK) $this->DisplayResultRqt($resultOK, $slinkOk, $aMsg[Admin::$lang]['ok_return'], '');
		else $this->DisplayResultRqt($resultOK, $slinkOk, '', $aMsg[Admin::$lang]['ko_return']);		
	}	
	
	return $resultOK;
}




}