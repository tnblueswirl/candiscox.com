<?php
/**
 * External product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

<div class="cart">
	<?php thegem_button(array(
		'text' => $button_text,
		'href' => esc_url($product_url),
		'attributes' => array('rel' => 'nofollow', 'class' => 'single_add_to_cart_button button alt'),
	), 1); ?>
	<?php do_action('thegem_woocommerce_after_add_to_cart_button'); ?>
</div>

<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>