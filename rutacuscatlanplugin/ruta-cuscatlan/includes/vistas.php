<?php
if(!defined('ABSPATH'))exit;
add_action('wp_enqueue_scripts',function(){wp_enqueue_style('ruta',RUTA_URL.'assets/ruta.css',[],RUTA_V);wp_enqueue_script('ruta',RUTA_URL.'assets/ruta.js',[],RUTA_V,true);});
add_filter('do_shortcode_tag',fn($o,$t)=>strpos($t,'ruta_')===0?'<div class="ruta">'.$o.'</div>':$o,10,2);
function ruta_aviso(){$k=sanitize_key($_GET['ruta_msg']??'');$m=['ok'=>['good','¡Gracias! Recibimos tu sugerencia y la revisaremos antes de publicarla.'],'err'=>['bad','Completa los campos obligatorios y escribe un correo válido.'],'nonce'=>['bad','El formulario expiró. Inténtalo de nuevo.'],'rapido'=>['bad','Ya enviaste una sugerencia hace un momento. Espera un minuto e inténtalo de nuevo.']];return isset($m[$k])?'<p class="msg '.$m[$k][0].'" role="status">'.esc_html($m[$k][1]).'</p>':'';}
function ruta_lugar_normalizar_titulo($title){
 $title=remove_accents(wp_strip_all_tags((string)$title));
 $title=strtolower($title);
 $title=preg_replace('/[^a-z0-9]+/',' ',$title);
 return trim(preg_replace('/\s+/',' ',$title));
}
function ruta_lugar_puntaje_completitud($p){
 $id=(int)$p->ID;$score=0;
 if(has_post_thumbnail($id))$score+=12;
 if(trim((string)get_post_meta($id,'_rc_legacy_image',true)))$score+=10;
 if(trim((string)$p->post_excerpt))$score+=3;
 if(trim((string)$p->post_content))$score+=2;
 foreach(['depto','muni','altura','dif','epoca','acts','tags','ref','dest'] as $key)if(trim((string)get_post_meta($id,'_rc_'.$key,true)))$score++;
 if(trim((string)get_post_meta($id,'_rc_hosp',true)))$score+=4;
 return $score;
}
function ruta_lugares_todos(){
 $posts=get_posts(['post_type'=>'lugar','post_status'=>'publish','numberposts'=>-1,'orderby'=>'title','order'=>'ASC']);
 $best=[];$hidden=0;
 foreach($posts as $p){
  $key=ruta_lugar_normalizar_titulo($p->post_title);if($key==='')$key='id-'.$p->ID;
  if(!isset($best[$key])){$best[$key]=$p;continue;}
  $hidden++;$old=$best[$key];
  if(ruta_lugar_puntaje_completitud($p)>ruta_lugar_puntaje_completitud($old))$best[$key]=$p;
 }
 $places=array_values($best);
 usort($places,function($a,$b){$cmp=strcasecmp(remove_accents($a->post_title),remove_accents($b->post_title));return $cmp!==0?$cmp:((int)$a->ID<=> (int)$b->ID);});
 $GLOBALS['ruta_lugares_duplicados_ocultos']=$hidden;
 return $places;
}
function ruta_lugares_duplicados_ocultos(){ruta_lugares_todos();return (int)($GLOBALS['ruta_lugares_duplicados_ocultos']??0);}
function ruta_card($p){$id=$p->ID;$c=ruta_cat_de($id);$cats=ruta_cats();$ci=$cats[$c]??['Lugar','📍',''];$h=ruta_estado_hosp($id);$hs=ruta_hosp($id);
 $nombres=implode(' ',array_map(fn($x)=>$x['n'],array_merge($hs['propios'],$hs['cerca'])));
 $busq=$p->post_title.' '.$ci[0].' '.$ci[2].' '.ruta_m($id,'depto').' '.ruta_m($id,'muni').' '.str_replace("\n",' ',ruta_m($id,'acts')).' '.ruta_m($id,'tags').' '.$nombres;
 $img=ruta_foto_lugar_markup($id,'card');
 if(!$img)$img=function_exists('ruta_foto_lugar_sin_foto_markup')?ruta_foto_lugar_sin_foto_markup($id):'<div class="ruta-place-photo-missing"><strong>Fotografía específica pendiente</strong></div>';
 $tag=$h==2?'<span class="tag ok">🛏️ Con hospedaje</span>':($h==1?'<span class="tag cerca">🛏️ Hospedaje cercano</span>':'<span class="tag no">Sin hospedaje registrado</span>');
 return '<article class="card" data-b="'.esc_attr($busq).'" data-c="'.esc_attr($c).'" data-d="'.esc_attr(ruta_m($id,'depto')).'" data-h="'.($h?1:0).'">'.$img.'<div class="cb"><h3><a href="'.esc_url(get_permalink($id)).'">'.esc_html($p->post_title).'</a></h3>'
 .'<p class="meta">'.esc_html($ci[0]).' · '.esc_html(ruta_m($id,'depto')).'</p><p>'.esc_html($p->post_excerpt).'</p><p>'.$tag.' '.ruta_badge_abierto($id).'</p><a class="btn" href="'.esc_url(get_permalink($id)).'">Ver detalles</a></div></article>';}
