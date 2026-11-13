<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Fotografías automáticas verificadas (v1.9.1)
 *
 * Para los destinos que no están en el catálogo manual, el plugin busca la imagen principal del
 * artículo de Wikipedia de ese mismo lugar (y, si no hay, busca directamente en Commons), confirma en Wikimedia Commons que la imagen tenga una
 * licencia libre (CC BY, CC BY-SA, CC0 o dominio público) y guarda autor, licencia y fuente.
 *
 * Reglas de seguridad editorial (las mismas del plugin):
 *  - Solo se acepta una imagen si el artículo menciona "Salvador" (evita homónimos de otros países).
 *  - No se aceptan desambiguaciones, SVG, mapas, escudos, banderas ni imágenes de menos de 800 px.
 *  - Una misma imagen nunca se asigna a dos destinos.
 *  - La búsqueda se hace en segundo plano (WP-Cron o botón del administrador); el sitio público
 *    solo lee el resultado guardado, así que nunca se vuelve lento por esta función.
 *  - La foto destacada de WordPress y el catálogo manual siguen teniendo prioridad.
 */

/** slug => títulos candidatos de Wikipedia. Prefijo "en:" para la Wikipedia en inglés (por defecto, español). */
function ruta_foto_auto_titulos() {
    return array(
        'miramundo' => array('Cerro Miramundo'),
        'costa-del-sol' => array('Costa del Sol (El Salvador)'),
        'maculis' => array('Playa El Maculís'),
        'amapulapa' => array('Parque Recreativo Amapulapa', 'Amapulapa'),
        'apulo' => array('Parque Recreativo Apulo', 'Parque Acuático Apulo'),
        'atecozol' => array('Parque Atecozol', 'Atecozol'),
        'sihuatehuacan' => array('Parque Sihuatehuacán', 'Sihuatehuacán'),
        'ichanmichen' => array('Ichanmichen', 'Parque Ichanmichen'),
        'laguna-apastepeque' => array('Laguna de Apastepeque'),
        'altos-de-la-cueva' => array('Altos de la Cueva'),
        'agua-fria' => array('Parque Agua Fría'),
        'sunset-park' => array('Sunset Park (El Salvador)'),
        'walter-thilo-deininger' => array('Parque Walter Thilo Deininger', 'Parque Nacional Walter T. Deininger', 'en:Walter Thilo Deininger National Park'),
        'parque-infantil-diversiones' => array('Parque Infantil de Diversiones'),
        'saburo-hirao' => array('Parque Saburo Hirao'),
        'balboa' => array('Parque Balboa (El Salvador)', 'Parque Natural Balboa', 'Parque Balboa'),
        'puerta-del-diablo' => array('Puerta del Diablo (El Salvador)', 'Puerta del Diablo'),
        'teatro-nacional-san-salvador' => array('Teatro Nacional de El Salvador', 'en:Teatro Nacional de El Salvador'),
        'plaza-morazan' => array('Plaza Morazán (San Salvador)', 'Plaza Morazán'),
        'plaza-gerardo-barrios' => array('Plaza Gerardo Barrios'),
        'plaza-libertad' => array('Plaza Libertad (San Salvador)', 'Plaza Libertad'),
        'palacio-nacional' => array('Palacio Nacional de El Salvador', 'en:National Palace (El Salvador)'),
        'catedral-metropolitana' => array('Catedral Metropolitana de San Salvador', 'en:Metropolitan Cathedral of the Holy Saviour'),
        'biblioteca-nacional' => array('Biblioteca Nacional de El Salvador'),
        'salvador-del-mundo' => array('Monumento al Divino Salvador del Mundo', 'Monumento al Salvador del Mundo'),
        'muna' => array('Museo Nacional de Antropología Dr. David J. Guzmán', 'Museo Nacional de Antropología (El Salvador)'),
        'marte' => array('Museo de Arte de El Salvador'),
        'teatro-santa-ana' => array('Teatro de Santa Ana', 'Teatro Nacional de Santa Ana'),
        'teatro-san-miguel' => array('Teatro Nacional de San Miguel', 'Teatro Nacional Francisco Gavidia'),
        'panchimalco' => array('Panchimalco'),
        'izalco' => array('Izalco'),
        'nahuizalco' => array('Nahuizalco'),
        'la-palma' => array('La Palma (El Salvador)', 'La Palma (Chalatenango)'),
        'ilobasco' => array('Ilobasco'),
        'san-andres' => array('San Andrés (sitio arqueológico)', 'Sitio arqueológico San Andrés'),
        'casa-blanca' => array('Casa Blanca (sitio arqueológico)', 'Sitio arqueológico Casa Blanca'),
        'ausoles-ahuachapan' => array('Los Ausoles', 'Ausoles de Ahuachapán'),
        'barra-de-santiago' => array('Barra de Santiago', 'Área de Conservación Barra de Santiago'),
        'bahia-jiquilisco' => array('Bahía de Jiquilisco'),
        'cuevas-de-corinto' => array('Cuevas de Corinto'),
        'laguna-de-alegria' => array('Laguna de Alegría'),
        'isla-meanguera' => array('Isla Meanguera del Golfo', 'Meanguera del Golfo'),
        'metalio' => array('Playa Metalío', 'Metalío'),
        'mizata' => array('Playa Mizata', 'Mizata'),
    );
}

