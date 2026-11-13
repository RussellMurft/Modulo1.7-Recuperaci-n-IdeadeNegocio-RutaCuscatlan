<?php
if(!defined('ABSPATH'))exit;
/* ---------- Tipos de contenido ---------- */
add_action('init',function(){
 register_post_type('lugar',['labels'=>['name'=>'Lugares turísticos','singular_name'=>'Lugar','add_new_item'=>'Agregar lugar','edit_item'=>'Editar lugar','menu_name'=>'Ruta Cuscatlán'],'public'=>true,'has_archive'=>false,'menu_icon'=>'dashicons-location-alt','supports'=>['title','editor','excerpt','thumbnail'],'rewrite'=>['slug'=>'lugar'],'query_var'=>false,'show_in_rest'=>true]);
 register_taxonomy('categoria_lugar','lugar',['labels'=>['name'=>'Categorías','singular_name'=>'Categoría'],'hierarchical'=>true,'public'=>true,'query_var'=>false,'show_admin_column'=>true,'show_in_rest'=>true,'rewrite'=>['slug'=>'categoria-lugar']]);
});
/* ---------- Datos de cada lugar ---------- */
function ruta_m($id,$k){return (string)get_post_meta($id,'_rc_'.$k,true);}
function ruta_cat_de($id){$t=get_the_terms($id,'categoria_lugar');return($t&&!is_wp_error($t))?$t[0]->slug:'';}
function ruta_hosp_lineas($txt){$r=[];foreach(preg_split('/\r?\n/',(string)$txt) as $l){$l=trim($l);if($l==='')continue;$p=array_map('trim',explode('|',$l));$r[]=['n'=>$p[0],'t'=>$p[1]??'Hospedaje','p'=>$p[2]??'','o'=>$p[3]??''];}return $r;}
function ruta_lugar_por_slug($s){static $c=[];if(isset($c[$s]))return $c[$s];$p=get_posts(['post_type'=>'lugar','name'=>$s,'numberposts'=>1,'post_status'=>'publish']);if($p)$c[$s]=$p[0];return $p?$p[0]:null;}
function ruta_hosp($id){static $cache=[];if(isset($cache[$id]))return $cache[$id];$propios=ruta_hosp_lineas(ruta_m($id,'hosp'));$ref=ruta_m($id,'ref');$cerca=[];$refp=null;
 if(!$propios&&$ref&&($refp=ruta_lugar_por_slug($ref)))$cerca=ruta_hosp_lineas(ruta_m($refp->ID,'hosp'));
 return $cache[$id]=['propios'=>$propios,'cerca'=>$cerca,'ref'=>$refp];}
function ruta_estado_hosp($id){$h=ruta_hosp($id);return $h['propios']?2:($h['cerca']?1:0);}
function ruta_maps($nombre){return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($nombre.', El Salvador');}
function ruta_hosp_img($id,$nombre){
 $media=get_post_meta($id,'_rc_hosp_media',true);$key=md5(mb_strtolower(trim((string)$nombre)));
 if(is_array($media)&&!empty($media[$key])){$url=wp_get_attachment_image_url((int)$media[$key],'large');if($url)return esc_url($url);}
 $rows=preg_split('/\\r?\\n/',(string)ruta_m($id,'hosp_imgs'));
 foreach($rows as $row){$parts=array_map('trim',explode('|',$row,2));if(count($parts)===2&&mb_strtolower($parts[0])===mb_strtolower($nombre)&&wp_http_validate_url($parts[1]))return esc_url($parts[1]);}
 return '';
}
function ruta_map_query($id){
 $custom=trim(ruta_m($id,'map'));if($custom)return $custom;
 return trim(get_the_title($id).', '.ruta_m($id,'muni').', '.ruta_m($id,'depto').', El Salvador',', ');
}
function ruta_como_llegar($nombre){return 'https://www.google.com/maps/dir/?api=1&destination='.rawurlencode($nombre.', El Salvador');}
function ruta_url($slug){static $c=[];if(isset($c[$slug]))return $c[$slug];global $wpdb;
 $m=['explorar'=>'ruta_explorar','donde-hospedarse'=>'ruta_hospedajes','sugerir-lugar'=>'ruta_sugerir','descubre'=>'ruta_inicio'];$p=get_page_by_path($slug);$id=($p&&$p->post_status==='publish')?$p->ID:0;
 if(!$id&&isset($m[$slug]))$id=(int)$wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status='publish' AND post_content LIKE %s ORDER BY ID LIMIT 1",'%['.$wpdb->esc_like($m[$slug]).'%'));
 return $c[$slug]=$id?get_permalink($id):home_url('/');}
