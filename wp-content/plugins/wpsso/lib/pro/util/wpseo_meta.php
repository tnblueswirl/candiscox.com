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

if ( ! class_exists( 'WpssoProUtilWpseoMeta' ) ) {

	class WpssoProUtilWpseoMeta {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( ! empty( $this->p->options['plugin_wpseo_social_meta'] ) ) {	// just in case
				$this->p->util->add_plugin_filters( $this, array( 
					'get_post_options' => 2,
					'get_term_options' => 2,
					'get_user_options' => 2,
				) );
			}
		}

		public function filter_get_post_options( $opts, $post_id ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( empty( $opts['og_title'] ) )
				$opts['og_title'] = (string) get_post_meta( $post_id,
					'_yoast_wpseo_opengraph-title', true );

			if ( empty( $opts['og_title'] ) )	// fallback to the SEO title
				$opts['og_title'] = (string) get_post_meta( $post_id,
					'_yoast_wpseo_title', true );

			if ( empty( $opts['og_desc'] ) )
				$opts['og_desc'] = (string) get_post_meta( $post_id,
					'_yoast_wpseo_opengraph-description', true );

			if ( empty( $opts['og_desc'] ) )	// fallback to the SEO description
				$opts['og_desc'] = (string) get_post_meta( $post_id,
					'_yoast_wpseo_metadesc', true );

			if ( empty( $opts['og_img_id'] ) && empty( $opts['og_img_url'] ) )
				$opts['og_img_url'] = (string) get_post_meta( $post_id,
					'_yoast_wpseo_opengraph-image', true );

			if ( empty( $opts['tc_desc'] ) )
				$opts['tc_desc'] = (string) get_post_meta( $post_id,
					'_yoast_wpseo_twitter-description', true );

			if ( empty( $opts['schema_desc'] ) )
				$opts['schema_desc'] = (string) get_post_meta( $post_id,
					'_yoast_wpseo_metadesc', true );

			$opts['seo_desc'] = (string) get_post_meta( $post_id,
				'_yoast_wpseo_metadesc', true );

			return $opts;
		}

		/*
		 * Yoast SEO does not support wordpress term meta (added in wp 4.4).
		 * Read term meta from the 'wpseo_taxonomy_meta' option instead.
		 */
		public function filter_get_term_options( $opts, $term_id ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$term_obj = get_term( $term_id );
			$tax_opts = get_option( 'wpseo_taxonomy_meta' );

			if ( ! isset( $term_obj->taxonomy ) || 
				! isset( $tax_opts[$term_obj->taxonomy][$term_id] ) )
					return $opts;

			$term_opts = $tax_opts[$term_obj->taxonomy][$term_id];

			if ( empty( $opts['og_title'] ) && 
				isset( $term_opts['wpseo_opengraph-title'] ) )
					$opts['og_title'] = (string) $term_opts['wpseo_opengraph-title'];

			if ( empty( $opts['og_title'] ) &&	// fallback to the SEO title
				isset( $term_opts['wpseo_title'] ) )
					$opts['og_title'] = (string) $term_opts['wpseo_title'];

			if ( empty( $opts['og_desc'] ) && 
				isset( $term_opts['wpseo_opengraph-description'] ) )
					$opts['og_desc'] = (string) $term_opts['wpseo_opengraph-description'];

			if ( empty( $opts['og_desc'] ) &&	// fallback to the SEO description
				isset( $term_opts['wpseo_desc'] ) )
					$opts['og_desc'] = (string) $term_opts['wpseo_desc'];

			if ( empty( $opts['og_img_id'] ) && empty( $opts['og_img_url'] ) &&
				isset( $term_opts['wpseo_opengraph-image'] ) )
					$opts['og_img_url'] = (string) $term_opts['wpseo_opengraph-image'];

			if ( empty( $opts['tc_desc'] ) &&
				isset( $term_opts['wpseo_twitter-description'] ) )
					$opts['tc_desc'] = (string) $term_opts['wpseo_twitter-description'];

			if ( empty( $opts['schema_desc'] ) && 
				isset( $term_opts['wpseo_desc'] ) )
					$opts['tc_desc'] = (string) $term_opts['wpseo_desc'];

			if ( isset( $term_opts['wpseo_desc'] ) )
				$opts['seo_desc'] = (string) $term_opts['wpseo_desc'];

			return $opts;
		}

		/*
		 * Yoast SEO does not provide social settings for users.
		 */
		public function filter_get_user_options( $opts, $user_id ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( empty( $opts['og_title'] ) )
				$opts['og_title'] = (string) get_user_meta( $user_id,
					'wpseo_title', true );

			if ( empty( $opts['og_desc'] ) )
				$opts['og_desc'] = (string) get_user_meta( $user_id,
					'wpseo_metadesc', true );

			$opts['seo_desc'] = (string) get_user_meta( $user_id,
				'wpseo_metadesc', true );

			return $opts;
		}
	}
}

?>
