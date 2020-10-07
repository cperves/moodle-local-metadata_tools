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
 * CLI create  metadata field for module context
 *
 * Notes:
 *   - it is required to use the web server account when executing PHP CLI scripts
 *   - you need to change the "www-data" to match the apache user account
 *   - use "su" if "sudo" not available
 *
 * @package    local_metadata_tools
 * @author 2020 Celine Perves cperves@unistra.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once("$CFG->libdir/clilib.php");
require_once("$CFG->dirroot/local/metadata_tools/lib.php");


// Define the input options.
$longparams = array(
        'help' => false,
        'field' => '',
        'category' => '' ,
        'contextlevel' => '',
        'datatype' => '',
        'sortorder' => '',
        'dname' => '',
        'param1' => ''
);

$shortparams = array(
        'h' => 'help',
        'f' => 'field',
        'c' => 'category',
        'x' => 'contextlevel',
        'd' => 'datatype',
        's' => 'sortorder',
        'd' => 'dname',
        'p' => 'param1'
);

// Now get cli options.
list($options, $unrecognized) = cli_get_params($longparams, $shortparams);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help = "Create a new metadata field for local metadata field in a particular context

Options:
-v, --verbose         Print verbose progess information
-h, --help            Print out this help
-f, -field            Field name
-c, --category         An existing local metadata category id
-x, --contextlevel    A contextlevel number (40,50,70,...)
-d, --datatype        checkbox,datetime,menu,text or textarea
-s, --sortorder       sortorder, 0 if last one
-d,--dname            field display name
-p, --param1          param1 filling

Example:
\$ sudo -u www-data /usr/bin/php /var/www/moodle/local/digital_training_account_services/cli/add_metadatafield.php"
    ." --category=1 --field=myfield --contextlevel=50 -- datatype=checkbox --sortorder=1";

    echo $help;
    die;
}
$category = $options['category'];
if ($category == '' ) {
    cli_heading('local metadata category id');
    $prompt = "Enter an existing local metadata category id";
    $category = cli_input($prompt);
}

$datatype = $options['datatype'];
if ($datatype == '' ) {
    cli_heading('local metadata datatype');
    $prompt = "Enter a datatype (checkbox,datetime,menu,text or textarea)";
    $datatype = cli_input($prompt);
}

$sortorder = $options['sortorder'];
if ($sortorder == '' ) {
    cli_heading('local metadata sortorder');
    $prompt = "Enter sortorder (0 means last element NOT implemented yet)";
    $sortorder = cli_input($prompt);
}

$field = $options['field'];
if ($field == '' ) {
    cli_heading('local metadata field name');
    $prompt = "Enter a new field name";
    $field = cli_input($prompt);
}
$dname = $options['dname'];
if ($dname == '' ) {
    cli_heading('local metadata field display name');
    $prompt = "Enter the new field display name";
    $dname = cli_input($prompt);
}

$contextlevel = $options['contextlevel'];
if ($contextlevel == '' ) {
    cli_heading('local metadata contextlevel');
    $prompt = "Enter a context level name (40,50,70,...)";
    $contextlevelname = cli_input($prompt);
}
$param1 = $options['param1'];

$result = local_metadata_tools::add_metadata_field($field, $dname, $category, $contextlevel, $datatype, $param1, $sortorder);

if (!empty($result)) {
    cli_writeln('local metadata field created with id '.$result);
} else {
    cli_error('local metadata field not created');
}

exit(1);