<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 */

$thegem_use_custom = get_post(thegem_get_option('404_page'));

$thegem_q = new WP_Query(array('page_id' => thegem_get_option('404_page')));

get_header(); ?>

<div id="main-content" class="main-content">

<?php if($thegem_use_custom && $thegem_q->have_posts()) : $thegem_q->the_post(); ?>

<?php
	get_template_part( 'content', 'page' );
?>

<?php else : ?>
<?php echo thegem_page_title(); ?>

<div class="block-content">
	<div class="container">
		<div class="entry-content">
			<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'thegem' ); ?></p>
			<div class="search-form-block"><?php get_search_form(); ?></div>
		</div><!-- .entry-content -->
	</div>
</div>
<?php endif; ?>

</div><!-- #main-content -->

<?php
get_footer();