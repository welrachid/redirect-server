<?php
// this file is to remove domains that are no longer pointing towards this server.

include("../config.php");
if(file_exists($cleanup_lock_file) && ((int)file_get_contents($cleanup_lock_file))+ 10*60 > time()){
    echo 'Already running';
    exit(0);
}
file_put_contents($cleanup_lock_file,time());

$domains = scandir($domains_dir);
$myip = current(gethostbynamel($server_host_name));
foreach($domains as $domain){
    if(substr($domain,0,1) == '.'){
        continue;
    }
    if(gethostbyname($domain) != $myip){
        echo "Deleting ".$domain."\n";
        unlink($domains_dir.DIRECTORY_SEPARATOR.escapeshellcmd($domain));
        exec("certbot delete --non-interactive --cert-name ".escapeshellcmd($domain));
    }
}
unlink ($cleanup_lock_file);
exit(0);