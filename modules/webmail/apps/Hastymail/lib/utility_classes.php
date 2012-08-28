<?php

/*  utility_classes.php: Backend to the main page logic 
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

/* string encoding, cleaning, language, and encryption routines */
class fw_string_factory {
    var $site_key;
    var $allowed_tag_list;
    var $gpc;
    var $default_lang;
    var $mb_support;
    var $iconv_support;
    var $utf8_encode_support;

    function fw_string_factory() {
        $this->default_lang = 'en_US';
        $this->allowed_tag_list = array();
        $this->site_key = false;
        $this->mb_support = false;
        $this->iconv_support = false;
        $this->utf8_encode_support = false;
    }
    function prep_string_factory() {
        if (ini_get('magic_quotes_gpc')) {
            $this->gpc = true;
        }
        else {
            $this->gpc = false;
        }
        if (function_exists('mb_convert_encoding') && function_exists('mb_detect_encoding')) {
            $this->mb_support = true;
        }
        if (function_exists('iconv')) {
            $this->iconv_support = true;
        }
        if (function_exists('utf8_encode')) {
            $this->utf8_encode_support = true;
        }
    }
    function code_url($string) {
        $chrs = $this->set_url_codes();
        $res = '';
        while (strlen($string) !== 0) {
            $char = $string{0};
            $string = substr($string, 1);
            $res .= $chrs[ord($char)];
        }
        return $res;
    }
    function set_url_codes() {
        $chrs = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        'Aa', 'Ab', 'Ac', 'Ad', 'Ae', 'Af', 'Ag', 'Ah', 'Ai', 'Aj', 'Ak', 'Al', 'Am', 'An', 'Ao', 'Ap', 'Aq', 'Ar', 'As', 'At', 'Au', 'Av', 'Aw', 'Ax', 'Ay', 'Az',
        'Ba', 'Bb', 'Bc', 'Bd', 'Be', 'Bf', 'Bg', 'Bh', 'Bi', 'Bj', 'Bk', 'Bl', 'Bm', 'Bn', 'Bo', 'Bp', 'Bq', 'Br', 'Bs', 'Bt', 'Bu', 'Bv', 'Bw', 'Bx', 'By', 'Bz',
        'Ca', 'Cb', 'Cc', 'Cd', 'Ce', 'Cf', 'Cg', 'Ch', 'Ci', 'Cj', 'Ck', 'Cl', 'Cm', 'Cn', 'Co', 'Cp', 'Cq', 'Cr', 'Cs', 'Ct', 'Cu', 'Cv', 'Cw', 'Cx', 'Cy', 'Cz',
        'Da', 'Db', 'Dc', 'Dd', 'De', 'Df', 'Dg', 'Dh', 'Di', 'Dj', 'Dk', 'Dl', 'Dm', 'Dn', 'Do', 'Dp', 'Dq', 'Dr', 'Ds', 'Dt', 'Du', 'Dv', 'Dw', 'Dx', 'Dy', 'Dz',
        'Ea', 'Eb', 'Ec', 'Ed', 'Ee', 'Ef', 'Eg', 'Eh', 'Ei', 'Ej', 'Ek', 'El', 'Em', 'En', 'Eo', 'Ep', 'Eq', 'Er', 'Es', 'Et', 'Eu', 'Ev', 'Ew', 'Ex', 'Ey', 'Ez',
        'Fa', 'Fb', 'Fc', 'Fd', 'Fe', 'Ff', 'Fg', 'Fh', 'Fi', 'Fj', 'Fk', 'Fl', 'Fm', 'Fn', 'Fo', 'Fp', 'Fq', 'Fr', 'Fs', 'Ft', 'Fu', 'Fv', 'Fw', 'Fx', 'Fy', 'Fz',
        'Ga', 'Gb', 'Gc', 'Gd', 'Ge', 'Gf', 'Gg', 'Gh', 'Gi', 'Gj', 'Gk', 'Gl', 'Gm', 'Gn', 'Go', 'Gp', 'Gq', 'Gr', 'Gs', 'Gt', 'Gu', 'Gv', 'Gw', 'Gx', 'Gy', 'Gz',
        'Pa', 'Pb', 'Pc', 'Pd', 'Pe', 'Pf', 'Pg', 'Ph', 'Pi', 'Pj', 'Pk', 'Pl', 'Pm', 'Pn', 'Po', 'Pp', 'Pq', 'Pr', 'Ps', 'Pt', 'Pu', 'Pv', 'Pw', 'Px', 'Py', 'Pz',
        'Qa', 'Qb', 'Qc', 'Qd', 'Qe', 'Qf', 'Qg', 'Qh', 'Qi', 'Qj', 'Qk', 'Ql', 'Qm', 'Qn', 'Qo', 'Qp', 'Qq', 'Qr', 'Qs', 'Qt', 'Qu', 'Qv',
        );
        return $chrs;
    }
    function uncode_url($string) {
        $chrs = $this->set_url_codes();
        $keys = array();
        foreach ($chrs as $i => $v) {
            $keys[$v] = $i;
        }
        $res = '';
        while ($string) {
            $char = $string{0};
            if (intval($char) == 0 && strtoupper($char) == $char) {
                $char .= $string{1};
                $string = substr($string, 2);
            }
            else {
                $string = substr($string, 1);
            }
            if (isset($keys[$char])) {
                $id = $keys[$char];
                $res .= chr($id);
            }
            else {
                echo 'BUG';
                return false;
            }
        }
        return $res;
    }
    function sp_decrypt($string) {
        global $hm_utils_mod;
        if ($hm_utils_mod) {
            $res = hm_decrypt($string, $this->get_key());
        }
        else {
            $res = $this->crypt_string($this->uncode_url($string));
        }
        if (strlen($res) < 11) {
            return array(false, '');
        }
        return array(substr($res, 0, 10), substr($res, 10));
    }
    function sp_crypt($string) {
        global $hm_utils_mod;
        if ($hm_utils_mod) {
            return hm_crypt($string, $this->get_key());
        }
        return $this->code_url($this->crypt_string(time().$string));
    }
    function get_key() {
        if (!$this->site_key) {
            echo 'FATAL: A site key was not found in your configuration file.';
            exit;
        }
        return $this->site_key;
    }
    function crypt_string($input, $key=false) {
        if (!$key) {
            $key = $this->get_key();
        }
        $k_tmp = preg_split('//', $key, -1, PREG_SPLIT_NO_EMPTY);
        foreach($k_tmp as $char) {
            $k[] = ord($char);
        }
        unset($k_tmp); 
        $message = preg_split('//', $input, -1, PREG_SPLIT_NO_EMPTY);
        $rep = count($k);
        for ($n=0;$n<$rep;$n++) {
            $s[] = $n;
        }
        $i = 0;
        $f = 0;
        for ($i = 0;$i<$rep;$i++) {
            $f = (($f + $s[$i] + $k[$i]) % $rep);
            $tmp = $s[$i];
            $s[$i] = $s[$f];
            $s[$f] = $tmp;
        }
        $i = 0;
        $f = 0;
        foreach($message as $letter) {
            $i = (($i + 1) % $rep);
            $f = (($f + $s[$i]) % $rep);
            $tmp = $s[$i];
            $s[$i] = $s[$f];
            $s[$f] = $tmp;
            $t = $s[$i] + $s[$f];
            $done = ($t^(ord($letter)));
            $i++;
            $f++;
            $enc_array[] = chr($done);
        }
        $coded = implode('', $enc_array);
        return $coded;
    }
    function quoted_decode($string, $header=false) {
        if ($header) {
            $string = str_replace('_', '=20', $string);
        }
        $string = preg_replace("/\=(\r\n|\n)/m", '', $string);
        $string = preg_replace("/(\=[0-9A-Z]{2})\n/i", "\\1 \n", $string);
        $result = preg_replace("/\=([0-9A-Z]{2})/ie", "''.chr(hexdec('\\1')).'' ", $string);
        return $result;
    }
    function utf8_to_html($input) {
        global $hm_utils_mod;
        if (preg_match('/(?:[^\x00-\x7F])/',$input) !== 1) {
            return $input;
        }
        if ($this->utf8_encode_support) {
            if (!$this->is_utf($input)) {
                $input = utf8_encode($input);
            }
        }
        if ($hm_utils_mod) {
            return hm_utf8_to_html($input);
        }
        $control = array(
            128 => 160, 129 => 160, 130 => 8218, 131 => 402, 132 => 8222, 133 => 8230,
            134 => 8224, 135 => 8225, 136 => 710, 137 => 8240, 138 => 352, 139 => 8249,
            140 => 338, 141 => 160, 142 => 160, 143 => 160, 144 => 160, 145 => 8216,
            146 => 8217, 147 => 8220, 148 => 8221, 149 => 8226, 150 => 8211, 151 => 8212,
            152 => 732, 153 => 8482, 154 => 353, 155 => 8250, 156 => 339, 157 => 160,
            158 => 160, 159 => 376);
    
        $output = '';
        $index = 0;
        $len = strlen($input);
        while ($index < $len) {
            $num = false;
            $char = ord($input[$index]);
            switch (true) {
                case $char == 10 || $char == 13 || $char == 9:
                    $output .= $input[$index];
                    $index += 1;
                    break;
                case $char < 0x20:
                    $output .= '?';
                    $index += 1;
                    break;
                case $char < 0x80:
                    $output .= $input[$index];
                    $index += 1;
                    break;
                case $char < 0xE0:
                    $num = ((($char % 0x20) * 0x40) + (@ord($input[$index + 1]) % 0x40));
                    $index += 2;
                    break;
                case $char < 0xF0:
                    $num = ((($char % 0x10) * 0x1000) + ((@ord($input[$index + 1]) % 0x40)
                        * 0x40) + (@ord($input[$index + 2]) % 0x40));
                    $index += 3;
                    break;
                case $char < 0xF8:
                    $num = ((($char % 0x08) * 0x40000) + ((@ord($input[$index + 1]) % 0x40)
                        * 0x1000) + ((@ord($input[$index + 2]) % 0x40) * 0x40) +
                        (@ord($input[$index + 3]) % 0x40));
                    $index += 4;
                    break;
                case $char < 0xFC:
                    $num = ((($char % 0x04) * 0x1000000) + ((@ord($input[$index + 1]) % 0x40)
                            * 0x40000) + ((@ord($input[$index + 2]) % 0x40) * 0x1000) +
                            ((@ord($input[$index + 3]) % 0x40) * 0x40) +
                            (@ord($input[$index + 4]) % 0x40));
                    $index += 5;
                    break;
                default:
                    $num = ((($char % 0x02) * 0x40000000) + ((@ord($input[$index + 1]) % 0x40)
                        * 0x1000000) + ((@ord($input[$index + 2]) % 0x40) * 0x40000) +
                        ((@ord($input[$index + 3]) % 0x40) * 0x1000) +
                        ((@ord($input[$index + 4]) % 0x40) * 0x40) +
                        (@ord($input[$index + 5]) % 0x40));
                    $index += 6;
                    break;
            }
            if ($num) {
                if ($num > 127 && $num < 160) {
                    $output .= '&#'.$control[$num].';';
                }
                else {
                    $output .= '&#'.$num.';';
                }
            }
        }
        return $output;
    } 
    function html_clean($string, $tags=array()) {
        if (empty($tags)) {
            $tags = $this->allowed_tag_list;
        }
        return $this->utf8_to_html(filter_html($string, $tags));
    }
    function hm_htmlentities($string) {
        global $hm_utils_mod;
        if ($hm_utils_mod) {
            return hm_html_entities($string);
        }
        $chars = array('<',    '>',    '& ',     '"',      "'"     );
        $ents  = array('&lt;', '&gt;', '&amp; ', '&#034;', '&#039;');
        return str_replace($chars, $ents, $string);
    }
    function html_safe($string) {
        return $this->utf8_to_html($this->hm_htmlentities($string));
    }
    function is_utf($string) {
        return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]
        |\xE0[\xA0-\xBF][\x80-\xBF]
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
        |\xED[\x80-\x9F][\x80-\xBF]
        |\xF0[\x90-\xBF][\x80-\xBF]{2}
        |[\xF1-\xF3][\x80-\xBF]{3}
        |\xF4[\x80-\x8F][\x80-\xBF]{2}
        )+%xs', $string);
    }
    function get_user_strings() {
        global $user;
        global $include_path;
        $str = array();
        if (isset($_SESSION['user_settings']['lang'])) {
            $lang = $_SESSION['user_settings']['lang'];
        }
        elseif ($this->default_lang) {
            $lang = $this->default_lang;
        }
        else {
            $lang = 'en_US';
        }
        if (isset($_SESSION['selected_lang']) && $_SESSION['selected_lang'] == $lang &&
            isset($_SESSION['str']) && !empty($_SESSION['str'])) {
            $str = -1;
        }
        if (empty($str)) {
            $file = 'lang/'.$lang.'.php';
            require_check($file);
            $str = require_once($include_path.$file);
            if (empty($str)) {
                echo 'FATAL: No language file found';
                die;
            }
            $temp = array();
            if (isset($str['charset'])) {
                $charset = $str['charset'];
            }
            else {
                $charset = false;
            }
            foreach ($str as $i => $v) {
                $temp[$i] = $user->htmlsafe($v, $charset, false, false, false, true);
            }
            $str = $temp;
            $_SESSION['selected_lang'] = $lang;
            $_SESSION['str'] = $str;
        }
        return $str;
    }
    function utf8_convert($text, $charset, $int_str=false) {
        global $mb_charset_codes;
        global $charset_codes;
        global $include_path;
        $entities = false;
        if ($charset == 'us-ascii' || $charset == 'utf-8') {
            return array(false, $text);
        }
        elseif (!trim($charset)) {
            if ($this->mb_support) {
                $enc = mb_detect_encoding($text);
                if ($enc) {
                    $charset = $enc;
                }
            }
        }
        if ($this->mb_support) {
            if (isset($mb_charset_codes[strtoupper($charset)])) {
                return array(false, @mb_convert_encoding($text, 'UTF-8', $charset));
            }
            elseif (!in_array(strtolower($charset), $charset_codes)) {
                return array(false, @mb_convert_encoding($text, 'UTF-8', @mb_detect_encoding($text)));
            }
        }
        elseif ($this->iconv_support) {
            return array(false, @iconv($charset, 'UTF-8', $text));
        }
        if (!isset($_SESSION['charset_codes'][$charset])) {
            $_SESSION['charset_codes'] = require_once($include_path.'lang/charsets.php');
        }
        if (!isset($_SESSION['charset_codes'][$charset])) {
            return array(false, $text);
        }
        $code_page = $_SESSION['charset_codes'][$charset];
        $utf8='';
        if ($int_str) {
            $entities = true;
        }
        while ($text) {
            $val = $text{0};
            if (ord($val) > 127) {
                $index = strtoupper(dechex(ord($val)));
                if (isset($code_page[$index])) {
                    $utf8 .= '&#x'.$code_page[$index].';';
                }
                else {
                    $utf8 .= $val;
                }
            }
            else {
                $utf8 .= $val;
            }
            $text = substr($text, 1);
        }
        return array($entities, $utf8);
    }
}

