<?php

/**
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 * 
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@zpanelcp.com
 * @copyright (c) 2008-2011 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 *
 * This program (ZPanel) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class module_controller {

    static $shout;

    static function getShadowAccounts() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT * FROM x_accounts WHERE ac_reseller_fk = '" . $currentuser['userid'] . "' AND ac_deleted_ts IS NULL ORDER BY ac_user_vc";
        $numrows = $zdbh->query($sql);
        if ($numrows->fetchColumn() <> 0) {
            $sql = $zdbh->prepare($sql);
            $res = array();
            $sql->execute();
            while ($rowclients = $sql->fetch()) {
                if ($rowclients['ac_id_pk'] != $currentuser['userid']) {
                    $clientdetail = ctrl_users::GetUserDetail($rowclients['ac_id_pk']);
                    array_push($res, array('clientusername' => $clientdetail['username'],
                        'clientid' => $rowclients['ac_id_pk'],
                        'packagename' => $clientdetail['packagename'],
                        'usergroup' => $clientdetail['usergroup'],
                        'currentdisk' => fs_director::ShowHumanFileSize(ctrl_users::GetQuotaUsages('diskspace', $rowclients['ac_id_pk'])),
                        'currentbandwidth' => fs_director::ShowHumanFileSize(ctrl_users::GetQuotaUsages('bandwidth', $rowclients['ac_id_pk']))));
                }
            }
            return $res;
        } else {
            return false;
        }
    }

    static function doShadowUser() {
        global $zdbh;
        global $controller;
        $currentuser = ctrl_users::GetUserDetail();
        $sql = "SELECT COUNT(*) FROM x_accounts WHERE ac_reseller_fk = '" . $currentuser['userid'] . "' AND ac_deleted_ts IS NULL";
        if ($numrows = $zdbh->query($sql)) {
            if ($numrows->fetchColumn() <> 0) {
                $sql = $zdbh->prepare("SELECT * FROM x_accounts WHERE ac_reseller_fk = '" . $currentuser['userid'] . "' AND ac_deleted_ts IS NULL");
                $sql->execute();
                while ($rowclients = $sql->fetch()) {
                    if (!fs_director::CheckForEmptyValue($controller->GetControllerRequest('FORM', 'inShadow_' . $rowclients['ac_id_pk']))) {
                        ctrl_auth::SetSession('ruid', $currentuser['userid']);
                        ctrl_auth::SetUserSession($rowclients['ac_id_pk']);
                        header("location: /");
                        exit;
                    }
                }
            }
        }
    }

    static function getModuleName() {
        $module_name = ui_module::GetModuleName();
        return $module_name;
    }

    static function getModuleIcon() {
        global $controller;
        $module_icon = "modules/" . $controller->GetControllerRequest('URL', 'module') . "/assets/icon.png";
        return $module_icon;
    }

    static function getModuleDesc() {
        $message = ui_language::translate(ui_module::GetModuleDescription());
        return $message;
    }

}

?>