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

    try {
        $user = ParseUser::become($_SESSION["token"]);
    } catch (ParseException $exc) {
        unset($_SESSION["token"]);
        header('Location: ./login.php');
    }
}
if ($user == null) {
    unset($_SESSION["token"]);
    header('Location: ./login.php');
}


/////
$emailvalid = false; // test if the passed EMAIL is valid 
$errors = array(); // a list of erros (cleared after request)
$isPost = false; // check if post request is sent

if (isset($_POST['p_email']) && $_POST['p_email'] != "") {
    $isPost = true;
    $email = $_POST['p_email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $emailvalid = true;
    } else {
        array_push($errors, "The provided Email is not valid");
    }

    if ($emailvalid && empty($errors)) {
        //  $user = ParseUser::getCurrentUser();
        $changed = false;
        if ($user->get("email") == NULL) {
            $user->set("email", $email);
            $user->save();
            ParseUser::requestPasswordReset($email);
            $changed = true;
        } else if ($user->get("email") == $email) {
            ParseUser::requestPasswordReset($email);
            $changed = true;
        } else {
            array_push($errors, "this is not your email");
        }
        if ($changed) {
            $user->logOut();
            if (isset($_SESSION["token"])) {
                unset($_SESSION["token"]);
            }
            header('Location: ./login.php', true, 302);
            exit;
        }
    }
}
?>

<?php if (!$isPost || !empty($errors)) {
    ?>



    <!DOCTYPE html>
    <html lang="en">

        <head>

            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="description" content="">
            <meta name="author" content="">

            <title>Reset your password</title>

            <!-- Bootstrap Core CSS -->
            <link href="css/bootstrap.min.css" rel="stylesheet">

            <!-- Custom CSS -->
            <style>
                body {
                    padding-top: 70px;
                    /* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
                }
            </style>

            <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
            <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
            <!--[if lt IE 9]>
                <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
                <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
            <![endif]-->

        </head>
        <body>

            <?php include './nav.php'; ?>


            <!-- Page Content -->
            <div class="container">

                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h1>Put your email</h1>
                        <?php
                        $errors = array_filter($errors);

                        if (!empty($errors)) {
                            echo '<p>';
                            foreach ($errors as $e) {
                                echo '<b>' . $e . '</b><br>';
                            }
                            echo '</p>';
                        }
                        unset($errors);
                        ?>

                        <form action = "reset.php" method = "post" >
                            <label>Email <input name = "p_email" type = "email" required="required" ></label>
                            <br>
                            <button type = "submit">Submit</button>
                        </form>
                    </div>
                </div>
                <!-- /.row -->

            </div>
            <!-- /.container -->

            <?php include './footer.php'; ?>


            <?php
        }
        ?>