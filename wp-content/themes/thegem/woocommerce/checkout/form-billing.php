<?php
/**
 * Checkout billing information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @global WC_Checkout $checkout */
?>
<div class="woocommerce-billing-fields">
	<?php if ( WC()->cart->ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h2><span class="light"><?php _e( 'Billing &amp; Shipping', 'woocommerce' ); ?></span></h2>

	<?php else : ?>

		<h2><span class="light"><?php _e( 'Billing Details', 'woocommerce' ); ?></span></h2>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<?php foreach ( $checkout->checkout_fields['billing'] as $key => $field ) : ?>

		<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>

	<?php do_action('woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

<?php if ( ! is_user_logged_in() && $checkout->enable_signup ) : ?>
	<div class="create-account-popup">
		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<h2><span class="light"><?php _e( 'Register', 'thegem' ); ?></span></h2>

		<div class="create-account-inner clearfix">
			<p class="create-account-notice">
				<?php _e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'thegem' ); ?>
			</p>
			<?php foreach ($checkout->checkout_fields['account'] as $key => $field) : ?>
				<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
			<?php endforeach; ?>

			<div class="clear"></div>
			<div class="create-account-popup-bottom clearfix">
				<?php if ( $checkout->enable_guest_checkout ) : ?>
					<p class="form-row form-row-wide create-account-checkbox">
						<input class="input-checkbox gem-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true) ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e( 'Create an account?', 'woocommerce' ); ?></label>
					</p>
				<?php endif; ?>

				<?php
					thegem_button(array(
						'tag' => 'button',
						'text' => esc_html__( 'Register', 'thegem' ),
						'style' => 'outline',
						'size' => 'medium',
						'extra_class' => 'checkout-create-account-button',
						'attributes' => array(
							'type' => 'button',
						)
					), true);
				?>
			</div>
		</div>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?>
