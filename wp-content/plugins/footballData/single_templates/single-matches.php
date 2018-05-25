<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
get_header();
?>



<link href='http://fonts.googleapis.com/css?family=Asap:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Bitter:400,700' rel='stylesheet' type='text/css'>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
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
<?php
// Start the Loop.
while (have_posts()) : the_post();
    ?>
    <div class="composition_match_wrapper">
        <div class="composition_match">
            <div class="row_div">
                <div class="cell-div th_wr"style='background: url(<?php echo wp_get_attachment_url( get_post_thumbnail_id(get_post_meta(get_the_ID(), "_match_team_id_1", true)) );?>)'>
                    <?php //echo get_the_post_thumbnail(get_post_meta(get_the_ID(), "_match_team_id_1", true), 'post-thumbnail', $args); ?>
                </div>
                <div class="scorefield">
                    <?php
                    if (get_post_meta(get_the_ID(), "_show_score", true)) {
                        echo get_post_meta(get_the_ID(), "_score1", true) . ":" . get_post_meta(get_the_ID(), "_score2", true);
                    } else {
                        echo " VS ";
                    }
                    ?>

                </div>
                <div class="cell-div th_wr" style='background: url(<?php echo wp_get_attachment_url( get_post_thumbnail_id(get_post_meta(get_the_ID(), "_match_team_id_2", true)) );?>)'>
                    <?php// echo get_the_post_thumbnail(get_post_meta(get_the_ID(), "_match_team_id_2", true), 'post-thumbnail', $args); ?>
                </div>
            </div>
            <div class="row_div">
                <div class="match_team_name">
                    <?php echo get_the_title(get_post_meta(get_the_ID(), "_match_team_id_1", true)); ?>
                </div>
                <div class="scorefield"></div>
                <div class="match_team_name">
                    <?php echo get_the_title(get_post_meta(get_the_ID(), "_match_team_id_2", true)); ?>
                </div>
            </div>
        </div>

        <div class="composition_match_date" >
            <?php
            $date = get_post_meta(get_the_ID(), "_match_date", true);
            list($yyyy, $mm, $dd) = split("-", $date, 3);
            echo "— " . $dd . "/" . $mm . "/" . $yyyy . " —"
            //echo get_post_meta(get_the_ID(), "_match_date", true); 
            ?>

        </div>
    </div>
    <p class="info_title">Match Preview</p>
    <div id="teams_description" class="players_description">

        <?php
        // $content_post = get_post(get_the_ID());
        //$content = $content_post->post_content;
        //$content = apply_filters('the_content', $content);
        //$content = str_replace(']]>', ']]&gt;', $content);
        the_content();
        ?>
    </div>










    <div class="spoil">
        <div class="smallfont">
            <div  id="input-b" class="input-b" onclick="spoil(this)">
                    <p class="info_title">
                        Lineups 
                        <i class="fa fa-chevron-circle-down ">
                            
                        </i>
                    </p>
            </div>
        </div>


         <div class="lineups_wrapper">
            <div id="hidden_part"  class='hidden_spoiler'>
                <div class="lineups_sub_wrapper1 lineups_sub_wrapper" id='big_wrapper1'>
                    <?php echo get_the_title(get_post_meta(get_the_ID(), "_match_team_id_1", true)); ?>
                    <div class="lineups_team clearfix">
                        <?php
                         $ft1 = explode(",",get_post_meta(get_the_ID(), '_ft1', true));
                         $sb1 = explode(",",get_post_meta(get_the_ID(), '_sb1', true));
                        ?>
                        <div class="lineups_team_sub_wrapper1 lineups_team_sub_wrapper" id='lineups_sub_wrapper1'>
                            
                            
                            <span class="first_team_title">First team</span><br/>
                            <table>
                            <?php
                            foreach($ft1 as $ft):
                                    ?>
                             <tr class="pltr">
                                    <td>
                                        <?php echo substr(get_post_meta($ft, '_players_position', true),0,1); ?>
                                    </td>
                                    <td>
                                        <?php echo get_post_meta($ft, '_number', true); ?>
                                    </td>
                                    <td>
                                        <?php echo get_the_title($ft); ?>
                                    </td>
                               </tr>
                            
                                    <?php
                                endforeach;
                            ?>
                               </table>
                            <?php //echo apply_filters('the_content', get_post_meta(get_the_ID(), '_first_team_1_players', true)); ?>
                            
                        </div>

                        <div class="lineups_team_sub_wrapper2 lineups_team_sub_wrapper" id='lineups_sub_wrapper2'>
                            <span class="first_team_title">Substitute</span><br/>
                            <table>
                            <?php
                            foreach($sb1 as $sb):
                                    ?>
                             <tr class="pltr">
                                    <td>
                                        <?php echo substr(get_post_meta($sb, '_players_position', true),0,1); ?>
                                    </td>
                                    <td>
                                        <?php echo get_post_meta($sb, '_number', true); ?>
                                    </td>
                                    <td>
                                        <?php echo get_the_title($sb); ?>
                                    </td>
                               </tr>
                            
                                    <?php
                                endforeach;
                            ?>
                               </table>
                            <?php// echo apply_filters('the_content', get_post_meta(get_the_ID(), '_substitute_1_players', true)); ?>
                        </div>


                    </div>
                </div>
                <div class="lineups_sub_wrapper2 lineups_sub_wrapper" id='big_wrapper2'>
                    <?php echo get_the_title(get_post_meta(get_the_ID(), "_match_team_id_2", true)); ?>					
                    <div class="lineups_team clearfix">
                        <?php
                         $ft2 = explode(",",get_post_meta(get_the_ID(), '_ft2', true));
                         $sb2 = explode(",",get_post_meta(get_the_ID(), '_sb2', true));
                        ?>
                        <div class="lineups_team_sub_wrapper1 lineups_team_sub_wrapper" id='lineups_sub_wrapper3'>
                            <span class="first_team_title">First team</span><br/>
                            <table>
                            <?php
                            foreach($ft2 as $ft):
                                    ?>
                             <tr class="pltr">
                                    <td>
                                        <?php echo substr(get_post_meta($ft, '_players_position', true),0,1); ?>
                                    </td>
                                    <td>
                                        <?php echo get_post_meta($ft, '_number', true); ?>
                                    </td>
                                    <td>
                                        <?php echo get_the_title($ft); ?>
                                    </td>
                               </tr>
                            
                                    <?php
                                endforeach;
                            ?>
                               </table>
                            <?php //echo apply_filters('the_content', get_post_meta(get_the_ID(), '_first_team_2_players', true)); ?>
                        </div>
                        <div class="lineups_team_sub_wrapper2 lineups_team_sub_wrapper" id='lineups_sub_wrapper4'>
                            <span class="first_team_title">Substitute</span><br/>
                            <table>
                            <?php
                            foreach($sb2 as $sb):
                                    ?>
                             <tr class="pltr">
                                    <td>
                                        <?php echo substr(get_post_meta($sb, '_players_position', true),0,1); ?>
                                    </td>
                                    <td>
                                        <?php echo get_post_meta($sb, '_number', true); ?>
                                    </td>
                                    <td>
                                        <?php echo get_the_title($sb); ?>
                                    </td>
                               </tr>
                            
                                    <?php
                                endforeach;
                            ?>
                               </table>
                            <?php //echo apply_filters('the_content', get_post_meta(get_the_ID(), '_substitute_2_players', true)); ?>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style='display: none' id='temp'></div>
