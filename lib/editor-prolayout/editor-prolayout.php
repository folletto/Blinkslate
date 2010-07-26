<?php
/*
Plugin Name: Editor ProLayout
Plugin URI: http://intenseminimalism.com
Description: This is a small plugin to add a few classes to the WYSIWYG post interface.
Author: Davide 'Folletto' Casali
Version: 10.1.2
Author URI: http://intenseminimalism.com
 ******************************************************************************************
 * Editor ProLayout
 * WP WYSIWYG enhancer for improved page layouts.
 * 
 * Last revision: 2010 01 02
 *
 * by Davide 'Folletto' Casali <folletto AT gmail DOT com>
 * intenseminimalism.com
 * Copyright (C) 2010 - Released under the BSD and GPL Licenses.
 *
 */

require 'WysiwygButtons.php';

// Just one line to add a button to TinyMCE. Nice class. ;)
$wb = new WysiwygButtons('prolayout', 'lib/editor-prolayout/editor-prolayout.js', array('plside', 'plhilight'));

if (!function_exists('prolayout_css') ) {
	function prolayout_css($wp) {
		$wp .= ',' . WP_PLUGIN_URL . '/tinymce-advanced/css/tadv-mce.css';
		
		if (strpos(__FILE__, '/themes/')) $pre = get_bloginfo('template_url') . '/'; // relative URL (template)
    else $pre = get_option('siteurl') . '/wp-content/plugins/editor-prolayout/'; // relative URL (plugin)
    
		return $wp . ',' . $pre . 'lib/editor-prolayout/css/prolayout.css';
	}
}
add_filter('mce_css', 'prolayout_css');

?>