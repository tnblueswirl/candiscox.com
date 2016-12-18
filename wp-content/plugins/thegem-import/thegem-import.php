<?php
/*
Plugin Name: TheGem Import
Plugin URI: http://codex-themes.com/thegem/
Author: Codex Themes
Version: 2.0.0
Author URI: http://codex-themes.com/thegem/
*/

function thegem_import_get_purchase() {
	$theme_options = get_option('thegem_theme_options');
	if($theme_options && isset($theme_options['purchase_code'])) {
		return $theme_options['purchase_code'];
	}
	return false;
}

if(!function_exists('thegem_is_plugin_active')) {
	function thegem_is_plugin_active($plugin) {
		include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		return is_plugin_active($plugin);
	}
}

add_action('admin_menu', 'thegem_import_submenu_page');
function thegem_import_submenu_page() {
	add_menu_page( 'TheGem Import', 'TheGem Import', 'manage_options', 'thegem-import-submenu-page', 'thegem_import_page', '', 81 );
}

function thegem_import_page() {
	$packs = array(
		'agencies' => array(
			'title' => 'Agencies',
			'pics' => array(1, 2, 3, 4, 5, 6, 7)
		),
		'apps' => array(
			'title' => 'Apps',
			'pics' => array(1, 2)
		),
		'architecture' => array(
			'title' => 'Construction, Architecture, Real Estate',
			'pics' => array(1,2,3)
		),
		'beauty' => array(
			'title' => 'Beauty',
			'pics' => array(1, 2)
		),
		'business' => array(
			'title' => 'Business',
			'pics' => array(1, 2, 3, 4, 5, 6)
		),
		'creative' => array(
			'title' => 'Creative',
			'pics' => array(1, 2, 3)
		),
		'gym' => array(
			'title' => 'Gym',
			'pics' => array(1, 2)
		),
		'hotels' => array(
			'title' => 'Hotels',
			'pics' => array(1, 2)
		),
		'landings' => array(
			'title' => 'Landing Pages',
			'pics' => array(1, 2, 3, 4, 5)
		),
		'lawyer' => array(
			'title' => 'Lawyers',
			'pics' => array(1, 2)
		),
		'medical' => array(
			'title' => 'Medical',
			'pics' => array(1, 2)
		),
		'photography' => array(
			'title' => 'Photography',
			'pics' => array(1, 2)
		),
		'portfolios' => array(
			'title' => 'Portfolios',
			'pics' => array(1, 2, 3, 4)
		),
		'restaurant' => array(
			'title' => 'Restaurant',
			'pics' => array(1, 2)
		),
		'shopdemos' => array(
			'title' => 'Shop',
			'pics' => array(1, 2, 3, 4, 5, 6, 7)
		),
		'coming-soon' => array(
			'title' => 'Coming Soon',
			'pics' => array(1, 2, 3, 4, 5, 6)
		),
	);
?>
<div class="wrap">
	<div id="icon-tools" class="icon32"></div>
	<h2>TheGem Import</h2>
	<?php if(thegem_is_plugin_active('wordpress-importer/wordpress-importer.php')) : ?>
		<p><?php printf(__('It seems that Wordpress Import Plugin is active. Please deactivate Wordpress Import Plugin on <a href="%s">plugins page</a> to proceed with import of TheGem\'s main demo content.'), admin_url('plugins.php')); ?></p>
	<?php elseif(get_template() != 'thegem') : ?>
		<p><?php _e('Your current activated theme in not TheGem main parent theme. Please note, that this import works only with TheGem main parent theme. Please activate TheGem main parent theme before proceeding with import.'); ?></p>
	<?php elseif(!thegem_is_plugin_active('thegem-elements/thegem-elements.php')) : ?>
		<p><?php _e('Plugin "TheGem Theme Elements" is not active.'); ?></p>
	<?php elseif(!thegem_import_get_purchase()) : ?>
		<p><?php printf(__('Please enter purchase code in <a href="%s">Theme options</a>'), admin_url('themes.php?page=options-framework#activation')); ?></p>
	<?php else : ?>
		<div class="thegem-import-prevent-message"><?php printf(__('The import of demo media works best on a new installation of WordPress. If you have already an existing WordPress installation, we recommend you to use "<a href="%s" target="_blank">Reset WP</a>" plugin to reset your media database and content.'), esc_url('https://wordpress.org/plugins/reset-wp/')); ?></div>
		<div class="thegem-import-output ui-no-theme">
			<div class="import-variants">
				<div id="full-import" class="import-variant">
					<h3><?php _e('Full demo import'); ?></h3>
					<div class="inside"><div>
						<p><?php _e('With this option you can import all demo content with one click, including import of media content (selected and optimized images & videos used on our demo website). Please note: full import & generating of image thumbnails may take from 30 min to 1 hour, depending on your server/hosting configuration.'); ?></p>
						<button class="import-button button-primary" data-import-part="full" data-import-pack="main"><?php _e('Start full demo import'); ?></button>
					</div></div>
				</div>

				<div id="pack-import" class="import-variant">
					<h3><?php _e('Import of selected demo concepts'); ?></h3>
					<div class="inside"><div>
						<p><?php _e('Lightweighted import option. Here you can select one purpose topic for demo import. It is useful if you are interested only in one special puprose (for example only business homepages) and don\'t need any other demos. This import will install only homepages of the selected topic as well as demo pages from "Pages" category of our demo website (About Us, Services etc.) and demo pages from "Elements" category (to give you all examples of how to use different shortcodes and elements). Please note: this import works best on a new intall of WordPress.'); ?></p>
						<div class="import-tabs">
							<ul class="clearfix">
								<?php foreach($packs as $pack => $content) : ?>
									<li><a href="#import-tab-<?php echo $pack; ?>"><?php echo $content['title']; ?></a></li>
								<?php endforeach; ?>
							</ul>
							<div class="import-tabs-content">
								<?php foreach($packs as $pack => $content) : ?>
									<div class="import-tab" id="import-tab-<?php echo $pack; ?>">
										<div class="import-pack-pics">
											<?php foreach($content['pics'] as $pic) : ?>
												<div class="import-pack-pic"><img src="<?php echo plugins_url( '/images/previews/'.$pack.'/'.$pic.'.jpg' , __FILE__ ) ?>" alt="#" /></div>
											<?php endforeach; ?>
										</div>
										<button class="import-button button-primary" data-import-part="full" data-import-pack="<?php echo $pack; ?>"><?php _e('Import'); ?></button>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div></div>
				</div>

				<div id="partical-import" class="import-variant">
					<h3><?php _e('Partial demo import'); ?></h3>
					<div class="inside"><div>
						<p><?php _e('With this option you can import all posts/pages and media content separately. For example, if you don\'t want to import our demo images & videos, you can click on "Import content without media". This import runs very fast and is done in minutes. In case you wish to import only media demo content (or add our demo images & videos after you\'ve made "Import content without media") you can click on "Import only media demo content".'); ?></p>
						<button class="import-button button-primary" data-import-part="posts" data-import-pack="main"><?php _e('Import content without media'); ?></button>
						<button class="import-button button-primary" data-import-part="media" data-import-pack="main"><?php _e('Import only media demo content'); ?></button>
					</div></div>
				</div>

			</div>
		</div>
	<?php endif; ?>
</div>
<?php
}

