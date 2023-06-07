<?php

/*
 * Plugin Name: basic_svg
 * Plugin URI: https://xayrin.com/plugins
 * Description: Make SVGs available
 * Author: Scott A. Dixon
 * Author URI: https://xayrin.com
 * Version: 1.0.0
 */

defined('ABSPATH') or die('⎺\_(ツ)_/⎺');

// defines      

define('_PLUGIN_BASIC_SVG', 'basic_svg');

define('_URL_BASIC_SVG', plugin_dir_url(__FILE__));
define('_PATH_BASIC_SVG', plugin_dir_path(__FILE__));

//   ▄████████   ▄██████▄   ███▄▄▄▄▄       ▄████████  
//  ███    ███  ███    ███  ███▀▀▀▀██▄    ███    ███  
//  ███    █▀   ███    ███  ███    ███    ███    █▀   
//  ███         ███    ███  ███    ███   ▄███▄▄▄      
//  ███         ███    ███  ███    ███  ▀▀███▀▀▀      
//  ███    █▄   ███    ███  ███    ███    ███         
//  ███    ███  ███    ███  ███    ███    ███         
//  ████████▀    ▀██████▀    ▀█    █▀     ███

// basic_svg args

define('_ARGS_BASIC_SVG', [
	'bs_active' => [
		'type' => 'string',
		'default' => 'yes'
	]
]);

// basic_svg admin

define('_ADMIN_BASIC_SVG', [
	'options' => [
		'label' => 'Options',
		'columns' => 4,
		'fields' => [
			'bs_active' => [
				'label' => 'SVGs Active',
				'type' => 'check'
			]
		]
	]
]);

// basic_svg api routes

define('_APIPATH_BASIC_SVG',
	'settings'
);

define('_API_BASIC_SVG', [
	[
		'methods' => 'POST',
		'callback' => 'update_settings',
		'args' => _bsSettings::args(),
		'permission_callback' => 'permissions'
	],
	[
		'methods' => 'GET',
		'callback' => 'get_settings',
		'args' => [],
		'permission_callback' => 'permissions'
	]
]);

//     ▄████████     ▄███████▄   ▄█   
//    ███    ███    ███    ███  ███   
//    ███    ███    ███    ███  ███▌  
//    ███    ███    ███    ███  ███▌  
//  ▀███████████  ▀█████████▀   ███▌  
//    ███    ███    ███         ███   
//    ███    ███    ███         ███   
//    ███    █▀    ▄████▀       █▀ 

class _bsAPI {
	public function add_routes() {
		if (count(_API_BASIC_SVG)) {

			foreach(_API_BASIC_SVG as $route) {
				register_rest_route(_PLUGIN_BASIC_SVG . '-api', '/' . _APIPATH_BASIC_SVG, [
					'methods' => $route['methods'],
					'callback' => [$this, $route['callback']],
					'args' => $route['args'],
					'permission_callback' => [$this, $route['permission_callback']]
				]);
			}
		}
	}

	public function permissions() {
		return current_user_can('manage_options');
	}

	public function update_settings(WP_REST_Request $request) {
		$settings = [];
		foreach (_bsSettings::args() as $key => $val) {
			$settings[$key] = $request->get_param($key);
		}
		_bsSettings::save_settings($settings);
		return rest_ensure_response(_bsSettings::get_settings());
	}

	public function get_settings(WP_REST_Request $request) {
		return rest_ensure_response(_bsSettings::get_settings());
	}
}

//     ▄████████     ▄████████      ███          ███       ▄█   ███▄▄▄▄▄       ▄██████▄      ▄████████  
//    ███    ███    ███    ███  ▀█████████▄  ▀█████████▄  ███   ███▀▀▀▀██▄    ███    ███    ███    ███  
//    ███    █▀     ███    █▀      ▀███▀▀██     ▀███▀▀██  ███▌  ███    ███    ███    █▀     ███    █▀   
//    ███          ▄███▄▄▄          ███   ▀      ███   ▀  ███▌  ███    ███   ▄███           ███         
//  ▀███████████  ▀▀███▀▀▀          ███          ███      ███▌  ███    ███  ▀▀███ ████▄   ▀███████████  
//           ███    ███    █▄       ███          ███      ███   ███    ███    ███    ███           ███  
//     ▄█    ███    ███    ███      ███          ███      ███   ███    ███    ███    ███     ▄█    ███  
//   ▄████████▀     ██████████     ▄████▀       ▄████▀    █▀     ▀█    █▀     ████████▀    ▄████████▀ 

