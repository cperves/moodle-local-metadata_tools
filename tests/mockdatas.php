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
 * local_metadatas mockdatas for tests.
 *
 * @package    local_metadata_tools
 * @copyright  2020 Université de Strasbourg {@link https://unistra.fr}
 * @author  Céline Pervès <cperves@unistra.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_metadata_tools\tests;
defined('MOODLE_INTERNAL') || die();

class mockdatas {
    private $user1;
    private $user2;
    private $course1;
    private $resource1;
    private $resourcecontext1;
    private $cmresource1;
    private $datagenerator;

    /**
     * moockdatas constructor.
     */
    public function __construct($datagenerator) {
        $this->datagenerator = $datagenerator;

    }

    private function get_data_generator() {
        return $this->datagenerator;
    }

    private function set_user($userid) {
        \advanced_testcase::set_user($userid);
    }
    private static function set_admin_user() {
        \advanced_testcase::set_user(2);
    }

    public function setup_users() {
        $this->user1 = $this->get_data_generator()->create_user();
    }


    public function create_modules($options = array()) {
        $this->resource1 = $this->get_data_generator()->create_module('resource', array('course' => $this->course1) + $options);
        $this->resourcecontext1 = \context_module::instance($this->resource1->cmid);
        $this->cmresource1 = get_coursemodule_from_instance('resource', $this->resource1->id);
        // Retrieve log_manager and flush to trigger events.
        get_log_manager(true);
    }

    public function create_course($options = array()) {
        $this->course1 = $this->get_data_generator()->create_course($options);
        // Retrieve log_manager and flush to trigger events.
        get_log_manager(true);
    }

    /**
     * @return mixed
     */
    public function get_user1() {
        return $this->user1;
    }

    /**
     * @return mixed
     */
    public function get_user2() {
        return $this->user2;
    }

    /**
     * @return mixed
     */
    public function get_course1() {
        return $this->course1;
    }

    /**
     * @return mixed
     */
    public function get_resource1() {
        return $this->resource1;
    }

    /**
     * @return mixed
     */
    public function get_resource_context1() {
        return $this->resourcecontext1;
    }

    /**
     * @return mixed
     */
    public function get_cmresource1() {
        return $this->cmresource1;
    }

    /**
     * @return \local_metadata_tools\database\metadata_category
     */
    public static function create_category() {
        $metadatacategory = new \local_metadata_tools\database\metadata_category;
        $data = new \stdClass();
        $data->contextlevel = CONTEXT_MODULE;
        $data->name = 'category';
        $data->sortorder = 1;
        $data->contextlevel = CONTEXT_MODULE;
        $metadatacategory->set_datas($data);
        $metadatacategory->save_datas();
        return $metadatacategory;
    }

    /**
     * @param $datatype
     * @param $datatypename
     * @param  $categoryid
     * @param int $currentsortorder
     * @return \local_metadata_tools\database\metadata_field
     */
    public static function create_field($datatype, $datatypename, $categoryid, int $sortorder) {
        $metadatafield = new \local_metadata_tools\database\metadata_field;
        $data = new \stdClass();
        $data->shortname = $datatypename;
        $data->name = $datatypename;
        $data->datatype = $datatype;
        $data->contextlevel = CONTEXT_MODULE;
        $data->categoryid = $categoryid;
        $data->sortorder = $sortorder;
        $metadatafield->set_datas($data);
        $metadatafield->save_datas();
        return $metadatafield;
    }
}

