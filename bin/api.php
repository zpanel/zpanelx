<?php

/**
 * The the main ZPanel(X) (M)odular (W)eb (S)ervice controller.
 * @package zpanelx
 * @subpackage core -> api
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
$raw_path = str_replace("\\", "/", dirname(__FILE__));
$root_path = str_replace("/bin", "/", $raw_path);
chdir($root_path);

require_once 'dryden/loader.inc.php';
require_once 'cnf/db.php';
require_once 'inc/dbc.inc.php';

debug_phperrors::SetMode('dev');

if (file_exists('modules/' . $_GET['m'] . '/code/webservice.ext.php')) {
    include 'modules/' . $_GET['m'] . '/code/controller.ext.php';
    include 'modules/' . $_GET['m'] . '/code/webservice.ext.php';
    $api = new webservice();

    if ($api->wsdataarray['request'] == '') {
        $response_nomethod = new runtime_dataobject;
        $response_nomethod->addItemValue('response', '1106');
        $response_nomethod->addItemValue('content', 'No \'request\' method was recieved');
        $api->SendResponse($response_nomethod->getDataObject());
        die();
    }

    if ($api->CheckServerAPIKey()) {
        if (method_exists($api, $api->wsdataarray['request'])) {
            $api->SendResponse(call_user_func(array($api, '' . $api->wsdataarray['request'] . '')));
        } else {
            $response_nomethod = new runtime_dataobject;
            $response_nomethod = new runtime_dataobject;
            $response_nomethod->addItemValue('response', '1102');
            $response_nomethod->addItemValue('content', 'Request not found');
            $api->SendResponse($response_nomethod->getDataObject());
        }
    } else {
        $response_nokey = new runtime_dataobject;
        $response_nokey->addItemValue('response', '1103');
        $response_nokey->addItemValue('content', 'Server API key authentication failed');
        $api->SendResponse($response_nokey->getDataObject());
    }
} else {
    echo "No modular web service found using this request URL (" . $_SERVER['REQUEST_URI'] . ")";
}
?>