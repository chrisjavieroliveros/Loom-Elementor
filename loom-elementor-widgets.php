<?php
/**
 * Plugin Name: Loom Elementor Widgets
 * Plugin URI: https://github.com/chrisjavieroliveros/Loom-Elementor-Widgets-
 * Description: A WordPress plugin containing custom Elementor widgets for embedding Loom videos and other features.
 * Version: 1.0.0
 * Author: Chris Javier Oliveros
 * Author URI: https://github.com/chrisjavieroliveros
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: loom-elementor-widgets
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Elementor tested up to: 3.16.0
 * Elementor Pro tested up to: 3.16.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Loom Elementor Widgets Class
 *
 * The main class that initiates and runs the plugin.
 */
final class Loom_Elementor_Widgets {

	/**
	 * Plugin Version
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

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
	 *
	 * Perform some compatibility checks to make sure basic requirements are meet.
	 * If all compatibility checks pass, initialize the functionality.
	 */
	public function __construct() {
		if ( $this->is_compatible() ) {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}
	}

	/**
	 * Compatibility Checks
	 *
	 * Checks whether the site meets the plugin requirement.
	 */
	public function is_compatible() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return false;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return false;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return false;
		}

		return true;
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 */
	public function init() {
		// Add Plugin actions
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_widget_categories' ) );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 */
	public function admin_notice_missing_main_plugin() {
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
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
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
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'loom-elementor-widgets' ),
			'<strong>' . esc_html__( 'Loom Elementor Widgets', 'loom-elementor-widgets' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'loom-elementor-widgets' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Add Elementor Widget Categories
	 *
	 * Register new widget categories for Loom Elementor Widgets.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
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
	 *
	 * Register new Elementor widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		// Include Widget files
		require_once( __DIR__ . '/widgets/loom-video-widget.php' );

		// Register widget
		$widgets_manager->register( new \Loom_Video_Widget() );
	}
}

Loom_Elementor_Widgets::instance();
