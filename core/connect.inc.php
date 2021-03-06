<?php
/*
 *
  utiliser pour input_session et FILTER_VALIDATE_BOOLEAN
 *
 */
//ob_start();


 $oAdmin = new Admin();  
     
 $aLang = $oAdmin -> getSetting();
 $lang = $aLang['language'];

 $aItems = $oAdmin->getItemTransation('BLOG', 'BACK', $lang, 'HOME');

$oSecure = new Securite();            

//Filter $_GET data
$val_p = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);

// Destroy session et session variables
if (isset($val_p) && 'logout' == $val_p) {
    session_unset();
    setcookie(session_name(), '', time()-2592000, '/');
    session_destroy();

}

$filtres = array('login' => array('filter' => FILTER_SANITIZE_STRING),
    'pwd' => array('filter' => FILTER_SANITIZE_STRING));

//Filter $_POST data
$clean = filter_input_array(INPUT_POST, $filtres);

$sloginEnter = $clean['login'];
$sPwdEnter = $clean['pwd'];

if (!isset($_SESSION['pwdOK']))
    $_SESSION['pwdOK'] = false;
if (!isset($_SESSION['loginOK']))
    $_SESSION['loginOK'] = false;

if (isset($sloginEnter) && null !== $sloginEnter && isset($sPwdEnter) && $_SESSION['loginOK'] === false) {
    $req = "select pwd_util from users where nom_util= :sloginEnter";

    if ($oSecure->verify_password_database($req, $sloginEnter, $sPwdEnter)) {
        //ob_end_clean();
        //session_regenerate_id();
        $_SESSION['loginOK'] = true;
        $_SESSION['nom'] = $sloginEnter;
    } else {
        $_SESSION['loginOK'] = false;
        $_SESSION['pwdOK'] = false;
    }
} elseif (!isset($_SESSION['loginOK']))
    $_SESSION['loginOK'] = false;

if ($_SESSION['loginOK'] === false) {
    echo "<form class='navbar-form pull-right' method='post' action='admin.php'>";
    echo "<span class='glyphicon glyphicon-user'></span> <input name='login' value='' maxlength='10' type='search' style='width:100px' class='input-sm form-control' placeholder='{$aItems[$lang]['val_input_name']}' value='' />";
    echo "<input name='pwd' value='' type='password' maxlength='20' style='width:100px' class='input-sm form-control' placeholder='{$aItems[$lang]['val_input_pwd']}' readonly onfocus=\"this.removeAttribute('readonly');\" >";
    echo "<button type='submit' class='btn btn-primary btn-xs'><span class='glyphicon glyphicon-off'></span> {$aItems[$lang]['lib_btt_login']}</button>";
    if ($_SESSION['pwdOK'] === false && (!is_null($sloginEnter)))
        echo "<br />{$aItems[$lang]['txt_err_login']}";
    elseif (!isset($sloginEnter))
        echo "<br /> {$aItems[$lang]['msg_please_login']}";
    echo "</form>";
}
elseif ($_SESSION['loginOK'] === true) {
    echo "<form class='navbar-form pull-right' method='post' action='admin.php?p=logout' >";
    echo "{$aItems[$lang]['txt_welcome']} {$_SESSION['nom']} ";
    echo "<button type='submit' class='btn btn-primary btn-xs'><span class='glyphicon glyphicon-eject'></span> {$aItems[$lang]['lib_btt_sign_out']}</button>";
    echo "</form>";
    }