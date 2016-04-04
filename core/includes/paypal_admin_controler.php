<?php
// read paypal settings
$oPaypal = new Paypal;

$aSettings = $oPaypal->getSettings();
$aMoneyCodes = $oPaypal->getMoneyCodes();

$prod = (bool) $aSettings['prod'];

$oProd = new Product;
$aCat = $oProd->getDataCategories();

if ($c == 'update_main'){
  $aSettings['user']      = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING);
  $aSettings['pwd']       = filter_input(INPUT_POST, 'pwd', FILTER_SANITIZE_STRING);
  $aSettings['signature'] = filter_input(INPUT_POST, 'signature', FILTER_SANITIZE_STRING);
  $aSettings['endpoint']  = filter_input(INPUT_POST, 'endpoint', FILTER_VALIDATE_URL);
  $aSettings['version']   = filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING);
  $aSettings['money']     = filter_input(INPUT_POST, 'money', FILTER_SANITIZE_STRING);
  $aSettings['prod']      = filter_input(INPUT_POST, 'opt-use', FILTER_VALIDATE_BOOLEAN);

  $oPaypal->UpdatePaypalSettings($aSettings);

}else include 'core/admin/view/back-paypal-settings.php';




