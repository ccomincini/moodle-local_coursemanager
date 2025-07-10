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
 * Italian language strings
 *
 * @package    local_coursemanager
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Gestione Corsi';

$string['coursemanager'] = 'Gestione Corsi';

$string['privacy:metadata'] = 'Il plugin Gestione Corsi non memorizza dati personali.';

// Messaggi di successo.
$string['section_created_success'] = 'Sezione creata con successo';
$string['section_updated_success'] = 'Sezione aggiornata con successo';
$string['resource_created_success'] = 'Risorsa URL creata con successo';
$string['no_changes_requested'] = 'Nessuna modifica richiesta';

// Messaggi di errore.
$string['course_not_found'] = 'Corso non trovato con idnumber: {$a}';
$string['external_id_exists'] = 'External ID già esistente per questo corso: {$a}';
$string['external_id_resource_exists'] = 'External ID per risorsa già esistente in questo corso: {$a}';
$string['invalid_url'] = 'URL non valido: "{$a}". Deve essere un URL HTTPS valido.';
$string['error_insert_url_table'] = 'Impossibile inserire nella tabella mod_url';
$string['error_insert_coursemodule'] = 'Impossibile inserire nella tabella course_modules';
$string['error_update_sequence'] = 'Impossibile aggiornare sequenza sezione';
$string['section_not_found_external'] = 'Sezione non trovata con external_id: {$a}';
$string['section_not_found'] = 'Sezione non trovata';
$string['course_not_found_simple'] = 'Corso non trovato';

// Errori generici per eccezioni.
$string['errorcreatesection'] = 'Errore nella creazione della sezione: {$a}';
$string['errorupdatesection'] = 'Errore nell\'aggiornamento della sezione: {$a}';
$string['errorcreateurlresource'] = 'Errore nella creazione della risorsa URL: {$a}';
