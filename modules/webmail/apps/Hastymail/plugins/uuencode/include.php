<?php

/*  include.php
    Copyright (C) 2002-2010  Hastymail Development group

    This file is part of Hastymail.

    Hastymail is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Hastymail is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hastymail; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

* $Id:$
*/
function get_attachments($message_data, $content=false) {
    $attachment = false;
    $attachment_lines = array();
    $attachments = array();
    $filename = false;
    $lines = explode("\r\n", $message_data);
    foreach ($lines as $line_number => $line) {
        if (!$attachment) {
            if (preg_match('/begin \d+ (.+)$/', trim($line), $matches)) {
                if (!$content) {
                    $filename = $matches[1];
                    $attachment = true;
                }
                elseif ($content == $matches[1]) {
                    $filename = $matches[1];
                    $attachment = true;
                }
            }
        }
        else {
            if (trim($line) == 'end') {
                $attachments[$filename] = join("\n", $attachment_lines);
                $attachment = false;
                $attachment_lines = array();
                $filename = false;
            }
            else {
                if ($content) {
                    $attachment_lines[] = $line;
                }
            }
        }
    }
    return $attachments;
}
/**
 * Replace convert_uudecode()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.convert_uudecode
 * @author      Michael Wallner <mike@php.net>
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.8 $
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
*/
function manual_convert_uudecode($string) {
    if (!is_scalar($string)) {
        user_error('convert_uuencode() expects parameter 1 to be'
            . ' string, ' . gettype($string) . ' given', 
            E_USER_WARNING);
        return false;
    }
    if (strlen($string) < 8) {
        user_error('convert_uuencode() The given parameter is not'
            . ' a valid uuencoded string', E_USER_WARNING);
        return false;
    }
    $decoded = '';
    foreach (explode("\n", $string) as $line) {
        $c = count($bytes = unpack('c*', substr(trim($line), 1)));
        while ($c % 4) {
            $bytes[++$c] = 0;
        }
        foreach (array_chunk($bytes, 4) as $b) {
            $b0 = $b[0] == 0x60 ? 0 : $b[0] - 0x20;
            $b1 = $b[1] == 0x60 ? 0 : $b[1] - 0x20;
            $b2 = $b[2] == 0x60 ? 0 : $b[2] - 0x20;
            $b3 = $b[3] == 0x60 ? 0 : $b[3] - 0x20;
            
            $b0 <<= 2;
            $b0 |= ($b1 >> 4) & 0x03;
            $b1 <<= 4;
            $b1 |= ($b2 >> 2) & 0x0F;
            $b2 <<= 6;
            $b2 |= $b3 & 0x3F;
            $decoded .= pack('c*', $b0, $b1, $b2);
        }
    }
    return rtrim($decoded, "\0");
}

?>
