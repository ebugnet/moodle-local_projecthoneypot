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
 

 
 
 // Include link to URL
$hpurl = 'http://www.sendcard.org/imperfectworm.php?mod=386';

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

/*** EDIT LINE 22 WITH YOUR OWN HTTP:BL ACCESS KEY ! ***/

if ($_COOKIE['notabot']) {
	//httpbl_logme(false,	$_SERVER['REMOTE_ADDR']);
	//add_to_log($courseid, $module, $action, $url='', $info='', $cm=0, $user=0)
	////httpbl_check();
} else {
	httpbl_check();
}


function httpbl_check() {	
	// your http:BL key 
	$apikey = 'abcdefghijkl';
	
	// IP to test
	$ip = $_SERVER['REMOTE_ADDR'];
    // IP de test
    //$ip = "173.44.37.234";
	

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
			//httpbl_logme($block,$ip,$type,$threat,$activity);
			//add_to_log($courseid, $module, $action, $url='', $info='', $cm=0, $user=0)
			httpbl_blockme();
			die();
		}
	
	}
}


function httpbl_logme($block = false, $ip='', $type='',$threat='',$activity='') {
global $CFG; 
	$log = fopen($CFG->dirroot.'/local/projecthoneypot/block.log','a');
	$stamp = date('Y-m-d :: H-i-s');
	
	// Some stuff you could log for further analysis
	$page = $_SERVER['REQUEST_URI'];
	$ua = $_SERVER["HTTP_USER_AGENT"];
		
	if ($block) {
		fputs($log,"$stamp :: BLOCKED $ip :: $type :: $threat :: $activity :: $page :: $ua\n");
	} else {
		fputs($log,"$stamp :: UNBLCKD $ip :: $page :: $ua\n");
	}
	fclose($log);
}


function httpbl_blockme() {
	header('HTTP/1.0 403 Forbidden');
	echo <<<HTML
	<script type="text/javascript">
	function setcookie( name, value, expires, path, domain, secure ) {
		// set time, it's in milliseconds
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
		setcookie('notabot','true',1,'/', '', '');
		location.reload(true);
	}
	</script>
	<h1>Forbidden</h1>
	<p>Sorry. You are using a suspicious IP.</p>
	<p>If you <strong>ARE NOT</strong> a bot of any kind, please <a href="javascript:letmein()">click here</a> to access the page. Sorry for this !</p>
	<p>Otherwise, please have fun with <a href="http://planetozh.com/smelly.php">this page</a></p>
HTML;
}




















