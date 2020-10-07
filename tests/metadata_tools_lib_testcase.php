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
 * local metadata tools library
 *
 * @package    local_metadata tools
 * @author Céline Pervès <cperves@unistra.fr>
 * @copyright Université de Strasbourg 2020 {@link http://unistra.fr}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot.'/local/metadata/definelib.php');
require_once($CFG->dirroot.'/local/metadata/lib.php');
require_once($CFG->dirroot.'/local/metadata_tools/lib.php');
require_once($CFG->dirroot.'/local/metadata_tools/tests/mockdatas.php');

use local_metadata_tools\tests\mockdatas;

class local_metadata_tools_lib_testcase extends advanced_testcase {
    public function  test_get_all_metadata_values_from_contextid() {
        $this->setup_datas();
        $metadatascount = $this->create_metadas_values($this->mockdatas->get_cmresource1()->id);
        $metadatas = local_metadata_tools::get_all_metadata_values_from_contextid($this->mockdatas->get_resource_context1()->id);
        $this->assertCount($metadatascount, $metadatas);
    }
    public function  test_get_all_metadata_values_from_contextid_filtering() {
        $this->setup_datas();
        $metadatascount = $this->create_metadas_values($this->mockdatas->get_cmresource1()->id);
        $metadatas = local_metadata_tools::get_all_metadata_values_from_contextid($this->mockdatas->get_resource_context1()->id,
            '', ['Checkbox', 'Text input']);
        $this->assertNotCount($metadatascount, $metadatas);
        $this->assertCount(2, $metadatas);
    }

    public function  test_get_all_metadata_values_from_contextid_excluding() {
        $this->setup_datas();
        $metadatascount = $this->create_metadas_values($this->mockdatas->get_cmresource1()->id);
        $metadatas = local_metadata_tools::get_all_metadata_values_from_contextid($this->mockdatas->get_resource_context1()->id,
            '', [], ['Checkbox']);
        $this->assertNotCount($metadatascount, $metadatas);
        $this->assertCount($metadatascount - 1, $metadatas);
    }

    public function  test_get_all_metadata_values_from_contextid_prefixing() {
        $this->setup_datas();
        $metadatascount = $this->create_metadas_values($this->mockdatas->get_cmresource1()->id);
        $metadatas = local_metadata_tools::get_all_metadata_values_from_contextid($this->mockdatas->get_resource_context1()->id,
            'Text');
        $this->assertNotCount($metadatascount, $metadatas);
        $this->assertCount(2, $metadatas);
    }

    public function test_get_contextid_for_contextlevel_field_and_value() {
        $this->setup_datas();
        $this->create_metadas_values($this->mockdatas->get_cmresource1()->id);
        $metadatas = local_metadata_tools::get_contextids_for_contextlevel_field_and_value(
                CONTEXT_MODULE, 'Checkbox', 'value');
        $this->assertCount(1, $metadatas);
        $this->assertEquals($this->mockdatas->get_resource_context1()->id, array_keys($metadatas)[0]);
    }

    private function setup_mockdatas() {
        $this->mockdatas = new mockdatas($this->getDataGenerator());
    }
    private function setup_datas() {
        $this->resetAfterTest();
        $this->preventResetByRollback(); // Logging waits till the transaction gets committed.
        $this->setup_mockdatas();
        $this->setAdminUser();
        $this->mockdatas->setup_users();
        $this->mockdatas->create_course();
        $this->mockdatas->create_modules();
    }

    private function create_metadas_values($cmid) {
        $metadatacategory = $this->mockdatas->create_category();
        $datatypes = local_metadata_list_datatypes();
        $currentsortorder = 0;
        foreach ($datatypes as $datatype => $datatypename) {
            $currentsortorder++;
            $metadatafield =
                    $this->mockdatas->create_field($datatype, $datatypename, $metadatacategory->get_id(), $currentsortorder);
            $metadatavalue = new \local_metadata_tools\database\metadata_value;
            $data = new stdClass();
            $data->instanceid = $cmid;
            $data->fieldid = $metadatafield->get_id();
            $data->data = 'value';
            $metadatavalue->set_datas($data);
            $metadatavalue->save_datas();
        }
        return $currentsortorder;
    }
}