/* ---------- Horarios iniciales (una sola vez; solo donde hay fuente y el lugar no tiene horario) ---------- */
function ruta_sembrar_horarios(){
 if(get_option('ruta_horarios_sembrados_v2'))return;
 $t=fn($h)=>array_fill(0,7,$h); // índice 0 = domingo, 1 = lunes
 $arq=['09:00-16:00','cerrado','09:00-16:00','09:00-16:00','09:00-16:00','09:00-16:00','09:00-16:00'];
 $nArq='Cerrado los lunes. Los parques arqueológicos cierran habitualmente a las 4:00 p. m. (Ministerio de Cultura).';
 $nAsc='Ascenso guiado en grupos, generalmente entre 7:30 y 11:00 a. m., desde Cerro Verde. Confirma el cupo antes de ir.';
 $libre='Acceso libre, sin horario oficial. Los servicios cercanos (restaurantes, lanchas, parqueos) tienen su propio horario.';
 $libreN='Sin horario oficial: visita de día y confirma el acceso con la comunidad local o un guía.';
 $libreP='Espacio público de acceso libre. Los museos, comercios y atractivos del lugar tienen su propio horario.';
 $d=[
  'tazumal'=>[$arq,$nArq],'joya-de-ceren'=>[$arq,$nArq],'cihuatan'=>[$arq,$nArq],
  'boqueron'=>[$t('08:00-17:00'),'Puede cerrar temporalmente por mantenimiento o clima.'],
  'los-chorros'=>[$t('08:00-16:00'),'Horario habitual de lunes a domingo.'],
  'el-imposible'=>[$t('07:30-15:30'),'El ingreso es por la mañana y algunos sectores requieren permiso previo del MARN.'],
  'cerro-verde'=>[$t('08:00-16:00'),'Algunas fuentes indican un cierre más tarde; confirma el horario antes de viajar.'],
  'volcan-santa-ana'=>[$t('07:30-11:00'),$nAsc],'volcan-izalco'=>[$t('07:30-11:00'),$nAsc],
  'montecristo'=>[$t('07:30-15:00'),'Ingreso de 7:30 a 9:00 a. m. con permiso previo del MARN y vehículo de doble tracción. Confirma que esté abierto antes de viajar.'],
  'volcan-san-miguel'=>[$t('cerrado'),'No hay sendero turístico autorizado: el MARN pide no subir por la actividad del volcán.'],
 ];
 foreach(['el-tunco','el-zonte','el-sunzal','puerto-la-libertad','los-cobanos','costa-del-sol','el-cuco','maculis','salinitas','coatepeque','ilopango','suchitlan','laguna-verde','guija'] as $s)$d[$s]=[$t('24h'),$libre];
 foreach(['suchitoto','ataco','juayua','ruta-flores','santa-ana','centro-san-salvador','perquin'] as $s)$d[$s]=[$t('24h'),$libreP];
 foreach(['el-pital','miramundo','chinchontepec','conchagua','cascada-tercios','malacatiupan'] as $s)$d[$s]=[$t('24h'),$libreN];
 foreach($d as $slug=>$x){$p=ruta_lugar_por_slug($slug);if(!$p)continue;$vacio=true;for($i=0;$i<7;$i++)if(ruta_m($p->ID,'h'.$i)!==''){$vacio=false;break;}
  if(!$vacio)continue;for($i=0;$i<7;$i++)update_post_meta($p->ID,'_rc_h'.$i,$x[0][$i]);if(ruta_m($p->ID,'hnota')==='')update_post_meta($p->ID,'_rc_hnota',$x[1]);}
 update_option('ruta_horarios_sembrados_v2',1);
}
/* ---------- Horarios y estado «abierto ahora» (hora de El Salvador) ---------- */
function ruta_horario_parse($t){$t=trim(mb_strtolower((string)$t));if($t==='')return null;
 if(in_array($t,['cerrado','cerrada'],true))return [];
 if(in_array($t,['24h','24 h','24 horas'],true))return [[0,1440]];
 $r=[];foreach(preg_split('/\s*[,;]\s*/',$t) as $seg){if(preg_match('/^(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})$/',$seg,$m)){$a=(int)$m[1]*60+(int)$m[2];$b=(int)$m[3]*60+(int)$m[4];if($b<=$a)$b+=1440;$r[]=[$a,$b];}}
 return $r?:null;}
