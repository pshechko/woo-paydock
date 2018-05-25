<?php
//SHORTCODES////////////////////////////////////////////////////////////////////

  

add_shortcode('league_news', 'league_news');

function league_news($atts = null) {

    $post_type = $atts['post_type'] ? $atts['post_type'] : 'news';
    $tax = get_object_taxonomies($post_type);

    $tax = $tax[0];

    $cat = null;
    if (isset($_GET['categories'])) {
        $cat = $_GET['categories'];
        //echo get_the_category_by_ID($cat);


        $cat_key = 'tax_query';
        $cat_val = array(
            array(
                'taxonomy' => $tax,
                'field' => 'term_id',
                'terms' => $cat,
            ),
        );
    } else {
        $cat_key = "category__not_in";
        $cat_val = array(0);
    }


    if (!$atts['league_id']) {
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => 3,
            $cat_key => $cat_val,
            'meta_query' => array(
                array(
                    'key' => '_top',
                    'value' => 1,
                ),
            ),
        );
    } else {

        $league_id = $atts['league_id'];
        $args = array(
            'post_type' => 'news',
            'posts_per_page' => 3,
            $cat_key => $cat_val,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_league',
                    'value' => $league_id,
                ),
                array(
                    'key' => '_top',
                    'value' => 1,
                ),
            ),
        );
    }

    if ($atts['cat']) {
        $cat = $atts['cat'];
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'news-category',
                'field' => 'term_id',
                'terms' => $cat,
            ),
        );
    }

    $query = new WP_Query($args);
    $posts = $query->get_posts();
    $miss_ids = array();
    foreach ($posts as $post) {
        array_push($miss_ids, $post->ID);
    }

    if ($query->have_posts()) :
        ?>
        <link href='http://fonts.googleapis.com/css?family=Asap:400,700,400italic,700italic' rel='stylesheet' type='text/css'>

        <div class="league_news <?php if ($atts['home']) echo 'home-news'; ?>">
            <?php
            $count = 0;


            while ($query->have_posts()) {
                $count++;
                $query->the_post();
                ?>


                <a href="<?php echo get_permalink(); ?>">
                    <div id="news-<?php echo $count ?>" class="match<?php
                    echo $count;
                    if (($atts['home']) && $count == 1)
                        echo" big_match";
                    ?>">

                        <?php
                        if (($atts['home']) && $count == 1) {
                            $thur = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID(), 'large'));
                        } else {
                            $thur = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID(), 'thumbnail'));
                        }
                        ?>
                        <div class="match-pic" style="background: url(<?php echo $thur; ?>)">

                        </div>
                        <div class="gradient_ln">
                            <div class="summary_back_holder"><div class="summary_back"><?php
                                    $cat = get_the_terms(get_the_ID(), $tax);
                                    if (is_array($cat)) {
                                        foreach ($cat as $c) {
                                            $cat = $c;
                                            break;
                                        }
                                    } else
                                        $cat = null;
                                    //var_dump($cat);
                                    $obj = get_post_type_object($post_type);
                                    echo!$cat || $cat->name == "" || $cat->name == "Uncategorized" ? $obj->labels->name : $cat->name;
                                    ?></div></div>
                            <div class="min_match_title">
                                <?php
                                echo get_the_title(get_the_ID());
                                ?>
                            </div> 
                        </div>

                    </div>
                </a>            

                <?php
            }
            wp_reset_postdata();
            ?>
        </div> 
        <?php
    endif;
    if (!$atts['league_id'])
        return $miss_ids;
}



function home_feed($atts = null) {

    $top_args = array(
        'post_type' => array('news', 'matches'/* ,'pub_talk' */),
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_top',
                'value' => true,
            ),
        ),
    );
    
    if (isset($_GET['category'])) {
        $cat = $_GET['category'];
        $top_args['tax_query'] = array(
            array(
                'taxonomy' => 'news-category',
                'field' => 'term_id',
                'terms' => $cat,
            ),
        );
    }
    
     $top_ids = array();
    $top_query = new WP_Query($top_args);
    if ($top_query->have_posts()) {
        $top_news = $top_query->get_posts();
        $top_ids = array();
        foreach ($top_news as $tn) {
            array_push($top_ids, $tn->ID);
        }
    }



    $fargs = array(
        'post_type' => array('news', 'matches'/* ,'pub_talk' */),
        'posts_per_page' => 2,
        'post__not_in' => $top_ids,
        'meta_query' => array(
            array(
                'key' => '_featured',
                'value' => true,
            ),
        ),
    );

    if (isset($_GET['category'])) {
        $cat = $_GET['category'];
        $fargs['tax_query'] = array(
            array(
                'taxonomy' => 'news-category',
                'field' => 'term_id',
                'terms' => $cat,
            ),
        );
    }


    $fids = array();
    $fquery = new WP_Query($fargs);
    if ($fquery->have_posts()) {
        $fnews = $fquery->get_posts();
        foreach ($fnews as $fn) {
            array_push($fids, $fn->ID);
        }
    }


    return array("top_ids" => $top_ids, "featured_ids" => $fids);
}

