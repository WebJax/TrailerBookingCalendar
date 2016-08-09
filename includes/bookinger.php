<?php
header('Content-Type:application/json');
/**
 * Makes json feed for events call from fullcalendar
 *
 */
$start = $_GET['start'];
$end = $_GET['end'];
/**
 * Call DB to see if theres registered any events between
 *  - @start (Start date)
 *  - @end (End date)
 *
 * Returning a JSON object containing all events within the requested timeperiod
 */
// - grab wp load, wherever it's hiding -
if(file_exists('../../../../wp-load.php')) :
	include '../../../../wp-load.php';
else:
	include '../../../../../wp-load.php';
endif;

global $wpdb;$table_name = $wpdb->prefix . "trailerbookingcalendar";

$start .= ' 00:00:00';
$end .= ' 23:45:00';

$sql = "SELECT * FROM ".$table_name." WHERE bookingstart >= '".$start."' AND bookingend <= '".$end."'";
$results = $wpdb->get_results( $sql );

$tbc_events = array();
if ($results) {
  foreach($results as $result) {
     $e = array();
     $e['id'] = $result->id;
     $e['title'] = $result->infotext;
     $e['start'] = $result->bookingstart;
     $e['end'] = $result->bookingend;
     $e['allDay'] = false;
     array_push($tbc_events, $e);
  }
  echo json_encode($tbc_events);
}
 