<?php
if (!defined('ABSPATH')) { exit; }

/** Fotografías específicas de destinos con atribución visible.
 * Prioridad: foto destacada del lugar, imagen heredada o foto Commons exacta por destino.
 * Nunca reutiliza una imagen genérica de categoría ni la foto de otro lugar.
 */
function ruta_fotografias_catalogo() {
    return array(
        'el-pital' => array('file'=>'Cerro El Pital, Chalatenango.JPG','author'=>'ElmerGuevara','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:Cerro_El_Pital,_Chalatenango.JPG','caption'=>'Fotografía del Cerro El Pital.'),
        'volcan-santa-ana' => array('file'=>'Volcán Santa Ana o Ilamatepec en el SV.jpg','author'=>'Cristian Chiquillo','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Volc%C3%A1n_Santa_Ana_o_Ilamatepec_en_el_SV.jpg','caption'=>'Fotografía del volcán Santa Ana o Ilamatepec.'),
        'volcan-izalco' => array('file'=>'Volcán de Izalco, Departamento de Sonsonate, El Salvador.jpg','author'=>'Rafael Montes E','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Volc%C3%A1n_de_Izalco,_Departamento_de_Sonsonate,_El_Salvador.jpg','caption'=>'Fotografía del volcán de Izalco.'),
        'cerro-verde' => array('file'=>'CERRO VERDE NATIONAL PARK, EL SALVADOR.jpg','author'=>'Jerrye & Roy Klotz, MD','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:CERRO_VERDE_NATIONAL_PARK,_EL_SALVADOR.jpg','caption'=>'Vista del Parque Nacional Cerro Verde y su entorno volcánico.'),
        'boqueron' => array('file'=>'EL BOQUERON.JPG','author'=>'Marcella Cuellar','license'=>'Dominio público','source'=>'https://commons.wikimedia.org/wiki/File:EL_BOQUERON.JPG','caption'=>'Vista panorámica del Parque Nacional El Boquerón.'),
        'volcan-san-miguel' => array('file'=>'Chaparrastique.jpg','author'=>'Lee Siebert (Smithsonian Institution)','license'=>'Dominio público','source'=>'https://commons.wikimedia.org/wiki/File:Chaparrastique.jpg','caption'=>'Campo de lava histórico en la ladera del volcán Chaparrastique.'),
        'conchagua' => array('file'=>'Volcan Conchagua.jpg','author'=>'Marcelo Alberto Miranda','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Volcan_Conchagua.jpg','caption'=>'Vista del volcán Conchagua desde la zona costera.'),
        'chinchontepec' => array('file'=>'Volcan de San Vicente (El Salvador).jpg','author'=>'Kevin Paniagua','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Volcan_de_San_Vicente_(El_Salvador).jpg','caption'=>'Volcán de San Vicente visto desde la carretera Interamericana.'),
        'montecristo' => array('file'=>'Bosque interior Parque Nacional Montecristo 01.JPG','author'=>'ElmerGuevara','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:Bosque_interior_Parque_Nacional_Montecristo_01.JPG','caption'=>'Bosque interior del Parque Nacional Montecristo.'),
        'laguna-verde' => array('file'=>'Laguna Verde de Apaneca.JPG','author'=>'ElmerGuevara','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Laguna_Verde_de_Apaneca.JPG','caption'=>'Fotografía de Laguna Verde, en Apaneca.'),
        'guija' => array('file'=>'Güija El Salvador.jpg','author'=>'JMRAFFi','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:G%C3%BCija_El_Salvador.jpg','caption'=>'Fotografía del lago de Güija.'),
        'coatepeque' => array('file'=>'Lago de Coatepeque-El Salvador.JPG','author'=>'ElmerGuevara','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:Lago_de_Coatepeque-El_Salvador.JPG','caption'=>'Fotografía del lago de Coatepeque.'),
        'ilopango' => array('file'=>'Lago de Ilopango.jpg','author'=>'JMRAFFi','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Lago_de_Ilopango.jpg','caption'=>'Fotografía del lago de Ilopango.'),
        'suchitlan' => array('file'=>'Lago Suchitlán (El Salvador).jpg','author'=>'Paolina1010','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Lago_Suchitl%C3%A1n_(El_Salvador).jpg','caption'=>'Fotografía del lago de Suchitlán.'),
        'el-tunco' => array('file'=>'El tunco.png','author'=>'Rebevon11','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:El_tunco.png','caption'=>'Fotografía de playa El Tunco.'),
        'el-zonte' => array('file'=>'El Zonte, Playa (01.2011) - panoramio.jpg','author'=>'Martin Haeusler','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:El_Zonte,_Playa_(01.2011)_-_panoramio.jpg','caption'=>'Fotografía de playa El Zonte.'),
        'el-cuco' => array('file'=>'El Cuco beach, El Salvador.JPG','author'=>'Autor no identificado; archivo cargado por Matteo747','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:El_Cuco_beach,_El_Salvador.JPG','caption'=>'Fotografía de playa El Cuco.'),
        'el-sunzal' => array('file'=>'Elsunzal.jpg','author'=>'AlxSky','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Elsunzal.jpg','caption'=>'Fotografía de playa El Sunzal.'),
        'salinitas' => array('file'=>'Playa Salinitas El Salvador.jpg','author'=>'Jenpq123','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Playa_Salinitas_El_Salvador.jpg','caption'=>'Fotografía de playa Salinitas.'),
        'puerto-la-libertad' => array('file'=>'PuertoLaLibertad.jpg','author'=>'Ayaita','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:PuertoLaLibertad.jpg','caption'=>'Fotografía del puerto de La Libertad.'),
        'los-cobanos' => array('file'=>'Playa Los Cóbanos 2026.jpg','author'=>'Bubulina26','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Playa_Los_C%C3%B3banos_2026.jpg','caption'=>'Fotografía de playa Los Cóbanos.'),
        'santa-ana' => array('file'=>'Catedral de la ciudad de Santa Ana.JPG','author'=>'Lacalderonjr','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:Catedral_de_la_ciudad_de_Santa_Ana.JPG','caption'=>'Fotografía de la catedral de la ciudad de Santa Ana.'),
        'centro-san-salvador' => array('file'=>'Centro Histórico de San Salvador, El Salvador.JPG','author'=>'Ruth Preza','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:Centro_Hist%C3%B3rico_de_San_Salvador,_El_Salvador.JPG','caption'=>'Fotografía del Centro Histórico de San Salvador.'),
        'juayua' => array('file'=>'Vista de Juayua - panoramio.jpg','author'=>'Roberto Alas','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:Vista_de_Juayua_-_panoramio.jpg','caption'=>'Vista de Juayúa, Sonsonate.'),
        'suchitoto' => array('file'=>'Suchitoto, El Salvador - panoramio (61).jpg','author'=>'PABLO NUÑEZ','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:Suchitoto,_El_Salvador_-_panoramio_(61).jpg','caption'=>'Fotografía de Suchitoto.'),
        'ataco' => array('file'=>'Concepción de Ataco, El Salvador - panoramio.jpg','author'=>'davidjoviera','license'=>'CC BY 3.0','source'=>'https://commons.wikimedia.org/wiki/File:Concepci%C3%B3n_de_Ataco,_El_Salvador_-_panoramio.jpg','caption'=>'Fotografía de Concepción de Ataco.'),
        'perquin' => array('file'=>'Parque de Perquín.jpg','author'=>'Tatiana Caledonio','license'=>'Dominio público','source'=>'https://commons.wikimedia.org/wiki/File:Parque_de_Perqu%C3%ADn.jpg','caption'=>'Parque central de Perquín.'),
        'ruta-flores' => array('file'=>'Mirador Turístico de Apaneca.jpg','author'=>'Alex Romero','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:Mirador_Tur%C3%ADstico_de_Apaneca.jpg','caption'=>'Mirador turístico de Apaneca, en la Ruta de las Flores.'),
        'joya-de-ceren' => array('file'=>'Parque Arqueológico Joya de Cerén - 55384174892.jpg','author'=>'xiquinhosilva','license'=>'CC BY 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Parque_Arqueol%C3%B3gico_Joya_de_Cer%C3%A9n_-_55384174892.jpg','caption'=>'Fotografía del parque arqueológico Joya de Cerén.'),
        'cihuatan' => array('file'=>'Piramide Cihuatan.jpg','author'=>'ElmerGuevara','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Piramide_Cihuatan.jpg','caption'=>'Pirámide principal del sitio arqueológico de Cihuatán.'),
        'tazumal' => array('file'=>'El Tazumal, Chalchuapa.jpg','author'=>'ARamirez69','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:El_Tazumal,_Chalchuapa.jpg','caption'=>'Fotografía del sitio arqueológico Tazumal.'),
        'cascada-tercios' => array('file'=>'Panorama Cascada los Tercios.jpg','author'=>'ElmerGuevara','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:Panorama_Cascada_los_Tercios.jpg','caption'=>'Fotografía de la cascada Los Tercios.'),
        'los-chorros' => array('file'=>'LOS CHORROS NATIONAL PARK, EL SALVADOR.jpg','author'=>'Jerrye & Roy Klotz, MD','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:LOS_CHORROS_NATIONAL_PARK,_EL_SALVADOR.jpg','caption'=>'Fotografía del parque Los Chorros.'),
        'malacatiupan' => array('file'=>'El Salto de Malacatiupan.jpg','author'=>'JMRAFFi','license'=>'CC BY-SA 4.0','source'=>'https://commons.wikimedia.org/wiki/File:El_Salto_de_Malacatiupan.jpg','caption'=>'Fotografía del salto de Malacatiupán.'),
        'el-imposible' => array('file'=>'Vista del Bosque Nacional El Imposible desde Tacuba El Salvador.jpg','author'=>'Pipil El Salvador','license'=>'CC BY 4.0','source'=>'https://commons.wikimedia.org/wiki/File:Vista_del_Bosque_Nacional_El_Imposible_desde_Tacuba_El_Salvador.jpg','caption'=>'Vista del bosque del Parque Nacional El Imposible desde Tacuba.'),
        'teatro-nacional-san-salvador' => array('file'=>'Teatro Nacional de San Salvador.JPG','author'=>'Cesarlainez','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:Teatro_Nacional_de_San_Salvador.JPG','caption'=>'Fachada del Teatro Nacional de San Salvador.'),
        'san-andres' => array('file'=>'San Andrés El Salvador 2.jpg','author'=>'Simon Burchell','license'=>'CC BY-SA 3.0','source'=>'https://commons.wikimedia.org/wiki/File:San_Andr%C3%A9s_El_Salvador_2.jpg','caption'=>'Pirámide del Parque Arqueológico San Andrés.'),
    );
}