add_shortcode('top_stories', 'top_stories');



function top_stories($atts = null, $posts_per_page = -1, $pagination = false, $page = 1) {

    $post_type = $atts['post_type'] ? $atts['post_type'] : 'news';

    //var_dump(single_cat_title('',false));

    $cat = null;
    $tax = $atts['tax'] ? $atts['tax'] : 'news-category';
    $miss_ids = null;
    if (single_cat_title('', false)) {
        $cat = get_term_by('name', single_cat_title('', false), $tax);
        //var_dump($cat);
        $c = get_term_by('id', $cat, $tax);
        echo "<h2>" . $c->name . "</h2>";
    }
    if ($atts != null && $atts['ln']) {
        $ln_arr = array("post_type" => $post_type);
        if ($cat)
            $ln_arr['cat'] = $cat;
        $miss_ids = league_news($ln_arr);
    }



    if (!$miss_ids)
        $miss_ids = $atts['miss'];




    $tax = get_object_taxonomies($post_type);
    $tax = $tax[0];
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;



    if ($atts != null && $atts['league']) {
        $mq = $atts['league'] ? array(array('key' => '_league', 'value' => $atts['league'])) : array();
        if ($cat != null) {
            $args = array(
                'paged' => $paged,
                'tax_query' => array(
                    array(
                        'taxonomy' => $tax,
                        'field' => 'term_id',
                        'terms' => $cat,
                    ),
                ),
                'post_type' => $post_type,
                'meta_query' => $mq,
                'posts_per_page' => $posts_per_page,
                'post__not_in' => $miss_ids,
            );
        } else {
            $args = array(
                'paged' => $paged,
                'post_type' => $post_type,
                'meta_query' => $mq,
                'post__not_in' => $miss_ids,
                'posts_per_page' => $posts_per_page
            );
        }
    } else {
        if ($cat != null) {
            $args = array(
                'paged' => $paged,
                'post_type' => $post_type,
                'tax_query' => array(
                    array(
                        'taxonomy' => $tax,
                        'field' => 'term_id',
                        'terms' => $cat,
                    ),
                ),
                'post__not_in' => $miss_ids,
                'posts_per_page' => $posts_per_page
            );
        } else {
            $args = array(
                'paged' => $paged,
                'post_type' => $post_type,
                'post__not_in' => $miss_ids,
                'posts_per_page' => $posts_per_page
            );
        }
    }





    $main_news_query = new WP_Query($args);


    if ($main_news_query->have_posts()) :
        $title = ($atts != null && $atts['title'] != null) ? $atts['title'] : "Top Stories";
        ?>
        <h2 class="top_stories_title"><?= $title; ?></h2>
        <?php
        while ($main_news_query->have_posts()) :
            $main_news_query->the_post();
            ?>
            <div class="home-news-excerpt">
                <div class="home-news-row">
                    <?php
                    $thur = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $size = 'medium')[0];
                    ?>
                    <div class="news-thumb-wr">
                        <a href="<?php echo get_the_permalink(); ?>">
                            <div class="news-thumb" style="background: url(<?php echo safe_image_url($thur); ?>)"></div>
                        </a>
                    </div>
                    <div class='news_itself'>
                        <div class="h3_wrapper">
                            <h3 class="entry-title">
                                <a href="<?php echo get_the_permalink(); ?>"> <?php the_title(); ?></a>
                            </h3>
                        </div>

                        <?php
                        $cat = get_the_terms(get_the_ID(), $tax);
                        if (is_array($cat) && count($cat) > 0 && $cat[0]->name != "Uncategorized" && $cat[0]->name != ""):
                            ?>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <!--by <a class="url fn n" href="<?php //echo get_author_posts_url(get_the_author_meta('ID'));                                                                                                                                                                                              ?>" title="<?php //echo esc_attr(get_the_author_meta('display_name'));                                                                                                                                                                                              ?>" rel="author"> <?php // the_author_meta('display_name');                                                                                                                                                                                              ?></a>-->
                            <div class='tags'>Categories: <?php
                                $count = 0;
                                foreach ($cat as $c) {
                                    // echo get_term_link( $c, $tax ); 
                                    echo "<a href='" . get_term_link($c, $tax) . "'>" . $c->name . "</a>";
                                    if (++$count != count($cat)) {
                                        echo", ";
                                    }
                                }
                                ?> 
                            </div>
                            <?php
                        endif;
                        ?>
                        <p>
                            <?php
                            the_excerpt();
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php
        endwhile;

        if ($posts_per_page > -1 && $posts_per_page != "" && !$pagination) {
            ?>
            <div class="more-news"><a href="<?= get_the_permalink(448) ?>"><h3> More news</h3></a></div>
            <?php
        } else if ($pagination) {


            if ($main_news_query->max_num_pages > 1) {
                $big = 999999999; // need an unlikely integer
                $translated = ''; // Supply translatable string

                echo paginate_links(array(
                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format' => '/page=%#%',
                    'current' => max(1, get_query_var('paged')),
                    'total' => $main_news_query->max_num_pages,
                    'before_page_number' => '<span class="screen-reader-text">' . $translated . ' </span>'
                ));
            } else {
                
            }
        }
    /* wp_reset_postdata(); */
    endif;
}

