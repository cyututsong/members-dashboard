<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ✅ Shortcode function for gallery
function display_images_from_folder($atts) {

    static $modal_printed = false;
    $user_id = get_current_user_id();

    $atts = shortcode_atts(
        array(
            'folder' => 'moment_capture/moments_' . $user_id,
            'width'  => '80%',
            'margin' => '20%',
            'images_per_page' => 18,
            'page'   => 1,
        ),
        $atts,
        'moments_gallery'
    );

    $upload_dir = wp_get_upload_dir();
    $folder     = trailingslashit($upload_dir['basedir']) . $atts['folder'] . '/';
    $url_folder = trailingslashit($upload_dir['baseurl']) . $atts['folder'] . '/';

    if ( ! is_dir($folder) ) {
        return '<p style="text-align:center;">No Image Found</p>';
    }

    $images       = glob($folder . "*.{jpg,jpeg,png,gif,bmp,tiff,webp}", GLOB_BRACE) ?: [];

    usort($images, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    $total_images = count($images);

    $paged          = max(1, (int) $atts['page']);
    $start          = ($paged - 1) * $atts['images_per_page'];
    $images_on_page = array_slice($images, $start, $atts['images_per_page']);

    // ✅ Wrapper that won’t get replaced, only inner content updates
    $output = '<div class="gallery-wrapper">';
    $output .= '<div class="moments-gallery">';

    foreach ($images_on_page as $image) {
        $image_url = str_replace($folder, $url_folder, $image);
        $output .= '<img src="' . esc_url($image_url) . '" 
            alt="Image" 
            class="moments-gallery-item">';
    }

    $output .= '</div>'; // close .moments-gallery

    // ✅ Pagination + Download button inside wrapper
    $total_pages = ceil($total_images / $atts['images_per_page']);
    $output .= '<div class="pagination" style="text-align:center; margin-top:20px;">';
                    $output .= '<div class="paginationNav">';
                        if ($total_pages > 1) {
                            if ($paged > 1) {
                                $output .= '<a href="#" class="prev-page" 
                                    data-page="' . ($paged - 1) . '" 
                                    data-folder="' . esc_attr($atts['folder']) . '" 
                                    data-images-per-page="' . esc_attr($atts['images_per_page']) . '">Previous</a> ';
                            }

                            if ($paged < $total_pages) {
                                $output .= '<a href="#" class="next-page" 
                                    data-page="' . ($paged + 1) . '" 
                                    data-folder="' . esc_attr($atts['folder']) . '" 
                                    data-images-per-page="' . esc_attr($atts['images_per_page']) . '">Next</a>';
                            }
                        }
                    $output .= '</div>';

                    // ✅ Download button
                    $output .= ' <a href="' . esc_url( add_query_arg([
                        'action' => 'download_moments_folder',
                        'folder' => $atts['folder'],
                        'nonce'  => wp_create_nonce('download-folder-nonce'),
                    ], admin_url('admin-ajax.php')) ) . '" 
                    class="download-folder-btn">Download All</a>';

                $output .= '</div>'; // close .pagination
    $output .= '</div>'; // close .gallery-wrapper
        
    return $output;
}
add_shortcode('moments_gallery', 'display_images_from_folder');


// ✅ AJAX handler (returns full gallery HTML again)
function load_images_page_callback() {
    check_ajax_referer('ajax-pagination-nonce', 'nonce');

    $atts = array(
        'folder' => sanitize_text_field($_POST['folder']),
        'page'   => intval($_POST['page']),
        'images_per_page' => intval($_POST['images_per_page']),
    );

    $html = display_images_from_folder($atts);
    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_load_images_page', 'load_images_page_callback');
add_action('wp_ajax_nopriv_load_images_page', 'load_images_page_callback');


// ✅ Handle folder download (zip + force download)
function download_moments_folder() {
    if ( ! isset($_GET['nonce']) || ! wp_verify_nonce($_GET['nonce'], 'download-folder-nonce') ) {
        wp_die('Invalid request.');
    }

    if (empty($_GET['folder'])) {
        wp_die('No folder specified.');
    }

    $upload_dir = wp_get_upload_dir();
    $folder = trailingslashit($upload_dir['basedir']) . sanitize_text_field($_GET['folder']) . '/';

    if ( ! is_dir($folder) ) {
        wp_die('Folder not found.');
    }

    // Prepare zip filename
    $zip_filename = 'capture-' . basename($folder) . '.zip';
    $zip_filepath = tempnam(sys_get_temp_dir(), 'zip');

    $zip = new ZipArchive();
    if ( $zip->open($zip_filepath, ZipArchive::OVERWRITE) !== TRUE ) {
        wp_die('Could not create zip file.');
    }

    // Add files
    $files = glob($folder . "*.{jpg,jpeg,png,gif,bmp,tiff,webp}", GLOB_BRACE);
    foreach ($files as $file) {
        $zip->addFile($file, basename($file));
    }
    $zip->close();

    // Send headers
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
    header('Content-Length: ' . filesize($zip_filepath));
    readfile($zip_filepath);

    unlink($zip_filepath); // cleanup
    exit;
}
add_action('wp_ajax_download_moments_folder', 'download_moments_folder');
add_action('wp_ajax_nopriv_download_moments_folder', 'download_moments_folder');



function add_gallery_modal_once() {
    if ( is_page(1367) ) {
        ?>
        <div id="imageModal" class="image-modal">
            <span class="close">&times;</span>
            <img class="modal-content" id="modalImg">
            <a class="prev">&#10094;</a>
            <a class="next">&#10095;</a>
        </div>
        <?php
    }
}
add_action('wp_footer', 'add_gallery_modal_once');