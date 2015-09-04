<?php

namespace Parse;

use Parse\ParseUser;
use Parse\ParseClient;

require './autoload.php';
require './config.php';

session_start();

if ((isset($_POST['name']) && isset($_POST['fbid'])) && isset($_POST['token'])) {

    ParseClient::initialize($app_id, $rest_key, $master_key);
    $FbUser = new ParseUser();
    $FbUser->logInWithFacebook($_POST['fbid'], $_POST['token']);

    $FbUser = ParseUser::getCurrentUser();
    $FbUser->set("name", $_POST['name']);
    
    if (isset($_POST['email'])) {
        $FbUser->set("email", $_POST['email']);
    }
    
    $FbUser->save();

    echo "ok";
} else {
    echo "error";
}



