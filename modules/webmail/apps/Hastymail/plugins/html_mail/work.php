<?php

/*  work.php: Plugin file responsible for the backend processing
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


/*  WORK HOOKS FUNCTIONS
    For every work hook the plugin registers in config.php there must
    be a corresponding function in this file called <plugin name>_<hook name>
    See docs/work_hooks.txt for a list of work hooks and descriptions.
*/
function html_mail_init($tools) {
    $opts = $tools->str;
    $tools->save_to_global_store('help_strings', $opts);
    if ($tools->get_page() == 'compose') {
        $tools->register_ajax_callback('convert_to_text', 1, false);
        if ($tools->get_setting('html_format_mail') || $tools->get_setting('html_mode_toggle')) {
            $tools->add_compose_content_type('html');
            $tools->disable_xhtml_http_header();
        }
        $tools->add_compose_get_content('html', 'tinyMCE.getInstanceById("compose_message").getBody().innerHTML;');

    }
}
function html_mail_update_settings($tools) {
    $html_font_sizes = array('xx-small', 'x-small', 'small', 'medium', 'large', 'x-large', 'xx-large');
    $html_font_families = array('Andale Mono', 'Arial', 'Arial Black', 'Book Antiqua',
        'Comic Sans MS', 'Courier New', 'Georgia', 'Helvetica', 'Impact', 'Symbol', 'Tahoma',
        'Terminal', 'Times New Roman', 'Trebuchet', 'Verdana', 'Webdings', 'Wingdings'
    );
    if (isset($_POST['html_format_mail']) && $_POST['html_format_mail']) {
        $tools->save_options_page_setting('html_format_mail', 1);
    }
    else {
        $tools->save_options_page_setting('html_format_mail', 0);
    }
    if (isset($_POST['html_mode_toggle']) && $_POST['html_mode_toggle']) {
        $tools->save_options_page_setting('html_mode_toggle', 1);
    }
    else {
        $tools->save_options_page_setting('html_mode_toggle', 0);
    }
    if (isset($_POST['html_font_size']) && in_array($_POST['html_font_size'], $html_font_sizes)) {
        $tools->save_options_page_setting('html_font_size', $_POST['html_font_size']);
    }
    else {
        $tools->save_options_page_setting('html_font_size', 0);
    }
    if (isset($_POST['html_font_family']) && in_array($_POST['html_font_family'], $html_font_families)) {
        $tools->save_options_page_setting('html_font_family', $_POST['html_font_family']);
    }
    else {
        $tools->save_options_page_setting('html_font_family', 0);
    }
}
function html_mail_page_end($tools) {
    if ($tools->get_page() != 'compose') {
        return;
    } 
    if (isset($_POST['html_mail_mode_type']) && intval($_POST['html_mail_mode_type']) == 1) {
        $post_enabled = true;
    }
    else {
        $post_enabled = false;
    }
    if ($post_enabled || $tools->get_setting('html_format_mail') || $tools->get_setting('html_mode_toggle')) {
        if ($post_enabled || $tools->get_setting('html_format_mail') || $tools->get_compose_content_type() == 'html') {
            $enable = true;
        }
        else {
            $enable = false;
        }
        $tools->add_style('<style type="text/css">#compose_message_tbl td{padding: 0px !important;}</style>');
        $js = '<script type="text/javascript" src="plugins/html_mail/tiny_mce/tiny_mce_gzip.js"></script>
        <script type="text/javascript">
        '.$tools->start_cdata().'
        tinyMCE_GZ.init({
            plugins : "table, advlink, insertdatetime, paste, style, xhtmlxtras, visualchars, spellchecker",
            themes : "advanced",
            languages : "en",
            disk_cache : true,
            debug : false
        });
        '.$tools->end_cdata().'
        </script>
        <script type="text/javascript">
        '.$tools->start_cdata().'
        tinyMCE.init({
            content_css : "?page=html_mail",
            force_br_newlines : true,
            forced_root_block : \'\' ,
            body_id: "compose_message",
            relative_urls : false,
            width: "635",
            mode: "none",
            theme: "advanced",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align : "left",
            cleanup_on_startup : true,
            convert_newlines_to_brs : true,
            convert_fonts_to_spans : true,
            theme_advanced_buttons1_add : "fontsizeselect, fontselect, forecolor",
            theme_advanced_buttons2_add : "styleprops, cite, ins, del, abbr, acronym, attribs, insertdate, inserttime, backcolor",
            theme_advanced_buttons3_add : "visualchars, copy, cut, paste, tablecontrols, spellchecker",
            theme_advanced_disable : "help,styleselect,image",
            extended_valid_elements : "hr[class|width|size|noshade]",
            plugins : "table, advlink, insertdatetime, paste, style, xhtmlxtras, visualchars, spellchecker",
            spellchecker_report_misspellings : "true",
            spellchecker_languages : "+English=en,French=fr",
            gecko_spellcheck : true,
            debug : false
        });
        function htmlmode(enable) {
            if (enable) {
                tinyMCE.execCommand("mceAddControl", false, "compose_message");
                document.getElementById("html_mode_link").innerHTML = "'.$tools->str[5].'"; 
                document.getElementById("html_mode_link").href = "javascript:htmlmode(false);"; 
                document.getElementById("html_mail_mode_type").value = 1; 
                document.getElementById("compose_content_type").value = "html";
            }
            else {
                tinyMCE.execCommand("mceRemoveControl", false, "compose_message");
                document.getElementById("html_mode_link").innerHTML = "'.$tools->str[6].'"; 
                document.getElementById("html_mode_link").href = "javascript:htmlmode(true);"; 
                document.getElementById("html_mail_mode_type").value = 0; 
                if (document.getElementById("compose_message").value) {
                    hm_ajax_html_mail_convert_to_text(document.getElementById("compose_message").value);
                    document.getElementById("compose_message").value = "Converting to text...";
                }
                document.getElementById("compose_content_type").value = "text";
            }
        }
        function callback_html_mail_convert_to_text(output) {
            document.getElementById("compose_message").value = output;
        }';
        if ($enable) {
            $js .= 'htmlmode(true);';
        }
        $js .= $tools->end_cdata().'
        </script>';
        $tools->add_js_tag($js);
    }
}
function html_mail_message_save($tools, $args) {
    if ((isset($_POST['rsargs'][12]) && $_POST['rsargs'][12] == 'html') ||
        (isset($_POST['compose_content_type']) && $_POST['compose_content_type'] == 'html')) {
        $body = $args[0];
        $alt_body = $tools->html2text($body);
        $tools->alter_compose_type('text/html', $body, $alt_body, '8-bit');
    }
}
function html_mail_message_send($tools, $args) {
    if (isset($_POST['html_mail_mode_type']) && intval($_POST['html_mail_mode_type']) == 1) {
        $body = $args[0];
        $alt_body = $tools->html2text($body);
        $tools->alter_compose_type('text/html', $body, $alt_body, '8-bit');
    }
}
?>
