<?php

/**
 * @package zpanelx
 * @subpackage modules
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class webservice extends ws_xmws {

    /**
     * Resets a user's ZPanel account password. Requires <uid> and <newpassword> tags.
     * @return type 
     */
    function ResetUserPassword() {
        $contenttags = $this->XMLDataToArray($this->wsdata);
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        if (module_controller::UpdatePassword($contenttags['xmws']['content']['uid'], $contenttags['xmws']['content']['newpassword'])) {
            $dataobject->addItemValue('content', ws_xmws::NewXMLTag('uid', $contenttags['xmws']['content']['uid']) . ws_xmws::NewXMLTag('reset', 'true'));
        } else {
            $dataobject->addItemValue('content', ws_xmws::NewXMLTag('uid', $contenttags['xmws']['content']['uid']) . ws_xmws::NewXMLTag('reset', 'false'));
        }
        return $dataobject->getDataObject();
    }

}

?>
