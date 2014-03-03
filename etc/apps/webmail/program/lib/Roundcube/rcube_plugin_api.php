<?php

/*
 +-----------------------------------------------------------------------+
 | This file is part of the Roundcube Webmail client                     |
 | Copyright (C) 2008-2012, The Roundcube Dev Team                       |
 |                                                                       |
 | Licensed under the GNU General Public License version 3 or            |
 | any later version with exceptions for skins & plugins.                |
 | See the README file for a full license statement.                     |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Plugins repository                                                  |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <roundcube@gmail.com>                        |
 +-----------------------------------------------------------------------+
*/

// location where plugins are loade from
if (!defined('RCUBE_PLUGINS_DIR')) {
    define('RCUBE_PLUGINS_DIR', RCUBE_INSTALL_PATH . 'plugins/');
}

/**
 * The plugin loader and global API
 *
 * @package    Framework
 * @subpackage PluginAPI
 */
class rcube_plugin_api
{
    static protected $instance;

    public $dir;
    public $url = 'plugins/';
    public $task = '';
    public $output;
    public $handlers              = array();
    public $allowed_prefs         = array();
    public $allowed_session_prefs = array();

    protected $plugins = array();
    protected $tasks = array();
    protected $actions = array();
    protected $actionmap = array();
    protected $objectsmap = array();
    protected $template_contents = array();
    protected $active_hook = false;

    // Deprecated names of hooks, will be removed after 0.5-stable release
    protected $deprecated_hooks = array(
        'create_user'       => 'user_create',
        'kill_session'      => 'session_destroy',
        'upload_attachment' => 'attachment_upload',
        'save_attachment'   => 'attachment_save',
        'get_attachment'    => 'attachment_get',
        'cleanup_attachments' => 'attachments_cleanup',
        'display_attachment' => 'attachment_display',
        'remove_attachment' => 'attachment_delete',
        'outgoing_message_headers' => 'message_outgoing_headers',
        'outgoing_message_body' => 'message_outgoing_body',
        'address_sources'   => 'addressbooks_list',
        'get_address_book'  => 'addressbook_get',
        'create_contact'    => 'contact_create',
        'save_contact'      => 'contact_update',
        'contact_save'      => 'contact_update',
        'delete_contact'    => 'contact_delete',
        'manage_folders'    => 'folders_list',
        'list_mailboxes'    => 'mailboxes_list',
        'save_preferences'  => 'preferences_save',
        'user_preferences'  => 'preferences_list',
        'list_prefs_sections' => 'preferences_sections_list',
        'list_identities'   => 'identities_list',
        'create_identity'   => 'identity_create',
        'delete_identity'   => 'identity_delete',
        'save_identity'     => 'identity_update',
        'identity_save'     => 'identity_update',
        // to be removed after 0.8
        'imap_init'         => 'storage_init',
        'mailboxes_list'    => 'storage_folders',
        'imap_connect'      => 'storage_connect',
    );