/* match predefined data types requires fw_string_factory */
class fw_type_check extends fw_string_factory {
    function fw_type_check() {
    }
    /* dynamically call the match_<type> method for the supplied input */
    function match($val, $type) {
        switch ($type) {
            case 'float';
            case 'email';
            case 'int';
            case 'url_clean';
            case 'html_clean';
            case 'true';
            case 'string';
            case 'url';
            case 'int_nonzero';
                $method = 'match_'.$type;
                if ($this->gpc) { stripslashes($val); }
                $res = $this->$method($val);
                break;
            case 'array';
                $method = 'match_'.$type;
                $res = $this->$method($val);
                break;
            default:
                $res = false;
        }
        return $res;
    }
    /* match a boolean type */
    function match_true($val) {
        if ($val) {
            return true;
        }
        else {
            return false;
        }
    }
    /* match an array data type */
    function match_array($val) {
        if (is_array($val)) {
            return true;
        }
        else {
            return false;
        }
    }
    /* matching a floating point number */
    function match_float($val) {
        return preg_match("/^(\-|\+){0,1}([0-9])*(\.){0,1}([0-9])*$/", trim($val));
    }
    /* try to accurately validate an E-mail. Based on RFC 3696 */
    function match_email_full($val) {
        /* defaults */
        $domain = false;
        $local = false;
        /* basic checks to weed out obviously incorrect values */
        if (!trim($val) || strlen($val) > 320) {
            return false;
        }
        /* determine if this is a local address or if it has a domain part */
        if (strpos($val, '@') !== false) {
            $local = substr($val, 0, strrpos($val, '@'));
            $domain = substr($val, (strrpos($val, '@') + 1));
        }
        else {
            $local = $val;
        }
        /* domain is not require but the local part is */
        if (!$local) {
            return false;
        }
        else {
            /* if we have a domain validate it. */
            if ($domain && !$this->validate_domain_full($domain)) {
                return false;
            }
            /* validate the required local part */
            if (!$this->validate_local_full($local)) {
                return false;
            }
        }
        /* E-mail is valid */
        return true;
    }
    /* Do email domain part checks per RFC 3696 section 2 */
    function validate_domain_full($val) {
        /* check for a dot, max allowed length and standard ASCII characters */
        if (strpos($val, '.') === false || strlen($val) > 255 || preg_match("/[^A-Z0-9\-\.]/i", $val) ||
            $val{0} == '-' || $val{(strlen($val) - 1)} == '-') {
            return false;
        }
        return true;
    }
    /* do email local part checks per RFC 3696 section 3 */
    function validate_local_full($val) {
        /* check length, "." rules, and for characters > ASCII 127 */
        if (strlen($val) > 64 || $val{0} == '.' || $val{(strlen($val) -1)} == '.' || strstr($val, '..') ||
            preg_match('/[^\x00-\x7F]/',$val)) {
            return false;
        }
        /* remove escaped characters and quoted strings */
        $local = preg_replace("/\\\\.{1}/", '', $val);
        $local = preg_replace("/\"[^\"]+\"/", '', $local);

        /* validate remaining unescaped characters */
        if (preg_match("/[[:print:]]/", $local) && !preg_match("/[@\\\",\[\]]/", $local)) {
            return true;
        }
        return false;
    }
    /* match an E-mail address using the full method or a regex */
    function match_email($val) {
        global $valid_email_regex;
        global $email_validation_type;
        if ($email_validation_type == 'full') {
            return $this->match_email_full($val);
        }
        else {
            return preg_match($valid_email_regex, $val);
        }
    }
    /* match an integer type */
    function match_int($val) {
        return preg_match("/^[0-9]+$/", trim($val));
    } 
    /* match a URL */
    function match_url($val) {
        return preg_match("/((http|ftp|rtsp)s?:\/\/(%[[:digit:]A-Fa-f][[:digit:]A-Fa-f]|[-_\.!~\*';\/\?#:@&=\+$,[:alnum:]])+)/i", $val);
    }
    function match_url_clean($val) {
        $cleaned = (string) preg_replace("/[^0-9a-zA-Z_*.]/", '', $val);
        if ($val != $cleaned) {
            return false;
        }
        else {
            return true;
        }
    }
    /* see if any html entities exist */
    function match_html_clean($val) {
        $cleaned = (string) htmlentities(str_replace(array('<', '>'), '',
                   strip_tags($val)));
        if ($cleaned != $val) {
            return false;
        }
        else {
            return true;
        }
    }
    /* match a test string */
    function match_string($val) {
        $typed = (string) $val;
        if ($typed != $val) {
            return false;
        }
        return true;
    }
    /* match a non-zero integer type */
    function match_int_nonzero($val) {
        $int = (int) $val;
        if ($int) {
            return true;
        }
        else {
            return false;
        }
    }
}

