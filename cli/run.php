<?php
include("../config.php");
//check for lock. if its over 10 minutes assume its crashed.
if(file_exists($run_lock_file) && ((int)file_get_contents($run_lock_file))+ 10*60 > time()){
    echo 'Already running';
    exit(0);
}
file_put_contents($run_lock_file,time());

$tempfile = tempnam(__DIR__,'vhost');
$domains = scandir($domains_dir);
foreach($domains as $domain){
    if(substr($domain,0,1) == '.'){
        continue;
    }
    $text = generateSSLTemplate($public_html_ssl_dir,$domain);
    if($text === false){
        echo 'Domain is not SSL enabled yet. try to make certs';
        exec('certbot certonly --non-interactive --agree-tos -m '.$certbot_email.' --webroot -w '.$public_html_non_ssl_dir.' -d '.$domain);
        $text = generateSSLTemplate($public_html_ssl_dir,$domain);
        file_put_contents($tempfile,$text."\n",FILE_APPEND);
    } else {
        echo "Have ssl template for ".$domain."\n";
        file_put_contents($tempfile,$text."\n",FILE_APPEND);
    }
    // echo $domain."\n";
}
file_put_contents($apache_non_ssl_conf,generateNONSSLTemplate($public_html_non_ssl_dir));

file_put_contents($apache_ssl_conf,file_get_contents($tempfile));
exec($reload_apache_cmd);
unlink ($tempfile);
unlink ($run_lock_file);
exit(0);
function generateSSLTemplate($public_html_ssl_dir,$domain){
    // if file exists it means we have already generated domain
    if(file_exists('/etc/letsencrypt/live/'.$domain.'/privkey.pem')){
        return '
        <VirtualHost *:443>
        ServerAlias '.$domain.'
        ServerName '.$domain.'
        DocumentRoot '.$public_html_ssl_dir.'
        <Directory '.$public_html_ssl_dir.'/>
                AllowOverride All
                Require all granted
        </Directory>
        SSLEngine on
        SSLCertificateKeyFile /etc/letsencrypt/live/'.$domain.'/privkey.pem
        SSLCertificateFile /etc/letsencrypt/live/'.$domain.'/fullchain.pem
        </VirtualHost>';
    } else {
       return false;
    }
}

function generateNONSSLTemplate($public_html_non_ssl_dir){
        $text = '
        <VirtualHost *:80>
        ServerAdmin webmaster@localhost
        DocumentRoot '.$public_html_non_ssl_dir.'
        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
        </VirtualHost>
    ';
    return $text;
}