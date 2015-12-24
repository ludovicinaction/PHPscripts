<?php
/*
 *
  utiliser pour input_session et FILTER_VALIDATE_BOOLEAN
 *
 */

//$aAutorise_p = array('cat', 'gest_art', 'p');
$oSecure = new Securite();            

//Filtrage des données $_GET.
//$val_p = $oSecure->filter_get_input('p', FILTER_SANITIZE_STRING, $aAutorise_p);
$val_p = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);

if (isset($val_p) && 'logout' == $val_p) {
    session_unset();
}

$filtres = array('login' => array('filter' => FILTER_SANITIZE_STRING),
    'pwd' => array('filter' => FILTER_SANITIZE_STRING));

//Filtrage des données $_POST
$clean = filter_input_array(INPUT_POST, $filtres);

$sloginEnter = $clean['login'];
$sPwdEnter = $clean['pwd'];

if (!isset($_SESSION['pwdOK']))
    $_SESSION['pwdOK'] = false;
if (!isset($_SESSION['loginOK']))
    $_SESSION['loginOK'] = false;

if (isset($sloginEnter) && null !== $sloginEnter && isset($sPwdEnter) && $_SESSION['loginOK'] === false) {
    $req = "select pwd_util from utilisateurs where nom_util= :sloginEnter";

    if ($oSecure->verify_password_database($req, $sloginEnter, $sPwdEnter)) {
        //
        session_regenerate_id();
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
    echo "<span class='glyphicon glyphicon-user'></span> <input name='login' value='' maxlength='10' type='search' style='width:100px' class='input-sm form-control' placeholder='nom' value='' />";
    echo "<input name='pwd' value='' type='password' maxlength='20' style='width:100px' class='input-sm form-control' placeholder='mot de passe' readonly onfocus=\"this.removeAttribute('readonly');\" >";
    echo "<button type='submit' class='btn btn-primary btn-xs'><span class='glyphicon glyphicon-off'></span> Se connecter</button>";
    if ($_SESSION['pwdOK'] === false && (!is_null($sloginEnter)))
        echo "<br />Mauvais nom ou mot de passe";
    elseif (!isset($sloginEnter))
        echo "<br /> Veuillez vous identifier svp";
    echo "</form>";
}
elseif ($_SESSION['loginOK'] === true) {
    echo "<form class='navbar-form pull-right' method='post' action='admin.php?p=logout' >";
    echo "Bienvenue {$_SESSION['nom']} ";
    echo "<button type='submit' class='btn btn-primary btn-xs'><span class='glyphicon glyphicon-eject'></span> Se Déconnecter</button>";
    echo "</form>";
    }
