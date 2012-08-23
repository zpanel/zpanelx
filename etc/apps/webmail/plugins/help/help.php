<?php

/**
 * Help Plugin
 *
 * @author Aleksander 'A.L.E.C' Machniak
 * @licence GNU GPL
 *
 * Configuration (see config.inc.php.dist)
 * 
 * */
class help extends rcube_plugin {

    // all task excluding 'login' and 'logout'
    public $task = '?(?!login|logout).*';
    // we've got no ajax handlers
    public $noajax = true;
    // skip frames
    public $noframe = true;

    function init() {
        $rcmail = rcmail::get_instance();

        $this->add_texts('localization/', false);

        // register task
        $this->register_task('help');

        // register actions
        $this->register_action('index', array($this, 'action'));
        $this->register_action('about', array($this, 'action'));
        $this->register_action('license', array($this, 'action'));

        // add taskbar button
        $this->add_button(array(
            'name' => 'helptask',
            'class' => 'button-help',
            'label' => 'help.help',
            'href' => './?_task=help',
            'onclick' => sprintf("return %s.command('help')", JS_OBJECT_NAME)
                ), 'taskbar');

        $rcmail->output->add_script(
                JS_OBJECT_NAME . ".enable_command('help', true);\n" .
                JS_OBJECT_NAME . ".help = function () { location.href = './?_task=help'; }", 'head');

        $skin = $rcmail->config->get('skin');
        if (!file_exists($this->home . "/skins/$skin/help.css"))
            $skin = 'default';

        // add style for taskbar button (must be here) and Help UI    
        $this->include_stylesheet("skins/$skin/help.css");
    }

    function action() {
        $rcmail = rcmail::get_instance();

        $this->load_config();

        // register UI objects
        $rcmail->output->add_handlers(array(
            'helpcontent' => array($this, 'content'),
        ));

        if ($rcmail->action == 'about')
            $rcmail->output->set_pagetitle($this->gettext('about'));
        else if ($rcmail->action == 'license')
            $rcmail->output->set_pagetitle($this->gettext('license'));
        else
            $rcmail->output->set_pagetitle($this->gettext('help'));

        $rcmail->output->send('help.help');
    }

    function content($attrib) {
        $rcmail = rcmail::get_instance();

        if ($rcmail->action == 'about') {
            return @file_get_contents($this->home . '/content/about.html');
        } else if ($rcmail->action == 'license') {
            return @file_get_contents($this->home . '/content/license.html');
        }

        // default content: iframe

        if ($src = $rcmail->config->get('help_source'))
            $attrib['src'] = $src;

        if (empty($attrib['id']))
            $attrib['id'] = 'rcmailhelpcontent';

        // allow the following attributes to be added to the <iframe> tag
        $attrib_str = create_attrib_string($attrib, array(
            'id', 'class', 'style', 'src', 'width', 'height', 'frameborder'));

        $out = sprintf('<iframe name="%s"%s></iframe>' . "\n", $attrib['id'], $attrib_str);

        return $out;
    }

}
