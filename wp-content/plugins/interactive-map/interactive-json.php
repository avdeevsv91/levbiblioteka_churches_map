<?php

/*
 LevBiblioteka Churches map
 Author: Sergey Avdeev
 E-Mail: avdeevsv91@gmail.com
 URL: https://github.com/avdeevsv91/levbiblioteka_churches_map
*/

require_once( '../../../wp-load.php' );

if($results = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'im_maps` WHERE `map_id`=%d LIMIT 1;', $_GET['map']))) {
	$json = array();
	$json['map'] = array(
		'center' => array(
			floatval($results[0]->latitude),
			floatval($results[0]->longitude)
		),
		'zoom'	 => intval($results[0]->zoom)
	);
	$results2 = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'im_points` WHERE `map_id` = '.$results[0]->map_id.' ORDER BY `position`;');
	$json['collection'] = array();
	$json['polyline'] = array('coordinates' => []);
	foreach($results2 as $point) {
		if(!in_array($point->preset, ['hidden#butPolyline', 'hidden#all'])) {
			$json['collection'][] = array(
				'geometry'	 => array(floatval($point->latitude), floatval($point->longitude)),
				'properties' => array('hintContent' => $point->title, 'contentUrl' => $point->url),
				'options'	 => array('hasBalloon' => false, 'preset' => $point->preset)
			);
		}
		if(!in_array($point->preset, ['hidden#all'])) {
			if($results[0]->polyline) {
				$json['polyline']['color'] = $results[0]->pl_color;
				$json['polyline']['width'] = $results[0]->pl_width;
				$json['polyline']['opacity'] = $results[0]->pl_opacity;
				$json['polyline']['coordinates'][] = array(floatval($point->latitude), floatval($point->longitude));
			}
		}
	}
	@header( 'Content-Type: application/json; charset='.get_option('blog_charset'), 'UTF-8');
	wp_send_json($json);
} else {
	@header('HTTP/1.0 404 Not Found');
}

?>
