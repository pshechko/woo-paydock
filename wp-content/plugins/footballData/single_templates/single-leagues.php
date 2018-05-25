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


$today = getdate();

$the_query = new WP_Query(
        array(
    'post_type' => 'matches',
    'meta_key'          => '_match_date',
    'orderby'           => 'meta_value_num',
    'order'             => 'ASC',
    'posts_per_page' => 3,
    'after' => array(
        'year' => $today['year'],
        'month' => $today['mon'],
        'day' => $today['mday'],
    ),
    'meta_query' => array(
        array(
            'key' => '_league',
            'value' => $post_ID,
        ),
    ),
        )
);

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
		$league = get_the_ID();
		$args= array('class'=>'nav_image');
		?>
		<a href="<?php echo get_the_permalink($league) ?>">
			<?php echo get_the_post_thumbnail($league, array(70,70), $args) ?>
		</a>
	</div>
</div>
<div id="content" class="site-content">
    <div class="custom-site-content">
        <div class="composition" id="composition">
            <div class="back_gradient"></div>

            <div class="team-left-side <?php if (!($the_query->have_posts())){ echo' full_width';} ?>">
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


            <?php if ($the_query->have_posts()):  ?>
            <div class="team-right-side">

                <div class="rigth_table">
                    <div class="right-title-row">
                        <div class="right-title">
                            <span class="game-type">
                                <strong>Schedule for League</strong>
                                <span>Upcoming Games</span>
                            </span>
                        </div>
                    </div>
                    <?php
                    $count=0;
                    while($the_query->have_posts()){
                        $the_query->the_post();
                       $count++;
                       echo'<div class="right-row">
                        <div class="right-game"';
                       if($count==3)echo'-last';
                       echo'>';
                       build_table_from_array(setMatchArray(get_the_ID()));

                       echo'</div>
                    </div>';
                    }
                    wp_reset_postdata();
                    ?>

                </div>



            </div>
            <?php endif;  ?>

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


        <div class="player_info" id="player_info">
            <p class="info_title">Tables</p>


            <?php
            echo get_post_meta(get_the_ID(), '_tables', true)
            ?>

            <script>

                        var table = document.getElementById("player_info").children[1]; //background-color: #e5e9ef;
                        console.log(document.getElementById( "player_info").children[1]);
                        //console.log(table);
                        table.className = "team_table";
                        var rows = table.rows;
                        rows[0].className = "first_players_row";
                        rows[0].innerHTML = '<td colspan="2 "></td>' +
                        '<td colspan="5" class="ov erall_td">OVERALL</td>' +
                        '<td colspan="1"></td >' +
                        '<td colspan="5" class="home_td">HOME</td>' +
                        '<td colspan="1"> </td>' +
                        '<td colspan="5" class="away_td">AWAY</td >' +
                        '<td colspan="3"></td>';
                        rows[1].className = "second_teams_row";
                        rows[1].innerHTML = ' <td class="pos_td">POS</td>     <!--0-->' +
                        '<td class="team_td">TEAM</td>   <!--1-->' +
                        '<td class="small_td">P</td>     <!--2-->' +
                                    '<td class="small_td">W</td>     <!--3-->' +
                                                '<td class="small_td">D</td>     <!--4-->' +
                                                            '<td class="small_td">L</td>     <!--5-->'+
                    '<td class="small_td">F</td>     <!--6-->'+
                    '<td class="a_td">A</td>         <!--7-->'+

                    '<td class="small_td">P</td>     <!--8-->'+
                    '<td class="small_td">W</td>     <!--9-->'+
                    '<td class="small_td">D</td>     <!--10-->'+
                    '<td class="small_td">L</td>     <!--11-->'+
                    '<td class="small_td">F</td>     <!--12-->'+
                    '<td class="a_td">A</td>         <!--13-->'+

                    '<td class="small_td">P</td>     <!--14-->'+
                    '<td class="small_td">W</td>     <!--15-->'+
                    '<td class="small_td">D</td>     <!--16-->'+
                    '<td class="small_td">L</td>     <!--17-->'+
                    '<td class="small_td">F</td>     <!--18-->'+
                    '<td class="a_td_l">A</td>       <!--19-->'+

                    '<td class="gd_td">GD</td>       <!--20-->'+
                    '<td class="pts_td">PTS</td>     <!--21-->';




                //console.log(rows);
                var count = 0;
                for (var i = 2; i < rows.length; i++) {
                    var row = rows[i];
                    var cells = row.cells;
                    cells[0].className = "pos_td";
                    cells[1].className = "team_td_bold";

                    cells[2].className = cells[3].className = cells[4].className = cells[5].className = cells[6].className =
                            cells[8].className = cells[9].className = cells[10].className = cells[11].className = cells[12].className =
                            cells[14].className = cells[15].className = cells[16].className = cells[17].className = cells[18].className = "small_td";

                    cells[7].className = cells[13].className = "a_td";
                    cells[19].className = "a_td_l";
                    cells[20].className = "gd_td_bold";
                    cells[21].className = "pts_td_bold";
                    row.className = "teams_row";
                    if (count % 2 === 0)
                        row.style.backgroundColor = "#e5e9ef";
                    count++;
                }
            </script>
        </div>
        <div class="legend">
            <div class='subW'>


                <span><b>P:</b> GAME PLAYED</span>
                <span><b>W:</b> WINS </span>
                <span><b>D:</b> DRAWS</span>
                <span><b>A:</b> GAME PLAYED</span>
                <span><b>GD:</b> GAME PLAYED</span>
                <span><b>PTS:</b> GAME PLAYED</span>

            </div>

        </div>

        <div id="league_news" >
            <p class="info_title">League News</p>
            <?php
            echo do_shortcode('[league_news league_id="' . get_the_ID() . '"]');
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



    