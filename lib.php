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
require_once($CFG->dirroot.'/local/metadata/definelib.php');
require_once($CFG->dirroot.'/local/metadata/lib.php');

class local_metadata_tools  {

    /**
     * @param $contextid
     * @param $filters an array of fieldname to filter results
     * @return array
     * @throws dml_exception
     */
    public static function get_all_metadata_values_from_contextid($contextid, $metadataprefix='', $filters=[], $excludedfields=[]) {
        global $DB;
        $metadatas = [];
        $insql = '';
        $additionalsql = '';
        $inparams = [];
        if (count($filters)) {
            list($insql, $inparams) = $DB->get_in_or_equal($filters, SQL_PARAMS_NAMED);
            $additionalsql .= ' and lmf.shortname '.$insql;
        }
        $additionalsqlexclusion = '';
        $inparamsexlusion = [];
        if (count($excludedfields)) {
            list($insql, $inparamsexlusion) = $DB->get_in_or_equal($excludedfields, SQL_PARAMS_NAMED, 'paramexlusion', false);
            $additionalsqlexclusion .= ' and lmf.shortname '.$insql;
        }
        $additionalsqlprefix = '';
        if (!empty($metadataprefix)) {
            $additionalsqlprefix = " and lmf.shortname like '$metadataprefix%'";
        }
        $sql = 'select lm.id as id from {local_metadata} lm '
            .'inner join {local_metadata_field} lmf on lmf.id=lm.fieldid '
            .'inner join {context} ctx on ctx.contextlevel=lmf.contextlevel and ctx.instanceid=lm.instanceid '
            .'where ctx.id=:contextid'.$additionalsql.$additionalsqlprefix.$additionalsqlexclusion;
        $params = array('contextid' => $contextid);
        $params = array_merge($params, $inparams, $inparamsexlusion);
        $records = $DB->get_records_sql($sql, $params);
        foreach ($records as $record) {
            $metadatas[$record->id] = \local_metadata_tools\database\metadata_value::load_datas($record->id);
        }
        return $metadatas;
    }

    /**
     * return all contextid asosciated with a particular contextlevel, a given field and a given value
     * @param $contextlevel
     * @param $fieldshortname
     * @param $value
     */
    public static function get_contextids_for_contextlevel_field_and_value($contextlevel, $fieldshortname, $value) {
        global $DB;
        $params = array(
                'fieldshortname' => $fieldshortname,
                'contextlevel' => $contextlevel,
                'value' => $value
        );
        $sql = 'select ctx.id, ctx.instanceid from {local_metadata} lm '
            .'inner join {local_metadata_field} lmf on lmf.id=lm.fieldid '
            .'inner join {context} ctx on ctx.contextlevel=lmf.contextlevel and ctx.instanceid=lm.instanceid '
            .'where ctx.contextlevel=:contextlevel and lm.data=:value and lmf.shortname=:fieldshortname';
        return $DB->get_records_sql($sql, $params);
    }

    public static function add_metadata_category($category, $contextlevel, $sortorder) {
        if (empty($sortorder)) {
            $sortorder = \local_metadata_tools\database\metadata_category::get_max_sortorder() + 1;
        }
        $metadatacategory = new \local_metadata_tools\database\metadata_category;
        $data = new \stdClass();
        $data->contextlevel = $contextlevel;
        $data->name = $category;
        $data->sortorder = $sortorder;
        $data->contextlevel = $contextlevel;
        $metadatacategory->set_datas($data);
        $metadatacategory->save_datas();
        return $metadatacategory->get_id();
    }

    public static function add_metadata_field($field, $dname, $category, $contextlevel, $datatype, $param1, $sortorder=0) {
        if (empty($sortorder)) {
            $sortorder = \local_metadata_tools\database\metadata_field::get_max_sortorder() + 1;
        }
        $metadatafield = new \local_metadata_tools\database\metadata_field;
        $data = new \stdClass();
        $data->shortname = $field;
        $data->name = $dname;
        $data->datatype = $datatype;
        $data->contextlevel = CONTEXT_MODULE;
        $data->categoryid = $category;
        $data->sortorder = $sortorder;
        $data->param1 = $param1;
        $metadatafield->set_datas($data);
        $metadatafield->save_datas();
        return $metadatafield->get_id();
    }
}
