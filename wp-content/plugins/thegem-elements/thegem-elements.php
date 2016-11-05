<?php
/*
Plugin Name: TheGem Theme Elements
Plugin URI: http://codex-themes.com/thegem/
Author: Codex Themes
Version: 1.3.0
Author URI: http://codex-themes.com/thegem/
TextDomain: thegem
DomainPath: /languages
*/

add_action( 'plugins_loaded', 'thegem_load_textdomain' );
function thegem_load_textdomain() {
	load_plugin_textdomain( 'thegem', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

if(!function_exists('thegem_is_plugin_active')) {
	function thegem_is_plugin_active($plugin) {
		include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		return is_plugin_active($plugin);
	}
}

if(!function_exists('thegem_user_icons_info_link')) {
function thegem_user_icons_info_link($pack = '') {
	return esc_url(apply_filters('thegem_user_icons_info_link', get_template_directory_uri().'/fonts/icons-list-'.$pack.'.html', $pack));
}
}

/* Get theme option*/
if(!function_exists('thegem_get_option')) {
function thegem_get_option($name, $default = false, $ml_full = false) {
	$options = get_option('thegem_theme_options');
	if(isset($options[$name])) {
		$ml_options = array('home_content', 'footer_html');
		if(in_array($name, $ml_options) && is_array($options[$name]) && !$ml_full) {
			if(defined('ICL_LANGUAGE_CODE')) {
				global $sitepress;
				if(isset($options[$name][ICL_LANGUAGE_CODE])) {
					$options[$name] = $options[$name][ICL_LANGUAGE_CODE];
				} elseif($sitepress->get_default_language() && isset($options[$name][$sitepress->get_default_language()])) {
					$options[$name] = $options[$name][$sitepress->get_default_language()];
				} else {
					$options[$name] = '';
				}
			}else {
				$options[$name] = reset($options[$name]);
			}
		}
		return apply_filters('thegem_option_'.$name, $options[$name]);
	}
	return apply_filters('thegem_option_'.$name, $default);
}
}

/* USER ICON PACK */

if(!function_exists('thegem_icon_userpack_enabled')) {
function thegem_icon_userpack_enabled() {
	return apply_filters('thegem_icon_userpack_enabled', false);
}
}

if(!function_exists('thegem_icon_packs_select_array')) {
function thegem_icon_packs_select_array() {
	$packs = array('elegant' => __('Elegant', 'thegem'), 'material' => __('Material Design', 'thegem'), 'fontawesome' => __('FontAwesome', 'thegem'));
	if(thegem_icon_userpack_enabled()) {
		$packs['userpack'] = __('UserPack', 'thegem');
	}
	return $packs;
}
}

if(!function_exists('thegem_icon_packs_infos')) {
function thegem_icon_packs_infos() {
	ob_start();
?>
<?php _e('Enter icon code', 'thegem'); ?>.
<a class="gem-icon-info gem-icon-info-elegant" href="<?php echo thegem_user_icons_info_link('elegant'); ?>" onclick="tb_show('<?php _e('Icons info', 'thegem'); ?>', this.href+'?TB_iframe=true'); return false;"><?php _e('Show Elegant Icon Codes', 'thegem'); ?></a>
<a class="gem-icon-info gem-icon-info-material" href="<?php echo thegem_user_icons_info_link('material'); ?>" onclick="tb_show('<?php _e('Icons info', 'thegem'); ?>', this.href+'?TB_iframe=true'); return false;"><?php _e('Show Material Design Icon Codes', 'thegem'); ?></a>
<a class="gem-icon-info gem-icon-info-fontawesome" href="<?php echo thegem_user_icons_info_link('fontawesome'); ?>" onclick="tb_show('<?php _e('Icons info', 'thegem'); ?>', this.href+'?TB_iframe=true'); return false;"><?php _e('Show FontAwesome Icon Codes', 'thegem'); ?></a>
<?php if(thegem_icon_userpack_enabled()) : ?>
<a class="gem-icon-info gem-icon-info-userpack" href="<?php echo thegem_user_icons_info_link('userpack'); ?>" onclick="tb_show('<?php _e('Icons info', 'thegem'); ?>', this.href+'?TB_iframe=true'); return false;"><?php _e('Show UserPack Icon Codes', 'thegem'); ?></a>
<?php endif; ?>
<?php
	return ob_get_clean();
}
}


/* META BOXES */

if(!function_exists('thegem_print_select_input')) {
function thegem_print_select_input($values = array(), $value = '', $name = '', $id = '') {
	if(!is_array($values)) {
		$values = array();
	}
?>
	<select name="<?php echo esc_attr($name) ?>" id="<?php echo esc_attr($id); ?>" class="thegem-combobox">
		<?php foreach($values as $key => $title) : ?>
			<option value="<?php echo esc_attr($key); ?>" <?php selected($key, $value); ?>><?php echo esc_html($title); ?></option>
		<?php endforeach; ?>
	</select>
<?php
}
}

if(!function_exists('thegem_print_checkboxes')) {
function thegem_print_checkboxes($values = array(), $value = array(), $name = '', $id_prefix = '', $after = '') {
	if(!is_array($values)) {
		$values = array();
	}
	if(!is_array($value)) {
		$value = array();
	}
?>
	<?php foreach($values as $key => $title) : ?>
		<input name="<?php echo esc_attr($name); ?>" type="checkbox" id="<?php echo esc_attr($id_prefix.'-'.$key); ?>" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $value), 1); ?> />
		<label for="<?php echo esc_attr($id_prefix.'-'.$key); ?>"><?php echo esc_html($title); ?></label>
		<?php echo $after; ?>
	<?php endforeach; ?>
<?php
}
}

