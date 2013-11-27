<?php

/**
 * System command execution class.
 * @package zpanelx
 * @subpackage dryden -> ctrl
 * @version 1.1.0
 * @author Kevin Andrews (kandrews@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 * @Update 10-10-2013 and shrink Pascal Peyemorte
 */
class ctrl_system
{
    /**
     * Safely run an escaped system() command.
     * @param string $command The command of which to be executed.
     * @param array $args Any arguments seperated by a space should be in a seperate array value.
     * @param $args can also be a value
     * @return string
     */
    static function systemCommand($command, $args)
    {
        $escapedCommand = escapeshellcmd($command);
        if (is_array($args))
        { //$args is an array, treat separately each param
            foreach ($args as $arg)
            {
                $escapedCommand .= ' ' . escapeshellarg($arg);
            }
        }
        else
        { //$args is not an array. Assume it is compatible with string
            $escapedCommand .= ' ' . escapeshellarg($args);
        }
        system($escapedCommand, $systemReturnValue);
        return $systemReturnValue;
    }
}