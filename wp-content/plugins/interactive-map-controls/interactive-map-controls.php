<?php
/*
Plugin Name: Interactive Map Controls
Description: Settings for interactive map.
Version: 1.0
Author: SoulTaker
Author URI: http://tst48.wordpress.com
*/


add_action('admin_menu', 'interactive_map_controls_admin_page');
function interactive_map_controls_admin_page() {
	add_menu_page( 
		'Настройки интерактивной карты', 'Интерактивная карта', 'manage_options', 'interactive_map_controls_admin_page', 'interactive_map_controls_admin_page_content', 'dashicons-location-alt', 85
	);
}
function interactive_map_controls_admin_page_content() {
	?>
	<div class="wrap">
	<h1>Настройки интерактивной карты</h1>
	<?php
	if(isset($_POST['submit'])) {
		if(wp_verify_nonce($_POST['_wpnonce'], 'interactive_map_controls_admin_page_update')) {
			update_option('interactive_map_latitude', floatval($_POST['latitude']));
			update_option('interactive_map_longitude', floatval($_POST['longitude']));
			update_option('interactive_map_zoom', intval($_POST['zoom']));
			?>
			<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
			<p><strong>Настройки сохранены.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Скрыть это уведомление.</span></button>
			</div>
			<?php
		}
	}
	?>
	<form method="post" action="admin.php?page=interactive_map_controls_admin_page" novalidate="novalidate">
	<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('interactive_map_controls_admin_page_update'); ?>">
	<table class="form-table">
	<tbody>
	<tr>
	<th scope="row"><label for="latitude">Широта</label></th>
	<td>
	<input name="latitude" type="text" id="latitude" value="<?php echo get_option('interactive_map_latitude'); ?>" class="regular-text" />
	<p class="description" id="latitude-description">Значение широты координат начальной точки.<br />Например: 53.210479</p>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="longitude">Долгота</label></th>
	<td>
	<input name="longitude" type="text" id="longitude" value="<?php echo get_option('interactive_map_longitude'); ?>" class="regular-text" />
	<p class="description" id="longitude-description">Значение широты координат начальной точки.<br />Например: 39.451272</p>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="zoom">Масштаб</label></th>
	<td>
	<input name="zoom" type="text" id="zoom" value="<?php echo intval(get_option('interactive_map_zoom')); ?>" class="regular-text" />
	<p class="description" id="zoom-description">Значение масштаба карты в начальной точке.<br />Диапазон допустимых значений: от 1 до 19.</p>
	</td>
	</tr>
	</tbody>
	</table>
	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Сохранить изменения" /></p>
	</form>
	</div>
	<?php
}