/* process $_POST input requires fw_string_factory, fw_tupe_check */
class fw_post_input extends fw_type_check {
    function check_post_forms($str) {
        foreach ($this->forms as $index => $vals) {
            if (isset($_POST[$index])) {
                $this->post_action = $index;
                $this->form_submitted = $vals;
                foreach ($vals as $name => $attrs) {
                    if (isset($_POST[$name])) {
                        if ($this->match($_POST[$name], $attrs[0])) {
                            if ($attrs[1] && @trim($_POST[$name]) !== '') {
                                if ($this->gpc && $attrs[0] != 'array') {
                                    $this->post[$name] = @stripslashes($_POST[$name]);
                                }
                                else {
                                    $this->post[$name] = $_POST[$name];
                                }
                            }
                            else {
                                if ($attrs[1]) {
                                    $this->bad_flds[$name] = $_POST[$name];
                                    $this->errors[] = $str[46].': '.$attrs[2];
                                }
                                else {
                                    if ($this->gpc && $attrs[0] != 'array') {
                                        $this->post[$name] = stripslashes($_POST[$name]);
                                    }
                                    else {
                                        $this->post[$name] = $_POST[$name];
                                    }
                                }
                            }
                        }
                        else {
                            $this->bad_flds[$name] = $_POST[$name];
                            if ($attrs[1] || trim($_POST[$name])) {
                                if (!trim($_POST[$name])) {
                                    $this->errors[] = $str[46].': '.$attrs[2];
                                }
                                else {
                                    $this->errors[] = $str[48].': '.$attrs[2];
                                }
                            }
                        }
                    }
                    else {
                        if (isset($_POST[$name])) {
                            $this->bad_flds[$name] = $_POST[$name];
                        }
                        else {
                            $this->bad_flds[$name] = false;
                        }
                        if ($attrs[1]) {
                            $this->errors[] = $str[47].': '.$attrs[2];
                        }
                    }
                }
                if (empty($this->errors)) {
                    $this->{'form_action_'.$this->post_action}($this->form_submitted, $this->post);
                }
                else {
                    foreach ($this->form_submitted as $i => $vals) {
                        if (isset($this->bad_flds[$i])) {
                            $this->form_vals[$i] = $this->bad_flds[$i];
                        }
                        elseif (isset($this->post[$i])) {
                            $this->form_vals[$i] = $this->post[$i];
                        }
                        else {
                            $this->form_vals[$i]  = '';
                        }
                    }
                }
                break;
            }
        }
        return $this->post;
    }
    function setup_post_forms() {
        $this->forms = $this->set_post_vars();
        return $this->forms;
    }
}

/* process $_GET input requires fw_string_factory, fw_type_check and fw_post_input */
class fw_get_input extends fw_post_input {
    function setup_get_vals() {
        $this->get_vals = $this->set_get_vars();
        return $this->get_vals;
    }
    function check_get_vals() {
        foreach ($_GET as $name => $val) {
            if (isset($this->get_vals[$name])) {
                $attrs = $this->get_vals[$name];
                if ($this->match($val, $attrs[0])) {
                    if ($this->gpc) {
                        $val = stripslashes($val);
                    }
                    $this->get[$name] = $val;
                }
            }
        }
        $this->process_get_vals($this->get);
    }
}

/* process all user input requires fw_string_factory, fw_type_check, fw_get_input, fw_post_input */
class fw_user_input extends fw_get_input {
    var $get_vals;
    var $form_submitted;
    var $get;
    var $ajax;
    var $post;
    var $errors;
    var $bad_flds;
    var $form_vals;
    var $form_redirect;
    var $forms;
    var $post_action;

    function fw_user_input() {
        $this->form_redirect = false;
        $this->form_vals = array();
        $this->get_vals = array();
        $this->post_action = false;
        $this->form_submitted = array();
        $this->forms = array();
        $this->get = array();
        $this->post = array();
        $this->errors = array();
        $this->bad_flds = array();
    }
    function process_user_input($str) {
        global $conf;
        global $user;
        $host_name = $conf['host_name'];
        $this->default_page_data();
        if (isset($_POST) && !empty($_POST) && !isset($_POST['login']) && !$user->is_ajax) {
            $this->setup_post_forms();
            $this->check_post_forms($str);
        }
        if ($user->just_logged_in || $this->form_redirect) {
            if (!isset($conf['show_imap_debug']) || !$conf['show_imap_debug']) {
                if (!isset($conf['show_smtp_debug']) || !$conf['show_smtp_debug']) {
                    $this->redirect_after_post();
                }
            }
        }
        if (isset($_GET) && !empty($_GET)) {
            $this->setup_get_vals();
        }
        else {
            $this->get = array();
        }
        if (isset($_SESSION['errors'])) {
            $user->redirected = true;
        }
        $this->check_get_vals();
        if ($user->redirected) {
            $this->errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }
    }
    function redirect_after_post() {
        global $conf;
        global $user;
        global $imap;
        global $start_pages;
        global $sticky_url;
        if (!empty($this->errors)) {
            $_SESSION['errors'] = $this->errors;
        }
        if (!$user->use_cookies) {
            $url_end = '&PHPSESSID='.session_id();
        }
        else {
            $url_end = '';
        }
        $url = $conf['http_prefix'].'://'.$conf['host_name'].str_replace('&amp;', '&', $sticky_url);
        if (isset($_SESSION['just_logged_in']) && $_SESSION['just_logged_in']) {
            $_SESSION['just_logged_in_redirect'] = 1;
            if (isset($_SESSION['user_settings']['start_page']) && $_SESSION['user_settings']['start_page'] != 'mailbox'
                && isset($start_pages[$_SESSION['user_settings']['start_page']])) {
                $url = $conf['http_prefix'].'://'.$conf['host_name'].$conf['url_base'].'?page='.$_SESSION['user_settings']['start_page'];
            }
        }
        elseif (isset($user->page_data['prev_next_action_url']) && $user->page_data['prev_next_action_url']) {
            $url = $conf['http_prefix'].'://'.$conf['host_name'].$conf['url_base'].$user->page_data['prev_next_action_url'];
        }
        elseif (isset($user->page_data['sent']) && $user->page_data['sent'] == 1 && !$user->page_data['new_window']) {
            if (isset($_SESSION['last_page'])) {
                $url = $conf['http_prefix'].'://'.$conf['host_name'].str_replace(array('&amp;', 'inline_html=1'), array('&', 'inline_html=0'), $_SESSION['last_page']);
            }
        }
        elseif (isset($user->page_data['sent']) && $user->page_data['sent'] == 1 && $user->page_data['new_window']) {
            if (isset($_SESSION['user_settings']['close_on_send']) && $_SESSION['user_settings']['close_on_send']) {
                if (isset($_SESSION['errors'])) {
                    unset($_SESSION['errors']);
                }
                ob_clean();
                echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
                     '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><title></title>'.
                     '<script type="text/javascript">window.close();</script></head><body></body></html>'; 
                exit;
            }
        }
        elseif (isset($user->redirect_page) && $user->redirect_page) {
            $url = $conf['http_prefix'].'://'.$conf['host_name'].$conf['url_base'].'?page='.$user->redirect_page;
        }
        if ($imap->connected) {
            $imap->disconnect();
        }
        $user->clean_up();
        header('HTTP/1.1 303 Found');
        if (substr($url, -1) == '/' && substr($url_end, 0, 1) == '&') {
            $url_end = '?'.substr($url_end, 1);
        }
        header('Location: '.$url.$url_end);
        exit;
    }
}

/* authenticate username & password */
class fw_auth {
    function md5_hash($string) {
        return '{MD5}'.base64_encode(pack('H*', md5($string)));
    }
    function do_auth($user, $pass, $proxyuser) {
        global $imap;
        $return = false;
        $imap->connect();
        if ($user && $pass && $imap->connected) {
            if ( isset($proxyuser) ) {
                return $imap->authenticate($user, $pass, $proxyuser);
            }
            else {
                return $imap->authenticate($user, $pass, false);
            }
        }
        return false;
    }
}

/* start/stop/continue user sessions requires fw_auth */
class fw_user_session extends fw_auth {
    var $logout;
    var $user_atts;
    var $logged_in;
    var $cookie_name;
    var $just_logged_in;
    var $login_attempt;
    var $random_session_id;
    var $basic_auth;
    var $admin;

