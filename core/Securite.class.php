<?php

/**
 * Securisation methods
 * @package COMMON
 * @see PHP5.5 necessary
 * @author Ludovic <ludovicinaction@yahoo.fr>
 * @todo : 
 */
class Securite
{
	/**
	 * Filtrage des données $_GET
	 *
	 * @param string $p valeur du $_GET à filtrer
	 * @param constant $filtre FILTER_xxx_xxx ; type de filtre
	 * @param array $autorised_values liste des valeurs autorisées dans le $_GET.
	 * @return string null si KO sinon la valeur authentifiée.
	 */
	public function filter_get_input($p, $filtre, $autorised_values){
		foreach($autorised_values as $value){
			$filter_var = filter_input(INPUT_GET, $p, $filtre);	
			
			if ($value === $filter_var) return $filter_var;
		}
		return null;
}
	
	
	/**
	 * Vérification du mot de passe saisit par l'utilisateur et celui stocké en base.
	 *
	 * @param string $req Requête qui recupére le password haché en base
	 * @param string $sPasswordUser Password saisit par l'utilisateur
	 * @return boolean true:password OK ; false:password KO
	 **/
	public function verify_password_database($req, $sloginEnter, $sPasswordUser){
		$stmt = SPDO::getInstance()->prepare($req);
		$stmt -> bindvalue(':sloginEnter', $sloginEnter);
		$stmt->execute();
		$aPwd_hash = $stmt -> fetchAll();
	
		if (!empty($aPwd_hash))	$sPwd_hash = $aPwd_hash[0][0];
		else $sPwd_hash = null;
		
		return $this->pwd_verify($sPasswordUser, $sPwd_hash);
	}
	
	
	
	/**
	 * password_verify
	 * Contre-mesures : Sécurisation du mot de passe par hachage.
	 * @see PHP 5 >= 5.5.0
	 * @param string $sPwdEnter password à vérifier (saisit par l'utilisateur)
	 * @param string $sPwd_hash password haché
	 * @return boolean true:password OK ; false:password KO
	 **/
	protected function pwd_verify($sPwdEnter, $sPwd_hash){
		if ( password_verify($sPwdEnter, $sPwd_hash) ) return true;
		else return false; 	
	}
	
	/*
	 * Create tokin and put it in session
	 * Measure against CSRF attacks 
	 * @param : none
	 * @return : none ( create $_SESSION['token'] )
	 */
	public function create_token(){
		date_default_timezone_set('Europe/Paris');
		$sJeton = sha1(uniqid(rand(), true)) . date('YmdHis');
		$_SESSION['token'] = $sJeton ; 
	 }
	
	
}
