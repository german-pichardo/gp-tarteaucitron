<?php
/**
 * Plugin Name: Gp: tarteaucitron
 * Description: Simple way to configure tarteaucitron.js (Menu->Settings->Gp Tarteaucitron.
 * Version: 1.1.0
 * Author: German Pichardo
 * Author URI: http://www.german-pichardo.com
 * Text Domain: gp-tarteaucitron
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

require_once plugin_dir_path(__FILE__) . 'admin/class-gp-tarteaucitron-admin.php'; // Admin

require_once plugin_dir_path(__FILE__) . 'front/class-gp-tarteaucitron-front.php'; // Front
