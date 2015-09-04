<?php

namespace Parse;

$loggedin = false;

if (isset($_SESSION["token"])) {
    ParseClient::initialize($app_id, $rest_key, $master_key);

    try {

        $thisuser = ParseUser::become($_SESSION["token"]);
        if ($thisuser) {
            $loggedin = true;
        }
    } catch (ParseException $exc) {
        
    }
}
?>
<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <?php if ($loggedin) { ?>
                <a class = "navbar-brand" href = "discover.php">Raed it!</a>
            <?php } else { ?>
                <a class = "navbar-brand" href = "index.php">Raed it!</a>
            <?php } ?>
        </div>
        <!--Collect the nav links, forms, and other content for toggling -->
        <div class = "collapse navbar-collapse" id = "bs-example-navbar-collapse-1">
            <ul class = "nav navbar-nav">
                <li>
                    <a href = "add.php">Submit</a>
                </li>
                <li>
                    <?php if ($loggedin) { ?>
                    <li><a href="user.php?u=<?php echo $thisuser->get('username'); ?>"><?php echo $thisuser->get('username'); ?></a></li>
                <?php } else { ?>
                    <a href = "discover.php">Discover</a>
                <?php } ?>


                </li>
            </ul>
            <ul class = "nav navbar-nav pull-right">
                <?php if ($loggedin) { ?>
                    <li><a href="logout.php">(<?php echo $thisuser->get('username'); ?>) Logout</a></li>
                <?php } else { ?>
                    <li><a href="login.php">Login</a></li>
                <?php } ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>

<?php //echo '<pre>' . var_export($user, true) . '</pre>';    ?>
