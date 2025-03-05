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
        'editor_script'   => 'cf7-submission-block',
        'render_callback' => 'cf7_render_submission_block',
        'attributes'      => array(
            'fields' => array(
                'type'    => 'array',
                'default' => array(),
                'items'   => array('type' => 'string')
            )
        ),
    ));
}
add_action('init', 'cf7_register_submission_block');

// Render the block output
function cf7_render_submission_block($attributes) {
    $selected_fields = isset($attributes['fields']) ? $attributes['fields'] : array();

    $args = array(
        'post_type'      => 'cf7_submission',
        'posts_per_page' => 5,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC'
    );

    $submissions = new WP_Query($args);

    if (!$submissions->have_posts()) {
        return '<div class="cf7-submission-block">No submissions found.</div>';
    }

    $output = '<div class="cf7-submission-block">';
    $output .= '<h3>Recent Submissions</h3>';

    while ($submissions->have_posts()) {
        $submissions->the_post();
        $output .= '<div class="cf7-submission">';
        $output .= '<h4>' . esc_html(get_the_title()) . '</h4>';
        $output .= '<ul>';

        $meta_values = get_post_meta(get_the_ID());

        foreach ($meta_values as $key => $value) {
            if (!empty($selected_fields) && !in_array($key, $selected_fields)) {
                continue; // Skip fields not selected
            }

            if (!empty($value)) {
                $output .= '<li><strong>' . esc_html($key) . ':</strong> ' . esc_html(is_array($value) ? implode(', ', $value) : $value) . '</li>';
            }
        }

        $output .= '</ul>';
        $output .= '<small>Submitted on ' . get_the_date() . '</small>';
        $output .= '</div>';
    }

    wp_reset_postdata();

    $output .= '</div>';

    return $output;
}

function cf7_get_submission_fields() {
    $args = array(
        'post_type'      => 'cf7_submission',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    );

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return rest_ensure_response(array());
    }

    $query->the_post();
    $fields = array_keys(get_post_meta(get_the_ID()));

    wp_reset_postdata();
    return rest_ensure_response($fields);
}

function cf7_register_api_routes() {
    register_rest_route('cf7/v1', '/fields', array(
        'methods'  => 'GET',
        'callback' => 'cf7_get_submission_fields',
        'permission_callback' => '__return_true',
    ));
}

add_action('rest_api_init', 'cf7_register_api_routes');