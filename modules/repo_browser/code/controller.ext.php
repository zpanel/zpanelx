<?php

/**
 *
 * ZPanel - repo browser zpanel plugin, written by RusTus: www.zpanelcp.com.
 *
 */
 
class module_controller extends ctrl_module {

  static $ok = NULL;
	static $zppyout1 = NULL;
	static $zppyout2 = NULL;
	static $modulenotfound = NULL;
	static $zppycommanderror = NULL;
	static $zppycommandblank = NULL;
	static $zppycommandzppynotneeded = NULL;
	static $zppypatchok = NULL;
	static $zppypatcherror = NULL;

    static function ListRepositories() {
        global $zdbh;
		global $controller;
		$repolist = ctrl_options::GetSystemOption('zpanel_root') . "etc/zppy-cache/repo.list";
		if (file_exists($repolist)){
       		$file_handle = fopen($repolist, "r");
			$res = array();
			while (!feof($file_handle)) {
   				$line = trim(fgets($file_handle));
				//if (strpos($line, "#") === false && !fs_director::CheckForEmptyValue($line) && $line <> "packages.zpanelcp.com"){
				if (strpos($line, "#") === false && !fs_director::CheckForEmptyValue($line) && $line <> "packages.zpanelcp.com"){
					array_push($res, array('line'   => $line));
				}
			}
			fclose($file_handle);
			asort($res);
			return $res;
		} else {
			return false;
		}
    }
	
    static function ListModules() {
        global $zdbh;
		global $controller;
		$modulelist = ctrl_options::GetSystemOption('zpanel_root') . "etc/zppy-cache/package.list";
		if (file_exists($modulelist)){
       		$file_handle = fopen($modulelist, "r");
			$res = array();
			while (!feof($file_handle)) {
   				$line = trim(fgets($file_handle));
				if (!fs_director::CheckForEmptyValue($line)){
					$content = preg_split("/[\s,]+/", $line);
					if (isset($content[1]) && !fs_director::CheckForEmptyValue($content[1])){
						if (fs_director::CheckForEmptyValue(self::ForbiddenModule($content[0]))) {	
							$url = "http://" . $content[1] . "/" . $content[0] . ".zpp";					
							$sql = "SELECT * FROM x_modules WHERE mo_folder_vc = '" . $content[0] . "'";
	        				$numrows = $zdbh->query($sql);
					        if ($numrows->fetchColumn() <> 0) {
								$installed = "installed";
								$title = $content[0] . " is already installed on this ZPanel server.";
								$modinfo = $zdbh->query("SELECT * FROM x_modules WHERE mo_folder_vc = '" . $content[0] . "'")->fetch();
								$info = ui_module::GetModuleXMLTags($modinfo['mo_folder_vc']);
								$description = $modinfo['mo_desc_tx'];
								$authorurl = $info['authorurl'];
								$version = $info['version'];
								$authoremail = $info['authoremail'];
								$authorname = $info['authorname'];
								$buttonaction = "Remove";
								$buttoncss = "button-loader delete btn btn-danger fg-button ui-state-default ui-corner-all";
								$installedhtml = "<a href=\"./?module=" . $content[0] . "\" title=\"Launch module\">" . $content[0] . "</a>";						
							} else {
								$installed = "notinstalled";
								$title = "To install this module, click: INSTALL";
								$description = "No description available.";
								$authorurl = "";
								$version = "not available";
								$authoremail = "not available";
								$authorname = "not available";
								$buttonaction = "Install";
								$buttoncss = "button-loader btn btn-primary fg-button ui-state-default ui-corner-all";
								$installedhtml = $content[0];
							} 
	                		array_push($res, array('modulename'  => trim($content[0]),
                    				   	   'reponame'        => trim($content[1]),
										   'description'     => $description,
										   'buttonaction'    => $buttonaction,
										   'buttoncss'		 => $buttoncss,
										   'authorurl'       => $authorurl,
										   'version'         => $version,
										   'authoremail'     => $authoremail,
										   'authorname'      => $authorname,
										   'installed'		 => $installed,
										   'title'           => $title,
										   'installedhtml'   => $installedhtml,
										   'url'			 => $url));
						}
					}
				}
			}
			fclose($file_handle);
			asort($res);
			return $res;
		} else {
			return false;
		}
    }
	
