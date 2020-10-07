<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * metadata field database object
 *
 * @package    local_metadata tools
 * @author Céline Pervès <cperves@unistra.fr>
 * @copyright Université de Strasbourg 2020 {@link http://unistra.fr}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_metadata_tools\database;
defined('MOODLE_INTERNAL') || die();

class metadata_field extends \stdClass {
    private $id;
    private $contextlevel;
    private $shortname;
    private $name;
    private $datatype;
    private $description;
    private $descriptionformat = FORMAT_HTML;
    private $categoryid;
    private $sortorder;
    private $required = 0;
    private $locked = 0;
    private $visible = 1;
    private $forceunique = 0;
    private $signup = 0;
    private $defaultdata;
    private $defaultdataformat = FORMAT_MOODLE;
    private $param1;
    private $param2;
    private $param3;
    private $param4;
    private $param5;
    private $category = null;
    /**
     * metadata_field_object constructor.
     */

    public function __construct() {
    }

    public function get_id() {
        return $this->id;
    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function get_contextlevel() {
        return $this->contextlevel;
    }

    public function set_contextlevel($contextlevel) {
        $this->contextlevel = $contextlevel;
    }

    public function get_shortname() {
        return $this->shortname;
    }

    public function set_shortname($shortname) {
        $this->shortname = $shortname;
    }

    public function get_name() {
        return $this->name;
    }

    public function set_name($name) {
        $this->name = $name;
    }

    public function get_datatype() {
        return $this->datatype;
    }

    public function set_datatype($datatype) {
        $this->datatype = $datatype;
    }

    public function get_description() {
        return $this->description;
    }

    public function set_description($description) {
        $this->description = $description;
    }

    public function get_descriptionformat() {
        return $this->descriptionformat;
    }

    public function set_descriptionformat($descriptionformat) {
        $this->descriptionformat = $descriptionformat;
    }

    public function get_categoryid() {
        return $this->categoryid;
    }

    public function set_categoryid($categoryid) {
        $this->categoryid = $categoryid;
    }

    public function get_sortorder() {
        return $this->sortorder;
    }

    public function set_sortorder($sortorder) {
        $this->sortorder = $sortorder;
    }

    public function get_required() {
        return $this->required;
    }

    public function set_required($required) {
        $this->required = $required;
    }

    public function get_locked() {
        return $this->locked;
    }

    public function set_locked($locked) {
        $this->locked = $locked;
    }

    public function get_visible() {
        return $this->visible;
    }

    public function set_visible($visible) {
        $this->visible = $visible;
    }

    public function get_forceunique() {
        return $this->forceunique;
    }

    public function set_forceunique($forceunique) {
        $this->forceunique = $forceunique;
    }

    public function get_signup() {
        return $this->signup;
    }

    public function set_signup($signup) {
        $this->signup = $signup;
    }

    public function get_defaultdata() {
        return $this->defaultdata;
    }

    public function set_defaultdata($defaultdata) {
        $this->defaultdata = $defaultdata;
    }

    public function get_defaultdataformat() {
        return $this->defaultdataformat;
    }

    public function set_defaultdataformat($defaultdataformat) {
        $this->defaultdataformat = $defaultdataformat;
    }

    public function get_param1() {
        return $this->param1;
    }

    public function set_param1($param1) {
        $this->param1 = $param1;
    }

    public function get_param2() {
        return $this->param2;
    }

    public function set_param2($param2) {
        $this->param2 = $param2;
    }

    public function get_param3() {
        return $this->param3;
    }

    public function set_param3($param3) {
        $this->param3 = $param3;
    }

    public function get_param4() {
        return $this->param4;
    }

    public function set_param4($param4) {
        $this->param4 = $param4;
    }

    public function get_param5() {
        return $this->param5;
    }

    public function set_param5($param5) {
        $this->param5 = $param5;
    }

    public function get_category() {
        return $this->category;
    }

    public function set_category($category) {
        $this->category = $category;
    }

    public function set_datas($datas) {
        foreach ($datas as $key => $value) {
            $this->$key = $value;
        }
    }
    public function save_datas() {
        global $DB;
        $record = false;
        if (!empty($this->id)) {
            $record = $DB->get_record('local_metadata_field', array('id' => $this->id));
        }
        if (!$record) {
            // Check that shortname not exists.
            if (! is_null($this->shortname)) {
                if ($result = $DB->get_record('local_metadata_field', array('shortname' => $this->shortname) )) {
                    print_error('local_metadata_tools metadata_field error, required shortname already exists');
                }
            } else {
                print_error('local_metadata_tools metadata_field error, required shortname not defined');
            }
            $this->id = $DB->insert_record('local_metadata_field', get_object_vars($this));
        } else {
            $this->id = $record->id;
            $DB->update_record('local_metadata_field', get_object_vars($this));
        }
        // Fill $category object.
        $this->category = metadata_category::load_datas($this->categoryid);
    }

    private function load($fieldid) {
        global $DB;
        $record = $DB->get_record('local_metadata_field', array('id' => $fieldid));
        if ($record) {
            foreach ($record as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public static function load_datas($id) {
        $metadatafield = new metadata_field();
        $metadatafield->load($id);
        if (! is_null($metadatafield->id)) {
            // Load category object.
            $metadatafield->category = metadata_category::load_datas($metadatafield->get_categoryid());
            return $metadatafield;
        }
        return null;
    }

    public static function load_datas_from_name($fieldname) {
        global $DB;
        $record = $DB->get_record('local_metadata_field', array('shortname' => $fieldname));
        if ($record) {
            return self::load_datas($record->id);
        }
        return null;
    }

    public static function get_max_sortorder() {
        global $DB;
        $record = $DB->get_record_sql('select max(sortorder) as maxsortorder from {local_metadata_field}');
        if ($record) {
            return $record->maxsortorder;
        }
         return 0;

    }

}