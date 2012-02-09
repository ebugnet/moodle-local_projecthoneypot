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
 * This file is used to setting the plugin allover the site
 *
 * @package    local
 * @subpackage projecthoneypot
 * @copyright  2012 Éric Bugnet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/lib/formslib.php');

class local_projecthoneypot_form extends moodleform {

    public function definition () {
        $mform =& $this->_form;
                
        $mform->addElement('text', 'hpurl', get_string('hpurl', 'local_projecthoneypot'));
        $mform->addHelpButton('hpurl', 'hpurl', 'local_projecthoneypot');
        
        $mform->addElement('text', 'apikey', get_string('apikey', 'local_projecthoneypot'));
        $mform->addHelpButton('apikey', 'apikey', 'local_projecthoneypot');
        
        $mform->addElement('checkbox', 'link', null, get_string('link_help', 'local_projecthoneypot'));
        $mform->setDefault('link', 1);
        
        $mform->addElement('checkbox', 'log', null, get_string('log_help', 'local_projecthoneypot'));
        $mform->setDefault('log', 0);
        
        $this->add_action_buttons();
    }

}