//CUSTOM FUNCTION///////////////////////////////////////////////////////////////
        
          function posKeys($letter) {
            switch ($letter) {
                case 'G':return 1;
                case 'D':return 10;
                case 'M':return 20;
                case 'F':return 30;
            }
        }

        function sortPlayers($data) {
            for ($i = count($data) - 1; $i > 0; $i--) {
                for ($j = 0; $j < $i; $j++) {
                    $posj = substr(get_post_meta($data[$j]->ID, '_players_position', true), 0, 1);
                    $posjj = substr(get_post_meta($data[$j + 1]->ID, '_players_position', true), 0, 1);

                    if (posKeys($posj) > posKeys($posjj)) {
                        $tmp = $data[$j];
                        $data[$j] = $data[$j + 1];
                        $data[$j + 1] = $tmp;
                    }
                }
            }
            return $data;
        }

function setMatchArray($match_ID, $teamID = null) {
    $match_team_id_1 = get_post_meta($match_ID, '_match_team_id_1', true);
    $match_team_id_2 = get_post_meta($match_ID, '_match_team_id_2', true);

    $dont_reverse_data = true;
    if ($teamID) {
        if ($teamID != $match_team_id_1) {
            $dont_reverse_data = false;
        }
    }

    $team_arr['permalink'] = get_permalink($match_ID);
    $team_arr['team_id_1'] = $dont_reverse_data ? $match_team_id_1 : $match_team_id_2;
    $team_arr['team_id_2'] = $dont_reverse_data ? $match_team_id_2 : $match_team_id_1;
    $args = array('class' => "mini-pic");
    
    //$url = wp_get_attachment_url( get_post_thumbnail_id($team_arr['team_id_1']) );
    $team_arr['team_1_image_url'] = wp_get_attachment_url( get_post_thumbnail_id($team_arr['team_id_1']) );
    $team_arr['team_2_image_url'] = wp_get_attachment_url( get_post_thumbnail_id($team_arr['team_id_2']) );
    $team_arr['team1'] = get_the_title($team_arr['team_id_1']);
    $team_arr['team2'] = get_the_title($team_arr['team_id_2']);

    if (get_post_meta($match_ID, '_show_score', true) == true) {

        $score1 = get_post_meta($match_ID, '_score1', true);
        $score2 = get_post_meta($match_ID, '_score2', true);
        $team_arr['score'] = $dont_reverse_data ? $score1 . ":" . $score2 : $score2 . ":" . $score1;
    } else {
        $team_arr['score'] = "VS";
    }
    return $team_arr;
}

