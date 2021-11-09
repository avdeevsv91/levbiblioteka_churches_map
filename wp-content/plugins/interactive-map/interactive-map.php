<?php
/*
  Plugin Name: Interactive Map
  Description: Interactive map for WordPress
  Version: 1.0
  Author: Sergey Avdeev
  Author URI: https://github.com/kasitoru/levbiblioteka_churches_map
*/

// Install plugin hook
register_activation_hook(__FILE__, 'im_install');
function im_install() {
	global $wpdb;
	// im_maps
	$query = '
	CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'im_maps` (
		`map_id` bigint(20) unsigned NOT NULL,
		`title` varchar(255) NOT NULL,
		`latitude` varchar(10) NOT NULL,
		`longitude` varchar(10) NOT NULL,
		`zoom` int(2) unsigned NOT NULL,
		`polyline` tinyint(1) NOT NULL DEFAULT \'0\',
		`pl_color` varchar(7) NOT NULL DEFAULT \'#000000\',
		`pl_width` int(1) NOT NULL DEFAULT \'2\',
		`pl_opacity` float NOT NULL DEFAULT \'0.5\'
	) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
	';
	$wpdb->query($query);
	// im_points
	$query = '
	CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'im_points` (
		`point_id` bigint(20) unsigned NOT NULL,
		`map_id` bigint(20) unsigned NOT NULL,
		`title` varchar(255) NOT NULL,
		`latitude` varchar(10) NOT NULL,
		`longitude` varchar(10) NOT NULL,
		`preset` varchar(64) NOT NULL DEFAULT \'islands#blueDotIcon\',
		`position` int(11) unsigned NOT NULL,
		`url` varchar(255) NOT NULL
	) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
	';
	$wpdb->query($query);
}

// Uninstall plugin hook
register_uninstall_hook(__FILE__, 'im_uninstall');
function im_uninstall() {
	// im_maps
	$query = 'DROP TABLE IF EXISTS `'.$wpdb->prefix.'im_maps`;';
	$wpdb->query($query);
	// im_points
	$query = 'DROP TABLE IF EXISTS `'.$wpdb->prefix.'im_points`;';
	$wpdb->query($query);
}

