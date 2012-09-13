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
     * Library of interface functions and constants for module coursework
     *
     * @package    block
     * @subpackage reports
     * @copyright  2012 University of London Computer Centre {@link www.ulcc.ac.uk}
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */

class block_reports extends block_list {

    public function init() {
        $this->title = get_string('reports', 'block_reports');
    }
    // The PHP tag and the curly bracket for the class definition
    // will only be closed after there is another function added in the next section.
    public function get_content() {
        global $OUTPUT, $PAGE, $CFG;
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         = new stdClass;
        $this->content->items  = array();
        $this->content->icons  = array();
        $this->content->footer = '';

        if (has_capability('moodle/site:viewreports', $PAGE->context)) { // Basic capability for listing of reports.
            // Get all course report type plug-ins.
            $reports = get_plugin_list_with_function('report', 'extend_navigation_course', 'lib.php');
            foreach ($reports as $key => $value) {
                $report = substr(strstr($key, '_'), 1);
                // Completion report requires course instead of id.
                switch ($report) {
                    case 'completion':
                        require_once($CFG->libdir.'/completionlib.php');
                        if (has_capability('report/completion:view', $PAGE->context)) {
                            $completion = new completion_info($PAGE->course);
                            if ($completion->is_enabled() && $completion->has_criteria()) {
                                $url = new moodle_url('/report/completion/index.php', array('course'=>$PAGE->course->id));
                                // Display link to report page.
                                $this->content->items[] = html_writer::tag('a', get_string('pluginname', $key),
                                    array('href' => $url));
                                $this->content->icons[] = '<img src="' . $OUTPUT->pix_url('i/stats') .
                                    '" class="icon" alt="" />&nbsp;';
                            }
                        }
                        break;

                    case 'log':
                        if (has_capability('report/log:view', $PAGE->context)) {
                            $url = new moodle_url('/report/log/index.php', array('id'=>$PAGE->course->id));
                            $this->content->items[] = html_writer::tag('a', get_string('pluginname', $key),
                                array('href' => $url));
                            $this->content->icons[] = '<img src="' . $OUTPUT->pix_url('i/stats') .
                                '" class="icon" alt="" />&nbsp;';
                        }
                        break;

                    case 'loglive':
                        if (has_capability('report/loglive:view', $PAGE->context)) {
                            $url = new moodle_url('/report/loglive/index.php', array('id'=>$PAGE->course->id, 'inpopup'=>1));
                            $action = new action_link($url, get_string('pluginname', 'report_loglive'),
                                new popup_action('click', $url));
                            $this->content->items[] = html_writer::tag('a', get_string('pluginname', $key),
                                array('href' => $url));
                            $this->content->icons[] = '<img src="' . $OUTPUT->pix_url('i/stats') .
                                '" class="icon" alt="" />&nbsp;';
                        }
                        break;

                    case 'outline':
                        if (has_capability('report/outline:view', $PAGE->context)) {
                            $url = new moodle_url('/report/outline/index.php', array('id'=>$PAGE->course->id));
                            $this->content->items[] = html_writer::tag('a', get_string('pluginname', $key),
                                array('href' => $url));
                            $this->content->icons[] = '<img src="' . $OUTPUT->pix_url('i/stats') .
                                '" class="icon" alt="" />&nbsp;';
                        }
                        break;

                    case 'participation':
                        if (has_capability('report/participation:view', $PAGE->context)) {
                            $url = new moodle_url('/report/participation/index.php', array('id'=>$PAGE->course->id));
                            $this->content->items[] = html_writer::tag('a', get_string('pluginname', $key),
                                array('href' => $url));
                            $this->content->icons[] = '<img src="' . $OUTPUT->pix_url('i/stats') .
                                '" class="icon" alt="" />&nbsp;';
                        }
                        break;

                    case 'progress':
                        require_once($CFG->libdir.'/completionlib.php');

                        $showonnavigation = has_capability('report/progress:view', $PAGE->context);
                        $group = groups_get_course_group($PAGE->course, true); // Supposed to verify group.
                        if ($group===0 && $PAGE->course->groupmode==SEPARATEGROUPS) {
                            $showonnavigation = ($showonnavigation
                                && has_capability('moodle/site:accessallgroups', $PAGE->context));
                        }

                        $completion = new completion_info($PAGE->course);
                        $showonnavigation = ($showonnavigation && $completion->is_enabled()
                            && count($completion->get_activities())>0);
                        if ($showonnavigation) {
                            $url = new moodle_url('/report/progress/index.php', array('course'=>$PAGE->course->id));
                            $this->content->items[] = html_writer::tag('a', get_string('pluginname', $key),
                                array('href' => $url));
                            $this->content->icons[] = '<img src="' . $OUTPUT->pix_url('i/stats') .
                                '" class="icon" alt="" />&nbsp;';
                        }
                        break;

                    case 'stats':
                        if (!empty($CFG->enablestats)) {
                            return;
                        }
                        if (has_capability('report/stats:view', $PAGE->context)) {
                            $url = new moodle_url('/report/stats/index.php', array('course'=>$PAGE->course->id));
                            $this->content->items[] = html_writer::tag('a', get_string('pluginname', $key),
                                array('href' => $url));
                            $this->content->icons[] = '<img src="' . $OUTPUT->pix_url('i/stats') .
                                '" class="icon" alt="" />&nbsp;';
                        }
                        break;

                    default:
                        $url = new moodle_url("/report/$report/index.php", array('id' => $PAGE->course->id));
                        $this->content->items[] = html_writer::tag('a', get_string('pluginname', $key),
                                array('href' => $url));
                        $this->content->icons[] = '<img src="' . $OUTPUT->pix_url('i/stats') .
                                '" class="icon" alt="" />&nbsp;';
                        break;
                }
            }
        }
        return $this->content;
    }
}   // Here's the closing bracket for the class definition.