/* ---------- Portada ---------- */
add_shortcode('ruta_inicio',function(){$ls=ruta_lugares_todos();$cats=ruta_cats();$cnt=[];$conh=0;$deps=[];
 foreach($ls as $p){$c=ruta_cat_de($p->ID);$cnt[$c]=($cnt[$c]??0)+1;if(ruta_estado_hosp($p->ID))$conh++;$deps[ruta_m($p->ID,'depto')]=1;}
 $h='<section class="hero"><img class="logo" src="'.esc_url(RUTA_URL.'assets/logo.svg').'" alt="Ruta Cuscatlán — Descubre El Salvador"><h2>Encuentra a dónde ir y dónde quedarte</h2><p>Montañas, playas, lagos, parques acuáticos, pueblos, plazas, teatros y sitios arqueológicos de El Salvador, con sus opciones de hospedaje.</p>'
 .'<form class="buscar" method="get" action="'.esc_url(ruta_url('explorar')).'" role="search"><label class="sr" for="rq">Buscar lugares</label><input id="rq" name="ruta_q" placeholder="Prueba: montañas, playas con hospedaje, Suchitoto…"><button class="btn nar" type="submit">Buscar</button></form></section>'
 .'<div class="stats"><div><b>'.count($ls).'</b>Lugares</div><div><b>'.$conh.'</b>Con hospedaje</div><div><b>'.count($deps).'</b>Departamentos</div></div><h2>Explora por categoría</h2><div class="cats">';
 foreach($cats as $s=>$c)$h.='<a class="cat" href="'.esc_url(add_query_arg('ruta_cat',$s,ruta_url('explorar'))).'"><span>'.$c[1].'</span><b>'.esc_html($c[0]).'</b><small>'.(int)($cnt[$s]??0).' lugares</small></a>';
 $h.='</div><h2>Destacados</h2><div class="grid">';$n=0;foreach($ls as $p)if(ruta_m($p->ID,'dest')==='1'&&$n++<6)$h.=ruta_card($p);
 return $h.'</div><p><a class="btn" href="'.esc_url(ruta_url('explorar')).'">Ver todos los lugares</a> <a class="btn nar" href="'.esc_url(ruta_url('donde-hospedarse')).'">Alojamiento</a></p>';});
/* ---------- Página completa de inicio: [ruta_pagina] ---------- */
add_shortcode('ruta_pagina',function(){
 $pasos=[['🔎','Busca','Escribe un lugar, una categoría o "playas con hospedaje" y filtra por departamento.'],['📍','Elige','Cada ficha trae qué hacer, mejor época, dificultad y cómo llegar.'],['🛏️','Reserva','Mira hospedajes cerca de cada destino y confirma precios directamente con ellos.']];
 $h=do_shortcode('[ruta_inicio]').'<h2>Cómo funciona</h2><div class="pasos">';
 foreach($pasos as $i=>$p)$h.='<div class="paso"><span>'.$p[0].'</span><b>'.($i+1).'. '.esc_html($p[1]).'</b><p>'.esc_html($p[2]).'</p></div>';
 $h.='</div><div class="msg"><b>¿Conoces un lugar que falta?</b> Compártelo y lo revisamos antes de publicarlo.</div>'.do_shortcode('[ruta_sugerir]');
 return $h;});
