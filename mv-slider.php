<?php

/**
 * Plugin Name: MV Slider
 * Plugin URI: https://www.wordpress.org/mv-slider
 * Description: My Plugin descriptions
 * Version: 1.0
 * Requires at least: 5.6
 * Author: Lucas Vinicius (admin)
 * Author URI: https://www.codigowp.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mv-slider
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('MV_SLIDER')) {



    class MV_Slider{


        function __construct(){
            $this->define_constants();

            add_action('admin_menu', array($this,'add_menu'));

            require_once(MV_SLIDER_PATH .'class.mv-slider-settings.php');
            require_once(MV_SLIDER_PATH . 'post-types/class.mv-slider-cpt.php');
            $MV_Slider_Post_Type = new MV_Slider_Post_Type();
            $MV_Slider_Settings =  new MV_Slider_Settings();

        }

        public function define_constants(){
            define('MV_SLIDER_PATH', plugin_dir_path(__FILE__));
            define('MV_SLIDER_URL', plugin_dir_url(__FILE__));
            define('MV_SLIDER_VERSION', '1.0.0');
        }

        public static function activate(){
            update_option('rewrite_rules', '');
        }

        public static function deactivate(){
            flush_rewrite_rules();
            unregister_post_type( 'mv-slider' );
        }

        public static function uninstall(){

        }

        public function add_menu(){
            add_menu_page(
                'MV Slider Options',
                'MV Slider',
                'manage_options',
                'mv_slider_admin',
                array($this, 'mv_slider_settings_page'),
                'dashicons-images-alt2'
            );

            add_submenu_page(
                'mv_slider_admin',
                'Manage Slides',
                'Manage Slides',
                'manage_options',
                'edit.php?post_type=mv-slider',
                null,
                null
            );

            add_submenu_page(
                'mv_slider_admin',
                'add New Slide',
                'add New Slide',
                'manage_options',
                'post-new.php?post_type=mv-slider',
                null,
                null
            );
        }

        public function mv_slider_settings_page(){
            if (! current_user_can('manage_options')){
                return;
            }
            if(isset($_GET['settings-updated'])){
                add_settings_error('mv_slider_options', 'mv_slider_message', 'Settings Saved', 'success');
            }
            settings_errors('mv_slider_options');
            require(MV_SLIDER_PATH . 'views/settings-page.php');
        }

        public function get_kanye_quote(){
            $response = wp_remote_get('https://api.kanye.rest');
            if ( is_wp_error( $response ) ){
                return;
            }

            $quote = json_decode(wp_remote_retrieve_body($response));
            if (! empty($quote)){
                return $quote->quote;
            }

        }

    }
}

if (class_exists('MV_Slider')) {
    register_activation_hook(__FILE__, array('MV_Slider', 'activate'));
    register_deactivation_hook(__FILE__, array('MV_Slider', 'deactivate'));
    register_uninstall_hook(__FILE__, array('MV_Slider', 'uninstall'));
    $mv_slider = new MV_Slider();
}

