<?php

/*
Template Name: Интерактивная карта
*/

get_header();
if(have_posts()) {
	while(have_posts()) {
		the_post();
		$class = function_exists('get_post_class') ? implode(' ', get_post_class()) : '';
		$id = get_the_ID();
		if($id != '') {
			$id = 'post-' . $id;
		}
		$post_content = '
		<!-- Интерактивная карта -->
		<!-- Автор: Сергей Авдеев (SoulTaker) -->
		<!-- LinkedIn: https://www.linkedin.com/in/soultaker/ -->
		<p>Лев–Толстовская земля, как и вся Россия 20 века, разделила жестокую участь гонений на православную церковь. Сегодня она наполнена особой благодатью, которая почти физически ощутима там, где стоят старинные храмы и церкви. Некоторые из них представляют собой архитектурные памятники регионального значения и являются истинными произведениями искусства.</p>
		<p>Карта предоставляет актуальную информацию о православных храмах и церквях в доступной и удобной современному интернет-пользователю форме.</p>
		<p>Beta-версия продукта включает 26 объектов (храмы, церкви) и охватывает весь Лев-Толстовский район.</p>
		<link rel="stylesheet" type="text/css" href="'.get_template_directory_uri().'/interactiveMap.css" />
		<div id="yandex_map" style="width: 720px; height: 500px; padding: 15px 0 0 0;"></div>
		<div id="modal_window">
			<h2 id="modal_title"></h2>
			<iframe id="modal_frame" src="about:blank" onload="this.style.display = \'block\';">Ваш браузер не поддерживает технологию iFrame!</iframe>
		</div>
		<div id="modal_overlay"></div>
		<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
		<script type="text/javascript">
			(function($) {
				$(document).ready(function() {
					ymaps.ready(function() {
						var json_file = \''.get_template_directory_uri().'/jsonMap.php\';
						$.getJSON(json_file, function(json_data) {
							var yandexMap = new ymaps.Map(\'yandex_map\', json_data.map);
							yandexMap.container.events.add(\'fullscreenenter\', function() {
								$(\'ymaps\').first().css(\'padding-top\', $(\'#wpadminbar\').height()+\'px\');
							});
							var yandexCollection = new ymaps.GeoObjectCollection();
							json_data.collection.forEach(function(collection) {
								yandexCollection.add(new ymaps.Placemark(collection.geometry, collection.properties, collection.options));
							});
							yandexMap.geoObjects.add(yandexCollection);
							if(json_data.collection.length>0) {
								if(json_data.collection.length==1) {
									yandexMap.setZoom(json_data.map.zoom, {checkZoomRange: true});
								} else {
									yandexMap.setBounds(yandexCollection.getBounds(), {checkZoomRange: true, zoomMargin: 25}).then(function() {
									}, function(err) {
										yandexMap.setCenter(json_data.map, {checkZoomRange: true});
									}, this);
								}
							}
							yandexCollection.events.add(\'click\', function(e) {
								e.preventDefault();
								if($(\'#modal_window\').css(\'display\')==\'none\') {
									$(\'html\').css(\'overflow\', \'hidden\');
									$(\'html\').css(\'margin-right\', \'17px\');
									var object = e.get(\'target\');
									var title = object.properties.get(\'hintContent\');
									var url = object.properties.get(\'contentUrl\');
									$(\'#modal_title\').text(title);
									$(\'#modal_frame\').hide();
									$(\'#modal_frame\').attr(\'src\', url);
									$(\'#modal_overlay\').fadeIn(400, function() {
										$(\'#modal_window\').css(\'display\', \'block\').animate({opacity: 1, top: \'50%\'}, 200);
									});
								}
							});
						}).fail(function(obj, textStatus, error) {
							console.error(\'getJSON failed, file: \'+json_file+\',status: \'+textStatus+\', error: \'+error);
						});
					});
					$(\'#modal_overlay\').click(function(e) {
						e.preventDefault();
						if($(\'#modal_window\').css(\'display\')!=\'none\') {
							$(\'#modal_window\').animate({opacity: 0, top: \'45%\'}, 200, function() {
								$(this).css(\'display\', \'none\');
								$(\'#modal_title\').text(\'\');
								$(\'#modal_frame\').attr(\'src\', \'about:blank\');
								$(\'#modal_overlay\').fadeOut(400);
								$(\'html\').css(\'margin-right\', \'0\');
								$(\'html\').css(\'overflow\', \'auto\');
							});
						}
					});
				});
			})(jQuery);
		</script>
		';
		art_post_box('', $post_content, $id, $class, array(
			'post_title'          =>   art_option('metadata.title') ? '' : art_get_post_title(),
			'post_thumbnail'      =>   art_get_post_thumbnail(),
			'post_metadataheader' =>   art_get_post_metadata('header'),
			'post_metadatafooter' =>   art_get_post_metadata('footer')
		));
		art_post_box('', art_get_post_content(), $id, $class, array(
			'post_title'          =>   '',
			'post_thumbnail'      =>   '',
			'post_metadataheader' =>   '',
			'post_metadatafooter' =>   ''
		));
		comments_template();
	}
} else {  
	art_not_found_msg();
}
get_footer(); 

?>