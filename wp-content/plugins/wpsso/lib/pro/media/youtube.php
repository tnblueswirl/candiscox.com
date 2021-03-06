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

if ( ! class_exists( 'WpssoProMediaYoutube' ) ) {

	class WpssoProMediaYoutube {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'video_info' => 4,
			), 10 );
		}

		public function filter_video_info( $og_video, $embed_url, $embed_width = 0, $embed_height = 0 ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( ! empty( $og_video['og:video:secure_url'] ) || ! empty( $og_video['og:video:url'] ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: returned video information found' );
				return $og_video;
			} elseif ( empty( $embed_url ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: embed url value is empty' );
				return $og_video;
			}

			/*
			 * YouTube video API
			 */
			if ( preg_match( '/^.*(youtube\.com|youtube-nocookie\.com|youtu\.be)\/(watch\/?\?v=)?([^\?\&\#<>]+)'.
				'(\?(list)=([^\?\&\#<>]+)|.*)$/', $embed_url, $match ) ) {

				if ( $this->p->debug->enabled )
					$this->p->debug->log_arr( 'match', $match );

				$list_name = false;
				$vid_name = isset( $match[3] ) ?
					preg_replace( '/^.*\//', '', $match[3] ) : false;
				$img_name = 'maxresdefault.jpg?m=default';

				if ( empty( $vid_name ) ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'exiting early: video name is empty' );
					return $og_video;
				} elseif ( $vid_name === 'videoseries' ) {
					if ( $this->p->debug->enabled )
						$this->p->debug->log( 'exiting early: video name is videoseries' );
					return $og_video;
				}

				/*
				 * Define all default meta tag values.
				 */
				if ( ! empty( $match[6] ) && $match[5] === 'list' ) {
					$list_name = $match[6];

					// add both https and http url meta tags
					if ( ! empty( $this->p->options['add_meta_property_og:video:secure_url'] ) ) {
						$og_video['og:video:secure_url'] = 'https://www.youtube.com/v/'.$vid_name.'?list='.$list_name;
						$og_video['og:video:url'] = 'http://www.youtube.com/v/'.$vid_name.'?list='.$list_name;
					// use the https url by default
					} else $og_video['og:video:url'] = 'https://www.youtube.com/v/'.$vid_name.'?list='.$list_name;

					$og_video['og:video:thumbnail_url'] = 'https://i.ytimg.com/vi/'.$vid_name.'/'.$img_name;
					$og_video['og:video:embed_url'] = 'https://www.youtube.com/embed/'.$vid_name.'?list='.$list_name;
					$og_video['og:video:has_image'] = true;

					// add both https and http url meta tags
					if ( ! empty( $this->p->options['add_meta_property_og:image:secure_url'] ) ) {
						$og_video['og:image:secure_url'] = 'https://i.ytimg.com/vi/'.$vid_name.'/'.$img_name;
						$og_video['og:image'] = 'http://i.ytimg.com/vi/'.$vid_name.'/'.$img_name;
					// use the https url by default
					} else $og_video['og:image'] = 'https://i.ytimg.com/vi/'.$vid_name.'/'.$img_name;

				} else {

					// add both https and http url meta tags
					if ( ! empty( $this->p->options['add_meta_property_og:video:secure_url'] ) ) {
						$og_video['og:video:secure_url'] = 'https://www.youtube.com/v/'.$vid_name.'?version=3&autohide=1';
						$og_video['og:video:url'] = 'http://www.youtube.com/v/'.$vid_name.'?version=3&autohide=1';
					// use the https url by default
					} else $og_video['og:video:url'] = 'https://www.youtube.com/v/'.$vid_name.'?version=3&autohide=1';

					$og_video['og:video:thumbnail_url'] = 'https://i.ytimg.com/vi/'.$vid_name.'/'.$img_name;
					$og_video['og:video:embed_url'] = 'https://www.youtube.com/embed/'.$vid_name;
					$og_video['og:video:has_image'] = true;

					// add both https and http url meta tags
					if ( ! empty( $this->p->options['add_meta_property_og:image:secure_url'] ) ) {
						$og_video['og:image:secure_url'] = 'https://i.ytimg.com/vi/'.$vid_name.'/'.$img_name;
						$og_video['og:image'] = 'http://i.ytimg.com/vi/'.$vid_name.'/'.$img_name;
					// use the https url by default
					} else $og_video['og:image'] = 'https://i.ytimg.com/vi/'.$vid_name.'/'.$img_name;
				}

				/*
				 * Get additional meta tags from the YouTube video webpage.
				 */
				$url = ( empty( $this->p->options['og_vid_https'] ) ? 'http:' : 'https:' ).
					'//www.youtube.com/watch?v='.$vid_name;

				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'fetching missing video width / height from '.$url );

				$metas = $this->p->util->get_head_meta( $url, '//meta' );

				if ( isset( $metas['meta'] ) ) {
					foreach ( $metas as $m ) {		// loop through all meta tags
						foreach ( $m as $a ) {		// loop through all attributes for that meta tag
							$meta_type = key( $a );
							$meta_name = reset( $a );
							switch ( $meta_type.'-'.$meta_name ) {
								case 'property-og:image:secure_url':
									if ( ! empty( $a['content'] ) ) {

										// add the meta name as a query string to know where the value came from
										$a['content'] = add_query_arg( 'm', $meta_name, $a['content'] );

										if ( ! empty( $this->p->options['add_meta_property_og:image:secure_url'] ) )
											$og_video['og:image:secure_url'] = $a['content'];
										else $og_video['og:image'] = $a['content'];

										$og_video['og:video:thumbnail_url'] = $a['content'];
									}
									break;
								case 'property-og:image:url':
								case 'property-og:image':
									if ( ! empty( $a['content'] ) ) {

										// add the meta name as a query string to know where the value came from
										$a['content'] = add_query_arg( 'm', $meta_name, $a['content'] );

										if ( strpos( $a['content'], 'https:' ) === 0 &&
											! empty( $this->p->options['add_meta_property_og:image:secure_url'] ) )
												$og_video['og:image:secure_url'] = $a['content'];

										$og_video['og:image'] = $a['content'];
										$og_video['og:video:thumbnail_url'] = $a['content'];
									}
									break;
								case 'property-og:video:width':
								case 'property-og:video:height':
									if ( ! empty( $a['content'] ) )
										$og_video[$a['property']] = $a['content'];
									break;
								case 'property-og:video:tag':
									if ( ! empty( $a['content'] ) )
										$og_video[$a['property']][] = $a['content'];	// array of tags
									break;
								case 'property-og:title':
								case 'property-og:description':
									// add additional, non-standard properties
									// like og:video:title and og:video:description
									if ( ! empty( $a['content'] ) ) {
										$og_key = 'og:video:'.substr( $a['property'], 3 );
										$og_video[$og_key] = $a['content'];
										if ( $this->p->debug->enabled )
											$this->p->debug->log( 'adding '.$og_key.' = '.
												$og_video[$og_key] );
									}
									break;
								case 'itemprop-datePublished':
									if ( ! empty( $a['content'] ) )
										$og_video['og:video:upload_date'] = date( 'c', strtotime( $a['content'] ) );
									break;
								case 'itemprop-embedURL':
									if ( ! empty( $a['content'] ) )
										$og_video['og:video:embed_url'] = $a['content'];
									break;
							}
						}
					}
				} elseif ( $this->p->debug->enabled )
					$this->p->debug->log( 'no head meta found in '.$url );

				if ( $this->p->debug->enabled )
					$this->p->debug->log( $og_video );
		
			} elseif ( $this->p->debug->enabled )
				$this->p->debug->log( 'embed url does not match a known YouTube video URL' );

			return $og_video;
		}
	}
}

?>
