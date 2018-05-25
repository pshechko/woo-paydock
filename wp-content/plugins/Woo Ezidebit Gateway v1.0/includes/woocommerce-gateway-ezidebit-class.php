<?php 

	// Exit if accessed directly
	if ( ! defined( 'WPINC' ) ) exit;

	class WC_Gateway_Ezidebit extends WC_Payment_Gateway {

		/**
		 * Constructor for ezidebit gateway.
		 */
		public function __construct() {
	  
			$this->id                 = 'ezidebit';
			$this->icon               = WC_EZIDEBIT_PLUGIN_URL . 'resources/ezidebit.png';
			$this->has_fields         = false;

			$this->method_title       = __( 'Ezidebit', 'wc-gateway-ezidebit' );
			$this->method_description = __( 'Credit card payment via ezidebit gateway.', 'wc-gateway-ezidebit' );
		  
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables
			$this->title        = 'Ezidebit';
			$this->description  = __( 'Credit card payment via ezidebit gateway.', 'wc-gateway-ezidebit' );

			// refund option
			$this->ezidebit_check_refund_option();
		  
			// Actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'ezidebit_receipt' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'ezidebit_thankyou_page' ) );
		}

		/**
		 * Ezidebit Backend Fields
		 */
		public function init_form_fields() {

			$this->form_fields = apply_filters( 'wc_offline_form_fields', array(

				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'wc-gateway-ezidebit' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Payment', 'wc-gateway-ezidebit' ),
					'default' => 'yes'
				),

				'ezidebit_environment' => array(
					'title'   => __( 'Mode', 'wc-gateway-ezidebit' ),
					'type'    => 'select',
					'options'  =>  array(
							'live' => 'live',
							'sandbox' => 'sandbox',
						),
					'label'   => __( 'Mode' ),
					'description' => __( 'Ezidebit environment, select sandbox for testing and live if you are ready to go.' ),
					'default'     => __( 'sandbox', 'wc-gateway-ezidebit' ),
				),

				'ezidebit_enabled_refund' => array(
					'title'   => __( 'Enable/Disable', 'wc-gateway-ezidebit' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Refunds', 'wc-gateway-ezidebit' ),
					'description' => __( 'If enable, This plugin will allow you process ezidebit refunds.' ),
					'default' => 'no'
				),

				'ezidebit_digital_key' => array(
					'title'       => __( 'Digital Key', 'wc-gateway-ezidebit' ),
					'type'        => 'text',
					'placeholder' => '',
					'description' => __( 'Digital Key supplied to you by Ezidebit to identify your business' ),
					'default'     => __( '8591BFD4-E7C8-4284-84F7-E6C419114FA8', 'wc-gateway-ezidebit' ),
					'desc_tip'    => true,
				),

				'ezidebit_url' => array(
					'title'       => __( 'WebPay Page URL', 'wc-gateway-ezidebit' ),
					'type'        => 'text',
					'placeholder' => '',
					'description' => __( 'You can get the hosted payment page on your ezidebit account. (Ezidebit Dashboard->ADMIN->Web Page Configuration->WebPay Live Page)', 'wc-gateway-ezidebit' ),
					'default'     => __( 'https://simple-business-tech.pay.demo.ezidebit.com.au', 'wc-gateway-ezidebit' ),
					'desc_tip'    => true,
				),

				'title' => array(
					'title'       => __( 'Title', 'wc-gateway-ezidebit' ),
					'type'        => 'text',
					'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'wc-gateway-ezidebit' ),
					'default'     => __( 'Ezidebit', 'wc-gateway-ezidebit' ),
					'desc_tip'    => true,
				),
				
				'description' => array(
					'title'       => __( 'Description', 'wc-gateway-ezidebit' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'wc-gateway-ezidebit' ),
					'default'     => __( 'Credit card payment via ezidebit gateway.', 'wc-gateway-ezidebit' ),
					'desc_tip'    => true,
				),

			) );
		}

		/**
		* This function will indicate if this plugin will support refund
		*/
		function ezidebit_check_refund_option() {
			if( $this->get_option( 'ezidebit_enabled_refund' ) == 'yes' ) {
				$this->supports[] = 'refunds';
			}
		}

		/**
		* This function will process woocommerce cart checkout via ezidebit
		*/
		function process_payment( $order_id ) {
		
			// get order
			$order = wc_get_order( $order_id );

			return array(
				'result' 	=> 'success',
				'redirect'	=> $order->get_checkout_payment_url( true )
			);
		}
 
		/**
		* Ezibit action after checkout, The ezidebit redirection goes here
		*/
		function ezidebit_receipt( $order ) {
			echo $this->ezidebit_receipt_page( $order );
		}

		/**
		* Redirect to ezidebit hosted payment page
		*/
		function ezidebit_receipt_page( $order_id ) {

			global $woocommerce;

			// check if ezidebit payment settings webpay url configured
			if( empty( $this->get_option( 'ezidebit_url' ) ) ) {

				// display an error message
				wc_enqueue_js( '
				$.blockUI({
						message: "' . esc_js( __( 'Unable to redirect to ezidebit payment page. Please contact your administrator.', 'wc-gateway-ezidebit' ) ) . '",
						baseZ: 99999,
						overlayCSS:
						{
							background: "#fff",
							opacity: 0.6
						},
						css: {
							padding:        "20px",
							zindex:         "9999999",
							textAlign:      "center",
							color:          "#555",
							border:         "3px solid #aaa",
							backgroundColor:"#fff",
							cursor:         "wait",
							lineHeight:		"24px",
						}
					});
				' );
				exit();
				
			} else {

				// version compare for compatibility
				$version_compare = version_compare( $woocommerce->version, '3.0.0', '<' );

				$spinner = WC_EZIDEBIT_PLUGIN_URL . 'resources/loading.gif';

				$order = wc_get_order( $order_id );

				$firstname = $version_compare ? $order->billing_first_name : $order->get_billing_first_name();
				$lastname = $version_compare ? $order->billing_last_name : $order->get_billing_last_name();
				$email = $version_compare ? $order->billing_email : $order->get_billing_email();
				$company = $version_compare ? $order->billing_company : $order->get_billing_company();

				$data = array(
					'firstname' => $firstname,
					'lastname' => $lastname,
					'email' => $email,
					'amount' => str_replace(',', '.', $order->get_total()),
					'return_url' => $this->get_return_url( $order ),
					'company' => $company,
					'type' => !empty( $company ) ? 'B' : 'I',
					'payment_ref' => time().'-'.$order_id,
					'ezidebit_url' => $this->get_option( 'ezidebit_url' )
				);

				wc_enqueue_js( '
					$.blockUI({
							message: "' . esc_js( __( 'Thank you for your order. We are now redirecting you to Ezidebit to make payment.', 'wc-gateway-ezidebit' ) ) . '",
							baseZ: 99999,
							overlayCSS:
							{
								background: "#fff",
								opacity: 0.6
							},
							css: {
								padding: "20px",
								zindex: "9999999",
								textAlign: "center",
								color: "#555",
								border: "3px solid #aaa",
								backgroundColor:"#fff",
								cursor: "wait",
								lineHeight: "24px",
								background: "rgba( 255, 255, 255, .8 ) url('.$spinner.') 50% 50% no-repeat"
							}
						});

					// Redirection
					setTimeout(function(){
						document.getElementById("ezidebit-form").submit();
					},1000)

				' );

				ob_start();
		            include( WC_EZIDEBIT_PLUGIN_DIR . '/public/woocommerce-gateway-ezidebit-web-pay.php' );
		            $html = ob_get_contents();
		        ob_end_clean();

		        return $html;
	        }
		}

		/**
		* Handling Ezidebit Response on thankyou page
		*/
		function ezidebit_thankyou_page() {

			global $woocommerce;

			// version compare for compatibility
			$version_compare = version_compare( $woocommerce->version, '3.0.0', '<' );

			// ezidebit response
			$data = $_POST;

			// get ezidebit referrence
			$order_id = explode( "-", $data['PaymentReference'] );

			// get woocommerce order details
			$order = wc_get_order( $order_id[1] );

			// order id
			$order_id = $version_compare ? $order->id : $order->get_id();

			//make sure process thank you page code once
 			if( !empty( get_post_meta( $order_id, 'ezidebit_response' ) ) ) {
 				return;
 			}
 
			// save ezidebit referrence
			update_post_meta( $order_id, 'ezidebit_response', $data );

			// checking payment status
			if( in_array( $data['ResultCode'], array('00', '08', '10', '11', '16', '77', '000', '003') ) ) {

				// add order note if success
				$order->add_order_note( sprintf( __( "Ezidebit transaction completed; Transaction ID = {$data['TransactionID']}", 'wc-gateway-ezidebit' ), $data ) );

				// ** After Payment Reduce Stock ** //

				// reduce stock
				if( $version_compare ) {
					$order->reduce_order_stock();
				} else {
					wc_reduce_stock_levels( $order_id );
				}

				// success transactions
				$order->update_status('processing');

			} else {

				// add order note if success
				$order->add_order_note( sprintf( __( "Ezidebit transaction failed; ResultCode = {$data['ResultCode']} and ResultText = {$data['ResultText']}", 'wc-gateway-ezidebit' ), $data ) );

				// failed transactions
				$order->update_status('failed');
			}

			// empty cart items
			$woocommerce->cart->empty_cart();
		}

		/**
		 * Ezidebit Refunds.
		*/
		function process_refund( $order_id, $amount = null, $reason = '' ) {

			global $woocommerce;

			// version compare for compatibility
			$version_compare = version_compare( $woocommerce->version, '3.0.0', '<' );

			$order = wc_get_order( $order_id );

			// order id
			$order_id = $version_compare ? $order->id : $order->get_id();

			// return error if refund amount is 0
			if ( 0 == $amount || null == $amount ) {

				// return an error
				return new WP_Error( 'ezidebit_refund_error', __( 'Refund Error: You need to specify a refund amount.', 'wc-gateway-ezidebit' ) );
			
			} else if( $amount > $order->get_total() ) {

				// return an error
				return new WP_Error( 'ezidebit_refund_error', __( 'The amount that the refund is to be processed for. This must be less than or equal to the amount originally paid by the payer.', 'wc-gateway-ezidebit' ) );
			}

			// // get ezidebit transaction
			$ezidebit_transaction = get_post_meta( $order_id, 'ezidebit_response' );

			// process ezidebit refund
			$refund = $this->ezidebit_process_refund( $ezidebit_transaction, $amount );

			// handling ezidebit response
		 	if( isset( $refund->ProcessRefundResult->Error ) ) {
				
				// return an error
				return new WP_Error( 'ezidebit_refund_error', __( $refund->ProcessRefundResult->ErrorMessage, 'wc-gateway-ezidebit' ) );
			
			} else if( isset( $refund['error_code'] ) ) {

				// return an error
				return new WP_Error( 'ezidebit_refund_error', __( $refund['error_msg'], 'wc-gateway-ezidebit' ) );

			} else {
				
				// add order note if success
				$order->add_order_note( sprintf( __( "Ezidebit refund completed; RefundPaymentID = {$refund->ProcessRefundResult->Data->RefundPaymentID}", 'wc-gateway-ezidebit' ), $refund ) );
				
				// update ezidebit transaction response
				update_post_meta( $order_id, 'ezidebit_response', $refund );
			}
		}

		/**
		* Process ezidebit response via soap client
		*/
		private function ezidebit_process_refund( $transactions, $amount ) {

	    	try {

				// get ezidebit mode
		 		$mode = $this->get_option( 'ezidebit_environment' );

		 		// contructing ezidebit endpoint url
		    	$mode_url = ( !empty( $mode ) && $mode == 'sandbox' ) ? 'demo.' : '';

	    		// requesting refund to ezidebit api
	    		$client = new SoapClient( "https://api.{$mode_url}ezidebit.com.au/v3-5/nonpci?wsdl", array(
					'trace'        => 1,
					'cache_wsdl'   => WSDL_CACHE_BOTH
				) );

	    		// return ezidebit refund response
				return $client->__soapCall( 'ProcessRefund', array( array(
					'DigitalKey' => trim( $this->get_option( 'ezidebit_digital_key' ) ),
	    			'PaymentID' => $transactions[0]['TransactionID'],
	    			'BankReceiptID' => '',
	    			'RefundAmountInCents' => str_replace( '.','', $amount )
				) ) );

	    	} catch (Exception $e) {
	    		return array(
	    			'error_code' => 0,
	    			'error_msg' => "Could not connect to ezidebit api, Please try again."
	    		);
	    	}

		}

	}

?>