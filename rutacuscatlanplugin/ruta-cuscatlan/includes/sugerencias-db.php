<?php
/**
 * Ruta Cuscatlán — Tabla propia para los lugares sugeridos por usuarios.
 *
 * Crea {prefijo}ruta_sugerencias en la MISMA base de datos de wp-config.php,
 * por lo que aparece en phpMyAdmin junto a wp_posts, wp_options, etc.
 * Cada sugerencia sigue creando además un lugar "pendiente" en wp_posts
 * (flujo anterior), enlazado mediante la columna post_id.
 */
if (!defined('ABSPATH')) exit;

define('RUTA_SUG_DB_V', '1');

function ruta_sug_tabla() {
    global $wpdb;
    return $wpdb->prefix . 'ruta_sugerencias';
}

/* ---------- Crear / actualizar la tabla ---------- */
function ruta_sug_asegurar_tabla() {
    global $wpdb;
    $tabla = ruta_sug_tabla();
    $existe = ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $tabla)) === $tabla);
    if ($existe && get_option('ruta_sug_db') === RUTA_SUG_DB_V) return true;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $charset = $wpdb->get_charset_collate();
    // dbDelta es estricto: dos espacios tras PRIMARY KEY y un campo por línea.
    $sql = "CREATE TABLE {$tabla} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  post_id bigint(20) unsigned DEFAULT NULL,
  nombre_lugar varchar(200) NOT NULL,
  categoria varchar(60) NOT NULL DEFAULT '',
  departamento varchar(60) NOT NULL DEFAULT '',
  descripcion text NOT NULL,
  hospedajes text,
  nombre_autor varchar(120) NOT NULL DEFAULT '',
  correo_autor varchar(190) NOT NULL DEFAULT '',
  ip_hash char(32) NOT NULL DEFAULT '',
  estado varchar(20) NOT NULL DEFAULT 'pendiente',
  notas_admin text,
  fecha_envio datetime NOT NULL,
  fecha_revision datetime DEFAULT NULL,
  PRIMARY KEY  (id),
  KEY post_id (post_id),
  KEY estado (estado),
  KEY fecha_envio (fecha_envio)
) {$charset};";
    dbDelta($sql);

    $existe = ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $tabla)) === $tabla);
    if ($existe) {
        update_option('ruta_sug_db', RUTA_SUG_DB_V);
        ruta_sug_importar_antiguas();
    }
    return $existe;
}

// Revisión al cargar el panel (cubre actualizaciones del plugin sin reactivarlo).
add_action('admin_init', function () {
    if (current_user_can('manage_options')) ruta_sug_asegurar_tabla();
});

/* ---------- Guardar una sugerencia nueva ---------- */
function ruta_sug_guardar($d) {
    global $wpdb;
    if (!ruta_sug_asegurar_tabla()) return 0;
    $ok = $wpdb->insert(ruta_sug_tabla(), array(
        'post_id'        => !empty($d['post_id']) ? (int) $d['post_id'] : null,
        'nombre_lugar'   => $d['nombre_lugar'],
        'categoria'      => $d['categoria'],
        'departamento'   => $d['departamento'],
        'descripcion'    => $d['descripcion'],
        'hospedajes'     => $d['hospedajes'],
        'nombre_autor'   => $d['nombre_autor'],
        'correo_autor'   => $d['correo_autor'],
        'ip_hash'        => md5(wp_salt() . ($_SERVER['REMOTE_ADDR'] ?? '')),
        'estado'         => 'pendiente',
        'fecha_envio'    => current_time('mysql'),
    ));
    return $ok ? (int) $wpdb->insert_id : 0;
}

/* ---------- Importar sugerencias que ya estaban en wp_posts ---------- */
function ruta_sug_importar_antiguas() {
    global $wpdb;
    if (get_option('ruta_sug_importado')) return;
    $ids = get_posts(array(
        'post_type'   => 'lugar',
        'post_status' => array('pending', 'publish', 'draft'),
        'numberposts' => -1,
        'fields'      => 'ids',
        'meta_key'    => '_rc_sug',
    ));
    foreach ($ids as $pid) {
        $ya = $wpdb->get_var($wpdb->prepare('SELECT id FROM ' . ruta_sug_tabla() . ' WHERE post_id = %d', $pid));
        if ($ya) continue;
        $p   = get_post($pid);
        $raw = (string) get_post_meta($pid, '_rc_sug', true);
        $autor = $correo = $hosp = '';
        if (preg_match('/^(.*?)\s*<([^>]*)>/s', $raw, $m)) { $autor = trim($m[1]); $correo = trim($m[2]); }
        if (preg_match('/Hospedajes:\s*(.*)$/s', $raw, $m)) $hosp = trim($m[1]);
        $cat = '';
        $t = get_the_terms($pid, 'categoria_lugar');
        if ($t && !is_wp_error($t)) $cat = $t[0]->slug;
        $wpdb->insert(ruta_sug_tabla(), array(
            'post_id'      => $pid,
            'nombre_lugar' => $p->post_title,
            'categoria'    => $cat,
            'departamento' => (string) get_post_meta($pid, '_rc_depto', true),
            'descripcion'  => $p->post_content,
            'hospedajes'   => $hosp,
            'nombre_autor' => $autor,
            'correo_autor' => $correo,
            'ip_hash'      => '',
            'estado'       => ($p->post_status === 'publish') ? 'aprobada' : 'pendiente',
            'fecha_envio'  => $p->post_date,
        ));
    }
    update_option('ruta_sug_importado', 1);
}

