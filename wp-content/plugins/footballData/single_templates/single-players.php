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
<?php do_shortcode('ssba') ?>
<div class="top-bar clearfix">
	<div class="entry-meta">
		<?php echo do_shortcode('[ssba]');?> 
	</div>
	<div class="league-logo">
		<?php
		$team = get_post_meta(get_the_ID(), "_team", true);
		$league = get_post_meta($team, "_league", true);
		$args= array('class'=>'nav_image');
		?>
		<a href="<?php echo get_the_permalink($league) ?>">
			<?php echo get_the_post_thumbnail($league, array(100,100), $args) ?>
		</a>
		<a href="<?php echo get_the_permalink($team) ?>">
			<?php echo get_the_post_thumbnail($team, array(100,100), $args) ?>
		</a>
	</div>

</div>
<div id="content" class="site-content">
    <div class="custom-site-content">
        <div class="thumbnail" id="thumbnail">
            <?php
            $args = array('class' => "featured_image");
            echo get_the_post_thumbnail(get_the_ID(), array(10000, 300), $args);
            ?>


            <div class="name_position">
                <p class="player_name">
                    <?php echo get_the_title(get_the_ID()); ?>
                </p>
                <p class="player_position">
                    <?php echo strtoupper(get_post_meta(get_the_ID(), "_players_position", true)); ?>
                </p>
            </div>
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
        <div id="player_info" class="player_info">
            <p class="info_title">Player Information</p>
            <table class="player_information_table">
                <tr>
                    <td class="info_property_first">Date of birth</td>
                    <td class="info_value_sp"> <?php echo get_post_meta(get_the_ID(), "_date_of_birth", true); ?> </td>

                    <td class="info_property">Height</td>
                    <td class="info_value"> <?php echo get_post_meta(get_the_ID(), "_height", true); ?> </td>
                </tr>
                <tr class="player_info_item_even">
                    <td class="info_property_first">Age</td>
                    <td class="info_value_sp"> <?php echo get_post_meta(get_the_ID(), "_age", true); ?> </td>

                    <td class="info_property">Weight</td>
                    <td class="info_value"> <?php echo get_post_meta(get_the_ID(), "_weight", true); ?> </td>
                </tr>
                <tr>
                    <td class="info_property_first">Country of birth</td>
                    <td class="info_value_sp"> <?php echo get_post_meta(get_the_ID(), "_country_of_birth", true); ?> </td>

                    <td class="info_property">National Team</td>
                    <td class="info_value"> <?php echo get_post_meta(get_the_ID(), "_natonal_team", true); ?> </td>
                </tr>
                <!--<tr>
                    <td class="info_property"> <?php ?> </td>
                    <td class="info_value"> <?php ?> </td>
                    <td class="info_property"> <?php ?> </td>
                    <td class="info_value"> <?php ?> </td>
                </tr>-->
            </table>
        </div>

        <div id="career_info" class="player_info">
            <p class="info_title">Career Information</p>
            <table class="player_information_table">
                <tr>
                    <td class="info_property_first">Appearances</td>
                    <td class="info_value_sp"> <?php echo get_post_meta(get_the_ID(), "_appearances", true); ?> </td>

                    <td class="info_property">Titles won</td>
                    <td class="info_value"> <?php echo get_post_meta(get_the_ID(), "_titles", true); ?> </td>
                </tr>
                <tr class="player_info_item_even">
                    <td class="info_property_first">Goals</td>
                    <td class="info_value_sp"> <?php echo get_post_meta(get_the_ID(), "_goals", true); ?> </td>

                    <td class="info_property">25-man squad memeber</td>
                    <td class="info_value"> <?php echo get_post_meta(get_the_ID(), "_squad", true); ?> </td>
                </tr>
                <tr>
                    <td class="info_property_first">Yellow cards</td>
                    <td class="info_value_sp"> <?php echo get_post_meta(get_the_ID(), "_yellow_cards", true); ?> </td>

                    <td class="info_property">Home grown player</td>
                    <td class="info_value"> <?php echo get_post_meta(get_the_ID(), "_home_grown", true); ?> </td>
                </tr>
                <tr class="player_info_item_even">
                    <td class="info_property_first">Red cards</td>
                    <td class="info_value_sp"> <?php echo get_post_meta(get_the_ID(), "_red_cards", true); ?> </td>

                    <td class="info_property"></td>
                    <td class="info_value"> </td>
                </tr>
                <!--<tr>
                    <td class="info_property"> <?php ?> </td>
                    <td class="info_value"> <?php ?> </td>
                    <td class="info_property"> <?php ?> </td>
                    <td class="info_value"> <?php ?> </td>
                </tr>-->
            </table>
        </div>


        <div id="league_news" >
            <p class="info_title">League News</p>
            <?php
            
            $p = get_page_by_title(get_post_meta($id, '_team', true), OBJECT, 'teams');
            $team_id = $p->ID;   
            do_shortcode( '[league_news league_name="'.get_post_meta($team_id, '_league', true).'"]' );
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

	<?php get_sidebar(); ?>
    <?php get_footer();?>

    