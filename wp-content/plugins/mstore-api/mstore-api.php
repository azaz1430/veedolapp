<?php
/**
 * Plugin Name: Mstore API
 * Plugin URI: https://github.com/inspireui/mstore-api
 * Description: The MStore API Plugin which is used for the Mstore and FluxStore App
 * Version: 2.8.2
 * Author: InspireUI
 * Author URI: http://inspireui.com
 *
 * Text Domain: MStore-Api
 */

defined('ABSPATH') or wp_die( 'No script kiddies please!' );


// use MStoreCheckout\Templates\MDetect;

include plugin_dir_path(__FILE__)."templates/class-mobile-detect.php";
include plugin_dir_path(__FILE__)."templates/class-rename-generate.php";
include_once plugin_dir_path(__FILE__)."controllers/FlutterUser.php";
include_once plugin_dir_path(__FILE__)."controllers/FlutterHome.php";
include_once plugin_dir_path(__FILE__)."controllers/FlutterVendor.php";
include_once plugin_dir_path(__FILE__)."controllers/FlutterBooking.php";
include_once plugin_dir_path(__FILE__)."controllers/FlutterWoo.php";

class MstoreCheckOut
{
    public $version = '2.8.2';

    public function __construct()
    {
        define('MSTORE_CHECKOUT_VERSION', $this->version);
        define('MSTORE_PLUGIN_FILE', __FILE__);
        include_once (ABSPATH . 'wp-admin/includes/plugin.php');
        if (is_plugin_active('woocommerce/woocommerce.php') == false) {
            return 0;
        }

        $path = get_template_directory()."/templates";
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $templatePath = plugin_dir_path(__FILE__)."templates/mstore-api-template.php";
        if (!copy($templatePath,$path."/mstore-api-template.php")) {
            return 0;
        }

        $order = filter_has_var(INPUT_GET, 'code') && strlen(filter_input(INPUT_GET, 'code')) > 0 ? true : false;
        if ($order) {
            add_filter('woocommerce_is_checkout', '__return_true');
        }

        include_once plugin_dir_path(__FILE__)."controllers/MStoreHome.php";

        add_action('wp_print_scripts', array($this, 'handle_received_order_page'));

        //add meta box shipping location in order detail
        add_action( 'add_meta_boxes', 'mv_add_meta_boxes' );
        if ( ! function_exists( 'mv_add_meta_boxes' ) )
        {
            function mv_add_meta_boxes()
            {
                add_meta_box( 'mv_other_fields', __('Shipping Location','woocommerce'), 'mv_add_other_fields_for_packaging', 'shop_order', 'side', 'core' );
            }
        }
        // Adding Meta field in the meta container admin shop_order pages
        if ( ! function_exists( 'mv_add_other_fields_for_packaging' ) )
        {
            function mv_add_other_fields_for_packaging()
            {
                global $post;
                $note = $post->post_excerpt;
                $items = explode("\n", $note);
                if (strpos($items[0],"URL:") !== false) {
                    $url = str_replace("URL:","",$items[0]);
                    echo '<iframe width="600" height="500" src="'.$url.'"></iframe>';
                }
            }
        }

        register_activation_hook( __FILE__, array($this,'create_custom_mstore_table') );

        /**
		 * Prepare data before checkout by webview
		 */
		add_action( 'template_redirect', 'prepare_checkout' );
    }

    public function handle_received_order_page()
    {
        // default return true for getting checkout library working
        if (is_order_received_page()) {
            $detect = new MDetect;
            if ($detect->isMobile()) {
                wp_register_style('mstore-order-custom-style', plugins_url('assets/css/mstore-order-style.css', MSTORE_PLUGIN_FILE));
                wp_enqueue_style('mstore-order-custom-style');
            }
        }

    }
}

$mstoreCheckOut = new MstoreCheckOut();

// use JO\Module\Templater\Templater;
include plugin_dir_path(__FILE__)."wp-templater/src/Templater.php";