/* ---------- Mantener el estado sincronizado con el lugar en WordPress ---------- */
add_action('transition_post_status', function ($nuevo, $viejo, $post) {
    if ($post->post_type !== 'lugar' || $nuevo === $viejo) return;
    global $wpdb;
    $tabla = ruta_sug_tabla();
    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $tabla)) !== $tabla) return;
    $estado = null;
    if ($nuevo === 'publish') $estado = 'aprobada';
    elseif ($nuevo === 'trash') $estado = 'rechazada';
    if (!$estado) return;
    $wpdb->query($wpdb->prepare(
        "UPDATE {$tabla} SET estado = %s, fecha_revision = %s WHERE post_id = %d AND estado <> %s",
        $estado, current_time('mysql'), $post->ID, $estado
    ));
}, 10, 3);

/* ---------- Pantalla de administración ---------- */
add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=lugar', 'Sugerencias de usuarios', 'Sugerencias', 'edit_others_posts', 'ruta-sugerencias', 'ruta_sug_pantalla');
});

function ruta_sug_estados() {
    return array('pendiente' => 'Pendiente', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada');
}

function ruta_sug_pantalla() {
    if (!current_user_can('edit_others_posts')) return;
    global $wpdb;
    $tabla  = ruta_sug_tabla();
    $cats   = function_exists('ruta_cats') ? ruta_cats() : array();
    $estados = ruta_sug_estados();
    $filtro = sanitize_key($_GET['estado'] ?? '');
    if (!isset($estados[$filtro])) $filtro = '';
    $por    = 20;
    $pag    = max(1, (int) ($_GET['paged'] ?? 1));
    $where  = $filtro ? $wpdb->prepare('WHERE estado = %s', $filtro) : '';
    $total  = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$tabla} {$where}");
    $filas  = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$tabla} {$where} ORDER BY fecha_envio DESC, id DESC LIMIT %d OFFSET %d",
        $por, ($pag - 1) * $por
    ));
    $base = admin_url('edit.php?post_type=lugar&page=ruta-sugerencias');

    echo '<div class="wrap"><h1>Sugerencias de usuarios</h1>';
    $msg = sanitize_key($_GET['ruta_ok'] ?? '');
    if ($msg) echo '<div class="notice notice-success is-dismissible"><p>Listo: ' . esc_html($msg) . '.</p></div>';

    echo '<p>Tabla en MySQL: <code>' . esc_html($tabla) . '</code> (base de datos <code>' . esc_html($wpdb->dbname) . '</code>). ';
    echo '<a class="button" href="' . esc_url(wp_nonce_url(admin_url('admin-post.php?action=ruta_sug_csv'), 'ruta_sug_csv')) . '">Descargar CSV</a></p>';

    echo '<ul class="subsubsub"><li><a href="' . esc_url($base) . '"' . ($filtro === '' ? ' class="current"' : '') . '>Todas</a></li>';
    foreach ($estados as $k => $v) {
        $n = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$tabla} WHERE estado = %s", $k));
        echo ' | <li><a href="' . esc_url(add_query_arg('estado', $k, $base)) . '"' . ($filtro === $k ? ' class="current"' : '') . '>' . esc_html($v) . ' (' . $n . ')</a></li>';
    }
    echo '</ul><table class="widefat striped" style="margin-top:12px"><thead><tr><th>#</th><th>Lugar</th><th>Categoría</th><th>Depto.</th><th>Descripción</th><th>Enviado por</th><th>Fecha</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>';

    if (!$filas) echo '<tr><td colspan="9">No hay sugerencias.</td></tr>';
    foreach ($filas as $f) {
        $cn = isset($cats[$f->categoria]) ? $cats[$f->categoria][0] : $f->categoria;
        $acc = array();
        if ($f->post_id && get_post($f->post_id)) $acc[] = '<a href="' . esc_url(get_edit_post_link($f->post_id)) . '">Editar lugar</a>';
        if ($f->estado !== 'aprobada' && $f->post_id) $acc[] = '<a href="' . esc_url(ruta_sug_url_accion('aprobar', $f->id)) . '">Aprobar y publicar</a>';
        if ($f->estado === 'pendiente') $acc[] = '<a href="' . esc_url(ruta_sug_url_accion('rechazar', $f->id)) . '">Rechazar</a>';
        $acc[] = '<a href="' . esc_url(ruta_sug_url_accion('eliminar', $f->id)) . '" onclick="return confirm(\'¿Eliminar este registro de la tabla? El lugar en WordPress no se borra.\')" style="color:#b32d2e">Eliminar registro</a>';
        echo '<tr><td>' . (int) $f->id . '</td><td><b>' . esc_html($f->nombre_lugar) . '</b></td><td>' . esc_html($cn) . '</td><td>' . esc_html($f->departamento) . '</td>';
        echo '<td style="max-width:320px">' . esc_html(wp_trim_words($f->descripcion, 30)) . ($f->hospedajes ? '<br><small><b>Hospedajes:</b> ' . esc_html(wp_trim_words($f->hospedajes, 15)) . '</small>' : '') . '</td>';
        echo '<td>' . esc_html($f->nombre_autor) . '<br><small>' . esc_html($f->correo_autor) . '</small></td><td>' . esc_html($f->fecha_envio) . '</td>';
        echo '<td>' . esc_html($estados[$f->estado] ?? $f->estado) . '</td><td>' . implode('<br>', $acc) . '</td></tr>';
    }
    echo '</tbody></table>';

    $paginas = (int) ceil($total / $por);
    if ($paginas > 1) {
        echo '<div class="tablenav"><div class="tablenav-pages">' . paginate_links(array(
            'base' => add_query_arg('paged', '%#%', $filtro ? add_query_arg('estado', $filtro, $base) : $base),
            'format' => '', 'current' => $pag, 'total' => $paginas,
        )) . '</div></div>';
    }
    echo '<h2>Verlas en phpMyAdmin</h2><p>Selecciona la base <code>' . esc_html($wpdb->dbname) . '</code>, abre la tabla <code>' . esc_html($tabla) . '</code> o ejecuta: <code>SELECT * FROM ' . esc_html($tabla) . ' ORDER BY fecha_envio DESC;</code></p></div>';
}