    function fw_user_session() {
        $this->login_attempt = false;
        $this->user_atts = array();
        $this->basic_auth = false;
        $this->cookie_name = 'hastymail2';
        $this->logged_in = false;
        $this->admin = false;
        $this->logout = false;
        $this->random_session_id = false;
    }
    function start_session() {
        global $user;
        @@ini_set('arg_separator.output', '&amp;');
        if ($user->use_cookies) {
            @ini_set('session.use_cookies', 1);
            @ini_set('session.use_trans_sid', 0);
            @ini_set('session.cookie_path', $user->cookie_path);
            @ini_set('session.cookie_secure', $user->cookie_secure);
            session_name($this->cookie_name);
        }
        else {
            @ini_set('session.use_cookies', 0);
            @ini_set('session.use_trans_sid', 1);
            ob_start();
        }
        @session_start();
        $_SESSION['last_page'] = false;
        $_SESSION['last_dsp_page'] = false;
        $_SESSION['logged_in'] = true;
        $_SESSION['just_logged_in'] = true;
        $_SESSION['user_data'] = $this->user_atts;
    }
    function check_basic_auth() {
        global $conf;
        if (isset($conf['http_auth_username']) && isset($conf['http_auth_password']) &&
            $conf['http_auth_username'] && $conf['http_auth_password']) {
            if (isset($_SERVER[$conf['http_auth_username']]) && isset($_SERVER[$conf['http_auth_password']])) {
                $_POST['user'] = $_SERVER[$conf['http_auth_username']];
                $_POST['pass'] = $_SERVER[$conf['http_auth_password']];
                $_POST['login'] = true;
                $this->basic_auth = true;
            }
        }
        elseif (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                if (preg_match("/basic (.+)/i", $headers['Authorization'], $matches)) {
                    if (isset($matches[1])) {
                        $parts = explode(':', @base64_decode($matches[1]));
                        if (count($parts) == 2) {
                            $_POST['user'] = $parts[0];
                            $_POST['pass'] = $parts[1];
                            $_POST['login'] = true;
                            $this->basic_auth = true;
                        }
                    }
                }
            }
        }
    }
    function check_session() {
        global $user;
        global $conf;
        global $imap;
        $imap_index = 0;
        if ( isset($conf['imap_enable_proxyauth']) && $conf['imap_enable_proxyauth'] &&
            isset($_SERVER[$conf['imap_enable_proxyauth']]) && isset($conf['imap_proxyauth_user']) && isset($_POST['login'])) {
            $plugins = get_plugins(true, true);
            do_work_hook('on_login', array(), $plugins);
            $this->login_attempt = true;
            if (isset($_POST['imap_server']) && $_POST['imap_server']) {
                $username = trim($_SERVER[$conf['imap_enable_proxyauth']]);
                $alt_servers = get_alt_servers($conf);
                if (isset($alt_servers[$_POST['imap_server']])) {
                    $imap_index = $_POST['imap_server'];
                    $vals = $alt_servers[$_POST['imap_server']];
                    foreach ($vals as $i => $v) {
                        $name = substr($i, 5);
                        $imap->$name = $v;
                    }
                }
            }
            else {
                if ($user->append_login_domain && !strstr($_SERVER[$conf['imap_enable_proxyauth']], '@')) {
                    if (isset($conf['percent_d_host']) && trim($conf['percent_d_host']) && strstr($user->append_login_domain, '%d')) {
                        $domain = $user->get_domain($conf['host_name'], $conf['percent_d_host']);
                        $user->append_login_domain = str_replace('%d', $domain, $user->append_login_domain); 
                    }
                    $username = $_SERVER[$conf['imap_enable_proxyauth']].'@'.$user->append_login_domain;
                }
                else {
                    $username = $_SERVER[$conf['imap_enable_proxyauth']];
                }
            }
            $pass = $conf['imap_proxyauth_pass'];
            if (isset($conf['trim_login_fields']) && $conf['trim_login_fields']) {
                $username = trim($username);    
                $pass = trim($pass); 
            }
            if ($this->do_auth($username, $pass, $conf['imap_proxyauth_user'])) {
                $this->start_session();
                $this->logged_in = true;
                $_SESSION['imap_index'] = $imap_index;
                $_SESSION['user_data'] = array(
                    'username' => $username,
                );
                $this->just_logged_in = true;
            }
        }
        elseif (isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['login'])) {
            $plugins = get_plugins(true, true);
            do_work_hook('on_login', array(), $plugins);
            $this->login_attempt = true;
            if (isset($_POST['imap_server']) && $_POST['imap_server']) {
                $username = trim($_POST['user']);
                $alt_servers = get_alt_servers($conf);
                if (isset($alt_servers[$_POST['imap_server']])) {
                    $imap_index = $_POST['imap_server'];
                    $vals = $alt_servers[$_POST['imap_server']];
                    foreach ($vals as $i => $v) {
                        $name = substr($i, 5);
                        $imap->$name = $v;
                    }
                }
            }
            else {
                if ($user->append_login_domain && !strstr($_POST['user'], '@')) {
                    if (isset($conf['percent_d_host']) && trim($conf['percent_d_host']) && strstr($user->append_login_domain, '%d')) {
                        $domain = $user->get_domain($conf['host_name'], $conf['percent_d_host']);
                        $user->append_login_domain = str_replace('%d', $domain, $user->append_login_domain); 
                    }
                    $username = $_POST['user'].'@'.$user->append_login_domain;
                }
                else {
                    $username = $_POST['user'];
                }
            }
            $pass = $_POST['pass'];
            if (isset($conf['trim_login_fields']) && $conf['trim_login_fields']) {
                $username = trim($username);    
                $pass = trim($pass); 
            }
            if ($this->do_auth($username, $pass, false)) {
                $this->start_session();
                $this->logged_in = true;
                $_SESSION['imap_index'] = $imap_index;
                $_SESSION['user_data'] = array(
                    'username' => $username,
                    'pass'     => $user->string_crypt($pass)
                );
                $this->just_logged_in = true;
            }
        }
        elseif (isset($_GET['page']) && $_GET['page'] == 'logout') {
            $this->logout = true;
            $this->continue_session();
        }
        elseif (!$this->login_attempt) {
            if ($user->use_cookies && isset($_COOKIE[$this->cookie_name])) {
                $this->continue_session();
            }
            elseif (!$user->use_cookies) {
                if (isset($_REQUEST['PHPSESSID'])) {
                    session_id($_REQUEST['PHPSESSID']);
                    $this->continue_session();
                }
            }
        }
        if (isset($_SESSION['imap_index'])) {
            if ($_SESSION['imap_index'] > 0) {
                $tmp_conf = $conf;
                foreach ($tmp_conf as $i => $v) {
                    if (preg_match('/alt_'.$_SESSION['imap_index'].'/', $i)) {
                        $conf[substr($i, 6)] = $v;
                    }
                }
            }
        }
    }
    function continue_session() {
        global $user;
        global $conf;
        global $imap;
        global $hastymail_version;
        @ini_set('arg_separator.output', '&amp;');
        if ($this->logout) {
            if ($user->use_cookies) {
                @ini_set('session.use_cookies', 1);
                @ini_set('session.use_trans_sid', 0);
                @ini_set('session.cookie_path', $user->cookie_path);
                @ini_set('session.cookie_secure', $user->cookie_secure);
                session_name($this->cookie_name);
            }
            else {
                @ini_set('session.use_trans_sid', 1);
                @ini_set('session.use_cookies', 0);
                ob_start();
            }
            @session_start();
            $user->user_action->logout_actions();
            $this->logged_in = false;
            if ($user->use_cookies) {
                setcookie($this->cookie_name, '', time()-42000, $user->cookie_path);
            }
        }
        else {
            if ($user->use_cookies) {
                @ini_set('session.use_cookies', 1);
                @ini_set('session.use_trans_sid', 0);
                @ini_set('session.cookie_path', $user->cookie_path);
                @ini_set('session.cookie_secure', $user->cookie_secure);
                session_name($this->cookie_name);
            }
            else {
                @ini_set('session.use_trans_sid', 1);
                @ini_set('session.use_cookies', 0);
                ob_start();
            }
            @session_start();
            if (isset($conf['site_random_session_id']) && $conf['site_random_session_id'] && !isset($_POST['rs'])
                && (!isset($_GET['page']) || $_GET['page'] != 'inline_image') && !isset($_GET['show_image'])) {
                if (!isset($_SESSION['reload_count'])) {
                    $_SESSION['reload_count'] = 1;
                }
                else {
                    $_SESSION['reload_count']++;
                    if ($_SESSION['reload_count'] > 2) {
                        $_SESSION['reload_count'] = 1;
                        session_regenerate_id(true);
                    }
                }
            }
        }
        if (!$this->logout && !empty($_SESSION) && isset($_SESSION['user_data']['username']) ) {
            if (isset($_SESSION['imap_index']) && $_SESSION['imap_index']) {
                $alt_servers = get_alt_servers($conf);
                if (isset($alt_servers[$_SESSION['imap_index']])) {
                    $vals = $alt_servers[$_SESSION['imap_index']];
                    foreach ($vals as $i => $v) {
                        $name = substr($i, 5);
                        $imap->$name = $v;
                    }
                }
            }
            if ( isset($_SESSION['user_data']['pass']) ) {
                $this->recrypt_pass();
            }
            $this->logged_in = true;
            $this->just_logged_in = false;
            $_SESSION['just_logged_in'] = false;
            if (isset($_SESSION['just_logged_in_redirect'])) {
                unset($_SESSION['just_logged_in_redirect']);
                $_SESSION['just_logged_in'] = true;
            }
        }
        if (isset($_SESSION['imap_index'])) {
            if ($_SESSION['imap_index'] > 0) {
                $tmp_conf = $conf;
                foreach ($tmp_conf as $i => $v) {
                    if (preg_match('/alt_'.$_SESSION['imap_index'].'/', $i)) {
                        $conf[substr($i, 6)] = $v;
                    }
                }
            }
        }
        if ($this->logged_in) {
            if (isset($_SESSION['hm_version'])) {
                if ($_SESSION['hm_version'] != $hastymail_version) {
                    if (isset($conf['imap_enable_proxyauth']) && $conf['imap_enable_proxyauth'] ) {
                        $_POST = array('login' => true);
                        $_SESSION = array();
                        $this->check_session();
                        $_SESSION['updated'] = true;
                    }
                    else {
                        $pass_vals = $user->string_decrypt($_SESSION['user_data']['pass']);
                        if (isset($pass_vals[1])) {
                            $_POST = array('user' => $_SESSION['user_data']['username'], 'pass' => $pass_vals[1], 'login' => true);
                            $_SESSION = array();
                            $this->check_session();
                            $_SESSION['updated'] = true;
                        }
                    }
                }
            }
        }
        $_SESSION['hm_version'] = $hastymail_version;
    }
    function recrypt_pass() {
        global $user;
        $pass_bits = $user->string_decrypt($_SESSION['user_data']['pass']);
        if (is_array($pass_bits) && isset($pass_bits[1])) {
            $_SESSION['user_data']['pass'] = $user->string_crypt($pass_bits[1]);
        }
        else {
            echo 'BUG';
        }
    }
    function imap_continue() {
        global $imap;
        global $user;
        global $conf;
        if (isset($_SESSION['user_data']['username']) && isset($conf['imap_enable_proxyauth']) && $conf['imap_enable_proxyauth']) {
            $username = $_SESSION['user_data']['username'];
            $pass = $conf['imap_proxyauth_pass'];
            if (isset($pass)) {
                $imap->connect();
                if ($imap->connected) {
                    return $imap->authenticate($username, $pass, $conf['imap_proxyauth_user']);
                }
            }
            return false;
        }
        if (isset($_SESSION['user_data']['username']) && isset($_SESSION['user_data']['pass'])) {
            $username = $_SESSION['user_data']['username'];
            $pass_bits = $user->string_decrypt($_SESSION['user_data']['pass']);
            if (is_array($pass_bits) && isset($pass_bits[1])) {
                $imap->connect();
                if ($imap->connected) {
                    return $imap->authenticate($username, $pass_bits[1], false);
                }
            }
            return false;
        }
    }
    function close_session() {
        if ($this->logout) {
            $_SESSION = array();
            @session_destroy();
        }
        else {
            @session_write_close();
        }
    } 
}

