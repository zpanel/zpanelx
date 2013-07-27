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

    static function getModuleName() {
        $module_name = ui_module::GetModuleName();
        return $module_name;
    }

    static function getLaunchWebMail() {
        $message = ui_language::translate("Launch Webmail");
        return $message;
    }

    static function getModuleDesc() {
        $message = ui_language::translate("Webmail is a convenient way for you to check your email accounts online without the need to configure an email client.");
        return $message;
    }

    static function getModuleIcon() {
        global $controller;
        $mod_folder = $controller->GetControllerRequest('URL', 'module');
        // Check is Userland Theme has a Module Icon Override
        if (file_exists('etc/styles/' . ui_template::GetUserTemplate() . '/images/'.$mod_folder.'/assets/icon.png')) {
            $module_icon = 'etc/styles/' . ui_template::GetUserTemplate() . '/images/'.$mod_folder.'/assets/icon.png';
        } else {
            $module_icon = 'modules/' . $mod_folder . '/assets/icon.png';
        }
        return $module_icon;
    }

}

?>