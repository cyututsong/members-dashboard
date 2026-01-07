<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

/**
 * Generate QR Code from user-assigned page ID (transparent PNG)
 */
function rr_user_page_qrcode( $atts ) {
    $atts = shortcode_atts( array(
        'size'    => 250,  // Default QR code size
        'purpose' => '',   // Invitation or Moments Captured
    ), $atts );

    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return '<p>Please log in to generate your QR code.</p>';
    }

    $user_info  = get_userdata( $user_id );
    $user_email = $user_info ? $user_info->user_email : '';
    $groom_name = get_user_meta( $user_id, 'groom_name', true );
    $bride_name = get_user_meta( $user_id, 'bride_name', true );
    $wedding_date = get_user_meta( $user_id, 'wedding_date', true );
    $wedding_date = date_i18n( 'm . d . Y', strtotime( $wedding_date ) ); // formatting date to d . m . Y
    $rsvp_deadline = get_user_meta( $user_id, 'rsvp_deadline', true );
    $rsvp_deadline = date_i18n( 'F d, Y', strtotime( $rsvp_deadline ) ); // formatting date to Month d, Y   


    // ✅ Get user-specific page ID
    $page_id = get_user_meta( $user_id, 'invitation_page_id', true );
    if ( ! $page_id ) {
        return '<p>No page assigned to your profile.</p>';
    }

    $purpose = sanitize_text_field( $atts['purpose'] );

    // ✅ Define the correct page URL based on purpose
    if ( $purpose === 'Invitation' ) {
        $page_url = get_permalink( $page_id );
    } elseif ( $purpose === 'Moments Captured' ) {
        $page_url = add_query_arg(
            array(
                'event' => $user_id,
                'email' => $user_email,
            ),
            get_permalink( 1582 )
        );
    } else {
        return '<p>Invalid purpose provided for QR code.</p>';
    }

    // ✅ Generate QR code with fully transparent background
    $qr = QrCode::create( $page_url )
        ->setEncoding( new Encoding( 'UTF-8' ) )
        ->setErrorCorrectionLevel( new ErrorCorrectionLevelHigh() )
        ->setSize( (int) $atts['size'] )
        ->setMargin( 0 )
        ->setForegroundColor( new Color(0, 0, 0) )        // Black QR dots
        ->setBackgroundColor( new Color(255, 255, 255, 0) ); // Transparent PNG (alpha = 0)

    $writer = new PngWriter();
    $result = $writer->write( $qr );

    // ✅ Output as PNG (with transparency)
    $dataUri = $result->getDataUri();

    // ✅ Get featured image (or fallback)
    $bg_url = get_the_post_thumbnail_url( $page_id, 'full' );
    if ( ! $bg_url ) {
        $bg_url = plugin_dir_url( __FILE__ ) . 'assets/default-bg.png'; // Use PNG background for better blending
    }

    // ✅ Load the correct template
    ob_start();

    if ( $purpose === 'Invitation' ) {
        include plugin_dir_path( __FILE__ ) . 'templates/qr-invitation-4.php';
    } elseif ( $purpose === 'Moments Captured' ) {
        include plugin_dir_path( __FILE__ ) . 'templates/qr-momentscaptured.php';
    } else {
        echo '<p>Unknown QR code purpose: ' . esc_html( $purpose ) . '</p>';
    }

    return ob_get_clean();
}
add_shortcode( 'user_qrcode', 'rr_user_page_qrcode' );
