<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters('loop_shop_columns', 4);
}

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array('inline-column');
if($woocommerce_loop['columns'] == 2) {
	$classes[] = 'col-xs-6';
} elseif($woocommerce_loop['columns'] == 3) {
	$classes[] = 'col-sm-4 col-xs-6';
} elseif($woocommerce_loop['columns'] == 4) {
	$classes[] = 'col-sm-3 col-xs-6';
} elseif($woocommerce_loop['columns'] == 6) {
	$classes[] = 'col-lg-2 col-md-4 col-sm-4 col-xs-4';
} else {
	$classes[] = 'col-xs-12';
}
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] ) {
	$classes[] = 'first';
}
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] ) {
	$classes[] = 'last';
}
?>
<div <?php post_class($classes); ?>>
	<div class="product-inner centered-box">

		<?php do_action('woocommerce_before_shop_loop_item'); ?>

		<a href="<?php the_permalink(); ?>" class="product-image">
			<div class="product-labels"><?php do_action('woocommerce_shop_loop_item_labels'); ?></div>
			<span class="product-image-inner"><?php do_action('woocommerce_shop_loop_item_image'); ?></span>
		</a>

		<div class="product-info clearfix">
			<?php do_action('woocommerce_before_shop_loop_item_title'); ?>
			<div class="product-title title-h6"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
			<?php do_action('woocommerce_after_shop_loop_item_title'); ?>
		</div>

		<div class="product-bottom clearfix">
			<?php do_action('woocommerce_after_shop_loop_item'); ?>
		</div>

	</div>
</div>
