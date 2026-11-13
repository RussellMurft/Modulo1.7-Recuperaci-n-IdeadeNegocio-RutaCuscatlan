<?php
/** Integration helpers for the Ruta Cuscatlán plugin and WordPress storage. */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The supplied plugin registers `lugar` and `categoria_lugar` and stores each
 * record in WordPress posts, postmeta and taxonomy tables. All reads therefore
 * use WordPress APIs and the database defined in wp-config.php.
 */
function rc_theme_plugin_ready() {
    return post_type_exists('lugar') && taxonomy_exists('categoria_lugar');
}

function rc_theme_get_destination_url() {
    $page = get_page_by_path('explorar');
    return ($page && 'publish' === $page->post_status) ? get_permalink($page) : home_url('/explorar/');
}

function rc_theme_get_stay_url() {
    $page = get_page_by_path('donde-hospedarse');
    return ($page && 'publish' === $page->post_status) ? get_permalink($page) : home_url('/donde-hospedarse/');
}

function rc_theme_get_suggest_url() {
    $page = get_page_by_path('sugerir-lugar');
    return ($page && 'publish' === $page->post_status) ? get_permalink($page) : home_url('/sugerir-lugar/');
}

function rc_theme_current_place_category() {
    if (!is_singular('lugar')) {
        return '';
    }
    $terms = get_the_terms(get_the_ID(), 'categoria_lugar');
    if (!$terms || is_wp_error($terms)) {
        return '';
    }
    return $terms[0]->name;
}

/** Small dashboard diagnostic so an administrator can verify the plugin/data link. */
function rc_theme_register_data_widget() {
    if (!current_user_can('manage_options')) {
        return;
    }
    wp_add_dashboard_widget(
        'rc_theme_data_status',
        __('Ruta Cuscatlán · Estado de los datos', 'ruta-cuscatlan-theme'),
        'rc_theme_render_data_widget'
    );
}
add_action('wp_dashboard_setup', 'rc_theme_register_data_widget');

function rc_theme_render_data_widget() {
    global $wpdb;
    $posts_table = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($wpdb->posts)));
    $db_ok = ($posts_table === $wpdb->posts);
    $plugin_ok = rc_theme_plugin_ready();
    $count = $plugin_ok ? rc_theme_place_count() : 0;

    echo '<div style="display:flex;gap:10px;align-items:center;margin:4px 0 12px">';
    echo '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:' . ($db_ok ? '#2e8b57' : '#b42318') . '"></span>';
    echo '<strong>' . esc_html($db_ok ? __('Base de datos de WordPress disponible', 'ruta-cuscatlan-theme') : __('No se pudo verificar la tabla de contenido', 'ruta-cuscatlan-theme')) . '</strong></div>';
    echo '<p>' . esc_html($plugin_ok ? __('Plugin Ruta Cuscatlán detectado.', 'ruta-cuscatlan-theme') : __('El plugin Ruta Cuscatlán no está activo o no ha registrado sus tipos de contenido.', 'ruta-cuscatlan-theme')) . '</p>';
    if ($plugin_ok) {
        echo '<p><strong style="font-size:24px;color:#276b55">' . esc_html(number_format_i18n($count)) . '</strong> ' . esc_html__('destinos publicados conectados a WordPress.', 'ruta-cuscatlan-theme') . '</p>';
        echo '<p><a href="' . esc_url(admin_url('edit.php?post_type=lugar')) . '">' . esc_html__('Administrar lugares turísticos', 'ruta-cuscatlan-theme') . ' →</a></p>';
    }
    echo '<p style="margin-bottom:0;color:#667570;font-size:12px">' . esc_html__('Los destinos y sus fichas se leen de la base configurada en wp-config.php. No se necesita una segunda conexión.', 'ruta-cuscatlan-theme') . '</p>';
}
