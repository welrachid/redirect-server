<?php

$server_host_name = 'redirect.notifierchatbot.com';
$certbot_email = 'info@notifierchatbot.com';


$public_html_non_ssl_dir_name = 'public_html_non_ssl';
$public_html_ssl_dir_name = 'public_html_ssl';
$apache_non_ssl_conf = '/etc/apache2/sites-available/non-ssl.conf';
$apache_ssl_conf = '/etc/apache2/sites-available/ssl.conf';
$reload_apache_cmd = '/usr/sbin/apache2ctl -k graceful';
$run_lock_file = 'run.lock';
$cleanup_lock_file = 'cleanup.lock';

$app_dir = __DIR__.DIRECTORY_SEPARATOR;
$public_html_non_ssl_dir = $app_dir.$public_html_non_ssl_dir_name.DIRECTORY_SEPARATOR;
$domains_dir = $public_html_non_ssl_dir."domains";
$public_html_ssl_dir = $app_dir.$public_html_ssl_dir_name.DIRECTORY_SEPARATOR;

// which ip addresses can generate a new domain to be handled. If left empty, anyone can
$whitelist_ips = [];
if(file_exists($app_dir.'config.live.php')){
    include_once($app_dir.'config.live.php');
}