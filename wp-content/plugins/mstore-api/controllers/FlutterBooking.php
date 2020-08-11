<?php
require_once( __DIR__ . '/FlutterBase.php');
/*
 * Base REST Controller for flutter
 *
 * @since 1.4.0
 *
 * @package booking
 */

class FlutterBooking extends FlutterBaseController
{
    /**
     * Endpoint namespace
     *
     * @var string
     */
    protected $namespace = 'api/flutter_booking';

    /**
     * Register all routes releated with stores
     *
     * @return void
     */
    public function __construct()
    {
		add_action('rest_api_init', array($this, 'register_flutter_booking_routes'));
    }

    public function register_flutter_booking_routes()
    {
        register_rest_route( $this->namespace,  '/checkout', array(
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'checkout' ),
				'permission_callback' => function () {
                    return parent::checkApiPermission();
                }
			),
        ));
    }
 
    public function checkout()
    {
        $json = file_get_contents('php://input');
        $params = json_decode($json, TRUE);
        if( !is_plugin_active( 'woocommerce-appointments/woocommerce-appointments.php' ) ) {
            return parent::sendError("invalid_plugin","You need to install WooCommerce Appointments plugin to use this api", 404);
        }

        //get order info
        $order = wc_get_order( $params["order_id"]);
        if($order){
            $order_items = $order->get_items();
            $orderItemId = 0;
            foreach ($order_items as $order_item_id => $order_item) { 
                if ($order_item->get_product()->get_id() == $params["product_id"]) {
                    $orderItemId = $order_item_id;
                }
            }
            //create appointment
            $params["add-to-cart"] = $params["product_id"];
            $params["customer_id"] = $order->get_customer_id();
            $params["order_item_id"] = $orderItemId;
            return $this->add_cart_item_data($params, $params["product_id"]);
        }else{
            return parent::sendError("invalid_order","The order is not found", 400);
        }
        
    }

    /**
	 * Add posted data to the cart item
	 *
	 * @param mixed $cart_item_meta
	 * @param mixed $product_id
	 * @return array $cart_item_meta
	 */
	private function add_cart_item_data( $params, $product_id ) {
        $cart_item_meta = [];

		$product = wc_get_product( $product_id );

		if ( ! is_wc_appointment_product( $product ) ) {
			return $cart_item_meta;
		}

		$cart_item_meta['appointment']          = wc_appointments_get_posted_data( $params, $product );
		$cart_item_meta['appointment']['_cost'] = WC_Appointments_Cost_Calculation::calculate_appointment_cost( $params, $product );

		if ( $cart_item_meta['appointment']['_cost'] instanceof WP_Error ) {
            return parent::sendError("invalid_data",$cart_item_meta['appointment']['_cost']->get_error_message(), 400);
		}

        $cart_item_meta['appointment']["_customer_id"] = $params["customer_id"];
        $cart_item_meta['appointment']["_order_id"] = $params["order_id"];
        $cart_item_meta['appointment']["_order_item_id"] = $params["order_item_id"];
        if($params["staff_ids"]){
            if(count($params["staff_ids"]) == 1){
                $cart_item_meta['appointment']["_staff_id"] = $params["staff_ids"][0];
            }
            if(count($params["staff_ids"]) > 1){
                $cart_item_meta['appointment']["_staff_ids"] = $params["staff_ids"];
            }
        }
        
		// Create the new appointment
		$new_appointment = $this->add_appointment_from_cart_data( $cart_item_meta, $product_id );

		// Store in cart
		$cart_item_meta['appointment']['_appointment_id'] = $new_appointment->get_id();

		return $cart_item_meta;
    }
    
    /**
	 * Create appointment from cart data
	 *
	 * @param        $cart_item_meta
	 * @param        $product_id
	 * @param string $status
	 *
	 * @return object
	 */
	private function add_appointment_from_cart_data( $cart_item_meta, $product_id, $status = 'unpaid' ) {
		// Create the new appointment
		$new_appointment_data = array(
			'product_id' => $product_id, // Appointment ID
			'cost'       => $cart_item_meta['appointment']['_cost'], // Cost of this appointment
			'start_date' => $cart_item_meta['appointment']['_start_date'],
			'end_date'   => $cart_item_meta['appointment']['_end_date'],
			'all_day'    => $cart_item_meta['appointment']['_all_day'],
			'qty'        => $cart_item_meta['appointment']['_qty'],
            'timezone'   => $cart_item_meta['appointment']['_timezone'],
            'customer_id'   => $cart_item_meta['appointment']['_customer_id'],
            'order_id'   => $cart_item_meta['appointment']['_order_id'],
            'order_item_id'   => $cart_item_meta['appointment']['_order_item_id'],
		);

		// Check if the appointment has staff
		if ( isset( $cart_item_meta['appointment']['_staff_id'] ) ) {
			$new_appointment_data['staff_id'] = $cart_item_meta['appointment']['_staff_id']; // ID of the staff
		}

		// Pass all staff selected
		if ( isset( $cart_item_meta['appointment']['_staff_ids'] ) ) {
			$new_appointment_data['staff_ids'] = $cart_item_meta['appointment']['_staff_ids']; // IDs of the staff
		}

        $new_appointment = get_wc_appointment( $new_appointment_data );
		$new_appointment->create( $status );

		return $new_appointment;
	}
    
}

new FlutterBooking;