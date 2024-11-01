<?php
/**
 * Admin Notices
 *
 * @package     S_CAMPS
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2014, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin Messages
 *
 * @since  1.0
 * @author Bryan Monzon
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @return void
 */
function s_camps_admin_messages() {
    global $s_camps_settings;

    settings_errors( 's_camps-notices' );
}
add_action( 'admin_notices', 's_camps_admin_messages' );


/**
 * Dismisses admin notices when Dismiss links are clicked
 *
 * @since 1.0
 * @return void
*/
function s_camps_dismiss_notices() {

    $notice = isset( $_GET['s_camps_notice'] ) ? $_GET['s_camps_notice'] : false;

    if( ! $notice )
        return; // No notice, so get out of here

    update_user_meta( get_current_user_id(), '_s_camps_' . $notice . '_dismissed', 1 );

    wp_redirect( remove_query_arg( array( 's_camps_action', 's_camps_notice' ) ) ); exit;

}
add_action( 's_camps_dismiss_notices', 's_camps_dismiss_notices' );
