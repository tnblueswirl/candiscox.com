<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$sidebar_data = thegem_get_sanitize_page_sidebar_data(get_the_ID());
$sidebar_position = thegem_check_array_value(array('', 'left', 'right'), $sidebar_data['sidebar_position'], '');
$left_classes = 'col-sm-6 col-xs-12';
$right_classes = 'col-sm-6 col-xs-12';
if(is_active_sidebar('shop-sidebar') && $sidebar_position) {
	$left_classes = 'col-sm-5 col-xs-12';
	$right_classes = 'col-sm-7 col-xs-12';
}

?>

<?php
	do_action( 'woocommerce_before_single_product' );
	if ( post_password_required() ) {
		echo get_the_password_form();
		return;
	}
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="single-product-content row">
		<div class="single-product-content-left <?php echo $left_classes; ?>">
			<?php do_action('thegem_woocommerce_single_product_left'); ?>
		</div>

		<div class="single-product-content-right <?php echo $right_classes; ?>">
			<?php do_action('thegem_woocommerce_single_product_right'); ?>
		</div>

	</div>

	<div class="single-product-content-bottom">
		<?php do_action('thegem_woocommerce_single_product_bottom'); ?>
	</div>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #product-<?php the_ID(); ?> -->
