<?php
/*
Plugin Name: Ruta Cuscatlán
Description: Guía turística de El Salvador: lugares por categoría (montañas, playas, lagos, pueblos, arqueología, naturaleza, parques recreativos y acuáticos, plazas, teatros y museos), buscador, fichas con hospedajes, página de hospedajes y formulario para sugerir lugares. Funciona con cualquier tema.
Version: 1.9.5
Author: Ruta Cuscatlán
Requires PHP: 7.4
Requires at least: 6.0
Text Domain: ruta-cuscatlan
*/
if(!defined('ABSPATH'))exit;
define('RUTA_V','1.9.5');define('RUTA_URL',plugin_dir_url(__FILE__));define('RUTA_DIR',plugin_dir_path(__FILE__));
require_once RUTA_DIR.'includes/datos.php';
require_once RUTA_DIR.'includes/nucleo.php';
require_once RUTA_DIR.'includes/fotografias.php';
require_once RUTA_DIR.'includes/fotos-auto.php';
require_once RUTA_DIR.'includes/sugerencias-db.php';
require_once RUTA_DIR.'includes/vistas.php';
// Ejecutar el alta/actualización después de que WordPress registre sus tipos y enlaces.
register_activation_hook(__FILE__,function(){ update_option('ruta_setup_pending',1); });
add_action('init',function(){
 if (get_option('ruta_setup_pending') || get_option('ruta_v') !== RUTA_V) {
  if (current_user_can('manage_options')) { ruta_setup(); delete_option('ruta_setup_pending'); }
 }
},30);