function build_table_from_array($arr, $sidebar = false) {

    if ($arr >= 0) {
        ?>
        <a href="<?php echo $arr['permalink']; ?>">
            <div class='mini_match_wrapper<?php if ($sidebar) echo " match_wrapper_sidebar"; ?>'>
                <div class='team1_t<?php if ($sidebar) echo " team_t_sidebar"; ?>' >
                    <div class='mini_team_pic th_wr<?php if ($sidebar) echo " mini_team_pic_sidebar"; ?>' style='background: url(<?php echo$arr['team_1_image_url']; ?>);'> 
                        <?php //echo $arr['team_1_image_url']; ?>
                    </div>
                    <div class='mini_team_name<?php if ($sidebar) echo " mini_team_name_sidebar"; ?>'>
                        <?php echo $arr['team1']; ?>
                    </div>
                </div>
                <div class='score_t<?php if ($sidebar) echo " score_t_sidebar"; ?>'>
                    <?php echo $arr['score']; ?>
                </div>
                <div class='team2_t<?php if ($sidebar) echo " team_t_sidebar"; ?>'>
                    <div class='mini_team_pic th_wr<?php if ($sidebar) echo " mini_team_pic_sidebar"; ?>' style='background: url(<?php echo$arr['team_2_image_url']; ?>);'>
                        <?php //echo $arr['team_2_image_url']; ?>
                    </div>
                    <div class='mini_team_name<?php if ($sidebar) echo " mini_team_name_sidebar"; ?>'>
                        <?php echo $arr['team2']; ?>
                    </div>
                </div>
            </div>
        </a>
    <?php } else { ?>
        <div class='small_match_composition no_game_field'> </div>
        <?php
    }
}


add_shortcode('show_session','show_session'); 
function show_session($args){
	$att;
	if(is_array($args)&&$args['att']){
		$att=$args['att'];
		?>
			<pre>
			<?php var_dump($_SESSION[$att]);?>
			</pre>
		<?php
		return;
	}
	?>
	<pre>
	<?php var_dump($_SESSION);?>
	</pre>
	<?php
}

function get_team_leagues($team_ID, $get_main_league = true) {
    $all_leagues = get_post_meta($team_ID, '_leagues', true);
    if ($get_main_league) {
        return $all_leagues;
    } 
    else {
        $main_league = get_post_meta($team_ID, '_league', true);
        $leagues = array();
        for ($i = 0; $i < count($all_leagues); $i++) {
            if ($all_leagues[$i] != $main_league) {
                array_push($leagues, $all_leagues[$i]);
            }
        }
        return $leagues;
    }
}

function team_belongs_to_league($team_ID,$league_ID){
    return in_array($league_ID, get_post_meta($team_ID, '_leagues', true));
}

function get_league_teams($league_ID){
   $the_query = new WP_Query(array('post_type' => 'teams','posts_per_page'=>-1));
   $all_teams = $the_query->get_posts();
   $teams=array();
   for($i=0;$i<count($all_teams);$i++){
       if(team_belongs_to_league($all_teams[$i]->ID, $league_ID)){
           array_push($teams,$all_teams[$i]->ID);
       }
   }
   return $teams;
}



add_shortcode( 'authors_posts' , 'get_authors_posts' );

function  get_authors_posts(){ 
	$args = array(
    'author'        =>  get_current_user_id(),
	'post_status' => 'any',

    );
	global $current_user;
    get_currentuserinfo();
	?>
	<h3>Articles by <?=$current_user->display_name?></h3>
	<?php	
	
	$post_type = get_post_type();

    $tax = get_object_taxonomies($post_type);
    $tax = $tax[0];

    $main_news_query = new WP_Query($args);


    if ($main_news_query->have_posts()) {
        while ($main_news_query->have_posts()) {
            $main_news_query->the_post();
            ?>
            <div class="home-news-excerpt">
                <div class="home-news-row">
                    <?php
                    /*$thur = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID(), 'large'));*/
					$thur =  wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),$size='medium')[0];
                    ?>
                    <div class="news-thumb-wr">
                        <a href="<?php echo get_the_permalink(); ?>">
                            <div class="news-thumb" style="background: url(<?php echo safe_image_url($thur); ?>)"><?php /* echo get_the_post_thumbnail(get_the_ID(), array(100,100)) */ ?>
                            </div>
                        </a>
                    </div>
                    <div class='news_itself'>
                        <div class="h3_wrapper">
							<h3 class="entry-title">
								<a href="<?php echo get_the_permalink(); ?>"> <?php the_title(); ?></a>
							</h3>
							Status: <?= get_post_status();?>
							
						</div>
						<a class="ed_p_wr" href="<?=get_edit_post_link()?>">
							<i class="fa fa-pencil-square-o"> Edit</i>
						</a>
                        <?php
                        $cat = get_the_terms(get_the_ID(), $tax);
                        $cat = $cat[0];
                        $obj = get_post_type_object($post_type);
                        ?>
                                                                                                            
                        <div class='tags'><?php _e('Categories: ', 'simplecatch'); ?> <?php echo $cat->name == "" || $cat->name == "Uncategorized" ? "No" : $cat->name; ?> </div>
                        <p>
                            <?php
                            the_excerpt();
							echo voting_themes_flat(get_the_ID()); 
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php
        }
    }
	
}


	add_shortcode( 'view_my_profile' , 'view_my_profile' );
	
	add_action('wp_logout', 'go_login');