/** Resuelve variaciones de slug generadas por títulos con artículos o duplicados antiguos. */
function ruta_foto_slug_canonico($post_id) {
    $slug = sanitize_title((string)get_post_field('post_name', $post_id));
    $title_slug = sanitize_title((string)get_the_title($post_id));
    $aliases = array(
        'cerro-el-pital' => 'el-pital',
        'volcan-de-santa-ana-ilamatepec' => 'volcan-santa-ana',
        'volcan-de-izalco' => 'volcan-izalco',
        'parque-natural-cerro-verde' => 'cerro-verde',
        'volcan-de-san-salvador-y-parque-el-boqueron' => 'boqueron',
        'volcan-de-san-miguel-chaparrastique' => 'volcan-san-miguel',
        'volcan-de-conchagua' => 'conchagua',
        'volcan-de-san-vicente-chinchontepec' => 'chinchontepec',
        'playa-salinitas' => 'salinitas',
        'parque-nacional-montecristo-el-trifinio' => 'montecristo',
        'lago-de-guija' => 'guija',
        'playa-el-tunco' => 'el-tunco',
        'playa-el-zonte' => 'el-zonte',
        'playa-el-sunzal' => 'el-sunzal',
        'puerto-de-la-libertad' => 'puerto-la-libertad',
        'playa-los-cobanos' => 'los-cobanos',
        'ciudad-de-santa-ana' => 'santa-ana',
        'centro-historico-de-san-salvador' => 'centro-san-salvador',
        'concepcion-de-ataco' => 'ataco',
        'parque-arqueologico-joya-de-ceren' => 'joya-de-ceren',
        'cascada-los-tercios' => 'cascada-tercios',
        'salto-de-malacatiupan' => 'malacatiupan',
        'parque-nacional-el-imposible' => 'el-imposible',
    );
    if (isset($aliases[$slug])) return $aliases[$slug];
    if (isset($aliases[$title_slug])) return $aliases[$title_slug];
    if (preg_match('/^(.*)-[0-9]+$/', $slug, $m) && (isset(ruta_fotografias_catalogo()[$m[1]]) || (function_exists('ruta_foto_auto_titulos') && isset(ruta_foto_auto_titulos()[$m[1]])))) return $m[1];
    return $slug;
}

