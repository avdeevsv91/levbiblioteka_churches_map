<?php

/*
Template Name: Только контент
*/

/*
 LevBiblioteka Churches map
 Author: Sergey Avdeev
 E-Mail: avdeevsv91@gmail.com
 URL: https://github.com/avdeevsv91/levbiblioteka_churches_map
*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?> style="margin-top: 0 !important;">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url') ?>" type="text/css" media="screen" />
<!--[if IE 6]><link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style.ie6.css" type="text/css" media="screen" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style.ie7.css" type="text/css" media="screen" /><![endif]-->
<?php if(WP_VERSION < 3.0): ?>
<link rel="alternate" type="application/rss+xml" title="<?php printf(__('%s RSS Feed', THEME_NS), get_bloginfo('name')); ?>" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php printf(__('%s Atom Feed', THEME_NS), get_bloginfo('name')); ?>" href="<?php bloginfo('atom_url'); ?>" />
<?php endif; ?>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php if (is_file(TEMPLATEPATH .'/favicon.ico')):?>
<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/favicon.ico" />
<?php endif; ?>
<?php 
remove_action('wp_head', 'wp_generator');
wp_enqueue_script('jquery');
if (is_singular() && comments_open() && (get_option('thread_comments') == 1)) {
 wp_enqueue_script('comment-reply'); 
}
wp_head(); 
?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/script.js"></script>
</head>
<body <?php if(function_exists('body_class')) body_class(); ?>>
<?php

add_filter('show_admin_bar', '__return_false');

if(have_posts()) {
	while(have_posts()) {
		?>
		<div class="art-post post-<?php the_ID(); ?> page type-page status-publish hentry" id="post-<?php the_ID(); ?>">
		<div class="art-post-body">
		<div class="art-post-inner art-article">
		<div class="art-postcontent">
		<?php
		the_post();
		$edit_link = get_edit_post_link();
		if(current_user_can('edit_posts')) {
			echo '<div>[<a href="'.$edit_link.'" title="Откроется в новой вкладке" target="_blank">Редактировать</a>]</div>';
		}
		the_content();
		//art_post();
		?>
		</div>
		<div class="cleared"></div>
		</div>
		<div class="cleared"></div>
		</div>
		</div>
		<?php
	}
}

wp_footer();
?>
</body>
</html>