// Admin menu
add_action('admin_menu', 'im_control');
function im_control() {
	add_menu_page( 
		'Настройка интерактивной карты', 'Интерактивная карта', 'manage_options', 'im_control', 'im_control_content', 'dashicons-location-alt', 85
	);
}
function im_control_content() {
	global $wpdb;
	?>
	<div class="wrap">
	<?php
	if(isset($_GET['map'])) {
		$map_id = $_REQUEST['map'];
		if($results = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'im_maps` WHERE `map_id`=%d LIMIT 1;', $map_id))) {
			$map_id = $results[0]->map_id;
			$map_title = $results[0]->title;
			switch($_GET['action']) {
				case 'add':
					?>
					<h1 class="wp-heading-inline">Добавить объект на карту &laquo;<?php echo htmlspecialchars($map_title); ?>&raquo;</h1>
					<?php
					$title = $latitude = $longitude = $preset = $url = '';
					if(isset($_POST['submit'])) {
						if(wp_verify_nonce($_POST['_wpnonce'], 'im_control_add_point_map_'.$map_id)) {
							$title = $_POST['title'];
							$latitude = $_POST['latitude'];
							$longitude = $_POST['longitude'];
							$preset = $_POST['preset'];
							$url = $_POST['url'];
							if(!empty($title) && !empty($latitude) && !empty($longitude) && !empty($preset) && !empty($url)) {
								$position = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `'.$wpdb->prefix.'im_points` WHERE `map_id` = %d', intval($map_id))) + 1;
								$wpdb->query($wpdb->prepare('INSERT INTO `'.$wpdb->prefix.'im_points` (`map_id`, `title`, `latitude`, `longitude`, `preset`, `url`, `position`) VALUES (%d, %s, %s, %s, %s, %s, %d);', intval($map_id), $title, floatval($latitude), floatval($longitude), $preset, $url, $position));
								$title = $latitude = $longitude = $preset = $url = '';
								?>
								<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
								<p><strong>Новый объект был успешно добавлен в базу данных.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
								</div>
								<?php
							} else {
								?>
								<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"> 
								<p><strong>Заполнены не все поля!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
								</div>
								<?php			
							}
						}
					}
					if(empty($preset)) $preset = 'islands#blueDotIcon';
					?>
					<form method="post" action="admin.php?page=im_control&map=<?php echo htmlspecialchars($map_id); ?>&action=add" novalidate="novalidate">
					<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('im_control_add_point_map_'.$map_id); ?>">
					<table class="form-table">
					<tbody>
					<tr>
					<th scope="row"><label for="title">Название</label></th>
					<td>
					<input name="title" type="text" id="title" value="<?php echo htmlspecialchars($title); ?>" class="regular-text" />
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="latitude">Широта</label></th>
					<td>
					<input name="latitude" type="text" id="latitude" value="<?php echo htmlspecialchars($latitude); ?>" class="regular-text" />
					<p class="description" id="latitude-description">Значение широты координат. Например: 53.210479</p>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="longitude">Долгота</label></th>
					<td>
					<input name="longitude" type="text" id="longitude" value="<?php echo htmlspecialchars($longitude); ?>" class="regular-text" />
					<p class="description" id="longitude-description">Значение широты координат. Например: 39.451272</p>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="preset">Тип метки</label></th>
					<td>
					<input name="preset" type="text" id="preset" value="<?php echo htmlspecialchars($preset); ?>" class="regular-text" />
					<p class="description" id="preset-description">Тим метки для отображения на карте.<br />Полный список значений находится <a href="https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/option.presetStorage-docpage/" target="_blank">здесь</a>.<br /><br /><i>hidden#butPolyline</i> - скрыть метку, но использовать для линии;<br /><i>hidden#all</i> - полностью скрыть метку.</p>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="url">URL адрес</label></th>
					<td>
					<input name="url" type="text" id="url" value="<?php echo htmlspecialchars($url); ?>" class="regular-text" />
					<p class="description" id="url-description">Ссылка на страницу с подробным описанием данного объекта.</p>
					</td>
					</tr>
					</tbody>
					</table>
					<p class="submit">
					<a href="admin.php?page=im_control&map=<?php echo htmlspecialchars($map_id); ?>" class="button button-secondary">&laquo; Назад</a>
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Сохранить изменения" />
					</p>
					</form>
					<?php
					break;
				case 'edit':
					?>
					<h1 class="wp-heading-inline">Редактировать объект</h1>
					<?php
					$point_id = $_REQUEST['point'];
					if($results = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'im_points` WHERE `map_id`=%d AND `point_id`=%d LIMIT 1;', $map_id, $point_id))) {
						$point_id = $results[0]->point_id;
						$title = $results[0]->title;
						$latitude = $results[0]->latitude;
						$longitude = $results[0]->longitude;
						$preset = $results[0]->preset;
						$url = $results[0]->url;
						if(isset($_POST['submit'])) {
							if(wp_verify_nonce($_POST['_wpnonce'], 'im_control_edit_map_'.$map_id.'_point_'.$point_id)) {
								$title = $_POST['title'];
								$latitude = $_POST['latitude'];
								$longitude = $_POST['longitude'];
								$preset = $_POST['preset'];
								$url = $_POST['url'];
								if(!empty($title) && !empty($latitude) && !empty($longitude) && !empty($preset) && !empty($url)) {
									$wpdb->query($wpdb->prepare('UPDATE `'.$wpdb->prefix.'im_points` SET `title`=%s, `latitude`=%s, `longitude`=%s, `preset`=%s, `url`=%s WHERE `map_id`=%d AND `point_id`=%d LIMIT 1;', $title, floatval($latitude), floatval($longitude), $preset, $url, $map_id, $point_id));
									if($results = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'im_points` WHERE `map_id`=%d AND `point_id`=%d LIMIT 1;', $map_id, $point_id))) {
										$title = $results[0]->title;
										$latitude = $results[0]->latitude;
										$longitude = $results[0]->longitude;
										$preset = $results[0]->preset;
										$url = $results[0]->url;
									}
									?>
									<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
									<p><strong>Данные объекта были успешно обновлены.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
									</div>
									<?php
								} else {
									?>
									<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"> 
									<p><strong>Заполнены не все поля!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
									</div>
									<?php			
								}
							}
						}
						if(empty($preset)) $preset = 'islands#blueDotIcon';
						?>
						<form method="post" action="admin.php?page=im_control&map=<?php echo htmlspecialchars($map_id); ?>&action=edit&point=<?php echo htmlspecialchars($point_id); ?>" novalidate="novalidate">
						<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('im_control_edit_map_'.$map_id.'_point_'.$point_id); ?>">
						<table class="form-table">
						<tbody>
						<tr>
						<th scope="row"><label for="title">Название</label></th>
						<td>
						<input name="title" type="text" id="title" value="<?php echo htmlspecialchars($title); ?>" class="regular-text" />
						</td>
						</tr>
						<tr>
						<th scope="row"><label for="latitude">Широта</label></th>
						<td>
						<input name="latitude" type="text" id="latitude" value="<?php echo htmlspecialchars($latitude); ?>" class="regular-text" />
						<p class="description" id="latitude-description">Значение широты координат. Например: 53.210479</p>
						</td>
						</tr>
						<tr>
						<th scope="row"><label for="longitude">Долгота</label></th>
						<td>
						<input name="longitude" type="text" id="longitude" value="<?php echo htmlspecialchars($longitude); ?>" class="regular-text" />
						<p class="description" id="longitude-description">Значение широты координат. Например: 39.451272</p>
						</td>
						</tr>
						<tr>
						<th scope="row"><label for="preset">Тип метки</label></th>
						<td>
						<input name="preset" type="text" id="preset" value="<?php echo htmlspecialchars($preset); ?>" class="regular-text" />
						<p class="description" id="preset-description">Тим метки для отображения на карте.<br />Полный список значений находится <a href="https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/option.presetStorage-docpage/" target="_blank">здесь</a>.<br /><br /><i>hidden#butPolyline</i> - скрыть метку, но использовать для линии;<br /><i>hidden#all</i> - полностью скрыть метку.</p>
						</td>
						</tr>
						<tr>
						<th scope="row"><label for="url">URL адрес</label></th>
						<td>
						<input name="url" type="text" id="url" value="<?php echo htmlspecialchars($url); ?>" class="regular-text" />
						<p class="description" id="url-description">Ссылка на страницу с подробным описанием данного объекта.</p>
						</td>
						</tr>
						</tbody>
						</table>
						<p class="submit">
						<a href="admin.php?page=im_control&map=<?php echo htmlspecialchars($map_id); ?>" class="button button-secondary">&laquo; Назад</a>
						<input type="submit" name="submit" id="submit" class="button button-primary" value="Сохранить изменения" />
						</p>
						</form>
						<?php
					} else {
						?>
						<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"> 
						<p><strong>Запрошенный объект не найден в базе данных!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
						</div>
						<?php	
					}
					break;
				case 'up':
					$point_id = $_REQUEST['point'];
					if($results = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'im_points` WHERE `map_id` = %d ORDER BY `position` DESC;', $map_id))) {
						$i = count($results); $n = -1; $c = 1;
						foreach($results as $result) {
							if($result->point_id == $point_id && $i > $c) {
								$wpdb->query($wpdb->prepare('UPDATE `'.$wpdb->prefix.'im_points` SET `position`=%d WHERE `point_id`=%d LIMIT 1;', $i - 1, $result->point_id));
								$n = $i;
							} elseif($i == $n - 1) {
								$wpdb->query($wpdb->prepare('UPDATE `'.$wpdb->prefix.'im_points` SET `position`=%d WHERE `point_id`=%d LIMIT 1;', $i + 1, $result->point_id));
							} else {
								$wpdb->query($wpdb->prepare('UPDATE `'.$wpdb->prefix.'im_points` SET `position`=%d WHERE `point_id`=%d LIMIT 1;', $i, $result->point_id));
							}
							$i--;
						}
					}
					?>
					<h3>Пожалуйста, подождите...</h3>
					<script>
						window.location.href = 'admin.php?page=im_control&map=<?php echo $map_id; ?>';
					</script>
					<?php
					break;
				case 'down':
					$point_id = $_REQUEST['point'];
					if($results = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'im_points` WHERE `map_id` = %d ORDER BY `position`;', $map_id))) {
						$i = 1; $n = -1; $c = count($results);
						foreach($results as $result) {
							if($result->point_id == $point_id && $i < $c) {
								$wpdb->query($wpdb->prepare('UPDATE `'.$wpdb->prefix.'im_points` SET `position`=%d WHERE `point_id`=%d LIMIT 1;', $i + 1, $result->point_id));
								$n = $i;
							} elseif($i == $n + 1) {
								$wpdb->query($wpdb->prepare('UPDATE `'.$wpdb->prefix.'im_points` SET `position`=%d WHERE `point_id`=%d LIMIT 1;', $i - 1, $result->point_id));
							} else {
								$wpdb->query($wpdb->prepare('UPDATE `'.$wpdb->prefix.'im_points` SET `position`=%d WHERE `point_id`=%d LIMIT 1;', $i, $result->point_id));
							}
							$i++;
						}
					}
					?>
					<h3>Пожалуйста, подождите...</h3>
					<script>
						window.location.href = 'admin.php?page=im_control&map=<?php echo $map_id; ?>';
					</script>
					<?php
					break;
				case 'delete':
					$point = $_REQUEST['point'];
					if(!empty($point)) {
						if(is_string($point)) {
							$point_string = $point;
							$point = array($point_string);
							$nonce_name = 'im_control_delete_map_'.$map_id.'_point'.$point_string;
						} else {
							$nonce_name  = 'im_control_delete_map_'.$map_id.'_point';
						}
						if(wp_verify_nonce($_REQUEST['_wpnonce'], $nonce_name)) {
							foreach($point as $current_point) {
								$wpdb->query($wpdb->prepare('DELETE FROM `'.$wpdb->prefix.'im_points` WHERE `map_id`=%d AND `point_id`=%d;', $map_id, $current_point));
							}
							?>
							<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
							<p><strong>Выбранные объекты были успешно удалены.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
							</div>
							<?php
						}
					}
				default;
					?>
					<h1 class="wp-heading-inline">Объекты интерактивной карты &laquo;<?php echo htmlspecialchars($map_title); ?>&raquo;</h1>
					<a href="admin.php?page=im_control&map=<?php echo htmlspecialchars($map_id); ?>&action=add" class="page-title-action">Добавить</a>
					<hr class="wp-header-end" />
					<form id="posts-filter" method="post" action="admin.php?page=im_control&map=<?php echo htmlspecialchars($map_id); ?>&action=delete">
					<?php wp_nonce_field('im_control_delete_map_'.$map_id.'_point'); ?>
					<div class="tablenav top">
						<div class="alignleft actions">
							<input type="submit" id="delete_button" class="button" value="Удалить выбранные" />
						</div>
						<br class="clear">
					</div>
					<table class="wp-list-table widefat fixed striped pages">
					<thead>
					<tr>
					<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Выделить все</label><input id="cb-select-all-1" type="checkbox" /></td>
					<th scope="col" id="title" class="manage-column column-title column-primary">Название</th>
					<th scope="col" id="coordinates" class="manage-column column-coordinates">Координаты</th>
					<th scope="col" id="preset" class="manage-column column-preset">Тип метки</th>
					<th scope="col" id="url" class="manage-column column-url">Ссылка</th>
					<th scope="col" id="position" class="manage-column column-position">Позиция</th>
					</tr>
					</thead>
					<tbody id="the-list">
					<?php
					$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'im_points` WHERE `map_id`=%d ORDER BY `position`;', $map_id));
					foreach($results as $point) {
					?>
						<tr id="post-<?php echo $point->point_id; ?>" class="iedit author-self level-0 post-<?php echo $point->point_id; ?> type-page status-publish hentry">
							<th scope="row" class="check-column">
								<label class="screen-reader-text" for="cb-select-8323">Выбрать <?php echo $point->title; ?></label>
								<input id="cb-select-<?php echo $point->point_id; ?>" type="checkbox" name="point[]" value="<?php echo $point->point_id; ?>" />
							</th>
							<td class="title column-title has-row-actions column-primary page-title" data-colname="Название">
								<div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
								<strong><?php echo $point->title; ?></strong>
								<div class="row-actions">
									<span class="edit"><a href="admin.php?page=im_control&map=<?php echo htmlspecialchars($map_id); ?>&action=edit&point=<?php echo $point->point_id; ?>" aria-label="Редактировать &laquo;<?php echo $point->title; ?>&raquo;">Изменить</a> | </span>
									<span class="delete"><a href="admin.php?page=im_control&map=<?php echo htmlspecialchars($map_id); ?>&action=delete&point=<?php echo $point->point_id; ?>&_wpnonce=<?php echo wp_create_nonce('im_control_delete_map_'.$map_id.'_point'.$point->point_id); ?>" class="submitdelete" aria-label="Удалить &laquo;<?php echo $point->title; ?>&raquo;">Удалить</a></span>
								</div>
							</td>
							<td class="coordinates column-coordinates" data-colname="Координаты"><a href="https://yandex.ru/maps/?text=<?php echo urlencode(floatval($point->latitude).', '.floatval($point->longitude)); ?>" target="_blank"><?php echo floatval($point->latitude).', '.floatval($point->longitude); ?></a></td>
							<td class="preset column-preset" data-colname="Тип метки"><?php echo $point->preset; ?></td>
							<td class="url column-url" data-colname="Ссылка"><a href="<?php echo $point->url; ?>" target="_blank"><?php echo $point->url; ?></a></td>
							<td class="position column-position" data-colname="Позиция" style="text-align: center;">[<a href="admin.php?page=im_control&map=<?php echo htmlspecialchars($map_id); ?>&action=down&point=<?php echo $point->point_id; ?>">Ниже</a>] [<?php echo $point->position; ?>] [<a href="admin.php?page=im_control&map=<?php echo htmlspecialchars($map_id); ?>&action=up&point=<?php echo $point->point_id; ?>">Выше</a>]</a></td>
						</tr>
					<?php
					}
					?>
					</tbody>
					<tfoot>
					<tr>
					<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Выделить все</label><input id="cb-select-all-2" type="checkbox" /></td>
					<th scope="col" id="title" class="manage-column column-title column-primary">Название</th>
					<th scope="col" id="coordinates" class="manage-column column-coordinates">Координаты</th>
					<th scope="col" id="preset" class="manage-column column-preset">Тип метки</th>
					<th scope="col" id="url" class="manage-column column-url">Ссылка</th>
					<th scope="col" id="position" class="manage-column column-position">Позиция</th>
					</tr>
					</tr>
					</tfoot>
					</table>
					<div class="tablenav bottom">
						<div class="alignleft actions">
							<input type="submit" id="delete_button" class="button" value="Удалить выбранные" />
						</div>
						<br class="clear">
					</div>
					</form>
					<?php
			}
		} else {
			?>
			<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"> 
			<p><strong>Запрошенная карта не найдена в базе данных!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
			</div>
			<?php	
		}
	} else {
		switch($_GET['action']) {
			case 'add':
				?>
				<h1 class="wp-heading-inline">Добавить интерактивную карту</h1>
				<?php
				$title = $latitude = $longitude = $zoom = '';
				if(isset($_POST['submit'])) {
					if(wp_verify_nonce($_POST['_wpnonce'], 'im_control_add_map')) {
						$title = $_POST['title'];
						$latitude = $_POST['latitude'];
						$longitude = $_POST['longitude'];
						$zoom = $_POST['zoom'];
						$polyline = $_POST['polyline'];
						$pl_color = $_POST['pl_color'];
						$pl_width = $_POST['pl_width'];
						$pl_opacity = $_POST['pl_opacity'];
						if(!empty($title) && !empty($latitude) && !empty($longitude) && !empty($zoom) && !empty($pl_color) && !empty($pl_width) && !empty($pl_opacity)) {
							$wpdb->show_errors();
							$wpdb->query($wpdb->prepare('INSERT INTO `'.$wpdb->prefix.'im_maps` (`title`, `latitude`, `longitude`, `zoom`, `polyline`, `pl_color`, `pl_width`, `pl_opacity`) VALUES (%s, %s, %s, %d, %d, %s, %d, %f);', $title, floatval($latitude), floatval($longitude), intval($zoom), (is_null($polyline)?0:1), $pl_color, intval($pl_width), round(intval($pl_opacity)/100, 2)));
							$title = $latitude = $longitude = $zoom = $polyline = $pl_color = $pl_width = $pl_opacity = '';
							?>
							<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
							<p><strong>Новая карта была успешно добавлена в базу данных.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
							</div>
							<?php
						} else {
							?>
							<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"> 
							<p><strong>Заполнены не все поля!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
							</div>
							<?php			
						}
					}
				}
				if(empty($zoom)) $zoom = 10;
				if(empty($pl_color)) $pl_color = '#000000';
				if(empty($pl_width)) $pl_width = 2;
				if(empty($pl_opacity)) $pl_opacity = 0.5;
				?>
				<form method="post" action="admin.php?page=im_control&action=add" novalidate="novalidate">
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('im_control_add_map'); ?>">
				<table class="form-table">
				<tbody>
				<tr>
				<th scope="row"><label for="title">Название</label></th>
				<td>
				<input name="title" type="text" id="title" value="<?php echo htmlspecialchars($title); ?>" class="regular-text" />
				</td>
				</tr>
				<tr>
				<th scope="row"><label for="latitude">Широта</label></th>
				<td>
				<input name="latitude" type="text" id="latitude" value="<?php echo htmlspecialchars($latitude); ?>" class="regular-text" />
				<p class="description" id="latitude-description">Значение широты координат. Например: 53.210479</p>
				</td>
				</tr>
				<tr>
				<th scope="row"><label for="longitude">Долгота</label></th>
				<td>
				<input name="longitude" type="text" id="longitude" value="<?php echo htmlspecialchars($longitude); ?>" class="regular-text" />
				<p class="description" id="longitude-description">Значение широты координат. Например: 39.451272</p>
				</td>
				</tr>
				<tr>
				<th scope="row"><label for="zoom">Масштаб</label></th>
				<td>
				<input name="zoom" type="text" id="zoom" value="<?php echo htmlspecialchars($zoom); ?>" class="regular-text" />
				<p class="description" id="zoom-description">Первоначальный масштаб карты. Целочисленное значение от 1 до 17.</p>
				</td>
				</tr>
				<tr>
				<th scope="row"><label for="polyline">Соединить точки</label></th>
				<td>
				<label for="polyline">
				<input name="polyline" type="checkbox" id="polyline" value="1"<?php if($polyline) echo ' checked="checked"'; ?>>
				Начертить ломаную линию между точек.
				</label>
				</td>
				</tr>
				<tr>
				<th scope="row"><label for="pl_color">Цвет линии</label></th>
				<td>
				<input name="pl_color" type="text" id="pl_color" value="<?php echo htmlspecialchars($pl_color); ?>" class="regular-text" />
				<p class="description" id="pl_color-description">Используется шестнадцатеричный формат записи. Например: #FF0000.</p>
				</td>
				</tr>
				<tr>
				<th scope="row"><label for="pl_width">Толщина линии</label></th>
				<td>
				<input name="pl_width" type="text" id="pl_width" value="<?php echo intval($pl_width); ?>" class="regular-text" />
				<p class="description" id="pl_width-description">В пикселях. Целое число от 1 до 9.</p>
				</td>
				</tr>
				<tr>
				<th scope="row"><label for="pl_opacity">Прозрачность</label></th>
				<td>
				<input name="pl_opacity" type="text" id="pl_opacity" value="<?php echo floatval($pl_opacity)*100; ?>" class="regular-text" />
				<p class="description" id="pl_opacity-description">Уровень прозрачности для ломаной линии (в процентах).</p>
				</td>
				</tr>
				</tbody>
				</table>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Сохранить изменения" /></p>
				</form>
				<?php
				break;
			case 'edit':
				?>
				<h1 class="wp-heading-inline">Изменить интерактивную карту</h1>
				<?php
				$map_id = $_REQUEST['id'];
				if($results = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'im_maps` WHERE `map_id`=%d LIMIT 1;', $map_id))) {
					$map_id = $results[0]->map_id;
					$title = $results[0]->title;
					$latitude = $results[0]->latitude;
					$longitude = $results[0]->longitude;
					$zoom = $results[0]->zoom;
					$polyline = $results[0]->polyline;
					$pl_color = $results[0]->pl_color;
					$pl_width = $results[0]->pl_width;
					$pl_opacity = $results[0]->pl_opacity;
					if(isset($_POST['submit'])) {
						if(wp_verify_nonce($_POST['_wpnonce'], 'im_control_edit_map_'.$map_id)) {
							$title = $_POST['title'];
							$latitude = $_POST['latitude'];
							$longitude = $_POST['longitude'];
							$zoom = $_POST['zoom'];
							$polyline = $_POST['polyline'];
							$pl_color = $_POST['pl_color'];
							$pl_width = $_POST['pl_width'];
							$pl_opacity = $_POST['pl_opacity'];
							if(!empty($title) && !empty($latitude) && !empty($longitude) && !empty($zoom) && !empty($pl_color) && !empty($pl_width) && !empty($pl_opacity)) {
								$wpdb->query($wpdb->prepare('UPDATE `'.$wpdb->prefix.'im_maps` SET `title`=%s, `latitude`=%s, `longitude`=%s, `zoom`=%d, `polyline`=%d, `pl_color`=%s, `pl_width`=%d, `pl_opacity`=%f WHERE `map_id`=%d LIMIT 1;', $title, floatval($latitude), floatval($longitude), intval($zoom), (is_null($polyline)?0:1), $pl_color, intval($pl_width), round(intval($pl_opacity)/100, 2),  $map_id));
								if($results = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'im_maps` WHERE `map_id`=%d LIMIT 1;', $map_id))) {
									$title = $results[0]->title;
									$latitude = $results[0]->latitude;
									$longitude = $results[0]->longitude;
									$zoom = $results[0]->zoom;
									$polyline = $results[0]->polyline;
									$pl_color = $results[0]->pl_color;
									$pl_width = $results[0]->pl_width;
									$pl_opacity = $results[0]->pl_opacity;
								}
								?>
								<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
								<p><strong>Данные карты были успешно обновлены.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
								</div>
								<?php
							} else {
								?>
								<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"> 
								<p><strong>Заполнены не все поля!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
								</div>
								<?php			
							}
						}
					}
					if(empty($zoom)) $zoom = 10;
					if(empty($pl_color)) $pl_color = '#000000';
					if(empty($pl_width)) $pl_width = 2;
					if(empty($pl_opacity)) $pl_opacity = 0.5;
					?>
					<form method="post" action="admin.php?page=im_control&action=edit&id=<?php echo htmlspecialchars($map_id); ?>" novalidate="novalidate">
					<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('im_control_edit_map_'.$map_id); ?>">
					<table class="form-table">
					<tbody>
					<tr>
					<th scope="row"><label for="title">Название</label></th>
					<td>
					<input name="title" type="text" id="title" value="<?php echo htmlspecialchars($title); ?>" class="regular-text" />
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="latitude">Широта</label></th>
					<td>
					<input name="latitude" type="text" id="latitude" value="<?php echo htmlspecialchars($latitude); ?>" class="regular-text" />
					<p class="description" id="latitude-description">Значение широты координат. Например: 53.210479</p>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="longitude">Долгота</label></th>
					<td>
					<input name="longitude" type="text" id="longitude" value="<?php echo htmlspecialchars($longitude); ?>" class="regular-text" />
					<p class="description" id="longitude-description">Значение широты координат. Например: 39.451272</p>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="zoom">Масштаб</label></th>
					<td>
					<input name="zoom" type="text" id="zoom" value="<?php echo htmlspecialchars($zoom); ?>" class="regular-text" />
					<p class="description" id="zoom-description">Первоначальный масштаб карты. Целочисленное значение от 1 до 17.</p>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="polyline">Соединить точки</label></th>
					<td>
					<label for="polyline">
					<input name="polyline" type="checkbox" id="polyline" value="1"<?php if($polyline) echo ' checked="checked"'; ?>>
					Начертить ломаную линию между точек.
					</label>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="pl_color">Цвет линии</label></th>
					<td>
					<input name="pl_color" type="text" id="pl_color" value="<?php echo htmlspecialchars($pl_color); ?>" class="regular-text" />
					<p class="description" id="pl_color-description">Используется шестнадцатеричный формат записи. Например: #FF0000.</p>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="pl_width">Толщина линии</label></th>
					<td>
					<input name="pl_width" type="text" id="pl_width" value="<?php echo intval($pl_width); ?>" class="regular-text" />
					<p class="description" id="pl_width-description">В пикселях. Целое число от 1 до 9.</p>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="pl_opacity">Прозрачность</label></th>
					<td>
					<input name="pl_opacity" type="text" id="pl_opacity" value="<?php echo floatval($pl_opacity)*100; ?>" class="regular-text" />
					<p class="description" id="pl_opacity-description">Уровень прозрачности для ломаной линии (в процентах).</p>
					</td>
					</tr>
					</tbody>
					</table>
					<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Сохранить изменения" /></p>
					</form>
					<?php
				} else {
					?>
					<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible"> 
					<p><strong>Запрошенная карта не найдена в базе данных!</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
					</div>
					<?php	
				}
				break;
			case 'delete':
				$maps = $_REQUEST['maps'];
				if(!empty($maps)) {
					if(is_string($maps)) {
						$maps_string = $maps;
						$maps = array($maps_string);
						$nonce_name = 'im_control_delete_map_'.$maps_string;
					} else {
						$nonce_name  = 'im_control_delete_map';
					}
					if(wp_verify_nonce($_REQUEST['_wpnonce'], $nonce_name)) {
						foreach($maps as $current_map) {
							$wpdb->query($wpdb->prepare('DELETE FROM `'.$wpdb->prefix.'im_maps` WHERE `map_id`=%d LIMIT 1;', $current_map));
							$wpdb->query($wpdb->prepare('DELETE FROM `'.$wpdb->prefix.'im_points` WHERE `map_id`=%d;', $current_map));
						}
						?>
						<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
						<p><strong>Выбранные карты были успешно удалены.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
						</div>
						<?php
					}
				}
			default:
				?>
				<h1 class="wp-heading-inline">Настройка интерактивной карты</h1>
				<a href="admin.php?page=im_control&action=add" class="page-title-action">Добавить</a>
				<hr class="wp-header-end" />
				<form id="posts-filter" method="post" action="admin.php?page=im_control&action=delete">
				<?php wp_nonce_field('im_control_delete_map'); ?>
				<div class="tablenav top">
					<div class="alignleft actions">
						<input type="submit" id="delete_button" class="button" value="Удалить выбранные" />
					</div>
					<br class="clear">
				</div>
				<table class="wp-list-table widefat fixed striped pages">
				<thead>
				<tr>
				<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Выделить все</label><input id="cb-select-all-1" type="checkbox" /></td>
				<th scope="col" id="id" class="manage-column column-id" style="width: 25px;">ID</th>
				<th scope="col" id="title" class="manage-column column-title column-primary">Название</th>
				<th scope="col" id="coordinates" class="manage-column column-coordinates">Координаты</th>
				<th scope="col" id="zoom" class="manage-column column-zoom">Масштаб</th>
				<th scope="col" id="objects" class="manage-column column-objects">Объектов</th>
				</tr>
				</thead>
				<tbody id="the-list">
				<?php
				$results = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'im_maps`;');
				foreach($results as $map) {
				?>
					<tr id="post-<?php echo $map->map_id; ?>" class="iedit author-self level-0 post-<?php echo $map->map_id; ?> type-page status-publish hentry">
						<th scope="row" class="check-column">
							<label class="screen-reader-text" for="cb-select-8323">Выбрать <?php echo $map->title; ?></label>
							<input id="cb-select-<?php echo $map->map_id; ?>" type="checkbox" name="maps[]" value="<?php echo $map->map_id; ?>" />
						</th>
						<td class="id column-id" data-colname="ID"><?php echo $map->map_id; ?></td>
						<td class="title column-title has-row-actions column-primary page-title" data-colname="Название">
							<div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
							<strong><?php echo $map->title; ?></strong>
							<div class="row-actions">
								<span class="edit"><a href="admin.php?page=im_control&action=edit&id=<?php echo $map->map_id; ?>" aria-label="Редактировать &laquo;<?php echo $map->title; ?>&raquo;">Изменить</a> | </span>
								<span class="delete"><a href="admin.php?page=im_control&action=delete&maps=<?php echo $map->map_id; ?>&_wpnonce=<?php echo wp_create_nonce('im_control_delete_map_'.$map->map_id); ?>" class="submitdelete" aria-label="Удалить &laquo;<?php echo $map->title; ?>&raquo;">Удалить</a></span>
							</div>
						</td>
						<td class="coordinates column-coordinates" data-colname="Координаты"><a href="https://yandex.ru/maps/?text=<?php echo urlencode(floatval($map->latitude).', '.floatval($map->longitude)); ?>" target="_blank"><?php echo floatval($map->latitude).', '.floatval($map->longitude); ?></a></td>
						<td class="zoom column-zoom" data-colname="Масштаб"><?php echo $map->zoom; ?></td>
						<td class="objects column-objects" data-colname="Объектов"><?php echo $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `'.$wpdb->prefix.'im_points` WHERE `map_id` = %d', $map->map_id)); ?> [<a href="admin.php?page=im_control&map=<?php echo $map->map_id; ?>" aria-label="Открыть &laquo;<?php echo $map->title; ?>&raquo;">Редактировать</a>]</td>
					</tr>
				<?php
				}
				?>
				</tbody>
				<tfoot>
				<tr>
				<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Выделить все</label><input id="cb-select-all-2" type="checkbox" /></td>
				<th scope="col" id="id" class="manage-column column-id" style="width: 25px;">ID</th>
				<th scope="col" id="title" class="manage-column column-title column-primary">Название</th>
				<th scope="col" id="coordinates" class="manage-column column-coordinates">Координаты</th>
				<th scope="col" id="zoom" class="manage-column column-zoom">Масштаб</th>
				<th scope="col" id="objects" class="manage-column column-objects">Объектов</th>
				</tr>
				</tr>
				</tfoot>
				</table>
				<div class="tablenav bottom">
					<div class="alignleft actions">
						<input type="submit" id="delete_button" class="button" value="Удалить выбранные" />
					</div>
					<br class="clear">
				</div>
				</form>
				<h2>Использование: <i>[im]ид_карты[/im]</i></h2>
				<?php
		}
	}
}

// CSS
add_action('wp_enqueue_scripts', 'im_jsandstyles');
function im_jsandstyles() {
	wp_enqueue_style('interactive-map', plugin_dir_url(__FILE__).'interactive-map.css');
}

// BB-code
function im_bbcode($content) {
	$post_content = '
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
						var json_file = \''.plugin_dir_url(__FILE__).'interactive-json.php?map=${1}\';
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
							if(json_data.polyline.coordinates.length > 0) {
								var yandexPolyline = new ymaps.Polyline(json_data.polyline.coordinates, { },
								{
									strokeColor: json_data.polyline.color,
									strokeWidth: json_data.polyline.width,
									strokeOpacity: json_data.polyline.opacity
								});
								yandexMap.geoObjects.add(yandexPolyline);
							}
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
	$content = preg_replace('/\[im\](\d+)\[\/im\]/is', $post_content, $content);
	return $content;
}
add_filter('the_content', 'im_bbcode');

?>