function thegem_import_enqueue($hook) {
	if($hook == 'toplevel_page_thegem-import-submenu-page') {
		wp_enqueue_script('thegem-import-scripts', plugins_url( '/js/ti-scripts.js' , __FILE__ ), array('jquery', 'jquery-ui-accordion', 'jquery-ui-tabs'), false, true);
		wp_localize_script('thegem-import-scripts', 'thegem_import_data', array(
			'ajax_url' => admin_url('admin-ajax.php')
		));
		wp_enqueue_style('thegem-import-css', plugins_url( '/css/ti-styles.css' , __FILE__ ));
	}
}
add_action('admin_enqueue_scripts', 'thegem_import_enqueue');

add_action('wp_ajax_thegem_import_files_list', 'thegem_import_files_list');
function thegem_import_files_list () {
	$response_p = wp_remote_get(add_query_arg(array('code' => thegem_import_get_purchase(), 'site_url' => get_site_url()), 'http://democontent.codex-themes.com/av_validate_code.php'), array('timeout' => 20));
	if(!is_wp_error($response_p)) {
		$rp_data = json_decode($response_p['body'], true);
		if(is_array($rp_data) && isset($rp_data['result']) && $rp_data['result'] && isset($rp_data['item_id']) && $rp_data['item_id'] === '16061685') {
			if(isset($_REQUEST['import_pack']) && isset($_REQUEST['import_part'])) {
				$response = wp_remote_get(add_query_arg(array('import_pack' => $_REQUEST['import_pack'], 'import_part' => $_REQUEST['import_part']), 'http://democontent.codex-themes.com/democontent-packs/index-new.php'), array('timeout' => 20));
				if(!is_wp_error($response)) {
					echo $response['body'];
				} else {
					echo json_encode(array('status' => 0, 'status_text' => 'Import failed.', 'message' => 'Some troubles with connecting to demo-content server.'));
				}
			} else {
				echo json_encode(array('status' => 0, 'status_text' => 'Import failed.', 'message' => 'Sending data error.'));
			}
		} else {
			echo json_encode(array('status' => 0, 'status_text' => 'Import failed.', 'message' => 'Purchase code verification failed. <a href="'.esc_url(admin_url('themes.php?page=options-framework#activation')).'">Activate TheGem</a>'));
		}
	} else {
		echo json_encode(array('status' => 0, 'status_text' => 'Import failed.', 'message' => 'Some troubles with connecting to demo-content server.'));
	}
	die(-1);
}