function ruta_horario_libre($w){foreach($w as $x)if($x!==[[0,1440]])return false;return true;}
function ruta_horario_semana($id){$w=[];for($i=0;$i<7;$i++)$w[$i]=ruta_horario_parse(ruta_m($id,'h'.$i));return $w;}
function ruta_horario_texto($v){if($v===null)return 'Sin dato';if(!$v)return 'Cerrado';if($v===[[0,1440]])return '24 horas';
 return implode(', ',array_map(fn($r)=>sprintf('%02d:%02d – %02d:%02d',intdiv($r[0],60),$r[0]%60,intdiv($r[1]%1440,60),$r[1]%60),$v));}
function ruta_abierto_ahora($id){$w=ruta_horario_semana($id);if(!array_filter($w,fn($x)=>$x!==null))return null;
 $n=new DateTime('now',new DateTimeZone('America/El_Salvador'));$d=(int)$n->format('w');$m=(int)$n->format('G')*60+(int)$n->format('i');
 foreach($w[$d]??[] as $r)if($m>=$r[0]&&$m<$r[1])return true;
 foreach($w[($d+6)%7]??[] as $r)if($r[1]>1440&&$m<$r[1]-1440)return true;
 return false;}
function ruta_badge_abierto($id){$w=ruta_horario_semana($id);if(!array_filter($w,fn($x)=>$x!==null))return '';if(ruta_horario_libre($w))return '<span class="tag rc-abierto ok" data-libre="1">● Acceso libre</span>';$o=ruta_abierto_ahora($id);
 return '<span class="tag rc-abierto '.($o?'ok':'no').'" data-h="'.esc_attr(wp_json_encode($w)).'">'.($o?'● Abierto ahora':'● Cerrado ahora').'</span>';}
