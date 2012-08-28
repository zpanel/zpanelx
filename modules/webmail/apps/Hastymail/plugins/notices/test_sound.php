<?php
global $include_path;
global $conf;
global $fd;
if ($include_path) {
    $include_path .= 'plugins'.$fd.'notices'.$fd;
}
$sounds = require_once($include_path.'sounds.php');
if (isset($_GET['sound_file']) && in_array($_GET['sound_file'], $sounds)) {
    $sound_file = $_GET['sound_file'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="application/xhtml-xml;charset=UTF-8" />
    <title id="title">Sound Test</title>
    <style type="text/css">
    #close_button{
        text-align: center;
        padding: 30px;
    }
    </style>
    <script type="text/javascript" src="script/soundmanager2-min.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            document.body.style.color = window.opener.get_notice_fg();
            document.body.style.backgroundColor = window.opener.get_notice_bg();
        }
        soundManager.debugMode = false;
        soundManager.url = "swf/";
        soundManager.onload = function() {
            var mySoundObject = soundManager.createSound({
                id: "mySound",
                autoPlay: true,
                autoLoad: true,
                url: "sounds/<?php echo $sound_file ?>"
            });
        }
    </script>
</head>
<body id="body">
    <div id="close_button"><input type="button" onclick="window.close();" value="Close" /></div>
</body>
</html>

<?php
}
?>
