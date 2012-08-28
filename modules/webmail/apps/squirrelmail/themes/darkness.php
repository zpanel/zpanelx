<?php

/**
 * Theme Name:   'Darkness'
 * Like black?
 *
 * @author Tyler Akins
 * @copyright 2001-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: darkness.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage themes
 */

/**
 * Load up the usual suspects.. */
require_once(SM_PATH . 'functions/strings.php');

   // Note:  The text distance is actually pre-squared
   // Background range is from 24-64, all three colors are the same
   // Text range is from 196 to 255
   $BackgroundTargetDistance = 12;
   $BackgroundAdjust = 1;
   $TextTargetDistance = 65536;
   $TextAdjust = 0.95;

function IsUnique($Distance, $r, $g, $b, $usedArray)
{
   foreach ($usedArray as $data) {
      $a = abs($data[0] - $r);
      $b = abs($data[1] - $g);
      $c = abs($data[2] - $b);
      $newDistance = $a * $a + $b * $b + $c * $c;
      if ($newDistance < $Distance)
         return false;
   }
   return true;
}


// Extra spiffy page fade if left frame
// Always tremble background
// This might make people go insane.  Yes!  *Victory dance!*
function Darkness_HeaderPlugin() {
   if (defined('PAGE_NAME') && PAGE_NAME=='left_main') {
      echo '<meta http-equiv="Page-Enter" content="' .
         'blendTrans(Duration=2.0)" />' . "\n";
   }

?><script type="text/javascript">
darkness_color = 0;
darkness_dir = +1;
darkness_hex = new Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
   'a', 'b', 'c', 'd', 'e', 'f');
function DarknessTremble() {
   if (darkness_color >= 32 || darkness_color <= 0)
      darkness_dir = - darkness_dir;
   darkness_color += darkness_dir;
   if (darkness_color < 0)
      darkness_color = 0;
   bigDigit = Math.floor(darkness_color / 16);
   littleDigit = darkness_color - (bigDigit * 16);
   Color = darkness_hex[bigDigit] + darkness_hex[littleDigit];
   document.bgColor='#' + Color + Color + Color;
   setTimeout('DarknessTremble()', 5000);
}
setTimeout('DarknessTremble()', 10000);
</script>
<?php
}

global $squirrelmail_plugin_hooks;
$squirrelmail_plugin_hooks['generic_header']['theme_darkness'] =
    'Darkness_HeaderPlugin';

/** seed the random number generator **/
sq_mt_randomize();

$color[3] = '#000000';
$color[4] = '#000000';
$used = array(0);
$targetDistance = $BackgroundTargetDistance;
$Left = array(0, 5, 9, 10, 12);
while (count($Left) > 0) {
    // Some background colors
    $r = mt_rand(24,64);
    $unique = true;
    foreach ($used as $col) {
        if (abs($r - $col) < $targetDistance)
            $unique = false;
    }
    if ($unique) {
        $i = array_shift($Left);
        $color[$i] = sprintf('#%02X%02X%02X',$r,$r, $r);
        $used[] = $r;
        $targetDistance = $BackgroundTargetDistance;
    } else {
        $targetDistance -= $BackgroundAdjust;
    }
}

// Set the error color to some shade of red
$r = mt_rand(196, 255);
$g = mt_rand(144, ($r * .8));
$color[2] = sprintf('#%02X%02X%02X', $r, $g, $g);
$used = array(array($r, $g, $g));

// Set normal text colors
$cmin = 196;
$cmax = 255;
foreach (array(6, 8) as $i) {
    /** generate random color **/
    $r = mt_rand($cmin,$cmax);
    $g = mt_rand($cmin,$cmax);
    $b = mt_rand($cmin,$cmax);
    $color[$i] = sprintf('#%02X%02X%02X',$r,$g,$b);
    $used[] = array($r, $g, $b);
}

$Left = array(1, 7, 11, 13, 14, 15);
$targetDistance = $TextTargetDistance;
while (count($Left) > 0) {
    // Text colors -- Try to keep the colors distinct
    $cmin = 196;
    $cmax = 255;

    /** generate random color **/
    $r = mt_rand($cmin,$cmax);
    $g = mt_rand($cmin,$cmax);
    $b = mt_rand($cmin,$cmax);

    if (IsUnique($targetDistance, $r, $g, $b, $used)) {
        $i = array_shift($Left);
        $color[$i] = sprintf('#%02X%02X%02X',$r,$g,$b);
        $used[] = array($r, $g, $b);
        $targetDistance = $TextTargetDistance;
    } else {
        $targetDistance *= $TextAdjust;
    }
}
