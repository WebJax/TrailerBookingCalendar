<?php
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Delete options
$option_name = 'trailerbookingcalendar_db_version';
delete_option( $option_name ); 
 
// Drop a custom db table
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}trailerbookingcalendar" );
/* EOF */