<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 0.1
 */
get_header();

if(  have_posts() ) : while( have_posts() ) : the_post();
   echo '<h2 id="wfc-the-title">'.get_the_title().'</h2>';
   echo '<div id="wfc-the-content">'.get_the_content().'</div>';
endwhile;endif;wp_reset_query();

get_footer();
