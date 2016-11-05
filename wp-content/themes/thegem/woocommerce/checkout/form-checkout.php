<?php
/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

wp_enqueue_script('thegem-checkout');
wp_enqueue_script('thegem-woocommerce');

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() ); ?>

<div class="checkout-steps <?php if(is_user_logged_in()): ?>user-logged<?php endif; ?> clearfix">
	<?php if(is_user_logged_in()): ?>
		<div class="checkout-step active" data-tab-id="checkout-billing">1. Billing</div>
		<div class="checkout-step" data-tab-id="checkout-payment">2. Payment</div>
		<div class="checkout-step disabled" data-tab-id="checkout-confirmation">3. Confirmation</div>
	<?php else: ?>
		<div class="checkout-step active" data-tab-id="checkout-signin">1. Sign in</div>
		<div class="checkout-step" data-tab-id="checkout-billing">2. Billing</div>
		<div class="checkout-step" data-tab-id="checkout-payment">3. Payment</div>
		<div class="checkout-step disabled" data-tab-id="checkout-confirmation">4. Confirmation</div>
	<?php endif; ?>
</div>

<?php if(!is_user_logged_in()): ?>
	<div class="checkout-contents clearfix" data-tab-content-id="checkout-signin">
		<div class="row" id="customer_details">
			<div class="col-sm-6 col-xs-12 checkout-login">
				<?php
					do_action( 'woocommerce_before_checkout_form', $checkout );
				?>
			</div>
			<?php if ($checkout->enable_guest_checkout || $checkout->enable_signup): ?>
				<div class="col-sm-6 col-xs-12 checkout-signin">
					<h2><span class="light">New customer</span></h2>
					<?php
						if ($checkout->enable_guest_checkout) {
							thegem_button(array(
								'tag' => 'button',
								'text' => __( 'checkout as guest', 'woocommerce' ),
								'style' => 'flat',
								'extra_class' => 'checkout-as-guest',
								'attributes' => array(
									'type' => 'button',
								)
							), true);
						}
					?>
					<?php
						if ($checkout->enable_signup) {
							thegem_button(array(
								'tag' => 'button',
								'text' => __( 'create an account', 'woocommerce' ),
								'style' => 'flat',
								'extra_class' => 'checkout-create-account',
								'attributes' => array(
									'type' => 'button',
								)
							), true);
						}
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<form name="checkout" method="post" novalidate class="checkout woocommerce-checkout" action="<?php echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data">

	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>
		<div class="checkout-contents clearfix" data-tab-content-id="checkout-billing">
			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

			<div class="row" id="customer_details">
				<div class="col-sm-6 col-xs-12">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				</div>
				<div class="col-sm-6 col-xs-12">
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>
			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<div class="checkout-navigation-buttons clearfix">
				<?php
					thegem_button(array(
						'tag' => 'button',
						'text' => __( 'Previous step', 'woocommerce' ),
						'style' => 'outline',
						'size' => 'medium',
						'extra_class' => 'checkout-prev-step',
						'attributes' => array(
							'value' => __( 'Previous step', 'woocommerce' ),
							'type' => 'button',
						)
					), true);
				?>
				<?php
					thegem_button(array(
						'tag' => 'button',
						'text' => __( 'Next step', 'woocommerce' ),
						'style' => 'outline',
						'size' => 'medium',
						'extra_class' => 'checkout-next-step',
						'attributes' => array(
							'value' => __( 'Next step', 'woocommerce' ),
							'type' => 'button',
						)
					), true);
				?>
			</div>
		</div>
	<?php endif; ?>

	<div class="checkout-contents clearfix" data-tab-content-id="checkout-payment">
		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

		<h2 id="order_review_heading"><span class="light"><?php _e( 'Your order', 'woocommerce' ); ?></span></h2>

		<div class="gem-table checkout-payment">
			<div id="order_review" class="woocommerce-checkout-review-order">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
	</div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

<script>
(function($) {
	function active_checkout_tab($tab, isinit) {
		if ($tab.length == 0 || ($tab.hasClass('active') && !isinit)) {
			return false;
		}

		$tab.parent().find('.checkout-step').removeClass('active before-active');
		$tab.addClass('active');
		$tab.prev('.checkout-step').addClass('before-active');
		var tab_id = $tab.data('tab-id');
		$('.checkout-contents').removeClass('active');
		$('.checkout-contents[data-tab-content-id="' + tab_id + '"]').addClass('active');
		window.location.hash = '#' + tab_id;
	}

	var m = window.location.hash.match(/#checkout\-(.+)/);
	if (m && $('.checkout-steps .checkout-step[data-tab-id="checkout-' + m[1] + '"]').length == 1) {
		active_checkout_tab($('.checkout-steps .checkout-step[data-tab-id="checkout-' + m[1] + '"]'), true);
	} else {
		active_checkout_tab($('.checkout-steps .checkout-step:first'), true);
	}

	$('.checkout-steps .checkout-step').not('.disabled').click(function() {
		active_checkout_tab($(this), false);
	});
})(jQuery);
</script>
