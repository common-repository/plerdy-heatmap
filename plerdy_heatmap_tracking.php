<?php
/*
Plugin Name: Plerdy
Plugin URI: https://www.plerdy.com
Description: The easiest way to add the Plerdy tracking script to your WordPress site!
Version: 1.4.4
Author: Plerdy
Author URI: https://www.plerdy.com
License: GPL
*/

/*  Copyright 2017  Plerdy  (email: hello@plerdy.com)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License v2 as published by
		the Free Software Foundation.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


class Plerdy {
    var $longName = 'Plerdy for WordPress Options';
    var $shortName = 'Plerdy';
    var $uniqueID = 'plerdy_heatmap';


    function __construct() {
        register_deactivation_hook(__FILE__, array( $this, 'delete_option' ) );
        add_action( 'wp_head', array( $this, 'add_abtracking_script' ), 1 );
        add_action( 'wp_footer', array( $this, 'add_script' ), 1 );
        add_action( 'woocommerce_thankyou', array( $this, 'myscript' ), 1 );
        add_action( 'admin_footer', array( $this, 'style_plerdy' ), 31 );
        if ( is_admin() ) {
            add_action( 'admin_menu', array( $this, 'admin_menu_page' ) );
            add_action( 'admin_init', array( $this, 'register_settings' ) );
            add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ), array( $this, 'add_settings_link' ) );
        }
    }

    public function delete_option($order_id) {
        delete_option('plerdy_tracking_script');
        delete_option('plerdy_abtracking_script');
    }

    public function myscript($order_id){
        $order = new WC_Order($order_id);
        if (get_option('checkbox') == 'checked'){
            ?>
            <script type="text/javascript" defer>
                var plerdysendData = {'type':'commerce', 'data':{'order_id':'<?php echo $order->get_order_number(); ?>', 'money':'<?php echo $order->get_total(); ?>' }}
            </script>
            <?php
        }
    }

    public function add_script() {
        echo get_option('plerdy_tracking_script');

    }

    public function add_abtracking_script() {
        echo get_option('plerdy_abtracking_script');
    }

    public function admin_menu_page() {
        add_menu_page(
            $this->longName,
            $this->shortName,
            'administrator',
            $this->uniqueID,
            array( $this, 'admin_options'),
            plugins_url('images/icon.png', __FILE__)
        );
    }

    public function register_settings() {
        register_setting( 'plerdy-options', 'plerdy_tracking_script', array( $this, 'validate_tracking_script' ) );
        register_setting( 'plerdy-options', 'plerdy_abtracking_script', array( $this, 'validate_abtracking_script' ) );
        register_setting( 'plerdy-options', 'checkbox' );
    }

    public function validate_tracking_script( $input ) {
        // regular expression for validation
        $pattern = "/^" .
            preg_quote("<!-- BEGIN PLERDY CODE -->", '/') .
            "\s*<script type=\"text\/javascript\" defer data-plerdy_code='1'>\s*" .
            "var _protocol=\"https:\"==document\.location\.protocol\?\"https:\/\/\":\"http:\/\/\";\s*" .
            "_site_hash_code = \"([^\"]*)\",_suid=([^;\s<>]*),\s*" .
            "plerdyScript=document\.createElement\(\"script\"\);\s*" .
            "plerdyScript\.setAttribute\(\"defer\",\"\"\),plerdyScript\.dataset\.plerdymainscript=\"plerdymainscript\",\s*" .
            "plerdyScript\.src=\"https:\/\/[a-z]\.plerdy\.com\/public\/js\/click\/main\.js\?v=\"\+Math\.random\(\);\s*" .
            "var plerdymainscript=document\.querySelector\(\"\\[data-plerdymainscript='plerdymainscript'\\]\"\);\s*" .
            "plerdymainscript&&plerdymainscript\.parentNode\.removeChild\(plerdymainscript\);\s*" .
            "try{document\.head\.appendChild\(plerdyScript\)}catch\(t\){console\.log\(t,\"unable add script tag\"\)}\s*" .
            "<\/script>\s*" .
            preg_quote("<!-- END PLERDY CODE -->", '/') .
            "$/";




        if (preg_match($pattern, $input) || empty($input)) {
            return $input;
        } else {
            // If validation fails, return the default value
            add_settings_error( 'plerdy_tracking_script', 'invalid_tracking_script', 'Please check: the Plerdy tracking code was added incorrectly.' );
            return get_option( 'plerdy_tracking_script' );
        }
    }

    public function validate_abtracking_script( $input ) {
        // regular expression for validation

        $pattern = "/<!-- BEGIN PLERDY A\/B TESTING CODE -->\s*" .
            "<script type=\"text\/javascript\">\s*" .
            "var _suid=(\d+);\s*" .
            "<\/script>\s*" .
            "<script\s+async\s+type=\"text\/javascript\"\s+src=\"https:\/\/[a-zA-Z0-9.\/:_-]+\/plerdy_ab-min\.js\?v=([^\"\s]+)\"[^>]*>\s*" .
            "<\/script>\s*" .
            "<!-- END PLERDY A\/B TESTING CODE -->\s*$/i";


        if (preg_match($pattern, $input) || empty($input)) {

            return $input;
        } else {
            // If validation fails, return the default value
            add_settings_error( 'plerdy_abtracking_script', 'invalid_abtracking_script', 'Please check: the Plerdy A/B testing tracking code was added incorrectly.

' );
            return get_option( 'plerdy_abtracking_script' );
        }
    }


    public function admin_options() {
        include 'views/options.php';
    }

    public function add_settings_link( $links ) {
        $settings_link = array( '<a href="admin.php?page=plerdy_heatmap">Settings'.'</a>' );
        return array_merge( $links, $settings_link );
    }
    public function style_plerdy(){
        ?>
        <style>
            .imgplerdynone {
                display: none;
                position: absolute;
                left: 0px;
                top: 0px;
            }
            .waper {
                position: relative;
                font-size: 12px;
                font-weight: 500;
                margin-left: 5px;
                cursor: pointer;
                border: 1px solid #6c6c6c;
                border-radius: 70px;
                color: #fff;
                background-color: #6c6c6c;
                display: flex;
                align-items: center;
                padding: 0px 8px;
            }
            .form-plerdy td {
                padding: 10px 0px;
            }
            .waper:hover .imgplerdynone {
                display: block;

            }
        </style>
        <?php
    }
}

add_action( 'init', 'PlerdyForWordpress' );
function PlerdyForWordpress() {
    global $PlerdyForWordpress;

    $PlerdyForWordpress = new Plerdy();
}
?>
