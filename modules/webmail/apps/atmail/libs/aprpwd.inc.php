<?php
//
// aprpwd.inc.php version 1.0 7 Oct 2004 By Comee Qin php@kuchingfest.com
// function name: salt(int); crypt_apr_md5(plain,salt); hex2bin(hex); get_apr_salt(aprstring); to64(value,count);
//
//
// Sub Module aprpwd.inc.php
//
// 1) salt(length);
// To generate salt base on the length given. Min is 2 and Max is 12
//
// 2) crypt_apr_md5(plain,salt);
// To generate the apache password string from the plain text given
// using the salt given. If salt is not given, a salt will be generated.
//
// 3) hex2bin(hex);
// To convert the hex string into bin code.
//
// 4) get_apr_salt(aprstring);
// To extract salt from existing apache password string given.
//
// 5) to64(value,count);
// return the number of count from the value using base conversion on MD5 root.
//


function get_apr_salt($string)
{
    $salt = FALSE;
    if (substr($string, 0, 6) == '$apr1$') $salt = substr($string, 6, 8);
    return $salt;
}

function to64($value, $count)
{
    $ROOT = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $result = '';
    while(--$count) {
        $result .= $ROOT[$value & 0x3f];
        $value >>= 6;
    }
    return $result;
}

function salt($length = 2)
{
    $ROOT = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    //     1234567890123456789012345678901234567890123456789012345678901234         64 chars
    $salt = '';
    $length = (int) $length;
    $length < 2 && $length = 2;
    for($i = 0; $i < $length; $i++)
    {
        $salt .= $ROOT[rand(0, 63)];
    }

    return $salt;
}

function hex2bin($hex)
{
    $bin = '';
    $ln = strlen($hex);
    for($i = 0; $i < $ln; $i += 2)
    {
        $bin .= chr(hexdec($hex{$i} . $hex{$i+1}));
    }

    return $bin;
}

function crypt_apr_md5($plain, $salt = null)
{
    if (is_null($salt))
    {
        $salt = salt(8);
    }
    elseif (preg_match('/^\$apr1\$/', $salt))
    {
        $salt = preg_replace('/^\$apr1\$([^$]+)\$.*/', '\\1', $salt);
    }
    else
    {
        $salt = substr($salt, 0,8);
    }

    $length     = strlen($plain);
    $context    = $plain . '$apr1$' . $salt;
    $binary     = hex2bin(md5($plain . $salt . $plain));

    for ($i = $length; $i > 0; $i -= 16)
    {
        $context .= substr($binary, 0, ($i > 16 ? 16 : $i));
    }

    for ( $i = $length; $i > 0; $i >>= 1)
    {
        $context .= ($i & 1) ? chr(0) : $plain[0];
    }

    $binary = hex2bin(md5($context));

    for($i = 0; $i < 1000; $i++)
    {
        $new = ($i & 1) ? $plain : substr($binary, 0,16);
        if ($i % 3) $new .= $salt;
        if ($i % 7) $new .= $plain;
        $new .= ($i & 1) ? substr($binary, 0,16) : $plain;
        $binary = hex2bin(md5($new));
    }

    $p = array();
    for ($i = 0; $i < 5; $i++)
    {
        $k = $i + 6;
        $j = $i + 12;
        if ($j == 16) $j = 5;
        $p[] = to64(
        (ord($binary[$i]) << 16) |
        (ord($binary[$k]) << 8) |
        (ord($binary[$j])), 5
        );
    }

    return '$apr1$' . $salt . '$' . implode($p) .
    to64(ord($binary[11]), 3);
}
?>