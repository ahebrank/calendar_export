<?php
if (! defined('APP_VER')) {
    exit('No direct script access allowed');
}

require_once PATH_THIRD.'calendar_export/config.php';

/**
* Calendar export MCP
*
* @package  Calendar export
* @author   Andy Hebrank - NewCity Media
* @link     http://www.insidenewcity.com
*/

class Calendar_export_mcp
{

        // --------------------------------------------------------------------
        /**
        * PHP4 Constructor
        *
        * @see  __construct()
        */
    public function Calendar_export_mcp()
    {
        $this->__construct();
    }

    // --------------------------------------------------------------------

    /**
    * PHP 5 Constructor
    *
    * @return void
    */
    public function __construct()
    {
        // Republic variable theme folder
        $this->theme_url = ee()->config->item('theme_folder_url') . 'third_party/';
        if (defined('URL_THIRD_THEMES')) {
            $this->theme_url = URL_THIRD_THEMES;
        }

        // Add css+javascript for the module
        ee()->cp->add_to_head('<link rel="stylesheet" href="'.$this->theme_url.'calendar_export/css/main.css" type="text/css" media="screen" />');
   
        // module url
        $this->module_url = $this->data['mod_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=calendar_export';
    }

    // --------------------------------------------------------------------

    /**
    * Home page for module, lists all variables
    *
    * @return View
    */
    public function index()
    {

        // Title tag + breadcrumb
        $this->_cp_title('calendar_export_export');

        ee()->cp->set_breadcrumb(BASE . AMP . $this->module_url, ee()->lang->line('calendar_export_module_name'));

        ee()->load->library('javascript');
        ee()->cp->load_package_js('index');

        $vars = array();

        // $vars['groups_and_variables']     = ee()->republic_variables_model->get_groups_and_variables();
        // $vars['variables']                = ee()->republic_variables_model->get_groupless_variables();
        // $vars['groups']                   = ee()->republic_variables_model->get_empty_groups($vars['groups_and_variables']);
        // $vars['module_url']               = $this->module_url;
        // $vars['languages']                = $this->languages;
        // $vars['settings']                 = $this->settings;
        // $vars['ok_icon']                  = $this->theme_url.'republic_variables/images/ok.png';
        // $vars['not_ok_icon']              = $this->theme_url.'republic_variables/images/cancel.png';
        // $vars['module_access']            = $this->has_access();
        // $vars['variable_edit_action_url'] = BASE . AMP . $this->module_url . AMP . 'method=update_variable' . AMP . 'variable_id=';

        return $this->_render('index', $vars);
    }



    /***************************
    * RENDERING
    ****************************/

    private function _cp_title($title, $lang_value = true)
    {
        if (APP_VER < '2.6.0') {
            $title = ($lang_value) ? ee()->lang->line($title) : $title;
            ee()->cp->set_variable('cp_page_title', $title);
        } else {
            $title = ($lang_value) ? lang($title) : $title;
            ee()->view->cp_page_title = $title;
        }
    }

    /**
    * Render the view files, set the right navigation
    *
    * @return void
    */
    public function _render($view = "", $vars = array(), $nav_array = array())
    {

        // Navigation
        $navigation = array(
            ee()->lang->line('calendar_export_export')     => BASE.AMP.$this->module_url,
            //ee()->lang->line('republic_variables_variable_add')   => BASE.AMP.$this->module_url.AMP.'method=variable_action',
        );

        ee()->cp->set_right_nav(array_merge($navigation, $nav_array));

        return ee()->load->view($view, $vars, true);
    }
}
// END CLASS
