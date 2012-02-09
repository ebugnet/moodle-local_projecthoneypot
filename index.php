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
 
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot.'/local/projecthoneypot/form.php');

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$pageparams = array();
admin_externalpage_setup('local_projecthoneypot', '', $pageparams);

$PAGE->set_heading($SITE->fullname);
$PAGE->set_title($SITE->fullname . ': ' . get_string('pluginname', 'local_projecthoneypot'));

$returnurl = new moodle_url('/local/projecthoneypot/');
$mform = new local_projecthoneypot_form($returnurl);
$mform->set_data(get_config('local_projecthoneypot'));

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $mform->get_data()) {
    if (isset($data->link)) {
        set_config('link' , 1, 'local_projecthoneypot');
    } else {
        set_config('link' , 0, 'local_projecthoneypot');
    }
    if (isset($data->log)) {
        set_config('log' , 1, 'local_projecthoneypot');
    } else {
        set_config('log' , 0, 'local_projecthoneypot');
    }
    set_config('hpurl' , $data->hpurl, 'local_projecthoneypot');
    set_config('apikey' , $data->apikey, 'local_projecthoneypot');
}


echo $OUTPUT->header();
$mform->display();
//echo 'Check your moodle using a bad ip : <a href="'.$CFG->wwwroot.'?check_projecthoneypot=1" target="_blank">Check</a>';
echo $OUTPUT->footer();
