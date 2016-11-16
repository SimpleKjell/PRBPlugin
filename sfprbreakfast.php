<?php
/*
Plugin Name: Pinkribbonbreakfast
Plugin URI: http://simplefox.de
Description: Spenden Übersicht Plugin für Pinkribbonbreakfast
Version: 1.0.0
Author: Simplefox
Author URI: http://simplefox.de
*/

define('sfprbreakfast_url',plugin_dir_url(__FILE__ ));
define('sfprbreakfast_path',plugin_dir_path(__FILE__ ));
define('sfprbreakfast_template','basic');

// Plugin Version
function sfprbreakfast_get_plugin_version()
{
    $default_headers = array( 'Version' => 'Version' );
    $plugin_data = get_file_data( __FILE__, $default_headers, 'plugin' );
    return $plugin_data['Version'];
}

$plugin = plugin_basename(__FILE__);


/* Textdomain (localization) */
function sfprbreakfast_load_textdomain()
{
  $locale = apply_filters( 'plugin_locale', get_locale(), 'sf-prbreakfast' );
  $mofile = sfprbreakfast_path . "languages/sfprbreakfast-$locale.mo";

	// Global + Frontend Locale
	load_textdomain( 'sfprbreakfast', $mofile );
	load_plugin_textdomain( 'sfprbreakfast', false, dirname(plugin_basename(__FILE__)).'/languages/' );
}
add_action('init', 'sfprbreakfast_load_textdomain');

/* Master Class  */
require_once (sfprbreakfast_path . 'prbclasses/prb.prbreakfast.class.php');
$pr_breakfast = new PRBreakfast();
$pr_breakfast->plugin_init();
