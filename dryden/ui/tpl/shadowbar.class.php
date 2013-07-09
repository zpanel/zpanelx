<?php

/**
 * Generic template place holder class.
 * @package zpanelx
 * @subpackage dryden -> ui -> tpl
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_tpl_shadowbar {

    public static function Template() {
        if (isset($_SESSION['ruid'])) {
            return "<div class=\"zshadowbar\" id=\"zshadowbar\"><a href=\"./?returnsession=true\" class=\"zshadowbar\" id=\"zshadowbar_a\" border=\"0\"></a><a href=\"./?returnsession=true\">" . "<: End shadow session and return to your session. :>" . "</a></div>";
        } else {
            return false;
        }
    }

}

?>