/** Lee el resultado guardado para un slug (array con 'file', o array vacío). */
function ruta_foto_auto_get($slug) {
    $all = get_option('ruta_fotos_auto', array());
    if (!is_array($all) || empty($all[$slug]['file'])) return array();
    return $all[$slug];
}

function ruta_foto_auto_http($url) {
    $r = wp_remote_get($url, array('timeout' => 12, 'redirection' => 3, 'headers' => array('Accept' => 'application/json'), 'user-agent' => 'RutaCuscatlan/' . RUTA_V . ' (WordPress; guia turistica El Salvador)'));
    if (is_wp_error($r)) return null;
    if ((int) wp_remote_retrieve_response_code($r) !== 200) return null;
    $j = json_decode(wp_remote_retrieve_body($r), true);
    return is_array($j) ? $j : null;
}

/** Archivos ya usados (catálogo manual + resultados automáticos), para no repetir una imagen. */
function ruta_foto_auto_archivos_usados($except_slug = '') {
    $used = array();
    foreach (ruta_fotografias_catalogo() as $it) { $used[mb_strtolower(str_replace('_', ' ', $it['file']))] = true; }
    $all = get_option('ruta_fotos_auto', array());
    if (is_array($all)) foreach ($all as $s => $it) { if ($s !== $except_slug && !empty($it['file'])) $used[mb_strtolower(str_replace('_', ' ', $it['file']))] = true; }
    return $used;
}


/** Normaliza texto para comparar (sin acentos, minúsculas). */
function ruta_foto_auto_norm($t) {
    return strtolower(remove_accents((string) $t));
}

/** Frase de búsqueda y palabra distintiva del destino, derivadas de su primer título candidato. */
function ruta_foto_auto_busqueda_datos($slug) {
    $map = ruta_foto_auto_titulos();
    if (empty($map[$slug])) return array('', '');
    $t = preg_replace('/^[a-z]{2}:/', '', $map[$slug][0]);
    $t = trim(preg_replace('/\s*\(.*?\)\s*/', ' ', $t));
    $generic = array('parque','plaza','teatro','museo','playa','nacional','recreativo','natural','laguna','cerro','sitio','arqueologico','acuatico','biblioteca','catedral','metropolitana','monumento','divino','palacio','isla','bahia','cuevas','barra','area','conservacion','golfo','salvador','santa','infantil','diversiones','los','las','del','de','la','el');
    $best = '';
    foreach (preg_split('/\s+/', ruta_foto_auto_norm($t)) as $w) {
        $w = preg_replace('/[^a-z0-9]/', '', $w);
        if (strlen($w) >= 4 && !in_array($w, $generic, true) && strlen($w) > strlen($best)) $best = $w;
    }
    if ($best === '') foreach (preg_split('/\s+/', ruta_foto_auto_norm($t)) as $w) { $w = preg_replace('/[^a-z0-9]/', '', $w); if (strlen($w) > strlen($best)) $best = $w; }
    $phrase = stripos($t, 'Salvador') === false ? $t . ' El Salvador' : $t;
    return array($phrase, $best);
}

/**
 * Segunda vía: búsqueda directa en Commons. Acepta una imagen solo si su título/categorías/descripción
 * mencionan la palabra distintiva del lugar y "Salvador", y si pasa los mismos filtros de licencia.
 * Devuelve array(info), array() o null (fallo de red).
 */
