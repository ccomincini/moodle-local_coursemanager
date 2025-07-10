<?php

// This file is part of Moodle - http://moodle.org/
//.
// Moodle is free software: you can redistribute it and/or modify.
// it under the terms of the GNU General Public License as published by.
// the Free Software Foundation, either version 3 of the License, or.
// (at your option) any later version.
//.
// Moodle is distributed in the hope that it will be useful,.
// but WITHOUT ANY WARRANTY; without even the implied warranty of.
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//.
// You should have received a copy of the GNU General Public License.
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * English language strings for the Course Manager plugin.
 *
 * @package    local_coursemanager

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin name.
$string['pluginname'] = 'Course Manager';

// General strings.
$string['coursemanager'] = 'Course Manager';

// Settings and configuration.
$string['settings'] = 'Settings';

$string['privacy:metadata'] = 'The Course Manager plugin does not store any personal data.';


// Messaggi di successo.
$string['section_created_success'] = 'Section created successfully';
$string['section_updated_success'] = 'Section updated successfully';
$string['resource_created_success'] = 'URL resource created successfully';
$string['no_changes_requested'] = 'No changes requested';

// Messaggi di errore.
$string['course_not_found'] = 'Course not found with idnumber: {$a}';
$string['external_id_exists'] = 'External ID already exists for this course: {$a}';
$string['external_id_resource_exists'] = 'External ID for resource already exists in this course: {$a}';
$string['invalid_url'] = 'Invalid URL: "{$a}". Must be a valid HTTPS URL.';
$string['error_insert_url_table'] = 'Unable to insert into mod_url table';
$string['error_insert_coursemodule'] = 'Unable to insert into course_modules table';
$string['error_update_sequence'] = 'Unable to update section sequence';
$string['section_not_found_external'] = 'Section not found with external_id: {$a}';
$string['section_not_found'] = 'Section not found';
$string['course_not_found_simple'] = 'Course not found';

// Errori generici per eccezioni.
$string['errorcreatesection'] = 'Error creating section: {$a}';
$string['errorupdatesection'] = 'Error updating section: {$a}';
$string['errorcreateurlresource'] = 'Error creating URL resource: {$a}';
