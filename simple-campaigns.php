<?php 
/**
 * Plugin Name: Simple Campaigns
 * Plugin URI: http://wordpress.org/plugins/simple-campaigns
 * Description: Easily create campaigns and track with Gravity Forms
 * Version: 1.0.8
 * Author: WP Setup
 * Author URI: http://wpsetup.co
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'S_CAMPS' ) ) :


/**
 * Main S_CAMPS Class
 *
 * @since 1.0 */
final class S_CAMPS {

  /**
   * @var S_CAMPS Instance
   * @since 1.0
   */
  private static $instance;


  /**
   * S_CAMPS Instance / Constructor
   *
   * Insures only one instance of S_CAMPS exists in memory at any one
   * time & prevents needing to define globals all over the place. 
   * Inspired by and credit to S_CAMPS.
   *
   * @since 1.0
   * @static
   * @uses S_CAMPS::setup_globals() Setup the globals needed
   * @uses S_CAMPS::includes() Include the required files
   * @uses S_CAMPS::setup_actions() Setup the hooks and actions
   * @see S_CAMPS()
   * @return void
   */
  public static function instance() {
    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof S_CAMPS ) ) {
      self::$instance = new S_CAMPS;
      self::$instance->setup_constants();
      self::$instance->includes();
      // self::$instance->load_textdomain();
      // use @examples from public vars defined above upon implementation
    }
    return self::$instance;
  }



  /**
   * Setup plugin constants
   * @access private
   * @since 1.0 
   * @return void
   */
  private function setup_constants() {
    // Plugin version
    if ( ! defined( 'S_CAMPS_VERSION' ) )
      define( 'S_CAMPS_VERSION', '1.0.*' );

    // Plugin Folder Path
    if ( ! defined( 'S_CAMPS_PLUGIN_DIR' ) )
      define( 'S_CAMPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

    // Plugin Folder URL
    if ( ! defined( 'S_CAMPS_PLUGIN_URL' ) )
      define( 'S_CAMPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

    // Plugin Root File
    if ( ! defined( 'S_CAMPS_PLUGIN_FILE' ) )
      define( 'S_CAMPS_PLUGIN_FILE', __FILE__ );

    if ( ! defined( 'S_CAMPS_DEBUG' ) )
      define ( 'S_CAMPS_DEBUG', true );
  }



  /**
   * Include required files
   * @access private
   * @since 1.0
   * @return void
   */
  private function includes() {
    global $s_camps_settings, $wp_version;

    require_once S_CAMPS_PLUGIN_DIR . '/includes/admin/settings/register-settings.php';
    $s_camps_settings = s_camps_get_settings();

    // Required Plugin Files
    require_once S_CAMPS_PLUGIN_DIR . '/includes/class-feed.php';
    require_once S_CAMPS_PLUGIN_DIR . '/includes/functions.php';
    require_once S_CAMPS_PLUGIN_DIR . '/includes/posttypes.php';
    require_once S_CAMPS_PLUGIN_DIR . '/includes/scripts.php';
    require_once S_CAMPS_PLUGIN_DIR . '/includes/shortcodes.php';
    require_once S_CAMPS_PLUGIN_DIR . '/includes/admin/campaigns/functions.php';
    require_once S_CAMPS_PLUGIN_DIR . '/includes/numbers-functions.php';

    if( is_admin() ){
        //Admin Required Plugin Files
        
        require_once S_CAMPS_PLUGIN_DIR . '/includes/admin/campaigns/metabox.php';
        require_once S_CAMPS_PLUGIN_DIR . '/includes/admin/admin-pages.php';
        require_once S_CAMPS_PLUGIN_DIR . '/includes/admin/admin-notices.php';
        require_once S_CAMPS_PLUGIN_DIR . '/includes/admin/settings/display-settings.php';

    }

    require_once S_CAMPS_PLUGIN_DIR . '/includes/install.php';


  }

} /* end S_CAMPS class */
endif; // End if class_exists check


/**
 * Main function for returning S_CAMPS Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $sqcash = S_CAMPS(); ?>
 *
 * @since 1.0
 * @return object The one true S_CAMPS Instance
 */
function S_CAMPS() {
  return S_CAMPS::instance();
}


/**
 * Initiate
 * Run the S_CAMPS() function, which runs the instance of the S_CAMPS class.
 */
S_CAMPS();



/**
 * Debugging
 * @since 1.0
 */
if ( S_CAMPS_DEBUG ) {
  ini_set('display_errors','On');
  error_reporting(E_ALL);
}


