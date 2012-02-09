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
 

// Include link to trapping URL
if (get_config('local_projecthoneypot', 'link')==1) {
    $hpurl = get_config('local_projecthoneypot', 'hpurl');

    switch (rand(0, 8)) {
        case 0:
            echo '<a href="'.$hpurl.'"><!-- merit-manor --></a>';
            break;
        case 1:
            echo '<a href="'.$hpurl.'"><img src="merit-manor.gif" height="1" width="1" border="0"></a>';
            break;
        case 2:
            echo '<a href="'.$hpurl.'" style="display: none;">merit-manor</a>';
            break;
        case 3:
            echo '<div style="display: none;"><a href="'.$hpurl.'">merit-manor</a></div>';
            break;
        case 4:
            echo '<a href="'.$hpurl.'"></a>';
            break;
        case 5:
            echo '<!-- <a href="'.$hpurl.'">merit-manor</a> -->';
            break;
        case 6:
            echo '<div style="position: absolute; top: -250px; left: -250px;"><a href="'.$hpurl.'">merit-manor</a></div>';
            break;
        case 7:
            echo '<a href="'.$hpurl.'"><span style="display: none;">merit-manor</span></a>';
            break;
        case 8:
            echo '<a href="'.$hpurl.'"><div style="height: 0px; width: 0px;"></div></a>';
            break;
    }
}



// Checking access
// using this : http://planetozh.com/blog/my-projects/honey-pot-httpbl-simple-php-script/

/*
Script Name: Simple PHP http:BL implementation
Script URI: http://planetozh.com/blog/my-projects/honey-pot-httpbl-simple-php-script/
Description: Simple script to check an IP against Project Honey Pot's database and let only legitimate users access your script
Author: Ozh
Version: 1.0
Author URI: http://planetozh.com/
*/


global $CFG, $USER, $COURSE;
if (@$_COOKIE['notaabot']) {
	// loggin activity : allowed with a cookie
	if (get_config('local_projecthoneypot', 'log')==1) {
	    $info = 'IP '.$_SERVER['REMOTE_ADDR'].' allowed by cookie. User agent : '.$_SERVER["HTTP_USER_AGENT"];
	    add_to_log($COURSE->id, 'projecthoneypot', 'pass', $_SERVER['REQUEST_URI'], $info, $cm=0, $USER->id);
	}
} else {
	httpbl_check();
}


function httpbl_check() {
    global $CFG, $USER, $COURSE;    

    $hpurl = get_config('local_projecthoneypot', 'hpurl');
    $apikey = get_config('local_projecthoneypot', 'apikey');
	$ip = $_SERVER['REMOTE_ADDR'];

    if (@$_GET['check_projecthoneypot']) {
        $ip = "118.39.80.201"; // Bad IP for testing
    }

	$lookup = $apikey . '.' . implode('.', array_reverse(explode ('.', $ip ))) . '.dnsbl.httpbl.org';
	$result = explode( '.', gethostbyname($lookup));

	if ($result[0] == 127) {
		$activity = $result[1];
		$threat = $result[2];
		$type = $result[3];
		$typemeaning = '';
		if ($type & 0) $typemeaning .= 'Search Engine, ';
		if ($type & 1) $typemeaning .= 'Suspicious, ';
		if ($type & 2) $typemeaning .= 'Harvester, ';
		if ($type & 4) $typemeaning .= 'Comment Spammer, ';
		$typemeaning = trim($typemeaning,', ');
		
		// Now determine some blocking policy
		if (
		($type >= 4 && $threat > 0) // Comment spammer with any threat level
			||
		($type < 4 && $threat > 20) // Other types, with threat level greater than 20
		) {
			$block = true;
		}
		
		if ($block) {
			// loggin activity
			$info = 'Suspect IP '.$ip.' blocked : '.$typemeaning.' ('.$threat.'%) - Activity : '.$activity.' - User agent : '.$_SERVER["HTTP_USER_AGENT"];
			add_to_log($COURSE->id, 'projecthoneypot', 'block', $_SERVER['REQUEST_URI'], $info, $cm=0, $USER->id);
			// blocking IP
			httpbl_blockme();
			die();
		}
	
	}
}






function httpbl_blockme() {
    global $CFG;
    $hpurl = get_config('local_projecthoneypot', 'hpurl');
    
    header('HTTP/1.0 403 Forbidden');

	echo 'HTML
	<script type="text/javascript">
	function setcookie( name, value, expires, path, domain, secure ) {
		// set time, it\'s in milliseconds
		var today = new Date();
		today.setTime( today.getTime() );
	
		if ( expires ) {
			expires = expires * 1000 * 60 * 60 * 24;
		}
		var expires_date = new Date( today.getTime() + (expires) );
	
		document.cookie = name + "=" +escape( value ) +
		( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) + 
		( ( path ) ? ";path=" + path : "" ) + 
		( ( domain ) ? ";domain=" + domain : "" ) +
		( ( secure ) ? ";secure" : "" );
	}	
	function letmein() {
		setcookie(\'notabot\',\'true\',1,\'/\', \'\', \'\');
		location.reload(true);
	}
	</script>
	<h1>Forbidden</h1>
	<p>Sorry. You are using a suspicious IP.</p>
	<p>If you <strong>ARE NOT</strong> a bot of any kind, please <a href="javascript:letmein()">click here</a> to access the page. Sorry for this !</p>
	<p>Otherwise, please have fun with <a href="'.$hpurl.'">this page</a></p>
HTML';

}