function ruta_foto_lugar_fuente_repetida($post_id, $src) {
    static $owners = array();
    $key = sha1((string)$src);
    if (isset($owners[$key]) && (int)$owners[$key] !== (int)$post_id) return true;
    $owners[$key] = (int)$post_id;
    return false;
}

function ruta_foto_lugar_info($post_id) {
    $post_id = absint($post_id);
    if (!$post_id) return array();

    if (has_post_thumbnail($post_id)) {
        $src = get_the_post_thumbnail_url($post_id, 'large');
        if ($src && !ruta_foto_lugar_fuente_repetida($post_id, $src)) {
            return array('src'=>$src,'kind'=>'site','caption'=>'Imagen asignada al destino en WordPress.');
        }
    }
    $legacy = trim((string)get_post_meta($post_id, '_rc_legacy_image', true));
    if ($legacy && wp_http_validate_url($legacy) && !ruta_foto_lugar_fuente_repetida($post_id, $legacy)) {
        return array('src'=>$legacy,'kind'=>'site','caption'=>'Imagen configurada para este destino.');
    }

    $catalog = ruta_fotografias_catalogo();
    $slug = ruta_foto_slug_canonico($post_id);
    if (isset($catalog[$slug]) && !empty($catalog[$slug]['source'])) {
        $item = $catalog[$slug];
        $item['src'] = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($item['file']) . '?width=1400';
        if (ruta_foto_lugar_fuente_repetida($post_id, $item['src'])) return array();
        $item['kind'] = 'exact';
        $item['caption'] = $item['caption'] ?? 'Fotografía del destino.';
        return $item;
    }

    // Fotografía automática verificada (Wikipedia/Commons, licencia libre) guardada por fotos-auto.php.
    if (function_exists('ruta_foto_auto_get')) {
        $auto = ruta_foto_auto_get($slug);
        if ($auto) {
            $auto['src'] = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($auto['file']) . '?width=1400';
            if (ruta_foto_lugar_fuente_repetida($post_id, $auto['src'])) return array();
            $auto['kind'] = 'exact';
            $auto['caption'] = 'Fotografía de ' . get_the_title($post_id) . '.';
            return $auto;
        }
    }

    // No usar fotos genéricas por categoría ni repetir una imagen asignada a otro destino.
    return array();
}

