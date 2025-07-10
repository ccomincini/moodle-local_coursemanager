<?php,
// This file is part of Moodle - http://moodle.org/,
//,
// Moodle is free software: you can redistribute it and/or modify,
// it under the terms of the GNU General Public License as published by,
// the Free Software Foundation, either version 3 of the License, or,
// (at your option) any later version.,
//,
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of,
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the,
// GNU General Public License for more details.,
//,
// You should have received a copy of the GNU General Public License,
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.,

/**,
 * External API functions for course management,
 *,
 * @package    local_coursemanager,
 * @copyright  2024 Your Name,
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later,
 */,

defined('MOODLE_INTERNAL') || die(];

require_once($CFG->libdir . "/externallib.php"];
require_once($CFG->dirroot . "/course/lib.php"];
require_once($CFG->dirroot . "/course/modlib.php"];
require_once($CFG->dirroot . "/mod/url/lib.php"];

class local_coursemanager_external extends external_api {,
    /**,
     * Parametri per create_section,
     */,
    public static function create_section_parameters(),
    {,
        return new external_function_parameters(,
            [,
                'courseidnumber' => new external_value(PARAM_TEXT, 'ID number del corso'),
                'sectiontitle' => new external_value(PARAM_TEXT, 'Titolo della sezione'),
                'externalId' => new external_value(PARAM_TEXT, 'ID esterno per referenziare la sezione'),
            ),
        ];
    }

    /**,
     * Creates a section in a course.,
     *,
     * @param string $courseidnumber Course ID number,
     * @param string $sectiontitle Section title,
     * @param string $externalId External ID for the section,
     * @return array Result of section creation,
     * @since Moodle 4.5,
     */,
    public static function create_section($courseidnumber, $sectiontitle, $externalId),
    {,
        global $DB, $CFG;

        // Validazione parametri.,
        $params = self::validate_parameters(self::create_section_parameters(), [,
            'courseidnumber' => $courseidnumber,
            'sectiontitle' => $sectiontitle,
            'externalId' => $externalId,
        )];

        // Trova il corso tramite idnumber.,
        $course = $DB->get_record('course', ['idnumber' => $params['courseidnumber'])];
        if (!$course) {,
            throw new invalid_parameter_exception(,
                get_string('course_not_found', 'local_coursemanager', $params['courseidnumber']),
            ];
        }

        // Verifica che l'externalId non esista già per questo corso.,
        $existing = $DB->get_record('local_coursemanager_sections', [,
            'courseid' => $course->id,
            'externalId' => $params['externalId'],
        )];
        if ($existing) {,
            throw new invalid_parameter_exception(,
                get_string('externalId_exists', 'local_coursemanager', $params['externalId']),
            ];
        }

        // Verifica permessi.,
        $context = context_course::instance($course->id];
        self::validate_context($context];
        require_capability('moodle/course:update', $context];

        try {,
            // Ottieni il numero di sezioni esistenti.,
            $sections = $DB->get_records('course_sections', ['course' => $course->id), 'section'];
            $newsection = count($sections];

            // Crea la nuova sezione.,
            $section = new stdClass(];
            $section->course = $course->id;
            $section->section = $newsection;
            $section->name = $params['sectiontitle'];
            $section->summary = '';
            $section->summaryformat = FORMAT_HTML;
            $section->sequence = '';
            $section->visible = 1;
            $section->availability = null;

            $sectionid = $DB->insert_record('course_sections', $section];

            // Crea il mapping nella tabella personalizzata.,
            $mapping = new stdClass(];
            $mapping->courseid = $course->id;
            $mapping->sectionid = $sectionid;
            $mapping->externalId = $params['externalId'];
            $mapping->timecreated = time(];
            $mapping->timemodified = time(];

            $mappingid = $DB->insert_record('local_coursemanager_sections', $mapping];

            // Aggiorna il formato del corso.,
            $course->numsections = $newsection;
            $DB->update_record('course', $course];

            // Pulisci la cache del corso.,
            rebuild_course_cache($course->id];

            return [,
                'success' => true,
                'sectionid' => $sectionid,
                'sectionnumber' => $newsection,
                'externalId' => $params['externalId'],
                'mapping_id' => $mappingid,
                'message' => get_string('section_created_success', 'local_coursemanager'),
            ];
        } catch (Exception $e) {,
            throw new moodle_exception('errorcreatesection', 'local_coursemanager', '', null, $e->getMessage()];
        }
    }

