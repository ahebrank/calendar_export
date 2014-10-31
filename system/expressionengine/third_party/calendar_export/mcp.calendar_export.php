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
        $this->_cp_title('calendar_export_export');
        ee()->cp->set_breadcrumb(BASE . AMP . $this->module_url, ee()->lang->line('calendar_export_module_name'));

        ee()->load->library('javascript');
        ee()->cp->load_package_js('index');

        $this->_inject_javascript();

        $vars = array();
        $vars['message'] = "";
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=calendar_export'.AMP.'method=index';
        $vars['fields'] = $this->_get_fields();

        $submit = ee()->input->post('submit');
        if ($submit) {
            $filter = ee()->input->post('filter');
            $fields_selected = ee()->input->post('fields');
            $events = $this->_get_events($filter, $fields_selected);
            if ($submit == "Export") {
                // helpful filenaming
                $label = array();
                if ($filter['from_date']) {
                    $label[] = "from-".$filter['from_date'];
                }
                if ($filter['to_date']) {
                    $label[] = "to-".$filter['to_date'];
                }
                if (!empty($label)) {
                    $label = "_" . implode("_", $label);
                }
                else {
                    $label = "";
                }
                if (!empty($events)) $this->_render_csv($events, $label);
            }
            $vars['events'] = $events;
            $vars['filter'] = $filter;
            $vars['fields_selected'] = $fields_selected;
        }

        return $this->_render('index', $vars);
    }

    /***************************
    * PROCESSING 
    ****************************/

    private function _get_fields() {
        // a list of all channel fields to show
        ee()->db->select('*');
        ee()->db->from('channel_fields');

        $allowed_fields = array('checkboxes', 'select', 'text', 'textarea');
        ee()->db->where_in('field_type', $allowed_fields);

        $fields = array();

        $results = ee()->db->get();
        if ($results->num_rows() > 0) {
            foreach ($results->result_array() as $row) {
                $fields[$row['field_id']] = $row['field_label'];
            }
        }

        return $fields;
    }

    private function _get_events($filter, $fields_selected) {

        ee()->db->select('*');
        ee()->db->from('calendar_events');

        // filter
        $where = array();
        if ($filter['from_date']) {
            $where['start_date >='] = $filter['from_date'];
        }
        if ($filter['to_date']) {
            $where['end_date <='] = $filter['to_date'];
        }
        if (!empty($where)) {
            ee()->db->where($where);
        }

        $results = ee()->db->get();
        
        if ($results->num_rows() > 0) {
            // collect entry_ids
            $entries = array('entries' => array());
            foreach ($results->result_array() as $row) {
                $entries['entries'][$row['entry_id']] = array(
                    'start_date' => $row['start_date'],
                    'start_time' => $row['start_time']? $row['start_time']:'',
                    'end_date' => $row['end_date'],
                    'end_time' => $row['end_time']? $row['end_time']:'');
            }

            $entries = $this->_get_entry_data($entries, $fields_selected);
            return $entries;
        }

        return false;
    }

    private function _get_entry_data($entries, $fields_selected) {
        // generate category lookup table
        ee()->db->select('*');
        ee()->db->from('categories');
        $results = ee()->db->get();
        $catmap = array();
        foreach ($results->result_array() as $row) {
            $catmap[$row['cat_id']] = $row['cat_name'];
        }

        // field name lookup
        $field_lookup = $this->_get_fields();
        $entries['field_lookup'] = array();
        if (!empty($fields_selected)) {
            foreach ($fields_selected as $id => $value) {
                if ($value) {
                    $entries['field_lookup'][$id] = $field_lookup[$id];
                }
            }
        }   

        // grab entry titles, etc.
        $ids = array_keys($entries['entries']);
        ee()->db->select('*');
        ee()->db->from('channel_titles t');
        ee()->db->join('channel_data d', 'd.entry_id = t.entry_id');
        ee()->db->where_in('t.entry_id', $ids);
        $results = ee()->db->get();

        foreach ($results->result_array() as $row) {
            $entries['entries'][$row['entry_id']]['title'] = $row['title'];

            // get categories
            ee()->db->select('*');
            ee()->db->from('category_posts');
            ee()->db->where('entry_id', $row['entry_id']);
            $cats = ee()->db->get();
            $catnames = array();
            foreach ($cats->result_array() as $catrow) {
                $catnames[] = $catmap[$catrow['cat_id']];
            }

            // stick a list of categories with each entry
            $entries['entries'][$row['entry_id']]['categories'] = implode(", ", $catnames);

            // other data
            if (!empty($fields_selected)) {
                foreach ($fields_selected as $id => $value) {
                    if ($value) {
                        $entries['entries'][$row['entry_id']]['data'][$id] = $row['field_id_'.$id];
                    }
                }
            }
        }

        return $entries;
    }


    /***************************
    * RENDERING
    ****************************/

    private function _inject_javascript() {
        ee()->cp->add_js_script(
            array('ui' => array(
                'core', 'datepicker'
            )
        ));
    }

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

    private function _quote_element($element) {
        $element = str_replace('"', '""', $element);
        return '"' . $element . '"';
    }

    private function _camelcase($element) {
        return $this->_quote_element(str_replace(" ", "", ucwords($element)));
    }

    private function _render_csv($events, $label) {
        // generate output
        $output = "EntryID,Title,Categories,StartDate,StartTime,EndDate,EndTime";
        if (!empty($events['field_lookup'])) {
            $headers = array_map(array($this, '_camelcase'), $events['field_lookup']);
            if (count($headers)) {
                $output .= ",".implode(",", $headers);
            }
        }
        $output .= "\n";

        foreach ($events['entries'] as $id=>$e) {
            $output_array = array($id,$e['title'],$e['categories'],
                        $e['start_date'],$e['start_time'],
                        $e['end_date'], $e['end_time']);

            if (!empty($events['field_lookup'])) {
                foreach ($events['field_lookup'] as $id => $tmp) {
                    $output_array[] = $e['data'][$id];
                }
            }   
            $output .= implode(",", array_map(array($this, '_quote_element'), $output_array))."\n"; 
        }
        
        // send csv attachment header
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=events_export".$label.".csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $output;
        exit();
    }

}
// END CLASS
