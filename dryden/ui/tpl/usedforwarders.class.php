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
class ui_tpl_usedforwarders {

    public function Template() {
        $currentuser = ctrl_users::GetUserDetail();
        $forwarders = ctrl_users::GetQuotaUsages('forwarders', $currentuser['userid']);
        if ($forwarders <> 0) {
            return (string) $forwarders;
        }
        return (string) 0;
    }

}

?>