/* ---------- Buscador ---------- */
add_shortcode('ruta_explorar',function(){$q=sanitize_text_field(wp_unslash($_GET['ruta_q']??''));$c=sanitize_key($_GET['ruta_cat']??'');$d=sanitize_text_field(wp_unslash($_GET['ruta_dep']??''));
 $ls=ruta_lugares_todos();$cats=ruta_cats();$cnt=[];$deps=[];foreach($ls as $p){$k=ruta_cat_de($p->ID);$cnt[$k]=($cnt[$k]??0)+1;if($x=ruta_m($p->ID,'depto'))$deps[$x]=1;}ksort($deps);
 $h='<div class="explorar" data-cat="'.esc_attr($c).'"><div class="filtros"><label>Buscar<input type="search" id="r-q" value="'.esc_attr($q).'" placeholder="Ej.: montañas, surf, cascada, Suchitoto"></label><label>Departamento<select id="r-d"><option value="">Todos</option>';
 foreach(array_keys($deps) as $x)$h.='<option'.selected($d,$x,false).'>'.esc_html($x).'</option>';
 $h.='</select></label><label class="chk"><input type="checkbox" id="r-h"> Solo con hospedaje</label><button type="button" class="btn lim" id="r-x">Limpiar filtros</button></div><div class="chips" role="group" aria-label="Categorías"><button type="button" class="chip" data-cat="">Todos ('.count($ls).')</button>';
 foreach($cats as $s=>$x)$h.='<button type="button" class="chip" data-cat="'.esc_attr($s).'">'.$x[1].' '.esc_html($x[0]).' ('.(int)($cnt[$s]??0).')</button>';
 $h.='</div><p id="r-n" class="count" aria-live="polite"></p><div class="grid" id="r-grid">';foreach($ls as $p)$h.=ruta_card($p);
 return $h.'</div><p id="r-0" class="msg bad" hidden>No encontramos lugares con esa búsqueda. Prueba otra palabra o <a href="'.esc_url(ruta_url('sugerir-lugar')).'">sugiere un lugar</a>.</p></div>';});
/* ---------- Hospedajes ---------- */
add_shortcode('ruta_hospedajes',function(){$map=[];$tipos=[];$deps=[];
 foreach(ruta_lugares_todos() as $p){foreach(ruta_hosp($p->ID)['propios'] as $x){$k=mb_strtolower($x['n']);if(!isset($map[$k]))$map[$k]=$x+['l'=>[]];$map[$k]['l'][]=$p;$tipos[$x['t']]=1;if($d=ruta_m($p->ID,'depto'))$deps[$d]=1;}}
 uasort($map,fn($a,$b)=>strcasecmp($a['n'],$b['n']));ksort($tipos);ksort($deps);
 $h='<div class="explorar hosp"><p>Hospedajes registrados cerca de cada destino. Los precios son referenciales: confirma disponibilidad y tarifas directamente con el establecimiento.</p><div class="filtros"><label>Buscar<input type="search" id="r-q" placeholder="Nombre o lugar"></label><label>Tipo<select id="r-t"><option value="">Todos</option>';
 foreach(array_keys($tipos) as $t)$h.='<option>'.esc_html($t).'</option>';$h.='</select></label><label>Departamento<select id="r-d"><option value="">Todos</option>';foreach(array_keys($deps) as $d)$h.='<option>'.esc_html($d).'</option>';
 $h.='</select></label></div><p id="r-n" class="count" aria-live="polite"></p><div class="grid" id="r-grid">';
 foreach($map as $x){$lug=implode(', ',array_map(fn($p)=>'<a href="'.esc_url(get_permalink($p)).'">'.esc_html($p->post_title).'</a>',$x['l']));$ds=implode(' ',array_unique(array_map(fn($p)=>ruta_m($p->ID,'depto'),$x['l'])));
  $h.='<article class="card" data-b="'.esc_attr($x['n'].' '.$x['t'].' '.$ds.' '.implode(' ',array_map(fn($p)=>$p->post_title,$x['l']))).'" data-h="1" data-t="'.esc_attr($x['t']).'" data-d="'.esc_attr(explode(' ',$ds)[0]===''?'':$ds).'"><div class="cb"><h3>'.esc_html($x['n']).'</h3><p><span class="tag ok">'.esc_html($x['t']).'</span></p>'
  .($x['p']?'<p><b>Precio referencial:</b> '.esc_html($x['p']).'</p>':'').($x['o']?'<p>'.esc_html($x['o']).'</p>':'').'<p class="meta">En / cerca de: '.$lug.'</p><a class="btn" target="_blank" rel="noopener" href="'.esc_url(ruta_maps($x['n'])).'">Ver en el mapa</a></div></article>';}
 return $h.'</div><p id="r-0" class="msg bad" hidden>No hay hospedajes con esos filtros.</p></div>';});
