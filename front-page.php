<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.2
 */
get_header();
Wfc_Core_Homecontent_Loop();
Wfc_Core_Campaign_Loop();
Wfc_Core_News_Loop();
Wfc_Core_Testimonial_Loop();

   while( have_posts() ) : the_post();
      echo '<h2 id="wfc-the-title">'.get_the_title().'</h2>';
      the_content();
   endwhile;wp_reset_query();
    ?>

<?php
get_footer();
