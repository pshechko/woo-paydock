<?php
/*
  Plugin Name: footballData
  Description: Plugin adds data for site, lika "players", "leagues" ect.
  Version: 1.0
  Author: pshechko
 */

//ADDING CUSTOM STYLES//////////////////////////////////////////////////////////



function my_theme_load_resources() {
    $urlstyle = plugins_url('styles/leagues_styles.css', __FILE__);
    wp_register_style('leagues_styles', $urlstyle, false, '0.1');
    wp_enqueue_style('leagues_styles');

    $urlstyle = plugins_url('styles/mystyles.css', __FILE__);
    wp_register_style('theme_style1', $urlstyle, false, '0.1');
    wp_enqueue_style('theme_style1');

    $urlstyle = plugins_url('styles/players_styles.css', __FILE__);
    wp_register_style('players_styles', $urlstyle, false, '0.1');
    wp_enqueue_style('players_styles');

    $urlstyle = plugins_url('styles/teams_styles.css', __FILE__);
    wp_register_style('teams_styles', $urlstyle, false, '0.1');
    wp_enqueue_style('teams_styles');

    $urlstyle = plugins_url('styles/matches_styles.css', __FILE__);
    wp_register_style('matches_styles', $urlstyle, false, '0.1');
    wp_enqueue_style('matches_styles');

    $urlstyle = plugins_url('styles/twitter_styles.css', __FILE__);
    wp_register_style('twitter_styles', $urlstyle, false, '0.1');
    wp_enqueue_style('twitter_styles');
}

add_action('wp_enqueue_scripts', 'my_theme_load_resources');



//SETTINGS PAGE/////////////////////////////////////////////////////////////////
function register_my_custom_menu_pages() {      // REGISTER MENU ITEMS
    $adr = plugins_url('', __FILE__) . "/icons/settings_ico.png";
    add_menu_page('Settings', 'Football data settings', 'manage_options', 'Settings', 'Settings', $adr, 130);
}

function Settings() { //PLUGIN SETTINGS////////////////////////////////////////
    $urlstyle = plugins_url('styles/adminStyles.css', __FILE__);

    $consumerKey = isset($_POST['consumerKey']) ? $_POST['consumerKey'] : get_option('consumerKey');
    update_option('consumerKey', $consumerKey);

    $consumerSecret = isset($_POST['consumerSecret']) ? $_POST['consumerSecret'] : get_option('consumerSecret');
    update_option('consumerSecret', $consumerSecret);

    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : get_option('accessToken');
    update_option('accessToken', $accessToken);

    $accessTokenSecret = isset($_POST['accessTokenSecret']) ? $_POST['accessTokenSecret'] : get_option('accessTokenSecret');
    update_option('accessTokenSecret', $accessTokenSecret);

    $space_symbol = isset($_POST['space_symbol']) ? $_POST['space_symbol'] : get_option('space_symbol');
    update_option('space_symbol', $space_symbol);

    $range_of_seasons = isset($_POST['range_of_seasons']) ? $_POST['range_of_seasons'] : get_option('range_of_seasons');
    if ($range_of_seasons == "" || $range_of_seasons == "null")
        $range_of_seasons = 1970;
    update_option('range_of_seasons', $range_of_seasons);
    ?>

    <link rel="stylesheet" href="<?php echo $urlstyle; ?>" type="text/css"/>

    <br/><br/>
    <form method="POST" enctype="multipart/form-data" action="">
        <div class="settings_block">
            <h2>Twitter settings: </h2>
            <p>
                Consumer Key: 
                <br/><input class="widefat" name="consumerKey" type="text" value="<?php echo esc_attr($consumerKey); ?>" />
            </p>
            <p>
                Consumer Secret: 
                <br/><input class="widefat"  name="consumerSecret" type="text" value="<?php echo esc_attr($consumerSecret); ?>" />
            </p>
            <p>
                Access Token: 
                <br/><input class="widefat"  name="accessToken" type="text" value="<?php echo esc_attr($accessToken); ?>" />
            </p>
            <p>

                Access Token Secret: 
                <br/><input class="widefat" name="accessTokenSecret" type="text" value="<?php echo esc_attr($accessTokenSecret); ?>" />

            </p>
        </div>
        <div class="settings_block">
            <h2>Data settings: </h2>
            <p>
                Alternative symbol for missing data:
            <div class="example">
                <input type="text" id="space" name="space_symbol" class="widefat" value="<?php echo esc_attr($space_symbol); ?>" placeholder="(none)">
            </div>
            <div class="example">
                <table class="tbl">
                    <tr class="first_tb">
                        <td>YCY</td>
                        <td>RCR</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td id="missing1"></td>
                    </tr>
                    <tr class="even">
                        <td id="missing2"></td>
                        <td>4</td>
                    </tr>
                    <tr>
                        <td>0</td>
                        <td>3</td>
                    </tr>
                </table>
            </div>

            <br/>
            </p>
            <p>

                Range of seasons: 
                <br/>
                <input class="widefat w_small" name="range_of_seasons" type="text" value="<?php echo esc_attr($range_of_seasons); ?>" /><?php echo " — " . date("Y"); ?>

            </p>


        </div>
        </br>
        <input type="submit" class="okbutton"  name="okbutton" value="Submit">
    </form>

    <script>
        var space_input = document.getElementById("space");
        space_input.onchange = function () {
            show_ex();
        };
        space_input.onkeyup = function () {
            show_ex();
        };
        function show_ex() {
            var sym = space_input.value;
            if (sym.length > 1) {
                space_input.value = space_input.value.substr(0, 1);
                sym = space_input.value;
            }
            document.getElementById("missing1").innerHTML = document.getElementById("missing2").innerHTML = sym;
        }
        ;
        show_ex();
    </script>

    <?php
} 

//CUSTOM POST TYPES/////////////////////////////////////////////////////////////

function wpt_player_posttype() {
    register_post_type('players', array(
        'labels' => array(
            'name' => __('Players'),
            'singular_name' => __('Player'),
            'add_new' => __('Add New Player'),
            'add_new_item' => __('Add New Player'),
            'edit_item' => __('Edit Player'),
            'new_item' => __('Add New Player'),
            'view_item' => __('View Player'),
            'search_items' => __('Search Player'),
            'not_found' => __('No players found'),
            'not_found_in_trash' => __('No players found in trash'),
        ),
        'public' => true,
        'menu_icon' => plugins_url('icons', __FILE__) . "/players.png",
        'supports' => array('title', 'editor', 'thumbnail', 'comments'),
        'capability_type' => 'post',
        'rewrite' => array("slug" => "players"), // Permalinks format
        'menu_position' => 1005,
        'register_meta_box_cb' => 'add_players_metaboxes'
            )
    );
}

add_action('init', 'wpt_player_posttype');

