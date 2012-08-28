<?php
if (file_exists("C:/zpanel/panel/index.php")) {
$site_config_file = false;
    $atts = "C:/zpanel/panel/modules/webmail/apps/Hastymail/hastymail2.conf";
            $site_config_file = $atts;
    $conf = array();
    if (is_readable($site_config_file)) {
        $lines = file($site_config_file);
        foreach ($lines as $line) {
            if (!trim($line)) {
                continue;
            }
            elseif (substr(trim($line), 0, 1) == '#') {
                continue;
            }
            else {
                $name = false;
                $value = false;
                $parts = explode('=', $line, 2);
                if (isset($parts[0]) && trim($parts[0])) {
                    $name = trim(strtolower($parts[0]));
                }
                if (isset($parts[1]) && trim($parts[1])) {
                    $value = trim($parts[1]);
                }
                if ($name == 'theme') {
                    $val_bits = explode(',', $value);
                    $theme = false;
                    $css = false;
                    $icons = false;
                    $templates = false;
                    if (isset($val_bits[0])) {
                        $theme = $val_bits[0];
                    }
                    if (isset($val_bits[1]) && $val_bits[1] == 'true') {
                        $css = true;
                    }
                    if (isset($val_bits[2]) && $val_bits[2] == 'true') {
                        $icons = true;
                    }
                    elseif (isset($val_bits[2]) && $val_bits[2] == 'default') {
                        $icons = 'default';
                    }
                    if (isset($val_bits[3]) && $val_bits[3] == 'true') {
                        $templates = true;
                    }
                    if ($theme) {
                        $conf['site_themes'][$theme] = array('icons' => $icons, 'templates' => $templates, 'css' => $css);
                    }
                }
                elseif ($name == 'plugin') {
                    $conf['plugins'][] = $value;
                }
                elseif (substr($name, 0, 7) == 'default') {
                    if ($name == 'default_folder_check') {
                        $conf['user_defaults']['folder_check'][] = $value;
                    }
                    else {
                        if (strtolower($value) == 'true') {
                            $value = true;
                        }
                        elseif (strtolower($value) == 'false') {
                            $value = false;
                        }
                        $conf['user_defaults'][substr($name, 8)] = $value;
                    }
                }
                elseif ($name) {
                    if (strtolower($value) == 'true') {
                        $value = true;
                    }
                    elseif (strtolower($value) == 'false') {
                        $value = false;
                    }
                    $conf[$name] = $value;
                }
            }
        }
    }
    else {
        echo "input file was Unreadable\n\n";
    }
    if (!empty($conf)) {
        $data = serialize($conf);
			$hastymail_config_file2 = fopen("C:/panel/modules/webmail/apps/Hastymail/hastymail2.rc", "w");
fwrite($hastymail_config_file2, "".$data."");
fclose($hastymail_config_file2);
    }
} else {
$site_config_file = false;
    $atts = "/etc/zpanel/panel/modules/webmail/apps/Hastymail/hastymail2.conf";
            $site_config_file = $atts;
    $conf = array();
    if (is_readable($site_config_file)) {
        $lines = file($site_config_file);
        foreach ($lines as $line) {
            if (!trim($line)) {
                continue;
            }
            elseif (substr(trim($line), 0, 1) == '#') {
                continue;
            }
            else {
                $name = false;
                $value = false;
                $parts = explode('=', $line, 2);
                if (isset($parts[0]) && trim($parts[0])) {
                    $name = trim(strtolower($parts[0]));
                }
                if (isset($parts[1]) && trim($parts[1])) {
                    $value = trim($parts[1]);
                }
                if ($name == 'theme') {
                    $val_bits = explode(',', $value);
                    $theme = false;
                    $css = false;
                    $icons = false;
                    $templates = false;
                    if (isset($val_bits[0])) {
                        $theme = $val_bits[0];
                    }
                    if (isset($val_bits[1]) && $val_bits[1] == 'true') {
                        $css = true;
                    }
                    if (isset($val_bits[2]) && $val_bits[2] == 'true') {
                        $icons = true;
                    }
                    elseif (isset($val_bits[2]) && $val_bits[2] == 'default') {
                        $icons = 'default';
                    }
                    if (isset($val_bits[3]) && $val_bits[3] == 'true') {
                        $templates = true;
                    }
                    if ($theme) {
                        $conf['site_themes'][$theme] = array('icons' => $icons, 'templates' => $templates, 'css' => $css);
                    }
                }
                elseif ($name == 'plugin') {
                    $conf['plugins'][] = $value;
                }
                elseif (substr($name, 0, 7) == 'default') {
                    if ($name == 'default_folder_check') {
                        $conf['user_defaults']['folder_check'][] = $value;
                    }
                    else {
                        if (strtolower($value) == 'true') {
                            $value = true;
                        }
                        elseif (strtolower($value) == 'false') {
                            $value = false;
                        }
                        $conf['user_defaults'][substr($name, 8)] = $value;
                    }
                }
                elseif ($name) {
                    if (strtolower($value) == 'true') {
                        $value = true;
                    }
                    elseif (strtolower($value) == 'false') {
                        $value = false;
                    }
                    $conf[$name] = $value;
                }
            }
        }
    }
    else {
        echo "input file was Unreadable\n\n";
    }
    if (!empty($conf)) {
        $data = serialize($conf);
			$hastymail_config_file2 = fopen("/etc/zpanel/panel/modules/webmail/apps/Hastymail/hastymail2.rc", "w");
fwrite($hastymail_config_file2, "".$data."");
fclose($hastymail_config_file2);
    }
}

?>