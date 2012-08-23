<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Code for displaying server selection
 *
 * @package PhpMyAdmin
 */

/**
 * display server selection in list or selectbox form, or option tags only
 *
 * @param boolean $not_only_options   whether to include form tags or not
 * @param boolean $ommit_fieldset     whether to ommit fieldset tag or not
 */
function PMA_select_server($not_only_options, $ommit_fieldset)
{
    // Show as list?
    if ($not_only_options) {
        $list = $GLOBALS['cfg']['DisplayServersList'];
        $not_only_options =! $list;
    } else {
        $list = false;
    }

    if ($not_only_options) {
        echo '<form method="post" action="index.php" target="_parent">';
        echo PMA_generate_common_hidden_inputs();

        if (! $ommit_fieldset) {
            echo '<fieldset>';
        }
        echo '<label for="select_server">' . __('Current Server') . ':</label> ';

        echo '<select name="server" id="select_server" class="autosubmit">';
        echo '<option value="">(' . __('Servers') . ') ...</option>' . "\n";
    } elseif ($list) {
        echo __('Current Server') . ':<br />';
        echo '<ul id="list_server">';
    }

    foreach ($GLOBALS['cfg']['Servers'] as $key => $server) {
        if (empty($server['host'])) {
            continue;
        }

        if (!empty($GLOBALS['server']) && (int) $GLOBALS['server'] === (int) $key) {
            $selected = 1;
        } else {
            $selected = 0;
        }
        if (!empty($server['verbose'])) {
            $label = $server['verbose'];
        } else {
            $label = $server['host'];
            if (!empty($server['port'])) {
                $label .= ':' . $server['port'];
            }
        }
        if (! empty($server['only_db'])) {
            if (! is_array($server['only_db'])) {
                $label .= ' - ' . $server['only_db'];
            // try to avoid displaying a too wide selector
            } elseif (count($server['only_db']) < 4) {
                $label .= ' - ' . implode(', ', $server['only_db']);
            }
        }
        if (!empty($server['user']) && $server['auth_type'] == 'config') {
            $label .= '  (' . $server['user'] . ')';
        }

        if ($list) {
            echo '<li>';
            if ($selected) {
                echo '<strong>' . htmlspecialchars($label) . '</strong>';
            } else {

                echo '<a class="item" href="index.php'
                    . PMA_generate_common_url(array('server' => $key))
                    . '" target="_top">' . htmlspecialchars($label) . '</a>';
            }
            echo '</li>';
        } else {
            echo '<option value="' . $key . '" '
                . ($selected ? ' selected="selected"' : '') . '>'
                . htmlspecialchars($label) . '</option>' . "\n";
        }
    } // end while

    if ($not_only_options) {
        echo '</select>';
        // Show submit button if we have just one server (this happens with no default)
        echo '<noscript>';
        echo '<input type="submit" value="' . __('Go') . '" />';
        echo '</noscript>';
        if (! $ommit_fieldset) {
            echo '</fieldset>';
        }
        echo '</form>';
    } elseif ($list) {
        echo '</ul>';
    }
}
?>
