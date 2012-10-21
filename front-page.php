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
      the_content();
   endwhile;wp_reset_query();
    ?>
<embed src="http://www.inorout.com/events">
<?php
get_footer();
