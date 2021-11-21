<?php
/**
 * Plugin Name: Woocommerce Products Importer
 * Plugin URI: https://gitlab.com/intervaro/work-sample-backend
 * Description: Import the WooCommerce Products from a API.This plugin will help to import in bulk and single products. And update signle Products.
 * Version: 1.0.0
 * Author: Intervaro
 * Author URI: https://Intervaro.se
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 */

define('WPI_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
define('WPI_URL', untrailingslashit(plugin_dir_url(__FILE__)));
define('WPI_VERSION', '1.0.0');
define('WPI_DEV_MAIL', 'mazhar4399@gmail.com');
define('WPI_API_BASE_URL', 'https://retoolapi.dev/AqgXOl/intervaro/');


require_once (WPI_PATH . '/core-functions.php');


add_action('plugins_loaded', 'wcp_load_plugin_textdomain');
add_action('admin_menu', 'wpi_plugin_settings');
add_action('admin_enqueue_scripts', 'admin_wpi_scripts');


require_once (WPI_PATH . '/classes/setup.class.php');
require_once (WPI_PATH . '/classes/hooks.class.php');

/**
 * Iniliatizing main class object for setting up product import system
 */
if( class_exists('WPI_Woo_Product_Import')){
    $wpi_ob = new WPI_Woo_Product_Import;
    register_activation_hook( __FILE__, array( 'WPI_Woo_Product_Import', 'wpi_activated' ) );
}

/**
 * Initializing Custom hooks (actions + filters)
 */
if( class_exists('WPI_Hooks')){
    $wpi_hk_ob = new WPI_Hooks;
}


function wpi_deactivation()
{
	//nothing
}

function admin_wpi_scripts($check)
{

    if ( $check == 'toplevel_page_wpi_settings')
    {
        wp_enqueue_script('jquery');
        wp_enqueue_media();
        wp_enqueue_style('wpi-bs', WPI_URL . '/admin/assets/css/bootstrap.min.css',array(), '1.0' );
        wp_enqueue_style('wpi-font-awesome', WPI_URL . '/admin/assets/font-awesome/css/font-awesome.min.css',array(), '1.1' );
        wp_enqueue_style('wpi-insp', WPI_URL . '/admin/assets/css/style.css',array(), '1.0' );
        wp_enqueue_style('wpi-custom', WPI_URL . '/admin/assets/css/custom.css',array(), '1.3');
        wp_enqueue_script('sweet-alerts', WPI_URL . '/admin/assets/js/sweetalert.min.js', array('jquery'));
        wp_enqueue_script('wpi-bootstrap', WPI_URL . '/admin/assets/js/bootstrap.min.js');
        wp_enqueue_script('wpi-LoadingOverla', WPI_URL . '/admin/assets/js/loadingoverlay.min.js');
        //wp_enqueue_script('wpi-custom', WPI_URL . '/admin/assets/js/custom.js');
    }


}

function wpi_plugin_settings()
{
    add_menu_page('Woo Product API Import', __('Products Import', 'woo-product-api-import') , 'administrator', 'wpi_settings', 'wpi_display_settings');
  
}

function wcp_load_plugin_textdomain()
{
    load_plugin_textdomain('woo-product-api-import', false, basename(WPI_PATH) . '/languages/');
}

function wpi_display_settings()
{
    include_once WPI_PATH . '/pages/page-woo-pro-api.php';

}

