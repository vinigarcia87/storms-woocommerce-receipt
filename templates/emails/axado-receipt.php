<?php
/**
 * Receipt HTML email notification.
 *
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php echo wptexturize( wpautop( $receipt_message ) ); ?>

<p><?php esc_html_e( 'Para referência, segue abaixo os detalhes do seu pedido.', 'wc-storms-receipt' ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text ); ?>

<h2><?php echo esc_html( __( 'Pedido:', 'wc-storms-receipt' ) . ' ' . $order->get_order_number() ); ?></h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Produto', 'wc-storms-receipt' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Quantidade', 'wc-storms-receipt' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Preço', 'wc-storms-receipt' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
            echo wc_get_email_order_items( $order );
		?>
	</tbody>
	<tfoot>
		<?php if ( $totals = $order->get_order_item_totals() ) :
			$i = 0;
			foreach ( $totals as $total ) :
				$i++;
				?>
				<tr>
					<th scope="row" colspan="2" style="text-align: left; border: 1px solid #eee; <?php echo ( 1 == $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo $total['label']; ?></th>
					<td style="text-align: left; border: 1px solid #eee; <?php echo ( 1 == $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo $total['value']; ?></td>
				</tr>
				<?php
			endforeach;
		endif; ?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text ); ?>

<?php

/**
 * Order meta.
 *
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

/**
 * Customer details.
 *
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text );

/**
 * Email footer.
 *
 * @hooked WC_Emails::email_footer() Output the email footer.
 */
do_action( 'woocommerce_email_footer' );