function ruta_foto_auto_buscar_commons($slug, $used) {
    list($phrase, $token) = ruta_foto_auto_busqueda_datos($slug);
    if ($phrase === '' || $token === '') return array();
    $api = add_query_arg(array(
        'action' => 'query', 'format' => 'json', 'generator' => 'search', 'gsrsearch' => $phrase,
        'gsrnamespace' => 6, 'gsrlimit' => 12, 'prop' => 'imageinfo', 'iiprop' => 'extmetadata|size|mime',
    ), 'https://commons.wikimedia.org/w/api.php');
    $j = ruta_foto_auto_http($api);
    if ($j === null) return null;
    if (empty($j['query']['pages'])) return array();
    $pages = $j['query']['pages'];
    uasort($pages, function ($a, $b) { return (int) ($a['index'] ?? 0) <=> (int) ($b['index'] ?? 0); });
    foreach ($pages as $pg) {
        $title = (string) ($pg['title'] ?? '');
        $file = str_replace('_', ' ', preg_replace('/^File:/i', '', $title));
        if ($file === '' || empty($pg['imageinfo'][0])) continue;
        $ii = $pg['imageinfo'][0];
        if (!in_array(($ii['mime'] ?? ''), array('image/jpeg', 'image/png', 'image/webp'), true)) continue;
        if ((int) ($ii['width'] ?? 0) < 1000) continue;
        if (preg_match('/\b(map|mapa|locator|location|ubicaci[oó]n|escudo|coat|flag|bandera|logo|seal|sello|plano|diagram)\b/i', $file)) continue;
        if (isset($used[mb_strtolower($file)])) continue;
        $m = $ii['extmetadata'] ?? array();
        $hay = ruta_foto_auto_norm($file . ' ' . ($m['Categories']['value'] ?? '') . ' ' . wp_strip_all_tags((string) ($m['ImageDescription']['value'] ?? '')));
        if (strpos($hay, $token) === false || strpos($hay, 'salvador') === false) continue;
        $lic = trim((string) ($m['LicenseShortName']['value'] ?? ''));
        if (!preg_match('/^(CC BY(-SA)? [0-9.]+|CC0.*|Public domain.*|PD.*)$/i', $lic)) continue;
        if (!empty($m['NonFree']['value'])) continue;
        $author = trim(html_entity_decode(wp_strip_all_tags((string) ($m['Artist']['value'] ?? '')), ENT_QUOTES, 'UTF-8'));
        $author = preg_replace('/\s+/', ' ', $author);
        if ($author === '') $author = 'Autor no identificado en la ficha de Commons';
        if (mb_strlen($author) > 110) $author = mb_substr($author, 0, 107) . '…';
        if (preg_match('/^(Public domain|PD)/i', $lic)) $lic = 'Dominio público';
        return array(
            'file' => $file, 'author' => $author, 'license' => $lic,
            'license_url' => esc_url_raw((string) ($m['LicenseUrl']['value'] ?? '')),
            'source' => 'https://commons.wikimedia.org/wiki/File:' . rawurlencode(str_replace(' ', '_', $file)),
            'wiki' => 'commons:' . $phrase, 'checked' => time(),
        );
    }
    return array();
}

/**
 * Intenta resolver un destino. Devuelve array(info) si hay foto válida, array() si no la hay,
 * o null si hubo un fallo de red (para reintentar más tarde sin marcarlo como fallido).
 */