add_action('plugins_loaded', 'load_mstore_templater');
function load_mstore_templater()
{

    // add our new custom templates
    $my_templater = new Templater(
        array(
            // YOUR_PLUGIN_DIR or plugin_dir_path(__FILE__)
            'plugin_directory' => plugin_dir_path(__FILE__),
            // should end with _ > prefix_
            'plugin_prefix' => 'plugin_prefix_',
            // templates directory inside your plugin
            'plugin_template_directory' => 'templates',
        )
    );
    $my_templater->add(
        array(
            'page' => array(
                'mstore-api-template.php' => 'Page Custom Template',
            ),
        )
    )->register();
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// Define for the API User wrapper which is based on json api user plugin
///////////////////////////////////////////////////////////////////////////////////////////////////

// if (!is_plugin_active('json-api/json-api.php') && !is_plugin_active('json-api-master/json-api.php')) {
//     // add_action('admin_notices', 'pim_draw_notice_json_api');
//     return;
// }

add_filter('json_api_controllers', 'registerJsonApiController');
add_filter('json_api_mstore_user_controller_path', 'setMstoreUserControllerPath');
add_action('init', 'json_apiCheckAuthCookie', 100);

//custom rest api
function mstore_users_routes() {
    $controller = new FlutterUserController();
    $controller->register_routes();
} 
add_action( 'rest_api_init', 'mstore_users_routes' );
add_action( 'rest_api_init', 'mstore_check_payment_routes' );
function mstore_check_payment_routes() {
    register_rest_route( 'order', '/verify', array(
                    'methods' => 'GET',
                    'callback' => 'mstore_check_payment'
                )
            );
}
function mstore_check_payment() {
    return true;
}



// Add menu Setting
add_action('admin_menu', 'mstore_plugin_setup_menu');

function mstore_plugin_setup_menu(){
        add_menu_page( 'MStore Api', 'MStore Api', 'manage_options', 'mstore-plugin', 'mstore_init' );
}

function mstore_init(){
    load_template( dirname( __FILE__ ) . '/templates/mstore-api-admin-page.php' );
}

function registerJsonApiController($aControllers)
{
    $aControllers[] = 'Mstore_User';
    return $aControllers;
}

function setMstoreUserControllerPath()
{
    return plugin_dir_path(__FILE__) . '/controllers/MstoreUser.php';
}

function json_apiCheckAuthCookie()
{
    global $json_api;

    if (isset($json_api->query) && $json_api->query->cookie) {
        $user_id = wp_validate_auth_cookie($json_api->query->cookie, 'logged_in');
        if ($user_id) {
            $user = get_userdata($user_id);
            wp_set_current_user($user->ID, $user->user_login);
        }
    }
    create_custom_mstore_table();
}

// function add_checkout_page() {
//     deleteDuplicateCheckoutPages();
//     $my_post = array(
//         'post_type' => 'page',
//         'post_name' => 'mstore-checkout',
//         'post_title'    => 'Mstore Checkout',
//         'post_status'   => 'publish',
//         'post_author'   => 1,
//         'post_type'     => 'page'
//     );

//     // Insert the post into the database
//     $page_id = wp_insert_post( $my_post );
//     update_post_meta( $page_id, '_wp_page_template', 'templates/mstore-api-template.php' );
// }

function create_custom_mstore_table() {
    global $wpdb;
    // include upgrade-functions for maybe_create_table;
    if (!function_exists('maybe_create_table')) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    }
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'mstore_checkout';
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        `code` tinytext NOT NULL,
        `order` text NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    $success = maybe_create_table($table_name, $sql);
}

/**
 * Register the mstore caching endpoints so they will be cached.
 */
function wprc_add_mstore_endpoints( $allowed_endpoints ) {
    if ( ! isset( $allowed_endpoints[ 'mstore/v1' ] ) || ! in_array( 'cache', $allowed_endpoints[ 'mstore/v1' ] ) ) {
        $allowed_endpoints[ 'mstore/v1' ][] = 'cache';
    }
    return $allowed_endpoints;
}
add_filter( 'wp_rest_cache/allowed_endpoints', 'wprc_add_mstore_endpoints', 10, 1);

add_filter( 'woocommerce_rest_prepare_product_variation_object','custom_woocommerce_rest_prepare_product_variation_object' );
add_filter( 'woocommerce_rest_prepare_product_object','custom_change_product_response', 20, 3 );
function custom_change_product_response( $response ) {
    global $woocommerce_wpml;

    if ( ! empty( $woocommerce_wpml->multi_currency ) && ! empty( $woocommerce_wpml->settings['currencies_order'] ) ) {

        $type  = $response->data['type'];
        $price = $response->data['price'];

            foreach ( $woocommerce_wpml->settings['currency_options'] as $key => $currency ) {
                $rate = (float)$currency["rate"];
                $response->data['multi-currency-prices'][ $key ]['price'] = $rate == 0 ? $price : sprintf("%.2f", $price*$rate);
            }
    }

    return $response;
}

