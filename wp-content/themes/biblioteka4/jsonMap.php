<?php

require_once( '../../../wp-load.php' );

$json = array();
$json['map'] = array(
						'center' => array(
											floatval(get_option('interactive_map_latitude', 53.210479)),
											floatval(get_option('interactive_map_longitude', 39.451272))
										),
						'zoom'	 => intval(get_option('interactive_map_zoom', 10))
					);

$results = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'interactive_map`;');
$json['collection'] = array();
foreach($results as $point) {
	$json['collection'][] = array(
									'geometry'	 => array(floatval($point->latitude), floatval($point->longitude)),
									'properties' => array('hintContent' => $point->title, 'contentUrl' => $point->url),
									'options'	 => array('hasBalloon' => false, 'preset' => $point->preset)
								);
}

@header( 'Content-Type: application/json; charset='.get_option('blog_charset'), 'UTF-8');
wp_send_json($json);

?>
