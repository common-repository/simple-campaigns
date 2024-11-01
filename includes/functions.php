<?php
/**
 * Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Provides a hook to display anything after the_content only on campaign pages.
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function scamps_after_campaign_content( $content ) 
{
    global $post;

    if ( $post && $post->post_type == 'campaigns' && is_singular( 'campaigns' ) && is_main_query() && !post_password_required() ) {
        ob_start();
        do_action( 'scamps_after_campaign_content', $post->ID );
        $content .= ob_get_clean();
    }

    return $content;

}
add_filter( 'the_content', 'scamps_after_campaign_content' );


/**
 * Grabs the button text set in settings. If nothing is set it defaults to "Donate"
 * @return [type] [description]
 */
function s_camps_get_button_text()
{
    global $s_camps_settings;

    $button_text = isset( $s_camps_settings['donation_button_text'] ) ? $s_camps_settings['donation_button_text'] : 'Donate';

    return $button_text;
}


/**
 * Appends campaign content with a button. Can be turnded off in the settings area
 * @return [type] [description]
 */
function scamps_append_donate_link() {
    global $post, $s_camps_settings;

    if ( !isset( $s_camps_settings['disable_after_campaign_content'] ) ) {
        $type  = get_post_meta( $post->ID, 's_camps_donation_type', true);
        $class = get_post_meta( $post->ID, 's_camps_button_class', true);
        $text  = get_post_meta( $post->ID, 's_camps_button_text', true);

        $type  = !empty( $type ) ? $type : 'one-time';
        $class = !empty( $class ) ? $class : 'button';
        $text  = !empty( $text ) ? $text : s_camps_get_button_text();

        return s_camps_donate_link( array( 'campaign_id' => $post->ID, 'type' => $type, 'class' => $class, 'text' => $text ) );
    }
}
add_action( 'scamps_after_campaign_content', 'scamps_append_donate_link' );


/**
 * If the provided form is imported, this should dynamically display what users are donating to.
 * @param  [type] $form [description]
 * @return [type]       [description]
 */
function s_camps_populate_html( $form )
{   

    $cid = isset(  $_REQUEST['cid'] ) ?  $_REQUEST['cid'] : '';

    $args = array(
        'post_type' => 'campaigns',
        'field'     => 'ids'
    );
    $campaign_ids = new WP_Query( $args );
    $campaign_ids = $campaign_ids->posts;

    $exists = false;
    foreach( $campaign_ids as $c ) {
        if( $c->ID == $cid ) {
            $exists = true;
        }
    }

    if( $exists == true ) {

        foreach( $form['fields'] as $field ) {
            if( $field['id'] == '10' && $field['type'] == 'html') {
                if( !empty( $cid ) ) {
                    
                    $campaign = get_post( $cid );

                    $content = '<div class="campaign-title"><span>You are supporting <a href="' . get_permalink( $campaign->ID ) .'" class="campaign-title-link">' . $campaign->post_title . '</a></span></div>';
                    
                    foreach( $form['fields'] as &$field ) {
                        //get html field
                        if ( $field["id"] == 10 ) {

                            //set the field content to the html
                            $field["content"] = apply_filters( 's_camps_campaign_title' , $content );
                        }
                    }
                }
            }
        }
    }elseif( $exists == false && isset( $_REQUEST['cid'] ) ) {

        foreach( $form['fields'] as $field ) {
            if( $field['id'] == '10' && $field['type'] == 'html') {
                if( !empty( $cid ) ) {

                    $content = '<div class="campaign-title"><span>You are not donating to a specific campaign.</span></div>';
                    
                    foreach( $form['fields'] as &$field ) {
                        //get html field
                        if ( $field["id"] == 10 ) {

                            //set the field content to the html
                            $field["content"] = $content;
                        }
                    }
                }
            }
        }
    }

    return $form;

}
add_filter( 'gform_pre_render', 's_camps_populate_html' );


/**
 * Helper function to calculate the percent raised
 * 
 * @param  [int] $raised
 * @param  [int] $goal  
 * @return [type]        
 */
function get_percent_funded( $raised = null, $goal = null)
{   

    if( isset( $raised) && isset( $goal ) ) {
        $percent = $raised / $goal * 100;    

        return $percent;
    } 
    return false;

}


/**
 * Gets the campaign goal
 * @param  [type]  $post_id [description]
 * @param  boolean $format  [description]
 * @return [type]           [description]
 */
function sc_get_campaign_goal( $post_id = null, $format = true )
{
    global $post;

    $post_id = isset( $post_id ) ? $post_id : $post->ID;

    $goal = get_post_meta( $post_id, 's_camps_goal', true );
    $goal = !empty( $goal ) ? $goal : 0;
    $goal = ( !$format ) ? $goal : number_format( $goal ); 

    return isset( $goal ) ? $goal : false;
}

/**
 * Get the amount that's been raised so far.
 * @param  [type]  $post_id [description]
 * @param  boolean $format  [description]
 * @return [type]           [description]
 */
function sc_get_campaign_raised( $post_id = null, $format = true )
{
    global $post;

    $post_id = isset( $post_id ) ? $post_id : $post->ID;

    $raised = get_post_meta( $post->ID, 's_camps_amount_raised', true );
    $raised = !empty( $raised ) ? $raised : 0;
    $raised = ( $format == false ) ? '$' . $raised : '$' .number_format( $raised ); 
    
    return isset( $raised ) ? $raised : false;
}


/**
 * Get the donor count
 * @param  [type]  $post_id [description]
 * @param  boolean $format  [description]
 * @return [type]           [description]
 */
function sc_get_campaign_donors( $post_id = null, $format = true )
{
    global $post;

    $post_id = isset( $post_id ) ? $post_id : $post->ID;

    $donors = get_post_meta( $post->ID, 's_camps_donor_count', true );
    $donors = !empty( $donors ) ? $donors : 0;
    $donors = ( !$format ) ? $donors : number_format( $donors ); 
    
    return isset( $donors ) ? $donors : false;
}


function populate_s_camps_campaign_title( $value ) 
{
    global $post;
    
    $cid = isset( $_REQUEST['cid'] ) ? $_REQUEST['cid'] : $post->ID;
    $campaign = get_post( $cid );
        
    return $campaign->post_title;
    
}
add_filter( 'gform_field_value_campaign_title', 'populate_s_camps_campaign_title' );

