<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'GFAPI' ) ) {
    add_shortcode('gf_entries', function() {
        return '<p><strong>Gravity Forms must be installed and active to use this shortcode.</strong></p>';
    });
    return;
}

/**
 * Export entries to Excel
 */

add_action('init', 'gf_export_entries_to_csv');
function gf_export_entries_to_csv() {
    if (isset($_GET['gf_export']) && $_GET['gf_export'] == '1' && !empty($_GET['form_id'])) {
        if (!is_user_logged_in()) {
            wp_die('You must be logged in to export entries.');
        }

        if (!class_exists('GFAPI')) {
            wp_die('Gravity Forms not active.');
        }

        $form_id = intval($_GET['form_id']);

        // Get entries
        $search_criteria = array();
        $sorting = null;
        $paging = array('offset' => 0, 'page_size' => 2000); // adjust limit
        $entries = GFAPI::get_entries($form_id, $search_criteria, $sorting, $paging);

        if (empty($entries)) {
            wp_die('No entries found.');
        }

        // Get form fields
        $form = GFAPI::get_form($form_id);
        $fields = $form['fields'];

        // Send headers for CSV
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=Submitted-RSVP.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Open output stream
        $output = fopen('php://output', 'w');

        // Output header row
        $header = [];
        foreach ($fields as $field) {
            $header[] = $field->label;
        }
        fputcsv($output, $header);

        // Output data rows
        foreach ($entries as $entry) {
            $row = [];
            foreach ($fields as $field) {
                $value = rgar($entry, $field->id);
                $row[] = $value ? $value : '-';
            }
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}

/**
 * Shortcode to display entries container
 */
function gf_entries_shortcode() {
    $user_id = get_current_user_id();
    $form_id = intval(get_user_meta($user_id, 'rsvp_form_id', true));

    if (!$form_id) {
        return '<p><strong>No RSVP Record found.</strong></p>';
    }

    ob_start();
    ?>
    <div class="gf-entries-container" data-form-id="<?php echo esc_attr($form_id); ?>">
        <!-- Search Bar -->
        <div class="gf-search-box">
            <input type="text" class="gf-search-input" placeholder="Search by name, email, or phone...">
            <button class="gf-search-btn">Search</button>
        </div>

        <!-- Loader -->
        <div class="gf-loading" style="display:none;">
            <div class="spinner"></div>
        </div>

        <!-- Results will load here -->
        <div class="gf-entries-table-wrapper"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gf_entries', 'gf_entries_shortcode');

/**
 * AJAX: Fetch Entries
 */
add_action('wp_ajax_gf_get_entries', 'gf_get_entries_ajax');
add_action('wp_ajax_nopriv_gf_get_entries', 'gf_get_entries_ajax');

function gf_get_entries_ajax() {
    if ( ! isset($_POST['form_id']) ) {
        wp_send_json_error(['message' => 'Invalid form ID']);
    }

    $form_id = intval($_POST['form_id']);
    $page    = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $search  = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    $per_page = 12;
    $offset   = ($page - 1) * $per_page;

    // --- Define field IDs ---
    $name_field_id     = 1;
    $email_field_id    = 8;
    $phone_field_id    = 10;
    $presence_field_id = 4;
    $comments_field_id = 6;
    $reason_field_id   = 9;

    // Search criteria
    $search_criteria = [];
    if ( $search ) {
        $search_criteria['field_filters'] = [
            'mode' => 'any',
            [
                'key' => $name_field_id,
                'value' => $search,
                'operator' => 'contains'
            ],
            [
                'key' => $email_field_id,
                'value' => $search,
                'operator' => 'contains'
            ],
            [
                'key' => $phone_field_id,
                'value' => $search,
                'operator' => 'contains'
            ]
        ];
    }

    $paging = ['offset' => $offset, 'page_size' => $per_page];
    $sorting = ['key' => 'date_created', 'direction' => 'DESC'];

    $total_count = 0;
    $entries = GFAPI::get_entries($form_id, $search_criteria, $sorting, $paging, $total_count);

    ob_start();
    ?>
    <table class="gf-entries-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Presence</th>
                <th>Comment / Reason</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($entries)): ?>
                <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td data-label="Name"><?php echo esc_html(rgar($entry, $name_field_id)); ?></td>
                        <td data-label="Email"><?php echo esc_html(rgar($entry, $email_field_id)); ?></td>
                        <td data-label="Phone"><?php echo esc_html(rgar($entry, $phone_field_id)); ?></td>
                        <td data-label="Presence"><?php echo esc_html(rgar($entry, $presence_field_id)); ?></td>
                        <td data-label="Comment / Reason">
                            <?php
                            $presence = rgar($entry, $presence_field_id);
                            if ($presence === "Yes" || $presence === "Yes, I'll be there") {
                                echo esc_html(rgar($entry, $comments_field_id));
                            } elseif ($presence === "No" || $presence === "Sorry, can't make it") {
                                echo esc_html(rgar($entry, $reason_field_id));
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5"><em>No entries found.</em></td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php
    $total_pages = ceil($total_count / $per_page);
    if ($total_pages > 1): ?>
    <div class="gf-entriesNav">
        <div class="gf-pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="#" class="gf-page-link <?php echo $i === $page ? 'active' : ''; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
        <div class="gf-download">
                <a href="<?php echo esc_url( add_query_arg( array(
                    'gf_export' => '1',
                    'form_id'   => $form_id
                ), site_url() ) ); ?>" class="gf-export-btn">Download</a>
        </div>
    </div>
    <?php endif;

    $html = ob_get_clean();
    wp_send_json_success(['html' => $html]);
}



// Shortcode: [gf_yes_count]
function gf_yes_entries_count_shortcode() {
    $user_id = get_current_user_id();
    $form_id = intval(get_user_meta($user_id, 'rsvp_form_id', true));

    if (!$form_id) {
        return '0';
    }

    if (!class_exists('GFAPI')) {
        return 'Gravity Forms is not active.';
    }

    $field_id = 4; // Field to check for "Yes"

    // Search criteria: only entries where field 4 = "Yes" or "Yes, I'll be there"
    $search_criteria = [
        'field_filters' => [
            'mode' => 'any',
            [
                'key'      => $field_id,
                'value'    => "Yes",
                'operator' => 'is'
            ],
            [
                'key'      => $field_id,
                'value'    => "Yes, I'll be there",
                'operator' => 'is'
            ]
        ]
    ];

    // Only need count, not full entries
    $total_count = GFAPI::count_entries($form_id, $search_criteria);

    return intval($total_count);
}
add_shortcode('gf_yes_count', 'gf_yes_entries_count_shortcode');



// ✅ Enqueue Gravity Form Entries JS
function md_gf_jscript() {
    // GF Entries script
    global $post;

    if ( is_page(1367) ) {

        wp_enqueue_script(
            'md-gf-entries',
            plugin_dir_url( __FILE__ ) . 'assets/js/gf-entries.js',
            [ 'jquery' ],
            '1.2.1',
            true
        );

        wp_localize_script( 'md-gf-entries', 'gfEntriesAjax', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ] );

    }

}

add_action( 'wp_enqueue_scripts', 'md_gf_jscript' );
