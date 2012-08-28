<?php

/**
 * html.class.php
 *
 * This contains functions needed to generate html output.
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: html.class.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/**
 * Undocumented class
 * @package squirrelmail
 */
class html {
    var $tag, $text, $style, $class,  
        $id, $html_el = array(), $javascript, $xtr_prop;

    function html($tag='', $text='', $style ='', $class='', $id='',
            $xtr_prop = '', $javascript = '') {
        $this->tag = $tag;
        $this->text = $text;
        $this->style = $style;
        $this->class = $class;
        $this->id = $id;
        $this->xtr_prop = $xtr_prop;
        $this->javascript = $javascript;
    }

    function htmlAdd($el, $last=true) {
        if ($last) {
            $this->html_el[] = $el;
        } else {
            $new_html_el = array();
            $new_html_el[] = $el;
            foreach ($this->html_el as $html_el) {
                $new_html_el[] = $html_el;
            }
            $this->html_el = $new_html_el;
        }
    }

    function AddChild($tag='', $text='', $style ='', $class='', $id='',
            $xtr_prop = '', $javascript = '') {
        $el = new html ($tag, $text, $style, $class, $id, $xtr_prop, $javascript);
        $this->htmlAdd($el);
    }

    function FindId($id) {
        $cnt = count($this->html_el);
        $el = false;
        if ($cnt) {
            for ($i = 0 ; $i < $cnt; $i++) {
                if ($this->html_el[$i]->id == $id) {
                    $ret = $this->html_el[$i];
                    return $ret;
                } else if (count($this->html_el[$i]->html_el)) {
                    $el = $this->html_el[$i]->FindId($id);
                }
                if ($el) return $el;
            }
        }
        return $el;
    }     

    function InsToId( $el, $id, $last=true) {
        $html_el = &$this->FindId($id);
        if ($html_el) {
            $html_el->htmlAdd($el, $last);
        }
    }     

    function scriptAdd($script) {
        $s = "\n".'<!--'."\n".
            $script .
            "\n".'// -->'."\n";
        $el = new html ('script',$s,'','','',array('language' => 'JavaScript',
                    'type' => 'text/javascript'));
        $this->htmlAdd($el);
    }

    function echoHtml( $usecss=false, $indent='x') {
        if ($indent == 'x') {
            $indent = ''; $indentmore = '';
        } else {
            $indentmore = $indent . '  ';
        }
        $tag = $this->tag;
        $text = $this->text;
        $class = $this->class;
        $id = $this->id;
        $style = $this->style;
        $javascript = $this->javascript;
        $xtr_prop = $this->xtr_prop;
        if ($xtr_prop) {
            $prop = '';
            foreach ($xtr_prop as $k => $v) {
                if (is_string($k)) {
                    $prop.=' '.$k.'="'.$v.'"';
                } else {
                    $prop.=' '.$v;
                }
            }
        }   
        if ($javascript) {
            $js = '';
            foreach ($javascript as $k => $v) { /* here we put the onclick, onmouseover etc entries */
                $js.=' '.$k.'="'.$v.'";';
            }
        }
        if ($tag) {   	  
            echo $indent . '<' . $tag;
        } else {
            echo $indent;
        }
        if ($class) {
            echo ' class="'.$class.'"';
        }  
        if ($id) {
            echo ' id="'.$id.'"';
        }
        if ($xtr_prop) {
            echo ' '.$prop;
        }
        if ($style && !$usecss && !is_array($style)) {
            /* last premisse is to prevent 'style="Array"' in the output */
            echo ' style="'.$style.'"';  
        }
        if ($javascript) {
            echo ' '.$js;
        }
        if ($tag) echo '>';

        $openstyles = '';
        $closestyles = '';
        if ($style && !$usecss) {
            foreach ($style as $k => $v) {
                $openstyles .= '<'.$k.'>';
            }
            foreach ($style as $k => $v) {
                /* if value of key value = true close the tag */
                if ($v) {
                    $closestyles .= '</'.$k.'>';
                }   
            }
        }
        echo $openstyles;

        if ($text) {
            echo $text;
        }

        $cnt = count($this->html_el);
        if ($cnt) {
            echo "\n";
            for($i = 0;$i<$cnt;$i++) {
                $el = $this->html_el[$i];
                $el->echoHtml($usecss,$indentmore);
            }
            echo $indent;
        }
        echo $closestyles;
        if ($tag) {
            echo '</'.$tag.'>'."\n";
        } else {
            echo "\n";
        }
    }
}