function go_login() {
    wp_redirect('/sign-in');
    exit();
}

 function view_my_profile($args) {
        global $current_user;
        get_currentuserinfo();
        ?>
        <h3>Hi, <?= $current_user->display_name ?> <a class="lourl" href="<?php echo wp_logout_url(); ?>">Log out</a></h3>
        <ul class="my_prof_menu">
            <li>
                <div class="links_wr">
                    <a href="<?php echo get_permalink($args['edit_profile']); ?>"><i class="fa fa-user"></i> Edit profile</a>
                </div>
            </li>
            <li>
                <div class="links_wr">
                    <a href="<?php echo get_permalink($args['my_posts']); ?>"><i class="fa fa-newspaper-o"></i> My articles</a> 
                </div>
            </li>
            <li>
                <div class="links_wr add_new">
                    <a href="<?php echo get_permalink($args['new_post']); ?>"><i class="fa fa-plus"></i> Add new</a>
                </div>
            </li>
        </ul>

        <?php 
    }

function safe_image_url($url){
    return $url ? $url : bloginfo('template_directory').'/images/default.png';

}

add_shortcode('gamecasts', 'gamecasts');

function gamecasts() {
    ?>
    <div id="tstdiv"></div>
    <script>
        function show_matches(param) {
            jQuery.ajax({
                url: '<?php echo plugins_url('seasons-ajax/return_gamecasts.php', __FILE__); ?>',
                data: param,
                success: function (data) {
                    jQuery('.matches_gamecasts').html(data);
                }
            });
        }
        jQuery(document).ready(function () {
            show_matches();
        });

    </script>

    <?php
    $season_date;
    $start_season_this_year = date('Y') . "-08-01";
    $today = date('Y-m-d');
    $season_date = $start_season_this_year >= $today ? (date('Y') - 1) . "-08-01" : $start_season_this_year;
    ?>

    <div id="period_wr">
        <span>
            Show posts for 
            <select id="period_sel">
                <option value="<?= $today ?>">today</option>
                <option selected value="<?= date('Y-m-d', strtotime('-7 days')) ?>">week</option>
                <option value="<?= date('Y-m-d', strtotime('-30 days')) ?>">30 days</option>
                <option value="<?= $season_date ?>">season</option>
                <option value="custom">custom period</option>
            </select>
        </span>
    </div>

    <script>
        var param = "";
        jQuery(document).ready(function () {
            jQuery('#period_sel').on('change', function () {
                param = "";
                if (jQuery('.slideset .slide.chosen').length) {
                    param += "league=" + jQuery('.slideset .slide.chosen').attr('league_id');
                }


                if (this.value != "custom") {
                    jQuery("#custom_per").remove();
                    param += "&from=" + this.value;
                    show_matches(param);
                    return;
                }

                if (!jQuery("#custom_per").length)
                    jQuery("#period_wr").append("<span id='custom_per'>From: <input type='date'> To: <input type='date'></span>");
                jQuery("#custom_per input:first-child").on("change", function () {
                    if (this.value && this.value != "") {
                        param += "&from=" + this.value;
                    }
                    if (jQuery("#custom_per input:last-child").val() && jQuery("#custom_per input:last-child").val != "") {
                        param += "&to=" + jQuery("#custom_per input:last-child").val();
                    }
                    show_matches(param);
                });
                jQuery("#custom_per input:last-child").on("change", function () {

                    if (jQuery("#custom_per input:first-child").val() && jQuery("#custom_per input:first-child").val() != "") {
                        param += "&from=" + jQuery("#custom_per input:first-child").val();
                    }
                    if (this.value && this.value != "") {
                        param += "&to=" + this.value;
                    }
                    show_matches(param);
                });
            });
        });
    </script>

    <?php
    $league_query = new WP_Query(
            array(
        'post_type' => 'leagues',
        'posts_per_page' => -1
            )
    );

    if ($league_query->have_posts()):
        ?>
        <div class="leagues_wr">
            <div class="title_wr">
                <span>Filter by league:</span>
            </div>
            <div class="matches gallery-holder carousel4">
                <div class="gallery">
                    <div class="gholder">
                        <a class="btn-prev dn" href="#">&lt;</a>
                        <a class="btn-next dn" href="#">&gt;</a>
                        <div class="gmask-center">
                            <div class="gmask">
                                <div class="slideset">
                                    <?php
                                    while ($league_query->have_posts()):
                                        $league_query->the_post();
                                        ?>
                                        <div class='slide' league_id="<?= get_the_ID() ?>">
                                            <div class="">
                                                <?= get_the_post_thumbnail(); ?>
                                            </div>
                                            <div class="league_title" style="display:none">
                                                <?= get_the_title(); ?>
                                            </div>
                                        </div>
                                        <?php
                                    endwhile;
                                    ?>
                                    <!-- <div class='slide'>*</div>
                                      <div class='slide'>$</div>
                                       <div class='slide'>6</div>
                                        <div class='slide'>#</div>
                                         <div class='slide'>i</div>
                                          <div class='slide'>@</div>
                                           <div class='slide'>)</div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function () {

                jQuery('.matches.gallery-holder .slide').click(function () {
                    param = "";
                    // jQuery('.matches_gamecasts').html(jQuery('#temp_matches').html());


                    if (!jQuery(this).hasClass("chosen")) {
                        jQuery('.matches.gallery-holder .slide.chosen').removeClass("chosen");
                        jQuery(this).addClass("chosen");
                         param += "league=" + jQuery(this).attr('league_id');
                        //jQuery('.matches_gamecasts .match[league_id="'+jQuery(this).attr("league_id")+'"]').addClass('dn');
                        // jQuery('.matches_gamecasts .match:not([league_id="' + jQuery(this).attr("league_id") + '"])').remove();
                        //console.log('.matches_gamecasts .match:not([league_id="' + jQuery(this).attr("league_id") + '"])');
                    }
                    else
                        jQuery(this).removeClass("chosen");
                    // jQuery('.matches_gamecasts .match:not([league_id="'+jQuery(this).attr("league_id")+'"])').removeClass('dn');
                  //  jQuery('#period_sel').trigger("change");
                  


                    if (jQuery("#custom_per input:last-child").length)
                        jQuery("#custom_per input:last-child").trigger('change');
                    else
                        jQuery('#period_sel').trigger('change');

                });
                var slide_wid = jQuery('.slideset>.slide').outerWidth();
                var slides_wid = jQuery('.slideset>div.slide').length * slide_wid;
                console.log(slides_wid);
                if (slides_wid > jQuery('.gholder').outerWidth()) {
                    jQuery('.gholder a[class^="btn-"]').removeClass("dn");
                }
            });
        </script>
        <?php
        wp_reset_postdata();
    endif;
    ?>
    <div class="matches_gamecasts"></div>
    <?php
}

