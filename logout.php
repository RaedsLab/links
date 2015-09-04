<?php

namespace Parse;

session_start();

use Parse\ParseUser;

require './autoload.php';
require './config.php';
ParseClient::initialize($app_id, $rest_key, $master_key);

try {

    $user = ParseUser::become($_SESSION["token"]);
//var_dump($user);
    if ($user) {
        $user->logOut();
    }
} catch (ParseException $exc) {
    
} finally {


    if (isset($_SESSION["token"])) {
        unset($_SESSION["token"]);
    }

    header('Location: ./login.php', true, 302);

    exit;
}


