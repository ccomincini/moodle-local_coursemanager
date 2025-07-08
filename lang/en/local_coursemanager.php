<?php
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Course Manager';
$string['privacy:metadata'] = 'The Course Manager plugin does not store any personal data.';

// Messaggi di successo
$string['section_created_success'] = 'Section created successfully';
$string['section_updated_success'] = 'Section updated successfully';
$string['resource_created_success'] = 'URL resource created successfully';
$string['no_changes_requested'] = 'No changes requested';

// Messaggi di errore
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

// Errori generici per eccezioni
$string['errorcreatesection'] = 'Error creating section: {$a}';
$string['errorupdatesection'] = 'Error updating section: {$a}';
$string['errorcreateurlresource'] = 'Error creating URL resource: {$a}';
