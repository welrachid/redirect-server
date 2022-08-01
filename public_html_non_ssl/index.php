<?php
include("../config.php");
if(!empty($whitelist_ips) && !in_array($_SERVER['REMOTE_ADDR'],$whitelist_ips)){
	//
	exit("NOT ALLOWED");
}
$domain = $_SERVER['SERVER_NAME'];
$domain_ip = gethostbyname($domain);
if($domain_ip == $_SERVER['SERVER_ADDR']){
	// we need to check and create a file.
	createOrUpdateDomain($domains_dir, $domain);
} else {
	echo 'Domain: '.$_SERVER['SERVER_NAME'].' is not pointing to correct ip adr. Found: '.$domain_ip.'';
}

if($domain == $server_host_name){
	echo "<pre>";
	include("../README.md");
	echo "</pre>";
}

function createOrUpdateDomain($domains_dir, $domain){
	$test1 = filter_var('http://'.$domain, FILTER_VALIDATE_URL);
	$test2 = filter_var($domain, FILTER_VALIDATE_IP);
	// possibly more filters?
	if($test1 === false || $test2 !== false){
		print "NOT VALID ".$domain;
		return;
	}
	$file = $domains_dir.DIRECTORY_SEPARATOR.escapeshellcmd($domain);
	if(!file_exists($file)){
		file_put_contents($file,$_SERVER['REMOTE_ADDR']);
		echo "OK - created";
	} else {
		print "Already exists - please be patient";
	}
}