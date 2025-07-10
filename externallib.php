<?php

// ===============================================
// File: local/coursemanager/externallib.php - v1.0.9
// ===============================================
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . "/course/lib.php");
require_once($CFG->dirroot . "/course/modlib.php");
require_once($CFG->dirroot . "/mod/url/lib.php");

class local_coursemanager_external extends external_api
{
    /**
     * Parametri per create_section
     */
    public static function create_section_parameters()
    {
        return new external_function_parameters(
            array(
                'courseidnumber' => new external_value(PARAM_TEXT, 'ID number del corso'),
                'sectiontitle' => new external_value(PARAM_TEXT, 'Titolo della sezione'),
                'external_id' => new external_value(PARAM_TEXT, 'ID esterno per referenziare la sezione')
            )
        );
    }

    /**
     * Crea una sezione nel corso
     */
    public static function create_section($courseidnumber, $sectiontitle, $external_id)
    {
        global $DB, $CFG;

        // Validazione parametri
        $params = self::validate_parameters(self::create_section_parameters(), array(
            'courseidnumber' => $courseidnumber,
            'sectiontitle' => $sectiontitle,
            'external_id' => $external_id
        ));

        // Trova il corso tramite idnumber
        $course = $DB->get_record('course', array('idnumber' => $params['courseidnumber']));
        if (!$course) {
            throw new invalid_parameter_exception(
                get_string('course_not_found', 'local_coursemanager', $params['courseidnumber'])
            );
        }

        // Verifica che l'external_id non esista già per questo corso
        $existing = $DB->get_record('local_coursemanager_sections', array(
            'courseid' => $course->id,
            'external_id' => $params['external_id']
        ));
        if ($existing) {
            throw new invalid_parameter_exception(
                get_string('external_id_exists', 'local_coursemanager', $params['external_id'])
            );
        }

        // Verifica permessi
        $context = context_course::instance($course->id);
        self::validate_context($context);
        require_capability('moodle/course:update', $context);

        try {
            // Ottieni il numero di sezioni esistenti
            $sections = $DB->get_records('course_sections', array('course' => $course->id), 'section');
            $newsection = count($sections);

            // Crea la nuova sezione
            $section = new stdClass();
            $section->course = $course->id;
            $section->section = $newsection;
            $section->name = $params['sectiontitle'];
            $section->summary = '';
            $section->summaryformat = FORMAT_HTML;
            $section->sequence = '';
            $section->visible = 1;
            $section->availability = null;

            $sectionid = $DB->insert_record('course_sections', $section);

            // Crea il mapping nella tabella personalizzata
            $mapping = new stdClass();
            $mapping->courseid = $course->id;
            $mapping->sectionid = $sectionid;
            $mapping->external_id = $params['external_id'];
            $mapping->timecreated = time();
            $mapping->timemodified = time();

            $mappingid = $DB->insert_record('local_coursemanager_sections', $mapping);

            // Aggiorna il formato del corso
            $course->numsections = $newsection;
            $DB->update_record('course', $course);

            // Pulisci la cache del corso
            rebuild_course_cache($course->id);

            return array(
                'success' => true,
                'sectionid' => $sectionid,
                'sectionnumber' => $newsection,
                'external_id' => $params['external_id'],
                'mapping_id' => $mappingid,
                'message' => get_string('section_created_success', 'local_coursemanager')
            );
        } catch (Exception $e) {
            throw new moodle_exception('errorcreatesection', 'local_coursemanager', '', null, $e->getMessage());
        }
    }

