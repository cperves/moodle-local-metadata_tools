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
 * local metadata tools maping tests
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
require_once($CFG->dirroot.'/local/metadata_tools/classes/database/metadata_field.php');
require_once($CFG->dirroot.'/local/metadata_tools/classes/database/metadata_value.php');
require_once($CFG->dirroot.'/local/metadata_tools/tests/mockdatas.php');

use local_metadata_tools\tests\mockdatas;


class local_metadata_tools_database_object_testcase extends advanced_testcase {

    public function  test_metadata_category_objects() {
        global $DB;
        $this->resetAfterTest();
        $this->preventResetByRollback();
        $this->setup_mockdatas();
        $metadatacategory = $this->mockdatas->create_category();
        $metadatacategoryread = \local_metadata_tools\database\metadata_category::load_datas($metadatacategory->get_id());
        $this->assertEquals(CONTEXT_MODULE, $metadatacategoryread->get_contextlevel());
        $this->assertEquals('category', $metadatacategoryread->get_name());
        $this->assertEquals(1, $metadatacategoryread->get_sortorder());
        $fields = $DB->get_records('local_metadata_category');
        $this->assertNotNull($fields);
        $this->assertCount(1, $fields);
    }

    public function  test_metadata_field_objects() {
        global $DB;
        $this->resetAfterTest();
        $this->preventResetByRollback();
        $this->setup_mockdatas();
        $metadatacategory = $this->mockdatas->create_category();
        $datatypes = local_metadata_list_datatypes();
        $currentsortorder = 0;
        foreach ($datatypes as $datatype => $datatypename) {
            $currentsortorder++;
            $metadatafield = $this->mockdatas->create_field($datatype, $datatypename,
                $metadatacategory->get_id(), $currentsortorder);
            $metadatafieldread = \local_metadata_tools\database\metadata_field::load_datas($metadatafield->get_id());
            $this->assertEquals($datatype, $metadatafieldread->get_datatype());
            $this->assertEquals($datatypename, $metadatafieldread->get_shortname());
            $this->assertEquals($datatypename, $metadatafieldread->get_name());
            $this->assertEquals($metadatacategory->get_id(), $metadatafieldread->get_categoryid());
            $this->assertEquals(CONTEXT_MODULE, $metadatafieldread->get_contextlevel());
            $this->assertEquals($currentsortorder,  $metadatafieldread->get_sortorder());
            $this->assertEquals($metadatacategory, $metadatafieldread->get_category());
        }
        $fields = $DB->get_records('local_metadata_field');
        $this->assertNotNull($fields);
        $this->assertCount(count($datatypes), $fields);
    }
    public function  test_metadata_value_objects() {
        global $DB;
        $this->setup_datas();
        $metadatacategory = $this->mockdatas->create_category();
        $datatypes = local_metadata_list_datatypes();
        $currentsortorder = 0;
        foreach ($datatypes as $datatype => $datatypename) {
            $currentsortorder++;
            $metadatafield = $this->mockdatas->create_field($datatype, $datatypename,
                $metadatacategory->get_id(), $currentsortorder);
            $metadatavalue = new \local_metadata_tools\database\metadata_value;
            $data = new stdClass();
            $data->instanceid = $this->mockdatas->get_cmresource1()->id;
            $data->fieldid = $metadatafield->get_id();
            $data->data = 'value';
            $metadatavalue->set_datas($data);
            $metadatavalue->save_datas();
            $metadatavalueread = \local_metadata_tools\database\metadata_value::load_datas($metadatavalue->get_id());
            $this->assertEquals($this->mockdatas->get_cmresource1()->id, $metadatavalueread->get_instanceid());
            $this->assertEquals($metadatavalue->get_id(), $metadatavalueread->get_id());
            $this->assertEquals('value', $metadatavalueread->get_data());
            $this->assertEquals($metadatafield, $metadatavalueread->get_field());
        }
        $fields = $DB->get_records('local_metadata');
        $this->assertNotNull($fields);
        $this->assertCount(count($datatypes), $fields);
    }
    private function setup_mockdatas() {
        $this->mockdatas = new mockdatas($this->getDataGenerator());
    }
    private function setup_datas() {
        $this->resetAfterTest();
        $this->preventResetByRollback(); // Logging waits till the transaction gets committed.
        $this->setup_mockdatas();
        $this->setAdminUser();
        $this->setup_mockdatas();
        $this->mockdatas->setup_users();
        $this->mockdatas->create_course();
        $this->mockdatas->create_modules();
    }


}