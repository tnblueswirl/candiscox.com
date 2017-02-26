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

if ( ! class_exists( 'WpssoProMediaGravatar' ) ) {

	class WpssoProMediaGravatar {

		private $p;
		private static $gravatar_urls = array();

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
			$this->add_actions();
		}

		protected function add_actions() {
			$this->p->util->add_plugin_filters( $this, array( 
				'get_user_options' => 2,
				'user_image_urls' => 3,
			), 1000 );	// hook after everything else
		}

		/*
		 * Remove the gravatar image url from the user meta options in favor
		 * of adding it back with the filter_user_image_urls() filter.
		 */
		public function filter_get_user_options( $opts = array(), $user_id = 0 ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$url_part = '.gravatar.com/avatar/';
			if ( isset( $opts['og_img_url'] ) && strpos( $opts['og_img_url'], $url_part ) !== false ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'removing gravatar image url from og_img_url option' );
				$opts['og_img_url'] = '';
			}

			return $opts;
		}

		public function filter_user_image_urls( $urls, $size_name, $user_id ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			// optimize by using a static property cache
			if ( isset( self::$gravatar_urls[$size_name][$user_id] ) ) {
				$urls[] = self::$gravatar_urls[$size_name][$user_id];
				return $urls;
			}

			$user_email = get_the_author_meta( 'user_email', $user_id );
			if ( empty( $user_email ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: empty user email' );
				return $urls;
			}

			$size_info = SucomUtil::get_size_info( $size_name );
			$img_size = $size_info['width'] > 2048 ? 2048 : $size_info['width'];
			$ret_url = ( SucomUtil::is_https() ? 'https://secure' : 'http://www' ).
				'.gravatar.com/avatar/'.md5( strtolower( trim( $user_email ) ) ).'?s='.$img_size;

			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'fetching default image url for gravatar fallback' );
			$def_img = WpssoOpenGraph::get_first_media_info( 'og:image',
				$this->p->media->get_default_image( 1, $size_name ) );

			// if we have a default image, use it as the fallback image
			if ( ! empty( $def_img ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'fallback default image: '.$def_img );
				$ret_url .= '&d='.urlencode( $def_img );
			} else {
				$head = wp_remote_head( $ret_url.'&d=404' );
				if ( is_wp_error( $head ) ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'gravatar check error: '.$head->get_error_message() );
					$ret_url = '';
				} elseif ( isset( $head['response']['code'] ) && $head['response']['code'] === 404 ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'gravatar check returned 404 response code' );
					$ret_url = '';
				} else $ret_url .= '&d=mm';
			}

			self::$gravatar_urls[$size_name][$user_id] = $ret_url;

			if ( ! empty( $ret_url ) )
				$urls[] = $ret_url;

			return $urls;
		}
	}
}

?>
