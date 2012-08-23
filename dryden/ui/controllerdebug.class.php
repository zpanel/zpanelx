<?php

/**
 * Class to display controller debugging in the template layer.
 * @package zpanelx
 * @subpackage dryden -> ui
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class ui_controllerdebug extends runtime_controller {

    /**
     * Template placeholder to display controller debug infomation.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @global obj $controller The controller object.
     * @return string HTML output to display the controller debug infomation in a pretty way
     */
    function Template() {
        global $controller;
        if ($controller->OutputControllerDebug()) {
            $controllerdebug = $controller->OutputControllerDebug();
            $retval = "<!-- BEGIN DEBUG -->
	<div class=\"zdebug\" id=\"zdebug\">" . $controllerdebug . "</div>
	<!-- END DEBUG -->";
            return $retval;
        }
    }

}

?>