/* FONTS MANAGER */

function thegem_fonts_allowed_mime_types( $existing_mimes = array() ) {
	$existing_mimes['ttf'] = 'font/ttf';
	$existing_mimes['eot'] = 'font/eot';
	$existing_mimes['woff'] = 'font/woff';
	$existing_mimes['svg'] = 'font/svg';
	$existing_mimes['json'] = 'application/json';
	return $existing_mimes;
}
add_filter('upload_mimes', 'thegem_fonts_allowed_mime_types');

function thegem_modify_post_mime_types( $post_mime_types ) {
	$post_mime_types['font/ttf'] = array(esc_html__('TTF Font', 'thegem'), esc_html__( 'Manage TTFs', 'thegem' ), _n_noop( 'TTF <span class="count">(%s)</span>', 'TTFs <span class="count">(%s)</span>', 'thegem' ) );
	$post_mime_types['font/eot'] = array(esc_html__('EOT Font', 'thegem'), esc_html__( 'Manage EOTs', 'thegem' ), _n_noop( 'EOT <span class="count">(%s)</span>', 'EOTs <span class="count">(%s)</span>', 'thegem' ) );
	$post_mime_types['font/woff'] = array(esc_html__('WOFF Font', 'thegem'), esc_html__( 'Manage WOFFs', 'thegem' ), _n_noop( 'WOFF <span class="count">(%s)</span>', 'WOFFs <span class="count">(%s)</span>', 'thegem' ) );
	$post_mime_types['font/svg'] = array(esc_html__('SVG Font', 'thegem'), esc_html__( 'Manage SVGs', 'thegem' ), _n_noop( 'SVG <span class="count">(%s)</span>', 'SVGs <span class="count">(%s)</span>', 'thegem' ) );
	return $post_mime_types;
}
add_filter('post_mime_types', 'thegem_modify_post_mime_types');

/* SCRTIPTs & STYLES */