function wpt_team_posttype() {
    register_post_type('teams', array(
        'labels' => array(
            'name' => __('Teams'),
            'singular_name' => __('Team'),
            'add_new' => __('Add New Team'),
            'add_new_item' => __('Add New Team'),
            'edit_item' => __('Edit Team'),
            'new_item' => __('Add New Team'),
            'view_item' => __('View Team'),
            'search_items' => __('Search Team'),
            'not_found' => __('No teams found'),
            'not_found_in_trash' => __('No teams found in trash'),
        ),
        'public' => true,
        'menu_icon' => plugins_url('icons', __FILE__) . "/teams.png",
        'supports' => array('title', 'editor', 'thumbnail', 'comments'),
        'capability_type' => 'post',
        'rewrite' => array("slug" => "teams"), // Permalinks format
        'menu_position' => 1006,
        'register_meta_box_cb' => 'add_teams_metaboxes'
            )
    );
}

add_action('init', 'wpt_team_posttype');

function wpt_league_posttype() {
    register_post_type('leagues', array(
        'labels' => array(
            'name' => __('Leagues'),
            'singular_name' => __('League'),
            'add_new' => __('Add New League'),
            'add_new_item' => __('Add New League'),
            'edit_item' => __('Edit League'),
            'new_item' => __('Add New League'),
            'view_item' => __('View League'),
            'search_items' => __('Search League'),
            'not_found' => __('No leagues found'),
            'not_found_in_trash' => __('No leagues found in trash'),
        ),
        'public' => true,
        'menu_icon' => plugins_url('icons', __FILE__) . "/leagues.png",
        'supports' => array('title', 'editor', 'thumbnail', 'comments'),
        'capability_type' => 'post',
        'rewrite' => array("slug" => "leagues"), // Permalinks format
        'menu_position' => 1007,
        'register_meta_box_cb' => 'add_leagues_metaboxes'
            )
    );
}

add_action('init', 'wpt_league_posttype');

function wpt_match_posttype() {
    register_post_type('matches', array(
        'labels' => array(
            'name' => __('Matches'),
            'singular_name' => __('Match'),
            'add_new' => __('Add New Match'),
            'add_new_item' => __('Add New Match'),
            'edit_item' => __('Edit Match'),
            'new_item' => __('Add New Match'),
            'view_item' => __('View Match'),
            'search_items' => __('Search Match'),
            'not_found' => __('No matches found'),
            'not_found_in_trash' => __('No matches found in trash'),
        ),
        'public' => true,
        'menu_icon' => plugins_url('icons', __FILE__) . "/matches.png",
        'supports' => array('title', 'editor', 'thumbnail', 'comments'),
        'capability_type' => 'post',
        'rewrite' => array("slug" => "matches"), // Permalinks format
        'menu_position' => 1008,
        'register_meta_box_cb' => 'add_matches_metaboxes'
            )
    );
}

add_action('init', 'wpt_match_posttype');

function wpt_news_posttype() {
    register_post_type('news', array(
        'labels' => array(
            'name' => __('News'),
            'singular_name' => __('News'),
            'add_new' => __('Add News'),
            'add_new_item' => __('Add News'),
            'edit_item' => __('Edit News'),
            'new_item' => __('Add News'),
            'view_item' => __('View News'),
            'search_items' => __('Search News'),
            'not_found' => __('No news found'),
            'not_found_in_trash' => __('No news found in trash'),
        ),
        'public' => true,
        'taxonomies' => array('news-category'),
        'menu_icon' => plugins_url('icons', __FILE__) . "/news.png",
        'supports' => array('title', 'editor', 'thumbnail', 'comments'),
        'capability_type' => 'post',
        'rewrite' => array("slug" => "news"), // Permalinks format
        'menu_position' => 1009,
        'register_meta_box_cb' => 'add_news_metaboxes'
            )
    );
    register_taxonomy('news-category', 'news', array('hierarchical' => true, 'label' => 'Categories', 'query_var' => true, 'rewrite' => true));
}

add_action('init', 'wpt_news_posttype');

function wpt_pub_talk_posttype() {
    register_post_type('pub_talk', array(
        'labels' => array(
            'name' => __('Pub talk'),
            'singular_name' => __('Pub talk'),
            'add_new' => __('Add New Pub talk'),
            'add_new_item' => __('Add New Pub talk'),
            'edit_item' => __('Edit Pub talk'),
            'new_item' => __('Add New Pub talk'),
            'view_item' => __('View Pub talk'),
            'search_items' => __('Search Pub talk'),
            'not_found' => __('No pub talk found'),
            'not_found_in_trash' => __('No pub talk found in trash'),
        ),
        'public' => true,
        'taxonomies' => array('pub_talk_category'),
        'menu_icon' => plugins_url('icons', __FILE__) . "/pub_talk.png",
        'supports' => array('title', 'editor', 'thumbnail', 'comments'),
        'capability_type' => 'post', 
        'rewrite' => array("slug" => "pub_talk"), // Permalinks format
        'menu_position' => 1010,
        'register_meta_box_cb' => 'add_pub_talk_metaboxes'
            )
    );
    register_taxonomy('pub_talk_category', 'pub_talk', array('hierarchical' => true, 'label' => 'Categories', 'query_var' => true, 'rewrite' => true));
}

add_action('init', 'wpt_pub_talk_posttype');



//END OF CUSTOM POST TYPES//////////////////////////////////////////////////////
//IMPORTING TWITTER/////////////////////////////////////////////////////////////

function importTwitter() {
    require('additional_classes/TwitterMatch.php' );
}

add_action('init', 'importTwitter');


global $jal_db_version;
$jal_db_version = "1.0";

function jal_install () {
   global $wpdb;
   global $jal_db_version;
 
    $table_name = $wpdb->prefix . "tweets";
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE " . $table_name . " (
	  id VARCHAR(55) NOT NULL,
	  txt text  NOT NULL,
	  min VARCHAR(55) NULL,
	  act VARCHAR(55)  NULL,
	  author VARCHAR(55) NOT NULL,
          hashtag VARCHAR(55) NOT NULL,
	  UNIQUE KEY id (id)
	);";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

      
        
 
      add_option("jal_db_version", $jal_db_version);

   }
}


register_activation_hook(__FILE__,'jal_install');


//REPLACING SINGLE TEMPLATES////////////////////////////////////////////////////

require_once('additional_classes/PageTemplater.php'); //class allows replace page template in plugins directory

function get_custom_post_type_template($single_template) {
    global $post;
    $custom_post_types = array('players','teams','leagues','matches','pub_talk');
    if (in_array($post->post_type, $custom_post_types)){
        $single_template = dirname(__FILE__) . '/single_templates/single-' . $post->post_type . '.php';
    }

    return $single_template;
}
add_filter('single_template', 'get_custom_post_type_template');

