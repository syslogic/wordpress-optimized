<?php
/*
	WordPress Installer for PagodaBox v1.05
	
	Features:
	- retrieves unique salts on first deployment
	- the admin is properly set to read-only mode
	
	Author:
	Martin Zeitler, Bavaria
	https://plus.google.com/107182394331269949090?rel=author
	http://profiles.wordpress.org/syslogic/
*/
$v=dirname(__FILE__).'/../wp-includes/version.php';
$d=dirname(__FILE__).'/wp-config.php';
$a=str_replace('g.','g_a.' ,$d);
$b=str_replace('g.','g_b.' ,$d);
$c=str_replace('g.','g_c.' ,$d);
if(!file_exists($b)){
	/* retrieve version number */
	if(file_exists($v)){
		require_once($v);
		echo "\nRetrieving fresh salts for WordPress v".$wp_version."\n";
	}
	if(wget('https://api.wordpress.org/secret-key/1.1/salt/',$b)){
		file_put_contents($d,file_get_contents($a)."\n".file_get_contents($b)."\n".file_get_contents($c));
		unlink($a);unlink($b);unlink($c);
		echo "\nThe following strings have been configured:\n";
		foreach(file($b) as $s){
			preg_match('/^define(\'(\w+)\',\s+\'(.*)\');/', $s, $m);
			echo $m[1].': '.$m[2];
		}
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
	echo "Fetched '$src' @ ".abs(round(($info['size_download']*8/$time/1024/1024),2))."MBit/s.\n";
	return true;
}
?>