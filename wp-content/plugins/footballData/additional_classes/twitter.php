<?php
$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';
mb_internal_encoding("UTF-8");

define('CONSUMER_KEY', get_option("consumerKey"));
define('CONSUMER_SECRET', get_option("consumerSecret"));
define('ACCESS_TOKEN', get_option("accessToken"));
define('ACCESS_TOKEN_SECRET', get_option("accessTokenSecret"));

require_once("twitterClasses.php");
//clearTweets();

$toa = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

$postcount = isset($_GET['count']) ? $_GET['count'] : 100;

$hashtag = isset($_GET['hashtag']) ? $_GET['hashtag'] : get_option("hashtag");

$query = array(
    "result_type" => "mixed",
    "include_entities" => "true",
    "q" => "#".$hashtag,
    'count' => $postcount,
);

$results = $toa->get('search/tweets', $query);


global $wpdb;

$table_name = $wpdb->prefix . "tweets";

$existingTweets = $wpdb->get_results("SELECT * FROM $table_name");


function tweetIsHashed($id) {
    global $existingTweets;
    // var_dump($existingTweets);
    if (!empty($existingTweets)) {
        foreach ($existingTweets as $exTweet) {
            if ($exTweet->id == $id) {
                return true;
            }
        }
    }
    return false;
}

$count = 0;

if (!empty($results)&& (is_object($results))) {
    foreach ($results->statuses as $result) {
        if (tweetIsHashed($result->id)) {
            continue;
        }
        $tst = $result->id;
        $count++;
        $tweet = new stdClass;
        $tweet->id = $result->id;
        $tweet->hashtag = $hashtag;
        $tweet->author = $result->user->screen_name;
        $text = $result->text;
        $tweet->isHashed=true;
        $words = explode(" ", $text);

        $i_min = null;
        $i_action = null;

        $new_text = " ";
        
        $rt=false;

        for ($i = 0; $i < count($words); $i++) {
            if (strcmp($words[$i], "#min") == 0) {
                $tweet->min = $words[$i + 1];
                $i_min = $i;
                continue;
            }
            if (strcmp($words[$i], "#action") == 0) {
                $tweet->act = $words[$i + 1];
                $i_action = $i;
                continue;
            }
            if (strrchr($words[$i], "#") != false) {
                continue;
            }

            if ((($i_min !== null) && ($i == $i_min + 1)) || (($i_action !== null) && ($i == $i_action + 1))) {
                continue;
            }
            
             if ($words[$i] === "RT") {
                $rt=true;
            }

            $new_text = $new_text . " " . $words[$i];

            if ($i != count($words) - 1) {
                $new_text = $new_text . " ";
            }
        }
        if($rt) continue;
        $tweet->txt = $new_text;

        global $existingTweets;
        if (is_array($existingTweets)) {
            array_push($existingTweets, $tweet);

            $wpdb->insert( 
                $table_name,
                array( 
                    'id' => $tweet->id, 
                    'txt' => $tweet->txt ,
                    'min' => $tweet->min ,
                    'act' => $tweet->act ,
                    'author' => $tweet->author ,
                    'hashtag' => $tweet->hashtag ,
                )
            );
        }
    }
}



if (is_array($existingTweets)) {
  

    $exTweets = array_reverse($existingTweets);

    foreach ($exTweets as $exTweet) :
        if (strcmp($exTweet->hashtag, $hashtag) == 0):
            ?>

            <div class='evenpost<?php if($exTweet->isHashed)echo" tst";?>'>
                <div class='pretwitmin'>
            <?php echo $exTweet->min; ?>
                </div>
                <div class='pretwitact'>
            <?php echo $exTweet->act; ?>
                </div>
                <div class='twit'>
            <?php echo $exTweet->txt; ?>      
                </div>
            </div>

            <?php
        endif;
    endforeach;
}