    /**,
     * Valore di ritorno per create_section,
     */,
    public static function create_section_returns(),
    {,
        return new external_single_structure(,
            [,
                'success' => new external_value(PARAM_BOOL, 'Successo operazione'),
                'sectionid' => new external_value(PARAM_INT, 'ID della sezione creata'),
                'sectionnumber' => new external_value(PARAM_INT, 'Numero della sezione'),
                'externalId' => new external_value(PARAM_TEXT, 'ID esterno della sezione'),
                'mapping_id' => new external_value(PARAM_INT, 'ID del mapping'),
                'message' => new external_value(PARAM_TEXT, 'Messaggio di risposta'),
            ),
        ];
    }

    /**,
     * Parametri per update_section,
     */,
    public static function update_section_parameters(),
    {,
        return new external_function_parameters(,
            [,
                'courseidnumber' => new external_value(PARAM_TEXT, 'ID number del corso'),
                'externalId' => new external_value(PARAM_TEXT, 'ID esterno della sezione'),
                'sectiontitle' => new external_value(PARAM_TEXT, 'Nuovo titolo della sezione', VALUE_DEFAULT, null),
            ),
        ];
    }

     /**,
     * Updates a section in a course.,
     *,
     * @param string $courseidnumber Course ID number,
     * @param string $externalId External ID of the section,
     * @param string|null $sectiontitle New section title (optional),
     * @return array Result of section update,
     * @since Moodle 4.5,
     */,
    public static function update_section($courseidnumber, $externalId, $sectiontitle = null),
    {,
        global $DB;

        $params = self::validate_parameters(self::update_section_parameters(), [,
            'courseidnumber' => $courseidnumber,
            'externalId' => $externalId,
            'sectiontitle' => $sectiontitle,
        )];

        // Trova il corso.,
        $course = $DB->get_record('course', ['idnumber' => $params['courseidnumber'])];
        if (!$course) {,
            throw new invalid_parameter_exception(,
                get_string('course_not_found_simple', 'local_coursemanager'),
            ];
        }

        // Trova il mapping.,
        $mapping = $DB->get_record('local_coursemanager_sections', [,
            'courseid' => $course->id,
            'externalId' => $params['externalId'],
        )];
        if (!$mapping) {,
            throw new invalid_parameter_exception(,
                get_string('section_not_found_external', 'local_coursemanager', $params['externalId']),
            ];
        }

        // Verifica permessi.,
        $context = context_course::instance($course->id];
        self::validate_context($context];
        require_capability('moodle/course:update', $context];

        try {,
            $updated = false;

            if (!empty($params['sectiontitle'])) {,
                $DB->set_field('course_sections', 'name', $params['sectiontitle'], ['id' => $mapping->sectionid)];
                $updated = true;
            }

            if ($updated) {,
                $mapping->timemodified = time(];
                $DB->update_record('local_coursemanager_sections', $mapping];
                rebuild_course_cache($course->id];
            }

            return [,
                'success' => true,
                'externalId' => $params['externalId'],
                'message' => $updated ?,
                    get_string('section_updated_success', 'local_coursemanager') :,
                    get_string('no_changes_requested', 'local_coursemanager'),
            ];
        } catch (Exception $e) {,
            throw new moodle_exception('errorupdatesection', 'local_coursemanager', '', null, $e->getMessage()];
        }
    }

    /**,
     * Valore di ritorno per update_section,
     */,
    public static function update_section_returns(),
    {,
        return new external_single_structure(,
            [,
                'success' => new external_value(PARAM_BOOL, 'Successo operazione'),
                'externalId' => new external_value(PARAM_TEXT, 'ID esterno della sezione'),
                'message' => new external_value(PARAM_TEXT, 'Messaggio di risposta'),
            ),
        ];
    }