add_shortcode('when_submited_article', 'when_submited_article');

function when_submited_article() {
    if (isset($_GET['usp_success'])) {
        ?>
        <p class="article_submited">
            <a href="<?php
            $page_id = get_page_by_title("My articles")->ID;
            echo get_the_permalink($page_id);
            ?>">
                <i class="fa fa-newspaper-o"></i>
                View my articles
            </a> 

            <a href="<?php
            $page_id = get_page_by_title("Submit an article")->ID;
            echo get_the_permalink($page_id);
            ?>">
                <i class="fa fa-plus"></i>
                Submit one more
            </a>
        </p>
        <?php
    }
}

add_shortcode('fbl','fbl');
function fbl(){ 
	 do_action('plugin_name_hook');
}


function pub_talk_home() {
        ?>


        <?php
        $args = array(
            'post_type' => 'pub_talk',
            'posts_per_page' => 4,
        );
        $query = new WP_Query($args);
        if ($query->have_posts()):

            function custom_excerpt_length_home($length) {
                return 10;
            }
            ?>
            <div class="pub_talks">
			<a class='pt_a' href="<?php $page_id = get_page_by_title("Pub talk")->ID;
  echo get_the_permalink($page_id); ?>">
                <div class="pub_talks_head">
                    <div class="pub_beer_logo" style="background: url( <?php bloginfo('template_directory'); ?>/images/beer_viva_w200.png)"></div>
                    <div class="pub_head"><h1>Pub Talk: Rumors, Lies, Damn Lies & Statistics</h1></div>
                </div>
		    </a>
                <div class="talks">
                    <?php
                    //$pts = $query->get_posts();
                    while ($query->have_posts()):
                        $query->the_post();
                        // foreach ($pts as $pt):
                        $thur = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $size = 'thumbnail');
                        $thur = $thur[0];
                        ?>
                        <div class="home_pt">
                            <div class="home_pt_thumbnail" style="background: url(<?= $thur ?>)">
                                <a href="<?= get_the_permalink(); ?>"></a>
                            </div>
                            <div class="home_pt_desc">
                                <a href="<?= get_the_permalink(); ?>"><h3><?= get_the_title() ?></h3></a>

                                <?php
                                add_filter('excerpt_length', 'custom_excerpt_length_home');
                                echo the_excerpt();
                                ?>

            <?php ?>
                            </div>
                        </div>
                        <?php
                    // endforeach;
                    endwhile;
                    wp_reset_postdata()
                    ?>
                </div>
            </div>
            <?php
        endif;
        ?>

        <?php
    }
	
	