function thegem_elements_scripts() {
	wp_register_style('thegem-portfolio', get_template_directory_uri() . '/css/portfolio.css');
	wp_register_style('thegem-gallery', get_template_directory_uri() . '/css/gallery.css');
	wp_register_script('thegem-diagram-line', get_template_directory_uri() . '/js/diagram_line.js', array('jquery', 'jquery-easing'), false, true);
	wp_register_script('raphael', get_template_directory_uri() . '/js/raphael.js', array('jquery'), false, true);
	wp_register_script('thegem-diagram-circle', get_template_directory_uri() . '/js/diagram_circle.js', array('jquery', 'raphael'), false, true);
	wp_register_script('thegem-news-carousel', get_template_directory_uri() . '/js/news-carousel.js', array('jquery', 'jquery-carouFredSel'), false, true);
	wp_register_script('thegem-clients-grid-carousel', get_template_directory_uri() . '/js/clients-grid-carousel.js', array('jquery', 'jquery-carouFredSel'), false, true);
	wp_register_script('thegem-portfolio-grid-carousel', get_template_directory_uri() . '/js/portfolio-grid-carousel.js', array('jquery', 'jquery-carouFredSel'), false, true);
	wp_register_script('thegem-testimonials-carousel', get_template_directory_uri() . '/js/testimonials-carousel.js', array('jquery', 'jquery-carouFredSel'), false, true);
	wp_register_script('thegem-widgets', get_template_directory_uri() . '/js/widgets.js', array('jquery', 'jquery-carouFredSel', 'jquery-effects-core'), false, true);
	wp_register_script('jquery-restable', get_template_directory_uri() . '/js/jquery.restable.js', array('jquery'), false, true);
	wp_register_script('thegem-quickfinders-effects', get_template_directory_uri() . '/js/quickfinders-effects.js', array('jquery'), false, true);
	wp_register_script('thegem-counters-effects', get_template_directory_uri() . '/js/counters-effects.js', array('jquery'), false, true);
	wp_register_script('thegem-parallax-vertical', get_template_directory_uri() . '/js/jquery.parallaxVertical.js', array('jquery'), false, true);
	wp_register_script('thegem-parallax-horizontal', get_template_directory_uri() . '/js/jquery.parallaxHorizontal.js', array('jquery'), false, true);
	wp_register_style('nivo-slider', get_template_directory_uri() . '/css/nivo-slider.css', array());
	wp_register_script('jquery-nivoslider', get_template_directory_uri() . '/js/jquery.nivo.slider.pack.js', array('jquery'));
	wp_register_script('thegem-nivoslider-init-script', get_template_directory_uri() . '/js/nivoslider-init.js', array('jquery', 'jquery-nivoslider'));
	wp_localize_script('thegem-nivoslider-init-script', 'thegem_nivoslider_options', array(
		'effect' => thegem_get_option('slider_effect') ? thegem_get_option('slider_effect') : 'random',
		'slices' => thegem_get_option('slider_slices') ? thegem_get_option('slider_slices') : 15,
		'boxCols' => thegem_get_option('slider_boxCols') ? thegem_get_option('slider_boxCols') : 8,
		'boxRows' => thegem_get_option('slider_boxRows') ? thegem_get_option('slider_boxRows') : 4,
		'animSpeed' => thegem_get_option('slider_animSpeed') ? thegem_get_option('slider_animSpeed')*100 : 500,
		'pauseTime' => thegem_get_option('slider_pauseTime') ? thegem_get_option('slider_pauseTime')*1000 : 3000,
		'directionNav' => thegem_get_option('slider_directionNav') ? true : false,
		'controlNav' => thegem_get_option('slider_controlNav') ? true : false,
	));
	wp_register_script('thegem-isotope-metro', get_template_directory_uri() . '/js/isotope_layout_metro.js', array('isotope-js'), '', true);
	wp_register_script('thegem-isotope-masonry-custom', get_template_directory_uri() . '/js/isotope-masonry-custom.js', array('jquery'), '', true);
	wp_register_script('thegem-juraSlider', get_template_directory_uri() . '/js/jquery.juraSlider.js', array('jquery'), '', true);
	wp_register_script('thegem-portfolio', get_template_directory_uri() . '/js/portfolio.js', array('jquery', 'jquery-dlmenu', 'thegem-scroll-monitor'), '', true);
	wp_register_script('thegem-removewhitespace', get_template_directory_uri() . '/js/jquery.removeWhitespace.min.js', array('jquery'), '', true);
	wp_register_script('jquery-collagePlus', get_template_directory_uri() . '/js/jquery.collagePlus.min.js', array('jquery'), '', true);
	wp_register_script('thegem-countdown', get_template_directory_uri() . '/js/thegem-countdown.js', array( 'jquery', 'raphael', 'odometr' ) );
	wp_register_style('thegem-countdown', get_template_directory_uri() . '/css/thegem-countdown.css');
}
add_action('wp_enqueue_scripts', 'thegem_elements_scripts', 6);


require_once(plugin_dir_path( __FILE__ ) . 'inc/content.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/remote_media_upload.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/diagram.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/additional.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/post-types/init.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/shortcodes/init.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/widgets/init.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/add_vc_icon_fonts.php');