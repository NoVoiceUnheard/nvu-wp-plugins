<?php
/**
 * Plugin Name: Custom Post Type Block
 * Description: Displays custom post types in gutenberg editor.
 * Version: 1.0
 * Author: NoVoiceUnheard
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
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

function cf7_register_submission_block() {
    wp_register_script(
        'cf7-submission-block',
        plugins_url('block.js', __FILE__),
        array('wp-blocks', 'wp-editor', 'wp-components', 'wp-element', 'wp-api-fetch')
    );

    register_block_type('cf7/submission-block', array(
        'editor_script'   => 'cf7-submission-block',
        'render_callback' => 'cf7_render_submission_block',
        'attributes'      => array(
            'postType' => array(
                'type'    => 'string',
                'default' => ''
            )
        ),
    ));
}
add_action('init', 'cf7_register_submission_block');
// Render the block output
function cf7_render_submission_block($attributes) {
    if (empty($attributes['postType'])) {
        return '<p>Please select a form to display its submissions.</p>';
    }

    $query = new WP_Query(array(
        'post_type'      => $attributes['postType'],
        'post_status'    => 'publish',
        'posts_per_page' => 5
    ));

    if (!$query->have_posts()) {
        return '<p>No submissions found.</p>';
    }

    $output = '<ul class="cf7-submission-list">';
    while ($query->have_posts()) {
        $query->the_post();
        $custom_fields = get_post_meta(get_the_ID());
        if (!empty($custom_fields)) {
            $output .= '<li><h3>' . get_the_title() . '</h3><p>' . $custom_fields['location'][0] .'</p><p>' . get_the_excerpt() . '</p></li>';
        }
    }
    wp_reset_postdata();

    return $output . '</ul>';
}