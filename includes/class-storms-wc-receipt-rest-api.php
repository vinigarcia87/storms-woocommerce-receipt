<?php
/**
 * Storms Receipt REST API
 *
 * @author    Vinicius Garcia | storms@storms.com.br
 * @copyright (c) Copyright 2012-2018, Storms Websolutions
 * @license   GPLv2 - GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package   Storms
 * @version   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Storms_Receipt_REST_API class.
 * Integracao dos campos de Nota Fiscal com a REST API
 */
class Storms_WC_Receipt_REST_API {

	/**
	 * Init REST API actions.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_receipt_fields' ), 100 );
	}

	/**
	 * Register tracking code field in WP REST API.
	 */
	public function register_receipt_fields() {
		if ( ! function_exists( 'register_rest_field' ) ) {
			return;
		}

		register_rest_field( 'shop_order',
			storms_wc_receipt_number(),
			array(
				'get_callback'    => array( $this, 'get_receipt_number_callback' ),
				'update_callback' => array( $this, 'update_receipt_number_callback' ),
				'schema'          => array(
					'description' => __( 'Número da Nota Fiscal', 'storms' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
			)
		);

		register_rest_field( 'shop_order',
			storms_wc_receipt_serie(),
			array(
				'get_callback'    => array( $this, 'get_receipt_serie_callback' ),
				'update_callback' => array( $this, 'update_receipt_serie_callback' ),
				'schema'          => array(
					'description' => __( 'Série da Nota Fiscal', 'storms' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
			)
		);

        register_rest_field( 'shop_order',
            storms_wc_receipt_key(),
            array(
                'get_callback'    => array( $this, 'get_receipt_key_callback' ),
                'update_callback' => array( $this, 'update_receipt_key_callback' ),
                'schema'          => array(
                    'description' => __( 'Chave da Nota Fiscal', 'storms' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
            )
        );
	}

	/**
	 * Get receipt number callback.
	 *
	 * @param array           $data    Details of current response.
	 * @param string          $field   Name of field.
	 * @param WP_REST_Request $request Current request.
	 *
	 * @return string
	 */
	function get_receipt_number_callback( $data, $field, $request ) {
		return get_post_meta( $data['id'], _storms_wc_receipt_number(), true );
	}

	/**
	 * Update receipt number callback.
	 *
	 * @param string  $value  The value of the field.
	 * @param WP_Post $object The object from the response.
	 *
	 * @return bool
	 */
	function update_receipt_number_callback( $value, $object ) {
		if ( ! $value || ! is_string( $value ) ) {
			return;
		}

		return wc_storms_update_receipt_number( $object->ID, $value );
	}

	/**
	 * Get receipt serie callback.
	 *
	 * @param array           $data    Details of current response.
	 * @param string          $field   Name of field.
	 * @param WP_REST_Request $request Current request.
	 *
	 * @return string
	 */
	function get_receipt_serie_callback( $data, $field, $request ) {
		return get_post_meta( $data['id'], _storms_wc_receipt_serie(), true );
	}

	/**
	 * Update receipt serie callback.
	 *
	 * @param string  $value  The value of the field.
	 * @param WP_Post $object The object from the response.
	 *
	 * @return bool
	 */
	function update_receipt_serie_callback( $value, $object ) {
		if ( ! $value || ! is_string( $value ) ) {
			return;
		}

		return wc_storms_update_receipt_serie( $object->ID, $value );
	}

    /**
     * Get receipt key callback.
     *
     * @param array           $data    Details of current response.
     * @param string          $field   Name of field.
     * @param WP_REST_Request $request Current request.
     *
     * @return string
     */
    function get_receipt_key_callback( $data, $field, $request ) {
        return get_post_meta( $data['id'], _storms_wc_receipt_key(), true );
    }

    /**
     * Update receipt key callback.
     *
     * @param string  $value  The value of the field.
     * @param WP_Post $object The object from the response.
     *
     * @return bool
     */
    function update_receipt_key_callback( $value, $object ) {
        if ( ! $value || ! is_string( $value ) ) {
            return;
        }

        return wc_storms_update_receipt_key( $object->ID, $value );
    }
}

new Storms_WC_Receipt_REST_API();