    static function GetTotalPackages() {
        global $zdbh;
		global $controller;
		$modulelist = ctrl_options::GetSystemOption('zpanel_root') . "etc/zppy-cache/package.list";
		if (file_exists($modulelist)){
			$lines = file($modulelist);
			$totalpackages = count($lines);
			return $totalpackages;
		} else {
			return false;
		}
	}

	static function RunZPPYCommand($commandtorun){
		if (!fs_director::CheckForEmptyValue($commandtorun)) {	 
			if (self::RunZPPYClient($commandtorun, false, true)){;
		 		return true;
			} else {
				return false;
			}
		 } else {
		 	self::$zppycommandblank = true;
		 	return false;
		 }
	}
	
	static function AddRepo($repotoadd, $debug=false){
		 if (!fs_director::CheckForEmptyValue($repotoadd)) {
		 	$command = "repo add " . $repotoadd;
			if (self::RunZPPYClient($command, true, $debug)){;
		 		return true;
			} else {
				return false;
			}
		 } else {
		 	return false;
		 }
	}

	static function ConfirmDeleteRepo($repotodelete, $debug=false){
		if (!fs_director::CheckForEmptyValue($repotodelete)) {
			if (!fs_director::CheckForEmptyValue($debug)) {
				$debug = true;
			}
			$command = "repo remove " . $repotodelete;
			if (self::RunZPPYClient($command, true, $debug)){;
		 		return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	static function ConfirmInstallModule($moduletoadd, $debug=false){
		global $zdbh;
		global $controller;
		 if (!fs_director::CheckForEmptyValue($moduletoadd)) {
		    $checkurl = str_replace(":::","/", $moduletoadd) . ".zpp";
		 	$moduletoadd = explode(":::", $moduletoadd);
		 	if (self::is_url_exist($checkurl)){
			 	$command = "install " . $moduletoadd[1];
				if (self::RunZPPYClient($command, true, $debug)){;
					$sql = "SELECT * FROM x_modules WHERE mo_folder_vc = '" . $moduletoadd[1] . "'";
	        				$numrows = $zdbh->query($sql);
					        if ($numrows->fetchColumn() <> 0) {
								$modinfo = $zdbh->query("SELECT * FROM x_modules WHERE mo_folder_vc = '" . $moduletoadd[1] . "'")->fetch();
								ctrl_groups::AddGroupModulePermissions("1", $modinfo['mo_id_pk']);
							}
			 		return true;
				} else {
					return false;
				}
			} else {
				self::$modulenotfound = true;
				return false;
			}
		 } else {
		 	return false;
		 }
	}

	static function ConfirmDeleteModule($moduletodelete, $debug=false){
		if (!fs_director::CheckForEmptyValue($moduletodelete)) {
			$moduletodelete = explode(":::", $moduletodelete);
			$command = "remove " . $moduletodelete[1] . " -y";
			if (self::RunZPPYClient($command, true, $debug)){;
		 		return true;
			} else {
				return false;
			}
			/**
			ob_start();
			system($command, $retval);
			ob_end_clean();
			ob_start();
			system($zppyupdate, $retval);
			ob_end_clean();
			**/
		} else {
			return false;
		}
	}

	static function PatchZPPY(){
		global $controller;
		$zppyfolder = ctrl_options::GetSystemOption('zpanel_root') . "bin/";
		$zppypatchedfolder = ctrl_options::GetSystemOption('zpanel_root') . "modules/repo_browser/bin/";
		if (fs_director::CheckFileExists($zppyfolder . "zppy") && fs_director::CheckFileExists($zppypatchedfolder . "zppy")){
			if (fs_director::RenameFileFolder($zppyfolder . "zppy", $zppyfolder . "zppy_repo_bak")){
				fs_filehandler::CopyFile($zppypatchedfolder . "zppy", $zppyfolder . "zppy");
				fs_director::SetFileSystemPermissions($zppypatchedfolder . "zppy", 777);
				if (!fs_director::CheckForEmptyValue(self::CheckZPPYPatch())){
					return true;
				} else {
					return false;
				}

			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	static function ConfirmUpdateVersionDB($module, $version){
		global $zdbh;
    	try {
			$sql = "UPDATE x_modules SET mo_version_in='" . $version . "' WHERE mo_folder_vc='" . $module . "'";
			$sql = $zdbh->prepare($sql);
			$sql->execute();
			$sql = "UPDATE x_modules SET mo_updatever_vc=NULL WHERE mo_folder_vc='" . $module . "'";
			$sql = $zdbh->prepare($sql);
			$sql->execute();
			return true;
	    } catch(Exception $e){
     		return false;
    	}
	}

	static function ConfirmUpdateModule($moduletoupdate, $debug=false){
		if (!fs_director::CheckForEmptyValue($moduletoupdate)) {
			$moduletoupdate = explode(":::", $moduletoupdate);
			$command = "upgrade " . $moduletoupdate[1];
			if (self::RunZPPYClient($command, true, $debug)){;
		 		return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
    static function getRepoList() {
        $repolist = self::ListRepositories();
        if (!fs_director::CheckForEmptyValue($repolist)) {
            return $repolist;
        } else {
            return false;
        }
    }
	
    static function getModuleList() {
        $modulelist = self::ListModules();
        if (!fs_director::CheckForEmptyValue($modulelist)) {
            return $modulelist;
        } else {
            return false;
        }
    }	

    static function doAddRepo() {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
		if (self::is_url_exist("http://" . $formvars['inAddRepo'] . "/packages.txt")){
			$debug = NULL;
			if (isset($formvars['inDebugZPPY'])){
				$debug = $formvars['inDebugZPPY'];
			}	
      		if (self::AddRepo($formvars['inAddRepo'], $debug)) {
				self::$ok = true;
	        } else {
	            return false;
	        }
		
		} else {
        	header("location: ./?module=" . $controller->GetCurrentModule() . "&show=ConfirmRepoInstall&other=" . $formvars['inAddRepo'] . "");
            exit;
		}
        return;
    }

    static function doDeleteRepo() {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
            if (isset($formvars['inDeleteRepo'])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=Delete&other=" . $formvars['inDeleteRepo'] . "");
                exit;
            }
        return;
    }

    static function doConfirmDeleteRepo() {
        global $controller;
        $formvars = $controller->GetAllControllerRequests('FORM');
		$debug = NULL;
		if (isset($formvars['inDebugZPPY'])){
			$debug = $formvars['inDebugZPPY'];
		}
        if (self::ConfirmDeleteRepo($formvars['inDelete'], $debug)) {
			self::$ok = true;
        } else {
            return false;
        }
        return;
    }

    static function getisConfirmDeleteRepo() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "Delete"))
            return true;
        return false;
    }

    static function getCurrentRepo() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            return $controller->GetControllerRequest('URL', 'other');
        } else {
            return "";
        }
    }

    static function getisConfirmAddRepo() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "ConfirmRepoInstall"))
            return true;
        return false;
    }

    static function doConfirmAddRepo() {
        global $controller;
        $formvars = $controller->GetAllControllerRequests('FORM');
		$debug = NULL;
		if (isset($formvars['inDebugZPPY'])){
			$debug = $formvars['inDebugZPPY'];
		}
        if (self::AddRepo($formvars['inConfirmAddRepo'], $debug)) {
			self::$ok = true;
        } else {
            return false;
        }
        return;
    }

    static function doPatchZPPY() {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::PatchZPPY()) {
			self::$zppypatchok = true;
        } else {
			self::$zppypatcherror = true;
            return false;
        }
        return;
    }