add_shortcode('writers', 'writers');

function getAgeFromDate($date) {
            return floor((time() - strtotime($date)) / (60 * 60 * 24 * 365.25));
        }

function writers() {

    $by = null;
    global $current_user;
    get_currentuserinfo();
    $current_id = $current_user->ID;






    if (isset($_GET['by'])):
		$author_id = $_GET['by'];
        $by = get_user_by('id',$author_id)->display_name;
        $by_user = get_user_by('id',$author_id);
		
       
    endif;
	


    $sort_by = isset($_GET['order']) && $_GET['order'] ? $_GET['order'] : 'author';
    // var_dump($sort_by);
    ?>
    <div class="sortby">
        <?php if (!$author_id): ?>

            Sort by 
            <form action="" class="sort">
                <select id="sort_select" name="order" >
                    <option value="date" <?php if ($sort_by === 'date') echo 'selected="true"'; ?>>date</option>
                    <option value="author" <?php if ($sort_by === 'author') echo 'selected="true"'; ?>>author</option>
                </select>
            </form>

            <script>

                jQuery(document).ready(function () {

                    jQuery('#sort_select').on('change', function () {
                        this.form.submit();
                    });
                });
            </script>
        <?php
        else:
            $sort_by = 'date';
            ?>
            <div class="other"><a href="/writers">Show other articles...</a></div>

    <?php endif; ?>
    </div>



    <div id="writers_wr">

        <?php
        if ($author_id && !$by_user):
            ?>
            <h1>
                No such user found
            </h1>
            <?php
	     
        else:
			
			if($author_id){
			?>
        <script>
            jQuery(document).ready(function () {
                jQuery('header.entry-header>h2.entry-title').html("Articles by <?php
        echo $by;
        if ($by === $current_user->user_login)
            echo " (You)";
        ?>");
            });
        </script>
        <?php
		 }
			 
		
            $count = 7;



            if (!$by) {
                $author_ids = array();
                foreach (array('author', 'administrator') as $role) {
                    $authors = get_users(array('role' => $role));
                    foreach ($authors as $author)
                        $author_ids[] = $author->ID;
                }
            } else {
                $author_ids = array($by_user->ID);
            }







            $args = array(
                'author' => implode(',', $author_ids),
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => $count,
                'orderby' => $sort_by,
                'paged' => get_query_var('paged', 1),
            );

            $article_author = "";

            $articles = new WP_Query($args);

            if ($articles->have_posts()) :
                while ($articles->have_posts()) :
                    $articles->the_post();
                    if (!$by && $article_author != get_the_author() && $sort_by == 'author') {
                        $article_author = get_the_author();

                        $atr_auth_id = get_the_author_meta("ID");
                        ?>
                        <div class="author_title">
                            <strong>
                                Articles by <?php
                                echo $article_author;
                                if ($atr_auth_id == $current_id)
                                    echo " (You)";
                                ?>
                            </strong>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="home-news-excerpt">
                        <div class="home-news-row">
                            <?php
							 $thur =  wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),$size='medium')[0]
                            ?>
                            <div class="news-thumb-wr">
                                <a href="<?= get_the_permalink(get_the_ID()); ?>">
                                    <div class="news-thumb" style="background: url(<?php echo safe_image_url($thur); ?>)"><?php /* echo get_the_post_thumbnail(get_the_ID(), array(100,100)) */ ?>
                                    </div>
                                </a>
                            </div>
                            <div class='news_itself'>
                                <?php
                                if (current_user_can('edit_post')):
                                    ?>
                                    <a class="ed_p_wr" href="<?= get_edit_post_link() ?>">
                                        <i class="fa fa-pencil-square-o"> Edit</i>
                                    </a>
                                    <?php
                                endif;
                                ?>
                                <div class="h3_wrapper">
                                    <h3 class="entry-title">
                                        <a href="<?php echo get_the_permalink(); ?>"> <?php the_title(); ?></a>
                                    </h3>
                                </div>

                                <div class='tags'>By <a href="/writers/?by=<?php the_author_ID(); ?>"><?= get_the_author() ?></a> on <?= get_the_date() ?></div>
                                <p>
                                    <?php
                                    the_excerpt();
									if(function_exists('voting_themes_flat')){
										echo voting_themes_flat(get_the_ID()); 
									}
                                    ?>
                                </p>


                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;

                wp_reset_postdata();
            endif;
        endif;
        ?>

    </div>
    <?php
    $big = 999999999; // need an unlikely integer
    $translated = ''; // Supply translatable string

    echo paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '/page=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $articles->max_num_pages,
        'before_page_number' => '<span class="screen-reader-text">' . $translated . ' </span>'
    ));
}


