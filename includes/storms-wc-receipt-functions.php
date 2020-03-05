<?php
/**
 * Storms Receipt Functions
 *
 * @author    Vinicius Garcia | storms@storms.com.br
 * @copyright (c) Copyright 2012-2018, Storms Websolutions
 * @license   GPLv2 - GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package   Storms
 * @version   1.0.0
 */

function storms_wc_receipt_number() {
	return 'storms_wc_receipt_number';
}

function _storms_wc_receipt_number() {
	return '_' . storms_wc_receipt_number();
}

function storms_wc_receipt_serie() {
	return 'storms_wc_receipt_serie';
}

function _storms_wc_receipt_serie() {
	return '_' . storms_wc_receipt_serie();
}

function storms_wc_receipt_key() {
    return 'storms_wc_receipt_key';
}

function _storms_wc_receipt_key() {
    return '_' . storms_wc_receipt_key();
}

/**
 * Update receipt number.
 *
 * @param  int    $order_id       Order ID.
 * @param  string $receipt_number Receipt Number.
 * @return bool
 */
function wc_storms_update_receipt_number( $order_id, $receipt_number ) {
	$receipt_number = sanitize_text_field( $receipt_number );
	$current       = get_post_meta( $order_id, _storms_wc_receipt_number(), true );

	if ( '' !== $receipt_number && $receipt_number !== $current ) {
		update_post_meta( $order_id, _storms_wc_receipt_number(), $receipt_number );

		// Gets order data.
		$order = wc_get_order( $order_id );

		// Add order note.
		$order->add_order_note( sprintf( __( 'Adicionado o Número da Nota Fiscal: %s', 'storms' ), $receipt_number ) );

		return true;
	} elseif ( '' === $receipt_number ) {
		delete_post_meta( $order_id, _storms_wc_receipt_number() );

		return true;
	}

	return false;
}

/**
 * Update receipt serie.
 *
 * @param  int    $order_id      Order ID.
 * @param  string $receipt_serie Receipt Serie.
 * @return bool
 */
function wc_storms_update_receipt_serie( $order_id, $receipt_serie ) {
	$receipt_serie = sanitize_text_field( $receipt_serie );
	$current       = get_post_meta( $order_id, _storms_wc_receipt_serie(), true );

	if ( '' !== $receipt_serie && $receipt_serie !== $current ) {
		update_post_meta( $order_id, _storms_wc_receipt_serie(), $receipt_serie );

		// Gets order data.
		$order = wc_get_order( $order_id );

		// Add order note.
		$order->add_order_note( sprintf( __( 'Adicionado a Série da Nota Fiscal: %s', 'storms' ), $receipt_serie ) );

		return true;
	} elseif ( '' === $receipt_serie ) {
		delete_post_meta( $order_id, _storms_wc_receipt_serie() );

		return true;
	}

	return false;
}

/**
 * Update receipt key.
 *
 * @param  int    $order_id      Order ID.
 * @param  string $receipt_key Receipt Key.
 * @return bool
 */
function wc_storms_update_receipt_key( $order_id, $receipt_key ) {
    $receipt_key = sanitize_text_field( $receipt_key );
    $current       = get_post_meta( $order_id, _storms_wc_receipt_key(), true );

    if ( '' !== $receipt_key && $receipt_key !== $current ) {
        update_post_meta( $order_id, _storms_wc_receipt_key(), $receipt_key );

        // Gets order data.
        $order = wc_get_order( $order_id );

        // Add order note.
        $order->add_order_note( sprintf( __( 'Adicionado a Chave da Nota Fiscal: %s', 'storms' ), $receipt_key ) );

        return true;
    } elseif ( '' === $receipt_key ) {
        delete_post_meta( $order_id, _storms_wc_receipt_key() );

        return true;
    }

    return false;
}

/**
 * Trigger receipt email notification.
 *
 * @param WC_Order $order         Order data.
 * @param string   $receipt_number The receipt number.
 * @param string   $receipt_serie The receipt serie.
 */
function wc_storms_trigger_receipt_email( $order, $receipt_number, $receipt_serie ) {
	$mailer       = WC()->mailer();
	$notification = $mailer->emails['Storms_WC_Receipt_Email'];

	if ( 'yes' === $notification->enabled ) {
		$notification->trigger( $order, $receipt_number, $receipt_serie );
	}
}
