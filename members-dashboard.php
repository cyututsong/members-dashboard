<?php
/*
Plugin Name: Members Dashboard
Description: An Ultimate RSVP plugin for all RSVP websites.
Version: 1.5
Author: DaddyDevs
Author URI: https://yourwebsite.com
Text Domain: members-dashboard
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

/**
 * Includes
 */
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php'; 
require_once plugin_dir_path( __FILE__ ) . 'mbd-qrgenerator.php'; 
require_once plugin_dir_path( __FILE__ ) . 'mbd-usersmetafields.php'; 
require_once plugin_dir_path( __FILE__ ) . 'mbd-shortcodes.php'; 
require_once plugin_dir_path( __FILE__ ) . 'mbd-gfentriesdisplay.php'; 
require_once plugin_dir_path( __FILE__ ) . 'mbd-momentcapture.php';
require_once plugin_dir_path( __FILE__ ) . 'mbd-displaymoments.php'; 

/**
 * Login redirect for subscribers
 */
if ( ! function_exists( 'md_custom_login_redirect' ) ) {
    function md_custom_login_redirect( $redirect_to, $request, $user ) {
        if ( isset( $user->roles ) && in_array( 'subscriber', (array) $user->roles, true ) ) {
            return home_url( '/members' );
        }
        return $redirect_to;
    }
}
add_filter( 'login_redirect', 'md_custom_login_redirect', 10, 3 );

/**
 * Restrict /members page to logged-in users
 */
if ( ! function_exists( 'md_restrict_members_page' ) ) {
    function md_restrict_members_page() {
        if ( is_page( 'members' ) && ! is_user_logged_in() ) {
            wp_redirect( wp_login_url() );
            exit;
        }
    }
}
add_action( 'template_redirect', 'md_restrict_members_page' );


function md_track_page_views() {
    if ( is_singular() && ! is_admin() ) {
        global $post;
        $post_id = $post->ID;
        $views = get_post_meta( $post_id, 'rr_page_views', true );

        if ( ! $views ) {
            $views = 0;
        }

        $views++;
        update_post_meta( $post_id, 'rr_page_views', $views );
    }
}
add_action( 'wp_head', 'md_track_page_views' );

// ✅ Enqueue all frontend assets in one place
function md_frontend_assets() {
    // Members dashboard CSS
    wp_enqueue_style(
        'members-dashboard-css',
        plugin_dir_url( __FILE__ ) . 'assets/css/members-dashboard.css',
        [],
        '1.0'
    );

    // html2canvas (CDN)
    wp_enqueue_script(
        'html2canvas',
        'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js',
        [],
        '1.4.1',
        true
    );

    // Invite screenshot script
    wp_enqueue_script(
        'md-invite-screenshot',
        plugin_dir_url( __FILE__ ) . 'assets/js/invite-screenshot.js',
        [ 'jquery', 'html2canvas' ],
        '1.0',
        true
    );

}
add_action( 'wp_enqueue_scripts', 'md_frontend_assets' );

