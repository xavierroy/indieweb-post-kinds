<?php
/**
 * Post Kinds
 *
 * @link    http://indieweb.org/Post_Kinds_Plugin
 * @package Post Kinds
 * Plugin Name: Post Kinds
 * Description: Ever want to reply to someone else's post with a post on your own site? Or to "like" someone else's post, but with your own site?
 * Original Version: 3.3.0
 * Original Author: David Shanske
 * Original Author URI: https://david.shanske.com
 * Text Domain: indieweb-post-kinds
 * Domain Path:  /languages
 * GitHub Plugin URI:	xavierroy/indieweb-post-kinds
 * Version: 3.1.5.11
 * Author: Xavier Roy
 * Author URI: https://xavierroy.com
 */

if ( ! defined( 'POST_KINDS_KSES' ) ) {
	define( 'POST_KINDS_KSES', false );
}



if ( ! file_exists( plugin_dir_path( __FILE__ ) . 'lib/parse-this/parse-this.php' ) ) {
	add_action( 'admin_notices', array( 'Post_Kinds_Plugin', 'parse_this_error' ) );
}

if ( ! class_exists( 'Classic_Editor' ) ) {
	add_action( 'admin_notices', array( 'Post_Kinds_Plugin', 'classic_editor_error' ) );
}

add_action( 'plugins_loaded', array( 'Post_Kinds_Plugin', 'plugins_loaded' ) );
add_action( 'init', array( 'Post_Kinds_Plugin', 'init' ) );

class Post_Kinds_Plugin {
	public static $version = '3.3.0';
	public static function init() {
		// Add Kind Taxonomy.
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-post-kind.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-kind-taxonomy.php';
		Kind_Taxonomy::init();
		Kind_Taxonomy::register();
	}

	public static function parse_this_error() {
		$class   = 'notice notice-error';
		$message = __( 'Parse This is not installed. Please advise the developer', 'indieweb-post-kinds' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}

	public static function classic_editor_error() {
		$class   = 'notice notice-error';
		$message = __( 'Classic Editor Plugin is not active. This plugin will not function correctly at this time without using the Classic Editor.', 'indieweb-post-kinds' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}

	public static function plugins_loaded() {
		$cls = get_called_class();
		load_plugin_textdomain( 'indieweb-post-kinds', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		// On Activation, add terms.
		register_activation_hook( __FILE__, array( 'Kind_Taxonomy', 'activate_kinds' ) );

		// Add Kind Global Functions.
		require_once plugin_dir_path( __FILE__ ) . '/includes/kind-functions.php';

		// Add Time Global Functions.
		require_once plugin_dir_path( __FILE__ ) . '/includes/time-functions.php';

		// Parse This
		require_once plugin_dir_path( __FILE__ ) . 'lib/parse-this/parse-this.php';

		// Plugin Specific Kind Customizations
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-kind-plugins.php';
		add_action( 'init', array( 'Kind_Plugins', 'init' ) );

		// Enhance Media Metadata
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-media-metadata.php';
		add_action( 'init', array( 'Media_Metadata', 'init' ) );

		// Config Settings.
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-kind-config.php';
		add_action( 'init', array( 'Kind_Config', 'init' ) );

		// Add a Settings Link to the Plugins Page.
		$plugin = plugin_basename( __FILE__ );
		add_filter( 'plugin_action_links_$plugin', array( 'Post_Kinds_Plugin', 'settings_link' ) );

		// Add Kind Post UI Configuration
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-kind-metabox.php';
		add_action( 'init', array( 'Kind_Metabox', 'init' ) );
		Kind_Metabox::$version = self::$version;

		// Add Kind Display Functions.
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-kind-view.php';
		add_action( 'init', array( 'Kind_View', 'init' ) );

		// Kind Widgets
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-kind-menu-widget.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-kind-post-widget.php';

		// Load stylesheets.
		add_action( 'wp_enqueue_scripts', array( $cls, 'style_load' ) );
		add_action( 'admin_enqueue_scripts', array( $cls, 'admin_style_load' ) );

		// Load Privacy Declaration
		add_action( 'admin_init', array( $cls, 'privacy_declaration' ) );

		remove_all_actions( 'do_feed_rss2' );
		remove_all_actions( 'do_feed_atom' );
		add_action( 'do_feed_rss2', array( $cls, 'do_feed_rss2' ), 10, 1 );
		add_action( 'do_feed_atom', array( $cls, 'do_feed_atom' ), 10, 1 );
	}

	public static function do_feed_atom( $for_comments ) {
		if ( $for_comments ) {
			load_template( plugin_dir_path( __FILE__ ) . 'templates/feed-atom-comments.php' );
		} else {
			load_template( plugin_dir_path( __FILE__ ) . 'templates/feed-atom.php' );
		}
	}

	public static function do_feed_rss2( $for_comments ) {
		if ( $for_comments ) {
			load_template( plugin_dir_path( __FILE__ ) . 'templates/feed-rss2-comments.php' );
		} else {
			load_template( plugin_dir_path( __FILE__ ) . 'templates/feed-rss2.php' );
		}
	}

	/**
	 * Adds link to Plugin Page for Options Page.
	 *
	 * @access public
	 * @param array $links Array of Existing Links.
	 * @return array Modified Links.
	 */
	public static function settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=kind_options">Settings</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Loads the Stylesheet for the Plugin.
	 */
	public static function style_load() {
		wp_enqueue_style( 'kind', plugin_dir_url( __FILE__ ) . 'css/kind.min.css', array(), self::$version );
	}

	/**
	 * Loads the Admin Stylesheet for the Plugin.
	 */
	public static function admin_style_load() {
		wp_enqueue_style( 'kind-admin', plugin_dir_url( __FILE__ ) . 'css/kind.admin.min.css', array(), self::$version );
	}

	public static function privacy_declaration() {
		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			$content = __(
				'For responses to URLs, such as responding to a post or article, this site allows the storage of data around the post/article in order to generate a rich
				citation. Items such as author name and image, summary of the text, embed provided by third-party site, etc may be stored and are solely to provide this
				context. We will remove any of this on request.',
				'indieweb-post-kinds'
			);
			wp_add_privacy_policy_content(
				'Post Kinds',
				wp_kses_post( wpautop( $content, false ) )
			);
		}
	}

}

if ( ! function_exists( 'ifset' ) ) {
	/**
	 * If set, return otherwise false.
	 *
	 * @param type $var Check if set.
	 * @return $var|false Return either $var or $return.
	 */
	function ifset( &$var, $return = false ) {

		return isset( $var ) ? $var : $return;
	}
}
