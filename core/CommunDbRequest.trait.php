<?php
 /**
  * Commun database request (Update, Insert, delete)
  *
  */
trait CommunDbRequest{


	 /**
	  * Delete information in data-base
	  *
	  * @param string $req Delete SQL request
	  * @param string $lien HTML link to return in initial page.
	  */
	 public function SupprimerInformation($req, $lien){
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
			if ($resultOK) $this->AfficherResultatRqt($resultOK, $lien, $aMsg[Admin::$lang]['ok_return'], '');
			else $this->AfficherResultatRqt($resultOK, $lien, '', $aMsg[Admin::$lang]['ko_return']);			

		}

	 }

	 /**
	  * Update information in data-base
	  *
	  * @param string $sReq Update SQL request
	  * @param array $aData The list of data to be updated
	  * @ignore string $sMsg (à SUPPRIMER)
	  * @param string $slinkOK Link to return in initial page.
	  */
public function updateInformation($sReq, $aData, $sMsg, $slinkOk){

	$update = SPDO::getInstance()->prepare($sReq);

	foreach ($aData as $key => $value) {
		$update -> bindValue($key, $value);		
	}

	try {
		$resultOK = $update->execute();
	} catch(PDOException $e){
		echo $e->getMessage();
	}

	$aMsg = $this->getItemTransation('BLOG', 'BACK', Admin::$lang, 'MSG_DB_RESULT');
	if ($resultOK) $this->AfficherResultatRqt($resultOK, $slinkOk, $aMsg[Admin::$lang]['ok_return'], '');
	else $this->AfficherResultatRqt($resultOK, $slinkOk, '', $aMsg[Admin::$lang]['ko_return']);		

}




}