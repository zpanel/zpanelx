<?php

/**
 * Archive
 *
 * Plugin that adds a new button to the mailbox toolbar
 * to move messages to a (user selectable) archive folder.
 *
 * @version @package_version@
 * @author Andre Rodier, Thomas Bruederli
 */
class archive extends rcube_plugin {

    public $task = 'mail|settings';

    function init() {
        $rcmail = rcmail::get_instance();

        // There is no "Archived flags"
        // $GLOBALS['IMAP_FLAGS']['ARCHIVED'] = 'Archive';
        if ($rcmail->task == 'mail' && ($rcmail->action == '' || $rcmail->action == 'show')
                && ($archive_folder = $rcmail->config->get('archive_mbox'))) {
            $skin_path = $this->local_skin_path();

            $this->include_script('archive.js');
            $this->add_texts('localization', true);
            $this->add_button(
                    array(
                'command' => 'plugin.archive',
                'imagepas' => $skin_path . '/archive_pas.png',
                'imageact' => $skin_path . '/archive_act.png',
                'width' => 32,
                'height' => 32,
                'title' => 'buttontitle',
                'domain' => $this->ID,
                    ), 'toolbar');

            // register hook to localize the archive folder
            $this->add_hook('render_mailboxlist', array($this, 'render_mailboxlist'));

            // set env variable for client
            $rcmail->output->set_env('archive_folder', $archive_folder);
            $rcmail->output->set_env('archive_folder_icon', $this->url($skin_path . '/foldericon.png'));

            // add archive folder to the list of default mailboxes
            if (($default_folders = $rcmail->config->get('default_imap_folders')) && !in_array($archive_folder, $default_folders)) {
                $default_folders[] = $archive_folder;
                $rcmail->config->set('default_imap_folders', $default_folders);
            }
        } else if ($rcmail->task == 'settings') {
            $dont_override = $rcmail->config->get('dont_override', array());
            if (!in_array('archive_mbox', $dont_override)) {
                $this->add_hook('preferences_list', array($this, 'prefs_table'));
                $this->add_hook('preferences_save', array($this, 'save_prefs'));
            }
        }
    }

    function render_mailboxlist($p) {
        $rcmail = rcmail::get_instance();
        $archive_folder = $rcmail->config->get('archive_mbox');

        // set localized name for the configured archive folder
        if ($archive_folder) {
            if (isset($p['list'][$archive_folder]))
                $p['list'][$archive_folder]['name'] = $this->gettext('archivefolder');
            else // search in subfolders
                $this->_mod_folder_name($p['list'], $archive_folder, $this->gettext('archivefolder'));
        }

        return $p;
    }

    function _mod_folder_name(&$list, $folder, $new_name) {
        foreach ($list as $idx => $item) {
            if ($item['id'] == $folder) {
                $list[$idx]['name'] = $new_name;
                return true;
            } else if (!empty($item['folders']))
                if ($this->_mod_folder_name($list[$idx]['folders'], $folder, $new_name))
                    return true;
        }
        return false;
    }

    function prefs_table($args) {
        global $CURR_SECTION;

        if ($args['section'] == 'folders') {
            $this->add_texts('localization');

            $rcmail = rcmail::get_instance();

            // load folders list when needed
            if ($CURR_SECTION)
                $select = rcmail_mailbox_select(array('noselection' => '---', 'realnames' => true,
                    'maxlength' => 30, 'exceptions' => array('INBOX'), 'folder_filter' => 'mail', 'folder_rights' => 'w'));
            else
                $select = new html_select();

            $args['blocks']['main']['options']['archive_mbox'] = array(
                'title' => $this->gettext('archivefolder'),
                'content' => $select->show($rcmail->config->get('archive_mbox'), array('name' => "_archive_mbox"))
            );
        }

        return $args;
    }

    function save_prefs($args) {
        if ($args['section'] == 'folders') {
            $args['prefs']['archive_mbox'] = get_input_value('_archive_mbox', RCUBE_INPUT_POST);
            return $args;
        }
    }

}
