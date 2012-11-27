<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 *
 * @package PhpMyAdmin
 */

/**
 *
 * @package PhpMyAdmin
 */
class PMA_Theme_Manager
{
    /**
     * @var string path to theme folder
     * @access protected
     */
    var $_themes_path;

    /**
     * @var array available themes
     */
    var $themes = array();

    /**
     * @var string  cookie name
     */
    var $cookie_name = 'pma_theme';

    /**
     * @var boolean
     */
    var $per_server = false;

    /**
     * @var string name of active theme
     */
    var $active_theme = '';

    /**
     * @var PMA_Theme PMA_Theme active theme
     */
    var $theme = null;

    /**
     * @var string
     */
    var $theme_default = 'original';

    function __construct()
    {
        $this->init();
    }

    /**
     * sets path to folder containing the themes
     *
     * @param string  $path   path to themes folder
     * @return  boolean success
     */
    function setThemesPath($path)
    {
        if (! $this->_checkThemeFolder($path)) {
            return false;
        }

        $this->_themes_path = trim($path);
        return true;
    }

    /**
     * @public
     * @return  string
     */
    function getThemesPath()
    {
        return $this->_themes_path;
    }

    /**
     * sets if there are different themes per server
     *
     * @param boolean $per_server
     */
    function setThemePerServer($per_server)
    {
        $this->per_server  = (bool) $per_server;
    }

    function init()
    {
        $this->themes = array();
        $this->theme_default = 'original';
        $this->active_theme = '';

        if (! $this->setThemesPath($GLOBALS['cfg']['ThemePath'])) {
            return false;
        }

        $this->setThemePerServer($GLOBALS['cfg']['ThemePerServer']);

        $this->loadThemes();

        $this->theme = new PMA_Theme;


        if (! $this->checkTheme($GLOBALS['cfg']['ThemeDefault'])) {
            trigger_error(
                sprintf(
                    __('Default theme %s not found!'),
                    htmlspecialchars($GLOBALS['cfg']['ThemeDefault'])
                    ),
                E_USER_ERROR
                );
            $GLOBALS['cfg']['ThemeDefault'] = false;
        }

        $this->theme_default = $GLOBALS['cfg']['ThemeDefault'];

        // check if user have a theme cookie
        if (! $this->getThemeCookie()
         || ! $this->setActiveTheme($this->getThemeCookie())) {
            // otherwise use default theme
            if ($GLOBALS['cfg']['ThemeDefault']) {
                $this->setActiveTheme($GLOBALS['cfg']['ThemeDefault']);
            } else {
                // or original theme
                $this->setActiveTheme('original');
            }
        }
    }

    function checkConfig()
    {
        if ($this->_themes_path != trim($GLOBALS['cfg']['ThemePath'])
         || $this->theme_default != $GLOBALS['cfg']['ThemeDefault']) {
            $this->init();
        } else {
            // at least the theme path needs to be checked every time for new
            // themes, as there is no other way at the moment to keep track of
            // new or removed themes
            $this->loadThemes();
        }
    }

    function setActiveTheme($theme = null)
    {
        if (! $this->checkTheme($theme)) {
            trigger_error(
                sprintf(
                    __('Theme %s not found!'),
                    htmlspecialchars($theme)
                ),
                E_USER_ERROR);
            return false;
        }

        $this->active_theme = $theme;
        $this->theme = $this->themes[$theme];

        // need to set later
        //$this->setThemeCookie();

        return true;
    }

    /**
     * @return  string  cookie name
     */
    function getThemeCookieName()
    {
        // Allow different theme per server
        if (isset($GLOBALS['server']) && $this->per_server) {
            return $this->cookie_name . '-' . $GLOBALS['server'];
        } else {
            return $this->cookie_name;
        }
    }

    /**
     * returns name of theme stored in the cookie
     * @return  string  theme name from cookie
     */
    function getThemeCookie()
    {
        if (isset($_COOKIE[$this->getThemeCookieName()])) {
            return $_COOKIE[$this->getThemeCookieName()];
        }

        return false;
    }

    /**
     * save theme in cookie
     *
     * @return bool true
     */
    function setThemeCookie()
    {
        $GLOBALS['PMA_Config']->setCookie($this->getThemeCookieName(), $this->theme->id,
            $this->theme_default);
        // force a change of a dummy session variable to avoid problems
        // with the caching of phpmyadmin.css.php
        $GLOBALS['PMA_Config']->set('theme-update', $this->theme->id);
        return true;
    }

