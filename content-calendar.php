<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://nikhil.wisdmlabs.net
 * @since             1.0.0
 * @package           Content_Calendar
 *
 * @wordpress-plugin
 * Plugin Name:       Content Calendar
 * Plugin URI:        https://content-calendar.com
 * Description:       Plugin that allows the admin to create a content calendar for content publishing. 
 * Inputs:
 * 1. Date (date)
 * 2. Occasion (e.g. Holi)
 * 3. Post Title 
 * 4. Author -- (it should be one of the WordPress users)
 * 5. Reviewer (it should be one of the WordPress users other than the author)

 * Currently, Display a simple table
 * Version:           1.0.0
 * Author:            Nikhil Mhaske
 * Author URI:        https://nikhil.wisdmlabs.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       content-calendar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('CONTENT_CALENDAR_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-content-calendar-activator.php
 */
function activate_content_calendar()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-content-calendar-activator.php';
	Content_Calendar_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-content-calendar-deactivator.php
 */
function deactivate_content_calendar()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-content-calendar-deactivator.php';
	Content_Calendar_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_content_calendar');
register_deactivation_hook(__FILE__, 'deactivate_content_calendar');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-content-calendar.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

//enqueue CSS
require plugin_dir_path(__FILE__) . 'scripts.php';

//Create Database
// Create a new database table
function cc_create_table()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'cc_data';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	  id mediumint(9) AUTO_INCREMENT,
	  date date NOT NULL,
	  occasion varchar(255) NOT NULL,
	  post_title varchar(255) NOT NULL,
	  author int(11) NOT NULL,
	  reviewer varchar(255) NOT NULL,
	  PRIMARY KEY  (id)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

register_activation_hook(__FILE__, 'cc_create_table');


function run_content_calendar()
{

	$plugin = new Content_Calendar();
	$plugin->run();
}
run_content_calendar();

?>