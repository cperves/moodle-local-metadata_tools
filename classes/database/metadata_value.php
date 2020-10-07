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

class metadata_value{
    private $id;
    private $instanceid;
    private $fieldid;
    private $data;
    private $dataformat = FORMAT_MOODLE;
    private $field = null;

    /**
     * @return mixed
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function set_id($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function get_instanceid() {
        return $this->instanceid;
    }

    /**
     * @param mixed $instanceid
     */
    public function set_instanceid($instanceid) {
        $this->instanceid = $instanceid;
    }

    /**
     * @return mixed
     */
    public function get_fieldid() {
        return $this->fieldid;
    }

    /**
     * @param mixed $fieldid
     */
    public function set_fieldid($fieldid) {
        $this->fieldid = $fieldid;
    }

    /**
     * @return mixed
     */
    public function get_data() {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function set_data($data) {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function get_dataformat() {
        return $this->dataformat;
    }

    /**
     * @param mixed $dataformat
     */
    public function set_dataformat($dataformat) {
        $this->dataformat = $dataformat;
    }

    /**
     * @return null
     */
    public function get_field() {
        return $this->field;
    }

    /**
     * @param null $metadatafield
     */
    public function set_field($metadatafield) {
        $this->metadatafield = $metadatafield;
    }



    public function __construct() {
    }


    public function set_datas($datas) {
        foreach ($datas as $key => $value) {
            $this->$key = $value;
        }
    }
    public function save_datas() {
        global $DB;
        $this->id = $DB->insert_record('local_metadata', get_object_vars($this));
        // Load field object.
        $this->field = metadata_field::load_datas($this->fieldid);
    }

    private function load($id) {
        global $DB;
        $record = $DB->get_record('local_metadata', array('id' => $id));
        if ($record) {
            foreach ($record as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public static function load_datas($id) {
        $metadatavalue = new metadata_value();
        $metadatavalue->load($id);
        if (!is_null($metadatavalue->id)) {
            // Load metadatafield object.
            $metadatavalue->field = metadata_field::load_datas($metadatavalue->get_fieldid());
            return $metadatavalue;
        }
        return null;
    }
}