class _bsSettings {
	protected static $option_key = _PLUGIN_BASIC_SVG . '-settings';

	public static function args() {
		$args = _ARGS_BASIC_SVG;
		foreach (_ARGS_BASIC_SVG as $key => $val) {
			$val['required'] = true;
			switch ($val['type']) {
				case 'integer': {
					$cb = 'absint';
					break;
				}
				default: {
					$cb = 'sanitize_text_field';
				}
				$val['sanitize_callback'] = $cb;
			}
		}
		return $args;
	}

	public static function get_settings() {
		$defaults = [];
		foreach (_ARGS_BASIC_SVG as $key => $val) {
			$defaults[$key] = $val['default'];
		}
		$saved = get_option(self::$option_key, []);
		if (!is_array($saved) || empty($saved)) {
			return $defaults;
		}
		return wp_parse_args($saved, $defaults);
	}

	public static function save_settings(array $settings) {
		$defaults = [];
		foreach (_ARGS_BASIC_SVG as $key => $val) {
			$defaults[$key] = $val['default'];
		}
		foreach ($settings as $i => $setting) {
			if (!array_key_exists($i, $defaults)) {
				unset($settings[$i]);
			}
		}
		update_option(self::$option_key, $settings);
	}
}

//    ▄▄▄▄███▄▄▄▄       ▄████████  ███▄▄▄▄▄    ███    █▄   
//  ▄██▀▀▀███▀▀▀██▄    ███    ███  ███▀▀▀▀██▄  ███    ███  
//  ███   ███   ███    ███    █▀   ███    ███  ███    ███  
//  ███   ███   ███   ▄███▄▄▄      ███    ███  ███    ███  
//  ███   ███   ███  ▀▀███▀▀▀      ███    ███  ███    ███  
//  ███   ███   ███    ███    █▄   ███    ███  ███    ███  
//  ███   ███   ███    ███    ███  ███    ███  ███    ███  
//   ▀█   ███   █▀     ██████████   ▀█    █▀   ████████▀ 

class _bsMenu {
	protected $slug = _PLUGIN_BASIC_SVG . '-menu';
	protected $assets_url;

	public function __construct($assets_url) {
		$this->assets_url = $assets_url;
		add_action('admin_menu', [$this, 'add_page']);
		add_action('admin_enqueue_scripts', [$this, 'register_assets']);
	}

	public function add_page() {
		add_menu_page(
			_PLUGIN_BASIC_SVG,
			_PLUGIN_BASIC_SVG,
			'manage_options',
			$this->slug,
			[$this, 'render_admin'],
			'data:image/svg+xml;base64,' . base64_encode(
				'<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="500px" height="500px" viewbox="0 0 500 500"><path fill="#a7aaad" d="M250,9.8L42,129.9v240.2l208,120.1l208-120.1V129.9L250,9.8z M407.42,263.66l-44.6-25.75v-53.04L250,119.72 L159.46,172l102.78,59.34l145.18,83.82v25.74L250,431.79L92.58,340.89V236.06l44.6,25.75v53.33L250,380.28l90.5-52.25l-51.24-29.58	l0.15-0.25L92.58,184.55v-25.45L250,68.21l157.42,90.89V263.66z"/></svg>'
			),
			30
		);

		// add posts menus

		$types = [
			'svg'
		];

		foreach ($types as $type) {
			add_submenu_page(
				$this->slug,
				$type . 's',
				$type . 's',
				'manage_options',
				'/edit.php?post_type=' . $type
			);
		}
	}

	public function register_assets() {
		$boo = microtime(false);
		wp_register_script($this->slug, $this->assets_url . '/' . _PLUGIN_BASIC_SVG . '.js?' . $boo, ['jquery']);
		wp_register_style($this->slug, $this->assets_url . '/' . _PLUGIN_BASIC_SVG . '.css?' . $boo);
		wp_localize_script($this->slug, _PLUGIN_BASIC_SVG, [
			'strings' => [
				'saved' => 'Settings Saved',
				'error' => 'Error'
			],
			'api' => [
				'url' => esc_url_raw(rest_url(_PLUGIN_BASIC_SVG . '-api/settings')),
				'nonce' => wp_create_nonce('wp_rest')
			]
		]);
	}

	public function enqueue_assets() {
		if (!wp_script_is($this->slug, 'registered')) {
			$this->register_assets();
		}

		wp_enqueue_script($this->slug);
		wp_enqueue_style($this->slug);
	}

