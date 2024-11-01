=== Plugin Name ===
Contributors: bryanmonzon
Tags: gravityform, campaigns
Requires at least: 3.0.1
Tested up to: 4.0
Version: 1.0.8
Stable tag: 1.0.8

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use Gravity Forms to accept donations and track them to campaigns.

== Description ==

Simple Campaigns is a Gravity Forms addon on that allows you to:

1. Create campaigns and set a campaign goal
1. Place a donate button anywhere and specify (if necassary) a campaign ID.
1. Set up a feed in the form settings and you're off an running.

Requires Gravity Forms and a payment gateway add-on.

You will need create an `archive-campaigns.php` and `single-campaigns.php` template to customize.

Checkout [A Night to Remember Prom](https://anighttorememberprom.com). An event for kids with special needs. 

= Sample Form =
[Download](http://wpsetup.co/go/simple-campaigns-form) a sample donation form to use on your Donation page. (It's a zipped XML export from Gravity Forms). Download it, log into your site and navigate to Forms > Import/Export. Select 'Import' and choose the recently downloaded file. 




== Installation ==

1. Upload this plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a Gravity Form with the minimum name field and a hidden field
1. Allow the hidden field to be populated dynamically and add `cid` as that value.s
1. Create a donate page and select it from the Campaign > Settings page.
1. Also select the form you intend to use for your donate page.

== Frequently Asked Questions ==

= Does this plugin require Gravity Forms? =

Yes. However, I'd like to integrate with other form plugins like Ninja Forms in the future.

= Do I need a payment gateway addon?  =

Yes. You need a developer license to use the addons by Gravity Forms. 

= Can users create their own campaigns? =

Technically, yes. You need user registration and you can configure it but it's not really set up for that yet.

= Can users donate to more than onen campaign? =

No. This is not a shopping cart. It's just a *simple* way to have campaigns on your site.

== Screenshots ==

No screenshots yet.

== Changelog ==

= 1.0.8: November 19, 2014 =
FIX: Missing file

= 1.0.7: November 19, 2014 =
FIX: Fixed an issue where campaign meta might get deleted on post save
NEW: Function `s_camps_get_total_raised();` to get total of all campaigns
NEW: Shortcode to display [total_raised]
NEW: Function `s_camps_get_total_number_campaigns();` that returns total number of campaigns
NEW" Shortcode to display the `[total_campaigns]` (the number of active/published campaigns )

= 1.0.6: September 28, 2014 =
NEW: Ability to pass campaign title to a hidden field.

= 1.0.5: September 15, 2014 =
FIX: Version number issues
FIX: Clarified some code comments


= 1.0.4: September 15, 2014 =
FIX: Output '$0' if no money has been raised

= 1.0.2: September 14, 2014 =
NEW: Some nice functions to get campaign data

= 1.0.1: September 10, 2014 =
NEW: a filter for changing the HTML content on the form

= 1.0: September 9, 2014 =
Initial launch