function ruta_foto_auto_resolver($slug) {
    $map = ruta_foto_auto_titulos();
    if (empty($map[$slug])) return array();
    $used = ruta_foto_auto_archivos_usados($slug);

    foreach ($map[$slug] as $cand) {
        $lang = 'es';
        if (strpos($cand, 'en:') === 0) { $lang = 'en'; $cand = substr($cand, 3); }
        $api = add_query_arg(array(
            'action' => 'query', 'format' => 'json', 'redirects' => 1, 'titles' => $cand,
            'prop' => 'pageimages|pageprops|extracts', 'piprop' => 'name', 'ppprop' => 'disambiguation',
            'exintro' => 1, 'explaintext' => 1, 'exchars' => 1200,
        ), 'https://' . $lang . '.wikipedia.org/w/api.php');
        $j = ruta_foto_auto_http($api);
        if ($j === null) return null;
        if (empty($j['query']['pages'])) continue;
        $page = reset($j['query']['pages']);
        if (isset($page['missing']) || isset($page['pageprops']['disambiguation'])) continue;
        $extract = (string) ($page['extract'] ?? '');
        if (stripos($extract, 'Salvador') === false) continue;   // evita homónimos de otros países
        $file = (string) ($page['pageimage'] ?? '');
        if ($file === '') continue;
        $file = str_replace('_', ' ', $file);
        if (!preg_match('/\.(jpe?g|png|webp)$/i', $file)) continue;
        if (preg_match('/\b(map|mapa|locator|location|ubicaci[oó]n|escudo|coat|flag|bandera|logo|seal|sello)\b/i', $file)) continue;
        if (isset($used[mb_strtolower($file)])) continue;        // ya asignada a otro destino

        $ci = add_query_arg(array(
            'action' => 'query', 'format' => 'json', 'titles' => 'File:' . $file,
            'prop' => 'imageinfo', 'iiprop' => 'extmetadata|size',
        ), 'https://commons.wikimedia.org/w/api.php');
        $c = ruta_foto_auto_http($ci);
        if ($c === null) return null;
        if (empty($c['query']['pages'])) continue;
        $cp = reset($c['query']['pages']);
        if (isset($cp['missing']) || empty($cp['imageinfo'][0])) continue;   // imagen local de Wikipedia, no de Commons
        $ii = $cp['imageinfo'][0];
        if ((int) ($ii['width'] ?? 0) < 800) continue;
        $m = $ii['extmetadata'] ?? array();
        $lic = trim((string) ($m['LicenseShortName']['value'] ?? ''));
        if (!preg_match('/^(CC BY(-SA)? [0-9.]+|CC0.*|Public domain.*|PD.*)$/i', $lic)) continue;
        if (!empty($m['NonFree']['value'])) continue;
        $author = trim(html_entity_decode(wp_strip_all_tags((string) ($m['Artist']['value'] ?? '')), ENT_QUOTES, 'UTF-8'));
        $author = preg_replace('/\s+/', ' ', $author);
        if ($author === '') $author = 'Autor no identificado en la ficha de Commons';
        if (mb_strlen($author) > 110) $author = mb_substr($author, 0, 107) . '…';
        $lic_url = esc_url_raw((string) ($m['LicenseUrl']['value'] ?? ''));
        if (preg_match('/^(Public domain|PD)/i', $lic)) $lic = 'Dominio público';

        return array(
            'file' => $file,
            'author' => $author,
            'license' => $lic,
            'license_url' => $lic_url,
            'source' => 'https://commons.wikimedia.org/wiki/File:' . rawurlencode(str_replace(' ', '_', $file)),
            'wiki' => $lang . ':' . $cand,
            'checked' => time(),
        );
    }
    // Segunda vía: búsqueda directa en Commons con las mismas reglas de validación.
    return ruta_foto_auto_buscar_commons($slug, $used);
}

/** Procesa hasta $max destinos pendientes. Devuelve el número de destinos aún pendientes. */
function ruta_foto_auto_procesar($max = 5, $reintentar = false) {
    $all = get_option('ruta_fotos_auto', array());
    if (!is_array($all)) $all = array();
    $catalog = ruta_fotografias_catalogo();
    $done = 0; $network_fail = false;

    foreach (array_keys(ruta_foto_auto_titulos()) as $slug) {
        if (isset($catalog[$slug])) continue;                                  // ya tiene foto manual
        if (!empty($all[$slug]['file'])) continue;                             // ya resuelto
        if (!empty($all[$slug]['fail']) && !$reintentar && (time() - (int) $all[$slug]['fail']) < 7 * DAY_IN_SECONDS) continue;
        if ($done >= $max) break;
        $r = ruta_foto_auto_resolver($slug);
        if ($r === null) { $network_fail = true; break; }
        $all[$slug] = $r ? $r : array('fail' => time());
        update_option('ruta_fotos_auto', $all, false);
        $done++;
    }
    update_option('ruta_fotos_auto_red', $network_fail ? time() : 0, false);

    $pending = 0;
    foreach (array_keys(ruta_foto_auto_titulos()) as $slug) {
        if (isset($catalog[$slug]) || !empty($all[$slug]['file'])) continue;
        if (!empty($all[$slug]['fail']) && (time() - (int) $all[$slug]['fail']) < 7 * DAY_IN_SECONDS) continue;
        $pending++;
    }
    return $network_fail ? max(1, $pending) : $pending;
}

