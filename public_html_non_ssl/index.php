<?php
include("../config.php");
$domain = $_SERVER['SERVER_NAME'];
$domain_ip = gethostbyname($domain);
if($domain_ip == $_SERVER['SERVER_ADDR']){

	$result=['code'=>'NOT_OK', 'message'=>'not valid'];
	$result = createOrUpdateDomain($domains_dir, $domain, $server_host_name,$whitelist_ips);

	if($result['code'] == 'OK'){
		if($result['redirect']){
			echo "Redirecting..";
			echo "<meta http-equiv='refresh' content='1;url=http://www.".$_SERVER['SERVER_NAME']."' />";
			exit();
		} else {
			echo $result['message'];
			exit();
		}
	} else {
		echo $result['message'];
	}
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

function createOrUpdateDomain($domains_dir, $domain, $server_host_name,$whitelist_ips): array {
	$test1 = filter_var('http://'.$domain, FILTER_VALIDATE_URL);
	$test2 = filter_var($domain, FILTER_VALIDATE_IP);
	// possibly more filters?
	if($test1 === false || $test2 !== false){
		return ['code' => 'NOT_VALID', 'redirect' => false, 'message' => 'Not valid domain: '.$domain];
	}
	if(mb_substr(mb_strtolower($domain),0,4) == 'www.'){
		return ['code' => 'NOT_VALID', 'redirect' => false, 'message' => 'Not valid domain: '.$domain];
	}
	$file = $domains_dir.DIRECTORY_SEPARATOR.escapeshellcmd($domain);
	if(!file_exists($file)){
		if(empty($whitelist_ips) || in_array($_SERVER['REMOTE_ADDR'],$whitelist_ips)){
			file_put_contents($file,$_SERVER['REMOTE_ADDR']);
			return ['code' => 'OK', 'redirect' => false, 'message' => 'Created'];
		} else {
			return ['code' => 'NOT_VALID', 'redirect' => false, 'message' => 'You are not allowed to create new hosts on this server'];
		}
	}

	if($domain == $server_host_name){
		return ['code' => 'OK', 'redirect' => false, 'message' => 'Application info'];
	}
	return ['code' => 'OK', 'redirect' => true, 'message' => 'already created'];
}