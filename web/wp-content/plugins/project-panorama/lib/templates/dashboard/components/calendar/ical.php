<?php

include( PROJECT_PANORAMA_DIR . '/lib/vendor/ical/zapcallib.php' );

// define('DATE_ICAL', 'Ymd\THis\Z');
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=calendar.ics');

$user_hash = get_query_var('psp_ical_page');
if( !$user_hash ) wp_die( __('Invalid iCalender feed', 'psp_projects' ) );

$cuser = wp_get_current_user();

$user_email = str_rot13($user_hash);
$user       = get_user_by( 'slug', $user_email );

if( !$user ) wp_die( __('Invalid iCalender feed', 'psp_projects' ) );

$meta_query = psp_access_meta_query( $user->ID );

$args = array(
    'post_type'			=>		'psp_projects',
    'posts_per_page'	=>		-1,
);

if( $meta_query ) {
    array_merge( $args, array( 'meta_query' => $meta_query ) );
}

$projects   = new WP_Query( $args );
$dates      = psp_get_project_dates( $projects );
/*
ob_start(); ?>
BEGIN:VCALENDAR<?php echo "\r\n"; ?>
METHOD:PUBLISH<?php echo "\r\n"; ?>
VERSION:2.0<?php echo "\r\n"; ?>
PRODID:-//<?php echo esc_html(get_bloginfo('name')); ?>//<?php echo $user->display_name; ?>//EN<?php echo "\r\n"; ?>
X-WR-CALNAME:<?php esc_html_e( 'Projects: ' ); echo $cuser->display_name . "\r\n"; ?>
X-WR-CALDESC:<?php esc_html_e( 'Projects: ' ); echo $cuser->display_name . "\r\n"; ?>
CALSCALE:GREGORIAN<?php echo "\r\n"; ?>
<?php foreach( $dates as $date ): ?>
BEGIN:VEVENT<?php echo "\r\n"; ?>
DTSTAMP:<?php echo date(DATE_ICAL, strtotime($date['start'])) . "\r\n"; ?>
DTSTART;VALUE=DATE:<?php echo date(DATE_ICAL, strtotime($date['start'])) . "\r\n"; ?>
SUMMARY:<?php echo strip_tags($date['title']) . "\r\n"; ?>
DESCRIPTION:<?php echo substr(strip_tags($date['ical_desc']) . ' <' . esc_url($date['url']) . '>', 0, 75); ?><?php echo "\r\n"; ?>
UID:<?php echo esc_html( $date['ID'] ) . $i . "\r\n"; ?>
END:VEVENT<?php echo "\r\n"; ?>
<?php endforeach; ?>
END:VCALENDAR<?php echo "\r\n"; ?>
<?php
$i++;
echo ob_get_clean();
*/

$ical_obj = new ZCiCal();

foreach( $dates as $date ) {

     $event_obj = new ZCiCalNode( 'VEVENT', $ical_obj->curnode );

     $event_obj->addNode( new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($date['start'])) );
     $event_obj->addNode( new ZCiCalDataNode("SUMMARY:" . $date['title'] ) );
     $event_obj->addNode( new ZCiCalDataNode("DESCRIPTION:" . substr( strip_tags($date['ical_desc']), 0, 75 ) ) );
     $event_obj->addNode( new ZCiCalDataNode("URL:" . $date['url'] ) );

}

echo $ical_obj->export();