/* wrapper around both fw_user_input and fw_user_session */ 
class fw_user {
    var $back_link;
    var $log_c_id;
    var $user_agent_class;
    var $user_agent;
    var $log_c_type;
    var $full_page_log;
    var $total_page_log;
    var $content_areas;
    var $default_timezone;
    var $admin;
    var $langs;
    var $themes;
    var $site_key;
    var $dsp_page;
    var $page_anchor;
    var $notices;
    var $login_action;
    var $post_action;
    var $get_action;
    var $get_vals;
    var $post_vals;
    var $logged_in;
    var $just_logged_in;
    var $user_session;
    var $user_action;
    var $random_session_id;
    var $cookie_name;
    var $cookie_secure;
    var $cookie_path;
    var $append_login_domain;
    var $username;
    var $settings_storage;
    var $ajax_enabled;
    var $sub_class_names;
    var $form_vals;
    var $default_lang;
    var $allowed_tag_list;
    var $is_ajax;
    var $str;
    var $use_cookies;
    var $redirected;
    var $page_title;
    var $html_content_type;
    
    function fw_user() {
        global $conf;
        global $langs;
        global $allowed_tag_list;
        $this->page_anchor = false;
        $this->page_title = '';
        $this->back_link = false;
        $this->is_ajax = false;
        $this->settings_storage = 'file';
        $this->user_agent_class = 'gecko';
        $this->user_agent = '';
        $this->log_c_type = 0;
        $this->log_c_id = 0;
        $this->admin = false;
        $this->langs = $langs;
        $this->full_page_log = false;
        $this->total_page_log = false;
        $this->redirected = false;
        $this->themes = array();
        $this->content_areas = array();
        $this->default_timezone = false;
        $this->allowed_tag_list = $allowed_tag_list;
        $this->ajax_enabled = true;
        $this->dsp_page = 'not_found';
        $this->notices = array();
        $this->default_lang = 'en_US';
        $this->logged_in = false;
        $this->login_action = 0;
        $this->post_action = 0;
        $this->just_logged_in = false;
        $this->sub_class_names = array('url' => false, 'post' => false);
        $this->random_session_id = false;
        $this->user_session = false;;
        $this->user_action = false;
        $this->get_vals = array();
        $this->username = false;
        $this->post_vals = array();
        $this->site_key = $conf['site_key'];
        $this->cookie_name = $conf['cookie_name'];
        $this->cookie_secure = $conf['http_prefix'] == 'https';
        $this->cookie_path = $conf['url_base'];
        $this->use_cookies = $conf['use_cookies'];
        $this->append_login_domain = false;
        $this->form_vals = array();
        $this->str = array();
    }
    function init() {
        global $imap;
        global $conf;
        global $include_path;
        global $phpversion;
        global $conf;
        global $fd;
        if (isset($_POST['rs']) && isset($_POST['rsrnd'])) {
            $this->is_ajax = true;
        }
        if (isset($_GET['anchor'])) {
            $this->page_anchor = $_GET['anchor'];
        }
        if ($phpversion < 5) {
            if ($this->default_timezone) {
                echo 'FATAL: Default Timezone support requires php5';
                die;
            }
        }
        elseif ($this->random_session_id && $phpversion < 5.1) {
            echo 'FATAL: Random session IDs requires php5 >= 5.1';
            die;
        }
        $this->get_user_agent();
        $this->sub_class_names = get_page_action($_GET, $_POST);
        if ($this->user_agent_class == 'palm' || $this->user_agent_class == 'simple') {
            if (isset($conf['no_simplemode_cookies']) and $conf['no_simplemode_cookies']) {
                $this->use_cookies = false;
            }
        }
        if (isset($_POST) && !empty($_POST) && !$this->is_ajax && !isset($_POST['login'])) {
            if ($this->sub_class_names['post']) {
                require_once($include_path.'lib'.$fd.'url_action_classes'.$fd.$this->sub_class_names['url'].'.php');
                require_once($include_path.'lib'.$fd.'post_action_class.php');
                require_once($include_path.'lib'.$fd.'post_action_classes'.$fd.$this->sub_class_names['post'].'.php');
                $post_class_name = 'fw_post_action_'.$this->sub_class_names['post'];
                $this->user_action = hm_new($post_class_name);
            }
            else {
                if ($this->sub_class_names['url']) {
                    require_once($include_path.'lib'.$fd.'url_action_classes'.$fd.$this->sub_class_names['url'].'.php');
                }
                else {
                    require_once($include_path.'lib'.$fd.'url_action_classes'.$fd.'misc.php');
                }
                require_once($include_path.'lib'.$fd.'post_action_class.php');
                $this->user_action = hm_new('fw_user_action_with_post');
            }
        }
        else {
            if ($this->sub_class_names['url']) {
                require_once($include_path.'lib'.$fd.'url_action_classes'.$fd.$this->sub_class_names['url'].'.php');
                $this->user_action = hm_new('fw_user_action_page');
            }
            else {
                require_once($include_path.'lib'.$fd.'url_action_classes'.$fd.'misc.php');
                $this->user_action = hm_new('fw_user_action_page');
            }
        }
        if (isset($conf['enable_database']) && $conf['enable_database']) {
            $this->start_database_connection($conf);
        }
        $this->user_session = hm_new('fw_user_session');
        $this->user_action->site_key = $this->site_key;
        $this->user_session->cookie_name = $this->cookie_name;
        $this->user_session->check_session();
        if (!$this->user_session->logout && !$this->user_session->logged_in && isset($conf['basic_http_auth']) && $conf['basic_http_auth']) {
            $this->user_session->check_basic_auth();
            $this->user_session->check_session();
        }
        $this->user_action->allowed_tag_list = $this->allowed_tag_list;
        $this->user_action->default_lang = $this->default_lang;
        $this->user_action->prep_string_factory();
        $this->str = $this->user_action->get_user_strings();
        if ($this->str == -1) {
            $this->str =& $_SESSION['str'];
        }
        $this->admin = $this->user_session->admin;
        if ($this->user_session->logout) {
            $this->login_action = 3;
        }
        elseif ($this->user_session->login_attempt) {
            if ($this->user_session->logged_in) {
                $this->just_logged_in = true;
                $this->login_action = 1;
            }
            else {
                if (empty($this->notices)) {
                    if (isset($imap->connected) && !$imap->connected) {
                        $this->notices[] = $this->str[505];
                    }
                    else {
                        $this->notices[] = $this->str[49];
                    }
                }
                $this->login_action = 2;
            }
        }
        $this->set_timezone();
        if ($this->user_session->logged_in) {
            global $conf;
            get_plugins();
            $this->logged_in = true;
            $this->username = $_SESSION['user_data']['username'];
        }
        $this->user_action->process_user_input($this->str);
        if ($this->user_action->post_action) {
            if (empty($this->user_action->errors)) {
                $this->post_action = 1;
                $this->post_vals = $this->user_action->post;
            }
            else {
                $this->form_vals = $this->user_action->form_vals;
                $this->post_action = 2;
            }
        }
        $this->get_vals = $this->user_action->get;
        if (!empty($this->user_action->errors)) {
            foreach ($this->user_action->errors as $v) {
                $this->notices[] = $v;
            }
        }
        if ($this->user_session->logout) {
            $this->purge_attachments();
            if (isset($conf['logout_url']) && $conf['logout_url']) {
                header('HTTP/1.1 303 Found');
                header('Location: '.$conf['logout_url']);
                exit;
            }
            $this->notices[] = $this->str[50];
        }
    }
    function purge_attachments() {
        if (isset($_SESSION['compose_sessions'])) {
            foreach ($_SESSION['compose_sessions'] as $i => $v) {
                unset_attachments($i);
            }
        }
    }
    function get_domain($host, $reg) {
        if (strpos($host, '.') !== false) {
            $parts = explode('.', $host);
        }
        else {
            return $host;
        }
        $name = array();
        foreach ($parts as $v) {
            if (!preg_match("/^$reg$/", $v)) {
                $name[] = $v;
            }
        }
        return implode('.', $name);
    }
    function start_database_connection($conf) {
        global $dbase;
        global $include_path;
        global $conf;
        global $fd;
        require_once($include_path.'db'.$fd.'db.php');
        if (isset($conf['db_type']) && isset($conf['db_pear_type']) && isset($conf['db_username']) &&
            isset($conf['db_password']) && isset($conf['db_hostname']) && isset($conf['db_database'])) {
            $dbase = hm_new('db_wrap');
            $dbase->db_type = $conf['db_type'];
            $dbase->pear_type = $conf['db_pear_type'];
            if (isset($conf['db_persistent'])) {
                $dbase->persistent = $conf['db_persistent'];
            }
            $dbase->add_read_server($conf['db_username'], $conf['db_password'], $conf['db_hostname'], $conf['db_database']);
            $dbase->connect();
        }
        else {
            $this->notices[] = 'Database support enabled, but the required settings not found in the configuration file';
        }
    }
    function string_crypt($string) {
        return $this->user_action->sp_crypt($string);
    }
    function string_decrypt($string) {
        $res = $this->user_action->sp_decrypt($string);
        return $res;
    }
    function encrypt($string, $key=false) {
        return $this->user_action->crypt_string($string, $key);
    }
    function utf8html($string) {
        return $this->user_action->utf8_to_html($string);
    }
    function decode_fld($string, $fld_charset) {
        list($entities, $string) = $this->user_action->utf8_convert($string, $fld_charset);
        if (preg_match_all("/(=\?[^\?]+\?(q|b)\?[^\?]+\?=)/i", $string, $matches)) {
            foreach ($matches[1] as $v) {
                $fld = substr($v, 2, -2);
                $charset = strtolower(substr($fld, 0, strpos($fld, '?')));
                $fld = substr($fld, (strlen($charset) + 1));
                $encoding = $fld{0};
                $fld = substr($fld, (strpos($fld, '?') + 1));
                if (strtoupper($encoding) == 'B') {
                    $fld = base64_decode($fld);
                    list($entities, $fld) = $this->user_action->utf8_convert($fld, $charset);
                }
                elseif (strtoupper($encoding) == 'Q') {
                    $fld = $this->user_action->quoted_decode($fld, true);
                    list($entities, $fld) = $this->user_action->utf8_convert($fld, $charset);
                }
                else {
                    list($entities, $fld) = $this->user_action->utf8_convert($fld, $charset);
                }
                $string = str_replace($v, $fld, $string);
            }
        }
        return $string;
    } 
    function htmlclean($string, $tags=array(), $qt=false, $charset=false) {
        if ($charset && $charset != 'us-ascii') {
            list ($entities, $string) = $this->user_action->utf8_convert($string, $charset);
            $string = $this->user_action->utf8_to_html($string);
        }
        $data = $this->user_action->html_clean($string, $tags);
        if ($qt) {
            $data = str_replace(array('&quot;'), array('"'), $data);
        }
        return $data;
    }
    function htmlsafe($string, $charset=false, $decode=false, $mailbox=false, $address=false, $interface_str=false, $entity_replace=true) {
        if ($decode) {
            $string = $this->decode_fld($string, $charset);
            $string = $this->user_action->utf8_to_html($this->user_action->hm_htmlentities($string));
        }
        elseif ($charset && $charset != 'us-ascii') {
            list ($entities, $string) = $this->user_action->utf8_convert($string, $charset, $interface_str);
            $string = $this->user_action->utf8_to_html($this->user_action->hm_htmlentities($string));
        }
        else {
            $string = $this->user_action->html_safe($string);
        }
        if ($mailbox) {
            $string = stripslashes($string);
        }
        if ($address) {
            $string = str_replace('"', '&quot;', $string);
        }
        if ($entity_replace) {
            $string = preg_replace('/&(?!([#a-z0-9]{3,};|lt;|gt;|mu;|nu;|xi;|ni;|or;|le;|ge;))/i','&amp;',$string);
            $string = str_replace(array('<', '>', '\'', '`'), array('&lt;', '&gt;', '&#096;', '&#039;'), $string);
        }
        return $string;
    }
    function is_utf8($string) {
        return $this->user_action->is_utf($string);
    }
    function make_int($input) {
        $res = (int) $input;
        return $res;
    }
    function make_float($input) {
        $res = (float) $input;
        return $float;
    }
    function set_timezone() {
        global $phpversion;
        if ($phpversion >= 5.2) {
            $tz_set = false;
            if (isset($_SESSION['user_settings']['timezone'])) {
                $tz_vals = DateTimeZone::listIdentifiers();
                if (in_array($_SESSION['user_settings']['timezone'], $tz_vals)) {
                    @date_default_timezone_set($_SESSION['user_settings']['timezone']);
                    $tz_set = true;
                }
            }
            if (!$tz_set) {
                if ($this->default_timezone) {
                    @date_default_timezone_set($this->default_timezone);
                }
                else {
                    @date_default_timezone_set(@date_default_timezone_get());
                }
            }
        }
    }
    function get_user_agent() {
        $user_agent_class = 'gecko';
        $user_agent = '';
        $simple_types = '(Windows CE;|BlackBerry|Links|Lynx|Blazer|Nokia|UPG1|Elinks'.
                        '|PalmSource|PalmOS|WebPro|Netfront|Xiino|hiptop|iPhone)';
        $palm_types   = '(BlackBerry|Blazer|Nokia|PalmSource|PalmOS|WebPro|'.
                        'Netfront|Xiino|hiptop)';
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            if (preg_match("/$simple_types/i", $_SERVER['HTTP_USER_AGENT'])) {
                $user_agent_class = 'simple';
                if (preg_match("/$palm_types/i", $_SERVER['HTTP_USER_AGENT'])) {
                    $user_agent_class = 'palm';
                }
            }
            else {
                switch (true) {
                    case stristr($_SERVER['HTTP_USER_AGENT'], 'opera'):
                        $user_agent_class = 'opera';
                        break;
                    case stristr($_SERVER['HTTP_USER_AGENT'], 'msie 7'):
                        $user_agent_class = 'msie7';
                        break;
                    case stristr($_SERVER['HTTP_USER_AGENT'], 'msie'):
                        $user_agent_class = 'msie';
                        break;
                    case stristr($_SERVER['HTTP_USER_AGENT'], 'konqueror'):
                    case stristr($_SERVER['HTTP_USER_AGENT'], 'safari'):
                        $user_agent_class = 'khtml';
                    default:
                        break;
                }
            }
        }
        // added dhb - If 'palm' or 'simple' is not already set, then
        // Check for a mobile UAProf and set $user_agent_class if found.
        if ($user_agent_class != 'palm' && $user_agent_class != 'simple') {
            if (function_exists('getallheaders')) {
                foreach (getallheaders() as $name => $value) {
                    if (stristr($name,'wap-profile') || preg_match('/^[0-9]+\-profile/i', $name)) {
                        $user_agent_class='palm';
                        break;
                    }
                }
            }
        }
        $this->user_agent_class = $user_agent_class;
        $this->user_agent = $user_agent;
        return $user_agent_class;
    }
    function clean_up() {
        global $sticky_url;
        global $dbase;
        if (is_object($dbase)) {
            $dbase->disconnect();
        }

        $nocache_args = array(
                    'thumbnail',
                    'show_image',
                    'download',
                    'rs',
                    'inline_image'
                );

        $cache_url = true;

        foreach($nocache_args as $test_arg) {
            if (isset($_GET[$test_arg])) {
                $cache_url = false;
                break;
            }
        }

        if ($this->dsp_page != 'compose' && $this->logged_in && $cache_url &&
            !strstr($sticky_url, 'inline_image') && $this->dsp_page != 'not_found'){
            $_SESSION['last_page'] = $sticky_url;
            $_SESSION['last_dsp_page'] = $this->dsp_page;
        }
        $this->user_session->close_session();
    }
} 

