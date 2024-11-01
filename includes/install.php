<?php
/**
 * Install Function
 *
 * @package     S_CAMPS
 * @subpackage  Functions/Install
 * @copyright   Copyright (c) 2014, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Install
 *
 * Runs on plugin install by setting up the post types, custom taxonomies,
 * flushing rewrite rules to initiate the new 'campaigns' slug and also
 * creates the plugin and populates the settings fields for those plugin
 * pages. After successful install, the user is redirected to the S_CAMPS Welcome
 * screen.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @global $wpdb
 * @global $s_camps_settings
 * @global $wp_version
 * @return void
 */
function s_camps_install() {
    global $wpdb, $s_camps_settings, $wp_version;

    // Setup the Downloads Custom Post Type
    setup_s_camps_post_types();

    // Setup the Download Taxonomies
    s_camps_setup_taxonomies();

    // Clear the permalinks
    flush_rewrite_rules();

    // Add Upgraded From Option
    $current_version = get_option( 's_camps_version' );
    if ( $current_version ) {
        update_option( 's_camps_version_upgraded_from', $current_version );
    }

    // Bail if activating from network, or bulk
    if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
        return;
    }

    // Add the transient to redirect
    set_transient( '_s_camps_activation_redirect', true, 30 );
}
register_activation_hook( S_CAMPS_PLUGIN_FILE, 's_camps_install' );


/**
 * Post-installation
 *
 * Runs just after plugin installation and exposes the
 * s_camps_after_install hook.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return void
 */
function s_camps_after_install() {

    if ( ! is_admin() ) {
        return;
    }

    $activation_pages = get_transient( '_s_camps_activation_pages' );

    // Exit if not in admin or the transient doesn't exist
    if ( false === $activation_pages ) {
        return;
    }

    // Delete the transient
    delete_transient( '_s_camps_activation_pages' );

    do_action( 's_camps_after_install', $activation_pages );
}
add_action( 'admin_init', 's_camps_after_install' );