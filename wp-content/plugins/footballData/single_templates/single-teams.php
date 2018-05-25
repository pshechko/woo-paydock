<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
get_header();
wp_reset_postdata();
$post_ID = get_the_ID();
$the_query = new WP_Query(
        array(
    'post_type' => 'matches',
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => '_match_team_id_1',
            'value' => get_the_ID($post_ID),
        ),
        array(
            'key' => '_match_team_id_2',
            'value' => get_the_ID($post_ID),
        )
    ),
        )
);


$upcoming = -1;
$last = -1;
$previous = -1;

$id_upcoming = -1;
$id_last = -1;
$id_previous = -1;

$min_next_date = "9999-99-99";
$max_date = "0000_00_00";

$matches = $the_query->get_posts();

//echo "<p> Before sort (all posts):<br/>";

for ($i = 0; $i < count($matches); $i++) {

    $id = $matches[$i]->ID;
    $match_date = get_post_meta($id, '_match_date', true);
    
    //echo $id." | ".$match_date."<br/>";
    
}
//echo "</p><p>After sort (all posts):<br/>";



for ($i = count($matches) - 1; $i > 0; $i--) {
    for ($j = 0; $j < $i; $j++) {
        $id1 = $matches[$j]->ID;
        $id2 = $matches[$j + 1]->ID;
        if (get_post_meta($id1, '_match_date', true) > get_post_meta($id2, '_match_date', true)) {
            $tmp = $matches[$j];
            $matches[$j] = $matches[$j + 1];
            $matches[$j + 1] = $tmp;
        }
    }
}

for ($i = 0; $i < count($matches); $i++) {

    $id = $matches[$i]->ID;
    $match_date = get_post_meta($id, '_match_date', true);
    
   // echo $id." | ".$match_date."<br/>";
    
}
//echo"</p>";


$today = getdate();
$dd = $today['mday'] > 9 ? $today['mday'] : '0' . $today['mday'];
$mm = $today['mon'] > 9 ? $today['mon'] : '0' . $today['mon'];
$yyyy = $today['year'];
$today = $yyyy . "-" . $mm . "-" . $dd;


for ($i = 0; $i < count($matches); $i++) {

    $id = $matches[$i]->ID;
    $match_date = get_post_meta($id, '_match_date', true);

    if ($match_date >= $today) {
        if ($id_upcoming == -1) {
            $id_upcoming = $id;
        }
        break;
    } else {
        $id_last = $id;
        if ($i > 0) {
            $prev_id = $matches[$i - 1]->ID;
            $prev_match_date = get_post_meta($prev_id, '_match_date', true);
            $id_previous = $prev_id;
        }
    }
}



if ($id_upcoming >= 0) {
    $upcoming = setMatchArray($id_upcoming,$post_ID);
    //echo "<br/>Upcomming: ".$id_upcoming."<br/>";
}
if ($id_last >= 0) {
    $last = setMatchArray($id_last,$post_ID);
   // echo "<br/>Last: ".$id_last."<br/>";
}
if ($id_previous >= 0) {
    $previous = setMatchArray($id_previous,$post_ID);
   // echo "<br/>Previous: ".$id_previous."<br/>";
}



wp_reset_postdata();
?>

<link href='http://fonts.googleapis.com/css?family=Asap:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Bitter:400,700' rel='stylesheet' type='text/css'>
<?php do_shortcode('ssba') ?>
<div class="top-bar clearfix">
	<div class="entry-meta">
		<?php echo do_shortcode('[ssba]');?> 
	</div>
	<div class="league-logo">
		<?php
		$league = get_post_meta(get_the_ID(), "_league", true);
		$args= array('class'=>'nav_image');
		?>
		<a href="<?php echo get_the_permalink($league) ?>">
			<?php echo get_the_post_thumbnail($league, array(100,100), $args) ?>
		</a>
	</div>
</div>
<div id="content" class="site-content">
    <div class="custom-site-content">
        <div class="composition" id="composition">
	        <div class="back_gradient"></div>
            <?php
            $args = array('class' => "featured_image");