/* ---------- Ficha de cada lugar ---------- */
add_filter('the_content',function($c){if(!is_singular('lugar')||!in_the_loop()||!is_main_query())return $c;$id=get_the_ID();$cats=ruta_cats();$ci=$cats[ruta_cat_de($id)]??['Lugar','📍',''];
 $h='<div class="ruta"><nav class="migas" aria-label="Ruta de navegación"><a href="'.esc_url(ruta_url('descubre')).'">Inicio</a> › <a href="'.esc_url(ruta_url('explorar')).'">Explorar lugares</a> › '.esc_html(get_the_title($id)).'</nav><p class="meta"><span class="tag ok">'.$ci[1].' '.esc_html($ci[0]).'</span> <span class="tag cerca">'.esc_html(ruta_m($id,'depto')).'</span></p>';
 $h.='<table class="ficha">';foreach(['Departamento'=>'depto','Municipio'=>'muni','Altura'=>'altura','Dificultad'=>'dif','Mejor época'=>'epoca'] as $l=>$k)if(ruta_m($id,$k))$h.='<tr><th>'.$l.'</th><td>'.esc_html(ruta_m($id,$k)).'</td></tr>';$h.='</table>'.ruta_ficha_extra($id);
 if($a=array_filter(array_map('trim',explode("\n",ruta_m($id,'acts'))))){$h.='<h3>Qué hacer</h3><ul>';foreach($a as $x)$h.='<li>'.esc_html($x).'</li>';$h.='</ul>';}
 $mapq=ruta_map_query($id);$mapurl='https://www.google.com/maps?q='.rawurlencode($mapq).'&output=embed';
 $h.='<p><a class="btn" target="_blank" rel="noopener" href="'.esc_url(ruta_maps($mapq)).'">Ver en el mapa</a> <a class="btn nar" target="_blank" rel="noopener" href="'.esc_url(ruta_como_llegar($mapq)).'">Cómo llegar</a> <a class="btn wa" target="_blank" rel="noopener" href="'.esc_url('https://wa.me/?text='.rawurlencode(get_the_title($id).' - '.get_permalink($id))).'">Compartir por WhatsApp</a></p><section class="ruta-map"><h3>¿Dónde está este lugar?</h3><div class="ruta-map-frame"><iframe title="Mapa de '.esc_attr(get_the_title($id)).'" src="'.esc_url($mapurl).'" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div></section><h3>Alojamiento</h3>';$hs=ruta_hosp($id);$lista=$hs['propios']?:$hs['cerca'];
 if($lista){if(!$hs['propios']&&$hs['ref'])$h.='<p class="msg">Este lugar no tiene hospedaje en el sitio. Estas son opciones cercanas, en <a href="'.esc_url(get_permalink($hs['ref'])).'">'.esc_html($hs['ref']->post_title).'</a>.</p>';
  $h.='<div class="grid">';foreach($lista as $x){$h.='<article class="card"><div class="cb"><h4>'.esc_html($x['n']).'</h4><p><span class="tag ok">'.esc_html($x['t']).'</span></p>'.($x['p']?'<p><b>Precio referencial:</b> '.esc_html($x['p']).'</p>':'').($x['o']?'<p>'.esc_html($x['o']).'</p>':'').'<a class="btn" target="_blank" rel="noopener" href="'.esc_url(ruta_maps($x['n'].', '.ruta_m($id,'muni').', El Salvador')).'">Ver en el mapa</a></div></article>';}$h.='</div>';}
 else $h.='<p class="msg">Aún no tenemos hospedajes registrados para este lugar. Busca opciones en los pueblos o ciudades cercanas. ¿Conoces uno? <a href="'.esc_url(ruta_url('sugerir-lugar')).'">Compártelo</a>.</p>';
 return $c.$h.ruta_relacionados($id).'<p class="meta">Información referencial. Confirma precios, horarios y disponibilidad directamente con cada lugar antes de viajar.</p></div>';});
/* ---------- Más lugares del mismo departamento ---------- */
function ruta_relacionados($id){$d=ruta_m($id,'depto');if(!$d)return '';
 $r=array_values(array_filter(ruta_lugares_todos(),function($p)use($id,$d){return (int)$p->ID!==(int)$id && ruta_lugar_normalizar_titulo(ruta_m($p->ID,'depto'))===ruta_lugar_normalizar_titulo($d);}));$r=array_slice($r,0,3);if(!$r)return '';
 $h='<h3>Más lugares en '.esc_html($d).'</h3><div class="grid">';foreach($r as $p)$h.=ruta_card($p);return $h.'</div>';}
