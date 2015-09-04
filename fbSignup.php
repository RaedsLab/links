<?php

namespace Parse;

session_start();

use Parse\ParseUser;
use Parse\ParseClient;

require './autoload.php';
require './config.php';

ParseClient::initialize($app_id, $rest_key, $master_key);

$FbUser = new ParseUser();

$FbUser->logInWithFacebook("859344777490207", "CAAHTaYH1lj8BABbKSNMtfziV3aqlZClMWFXx0Aw5MlwboiKOIt6IyKjZCzqjblVQ1PCHVZAhTZBLLcmRwwZCZBZCnlTEhQMHHfuNMg33osufNewnnoMc7T8D8y2lbrIpMLI5BZB0UK6m9txvVUVHRr5j7s0dwxRt9yZBZCZCQ4VfeuwCABRiuqVp83JpJlomonrXzd4YB2pKUjzTwZDZD");