/*function get_custom_post_type_template($single_template) {
    global $post;
    global $post_type;
    if ($post->post_type == $post_type) {
        $single_template = dirname(__FILE__) . '/single_templates/single-' . $post_type . '.php';
    }
    return $single_template;
}

$post_type = 'players';
add_filter('single_template', 'get_custom_post_type_template');
$post_type = 'teams';
add_filter('single_template', 'get_custom_post_type_template');
$post_type = 'leagues';
add_filter('single_template', 'get_custom_post_type_template');
$post_type = 'matches';
add_filter('single_template', 'get_custom_post_type_template');*/



//SINGLE TEMPLATES ARE REPLACED/////////////////////////////////////////////////
//SETTING METABOXES/////////////////////////////////////////////////////////////
//PLAYERS METABOXES/////////////////////////////////////////////////////////////

function add_players_metaboxes() {
    add_meta_box('wpt_player_information', 'Player information', 'wpt_player_information', 'players', 'normal', 'high');
    add_meta_box('wpt_career_overview', 'Career overview', 'wpt_career_overview', 'players', 'normal', 'high');
    add_meta_box('wpt_players_team', 'Player`s team', 'wpt_players_team', 'players', 'normal', 'high');
}

function wpt_career_overview() {

    $post = $_GET['post'];

    $location = get_post_meta($post->ID, '_appearances', true);
// echo"<p>!" . $post. "!</p>";
    echo"<table>";
    echo "<tr>";
    echo "<td>Appearances:</td><td><input type='number' name='_appearances' value='" . get_post_meta($post, '_appearances', true) . "'/></td>";
    echo "<td>Goals:</td><td><input type='number' name='_goals' value='" . get_post_meta($post, '_goals', true) . "'/></td>";
    echo "<td>Yellow cards:</td><td><input type='number' name='_yellow_cards' value='" . get_post_meta($post, '_yellow_cards', true) . "'/></td>";
    echo "</tr><tr>";
    echo "<td>Red cards:</td><td><input type='number' name='_red_cards' value='" . get_post_meta($post, '_red_cards', true) . "'/></td>";
    echo "<td>Titles won:</td><td><input type='number' name='_titles' value='" . get_post_meta($post, '_titles', true) . "'/></td>";
    echo "<td>25-man squad member:</td><td><input type='text' name='_squad' value='" . get_post_meta($post, '_squad', true) . "'/></td>";
    echo "</tr><tr>";
    echo "<td>Home grown player:</td><td><input type='text' name='_home_grown' value='" . get_post_meta($post, '_home_grown', true) . "'/></td>";
    echo "</tr>";
    echo "</table>";
}

function wpt_player_information() {
    $post = $_GET['post'];

    echo"<table>";
    echo "<tr>";
    echo "<td>Date of Birth:</td><td><input type='date' name='_date_of_birth' value='" . get_post_meta($post, '_date_of_birth', true) . "'/></td>";
    echo "<td>Age:</td><td><input type='number' name='_age' value='" . get_post_meta($post, '_age', true) . "'/></td>";
    echo "<td>Country of birth:</td><td><input type='text' name='_country_of_birth' value='" . get_post_meta($post, '_country_of_birth', true) . "'/></td>";
    echo "</tr><tr>";
    echo "<td>Heigth:</td><td><input type='number' name='_height' value='" . get_post_meta($post, '_height', true) . "'/></td>";
    echo "<td>Weigth:</td><td><input type='number' name='_weight' value='" . get_post_meta($post, '_weight', true) . "'/></td>";
    echo "<td>National team:</td><td><input type='text' name='_natonal_team' value='" . get_post_meta($post, '_natonal_team', true) . "'/></td>";
    echo "</tr>";
    echo "</table>";
}

function wpt_players_team() {
    $post = $_GET['post'];
// global $this_post;
//$this_post=$post;
    $location = get_post_meta($post, '_team', true);
//echo"<p>!" . $this_post . "!</p>";

    $the_query = new WP_Query(array('post_type' => 'teams','posts_per_page'=>-1));

    echo"<table><tr><td>";

    echo "Player`s team: ";
    echo'</td><td>';
// The Loop
    if ($the_query->have_posts()) {

        echo'<select required name="_team" >';
        echo '<option disabled';
        if (!$location)
            echo ' selected';
        echo'>select a team</option>';

//$count = 0;
        while ($the_query->have_posts()) {
            $the_query->the_post();
            $count = get_the_ID();
            echo'<option';
            if ($location == $count && $location != NULL) {
                echo ' selected';
            }
            echo' value="' . $count . '">';
            echo get_the_title();
            echo'</option>';
//$count++;
        }
        echo'</select>';
    }
    echo'</td><td>';
    echo"Player`s position: ";
    echo'</td><td>';

    $positions = array("Goalkeeper", "Defender", "Midfielder", "Forward");
    $current_value = get_post_meta($post, '_players_position', true);
    echo'
    <select name="_players_position">';

    echo'<option disabled';
    if (!$current_value) {
        echo" selected";
    }
    echo'>select player`s position</option>';
//$count = 0;
    foreach ($positions as $position) {
        echo'<option value="' . $position . '"';
        if ($current_value == $position && $current_value != NULL) {
            echo" selected";
        }
        echo'>' . $position . '</option>';
    }
    echo'</select>';

    echo'</td><td>';
    echo'Player`s number:';
    echo'</td><td>';

    $current_value = get_post_meta($post, '_number', true);
    echo '<input type="number" name="_number" value="' . $current_value . '" />';
    /*
      echo "<select name='_number'>";
      echo'<option disabled';
      if (!$current_value) {
      echo" selected";
      }
      echo'>select player`s number</option>';
      for ($i = 1; $i <= 11; $i++) {
      echo'<option value="' . $i . '"';
      if ($current_value == $i && $current_value != NULL) {
      echo" selected";
      }
      echo'>' . $i . '</option>';
      }

      echo "<select>"; */

    echo"</td></tr></table>";

//wp_reset_postdata();
}

//TEAMS METABOXES///////////////////////////////////////////////////////////////

function add_teams_metaboxes() {
    add_meta_box('wpt_teams_league', 'Team`s league', 'wpt_teams_league', 'teams', 'normal', 'high');
    add_meta_box('wpt_teams_background', 'Team`s background image', 'wpt_teams_background', 'teams', 'normal', 'high');
    add_meta_box('wpt_teams_players', 'Players', 'wpt_teams_players', 'teams', 'normal', 'high');
}

