<?php

/**
 * tree.php
 *
 * This file provides functions to walk trees of folders, for
 * instance to delete a whole tree.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: tree.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** Clearly, this needs the IMAP functions.. */
require_once(SM_PATH . 'functions/imap.php');

/**
 * Recursive function to find the correct parent for a new node.
 *
 * @param mixed value the value to find a parent for
 * @param int treeIndexToStart where to start the search, usually the root node (0)
 * @param array tree the tree to search
 * @return int the index of the parent
 */
function findParentForChild($value, $treeIndexToStart, $tree) {
    // is $value in $tree[$treeIndexToStart]['value']
    if ((isset($tree[$treeIndexToStart])) && (strstr($value, $tree[$treeIndexToStart]['value']))) {
        // do I have children, if not then must be a childnode of the current node
        if ($tree[$treeIndexToStart]['doIHaveChildren']) {
            // loop through each subNode checking to see if we are a subNode of one of them
            for ($i=0;$i< count($tree[$treeIndexToStart]['subNodes']);$i++) {
            $result = findParentForChild($value, $tree[$treeIndexToStart]['subNodes'][$i], $tree);
            if ($result > -1)
                return $result;
            }
            // if we aren't a child of one of the subNodes, must be a child of current node
            return $treeIndexToStart;
        } else
            return $treeIndexToStart;
    } else {
        // we aren't a child of this node at all
        return -1;
    }
}

/**
 * Will insert a new value into the tree, based on a given comparison value.
 *
 * @param mixed comparisonValue the value to determine where the new element should be placed.
 * @param mixed value the new node to insert
 * @param array tree the tree to insert the node in, by ref
 */
function addChildNodeToTree($comparisonValue, $value, &$tree) {
    $parentNode = findParentForChild($comparisonValue, 0, $tree);

    // create a new subNode
    $newNodeIndex = count($tree);
    $tree[$newNodeIndex]['value'] = $value;
    $tree[$newNodeIndex]['doIHaveChildren'] = false;

    if ($tree[$parentNode]['doIHaveChildren'] == false) {
        // make sure the parent knows it has children
        $tree[$parentNode]['subNodes'][0] = $newNodeIndex;
        $tree[$parentNode]['doIHaveChildren'] = true;
    } else {
        $nextSubNode = count($tree[$parentNode]['subNodes']);
        // make sure the parent knows it has children
        $tree[$parentNode]['subNodes'][$nextSubNode] = $newNodeIndex;
    }
}

/**
 * Recursively walk the tree of trash mailboxes and delete all folders and messages
 *
 * @param int index the place in the tree to start, usually 0
 * @param stream imap_stream the IMAP connection to send commands to
 * @param array tree the tree to walk
 * @return void
 */
function walkTreeInPreOrderEmptyTrash($index, $imap_stream, $tree) {
    global $trash_folder;
    if ($tree[$index]['doIHaveChildren']) {
        for ($j = 0; $j < count($tree[$index]['subNodes']); $j++) {
            walkTreeInPreOrderEmptyTrash($tree[$index]['subNodes'][$j], $imap_stream, $tree);
        }
        if ($tree[$index]['value'] != $trash_folder) {
            sqimap_mailbox_delete($imap_stream, $tree[$index]['value']);
        } else {
            $mbx_response = sqimap_mailbox_select($imap_stream, $trash_folder);
            if ($mbx_response['EXISTS'] > 0) {
               sqimap_messages_flag ($imap_stream, 1, '*', 'Deleted', true);
               // CLOSE === EXPUNGE and UNSELECT
               sqimap_run_command($imap_stream,'CLOSE',false,$response,$message);
            }
        }
    } else {
        if ($tree[$index]['value'] != $trash_folder) {
            sqimap_mailbox_delete($imap_stream, $tree[$index]['value']);
        } else {
            $mbx_response = sqimap_mailbox_select($imap_stream, $trash_folder);
            if ($mbx_response['EXISTS'] > 0) {
                sqimap_messages_flag ($imap_stream, 1, '*', 'Deleted', true);
                // CLOSE === EXPUNGE and UNSELECT
                sqimap_run_command($imap_stream,'CLOSE',false,$response,$message);
            }
        }
    }
}


/**
 * Recursively delete a tree of mail folders.
 *
 * @param int index the place in the tree to start, usually 0
 * @param stream imap_stream the IMAP connection to send commands to
 * @param array tree the tree to walk
 * @return void
 */
function walkTreeInPreOrderDeleteFolders($index, $imap_stream, $tree) {
    if ($tree[$index]['doIHaveChildren']) {
        for ($j = 0; $j < count($tree[$index]['subNodes']); $j++) {
            walkTreeInPreOrderDeleteFolders($tree[$index]['subNodes'][$j], $imap_stream, $tree);
        }
        sqimap_mailbox_delete($imap_stream, $tree[$index]['value']);
    } else {
        sqimap_mailbox_delete($imap_stream, $tree[$index]['value']);
    }
}

/**
 * Recursively walk a tree of folders to create them under the trash folder.
 */
function walkTreeInPostOrderCreatingFoldersUnderTrash($index, $imap_stream, $tree, $topFolderName) {
    global $trash_folder, $delimiter;

    $position = strrpos($topFolderName, $delimiter);
    if ($position !== FALSE) {
        $position++;
    }
    $subFolderName = substr($tree[$index]['value'], $position);

    if ($tree[$index]['doIHaveChildren']) {
        sqimap_mailbox_create($imap_stream, $trash_folder . $delimiter . $subFolderName, "");
        $mbx_response = sqimap_mailbox_select($imap_stream, $tree[$index]['value']);
        $messageCount = $mbx_response['EXISTS'];
        if ($messageCount > 0) {
            sqimap_msgs_list_copy($imap_stream, '1:*', $trash_folder . $delimiter . $subFolderName);
        }
        // after copy close the mailbox to get in unselected state
        sqimap_run_command($imap_stream,'CLOSE',false,$response,$message);
        for ($j = 0;$j < count($tree[$index]['subNodes']); $j++)
            walkTreeInPostOrderCreatingFoldersUnderTrash($tree[$index]['subNodes'][$j], $imap_stream, $tree, $topFolderName);
    } else {
        sqimap_mailbox_create($imap_stream, $trash_folder . $delimiter . $subFolderName, '');
        $mbx_response = sqimap_mailbox_select($imap_stream, $tree[$index]['value']);
        $messageCount = $mbx_response['EXISTS'];
        if ($messageCount > 0) {
            sqimap_msgs_list_copy($imap_stream, '1:*', $trash_folder . $delimiter . $subFolderName);
        }
        // after copy close the mailbox to get in unselected state
        sqimap_run_command($imap_stream,'CLOSE',false,$response,$message);
    }
}

/**
 * Recursive function that outputs a tree In-Pre-Order.
 * @param int index the node to start (usually 0)
 * @param array tree the tree to walk
 * @return void
 */
function simpleWalkTreePre($index, $tree) {
    if ($tree[$index]['doIHaveChildren']) {
        for ($j = 0; $j < count($tree[$index]['subNodes']); $j++) {
            simpleWalkTreePre($tree[$index]['subNodes'][$j], $tree);
        }
        echo $tree[$index]['value'] . '<br />';
    } else {
        echo $tree[$index]['value'] . '<br />';
    }
}
