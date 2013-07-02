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
    if( have_posts() ) : while( have_posts() ) : the_post(); ?>
        <div class="news-entry">
            <?php
            echo'<h2 class="page_title"><a href="'.get_permalink().'">'.get_the_title().
                '</a></h2>';
            ?>
            <p class="entry-meta">
                Published on
                <span><?php the_time( 'F j, Y' ); ?></span>
                in
                <span><?php echo get_the_term_list( get_the_ID(), 'topics', 'News: ', ', ', '' );  ?></span>
            </p>
            <div class="post-thumbnail">
                <a href="<?php echo get_permalink(); ?>"><?php the_post_thumbnail( "thumb" ); ?></a>
            </div>
            <?php the_excerpt( 425 ); ?>
            <a class="read-more" href="<?php echo get_permalink(); ?>">Read More &#187;</a>
            <div style="clear:both;"></div>
        </div>
    <?php
    endwhile;endif;
    wp_reset_query();
?>
    <div id="sidebar" class="left_sidebar">
        <?php
        Wfc_Core_Sidebar();
        ?>
    </div><!-- //#sidebar.left_sidebar -->

<?php get_footer(); ?>