/* ---------- Sugerir un lugar ---------- */
add_shortcode('ruta_sugerir',function(){$o='';foreach(ruta_cats() as $s=>$c)$o.='<option value="'.esc_attr($s).'">'.esc_html($c[0]).'</option>';$d='';foreach(ruta_deptos() as $x)$d.='<option>'.esc_html($x).'</option>';
 return ruta_aviso().'<form class="form" method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="ruta_sugerir">'.wp_nonce_field('ruta_sug','_n',true,false).'<input class="hp" name="web" tabindex="-1" autocomplete="off" aria-hidden="true">'
 .'<label>Nombre del lugar *<input name="nombre" required></label><div class="dos"><label>Categoría *<select name="cat" required>'.$o.'</select></label><label>Departamento *<select name="depto" required>'.$d.'</select></label></div>'
 .'<label>¿Cómo es y qué se puede hacer? *<textarea name="desc" rows="4" required></textarea></label><label>Hospedajes que conozcas (opcional)<textarea name="hosp" rows="2" placeholder="Nombre del hotel o cabaña, y dónde está"></textarea></label>'
 .'<div class="dos"><label>Tu nombre *<input name="autor" required></label><label>Tu correo *<input type="email" name="correo" required></label></div><button class="btn nar" type="submit">Enviar sugerencia</button></form>';});
function ruta_volver($k){wp_safe_redirect(add_query_arg('ruta_msg',$k,remove_query_arg('ruta_msg',wp_get_referer()?:home_url())));exit;}
foreach(['admin_post_ruta_sugerir','admin_post_nopriv_ruta_sugerir'] as $h)add_action($h,function(){
 if(!wp_verify_nonce($_POST['_n']??'','ruta_sug'))ruta_volver('nonce');if(!empty($_POST['web']))ruta_volver('ok');
 $n=sanitize_text_field($_POST['nombre']??'');$c=sanitize_key($_POST['cat']??'');$d=sanitize_text_field($_POST['depto']??'');$t=sanitize_textarea_field($_POST['desc']??'');$ho=sanitize_textarea_field($_POST['hosp']??'');$a=sanitize_text_field($_POST['autor']??'');$m=sanitize_email($_POST['correo']??'');
 if(!$n||!$t||!$a||!is_email($m)||!isset(ruta_cats()[$c])||!in_array($d,ruta_deptos(),true))ruta_volver('err');
 $rl='rc_rl_'.md5($_SERVER['REMOTE_ADDR']??'x');if(get_transient($rl))ruta_volver('rapido');set_transient($rl,1,MINUTE_IN_SECONDS);
 $id=wp_insert_post(['post_type'=>'lugar','post_status'=>'pending','post_title'=>$n,'post_content'=>$t,'post_excerpt'=>wp_trim_words($t,28)]);
 ruta_sug_guardar(['post_id'=>(is_wp_error($id)?0:(int)$id),'nombre_lugar'=>$n,'categoria'=>$c,'departamento'=>$d,'descripcion'=>$t,'hospedajes'=>$ho,'nombre_autor'=>$a,'correo_autor'=>$m]);
 if($id&&!is_wp_error($id)){wp_set_object_terms($id,$c,'categoria_lugar');update_post_meta($id,'_rc_depto',$d);update_post_meta($id,'_rc_sug',"$a <$m>\nHospedajes: $ho");wp_mail(get_option('admin_email'),'Ruta Cuscatlán: nueva sugerencia',"$n ($d)\nDe: $a <$m>\n\n$t\n\nHospedajes: $ho\n\nRevisar: ".admin_url('post.php?post='.$id.'&action=edit'));}
 ruta_volver('ok');});