function ruta_foto_lugar_sin_foto_markup($post_id) {
    $category = function_exists('ruta_cat_de') ? ruta_cat_de($post_id) : '';
    $cats = function_exists('ruta_cats') ? ruta_cats() : array();
    $icon = isset($cats[$category][1]) ? $cats[$category][1] : '📍';
    $title = get_the_title($post_id);
    return '<div class="ruta-place-photo-missing cat-'.esc_attr($category).'" role="img" aria-label="Fotografía específica pendiente para '.esc_attr($title).'">'
        .'<span aria-hidden="true">'.$icon.'</span><strong>Fotografía específica pendiente</strong>'
        .'<small>Aún no hay una imagen propia verificada para este destino.</small></div>';
}

function ruta_foto_credito_html($info) {
    if (empty($info['src'])) return '';
    if (($info['kind'] ?? '') === 'site') {
        return '<figcaption class="ruta-photo-credit">'.esc_html($info['caption'] ?? 'Imagen del sitio').'</figcaption>';
    }
    $caption = esc_html($info['caption'] ?? 'Fotografía');
    $changes = '<span>Presentación: redimensionamiento web y ajuste de encuadre.</span>';
    $author = esc_html($info['author'] ?? 'Autor');
    $license_raw = (string)($info['license'] ?? 'Licencia Creative Commons');
    $license = esc_html($license_raw);
    $license_urls = array(
        'CC BY-SA 3.0' => 'https://creativecommons.org/licenses/by-sa/3.0/',
        'CC BY-SA 4.0' => 'https://creativecommons.org/licenses/by-sa/4.0/',
        'CC BY-SA 2.0' => 'https://creativecommons.org/licenses/by-sa/2.0/',
        'CC BY 3.0' => 'https://creativecommons.org/licenses/by/3.0/',
        'CC BY 4.0' => 'https://creativecommons.org/licenses/by/4.0/',
        'Dominio público' => 'https://commons.wikimedia.org/wiki/Commons:Copyright_tags#pd',
    );
    $license_url = esc_url(!empty($info['license_url']) ? $info['license_url'] : ($license_urls[$license_raw] ?? 'https://creativecommons.org/'));
    $source = esc_url($info['source'] ?? 'https://commons.wikimedia.org/');
    return '<figcaption class="ruta-photo-credit">'.$caption.' <span>Foto: '.$author.' · <a href="'.$license_url.'" target="_blank" rel="license noopener noreferrer">'.$license.'</a></span> '.$changes.' <a href="'.$source.'" target="_blank" rel="noopener noreferrer">Fuente original</a></figcaption>';
}