function wpt_teams_players() {
    $post = $_GET['post'];
    $urlstyle = plugins_url('styles/adminStyles.css', __FILE__);
    ?>
    <link rel="stylesheet" href="<?php echo $urlstyle; ?>" type="text/css"/>
    <table id="players_team_admin">
        <tr class='first_tb'>
            <td>POS</td>
            <td>#</td>
            <td>PLAYER</td>
            <td>
                <i class="fa pl_medkit_t fa-medkit"></i>
            </td>
        </tr>

        <?php
        $players = explode(",", get_post_meta($post, '_incap', true));
        $pl_query = new WP_Query(array(
            'post_type' => 'players',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_team',
                    'value' => $post,
                ),
            ),
        ));


        if ($pl_query->have_posts()) :


            $pl = sortPlayers($pl_query->get_posts());
            //$pl=$pl_query->get_posts();
            $eo = 0;
            foreach ($pl as $p):
                ?>
                <tr class="pltr<?php if ($eo++ % 2 === 0) echo " even"; ?>">
                    <td>
                        <?php echo substr(get_post_meta($p->ID, '_players_position', true), 0, 1); ?>
                    </td>
                    <td>
                        <?php echo get_post_meta($p->ID, '_number', true); ?>
                    </td>
                    <td>
                        <?php echo get_the_title($p->ID); ?>
                    </td>      
                    <td>
                        <input  class="lineups_cb" type='checkbox' <?php if (in_array($p->ID, $players)) echo "checked"; ?> name='_incap[<?php echo $p->ID; ?>]'/>
                    </td>
                </tr>

                <?php
            endforeach;
        endif;
        ?>

    </table>
    <script>

    function ltrouth(el) {
        el = jQuery(el);
        if (el.prop("checked"))
            el.parent().parent().css({'text-decoration': 'line-through'});
        else
            el.parent().parent().css({'text-decoration': 'none'});
    }

    jQuery(document).ready(function () {
        jQuery('.lineups_cb').change(function () {
            ltrouth(this);
        });
        jQuery('.lineups_cb').each(function () {
            ltrouth(this);
        });

    });
    </script>
    <?php
}

function wpt_teams_league() {
    /*
      global $post;

      $location = get_post_meta($post->ID, '_league', true);

      $the_query = new WP_Query(array('post_type' => 'leagues','posts_per_page'=>-1));

      if ($the_query->have_posts()) {

      echo'<select required name="_league">';
      echo '<option disabled';
      if (!$location)
      echo ' selected';
      echo'>Select a league</option>';

      $count = 0;
      while ($the_query->have_posts()) {
      $the_query->the_post();
      $count =  get_the_ID();
      echo'<option';
      if ($location == $count && $location != NULL) {
      echo ' selected';
      }
      echo' value="' . $count . '">';
      echo get_the_title();
      echo'</option>';
      //$count++;
      }
      echo'</select>';
      } */
    global $post;

    $league = get_post_meta($post->ID, '_league', true);
    $leagues = get_post_meta($post->ID, '_leagues', true);
    // print_r($leagues);
    $the_query = new WP_Query(array('post_type' => 'leagues','posts_per_page'=>-1));




    if ($the_query->have_posts()) {
        ?>
        <p>
            <?php
            $count = 0;
            $subcount = 0;
            while ($the_query->have_posts()) {
                $the_query->the_post();
                ?>
                <label><input id="cb<?php echo $count; ?>" type="checkbox" <?php if ($leagues[$subcount] == get_the_ID()) {
                echo ' checked ';
                $subcount++;
            } ?> name="_leagues<?php echo $count; ?>" />
                    <input id="r<?php echo $count; ?>" type="radio" <?php if ($league == get_the_ID()) echo "checked"; ?> name="_league" value="<?php echo get_the_ID(); ?>"> <?php echo get_the_title(); ?><Br></label>
                <?php
                $count++;
            }
            ?>
        </p>
        <p>Main team league (second column with radiobuttons) can be marked only for the selected leagues.</p>
        <?php
    }
}

function wpt_teams_background() {

    $post = $_GET['post'];


    echo '<input id="upload_image" type="text" size="36" name="_teams_back_image_url" value="" />
<input id="upload_image_button" type="button" value="';
    echo get_post_meta($post, '_teams_back_image_url', true) ? "Change Image" : "Upload Image";
    echo'" />
<br/><br/>
<img style="max-width: 30%;" id="teams_back_image" name="teams_back_image" src ="' . get_post_meta($post, '_teams_back_image_url', true) . '" ?>';

    function my_admin_scripts() {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_register_script('my-upload', WP_PLUGIN_URL . '/my-script.js', array('jquery', 'media-upload', 'thickbox'));
        wp_enqueue_script('my-upload');
    }

    function my_admin_styles() {

        wp_enqueue_style('thickbox');
    }

// better use get_current_screen(); or the global $current_screen
    if (isset($_GET['page']) && $_GET['page'] == 'my_plugin_page') {

        add_action('admin_print_scripts', 'my_admin_scripts');
        add_action('admin_print_styles', 'my_admin_styles');
    }
    ?>
    <script>jQuery(document).ready(function ($) {

            $('#upload_image_button').click(function () {

                formfield = $('#upload_image').attr('name');
                tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
                return false;
            });

            window.send_to_editor = function (html) {

                imgurl = $('img', html).attr('src');
                $('#upload_image').val(imgurl);
                document.getElementById("teams_back_image").src = imgurl;
                document.getElementById("upload_image_button").value = "Change Image";
                tb_remove();
            };

        });
    </script>
    <?php
}

//MATCHES METABOXES/////////////////////////////////////////////////////////////

function add_matches_metaboxes() {
    add_meta_box('wpt_match_information', 'Match information', 'wpt_match_information', 'matches', 'normal', 'high');
    add_meta_box('wpt_feed_top', 'Top feed', 'wpt_feed_top', 'matches', 'normal', 'high');
}

