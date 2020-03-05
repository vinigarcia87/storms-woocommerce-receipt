<?php
/**
 * Storms Receipt Email.
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
 * Storms Receipt email.
 */
class Storms_WC_Receipt_Email extends WC_Email {

	/**
	 * Initialize receipt template.
	 */
	public function __construct() {
		$this->id               = 'storms_receipt';
		$this->title            = __( 'Notal Fiscal', 'storms' );
		$this->customer_email   = true;
		$this->description      = __( 'Este e-mail é enviado quando uma Notal Fiscal é informada em um pedido.', 'storms' );
		$this->heading          = __( 'A Notal Fiscal do seu pedido está disponível', 'storms' );
		$this->subject          = __( '[{site_title}] Seu pedido {order_number} recebeu uma Notal Fiscal', 'storms' );
		$this->message          = __( 'Olá! O número da Notal Fiscal do seu pedido é: {receipt_number} e a série é {receipt_serie}.', 'storms' )
									. PHP_EOL . ' ' . PHP_EOL
									. __( 'Se você tiver dúvidas ou perguntas, por favor, entre em contato conosco.', 'storms' )
									. PHP_EOL . ' ' . PHP_EOL
									. __( 'Segue abaixo os detalhes do seu pedido:', 'storms' );
		$this->receipt_message = $this->get_option( 'receipt_message', $this->message );
		$this->template_html    = 'emails/storms-receipt.php';
		$this->template_plain   = 'emails/plain/storms-receipt.php';

		// Call parent constructor.
		parent::__construct();

		$this->template_base = WC_Storms_Receipt::get_templates_path();
	}

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'storms' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'storms' ),
				'default' => 'yes',
			),
			'subject' => array(
				'title'       => __( 'Subject', 'storms' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'storms' ), $this->subject ),
				'placeholder' => $this->subject,
				'default'     => '',
				'desc_tip'    => true,
			),
			'heading' => array(
				'title'       => __( 'Email Heading', 'storms' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email. Leave blank to use the default heading: <code>%s</code>.', 'storms' ), $this->heading ),
				'placeholder' => $this->heading,
				'default'     => '',
				'desc_tip'    => true,
			),
			'receipt_message' => array(
				'title'       => __( 'Email Content', 'storms' ),
				'type'        => 'textarea',
				'description' => sprintf( __( 'This controls the initial content of the email. Leave blank to use the default content: <code>%s</code>.', 'storms' ), $this->message ),
				'placeholder' => $this->message,
				'default'     => '',
				'desc_tip'    => true,
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'storms' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'storms' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_custom_email_type_options(),
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Email type options.
	 *
	 * @return array
	 */
	protected function get_custom_email_type_options() {
		if ( method_exists( $this, 'get_email_type_options' ) ) {
			return $this->get_email_type_options();
		}

		$types = array( 'plain' => __( 'Plain text', 'storms' ) );

		if ( class_exists( 'DOMDocument' ) ) {
			$types['html']      = __( 'HTML', 'storms' );
			$types['multipart'] = __( 'Multipart', 'storms' );
		}

		return $types;
	}

	/**
	 * Get email receipt message.
	 *
	 * @return string
	 */
	public function get_receipt_message() {
		return apply_filters( 'woocommerce_storms_email_receipt_message', $this->format_string( $this->receipt_message ), $this->object );
	}

	/**
	 * Trigger email.
	 *
	 * @param  WC_Order $order         Order data.
	 * @param  string   $receipt_number The receipt number.
	 * @param  string   $receipt_serie The receipt serie.
	 */
	public function trigger( $order, $receipt_number = '', $receipt_serie = '' ) {
		// Get the order object while resending emails.
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		$placeholders = array();
		if ( is_object( $order ) ) {
			$this->object    = $order;
			$this->recipient = $this->object->get_billing_email();


			$placeholders['{order_number}'] = $this->object->get_order_number();

			$placeholders['{date}'] = date_i18n( wc_date_format(), time() );

			if ( empty( $receipt_number ) || empty( $receipt_serie ) ) {
				$receipt_number = get_post_meta( $this->object->get_id(), _storms_wc_receipt_number(), true );
				$receipt_serie = get_post_meta( $this->object->get_id(), _storms_wc_receipt_serie(), true );
			}

			$placeholders['{receipt_number}'] = $receipt_number;
			$placeholders['{receipt_serie}'] = $receipt_serie;
		}

		$this->placeholders = array_merge($placeholders, $this->placeholders);

		if ( ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get content HTML.
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();

		wc_get_template( $this->template_html, array(
			'order'            => $this->object,
			'email_heading'    => $this->get_heading(),
			'receipt_message' => $this->get_receipt_message(),
			'sent_to_admin'    => false,
			'plain_text'       => false,
		), '', $this->template_base );

		return ob_get_clean();
	}

	/**
	 * Get content plain text.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();

		wc_get_template( $this->template_plain, array(
			'order'            => $this->object,
			'email_heading'    => $this->get_heading(),
			'receipt_message' => $this->get_receipt_message(),
			'sent_to_admin'    => false,
			'plain_text'       => true,
		), '', $this->template_base );

		return ob_get_clean();
	}
}

return new Storms_WC_Receipt_Email();
