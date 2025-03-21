<?php
/**
 * Plugin Name: CF7 to Custom Post Type
 * Description: Saves Contact Form 7 submissions as a custom post type with all form fields and provides a block to display approved submissions.
 * Version: 1.0
 * Author: NoVoiceUnheard
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
// function list_all_registered_post_types() {
//     $post_types = get_post_types(array(), 'names'); // Get all post types by name
//     echo '<pre>';
//     print_r($post_types); // Display all registered post types
//     echo '</pre>';
// }
// add_action('wp_footer', 'list_all_registered_post_types'); // Output to the footer
add_action('init', 'register_cf7_post_types_for_all_forms', 10);
function register_cf7_post_types_for_all_forms() {
    // Get all CF7 forms using WPCF7_ContactForm::find() method
    $contact_forms = WPCF7_ContactForm::find();

    // Check if there are any forms
    if ($contact_forms) {
        // Loop through each form and register a post type if needed
        foreach ($contact_forms as $contact_form) {
            $form_id = $contact_form->id();
            $form_title = $contact_form->title();

            // Check if the 'cf7_save_submissions' option is enabled for this form
            $save_submissions = get_post_meta($form_id, '_cf7_save_submissions', true);
            if ($save_submissions !== 'yes') {
                continue; // Skip if submissions are not being saved for this form
            }

            // Create a unique post type slug based on the form ID
            $post_type_slug = 'cf7_' . sanitize_title($form_title);

            // Check if the post type is already registered
            if (!post_type_exists($post_type_slug)) {
                // Register a post type for each form
                $args = array(
                    'labels' => array(
                        'name' => $form_title . ' Submissions',
                        'singular_name' => $form_title . ' Submission',
                    ),
                    'public' => true,
                    'show_ui' => true,
                    'has_archive' => true,
                    'supports' => array('title', 'editor', 'custom-fields', 'tags'),
                    'show_in_menu' => true,
                    'capability_type' => 'post',
                    'show_in_rest'  => true,
                    'taxonomies'    => array('post_tag'),
                );

                // Register the custom post type
                register_post_type($post_type_slug, $args);
            }
        }
    }
}
// Add a settings panel in CF7 editor
function cf7_add_submission_settings_panel($panels) {
    $panels['submission-settings'] = array(
        'title' => __('Submission Settings', 'cf7'),
        'callback' => 'cf7_submission_settings_panel_callback'
    );
    return $panels;
}
add_filter('wpcf7_editor_panels', 'cf7_add_submission_settings_panel');

// Callback function to display the checkbox in the CF7 admin panel
function cf7_submission_settings_panel_callback($post) {
    $option = get_post_meta($post->id(), '_cf7_save_submissions', true);
    ?>
    <h2><?php _e('Save Submissions', 'cf7'); ?></h2>
    <fieldset>
        <label>
            <input type="checkbox" name="cf7_save_submissions" value="yes" <?php checked($option, 'yes'); ?>>
            <?php _e('Save form submissions as custom posts', 'cf7'); ?>
        </label>
    </fieldset>
    <?php
}

// Save the option when the form is updated
function cf7_save_form_option($post) {
    $save_submissions = isset($_POST['cf7_save_submissions']) ? 'yes' : 'no';
    update_post_meta($post->id(), '_cf7_save_submissions', $save_submissions);
}
add_action('wpcf7_save_contact_form', 'cf7_save_form_option');

// Capture CF7 submissions and save them as custom posts if enabled
function cf7_save_submission_to_post($contact_form) {
    $form_id = $contact_form->id();

    // Get the form title to use as the post type
    $form_title = get_the_title($form_id);
    $post_type = 'cf7_' . sanitize_title($form_title); // Convert form title to a slug-friendly post type
    if (get_post_meta($form_id, '_cf7_save_submissions', true) !== 'yes') {
        return; // Skip if the option is disabled
    }

    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $posted_data = $submission->get_posted_data();
        
        // Set a default title if no specific field is set
        $post_title = isset($posted_data['title']) ? sanitize_text_field($posted_data['title']) : 'Submission';
        $post_content = isset($posted_data['description']) ? sanitize_textarea_field($posted_data['description']) : '';

        // Create a new post
        $post_id = wp_insert_post(array(
            'post_title'    => $post_title,
            'post_content'  => $post_content,
            'post_status'   => 'pending', // Set to 'pending' for moderation
            'post_type'     => $post_type,
        ));

        // Store all form fields as custom fields
        if ($post_id) {
            foreach ($posted_data as $key => $value) {
                if (!is_array($value)) {
                    update_post_meta($post_id, $key, sanitize_text_field($value));
                } else {
                    update_post_meta($post_id, $key, array_map('sanitize_text_field', $value));
                }
            }
        }
    }
}
add_action('wpcf7_mail_sent', 'cf7_save_submission_to_post');