function wpt_match_information() {
    $urlstyle = plugins_url('styles/adminStyles.css', __FILE__);
    ?>
    <link rel="stylesheet" href="<?php echo $urlstyle; ?>" type="text/css"/>
    <?php
    $post = $_GET['post'];
    $image1url;
    $image2url;

    $the_query = new WP_Query(array('post_type' => 'teams', 'posts_per_page'=>-1));


    if ($the_query->have_posts()) {
        $currentValue1 = get_post_meta($post, '_match_team_id_1', true);
        $currentValue2 = get_post_meta($post, '_match_team_id_2', true);
        ?>
        <table class="team_choose">
            <tr>
                <td>
                    <select required name="_match_team_id_1" id="sel1" onchange="
                    getThumbnailAndLineups(this, 1);
                    updateList(this, 1);">
                        <option disabled<?php if (!$currentValue1) echo ' selected'; ?> >select first team</option>
                            <?php
                            while ($the_query->have_posts()) :
                                $the_query->the_post();
                                $id = get_the_ID(); //get_the_title();
                                ?>
                            <option <?php
                            if ($currentValue1 == $id && $currentValue1 != NULL) {
                                echo ' selected';
                                $image1url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
                            }

                            if ($currentValue2 == $id && $currentValue2 != NULL) {
                                echo ' style="display:none"';
                            }
                            /* echo' myid="' . get_the_ID() . '"'; */
                            /* echo' id="1-' . wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())) . '" '; */
                            ?> value="<?php echo $id; ?>">
                            <?php
                                echo get_the_title();
                                ?>
                            </option>
                                <?php endwhile; ?>
                    </select>
                </td>
                <td>

                    VS



                </td>
                <td>


                    <select required id="sel2" name="_match_team_id_2" onchange="
                                    getThumbnailAndLineups(this, 2);
                                    updateList(this, 2);">';
                        <option disabled <?php if (!$currentValue2) echo ' selected'; ?> >select second team</option>';
                        <?php
                        while ($the_query->have_posts()) :
                            $the_query->the_post();
                            $id = get_the_ID();
                            ?>
                            <option
                                    <?php
                                    if ($currentValue2 == $id && $currentValue2 != NULL) {
                                        echo ' selected';
                                        $image2url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
                                    }

                                    if ($currentValue1 == $id && $currentValue1 != NULL) {
                                        echo ' style="display:none"';
                                    }
                                    /* echo' myid="' . get_the_ID() . '"'; */
                                    /* echo' id="2-' . wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())) . '" '; */
                                    ?>
                                value="<?php echo $id ?>">
                            <?php echo get_the_title(); ?>
                            </option>
                                <?php endwhile; ?>
                    </select> 
                </td>
            </tr>
            <tr>
                <td>
                    <img id="team1_image" class="image_w_100" src ="<?php echo $image1url; ?>" >
                </td>
                <td> SCORE<br/>
                    <input type='number' name='_score1' value='<?php echo get_post_meta($post, '_score1', true) ?>' class='score_box'/>
                    <input type='number' name='_score2' value='<?php echo get_post_meta($post, '_score2', true) ?>' class='score_box'/>
                </td>
                <td>
                    <img id="team2_image" class="image_w_100"  src = "<?php echo $image2url ?>" >
                </td></tr>



         
            <tr>


                <td class="txtar">
                    <table id="players1">

        <?php //var_dump(explode(",",get_post_meta($post, '_ft1', true))); ?>

                       
        <?php
        // $players = explode(",",get_post_meta($post, '_ft1', true));
        $pl_query = new WP_Query(array(
            'post_type' => 'players',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_team',
                    'value' => $currentValue1,
                ),
            ),
        ));


        //print_r($pl_query->get_posts());
        //echo"<br/><br/>";
        //var_dump(sortPlayers($pl_query->get_posts()));


        $ft1 = explode(",", get_post_meta($post, '_ft1', true));
        $sb1 = explode(",", get_post_meta($post, '_sb1', true));
        $incap = explode(",", get_post_meta($currentValue1, '_incap', true));
        if ($pl_query->have_posts()) :

            $pl = sortPlayers($pl_query->get_posts());
            //$pl=$pl_query->get_posts();
        ?> <tr>
                            <td colspan="3"></td>
                            <td>
                                FT
                            </td>
                            <td>
                                Sub
                            </td>
                        </tr>
            <?php

            foreach ($pl as $p):
                ?>
                                <tr class="pltr<?php if (in_array($p->ID, $incap)) echo ' disabled_tr_m' ?>">
                                    <td>
                <?php echo substr(get_post_meta($p->ID, '_players_position', true), 0, 1); ?>
                                    </td>
                                    <td>
                <?php echo get_post_meta($p->ID, '_number', true); ?>
                                    </td>
                                    <td>
                <?php echo get_the_title($p->ID); ?>
                                    </td>
                                        <?php if (!in_array($p->ID, $incap)) : ?>
                                        <td>
                                            <input  class="lineups_cb main_cb" type='checkbox' <?php if (in_array($p->ID, $ft1)) echo "checked"; ?> name='_ft1[<?php echo $p->ID; ?>]'/>
                                        </td>
                                        <td>
                                            <input   class="lineups_cb" type='checkbox' <?php if (in_array($p->ID, $sb1)) echo "checked"; ?> name='_sb1[<?php echo $p->ID; ?>]'/>
                                        </td>
                <?php else: ?>
                                        <td style="text-align: center!important;" colspan="2"><i class="fa pl_medkit_t pl_medkit_m fa-medkit"></i><td>
                                    <?php endif; ?>


                                </tr>

                <?php
            endforeach;
        endif;
        ?>

                    </table>
                </td>
                <td>
                </td>
                <td  class="txtar">
                    <table id="players2">


        <?php
        // $players = explode(",",get_post_meta($post, '_ft1', true));
        $pl_query = new WP_Query(array(
            'post_type' => 'players',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_team',
                    'value' => $currentValue2,
                ),
            ),
        ));


        $ft2 = explode(",", get_post_meta($post, '_ft2', true));
        $sb2 = explode(",", get_post_meta($post, '_sb2', true));
        $incap = explode(",", get_post_meta($currentValue2, '_incap', true));
        if ($pl_query->have_posts()) :

            $pl = sortPlayers($pl_query->get_posts());
            //$pl=$pl_query->get_posts();
        ?> <tr>
                            <td colspan="3"></td>
                            <td>
                                FT
                            </td>
                            <td>
                                Sub
                            </td>
                        </tr>
            <?php

            foreach ($pl as $p):
                ?>
                                <tr class="pltr<?php if (in_array($p->ID, $incap)) echo ' disabled_tr_m' ?>">
                                    <td>
                                <?php echo substr(get_post_meta($p->ID, '_players_position', true), 0, 1); ?>
                                    </td>
                                    <td>
                                        <?php echo get_post_meta($p->ID, '_number', true); ?>
                                    </td>
                                    <td>
                                        <?php echo get_the_title($p->ID); ?>
                                    </td>
                                        <?php if (!in_array($p->ID, $incap)) : ?>
                                        <td>
                                            <input  class="lineups_cb main_cb" type='checkbox' <?php if (in_array($p->ID, $ft2)) echo "checked"; ?> name='_ft2[<?php echo $p->ID; ?>]'/>
                                        </td>
                                        <td>
                                            <input   class="lineups_cb" type='checkbox' <?php if (in_array($p->ID, $sb2)) echo "checked"; ?> name='_sb2[<?php echo $p->ID; ?>]'/>
                                        </td>
                <?php else: ?>
                                       <td style="text-align: center!important;" colspan="2"><i class="fa pl_medkit_t pl_medkit_m fa-medkit"></i><td>
                                    <?php endif; ?>


                                </tr>

                <?php
            endforeach;
        endif;
        ?>
                    </table>
                </td>



            </tr>
            <tr>
               
                <td colspan="3">
                    <table>
                        <tr>
                            <td>
                                MATCH DATE: <input type="date" value=<?php echo"'" . get_post_meta($post, '_match_date', true) . "'" ?> name="_match_date">
                            </td>
                            <td colspan="2">
                                MATCH TIME: <input type="time" value=<?php echo"'" . get_post_meta($post, '_match_time', true) . "'" ?> name="_match_time" >
                            </td>

                        </tr>
               <td></td>
                        <tr>
                            <td>
        <?php
        $location = get_post_meta($post, '_league', true);

        $the_query = new WP_Query(array('post_type' => 'leagues','posts_per_page'=>-1));


        if ($the_query->have_posts()) {

            echo'League: <select required id="_league" name="_league">';
            echo '<option disabled value="null"';
            if ($location == NULL)
                echo ' selected';
            echo'>Select a league</option>';


            while ($the_query->have_posts()) {
                $the_query->the_post();
                $count = get_the_ID();
                echo'<option';
                if ($location == $count && $location != NULL) {
                    echo ' selected';
                }
                echo' value="' . $count . '">';
                echo get_the_title();
                echo'</option>';
            }
            echo'</select>';
        }
        ?>

                            </td>
                            <td colspan="2">
                                <input type="hidden" id="ht" value=<?php echo "'" . plugins_url('show_seasons.php', __FILE__) . "'"; ?> />
                                <input type="hidden" id="curr_league" value="<?php echo get_post_meta($post, '_league', true); ?>" />
                                <input type="hidden" id="curr_season" value="<?php echo get_post_meta($post, '_season', true); ?>" />
                                 hashtag: #<input type="text" name="_match_hashtag" value=<?php echo"'" . get_post_meta($post, '_match_hashtag', true) . "'" ?> placeholder="football">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                Show match score (if the match has already started or ended): <input type="checkbox" name="_show_score" <?php
                        if (get_post_meta($post, '_show_score', true)) {
                            echo "checked";
                        }
                        ?>>
                            </td>
                        </tr>
                </td>
                </tr>
        </table>
        
        </table>
        <?php
    }
    ?>
               <script>

            jQuery(document).ready(function () {
                jQuery(".lineups_cb").change(function (e) {
                    checkNay(e.target);
                });
                check11();
            });

            function checkNay(e) {
                var el = jQuery(e);
                if (el.prop('checked')) {
                    if (!el.parent().prev().children().is('input'))
                        el.parent().next().children().prop('checked', false);
                    else
                        el.parent().prev().children().prop('checked', false);
                }
                check11();
            }

            function check11() {
                console.log("---------");
                jQuery('.txtar').each(function () {
                    console.log("-");
                    console.log(jQuery(this).find('.main_cb:checked').length);
                    if (jQuery(this).find('.main_cb:checked').length >= 11) {
                        console.log(jQuery(this).find('.main_cb:checked').length + ">=" + 11);
                        jQuery(this).find('.pltr').each(function () {
                            if (!jQuery(this).find('.main_cb').prop("checked"))
                                jQuery(this).find('.main_cb').attr("disabled", "true");
                        });
                    } else
                        jQuery(this).find('.main_cb').removeAttr("disabled");
                });
            }

            function updateList(sel, num)
            {
                num = num === 2 ? 1 : 2;
                jQuery('#sel' + num).children().css({'display': 'block'});
                jQuery('#sel' + num + ' [value="' + sel[sel.selectedIndex].value + '"]').css({"display": "none"});
            }

            function getThumbnailAndLineups(el, num) {
                jQuery.ajax({
                    type: "GET",
                    url: "<?php echo plugins_url("admin_matches_teams.php", __FILE__); ?>",
                    data: "id=" + el[el.selectedIndex].value,
                    success: function (msg) {
                        //alert("������� ������: " + msg);

                        var team = jQuery.parseJSON(msg);
                        console.log(team);
                        jQuery('#team' + num + '_image').attr('src', team[0]);
                        var players_wr = jQuery('#players' + num);
                        players_wr.html('');
                        players_wr.append('<tr><td colspan="3"></td><td>FT</td><td>Sub</td></tr>');
                        var players = team[1];
                        for (var i = 0; i < players.length; i++) {
                            var player = "<tr class='pltr";
                            if(players[i].incap)player += " disabled_tr_m";
                            player +="'><td>";
                            player += players[i].position;
                            player += "</td><td>";
                            player += players[i].number;
                            player += "</td><td>";
                            player += players[i].name;
                            player += "</td>"
                   
                                if(!players[i].incap){
                            player += "<td>";
                            player += "<input onchange='checkNay(this)' class='lineups_cb main_cb' type='checkbox' name='_ft" + num + "[" + players[i].id + "]'/>";
                            player += "</td><td>";
                            player += "<input onchange='checkNay(this)' class='lineups_cb' type='checkbox' name='_sb" + num + "[" + players[i].id + "]'/>";
                            player += "</td>";
                        }
                        else{
                             player += '<td style="text-align: center!important;" colspan="2"><i class="fa pl_medkit_t pl_medkit_m fa-medkit"></i><td>';
                        }
       
                            player += "</tr>";
                            players_wr.append(player);
                        }
                        check11();
                    }
                });
            }

        </script>

    <?php
}

