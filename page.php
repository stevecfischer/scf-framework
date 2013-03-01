<?php
    /**
     *
     * @package scf-framework
     * @author Steve (6/11/2012)
     * @version 2.2
     */
    get_header();
?>
<div id="navigation" class="left_sidebar">
    <?php
    Wfc_Core_Sidebar();
    ?>
</div><!-- //#navigation.left_sidebar -->
<?php Wfc_Core_Page_Loop(); ?>
<?php get_footer(); ?>