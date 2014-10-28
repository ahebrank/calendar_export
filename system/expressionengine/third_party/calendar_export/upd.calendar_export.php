<?php
if (! defined('APP_VER')) {
    exit('No direct script access allowed');
}

require_once PATH_THIRD.'calendar_export/config.php';

/**
* Calendar export Install and update class
*
* @package  Calendar export
* @author   Andy Hebrank - NewCity Media
* @link     http://www.insidenewcity.com
*/
class Calendar_export_upd
{

    /**
    * Version number
    *
    * @var  string
    */
    public $version = CALENDAR_EXPORT_VERSION;

    // --------------------------------------------------------------------

    /**
    * PHP4 Constructor
    *
    * @see  __construct()
    */
    public function Calendar_export_upd()
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
        // set module name
        $this->name = str_replace('_upd', '', ucfirst(get_class($this)));
    }

    // --------------------------------------------------------------------

    /**
    * Install the module
    *
    * @return bool
    */
    public function install()
    {
        ee()->db->insert(
            'modules',
            array(
                'module_name'        => $this->name,
                'module_version'     => $this->version,
                'has_cp_backend'     => 'y',
                'has_publish_fields' => 'n'
            )
        );

        return true;
    }

    // --------------------------------------------------------------------

    /**
    * Uninstall the module
    *
    * @return bool
    */
    public function uninstall()
    {
        ee()->load->dbforge();

        // get module id
        ee()->db->select('module_id');
        ee()->db->from('exp_modules');
        ee()->db->where('module_name', $this->name);
        $query = ee()->db->get();

        // remove references from module_member_groups
        ee()->db->where('module_id', $query->row('module_id'));
        ee()->db->delete('module_member_groups');

        // remove references from modules
        ee()->db->where('module_name', $this->name);
        ee()->db->delete('modules');

        return true;
    }

    // --------------------------------------------------------------------

    /**
    * Update the module
    *
    * @return bool
    */
    public function update($current = '')
    {
        return true;
    }

  
}

// END CLASS