function wpt_match_information2() {
    ?>
    <style>
        .image_w_100{
            width: 100px;
            min-width: 100px;
            //min-height: 100px;
            height: auto;
            //background-color: #DDD;
        }
        .score_box{
            width:40px 
        }
        .txtar{
            background-color: #DDD;
            vertical-align: top;
            min-width: 120px;
        }
        .txtar_title{
            margin-top: 0px!important;
            margin-bottom: 0px!important;
        }
        .txt{
            height: 220px;
        }
        .team_choose td
        {
            text-align: center;
        }
    </style>
    <?php
    $post = $_GET['post'];
    $image1url;
    $image2url;

    $the_query = new WP_Query(array('post_type' => 'teams','posts_per_page'=>-1));


    if ($the_query->have_posts()) {
        $currentValue1 = get_post_meta($post, '_match_team_id_1', true);
        $currentValue2 = get_post_meta($post, '_match_team_id_2', true);

        echo '<table class="team_choose">';
        echo'<tr>';





        echo'<td>';
        echo'<select required name="_match_team_id_1" id="sel1" onchange="if (this.selectedIndex) updateSecondList(this);">';
        echo '<option disabled';
        if (!$currentValue1)
            echo ' selected';
        echo'>select first team</option>';

        while ($the_query->have_posts()) {
            $the_query->the_post();
            $count = get_the_ID(); //get_the_title();
            echo'<option ';
            if ($currentValue1 == $count && $currentValue1 != NULL) {
                echo ' selected';
                $image1url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
            }

            if ($currentValue2 == $count && $currentValue2 != NULL) {
                echo ' style="display:none"';
            }

            echo' id="1-' . wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())) . '" ';
            echo' value="' . $count . '">';
//echo get_the_post_thumbnail($post, array(10, 10));
            echo get_the_title();
            echo'</option>';
        }
        echo'</select></td><td>';

        echo"VS";



        echo'</td><td>';


        echo'<select required id="sel2" name="_match_team_id_2" onchange="if (this.selectedIndex) updateFirstList(this);">';
        echo '<option disabled';
        if (!$currentValue2)
            echo ' selected';
        echo'>select second team</option>';
        while ($the_query->have_posts()) {
            $the_query->the_post();
            $count = get_the_ID();
            echo'<option';
            if ($currentValue2 == $count && $currentValue2 != NULL) {
                echo ' selected';
                $image2url = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
            }

            if ($currentValue1 == $count && $currentValue1 != NULL) {
                echo ' style="display:none"';
            }

            echo' id="2-' . wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())) . '" ';
            echo' value="' . $count . '">';
            echo get_the_title();
            echo'</option>';
        }
        echo'</select> </td>';
        echo '</tr><tr>'
        . '<td>';
        echo'<img id="team1_image" class="image_w_100" src ="' . $image1url . '" ></td><td> SCORE<br/>';
        echo "<input type='number' name='_score1' value='" . get_post_meta($post, '_score1', true) . "' class='score_box'/>";
        echo "<input type='number' name='_score2' value='" . get_post_meta($post, '_score2', true) . "' class='score_box'/>";
        echo "</td><td>";
        echo '<img id="team2_image" class="image_w_100"  src = "' . $image2url . '";  >';
        echo "</td></tr>";
        ?>


        <tr></tr>
        <tr>
            <td colspan="3">
                <table>
                    <tr>
                        <td class="txtar">
                            <h2 class="txtar_title">First team</h2>
                            <textarea class="txt" name="_first_team_1_players"  id="first_team_1_players" ><?php echo get_post_meta($post, '_first_team_1_players', true); ?></textarea>
                        </td>
                        <td class="txtar">
                            <h2 class="txtar_title">Substitute</h2>
                            <textarea class="txt" name="_substitute_1_players" id="substitute_1_players" ><?php echo get_post_meta($post, '_substitute_1_players', true); ?></textarea>
                        </td>

                        <td class="txtar">
                            <h2 class="txtar_title">First team</h2>
                            <textarea class="txt" name="_first_team_2_players" id="first_team_2_players" ><?php echo get_post_meta($post, '_first_team_2_players', true); ?></textarea>
                        </td>
                        <td class="txtar">
                            <h2 class="txtar_title">Substitute</h2>
                            <textarea class="txt" name="_substitute_2_players" id="substitute_2_players" ><?php echo get_post_meta($post, '_substitute_2_players', true); ?></textarea>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
        <tr>
            <td>
                MATCH DATE: <input type="date" value=<?php echo"'" . get_post_meta($post, '_match_date', true) . "'" ?> name="_match_date">
            </td>
            <td>
                MATCH TIME: <input type="time" value=<?php echo"'" . get_post_meta($post, '_match_time', true) . "'" ?> name="_match_time" >
            </td>
            <td>
                hashtag: #<input type="text" name="_match_hashtag" value=<?php echo"'" . get_post_meta($post, '_match_hashtag', true) . "'" ?> placeholder="football">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Show match score (if the match has already started or ended): <input type="checkbox" name="_show_score" <?php
                if (get_post_meta($post, '_show_score', true)) {
                    echo "checked";
                }
                ?>>
            </td>
            <td>
                <?php
                $location = get_post_meta($post, '_league', true);

                $the_query = new WP_Query(array('post_type' => 'leagues','posts_per_page'=>-1));


                if ($the_query->have_posts()) {

                    echo'League: <select required name="_league">';
                    echo '<option disabled';
                    if ($location == NULL)
                        echo ' selected';
                    echo'>Select a league</option>';


                    while ($the_query->have_posts()) {
                        $the_query->the_post();
                        $count = get_the_ID();
                        echo'<option';
                        if ($location == $count && $location != NULL) {
                            echo ' selected';
                        }
                        echo' value="' . $count . '">';
                        echo get_the_title();
                        echo'</option>';
                    }
                    echo'</select>';
                }
                ?>

            </td>
        </tr>
        </table>
        <?php
    }
    ?>
    <script>


        function updateSecondList(sel1)
        {
            var team1 = sel1.options[sel1.selectedIndex].id;
            team1 = team1.substr(2, team1.length);
            var sel2 = document.getElementById("sel2");
            for (var i = 0; i < sel2.options.length; i++) {
                sel2.options[i].style.display = "block";
            }
            document.getElementById("2-" + team1).style.display = "none";
            document.getElementById("team1_image").src = team1;

        }

        function updateFirstList(sel2)
        {
            var team2 = sel2.options[sel2.selectedIndex].id;
            team2 = team2.substr(2, team2.length);
            var sel1 = document.getElementById("sel1");
            for (var i = 0; i < sel1.options.length; i++) {
                sel1.options[i].style.display = "block";
            }
            console.log(team2);
            document.getElementById("1-" + team2).style.display = "none";
            document.getElementById("team2_image").src = team2;
        }
    </script>

    <?php
}

