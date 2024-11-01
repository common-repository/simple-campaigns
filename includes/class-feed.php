<?php
/**
 * Sets up a feed to send info to the campaign
 */


if (class_exists("GFForms")) {
    GFForms::include_feed_addon_framework();

    class GFSimpleCampaignsAddOn extends GFFeedAddOn {

        protected $_version                  = "1.0";
        protected $_min_gravityforms_version = "1.7.9999";
        protected $_slug                     = "simplecampsaddon";
        protected $_path                     = "simple-campaigns/includes/class-feed.php";
        protected $_full_path                = __FILE__;
        protected $_title                    = "Gravity Forms Simple Campaigns Add-On";
        protected $_short_title              = "Simple Campaigns";


        public function feed_settings_fields() {
            return array(
                array(
                    "title"  => "Campaign Feed Settings",
                    "fields" => array(
                        array(
                            'label'   => 'Feed name',
                            'type'    => 'text',
                            'name'    => 'feedName',
                            'tooltip' => 'Name the feed',
                            'class'   => 'small'
                        ),
                        array(
                            "name"      => "mappedFields",
                            "label"     => "Map Fields",
                            "type"      => "field_map",
                            "field_map" => array(   
                                                array(
                                                    "name"     => "amount", 
                                                    "label"    => "Amount",
                                                    "required" => 0
                                                ),
                                                array(
                                                    "name"     => "campaign_id",
                                                    "label"    => "Campaign ID",
                                                    "required" => 0
                                                ),
                                                array(
                                                    "name"     => "fname",
                                                    "label"    => "First Name",
                                                    "required" => 0
                                                ),
                                                array(
                                                    "name"     => "lname",
                                                    "label"    => "Last Name",
                                                    "required" => 0
                                                ),
                                                array(
                                                    "name"     => "email",
                                                    "label"    => "Email",
                                                    "required" => 0
                                                ),
                                                array(
                                                    "name"     => "org_name",
                                                    "label"    => "Company/Org Name",
                                                    "required" => 0
                                                ),
                                                array(
                                                    "name"     => "type",
                                                    "label"    => "Donation Type",
                                                    "required" => 0
                                                ),
                            )
                        ),
                    )
                )
            );
        }

        protected function feed_list_columns() {
            return array(
                'feedName' => __('Name', 'simplecampsaddon'),
            );
        }

        // customize the value of mytextbox before it's rendered to the list
        public function get_column_value_mytextbox($feed){
            return "<b>" . $feed["meta"]["mytextbox"] ."</b>";
        }


        public function process_feed($feed, $entry, $form){

            $amount      = $feed["meta"]["mappedFields_amount"];
            $campaign_id = $feed["meta"]["mappedFields_campaign_id"];
            $fname       = $feed["meta"]["mappedFields_fname"];
            $lname       = $feed["meta"]["mappedFields_lname"];
            $email       = $feed["meta"]["mappedFields_email"];
            $org_name    = $feed["meta"]["mappedFields_org_name"];
            $type        = $feed["meta"]["mappedFields_type"];
            
            $amount      = $entry[$amount];
            $campaign_id = $entry[$campaign_id];
            $fname       = $entry[$fname];
            $lname       = $entry[$lname];
            $email       = $entry[$email];
            $org_name    = $entry[$org_name];
            $type        = $entry[$type];

            if( !empty( $campaign_id ) ) {

                $currently_raised = get_post_meta( $campaign_id, 's_camps_amount_raised', true );
                $donor_count      = get_post_meta( $campaign_id, 's_camps_donor_count', true );

                if( !empty( $currently_raised ) ) {
                    update_post_meta( $campaign_id, 's_camps_amount_raised', $currently_raised + $amount );  
                }else{
                    update_post_meta( $campaign_id, 's_camps_amount_raised', $amount );
                }

                if( !empty( $donor_count ) ) {
                    update_post_meta( $campaign_id, 's_camps_donor_count', $donor_count + 1 );
                }else{
                    update_post_meta( $campaign_id, 's_camps_donor_count', 1 );
                }


                $donor_list = get_post_meta( $campaign_id, 's_camps_donor_list', true );

                $donor_array = array(
                    'donor_name'   => $fname . ' ' . $lname,
                    'org_name'     => $org_name,
                    'email'        => $email,
                    'date_created' => $entry['date_created'],
                    'type'         => $type
                    );
                    
                if( $donor_list && is_array( $donor_list ) ) {
                    $donor_list[] = $donor_array;
                    
                }else{
                    $donor_list = array();
                    $donor_list[] = $donor_array;
                    
                }

                $donor_list = update_post_meta( $campaign_id, 's_camps_donor_list', $donor_list );

            }
        }
    }

    new GFSimpleCampaignsAddOn();
}