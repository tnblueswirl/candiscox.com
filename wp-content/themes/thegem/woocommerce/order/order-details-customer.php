<?php
/**
 * Order Customer Details
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="row order-customer-details">
	<div class="<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() ) : ?>col-sm-12 col-md-4<?php else: ?>col-sm-6 col-xs-12<?php endif; ?>">
		<header><h3><span class="light"><?php _e( 'Customer Details', 'woocommerce' ); ?></span></h3></header>
		<?php if ( $order->customer_note ) : ?>
			<?php _e( 'Note:', 'woocommerce' ); ?>
			<?php echo wptexturize( $order->customer_note ); ?>
			<br/>
		<?php endif; ?>

		<?php if ( $order->billing_email ) : ?>
			<?php _e( 'Email:', 'woocommerce' ); ?>
			<strong><?php echo esc_html( $order->billing_email ); ?></strong>
			<br/>
		<?php endif; ?>

		<?php if ( $order->billing_phone ) : ?>
			<?php _e( 'Telephone:', 'woocommerce' ); ?>
			<strong><?php echo esc_html( $order->billing_phone ); ?></strong>
			<br/>
		<?php endif; ?>

		<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
	</div>

	<div class="<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() ) : ?>col-sm-12 col-md-4<?php else: ?>col-sm-6 col-xs-12<?php endif; ?>">
		<header class="title">
			<h3><span class="light"><?php _e( 'Billing Address', 'woocommerce' ); ?></span></h3>
		</header>
		<address>
			<?php echo ( $address = $order->get_formatted_billing_address() ) ? $address : __( 'N/A', 'woocommerce' ); ?>
		</address>
	</div>

	<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() ) : ?>
		<div class="col-sm-12 col-md-4">
			<header class="title">
				<h3><span class="light"><?php _e( 'Shipping Address', 'woocommerce' ); ?></span></h3>
			</header>
			<address>
				<?php echo ( $address = $order->get_formatted_shipping_address() ) ? $address : __( 'N/A', 'woocommerce' ); ?>
			</address>
		</div>
	<?php endif; ?>
</div>
