<?php
/**
 *
 * @package scf-framework
 * @author Steve (6/11/2012)
 * @version 2.1
 */
get_header();
?>
	<?php
		if( have_posts()) : 
			while( have_posts() ) : the_post();
				echo '<h2 id="wfc-the-title">'.get_the_title().'</h2>';
				echo '<p>'.get_the_content().'</p>';
			endwhile;
		else:
			echo 'There are no posts matching that category.';
		endif;
		wp_reset_query();
	?>

	<div id="sidebar" class="left_sidebar">
		<?php if( !dynamic_sidebar('Left Sidebar')) :
			echo 'no sidebar';
			endif;
		?>
	</div><!-- //#sidebar.left_sidebar -->
	
<?php get_footer(); ?>