<?php
include("../config.php");
if(!empty($whitelist_ips) && !in_array($_SERVER['REMOTE_ADDR'],$whitelist_ips)){
	exit("NOT ALLOWED");
}
$domain = $_SERVER['SERVER_NAME'];
$domain_ip = gethostbyname($domain);
if($domain_ip == $_SERVER['SERVER_ADDR']){
	// we need to check and create a file.
	createOrUpdateDomain($domains_dir, $domain, $server_host_name);
} else {
	echo 'Domain: '.$_SERVER['SERVER_NAME'].' is not pointing to correct ip adr. Found: '.$domain_ip.'';
}

if($domain == $server_host_name){
	?>
	<h3>Redirect-server</h3>
	<a href="https://github.com/welrachid/redirect-server/">Github repo</a><br>
	
	<p>To use this service, just create an A-record in your DNS panel and point it to <?php echo gethostbyname($server_host_name);?></p>
	<p>Once it works visit your domain and you should see a message stating its created or it already exists</p>
	<p>Wait a couple of minutes and we will issue a new certificate for you</p>

	<pre>
		<?php include("../README.md"); ?>
	</pre>
	<?php 
}

function createOrUpdateDomain($domains_dir, $domain, $server_host_name){
	$test1 = filter_var('http://'.$domain, FILTER_VALIDATE_URL);
	$test2 = filter_var($domain, FILTER_VALIDATE_IP);
	// possibly more filters?
	if($test1 === false || $test2 !== false){
		echo "NOT VALID ".$domain;
		return;
	}
	if(mb_substr(mb_strtolower($domain),0,4) == 'www.'){
		echo "NOT VALID ".$domain;
		return;
	}
	$file = $domains_dir.DIRECTORY_SEPARATOR.escapeshellcmd($domain);
	if(!file_exists($file)){
		file_put_contents($file,$_SERVER['REMOTE_ADDR']);
		echo "OK - created";
	} else {
		echo "Redirecting..";
		if($domain != $server_host_name){
			echo "<meta http-equiv='refresh' content='1;url=http://www.".$_SERVER['SERVER_NAME']."' />";
		}
	}
}