<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.2
 */
require_once('wfc_config/wfc_config.php');
/***********************/
/*
=== Add theme specific functions below.
=== If you feel you need to edit the framework files consult a manager first.
*/
function Wfc_Core_Homecontent_Loop(){
    global $wpdb;
    $query = new WP_Query(array('post_type' => 'homeboxes', 'order' => 'ASC'));
    $i     = 1;
    if( $query->have_posts() ) : while( $query->have_posts() && $i <= 3 ) : $query->the_post(); ?>
    <div class="col_<?php echo $i++;?>">
        <div id="block">
            <h2><?php echo get_the_title(); ?></h2>
            <?php the_post_thumbnail( 'large' ); ?>
            <?php echo get_the_content(); ?>
        </div>
        <a class="learn_more" href="<?php  echo get_post_meta( $post->ID, 'homeposts_link_', true ); ?>">Learn More</a>
    </div><!--/ col_1-->
    <?php if( $i <= 3 ){
            echo '<div id="vert_div">&nbsp;</div>';
        }
    endwhile;endif;
    wp_reset_query();
}

function Wfc_Core_Campaign_Loop(){
    global $wpdb;
    $query = new WP_Query(array('post_type' => 'campaign', 'order' => 'ASC'));
    if( $query->have_posts() ) : while( $query->have_posts() && $i <= 3 ) : $query->the_post(); ?>
    <div class="col_<?php echo $i++;?>">
        <div id="block">
            <h2><?php echo get_the_title(); ?></h2>
            <?php the_post_thumbnail( 'large' ); ?>
            <?php echo get_the_content(); ?>
        </div>
        <a class="learn_more" href="<?php  echo get_post_meta( $post->ID, 'homeposts_link_', true ); ?>">Learn More</a>
    </div>
    endwhile;endif;
    wp_reset_query();
}

function Wfc_Core_News_Loop(){
    global $wpdb;
    $query = new WP_Query(array('post_type' => 'news', 'order' => 'ASC'));
    $i     = 1;
    if( $query->have_posts() ) : while( $query->have_posts() && $i <= 3 ) : $query->the_post(); ?>
    <div class="col_<?php echo $i++;?>">
        <div id="block">
            <h2><?php echo get_the_title(); ?></h2>
            <?php the_post_thumbnail( 'large' ); ?>
            <?php echo get_the_content(); ?>
        </div>
        <a class="learn_more" href="<?php  echo get_post_meta( $post->ID, 'homeposts_link_', true ); ?>">Learn More</a>
    </div><!--/ col_1-->
    <?php if( $i <= 3 ){
            echo '<div id="vert_div">&nbsp;</div>';
        }
    endwhile;endif;
    wp_reset_query();
}

function Wfc_Core_Testimonial_Loop(){
    global $wpdb;
    $query = new WP_Query(array('post_type' => 'testimonial', 'order' => 'ASC'));
    $i     = 1;
    if( $query->have_posts() ) : while( $query->have_posts() && $i <= 3 ) : $query->the_post(); ?>
    <div class="col_<?php echo $i++;?>">
        <div id="block">
            <h2><?php echo get_the_title(); ?></h2>
            <?php the_post_thumbnail( 'large' ); ?>
            <?php echo get_the_content(); ?>
        </div>
        <a class="learn_more" href="<?php  echo get_post_meta( $post->ID, 'homeposts_link_', true ); ?>">Learn More</a>
    </div><!--/ col_1-->
    <?php if( $i <= 3 ){
            echo '<div id="vert_div">&nbsp;</div>';
        }
    endwhile;endif;
    wp_reset_query();
}