	public function render_admin() {
		wp_enqueue_media();
		$this->enqueue_assets();

		$name = _PLUGIN_BASIC_SVG;
		$form = _ADMIN_BASIC_SVG;

		// build form

		echo '<div id="' . $name . '-wrap" class="wrap">';
			echo '<h1>' . $name . '</h1>';
			echo '<p>Configure your ' . $name . ' settings...</p>';
			echo '<form id="' . $name . '-form" method="post">';
				echo '<nav id="' . $name . '-nav" class="nav-tab-wrapper">';

				foreach ($form as $tid => $tab) {
					echo '<a href="#' . $name . '-' . $tid . '" class="nav-tab">' . $tab['label'] . '</a>';
				}
				echo '</nav>';
				echo '<div class="tab-content">';

				foreach ($form as $tid => $tab) {
					echo '<div id="' . $name . '-' . $tid . '" class="' . $name . '-tab">';

					foreach ($tab['fields'] as $fid => $field) {
						echo '<div class="form-block col-' . $tab['columns'] . '">';
						
						switch ($field['type']) {
							case 'input': {
								echo '<label for="' . $fid . '">';
									echo $field['label'] . ':';
								echo '</label>';
								echo '<input id="' . $fid . '" type="text" name="' . $fid . '">';
								break;
							}
							case 'select': {
								echo '<label for="' . $fid . '">';
									echo $field['label'] . ':';
								echo '</label>';
								echo '<select id="' . $fid . '" name="' . $fid . '">';
									foreach ($field['values'] as $value => $label) {
										echo '<option value="' . $value . '">' . $label . '</option>';
									}
								echo '</select>';
								break;
							}
							case 'text': {
								echo '<label for="' . $fid . '">';
									echo $field['label'] . ':';
								echo '</label>';
								echo '<textarea id="' . $fid . '" class="tabs" name="' . $fid . '"></textarea>';
								break;
							}
							case 'file': {
								echo '<label for="' . $fid . '">';
									echo $field['label'] . ':';
								echo '</label>';
								echo '<input id="' . $fid . '" type="text" name="' . $fid . '">';
								echo '<input data-id="' . $fid . '" type="button" class="button-primary choose-file-button" value="...">';
								break;
							}
							case 'colour': {
								echo '<label for="' . $fid . '">';
									echo $field['label'] . ':';
								echo '</label>';
								echo '<input id="' . $fid . '" type="text" name="' . $fid . '">';
								echo '<input data-id="' . $fid . '" type="color" class="choose-colour-button" value="#000000">';
								break;
							}
							case 'code': {
								echo '<label for="' . $fid . '">';
									echo $field['label'] . ':';
								echo '</label>';
								echo '<textarea id="' . $fid . '" class="code" name="' . $fid . '"></textarea>';
								break;
							}
							case 'check': {
								echo '<em>' . $field['label'] . ':</em>';
								echo '<label class="switch">';
									echo '<input type="checkbox" id="' . $fid . '" name="' . $fid . '" value="yes">';
									echo '<span class="slider"></span>';
								echo '</label>';
								break;
							}
						}
						echo '</div>';
					}
					echo '</div>';
				}
				echo '</div>';
				echo '<div>';
					submit_button();
				echo '</div>';
				echo '<div id="' . $name . '-feedback"></div>';
			echo '</form>';
		echo '</div>';
	}
}

//   ▄█   ███▄▄▄▄▄     ▄█       ███      
//  ███   ███▀▀▀▀██▄  ███   ▀█████████▄  
//  ███▌  ███    ███  ███▌     ▀███▀▀██  
//  ███▌  ███    ███  ███▌      ███   ▀  
//  ███▌  ███    ███  ███▌      ███      
//  ███   ███    ███  ███       ███      
//  ███   ███    ███  ███       ███      
//  █▀     ▀█    █▀   █▀       ▄████▀

function bs_init($dir) {
	// set up post types

	$types = [
		'svg'
	];

	foreach ($types as $type) {
		$uc_type = strtoupper($type);

		$labels = [
			'name' => $uc_type . 's',
			'singular_name' => $uc_type,
			'menu_name' => $uc_type . 's',
			'name_admin_bar' => $uc_type . 's',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New ' . $uc_type,
			'new_item' => 'New ' . $uc_type,
			'edit_item' => 'Edit ' . $uc_type,
			'view_item' => 'View ' . $uc_type,
			'all_items' => $uc_type . 's',
			'search_items' => 'Search ' . $uc_type . 's',
			'not_found' => 'No ' . $uc_type . 's Found'
		];

		register_post_type($type, [
			'supports' => [
				'title'
			],
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_in_menu' => false,
			'query_var' => true,
			'has_archive' => false,
			'rewrite' => ['slug' => $type]
		]);
	}
}

