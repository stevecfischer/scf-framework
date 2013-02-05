<?php
    /**
     *
     * @package scf-framework
     * @author Steve (6/11/2012)
     * @version 2.2
     */
    get_header();
    ?>
<pre>
<?php

    echo 'some_setting => ' .get_theme_mod( 'some_setting', 'default_value' )."\n";
    echo 'some_other_setting => ' .get_theme_mod( 'some_other_setting', '#000000' )."\n";
    echo 'non_existent_setting => '.get_theme_mod( 'non_existent_setting', 'default_value' )."\n";
    echo 'some_setting2 => '.get_theme_mod( 'some_setting2', 'default_value' )."\n";

    ?>
</pre>
    <?php
    Wfc_Core_Homecontent_Loop();
    Wfc_Core_Campaign_Loop();
    Wfc_Core_News_Loop();
    Wfc_Core_Testimonial_Loop();
    while( have_posts() ) : the_post();
        echo '<h2 id="wfc-the-title">'.get_the_title().'</h2>';
        the_content();
    endwhile;
    wp_reset_query();

    get_footer();