    static function doAddModule() {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
		$debug = NULL;
		if (isset($formvars['inDebugZPPY'])){
			$debug = $formvars['inDebugZPPY'];
		}
        if (self::AddModule($formvars['inInstallModule'], $debug)) {
			self::$ok = true;
        } else {
            return false;
        }
        return;
    }
	
    static function doRunZPPYCommand() {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::RunZPPYCommand($formvars['inRunZPPYCommand'])) {
			self::$ok = true;
        } else {
            return false;
        }
        return;
    }


    static function doDeleteModule() {
        global $controller;
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
		$urlvars = $controller->GetAllControllerRequests('URL');
            if (isset($formvars['inRemoveModule'])) {
				if (!fs_director::CheckForEmptyValue(self::CheckZPPYPatch())) {
	                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=ConfirmModuleDelete&other=" . $formvars['inRemoveModule'] . "");
	                exit;
				} else {
                	header("location: ./?module=" . $controller->GetCurrentModule() . "&show=ConfirmZPPYPatch&other=" . $formvars['inRemoveModule'] . "");
                	exit;				
				}
            }
            if (isset($formvars['inInstallModule'])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=ConfirmModuleInstall&other=" . $formvars['inInstallModule'] . "");
                exit;
            }
            if (isset($formvars['inUpdateVersionDB'])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=ConfirmUpdateVersionDB&other=" . $formvars['inUpdateVersionDB'] . "");
                exit;
            }
            if (isset($formvars['inUpdateModule'])) {
                header("location: ./?module=" . $controller->GetCurrentModule() . "&show=ConfirmModuleUpdate&other=" . $formvars['inUpdateModule'] . "");
                exit;
            }
        return;
    }

    static function doConfirmDeleteModule() {
        global $controller;
        $formvars = $controller->GetAllControllerRequests('FORM');
		$debug = NULL;
		if (isset($formvars['inDebugZPPY'])){
			$debug = $formvars['inDebugZPPY'];
		}
        if (self::ConfirmDeleteModule($formvars['inDelete'], $debug)) {
			self::$ok = true;
        } else {
            return false;
        }
        return;
    }

    static function doConfirmInstallModule() {
        global $controller;
        $formvars = $controller->GetAllControllerRequests('FORM');
		$debug = NULL;
		if (isset($formvars['inDebugZPPY'])){
			$debug = $formvars['inDebugZPPY'];
		}
        if (self::ConfirmInstallModule($formvars['inInstall'], $debug)) {
			self::$ok = true;
        } else {
            return false;
        }
        return;
    }

    static function doConfirmUpdateVersionDB() {
        global $controller;
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (self::ConfirmUpdateVersionDB($formvars['inUpdate'], $formvars['inVersion'])) {
			self::$ok = true;
        } else {
            return false;
        }
        return;
    }

    static function doConfirmUpdateModule() {
        global $controller;
        $formvars = $controller->GetAllControllerRequests('FORM');
		$debug = NULL;
		if (isset($formvars['inDebugZPPY'])){
			$debug = $formvars['inDebugZPPY'];
		}
        if (self::ConfirmUpdateModule($formvars['inUpdate'], $debug)) {
			self::$ok = true;
        } else {
            return false;
        }
        return;
    }

    static function getisConfirmDeleteModule() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "ConfirmModuleDelete"))
            return true;
        return false;
    }

    static function getisConfirmZPPYPatch() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "ConfirmZPPYPatch"))
            return true;
        return false;
    }

    static function getisConfirmInstallModule() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "ConfirmModuleInstall"))
            return true;
        return false;
    }

    static function getisConfirmUpdateVersionDB() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "ConfirmUpdateVersionDB"))
            return true;
        return false;
    }

    static function getisConfirmUpdateModule() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((isset($urlvars['show'])) && ($urlvars['show'] == "ConfirmModuleUpdate"))
            return true;
        return false;
    }

    static function getCurrentModuleFull() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
            return $controller->GetControllerRequest('URL', 'other');
        } else {
            return "";
        }
    }

    static function getCurrentModuleName() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
			$Modulename = explode(":::", $controller->GetControllerRequest('URL', 'other'));
			if (isset($Modulename[1])){
				return $Modulename[1];
			} else {
				return "";
			}
        } else {
            return "";
        }
    }

    static function getCurrentModuleVersion() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
			$Moduleversion = explode(":::", $controller->GetControllerRequest('URL', 'other'));
			if (isset($Moduleversion[0])){
				return $Moduleversion[0];
			} else {
				return "";
			}
        } else {
            return "";
        }
    }

    static function getCurrentModuleInstalledVersion() {
        global $controller;
        if ($controller->GetControllerRequest('URL', 'other')) {
			$Moduleversion = explode(":::", $controller->GetControllerRequest('URL', 'other'));
			if (isset($Moduleversion[2])){
				return $Moduleversion[2];
			} else {
				return "";
			}
        } else {
            return "";
        }
    }

    static function getisMain() {
        global $controller;
        $urlvars = $controller->GetAllControllerRequests('URL');
        if ((!isset($urlvars['show'])))
            return true;
        return false;
    }

	static function getZPanelID() {
		return ctrl_auth::CurrentUserID();
    }

    static function getModulePath() {
        global $controller;
        $module_path = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/";
        return $module_path;
    }

	static function is_url_exist($url){
    	$ch = curl_init($url);    
	    curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_exec($ch);
	    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    if($code == 200){
	       $status = true;
	    }else{
	      $status = false;
	    }
	    curl_close($ch);
	   return $status;
	}
	
	static function RunZPPYClient($command, $update=false, $out=false){
		$cmd1 = NULL;
		$cmd2 = NULL;
		 if (fs_director::CheckForEmptyValue($command)) {
			self::$zppycommandblank = true;
			return false;
	 	}
		$checkcommand = explode(" ", $command);
		if (strstr("ZPPY", strtoupper($checkcommand[0]))){
			self::$zppycommandzppynotneeded = true;
			return false;
		}
		if (!self::ZPPYActionsArray($command)){
			self::$zppycommanderror = true;
			return false;
		}
		if (sys_versions::ShowOSPlatformVersion() <> "WIN"){
			$cmd = "zppy " . $command;
			$zppyupdate = "zppy update";
			$cmd1 = shell_exec($cmd);
			if ($update == true){
				$cmd2 = shell_exec($zppyupdate);
			}
		} else {
			$cmd = "zppy " . $command;
			$zppyupdate = "zppy update";
			$cmd1 = shell_exec($cmd);
			if ($update == true){
				$cmd2 = shell_exec($zppyupdate);
			}
		}
		if (!fs_director::CheckForEmptyValue($cmd1)) {
			if ($out == true){
				self::$zppyout1 = "<strong>" . ui_language::translate("Command Given") . ":</strong> " . $cmd . "<br><strong>" . ui_language::translate("Command Response") . ":</strong> " . $cmd1 . "<br>";
			}
		}
		if (!fs_director::CheckForEmptyValue($cmd2)) {
			if ($out == true){
				self::$zppyout2 = "<strong>" . ui_language::translate("Command Given") . ":</strong> " . $zppyupdate . "<br><strong>" . ui_language::translate("Command Response") . ":</strong> " . $cmd2 . "<br>";
			}
		}
		return true;
	}

    static function getResult() {
        if (!fs_director::CheckForEmptyValue(self::$ok)) {
            return ui_sysmessage::shout(ui_language::translate("Repository and ZPPY cache has been updated successfully."), "zannounceok");
        }
        if (!fs_director::CheckForEmptyValue(self::$modulenotfound)) {
            return ui_sysmessage::shout(ui_language::translate("ZPPY can not install this module because it does not exist in the repository."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$zppycommandzppynotneeded)) {
            return ui_sysmessage::shout(ui_language::translate("You do not need to proceed the command with \"zppy\"."), "zannounceerror");
        }
        if (!fs_director::CheckForEmptyValue(self::$zppycommanderror)) {
            return ui_sysmessage::shout(ui_language::translate("You have entered and invalid ZPPY command."), "zannounceerror");
        }
		if (!fs_director::CheckForEmptyValue(self::$zppycommandblank)) {
			return ui_sysmessage::shout(ui_language::translate("You have entered and invalid ZPPY command."), "zannounceerror");
		}
		if (!fs_director::CheckForEmptyValue(self::$zppypatchok)) {
			return ui_sysmessage::shout(ui_language::translate("Repo Browser has successfully installed the ZPPY patch file. You can now remove your module with Repo Browser"), "zannounceok");
		}
		if (!fs_director::CheckForEmptyValue(self::$zppypatcherror)) {
			return ui_sysmessage::shout(ui_language::translate("There was a problem patching your ZPPY file. If you still cannot delete a module, then try installing the patch manually. (See developer notes)"), "zannounceerror");
		}
        return;
    }

    static function getZppyOut() {
		$out = false;
		if (!fs_director::CheckForEmptyValue(self::$zppyout1)) {
			$out = self::$zppyout1;
		}
		if (!fs_director::CheckForEmptyValue(self::$zppyout2)) {
			$out = self::$zppyout2;
		}
		if (!fs_director::CheckForEmptyValue(self::$zppyout1) && !fs_director::CheckForEmptyValue(self::$zppyout2)) {
			$out = self::$zppyout1 . "<br>" . self::$zppyout2;
		}
        if (!fs_director::CheckForEmptyValue($out)) {
            return "<div class=\"zdebug alert alert-block alert-danger\"><button class=\"close\" data-dismiss=\"alert\" type=\"button\">&times</button><strong>" . ui_language::translate("ZPPY COMMAND OUTPUT") . ":</strong><br><br>" . $out . "</div>";
        }
        return;
    }

	static function CheckZPPYPatch(){
		$zpanelversion = ctrl_options::GetSystemOption('dbversion');
		$zppy = ctrl_options::GetSystemOption('zpanel_root') . "bin/zppy";
		if ($zpanelversion < '10.1.0'){	
			if (file_exists($zppy)){
				$zppy = file_get_contents($zppy);
				if(strpos($zppy, '/**REPO_BROWSER**/') !== FALSE){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		return true;
	}

    static function ZPPYActionsArray($check) {
		$check = explode(" ", $check);
		$commands = array('install',
						  'upgrade',
						  'update',
						  'remove',
						  'repo',
						  'checkperms',
						  '--version',
						  '--help');
		if (in_array($check[0], $commands)){
			return true;
		} else {
			return false;
		}				  
    }

	static function ForbiddenModule($module){
        global $zdbh;
		$coremodules = array('ALIASES',
						  'APACHE_ADMIN',
						  'BACKUP_ADMIN',
						  'BACKUPMGR',
						  'CLIENT_NOTICES',
						  'CRON',
						  'DISTLISTS',
						  'DNS_ADMIN',
						  'DNS_MANAGER',
						  'DOMAINS',
						  'FAQS',
						  'FORWARDERS',
						  'FTP_ADMIN',
						  'FTP_MANAGEMENT',
						  'HTPASSWD',
						  'MAIL_ADMIN',
						  'MAILBOXES',
						  'MANAGE_CLIENTS',
						  'MANAGE_GROUPS',
						  'MODULEADMIN',
						  'MY_ACCOUNT',
						  'MYSQL_DATABASES',
						  'MYSQL_USERS',
						  'NEWS',
						  'PACKAGES',
						  'PARKED_DOMAINS',
						  'PASSWORD_ASSISTANT',
						  'PHPINFO',
						  'PHPMYADMIN',
						  'PHPSYSINFO',
						  'SERVICES',
						  'SHADOWING',
						  'SUB_DOMAINS',
						  'THEME_MANAGER',
						  'UPDATES',
						  'USAGE_VIEWER',
						  'WEBALIZER_STATS',
						  'WEBMAIL',
						  'ZPANELCONFIG',
						  'ZPX_CORE_MODULE');
		if (in_array(strtoupper($module), $coremodules)){
			return true;
		} else {
			return false;
		}
	}

}

?>