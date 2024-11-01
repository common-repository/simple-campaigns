<?php
/**
 * Metabox Functions
 *
 * @package     S_CAMPS
 * @subpackage  Admin/Classes
 * @copyright   Copyright (c) 2014, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register all the meta boxes for the Download custom post type
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return void
 */
function s_camps_add_meta_box() {


    $post_types = apply_filters( 's_camps_metabox_post_types' , array( 'campaigns' ) );

    foreach ( $post_types as $post_type ) {

        /** Class Configuration */
        add_meta_box( 'campaigndetails', sprintf( __( '%1$s Details', 's_camps' ), s_camps_get_label_singular(), s_camps_get_label_plural() ),  's_camps_render_meta_box', $post_type, 'side', 'core' );
        add_meta_box( 'donorlist', 'Donor List',  's_camps_render_donor_list_meta_box', $post_type, 'normal', 'core' );
        
    }
}
add_action( 'add_meta_boxes', 's_camps_add_meta_box' );


/**
 * Sabe post meta when the save_post action is called
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param int $post_id Download (Post) ID
 * @global array $post All the data of the the current post
 * @return void
 */
function s_camps_meta_box_save( $post_id) {
    global $post, $s_camps_settings;

    if ( ! isset( $_POST['s_camps_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['s_camps_meta_box_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) )
        return $post_id;

    if ( isset( $post->post_type ) && $post->post_type == 'revision' )
        return $post_id;




    // The default fields that get saved
    $fields = apply_filters( 's_camps_metabox_fields_save', array(
            's_camps_goal',
            's_camps_default_donation_amount',
            's_camps_donation_type',
            's_camps_button_class',
            's_camps_button_text'


        )
    );


    foreach ( $fields as $field ) {
        if ( ! empty( $_POST[ $field ] ) ) {
            $new = apply_filters( 's_camps_metabox_save_' . $field, $_POST[ $field ] );
            update_post_meta( $post_id, $field, $new );
        } else {
            delete_post_meta( $post_id, $field );
        }
    }
}
add_action( 'save_post', 's_camps_meta_box_save' );





/** Class Configuration *****************************************************************/

/**
 * Class Metabox
 *
 * Extensions (as well as the core plugin) can add items to the main download
 * configuration metabox via the `s_camps_meta_box_fields` action.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return void
 */
function s_camps_render_meta_box() {
    global $post, $s_camps_settings;

    do_action( 's_camps_meta_box_fields', $post->ID );
    wp_nonce_field( basename( __FILE__ ), 's_camps_meta_box_nonce' );
}

/**
 * Guest List Metabox
 *
 * Extensions (as well as the core plugin) can add items to the main download
 * configuration metabox via the `ifg_gatherings_meta_box_fields` action.
 *
 * @since  1.0
 * @author Bryan Monzon
 * @return void
 */
function s_camps_render_donor_list_meta_box() {
    global $post, $s_camps_settings;

    do_action( 's_camps_meta_box_donor_list_fields', $post->ID );
    wp_nonce_field( basename( __FILE__ ), 's_camps_meta_box_nonce' );
}




/**
 * Render the fields
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param  [type] $post [description]
 * @return [type]       [description]
 */
function s_camps_render_fields( $post )
{
    global $post, $s_camps_settings; 

    $s_camps_goal                    = get_post_meta( $post->ID, 's_camps_goal', true);
    $s_camps_amount_raised           = get_post_meta( $post->ID, 's_camps_amount_raised', true);
    $donor_count                     = get_post_meta( $post->ID, 's_camps_donor_count', true);
    $s_camps_default_donation_amount = get_post_meta( $post->ID, 's_camps_default_donation_amount', true );
    $percent_funded                  = !empty( $s_camps_goal ) ? get_percent_funded( $s_camps_amount_raised, $s_camps_goal ) : '';
    $s_camps_donation_type           = get_post_meta( $post->ID, 's_camps_donation_type', true );      
    $s_camps_button_class            = get_post_meta( $post->ID, 's_camps_button_class', true ); 
    $s_camps_button_text             = get_post_meta( $post->ID, 's_camps_button_text', true );                

    $donor_count    = !empty( $donor_count ) ? $donor_count : 0;
    $percent_funded = !empty( $percent_funded ) ? $percent_funded : 0;
    

    ?>  
    
    <div id="campaign_details_wrapper">
        <p>
            <strong>Campaign Goal</strong><br>
            <label for="s_camps_goal">
                $<input type="number" step="0.01" name="s_camps_goal" value="<?php echo $s_camps_goal ?>" /><br>
            </label>
        </p>
        <hr>
        <p>
            <label for="s_camps_amount_raised">
                <strong>Amount Raised</strong><br>
                <?php $s_camps_amount_raised = ($s_camps_amount_raised > 0) ? '$' . $s_camps_amount_raised : '$0.00'; echo $s_camps_amount_raised; ?><br>
            </label>
        </p>
        <hr>
        <p>
            <label for="s_camps_percent_funded">
                <strong>Percet Raised</strong><br>
                <?php echo $percent_funded; ?>%<br>
            </label>
        </p>
        <hr>
        <p>
            <label for="s_camps_donor_count">
                <strong>Donor Count</strong><br>
                <?php echo $donor_count; ?><br>
            </label>
        </p>
        <hr>
        <p>
            <label for="s_camps_default_donation_amount">
                $<input type="number" step="0.01" name="s_camps_default_donation_amount" value="<?php echo $s_camps_default_donation_amount ?>" /><br>
                <em class="hint">Enter a default donation amount.</em>
            </label>
        </p>
        <hr>
        <p>
            <label for="s_camps_donation_type">
                <strong>Donation Type</strong><br>
                <select name="s_camps_donation_type" id="">
                    <option value="one-time" <?php selected( $s_camps_donation_type, 'one-time' ); ?>>One Time</option>
                    <option value="recurring" <?php selected( $s_camps_donation_type, 'recurring' ); ?>>Recurring</option>
                </select>
            </label>
        </p>
        <hr>
        <p>
            <label for="s_camps_button_text">
                <strong>Custom Button Text</strong><br>
                <input type="text" value="<?php echo $s_camps_button_text; ?>" name="s_camps_button_text"><br>
                <em class="hint">Override the button text.</em>
            </label>
        </p>
        <hr>
        <p>
            <label for="s_camps_button_class">
                <strong>Button Class</strong><br>
                <input type="text" value="<?php echo $s_camps_button_class; ?>" name="s_camps_button_class"><br>
                <em class="hint">If needed you can override the button classes.</em>
            </label>
        </p>
    
    </div>
    
    <?php

}
add_action( 's_camps_meta_box_fields', 's_camps_render_fields', 10 );



/**
 * Render the fields
 *
 * @since  1.0
 * @author Bryan Monzon
 * @param  [type] $post [description]
 * @return [type]       [description]
 */
function s_camps_render_donor_list_fields( $post )
{
    global $post;

    $donor_list  = get_post_meta( $post->ID, 's_camps_donor_list', true );
    $donor_count = get_post_meta( $post->ID, 's_camps_donor_count', true);

    $style = ($donor_count >= 1) ? ' donor ' : '';
    ?>
    <style>
    .date-registered{ float:right;}
    .donor{ border-bottom:1px solid #E5E5E5; padding:5px 0; display:block; line-height:200%; }
    </style>
    <div class="admin_donor_list_wrapper">

        <?php if( $donor_list ) : $line_count = 1; ?>
            <?php foreach( $donor_list as $donor ) : ?>
                <?php 
                    $date_created = strtotime( $donor['date_created'] ); 
                    $date         = date( 'm/d - g:ia', $date_created );

                ?>
                <div class="<?php echo $style; ?>"><a href="mailto:<?php echo $donor['email'] ?>" title="Send <?php echo $donor['donor_name']; ?> an email"><?php echo $donor['donor_name']; ?></a> <?php if( isset( $donor['org_name'] ) ) : echo ' - ' . $donor['org_name']; endif; ?><span class="date-registered"><?php echo $date; ?></span> </div>
            <?php endforeach; ?>
        <?php else: ?>
        <p>No donations for this campaign, yet.</p>
        <?php endif; ?>
    </div>
    <?php
}
add_action( 's_camps_meta_box_donor_list_fields', 's_camps_render_donor_list_fields', 10 );


