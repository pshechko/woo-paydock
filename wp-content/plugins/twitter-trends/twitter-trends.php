<?php
/*
  Plugin Name: Twitter Trends
  Description: Tweet map
  Version: 1.0
  Author: pshechko
 */

require "vendor/autoload.php";
use \Abraham\TwitterOAuth\TwitterOAuth;

define('CONSUMER_KEY', 'XL65oKSdkS6cYgcJ8rM8pgYlG');
define('CONSUMER_SECRET', 'YkS6FvQXSFhNgTjgGkMCOyMsyRWolnlasT95vIBWrVWiAuBuqe');

$access_token = '2974301745-axKbbsQGdhPmhwuP4frwPudmEifQxofTPTJWZpl';
$access_token_secret = 'bkvpP8SDqrf51CcWwynLMWgRYawwdQwQ6WFoMcYIMdm6U';

$connection = new  TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, $access_token_secret);


$ukraineId = $connection->get("geo/search", ["query" => "Kharkiv", "granularity"=>'city'])->result->places[0]->id;

wp_send_json($ukraineId);


$tweets = $connection->get("search/tweets", ["q" => "Putin place:{$ukraineId}", "count" => 5 ]);

wp_send_json($tweets);

foreach ($tweets->statuses as $i=>$tweet){
    //echo "<pre style='background: ".($i%2 ? "#d9eae9":"white"). "'>";
   // var_dump($tweet);
    //echo "</pre><br/>================================================<br/><br/>";
}



//echo "<pre>"; var_dump($tweets->statuses);echo "</pre>";
//die();