add_action('admin_menu', 'interactive_map_controls_points_page');
function interactive_map_controls_points_page() {
	add_submenu_page('interactive_map_controls_admin_page', 'Объекты интерактивной карты', 'Управление объектами', 'manage_options', 'interactive_map_controls_points_page', 'interactive_map_controls_points_page_content');
}
function interactive_map_controls_points_page_content() {
	global $wpdb;
	?>
	<div class="wrap">
	<h1 class="wp-heading-inline">Объекты интерактивной карты</h1>
	<?php
	switch($_GET['action']) {
		case 'add':
			$title = $latitude = $longitude = $preset = $url = '';
			if(isset($_POST['submit'])) {
				if(wp_verify_nonce($_POST['_wpnonce'], 'interactive_map_controls_points_page_add')) {
					$title = $_POST['title'];
					$latitude = $_POST['latitude'];
					$longitude = $_POST['longitude'];
					$preset = $_POST['preset'];
					$url = $_POST['url'];
					if(!empty($title) && !empty($latitude) && !empty($longitude) && !empty($preset) && !empty($url)) {
						$wpdb->query($wpdb->prepare('INSERT INTO `'.$wpdb->prefix.'interactive_map` (`title`, `latitude`, `longitude`, `preset`, `url`) VALUES (%s, %s, %s, %s, %s);', $title, floatval($latitude), floatval($longitude), $preset, $url));
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
			if(empty($preset)) $preset = 'islands#blueWorshipIcon';
			?>
			<form method="post" action="admin.php?page=interactive_map_controls_points_page&action=add" novalidate="novalidate">
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('interactive_map_controls_points_page_add'); ?>">
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
			<p class="description" id="preset-description">Тим метки для отображения на карте.<br />Полный список значений находится <a href="https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/option.presetStorage-docpage/" target="_blank">здесь</a>.</p>
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
			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Сохранить изменения" /></p>
			</form>
			<?php
			break;
		case 'edit':
			$point_id = $_REQUEST['point'];
			if($results = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'interactive_map` WHERE `point_id`=%d LIMIT 1;', $point_id))) {
				$point_id = $results[0]->point_id;
				$title = $results[0]->title;
				$latitude = $results[0]->latitude;
				$longitude = $results[0]->longitude;
				$preset = $results[0]->preset;
				$url = $results[0]->url;
				if(isset($_POST['submit'])) {
					if(wp_verify_nonce($_POST['_wpnonce'], 'interactive_map_controls_points_page_edit')) {
						$title = $_POST['title'];
						$latitude = $_POST['latitude'];
						$longitude = $_POST['longitude'];
						$preset = $_POST['preset'];
						$url = $_POST['url'];
						if(!empty($title) && !empty($latitude) && !empty($longitude) && !empty($preset) && !empty($url)) {
							$wpdb->query($wpdb->prepare('UPDATE `'.$wpdb->prefix.'interactive_map` SET `title`=%s, `latitude`=%s, `longitude`=%s, `preset`=%s, `url`=%s WHERE `point_id`=%d LIMIT 1;', $title, floatval($latitude), floatval($longitude), $preset, $url, $point_id));
							if($results = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'interactive_map` WHERE `point_id`=%d LIMIT 1;', $point_id))) {
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
				if(empty($preset)) $preset = 'islands#blueWorshipIcon';
				?>
				<form method="post" action="admin.php?page=interactive_map_controls_points_page&action=edit&point=<?php echo htmlspecialchars($point_id); ?>" novalidate="novalidate">
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('interactive_map_controls_points_page_edit'); ?>">
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
				<p class="description" id="preset-description">Тим метки для отображения на карте.<br />Полный список значений находится <a href="https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/option.presetStorage-docpage/" target="_blank">здесь</a>.</p>
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
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Сохранить изменения" /></p>
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
		case 'delete':
			$point = $_REQUEST['point'];
			if(!empty($point)) {
				if(is_string($point)) {
					$point_string = $point;
					$point = array($point_string);
					$nonce_name = 'interactive_map_controls_points_page_delete_'.$point_string;
				} else {
					$nonce_name  = 'interactive_map_controls_points_page_delete_selected';
				}
				if(wp_verify_nonce($_REQUEST['_wpnonce'], $nonce_name)) {
					foreach($point as $current_point) {
						$wpdb->query($wpdb->prepare('DELETE FROM `'.$wpdb->prefix.'interactive_map` WHERE `point_id`=%d;', $current_point));
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
			<a href="admin.php?page=interactive_map_controls_points_page&action=add" class="page-title-action">Добавить</a>
			<hr class="wp-header-end" />
			<form id="posts-filter" method="post" action="admin.php?page=interactive_map_controls_points_page&action=delete">
			<?php wp_nonce_field('interactive_map_controls_points_page_delete_selected'); ?>
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
			<th scope="col" id="url" class="manage-column column-url">Ссылка</th>
			</tr>
			</thead>
			<tbody id="the-list">
			<?php
			$results = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'interactive_map`;');
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
							<span class="edit"><a href="admin.php?page=interactive_map_controls_points_page&action=edit&point=<?php echo $point->point_id; ?>" aria-label="Редактировать &laquo;<?php echo $point->title; ?>&raquo;">Изменить</a> | </span>
							<span class="delete"><a href="admin.php?page=interactive_map_controls_points_page&action=delete&point=<?php echo $point->point_id; ?>&_wpnonce=<?php echo wp_create_nonce('interactive_map_controls_points_page_delete_'.$point->point_id); ?>" class="submitdelete" aria-label="Удалить &laquo;<?php echo $point->title; ?>&raquo;">Удалить</a></span>
						</div>
					</td>
					<td class="coordinates column-coordinates" data-colname="Координаты"><a href="https://yandex.ru/maps/?text=<?php echo urlencode(floatval($point->latitude).', '.floatval($point->longitude)); ?>" target="_blank"><?php echo floatval($point->latitude).', '.floatval($point->longitude); ?></a></td>
					<td class="url column-url" data-colname="Ссылка"><a href="<?php echo $point->url; ?>" target="_blank"><?php echo $point->url; ?></a></td>
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
			<th scope="col" id="url" class="manage-column column-url">Ссылка</th>
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
	?>
	</div>
	<?php
}

?>