function ruta_ficha_extra($id){$o='';$w=ruta_horario_semana($id);
 if(array_filter($w,fn($x)=>$x!==null)){$dias=['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];$hoy=(int)(new DateTime('now',new DateTimeZone('America/El_Salvador')))->format('w');
  $o.='<section class="ruta-horario" id="horario"><h3>Horario '.ruta_badge_abierto($id).'</h3>';
  if(ruta_horario_libre($w))$o.='<p>Acceso libre, sin horario oficial.</p>';else{$o.='<table class="ficha">';
  foreach([1,2,3,4,5,6,0] as $d)$o.='<tr data-d="'.$d.'"'.($d===$hoy?' class="hoy"':'').'><th>'.$dias[$d].'</th><td>'.esc_html(ruta_horario_texto($w[$d])).'</td></tr>';
  $o.='</table>';}if($n=ruta_m($id,'hnota'))$o.='<p class="meta">'.esc_html($n).'</p>';
  $o.='<p class="meta">Horarios referenciales (hora de El Salvador). Confirma antes de viajar: pueden cambiar en feriados o por clima.</p></section>';}
 $rows='';
 if($t=ruta_m($id,'tel'))$rows.='<tr><th>Teléfono</th><td><a href="tel:'.esc_attr(preg_replace('/[^0-9+]/','',$t)).'">'.esc_html($t).'</a></td></tr>';
 if($t=ruta_m($id,'wa')){$d=preg_replace('/\D/','',$t);if($d)$rows.='<tr><th>WhatsApp</th><td><a target="_blank" rel="noopener" href="'.esc_url('https://wa.me/'.$d).'">'.esc_html($t).'</a></td></tr>';}
 if($t=ruta_m($id,'web'))$rows.='<tr><th>Sitio web / redes</th><td><a target="_blank" rel="noopener" href="'.esc_url($t).'">'.esc_html(preg_replace('#^https?://#','',$t)).'</a></td></tr>';
 if($t=ruta_m($id,'entrada'))$rows.='<tr><th>Entrada</th><td>'.esc_html($t).'</td></tr>';
 if($rows)$o.='<table class="ficha">'.$rows.'</table>';
 if($s=array_filter(array_map('trim',explode(',',ruta_m($id,'servicios'))))){$o.='<p>';foreach($s as $x)$o.='<span class="tag ok">'.esc_html($x).'</span> ';$o.='</p>';}
 return $o;}
/* ---------- Edición en el panel ---------- */
add_action('add_meta_boxes',fn()=>add_meta_box('rc_ficha','Ficha del lugar',function($p){wp_nonce_field('rc_f','rc_fn');
 $v=fn($k)=>esc_attr(ruta_m($p->ID,$k));
 echo '<p><label><b>Departamento</b><br><select name="rc_depto"><option value="">—</option>';foreach(ruta_deptos() as $d)echo '<option'.selected(ruta_m($p->ID,'depto'),$d,false).'>'.esc_html($d).'</option>';echo '</select></label> ';
 foreach(['muni'=>'Municipio','altura'=>'Altura','dif'=>'Dificultad','epoca'=>'Mejor época'] as $k=>$l)echo '<label style="margin-left:12px"><b>'.$l.'</b><br><input name="rc_'.$k.'" value="'.$v($k).'"></label>';
 echo '</p><p><label><b>Qué hacer</b> (una actividad por línea)<br><textarea class="widefat" rows="4" name="rc_acts">'.esc_textarea(ruta_m($p->ID,'acts')).'</textarea></label></p>';
 echo '<p><label><b>Hospedajes de este lugar</b> — uno por línea: <code>Nombre | Tipo | Precio | Nota</code><br><textarea class="widefat" rows="6" name="rc_hosp">'.esc_textarea(ruta_m($p->ID,'hosp')).'</textarea></label></p>';
 echo '<p><label><b>Fotografías de hospedajes</b> — una por línea: <code>Nombre exacto del hospedaje | URL de imagen</code><br><textarea class="widefat" rows="5" name="rc_hosp_imgs" placeholder="Hotel Ejemplo | https://...">'.esc_textarea(ruta_m($p->ID,'hosp_imgs')).'</textarea></label><br><span class="description">Usa únicamente fotografías propias, autorizadas o con licencia compatible. El nombre debe coincidir exactamente con el hospedaje.</span></p>';
 $hospedajes=ruta_hosp_lineas(ruta_m($p->ID,'hosp'));$media=get_post_meta($p->ID,'_rc_hosp_media',true);if(!is_array($media))$media=[];
 echo '<div class="rc-hosp-media-box"><h4>Fotos de cada hospedaje (Biblioteca de Medios)</h4><p class="description">Primero guarda la lista de hospedajes. Después selecciona una fotografía propia y autorizada para cada establecimiento. Nunca se reutilizará la fotografía del destino como si fuera la del hotel.</p>';
 if($hospedajes){foreach($hospedajes as $hotel){$key=md5(mb_strtolower(trim($hotel['n'])));$aid=(int)($media[$key]??0);$preview=$aid?wp_get_attachment_image_url($aid,'thumbnail'):'';echo '<div class="rc-hosp-media-row" data-hotel="'.esc_attr($hotel['n']).'"><strong>'.esc_html($hotel['n']).'</strong><input class="rc-hosp-media-id" type="hidden" name="rc_hosp_media['.esc_attr($key).']" value="'.$aid.'"><span class="rc-hosp-media-preview">'.($preview?'<img src="'.esc_url($preview).'" alt="">':'<span class="rc-hosp-no-image">Sin foto seleccionada</span>').'</span><button type="button" class="button rc-hosp-media-select">'.($preview?'Cambiar foto':'Elegir foto').'</button><button type="button" class="button rc-hosp-media-remove"'.($preview?'':' hidden').'>Quitar</button></div>';}}else{echo '<p class="description">Aún no hay hospedajes en la lista. Añade los nombres arriba, guarda el lugar y vuelve a editarlo para asignar sus fotos.</p>';}
 echo '</div>';
 echo '<p><label><b>Ubicación para el mapa (opcional)</b><br><input class="widefat" name="rc_map" value="'.esc_attr(ruta_m($p->ID,'map')).'" placeholder="Nombre del sitio, municipio, departamento, El Salvador"></label><br><span class="description">Si lo dejas vacío, se genera automáticamente con el nombre y municipio.</span></p>';
 echo '<p><label><b>Si no tiene hospedaje propio, usar el de este lugar cercano</b> (slug, ej. suchitoto)<br><input name="rc_ref" value="'.$v('ref').'"></label> <label style="margin-left:12px"><input type="checkbox" name="rc_dest" value="1"'.checked(ruta_m($p->ID,'dest'),'1',false).'> Destacado en la portada</label></p>';
 echo '<hr><h4>Horario de atención</h4><p class="description">Formato de 24 horas: <code>08:00-17:00</code>. Varios turnos: <code>08:00-12:00, 14:00-18:00</code>. Escribe <code>24h</code> si abre todo el día o <code>cerrado</code>. Si no llenas ningún día, no se muestra el estado «abierto ahora».</p><p>';
 foreach(['h1'=>'Lunes','h2'=>'Martes','h3'=>'Miércoles','h4'=>'Jueves','h5'=>'Viernes','h6'=>'Sábado','h0'=>'Domingo'] as $k=>$l)echo '<label style="display:inline-block;margin:0 12px 8px 0"><b>'.$l.'</b><br><input name="rc_'.$k.'" value="'.$v($k).'" placeholder="08:00-17:00" size="18"></label>';
 echo '</p><p><label><b>Nota del horario</b> (feriados, temporada, etc.)<br><input class="widefat" name="rc_hnota" value="'.$v('hnota').'"></label></p><h4>Contacto y datos útiles</h4><p>';
 foreach(['tel'=>'Teléfono','wa'=>'WhatsApp (con código de país, ej. 50370000000)','web'=>'Sitio web o red social','entrada'=>'Entrada / precio'] as $k=>$l)echo '<label style="display:inline-block;margin:0 12px 8px 0"><b>'.$l.'</b><br><input name="rc_'.$k.'" value="'.$v($k).'" size="30"></label>';
 echo '</p><p><label><b>Servicios</b> (separados por coma: Parqueo, Baños, Restaurante, Guías, Acceso para silla de ruedas…)<br><input class="widefat" name="rc_servicios" value="'.$v('servicios').'"></label></p>';
 echo '<p><label><b>Palabras de búsqueda extra</b><br><input class="widefat" name="rc_tags" value="'.$v('tags').'"></label></p>';},'lugar','normal'));
add_action('save_post_lugar',function($id){if(!isset($_POST['rc_fn'])||!wp_verify_nonce($_POST['rc_fn'],'rc_f')||!current_user_can('edit_post',$id))return;
 foreach(['depto','muni','altura','dif','epoca','ref','tags','map','h0','h1','h2','h3','h4','h5','h6','hnota','tel','wa','web','entrada','servicios'] as $k)update_post_meta($id,'_rc_'.$k,sanitize_text_field($_POST['rc_'.$k]??''));
 foreach(['acts','hosp','hosp_imgs'] as $k)update_post_meta($id,'_rc_'.$k,sanitize_textarea_field(wp_unslash($_POST['rc_'.$k]??'')));$media=[];foreach((array)wp_unslash($_POST['rc_hosp_media']??[]) as $key=>$attachment_id){$key=sanitize_key($key);$attachment_id=absint($attachment_id);if($key&&$attachment_id&&wp_attachment_is_image($attachment_id))$media[$key]=$attachment_id;}update_post_meta($id,'_rc_hosp_media',$media);update_post_meta($id,'_rc_dest',empty($_POST['rc_dest'])?'':'1');});
/* ---------- Sugerencias de visitantes visibles en el panel ---------- */
add_action('add_meta_boxes_lugar',function($p){if(ruta_m($p->ID,'sug'))add_meta_box('rc_sug','Sugerido por un visitante',function($p){echo '<p style="white-space:pre-wrap">'.esc_html(ruta_m($p->ID,'sug')).'</p>';},'lugar','side','high');});
add_filter('manage_lugar_posts_columns',function($c){$c['rc_depto']='Departamento';$c['rc_hosp']='Hospedaje';return $c;});
add_action('manage_lugar_posts_custom_column',function($col,$id){if($col==='rc_depto')echo esc_html(ruta_m($id,'depto'));if($col==='rc_hosp')echo esc_html(['Sin registro','Cercano','Propio'][ruta_estado_hosp($id)]);},10,2);
/* ---------- Selector visual de fotos de hospedajes ---------- */
add_action('admin_enqueue_scripts',function($hook){
 if(!in_array($hook,['post.php','post-new.php'],true))return;$screen=get_current_screen();if(!$screen||$screen->post_type!=='lugar')return;
 wp_enqueue_media();wp_enqueue_script('ruta-hosp-media',RUTA_URL.'assets/hosp-media.js',['jquery'],RUTA_V,true);
});
/* ---------- Diagnóstico: conexión y ubicación real de los datos ---------- */
add_action('admin_menu',function(){add_submenu_page('edit.php?post_type=lugar','Estado de datos','Estado de datos','manage_options','ruta-estado-datos','ruta_admin_estado_datos');});
function ruta_admin_estado_datos(){
 if(!current_user_can('manage_options'))return;global $wpdb;$counts=wp_count_posts('lugar');$published=(int)($counts->publish??0);$pending=(int)($counts->pending??0);
 $legacy_l=$wpdb->prefix.'ruta_lugares';$legacy_c=$wpdb->prefix.'ruta_categorias';
 $has_l=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$legacy_l))===$legacy_l;$has_c=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$legacy_c))===$legacy_c;
 echo '<div class="wrap"><h1>Estado de datos · Ruta Cuscatlán</h1><p>El plugin utiliza la misma conexión MySQL definida por WordPress en <code>wp-config.php</code>. No abre una segunda conexión ni necesita una base de datos paralela.</p><table class="widefat striped" style="max-width:900px"><tbody>';
 $visible=count(ruta_lugares_todos());$duplicates=ruta_lugares_duplicados_ocultos();
 $rows=['Base de datos activa'=>$wpdb->dbname,'Prefijo de tablas'=>$wpdb->prefix,'Tabla principal de contenidos'=>$wpdb->posts,'Tabla de datos complementarios'=>$wpdb->postmeta,'Lugares publicados en WordPress'=>$published,'Lugares únicos mostrados en el catálogo'=>$visible,'Registros con título duplicado ocultos (sin borrar)'=>$duplicates,'Lugares pendientes de revisión'=>$pending,'Tabla antigua '.$legacy_l=>$has_l?'Detectada':'No detectada','Tabla antigua '.$legacy_c=>$has_c?'Detectada':'No detectada'];
 foreach($rows as $label=>$value)echo '<tr><th style="width:42%">'.esc_html($label).'</th><td>'.esc_html((string)$value).'</td></tr>';
 echo '</tbody></table><h2>Cómo se guardan los datos</h2><p><code>'.$wpdb->posts.'</code> contiene los registros de tipo <code>lugar</code>. <code>'.$wpdb->postmeta.'</code> contiene departamento, municipio, actividades, hospedajes, referencias e imágenes. Las categorías usan las tablas de términos de WordPress.</p>';
 if($published===0){echo '<div class="notice notice-warning inline"><p>No se encontraron lugares publicados en la base activa. No cambies <code>DB_NAME</code> a otra base vacía. Comprueba si los datos antiguos están en un respaldo SQL o si falta importarlos.</p></div>';}
 if($has_l||$has_c){echo '<div class="notice notice-info inline"><p>Se detectó al menos una tabla antigua de Ruta Cuscatlán. La migración la puede incorporar al catálogo WordPress sin borrar la tabla original.</p></div>';}
 echo '<p>Para verificar registros en phpMyAdmin, selecciona primero la base indicada arriba y ejecuta: <code>SELECT ID, post_title, post_status FROM '.$wpdb->posts.' WHERE post_type = &#39;lugar&#39; ORDER BY ID DESC LIMIT 30;</code></p></div>';
}
/* ---------- Horarios de los lugares agregados en 1.9.0 (una sola vez; no pisa horarios ya cargados) ---------- */
function ruta_sembrar_horarios_v3(){
 if(get_option('ruta_horarios_sembrados_v3'))return;
 $t=fn($h)=>array_fill(0,7,$h); // índice 0 = domingo, 1 = lunes
 $istu='Horario publicado por el ISTU. Confirma antes de viajar.';
 $libre='Acceso libre, sin horario oficial. Los servicios cercanos tienen su propio horario.';
 $libreP='Espacio público de acceso libre. Los museos, comercios y atractivos del lugar tienen su propio horario.';
 $d=[];
 foreach(['amapulapa','apulo','atecozol','sihuatehuacan','ichanmichen','laguna-apastepeque','altos-de-la-cueva','agua-fria','walter-thilo-deininger'] as $s)$d[$s]=[$t('08:00-16:00'),$istu];
 $d['balboa']=[$t('06:00-22:00'),'Ingreso peatonal de 6:00 a. m. a 10:00 p. m.; ingreso vehicular de 8:00 a. m. a 9:00 p. m. (ISTU).'];
 $d['puerta-del-diablo']=[$t('08:00-22:00'),$istu];
 $mi=['09:00-16:00','cerrado','cerrado','09:00-16:00','09:00-16:00','09:00-16:00','09:00-16:00'];
 $d['parque-infantil-diversiones']=[$mi,'Abre de miércoles a domingo (ISTU). Confirma antes de viajar.'];
 $d['saburo-hirao']=[$mi,'Abre de miércoles a domingo (ISTU). Confirma antes de viajar.'];
 $d['sunset-park']=[['10:00-22:00','cerrado','cerrado','cerrado','14:00-20:00','14:00-22:00','10:00-22:00'],'Los miércoles se reserva para centros escolares y comunidades que lo soliciten al ISTU. Confirma antes de viajar.'];
 foreach(['plaza-morazan','plaza-gerardo-barrios','plaza-libertad','salvador-del-mundo'] as $s)$d[$s]=[$t('24h'),$libreP];
 foreach(['metalio','mizata','isla-meanguera','barra-de-santiago'] as $s)$d[$s]=[$t('24h'),$libre];
 foreach($d as $slug=>$x){$p=ruta_lugar_por_slug($slug);if(!$p)continue;$vacio=true;for($i=0;$i<7;$i++)if(ruta_m($p->ID,'h'.$i)!==''){$vacio=false;break;}
  if(!$vacio)continue;for($i=0;$i<7;$i++)update_post_meta($p->ID,'_rc_h'.$i,$x[0][$i]);if(ruta_m($p->ID,'hnota')==='')update_post_meta($p->ID,'_rc_hnota',$x[1]);}
 update_option('ruta_horarios_sembrados_v3',1);
}
/* ---------- Datos estructurados para buscadores (SEO) ---------- */
add_action('wp_head',function(){if(!is_singular('lugar'))return;$id=get_the_ID();
 $d=['@context'=>'https://schema.org','@type'=>'TouristAttraction','name'=>get_the_title($id),'description'=>wp_strip_all_tags(get_the_excerpt($id)),'url'=>get_permalink($id),
  'address'=>['@type'=>'PostalAddress','addressRegion'=>ruta_m($id,'depto'),'addressLocality'=>ruta_m($id,'muni'),'addressCountry'=>'SV']];
 if(has_post_thumbnail($id))$d['image']=get_the_post_thumbnail_url($id,'large');elseif(function_exists('ruta_foto_lugar_info')){$fi=ruta_foto_lugar_info($id);if(!empty($fi['src']))$d['image']=$fi['src'];}
 echo '<script type="application/ld+json">'.wp_json_encode($d,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG).'</script>';});
