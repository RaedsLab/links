<?php

namespace Parse;

session_start();

use Parse\ParseObject;
use Parse\ParseACL;
use Parse\ParseUser;
use Parse\ParseFile;
use Parse\ParseException;
use Parse\ParseClient;

require './autoload.php';
require './config.php';
require './LinkManager.php';

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
$urlValid = false; // test if the passed URL is valid 
$titleProvided = false; // test if the Title is passed 
$errors = array(); // a list of erros (cleared after request)
$isPost = false; // check if post request is sent
$title = "";

if (isset($_POST['p_link']) && $_POST['p_link'] != "") {
    $isPost = true;

    $url = $_POST['p_link'];

    try {
        // SET URL
        $lm = new \LinkManager($url);
        $urlValid = true;

        // SET TITLE
        if (isset($_POST['p_title']) && $_POST['p_title'] != "") {
            $lm->setTitle($_POST['p_title']);
        }

        $ln = $lm->getLink();
        $link = new ParseObject("Link");

        $link->set("url", $ln['url']);
        $link->set("title", $ln['title']);
        $link->set("description", $ln['description']);
        $link->set("hn", $ln['hn']);
        $link->set("hn_points", $ln['hn_points']);
        $link->set("type", $ln['type']);

        $link->set("user", $user);

        $file = ParseFile::createFromFile($ln['img'], "photo.png");
        $file->save();

        $link->set("img", $file);

        $linkACL = ParseACL::createACLWithUser(ParseUser::getCurrentUser());
        $linkACL->setPublicReadAccess(true);
        $link->setACL($linkACL);


        try {
            $link->save();
            echo '<br>';
            echo 'New object created with objectId: ' . $link->getObjectId();

            header('Location: link.php?q=' . $link->getObjectId());
        } catch (ParseException $ex) {
            // Execute any logic that should take place if the save fails.
            // error is a ParseException object with an error code and message.
            echo 'Failed to create new object, with error message: ' . $ex->getMessage();
        }
///
    } catch (Exception $exc) {
        echo "You got an error in the link.";
    }



    ////////////////////////////////////////////////////////////
}
?>


<?php if (!$isPost || !empty($errors)) { ?>



    <!DOCTYPE html>
    <html lang="en">

        <head>

            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="description" content="">
            <meta name="author" content="">

            <title>Submit link</title>

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
                        <h1>Add a link</h1>
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

                        <form action = "add.php" method = "post" >
                            <label>Title <input placeholder="Optional" width="100" name = "p_title" type = "text"></label>
                            <br>
                            <label>Link <input name = "p_link" type = "url" required="required" ></label>
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