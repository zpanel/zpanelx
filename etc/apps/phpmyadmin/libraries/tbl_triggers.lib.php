<?php

/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 *
 * @package phpMyAdmin
 */
if (!defined('PHPMYADMIN')) {
    exit;
}

$url_query .= '&amp;goto=tbl_structure.php';

$triggers = PMA_DBI_get_triggers($db, $table);

if ($triggers) {
    echo '<div id="tabletriggers">' . "\n";
    echo '<table class="data">' . "\n";
    echo ' <caption class="tblHeaders">' . __('Triggers') . '</caption>' . "\n";
    echo sprintf('<tr>
                          <th>%s</th>
                          <th>&nbsp;</th>
                          <th>&nbsp;</th>
                          <th>%s</th>
                          <th>%s</th>
                    </tr>', __('Name'), __('Time'), __('Event'));
    $ct = 0;
    $delimiter = '//';
    if ($GLOBALS['cfg']['AjaxEnable']) {
        $conditional_class = 'class="drop_trigger_anchor"';
    } else {
        $conditional_class = '';
    }

    foreach ($triggers as $trigger) {
        $drop_and_create = $trigger['drop'] . $delimiter . "\n" . $trigger['create'] . "\n";

        echo sprintf('<tr class="noclick %s">
                              <td><strong>%s</strong></td>
                              <td>%s</td>
                              <td>%s</td>
                              <td>%s</td>
                              <td>%s</td>
                         </tr>', ($ct % 2 == 0) ? 'even' : 'odd', $trigger['name'], PMA_linkOrButton('tbl_sql.php?' . $url_query . '&amp;sql_query=' . urlencode($drop_and_create) . '&amp;show_query=1&amp;delimiter=' . urlencode($delimiter), $titles['Change']), '<a ' . $conditional_class . ' href="sql.php?' . $url_query . '&sql_query=' . urlencode($trigger['drop']) . '" >' . $titles['Drop'] . '</a>', $trigger['action_timing'], $trigger['event_manipulation']);
        $ct++;
    }
    echo '</table>';
    echo '</div>' . "\n";
}
?>
