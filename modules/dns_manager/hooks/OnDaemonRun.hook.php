<?phpif (file_exists("C:/zpanel/panel/index.php")) {
	echo fs_filehandler::NewLine() . "START DNS Manager Hook" . fs_filehandler::NewLine();
	if (ui_module::CheckModuleEnabled('Backup Config')){
		echo "DNS Manager module ENABLED..." . fs_filehandler::NewLine();
		if (!fs_director::CheckForEmptyValue(ctrl_options::GetOption('dns_hasupdates'))){
			echo "DNS Records have changed... Writing new/updated records..." . fs_filehandler::NewLine();
			WriteDNSZoneRecordsHook();
			WriteDNSNamedHook();
			ResetDNSRecordsUpatedHook();
			PurgeOldZoneDNSRecordsHook();
			SendSlaveNamedConfigHook();
			ReloadBindHook();
		} else {
			echo "DNS Records have not changed...nothing to do." . fs_filehandler::NewLine();
		}
	} else {
		echo "DNS Manager module DISABLED...nothing to do." . fs_filehandler::NewLine();
	}
	echo "END DNS Manager Hook." . fs_filehandler::NewLine();

	function WriteDNSZoneRecordsHook() {
		global $zdbh;
		$dnsrecords = array();
        $RecordsNeedingUpdateArray = array();
		//Get all the records needing upadated and put them in an array.
		$GetRecordsNeedingUpdate = ctrl_options::GetOption('dns_hasupdates');
		$RecordsNeedingUpdate = explode(",", $GetRecordsNeedingUpdate);
		foreach ($RecordsNeedingUpdate as $RecordNeedingUpdate){
			$RecordsNeedingUpdateArray[] = $RecordNeedingUpdate;
		}	
        //Get all the domain ID's we need and put them in an array.
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NULL GROUP BY dn_vhost_fk");
                $sql->execute();
                while ($rowdns = $sql->fetch()) {
                    $dnsrecords[] = $rowdns['dn_vhost_fk'];
                }
            }
        }
        //Now we have all domain ID's, loop through them and find records for each zone file.
        foreach ($dnsrecords as $dnsrecord) {
		//if (in_array($dnsrecord, $RecordsNeedingUpdateArray)){
            $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_vhost_fk=" . $dnsrecord . "  AND dn_deleted_ts IS NULL";
            if ($numrows = $zdbh->query($sql)) {
                if ($numrows->fetchColumn() <> 0) {
                    $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_vhost_fk=" . $dnsrecord . " AND dn_deleted_ts IS NULL ORDER BY dn_type_vc");
                    $sql->execute();
                    $domain = $zdbh->query("SELECT dn_name_vc FROM x_dns WHERE dn_vhost_fk=" . $dnsrecord . " AND dn_deleted_ts IS NULL")->Fetch();
					//Create zone directory if it doesnt exists...
				    if (!is_dir(ctrl_options::GetOption('zone_dir'))) {
                    	fs_director::CreateDirectory(ctrl_options::GetOption('zone_dir'));
						fs_director::SetFileSystemPermissions(ctrl_options::GetOption('zone_dir'));
                	}
                  $zone_file = (ctrl_options::GetOption('zone_dir')) . $domain['dn_name_vc'] . ".txt";
                    $line = "$" . "TTL 10800" . fs_filehandler::NewLine();
                    $line .= "@ IN SOA " . $domain['dn_name_vc'] . ".    ";
                    $line .= "postmaster." . $domain['dn_name_vc'] . ". (" . fs_filehandler::NewLine();
                    $line .= "                       " . date("Ymdt") . "	;serial" . fs_filehandler::NewLine();
                    $line .= "                       " . ctrl_options::GetOption('refresh_ttl') . "      ;refresh after 6 hours" . fs_filehandler::NewLine();
                    $line .= "                       " . ctrl_options::GetOption('retry_ttl') . "       ;retry after 1 hour" . fs_filehandler::NewLine();
                    $line .= "                       " . ctrl_options::GetOption('expire_ttl') . "     ;expire after 1 week" . fs_filehandler::NewLine();
                    $line .= "                       " . ctrl_options::GetOption('minimum_ttl') . " )    ;minimum TTL of 1 day" . fs_filehandler::NewLine();
                    while ($rowdns = $sql->fetch()) {
                        if ($rowdns['dn_type_vc'] == "A") {
                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		A		" . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "AAAA") {
                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		AAAA		" . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "CNAME") {
                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		CNAME		" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "MX") {
                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		MX		" . $rowdns['dn_priority_in'] . "	" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                        }
						if ($rowdns['dn_type_vc'] == "DKIM") {
                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		TXT		\"( " . stripslashes($rowdns['dn_target_vc']) . " )\"" . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "TXT") {
                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		TXT		\"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "SRV") {
                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		SRV		" . $rowdns['dn_priority_in'] . "	" . $rowdns['dn_weight_in'] . "	" . $rowdns['dn_port_in'] . "	" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "SPF") {
                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		TXT		\"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();
                        }
                        if ($rowdns['dn_type_vc'] == "NS") {
                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		NS		" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();
                        }
                    }

					echo "Updating zone record: " . $domain['dn_name_vc'] . fs_filehandler::NewLine();
					fs_filehandler::UpdateFile($zone_file, 0777, $line);
                }
            }
        }
	  //}	
		
	
    }

	function WriteDNSNamedHook() {
		global $zdbh;
		$domains = array();	
        //Get all the domain ID's we need and put them in an array.
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NULL GROUP BY dn_vhost_fk");
                $sql->execute();
                while ($rowdns = $sql->fetch()) {
                    $domains[] = $rowdns['dn_name_vc'];
                }
            }
        }
		//Create named directory if it doesnt exists...
		if (!is_dir(ctrl_options::GetOption('named_dir'))) {
        	fs_director::CreateDirectory(ctrl_options::GetOption('named_dir'));
			fs_director::SetFileSystemPermissions(ctrl_options::GetOption('named_dir'));
        }

		$named_file       = ctrl_options::GetOption('named_dir') . ctrl_options::GetOption('named_conf');

    // Slave named file won't be created if the filename is blank...
		$named_slave_file = ctrl_options::GetOption('named_conf_slave');
		$named_slave_file = (fs_director::CheckForEmptyValue($named_slave_file)?'':ctrl_options::GetOption('named_dir').$named_slave_file);

    if ($named_slave_file==$named_file)
      $named_slave_file='';

		echo "Updating " . $named_file . fs_filehandler::NewLine();
        //Now we have all domain ID's, loop through them and find records for each zone file.
		$line = "";
		$slaveline = '';
        foreach ($domains as $domain) {
			echo "CHECKING ZONE FILE: " . ctrl_options::GetOption('zone_dir') . $domain . ".txt..." . fs_filehandler::NewLine();
			system(ctrl_options::GetOption('named_checkzone') . " " . $domain . " " . ctrl_options::GetOption('zone_dir') . $domain . ".txt", $retval);
			echo $retval . fs_filehandler::NewLine();
			if ($retval == 0){
				echo "Syntax check passed. Adding zone to " . ctrl_options::GetOption('named_conf') . fs_filehandler::NewLine();
    		$line .= "zone \"" . $domain . "\" IN {" . fs_filehandler::NewLine();
			$line .= "	type master;" . fs_filehandler::NewLine();
			$line .= "	file \"" . ctrl_options::GetOption('zone_dir') . $domain . ".txt\";" . fs_filehandler::NewLine();
			$line .= "	allow-transfer { " .ctrl_options::GetOption('allow_xfer') . "; };" . fs_filehandler::NewLine();
			$line .= "};" . fs_filehandler::NewLine();

        // Slave named configuration
        if (!fs_director::CheckForEmptyValue($named_slave_file))
        {
          $slaveline .= "zone \"$domain\" IN {"   . fs_filehandler::NewLine()
                     . '	type slave;'           . fs_filehandler::NewLine()
                     . '	masters { ' . getHostByName(getHostName()) . '; };' . fs_filehandler::NewLine()
                     . "	file \"C:\zpanel\configs\bind\zones\\$domain.txt\";" . fs_filehandler::NewLine()
                     . '};'                      . fs_filehandler::NewLine();
        } //if (!fs_director::CheckForEmptyValue($named_slave_file))...

			} else {
				echo "Syntax ERROR. Skipping zone record." . fs_filehandler::NewLine();
			}
        }
		fs_filehandler::UpdateFile($named_file, 0777, $line);
    if (!fs_director::CheckForEmptyValue($named_slave_file))
      fs_filehandler::UpdateFile($named_slave_file, 0777, $slaveline);

    }



	function ResetDNSRecordsUpatedHook() {
		global $zdbh;
	    $sql = $zdbh->prepare("UPDATE x_settings SET so_value_tx=NULL WHERE so_name_vc='dns_hasupdates'");
        $sql->execute();	
	}



	function PurgeOldZoneDNSRecordsHook() {
		global $zdbh;
		$domains = array();
        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NULL GROUP BY dn_name_vc");
                $sql->execute();
                while ($rowvhost = $sql->fetch()) {
                    $domains[] = $rowvhost['dn_name_vc'];
                }
            }
        }
		$zonefiles = scandir(ctrl_options::GetOption('zone_dir'));
		foreach ($zonefiles as $zonefile){
			if (!in_array(substr($zonefile, 0 , -4), $domains) && $zonefile != "." && $zonefile != ".."){
        		if (file_exists(ctrl_options::GetOption('zone_dir') . $zonefile)) {
					echo "Purging old zone record from disk: " . substr($zonefile, 0, -4) . fs_filehandler::NewLine();
					unlink(ctrl_options::GetOption('zone_dir') . $zonefile);
				}
			}
		}
	}

	function ReloadBindHook(){
		if (sys_versions::ShowOSPlatformVersion() == "Windows") {
			$reload_bind = ctrl_options::GetOption('bind_dir') . "rndc.exe reload";
		}else{
			$reload_bind = ctrl_options::GetOption('zsudo') . " service " . ctrl_options::GetOption('bind_service') . " reload";
		}
		echo "Reloading BIND now..." . fs_filehandler::NewLine();
		pclose(popen($reload_bind,'r'));
	}

  /*
   * Send the slave configuration file to slave server.
   *   The 'slave_connection_details' Option should have Newline delimited connection
   *   details forall the slave servers.
   *   Each line should state the username, password, protocol, ipaddress, port.
   *      i.e.:  username=xxxx;password=yyyy:192.168.0.2:21:/path/to/slave/
   *             username=xxxx;password=yyyy:192.168.0.3:20:/path/to/slave/
   *             username=xxxx;password=yyyy:192.168.0.3:/path/to/slave/
   */
  function SendSlaveNamedConfigHook()
  {
    $connection_detail_set = ctrl_options::GetOption('slave_connection_details');

    if (fs_director::CheckForEmptyValue($connection_detail_set))
      return false;

		$named_slave_file = ctrl_options::GetOption('named_conf_slave');
		$named_slave_file = (fs_director::CheckForEmptyValue($named_slave_file)? '' : ctrl_options::GetOption('named_dir').$named_slave_file);

    if ($named_slave_file=='')
      return false;

    $connection_detail_set = explode("\n", $connection_detail_set);

    foreach ($connection_detail_set as $connection_detail)
    {
      $connection_detail = explode(':', $connection_detail);

      if (count($connection_detail) < 3)
      {
        echo "Slave Connection details are invalid.\n";

        return false;
      } // if (count($connection_detail) < 3)

      $connection_params = explode(';', $connection_detail[0]);
      $slave_ip_address  = $connection_detail[1];
      $slave_port        = (count($connection_detail) > 3 ? $connection_detail[2] : '');
      $slave_config_path = $connection_detail[($slave_port=='' ? 2 : 3)];
      $slave_config_path = rtrim($slave_config_path, '/') .'/'. ctrl_options::GetOption('named_conf_slave');

      foreach($connection_params as $param)
      {
        $param = explode('=', $param);

        switch ($param[0]) {
          case 'username': $username = $param[1]; break;
          case 'password': $password = $param[1]; break;

          default: echo "Invalid configuration.\n"; break;
        }//switch $param[0]
      } //foreach($connection_params as $param)

      if ($slave_port == '')
        $slave_port = 21;

      $connection = ftp_connect($slave_ip_address, $slave_port, 30);
      if (!$connection)
      {
        echo "FTP connection has failed!.\n";

        return false;
      } //if (!$connection)

      $login_result = ftp_login($connection, $username, $password);

      ftp_pasv($connection, true); 

      if ((!$connection) || (!$login_result))
      {
        echo "Attempted to connect to $slave_ip_address:$slave_port for user $username and has failed.\n";

        ftp_close($connection);
        unset    ($connection);

        return false;
      } //if ((!$connection) || (!$login_result))
      else
        echo "Connected to $slave_ip_address, for user $username/\n";

      $upload = ftp_put($connection, $slave_config_path, $named_slave_file, FTP_ASCII); 

      // check upload status
      if (!$upload)
        echo "FTP upload has failed!\n";
      else
        echo "Uploaded $slave_config_path to $slave_ip_address as $named_slave_file.\n";

      ftp_close($connection);
      unset    ($connection);

    } //foreach ($connection_detail_set as $key => $connection_detail)
  } //function SendSlaveNamedConfigHook()} else {	echo fs_filehandler::NewLine() . "START DNS Manager Hook" . fs_filehandler::NewLine();	if (ui_module::CheckModuleEnabled('Backup Config')){		echo "DNS Manager module ENABLED..." . fs_filehandler::NewLine();		if (!fs_director::CheckForEmptyValue(ctrl_options::GetOption('dns_hasupdates'))){			echo "DNS Records have changed... Writing new/updated records..." . fs_filehandler::NewLine();			WriteDNSZoneRecordsHook();			WriteDNSNamedHook();			ResetDNSRecordsUpatedHook();			PurgeOldZoneDNSRecordsHook();			SendSlaveNamedConfigHook();			ReloadBindHook();		} else {			echo "DNS Records have not changed...nothing to do." . fs_filehandler::NewLine();		}	} else {		echo "DNS Manager module DISABLED...nothing to do." . fs_filehandler::NewLine();	}	echo "END DNS Manager Hook." . fs_filehandler::NewLine();	function WriteDNSZoneRecordsHook() {		global $zdbh;		$dnsrecords = array();        $RecordsNeedingUpdateArray = array();		//Get all the records needing upadated and put them in an array.		$GetRecordsNeedingUpdate = ctrl_options::GetOption('dns_hasupdates');		$RecordsNeedingUpdate = explode(",", $GetRecordsNeedingUpdate);		foreach ($RecordsNeedingUpdate as $RecordNeedingUpdate){			$RecordsNeedingUpdateArray[] = $RecordNeedingUpdate;		}	        //Get all the domain ID's we need and put them in an array.        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NULL";        if ($numrows = $zdbh->query($sql)) {            if ($numrows->fetchColumn() <> 0) {                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NULL GROUP BY dn_vhost_fk");                $sql->execute();                while ($rowdns = $sql->fetch()) {                    $dnsrecords[] = $rowdns['dn_vhost_fk'];                }            }        }        //Now we have all domain ID's, loop through them and find records for each zone file.        foreach ($dnsrecords as $dnsrecord) {		//if (in_array($dnsrecord, $RecordsNeedingUpdateArray)){            $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_vhost_fk=" . $dnsrecord . "  AND dn_deleted_ts IS NULL";            if ($numrows = $zdbh->query($sql)) {                if ($numrows->fetchColumn() <> 0) {                    $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_vhost_fk=" . $dnsrecord . " AND dn_deleted_ts IS NULL ORDER BY dn_type_vc");                    $sql->execute();                    $domain = $zdbh->query("SELECT dn_name_vc FROM x_dns WHERE dn_vhost_fk=" . $dnsrecord . " AND dn_deleted_ts IS NULL")->Fetch();					//Create zone directory if it doesnt exists...				    if (!is_dir(ctrl_options::GetOption('zone_dir'))) {                    	fs_director::CreateDirectory(ctrl_options::GetOption('zone_dir'));						fs_director::SetFileSystemPermissions(ctrl_options::GetOption('zone_dir'));                	}                  $zone_file = (ctrl_options::GetOption('zone_dir')) . $domain['dn_name_vc'] . ".txt";                    $line = "$" . "TTL 10800" . fs_filehandler::NewLine();                    $line .= "@ IN SOA " . $domain['dn_name_vc'] . ".    ";                    $line .= "postmaster." . $domain['dn_name_vc'] . ". (" . fs_filehandler::NewLine();                    $line .= "                       " . date("Ymdt") . "	;serial" . fs_filehandler::NewLine();                    $line .= "                       " . ctrl_options::GetOption('refresh_ttl') . "      ;refresh after 6 hours" . fs_filehandler::NewLine();                    $line .= "                       " . ctrl_options::GetOption('retry_ttl') . "       ;retry after 1 hour" . fs_filehandler::NewLine();                    $line .= "                       " . ctrl_options::GetOption('expire_ttl') . "     ;expire after 1 week" . fs_filehandler::NewLine();                    $line .= "                       " . ctrl_options::GetOption('minimum_ttl') . " )    ;minimum TTL of 1 day" . fs_filehandler::NewLine();                    while ($rowdns = $sql->fetch()) {                        if ($rowdns['dn_type_vc'] == "A") {                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		A		" . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();                        }                        if ($rowdns['dn_type_vc'] == "AAAA") {                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		AAAA		" . $rowdns['dn_target_vc'] . fs_filehandler::NewLine();                        }                        if ($rowdns['dn_type_vc'] == "CNAME") {                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		CNAME		" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();                        }                        if ($rowdns['dn_type_vc'] == "MX") {                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		MX		" . $rowdns['dn_priority_in'] . "	" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();                        }						if ($rowdns['dn_type_vc'] == "DKIM") {                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		TXT		\"( " . stripslashes($rowdns['dn_target_vc']) . " )\"" . fs_filehandler::NewLine();                        }                        if ($rowdns['dn_type_vc'] == "TXT") {                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		TXT		\"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();                        }                        if ($rowdns['dn_type_vc'] == "SRV") {                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		SRV		" . $rowdns['dn_priority_in'] . "	" . $rowdns['dn_weight_in'] . "	" . $rowdns['dn_port_in'] . "	" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();                        }                        if ($rowdns['dn_type_vc'] == "SPF") {                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		TXT		\"" . stripslashes($rowdns['dn_target_vc']) . "\"" . fs_filehandler::NewLine();                        }                        if ($rowdns['dn_type_vc'] == "NS") {                            $line .= $rowdns['dn_host_vc'] . "		" . $rowdns['dn_ttl_in'] . "		IN		NS		" . $rowdns['dn_target_vc'] . "." . fs_filehandler::NewLine();                        }                    }					echo "Updating zone record: " . $domain['dn_name_vc'] . fs_filehandler::NewLine();					fs_filehandler::UpdateFile($zone_file, 0777, $line);                }            }        }	  //}				    }	function WriteDNSNamedHook() {		global $zdbh;		$domains = array();	        //Get all the domain ID's we need and put them in an array.        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NULL";        if ($numrows = $zdbh->query($sql)) {            if ($numrows->fetchColumn() <> 0) {                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NULL GROUP BY dn_vhost_fk");                $sql->execute();                while ($rowdns = $sql->fetch()) {                    $domains[] = $rowdns['dn_name_vc'];                }            }        }		//Create named directory if it doesnt exists...		if (!is_dir(ctrl_options::GetOption('named_dir'))) {        	fs_director::CreateDirectory(ctrl_options::GetOption('named_dir'));			fs_director::SetFileSystemPermissions(ctrl_options::GetOption('named_dir'));        }		$named_file       = ctrl_options::GetOption('named_dir') . ctrl_options::GetOption('named_conf');    // Slave named file won't be created if the filename is blank...		$named_slave_file = ctrl_options::GetOption('named_conf_slave');		$named_slave_file = (fs_director::CheckForEmptyValue($named_slave_file)?'':ctrl_options::GetOption('named_dir').$named_slave_file);    if ($named_slave_file==$named_file)      $named_slave_file='';		echo "Updating " . $named_file . fs_filehandler::NewLine();        //Now we have all domain ID's, loop through them and find records for each zone file.		$line = "";		$slaveline = '';        foreach ($domains as $domain) {			echo "CHECKING ZONE FILE: " . ctrl_options::GetOption('zone_dir') . $domain . ".txt..." . fs_filehandler::NewLine();			system(ctrl_options::GetOption('named_checkzone') . " " . $domain . " " . ctrl_options::GetOption('zone_dir') . $domain . ".txt", $retval);			echo $retval . fs_filehandler::NewLine();			if ($retval == 0){				echo "Syntax check passed. Adding zone to " . ctrl_options::GetOption('named_conf') . fs_filehandler::NewLine();    		$line .= "zone \"" . $domain . "\" IN {" . fs_filehandler::NewLine();			$line .= "	type master;" . fs_filehandler::NewLine();			$line .= "	file \"" . ctrl_options::GetOption('zone_dir') . $domain . ".txt\";" . fs_filehandler::NewLine();			$line .= "	allow-transfer { " .ctrl_options::GetOption('allow_xfer') . "; };" . fs_filehandler::NewLine();			$line .= "};" . fs_filehandler::NewLine();        // Slave named configuration        if (!fs_director::CheckForEmptyValue($named_slave_file))        {          $slaveline .= "zone \"$domain\" IN {"   . fs_filehandler::NewLine()                     . '	type slave;'           . fs_filehandler::NewLine()                     . '	masters { ' . getHostByName(getHostName()) . '; };' . fs_filehandler::NewLine()                     . "	file \"/etc/zpanel/configs/bind/zones/\$domain.txt\";" . fs_filehandler::NewLine()                     . '};'                      . fs_filehandler::NewLine();        } //if (!fs_director::CheckForEmptyValue($named_slave_file))...			} else {				echo "Syntax ERROR. Skipping zone record." . fs_filehandler::NewLine();			}        }		fs_filehandler::UpdateFile($named_file, 0777, $line);    if (!fs_director::CheckForEmptyValue($named_slave_file))      fs_filehandler::UpdateFile($named_slave_file, 0777, $slaveline);    }	function ResetDNSRecordsUpatedHook() {		global $zdbh;	    $sql = $zdbh->prepare("UPDATE x_settings SET so_value_tx=NULL WHERE so_name_vc='dns_hasupdates'");        $sql->execute();		}	function PurgeOldZoneDNSRecordsHook() {		global $zdbh;		$domains = array();        $sql = "SELECT COUNT(*) FROM x_dns WHERE dn_deleted_ts IS NULL";        if ($numrows = $zdbh->query($sql)) {            if ($numrows->fetchColumn() <> 0) {                $sql = $zdbh->prepare("SELECT * FROM x_dns WHERE dn_deleted_ts IS NULL GROUP BY dn_name_vc");                $sql->execute();                while ($rowvhost = $sql->fetch()) {                    $domains[] = $rowvhost['dn_name_vc'];                }            }        }		$zonefiles = scandir(ctrl_options::GetOption('zone_dir'));		foreach ($zonefiles as $zonefile){			if (!in_array(substr($zonefile, 0 , -4), $domains) && $zonefile != "." && $zonefile != ".."){        		if (file_exists(ctrl_options::GetOption('zone_dir') . $zonefile)) {					echo "Purging old zone record from disk: " . substr($zonefile, 0, -4) . fs_filehandler::NewLine();					unlink(ctrl_options::GetOption('zone_dir') . $zonefile);				}			}		}	}	function ReloadBindHook(){		if (sys_versions::ShowOSPlatformVersion() == "Windows") {			$reload_bind = ctrl_options::GetOption('bind_dir') . "rndc.exe reload";		}else{			$reload_bind = ctrl_options::GetOption('zsudo') . " service " . ctrl_options::GetOption('bind_service') . " reload";		}		echo "Reloading BIND now..." . fs_filehandler::NewLine();		pclose(popen($reload_bind,'r'));	}  /*   * Send the slave configuration file to slave server.   *   The 'slave_connection_details' Option should have Newline delimited connection   *   details forall the slave servers.   *   Each line should state the username, password, protocol, ipaddress, port.   *      i.e.:  username=xxxx;password=yyyy:192.168.0.2:21:/path/to/slave/   *             username=xxxx;password=yyyy:192.168.0.3:20:/path/to/slave/   *             username=xxxx;password=yyyy:192.168.0.3:/path/to/slave/   */  function SendSlaveNamedConfigHook()  {    $connection_detail_set = ctrl_options::GetOption('slave_connection_details');    if (fs_director::CheckForEmptyValue($connection_detail_set))      return false;		$named_slave_file = ctrl_options::GetOption('named_conf_slave');		$named_slave_file = (fs_director::CheckForEmptyValue($named_slave_file)? '' : ctrl_options::GetOption('named_dir').$named_slave_file);    if ($named_slave_file=='')      return false;    $connection_detail_set = explode("\n", $connection_detail_set);    foreach ($connection_detail_set as $connection_detail)    {      $connection_detail = explode(':', $connection_detail);      if (count($connection_detail) < 3)      {        echo "Slave Connection details are invalid.\n";        return false;      } // if (count($connection_detail) < 3)      $connection_params = explode(';', $connection_detail[0]);      $slave_ip_address  = $connection_detail[1];      $slave_port        = (count($connection_detail) > 3 ? $connection_detail[2] : '');      $slave_config_path = $connection_detail[($slave_port=='' ? 2 : 3)];      $slave_config_path = rtrim($slave_config_path, '/') .'/'. ctrl_options::GetOption('named_conf_slave');      foreach($connection_params as $param)      {        $param = explode('=', $param);        switch ($param[0]) {          case 'username': $username = $param[1]; break;          case 'password': $password = $param[1]; break;          default: echo "Invalid configuration.\n"; break;        }//switch $param[0]      } //foreach($connection_params as $param)      if ($slave_port == '')        $slave_port = 21;      $connection = ftp_connect($slave_ip_address, $slave_port, 30);      if (!$connection)      {        echo "FTP connection has failed!.\n";        return false;      } //if (!$connection)      $login_result = ftp_login($connection, $username, $password);      ftp_pasv($connection, true);       if ((!$connection) || (!$login_result))      {        echo "Attempted to connect to $slave_ip_address:$slave_port for user $username and has failed.\n";        ftp_close($connection);        unset    ($connection);        return false;      } //if ((!$connection) || (!$login_result))      else        echo "Connected to $slave_ip_address, for user $username/\n";      $upload = ftp_put($connection, $slave_config_path, $named_slave_file, FTP_ASCII);       // check upload status      if (!$upload)        echo "FTP upload has failed!\n";      else        echo "Uploaded $slave_config_path to $slave_ip_address as $named_slave_file.\n";      ftp_close($connection);      unset    ($connection);    } //foreach ($connection_detail_set as $key => $connection_detail)  } //function SendSlaveNamedConfigHook()
}
?>