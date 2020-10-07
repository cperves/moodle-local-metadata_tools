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
 * metadata category database object
 *
 * @package    local_metadata tools
 * @author Céline Pervès <cperves@unistra.fr>
 * @copyright Université de Strasbourg 2020 {@link http://unistra.fr}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_metadata_tools\database;
defined('MOODLE_INTERNAL') || die();

class metadata_category{
    private $id;
    private $contextlevel;
    private $name;
    private $sortorder;

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
    public function get_contextlevel() {
        return $this->contextlevel;
    }

    /**
     * @param mixed $contextlevel
     */
    public function set_contextlevel($contextlevel) {
        $this->contextlevel = $contextlevel;
    }

    /**
     * @return mixed
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function set_name($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function get_sortorder() {
        return $this->sortorder;
    }

    /**
     * @param mixed $sortorder
     */
    public function set_sortorder($sortorder) {
        $this->sortorder = $sortorder;
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
        $this->id = $DB->insert_record('local_metadata_category', get_object_vars($this));
    }

    private function load($id) {
        global $DB;
        $record = $DB->get_record('local_metadata_category', array('id' => $id));
        if ($record) {
            foreach ($record as $key => $value) {
                $this->$key = $value;
            }
        }
    }
    public static function load_datas($id) {
        $metadatacategory = new metadata_category();
        $metadatacategory->load($id);
        if (! is_null($metadatacategory->id)) {
            return $metadatacategory;
        }
        return null;
    }
    public static function get_max_sortorder() {
        global $DB;
        $record = $DB->get_records_sql('select max(sortorder) as maxsortorder from {local_metadata_category}');
        if (!$record) {
            return 0;
        }
        return $record->maxsortorder;

    }
}