//LIGUES METABOXES//////////////////////////////////////////////////////////////

function add_leagues_metaboxes() {
    add_meta_box('wpt_league_tables', 'Tables', 'wpt_league_tables', 'leagues', 'normal', 'high');
}

function wpt_league_tables() {
    wp_reset_postdata();

    $default_table = "
    <table>
    <tr>
                <td colspan='2'></td>
                <td colspan='6'>OVERALL</td>
                <td colspan='6'>HOME</td>
                <td colspan='6'>AWAY</td>
                <td colspan='3'></td>
    </tr>
    <tr>           
            <td >POS</td>   
            <td >TEAM</td>   

            <td >P</td>    
            <td >W</td>     
            <td >D</td>     
            <td >L</td>     
            <td >F</td>    
            <td >A</td>         

            <td >P</td>     
            <td >W</td>     
            <td >D</td>     
            <td >L</td>     
            <td >F</td>     
            <td >A</td>        

            <td >P</td>     
            <td >W</td>     
            <td >D</td>     
            <td >L</td>     
            <td >F</td>     
            <td >A</td>       

            <td >GD</td>      
            <td >PTS</td>
    </tr>
    <tr>
   <td> </td>  <!--POS-->
   <td> </td>  <!--TEAM-->

<!--   W            P            D            L            F            A   -->
   <td> </td>   <td> </td>   <td> </td>   <td> </td>   <td> </td>   <td> </td>  <!--OVERALL-->
   <td> </td>   <td> </td>   <td> </td>   <td> </td>   <td> </td>   <td> </td>  <!--HOME-->
   <td> </td>   <td> </td>   <td> </td>   <td> </td>   <td> </td>   <td> </td>  <!--AWAY-->
 
   <td> </td> <!--GD-->
   <td> </td> <!--PTS-->
</tr></table>";

    if (get_post_meta(get_the_ID(), '_tables', true) != null) {
        wp_editor(get_post_meta(get_the_ID(), '_tables', true), '_tables');
    } else {
        wp_editor($default_table, '_tables');
    }
}

//NEWS METABOXES////////////////////////////////////////////////////////////////

function add_news_metaboxes() {
    add_meta_box('wpt_news_league', 'League', 'wpt_news_league', 'news', 'normal', 'high');
    add_meta_box('wpt_feed_top', 'Top feed', 'wpt_feed_top', 'news', 'normal', 'high');
    
}

