<?php

namespace Parse;

session_start();

use Parse\ParseUser;
use Parse\ParseException;
use Parse\ParseClient;

require './autoload.php';
require './config.php';

ParseClient::initialize($app_id, $rest_key, $master_key);

if (isset($_SESSION["token"])) {
    $user = ParseUser::become($_SESSION["token"]);

    $currentUser = ParseUser::getCurrentUser();
    if ($currentUser) {
        header('Location: ./user.php?u=' . $user->get('username'));
    } else {
        unset($_SESSION["token"]);
    }
}

$errors = array(); // a list of erros (cleared after request)

if (isset($_POST['p_username']) && $_POST['p_username'] != "") {
    if (isset($_POST['p_pwd']) && $_POST['p_pwd'] != "") {

        try {
            $user = ParseUser::logIn($_POST['p_username'], $_POST['p_pwd']);
            $_SESSION["token"] = $user->getSessionToken();

            header('Location: ./user.php?u=' . $user->get('username'));
        } catch (ParseException $error) {
            array_push($errors, $error->getMessage());
        }


////////////////////////////////////////////////////////////
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | Raed.it</title>

        <!-- CSS -->
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="assets/css/form-elements.css">
        <link rel="stylesheet" href="assets/css/style.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Favicon and touch icons -->
        <link rel="shortcut icon" href="assets/ico/favicon.png">
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">

    </head>

    <body>

        <!-- Top content -->
        <div class="top-content">

            <div class="inner-bg">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2 text">
                            <h1><strong>Raed.it</strong> Login Form</h1>
                            <div class="description">
                                <p>
                                    This app is in closed alpha. 
                                    You can request an invite <a href="http://raed.tn"><strong>HERE</strong></a>!
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 form-box">
                            <div class="form-top">
                                <div class="form-top-left">
                                    <h3>Login</h3>
                                    <p>Enter your username and password to log:</p>
                                    <?php
                                    $errors = array_filter($errors);
                                    if (!empty($errors)) {
                                        echo '<p>';
                                        foreach ($errors as $e) {
                                            echo '<b>' . $e . '<b><br>';
                                        }
                                        echo '</p><hr>';
                                    }
                                    unset($errors);
                                    ?>
                                </div>
                                <div class="form-top-right">
                                    <i class="fa fa-key"></i>
                                </div>
                            </div>
                            <div class="form-bottom">
                                <form role="form" action="login.php" method="post" class="login-form">
                                    <div class="form-group">
                                        <label class="sr-only" for="p_username">Username</label>
                                        <input type="text" name="p_username" placeholder="Username..." class="form-username form-control" id="form-username">
                                    </div>
                                    <div class="form-group">
                                        <label class="sr-only" for="p_pwd">Password</label>
                                        <input type="password" name="p_pwd" placeholder="Password..." class="form-password form-control" id="form-password">
                                    </div>
                                    <button type="submit" class="btn">Sign in!</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 social-login">
                            <h3>...or soon login with:</h3>
                            <div class="social-login-buttons">
                                <a disabled="true" class="btn btn-link-1 btn-link-1-facebook" href="#">
                                    <i class="fa fa-facebook"></i> Facebook
                                </a>
                                <a disabled="true" class="btn btn-link-1 btn-link-1-twitter" href="#">
                                    <i class="fa fa-twitter"></i> Twitter
                                </a>
                                <a disabled="true" class="btn btn-link-1 btn-link-1-google-plus" href="#">
                                    <i class="fa fa-google-plus"></i> Google Plus
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <!-- Javascript -->
        <script src="assets/js/jquery-1.11.1.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.backstretch.min.js"></script>
        <script src="assets/js/scripts.js"></script>

        <!--[if lt IE 10]>
            <script src="assets/js/placeholder.js"></script>
        <![endif]-->

    </body>
</html>