add_shortcode("writers_block", "writers_block");

function writers_block() {
    ?>
    
        <?php
        $count = 4;
        $author_ids = array();
        foreach (array('author', 'administrator') as $role) {
            $authors = get_users(array('role' => $role));
            foreach ($authors as $author)
                $author_ids[] = $author->ID;
        }

        global $current_user;

        get_currentuserinfo();
        $current_id = $current_user->ID;

        $args = array(
            'author' => implode(',', $author_ids), 
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $count,
            'orderby' => 'date',
            'paged' => 1
        );

        $article_author = "";

        $articles = new WP_Query($args);

        if ($articles->have_posts()) :
			?>
			<h1>Writers</h1>
			<div id="writers_wr">
			<?php
				while ($articles->have_posts()) :
					$articles->the_post();
					?>
					<div class="home-news-excerpt">
						<div class="home-news-row">
							<?php
							$thur =  wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()),$size='medium')[0];
							?>
							<div class="news-thumb-wr">
								<a href="<?= get_the_permalink(get_the_ID()); ?>">
									<div class="news-thumb" style="background: url(<?= safe_image_url($thur); ?>)">
									</div>
								</a>
							</div>
							<div class='news_itself'>
								<div class="h3_wrapper">
									<h3 class="entry-title">
										<a href="<?php echo get_the_permalink(); ?>"> <?php the_title(); ?></a>
									</h3>
								</div>
							   <div class='tags'>By <a href="/writers/?by=<?php the_author_ID(); ?>"><?= get_the_author() ?></a> on <?= get_the_date() ?></div>
								<p>
									<?php
									the_excerpt();
									if(function_exists('voting_themes_flat')){
										echo voting_themes_flat(get_the_ID()); 
									}
									?>
								</p>

							</div>
						</div>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
				<div class="more-news"><a href="/writers"><h3> More articles</h3></a></div>
			</div>
			<?php
        endif;
        ?>


    <?php
}



add_action( 'wp_enqueue_scripts', 'enqueue_and_register_my_scripts' );

function enqueue_and_register_my_scripts(){

    // Use `get_stylesheet_directory_uri() if your script is inside your theme or child theme.
    wp_register_script( 'custom-script', plugins_url('/js/custom.js', __FILE__));

    // Let's enqueue a script only to be used on a specific page of the site

        // Enqueue a script that has both jQuery (automatically registered by WordPress)
        // and my-script (registered earlier) as dependencies.
    wp_enqueue_script( 'custom-script', array( 'jquery') );
    
}


function change_post_menu_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'Articles';
    $submenu['edit.php'][5][0] = 'Articles';
    $submenu['edit.php'][10][0] = 'Add Articles';
    $submenu['edit.php'][15][0] = 'Status'; // Change name for categories
    $submenu['edit.php'][16][0] = 'Labels'; // Change name for tags
    echo '';
}

function change_post_object_label() {
        global $wp_post_types;
        $labels = &$wp_post_types['post']->labels;
        $labels->name = 'Articles';
        $labels->singular_name = 'Article';
        $labels->add_new = 'Add Article';
        $labels->add_new_item = 'Add Article';
        $labels->edit_item = 'Edit Article';
        $labels->new_item = 'Article';
        $labels->view_item = 'View Article';
        $labels->search_items = 'Search Articles';
        $labels->not_found = 'No Articles found';
        $labels->not_found_in_trash = 'No Articles found in Trash';
    }
    add_action( 'init', 'change_post_object_label' );
    add_action( 'admin_menu', 'change_post_menu_label' );
	
	// CUSTOMIZE ADMIN MENU ORDER
   function custom_menu_order($menu_ord) {
       if (!$menu_ord) return true;
       return array(
        'index.php', // this represents the dashboard link
        'edit.php', //the posts tab
        'upload.php', // the media manager
        'edit.php?post_type=page', //the posts tab
    );
   }
   add_filter('custom_menu_order', 'custom_menu_order');
   add_filter('menu_order', 'custom_menu_order');

