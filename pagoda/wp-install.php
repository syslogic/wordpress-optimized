<?php
/*
	How to apply salt to a dish?
	
	written in 2013 by Martin Zeitler
	http://profiles.wordpress.org/syslogic/
*/
$x=dirname(__FILE__).'/wp-config.php';
$a=dirname(__FILE__).'/wp-config_a.php';
$b=dirname(__FILE__).'/wp-config_b.php';
$c=dirname(__FILE__).'/wp-config_c.php';

if(!file_exists($b)){
	if(wget('https://api.wordpress.org/secret-key/1.1/salt/',$b)){
		file_put_contents($x,file_get_contents($a)."\n".file_get_contents($b)."\n".file_get_contents($c));
		foreach(file($b) as $line){
			preg_match('/^define(\'(\w+)\',\s+\'(.*)\');/', $line, $matches);
			echo $matches[1].':'.$matches[2];
		}
	}
}

/* helpers */
function wget($src, $dst){
	$fp = fopen($dst, 'w');
	$curl = curl_init();
	$opt = array(
		CURLOPT_URL => $src,
		CURLOPT_HEADER => false,
		CURLOPT_FILE => $fp
	);
	curl_setopt_array($curl, $opt);
	$rsp = curl_exec($curl);
	if($rsp===false){
		die("[cURL] errno:".curl_errno($curl)."\n[cURL] error:".curl_error($curl)."\n");
	}
	$info = curl_getinfo($curl);
	curl_close($curl);
	fclose($fp);
	
	/* cURL stats */
	$time = $info['total_time']-$info['namelookup_time']-$info['connect_time']-$info['pretransfer_time']-$info['starttransfer_time']-$info['redirect_time'];
	echo "Fetched '$src' @ ".abs(round(($info['size_download']*8/$time/1024/1024),2))."MBit/s.\n";
	
	/* better make text contents invisible */
	/* file_put_contents($dst, "<?php\n".file_get_contents($dst)."\n?>"); */
	echo "Fresh salts have been applied to your WordPress install.\n";
	return true;
}
function format_size($size=0){
	if($size < 1024){return $size.'b';}elseif($size < 1048576){return round($size/1024,2).'kb';}else{return round($size/1048576,2).'mb';}
}
?>