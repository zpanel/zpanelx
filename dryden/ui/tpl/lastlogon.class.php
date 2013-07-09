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
class ui_tpl_lastlogon {

    public static function Template() {
        $currentuser = ctrl_users::GetUserDetail(ctrl_auth::CurrentUserID());
        if ($currentuser['lastlogon']) {
            return date(ctrl_options::GetSystemOption('zpanel_df'), $currentuser['lastlogon']);
        } else {
            return "<: Never :>";
        }
    }

}

?>