<?php
 /**
  * Commentaire ici
  *
  * @param type comentaire_ici
  * @return type comentaire_ici
  */
trait CommunDbRequest{


	 /**
	  * Suppression d'une information (articles, commentaires...) en base
	  *
	  * @param string Requete SQL de suppression
	  * @param string Liens HTML pour revenir au tableau des informations
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
			
			/*
			$aMsg = $this->getItemTransation('BLOG', 'BACK', $this->lang, 'MSG_DB_RESULT');
			if ($resultOK) $this->AfficherResultatRqt($resultOK, $lien, $aMsg[$this->lang]['ok_return'], '');
			else $this->AfficherResultatRqt($resultOK, $lien, '', $aMsg[$this->lang]['ko_return']);			
			*/
			$aMsg = $this->getItemTransation('BLOG', 'BACK', Admin::$lang, 'MSG_DB_RESULT');
			if ($resultOK) $this->AfficherResultatRqt($resultOK, $lien, $aMsg[Admin::$lang]['ok_return'], '');
			else $this->AfficherResultatRqt($resultOK, $lien, '', $aMsg[Admin::$lang]['ko_return']);			

		}

	 }

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