<?php
/**
 * Storms Order Receipt
 *
 * @author    Vinicius Garcia | storms@storms.com.br
 * @copyright (c) Copyright 2012-2018, Storms Websolutions
 * @license   GPLv2 - GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package   Storms
 * @version   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Storms Order Receipt
 * Adicionando Numero e serie da nota fiscal
 */
class WC_Storms_Order_Receipt {
    /**
     * Initialize the order actions.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'register_receipt_metabox' ) );
        add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_receipt' ) );
        add_filter( 'woocommerce_resend_order_emails_available', array( $this, 'resend_receipt_email' ) );
    }

    /**
     * Register Axado metabox.
     */
    public function register_receipt_metabox() {
        add_meta_box(
            'wc_storms_receipt',
			esc_html__( 'Nota Fiscal', 'wc-storms-receipt' ),
            array( $this, 'metabox_content' ),
            'shop_order',
            'side',
            'default'
        );
    }

    /**
     * Storms metabox content.
     * Adiciona campo para a nota fiscal na tela do pedido
	 *
     * @param WC_Post $post Post data.
     */
    public function metabox_content( $post ) {
		echo '<label for="storms_receiptNumber">' . esc_html__( 'Número da Nota Fiscal:', 'wc-storms-receipt' ) . '</label><br />';
		echo '<input type="text" id="storms_receiptNumber" name="storms_receiptNumber" value="' . esc_attr( get_post_meta( $post->ID, _storms_receipt_number(), true ) ) . '" style="width: 100%;" />';

		echo '<label for="storms_receiptSerie">' . esc_html__( 'Série da Nota Fiscal:', 'wc-storms-receipt' ) . '</label><br />';
		echo '<input type="text" id="storms_receiptSerie" name="storms_receiptSerie" value="' . esc_attr( get_post_meta( $post->ID, _storms_receipt_serie(), true ) ) . '" style="width: 100%;" />';

        echo '<label for="storms_receiptKey">' . esc_html__( 'Chave da Nota Fiscal:', 'wc-storms-receipt' ) . '</label><br />';
        echo '<input type="text" id="storms_receiptKey" name="storms_receiptKey" value="' . esc_attr( get_post_meta( $post->ID, _storms_receipt_key(), true ) ) . '" style="width: 100%;" />';
    }

    /**
     * Save Storms receipt info.
	 * Salva a nota fiscal na tela do pedido
     *
     * @param int $post_id Current post type ID.
     */
    public function save_receipt( $post_id ) {
        if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) {
            return;
        }

        if ( isset( $_POST['storms_receiptNumber'] ) ) {
            wc_storms_update_receipt_number( $post_id, wp_unslash( $_POST['storms_receiptNumber'] ) );
        }

		if ( isset( $_POST['storms_receiptSerie'] ) ) {
			wc_storms_update_receipt_serie( $post_id, wp_unslash( $_POST['storms_receiptSerie'] ) );
		}

        if ( isset( $_POST['storms_receiptKey'] ) ) {
            wc_storms_update_receipt_key( $post_id, wp_unslash( $_POST['storms_receiptKey'] ) );
        }
    }

    /**
     * Include option to resend the tracking code email.
     *
     * @param array $emails List of emails.
     *
     * @return array
     */
    public function resend_receipt_email( $emails ) {
        $emails[] = 'storms_receipt';

        return $emails;
    }
}

new WC_Storms_Order_Receipt();