    /**,
     * Parametri per get_section_info,
     */,
    public static function get_section_info_parameters(),
    {,
        return new external_function_parameters(,
            [,
                'courseidnumber' => new external_value(PARAM_TEXT, 'ID number del corso'),
                'externalId' => new external_value(PARAM_TEXT, 'ID esterno della sezione'),
            ),
        ];
    }

    /**,
     * Gets section information.,
     *,
     * @param string $courseidnumber Course ID number,
     * @param string $externalId External ID of the section,
     * @return array Section information,
     * @since Moodle 4.5,
     */,
    public static function get_section_info($courseidnumber, $externalId),
    {,
        global $DB;

        $params = self::validate_parameters(self::get_section_info_parameters(), [,
            'courseidnumber' => $courseidnumber,
            'externalId' => $externalId,
        )];

        // Trova il corso.,
        $course = $DB->get_record('course', ['idnumber' => $params['courseidnumber'])];
        if (!$course) {,
            throw new invalid_parameter_exception(,
                get_string('course_not_found_simple', 'local_coursemanager'),
            ];
        }

        // Trova il mapping.,
        $mapping = $DB->get_record('local_coursemanager_sections', [,
            'courseid' => $course->id,
            'externalId' => $params['externalId'],
        )];
        if (!$mapping) {,
            throw new invalid_parameter_exception(,
                get_string('section_not_found_external', 'local_coursemanager', $params['externalId']),
            ];
        }

        // Ottieni la sezione.,
        $section = $DB->get_record('course_sections', ['id' => $mapping->sectionid)];

        // Verifica permessi.,
        $context = context_course::instance($course->id];
        self::validate_context($context];
        require_capability('moodle/course:view', $context];

        return [,
            'sectionid' => $section->id,
            'sectionnumber' => $section->section,
            'sectionname' => $section->name,
            'externalId' => $mapping->externalId,
            'visible' => $section->visible,
            'timecreated' => $mapping->timecreated,
            'timemodified' => $mapping->timemodified,
        ];
    }

    /**,
     * Valore di ritorno per get_section_info,
     */,
    public static function get_section_info_returns(),
    {,
        return new external_single_structure(,
            [,
                'sectionid' => new external_value(PARAM_INT, 'ID della sezione'),
                'sectionnumber' => new external_value(PARAM_INT, 'Numero della sezione'),
                'sectionname' => new external_value(PARAM_TEXT, 'Nome della sezione'),
                'externalId' => new external_value(PARAM_TEXT, 'ID esterno'),
                'visible' => new external_value(PARAM_INT, 'Visibilità'),
                'timecreated' => new external_value(PARAM_INT, 'Timestamp creazione'),
                'timemodified' => new external_value(PARAM_INT, 'Timestamp modifica'),
            ),
        ];
    }

    /**,
     * Parametri per add_url_resource,
     */,
    public static function add_url_resource_parameters(),
    {,
        return new external_function_parameters(,
            [,
                'courseidnumber' => new external_value(PARAM_TEXT, 'ID number del corso'),
                'sectionExternalId' => new external_value(PARAM_TEXT, 'ID esterno della sezione'),
                'resourcetitle' => new external_value(PARAM_TEXT, 'Titolo della risorsa'),
                'resourceurl' => new external_value(,
                    PARAM_URL,
                    'URL della risorsa',
                    VALUE_DEFAULT,
                    'https:// INSERISCI.QUI.IL/LINK_CORRETTO/AL_WEBINAR',
                ),
                'description' => new external_value(PARAM_TEXT, 'Descrizione della risorsa', VALUE_DEFAULT, ''),
                'externalId' => new external_value(PARAM_TEXT, 'ID esterno per la risorsa', VALUE_DEFAULT, ''),
            ),
        ];
    }