    /**
     * Valore di ritorno per create_section
     */
    public static function create_section_returns()
    {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Successo operazione'),
                'sectionid' => new external_value(PARAM_INT, 'ID della sezione creata'),
                'sectionnumber' => new external_value(PARAM_INT, 'Numero della sezione'),
                'external_id' => new external_value(PARAM_TEXT, 'ID esterno della sezione'),
                'mapping_id' => new external_value(PARAM_INT, 'ID del mapping'),
                'message' => new external_value(PARAM_TEXT, 'Messaggio di risposta')
            )
        );
    }

    /**
     * Parametri per update_section
     */
    public static function update_section_parameters()
    {
        return new external_function_parameters(
            array(
                'courseidnumber' => new external_value(PARAM_TEXT, 'ID number del corso'),
                'external_id' => new external_value(PARAM_TEXT, 'ID esterno della sezione'),
                'sectiontitle' => new external_value(PARAM_TEXT, 'Nuovo titolo della sezione', VALUE_DEFAULT, null)
            )
        );
    }

    /**
     * Aggiorna una sezione usando external_id
     */
    public static function update_section($courseidnumber, $external_id, $sectiontitle = null)
    {
        global $DB;

        $params = self::validate_parameters(self::update_section_parameters(), array(
            'courseidnumber' => $courseidnumber,
            'external_id' => $external_id,
            'sectiontitle' => $sectiontitle
        ));

        // Trova il corso
        $course = $DB->get_record('course', array('idnumber' => $params['courseidnumber']));
        if (!$course) {
            throw new invalid_parameter_exception(
                get_string('course_not_found_simple', 'local_coursemanager')
            );
        }

        // Trova il mapping
        $mapping = $DB->get_record('local_coursemanager_sections', array(
            'courseid' => $course->id,
            'external_id' => $params['external_id']
        ));
        if (!$mapping) {
            throw new invalid_parameter_exception(
                get_string('section_not_found_external', 'local_coursemanager', $params['external_id'])
            );
        }

        // Verifica permessi
        $context = context_course::instance($course->id);
        self::validate_context($context);
        require_capability('moodle/course:update', $context);

        try {
            $updated = false;

            if (!empty($params['sectiontitle'])) {
                $DB->set_field('course_sections', 'name', $params['sectiontitle'], array('id' => $mapping->sectionid));
                $updated = true;
            }

            if ($updated) {
                $mapping->timemodified = time();
                $DB->update_record('local_coursemanager_sections', $mapping);
                rebuild_course_cache($course->id);
            }

            return array(
                'success' => true,
                'external_id' => $params['external_id'],
                'message' => $updated ?
                    get_string('section_updated_success', 'local_coursemanager') :
                    get_string('no_changes_requested', 'local_coursemanager')
            );
        } catch (Exception $e) {
            throw new moodle_exception('errorupdatesection', 'local_coursemanager', '', null, $e->getMessage());
        }
    }

    /**
     * Valore di ritorno per update_section
     */
    public static function update_section_returns()
    {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Successo operazione'),
                'external_id' => new external_value(PARAM_TEXT, 'ID esterno della sezione'),
                'message' => new external_value(PARAM_TEXT, 'Messaggio di risposta')
            )
        );
    }

    /**
     * Parametri per get_section_info
     */
    public static function get_section_info_parameters()
    {
        return new external_function_parameters(
            array(
                'courseidnumber' => new external_value(PARAM_TEXT, 'ID number del corso'),
                'external_id' => new external_value(PARAM_TEXT, 'ID esterno della sezione')
            )
        );
    }

    /**
     * Ottiene informazioni su una sezione
     */
    public static function get_section_info($courseidnumber, $external_id)
    {
        global $DB;

        $params = self::validate_parameters(self::get_section_info_parameters(), array(
            'courseidnumber' => $courseidnumber,
            'external_id' => $external_id
        ));

        // Trova il corso
        $course = $DB->get_record('course', array('idnumber' => $params['courseidnumber']));
        if (!$course) {
            throw new invalid_parameter_exception(
                get_string('course_not_found_simple', 'local_coursemanager')
            );
        }

        // Trova il mapping
        $mapping = $DB->get_record('local_coursemanager_sections', array(
            'courseid' => $course->id,
            'external_id' => $params['external_id']
        ));
        if (!$mapping) {
            throw new invalid_parameter_exception(
                get_string('section_not_found_external', 'local_coursemanager', $params['external_id'])
            );
        }

        // Ottieni la sezione
        $section = $DB->get_record('course_sections', array('id' => $mapping->sectionid));

        // Verifica permessi
        $context = context_course::instance($course->id);
        self::validate_context($context);
        require_capability('moodle/course:view', $context);

        return array(
            'sectionid' => $section->id,
            'sectionnumber' => $section->section,
            'sectionname' => $section->name,
            'external_id' => $mapping->external_id,
            'visible' => $section->visible,
            'timecreated' => $mapping->timecreated,
            'timemodified' => $mapping->timemodified
        );
    }

    /**
     * Valore di ritorno per get_section_info
     */
    public static function get_section_info_returns()
    {
        return new external_single_structure(
            array(
                'sectionid' => new external_value(PARAM_INT, 'ID della sezione'),
                'sectionnumber' => new external_value(PARAM_INT, 'Numero della sezione'),
                'sectionname' => new external_value(PARAM_TEXT, 'Nome della sezione'),
                'external_id' => new external_value(PARAM_TEXT, 'ID esterno'),
                'visible' => new external_value(PARAM_INT, 'Visibilità'),
                'timecreated' => new external_value(PARAM_INT, 'Timestamp creazione'),
                'timemodified' => new external_value(PARAM_INT, 'Timestamp modifica')
            )
        );
    }

    /**
     * Parametri per add_url_resource
     */
    public static function add_url_resource_parameters()
    {
        return new external_function_parameters(
            array(
                'courseidnumber' => new external_value(PARAM_TEXT, 'ID number del corso'),
                'section_external_id' => new external_value(PARAM_TEXT, 'ID esterno della sezione'),
                'resourcetitle' => new external_value(PARAM_TEXT, 'Titolo della risorsa'),
                'resourceurl' => new external_value(
                    PARAM_URL,
                    'URL della risorsa',
                    VALUE_DEFAULT,
                    'https://INSERISCI.QUI.IL/LINK_CORRETTO/AL_WEBINAR'
                ),
                'description' => new external_value(PARAM_TEXT, 'Descrizione della risorsa', VALUE_DEFAULT, ''),
                'external_id' => new external_value(PARAM_TEXT, 'ID esterno per la risorsa', VALUE_DEFAULT, '')
            )
        );
    }

    /**
     * Aggiunge una risorsa URL alla sezione - VERSIONE 1.0.9 MANUALE
     */
    public static function add_url_resource(
        $courseidnumber,
        $section_external_id,
        $resourcetitle,
        $resourceurl = 'https://INSERISCI.QUI.IL/LINK_CORRETTO/AL_WEBINAR',
        $description = '',
        $external_id = ''
    ) {
        global $DB, $CFG;

        // Validazione parametri
        $params = self::validate_parameters(self::add_url_resource_parameters(), array(
            'courseidnumber' => $courseidnumber,
            'section_external_id' => $section_external_id,
            'resourcetitle' => $resourcetitle,
            'resourceurl' => $resourceurl,
            'description' => $description,
            'external_id' => $external_id
        ));

        // Trova il corso tramite idnumber
        $course = $DB->get_record('course', array('idnumber' => $params['courseidnumber']));
        if (!$course) {
            throw new invalid_parameter_exception(
                get_string('course_not_found', 'local_coursemanager', $params['courseidnumber'])
            );
        }

        // Trova la sezione tramite external_id
        $mapping = $DB->get_record('local_coursemanager_sections', array(
            'courseid' => $course->id,
            'external_id' => $params['section_external_id']
        ));
        if (!$mapping) {
            throw new invalid_parameter_exception(
                get_string('section_not_found_external', 'local_coursemanager', $params['section_external_id'])
            );
        }

        // Ottieni la sezione
        $section = $DB->get_record('course_sections', array('id' => $mapping->sectionid));
        if (!$section) {
            throw new invalid_parameter_exception(
                get_string('section_not_found', 'local_coursemanager')
            );
        }

        // Verifica permessi
        $context = context_course::instance($course->id);
        self::validate_context($context);
        require_capability('moodle/course:manageactivities', $context);

        try {
            // Verifica se external_id è già utilizzato come cmidnumber - CONTROLLO MIGLIORATO
            if (!empty($params['external_id']) && trim($params['external_id']) !== '') {
                $clean_external_id = clean_param(trim($params['external_id']), PARAM_TEXT);

                // Controllo duplicati più specifico
                $existing_cm = $DB->get_record('course_modules', array(
                    'course' => $course->id,
                    'idnumber' => $clean_external_id
                ));

                if ($existing_cm) {
                    // Errore chiaro e specifico per duplicati - USA STRINGA LOCALIZZATA
                    throw new invalid_parameter_exception(
                        get_string('external_id_resource_exists', 'local_coursemanager', $clean_external_id)
                    );
                }

                // Controllo duplicati globali (opzionale ma utile)
                $existing_global = $DB->get_record('course_modules', array('idnumber' => $clean_external_id));
                if ($existing_global) {
                    throw new invalid_parameter_exception(
                        get_string(
                            'external_id_resource_exists',
                            'local_coursemanager',
                            $clean_external_id . ' (in corso ID: ' . $existing_global->course . ')'
                        )
                    );
                }
            }

            // APPROCCIO SICURO v1.0.9 - Creazione step by step con validazione

            // 1. Valida e pulisci i parametri PRIMA di inserire
            $clean_title = clean_param($params['resourcetitle'] . ' - LINK DA MODIFICARE!!!', PARAM_TEXT);
            $clean_description = clean_param($params['description'], PARAM_RAW);
            $clean_url = clean_param($params['resourceurl'], PARAM_URL);

            // Controllo URL valido
            if (empty($clean_url) || $clean_url !== $params['resourceurl']) {
                throw new invalid_parameter_exception(
                    get_string('invalid_url', 'local_coursemanager', $params['resourceurl'])
                );
            }

            // Debug: Log parametri validati
            debugging('Parametri validati: ' . json_encode(array(
                'title' => $clean_title,
                'url' => $clean_url,
                'external_id' => isset($clean_external_id) ? $clean_external_id : 'NOT_SET'
            )), DEBUG_DEVELOPER);

            // 2. Crea l'istanza URL nella tabella mod_url
            $url = new stdClass();
            $url->course = $course->id;
            $url->name = $clean_title;
            $url->intro = $clean_description;
            $url->introformat = FORMAT_HTML;
            $url->externalurl = $clean_url;
            $url->display = 0;
            $url->displayoptions = '';
            $url->parameters = '';
            $url->timemodified = time();

            $urlid = $DB->insert_record('url', $url);

            if (!$urlid) {
                throw new moodle_exception(
                    'errorcreateurlresource',
                    'local_coursemanager',
                    '',
                    null,
                    get_string('error_insert_url_table', 'local_coursemanager')
                );
            }

            // 3. Ottieni info sul modulo URL
            $module = $DB->get_record('modules', array('name' => 'url'), '*', MUST_EXIST);

            // 4. Crea il course module
            $cm = new stdClass();
            $cm->course = $course->id;
            $cm->module = $module->id;
            $cm->instance = $urlid;
            $cm->section = $section->section;
            $cm->visible = 1;
            $cm->visibleoncoursepage = 1;
            $cm->groupmode = 0;
            $cm->groupingid = 0;
            $cm->completion = 0;
            $cm->completionview = 0;
            $cm->completionexpected = 0;
            $cm->showdescription = 0;
            $cm->deletioninprogress = 0;
            $cm->added = time();

            // Aggiungi idnumber solo se fornito e validato
            if (!empty($params['external_id']) && trim($params['external_id']) !== '') {
                $cm->idnumber = $clean_external_id;
            }

            $cmid = $DB->insert_record('course_modules', $cm);

            if (!$cmid) {
                // Rollback: elimina URL creato
                $DB->delete_records('url', array('id' => $urlid));
                throw new moodle_exception(
                    'errorcreateurlresource',
                    'local_coursemanager',
                    '',
                    null,
                    get_string('error_insert_coursemodule', 'local_coursemanager')
                );
            }

            // 5. Aggiorna la sequenza della sezione
            $current_sequence = $section->sequence;
            $new_sequence = $current_sequence ? $current_sequence . ',' . $cmid : $cmid;

            $update_result = $DB->set_field('course_sections', 'sequence', $new_sequence, array('id' => $section->id));

            if (!$update_result) {
                // Rollback completo
                $DB->delete_records('course_modules', array('id' => $cmid));
                $DB->delete_records('url', array('id' => $urlid));
                throw new moodle_exception(
                    'errorcreateurlresource',
                    'local_coursemanager',
                    '',
                    null,
                    get_string('error_update_sequence', 'local_coursemanager')
                );
            }

            // 6. Pulisci cache
            rebuild_course_cache($course->id);

            return array(
                'success' => true,
                'cmid' => $cmid,
                'instanceid' => $urlid,
                'section_external_id' => $params['section_external_id'],
                'resource_external_id' => isset($clean_external_id) ? $clean_external_id : '',
                'message' => get_string('resource_created_success', 'local_coursemanager')
            );
        } catch (invalid_parameter_exception $e) {
            // Errori di validazione - già chiari, passali direttamente
            throw $e;
        } catch (Exception $e) {
            // Debug dettagliato per errori imprevisti
            $error_details = "Errore imprevisto: " . $e->getMessage();
            if (method_exists($e, 'getFile')) {
                $error_details .= " in " . $e->getFile() . " line " . $e->getLine();
            }

            throw new moodle_exception('errorcreateurlresource', 'local_coursemanager', '', null, $error_details);
        }
    }

    /**
     * Valore di ritorno per add_url_resource
     */
    public static function add_url_resource_returns()
    {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Successo operazione'),
                'cmid' => new external_value(PARAM_INT, 'ID del course module'),
                'instanceid' => new external_value(PARAM_INT, 'ID dell\'istanza URL'),
                'section_external_id' => new external_value(PARAM_TEXT, 'ID esterno della sezione'),
                'resource_external_id' => new external_value(PARAM_TEXT, 'ID esterno della risorsa'),
                'message' => new external_value(PARAM_TEXT, 'Messaggio di risposta')
            )
        );
    }
}
