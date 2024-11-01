<?php
/**
 * Post Type Functions
 *
 * @package     S_CAMPS
 * @subpackage  Functions
 * @copyright   Copyright (c) 2014, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers and sets up the Downloads custom post type
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return void
 */
function setup_s_camps_post_types() {
	global $s_camps_settings;

	//Check to see if anything is set in the settings area.
	if( !empty( $s_camps_settings['campaign_slug'] ) ) {
	    $slug = defined( 'S_CAMPS_SLUG' ) ? S_CAMPS_SLUG : $s_camps_settings['campaign_slug'];
	} else {
	    $slug = defined( 'S_CAMPS_SLUG' ) ? S_CAMPS_SLUG : 'campaigns';
	}

	if( !isset( $s_camps_settings['disable_archive'] ) ) {
	    $archives = true;
	}else{
	    $archives = false;
	}

	$exclude_from_search = isset( $s_camps_settings['exclude_from_search'] ) ? true : false;
	
	$rewrite  = defined( 'S_CAMPS_DISABLE_REWRITE' ) && S_CAMPS_DISABLE_REWRITE ? false : array('slug' => $slug, 'with_front' => false);

	$campaigns_labels =  apply_filters( 's_camps_campaigns_labels', array(
		'name' 				=> '%2$s',
		'singular_name' 	=> '%1$s',
		'add_new' 			=> __( 'Add New', 's_camps' ),
		'add_new_item' 		=> __( 'Add New %1$s', 's_camps' ),
		'edit_item' 		=> __( 'Edit %1$s', 's_camps' ),
		'new_item' 			=> __( 'New %1$s', 's_camps' ),
		'all_items' 		=> __( 'All %2$s', 's_camps' ),
		'view_item' 		=> __( 'View %1$s', 's_camps' ),
		'search_items' 		=> __( 'Search %2$s', 's_camps' ),
		'not_found' 		=> __( 'No %2$s found', 's_camps' ),
		'not_found_in_trash'=> __( 'No %2$s found in Trash', 's_camps' ),
		'parent_item_colon' => '',
		'menu_name' 		=> __( '%2$s', 's_camps' )
	) );

	foreach ( $campaigns_labels as $key => $value ) {
	   $campaigns_labels[ $key ] = sprintf( $value, s_camps_get_label_singular(), s_camps_get_label_plural() );
	}

	$campaigns_args = array(
		'labels'              => $campaigns_labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-groups',
		'query_var'           => true,
		'exclude_from_search' => $exclude_from_search,
		'rewrite'             => $rewrite,
		'map_meta_cap'        => true,
		'has_archive'         => $archives,
		'show_in_nav_menus'   => true,
		'hierarchical'        => false,
		'supports'            => apply_filters( 's_camps_supports', array( 'title', 'editor', 'thumbnail', 'excerpt' ) ),
	);
	register_post_type( 'campaigns', apply_filters( 's_camps_post_type_args', $campaigns_args ) );
	
}
add_action( 'init', 'setup_s_camps_post_types', 1 );

/**
 * Get Default Labels
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return array $defaults Default labels
 */
function s_camps_get_default_labels() {
	global $s_camps_settings;

	if( !empty( $s_camps_settings['campaigns_label_plural'] ) || !empty( $s_camps_settings['campaigns_label_singular'] ) ) {
	    $defaults = array(
	       'singular' => $s_camps_settings['campaigns_label_singular'],
	       'plural' => $s_camps_settings['campaigns_label_plural']
	    );
	 } else {
		$defaults = array(
		   'singular' => __( 'Campaign', 's_camps' ),
		   'plural' => __( 'Campaigns', 's_camps')
		);
	}
	
	return apply_filters( 's_camps_default_name', $defaults );

}

/**
 * Get Singular Label
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return string $defaults['singular'] Singular label
 */
function s_camps_get_label_singular( $lowercase = false ) {
	$defaults = s_camps_get_default_labels();
	return ($lowercase) ? strtolower( $defaults['singular'] ) : $defaults['singular'];
}

