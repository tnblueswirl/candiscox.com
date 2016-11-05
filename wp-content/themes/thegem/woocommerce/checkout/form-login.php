<?php
/**
 * Checkout login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
	return;
}

$info_message  = apply_filters( 'woocommerce_checkout_login_message', __( 'Existing customer', 'woocommerce' ) );
?>
<h2><span class="light"><?php echo $info_message; ?></span></h2>

<?php
	woocommerce_login_form(
		array(
			'message'  => '',
			'redirect' => wc_get_page_permalink( 'checkout' ),
			'hidden'   => false
		)
	);
?>