//echo get_the_post_thumbnail(get_the_ID(), array(10000, 300), $args);
            echo'<img id="teams_back_image" name="teams_back_image" src ="' . get_post_meta(get_the_ID(), '_teams_back_image_url', true) . '" ?>';
            ?>

            <div class="team-left-side">
                <div id="emblem_n_name" class="emblem_n_name">
                    <div id="emblem" class="emblem">
                        <?php
                        $args = array('class' => "teams_emblem");
                        echo get_the_post_thumbnail(get_the_ID(), array(300, 300), $args);
                        ?>
                    </div>
                    <div class="name_wrap">
                        <span class="team_name">
                            <?php echo strtoupper(get_the_title(get_the_ID())); ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="team-right-side">
	            <div class="rigth_table">
		            <div class="right-title-row">
			            <div class="right-title">
                            <span class="game-type">
                                <strong>Schedule for Team</strong>
                            </span>
			            </div>
		            </div>
                    <div class="right-row">
                        <div class="right-game">
                            <span class="game-type">
                                <span>Upcoming Games</span>
                            </span>
                            <?php build_table_from_array($upcoming); ?>
                        </div>
                    </div>
                    <div class="right-row">
                        <div class="right-game">
                            <span class="game-type">
                                <span>Last Game Result</span>
                            </span>
                            <?php build_table_from_array($last); ?>
                        </div>
                    </div>
                    <div class="right-row-last right-row">
                        <div class="right-game">
                            <span class="game-type">
                                <span>Previous Game</span>
                            </span>
                            <?php build_table_from_array($previous); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!--<div class="name_position">
                <p class="player_name">
            <?php // echo get_the_title(get_the_ID());          ?>
                </p>
                <p class="player_position">
            <?php //echo strtoupper(get_post_meta(get_the_ID(), "_players_position", true));         ?>
                </p>
            </div>-->
        </div>
        <div id="players_description" class="players_description">

            <?php
            $content_post = get_post(get_the_ID());
            $content = $content_post->post_content;
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);
            echo $content;
            ?>
        </div>


        <div class="player_info"> 
            <p class="info_title">Team Squad</p>
            <table class="team_table">
                <tr class="first_players_row">
                    <td class="number_td">#</td>
                    <td class="member_td">MEMBER</td>
                    <td class="position_td">POSITION</td>
                    <td class="played_td">PLAYED</td>
                    <td class="goals_td">GOALS</td>
                    <td class="ycy_td">YCY</td>
                    <td class="rcr_td">RCR</td>
                </tr>
                <?php
                $post_ID = get_the_ID();
                $the_query = new WP_Query(
                        array('post_type' => 'players',
                    //'meta_key' => '_team',
                    //'meta_value' => get_the_title($post_ID),
                    'meta_key' => '_number',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC',
					'posts_per_page'=>-1,
                    'meta_query' => array(
                        array(
                            'key' => '_team',
                            'value' => $post_ID,
                        ),
                    ),
                ));
                $array_rev = array_reverse($the_query->posts);
                $the_query->posts = $array_rev;


                if ($the_query->have_posts()) {
                    $count = 1;
                    while ($the_query->have_posts()) {
                        echo"<tr";
                        if ($count % 2 == 0) {
                            echo " class='even_players_row'";
                        }
                        echo">";
                        $the_query->the_post();
                        $id = get_the_ID();
                        echo'<td class="number_td">';
                        echo get_post_meta($id, '_number', true) . "  ";
                        echo'</td><td class="member_td">';
                        echo get_the_title() . "  ";
                        echo'</td><td class="position_td">';
                        echo get_post_meta($id, '_players_position', true) . "  ";
                        echo'</td><td class="played_td">';
                        echo get_post_meta($id, '_appearances', true) . "  ";
                        echo'</td><td class="goals_td">';
                        echo get_post_meta($id, '_goals', true) . "  ";
                        echo'</td><td class="ycy_td">';
                        echo get_post_meta($id, '_yellow_cards', true) . "  ";
                        echo'</td><td class="rcr_td">';
                        echo get_post_meta($id, '_red_cards', true) . "  ";
                        echo'</td>';
                        $count++;
                        echo'</tr>';
                    }
                }
                ?>
            </table>
        </div>

        <div id="league_news" >
            <p class="info_title">League News</p>
            <?php
            do_shortcode( '[league_news league_name="'. get_post_meta($id, '_league', true).'"]' );
            ?>

        </div>
    </div>

    <?php
    if (comments_open() || get_comments_number()) {
	    comments_template();
    }
    ?>


	</div>
</div>
</div>
<?php get_sidebar();?>
<?php get_footer();?>

    