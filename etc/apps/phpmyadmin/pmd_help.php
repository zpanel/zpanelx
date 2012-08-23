<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 *
 * @package phpMyAdmin-Designer
 */
/**
 *
 */
require_once 'pmd_common.php';
?>
<html>
    <head>
        <?php if (0) { ?>
            <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
            <link rel="stylesheet" type="text/css" href="./libraries/pmd/styles/default/style1.css">
        <?php } ?>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>" />
        <link rel="stylesheet" type="text/css" href="./libraries/pmd/styles/<?php echo $GLOBALS['PMD']['STYLE'] ?>/style1.css">
        <title>Designer</title>
    </head>

    <body>
        <?php
        echo '<p>' . __('To select relation, click :') . '<br />';
        echo '<img src="pmd/images/help_relation.png" border="1"></p>';
        echo '<p>' . __('The display column is shown in pink. To set/unset a column as the display column, click the "Choose column to display" icon, then click on the appropriate column name.') . '</p>';
        ?>
    </body>
</html>