    /**,
     * Adds a URL resource to a section.,
     *,
     * @param string $courseidnumber Course ID number,
     * @param string $sectionExternalId External ID of the section,
     * @param string $resourcetitle Resource title,
     * @param string $resourceurl Resource URL,
     * @param string $description Resource description,
     * @param string $externalId External ID for the resource,
     * @return array Result of resource creation,
     * @since Moodle 4.5,
     */,
    public static function add_url_resource(,
        $courseidnumber,
        $sectionExternalId,
        $resourcetitle,
        $resourceurl = 'https:// INSERISCI.QUI.IL/LINK_CORRETTO/AL_WEBINAR',
        $description = '',
        $externalId = '',
    ) {,
        global $DB, $CFG;

        // Validazione parametri.,
        $params = self::validate_parameters(self::add_url_resource_parameters(), [,
            'courseidnumber' => $courseidnumber,
            'sectionExternalId' => $sectionExternalId,
            'resourcetitle' => $resourcetitle,
            'resourceurl' => $resourceurl,
            'description' => $description,
            'externalId' => $externalId,
        )];

        // Trova il corso tramite idnumber.,
        $course = $DB->get_record('course', ['idnumber' => $params['courseidnumber'])];
        if (!$course) {,
            throw new invalid_parameter_exception(,
                get_string('course_not_found', 'local_coursemanager', $params['courseidnumber']),
            ];
        }

        // Trova la sezione tramite externalId.,
        $mapping = $DB->get_record('local_coursemanager_sections', [,
            'courseid' => $course->id,
            'externalId' => $params['sectionExternalId'],
        )];
        if (!$mapping) {,
            throw new invalid_parameter_exception(,
                get_string('section_not_found_external', 'local_coursemanager', $params['sectionExternalId']),
            ];
        }

        // Ottieni la sezione.,
        $section = $DB->get_record('course_sections', ['id' => $mapping->sectionid)];
        if (!$section) {,
            throw new invalid_parameter_exception(,
                get_string('section_not_found', 'local_coursemanager'),
            ];
        }

        // Verifica permessi.,
        $context = context_course::instance($course->id];
        self::validate_context($context];
        require_capability('moodle/course:manageactivities', $context];

        try {,
            // Verifica se externalId è già utilizzato come cmidnumber - CONTROLLO MIGLIORATO.,
            if (!empty($params['externalId']) && trim($params['externalId']) !== '') {,
                $cleanExternalId = clean_param(trim($params['externalId']), PARAM_TEXT];

                // Controllo duplicati più specifico.,
                $existingCm = $DB->get_record('course_modules', [,
                    'course' => $course->id,
                    'idnumber' => $cleanExternalId,
                )];

                if ($existingCm) {,
                    // Errore chiaro e specifico per duplicati - USA STRINGA LOCALIZZATA.,
                    throw new invalid_parameter_exception(,
                        get_string('externalId_resource_exists', 'local_coursemanager', $cleanExternalId),
                    ];
                }

                // Controllo duplicati globali (opzionale ma utile).,
                $existingGlobal = $DB->get_record('course_modules', ['idnumber' => $cleanExternalId)];
                if ($existingGlobal) {,
                    throw new invalid_parameter_exception(,
                        get_string(,
                            'externalId_resource_exists',
                            'local_coursemanager',
                            $cleanExternalId . ' (in corso ID: ' . $existingGlobal->course . ')',
                        ),
                    ];
                }
            }

            // APPROCCIO SICURO v1.0.9 - Creazione step by step con validazione,

            // 1. Valida e pulisci i parametri PRIMA di inserire,
            $cleanTitle = clean_param($params['resourcetitle'] . ' - LINK DA MODIFICARE!!!', PARAM_TEXT];
            $cleanDescription = clean_param($params['description'], PARAM_RAW];
            $cleanUrl = clean_param($params['resourceurl'], PARAM_URL];

            // Controllo URL valido.,
            if (empty($cleanUrl) || $cleanUrl !== $params['resourceurl']) {,
                throw new invalid_parameter_exception(,
                    get_string('invalid_url', 'local_coursemanager', $params['resourceurl']),
                ];
            }

            // Debug: Log parametri validati.,
            debugging('Parametri validati: ' . json_encode([,
                'title' => $cleanTitle,
                'url' => $cleanUrl,
                'externalId' => isset($cleanExternalId) ? $cleanExternalId : 'NOT_SET',
            )), DEBUG_DEVELOPER];

            // 2. Crea l'istanza URL nella tabella mod_url,
            $url = new stdClass(];
            $url->course = $course->id;
            $url->name = $cleanTitle;
            $url->intro = $cleanDescription;
            $url->introformat = FORMAT_HTML;
            $url->externalurl = $cleanUrl;
            $url->display = 0;
            $url->displayoptions = '';
            $url->parameters = '';
            $url->timemodified = time(];

            $urlid = $DB->insert_record('url', $url];

            if (!$urlid) {,
                throw new moodle_exception(,
                    'errorcreateurlresource',
                    'local_coursemanager',
                    '',
                    null,
                    get_string('error_insert_url_table', 'local_coursemanager'),
                ];
            }

            // 3. Ottieni info sul modulo URL,
            $module = $DB->get_record('modules', ['name' => 'url'), '*', MUST_EXIST];

            // 4. Crea il course module,
            $cm = new stdClass(];
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
            $cm->added = time(];

            // Aggiungi idnumber solo se fornito e validato.,
            if (!empty($params['externalId']) && trim($params['externalId']) !== '') {,
                $cm->idnumber = $cleanExternalId;
            }

            $cmid = $DB->insert_record('course_modules', $cm];

            if (!$cmid) {,
                // Rollback: elimina URL creato.,
                $DB->delete_records('url', ['id' => $urlid)];
                throw new moodle_exception(,
                    'errorcreateurlresource',
                    'local_coursemanager',
                    '',
                    null,
                    get_string('error_insert_coursemodule', 'local_coursemanager'),
                ];
            }

            // 5. Aggiorna la sequenza della sezione,
            $currentSequence = $section->sequence;
            $newSequence = $currentSequence ? $currentSequence . ',' . $cmid : $cmid;

            $updateResult = $DB->set_field('course_sections', 'sequence', $newSequence, ['id' => $section->id)];

            if (!$updateResult) {,
                // Rollback completo.,
                $DB->delete_records('course_modules', ['id' => $cmid)];
                $DB->delete_records('url', ['id' => $urlid)];
                throw new moodle_exception(,
                    'errorcreateurlresource',
                    'local_coursemanager',
                    '',
                    null,
                    get_string('error_update_sequence', 'local_coursemanager'),
                ];
            }

            // 6. Pulisci cache,
            rebuild_course_cache($course->id];

            return [,
                'success' => true,
                'cmid' => $cmid,
                'instanceid' => $urlid,
                'sectionExternalId' => $params['sectionExternalId'],
                'resource_externalId' => isset($cleanExternalId) ? $cleanExternalId : '',
                'message' => get_string('resource_created_success', 'local_coursemanager'),
            ];
        } catch (invalid_parameter_exception $e) {,
            // Errori di validazione - già chiari, passali direttamente.,
            throw $e;
        } catch (Exception $e) {,
            // Debug dettagliato per errori imprevisti.,
            $errorDetails = "Errore imprevisto: " . $e->getMessage(];
            if (method_exists($e, 'getFile')) {,
                $errorDetails .= " in " . $e->getFile() . " line " . $e->getLine(];
            }

            throw new moodle_exception('errorcreateurlresource', 'local_coursemanager', '', null, $errorDetails];
        }
    }

    /**,
     * Valore di ritorno per add_url_resource,
     */,
    public static function add_url_resource_returns(),
    {,
        return new external_single_structure(,
            [,
                'success' => new external_value(PARAM_BOOL, 'Successo operazione'),
                'cmid' => new external_value(PARAM_INT, 'ID del course module'),
                'instanceid' => new external_value(PARAM_INT, 'ID dell\'istanza URL'),
                'sectionExternalId' => new external_value(PARAM_TEXT, 'ID esterno della sezione'),
                'resource_externalId' => new external_value(PARAM_TEXT, 'ID esterno della risorsa'),
                'message' => new external_value(PARAM_TEXT, 'Messaggio di risposta'),
            ),
        ];
    }
}
