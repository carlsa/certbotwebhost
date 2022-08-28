<?php
    $CERTBOT_VALIDATION = $_GET['CERTBOT_VALIDATION'];
    $CERTBOT_TOKEN = $_GET['CERTBOT_TOKEN'];
    $SIGNATURE = $_GET['SIGNATURE'];

    $HASH_SECRET = "------------CHANGE_ME------------";
    $HASH = hash('sha256', $CERTBOT_VALIDATION . $CERTBOT_TOKEN . $HASH_SECRET);

    if ($SIGNATURE === $HASH) {
        $basePath = dirname(__FILE__);
        $validation_file_path = "$basePath/.well-known/acme-challenge";

        if( is_dir($validation_file_path) === false )
        {
            mkdir($validation_file_path, 0744, true);
        }

        $myfile = fopen("$validation_file_path/$CERTBOT_TOKEN", "w");
        fwrite($myfile, $CERTBOT_VALIDATION);
        fclose($myfile);
        print "OK";
    } else {
        print "Error. Missing or invalid parameters.";
    }
?>