    /**
     * This implements the 'singleton' design pattern
     *
     * @return rcube_plugin_api The one and only instance if this class
     */
    static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new rcube_plugin_api();
        }

        return self::$instance;
    }

    /**
     * Private constructor
     */
    protected function __construct()
    {
        $this->dir = slashify(RCUBE_PLUGINS_DIR);
    }

    /**
     * Initialize plugin engine
     *
     * This has to be done after rcmail::load_gui() or rcmail::json_init()
     * was called because plugins need to have access to rcmail->output
     *
     * @param object rcube Instance of the rcube base class
     * @param string Current application task (used for conditional plugin loading)
     */
    public function init($app, $task = '')
    {
        $this->task   = $task;
        $this->output = $app->output;

        // register an internal hook
        $this->register_hook('template_container', array($this, 'template_container_hook'));

        // maybe also register a shudown function which triggers
        // shutdown functions of all plugin objects
    }

    /**
     * Load and init all enabled plugins
     *
     * This has to be done after rcmail::load_gui() or rcmail::json_init()
     * was called because plugins need to have access to rcmail->output
     *
     * @param array List of configured plugins to load
     * @param array List of plugins required by the application
     */
    public function load_plugins($plugins_enabled, $required_plugins = array())
    {
        foreach ($plugins_enabled as $plugin_name) {
            $this->load_plugin($plugin_name);
        }

        // check existance of all required core plugins
        foreach ($required_plugins as $plugin_name) {
            $loaded = false;
            foreach ($this->plugins as $plugin) {
                if ($plugin instanceof $plugin_name) {
                    $loaded = true;
                    break;
                }
            }

            // load required core plugin if no derivate was found
            if (!$loaded) {
                $loaded = $this->load_plugin($plugin_name);
            }

            // trigger fatal error if still not loaded
            if (!$loaded) {
                rcube::raise_error(array(
                    'code' => 520, 'type' => 'php',
                    'file' => __FILE__, 'line' => __LINE__,
                    'message' => "Requried plugin $plugin_name was not loaded"), true, true);
            }
        }
    }

    /**
     * Load the specified plugin
     *
     * @param string Plugin name
     *
     * @return boolean True on success, false if not loaded or failure
     */
    public function load_plugin($plugin_name)
    {
        static $plugins_dir;

        if (!$plugins_dir) {
            $dir         = dir($this->dir);
            $plugins_dir = unslashify($dir->path);
        }

        // plugin already loaded
        if ($this->plugins[$plugin_name] || class_exists($plugin_name, false)) {
            return true;
        }

        $fn = $plugins_dir . DIRECTORY_SEPARATOR . $plugin_name
            . DIRECTORY_SEPARATOR . $plugin_name . '.php';

        if (file_exists($fn)) {
            include $fn;

            // instantiate class if exists
            if (class_exists($plugin_name, false)) {
                $plugin = new $plugin_name($this);
                // check inheritance...
                if (is_subclass_of($plugin, 'rcube_plugin')) {
                    // ... task, request type and framed mode
                    if ((!$plugin->task || preg_match('/^('.$plugin->task.')$/i', $this->task))
                        && (!$plugin->noajax || (is_object($this->output) && $this->output->type == 'html'))
                        && (!$plugin->noframe || empty($_REQUEST['_framed']))
                    ) {
                        $plugin->init();
                        $this->plugins[$plugin_name] = $plugin;
                    }

                    if (!empty($plugin->allowed_prefs)) {
                        $this->allowed_prefs = array_merge($this->allowed_prefs, $plugin->allowed_prefs);
                    }

                    return true;
                }
            }
            else {
                rcube::raise_error(array('code' => 520, 'type' => 'php',
                    'file' => __FILE__, 'line' => __LINE__,
                    'message' => "No plugin class $plugin_name found in $fn"),
                    true, false);
            }
        }
        else {
            rcube::raise_error(array('code' => 520, 'type' => 'php',
                'file' => __FILE__, 'line' => __LINE__,
                'message' => "Failed to load plugin file $fn"), true, false);
        }

        return false;
    }

    /**
     * Allows a plugin object to register a callback for a certain hook
     *
     * @param string $hook Hook name
     * @param mixed  $callback String with global function name or array($obj, 'methodname')
     */
    public function register_hook($hook, $callback)
    {
        if (is_callable($callback)) {
            if (isset($this->deprecated_hooks[$hook])) {
                rcube::raise_error(array('code' => 522, 'type' => 'php',
                    'file' => __FILE__, 'line' => __LINE__,
                    'message' => "Deprecated hook name. "
                        . $hook . ' -> ' . $this->deprecated_hooks[$hook]), true, false);
                $hook = $this->deprecated_hooks[$hook];
            }
            $this->handlers[$hook][] = $callback;
        }
        else {
            rcube::raise_error(array('code' => 521, 'type' => 'php',
                'file' => __FILE__, 'line' => __LINE__,
                'message' => "Invalid callback function for $hook"), true, false);
        }
    }

    /**
     * Allow a plugin object to unregister a callback.
     *
     * @param string $hook Hook name
     * @param mixed  $callback String with global function name or array($obj, 'methodname')
     */
    public function unregister_hook($hook, $callback)
    {
        $callback_id = array_search($callback, $this->handlers[$hook]);
        if ($callback_id !== false) {
            unset($this->handlers[$hook][$callback_id]);
        }
    }

    /**
     * Triggers a plugin hook.
     * This is called from the application and executes all registered handlers
     *
     * @param string $hook Hook name
     * @param array $args Named arguments (key->value pairs)
     *
     * @return array The (probably) altered hook arguments
     */
    public function exec_hook($hook, $args = array())
    {
        if (!is_array($args)) {
            $args = array('arg' => $args);
        }

        $args += array('abort' => false);
        $this->active_hook = $hook;

        foreach ((array)$this->handlers[$hook] as $callback) {
            $ret = call_user_func($callback, $args);
            if ($ret && is_array($ret)) {
                $args = $ret + $args;
            }

            if ($args['abort']) {
                break;
            }
        }

        $this->active_hook = false;
        return $args;
    }

    /**
     * Let a plugin register a handler for a specific request
     *
     * @param string $action   Action name (_task=mail&_action=plugin.foo)
     * @param string $owner    Plugin name that registers this action
     * @param mixed  $callback Callback: string with global function name or array($obj, 'methodname')
     * @param string $task     Task name registered by this plugin
     */
    public function register_action($action, $owner, $callback, $task = null)
    {
        // check action name
        if ($task)
            $action = $task.'.'.$action;
        else if (strpos($action, 'plugin.') !== 0)
            $action = 'plugin.'.$action;

        // can register action only if it's not taken or registered by myself
        if (!isset($this->actionmap[$action]) || $this->actionmap[$action] == $owner) {
            $this->actions[$action] = $callback;
            $this->actionmap[$action] = $owner;
        }
        else {
            rcube::raise_error(array('code' => 523, 'type' => 'php',
                'file' => __FILE__, 'line' => __LINE__,
                'message' => "Cannot register action $action;"
                    ." already taken by another plugin"), true, false);
        }
    }

    /**
     * This method handles requests like _task=mail&_action=plugin.foo
     * It executes the callback function that was registered with the given action.
     *
     * @param string $action Action name
     */
    public function exec_action($action)
    {
        if (isset($this->actions[$action])) {
            call_user_func($this->actions[$action]);
        }
        else if (rcube::get_instance()->action != 'refresh') {
            rcube::raise_error(array('code' => 524, 'type' => 'php',
                'file' => __FILE__, 'line' => __LINE__,
                'message' => "No handler found for action $action"), true, true);
        }
    }

    /**
     * Register a handler function for template objects
     *
     * @param string $name Object name
     * @param string $owner Plugin name that registers this action
     * @param mixed  $callback Callback: string with global function name or array($obj, 'methodname')
     */
    public function register_handler($name, $owner, $callback)
    {
        // check name
        if (strpos($name, 'plugin.') !== 0) {
            $name = 'plugin.' . $name;
        }

        // can register handler only if it's not taken or registered by myself
        if (is_object($this->output)
            && (!isset($this->objectsmap[$name]) || $this->objectsmap[$name] == $owner)
        ) {
            $this->output->add_handler($name, $callback);
            $this->objectsmap[$name] = $owner;
        }
        else {
            rcube::raise_error(array('code' => 525, 'type' => 'php',
                'file' => __FILE__, 'line' => __LINE__,
                'message' => "Cannot register template handler $name;"
                    ." already taken by another plugin or no output object available"), true, false);
        }
    }

    /**
     * Register this plugin to be responsible for a specific task
     *
     * @param string $task Task name (only characters [a-z0-9_.-] are allowed)
     * @param string $owner Plugin name that registers this action
     */
    public function register_task($task, $owner)
    {
        // tasks are irrelevant in framework mode
        if (!class_exists('rcmail', false)) {
            return true;
        }

        if ($task != asciiwords($task)) {
            rcube::raise_error(array('code' => 526, 'type' => 'php',
                'file' => __FILE__, 'line' => __LINE__,
                'message' => "Invalid task name: $task."
                    ." Only characters [a-z0-9_.-] are allowed"), true, false);
        }
        else if (in_array($task, rcmail::$main_tasks)) {
            rcube::raise_error(array('code' => 526, 'type' => 'php',
                'file' => __FILE__, 'line' => __LINE__,
                'message' => "Cannot register taks $task;"
                    ." already taken by another plugin or the application itself"), true, false);
        }
        else {
            $this->tasks[$task] = $owner;
            rcmail::$main_tasks[] = $task;
            return true;
        }

        return false;
    }

    /**
     * Checks whether the given task is registered by a plugin
     *
     * @param string $task Task name
     *
     * @return boolean True if registered, otherwise false
     */
    public function is_plugin_task($task)
    {
        return $this->tasks[$task] ? true : false;
    }

    /**
     * Check if a plugin hook is currently processing.
     * Mainly used to prevent loops and recursion.
     *
     * @param string $hook Hook to check (optional)
     *
     * @return boolean True if any/the given hook is currently processed, otherwise false
     */
    public function is_processing($hook = null)
    {
        return $this->active_hook && (!$hook || $this->active_hook == $hook);
    }

    /**
     * Include a plugin script file in the current HTML page
     *
     * @param string $fn Path to script
     */
    public function include_script($fn)
    {
        if (is_object($this->output) && $this->output->type == 'html') {
            $src = $this->resource_url($fn);
            $this->output->add_header(html::tag('script',
                array('type' => "text/javascript", 'src' => $src)));
        }
    }

    /**
     * Include a plugin stylesheet in the current HTML page
     *
     * @param string $fn Path to stylesheet
     */
    public function include_stylesheet($fn)
    {
        if (is_object($this->output) && $this->output->type == 'html') {
            $src = $this->resource_url($fn);
            $this->output->include_css($src);
        }
    }

    /**
     * Save the given HTML content to be added to a template container
     *
     * @param string $html HTML content
     * @param string $container Template container identifier
     */
    public function add_content($html, $container)
    {
        $this->template_contents[$container] .= $html . "\n";
    }

    /**
     * Returns list of loaded plugins names
     *
     * @return array List of plugin names
     */
    public function loaded_plugins()
    {
        return array_keys($this->plugins);
    }

    /**
     * Callback for template_container hooks
     *
     * @param array $attrib
     * @return array
     */
    protected function template_container_hook($attrib)
    {
        $container = $attrib['name'];
        return array('content' => $attrib['content'] . $this->template_contents[$container]);
    }

    /**
     * Make the given file name link into the plugins directory
     *
     * @param string $fn Filename
     * @return string
     */
    protected function resource_url($fn)
    {
        if ($fn[0] != '/' && !preg_match('|^https?://|i', $fn))
            return $this->url . $fn;
        else
            return $fn;
    }
}
