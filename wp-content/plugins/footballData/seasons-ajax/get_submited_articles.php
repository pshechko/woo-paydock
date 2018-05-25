<?php
$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';
mb_internal_encoding("UTF-8");

$count = isset($_GET['count']) && $_GET['count'] > 0 ? $_GET['count'] : -1;
$sort_by = isset($_GET['sort_by']) && $_GET['sort_by'] ? $_GET['sort_by'] : 'author';


$authors = get_users(array('role' => 'author'));
$author_ids = array();

foreach ($authors as $author)
    $author_ids[] = $author->ID;

global $current_user;
get_currentuserinfo();
$current_id = $current_user->ID;



$args = array(
    'author' => implode(',', $author_ids),
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => $count,
    'orderby' => $sort_by,
);

$article_author = "";

$articles = new WP_Query($args);

if ($articles->have_posts()) :
    while ($articles->have_posts()) :
        $articles->the_post();
        if ($article_author != get_the_author() && $sort_by == 'author') {
            $article_author = get_the_author();

            $atr_auth_id = get_the_author_meta("ID");
            ?>
            <div class="author_title">
                <strong>
                    Articles by <?php
                    echo $article_author;
                    if($atr_auth_id == $current_id) echo " (You)";
                    ?>
                </strong>
            </div>
            <?php
        }
        ?>
        <div class="home-news-excerpt">
            <div class="home-news-row">
                <?php
                $thur = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID(), 'large'));
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

                    <?php
					$post_type = get_post_type(get_the_ID());
					$tax = get_object_taxonomies($post_type);
					$tax = $tax[0];
                    $cat = get_the_terms(get_the_ID(), $tax);
					/*var_dump($cat); */
                    $cat = $cat[0];
                    $obj = get_post_type_object($post_type);
                    ?>

                    <div class='tags'><?php _e('Categories: ', 'simplecatch'); ?> <?php echo $cat->name == "" || $cat->name == "Uncategorized" ? "No" : $cat->name; ?> </div>
                    <div class='tags'>Author: <?= get_the_author() ?></div>
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
    wp_reset_postdata();
endif;