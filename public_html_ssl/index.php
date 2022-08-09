<?php
include("../config.php");
$domain = $_SERVER['SERVER_NAME'];
if($domain != $server_host_name){
    header("Location: https://www.".$_SERVER['SERVER_NAME']."");
    exit();
} else {
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
