<?php

namespace Parse;

use Parse\ParseQuery;
use Parse\ParseUser;
use Parse\ParseException;
use Parse\ParseClient;

require './autoload.php';
require './config.php';


$today = false; // check if query today's posts

if (isset($_GET['t'])) {
    $today = true;
}


if (!(isset($_GET['u']) && $_GET['u'] != "")) {
    header("Status: 404 Not Found");
    header("Location: ./404.php");
}
ParseClient::initialize($app_id, $rest_key, $master_key);

try {

    $userQuery = ParseUser::query();
    $userQuery->equalTo("username", $_GET['u']);
    $users = $userQuery->find();
    if (count($users) == 0) {
        header("Status: 404 Not Found");
        header("Location: ./404.php");
    }
    $user = $users[0];
} catch (ParseException $exc) {

    header("Status: 404 Not Found");
    header("Location: ./404.php");
}



$linkQuery = new ParseQuery("Link");
$linkQuery->equalTo("user", $user);
$links = $linkQuery->find();

session_start();

$current_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title><?php echo $user->get('username') ?>'s list</title>

        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="css/1-col-portfolio.css" rel="stylesheet">

        <link href="css/style.css" rel="stylesheet">


        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body>
        <div id="fb-root"></div>

        <?php include './nav.php'; ?>
        <div class="container">

            <!-- Page Content -->
            <?php
            if (null!=ParseUser::getCurrentUser() && $user->get('username') == ParseUser::getCurrentUser()->get('username')) {
                ?>
                <div class="row">
                    <div class="col-lg-10 col-lg-offset-1 col-md-12 col-sm-12 well-lg">
                        Hello, this is your personal area where all you links are shown
                    </div>
                </div>
                <?php
            }
            ?>


            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?php echo $user->get('username') ?>
                        <small>
                            <?php
                            if ($today) {
                                echo date("d/m/Y ");                         // 03.10.01
                            }
                            ?>
                            catered list</small>
                    </h1>
                </div>
            </div>
            <!-- /.row -->
            <style>
                .imgft{
                    display: block;
                    margin: 0 auto;
                }
            </style>

            <?php
            if (count($links) < 1) {
                echo '<h1>No Links available...</h1>';
            }

            foreach ($links as $link) {
                if ($today) {
                    $date = new \DateTime();
                    if ($date->diff($link->getCreatedAt())->days >= 1) {
                        break;
                    }
                }
                ?>
                <div class="row">
                    <div class="col-md-7">
                        <a href="<?php echo $link->get('url') ?>">
                            <img class="imgft" style="max-width:630px; max-height:300px;" 
                                 src="<?php echo $link->get('img')->getURL(); ?>" alt="<?php echo $link->get('title') ?>">
                        </a>
                    </div>
                    <div class="col-md-5">
                        <h3><?php echo $link->get('title') ?></h3>
                        <?php if ($today) { ?>
                            <p><small><?php echo ($link->getCreatedAt()->format('H:i')) ?></small></p>
                        <?php } else { ?>
                            <p><small><?php echo ($link->getCreatedAt()->format('Y-m-d H:i')) ?></small></p>
                        <?php } ?>
                        <p><?php echo $link->get('description') ?></p>
                        <a class="btn btn-primary" href="link.php?q=<?php echo $link->getObjectId() ?>">Dicuss<span class="glyphicon glyphicon-chevron-right"></span></a>
                    </div>
                </div>
                <hr>

                <!-- /.row -->
                <?php
            }
            ?>

            <div class="col-lg-10 col-lg-offset-1">
                <div class="fb-comments" data-href="<?php echo $current_link ?>" data-numposts="5"></div>
            </div>

            <script>(function (d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id))
                        return;
                    js = d.createElement(s);
                    js.id = id;
                    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));</script>


            <?php include './footer.php'; ?>
