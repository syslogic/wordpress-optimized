<?php
/*
	WordPress Installer for PagodaBox v1.05
	
	Features:
	- retrieves unique salts on first deployment
	- the admin is properly set to read-only mode
	
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
if(!file_exists($b)){
	if(file_exists($v)){
		require_once($v);
		echo "+> Salted Installer for WordPress v".$wp_version."\n";
		echo "+> Copyright 2013 by Martin Zeitler, Bavaria\n";
		echo "+> Freelance IT Solution Development\n";
		echo "+> https://plus.google.com/107182394331269949090\n";
		echo "+> http://www.freelancer.com/u/syslogic.html\n";
		echo "+> http://profiles.wordpress.org/syslogic\n\n";
	}
	if(wget('https://api.wordpress.org/secret-key/1.1/salt/',$b)){
		file_put_contents($d,file_get_contents($a)."\n".file_get_contents($b)."\n".file_get_contents($c));
		foreach(file($b) as $x){
			preg_match("/^define\('(\w+)',\s+'(.*)'\);/",$x,$y);
			echo $y[1].': '.$y[2]."\n";
		}
		unlink($a);unlink($b);unlink($c);
	}
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
	echo "+> Fetched '$src' @ ".abs(round(($info['size_download']*8/$time/1024/1024),2))."MBit/s\n";
	return true;
}
?>