function ruta_foto_lugar_markup($post_id, $context = 'card') {
    $info = ruta_foto_lugar_info($post_id);
    if (empty($info['src'])) return '';
    $title = get_the_title($post_id);
    $loading = ($context === 'hero') ? 'eager' : 'lazy';
    $class = 'ruta-photo ruta-photo-' . sanitize_html_class($context) . ' ruta-photo-' . sanitize_html_class($info['kind'] ?? 'site');
    return '<figure class="'.$class.'"><div class="ruta-photo-frame"><img src="'.esc_url($info['src']).'" alt="'.esc_attr('Fotografía de '.$title).'" loading="'.$loading.'" decoding="async"></div>'.ruta_foto_credito_html($info).'</figure>';
}

/**
 * Muestra únicamente una fotografía asignada al establecimiento.
 * No reutiliza la foto del destino: varios hoteles del mismo destino no deben
 * terminar con la misma imagen contextual como si perteneciera a cada negocio.
 */
function ruta_foto_hosp_markup($post_id, $hotel_name) {
    $specific = function_exists('ruta_hosp_img') ? ruta_hosp_img($post_id, $hotel_name) : '';
    if (!$specific) return '';

    $caption = 'Fotografía propia asignada a este hospedaje.';
    return '<figure class="ruta-photo ruta-photo-hotel ruta-photo-hotel-specific"><div class="ruta-photo-frame"><img src="'.esc_url($specific).'" alt="'.esc_attr('Fotografía de '.$hotel_name).'" loading="lazy" decoding="async"></div><figcaption class="ruta-photo-credit">'.esc_html($caption).'</figcaption></figure>';
}

/** Marcador visual honesto mientras no exista una fotografía propia autorizada. */
function ruta_hosp_sin_foto_markup($hotel_name) {
    return '<div class="ruta-hosp-photo-missing" role="img" aria-label="Fotografía propia pendiente para '.esc_attr($hotel_name).'">'
        .'<span class="ruta-hosp-photo-icon" aria-hidden="true">⌂</span>'
        .'<strong>Fotografía del hospedaje pendiente</strong>'
        .'<small>Aún no hay una imagen propia autorizada para este establecimiento.</small>'
        .'</div>';
}
