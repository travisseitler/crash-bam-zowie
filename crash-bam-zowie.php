<?php
/**
 * @package    WordPress
 * @subpackage Crash_Bam_Zowie
 * @link       https://github.com/travisseitler/crash-bam-zowie
 * @version    0.5
 * @author     Travis Seitler <travis@webseitler.com>
 * @copyright  Copyright (c) 2016, Travis Seitler
 * @license    https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @wordpress-plugin
 * Plugin Name: CRASH! BAM! ZOWIE!
 * Plugin URI:  https://github.com/travisseitler/crash-bam-zowie
 * Description: Easily manage and publish your webcomics with WordPress.
 * Text Domain: crash-bam-zowie
 * Version:     0.5
 * Domain Path: /languages/
 * Author:      Travis Seitler
 * Author URI:  http://www.webseitler.com/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Includes the class that provides the functionality for the plugin
include_once( 'class-crash-bam-zowie.php' );

/**
 * Instantiates the plugin and initializes the functionality necessary for WordPress
 *
 * @since 0.1.1
 */
$crash_bam_zowie_plugin = new Crash_Bam_Zowie();
$crash_bam_zowie_plugin->init();

/**
 * Since we define some custom rewrite rules in this plugin, we'll automatically
 * flush the rewrite rules cache any time it's activated or deactivated.
 *
 * @since 0.1.1
 */
function crash_bam_zowie_flush_rewrites() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'crash_bam_zowie_flush_rewrites' );
register_deactivation_hook( __FILE__, 'crash_bam_zowie_flush_rewrites' );