function custom_woocommerce_rest_prepare_product_variation_object( $response ) {

    global $woocommerce_wpml;

    if ( ! empty( $woocommerce_wpml->multi_currency ) && ! empty( $woocommerce_wpml->settings['currencies_order'] ) ) {

        $type  = $response->data['type'];
        $price = $response->data['price'];

            foreach ( $woocommerce_wpml->settings['currency_options'] as $key => $currency ) {
                $rate = (float)$currency["rate"];
                $response->data['multi-currency-prices'][ $key ]['price'] = $rate == 0 ? $price : sprintf("%.2f", $price*$rate);
            }
    }

    return $response;
}

//Prepare data before checkout by webview
function prepare_checkout() {

    if ( isset( $_GET['mobile'] ) && isset( $_GET['code'] ) ) {

        $code = $_GET['code'];
        global $wpdb;
        $table_name = $wpdb->prefix . "mstore_checkout";
        $item = $wpdb->get_row( "SELECT * FROM $table_name WHERE code = '$code'" );
        if ($item) {
            $data = json_decode(urldecode(base64_decode($item->order)), true);
        }else{
            return var_dump("Can't not get the order");
        }

		$shipping = isset($data['shipping']) ? $data['shipping'] : $data['billing'];
        $billing = isset($data['billing']) ? $data['billing'] : $shipping;
		
        if (isset($data['token'])) {
            // Validate the cookie token
            $userId = wp_validate_auth_cookie($data['token'], 'logged_in');
            if (isset($billing)) {
                update_user_meta($userId, 'billing_first_name', $billing["first_name"]);
                update_user_meta($userId, 'billing_last_name', $billing["last_name"]);
                update_user_meta($userId, 'billing_company', $billing["company"]);
                update_user_meta($userId, 'billing_address_1', $billing["address_1"]);
                update_user_meta($userId, 'billing_address_2', $billing["address_2"]);
                update_user_meta($userId, 'billing_city', $billing["city"]);
                update_user_meta($userId, 'billing_state', $billing["state"]);
                update_user_meta($userId, 'billing_postcode', $billing["postcode"]);
                update_user_meta($userId, 'billing_country', $billing["country"]);
                update_user_meta($userId, 'billing_email', $billing["email"]);
                update_user_meta($userId, 'billing_phone', $billing["phone"]);

                update_user_meta($userId, 'shipping_first_name', $billing["first_name"]);
                update_user_meta($userId, 'shipping_last_name', $billing["last_name"]);
                update_user_meta($userId, 'shipping_company', $billing["company"]);
                update_user_meta($userId, 'shipping_address_1', $billing["address_1"]);
                update_user_meta($userId, 'shipping_address_2', $billing["address_2"]);
                update_user_meta($userId, 'shipping_city', $billing["city"]);
                update_user_meta($userId, 'shipping_state', $billing["state"]);
                update_user_meta($userId, 'shipping_postcode', $billing["postcode"]);
                update_user_meta($userId, 'shipping_country', $billing["country"]);
                update_user_meta($userId, 'shipping_email', $billing["email"]);
                update_user_meta($userId, 'shipping_phone', $billing["phone"]);
            }else{
                $billing = [];
                $shipping = [];

                $billing["first_name"] = get_user_meta($userId, 'billing_first_name', true );
                $billing["last_name"] = get_user_meta($userId, 'billing_last_name', true );
                $billing["company"] = get_user_meta($userId, 'billing_company', true );
                $billing["address_1"] = get_user_meta($userId, 'billing_address_1', true );
                $billing["address_2"] = get_user_meta($userId, 'billing_address_2', true );
                $billing["city"] = get_user_meta($userId, 'billing_city', true );
                $billing["state"] = get_user_meta($userId, 'billing_state', true );
                $billing["postcode"] = get_user_meta($userId, 'billing_postcode', true );
                $billing["country"] = get_user_meta($userId, 'billing_country', true );
                $billing["email"] = get_user_meta($userId, 'billing_email', true );
                $billing["phone"] = get_user_meta($userId, 'billing_phone', true );

                $shipping["first_name"] = get_user_meta($userId, 'shipping_first_name', true );
                $shipping["last_name"] = get_user_meta($userId, 'shipping_last_name', true );
                $shipping["company"] = get_user_meta($userId, 'shipping_company', true );
                $shipping["address_1"] = get_user_meta($userId, 'shipping_address_1', true );
                $shipping["address_2"] = get_user_meta($userId, 'shipping_address_2', true );
                $shipping["city"] = get_user_meta($userId, 'shipping_city', true );
                $shipping["state"] = get_user_meta($userId, 'shipping_state', true );
                $shipping["postcode"] = get_user_meta($userId, 'shipping_postcode', true );
                $shipping["country"] = get_user_meta($userId, 'shipping_country', true );
                $shipping["email"] = get_user_meta($userId, 'shipping_email', true );
                $shipping["phone"] = get_user_meta($userId, 'shipping_phone', true );

                if (isset($billing["first_name"]) && !isset($shipping["first_name"])) {
                    $shipping = $billing;
                }
                if (!isset($billing["first_name"]) && isset($shipping["first_name"])) {
                    $billing = $shipping;
                }
            }
			
            // Check user and authentication
            $user = get_userdata($userId);
            if ($user && (!is_user_logged_in() || get_current_user_id() != $userId)) {
                wp_set_current_user( $userId, $user->user_login );
                wp_set_auth_cookie( $userId );

                header( "Refresh:0" );
            }
        } else {
            if ( is_user_logged_in()) {
                wp_logout();
                wp_set_current_user( 0 );
                header( "Refresh:0" );
            }
        }
        
        global $woocommerce;
        WC()->session->set( 'refresh_totals', true );
        WC()->cart->empty_cart();
        
        $products = $data['line_items'];
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
                        $woocommerce->cart->add_to_cart($productId, $quantity, $variationId, $attribute);
                    }
                }
            } else {
                $woocommerce->cart->add_to_cart($productId, $quantity);
            }
        }

        if(isset($shipping)){
            $woocommerce->customer->set_shipping_first_name($shipping["first_name"]);
            $woocommerce->customer->set_shipping_last_name($shipping["last_name"]);
            $woocommerce->customer->set_shipping_company($shipping["company"]);
            $woocommerce->customer->set_shipping_address_1($shipping["address_1"]);
            $woocommerce->customer->set_shipping_address_2($shipping["address_2"]);
            $woocommerce->customer->set_shipping_city($shipping["city"]);
            $woocommerce->customer->set_shipping_state($shipping["state"]);
            $woocommerce->customer->set_shipping_postcode($shipping["postcode"]);
            $woocommerce->customer->set_shipping_country($shipping["country"]);
        }
        
		if(isset($billing)){
            $woocommerce->customer->set_billing_first_name($billing["first_name"]);
            $woocommerce->customer->set_billing_last_name($billing["last_name"]);
            $woocommerce->customer->set_billing_company($billing["company"]);
            $woocommerce->customer->set_billing_address_1($billing["address_1"]);
            $woocommerce->customer->set_billing_address_2($billing["address_2"]);
            $woocommerce->customer->set_billing_city($billing["city"]);
            $woocommerce->customer->set_billing_state($billing["state"]);
            $woocommerce->customer->set_billing_postcode($billing["postcode"]);
            $woocommerce->customer->set_billing_country($billing["country"]);
            $woocommerce->customer->set_billing_email($billing["email"]);
            $woocommerce->customer->set_billing_phone($billing["phone"]);
        }
        
        if (!empty($data['coupon_lines'])) {
            $coupons = $data['coupon_lines'];
            foreach ($coupons as $coupon) {
                $woocommerce->cart->add_discount($coupon['code']);
            }
        }

        if (!empty($data['shipping_lines'])) {
            $shippingLines = $data['shipping_lines'];
            $shippingMethod = $shippingLines[0]['method_id'];
            WC()->session->set( 'chosen_shipping_methods', array($shippingMethod) );
        }
        if (!empty($data['payment_method'])) {
            WC()->session->set('chosen_payment_method', $data['payment_method']);
        }
    }
}