function wpt_feed_top() {
    $urlstyle = plugins_url('styles/adminStyles.css', __FILE__);
    wp_reset_postdata();
    $post =$_GET['post'];

    ?>
    <link rel="stylesheet" href="<?php echo $urlstyle; ?>" type="text/css"/>
    <div class="admin_big_text">
        <table>
            <tr>
                <td>
                    Top feed:
                </td>
                <td>
                    <input type="checkbox" <?php if(get_post_meta($post, '_top', true)){echo "checked";}?> name="_top"/>
                <td>
                <td rowspan="2">
                    <table>
                        <tr>
                            <td rowspan="2" class="ttn" style="background-color: #bcbcbc">
                                Top feed
                            </td>
                            <td class="tfn" style="background-color: #a9a9a9">
                                Featured feed
                            </td>
                        </tr>
                        <tr>
                            <td class="tfn" style="background-color: #999999">
                                Featured feed
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    Featured feed: 
                </td>
                <td>
                    <input type="checkbox" <?php if(get_post_meta($post, '_featured', true)){echo "checked";}?>  name="_featured"/>
                </td>
            </tr>
        </table>
        </div>

        <?php
    }

function wpt_news_league() {
    global $post;

    $location = get_post_meta($post->ID, '_league', true);

    $the_query = new WP_Query(array('post_type' => 'leagues','posts_per_page'=>-1));

    if ($the_query->have_posts()) {

        echo'<select required name="_league">';
        echo '<option disabled';
        if (!$location)
            echo ' selected';
        echo'>Select a league</option>';


        while ($the_query->have_posts()) {
            $the_query->the_post();
            $count = get_the_ID();
            echo'<option';
            if ($location == $count && $location != NULL) {
                echo ' selected';
            }
            echo' value="' . $count . '">';
            echo get_the_title();
            echo'</option>';
        }
        echo'</select>';
    }
}

//PUB TALK METABOXES////////////////////////////////////////////////////////////

function add_pub_talk_metaboxes() {
    add_meta_box('wpt_feed_top', 'Top feed', 'wpt_feed_top', 'pub_talk', 'normal', 'high');
}


//SAVING META///////////////////////////////////////////////////////////////////

function wpt_save_meta($post_id, $post) {


    if (!current_user_can('edit_post', $post->ID)) {
        return $post->ID;
    }



    if (strcmp(get_post_type($post->ID), "teams") == 0) {
        $temp = array(
            '_league',
            '_teams_back_image_url',
        );
        // if(isset($_POST['_league'])){echo $_POST['_league'];}
        $the_query = new WP_Query(array('post_type' => 'leagues','posts_per_page'=>-1));
        $count = $the_query->post_count;
        $all_leagues = $the_query->get_posts();
        $leagues = array();
        for ($i = 0; $i < $count; $i++) {
            if (isset($_POST['_leagues' . $i])) {
                array_push($leagues, ($all_leagues[$i]->ID));
                // $leagues["some".$i]=$all_leagues[$i]->ID;
            }
        }
        if (count($leagues) > 0) {
            update_post_meta($post->ID, '_leagues', $leagues);
        } else
            $meta["_leagues"] = null;
        
         $tmp = array();
        foreach ($_POST['_incap'] as $p => $value) {
            array_push($tmp, $p);
        }
        $meta['_incap'] = $tmp;
    }

    if (strcmp(get_post_type($post->ID), "players") == 0) {

        $temp = array(
            '_team',
            '_date_of_birth',
            '_age',
            '_country_of_birth',
            '_height',
            '_weight',
            '_natonal_team',
            '_appearances',
            '_goals',
            '_yellow_cards',
            '_red_cards',
            '_titles',
            '_squad',
            '_home_grown',
            '_players_position',
            '_number'
        );
    }

    if (strcmp(get_post_type($post->ID), "matches") == 0) {

        $temp = array(
            '_match_team_id_1',
            '_match_team_id_2',
            '_score1',
            '_score2',
            '_match_date',
            '_match_time',
            '_match_hashtag',
            '_first_team_1_players',
            '_substitute_1_players',
            '_first_team_2_players',
            '_substitute_2_players',
            '_league',
        );
        
         foreach (array('_ft1', '_sb1', '_ft2', '_sb2')as $m) {
            if (isset($_POST[$m])) {
                $tmp = array();
                foreach ($_POST[$m] as $p => $value) {
                    array_push($tmp, $p);
                }
                $meta[$m] = $tmp;
            }
              else $meta[$m]=null;
        }

        if (isset($_POST['_show_score'])) {
            $meta['_show_score'] = true;
        } else
            $meta['_show_score'] = false;
        
        $meta['_top'] = $_POST['_top']=="on"? true : false;
                $meta['_featured'] = $_POST['_featured']=="on"? true : false;
    }
	
	if (get_post_type($post->ID) === "pub_talk") {
        $meta['_top'] = $_POST['_top'] == "on" ? true : false;
        $meta['_featured'] = $_POST['_featured'] == "on" ? true : false;
    }

    if (strcmp(get_post_type($post->ID), "news") == 0) {
        $temp = array(
            '_league',
        );
  
        $meta['_top'] = $_POST['_top']=="on"? true : false;
                $meta['_featured'] = $_POST['_featured']=="on"? true : false;
    }

    if (strcmp(get_post_type($post->ID), "leagues") == 0) {
        $temp = array(
            '_tables',
        );
    }

    if (count($temp) > 0) {
        foreach ($temp as $temp_value) {
            if (isset($_POST[$temp_value])) {
                $meta[$temp_value] = $_POST[$temp_value];
            }
        }
    }



//echo '<script>alert("'.$_POST['_stxt'].'");</script>';
// Add values of $meta as custom fields
    if (count($meta) == 0) {
        return;
    }

    foreach ($meta as $key => $value) { // Cycle through the $meta array!
        if ($post->post_type == 'revision') {
//echo "<p>Don`t store custom data twice: ".$key."=> ".$value."</p>";
            return; // Don't store custom data twice
        }
        $value = implode(',', (array) $value); // If $value is an array, make it a CSV (unlikely)

        if (get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
//echo "<p>custom field already has a value: ".$key."=> ".$value."</p>";
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
// echo "<p>custom field doesn't have a value, create it: ".$key."=> ".$value."</p>";
            add_post_meta($post->ID, $key, $value);
        }
        if ($value == NULL) {
//echo "<p>Value is not set: ".$key."=> ".$value."</p>";
            delete_post_meta($post->ID, $key); // Delete if blank
        }
    }
}

add_action('save_post', 'wpt_save_meta', 1, 2); // save the custom fields
//METABOXES ARE SETTED//////////////////////////////////////////////////////////

	
	require "functions.php";


//DEINSTALLATION////////////////////////////////////////////////////////////////
register_uninstall_hook(__FILE__, 'my_uninstall_hook');

function my_uninstall_hook(){
    global $wpdb;
    $table_name = $wpdb->prefix . "tweets";
    $e=$wpdb->query("DROP TABLE {$table_name}");
	//die(var_dump($e));
}


function clearTweets(){
    my_uninstall_hook();
    jal_install ();
}