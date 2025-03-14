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
    $search = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $meta_query = array();
    if ($attributes['postType'] == 'cf7_protest-listing') {
        $metakey = 'dateandtime';
        if (!empty($search)) {
            $meta_query[] = array(
                'key'     => 'multi-lineaddress',
                'value'   => $search,
                'compare' => 'LIKE', // Searches for the state within the multi-line address
            );
        }
        $meta_query[] = array(
            'key'     => 'dateandtime',
            'value'   => current_time('mysql'), // Current date/time in WP format
            'compare' => '>=', // Only get future events
            'type'    => 'DATETIME',
        );
        $orderby = 'meta_value';
        $query = array(
            'post_type'      => $attributes['postType'],
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'meta_key'       => $metakey,
            'orderby'        => $orderby,
            'order'          => 'ASC',
            'paged'          => $paged,
            'meta_query'     => $meta_query,
        );
    } else if ($attributes['postType'] == 'cf7_organizations') {
        $orderby = 'title';
        $query = array(
            'post_type'      => $attributes['postType'],
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'orderby'        => $orderby,
            'order'          => 'ASC',
            'paged'          => $paged,
        );
    }
    $query = new WP_Query($query);

    if (!$query->have_posts()) {
        return '<p>No submissions found.</p>';
    }
    $seach_results = '';
    if (!empty($search)) {
        $seach_results = '<p>Results found for: ' . $search;
    }
    $output = $seach_results . '<ul class="cf7-submission-list">';
    while ($query->have_posts()) {
        $query->the_post();
        $custom_fields = get_post_meta(get_the_ID());
        $datetime_string = '';
        $location_string = '';
        $organizer_string = '';
        if (!empty($custom_fields['location'])) {
            $location = $custom_fields['location'];
        } else if (!empty($custom_fields['multi-lineaddress'])) {
            $location = $custom_fields['multi-lineaddress'];
        }
        if (!empty($location)) {
            $location_string = '<p class="location">' . $location[0] . '</p>';
        }
        if (!empty($custom_fields['dateandtime'])) {
            $datetime = new DateTime($custom_fields['dateandtime'][0]);
            $datetime_string = '<div class="submission-date"><p>' . $datetime->format("j M \r\n g:i A") . '</p></div>';
        }
        if (!empty($custom_fields['organizer'])) {
            $organizer_string = '<h4>' . $custom_fields['organizer'][0] . '</h4>';
        }
        if (!empty($custom_fields)) {
            $output .= '<li>' . 
            $datetime_string .
            '<div class="submission-content">' . 
            $organizer_string .
            '<h3>' . get_the_title() . '</h3>' . 
            '<p>' . get_the_excerpt() . '</p>' . 
            $location_string .
            '</div>' . 
            '</li>';
        }
    }
    $output .= '<div class="pagination">' . paginate_links(array(
        'total'   => $query->max_num_pages,
        'current' => max(1, get_query_var('paged')),
        'format'  => '?paged=%#%',
        'prev_text' => '« Previous',
        'next_text' => 'Next »',
    )) . '</div>';
    wp_reset_postdata();

    return $output . '</ul>';
}