<?php
$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';
mb_internal_encoding("UTF-8");

$args = array(
    'post_type' => 'matches',
    'meta_query' => array(
        'relation' => 'AND',
    ),
);




$date_args = array(
    array(
        'key' => '_match_date',
        'value' => isset($_GET['from']) && $_GET['from'] ? $_GET['from'] : date('Y-m-d', strtotime('-7 days')),
        'compare' => '>=',
    ),
    array(
        'key' => '_match_date',
        'value' => isset($_GET['to']) && $_GET['to'] ? $_GET['to'] : date('Y-m-d'),
        'compare' => '<=',
    )
);



$args['meta_query'] = array_merge($args['meta_query'], $date_args);


if (isset($_GET['league']) && $_GET['league']) {

    $league_args = array(
        array(
            'key' => '_league',
            'value' => $_GET['league'],
            'compare' => '=',
        ),
    );

    $args['meta_query'] = array_merge($args['meta_query'], $league_args);
}

$matches_query = new WP_Query($args);

if ($matches_query->have_posts()):

    while ($matches_query->have_posts()):
        $matches_query->the_post();
        $match_id = get_the_ID();
        $team_1_id = get_post_meta($match_id, '_match_team_id_1', true);
        $team_2_id = get_post_meta($match_id, '_match_team_id_2', true);

        $team1 = get_the_title($team_1_id);
        $team2 = get_the_title($team_2_id);

        $team_1_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($team_1_id), "medium");
        $team_1_image_url = $team_1_image_url[0];

        $team_2_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($team_2_id), "medium");
        $team_2_image_url = $team_2_image_url[0];

        $score_1 = get_post_meta($match_id, '_score1', true);
        $score_2 = get_post_meta($match_id, '_score2', true);

        $league = get_post_meta($match_id, '_league', true);

        $date = get_post_meta($match_id, "_match_date", true);
        list($yyyy, $mm, $dd) = split("-", $date, 3);
        ?>
        <div class="match" league_id="<?= $league ?>">
            <a href="<?= get_the_permalink() ?>">
                <div class="m_team t1">
                    <span class="m_team_name">
                        <?= $team1; ?>
                    </span>
                    <div class="m_baclground_image_wr" style="background-image: url(<?= $team_1_image_url ?>)"></div>
                </div>
                <div class="m_scoe_and date">
                    <div>
                        <div class="m_score">
                            <?= $score_1 . " : " . $score_2 ?>
                        </div>
                        <div class="m_date">
                            <?= "<span class='tyre'>—</span> " . $dd . "/" . $mm . "/" . $yyyy . " <span class='tyre'>—</span>" ?>
                        </div>
                    </div>
                </div>
                <div class="m_team t2">
                    <span class="m_team_name">
                        <?= $team2; ?>
                    </span>
                    <div class="m_baclground_image_wr" style="background-image: url(<?= $team_2_image_url ?>)"></div>
                </div>
            </a>
        </div>
        <?php
    endwhile;

    wp_reset_postdata();
endif;
?>


<!--<pre>
<?php // var_dump($args); ?>
</pre>-->
