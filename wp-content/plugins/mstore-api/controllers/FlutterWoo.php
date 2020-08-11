<?php
require_once( __DIR__ . '/FlutterBase.php');
/*
 * Base REST Controller for flutter
 *
 * @since 1.4.0
 *
 * @package shipping
 */

class FlutterWoo extends FlutterBaseController
{
    /**
     * Endpoint namespace
     *
     * @var string
     */
    protected $namespace = 'api/flutter_woo';

    /**
     * Register all routes releated with stores
     *
     * @return void
     */
    public function __construct()
    {
		add_action('rest_api_init', array($this, 'register_flutter_woo_routes'));
    }

    public function register_flutter_woo_routes()
    {
        register_rest_route( $this->namespace,  '/shipping_methods', array(
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'shipping_methods' ),
				'permission_callback' => function () {
                    return parent::checkApiPermission();
                }
			),
        ));

        register_rest_route( $this->namespace,  '/coupon', array(
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'coupon' ),
				'permission_callback' => function () {
                    return parent::checkApiPermission();
                }
			),
        ));
    }
 
    /**
     * Check any prerequisites for our REST request.
     */
    private function check_prerequisites() {
        if ( defined( 'WC_ABSPATH' ) ) {
            // WC 3.6+ - Cart and other frontend functions are not included for REST requests.
            include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
            include_once WC_ABSPATH . 'includes/wc-notice-functions.php';
            include_once WC_ABSPATH . 'includes/wc-template-hooks.php';
        }

        if ( null === WC()->session ) {
            $session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

            WC()->session = new $session_class();
            WC()->session->init();
        }

        if ( null === WC()->customer ) {
            WC()->customer = new WC_Customer( get_current_user_id(), true );
        }

        if ( null === WC()->cart ) {
            WC()->cart = new WC_Cart();
        }else{
            WC()->cart->empty_cart( true );
        }
    }

    /**
	 * Add a product to the cart.
	 *
	 * @throws Exception Plugins can throw an exception to prevent adding to cart.
	 * @param int   $product_id contains the id of the product to add to the cart.
	 * @param int   $quantity contains the quantity of the item to add.
	 * @param int   $variation_id ID of the variation being added to the cart.
	 * @param array $variation attribute values.
	 * @param array $cart_item_data extra cart item data we want to pass into the item.
	 * @return string|bool $cart_item_key
	 */
	public function add_to_cart( $product_id = 0, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data = array() ) {
		try {
			$product_id   = absint( $product_id );
			$variation_id = absint( $variation_id );

			// Ensure we don't add a variation to the cart directly by variation ID.
			if ( 'product_variation' === get_post_type( $product_id ) ) {
				$variation_id = $product_id;
				$product_id   = wp_get_post_parent_id( $variation_id );
			}

			$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
			$quantity     = apply_filters( 'woocommerce_add_to_cart_quantity', $quantity, $product_id );

            if ($quantity <= 0) {
                throw new Exception("The quantity must be a valid number greater than 0");
            }
            if(! $product_data){
                throw new Exception("The product is not found");
            }
            if('trash' === $product_data->get_status()){
                throw new Exception("The product is trash");
            }

			// Load cart item data - may be added by other plugins.
			$cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity );

			// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
			$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

			// Find the cart item key in the existing cart.
			$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

			// Force quantity to 1 if sold individually and check for existing item in cart.
			if ( $product_data->is_sold_individually() ) {
				$quantity      = apply_filters( 'woocommerce_add_to_cart_sold_individually_quantity', 1, $quantity, $product_id, $variation_id, $cart_item_data );
				$found_in_cart = apply_filters( 'woocommerce_add_to_cart_sold_individually_found_in_cart', $cart_item_key && WC()->cart->cart_contents[ $cart_item_key ]['quantity'] > 0, $product_id, $variation_id, $cart_item_data, $cart_id );

				if ( $found_in_cart ) {
					/* translators: %s: product name */
					throw new Exception( sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), __( 'View cart', 'woocommerce' ), sprintf( __( 'You cannot add another "%s" to your cart.', 'woocommerce' ), $product_data->get_name() ) ) );
				}
			}

			if ( ! $product_data->is_purchasable() ) {
				$message = __( 'Sorry, this product cannot be purchased.', 'woocommerce' );
				/**
				 * Filters message about product unable to be purchased.
				 *
				 * @since 3.8.0
				 * @param string     $message Message.
				 * @param WC_Product $product_data Product data.
				 */
				$message = apply_filters( 'woocommerce_cart_product_cannot_be_purchased_message', $message, $product_data );
				throw new Exception( $message );
			}

			// Stock check - only check if we're managing stock and backorders are not allowed.
			if ( ! $product_data->is_in_stock() ) {
				/* translators: %s: product name */
				throw new Exception( sprintf( __( 'You cannot add &quot;%s&quot; to the cart because the product is out of stock.', 'woocommerce' ), $product_data->get_name() ) );
			}

			if ( ! $product_data->has_enough_stock( $quantity ) ) {
				/* translators: 1: product name 2: quantity in stock */
				throw new Exception( sprintf( __( 'You cannot add that amount of &quot;%1$s&quot; to the cart because there is not enough stock (%2$s remaining).', 'woocommerce' ), $product_data->get_name(), wc_format_stock_quantity_for_display( $product_data->get_stock_quantity(), $product_data ) ) );
			}

			// Stock check - this time accounting for whats already in-cart.
			if ( $product_data->managing_stock() ) {
				$products_qty_in_cart = WC()->cart->get_cart_item_quantities();

				if ( isset( $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ] ) && ! $product_data->has_enough_stock( $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ] + $quantity ) ) {
					throw new Exception(
						sprintf(
							'<a href="%s" class="button wc-forward">%s</a> %s',
							wc_get_cart_url(),
							__( 'View cart', 'woocommerce' ),
							/* translators: 1: quantity in stock 2: current quantity */
							sprintf( __( 'You cannot add that amount to the cart &mdash; we have %1$s in stock and you already have %2$s in your cart.', 'woocommerce' ), wc_format_stock_quantity_for_display( $product_data->get_stock_quantity(), $product_data ), wc_format_stock_quantity_for_display( $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ], $product_data ) )
						)
					);
				}
			}

			// If cart_item_key is set, the item is already in the cart.
			if ( $cart_item_key ) {
				$new_quantity = $quantity + WC()->cart->cart_contents[ $cart_item_key ]['quantity'];
				WC()->cart->set_quantity( $cart_item_key, $new_quantity, false );
			} else {
				$cart_item_key = $cart_id;

				// Add item after merging with $cart_item_data - hook to allow plugins to modify cart item.
				WC()->cart->cart_contents[ $cart_item_key ] = apply_filters(
					'woocommerce_add_cart_item',
					array_merge(
						$cart_item_data,
						array(
							'key'          => $cart_item_key,
							'product_id'   => $product_id,
							'variation_id' => $variation_id,
							'variation'    => $variation,
							'quantity'     => $quantity,
							'data'         => $product_data,
							'data_hash'    => wc_get_cart_item_data_hash( $product_data ),
						)
					),
					$cart_item_key
				);
			}

			WC()->cart->cart_contents = apply_filters( 'woocommerce_cart_contents_changed', WC()->cart->cart_contents );

			do_action( 'woocommerce_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data );

			return true;

		} catch ( Exception $e ) {
			if ( $e->getMessage() ) {
                return html_entity_decode(strip_tags($e->getMessage()));
			}
			return false;
		}
    }
    
    private function add_items_to_cart($products, $isValidate = true){
        try {
            foreach ($products as $product) {
                $productId = absint($product['product_id']);
    
                $quantity = $product['quantity'];
                $variationId = isset($product['variation_id']) ? $product['variation_id'] : "";
    
                // Check the product variation
                if (!empty($variationId)) {
                    $productVariable = new WC_Product_Variable($productId);
                    $listVariations = $productVariable->get_available_variations();
                    foreach ($listVariations as $vartiation => $value) {
                        if ($variationId == $value['variation_id']) {
                            $attribute = $value['attributes'];
                            $error = $this->add_to_cart($productId, $quantity, $variationId, $attribute);
                            if ((is_string($error) || $error == false) && $isValidate) {
                                throw new Exception($error);
                            }
                        }
                    }
                } else {
                    $error = $this->add_to_cart($productId, $quantity);
                    if ((is_string($error) || $error == false) && $isValidate) {
                        throw new Exception($error);
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
        
    }

    public function shipping_methods($request){
		$json = file_get_contents('php://input');
        $body = json_decode($json, TRUE);

        $this->check_prerequisites();

        $shipping = $body["shipping"];
        WC()->customer->set_shipping_first_name($shipping["first_name"]);
        WC()->customer->set_shipping_last_name($shipping["last_name"]);
        WC()->customer->set_shipping_company($shipping["company"]);
        WC()->customer->set_shipping_address_1($shipping["address_1"]);
        WC()->customer->set_shipping_address_2($shipping["address_2"]);
        WC()->customer->set_shipping_city($shipping["city"]);
        WC()->customer->set_shipping_state($shipping["state"]);
        WC()->customer->set_shipping_postcode($shipping["postcode"]);
        WC()->customer->set_shipping_country($shipping["country"]);

        $error = $this->add_items_to_cart($body['line_items'], false);
        if(is_string($error)){
            return parent::sendError("invalid_item",$error, 400);
        }

        $shipping_methods =  WC()->shipping->calculate_shipping( WC()->cart->get_shipping_packages() );
        $results = [];
        foreach ($shipping_methods as $shipping_method) {
            $rates = $shipping_method['rates'];
            foreach ($rates as $rate) {
                $results[] = [
                    "id"=>$rate->get_id(),
                    "method_id"=>$rate->get_method_id(),
                    "instance_id"=>$rate->get_instance_id(),
                    "label"=>$rate->get_label(),
                    "cost"=>$rate->get_cost(),
                    "taxes"=>$rate->get_taxes(),
                    "shipping_tax"=>$rate->get_shipping_tax()
                ];
            }
        }
        return $results;
    }

    public function coupon($request){
		$json = file_get_contents('php://input');
        $body = json_decode($json, TRUE);

        $this->check_prerequisites();
        $error = $this->add_items_to_cart($body['line_items']);
        if(is_string($error)){
            return parent::sendError("invalid_item",$error, 400);
        }
        $coupon_code = $body["coupon_code"];

        // Coupons are globally disabled.
		if ( ! wc_coupons_enabled() ) {
			return parent::sendError("invalid_coupon","Coupon is disabled", 400);
		}

		// Sanitize coupon code.
		$coupon_code = wc_format_coupon_code( $coupon_code );

		// Get the coupon.
		$the_coupon = new WC_Coupon( $coupon_code );

		// Prevent adding coupons by post ID.
		if ( $the_coupon->get_code() !== $coupon_code ) {
            $the_coupon->set_code( $coupon_code );
            return parent::sendError("invalid_coupon",$the_coupon->get_coupon_error( WC_Coupon::E_WC_COUPON_NOT_EXIST ), 400);
		}

		// Check it can be used with cart.
		if ( ! $the_coupon->is_valid() ) {
            return parent::sendError("invalid_coupon",html_entity_decode(strip_tags($the_coupon->get_error_message())), 400);
		}

		// Check if applied.
		if ( WC()->cart->has_discount( $coupon_code ) ) {
			WC()->cart->remove_coupons();
		}

		// If its individual use then remove other coupons.
		if ( $the_coupon->get_individual_use() ) {

			foreach ( WC()->cart->applied_coupons as $applied_coupon ) {
				$keep_key = array_search( $applied_coupon, $coupons_to_keep, true );
				if ( false === $keep_key ) {
					WC()->cart->remove_coupon( $applied_coupon );
				} else {
					unset( $coupons_to_keep[ $keep_key ] );
				}
			}

			if ( ! empty( $coupons_to_keep ) ) {
				WC()->cart->applied_coupons += $coupons_to_keep;
			}
		}

        WC()->cart->set_applied_coupons([$coupon_code]);
        WC()->cart->calculate_totals();

        $price = WC()->cart->get_coupon_discount_amount( $the_coupon->get_code(), WC()->cart->display_cart_ex_tax );
        return ["coupon"=>$this->get_formatted_coupon_data($the_coupon), "discount"=>$price  ];
    }

    protected function get_formatted_coupon_data( $object ) {
		$data = $object->get_data();

		$format_decimal = array( 'amount', 'minimum_amount', 'maximum_amount' );
		$format_date    = array( 'date_created', 'date_modified', 'date_expires' );
		$format_null    = array( 'usage_limit', 'usage_limit_per_user', 'limit_usage_to_x_items' );

		// Format decimal values.
		foreach ( $format_decimal as $key ) {
			$data[ $key ] = wc_format_decimal( $data[ $key ], 2 );
		}

		// Format date values.
		foreach ( $format_date as $key ) {
			$datetime              = $data[ $key ];
			$data[ $key ]          = wc_rest_prepare_date_response( $datetime, false );
			$data[ $key . '_gmt' ] = wc_rest_prepare_date_response( $datetime );
		}

		// Format null values.
		foreach ( $format_null as $key ) {
			$data[ $key ] = $data[ $key ] ? $data[ $key ] : null;
		}

		return array(
			'id'                          => $object->get_id(),
			'code'                        => $data['code'],
			'amount'                      => $data['amount'],
			'date_created'                => $data['date_created'],
			'date_created_gmt'            => $data['date_created_gmt'],
			'date_modified'               => $data['date_modified'],
			'date_modified_gmt'           => $data['date_modified_gmt'],
			'discount_type'               => $data['discount_type'],
			'description'                 => $data['description'],
			'date_expires'                => $data['date_expires'],
			'date_expires_gmt'            => $data['date_expires_gmt'],
			'usage_count'                 => $data['usage_count'],
			'individual_use'              => $data['individual_use'],
			'product_ids'                 => $data['product_ids'],
			'excluded_product_ids'        => $data['excluded_product_ids'],
			'usage_limit'                 => $data['usage_limit'],
			'usage_limit_per_user'        => $data['usage_limit_per_user'],
			'limit_usage_to_x_items'      => $data['limit_usage_to_x_items'],
			'free_shipping'               => $data['free_shipping'],
			'product_categories'          => $data['product_categories'],
			'excluded_product_categories' => $data['excluded_product_categories'],
			'exclude_sale_items'          => $data['exclude_sale_items'],
			'minimum_amount'              => $data['minimum_amount'],
			'maximum_amount'              => $data['maximum_amount'],
			'email_restrictions'          => $data['email_restrictions'],
			'used_by'                     => $data['used_by'],
			'meta_data'                   => $data['meta_data'],
		);
	}
}

new FlutterWoo;

