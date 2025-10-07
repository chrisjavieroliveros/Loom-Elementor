<?php
/**
 * Plugin Name: Loom Elementor Widgets
 * Description: Custom Elementor widgets for embedding Loom videos
 * Version: 1.0.0
 * Author: Chris Javier Oliveros
 * Author URI: https://github.com/chrisjavieroliveros
 * Text Domain: loom-elementor-widgets
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'LOOM_ELEMENTOR_WIDGETS_VERSION', '1.0.0' );
define( 'LOOM_ELEMENTOR_WIDGETS_FILE', __FILE__ );
define( 'LOOM_ELEMENTOR_WIDGETS_PATH', plugin_dir_path( __FILE__ ) );
define( 'LOOM_ELEMENTOR_WIDGETS_URL', plugins_url( '/', __FILE__ ) );

/**
 * Main Loom Elementor Widgets Class
 */
final class Loom_Elementor_Widgets {

	/**
	 * Instance
	 *
	 * @var Loom_Elementor_Widgets The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Loom_Elementor_Widgets An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {
		// Check if Elementor is installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_elementor' ) );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, '3.0.0', '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Register widget category
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_widget_categories' ) );

		// Register widgets
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/**
	 * Admin notice for missing Elementor
	 */
	public function admin_notice_missing_elementor() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'loom-elementor-widgets' ),
			'<strong>' . esc_html__( 'Loom Elementor Widgets', 'loom-elementor-widgets' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'loom-elementor-widgets' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice for minimum Elementor version
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'loom-elementor-widgets' ),
			'<strong>' . esc_html__( 'Loom Elementor Widgets', 'loom-elementor-widgets' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'loom-elementor-widgets' ) . '</strong>',
			'3.0.0'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Add custom Elementor widget category
	 */
	public function add_elementor_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'loom-widgets',
			array(
				'title' => esc_html__( 'Loom Widgets', 'loom-elementor-widgets' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Register Widgets
	 */
	public function register_widgets( $widgets_manager ) {
		// Include widget files
		require_once LOOM_ELEMENTOR_WIDGETS_PATH . 'widgets/loom-video-widget.php';

		// Register widgets
		$widgets_manager->register( new \Loom_Video_Widget() );
	}
}

// Initialize the plugin
Loom_Elementor_Widgets::instance();
