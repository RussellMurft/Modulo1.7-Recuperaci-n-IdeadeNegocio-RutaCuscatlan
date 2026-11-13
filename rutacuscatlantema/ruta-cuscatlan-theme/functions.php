<?php
/**
 * Ruta Cuscatlán Explorer theme functions.
 * The theme intentionally uses the active WordPress database connection via
 * WordPress APIs. It does not store database credentials or open a second DB connection.
 */
if (!defined('ABSPATH')) {
    exit;
}

define('RC_THEME_VERSION', '1.3.8');
define('RC_THEME_DIR', get_template_directory());
define('RC_THEME_URI', get_template_directory_uri());

require_once RC_THEME_DIR . '/inc/integration.php';

function rc_theme_setup() {
    load_theme_textdomain('ruta-cuscatlan-theme', RC_THEME_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_theme_support('automatic-feed-links');
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 320,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));

    register_nav_menus(array(
        'primary' => __('Menú principal', 'ruta-cuscatlan-theme'),
        'footer'  => __('Menú del pie', 'ruta-cuscatlan-theme'),
    ));

    add_image_size('rc-place-card', 720, 520, true);
    add_image_size('rc-place-hero', 1600, 900, true);
}
add_action('after_setup_theme', 'rc_theme_setup');

function rc_theme_assets() {
    wp_enqueue_style('rc-theme-style', get_stylesheet_uri(), array(), RC_THEME_VERSION);
    wp_enqueue_style('rc-theme-main', RC_THEME_URI . '/assets/theme.css', array('rc-theme-style'), RC_THEME_VERSION);
    wp_enqueue_script('rc-theme-main', RC_THEME_URI . '/assets/theme.js', array(), RC_THEME_VERSION, true);

    // The plugin enqueues its own route CSS/JS. This stylesheet is loaded after it
    // so the theme can provide a cohesive visual layer without replacing its logic.
    if (function_exists('ruta_cats')) {
        wp_enqueue_style('rc-theme-plugin-polish', RC_THEME_URI . '/assets/plugin-polish.css', array('rc-theme-main'), RC_THEME_VERSION);
    }
}
add_action('wp_enqueue_scripts', 'rc_theme_assets', 20);

function rc_theme_body_classes($classes) {
    if (function_exists('ruta_cats')) {
        $classes[] = 'has-ruta-cuscatlan-plugin';
    }
    if (is_singular('lugar')) {
        $classes[] = 'rc-single-place';
    }
    return $classes;
}
add_filter('body_class', 'rc_theme_body_classes');

function rc_theme_logo_markup() {
    if (has_custom_logo()) {
        return get_custom_logo();
    }

    if (defined('RUTA_URL')) {
        return '<a class="rc-brand-mark" href="' . esc_url(home_url('/')) . '" aria-label="' . esc_attr(get_bloginfo('name')) . '"><img src="' . esc_url(RUTA_URL . 'assets/logo.svg') . '" alt="' . esc_attr(get_bloginfo('name')) . '"></a>';
    }

    return '<a class="rc-brand-wordmark" href="' . esc_url(home_url('/')) . '"><span class="rc-brand-icon" aria-hidden="true">⌖</span><span><strong>RUTA CUSCATLÁN</strong><small>DESCUBRE EL SALVADOR</small></span></a>';
}

function rc_theme_fallback_menu() {
    $links = array(
        array(__('Inicio', 'ruta-cuscatlan-theme'), home_url('/')),
        array(__('Explorar destinos', 'ruta-cuscatlan-theme'), home_url('/explorar/')),
        array(__('Alojamiento', 'ruta-cuscatlan-theme'), home_url('/donde-hospedarse/')),
        array(__('Nosotros', 'ruta-cuscatlan-theme'), home_url('/nosotros/')),
        array(__('Sugerir un lugar', 'ruta-cuscatlan-theme'), home_url('/sugerir-lugar/')),
    );
    echo '<ul class="rc-menu">';
    foreach ($links as $link) {
        printf('<li><a href="%s">%s</a></li>', esc_url($link[1]), esc_html($link[0]));
    }
    echo '</ul>';
}

function rc_theme_dedupe_menu_items($items, $args) {
    if (empty($args->theme_location) || 'primary' !== $args->theme_location) {
        return $items;
    }
    $seen = array();
    $clean = array();
    foreach ($items as $item) {
        $url = strtolower(untrailingslashit((string) $item->url));
        $title = strtolower(trim(wp_strip_all_tags((string) $item->title)));
        $key = $url . '|' . $title;
        $title_key = 'title|' . $title;
        if (isset($seen[$key]) || isset($seen[$title_key])) {
            continue;
        }
        $seen[$key] = true;
        $seen[$title_key] = true;
        $clean[] = $item;
    }
    return $clean;
}
add_filter('wp_nav_menu_objects', 'rc_theme_dedupe_menu_items', 20, 2);

function rc_theme_primary_menu() {
    $menu = wp_get_nav_menu_object('Ruta Cuscatlán');
    if (has_nav_menu('primary')) {
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'rc-menu',
            'fallback_cb'    => 'rc_theme_fallback_menu',
            'depth'          => 2,
        ));
    } elseif ($menu) {
        wp_nav_menu(array(
            'menu'           => $menu->term_id,
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'rc-menu',
            'fallback_cb'    => 'rc_theme_fallback_menu',
            'depth'          => 2,
        ));
    } else {
        rc_theme_fallback_menu();
    }
}

function rc_theme_plugin_missing_notice() {
    if (!current_user_can('manage_options') || function_exists('ruta_cats') || !current_user_can('switch_themes')) {
        return;
    }
    echo '<div class="notice notice-warning"><p><strong>Ruta Cuscatlán Explorer:</strong> ' . esc_html__('activa el plugin Ruta Cuscatlán para habilitar el catálogo, los filtros, las fichas y los hospedajes.', 'ruta-cuscatlan-theme') . '</p></div>';
}
add_action('admin_notices', 'rc_theme_plugin_missing_notice');

