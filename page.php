<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.2
 */
get_header();
?>
   <?php
    Wfc_Core_Page_Loop();
   ?>

   <div id="sidebar" class="left_sidebar">
      <?php
        Wfc_Core_Sidebar();
      ?>
   </div><!-- //#sidebar.left_sidebar -->

<?php get_footer(); ?>