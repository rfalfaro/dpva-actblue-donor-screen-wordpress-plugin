<?php

// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}
 
// Drop custom plugin tables to fully uninstall this plugin
global $wpdb;
$table = $wpdb->prefix."dpva_actblue_donor_screen";
$settings_table_name = $wpdb->prefix . "dpva_actblue_donor_screen_settings";
$wpdb->query("DROP TABLE IF EXISTS $table");
$wpdb->query("DROP TABLE IF EXISTS $settings_table_name");

?>