<?php

// add cron intervals
function wpcron_intervals( $schedules ) {

	// one minute

	$one_minute = array(
					'interval' => 60,
					'display' => 'One Minute'
				);

	$schedules[ 'one_minute' ] = $one_minute;

	// five minutes

	$five_minutes = array(
					'interval' => 300,
					'display' => 'Five Minutes'
				);

	$schedules[ 'five_minutes' ] = $five_minutes;

	// 6 hours

	$six_hours = array(
					'interval' => 21600,
					'display' => 'Six Hours'
				);

	$schedules[ 'six_hours' ] = $six_hours;

	// return data

	return $schedules;

}
add_filter( 'cron_schedules', 'wpcron_intervals' );


// add cron event


	if ( ! wp_next_scheduled( 'yacht_feed' ) ) {

		wp_schedule_event( time(), 'six_hours', 'yacht_feed' );

	}


// cron event
function wpcron_yacht_feed() {

	check_for_xml_updates();

}
add_action( 'yacht_feed', 'wpcron_yacht_feed' );


