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
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         =  new stdClass;

        $reports = get_plugin_list_with_function('report', 'extend_navigation_course', 'lib.php');
        foreach ($reports as $reportfunction) {
            $reportfunction($reportnav, $course, $this->page->context);
        }
        $this->content->footer = '';

        return $this->content;
    }
}   // Here's the closing bracket for the class definition
