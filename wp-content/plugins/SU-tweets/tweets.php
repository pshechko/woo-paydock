<?php

/*
  Plugin Name: SU Tweets
  Plugin URI: https://houstonapps.co/
  Description: Adds a tweet stream functionality
  Version: 1
  Author: pshechko
  Author URI: https://houstonapps.co/
 */

use Abraham\TwitterOAuth\TwitterOAuth;

define("SUURL", plugins_url('', __FILE__));
define("SUDIR", plugin_dir_path(__FILE__));
define("TWITTERUSER", 'pshechko@outlook.com');
define("TWITTERPASS", 'elche#96');
define("TWITTERCONSUMERKEY", "fIBAlUQQOdPPs9TBCjiakGCUU");
define("TWITTERCONSUMERSECRETKEY", "A1qnxfeCRKNSrhw8Gna4xaLLE9jYLm4Kb0CAOCnc3bvlL96zRK");
define("TWITTERACCESSTOKEN", "2974301745-QFODn1NrIRC4CMjcjjcQVQffoAoMV8n0WbFQ8P5");
define("TWITTERACCESSTOKENSECRET", "3v9jC6EEsLyhEUNfhOq3nIDSF1abM5nFUkJComqV0lkTX");


require('vendor/twitteroauth/autoload.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$connection = new TwitterOAuth(TWITTERCONSUMERKEY, TWITTERCONSUMERSECRETKEY, TWITTERACCESSTOKEN, TWITTERACCESSTOKENSECRET);
$results = $connection->get("search/tweets",[
    "include_entities" => true,
    "q" => "#2017SUDypoTest",
    "count" => 5
]);