/* ---------- Carga inicial: categorías, lugares, páginas y menú ---------- */
function ruta_setup(){
 foreach(ruta_cats() as $s=>$c)if(!term_exists($s,'categoria_lugar'))wp_insert_term($c[0],'categoria_lugar',['slug'=>$s]);
 foreach(ruta_lugares() as $L){[$s,$n,$cat,$dep,$mu,$res,$det,$alt,$dif,$ep,$acts,$hos,$ref,$dest]=$L;if(ruta_lugar_por_slug($s))continue;
  $id=wp_insert_post(['post_type'=>'lugar','post_status'=>'publish','post_title'=>$n,'post_name'=>$s,'post_content'=>$det,'post_excerpt'=>$res]);if(!$id||is_wp_error($id))continue;
  wp_set_object_terms($id,$cat,'categoria_lugar');
  foreach(['depto'=>$dep,'muni'=>$mu,'altura'=>$alt,'dif'=>$dif,'epoca'=>$ep,'acts'=>implode("\n",$acts),'ref'=>$ref,'dest'=>$dest?'1':''] as $k=>$v)update_post_meta($id,'_rc_'.$k,$v);
  update_post_meta($id,'_rc_hosp',implode("\n",array_map(fn($h)=>implode(' | ',$h),$hos)));}
 $pg=[['Descubre El Salvador','descubre','[ruta_inicio]'],['Explorar lugares','explorar','[ruta_explorar]'],['Alojamiento','donde-hospedarse','[ruta_hospedajes]'],['Sugerir un lugar','sugerir-lugar','[ruta_sugerir]'],['Nosotros','nosotros','[ruta_nosotros]']];$ids=[];
 foreach($pg as $p){$x=get_page_by_path($p[1]);if($x&&$p[1]==='donde-hospedarse'&&$x->post_title==='Dónde hospedarse')wp_update_post(['ID'=>$x->ID,'post_title'=>'Alojamiento']);$id=$x?$x->ID:wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>$p[0],'post_name'=>$p[1],'post_content'=>$p[2]]);if($id&&!is_wp_error($id))$ids[$p[1]]=$id;}
 // Mantener un único menú gestionado por el tema. Si existe, limpiar duplicados y actualizarlo.
 $menu=wp_get_nav_menu_object('Ruta Cuscatlán');
 if(!$menu){$mid=wp_create_nav_menu('Ruta Cuscatlán');$menu=!is_wp_error($mid)?wp_get_nav_menu_object($mid):false;}
 if($menu){$wanted=['descubre','explorar','donde-hospedarse','nosotros','sugerir-lugar'];$existing=[];foreach(wp_get_nav_menu_items($menu->term_id)?:[] as $item){$slug=basename(untrailingslashit(parse_url($item->url,PHP_URL_PATH)));if($slug==='ruta-cuscatlan'||stripos($item->title,'página de inicio')!==false){wp_delete_post($item->ID,true);continue;}if(isset($existing[$slug]))wp_delete_post($item->ID,true);else $existing[$slug]=$item->ID;}
 foreach($wanted as $slug){if(empty($ids[$slug]))continue;$id=$ids[$slug];if(isset($existing[$slug])){wp_update_nav_menu_item($menu->term_id,$existing[$slug],['menu-item-title'=>get_the_title($id),'menu-item-object'=>'page','menu-item-object-id'=>$id,'menu-item-type'=>'post_type','menu-item-status'=>'publish']);}else{wp_update_nav_menu_item($menu->term_id,0,['menu-item-title'=>get_the_title($id),'menu-item-object'=>'page','menu-item-object-id'=>$id,'menu-item-type'=>'post_type','menu-item-status'=>'publish']);}}}
 // Importar una sola vez los registros de la tabla heredada de v1.4, sin borrarla.
 ruta_migrar_tablas_v14();
 ruta_sembrar_horarios();
 ruta_sembrar_horarios_v3();
 update_option('ruta_v',RUTA_V);flush_rewrite_rules();}

