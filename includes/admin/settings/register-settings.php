<?php
/**
 * Register Settings
 *
 * @package     S_CAMPS
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2014, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return mixed
 */
function s_camps_get_option( $key = '', $default = false ) {
    global $s_camps_settings;
    return isset( $s_camps_settings[ $key ] ) ? $s_camps_settings[ $key ] : $default;
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return array S_CAMPS settings
 */
function s_camps_get_settings() {

    $settings = get_option( 's_camps_settings' );
    if( empty( $settings ) ) {

        // Update old settings with new single option

        $general_settings = is_array( get_option( 's_camps_settings_general' ) )    ? get_option( 's_camps_settings_general' )      : array();


        $settings = array_merge( $general_settings );

        update_option( 's_camps_settings', $settings );
    }
    return apply_filters( 's_camps_get_settings', $settings );
}

/**
 * Add all settings sections and fields
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return void
*/
function s_camps_register_settings() {

    if ( false == get_option( 's_camps_settings' ) ) {
        add_option( 's_camps_settings' );
    }

    foreach( s_camps_get_registered_settings() as $tab => $settings ) {

        add_settings_section(
            's_camps_settings_' . $tab,
            __return_null(),
            '__return_false',
            's_camps_settings_' . $tab
        );

        foreach ( $settings as $option ) {
            add_settings_field(
                's_camps_settings[' . $option['id'] . ']',
                $option['name'],
                function_exists( 's_camps_' . $option['type'] . '_callback' ) ? 's_camps_' . $option['type'] . '_callback' : 's_camps_missing_callback',
                's_camps_settings_' . $tab,
                's_camps_settings_' . $tab,
                array(
                    'id'      => $option['id'],
                    'desc'    => ! empty( $option['desc'] ) ? $option['desc'] : '',
                    'name'    => $option['name'],
                    'section' => $tab,
                    'size'    => isset( $option['size'] ) ? $option['size'] : null,
                    'options' => isset( $option['options'] ) ? $option['options'] : '',
                    'std'     => isset( $option['std'] ) ? $option['std'] : ''
                )
            );
        }

    }

    // Creates our settings in the options table
    register_setting( 's_camps_settings', 's_camps_settings', 's_camps_settings_sanitize' );

}
add_action('admin_init', 's_camps_register_settings');

/**
 * Retrieve the array of plugin settings
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return array
*/
function s_camps_get_registered_settings() {

    $pages = get_pages();
    $pages_options = array( 0 => '' ); // Blank option
    if ( $pages ) {
        foreach ( $pages as $page ) {
            $pages_options[ $page->ID ] = $page->post_title;
        }
    }

    if( class_exists( 'RGFormsModel' ) ) {
       $form_options = array( 0 => '' ); // Blank option
        $forms = RGFormsModel::get_forms( null, 'title' );
        if( $forms ) {
            foreach( $forms as $form ) {
                $form_options[$form->id] = $form->title;
            }
        }    
    }
    

    /**
     * 'Whitelisted' S_CAMPS settings, filters are provided for each settings
     * section to allow extensions and other plugins to add their own settings
     */
    $s_camps_settings = array(
        /** General Settings */
        'general' => apply_filters( 's_camps_settings_general',
            array(
                'basic_settings' => array(
                    'id' => 'basic_settings',
                    'name' => '<strong>' . __( 'Basic Settings', 's_camps' ) . '</strong>',
                    'desc' => '',
                    'type' => 'header'
                ),
                'campaign_slug' => array(
                    'id' => 'campaign_slug',
                    'name' => __( s_camps_get_label_plural() . ' URL Slug', 's_camps' ),
                    'desc' => __( 'Enter the slug you would like to use for your ' . strtolower( s_camps_get_label_plural() ) . '. (<em>You will need to <a href="' . admin_url( 'options-permalink.php' ) . '">refresh permalinks</a>, after saving changes</em>).'  , 's_camps' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => strtolower( s_camps_get_label_plural() )
                ),
                'campaigns_label_plural' => array(
                    'id' => 'campaigns_label_plural',
                    'name' => __( s_camps_get_label_plural() . ' Label Plural', 's_camps' ),
                    'desc' => __( 'Enter the label you would like to use for your ' . strtolower( s_camps_get_label_plural() ) . '.', 's_camps' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => s_camps_get_label_plural()
                ),
                'campaigns_label_singular' => array(
                    'id' => 'campaigns_label_singular',
                    'name' => __( s_camps_get_label_singular() . ' Label Singular', 's_camps' ),
                    'desc' => __( 'Enter the label you would like to use for your ' . strtolower( s_camps_get_label_singular() ) . '.', 's_camps' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => s_camps_get_label_singular()
                ),
                'disable_archive' => array(
                    'id' => 'disable_archive',
                    'name' => __( 'Disable Archives Page', 's_camps' ),
                    'desc' => __( 'Check to disable archives page. (<em>You might need to <a href="' . admin_url( 'options-permalink.php' ) . '">refresh permalinks</a>, after saving changes</em>).', 's_camps' ),
                    'type' => 'checkbox',
                    'std' => ''
                ),
                'exclude_from_search' => array(
                    'id' => 'exclude_from_search',
                    'name' => __( 'Exclude from Search', 's_camps' ),
                    'desc' => __( 'Check to exclude from search. (<em>You might need to <a href="' . admin_url( 'options-permalink.php' ) . '">refresh permalinks</a>, after saving changes</em>)', 's_camps' ),
                    'type' => 'checkbox',
                    'std' => ''
                ),
                'disable_after_campaign_content' => array(
                    'id' => 'disable_after_campaign_content',
                    'name' => __( 'Disable Button', 's_camps' ),
                    'desc' => __( 'Check to remove the donate button after the campaign content.', 's_camps' ),
                    'type' => 'checkbox',
                    'std' => ''
                ),
                'gravity_donation_form' => array(
                    'id'      => 'gravity_donation_form',
                    'name'    => __( 'Donation Form', 's_camps' ),
                    'desc'    => __( 'This is the form you will use to process donations', 's_camps' ),
                    'type'    => 'select',
                    'options' => $form_options
                ),
                'donation_page' => array(
                    'id'      => 'donation_page',
                    'name'    => __( 'Donation Page', 's_camps' ),
                    'desc'    => __( 'This is the page where all of donations will be processed', 's_camps' ),
                    'type'    => 'select',
                    'options' => $pages_options
                ),
                'donation_button_text' => array(
                    'id' => 'donation_button_text',
                    'name' => __( 'Donation Button Text', 's_camps' ),
                    'desc' => __( 'Use custom text for your donation button', 's_camps' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => ''
                ),
            )
        ),
        
    );

    return $s_camps_settings;
}

/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @return void
 */
function s_camps_header_callback( $args ) {
    $html = '<label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';
    echo $html;
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @return void
 */
function s_camps_checkbox_callback( $args ) {
    global $s_camps_settings;

    $checked = isset($s_camps_settings[$args['id']]) ? checked(1, $s_camps_settings[$args['id']], false) : '';
    $html = '<input type="checkbox" id="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" name="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/>';
    $html .= '<label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @return void
 */
function s_camps_multicheck_callback( $args ) {
    global $s_camps_settings;

    foreach( $args['options'] as $key => $option ):
        if( isset( $s_camps_settings[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
        echo '<input name="s_camps_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" id="s_camps_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
        echo '<label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
    endforeach;
    echo '<p class="description">' . $args['desc'] . '</p>';
}

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @return void
 */
function s_camps_radio_callback( $args ) {
    global $s_camps_settings;

    foreach ( $args['options'] as $key => $option ) :
        $checked = false;

        if ( isset( $s_camps_settings[ $args['id'] ] ) && $s_camps_settings[ $args['id'] ] == $key )
            $checked = true;
        elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $s_camps_settings[ $args['id'] ] ) )
            $checked = true;

        echo '<input name="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"" id="s_camps_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
        echo '<label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
    endforeach;

    echo '<p class="description">' . $args['desc'] . '</p>';
}



/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @return void
 */
function s_camps_text_callback( $args ) {
    global $s_camps_settings;

    if ( isset( $s_camps_settings[ $args['id'] ] ) )
        $value = $s_camps_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text" id="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" name="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}


/**
 * S_CAMPS Hidden Text Field Callback
 *
 * Renders text fields (Hidden, for necessary values in s_camps_settings in the wp_options table)
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @return void
 * @todo refactor it is not needed entirely
 */
function s_camps_hidden_callback( $args ) {
    global $s_camps_settings;

    $hidden = isset($args['hidden']) ? $args['hidden'] : false;

    if ( isset( $s_camps_settings[ $args['id'] ] ) )
        $value = $s_camps_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="hidden" class="' . $size . '-text" id="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" name="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['std'] . '</label>';

    echo $html;
}




/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @return void
 */
function s_camps_textarea_callback( $args ) {
    global $s_camps_settings;

    if ( isset( $s_camps_settings[ $args['id'] ] ) )
        $value = $s_camps_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<textarea class="large-text" cols="50" rows="5" id="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" name="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    $html .= '<label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @return void
 */
function s_camps_password_callback( $args ) {
    global $s_camps_settings;

    if ( isset( $s_camps_settings[ $args['id'] ] ) )
        $value = $s_camps_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="password" class="' . $size . '-text" id="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" name="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
    $html .= '<label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @return void
 */
function s_camps_missing_callback($args) {
    printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 's_camps' ), $args['id'] );
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @return void
 */
function s_camps_select_callback($args) {
    global $s_camps_settings;

    if ( isset( $s_camps_settings[ $args['id'] ] ) )
        $value = $s_camps_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" name="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $name ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @return void
 */
function s_camps_color_select_callback( $args ) {
    global $s_camps_settings;

    if ( isset( $s_camps_settings[ $args['id'] ] ) )
        $value = $s_camps_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" name="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $color ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $color['label'] . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @global $wp_version WordPress Version
 */
function s_camps_rich_editor_callback( $args ) {
    global $s_camps_settings, $wp_version;

    if ( isset( $s_camps_settings[ $args['id'] ] ) )
        $value = $s_camps_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    if ( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
        $html = wp_editor( stripslashes( $value ), 's_camps_settings_' . $args['section'] . '[' . $args['id'] . ']', array( 'textarea_name' => 's_camps_settings_' . $args['section'] . '[' . $args['id'] . ']' ) );
    } else {
        $html = '<textarea class="large-text" rows="10" id="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" name="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    }

    $html .= '<br/><label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @return void
 */
function s_camps_upload_callback( $args ) {
    global $s_camps_settings;

    if ( isset( $s_camps_settings[ $args['id'] ] ) )
        $value = $s_camps_settings[$args['id']];
    else
        $value = isset($args['std']) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text s_camps_upload_field" id="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" name="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<span>&nbsp;<input type="button" class="s_camps_settings_upload_button button-secondary" value="' . __( 'Upload File', 's_camps' ) . '"/></span>';
    $html .= '<label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $s_camps_settings Array of all the S_CAMPS Options
 * @return void
 */
function s_camps_color_callback( $args ) {
    global $s_camps_settings;

    if ( isset( $s_camps_settings[ $args['id'] ] ) )
        $value = $s_camps_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $default = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="s_camps-color-picker" id="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" name="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
    $html .= '<label for="s_camps_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}



/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @return void
 */
function s_camps_hook_callback( $args ) {
    do_action( 's_camps_' . $args['id'] );


    
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $input The value inputted in the field
 * @return string $input Sanitizied value
 */
function s_camps_settings_sanitize( $input = array() ) {

    global $s_camps_settings;

    parse_str( $_POST['_wp_http_referer'], $referrer );

    $output    = array();
    $settings  = s_camps_get_registered_settings();
    $tab       = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
    $post_data = isset( $_POST[ 's_camps_settings_' . $tab ] ) ? $_POST[ 's_camps_settings_' . $tab ] : array();

    $input = apply_filters( 's_camps_settings_' . $tab . '_sanitize', $post_data );

    // Loop through each setting being saved and pass it through a sanitization filter
    foreach( $input as $key => $value ) {

        // Get the setting type (checkbox, select, etc)
        $type = isset( $settings[ $key ][ 'type' ] ) ? $settings[ $key ][ 'type' ] : false;

        if( $type ) {
            // Field type specific filter
            $output[ $key ] = apply_filters( 's_camps_settings_sanitize_' . $type, $value, $key );
        }

        // General filter
        $output[ $key ] = apply_filters( 's_camps_settings_sanitize', $value, $key );
    }


    // Loop through the whitelist and unset any that are empty for the tab being saved
    if( ! empty( $settings[ $tab ] ) ) {
        foreach( $settings[ $tab ] as $key => $value ) {

            // settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
            if( is_numeric( $key ) ) {
                $key = $value['id'];
            }

            if( empty( $_POST[ 's_camps_settings_' . $tab ][ $key ] ) ) {
                unset( $s_camps_settings[ $key ] );
            }

        }
    }

    // Merge our new settings with the existing
    $output = array_merge( $s_camps_settings, $output );

    // @TODO: Get Notices Working in the backend.
    add_settings_error( 's_camps-notices', '', __( 'Settings Updated', 's_camps' ), 'updated' );

    return $output;

}

/**
 * Sanitize text fields
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function s_camps_sanitize_text_field( $input ) {
    return trim( $input );
}
add_filter( 's_camps_settings_sanitize_text', 's_camps_sanitize_text_field' );

/**
 * Retrieve settings tabs
 * @since  1.0
 * @author Bryan Monzon
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function s_camps_get_settings_tabs() {

    $settings = s_camps_get_registered_settings();

    $tabs            = array();
    $tabs['general'] = __( 'General', 's_camps' );

    return apply_filters( 's_camps_settings_tabs', $tabs );
}
