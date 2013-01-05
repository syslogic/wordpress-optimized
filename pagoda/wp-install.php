<?php
/*
	How to apply salt to a dish?
	
	written in 2013 by Martin Zeitler
	http://profiles.wordpress.org/syslogic/
*/
$config=dirname(__FILE__).'/wp-config.php';
$config_a=dirname(__FILE__).'/wp-config_a.php';
$salts_file=dirname(__FILE__).'/wp-salts.php';
$config_b=dirname(__FILE__).'/wp-config_b.php';

if(!file_exists($salts_file)){
	if(wget('https://api.wordpress.org/secret-key/1.1/salt/', $salts_file)){
		file_put_contents($config,file_get_contents($config_a)."\n".file_get_contents($salts_file)."\n".file_get_contents($config_b));
		$lines=file($salts_file);
		foreach($lines as $line){echo $line."\n";}
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