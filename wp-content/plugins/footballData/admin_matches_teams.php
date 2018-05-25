<?php

$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';
mb_internal_encoding("UTF-8");

$team_id = $_GET['id'];
$thumbnail_url = wp_get_attachment_url(get_post_thumbnail_id($team_id));


$the_query = new WP_Query(array(
    'post_type' => 'players',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => '_team',
            'value' => $team_id,
        ),
    ),
        ));

$players = array();

$pl=sortPlayers($the_query->get_posts());
$incap = explode(",", get_post_meta($team_id, '_incap', true));
if ($the_query->have_posts()) :
    foreach ($pl as $p):
        array_push($players, array(
            "id" => $p->ID,
            "name" => $p->post_title,
            "position" => substr(get_post_meta($p->ID, '_players_position', true), 0, 1),
            "number" => get_post_meta($p->ID, '_number', true),
            "incap"=>in_array($p->ID, $incap),
                )
        );

    endforeach;
endif;

$team = array($thumbnail_url, $players);
echo json_encode($team);


