<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Catch Themes
 * @subpackage Simple_Catch_Pro
 * @since Simple Catch Pro 1.0
 */
?>

<?php// print_r(get_object_taxonomies(get_post_type(get_the_ID())))?>

<?php if ( function_exists( 'simplecatch_content' ) ) simplecatch_content(); ?>