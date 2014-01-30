<?php
error_reporting(E_ERROR | E_PARSE);
require_once('../../../cnf/db.php');
require_once('../../../dryden/db/driver.class.php');
require_once('../../../dryden/debug/logger.class.php');
require_once('../../../dryden/runtime/dataobject.class.php');
require_once('../../../dryden/sys/versions.class.php');
require_once('../../../dryden/ctrl/options.class.php');
require_once('../../../dryden/ctrl/auth.class.php');
require_once('../../../dryden/ctrl/users.class.php');
require_once('../../../dryden/fs/director.class.php');
require_once('../../../dryden/ui/sysmessage.class.php');
require_once('../../../dryden/ui/module.class.php');
require_once('../../../dryden/runtime/hook.class.php');
require_once('../../../dryden/xml/reader.class.php');
require_once('../../../dryden/fs/filehandler.class.php');
require_once('../../../inc/dbc.inc.php');

/*
if (isset($_GET['id'])) {
    $userid = $_GET['id'];
} else {
    $userid = NULL;
}

session_start();
if ($_SESSION['zpuid'] == $userid) {
    $currentuser = ctrl_users::GetUserDetail($userid);
*/	
	if (isset($_GET['request'])){
		AjaxReturn($_GET['request']);
	}

	if (isset($_GET['modinfo'])){
		AjaxReturnModInfo($_GET['modinfo']);
	}

	if (isset($_GET['update'])){
		AjaxReturnUpdate($_GET['update']);
	}

	if (isset($_GET['repostat'])){
		AjaxReturnDownloads($_GET['repostat']);
	}

//}

function AjaxReturn($url=NULL){
	if(!empty($url)){
		$urlparts = explode(":::", $url);
		if (UrlExist($urlparts[1])){
			//EXIST
			if ($urlparts[0] == "R"){
			//$line = "<a href=\"javascript:void(0)\" title=\"Repository appears to be valid.\"><img src=\"modules/repo_browser/assets/up.png\" border=\"0\"></a>";
			$line = "<a href=\"javascript:void(0)\" title=\"Repository appears to be valid.\"><span class=\"label label-success\">OK</span></a>";
			}
			if ($urlparts[0] == "M"){
			//$line =  "<a href=\"javascript:void(0)\" title=\"Module appears to exist in the repository and is available for installation.\"><img src=\"modules/repo_browser/assets/up.png\" border=\"0\"></a>";
			$line =  "<a href=\"javascript:void(0)\" title=\"Module appears to exist in the repository and is available for installation.\"><span class=\"label label-success\">OK</span></a>";
			}
			echo $line;		
		} else {
			//NOT EXIST
			if ($urlparts[0] == "R"){
			//$line =  "<a href=\"javascript:void(0)\" title=\"Repository seems to be invalid or offline.\"><img src=\"modules/repo_browser/assets/down.png\" border=\"0\"></a>";
			$line =  "<a href=\"javascript:void(0)\" title=\"Repository seems to be invalid or offline.\"><span class=\"label label-danger\">NO</span></a>";
			}
			if ($urlparts[0] == "M"){
			//$line =  "<a href=\"javascript:void(0)\" title=\"Can not find module in the repository! Module is currently not available for installation.\"><img src=\"modules/repo_browser/assets/down.png\" border=\"0\"></a>";
			$line =  "<a href=\"javascript:void(0)\" title=\"Can not find module in the repository! Module is currently not available for installation.\"><span class=\"label label-danger\">NO</span></a>";
			}
			echo $line;
		}
	} else {
		return false;
	}
}

