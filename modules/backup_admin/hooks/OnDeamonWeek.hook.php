<?php

include('cnf/db.php');
$z_db_user = $user;
$z_db_pass = $pass;
$z_db_host = $host;
$z_db_name = $dbname;
try {
    $zdbh = new db_driver("mysql:host=" . $z_db_host . ";dbname=" . $z_db_name . "", $z_db_user, $z_db_pass);
} catch (PDOException $e) {
    
}

echo fs_filehandler::NewLine() . "START Backup Config." . fs_filehandler::NewLine();
if (ui_module::CheckModuleEnabled('Backup Config')) {
    echo "Backup Config module ENABLED..." . fs_filehandler::NewLine();

// Schedule daily backups are enabled and set to week...
    if (strtolower(ctrl_options::GetSystemOption('schedule_bu')) == "true" && strtolower(ctrl_options::GetSystemOption('files_bu')) == 'week') {
        runtime_hook::Execute('OnBeforeScheduleBackup');
        echo "Backup Scheduling enabled - Backing up all enabled client files now..." . fs_filehandler::NewLine();
        // Get all accounts
        $bsql = "SELECT * FROM x_accounts WHERE ac_enabled_in=1 AND ac_deleted_ts IS NULL";
        $numrows = $zdbh->query($bsql);
        if ($numrows->fetchColumn() <> 0) {
            $bsql = $zdbh->prepare($bsql);
            $bsql->execute();
            while ($rowclients = $bsql->fetch()) {
                echo "Backing up client folder: " . $rowclients['ac_user_vc'] . "/public_html..." . fs_filehandler::NewLine();
                // User loop
                $username = $rowclients['ac_user_vc'];
                $userid = $rowclients['ac_id_pk'];
                $homedir = ctrl_options::GetSystemOption('hosted_dir') . $username;
                $backupname = $username . "_files_" . date("M-d-Y_hms", time());
                $dbstamp = date("dmy_Gi", time());
                // We now see what the OS is before we work out what compression command to use.. 
	            if (sys_versions::ShowOSPlatformVersion() == "Windows") {
	                $resault = exec(fs_director::SlashesToWin(ctrl_options::GetSystemOption('zip_exe') . " a -tzip -y-r " . ctrl_options::GetSystemOption('temp_dir') . $backupname . ".zip " . $homedir . "/public_html"));
	            } else {//cd /var/zpanel/hostdata/zadmin/; zip -r backups/backup.zip public_html/
	                $resault = exec("cd " . $homedir . "/ && " . ctrl_options::GetSystemOption('zip_exe') . " -r9 " . ctrl_options::GetSystemOption('temp_dir') . $backupname . " public_html/*");
	                @chmod(ctrl_options::GetSystemOption('temp_dir') . $backupname . ".zip", 0777);
	            }
                // We have the backup now lets output it to disk or download
                if (file_exists(ctrl_options::GetSystemOption('temp_dir') . $backupname . ".zip")) {
                    // Copy Backup to user home directory...
                    $backupdir = $homedir . "/backups/";
                    if (!is_dir($backupdir)) {
                        mkdir($backupdir, 0777, TRUE);
                    }
                    copy(ctrl_options::GetSystemOption('temp_dir') . $backupname . ".zip", $backupdir . $backupname . ".zip");
                    unlink(ctrl_options::GetSystemOption('temp_dir') . $backupname . ".zip");
                    fs_director::SetFileSystemPermissions($backupdir . $backupname . ".zip", 0777);
                    echo $backupdir . $backupname . ".zip" . fs_filehandler::NewLine();
                }
            }
        }
        runtime_hook::Execute('OnAfterScheduleBackup');
        echo "Backup Schedule COMPLETE..." . fs_filehandler::NewLine();
    }

    // Clean temp backups....
    echo fs_filehandler::NewLine() . "Purging backups from temp folder..." . fs_filehandler::NewLine();
    clearstatcache();
    echo "[FILE][PURGE_DATE][FILE_DATE][ACTION]" . fs_filehandler::NewLine();
    $temp_dir = ctrl_options::GetSystemOption('zpanel_root') . "/modules/backupmgr/temp/";
    if ($handle = @opendir($temp_dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $filetime = @filemtime($temp_dir . $file);
                if ($filetime == NULL) {
                    $filetime = @filemtime(utf8_decode($temp_dir . $file));
                }
                $filetime = floor((time() - $filetime) / 86400);
                echo "" . $file . " - " . $purge_date . " - " . $filetime . "";
                if (1 <= $filetime) {
                    //delete the file
                    echo " - Deleting file..." . fs_filehandler::NewLine();
                    unlink($temp_dir . $file);
                } else {
                    echo " - Skipping file..." . fs_filehandler::NewLine();
                }
            }
        }
    }
} else {
    echo "Backup Config module DISABLED...nothing to do." . fs_filehandler::NewLine();
}
echo "END Backup Config." . fs_filehandler::NewLine();
?>