function ruta_shortcode_nosotros(){
 $asset=RUTA_URL.'assets/anexos/';
 $img=function($file,$alt,$caption='')use($asset){return '<figure class="rn-annex-figure"><a href="'.esc_url($asset.$file).'" target="_blank" rel="noopener"><img src="'.esc_url($asset.$file).'" alt="'.esc_attr($alt).'" loading="lazy" decoding="async"></a>'.($caption?'<figcaption>'.esc_html($caption).'</figcaption>':'').'</figure>';};
 $html='<section class="ruta-nosotros ruta-nosotros-documento">';
 $html.='<header class="rn-hero"><span class="ruta-eyebrow">RUTA CUSCATLÁN · DESCUBRE EL SALVADOR</span><h1>Encuentra a dónde ir y dónde quedarte, en un solo lugar</h1><p>Guía turística digital de El Salvador con buscador por categorías y hospedajes.</p><div class="rn-doc-byline">Elaborado por Guillermo Steven Rauda Bonilla · Instituto Nacional San Luis (INSAL) · Noviembre de 2026</div></header>';
 $html.='<section class="rn-section rn-document-section"><span class="rn-kicker">PRESENTACIÓN DEL PROYECTO</span><h2>Introducción</h2>';
 $html.='<p>El Salvador es un país pequeño con una variedad enorme de destinos: volcanes y cerros de clima frío, playas reconocidas por el surf, lagos de origen volcánico, pueblos coloniales, sitios arqueológicos y bosques con cascadas. En pocas horas de camino una persona puede pasar de la costa a la montaña, y esa cercanía convierte al país en un lugar ideal para escapadas de fin de semana, vacaciones y excursiones.</p>';
 $html.='<p>Sin embargo, quien quiere planear un viaje debe buscar la información en muchos lugares: redes sociales, blogs, noticias y portales de reservas. Rara vez encuentra en un solo sitio qué lugares existen en una categoría, cómo llegar, qué actividades hay y dónde dormir cerca. A esto se suma que muchos negocios turísticos pequeños, como cabañas de montaña y posadas familiares, casi no aparecen en línea y pierden la oportunidad de recibir visitantes.</p>';
 $html.='<p>Ruta Cuscatlán nace para resolver ese problema. Es un sitio web (con su plugin para WordPress) que reúne los lugares turísticos del país, los organiza por categoría y muestra en cada ficha si hay hospedaje y cuáles son las opciones. Si una persona busca «montañas», la plataforma muestra todas las montañas y volcanes registrados, con su ubicación, su dificultad y los hospedajes disponibles en la zona.</p>';
 $html.='<p>Este documento presenta la idea de negocio, sus objetivos, la misión y la visión, los valores y principios, y las políticas que guían el funcionamiento de la plataforma. Al final se incluyen anexos gráficos con el nombre y logotipo, la paleta de colores, la estructura de la página, los lugares incluidos y un ejemplo de ficha.</p></section>';
 $html.='<section class="rn-section rn-objectives"><span class="rn-kicker">PROPÓSITO Y METAS</span><h2>Objetivos</h2><div class="rn-objective-main"><h3>Objetivo general</h3><p>Crear una plataforma web de turismo que permita descubrir los lugares turísticos de El Salvador por categoría y conocer, en una sola ficha, los hospedajes disponibles en cada destino, para facilitar la planificación de viajes y apoyar a los negocios turísticos locales.</p></div><h3>Objetivos específicos</h3><ol class="rn-numbered"><li>Organizar los destinos turísticos en seis categorías (montañas y volcanes, playas, lagos y lagunas, pueblos y cultura, sitios arqueológicos, bosques y cascadas) con un buscador rápido.</li><li>Registrar en cada ficha los hospedajes del lugar o cercanos (hoteles, cabañas, hostales y campamentos), con su tipo, precio referencial y una nota útil.</li><li>Dar visibilidad a emprendedores y comunidades locales mediante un formulario de sugerencias, que se publican después de ser revisadas.</li></ol></section>';
 $html.='<section class="rn-section rn-document-section"><span class="rn-kicker">PROPUESTA DE VALOR</span><h2>Idea de negocio</h2>';
 $html.='<p>Ruta Cuscatlán es un directorio turístico digital que reúne en una sola plataforma los lugares para visitar en El Salvador y los hospedajes donde quedarse. Cada lugar tiene una ficha con su descripción, departamento, municipio, altura o dificultad cuando aplica, mejor época para visitarlo, actividades y mapa, y debajo de esa información aparece la sección «Alojamiento», con las opciones propias del sitio o las más cercanas.</p>';
 $html.='<p>La idea surge de un problema común: la información turística está dispersa y a veces desactualizada, los portales de reservas internacionales se concentran en hoteles y no explican qué hacer en cada destino, resulta difícil comparar lugares del mismo tipo y saber si tienen hospedaje, y muchos negocios pequeños casi no aparecen en línea. Para resolverlo, la plataforma ofrece un buscador por categoría y palabra, donde escribir «montañas», «playas» o «Suchitoto» muestra los lugares que corresponden y permite filtrar por departamento o por «solo con hospedaje», una ficha completa de cada destino con datos prácticos, actividades y enlace al mapa, y una página con todos los alojamientos registrados, con filtros por tipo y departamento.</p>';
 $html.='<p>Está pensada para turistas nacionales que buscan escapadas de fin de semana, salvadoreños en el exterior que visitan el país y quieren conocer nuevos destinos, turistas extranjeros que planean una ruta, y estudiantes, familias y grupos que organizan excursiones. Su propuesta de valor se resume en «Encuentra a dónde ir y dónde quedarte, en un solo lugar», y se diferencia de las redes sociales por ser ordenada y de las plataformas de reserva por mostrar primero el destino y después el hospedaje cercano.</p>';
 $html.='<p>En cuanto a los ingresos, la ficha básica de hospedaje es gratuita para que cualquier negocio pueda aparecer, y desde el inicio los hospedajes y tours que paguen una ficha destacada aparecen primero con la etiqueta «Destacado». Conforme la plataforma crezca se venderán espacios de publicidad local a restaurantes, tours y transporte turístico, y en el futuro se podrán cobrar comisiones por las reservas hechas desde la plataforma y vender rutas temáticas armadas con aliados locales. Los precios de cada plan se definirán después de validar la idea con los negocios turísticos.</p>';
 $html.='<p>Para el visitante el uso es sencillo: entra a la portada, busca por palabra o elige una categoría, ve las tarjetas de los lugares con su departamento y si tienen hospedaje, abre la ficha para revisar los datos y las actividades, y consulta los hospedajes con enlace al mapa. Si conoce un lugar que falta, lo sugiere en el formulario y el equipo lo revisa antes de publicarlo.</p></section>';
 $html.='<section class="rn-section rn-pillars-document"><span class="rn-kicker">IDENTIDAD DEL PROYECTO</span><h2>Misión y visión</h2><div class="rn-pillars"><article><span>✦</span><h3>Misión</h3><p>Ser una empresa que impulse el turismo en El Salvador mediante un sitio web que facilite el acceso a información clara, organizada y confiable sobre destinos turísticos y opciones de hospedaje. Buscamos conectar a los viajeros con las riquezas de nuestro país, brindar visibilidad a los negocios turísticos locales y generar oportunidades de crecimiento para emprendedores, cooperativas y comunidades, contribuyendo al desarrollo del sector turístico nacional.</p></article><article><span>◎</span><h3>Visión</h3><p>Como empresa, aspiramos a expandir nuestros servicios desde El Salvador hacia toda Centroamérica y, a largo plazo, hacia el continente americano, convirtiéndonos en un referente del sector turístico digital. Buscamos alcanzar este crecimiento mediante la innovación tecnológica, la incorporación de nuevos destinos turísticos y la creación de alianzas estratégicas con empresas y comunidades de cada región, con el propósito de conectar a los viajeros con la riqueza cultural y natural de cada lugar e impulsar el turismo a nivel internacional.</p></article></div></section>';
 $html.='<section class="rn-section"><span class="rn-kicker">IDENTIDAD Y CULTURA</span><h2>Valores</h2><div class="rn-values rn-values-three"><div><b>Respeto</b><p>Trato digno a las personas, las comunidades y el entorno natural.</p></div><div><b>Responsabilidad</b><p>Cumplir con lo que se publica y proteger los datos de los usuarios.</p></div><div><b>Rectitud</b><p>Actuar con honestidad e información verdadera, indicando cuándo un dato es referencial.</p></div></div></section>';
 $html.='<section class="rn-section"><span class="rn-kicker">FORMA DE TRABAJAR</span><h2>Principios</h2><div class="rn-values rn-principles"><div><b>Trabajo en equipo</b><p>Se construye en conjunto con aliados, emprendedores y usuarios.</p></div><div><b>Excelencia</b><p>Información completa, clara y bien presentada.</p></div><div><b>Disciplina</b><p>Revisar y actualizar la información de forma constante.</p></div><div><b>Espíritu de servicio</b><p>Ayudar al visitante a planear mejor su viaje.</p></div><div><b>Fraternidad</b><p>Unir a las comunidades y a los visitantes con trato cercano.</p></div><div><b>Emprendimiento</b><p>Impulsar a los negocios locales y la innovación turística.</p></div></div></section>';
 $html.='<section class="rn-section rn-policies"><span class="rn-kicker">REGLAS DE FUNCIONAMIENTO</span><h2>Políticas</h2><ol class="rn-policy-list">';
 $policies=array(
  array('Contenido y verificación','La información se redacta con fuentes públicas, como el portal oficial de turismo y medios nacionales. Los precios, horarios y disponibilidad son referenciales y deben confirmarse con el establecimiento. Se revisa periódicamente y se corrigen los errores reportados.'),
  array('Hospedajes','Solo se registran hospedajes existentes y ubicables. Los de ficha destacada se marcan con claridad para no confundirlos con recomendaciones, y si un negocio pide retirarse o corregir sus datos, se atiende en un plazo razonable.'),
  array('Sugerencias de usuarios','Se guardan como borrador y se publican solo después de una revisión. No se publican contenidos ofensivos, falsos ni que pongan en riesgo a las personas.'),
  array('Privacidad y datos personales','Solo se piden los datos necesarios, como nombre y correo. No se venden ni se comparten con terceros sin permiso, y los formularios tienen protección contra spam.'),
  array('Turismo responsable y seguridad','Se recomienda visitar los sitios naturales con guías y respetar las normas de cada parque. En volcanes activos o rutas exigentes se advierte la dificultad y la necesidad de guía. Se promueve no dejar basura, no alterar la flora ni la fauna y respetar a las comunidades.'),
  array('Imágenes y propiedad intelectual','Solo se usan imágenes propias, con autorización o con licencia que permita su uso, respetando la autoría de textos y fotografías de terceros.'),
  array('Accesibilidad y uso','La página se adapta a computadora, tableta y celular, con buen contraste, textos alternativos y navegación con teclado. Los visitantes usan la información como guía y planean su viaje bajo su propia responsabilidad.')
 );
 foreach($policies as $pol)$html.='<li><h3>'.esc_html($pol[0]).'</h3><p>'.esc_html($pol[1]).'</p></li>';
 $html.='</ol></section>';
 $html.='<section class="rn-section rn-appendices"><span class="rn-kicker">MATERIAL GRÁFICO DEL DOCUMENTO</span><h2>Anexos</h2>';
 $html.='<article class="rn-annex"><div><span class="rn-annex-label">ANEXO A</span><h3>Nombre y logotipo</h3><p>Ruta Cuscatlán · Lema: «Descubre El Salvador». El volcán representa las montañas, el sol el clima tropical y las olas la costa.</p></div><div class="rn-annex-gallery rn-annex-gallery-two">'.$img('anexo-a-logotipo.png','Logotipo de Ruta Cuscatlán','Logotipo del proyecto').$img('anexo-a-simbolos.png','Ilustración del volcán, sol y olas','Elementos del símbolo').'</div></article>';
 $html.='<article class="rn-annex"><div><span class="rn-annex-label">ANEXO B</span><h3>Paleta de colores</h3></div>'.$img('anexo-b-paleta-colores.png','Paleta de colores de Ruta Cuscatlán','Paleta de colores definida en el documento original.').'</article>';
 $html.='<article class="rn-annex"><div><span class="rn-annex-label">ANEXO C</span><h3>Estructura de la página</h3></div>'.$img('anexo-c-estructura-pagina.png','Diagrama de la estructura de la página Ruta Cuscatlán','Estructura de navegación de la página.').'</article>';
 $html.='<article class="rn-annex"><div><span class="rn-annex-label">ANEXO D</span><h3>Lugares incluidos al inicio</h3><p>Ejemplos: Cerro El Pital, Volcán de Santa Ana, El Tunco, Suchitoto, Lago de Coatepeque, Los Chorros, Joya de Cerén. La lista puede crecer con las sugerencias de los usuarios.</p></div>'.$img('anexo-d-lugares-iniciales.png','Gráfico de lugares iniciales por categoría','Distribución inicial de lugares por categoría, según el documento.').'</article>';
 $html.='<article class="rn-annex"><div><span class="rn-annex-label">ANEXO E</span><h3>Ejemplo de ficha: Cerro El Pital</h3></div>'.$img('anexo-e-ficha-cerro-el-pital.png','Ejemplo visual de ficha para Cerro El Pital','Ejemplo de cómo se presenta una ficha de destino.').'</article>';
 $html.='<article class="rn-annex rn-annex-sources"><div><span class="rn-annex-label">ANEXO F</span><h3>Fuentes de la información</h3><p>La información inicial proviene del portal oficial de turismo de El Salvador (<a href="https://elsalvador.travel/" target="_blank" rel="noopener">elsalvador.travel</a>) y de medios nacionales como <a href="https://www.elsalvador.com/" target="_blank" rel="noopener">El Salvador.com</a>. Los precios son referenciales y deben confirmarse con cada establecimiento antes de viajar.</p></div></article>';
 $html.='</section><footer class="rn-cta"><div><span class="rn-kicker">RUTA CUSCATLÁN · INSAL · NOVIEMBRE DE 2026</span><h2>Descubre El Salvador</h2><p>Encuentra a dónde ir y dónde quedarte, en un solo lugar.</p></div><a class="btn nar" href="'.esc_url(ruta_url('explorar')).'">Explorar lugares →</a></footer></section>';
 return $html;
}
add_shortcode('ruta_nosotros','ruta_shortcode_nosotros');