//    ▄▄▄▄███▄▄▄▄       ▄████████      ███         ▄████████  
//  ▄██▀▀▀███▀▀▀██▄    ███    ███  ▀█████████▄    ███    ███  
//  ███   ███   ███    ███    █▀      ▀███▀▀██    ███    ███  
//  ███   ███   ███   ▄███▄▄▄          ███   ▀    ███    ███  
//  ███   ███   ███  ▀▀███▀▀▀          ███      ▀███████████  
//  ███   ███   ███    ███    █▄       ███        ███    ███  
//  ███   ███   ███    ███    ███      ███        ███    ███  
//   ▀█   ███   █▀     ██████████     ▄████▀      ███    █▀   

function bs_add_metaboxes() {
	$screens = ['svg'];
	foreach ($screens as $screen) {
		add_meta_box(
			'bs_meta_box',
			'SVG Data',
			'bs_svg_metabox',
			$screen
		);
	}
}

function bs_svg_metabox($post) {
	$prefix = '_bs-svg_';
	$keys = [
		'pid',
		'code'
	];
	foreach ($keys as $key) {
		$$key = get_post_meta($post->ID, $prefix . $key, true);
	}
	wp_nonce_field(plugins_url(__FILE__), 'wr_plugin_noncename');
	?>
	<style>
		#bs_meta_box label {
			display: block;
			font-weight: 700;
			padding: 4px 0 0;
		}
		#bs_meta_box input,
		#bs_meta_box select,
		#bs_meta_box textarea,
		#bs_meta_box span.preview {
			box-sizing: border-box;
			display: block;
			vertical-align: middle;
			margin-top: 10px;
			float: none;
		}
		#bs_meta_box span.desc {
			display: block;
			padding: 6px 0;
			clear: both;
			font-style: italic;
			font-size: 12px;
		}
		#bs_meta_box span.preview img {
			max-width: 100%;
			height: 100px;
		}
		#bs_meta_box div.middle {
			margin-bottom: 10px;
			padding-bottom: 10px;
			border-bottom: 1px dashed #ddd;
		}
		#bs_meta_box div.top {
			margin-top: 10px;
			margin-bottom: 10px;
			padding-bottom: 10px;
			border-bottom: 1px dashed #ddd;
		}
		#bs_meta_box div.bottom {
			margin-bottom: 0;
			padding-bottom: 0;
			border-bottom: 0;
		}
		.CodeMirror {
			border: 1px solid #ccc;
			border-radius: 4px;
			margin-bottom: 10px;
		}
	</style>
	<div class="inside">
		<div class="top">
			<label>Code:</label>
			<span class="desc">Markup for this SVG</span>
			<input type="hidden" id="bs-svg-pid" name="_bs-svg_pid" value="<?php echo $pid; ?>">
			<textarea id="bs-svg-code" class="code" name="_bs-svg_code"><?php echo $code; ?></textarea>
		</div>
		<div class="bottom">
			<label>Preview:</label>
			<span class="desc">Preview of this SVG</span>
			<span class="preview"><img id="bs-preview" src=""></span>
		</div>
	</div>
	<script>
		jQuery(document).ready(function($) {
			var preview = function() {
				$('#bs-preview').attr('src', 'data:image/svg+xml;base64,' + btoa($('#bs-svg-code').val()));
			}
			var markup = $('#bs-svg-code');
			markup.on('change', function() {
				preview();
			});
			preview();
			editors = ['bs-svg-code'];
			editors.forEach(function(item, index, arr) {
				var eid = $('#' + item);
				if (eid.length) {
					var es = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
					es.codemirror = _.extend(
						{},
						es.codemirror, {
							indentUnit: 2,
							tabSize: 2,
							mode: 'css'
						}
					);
					var editor = wp.codeEditor.initialize(item, es);
					editor.codemirror.on('change', function(cMirror) {
						editor.codemirror.save();
						eid.change();
					});
				}
			});
		});
	</script>
<?php
}

function bs_save_postdata($post_id) {
	$prefix = '_bs-svg_';
	$keys = [
		'pid',
		'code'
	];
	foreach ($keys as $key) {
		if (array_key_exists($prefix . $key, $_POST)) {
			update_post_meta(
				$post_id,
				$prefix . $key,
				$_POST[$prefix . $key]
			);
		}
	}
}

// menu stuff

