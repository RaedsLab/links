<?php

namespace Parse;

error_reporting(E_ERROR | E_PARSE);

use Parse\ParseQuery;
use Parse\ParseUser;
use Parse\ParseClient;

require './autoload.php';
require './config.php';

ParseClient::initialize($app_id, $rest_key, $master_key);

$linkQuery = new ParseQuery("Link");
$linkQuery->addDescending('createdAt');
$linkQuery->exists('img');
$linkQuery->limit(12);
$links = $linkQuery->find();

session_start();
?>


<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Discover links</title>

        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="css/3-col-portfolio.css" rel="stylesheet">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body>

        <?php require './nav.php'; ?>

        <?php //echo '<pre>' . var_export($links, true) . '</pre>';  ?>

        <!-- Page Content -->
        <div class="container">

            <!-- Page Header -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Discover cool links
                        <small>Programmatically generated</small>
                    </h1>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">

                <!-- Projects Row -->
                <?php
                for ($index = 0; $index < count($links); $index++) {
                    if ($index > 0 && $index % 3 == 0) {
                        ?>
                    </div>
                    <div class="row">


                        <?php
                    }
                    ?>
                    <div class="well-sm well col-md-4 portfolio-item">
                        <a href="link.php?q=<?php echo $links[$index]->getObjectId(); ?>">
                            <img class="img-responsive" src="<?php echo $links[$index]->get('img')->getURL(); ?>" alt="<?php echo $links[$index]->get('title') ?>">
                        </a>
                        <h3>
                            <a href="link.php?q=<?php echo $links[$index]->getObjectId(); ?>"><?php echo $links[$index]->get('title') ?></a>
                        </h3>
                        <p><?php echo $links[$index]->get('description') ?></p>
                    </div>


                    <?php
                }
                ?>
            </div>


            <!-- /.row -->

            <?php require './footer.php'; ?>