    /**
     * @private
     * @param string $folder
     * @return  boolean
     */
    private function _checkThemeFolder($folder)
    {
        if (! is_dir($folder)) {
            trigger_error(
                sprintf(__('Theme path not found for theme %s!'),
                    htmlspecialchars($folder)),
                E_USER_ERROR);
            return false;
        }

        return true;
    }

    /**
     * read all themes
     *
     * @return bool true
     */
    function loadThemes()
    {
        $this->themes = array();

        if ($handleThemes = opendir($this->getThemesPath())) {
            // check for themes directory
            while (false !== ($PMA_Theme = readdir($handleThemes))) {
                // Skip non dirs, . and ..
                if ($PMA_Theme == '.' || $PMA_Theme == '..' || ! is_dir($this->getThemesPath() . '/' . $PMA_Theme)) {
                    continue;
                }
                if (array_key_exists($PMA_Theme, $this->themes)) {
                    continue;
                }
                $new_theme = PMA_Theme::load($this->getThemesPath() . '/' . $PMA_Theme);
                if ($new_theme) {
                    $new_theme->setId($PMA_Theme);
                    $this->themes[$PMA_Theme] = $new_theme;
                }
            } // end get themes
            closedir($handleThemes);
        } else {
            trigger_error(
                'phpMyAdmin-ERROR: cannot open themes folder: ' . $this->getThemesPath(),
                E_USER_WARNING);
            return false;
        } // end check for themes directory

        ksort($this->themes);
        return true;
    }

    /**
     * checks if given theme name is a known theme
     *
     * @param string  $theme  name fo theme to check for
     * @return bool
     */
    function checkTheme($theme)
    {
        if (! array_key_exists($theme, $this->themes)) {
            return false;
        }

        return true;
    }

    /**
     * returns HTML selectbox, with or without form enclosed
     *
     * @param boolean $form   whether enclosed by from tags or not
     * @return string
     */
    function getHtmlSelectBox($form = true)
    {
        $select_box = '';

        if ($form) {
            $select_box .= '<form name="setTheme" method="post" action="index.php"'
                .' target="_parent">';
            $select_box .=  PMA_generate_common_hidden_inputs();
        }

        $theme_preview_path= './themes.php';
        $theme_preview_href = '<a href="' . $theme_preview_path . '" target="themes" class="themeselect">';
        $select_box .=  $theme_preview_href . __('Theme') . '</a>:' . "\n";

        $select_box .=  '<select name="set_theme" xml:lang="en" dir="ltr" class="autosubmit">';
        foreach ($this->themes as $each_theme_id => $each_theme) {
            $select_box .=  '<option value="' . $each_theme_id . '"';
            if ($this->active_theme === $each_theme_id) {
                $select_box .=  ' selected="selected"';
            }
            $select_box .=  '>' . htmlspecialchars($each_theme->getName()) . '</option>';
        }
        $select_box .=  '</select>';

        if ($form) {
            $select_box .=  '<noscript><input type="submit" value="' . __('Go') . '" /></noscript>';
            $select_box .=  '</form>';
        }

        return $select_box;
    }

    /**
     * enables backward compatibility
     */
    function makeBc()
    {
        $GLOBALS['theme']           = $this->theme->getId();
        $GLOBALS['pmaThemePath']    = $this->theme->getPath();
        $GLOBALS['pmaThemeImage']   = $this->theme->getImgPath();

        /**
         * load layout file if exists
         */
        if (file_exists($this->theme->getLayoutFile())) {
            include $this->theme->getLayoutFile();
        }


    }

    /**
     * prints out preview for every theme
     *
     */
    function printPreviews()
    {
        foreach ($this->themes as $each_theme) {
            $each_theme->printPreview();
        } // end 'open themes'
    }

    /**
     * returns PMA_Theme object for fall back theme
     * @return object   PMA_Theme
     */
    function getFallBackTheme()
    {
        if (isset($this->themes['original'])) {
            return $this->themes['original'];
        }

        return false;
    }

    /**
     * prints css data
     *
     * @param string $type
     * @return bool
     */
    function printCss($type)
    {
        if ($this->theme->loadCss($type)) {
            return true;
        }

        // if loading css for this theme failed, try default theme css
        $fallback_theme = $this->getFallBackTheme();
        if ($fallback_theme && $fallback_theme->loadCss($type)) {
            return true;
        }

        return false;
    }
}
?>
