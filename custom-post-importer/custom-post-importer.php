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
    // State name to abbreviation map (keys will always be lowercase)
    $state_map = array_change_key_case(array(
        'Alabama' => 'AL', 'Alaska' => 'AK', 'Arizona' => 'AZ', 'Arkansas' => 'AR',
        'California' => 'CA', 'Colorado' => 'CO', 'Connecticut' => 'CT', 'Delaware' => 'DE',
        'Florida' => 'FL', 'Georgia' => 'GA', 'Hawaii' => 'HI', 'Idaho' => 'ID',
        'Illinois' => 'IL', 'Indiana' => 'IN', 'Iowa' => 'IA', 'Kansas' => 'KS',
        'Kentucky' => 'KY', 'Louisiana' => 'LA', 'Maine' => 'ME', 'Maryland' => 'MD',
        'Massachusetts' => 'MA', 'Michigan' => 'MI', 'Minnesota' => 'MN', 'Mississippi' => 'MS',
        'Missouri' => 'MO', 'Montana' => 'MT', 'Nebraska' => 'NE', 'Nevada' => 'NV',
        'New Hampshire' => 'NH', 'New Jersey' => 'NJ', 'New Mexico' => 'NM', 'New York' => 'NY',
        'North Carolina' => 'NC', 'North Dakota' => 'ND', 'Ohio' => 'OH', 'Oklahoma' => 'OK',
        'Oregon' => 'OR', 'Pennsylvania' => 'PA', 'Rhode Island' => 'RI', 'South Carolina' => 'SC',
        'South Dakota' => 'SD', 'Tennessee' => 'TN', 'Texas' => 'TX', 'Utah' => 'UT',
        'Vermont' => 'VT', 'Virginia' => 'VA', 'Washington' => 'WA', 'West Virginia' => 'WV',
        'Wisconsin' => 'WI', 'Wyoming' => 'WY'
    ), CASE_LOWER); // Convert keys to lowercase
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
        // **Check for the 'state' field and assign it as a tag**
        if (!empty($mapped_data['State'])) {
            $state_name = trim($mapped_data['State']);
            $state_key = strtolower($state_name); // Convert input to lowercase for matching
            $state_abbr = isset($state_map[$state_key]) ? $state_map[$state_key] : $state_name; // Normalize state
            // Ensure the term exists, create if not
            $term = term_exists($state_abbr, 'post_tag'); 
            if (!$term) {
                $term = wp_insert_term($state_abbr, 'post_tag'); // Create tag if not exists
            }
            wp_set_post_terms($post_id, $state_abbr, 'post_tag', true); 
            // 'post_tag' is the default WP tag taxonomy. If using a custom taxonomy, change it accordingly.
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

        // **Check for Multi-line Address field and extract city/state**
        if (!empty($mapped_data['Multi-line address'])) {
            $address = sanitize_text_field($mapped_data['Multi-line address']);
            $location_tag = extract_state_from_address($address);

            if (!empty($location_tag)) {
                // Ensure the term exists, create if not
                $term = term_exists($location_tag, 'post_tag'); 
                if (!$term) {
                    $term = wp_insert_term($location_tag, 'post_tag'); 
                }
                wp_set_post_terms($post_id, $location_tag, 'post_tag', true);
            }
        }
        
    }
}
// **Extract city or state from the address**
function extract_state_from_address($address) {
    // List of all state abbreviations
    $state_abbreviations = array(
        "AL", "AK", "AZ", "AR", "CA", "CO", "CT", "DE", "FL", "GA",
        "HI", "ID", "IL", "IN", "IA", "KS", "KY", "LA", "ME", "MD",
        "MA", "MI", "MN", "MS", "MO", "MT", "NE", "NV", "NH", "NJ",
        "NM", "NY", "NC", "ND", "OH", "OK", "OR", "PA", "RI", "SC",
        "SD", "TN", "TX", "UT", "VT", "VA", "WA", "WV", "WI", "WY"
    );

    // Match the first valid state abbreviation (two uppercase letters, after a comma or space)
    if (preg_match('/\b(' . implode('|', $state_abbreviations) . ')\b/', $address, $matches)) {
        return $matches[1]; // Return the first matched state abbreviation
    }

    return ''; // Return empty if no state abbreviation is found
}