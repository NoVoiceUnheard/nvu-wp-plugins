<?php
/**
 * Plugin Name: Organization CSV Importer
 * Description: Adds a CSV import feature to the Organizations post type menu.
 * Version: 1.0
 * Author: NoVoiceUnheard
 */

if (!defined('ABSPATH')) {
    exit;
}

// Add "Import CSV" Submenu under "Organizations"
function csv_importer_add_submenu() {
    add_submenu_page(
        'edit.php?post_type=cf7_organizations', // Parent menu (Organizations)
        'Import Organizations',
        'Import CSV',
        'manage_options',
        'import-organizations',
        'csv_importer_upload_page'
    );
}
add_action('admin_menu', 'csv_importer_add_submenu');

// Display the CSV Upload Form
function csv_importer_upload_page() {
    ?>
    <div class="wrap">
        <h1>Import Organizations from CSV</h1>
        <form method="post" enctype="multipart/form-data" action="">
            <input type="file" name="csv_file" accept=".csv" required>
            <input type="hidden" name="csv_upload_nonce" value="<?php echo wp_create_nonce('csv_upload_action'); ?>">
            <?php submit_button('Upload and Import'); ?>
        </form>
    </div>
    <?php

    // Process the file only when the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
        csv_importer_handle_upload();
    }
}


// Process the CSV and Insert Data
function csv_importer_handle_upload() {
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

    // Process each row
    while (($data = fgetcsv($handle)) !== false) {
        $mapped_data = array_combine($headers, $data);

        if (!$mapped_data || !isset($mapped_data['Organization Name'])) {
            continue;
        }

        // Insert into 'organizations' post type
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

    fclose($handle);
    echo '<p style="color: green;">CSV Imported Successfully!</p>';
}
