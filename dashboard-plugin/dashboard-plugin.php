<?php
   /*
   Plugin Name: Dashboard Plugin
   Plugin URI: https://resolutiontelevision.com
   description: Plugin For Dashboard Pages, Requires wpDataTables!
   Version: 1.0
   Author: Mike Wing
   License: GPL2
   */
date_default_timezone_set('Europe/London');

function passed_elearners_shortcode(){
global $wpdb;
// MW - PASSED ELEARNERS PAGE
$plugin_shortcodes = $_SERVER["DOCUMENT_ROOT"].'/wp-content/plugins/dashboard-plugin/shortcodes/';
require_once( $plugin_shortcodes.'/passed-elearners.php' );
}
add_shortcode('passed_elearners', 'passed_elearners_shortcode');
?>