function bs_set_current_menu($parent_file) {
	global $submenu_file, $current_screen, $pagenow;

	if (in_array($current_screen->id, ['edit-svg', 'svg'])) {
		if ($pagenow == 'post.php') {
			$submenu_file = 'edit.php?post_type=' . $current_screen->post_type;
		}
		$parent_file = _PLUGIN_BASIC_SVG . '-menu';
	}
	return $parent_file;
}

//     ▄████████     ▄█    █▄      ▄██████▄      ▄████████      ███      
//    ███    ███    ███    ███    ███    ███    ███    ███  ▀█████████▄  
//    ███    █▀     ███    ███    ███    ███    ███    ███     ▀███▀▀██  
//    ███          ▄███▄▄▄▄███▄▄  ███    ███   ▄███▄▄▄▄██▀      ███   ▀  
//  ▀███████████  ▀▀███▀▀▀▀███▀   ███    ███  ▀▀███▀▀▀▀▀        ███      
//           ███    ███    ███    ███    ███  ▀███████████      ███      
//     ▄█    ███    ███    ███    ███    ███    ███    ███      ███      
//   ▄████████▀     ███    █▀      ▀██████▀     ███    ███     ▄████▀

//   ▄████████   ▄██████▄   ████████▄      ▄████████  
//  ███    ███  ███    ███  ███   ▀███    ███    ███  
//  ███    █▀   ███    ███  ███    ███    ███    █▀   
//  ███         ███    ███  ███    ███   ▄███▄▄▄      
//  ███         ███    ███  ███    ███  ▀▀███▀▀▀      
//  ███    █▄   ███    ███  ███    ███    ███    █▄   
//  ███    ███  ███    ███  ███   ▄███    ███    ███  
//  ████████▀    ▀██████▀   ████████▀     ██████████  

function bs_shortcode($atts = [], $content = null, $tag = '') {
	ob_start();

	if (strpos($content, ',') !== FALSE) {
		$array = explode(',', $content);
		$name = $array[rand(0, count($array) - 1)];
	}
	else {
		$name = $content;
	}

	if ($name) {
		$svg = get_posts([
			'name' => $name,
			'post_type'   => 'svg',
			'post_status' => 'publish',
			'numberposts' => 1
		]);

		$id = $svg[0]->ID;
		$pid = get_post_meta($id, '_bs-svg_pid', true);
		$code = get_post_meta($id, '_bs-svg_code', true);
		$colour = get_post_meta($id, '_bs-svg_colour', true);

		echo $code;
	}

	return ob_get_clean();
}

// some admin styling

function bs_admin_styling() {
	if (get_current_screen()->post_type == 'svg') {
		echo '<style>';
			echo '';
		echo '</style>';
	}
}

// add admin scripts

function bs_add_scripts($hook) {
	if (get_current_screen()->post_type == 'svg') {
		wp_enqueue_code_editor(['type' => 'application/x-httpd-php']);
	}
}

// caller function

function basic_svg($svg) {
	echo do_shortcode('[svg]' . $svg . '[/svg]');
}

//     ▄██████▄    ▄██████▄   
//    ███    ███  ███    ███  
//    ███    █▀   ███    ███  
//   ▄███         ███    ███  
//  ▀▀███ ████▄   ███    ███  
//    ███    ███  ███    ███  
//    ███    ███  ███    ███  
//    ████████▀    ▀██████▀   

define('_BS', _bsSettings::get_settings());

// actions

add_action('init', 'bs_init');
add_action('admin_head', 'bs_admin_styling');
add_action('admin_enqueue_scripts', 'bs_add_scripts');
add_action('add_meta_boxes', 'bs_add_metaboxes');
add_action('save_post', 'bs_save_postdata');

// filters

add_filter('parent_file', 'bs_set_current_menu');

// shortcodes

add_shortcode('svg', 'bs_shortcode');

// boot plugin

add_action('init', function() {
	if (is_admin()) {
		new _bsMenu(_URL_BASIC_SVG);
	}
});

add_action('rest_api_init', function() {
	_bsSettings::args();
	$api = new _bsAPI();
	$api->add_routes();
});

//     ▄█    █▄        ▄████████   ▄█           ▄███████▄  
//    ███    ███      ███    ███  ███          ███    ███  
//    ███    ███      ███    █▀   ███          ███    ███  
//   ▄███▄▄▄▄███▄▄   ▄███▄▄▄      ███          ███    ███  
//  ▀▀███▀▀▀▀███▀   ▀▀███▀▀▀      ███        ▀█████████▀   
//    ███    ███      ███    █▄   ███          ███         
//    ███    ███      ███    ███  ███▌    ▄    ███         
//    ███    █▀       ██████████  █████▄▄██   ▄████▀



// eof