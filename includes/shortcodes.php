<?php
/**
 * Shortcodes
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


function s_camps_donate_button_shortcode( $atts, $content=null){

    global $post, $s_camps_settings;

    extract( shortcode_atts( array(
        'campaign_id'   => '',
        'class'         => !empty( $class ) ? $class : 'button',
        'amount'        => '',
        'type'          => 'one-time'
    ), $atts ) );
 
    //Is the Campaign ID set?
    $campaign_id = isset( $campaign_id ) ? $campaign_id : '';

    //Fallback for button text
    $button_text = isset( $s_camps_settings['donation_button_text'] ) ? $s_camps_settings['donation_button_text'] : '';
    $content     = !empty( $content ) ? $content : $button_text;

    ob_start();
    s_camps_donate_link( array(
        'campaign_id' => $campaign_id,
        'amount'      => $amount,
        'class'       => $class,
        'text'        => $content, 
        'type'        => $type
        )
    );

    return ob_get_clean();
}
add_shortcode('donate_button', 's_camps_donate_button_shortcode');


/**
 * Simple shortcode to display total raised. 
 *
 * @since  1.0.7
 * @param  [type] $atts    [description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function s_camps_total_raised_shortcode( $atts, $content = null ) 
{
    extract( shortcode_atts( array(
        'dollar_sign'   => true
    ), $atts ) );

    $sign = $dollar_sign ? '$' : null;
    return $sign . s_camps_get_total_raised();
}
add_shortcode( 'total_raised', 's_camps_total_raised_shortcode' );


function s_camps_total_campaigns_shortcode( $atts, $content=null)
{
    return s_camps_get_total_number_campaigns();
}
add_shortcode( 'total_campaigns', 's_camps_total_campaigns_shortcode' );