<?php
/**
 * Plugin Name: Storms WooCommerce Receipt
 * Plugin URI: https://github.com/vinigarcia87/storms-woocommerce-receipt
 * Description: Incluindo campos de Nota Fiscal no WooCommerce - Adiciona Numero, Serie e Chave da nota fiscal nos pedidos
 * Author: Storms Websolutions - Vinicius Garcia
 * Author URI: http://storms.com.br/
 * Copyright: (c) Copyright 2012-2020, Storms Websolutions
 * License: GPLv2 - GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * Version: 1.0
 *
 * WC requires at least: 3.9.2
 * WC tested up to: 3.9.2
 *
 * Text Domain: storms
 * Domain Path: /languages
*/

if( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	/**
	 * WooCommerce Storms Receipt main class
	 */
	class Storms_WC_Receipt
	{
		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		const VERSION = '1.0.0';

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin public actions.
		 */
		private function __construct() {

			// Loading plugin text domain
			add_action( 'init', array( $this, 'load_plugin_textdomain' ), -1 );

			include_once dirname( __FILE__ ) . '/includes/storms-wc-receipt-functions.php';
			include_once dirname( __FILE__ ) . '/includes/class-storms-wc-order-receipt.php';
			include_once dirname( __FILE__ ) . '/includes/class-storms-wc-receipt-rest-api.php'; // Integração com a WC REST API

			// Email Manager
			add_filter( 'woocommerce_email_classes', array( $this, 'include_emails' ) );
		}

		/**
		 * Return an instance of this class.
		 * Singleton instantiation pattern
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Get plugin path.
		 *
		 * @return string
		 */
		public static function get_plugin_path() {
			return plugin_dir_path( __FILE__ );
		}

		/**
		 * Get templates path.
		 *
		 * @return string
		 */
		public static function get_templates_path() {
			return self::get_plugin_path() . 'templates/';
		}

		/**
		 * Load the plugin text domain for translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'storms', false, dirname( plugin_basename( __FILE__ ) )  . '/languages/' );
		}

		/**
		 * Include emails.
		 *
		 * @param array $emails Default emails.
		 * @return array
		 */
		public function include_emails($emails) {
			if( ! isset( $emails['Storms_WC_Receipt_Email'] ) ) {
				$emails['Storms_WC_Receipt_Email'] = include( dirname( __FILE__ ) . '/includes/class-storms-wc-receipt-email.php' );
			}

			return $emails;
		}

	}

	add_action( 'plugins_loaded', array( 'Storms_WC_Receipt', 'get_instance' ) );

}
