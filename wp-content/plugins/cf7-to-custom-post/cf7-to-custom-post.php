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

// Register the 'Submissions' custom post type
function cf7_create_custom_post_type() {
    register_post_type('submissions',
        array(
            'labels' => array(
                'name' => __('Submissions'),
                'singular_name' => __('Submission')
            ),
            'public' => false,
            'show_ui' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
        )
    );
}
add_action('init', 'cf7_create_custom_post_type');

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
    if (get_post_meta($form_id, '_cf7_save_submissions', true) !== 'yes') {
        return; // Skip if the option is disabled
    }

    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $posted_data = $submission->get_posted_data();
        
        // Set a default title if no specific field is set
        $post_title = isset($posted_data['your-name']) ? sanitize_text_field($posted_data['your-name']) : 'Submission';
        $post_content = isset($posted_data['your-message']) ? sanitize_textarea_field($posted_data['your-message']) : '';

        // Create a new post
        $post_id = wp_insert_post(array(
            'post_title'    => $post_title,
            'post_content'  => $post_content,
            'post_status'   => 'pending', // Set to 'pending' for moderation
            'post_type'     => 'submissions',
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

// Register a Gutenberg block to display approved submissions
function cf7_register_submission_block() {
    wp_register_script(
        'cf7-submission-block',
        plugins_url('block.js', __FILE__),
        array('wp-blocks', 'wp-editor', 'wp-components', 'wp-element')
    );

    register_block_type('cf7/submission-block', array(
        'editor_script' => 'cf7-submission-block',
        'render_callback' => 'cf7_render_submission_block'
    ));
}
add_action('init', 'cf7_register_submission_block');

// Render the block output
function cf7_render_submission_block() {
    $args = array(
        'post_type' => 'submissions',
        'post_status' => 'publish', // Only approved submissions
        'numberposts' => 10
    );
    
    $submissions = get_posts($args);
    $output = '<div class="cf7-submission-list">';
    
    if ($submissions) {
        foreach ($submissions as $submission) {
            $output .= '<div class="cf7-submission-item">';
            $output .= '<h3>' . esc_html($submission->post_title) . '</h3>';
            $output .= '<p>' . esc_html($submission->post_content) . '</p>';
            $output .= '</div>';
        }
    } else {
        $output .= '<p>No approved submissions found.</p>';
    }
    
    $output .= '</div>';
    return $output;
}