/**
 * Get Plural Label
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return string $defaults['plural'] Plural label
 */
function s_camps_get_label_plural( $lowercase = false ) {
	$defaults = s_camps_get_default_labels();
	return ( $lowercase ) ? strtolower( $defaults['plural'] ) : $defaults['plural'];
}

/**
 * Change default "Enter title here" input
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param string $title Default title placeholder text
 * @return string $title New placeholder text
 */
function s_camps_change_default_title( $title ) {
     $screen = get_current_screen();

     if  ( 's_camps' == $screen->post_type ) {
     	$label = s_camps_get_label_singular();
        $title = sprintf( __( 'Enter %s title here', 's_camps' ), $label );
     }

     return $title;
}
add_filter( 'enter_title_here', 's_camps_change_default_title' );

/**
 * Registers the custom taxonomies for the downloads custom post type
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return void
*/
function s_camps_setup_taxonomies() {

	$slug     = defined( 'S_CAMPS_SLUG' ) ? S_CAMPS_SLUG : 'campaigns';

	/** Categories */
	$category_labels = array(
		'name' 				=> sprintf( _x( '%s Categories', 'taxonomy general name', 's_camps' ), s_camps_get_label_singular() ),
		'singular_name' 	=> _x( 'Category', 'taxonomy singular name', 's_camps' ),
		'search_items' 		=> __( 'Search Categories', 's_camps'  ),
		'all_items' 		=> __( 'All Categories', 's_camps'  ),
		'parent_item' 		=> __( 'Parent Category', 's_camps'  ),
		'parent_item_colon' => __( 'Parent Category:', 's_camps'  ),
		'edit_item' 		=> __( 'Edit Category', 's_camps'  ),
		'update_item' 		=> __( 'Update Category', 's_camps'  ),
		'add_new_item' 		=> __( 'Add New Category', 's_camps'  ),
		'new_item_name' 	=> __( 'New Category Name', 's_camps'  ),
		'menu_name' 		=> __( 'Categories', 's_camps'  ),
	);

	$category_args = apply_filters( 's_camps_category_args', array(
			'hierarchical' 		=> true,
			'labels' 			=> apply_filters('s_camps_category_labels', $category_labels),
			'show_ui' 			=> true,
			'query_var' 		=> 'campaigns_category',
			'rewrite' 			=> array('slug' => $slug . '/category', 'with_front' => false, 'hierarchical' => true ),
			'capabilities'  	=> array( 'manage_terms','edit_terms', 'assign_terms', 'delete_terms' ),
			'show_admin_column'	=> true
		)
	);
	register_taxonomy( 'campaigns_category', array('campaigns'), $category_args );
	register_taxonomy_for_object_type( 'campaigns_category', 'campaigns' );

}
add_action( 'init', 's_camps_setup_taxonomies', 0 );



/**
 * Updated Messages
 *
 * Returns an array of with all updated messages.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $messages Post updated message
 * @return array $messages New post updated messages
 */
function s_camps_updated_messages( $messages ) {
	global $post, $post_ID;

	$url1 = '<a href="' . get_permalink( $post_ID ) . '">';
	$url2 = s_camps_get_label_singular();
	$url3 = '</a>';

	$messages['campaigns'] = array(
		1 => sprintf( __( '%2$s updated. %1$sView %2$s%3$s.', 's_camps' ), $url1, $url2, $url3 ),
		4 => sprintf( __( '%2$s updated. %1$sView %2$s%3$s.', 's_camps' ), $url1, $url2, $url3 ),
		6 => sprintf( __( '%2$s published. %1$sView %2$s%3$s.', 's_camps' ), $url1, $url2, $url3 ),
		7 => sprintf( __( '%2$s saved. %1$sView %2$s%3$s.', 's_camps' ), $url1, $url2, $url3 ),
		8 => sprintf( __( '%2$s submitted. %1$sView %2$s%3$s.', 's_camps' ), $url1, $url2, $url3 )
	);

	return $messages;
}
add_filter( 'post_updated_messages', 's_camps_updated_messages' );
