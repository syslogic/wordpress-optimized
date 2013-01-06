<?php
/*
	Cloud-Installer for WordPress v3.5
	
	Features:
	
	- generates a wp-config.php with unique salts on first deployment
		(which can be downloaded via SSH from /var/www/logs/wp-config.php)
	
	- the admin is set to read-only mode (= no useless buttons)
	
	- PHP errors are logged /var/www/logs/
	
	Good to know:
	
	- if a pagoda/wp-config.php file is present, the installer will exit.
	
	Author:
	Copyright 2013 by Martin Zeitler, Bavaria
	https://plus.google.com/107182394331269949090?rel=author
	http://profiles.wordpress.org/syslogic/
*/
$v=dirname(__FILE__).'/../wp-includes/version.php';
$d=dirname(__FILE__).'/wp-config.php';
$a=str_replace('g.','g_a.' ,$d);
$b=str_replace('g.','g_b.' ,$d);
$c=str_replace('g.','g_c.' ,$d);
if(file_exists($v)){
	require_once($v);
	echo "+> Cloud-Installer for WordPress v".$wp_version."\n->\n";
	echo "+> Copyright 2013 by Martin Zeitler, Bavaria\n";
	echo "+> Freelance IT Solution Development\n->\n";
	echo "+> https://plus.google.com/107182394331269949090\n";
	echo "+> http://www.freelancer.com/u/syslogic.html\n";
	echo "+> http://profiles.wordpress.org/syslogic\n->\n";
}
if(!file_exists($d)){
	if(!file_exists($b)){
		if(wget('https://api.wordpress.org/secret-key/1.1/salt/',$b)){
			file_put_contents($d,file_get_contents($a)."\n".file_get_contents($b)."\n".file_get_contents($c));
			foreach(file($b) as $x){
				preg_match("/^define\('(\w+)',\s+'(.*)'\);/",$x,$y);
				echo '-> '.$y[1].': '.$y[2]."\n";
			}
			unlink($a);unlink($b);unlink($c);
		}
	}
}
else{
	if(file_exists($a)){unlink($a);}
	if(file_exists($c)){unlink($c);}
	echo '-> The file wp-config.php is present - just proceeding with the next hook.';
}
function wget($src, $dst){
	$fp = fopen($dst, 'w');
	$curl = curl_init();
	$opt = array(CURLOPT_URL => $src, CURLOPT_HEADER => false, CURLOPT_FILE => $fp);
	curl_setopt_array($curl, $opt);
	$rsp = curl_exec($curl);
	if($rsp===false){die("[cURL] errno:".curl_errno($curl)."\n[cURL] error:".curl_error($curl)."\n");}
	$info = curl_getinfo($curl);
	curl_close($curl);
	fclose($fp);
	
	/* cURL stats */
	$time = $info['total_time']-$info['namelookup_time']-$info['connect_time']-$info['pretransfer_time']-$info['starttransfer_time']-$info['redirect_time'];
	echo "+> Fetched '$src' @ ".abs(round(($info['size_download']*8/$time/1024/1024),2))."MBit/s\n->\n";
	return true;
}
?>