function ruta_migrar_tablas_v14(){
 global $wpdb; if(get_option('ruta_migracion_v14'))return;
 $lt=$wpdb->prefix.'ruta_lugares';$ct=$wpdb->prefix.'ruta_categorias';
 if($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$lt))===$lt){
  $rows=$wpdb->get_results("SELECT * FROM `{$lt}`",ARRAY_A);
  foreach($rows as $r){$title=sanitize_text_field($r['nombre']??'');if(!$title)continue;$slug=sanitize_title($r['slug']??$title);if(ruta_lugar_por_slug($slug))continue;
   $id=wp_insert_post(['post_type'=>'lugar','post_status'=>'publish','post_title'=>$title,'post_name'=>$slug,'post_content'=>wp_kses_post($r['descripcion']??'')]);if(!$id||is_wp_error($id))continue;
   foreach(['depto'=>$r['departamento']??'','muni'=>$r['ubicacion']??''] as $k=>$v)update_post_meta($id,'_rc_'.$k,sanitize_text_field($v));
   if(!empty($r['imagen_url'])&&wp_http_validate_url($r['imagen_url']))update_post_meta($id,'_rc_legacy_image',esc_url_raw($r['imagen_url']));
   if(!empty($r['destacado']))update_post_meta($id,'_rc_dest','1');
   $cat='naturaleza';if(!empty($r['id_categoria'])&&$wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s",$ct))===$ct){$c=$wpdb->get_row($wpdb->prepare("SELECT slug,nombre FROM `{$ct}` WHERE id_categoria=%d",(int)$r['id_categoria']),ARRAY_A);if($c){$cat=sanitize_title($c['slug']);if(!term_exists($cat,'categoria_lugar'))wp_insert_term(sanitize_text_field($c['nombre']),'categoria_lugar',['slug'=>$cat]);}}
   $term=get_term_by('slug',$cat,'categoria_lugar');if(!$term){$cat='naturaleza';$term=get_term_by('slug',$cat,'categoria_lugar');}if($term)wp_set_object_terms($id,$term->term_id,'categoria_lugar');
  }
 }
 update_option('ruta_migracion_v14',1,false);
}
add_action('wp_loaded',function(){if(current_user_can('manage_options')&&get_option('ruta_v')!==RUTA_V)ruta_setup();});

/* Oculta del menú de navegación el enlace «Ruta Cuscatlán: página de inicio». */
add_filter('wp_nav_menu_objects',function($items){return array_values(array_filter($items,function($i){$slug=basename(untrailingslashit((string)parse_url((string)$i->url,PHP_URL_PATH)));return $slug!=='ruta-cuscatlan'&&stripos(wp_strip_all_tags((string)$i->title),'página de inicio')===false;}));},5);
