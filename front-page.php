<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.2
 */
get_header();

   while( have_posts() ) : the_post();
      echo '<h2 id="wfc-the-title">'.get_the_title().'</h2>';
      echo '<div id="wfc-the-content">'.get_the_content().'</div>';
   endwhile;wp_reset_query();

get_footer();
