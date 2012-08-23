<?php

/**
 * The web gui initiation script.
 * @package zpanelx
 * @subpackage core
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
if (!isset($Langue)) {
$Langue = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
$Langue = strtolower(substr(chop($Langue[0]),0,2));
}
 
global $controller, $zdbh, $zlo;
$controller = new runtime_controller();

$zlo->method = ctrl_options::GetOption('logmode');
if ($zlo->hasInfo()) {
    $zlo->writeLog();
    $zlo->reset();
}

if (isset($_GET['logout'])) {
    ctrl_auth::KillSession();
    ctrl_auth::KillCookies();
    header("location: ./?loggedout");
    exit;
}

if (isset($_GET['returnsession'])) {
    if (isset($_SESSION['ruid'])) {
        ctrl_auth::SetUserSession($_SESSION['ruid']);
        $_SESSION['ruid'] = null;
    }
    header("location: ./");
    exit;
}
if (($_POST['panel']=='reset2')) { 
if (file_exists("lang/reset2.".$Langue.".php")) { 
include("lang/reset2.".$Langue.".php");
} else { 
include("/zpanel/panel/lang/reset2.en.php");
} } else { 
if (file_exists("lang/".$Langue.".php")) { 
include("lang/".$Langue.".php");
} else { 
include("/zpanel/panel/lang/en.php");
}
}



if (isset($_POST['inConfEmail'])) {
    $result = $zdbh->query("SELECT ac_id_pk FROM x_accounts WHERE ac_email_vc = '" . $_POST['inConfEmail'] . "' AND ac_resethash_tx = '" . $_GET['resetkey'] . "' AND ac_resethash_tx IS NOT NULL")->Fetch();
    if ($result) {
        $zdbh->exec("UPDATE x_accounts SET ac_resethash_tx = '', ac_pass_vc= '" . md5($_POST['inNewPass']) . "' WHERE ac_id_pk=" . $result['ac_id_pk'] . "");
        runtime_hook::Execute('OnSuccessfulPasswordReset');
    } else {
        runtime_hook::Execute('OnFailedPasswordReset');
    }
    header("location: ./?passwordreset");
    exit();
}

if (isset($_POST['inUsername'])) {
    if (!isset($_POST['inRemember'])) {
        $rememberdetails = false;
    } else {
        $rememberdetails = true;
    }
    if (!ctrl_auth::Authenticate($_POST['inUsername'], md5($_POST['inPassword']), $rememberdetails, false)) {
        header("location: ./?invalidlogin");
        exit();
    }
}

if (isset($_COOKIE['zUser'])) {
    ctrl_auth::Authenticate($_COOKIE['zUser'], $_COOKIE['zPass'], false, true);
}

if (!isset($_SESSION['zpuid'])) {
    ctrl_auth::RequireUser();
}


runtime_hook::Execute('OnBeforeControllerInit');
$controller->Init();
ui_templateparser::Generate("etc/styles/" . ui_template::GetUserTemplate());
?>