function AjaxReturnModInfo($module=NULL){
	global $zdbh;
	$module = explode(":::", $module);
	$reponame = $module[0];
	$modulename = $module[1];	
	$sql = "SELECT * FROM x_modules WHERE mo_folder_vc = '" . $modulename . "'";
    $numrows = $zdbh->query($sql);
	if ($numrows->fetchColumn() <> 0) {
		$modinfo = $zdbh->query("SELECT * FROM x_modules WHERE mo_folder_vc = '" . $modulename . "'")->fetch();
		$info = AjaxGetModuleXMLTags($modulename);
		$description = $modinfo['mo_desc_tx'];
		$authorurl = $info['authorurl'];
		$version = $info['version'];
		$authoremail = $info['authoremail'];
		$authorname = $info['authorname'];
		$line = "<font color=\"red\"><span class=\"label label-success\">Module Installed.</span></font><p>" . $modinfo['mo_desc_tx'] . "</p>Version " . $modinfo['mo_version_in'] . " | By <a href=\"mailto:" . $authoremail . "\">" . $authorname . "</a> | <a href=\"" . $authorurl . "\" target=\"_blank\">Visit Author Site</a><br>Repo:" . $reponame . "";
	} else {
		$url  = "http://" . $reponame . "/" . $modulename . ".xml";
		$unfo = AjaxGetUpdateXMLTags($url);
		if (!fs_director::CheckForEmptyValue($unfo)){
			$updatedescription = NULL;
			$authornameemail   = NULL;
			$description = NULL;
			$version     = NULL;
			$name        = NULL;
			$authoremail = NULL;
			$authorurl   = NULL;
			$ostype      = NULL;
			$osdesc      = NULL;
			if (isset($unfo['desc'])){
				$description = "<p>" . $unfo['desc'] . "</p>";
			}
			if (isset($unfo['updatedesc'])){
				$updatedescription = "<p>" . $unfo['updatedesc'] . "</p>";
			}
			if (isset($unfo['ostype'])){
				if (strtoupper($unfo['ostype']) == 'WIN'){
					$ostype = "<br><img src=\"modules/repo_browser/assets/win.png\" width=\"16px\" height=\"16px\"> This module is for Windows operating systems ONLY!";
				}
				if (strtoupper($unfo['ostype']) == 'LIN'){
					$ostype = "<br><img src=\"modules/repo_browser/assets/lin.png\" width=\"16px\" height=\"16px\"> This module is for Linux operating systems ONLY!";
				}
				if (strtoupper($unfo['ostype']) == 'ALL'){
					$ostype = "<br><img src=\"modules/repo_browser/assets/all.png\" width=\"16px\" height=\"16px\"> This module is for all operating systems.";
				}
			}
			if (isset($unfo['latestversion'])){
				$version = "Version " . $unfo['latestversion'] . " | ";
			}
			if (isset($unfo['name'])){
				$name = $unfo['name'];
			}
			if (isset($unfo['authorname']) && isset($unfo['authoremail'])){
				$authornameemail = "By <a href=\"mailto:" . $unfo['authoremail'] . "\">" . $unfo['authorname'] . "</a> | ";
			}
			if (isset($unfo['authorurl'])){
				$authorurl = "<a href=\"" . $unfo['authorurl'] . "\" target=\"_blank\">Visit Author Site</a>";
			}
			$line = "<font color=\"red\"><span class=\"label label-danger\">Module not installed.</span></font><br>" . $name . $description . $version . $authornameemail . $authorurl . "<br>Repo:" . $reponame . $ostype ."";	
		} else {	
			$line = "<font color=\"red\"><span class=\"label label-danger\">Module not installed. No information available.</span></font><br>Repo:" . $reponame . "";
		}
	}
	echo $line;
}

function AjaxReturnUpdate($Module=NULL){
	global $zdbh;
	$Module = explode(":::", $Module);
	$reponame = $Module[0];
	$modulename = $Module[1];
	$url  = "http://" . $reponame . "/" . $modulename . ".xml";
	$file = "http://" . $reponame . "/" . $modulename . ".zpp";
	$sql  = "SELECT * FROM x_modules WHERE mo_folder_vc = '" . $modulename . "'";
    $numrows = $zdbh->query($sql);
	if ($numrows->fetchColumn() <> 0) {
		$modinfo = $zdbh->query("SELECT * FROM x_modules WHERE mo_folder_vc = '" . $modulename . "'")->fetch();
		if (UrlExist($url)){
			if (UrlExist($file)){
				$info = AjaxGetUpdateXMLTags($url);
				$latestversion = $info['latestversion'];
				$downloadurl = $info['downloadurl'];
				if (isset($info['updatedesc'])){
					$updatedescription = "<br><strong>Update Information:</strong><br>" . $info['updatedesc'] . "<br>";
				} else {
					$updatedescription = "";
				}
				$installedversion = $modinfo['mo_version_in'];
				if ($latestversion > $installedversion){
				
					$checkverion =  AjaxGetModuleXMLTags($modulename);
					if ($checkverion['version'] == $latestversion){
						$line = "<div class=\"znotice alert alert-block alert-info\"><h2><img src=\"modules/repo_browser/assets/warning-small.png\"> Version Mismatch!</h2><p>ZPanel thinks that " . $modulename . " version is:<b>" . $installedversion . "</b>, but the installed module file is showing that it is actually version:<b>" . $checkverion['version'] . "</b></p><p>This is usually because the module developer failed to update the ZPanel database with the new version number when the module was updated.</p><p>If you just upgraded this module and are getting this warning then you can allow Repo Browser to manually update ZPanel with the correct version number.</p><p><button class=\"button-loader btn btn-primary fg-button ui-state-default ui-corner-all\" type=\"submit\" name=\"inUpdateVersionDB\" id=\"inUpdateVersionDB\" value=\"" . $checkverion['version'] . ":::" . $modulename . ":::" . $installedversion . "\">FIX VERSION NUMBER</button></p></div>";
					} else {
						$line = "";
					}
				
					$line .= "<div class=\"znotice alert alert-block alert-info\"><img src=\"modules/repo_browser/assets/warning-small.png\"> <b>There is a new version available for " . $modulename . ". Version: ".$latestversion."</b><br>" . $updatedescription . "<br><a href=\"".$downloadurl."\"><b>Download</b></a> or <a href=\"#\" onclick=\"$(this).closest('form').submit(); return false;\"><b>Update Module</b></a><input type=\"hidden\" name=\"inUpdateModule\" value=\"" . $reponame . ":::" . $modulename . "\"></div>";
					} else {
						$line = "";
					}
			} else {
				$line = "";
			}
		} else {
			$line = "";
		}
	} else {
		$line = "";
	}
	echo $line;
}

