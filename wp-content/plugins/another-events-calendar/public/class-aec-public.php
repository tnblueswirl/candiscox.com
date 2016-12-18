<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link          http://yendif.com
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/public
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC_Public Class
 *
 * @since    1.0.0
 */
class AEC_Public {

	/**
	 * Output buffer.
	 *
	 * @since    1.5.0
	 * @access   public
	 */
	public function output_buffer() {
	
		if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
		
			if( ( isset( $_POST['aec_public_event_nonce'] ) && wp_verify_nonce( $_POST['aec_public_event_nonce'], 'aec_public_save_event' ) ) ||
				( isset( $_POST['aec_public_venue_nonce'] ) && wp_verify_nonce( $_POST['aec_public_venue_nonce'], 'aec_public_save_venue' ) ) ||
				( isset( $_POST['aec_public_organizer_nonce'] ) && wp_verify_nonce( $_POST['aec_public_organizer_nonce'], 'aec_public_save_organizer' ) ) ) {
				ob_start();
			}
			
		}
	
	}
	
	/**
	 * Add rewrite rules.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_rewrites() { 

		$page_settings = get_option( 'aec_page_settings' );
		$url = network_home_url();
		
		// Recurring Events Page
		$id = $page_settings['events'];
		if( $id > 0 ) {
			$link = str_replace( $url, '', get_permalink( $id ) );			
			$link = trim( $link, '/' );		
			
			add_rewrite_rule( "$link/([^/]+)/page/?([0-9]{1,})/?$", 'index.php?page_id='.$id.'&aec_event=$matches[1]&paged=$matches[2]', 'top' );
			add_rewrite_rule( "$link/([^/]+)/?$", 'index.php?page_id='.$id.'&aec_event=$matches[1]', 'top' );
		}
		
		// Single Category Page
		$id = $page_settings['category'];
		if( $id > 0 ) {
			$link = str_replace( $url, '', get_permalink( $id ) );			
			$link = trim( $link, '/' );		
			
			add_rewrite_rule( "$link/([^/]+)/page/?([0-9]{1,})/?$", 'index.php?page_id='.$id.'&aec_category=$matches[1]&paged=$matches[2]', 'top' );
			add_rewrite_rule( "$link/([^/]+)/?$", 'index.php?page_id='.$id.'&aec_category=$matches[1]', 'top' );
		}
		
		// Single Tag Page
		$id = $page_settings['tag'];
		if( $id > 0 ) {
			$link = str_replace( $url, '', get_permalink( $id ) );			
			$link = trim( $link, '/' );		
			
			add_rewrite_rule( "$link/([^/]+)/page/?([0-9]{1,})/?$", 'index.php?page_id='.$id.'&aec_tag=$matches[1]&paged=$matches[2]', 'top' );
			add_rewrite_rule( "$link/([^/]+)/?$", 'index.php?page_id='.$id.'&aec_tag=$matches[1]', 'top' );
		}
		
		// Single Venue Page
		$id = $page_settings['venue'];
		if( $id > 0 ) {
			$link = str_replace( $url, '', get_permalink( $id ) );			
			$link = trim( $link, '/' );		
			
			add_rewrite_rule( "$link/([^/]+)/page/?([0-9]{1,})/?$", 'index.php?page_id='.$id.'&aec_venue=$matches[1]&paged=$matches[2]', 'top' );
			add_rewrite_rule( "$link/([^/]+)/?$", 'index.php?page_id='.$id.'&aec_venue=$matches[1]', 'top' );
		}
		
		// Single Organizer Page
		$id = $page_settings['organizer'];
		if( $id > 0 ) {
			$link = str_replace( $url, '', get_permalink( $id ) );			
			$link = trim( $link, '/' );		
			
			add_rewrite_rule( "$link/([^/]+)/page/?([0-9]{1,})/?$", 'index.php?page_id='.$id.'&aec_organizer=$matches[1]&paged=$matches[2]', 'top' );
			add_rewrite_rule( "$link/([^/]+)/?$", 'index.php?page_id='.$id.'&aec_organizer=$matches[1]', 'top' );
		}
		
		// Event Form [ Edit Page ]
		$id = $page_settings['event_form'];
		if( $id > 0 ) {
			$link = str_replace( $url, '', get_permalink( $id ) );			
			$link = trim( $link, '/' );		
			
			add_rewrite_rule( "$link/([^/]+)/([0-9]{1,})/?$", 'index.php?page_id='.$id.'&aec_action=$matches[1]&aec_id=$matches[2]', 'top' );
		}
		
		// Venue Form [ Edit Page ]
		$id = $page_settings['venue_form'];
		if( $id > 0 ) {
			$link = str_replace( $url, '', get_permalink( $id ) );			
			$link = trim( $link, '/' );		
			
			add_rewrite_rule( "$link/([^/]+)/([0-9]{1,})/?$", 'index.php?page_id='.$id.'&aec_action=$matches[1]&aec_id=$matches[2]', 'top' );
		}
		
		// Organizer Form [ Edit Page ]
		$id = $page_settings['organizer_form'];
		if( $id > 0 ) {
			$link = str_replace( $url, '', get_permalink( $id ) );			
			$link = trim( $link, '/' );		
			
			add_rewrite_rule( "$link/([^/]+)/([0-9]{1,})/?$", 'index.php?page_id='.$id.'&aec_action=$matches[1]&aec_id=$matches[2]', 'top' );
		}
	
		// Rewrite tags
		add_rewrite_tag( '%aec_event%', '([^/]+)' );
		add_rewrite_tag( '%aec_category%', '([^/]+)' );	
		add_rewrite_tag( '%aec_tag%', '([^/]+)' );	
		add_rewrite_tag( '%aec_venue%', '([^/]+)' );				
		add_rewrite_tag( '%aec_organizer%', '([^/]+)' );
		add_rewrite_tag( '%aec_id%', '([0-9]{1,})' );
	
	}
	
	/**
	 * Flush rewrite rules when it's necessary.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	 public function maybe_flush_rules() {

		$rewrite_rules = get_option( 'rewrite_rules' );
				
		if( $rewrite_rules ) {
		
			global $wp_rewrite;
			
			foreach( $rewrite_rules as $rule => $rewrite ) {
				$rewrite_rules_array[$rule]['rewrite'] = $rewrite;
			}
			$rewrite_rules_array = array_reverse( $rewrite_rules_array, true );
		
			$maybe_missing = $wp_rewrite->rewrite_rules();
			$missing_rules = false;		
		
			foreach( $maybe_missing as $rule => $rewrite ) {
				if( ! array_key_exists( $rule, $rewrite_rules_array ) ) {
					$missing_rules = true;
					break;
				}
			}
		
			if( true === $missing_rules ) {
				flush_rewrite_rules();
			}
		
		}
	
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		
		$settings = get_option( 'aec_general_settings');
		
		wp_register_style( 'jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
				
		$deps = array( 'jquery-ui-css' );
		
		if( array_key_exists( 'bootstrap', $settings ) && in_array( 'css', $settings['bootstrap'] ) ) {
			wp_register_style( AEC_PLUGIN_SLUG.'-bootstrap', AEC_PLUGIN_URL.'public/css/bootstrap.css', array(), AEC_PLUGIN_VERSION, 'all' );
			$deps[] = AEC_PLUGIN_SLUG.'-bootstrap';
		}	
				
		wp_register_style( AEC_PLUGIN_SLUG, AEC_PLUGIN_URL.'public/css/aec-public.css', $deps, AEC_PLUGIN_VERSION, 'all' );
		
		if( is_singular('aec_events') ) {
		
			wp_enqueue_style( AEC_PLUGIN_SLUG );
			
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		$general_settings = get_option( 'aec_general_settings' );
		$map_settings = get_option( 'aec_map_settings' );

		$deps = array( 'jquery' ,'jquery-ui-datepicker' );
		
		if( array_key_exists( 'bootstrap', $general_settings ) && in_array( 'javascript', $general_settings['bootstrap'] ) ) {
			wp_register_script( AEC_PLUGIN_SLUG.'-bootstrap', AEC_PLUGIN_URL.'public/js/bootstrap.min.js', array( 'jquery' ), AEC_PLUGIN_VERSION, true );
			$deps[] = AEC_PLUGIN_SLUG.'-bootstrap';
		}

		wp_register_script( AEC_PLUGIN_SLUG, AEC_PLUGIN_URL.'public/js/aec-public.js', $deps, AEC_PLUGIN_VERSION, true );

		wp_localize_script(
			AEC_PLUGIN_SLUG,
			'aec',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'zoom'    => ! empty( $map_settings['zoom_level'] ) ? $map_settings['zoom_level'] : 5
			)
		);
		
		wp_register_script( AEC_PLUGIN_SLUG.'-bootstrap-validator', AEC_PLUGIN_URL.'public/js/validator.js', array( 'jquery' ), AEC_PLUGIN_VERSION, false );
		
		$api_key = ! empty( $map_settings['api_key'] ) ? '&key='.$map_settings['api_key'] : '';
		wp_register_script( AEC_PLUGIN_SLUG.'-google-map', '//maps.googleapis.com/maps/api/js?v=3.exp'.$api_key );	
		
		if( is_singular('aec_events') ) {
		
			wp_enqueue_script( AEC_PLUGIN_SLUG.'-google-map' );
			wp_enqueue_script( AEC_PLUGIN_SLUG );
			
			
		}

	}
	
	/**
	 * Adds the Facebook OG tags and Twitter Cards.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function og_metatags() {
	
		global $post;
		
		if( empty( $post ) ) return;
		
		$page_settings        = get_option( 'aec_page_settings' );
		$socialshare_settings = get_option( 'aec_socialshare_settings' );
			
		$page = '';
		if( is_singular('aec_events') ) {
			
			$page = 'event_detail';
				
		} else {
			
			if( $page_settings['categories'] == $post->ID ) {
				$page = 'categories';
			}
				
			if( in_array( $post->ID, array( $page_settings['calendar'], $page_settings['events'], $page_settings['category'], $page_settings['tag'], $page_settings['venue'], $page_settings['organizer'],  $page_settings['search'] ) ) ) {
				$page = 'event_archives';
			}
				
		}
			
		if( isset( $socialshare_settings['pages'] ) && in_array( $page, $socialshare_settings['pages'] ) ) {
			
			$permalink = aec_get_current_url();
			$title = get_the_title();
			$content = get_the_content();
			$post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID  ), 'full' ); 
					
			// If current page = single category page
			if( $post->ID == $page_settings['category'] ) {
			
				$slug = get_query_var( 'aec_category' );
				
				if( $slug && $term = get_term_by( 'slug', $slug, 'aec_categories' ) ) {
					$title = $term->name;
					$content = $term->description;
				}
				
			}
				
			// If current page = single tag page
			if( $post->ID == $page_settings['tag'] ) {
			
				$slug = get_query_var( 'aec_tag' );
				
				if( $slug && $term = get_term_by( 'slug', $slug, 'aec_tags' ) ) {
					$title = $term->name;
					$content = $term->description;
				}
				
			}
				
			// If current page = single venue page
			if( $post->ID == $page_settings['venue'] ) {
			
				$slug = get_query_var( 'aec_venue' );
				
				if( $slug && $page = get_page_by_path( $slug, OBJECT, 'aec_venues' ) ) {
					$title = $page->post_title;	
					$content = $page->post_content;	
				}
				
			}
				
			// If current page = single organizer page
			if( $post->ID == $page_settings['organizer'] ) {
			
				$slug = get_query_var( 'aec_organizer' );
				
				if( $slug && $page = get_page_by_path( $slug, OBJECT, 'aec_organizers' ) ) {
					$title = $page->post_title;	
					$content = $page->post_content;
					$post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $page->ID  ), 'full' ); 	
				}
				
			}

			// ...
			$meta = array();
			$meta[] = '<meta property="og:url" content="'.$permalink.'" />';
			$meta[] = '<meta property="og:type" content="article" />';	
			$meta[] = '<meta property="og:title" content="'.$title.'" />';	
			if( ! empty( $content ) ) $meta[] = '<meta property="og:description" content="'.wp_trim_words( $content, 150 ).'" />';
			if( ! empty( $post_thumbnail ) ) $meta[] = '<meta property="og:image" content="'.$post_thumbnail[0].'" />';
			$meta[] = '<meta property="og:site_name" content="'.get_bloginfo( 'name' ).'" />';
			$meta[] = '<meta name="twitter:card" content="summary">';
			
			echo "\n".implode("\n", $meta)."\n";
			
		}
		
	}
	
	/**
	 * Change the current page title if applicable.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 string    $title    Current page title.
	 * @return	 string    $title    Modified page title.
	 */
	public function the_title( $title ) {

		global $id, $post;
		
		if( is_singular('aec_events') && 'aec_events' == $post->post_type ) return $title;
		
		if( is_page() && in_the_loop() ) {
		
			$page_settings = get_option( 'aec_page_settings' );
			
			// Change Recurring events page title
			if( $id == $page_settings['events'] ) {
			
				$slug = get_query_var( 'aec_event' );
				if( $slug && $page = get_page_by_path( $slug, OBJECT, 'aec_events' ) ) {
					$title = $page->post_title;			
				}
				
			}
			
			// Change Category page title
			if( $id == $page_settings['category'] ) {
			
				$slug = get_query_var( 'aec_category' );
				if( $slug && $term = get_term_by( 'slug', $slug, 'aec_categories' ) ) {
					$title = $term->name;			
				}
				
			}
			
			// Change Tag page title
			if( $id == $page_settings['tag'] ) {
			
				$slug = get_query_var( 'aec_tag' );
				
				if( $slug && $term = get_term_by( 'slug', $slug, 'aec_tags' ) ) {
					$title = $term->name;	
				}
				
			}
			
			// Change Venue page title
			if( $id == $page_settings['venue'] ) {
			
				$slug = get_query_var( 'aec_venue' );
				
				if( $slug && $page = get_page_by_path( $slug, OBJECT, 'aec_venues' ) ) {
					$title = $page->post_title;			
				}
				
			}
			
			// Change Organizer page title
			if( $id == $page_settings['organizer'] ) {
			
				$slug = get_query_var( 'aec_organizer' );
				
				if( $slug && $page = get_page_by_path( $slug, OBJECT, 'aec_organizers' ) ) {
					$title = $page->post_title;			
				}
				
			}
			
		}
		
		return $title;
	
	}
	
		
}
