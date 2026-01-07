<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

//        [member_field key="rsvp_form_id"]
function md_member_field_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'key'     => '',
        'user_id' => get_current_user_id(), // default: current logged-in user
    ), $atts, 'member_field' );

    if ( empty( $atts['key'] ) ) {
        return '';
    }

    $value = get_user_meta( intval( $atts['user_id'] ), $atts['key'], true );

    // If it's a profile picture
    if ( $atts['key'] === 'profile_picture' ) {
        if ( ! $value ) {
            // Use default image if none is set
            $value = 'https://localhost/bestwishes/wp-content/uploads/2025/08/dummy-profile.png';
        }
        return '<img class="mbProfilepic"src="' . esc_url( $value ) . '" alt="Profile Picture" style="max-width:150px; border-radius:50%;">';
    }

    return esc_html( $value );
}
add_shortcode( 'member_field', 'md_member_field_shortcode' );


// ===== Shortcode to Display Total Entries of User's Assigned Form =====
// Usage: [user_form_entries]
function md_user_form_entries_shortcode( $atts ) {
    $user_id = get_current_user_id();
    if ( ! $user_id ) return 'Please log in to see your form entries.';

    // Get the form ID assigned to this user
    $form_id = get_user_meta( $user_id, 'rsvp_form_id', true );

    if ( ! $form_id ) return '0';

    // Make sure Gravity Forms is active
    if ( ! class_exists( 'GFAPI' ) ) return 'Gravity Forms plugin is not active.';

    // Get total entries for the form
    $search_criteria = array(); // no filters
    $total_entries = GFAPI::count_entries( intval( $form_id ), $search_criteria );

    return intval( $total_entries );
}
add_shortcode( 'user_form_entries', 'md_user_form_entries_shortcode' );


function md_user_page_views_shortcode() {
    $user_id = get_current_user_id();
    if ( ! $user_id ) return '';

    // Get the user's assigned Invitation Page ID
    $page_id = get_user_meta( $user_id, 'invitation_page_id', true );
    if ( ! $page_id ) return '0';

    // Get total views from post meta
    $views = get_post_meta( intval($page_id), 'rr_page_views', true );

    return intval($views);
}
add_shortcode( 'user_page_views', 'md_user_page_views_shortcode' );