function ruta_sug_url_accion($accion, $id) {
    return wp_nonce_url(admin_url('admin-post.php?action=ruta_sug_accion&accion=' . $accion . '&id=' . (int) $id), 'ruta_sug_' . $accion . '_' . (int) $id);
}

add_action('admin_post_ruta_sug_accion', function () {
    if (!current_user_can('edit_others_posts')) wp_die('Sin permiso.');
    global $wpdb;
    $accion = sanitize_key($_GET['accion'] ?? '');
    $id     = (int) ($_GET['id'] ?? 0);
    check_admin_referer('ruta_sug_' . $accion . '_' . $id);
    $tabla = ruta_sug_tabla();
    $f = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tabla} WHERE id = %d", $id));
    $ok = 'sin cambios';
    if ($f) {
        if ($accion === 'aprobar' && $f->post_id && get_post($f->post_id)) {
            wp_update_post(array('ID' => (int) $f->post_id, 'post_status' => 'publish')); // el hook actualiza el estado
            $ok = 'sugerencia aprobada y publicada';
        } elseif ($accion === 'rechazar') {
            if ($f->post_id && get_post($f->post_id)) wp_trash_post((int) $f->post_id); // el hook actualiza el estado
            else $wpdb->update($tabla, array('estado' => 'rechazada', 'fecha_revision' => current_time('mysql')), array('id' => $id));
            $ok = 'sugerencia rechazada';
        } elseif ($accion === 'eliminar') {
            $wpdb->delete($tabla, array('id' => $id), array('%d'));
            $ok = 'registro eliminado';
        }
    }
    wp_safe_redirect(add_query_arg('ruta_ok', rawurlencode($ok), admin_url('edit.php?post_type=lugar&page=ruta-sugerencias')));
    exit;
});

/* ---------- Exportar a CSV (abre bien en Excel) ---------- */
add_action('admin_post_ruta_sug_csv', function () {
    if (!current_user_can('edit_others_posts')) wp_die('Sin permiso.');
    check_admin_referer('ruta_sug_csv');
    global $wpdb;
    $filas = $wpdb->get_results('SELECT * FROM ' . ruta_sug_tabla() . ' ORDER BY fecha_envio DESC, id DESC', ARRAY_A);
    nocache_headers();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="sugerencias-ruta-cuscatlan-' . gmdate('Y-m-d') . '.csv"');
    $o = fopen('php://output', 'w');
    fwrite($o, "\xEF\xBB\xBF");
    fputcsv($o, array('id', 'post_id', 'nombre_lugar', 'categoria', 'departamento', 'descripcion', 'hospedajes', 'nombre_autor', 'correo_autor', 'estado', 'fecha_envio', 'fecha_revision'));
    foreach ($filas as $r) {
        $linea = array();
        foreach (array('id', 'post_id', 'nombre_lugar', 'categoria', 'departamento', 'descripcion', 'hospedajes', 'nombre_autor', 'correo_autor', 'estado', 'fecha_envio', 'fecha_revision') as $k) {
            $v = (string) ($r[$k] ?? '');
            if ($v !== '' && strpbrk($v[0], "=+-@\t\r") !== false) $v = "'" . $v; // evita inyección de fórmulas en Excel
            $linea[] = $v;
        }
        fputcsv($o, $linea);
    }
    fclose($o);
    exit;
});