function AjaxReturnDownloads($Module=NULL){
	$Module = explode(":::", $Module);
	$reponame = $Module[0];
	$modulename = $Module[1];
	$url  	 = "http://" . $reponame . "/repostatslist";
	if (UrlExist($url)){
       		$file_handle = fopen($url, "r");
			while (!feof($file_handle)) {
   				$line = trim(fgets($file_handle));
				$line = explode(" ", $line);
					if ($modulename == $line[0]){
						$totaldownloads = $line['1'];
						echo number_format($totaldownloads) . " downloads";
					}					
			}
			fclose($file_handle);
	} else {
		$url  	 = "http://" . $reponame . "/repostats.php";
		$jsonurl = "http://" . $reponame . "/repostats.php?json=true";
		if (UrlExist($url)){
			$repostats = json_decode(file_get_contents($jsonurl), true);
			if (isset($repostats)){
				foreach($repostats as $repostat){
					if ($modulename == $repostat['package']){
						$totaldownloads = $repostat['downloads'];
						echo number_format($totaldownloads) . " downloads";
					}
				}
			}
		}
	}
}

function UrlExist($url){
   	$ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($code == 200){
       $status = true;
    }else{
   		$ch = curl_init($url . "/");    
	    curl_setopt($ch, CURLOPT_NOBODY, true);
	    curl_exec($ch);
	    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);	
    	if($code == 200){
       		$status = true;
    	}else{	
      		$status = false;
    	}	
      $status = false;
    }
    curl_close($ch);
    return $status;
}

function AjaxGetModuleXMLTags($modulefolder) {
	global $zlo;
    $mod_xml = ctrl_options::GetSystemOption('zpanel_root') . "/modules/" . $modulefolder . "/module.xml";
    $info = array();
    try {
    	$mod_config = new xml_reader(fs_filehandler::ReadFileContents($mod_xml));
        $mod_config->Parse();
		if (isset($mod_config->document->name[0]->tagData)) {
        	$info['name'] = $mod_config->document->name[0]->tagData;
	        $info['version'] = $mod_config->document->version[0]->tagData;
	        $info['desc'] = $mod_config->document->desc[0]->tagData;
			$info['updatedesc'] = $mod_config->document->updatedesc[0]->tagData;
	        $info['authorname'] = $mod_config->document->authorname[0]->tagData;
	        $info['authoremail'] = $mod_config->document->authoremail[0]->tagData;
	        $info['authorurl'] = $mod_config->document->authorurl[0]->tagData;
	        $info['updateurl'] = $mod_config->document->updateurl[0]->tagData;
	        return $info;
		} else {
			return false;
		}
    } catch (Exception $e) {
    	return false;
    }
}

function AjaxGetUpdateXMLTags($url) {
	global $zlo;
    $mod_xml = $url;
    $info = array();
    try {
    	$mod_config = new xml_reader(fs_filehandler::ReadFileContents($mod_xml));
        $mod_config->Parse();
		if (isset($mod_config->document->latestversion[0]->tagData)) {
   	 		$info['latestversion'] = $mod_config->document->latestversion[0]->tagData;
    		$info['downloadurl'] = $mod_config->document->downloadurl[0]->tagData;
			if (isset($mod_config->document->desc[0]->tagData)) {
				$info['desc'] = $mod_config->document->desc[0]->tagData;
			}
			if (isset($mod_config->document->desc[0]->tagData)) {
				$info['updatedesc'] = $mod_config->document->updatedesc[0]->tagData;
			}
			if (isset($mod_config->document->name[0]->tagData)) {
				$info['name'] = $mod_config->document->name[0]->tagData;
			}
			if (isset($mod_config->document->authorname[0]->tagData)) {
				$info['authorname'] = $mod_config->document->authorname[0]->tagData;
			}
			if (isset($mod_config->document->authoremail[0]->tagData)) {
				$info['authoremail'] = $mod_config->document->authoremail[0]->tagData;
			}
			if (isset($mod_config->document->authorurl[0]->tagData)) {
				$info['authorurl'] = $mod_config->document->authorurl[0]->tagData;
			}
			if (isset($mod_config->document->ostype[0]->tagData)) {
				$info['ostype'] = $mod_config->document->ostype[0]->tagData;
			}
        	return $info;
		} else {
			return false;
		}
    } catch (Exception $e) {
    	return false;
    }
}

?>
<script type="text/javascript">
$(document).ready(function() {
    $('#content a[href][title]').qtip({
        content: {
            text: false
        },
        position: {
            my: 'middle left', 
            at: 'top right'
        }
    });
});
</script>