/* base template page data */
class fw_page_data {
    var $page_anchor;
    var $form_vals;
    var $notices;
    var $page_title;
    var $host_name;
    var $dsp_page;
    var $user; 
    var $new_window;
    var $parent_refresh;
    var $str;
    var $content_text;
    var $html_content_type;
    var $pd;
    var $page_id;
    var $msg_list_flds;
    var $show_headers;
    var $enable_onclick;

    function init_base_data() {
        global $host_name;
        global $user;
        global $conf;
        global $page_id;
        $this->page_id = $page_id;
        if ((isset($conf['show_imap_debug']) && $conf['show_imap_debug']) ||
            (isset($conf['show_smtp_debug']) && $conf['show_smtp_debug']) ||
            (isset($conf['db_debug']) && $conf['db_debug']) ||
            (isset($conf['show_cache_usage']) && $conf['show_cache_usage'])) {
            $this->html_content_type = 'html';
        }
        else {
            if (isset($conf['http_content_header'])) {
                $this->html_content_type = $conf['http_content_header'];
            }
            else {
                $this->html_content_type = 'html';
            }
        }
        $this->new_window = false;
        $this->content_text = '';
        $this->notices = $user->notices;
        $this->hostname = $conf['host_name'];
        $this->dsp_page = $user->dsp_page;
        $this->pd =& $user->page_data;
        $this->new_window = $this->pd['new_window'];
        $this->parent_refresh = $this->pd['parent_refresh'];
        $this->user =& $user;
        if (isset($this->user->html_content_type) && $this->user->html_content_type) {
            $this->html_content_type = $this->user->html_content_type;
        }
        $this->page_title = $user->page_title;
        $this->page_anchor = $user->page_anchor;
        $this->form_vals = $user->user_action->form_vals;
        list($this->msg_list_flds, $this->show_headers, $this->onclick) = get_msg_list_settings(); 
    }
    function start_cdata() {
        if ($this->html_content_type == 'xhtml') {
            return '/*<![CDATA[*/
            ';
        }
        return '';
    }
    function end_cdata() {
        if ($this->html_content_type == 'xhtml') {
            return '//]]>';
        }
        return '';
    }
}