/* ---------- WP-Cron: procesa por lotes pequeños en segundo plano ---------- */
add_action('ruta_fotos_auto_cron', function () {
    $pend = ruta_foto_auto_procesar(5);
    if ($pend > 0) {
        $delay = get_option('ruta_fotos_auto_red') ? HOUR_IN_SECONDS : 60;
        if (!wp_next_scheduled('ruta_fotos_auto_cron')) wp_schedule_single_event(time() + $delay, 'ruta_fotos_auto_cron');
    }
});
function ruta_foto_auto_programar() {
    if (!wp_next_scheduled('ruta_fotos_auto_cron')) wp_schedule_single_event(time() + 30, 'ruta_fotos_auto_cron');
}
add_action('admin_init', function () {
    if (get_option('ruta_fotos_auto_v') !== RUTA_V && current_user_can('manage_options')) {
        ruta_foto_auto_programar();
        update_option('ruta_fotos_auto_v', RUTA_V, false);
    }
});

/* ---------- Botón y resumen en Lugares turísticos > Fotografías ---------- */
add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=lugar', 'Fotografías', 'Fotografías', 'manage_options', 'ruta-fotografias', 'ruta_admin_fotografias');
});
function ruta_admin_fotografias() {
    if (!current_user_can('manage_options')) return;
    $msg = '';
    if (!empty($_POST['ruta_fotos_accion']) && check_admin_referer('ruta_fotos_accion')) {
        $accion = sanitize_key($_POST['ruta_fotos_accion']);
        if ($accion === 'reiniciar') { delete_option('ruta_fotos_auto'); }
        $pend = ruta_foto_auto_procesar(8, $accion === 'reintentar' || $accion === 'reiniciar');
        if ($pend > 0) ruta_foto_auto_programar();
        $msg = $pend > 0 ? 'Se procesó un lote. Quedan ' . $pend . ' destinos por revisar; continuarán solos en segundo plano.' : 'Revisión terminada.';
        if (get_option('ruta_fotos_auto_red')) $msg .= ' Atención: el servidor no pudo conectar con Wikimedia (revisa que permita conexiones salientes).';
    }
    $all = get_option('ruta_fotos_auto', array()); if (!is_array($all)) $all = array();
    $catalog = ruta_fotografias_catalogo();
    $lugares = array(); foreach (ruta_lugares() as $L) $lugares[$L[0]] = $L[1];
    echo '<div class="wrap"><h1>Fotografías · Ruta Cuscatlán</h1>';
    if ($msg) echo '<div class="notice notice-info"><p>' . esc_html($msg) . '</p></div>';
    echo '<p>Los destinos sin foto en el catálogo manual se buscan automáticamente en Wikipedia y Wikimedia Commons. Solo se usan imágenes con licencia libre, y cada tarjeta muestra autor, licencia y enlace a la fuente. Una foto destacada que asignes en WordPress siempre tiene prioridad.</p>';
    echo '<form method="post" style="margin:12px 0">'; wp_nonce_field('ruta_fotos_accion');
    echo '<button class="button button-primary" name="ruta_fotos_accion" value="buscar">Buscar fotografías pendientes</button> ';
    echo '<button class="button" name="ruta_fotos_accion" value="reintentar">Reintentar los que no tuvieron resultado</button> ';
    echo '<button class="button" name="ruta_fotos_accion" value="reiniciar" onclick="return confirm(\'Se borrarán los resultados automáticos guardados y se volverá a buscar. ¿Continuar?\')">Reiniciar búsqueda</button></form>';
    echo '<table class="widefat striped" style="max-width:1100px"><thead><tr><th>Destino</th><th>Estado</th><th>Autor</th><th>Licencia</th></tr></thead><tbody>';
    foreach ($lugares as $slug => $nombre) {
        if (isset($catalog[$slug])) { $st = 'Catálogo manual'; $au = $catalog[$slug]['author']; $li = $catalog[$slug]['license']; }
        elseif (!empty($all[$slug]['file'])) { $st = 'Automática'; $au = $all[$slug]['author']; $li = $all[$slug]['license']; }
        elseif (!empty($all[$slug]['fail'])) { $st = 'Sin foto libre encontrada'; $au = '—'; $li = '—'; }
        elseif (isset(ruta_foto_auto_titulos()[$slug])) { $st = 'Pendiente de revisar'; $au = '—'; $li = '—'; }
        else { $st = 'Asigna una foto destacada'; $au = '—'; $li = '—'; }
        echo '<tr><td>' . esc_html($nombre) . '</td><td>' . esc_html($st) . '</td><td>' . esc_html($au) . '</td><td>' . esc_html($li) . '</td></tr>';
    }
    echo '</tbody></table></div>';
}
