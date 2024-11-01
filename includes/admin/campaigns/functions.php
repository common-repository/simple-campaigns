<?php
/**
 * Campaign Related Functions
 */

/**
 * Get the URL of the donation page
 *
 * @since 1.0.8
 * @global $s_camps_settings Array of all the Simple Campaigns Options
 * @param array $args Extra query args to add to the URI
 * @return mixed Full URL to the checkout page, if present | null if it doesn't exist
 */
function s_camps_get_donation_page_uri( $args = array() ) {
    global $s_camps_settings;

    $uri = isset( $s_camps_settings['donation_page'] ) ? get_permalink( $s_camps_settings['donation_page'] ) : NULL;

    if ( ! empty( $args ) ) {
        // Check for backward compatibility
        if ( is_string( $args ) )
            $args = str_replace( '?', '', $args );

        $args = wp_parse_args( $args );

        $uri = add_query_arg( $args, $uri );
    }

    $scheme = defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ? 'https' : 'admin';

    $ajax_url = admin_url( 'admin-ajax.php', $scheme );

    if ( ! preg_match( '/^https/', $uri ) && preg_match( '/^https/', $ajax_url ) ) {
        $uri = preg_replace( '/^http/', 'https', $uri );
    }


    return apply_filters( 's_camps_get_donation_page_uri', $uri );
}


/**
 * Get Donate Link
 *
 * Builds a URL to create a dynamic donation link based on the campaign ID.
 * @param  [type] $campaign_id [description]
 * @return [type]              [description]
 */
function s_camps_donate_link( $args = array() ) {
    global $post, $s_camps_settings;


    $defaults = apply_filters( 's_camps_donation_link_defaults', array(
        'campaign_id' => '',
        'type'        => 'one-time',
        'text'        => !empty( $s_camps_settings[ 'donation_button_text' ] ) ? $s_camps_settings[ 'donation_button_text' ] : __( 'Donate', 's_camps' ),
        'amount'      => isset( $args['amount'] ) ? $args['amount'] : '',
        'class'       => 'button'
    ) );


    $args = wp_parse_args( $args, $defaults );


    if( !empty( $args['campaign_id'] ) && !empty( $args['amount'] ) ) {
        
        $append_url = '?cid=' . $args['campaign_id'] . '&amount=' . $args['amount'] . '&type=' . $args['type'];

    }elseif( !empty( $args['campaign_id'] ) ) {
        
        $append_url = '?cid=' . $args['campaign_id'] . '&type=' . $args['type'];
    }else{
        $append_url = '';
    }
    
?>

    <div class="donation_button_wrapper">
        <?php 
        printf(
        '<a href="%2$s" class="%3$s">%4$s</a>',
        implode( ' ', array( trim( $args['class'] ), $args['text'] ) ),
        esc_url( s_camps_get_donation_page_uri() . $append_url ),
        esc_attr( $args['class'] ),
        esc_attr( $args['text'] )
        )
        ?>
    </div>
<?php

    return apply_filters( 's_camps_donately_button', $args );
}




