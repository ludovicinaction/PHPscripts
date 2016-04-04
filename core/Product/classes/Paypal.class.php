<?php
class Paypal{
use MessageAlert, CommunDbRequest;

private $user;
private $pwd;
private $signature;
private $endpoint;
private $api_version;
public $money_code;

public $errors    = array();

public function __construct($user=false, $pwd=false, $signature=false, $prod=false, $api_version=false, $endpoint=false, $money=false){
	if($user){  $this->user = $user;
	}
	if($pwd){
		$this->pwd = $pwd;
	}
	if($signature){
		$this->signature = $signature;
	}
  if ($endpoint) {
    $this->endpoint = $endpoint;
  }

	if($prod){
		$this->endpoint = str_replace('sandbox.','', $this->endpoint);
	}

  if($money) $this->money_code = $money;

	if($api_version) $this->api_version = $api_version;
}


public function request($method, $params){
	$params = array_merge($params, array(
			'METHOD' => $method,
			'VERSION' => $this->api_version,
			'USER'	 => $this->user,
			'SIGNATURE' => $this->signature,
			'PWD'	 => $this->pwd
	));

	// String transformation (paypal wait a string)
	$params = http_build_query($params);

	// Exec request
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => $this->endpoint,
		CURLOPT_POST=> 1,
		CURLOPT_POSTFIELDS => $params,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_VERBOSE => 1
	));

	// Retrieve answers
	$response = curl_exec($curl);
	$responseArray = array();
	
	//parse the response
	parse_str($response, $responseArray);

	//If curl error
	if(curl_errno($curl)){
    
		$this->errors = curl_error($curl);
		var_dump($this->errors);
		curl_close($curl);
		return false;
	}else{
		// Paypal Success
		if($responseArray['ACK'] == 'Success'){
			curl_close($curl);
			return $responseArray;
		}else{
			$this->errors = $responseArray;
			curl_close($curl);
			return false;
		}
	}
}

public function getSettings(){
  $sReq = "SELECT * FROM adm_paypal";
  $select = SPDO::getInstance()->query($sReq);
    return $select->fetch(PDO::FETCH_ASSOC);
}

/*
public function getAllSettingsProducts(){
  $sReq = "SELECT * FROM adm_paypal_product order by id_product_type ";
  $select = SPDO::getInstance()->query($sReq);
    return $select->fetchAll(PDO::FETCH_ASSOC);

}
*/
public function getMoneyCodes(){
  $sReq = "SELECT money_name, code FROM adm_currency_codes order by money_name";
  $select = SPDO::getInstance()->query($sReq);
  return $select->fetchAll(PDO::FETCH_ASSOC);
}

public function getHostUrl(){
  $sReq = "SELECT websitehost FROM adm_common";
  $select = SPDO::getInstance()->query($sReq);

  $url = $select->fetch(PDO::FETCH_ASSOC);
  
  if (strrchr($url['websitehost'], '/') != '/') $url = $url['websitehost'] . '/';
  else $url = $url['websitehost'];

  return $url;
}



public function recordErrorOrder($error, $id_order){
  $serial_error = serialize($error);
  $dDate = date('Y-m-d H:i:s');

  $sReq = "INSERT INTO products_order_errors (errors, id_order, date_order) VALUES (:error, :id_order, :date_order)";

  $insert = SPDO::getInstance()->prepare($sReq);
  $insert->bindParam(':error', $serial_error);
  $insert->bindParam(':id_order', $id_order);
  $insert->bindParam(':date_order', $dDate);
  try{
    $insert->execute();
  }catch(PDOException $e){
    echo $e->getMessage();
  }
}


public function UpdatePaypalSettings($aSettings){
	$notype=PDO::PARAM_STR;
	$sReq = "UPDATE adm_paypal set user=:user, pwd=:pwd, signature=:signature, endpoint=:endpoint, version=:version, money=:money, prod=:prod";
	$aBindVar = array( array('type'=>$notype, ':user'=>$aSettings['user'])
	, array('type'=>$notype, ':pwd'=>$aSettings['pwd'])
	, array('type'=>$notype, ':signature'=>$aSettings['signature'])
	, array('type'=>$notype, ':endpoint'=>$aSettings['endpoint'])
	, array('type'=>$notype, ':version'=>$aSettings['version'])
	, array('type'=>$notype, ':money'=>$aSettings['money'])
	, array('type'=>PDO::PARAM_BOOL, ':prod'=>$aSettings['prod'])
	);

	$this->executeDbQuery($sReq, $aBindVar, '', 'admin.php?p=paypal', true);
}


public function RecordPaypalPayer($aPaypalResponse){
  $id_paypal = $aPaypalResponse['PAYERID'];
  $nbr = $this->CheckPayerRecorded($id_paypal);
  $iNbr = (int) $nbr['nbr'];

  if ($iNbr === 0){
    $reqInsert = "INSERT INTO products_payer (id_paypal,firstname,lastname,email,adresse,city,country_code,country_name,state,zipcode)";
    $reqInsert .= " VALUES (:id_paypal,:firstname,:lastname,:email,:adresse,:city,:country_code,:country_name,:state,:zipcode)";
  
    $insert = SPDO::getInstance()->prepare($reqInsert);
    $insert->bindParam(':id_paypal', $aPaypalResponse['PAYERID']);
    $insert->bindParam(':firstname', $aPaypalResponse['FIRSTNAME']);
    $insert->bindParam(':lastname', $aPaypalResponse['LASTNAME']);
    $insert->bindParam(':email', $aPaypalResponse['EMAIL']);
    $insert->bindParam(':adresse', $aPaypalResponse['SHIPTOSTREET']);
    $insert->bindParam(':city', $aPaypalResponse['SHIPTOCITY']);
    $insert->bindParam(':country_code', $aPaypalResponse['COUNTRYCODE']);
    $insert->bindParam(':country_name', $aPaypalResponse['SHIPTOCOUNTRYNAME']);
    $insert->bindParam(':state', $aPaypalResponse['SHIPTOSTATE']);
    $insert->bindParam(':zipcode', $aPaypalResponse['SHIPTOZIP']);
    try{
      $result = $insert->execute();
    } catch(PDOException $e){
      echo $e->getMessage();
    } 
  }
}


private function CheckPayerRecorded ($id_paypal){
  $sReq = "SELECT count(id_payer) nbr FROM products_payer WHERE id_paypal='$id_paypal' ";
    $aResult = SPDO::getInstance()->query($sReq);
    return $aResult->fetch(PDO::FETCH_ASSOC);

}



}