class plugin_tools {
    var $data_store; 
    var $db;
    var $username;
    var $plugin;
    var $include_path;

    /* init */
    function plugin_tools($plugin) {
        global $include_path;
        global $fd;
        $this->data_store = array();
        $this->plugin = $plugin;
        $this->db = false;
        $this->get_strings();
        $this->include_path = '';
        if (isset($_SESSION['user_data']['username'])) {
            $this->username = $_SESSION['user_data']['username'];
        }
        else {
            $this->username = '';
        }
        if ($include_path) {
            $this->include_path = $include_path.'plugins'.$fd.$this->plugin.$fd;
        }
    }
    /* get plugin strings */
    function get_strings() {
        global $user;
        if (isset($_SESSION['plugin_strings'][$this->plugin])) {
            $strings = $_SESSION['plugin_strings'][$this->plugin];
            if (isset($_SESSION['user_settings']['lang'])) {
                $lang = $_SESSION['user_settings']['lang'];
            }
            elseif (isset($user->default_lang)) {
                $lang = $user->default_lang;
            }
            else {
                $lang = false;
            }
            if (isset($strings[$lang])) {
                $this->str = $strings[$lang];
            }
            else {
                if (count($strings) > 0) {
                    $this->str = array_shift($strings);
                }
            }
        }
        else {
            $this->str = array();
        }
    } 
    function is_new_window() {
        global $user;
        if (isset($user->page_data['new_window']) && $user->page_data['new_window']) {
            return true;
        }
        return false;
    }
    function get_hm_strings() {
        global $user;
        $pd = hm_new('site_page');
        return $pd->user->str;
    }
    /* imap functions */
    function imap_get_folders($force_update=false) {
        global $imap;
        if ($force_update) {
            $imap->get_folders(true);
        }
        return $imap->folder_list;
    }
    function imap_get_capability() {
        global $imap;
        global $user;
        $caps = '';
        if (!isset($user->page_data['imap_capability'])) {
            $imap->get_capability();
            $caps = $imap->capability;
        }
        else {
            $caps = $user->page_data['imap_capability'];
        }
        return $caps;
    }
    function imap_select_mailbox($mailbox, $sort_by='ARRIVAL', $unseen=false, $quick=false, $filter='ALL') {
        global $imap;
        return $imap->select_mailbox($mailbox, $sort_by, $unseen, $quick, $filter);
    }
    function imap_sort_mailbox($mailbox, $sort_type, $filter) {
        global $imap;
        return $imap->imap_sort_mailbox($mailbox, $sort_type, $filter);
    }
    function imap_get_mailbox_uids($mailbox) {
        global $imap;
        $uids = array();
        if (isset($_SESSION['uid_cache'][$mailbox]['uids'])) {
            $uids = $_SESSION['uid_cache'][$mailbox]['uids'];
        }
        return $uids;
    }
    function imap_get_header_list($mailbox, $uids) {
        global $imap;
        return $imap->get_mailbox_page($mailbox, $uids, false);
    }
    function imap_get_message_headers($uid, $part) {
        global $imap;
        $uid = (int) $uid;
        if ($uid) {
            return $imap->get_message_headers($uid, $part);
        }
        else {
            return array();
        }
    }
    function imap_get_message_structure($uid) {
        global $imap;
        return $imap->get_message_structure($uid);
    }
    function imap_move_messages($source_mailbox, $uids, $destination_mailbox) {
        global $user;
        if (isset($_SESSION['user_settings']['trash_folder']) && $_SESSION['user_settings']['trash_folder']) {
            $trash_folder = $_SESSION['user_settings']['trash_folder'];
        }
        else {
            $trash_folder = false;
        }
        $user->user_action->perform_imap_action('MOVE', $source_mailbox, $uids, $trash_folder, $destination_mailbox);
    }
    function imap_copy_messages($source_mailbox, $uids, $destination_mailbox) {
        global $user;
        if (isset($_SESSION['user_settings']['trash_folder']) && $_SESSION['user_settings']['trash_folder']) {
            $trash_folder = $_SESSION['user_settings']['trash_folder'];
        }
        else {
            $trash_folder = false;
        }
        $user->user_action->perform_imap_action('COPY', $source_mailbox, $uids, $trash_folder, $destination_mailbox);
    }
    function imap_flag_messages($mailbox, $uids, $flag) {
        global $user;
        $user->user_action->perform_imap_action($flag, $mailbox, $uids, false, false);
    }
    function imap_delete_messages($mailbox, $uids, $skip_trash=false, $silent=false) {
        global $user;
        if (!$skip_trash && isset($_SESSION['user_settings']['trash_folder']) && $_SESSION['user_settings']['trash_folder']) {
            $trash_folder = $_SESSION['user_settings']['trash_folder'];
        }
        else {
            $trash_folder = false;
        }
        $user->user_action->perform_imap_action('DELETE', $mailbox, $uids, $trash_folder, false, false, false, $silent);
    }
    function imap_expunge_mailbox($mailbox, $silent=false, $uids=array(), $force=false) {
        global $user;
        $user->user_action->perform_imap_action('EXPUNGE', $mailbox, $uids, false, false, false, $force, $silent);
    }
    function imap_search_mailbox($terms) {
        global $imap;
        $res = $imap->full_search($terms);
        return $res;
    }
    function imap_append_start($mailbox, $size, $seen=true) {
        global $imap;
        return $imap->append_start($mailbox, $size, $seen);
    }
    function imap_append_end() {
        global $imap;
        return $imap->append_end();
    }
    function imap_append_feed($string, $as_is=false) {
        global $imap;
        $imap->append_feed($string, $as_is);
    }
    function imap_custom_command($command, $chunked=false) {
        global $imap;
        $imap->send_command($command);
        $result = $imap->get_response(false, $chunked);
        $status = $imap->check_response($result, $chunked);
        return array($result, $status);
    }
    function get_url() {
        global $sticky_url;
        return $sticky_url;
    }
    function get_page() {
        global $user;
        global $sticky_url;
        if (preg_match("/page=([^&]{3,})(&amp;|$)/", $sticky_url, $matches)) {
            return $matches[1];
        }
        if ($user->logged_in) {
            return 'mailbox';
        }
        else {
            return 'login';
        }
    }
    function set_config_value($name, $value) {
        global $conf;
        $conf[$name] = $value;
    }
    function page_not_found() {
        global $user;
        $user->dsp_page = 'not_found';
    }
    function just_logged_in() {
        global $user;
        return $user->just_logged_in;
    }
    function logged_in() {
        global $user;
        return $user->logged_in;
    }
    function get_mailbox() {
        global $user;
        if (isset($_GET['mailbox']) && isset($_SESSION['folders']) &&
                isset($_SESSION['folders'][$_GET['mailbox']])) {
            return $_GET['mailbox'];
        }
        elseif (isset($user->page_data['mailbox'])) {
            return $user->page_data['mailbox'];
        }
        else {
            return false;
        }
    }
    function set_current_message($string) {
        global $pd;
        $pd->pd['message_data'] = $string;
    }
    function get_current_message_uid() {
        global $pd;
        if (isset($pd->pd['message_uid'])) {
            return $pd->pd['message_uid'];
        }
        elseif (isset($_GET['uid'])) {
            return $_GET['uid'];
        }
        return 0;
    }
    function get_current_message_type() {
        global $pd;
        if (isset($pd->pd['raw_message_type'])) {
            return $pd->pd['raw_message_type'];
        }
        else {
            return '';
        }
    }
    function get_current_message() {
        global $pd;
        global $user;
        if (isset($pd->pd['message_data']) && $pd->pd['message_data']) {
            return $pd->pd['message_data'];
        }
        elseif (isset($user->page_data['message_data']) && $user->page_data['message_data']) {
            return $user->page_data['message_data'];
        }
        else {
            return '';
        }
    }
    function get_current_message_headers() {
        global $pd;
        if (isset($pd->pd['full_message_headers'])) {
            return $pd->pd['full_message_headers'];
        }
        else {
            return array();
        }
    }
    function set_mailbox($mailbox) {
        global $user;
        if (isset($user->page_data)) {
            $user->page_data['mailbox'] = $mailbox;
        }
    }
    function add_outgoing_header($name, $val) {
        global $message;
        $message->set_header($name, $val);
    }
    /* db functions */
    function get_db() {
        global $dbase;
        if (!is_object($dbase) || PEAR::isError($dbase->db_read)) {
            $res = false;
        }
        else {
            $res = true;
            $this->db = true;
        }
        return $res;
    }
    function db_insert($sql) {
        global $dbase;
        if ($this->db) {
            return $dbase->insert($sql);
        }
    }
    function db_query_one($sql) {
        global $dbase;
        if ($this->db) {
            return $dbase->single($sql);
        }
    }
    function db_query($sql) {
        global $dbase;
        if ($this->db) {
            return $dbase->select($sql);
        }
    }
    function db_quote($sql) {
        global $dbase;
        if ($this->db) {
            return $dbase->qt($sql);
        }
    }
    function db_update($sql) {
        global $dbase;
        if ($this->db) {
            return $dbase->update($sql);
        }
    }
    function db_delete($sql) {
        global $dbase;
        if ($this->db) {
            return $dbase->delete($sql);
        }
    }
    function db_puke() {
        global $dbase;
        if ($this->db) {
            echo_r($dbase->puke());
        }
    }
    /* temp storage */
    function save_to_global_store($name, $value) {
        $_SESSION['plugin_store'][$this->plugin][$name] = $value;
    }
    function get_from_global_store($name) {
        $res = array();
        if (isset($_SESSION['plugin_store']) && is_array($_SESSION['plugin_store'])) {
            foreach ($_SESSION['plugin_store'] as $plugin => $vals) {
                if (isset($vals[$name])) {
                    $res[$plugin] = $vals[$name];
                }
            }
        }
        return $res;
    }
    function remove_from_global_store($name) {
        if (isset($_SESSION['plugin_store'][$this->plugin][$name])) {
            unset($_SESSION['plugin_store'][$this->plugin][$name]);
        }
    }
    function add_to_store($name, $value) {
        $this->data_store[$name] = $value;
    }
    function remove_from_store($name) {
        if (isset($this->data_store[$name])) {
            unset($this->data_store[$name]);
        }
    }
    function get_from_store($name) {
        $res = false;
        if (isset($this->data_store[$name])) {
            $res = $this->data_store[$name];
        }
        return $res;
    }
    function save_options_page_setting($name, $value) {
        global $user;
        if ($user->logged_in) {
            $_SESSION['plugin_settings'][$name] = $value;
        }
    }
    /* user settings */
    function save_setting($name, $value) {
        global $user;
        if ($user->logged_in) {
            $_SESSION['user_settings'][$name] = $value;
            $user->user_action->write_settings(true);
        }
    }
    function get_setting($name) {
        global $user;
        $res = false;
        if (isset($_SESSION['user_settings'][$name])) {
            $res = $_SESSION['user_settings'][$name];
        }
        return $res;
    }
    function set_search_params($vals) {
        global $user;
        $user->page_data['search_terms'] = $vals;
        $user->page_data['max_search'] = count($vals) - 1;
    }
    /* output functions */
    function disable_xhtml_http_header() {
        global $user;
        $user->html_content_type = 'html';
    }
    function ajax_utf8_decode($string) {
        return decode_unicode_url($string);
    }
    function html2text($string) {
        return html_2_text($string);
    }
    function format_size($val, $extra=false) {
        return format_size($val, $extra);
    }
    function get_notices() {
        global $user;
        return $user->notices;
    }
    function send_notice($string) {
        global $user;
        $user->notices[] = $string;
    }
    function display_html($string, $tags=array(), $qt=false, $charset=false) {
        global $user;
        return $user->htmlclean($string, $tags, $qt, $charset);
    }
    function display_safe($string, $charset=false, $decode=false, $mailbox=false, $address=false, $int_str=false, $entity=true) {
        global $user;
        return $user->htmlsafe($string, $charset, $decode, $mailbox, $address, $int_str, $entity);
    }
    function add_js_event_handler($element_id, $event, $callback) {
        global $user;
        $user->page_data['plugin_js_events'][$element_id][$event][] = array('name' => $this->plugin, 'handler' => $callback);
    }
    function add_compose_get_content($content_type, $value_str) {
        global $user;
        $user->page_data['plugin_get_compose_message'][] = array($content_type, $value_str);
    }
    function add_compose_content_type($type) {
        global $user;
        $user->page_data['plugin_compose_content_type'][] = $type;
    }
    function get_compose_content_type() {
        global $user;
        $res = false;
        if (isset($user->page_data['ctype'])) {
            $res = $user->page_data['ctype'];
        }
        return $res;
    }
    function add_js_onload($string) {
        global $user;
        $user->page_data['plugin_js_onload'][] = $string;
    }
    function set_title($string) {
        global $user;
        $user->page_title = '| '.$string.' |';
    }
    function add_style($string) {
        global $user;
        $user->page_data['plugin_style'][] = $string;
    }
    function add_inline_js($string) {
        global $user;
        $user->page_data['inline_plugin_js'][] = $string;
    }
    function add_js_tag($string) {
        global $user;
        $user->page_data['plugin_js'][] = $string;
    }
    function add_js_update_function($func_name) {
        global $user;
        $user->page_data['js_update_functions'][] = $func_name;
    }
    function decode_maill_field($string) {
        global $user;
        return $user->decode_fld($string, false);
    }
    function print_mailbox_list($header_list, $mailbox, $n=1, $cols=array()) {
        $pd = hm_new('site_page');
        if (!empty($cols)) {
            $pd->msg_list_flds = array_merge($pd->msg_list_flds, $cols);
        }
        return $pd->print_mailbox_list_rows($pd->msg_list_flds, $header_list, $pd->onclick, $mailbox, $n);
    }
    function print_mailbox_list_headers($cols=array()) {
        $pd = hm_new('site_page');
        if (!empty($cols)) {
            $pd->msg_list_flds = array_merge($pd->msg_list_flds, $cols);
        }
        return $pd->print_mailbox_list_headers();
    }
    function print_message_controls() {
        $pd = hm_new('site_page');
        return $pd->print_message_controls();
    }
    function print_folder_dropdown($folders, $selected, $clean=false, $no_current=false, $selectable_type='selectable', $exclude_list=array(), $ignore_parents=false, $folder_check=array(), $allow_no_selection=false) {
        $pd = hm_new('site_page');
        return $pd->print_folder_option_list($folders, false, 0, $selected, $clean, $no_current, $selectable_type, $exclude_list, $ignore_parents, $folder_check, $allow_no_selection);
    }
    function start_cdata() {
        $pd = hm_new('site_page');
        if ($pd->html_content_type == 'xhtml') {
            return '/*<![CDATA[*/
            ';
        }
        return '';
    }
    function end_cdata() {
        $pd = hm_new('site_page');
        if ($pd->html_content_type == 'xhtml') {
            return '//]]>';
        }
        return '';
    }
    /* ajax setup */
    function register_ajax_callback($name, $args, $div_id) {
        global $user;
        if (!$user->is_ajax) {
            $user->page_data['plugin_ajax'][$this->plugin.'_'.$name] = array('plugin' => $this->plugin, 'name' => $name, 'args' => $args, 'div_id' => $div_id);
        }
    }
    /* compose page contact list */
    function merge_contacts_source($contacts) {
        $_SESSION['quick_list'] = array_merge($_SESSION['quick_list'], $contacts);
    }
    function register_contacts_source($title, $source) {
        $_SESSION['contact_sources'][] = array('title' => $title, 'source' => $source);
    }
    function alter_compose_type($mime_type, $body, $alt_body, $alt_encoding) {
        global $message;
        $message->alt_part = $body;
        $message->alt_part_mime = $mime_type;
        $message->alt_part_encoding = $alt_encoding;
        $message->body = $alt_body;
    }
    function override_sent_folder($folder) {
        $_SESSION['sent_folder_override'] = $folder;
    }
    function get_string($val) {
        global $user;
        if (isset($user->str[$val])) {
            return $user->str[$val];
        }
        else {
            return '';
        }
    }
    function get_contact_list($sort='sort_name', $page=1, $source='local', $filter=false, $page_size=false, $filter_regex=false) {
        require_once('vcard.php');
        $vcard = hm_new('vcard');
        return $vcard->get_quick_list($sort, $page, $source, $filter, $page_size, $filter_regex);
    }
    function get_theme() {
        $theme = 'default';
        if (isset($_SESSION['user_settings']['theme'])) {
            $theme = $_SESSION['user_settings']['theme'];
        }
        return $theme;
    }
}
?>
