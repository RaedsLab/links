<?php

namespace Parse;

session_start();

use Parse\ParseUser;
use Parse\ParseClient;
use Parse\ParseException;

require './autoload.php';
require './config.php';


ParseClient::initialize($app_id, $rest_key, $master_key);

$currentUser = NULL;

if (isset($_SESSION["token"])) {
    try {
        $currentUser = ParseUser::become($_SESSION["token"]);
        $currentUser = ParseUser::getCurrentUser();
    } catch (ParseException $exc) {
        unset($_SESSION["token"]);
        header('Location: ./login.php');
    }
}


if ($currentUser) {
    header('Location: ./user.php?u=' . $currentUser->get('username'));
} else {
    unset($_SESSION["token"]);
    header('Location: ./login.php');
}






