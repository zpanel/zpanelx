<?php

/**
 * @package zpanelx
 * @subpackage modules
 * @author Bobby Allen (porya_ras@the4.ir)
 * @copyright ZPanel Project (http://www.the4.ir/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class webservice extends ws_xmws
{
    /**
     * Get the full list of packages
     * @global type $zdbh
     * @return type
     */
    public function GetAllPackages() {
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        $allpackages=array();
        $response_xml = "\n";
        if(!is_null($contenttags['uid'])) {

            $allpackages=module_controller::ListPackages($contenttags['uid']);
        }
        else
        {
            $allpackages=module_controller::ListPackages(1);
        }
        foreach ($allpackages as $package) {
            $response_xml = $response_xml . ws_xmws::NewXMLContentSection('packages', array(
                    'id' => $package['packageid'],
                    'pakage' => $package['packagename']
                ));
        }
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();


    }
    public function GetPackageId(){
        $request_data = $this->RawXMWSToArray($this->wsdata);
        $contenttags = $this->XMLDataToArray($request_data['content']);
        $packageId=0;
        $response_xml = "\n";
        $allpackages=module_controller::ListPackages(1);
        foreach ($allpackages as $package) {
           if($package['packagename']===$contenttags['pakagename'])
           {
               $packageId=$package['packageid'];
           }
        }
        $response_xml = $response_xml . ws_xmws::NewXMLContentSection('pakageid',$packageId );
        $dataobject = new runtime_dataobject();
        $dataobject->addItemValue('response', '');
        $dataobject->addItemValue('content', $response_xml);
        return $dataobject->getDataObject();

    }

}
