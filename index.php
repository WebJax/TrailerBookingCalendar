<?php
/*
Plugin Name: Trailer Booking Calendar
Plugin URI: skelbæklund.dk
Description: Book your trailer via the fullcalendar.io script. Just use [trailerbooking] shortcode in any post/page
Version: 1.0.0
Author: Jacob Thygesen
Author URI: jaxweb.dk
License: GPLv2
*/

// include() or require() any necessary files here...
include_once('includes/TrailerBookingCalendar.php');

// Tie into WordPress Hooks and any functions that should run on load.
register_activation_hook( __FILE__, 'trailerbookingcalendar_activating' );

// Instantiate our class
$TrailerBookingCalendar = TrailerBookingCalendar::getInstance();
/* EOF */