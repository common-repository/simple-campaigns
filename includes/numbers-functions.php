<?php
/**
 * Campaign Totals Numbers
 * @package S_CAMPS
 */


/**
 * Returns the total raised from all campaigns.
 *
 * @author Bryan Monzon
 * @since  1.0.7
 * @return [string]
 */
function s_camps_get_total_raised()
{
    do_action( 's_camps_pre_get_total_raised' );
    
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'campaigns'
        );
    $campaigns = get_posts( $args );

    if( $campaigns ) {
        
        $total_raised_array = array();
        
        foreach( $campaigns as $campaign ) {
            
            $campaign_raised = get_post_meta( $campaign->ID, 's_camps_amount_raised', true );

            if( !empty( $campaign_raised ) ) {
                $total_raised_array[] = $campaign_raised;
            }

        }

        $total_raised = number_format( array_sum( $total_raised_array ), 2 );

        return apply_filters( 's_camps_total_raised', $total_raised, $total_raised_array );
    }

    return false;
    
}

/**
 * Simple query that returns total number of campaigns
 *
 * @since  1.0.7
 * @author Bryan Monzon
 * @return [int]
 */
function s_camps_get_total_number_campaigns()
{
    $query_args = array(
        'post_type'      => 'campaigns',
        'posts_per_page' => -1,
        'post_status'    => 'publish'
    );

    $campaigns = new WP_Query( $query_args );

    return (int) $campaigns->post_count;
}
