<?php
/**
* Plugin Name: Storms WooCommerce Receipt
* Plugin URI: https://github.com/vinigarcia87/storms-woocommerce-receipt
* Description: Incluindo campos de Nota Fiscal para WooCommerce by Storms
* Author: Vinicius Garcia
* Author URI: http://storms.com.br/
* Version: 1.0.0
* License: GPLv2 or later
* Text Domain: wc-storms-receipt
* Domain Path: languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Storms_Receipt' ) ) :

    /**
    * WooCommerce Storms Receipt main class
    */
    class WC_Storms_Receipt {
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
            add_action('init', array($this, 'load_plugin_textdomain'), -1);

            // Checks with WooCommerce is installed.
            if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.6.0', '>=' ) ) {

                if ( is_admin() ) {
                    $this->includes();
                }

                // Email manager
                add_filter( 'woocommerce_email_classes', array( $this, 'include_emails' ) );
            }
        }

        /**
         * Return an instance of this class.
         * Singleton instantiation pattern
         *
         * @return object A single instance of this class.
         */
        public static function get_instance() {
            // If the single instance hasn't been set, set it now.
            if ( null === self::$instance ) {
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
            load_plugin_textdomain( 'wc-storms-receipt', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        }

        /**
         * Includes.
         */
        private function includes() {
			include_once dirname(__FILE__) . '/includes/wc-storms-receipt-functions.php';
            include_once dirname(__FILE__) . '/includes/class-wc-storms-order-receipt.php';
            include_once dirname(__FILE__) . '/includes/class-wc-storms-receipt-rest-api.php'; // Integraçao com a WC REST API
        }

        /**
         * Include emails.
         *
         * @param  array $emails Default emails.
         * @return array
         */
        public function include_emails( $emails ) {
            if ( ! isset( $emails['WC_Storms_Receipt_Email'] ) ) {
                $emails['WC_Storms_Receipt_Email'] = include( dirname( __FILE__ ) . '/includes/class-wc-storms-receipt-email.php' );
            }

            return $emails;
        }

    }

    add_action( 'plugins_loaded', array( 'WC_Storms_Receipt', 'get_instance' ) );

endif;