function rc_theme_place_count() {
    if (!post_type_exists('lugar')) {
        return 0;
    }
    $counts = wp_count_posts('lugar');
    return isset($counts->publish) ? (int) $counts->publish : 0;
}

function rc_theme_page_content($slug, $shortcode = '') {
    $page = get_page_by_path($slug);
    if ($page && 'publish' === $page->post_status) {
        return apply_filters('the_content', $page->post_content);
    }
    return $shortcode ? do_shortcode($shortcode) : '';
}

/** About page and social profiles managed from the WordPress Customizer. */
function rc_theme_ensure_about_page() {
    if (!get_page_by_path('nosotros')) {
        wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>'Nosotros','post_name'=>'nosotros','post_content'=>'[ruta_nosotros]'));
    }
}
add_action('after_switch_theme','rc_theme_ensure_about_page');
function rc_theme_social_customizer($wp_customize) {
    $wp_customize->add_section('rc_social_section', array(
        'title'       => __('Redes sociales · Ruta Cuscatlán', 'ruta-cuscatlan-theme'),
        'priority'    => 160,
        'description' => __('Agrega los enlaces oficiales de Ruta Cuscatlán. Solo se mostrarán en el pie de página las redes que tengan una URL configurada.', 'ruta-cuscatlan-theme'),
    ));
    $networks = array(
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
        'tiktok'    => 'TikTok',
        'youtube'   => 'YouTube',
        'whatsapp'  => 'WhatsApp',
    );
    foreach ($networks as $key => $label) {
        $wp_customize->add_setting('rc_social_' . $key, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control('rc_social_' . $key, array(
            'label'       => $label . ' URL',
            'section'     => 'rc_social_section',
            'type'        => 'url',
            'input_attrs' => array('placeholder' => 'https://'),
        ));
    }
}
add_action('customize_register', 'rc_theme_social_customizer');

function rc_theme_social_links() {
    $networks = array(
        'facebook' => array(
            'label' => 'Facebook',
            'svg' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 21v-8h2.7l.4-3.1h-3.1v-2c0-.9.3-1.5 1.6-1.5h1.7V3.6c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.1H7.3V13h2.8v8z"/></svg>',
        ),
        'instagram' => array(
            'label' => 'Instagram',
            'svg' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.2" y="3.2" width="17.6" height="17.6" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4.1" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.8" cy="6.5" r="1.2"/></svg>',
        ),
        'tiktok' => array(
            'label' => 'TikTok',
            'svg' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15.7 3h-3.2v12.1a2.8 2.8 0 1 1-2.4-2.8v-3.3a6.1 6.1 0 1 0 5.6 6.1V9a8.1 8.1 0 0 0 4.4 1.3V7.1c-2.4-.1-4.2-1.7-4.4-4.1z"/></svg>',
        ),
        'youtube' => array(
            'label' => 'YouTube',
            'svg' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23 7.1a3 3 0 0 0-2.1-2.2C19 4.4 12 4.4 12 4.4s-7 0-8.9.5A3 3 0 0 0 1 7.1 31 31 0 0 0 .5 12 31 31 0 0 0 1 16.9a3 3 0 0 0 2.1 2.2c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 0 0 2.1-2.2 31 31 0 0 0 .5-4.9 31 31 0 0 0-.5-4.9zM9.6 15.6V8.4l6.2 3.6z"/></svg>',
        ),
        'whatsapp' => array(
            'label' => 'WhatsApp',
            'svg' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 3.5A11.8 11.8 0 0 0 2 17.7L.5 23.5l6-1.6a11.8 11.8 0 0 0 5.6 1.4h.1A11.8 11.8 0 0 0 20.5 3.5zM12.2 21.3a9.8 9.8 0 0 1-5-1.4l-.4-.2-3.5.9.9-3.4-.2-.4a9.8 9.8 0 1 1 8.2 4.5zm5.4-7.3c-.3-.2-1.8-.9-2.1-1s-.5-.2-.7.2-.8 1-1 1.2-.4.2-.7.1a8 8 0 0 1-2.4-1.5 9 9 0 0 1-1.7-2.1c-.2-.3 0-.5.1-.6l.5-.6.3-.5c.1-.2 0-.4 0-.6s-.7-1.7-1-2.3c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4s-1.2 1.2-1.2 2.8 1.2 3.2 1.4 3.4a11.3 11.3 0 0 0 4.4 3.9c.6.3 1.1.4 1.5.6.6.2 1.2.2 1.7.1.5-.1 1.8-.7 2-1.4.3-.7.3-1.3.2-1.4s-.3-.2-.6-.4z"/></svg>',
        ),
    );

    echo '<div class="rc-social-links" aria-label="' . esc_attr__('Redes sociales', 'ruta-cuscatlan-theme') . '"><div class="rc-social-list">';
    foreach ($networks as $key => $network) {
        $url = trim((string) get_theme_mod('rc_social_' . $key, ''));
        $icon = '<span class="rc-social-icon">' . $network['svg'] . '</span>';
        if ($url && wp_http_validate_url($url)) {
            echo '<a class="rc-social-link rc-social-link-' . esc_attr($key) . '" href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr($network['label']) . '" title="' . esc_attr($network['label']) . '">' . $icon . '</a>';
        } else {
            echo '<span class="rc-social-link rc-social-link-' . esc_attr($key) . ' rc-social-link-disabled" aria-label="' . esc_attr($network['label']) . '" title="' . esc_attr($network['label']) . '">' . $icon . '</span>';
        }
    }
    echo '</div></div>';
}
