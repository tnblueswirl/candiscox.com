<?php
/*
 * IMPORTANT: READ THE LICENSE AGREEMENT CAREFULLY.
 *
 * BY INSTALLING, COPYING, RUNNING, OR OTHERWISE USING THE 
 * WORDPRESS SOCIAL SHARING OPTIMIZATION (WPSSO) PRO APPLICATION, YOU AGREE 
 * TO BE BOUND BY THE TERMS OF ITS LICENSE AGREEMENT.
 * 
 * License: Nontransferable License for a WordPress Site Address URL
 * License URI: https://surniaulula.com/wp-content/plugins/wpsso/license/pro.txt
 *
 * IF YOU DO NOT AGREE TO THE TERMS OF ITS LICENSE AGREEMENT,
 * PLEASE DO NOT INSTALL, RUN, COPY, OR OTHERWISE USE THE
 * WORDPRESS SOCIAL SHARING OPTIMIZATION (WPSSO) PRO APPLICATION.
 * 
 * Copyright 2012-2017 Jean-Sebastien Morisset (https://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoProEcomWoocommerce' ) ) {

	class WpssoProEcomWoocommerce {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( SucomUtil::get_const( 'WPSSO_CHECK_PRODUCT_OBJECT' ) === false ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'WPSSO_CHECK_PRODUCT_OBJECT is false' );
			} else add_action( 'wp', array( &$this, 'wp_check_product_object' ) );

			// load the missing woocommerce front-end libraries
			if ( is_admin() ) {
				$this->p->util->add_plugin_actions( $this, array( 
					'admin_post_head' => 1, 
				) );
			}

			$this->p->util->add_plugin_filters( $this, array( 
				'og_prefix_ns' => 1,
				'head_use_post' => 1,
				'schema_type_id' => 3,
				'force_default_img' => 1,
				'tags' => 2,
				'description_seed' => 2,
				'attached_image_ids' => 2,
				'term_image_ids' => 3,
				'og_seed' => 3, 
			) );
		}

		// make sure the global $product variable is an object, not a string / slug
		public function wp_check_product_object() {
			global $product, $post;
			if ( ! empty( $product ) && is_string( $product ) && is_product() && ! empty( $post->ID ) )
				$product = $this->get_product( $post->ID );
		}

		public function action_admin_post_head( $mod ) {
			if ( ! empty( $this->p->options['plugin_filter_content'] ) ) {
				global $woocommerce;
				$wc_plugindir = trailingslashit( realpath( dirname( WC_PLUGIN_FILE ) ) );
				foreach ( array(
					'includes/class-wc-shortcodes.php',
					'includes/wc-notice-functions.php',
					'includes/wc-template-functions.php',
					'includes/abstracts/abstract-wc-session.php',
					'includes/class-wc-session-handler.php',
				) as $wc_inc_file )
					if ( file_exists( $wc_plugindir.$wc_inc_file ) )
						include_once( $wc_plugindir.$wc_inc_file );
					elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'include file missing: '.$wc_plugindir.$wc_inc_file );

				$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
				if ( class_exists( $session_class ) )
					$woocommerce->session  = new $session_class();
			}
		}

		public function filter_og_prefix_ns( $ns ) {
			$ns['product'] = 'http://ogp.me/ns/product#';
			return $ns;
		}

		public function filter_head_use_post( $use_post ) {
			if ( is_shop() ) {
				$use_post = (int) wc_get_page_id( 'shop' );
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'woocommerce shop page id: '.$use_post );
			}
			return $use_post;
		}

		public function filter_schema_type_id( $type_id, $mod, $is_md_type ) {
			if ( ! $is_md_type ) {	// skip if we have a custom type from the post meta
				if ( is_shop() ) {
					$use_post = (int) wc_get_page_id( 'shop' );
					if ( $mod['id'] === $use_post ) {	// return for the collection page, not its parts
						$type_id = $this->p->schema->get_schema_type_id_for_name( 'archive_page' );
						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'woocommerce shop type id: '.$type_id );
					}
				}
			}
			return $type_id;
		}

		// don't force default images on woocommerce product category and tag pages
		public function filter_force_default_img( $ret ) {
			if ( $ret ) {
				if ( SucomUtil::is_term_page() ) {
					if ( SucomUtil::is_product_category() || 
						SucomUtil::is_product_tag() )
							return false;
				}
			}
			return $ret;
		}

		public function filter_tags( $tags, $post_id ) {
			$terms = get_the_terms( $post_id, 'product_tag' );
			if ( is_array( $terms ) )
				foreach( $terms as $term )
					$tags[] = $term->name;
			return $tags;
		}

		public function filter_description_seed( $desc, $mod ) {
			if ( $mod['is_term'] ) {
				if ( SucomUtil::is_product_category() || SucomUtil::is_product_tag() ) {
					$term_desc = $this->p->util->cleanup_html_tags( term_description() );
					if ( ! empty( $term_desc ) )
						$desc = $term_desc;
				}
			} elseif ( is_page() ) {
				if ( is_cart() )
					$desc = 'Shopping Cart';
				elseif ( is_checkout() )
					$desc = 'Checkout Page';
				elseif ( is_account_page() )
					$desc = 'Account Page';
			}
			return $desc;
		}

		// images can only be attached to a post ID
		public function filter_attached_image_ids( $ids, $post_id ) {
			if ( ! SucomUtil::is_product_page( $post_id ) )
				return $ids;

			if ( ( $product = $this->get_product( $post_id ) ) === false )
				return $ids;	// abort

			if ( method_exists( $product, 'get_gallery_attachment_ids' ) ) {	// WooCommerce v2.x
				$attach_ids = $product->get_gallery_attachment_ids();
				if ( is_array( $attach_ids ) )
					$ids = array_merge( $attach_ids, $ids );
			}

			return $ids;
		}

		public function filter_term_image_ids( $ids, $size_name, $term_id ) {
			if ( SucomUtil::is_product_category() || SucomUtil::is_product_tag() ) {
				$pid = get_woocommerce_term_meta( $term_id, 'thumbnail_id', true );
				if ( ! empty( $pid ) )
					$ids[] = $pid;
			}
			return $ids;
		}

		public function filter_og_seed( $og, $use_post, $mod ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$lca = $this->p->cf['lca'];
			$size_name = $lca.'-opengraph';
			$og_ecom = array();

			if ( $mod['is_post'] ) {
				// support checks for both front-end and back-end
				if ( is_product() || $mod['post_type'] === 'product' ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'object ID '.$mod['id'].' is a product' );

					if ( ( $product = $this->get_product( $mod['id'] ) ) === false )
						return $og;	// abort

					$og_ecom['og:type'] = 'product';

					$this->add_product_mt( $og_ecom, $product );

					/*
					 * True if WooCommerce product reviews are enabled and
					 * yotpo-social-reviews-for-woocommerce is not active.
					 */
					if ( apply_filters( $lca.'_og_add_product_mt_rating', 
						( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && 
							empty( $this->p->is_avail['ecom']['yotpowc'] ) ? true : false ), $mod ) ) {

						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'add product rating meta tags is true' );

						$og_ecom['product:rating:average'] = (float) $product->get_average_rating();
						$og_ecom['product:rating:count'] = (int) $product->get_rating_count();
						$og_ecom['product:rating:worst'] = 1;
						$og_ecom['product:rating:best'] = 5;
						$og_ecom['product:review:count'] = (int) $product->get_review_count();

					} elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'add product rating meta tags is false' );

					/*
					 * True if WooCommerce product reviews are enabled and 
					 * yotpo-social-reviews-for-woocommerce is not active.
					 *
					 * [product:reviews] => Array (
					 *	[0] => Array (
					 *		[product:review:author:name] => jsmoriss
					 *		[product:review:author:id] => 1
					 *		[product:review:excerpt] => 3 star review.
					 *		[product:review:created_time] => 2015-11-16T18:56:40+00:00
					 *		[product:review:id] => 161
					 *		[product:review:url] => http://adm.surniaulula.com/product/a-variable-product-test/#comment-161
					 *		[product:review:rating:value] => 3
					 *		[product:review:rating:worst] => 1
					 *		[product:review:rating:best] => 5
					 *	)
					 * ) 
					 */
					if ( apply_filters( $lca.'_og_add_product_mt_reviews',
						( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && 
							empty( $this->p->is_avail['ecom']['yotpowc'] ) ? true : false ), $mod ) ) {

						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'add product review meta tags is true' );

						$comments = get_comments( array(
							'post_id' => $mod['id'],
							'status' => 'approve',
							'parent' => 0,	// don't get replies
							'order' => 'DESC',
							'number' => get_option( 'page_comments' ),	// limit number of comments
						) );
						if ( is_array( $comments ) ) {
							foreach( $comments as $num => $cmt ) {
								$og_review = array();	// start with an empty array
								$this->add_review_mt( $og_review, $cmt, 'product:review' );	// $mt_pre = 'product:review'
								if ( ! empty( $og_review ) )
									$og_ecom['product:reviews'][] = $og_review;
							}
						}
					} elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'add product review meta tags is false' );

					/*
					 * False by default, but returns true if the WPSSO JSON extension is active.
					 */
					if ( apply_filters( $lca.'_og_add_product_mt_offers', false, $mod ) &&
						isset( $product->product_type ) && $product->product_type === 'variable' ) {

						if ( $this->p->debug->enabled )
							$this->p->debug->log( 'add product offers meta tags is true' );

						$variations = $product->get_available_variations();
						if ( is_array( $variations ) ) {
							foreach( $variations as $num => $var ) {
								$og_offer = array();	// start with an empty array
								$this->add_product_mt( $og_offer, $var, 'product:offer' );	// $mt_pre = 'product:offer'
								if ( ! empty( $og_offer ) )
									$og_ecom['product:offers'][] = $og_offer;
							}
						}
					} elseif ( $this->p->debug->enabled )
						$this->p->debug->log( 'add product offers meta tags is false' );
	
					// hooked by the yotpo module to provide product ratings
					$og_ecom = apply_filters( $lca.'_og_woocommerce_product_page', $og_ecom, $mod );

				} else {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'exiting early: object ID '.$mod['id'].' is not a product' );
					return $og;	// abort
				}
			} elseif ( $mod['is_term'] ) {
				if ( SucomUtil::is_product_category() || SucomUtil::is_product_tag() ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'setting og:type for product '.
							( SucomUtil::is_product_category() ? 'category' : 'tag' ) );
					$og_ecom['og:type'] = 'product';
				} else {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'exiting early: term is not product category or tag' );
					return $og;	// abort
				}
			} else return $og;	// abort

			$og_ecom = apply_filters( $lca.'_og_woocommerce', $og_ecom, $mod );

			return array_merge( $og, $og_ecom );
		}

		private function get_product( $id ) {
			global $woocommerce;
			if ( ! empty( $woocommerce->product_factory ) && 
				method_exists( $woocommerce->product_factory, 'get_product' ) ) {		// WooCommerce v2.x
				return $woocommerce->product_factory->get_product( $id );

			} elseif ( class_exists( 'WC_Product' ) ) {						// WooCommerce v1.x
				return new WC_Product( $id );

			} else {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: failed to get product object' );
				return false;
			}
		}

		private function add_review_mt( array &$og, $cmt, $mt_pre = 'product:review' ) {
			$og[$mt_pre.':author:name'] = $cmt->comment_author;	// author's (display) name
			$og[$mt_pre.':author:id'] = $cmt->user_id;	// author's ID if registered (0 otherwise)
			$og[$mt_pre.':excerpt'] = get_comment_excerpt( $cmt->comment_ID );
			//$og[$mt_pre.':content'] = $cmt->comment_content;
			$og[$mt_pre.':created_time'] = mysql2date( 'c', $cmt->comment_date_gmt );
			$og[$mt_pre.':id'] = $cmt->comment_ID;
			$og[$mt_pre.':url'] = get_comment_link( $cmt->comment_ID );
			$og[$mt_pre.':rating:value'] = (float) get_comment_meta( $cmt->comment_ID, 'rating', true );	// $single = true
			$og[$mt_pre.':rating:worst'] = 1;
			$og[$mt_pre.':rating:best'] = 5;
		}

		private function add_product_mt( array &$og, $mixed, $mt_pre = 'product' ) {

			$dim_unit = 'cm';	// 'in', 'm', 'cm', or 'mm' 
			$weight_unit = 'kg';	// 'lbs', 'g', or 'kg' 
			$is_variation = false;

			if ( is_array( $mixed ) ) {
				$var =& $mixed;
				// check for incomplete variations
				if ( empty( $var['variation_is_visible'] ) ||
					empty( $var['variation_is_active'] ) ||
					empty( $var['is_purchasable'] ) ||
					empty( $var['variation_id'] ) )
						return false;	// abort
				$is_variation = true;
				if ( ( $product = $this->get_product( $var['variation_id'] ) ) === false )
					return false;	// abort
			} elseif ( is_object( $mixed ) ) {
				$product =& $mixed;
			} elseif ( ( $product = $this->get_product( $mixed ) ) === false ) {
				return false;	// abort
			}

			$id = method_exists( $product, 'get_id' ) ?	// since wc 2.5
				$product->get_id() : $product->id;

			$og[$mt_pre.':id'] = $id;
			$og[$mt_pre.':sku'] = $product->get_sku();

			if ( $is_variation ) {	// additional information for offers / variations
				$og[$mt_pre.':url'] = $product->get_permalink();
				$og[$mt_pre.':title'] = $product->get_title();
				$og[$mt_pre.':image:id'] = $product->get_image_id();
			}

			$og[$mt_pre.':price:amount'] = $product->get_price();
			$og[$mt_pre.':price:currency'] = get_woocommerce_currency();

			/*
			 * Possible values (see https://schema.org/ItemAvailability):
			 *	Discontinued
			 *	InStock
			 *	InStoreOnly
			 *	LimitedAvailability
			 *	OnlineOnly
			 *	OutOfStock
			 *	PreOrder
			 *	SoldOut 
			 */
			if ( $product->is_in_stock() )
				$og[$mt_pre.':availability'] = 'InStock';
			else $og[$mt_pre.':availability'] = 'OutOfStock';

			if ( $product->has_dimensions() ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'getting product dimensions' );

				$og[$mt_pre.':dimensions'] = $product->get_dimensions();

				if ( function_exists( 'wc_get_dimension' ) ) {
					if ( is_callable( array( $product, 'get_width' ) ) )	// just in case
						$og[$mt_pre.':width'] = (float) wc_get_dimension( $product->get_width(), $dim_unit );

					if ( is_callable( array( $product, 'get_height' ) ) )	// just in case
						$og[$mt_pre.':height'] = (float) wc_get_dimension( $product->get_height(), $dim_unit );

					if ( is_callable( array( $product, 'get_length' ) ) )	// just in case
						$og[$mt_pre.':length'] = (float) wc_get_dimension( $product->get_length(), $dim_unit );
				}
			}

			if ( $product->has_weight() ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'getting product weight' );

				if ( function_exists( 'wc_get_weight' ) ) {
					if ( is_callable( array( $product, 'get_weight' ) ) )	// just in case
						$og[$mt_pre.':weight'] = (float) wc_get_weight( $product->get_weight(), $weight_unit );
				}
			}

			if ( ! $is_variation ) {
				$og['product:color'] = $product->get_attribute( 'color' );
				$og['product:size'] = $product->get_attribute( 'size' );
			} else {
				$og['product:offer:color'] = empty( $var['attributes']['attribute_color'] ) ?
					'' : $var['attributes']['attribute_color'];
				$og['product:offer:size'] = empty( $var['attributes']['attribute_size'] ) ?
					'' : $var['attributes']['attribute_size'];
				$og['product:offer:description'] = empty( $var['variation_description'] ) ?
					'' : $this->p->util->cleanup_html_tags( $var['variation_description'] );
			}

			$terms = get_the_terms( $id, 'product_cat' );
			if ( is_array( $terms ) ) {
				$cats = array();
				foreach( $terms as $term )
					$cats[] = $term->name;
				if ( ! empty( $cats ) )
					$og[$mt_pre.':category'] = implode( ' > ', $cats );
			}

			$terms = get_the_terms( $id, 'product_tag' );
			if ( is_array( $terms ) ) {
				foreach( $terms as $term )
					$og[$mt_pre.':tag'][] = $term->name;
			}
		}
	}
}

?>
