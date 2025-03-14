<?php
/**
 * Plugin Name: Custom Post CSV Importer
 * Description: Adds a CSV import feature to the Organizations post type menu.
 * Version: 1.0
 * Author: NoVoiceUnheard
 */

if (!defined('ABSPATH')) {
    exit;
}

// Add "Import CSV" Submenu under custom post menus
function csv_importer_add_submenu() {
    $post_types = array('organizations','protest-listing');
    foreach($post_types as $type) {
        add_submenu_page(
            'edit.php?post_type=cf7_' . $type, // Parent menu (Organizations)
            'Import ' . ucwords($type),
            'Import CSV',
            'manage_options',
            'import-' . $type,
            'csv_importer_upload_page'
        );
    }
}
add_action('admin_menu', 'csv_importer_add_submenu');

// Display the CSV Upload Form
function csv_importer_upload_page() {
    ?>
    <div class="wrap">
        <h1>Import from CSV</h1>
        <form method="post" enctype="multipart/form-data" action="">
            <input type="file" name="csv_file" accept=".csv" required>
            <input type="hidden" name="csv_upload_nonce" value="<?php echo wp_create_nonce('csv_upload_action'); ?>">
            <?php submit_button('Upload and Import'); ?>
        </form>
    </div>
    <?php

    // Process the file only when the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
        $type = $_GET['post_type'];
        csv_importer_handle_upload($type);
    }
}


// Process the CSV and Insert Data
function csv_importer_handle_upload($type) {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized access');
    }

    // Verify nonce
    if (!isset($_POST['csv_upload_nonce']) || !wp_verify_nonce($_POST['csv_upload_nonce'], 'csv_upload_action')) {
        wp_die('Nonce verification failed');
    }

    // Check for file upload errors
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        echo '<p style="color: red;">Error uploading file. Error Code: ' . $_FILES['csv_file']['error'] . '</p>';
        return;
    }

    // Debugging: Print file details
    echo '<pre>';
    print_r($_FILES['csv_file']);
    echo '</pre>';

    $file = $_FILES['csv_file']['tmp_name'];
    
    if (!file_exists($file)) {
        echo '<p style="color: red;">Uploaded file not found.</p>';
        return;
    }

    $handle = fopen($file, 'r');
    if (!$handle) {
        echo '<p style="color: red;">Could not open file.</p>';
        return;
    }

    // Read headers
    $headers = fgetcsv($handle);
    if (!$headers) {
        echo '<p style="color: red;">Invalid CSV format.</p>';
        fclose($handle);
        return;
    }
    if ($type == 'cf7_organizations') {
        process_organizations($handle, $headers);
    } else if ($type == 'cf7_protest-listing') {
        process_protest_listing($handle, $headers);
    }

    fclose($handle);
    echo '<p style="color: green;">CSV Imported Successfully!</p>';
}

function process_organizations($handle, $headers) {
    // Process each row
    while (($data = fgetcsv($handle)) !== false) {
        $mapped_data = array_combine($headers, $data);

        if (!$mapped_data || !isset($mapped_data['Organization Name'])) {
            continue;
        }

        // Insert into 'cf7_organizations' post type
        $post_data = array(
            'post_title'   => sanitize_text_field($mapped_data['Organization Name']),
            'post_content' => sanitize_textarea_field($mapped_data['Short description']),
            'post_type'    => 'cf7_organizations',
            'post_status'  => 'publish',
        );

        $post_id = wp_insert_post($post_data);

        if ($post_id) {
            foreach ($mapped_data as $key => $value) {
                if ($key !== 'Organization Name' && $key !== 'Short description') {
                    update_post_meta($post_id, sanitize_key($key), sanitize_text_field($value));
                }
            }
        }
    }
}
function process_protest_listing($handle, $headers) {
    // Process each row
    while (($data = fgetcsv($handle)) !== false) {
        $mapped_data = array_combine($headers, $data);

        if (!$mapped_data || !isset($mapped_data['Event Title'])) {
            continue;
        }

        // Insert into 'cf7_protest-listing' post type
        $post_data = array(
            'post_title'   => sanitize_text_field($mapped_data['Event Title']),
            'post_content' => sanitize_textarea_field($mapped_data['Event Description']),
            'post_type'    => 'cf7_protest-listing',
            'post_status'  => 'publish',
        );

        $post_id = wp_insert_post($post_data);

        if ($post_id) {
            foreach ($mapped_data as $key => $value) {
                if ($key !== 'Event Title' && $key !== 'Event Description') {
                    update_post_meta($post_id, sanitize_key($key), sanitize_text_field($value));
                }
            }
        }
    }
}