add_action('wp_ajax_thegem_import_file', 'thegem_import_file');
function thegem_import_file () {
	$filedir = '/packs/thegem/';
	if(isset($_REQUEST['import_pack']) && ($_REQUEST['import_pack'] !== 'main')) {
		$filedir = '/packs/'.$_REQUEST['import_pack'].'/';
	}

	if(!empty($_REQUEST['filename'])) {
		ob_start();
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		$tmp = download_url('http://democontent.codex-themes.com/democontent-packs' .$filedir.$_REQUEST['filename']);
		if( is_wp_error( $tmp ) ) {
			print_r($tmp->get_error_messages());
		} else {
			if (! defined('WP_LOAD_IMPORTERS')) define('WP_LOAD_IMPORTERS', true);
			require_once(plugin_dir_path( __FILE__ ) . '/inc/wordpress-importer.php');
			$wp_import = new WP_Import();
			$wp_import->fetch_attachments = true;
			$wp_import->import($tmp);
		}
		@unlink( $tmp );
		$messages = ob_get_clean();
		echo json_encode(array('status' => 1, 'message' => 'Done. <!-- '.$messages.' -->'));
	}
	die(-1);
}

function thegem_import_replace_array($dir = 1) {
	$packs = array(
		'agencies',
		'apps',
		'architecture',
		'beauty',
		'business',
		'creative',
		'gym',
		'hotels',
		'landings',
		'lawyer',
		'medical',
		'photography',
		'portfolios',
		'restaurant',
		'shopdemos',
		'coming-soon'
	);
	if($dir === 1) {
		$replace_array = array('http://democontent.codex-themes.com/thegem/wp-content/uploads');
	} else {
		$replace_array = array('http://democontent.codex-themes.com/thegem/wp-content/themes/TheGem');
	}
	foreach($packs as $pack) {
		if($dir === 1) {
			$replace_array[] = 'http://democontent.codex-themes.com/thegem-'.$pack.'/wp-content/uploads';
		} else {
			$replace_array[] = 'http://democontent.codex-themes.com/thegem-'.$pack.'/wp-content/themes/TheGem';
		}
	}
	return $replace_array;
}

add_filter('wp_import_post_data_raw', 'thegem_import_wp_import_post_data_raw');
function thegem_import_wp_import_post_data_raw($post) {
	$upload_dir = wp_upload_dir();
	$post['post_content'] = str_replace(thegem_import_replace_array(), $upload_dir['baseurl'], $post['post_content']);
	$post['post_content'] = str_replace(thegem_import_replace_array(2), get_template_directory_uri(), $post['post_content']);
	return $post;
}

add_filter('import_post_meta', 'thegem_import_post_meta', 11, 3);
function thegem_import_post_meta($post_id, $key, $value) {
	$upload_dir = wp_upload_dir();
	if(is_array($value)) {
		foreach($value as $k => $v) {
			if(is_array($v)) {
				foreach($v as $a => $b) {
					$value[$k][$a] = str_replace(thegem_import_replace_array(), $upload_dir['baseurl'], $value[$k][$a]);
					$value[$k][$a] = str_replace(thegem_import_replace_array(2), get_template_directory_uri(), $value[$k][$a]);
				}
			} else {
				$value[$k] = str_replace(thegem_import_replace_array(), $upload_dir['baseurl'], $value[$k]);
				$value[$k] = str_replace(thegem_import_replace_array(2), get_template_directory_uri(), $value[$k]);
			}
		}
	} else {
		$value = str_replace(thegem_import_replace_array(), $upload_dir['baseurl'], $value);
		$value = str_replace(thegem_import_replace_array(2), get_template_directory_uri(), $value);
	}
	update_post_meta($post_id, $key, $value);
}