<?php 
$toggle_on = get_post_meta(get_the_ID(), '_comment_enabled', true)!=="0"; 
if($toggle_on):
?>
    <div>
        <p class="info_title">Match Comment</p>

        <?php
        $sc = "twitter";
        $hash = get_post_meta(get_the_ID(), '_match_hashtag', true);
        if (!empty($hash)) {
            $sc=$sc." hashtag='".$hash."'";
        }
        $auth=get_post_meta(get_the_ID(), '_author', true);
        if (!empty($auth)) {
            $sc=$sc." author='".$auth."'"; 
        }
        do_shortcode("[".$sc."]");
//get_post_meta(get_the_ID(), '_first_team_2_players', true)
        ?>
    </div>
    <?php endif;?>

    <?php
    if (comments_open() || get_comments_number()) {
        comments_template();
    }
endwhile;
?>

</div>
</div>
<?php get_sidebar();?>

<?php

get_footer();
?>
    <script>

        var hidden_d = document.getElementById('hidden_part');
        var content = hidden_d.innerHTML;

        function spoil(el) {


            if (hidden_d.innerHTML != '') {

                hidden_d.innerHTML = '';
                hidden_d.style.height = "0px";
                //el.innerHTML = 'Lineups  <i class="fa fa-chevron-circle-down"></i>';
                el.children[0].children[0].className="fa fa-chevron-circle-down";
            }
            else {
                hidden_d.innerHTML = content;
                el.children[0].children[0].className="fa fa-chevron-circle-up";
                //el.innerHTML = 'Lineups  <i class="fa fa-chevron-circle-up"></i>';

                function getMax(arr) {
                    var max = 0;
                    for (var i = 0; i < arr.length; i++) {
                        //console.log(arr[i].id + " " + arr[i].offsetHeight);
                        if (arr[i].offsetHeight > max) {
                            max = arr[i].offsetHeight;
                        }
                    }
                    return max;
                }

                var wrapper = [
                    document.getElementById("lineups_sub_wrapper1"),
                    wrapper2 = document.getElementById("lineups_sub_wrapper2"),
                    wrapper3 = document.getElementById("lineups_sub_wrapper3"),
                    wrapper4 = document.getElementById("lineups_sub_wrapper4")];


                var big_wrapper = [document.getElementById("big_wrapper1"),
                    document.getElementById("big_wrapper2")];

                hidden_d.style.height = getMax(big_wrapper) + "px";

                var max = getMax(wrapper);
                for (var i = 0; i < wrapper.length; i++) {
                    wrapper[i].style.height = max + "px";

                }
            }
        }

        spoil(document.getElementById('input-b'));





    </script>















