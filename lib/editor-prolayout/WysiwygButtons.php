<?php
/******************************************************************************************
 * Wysiwyg Buttons
 * Helper class to add buttons to TinyMCE
 * 
 * Last revision: 2010 01 02
 *
 * by Davide 'Folletto' Casali <folletto AT gmail DOT com>
 * intenseminimalism.com
 * Copyright (C) 2010 - Released under the BSD and GPL Licenses.
 *
 *
 *
 * USAGE:
 *
 *   $wb = new WysiwygButtons('plugin-name', 'plugin-name.js', array('button1', 'button2'));
 *
 * Just create the class using the plugin name and the plugin file relative URL to
 * create it.
 *
 */
class WysiwygButtons {
  
  var $name = '';
  var $url_js = '';
  var $buttons = array();
  
  function WysiwygButtons($name, $url_js, $buttons) {
    /****************************************************************************************************
     * Create the new button
     * 
     * @param   plugin name
     * @param   plugin javascript relative URL
     * @param   array of buttons to be added
     */
    $this->name = $name;
    $this->buttons = $buttons;
    
    if (strpos($url_js, 'http://') !== false) $this->url_js = $url_js; // absolute URL
    else if (strpos(__FILE__, '/themes/')) $this->url_js = get_bloginfo('template_url') . '/' . $url_js; // relative URL (template)
    else $this->url_js = get_option('siteurl') . '/wp-content/plugins/' . $url_js; // relative URL (plugin)
    
    add_action('init', array($this, 'add_button'));
  }
  
  function add_button() {
    /****************************************************************************************************
     * Add a button to TinyMCE
     */
    // Security check
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
    
    // Add only in Rich Editor mode
    if (get_user_option('rich_editing') == 'true') {
      add_filter("mce_external_plugins", array(&$this, 'attach_js'));
      add_filter('mce_buttons', array(&$this, 'attach_button'));
    }
  }
  
  function attach_button($buttons) {
    /****************************************************************************************************
     * Add the button (by name) to the TinyMCE buttons array
     */
    //array_push($buttons, "separator", $this->name, 'prolayouthilight');
    $buttons = array_merge($buttons, array("separator"), $this->buttons);
    return $buttons;
  }
  function attach_js($plugin_array) {
    /****************************************************************************************************
     * Add the javascript code (by uri) to the TinyMCE plugins array
     */
    $plugin_array[$this->name] = $this->url_js;
    return $plugin_array;
  }
}
?>