<?php
/**
 * Admin Pages
 *
 * @package     S_CAMPS
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2014, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;




/**
 * Creates the admin menu pages under Donately and assigns them their global variables
 *
 * @since  1.0
 * @author Bryan Monzon
 * @global  $s_camps_settings_page
 * @return void
 */
function s_camps_add_menu_page() {
    global $s_camps_settings_page;

    $s_camps_settings_page = add_submenu_page( 'edit.php?post_type=campaigns', __( 'Settings', 's_camps' ), __( 'Settings', 's_camps'), 'edit_pages', 'campaigns-settings', 's_camps_settings_page' );
    
}
add_action( 'admin_menu', 's_camps_add_menu_page', 11 );
