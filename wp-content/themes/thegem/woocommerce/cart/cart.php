<?php
/**
 * Cart Page
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.3.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
wp_enqueue_script('thegem-woocommerce');

wc_print_notices();
?>

<div class="woocommerce-before-cart clearfix"><?php do_action( 'woocommerce_before_cart' ); ?></div>

<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post" class="woocommerce-cart-form">

<?php do_action( 'woocommerce_before_cart_table' ); ?>

<div class="gem-table"><table class="shop_table cart" cellspacing="0">
	<thead>
		<tr>
			<th class="product-remove">&nbsp;</th>
			<th class="product-name" colspan="2"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-price"><?php _e( 'Price', 'woocommerce' ); ?></th>
			<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="product-subtotal"><?php _e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

					<td class="product-remove">
						<?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s">&#xe619;</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
						?>
					</td>

					<td class="product-thumbnail">
						<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							if ( ! $_product->is_visible() ) {
								echo $thumbnail;
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $thumbnail );
							}
						?>
					</td>

					<td class="product-name">
						<div class="product-title"><?php
							if ( ! $_product->is_visible() ) {
								echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
							} else {
								echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s </a>', esc_url( $_product->get_permalink( $cart_item ) ), $_product->get_title() ), $cart_item, $cart_item_key );
							}
						?></div>
						<div class="product-meta"><?php

							// Meta data
							echo WC()->cart->get_item_data( $cart_item );

							// Backorder notification
							if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
								echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
							}
						?></div>
					</td>

					<td class="product-price">
						<?php
							echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>
					</td>

					<td class="product-quantity">
						<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							} else {
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "cart[{$cart_item_key}][qty]",
									'input_value' => $cart_item['quantity'],
									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									'min_value'   => '0'
								), $_product, false );
							}

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
						?>
					</td>

					<td class="product-subtotal">
						<?php
							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
					</td>
				</tr>
				<?php
			}
		}

		do_action( 'woocommerce_cart_contents' );
		?>
		<tr>
			<td colspan="6" class="actions">

				<?php if ( WC()->cart->coupons_enabled() ) { ?>
					<div class="coupon">

						<input type="text" name="coupon_code" class="input-text coupon-code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'thegem' ); ?>" />
						<?php
							thegem_button(array(
								'tag' => 'button',
								'text' => __( 'Apply', 'woocommerce' ),
								'style' => 'outline',
								'size' => 'medium',
								'attributes' => array(
									'name' => 'apply_coupon',
									'value' => __( 'Apply', 'woocommerce' ),
									'type' => 'submit',
								)
							), true);
						?>

						<?php do_action('woocommerce_cart_coupon'); ?>

					</div>
				<?php } ?>

				<div class="submit-buttons">
					<?php
						thegem_button(array(
							'tag' => 'button',
							'text' => __( 'Update Cart', 'woocommerce' ),
							'size' => 'medium',
							'extra_class' => 'update-cart',
							'attributes' => array(
								'name' => 'update_cart',
								'value' => __( 'Update Cart', 'woocommerce' ),
								'type' => 'submit',
							)
						), true);
					?>
					<?php
						thegem_button(array(
							'tag' => 'button',
							'text' => __( 'Checkout', 'woocommerce' ),
							'size' => 'medium',
							'extra_class' => 'checkout-button-button',
							'attributes' => array(
								'name' => 'proceed',
								'value' => __( 'Proceed to Checkout', 'woocommerce' ),
								'type' => 'submit',
							)
						), true);
					?>
				</div>

				<?php wp_nonce_field( 'woocommerce-cart' ); ?>
			</td>
		</tr>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
	</tbody>
</table></div>

<?php do_action( 'woocommerce_after_cart_table' ); ?>

</form>

<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post" class="woocommerce-cart-form responsive">

<?php do_action( 'woocommerce_before_cart_table' ); ?>

<?php
foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
	$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
	$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

	if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
		?>

		<div class="cart-item rounded-corners shadow-box">
			<table class="shop_table cart"><tbody><tr>
				<td class="product-thumbnail">
					<?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

						if ( ! $_product->is_visible() )
							echo $thumbnail;
						else
							printf( '<a href="%s">%s</a>', esc_url($_product->get_permalink($cart_item)), $thumbnail );
					?>
				</td>

				<td class="product-name">
					<div class="product-title"><?php
						if ( ! $_product->is_visible() )
							echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
						else
							echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url($_product->get_permalink()), $_product->get_title() ), $cart_item, $cart_item_key );
						?></div>
					<div class="product-meta"><?php
						// Meta data
						echo WC()->cart->get_item_data( $cart_item );

						// Backorder notification
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
							echo '<p class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</p>';
					?></div>
				</td>

				<td class="product-remove">
					<?php
						echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s">&#xe619;</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
					?>
				</td>
			</tr></tbody></table>
			<div class="gem-table"><table class="shop_table cart">
				<thead>
					<tr>
						<th class="product-price"><?php _e( 'Price', 'woocommerce' ); ?></th>
						<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
						<th class="product-subtotal"><?php _e( 'Total', 'woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="product-price">
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
							?>
						</td>

						<td class="product-quantity">
							<?php
								if ( $_product->is_sold_individually() ) {
									$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
								} else {
									$product_quantity = woocommerce_quantity_input( array(
										'input_name'  => "cart[{$cart_item_key}][qty]",
										'input_value' => $cart_item['quantity'],
										'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
										'min_value'   => '0'
									), $_product, false );
								}

								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
							?>
						</td>

						<td class="product-subtotal">
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
							?>
						</td>
					</tr>
				</tbody>
			</table></div>
		</div>
		<?php
	}
}

?>

<div class="actions">
	<?php if ( WC()->cart->coupons_enabled() ) { ?>
		<div class="coupon shadow-box rounded-corners centered-box">

			<input type="text" name="coupon_code" class="input-text coupon-code" value="" placeholder="<?php esc_attr_e( 'Coupon', 'thegem' ); ?>" />
			<?php
				thegem_button(array(
					'tag' => 'button',
					'text' => __( 'Apply', 'woocommerce' ),
					'style' => 'outline',
					'size' => 'medium',
					'attributes' => array(
						'name' => 'apply_coupon',
						'value' => __( 'Apply', 'woocommerce' ),
						'type' => 'submit',
					)
				), true);
			?>

			<?php do_action('woocommerce_cart_coupon'); ?>

		</div>

		<div class="submit-buttons centered-box">
			<?php
				thegem_button(array(
					'tag' => 'button',
					'text' => __( 'Update Cart', 'woocommerce' ),
					'size' => 'medium',
					'extra_class' => 'update-cart',
					'attributes' => array(
						'name' => 'update_cart',
						'value' => __( 'Update Cart', 'woocommerce' ),
						'type' => 'submit',
					)
				), true);
			?>
			<?php
				thegem_button(array(
					'tag' => 'button',
					'text' => __( 'Checkout', 'woocommerce' ),
					'size' => 'medium',
					'extra_class' => 'checkout-button-button',
					'attributes' => array(
						'name' => 'proceed',
						'value' => __( 'Proceed to Checkout', 'woocommerce' ),
						'type' => 'submit',
					)
				), true);
			?>
		</div>
		<?php wp_nonce_field( 'woocommerce-cart' ); ?>

	<?php } ?>
</div>

<?php do_action( 'woocommerce_after_cart_table' ); ?>

</form>

<div class="cart-collaterals">

	<div class="row">
		<div class="col-md-6 col-sm-12">
			<?php woocommerce_shipping_calculator(); ?>
		</div>
		<div class="col-md-6 col-sm-12">
			<?php woocommerce_cart_totals(); ?>
		</div>
	</div>

</div>

<?php woocommerce_cross_sell_display(6, 6); ?>

<?php do_action( 'woocommerce_after_cart' ); ?>
