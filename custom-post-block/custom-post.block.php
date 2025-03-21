<?php
/**
 * Plugin Name: Custom Post Type Block
 * Description: Displays custom post types in gutenberg editor.
 * Version: 1.4
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
function register_custom_tag_dropdown_block() {
    wp_register_script(
        'custom-tag-dropdown-block',
        plugins_url('block.js', __FILE__),
        array('wp-blocks', 'wp-editor', 'wp-components', 'wp-data'),
        filemtime(plugin_dir_path(__FILE__) . 'block.js')
    );

    register_block_type('custom/tag-dropdown', array(
        'editor_script' => 'custom-tag-dropdown-block',
        'render_callback' => 'render_custom_tag_dropdown',
    ));
}
add_action('init', 'register_custom_tag_dropdown_block');

function render_custom_tag_dropdown($attributes) {
    $terms = get_terms(array(
        'taxonomy' => 'post_tag',
        'hide_empty' => false,
    ));

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No tags found.</p>';
    }
    // Get the selected tag from the URL query parameter
    $selected_tag = isset($_GET['q']) ? esc_attr($_GET['q']) : '';

    ob_start();
    ?>
    <select name="post_tags" id="post-tags-dropdown" class="<?php echo !empty($attributes['customClass']) ?? $attributes['customClass']; ?>">
        <option value="">Filter by state</option>
        <?php foreach ($terms as $term): ?>
            <option value="<?php echo esc_attr($term->name); ?>"
                <?php selected($selected_tag, $term->name); ?>>
                <?php echo esc_html($term->name); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <script>
        jQuery(document).ready(function() {
            // Initialize Select2
            jQuery("#post-tags-dropdown").select2();

            // Listen for the Select2 change event
            jQuery("#post-tags-dropdown").on("select2:select", function(e) {
                var selectedTag = e.params.data.id; // Get selected value
                if (selectedTag) {
                    window.location.href = "<?php echo esc_url(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>?q=" + encodeURIComponent(selectedTag);
                } else {
                    window.location.href = "<?php echo esc_url(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>";
                }
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
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
    $cache_key = 'cf7_submission_list_' . md5($attributes['postType'] . $search . $paged);

    // Check cache first
    $cached_output = get_transient($cache_key);
    if ($cached_output !== false) {
        return $cached_output; // Return cached output if it exists
    }

    $meta_query = array();
    if ($attributes['postType'] == 'cf7_protest-listing') {
        $tax_query = [];
        if (!empty($search)) {
            $tax_query[] = array(
                'taxonomy' => 'post_tag',
                'field'    => 'name',
                'terms'    => $search,
            );
        }
        $current_time_minus_24 = date('Y-m-d H:i:s', strtotime('-24 hours'));
        $meta_query = array(
            'relation' => 'OR',
            array(
                'key'     => 'dateandtime',
                'value'   => $current_time_minus_24,
                'compare' => '>=',
                'type'    => 'DATETIME',
            ),
            array(
                'key'     => 'startdate',
                'value'   => $current_time_minus_24,
                'compare' => '>=',
                'type'    => 'DATETIME',
            )
        );
        $orderby = 'meta_value';
        $query = array(
            'post_type'      => $attributes['postType'],
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'orderby'        => $orderby,
            'order'          => 'ASC',
            'paged'          => $paged,
            'meta_query'     => $meta_query,
            'tax_query'      => $tax_query,
        );
    } else if ($attributes['postType'] == 'cf7_organizations') {
        $tax_query = [];
        if (!empty($search)) {
            $tax_query[] = array(
                'taxonomy' => 'post_tag',
                'field'    => 'name',
                'terms'    => $search,
            );
        }
        $orderby = 'title';
        $query = array(
            'post_type'      => $attributes['postType'],
            'post_status'    => 'publish',
            'posts_per_page' => 10,
            'orderby'        => $orderby,
            'order'          => 'ASC',
            'paged'          => $paged,
            'tax_query'      => $tax_query,
        );
    }

    $query = new WP_Query($query);

    if (!$query->have_posts()) {
        return '<p>No submissions found.</p>';
    }

    // Prepare search results message
    $search_results = '';
    if (!empty($search)) {
        $search_results = '<p>Results found for: ' . $search . '</p>';
    }

    $output = $search_results . '<ul class="cf7-submission-list">';

    // Loop through posts and render each submission
    while ($query->have_posts()) {
        $query->the_post();
        $custom_fields = get_post_meta(get_the_ID());
        $tags = get_the_terms(get_the_ID(), 'post_tag'); // Get tags for the post
        
        $output .= cf7_render_submission_item($custom_fields, $tags, $search);
    }

    $output .= '</ul>';
    $output .= cf7_render_pagination($query);

    // Include JavaScript for modal functionality
    $output .= cf7_render_modal_script();

    wp_reset_postdata();

    // Cache the output for 1 hour
    set_transient($cache_key, $output, HOUR_IN_SECONDS);

    return $output;
}

// Function to render each submission item
function cf7_render_submission_item($custom_fields, $tags, $search) {
    $is_organizer_email = !empty($custom_fields['isthistheorganizersemail'][0]) 
        && !empty($custom_fields['email'][0]) 
        && strtolower($custom_fields['isthistheorganizersemail'][0]) === 'yes';
    $organizer = !empty($custom_fields['organizer'][0]) ? esc_html($custom_fields['organizer'][0]) : null;
    $legacy_address = !empty($custom_fields['multi-lineaddress'][0]) ? esc_html($custom_fields['multi-lineaddress'][0]) : null;
    $city = !empty($custom_fields['city']) ? esc_html($custom_fields['city'][0]) : '';
    $state = !empty($custom_fields['state']) ? esc_html($custom_fields['state'][0]) : '';
    $location = !empty($custom_fields['location']) ? esc_html($custom_fields['location'][0]) : null;
    $address = $legacy_address ?? $location ?? $city . ', ' . $state;
    $date_time = !empty($custom_fields['dateandtime']) ? esc_html($custom_fields['dateandtime'][0]) : null;
    $legacy_link = !empty($custom_fields['externalsinguplistinglink']) ? esc_url($custom_fields['externalsinguplistinglink'][0]) : null;
    $signup_link = !empty($custom_fields['external-link']) ? esc_url($custom_fields['external-link'][0]) : null;
    $email = $is_organizer_email ? esc_html($custom_fields['email'][0]) : null;
    $image_url = !empty($custom_fields['file-upload']) ? esc_url($custom_fields['file-upload'][0]) : null;
    $legacy_img = !empty($custom_fields['uploadflyershere']) ? esc_url($custom_fields['uploadflyershere'][0]) : null;
    $legacy_logo = !empty($custom_fields['logoupload']) ? esc_url($custom_fields['logoupload'][0]) : null;
    $website = !empty($custom_fields['website']) ? esc_url($custom_fields['website'][0]) : null;
    
    // Format the date/time
    $datetime_string = '';
    $timestamp = null;
    if (!empty($date_time)) {
        $datetime = new DateTime($date_time);
        $starttime = null;
    }
    if (!empty($custom_fields['startdate'])) {
        $starttime = !empty($custom_fields['starttime']) ? $custom_fields['starttime'][0] : null;
        $datetime = new DateTime($custom_fields['startdate'][0] . ' ' . $starttime);
    }
    if ($datetime) {
        $datetime_string = '<div class="submission-date"><p>' . $datetime->format("j M \r\n g:i A") . '</p></div>';
        $timestamp = $datetime->getTimestamp();
    }
    
    $organizer_string = $organizer ? '<h4 class="organizer">' . $organizer . '</h4>' : '';
    $location_string = $address ? '<p class="location">' . $address . '</p>' : '';
    
    // Create tags for the post
    $tag_string = '';
    if (!empty($tags) && !is_wp_error($tags)) {
        foreach ($tags as $tag) {
            $tag_string .= '<span class="tag"><a href="' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '?q=' . esc_html($tag->name) . '">#' . esc_html($tag->name) . '</a></span>';
        }
    } else {
        $tag_string = '<span class="no-tags">No tags assigned</span>';
    }
    
    // Render the submission item HTML
    $output = '<li class="cf7-submission-item" data-id="' . get_the_ID() . '" data-email="' . $email . '" data-link="' . ($legacy_link ?? $signup_link) . '" data-timestamp="' . $timestamp . '" data-image="'. ($legacy_img ?? $image_url ?? $legacy_logo) .'" data-website="' . $website . '">' . 
                $datetime_string .
                '<div class="submission-content">' . 
                $organizer_string .
                '<h3 class="title">' . get_the_title() . '</h3>' . 
                '<p class="content">' . get_the_excerpt() . '</p>' . 
                $location_string .
                $tag_string .
                '</div>' . 
            '</li>';

    return $output;
}

// Function to render pagination
function cf7_render_pagination($query) {
    return '<div class="pagination">' . paginate_links(array(
        'total'   => $query->max_num_pages,
        'current' => max(1, get_query_var('paged')),
        'format'  => '?paged=%#%',
        'prev_text' => '« Previous',
        'next_text' => 'Next »',
    )) . '</div>';
}

// Function to render the modal script
function cf7_render_modal_script() {
    return '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const modalHTML = `
                <div id="submission-modal" class="cf7-modal" style="display:none;">
                    <div class="cf7-modal-content">
                        <span class="cf7-close">&times;</span>
                        <div id="modal-body"></div>
                    </div>
                </div>`;
            document.body.insertAdjacentHTML("beforeend", modalHTML);
            const items = document.querySelectorAll(".cf7-submission-item");
            const modal = document.getElementById("submission-modal");
            const modalBody = document.getElementById("modal-body");
            const closeModal = document.querySelector(".cf7-close");

            items.forEach(item => {
                item.addEventListener("click", function() {
                    let postId = this.getAttribute("data-id");
                    let title = this.querySelector(".title").innerHTML;
                    let address = this.querySelector(".location").outerHTML;
                    let content = this.querySelector(".content").outerHTML;
                    let email = this.getAttribute("data-email");
                    let organizer = this.querySelector(".organizer") && this.querySelector(".organizer").innerHTML;
                    let date_time = this.querySelector(".submission-date") && this.querySelector(".submission-date").outerHTML;
                    let timestamp = this.getAttribute("data-timestamp");
                    let externalLink = this.getAttribute("data-link");
                    let image = this.getAttribute("data-image");
                    let website = this.getAttribute("data-website");

                    let organizer_string = (email ? "<a href=\"mailto:" + email + "\">" + organizer + "</a>" : organizer);
                    let date_string = date_time ? `<h4>Date & Time:</h4> <time datetime="${timestamp || null}">${date_time}</time>` : null;
                    
                    modalBody.innerHTML = `
                        ${image ? `
                        <div class="image">
                            <img src="${image}" width="auto" height="auto" />
                        </div>` : ""}
                        <h2>${title}</h2>
                        ${website ? `<p><a href="${website}" target="_blank" rel="noopener noreferrer">Website</a></p>` : ""}
                        ${(organizer_string && `<h4>Organizer:</h4> <p>${organizer_string}</p>`) || \'\'}
                        <h4>Location:</h4> <address>${address}</address>
                        ${date_string ? date_string : ""}
                        ${content}
                        ${externalLink ? `<p><a href="${externalLink}" target="_blank" rel="noopener noreferrer">Sign Up</a></p>` : ""}
                    `;
                    modal.style.display = "flex";
                    document.body.style.overflow = "hidden"; 
                });
            });

            closeModal.addEventListener("click", function() {
                modal.style.display = "none";
                document.body.style.overflow = ""; 
            });

            window.addEventListener("click", function(event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                    document.body.style.overflow = ""; 
                }
            });
        });
    </script>';
}