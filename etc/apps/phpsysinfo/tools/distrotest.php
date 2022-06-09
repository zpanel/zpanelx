<?php
header('Content-Type: text/plain');
$filemaskarray=array("/etc/*-release",
                     "/etc/*_release",
                     "/etc/*-version",
                     "/etc/*_version",
                     "/etc/version",
                     "/etc/release",
                     "/etc/DISTRO_SPECS",
                     "/etc/eisfair-system",
                     "/usr/share/doc/tc/release.txt",
                     "/etc/synoinfo.conf",
                     "/etc/config/uLinux.conf",
                     "/etc/salix-update-notifier.conf",
                     "/etc/solydxk/info",
                     "/etc/vortexbox/vortexbox-version",
                     "/etc/GoboLinuxVersion",
                     "/etc/VERSION");
$fp = popen("lsb_release -a 2>/dev/null", "r");
if (is_resource($fp)) {
    $contents="";
    $start=true;
    while (!feof($fp)) {
        $contents=fgets($fp, 4096);
        if ($start && (strlen($contents)>0)) {
            echo "----------lsb_release -a----------\n";
            $start=false;
        }
        echo $contents;
    }
    if ((strlen($contents)>0)&&(substr($contents, -1)!="\n")) {
        echo "\n";
    }
    pclose($fp);
}

foreach ($filemaskarray as $filemask) {
    $filenames = glob($filemask);
    if (is_array($filenames)) foreach ($filenames as $filename) {
        echo "----------".$filename."----------\n";
        echo $contents=file_get_contents($filename);
        if ((strlen($contents)>0)&&(substr($contents, -1)!="\n")) {
            echo "\n";
        }
    }
}
