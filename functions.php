<?php

/**
 * MEC Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package MEC
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define('CHILD_THEME_MEC_VERSION', '1.0.0');

if (! defined('WP_DEBUG')) {
    die('Direct access forbidden.');
}
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
});

add_action('admin_init', 'check_acf_plugin_active');
function check_acf_plugin_active()
{
    if (!is_plugin_active('advanced-custom-fields/acf.php') && !is_plugin_active('advanced-custom-fields-pro/acf.php')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p><strong>ACF Plugin is required</strong> — Please install and activate the Advanced Custom Fields plugin.</p></div>';
        });
    }
}

function mec_scripts()
{
    wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.7.1.min.js', array(), '3.7.1', true);
}
add_action('wp_enqueue_scripts', 'mec_scripts');

// wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
function theme_gsap_script()
{
    wp_enqueue_script('lenis-js', 'https://unpkg.com/lenis@1.3.8/dist/lenis.min.js', array(), false, true);
    // The core GSAP library
    wp_enqueue_script('gsap-js', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js', array(), false, true);
    // ScrollTrigger - with gsap.js passed as a dependency
    wp_enqueue_script('gsap-st', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js', array('gsap-js'), false, true);
    // Your animation code file - with gsap.js passed as a dependency
    // wp_enqueue_script('gsap-js2', get_stylesheet_directory_uri() . '/assets/js/app.js', array('gsap-js'), false, true);
    // Tilt.js - with gsap.js passed as a dependency
    wp_enqueue_script('tilt-js', "https://cdn.jsdelivr.net/npm/tilt.js@1.2.1/dest/tilt.jquery.min.js", array(), false, true);
}
add_action('wp_enqueue_scripts', 'theme_gsap_script');

function theme_enqueue_assets()
{
    // Filesystem path for PHP file functions
    $manifest_path = get_stylesheet_directory() . '/dist/manifest.json';

    if (!file_exists($manifest_path)) return;

    $manifest = json_decode(file_get_contents($manifest_path), true);

    $js = $manifest['assets/js/app.js']['file'] ?? '';
    $css = $manifest['assets/scss/app.scss']['file'] ?? '';
    $tailwind = $manifest['assets/css/tailwind.css']['file'] ?? '';

    $is_cdn = strpos($js, 'https://') === 0 || strpos($css, 'https://') === 0;

    // URL base for enqueue (must be URI, not filesystem path)
    $base_uri =  ""; // get_stylesheet_directory_uri() . '/dist/';

    // If CDN URLs, use them directly; otherwise use local paths
    if ($is_cdn) {
        // Files already contain full CDN URLs, use them directly
        $css_url = $css;
        $tailwind_url = $tailwind;
        $js_url = $js;
    } else {
        // Files contain relative paths, prepend local base URI
        $base_uri = get_stylesheet_directory_uri() . '/dist/';
        $css_url = $base_uri . $css;
        $tailwind_url = $base_uri . $tailwind;
        $js_url = $base_uri . $js;
    }

    if ($css_url) {
        wp_enqueue_style('theme-style', $css_url, [], null);
    }

    if ($tailwind_url) {
        wp_enqueue_style('theme-tailwind', $tailwind_url, [], null);
    }

    if ($js_url) {
        wp_enqueue_script('theme-script', $js_url, [], null, true);
    }

    add_filter('script_loader_tag', function ($tag, $handle, $src) {
        if ($handle === 'theme-script') {
            return '<script type="module" src="' . esc_url($src) . '" defer></script>';
        }
        return $tag;
    }, 10, 3);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_assets');

/*
// Projects post type
function register_project_post_type_and_fields()
{

    // 1. Register the Custom Post Type
    $labels = array(
        'name' => _x('Projects', 'Post Type General Name', 'textdomain'),
        'singular_name' => _x('Project', 'Post Type Singular Name', 'textdomain'),
        'menu_name' => __('Projects', 'textdomain'),
        'add_new_item' => __('Add New Project', 'textdomain'),
        'edit_item' => __('Edit Project', 'textdomain'),
        'all_items' => __('All Projects', 'textdomain'),
    );

    $args = array(
        'label' => __('Project', 'textdomain'),
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-building',
        'supports' => array('title', 'excerpt', 'thumbnail'),
        'has_archive' => true,
        'rewrite' => array('slug' => 'projects'),
        'show_in_rest' => true,
    );

    register_post_type('project', $args);

    // 2. Register ACF Fields (only if ACF is active)
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_project_fields',
            'title' => 'Project Details',
            'fields' => array(
                array(
                    'key' => 'field_client_name',
                    'label' => 'Client Name',
                    'name' => 'client_name',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_location',
                    'label' => 'Location',
                    'name' => 'location',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_year',
                    'label' => 'Year',
                    'name' => 'year',
                    'type' => 'number',
                    'min' => 1900,
                    'max' => 2099,
                ),
                array(
                    'key' => 'field_details',
                    'label' => 'Project Details',
                    'name' => 'details',
                    'type' => 'textarea',
                    'rows' => 5,
                    'new_lines' => 'wpautop', // Formats the output with <p> tags
                ),
                array(
                    'key' => 'field_gallery',
                    'label' => 'Project Gallery',
                    'name' => 'project_gallery',
                    'type' => 'gallery',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'project',
                    ),
                ),
            ),
            'position' => 'normal',
            'style' => 'default', // Optional: or 'seamless'
        ));

        acf_add_local_field_group(array(
            'key' => 'group_project_sidebar',
            'title' => 'Feature Settings',
            'fields' => array(
                array(
                    'key' => 'field_is_featured',
                    'label' => 'Featured Project',
                    'name' => 'is_featured',
                    'type' => 'true_false',
                    'ui' => 1,
                    'message' => 'Mark as Featured',
                    'default_value' => 0,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'project',
                    ),
                ),
            ),
            'position' => 'side', // 🟢 Sidebar
            'style' => 'seamless',
        ));
    }
}
// add_action('init', 'register_project_post_type_and_fields');

add_action('admin_init', function () {
    remove_post_type_support('project', 'editor');
});
*/