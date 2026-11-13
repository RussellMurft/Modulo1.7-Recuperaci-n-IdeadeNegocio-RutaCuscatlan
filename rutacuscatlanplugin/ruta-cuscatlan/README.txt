RUTA CUSCATLÁN 1.9.5

NOVEDADES 1.9.5
- "Dónde hospedarse" ahora se llama "Alojamiento" (título de la página, menú y botón de inicio). La dirección /donde-hospedarse/ no cambia, para no romper enlaces.

NOVEDADES 1.9.4
- Tabla propia para los lugares sugeridos: wp_ruta_sugerencias (con el prefijo real de WordPress), creada automáticamente en la misma base de datos de wp-config.php. Se ve en phpMyAdmin con columnas claras: nombre_lugar, categoria, departamento, descripcion, hospedajes, nombre_autor, correo_autor, estado, fecha_envio, fecha_revision, post_id.
- Cada sugerencia nueva se guarda en esa tabla y, como antes, crea un lugar "pendiente" en wp_posts (enlazado por post_id).
- Nueva pantalla Lugares turísticos > Sugerencias: lista, filtra por estado, aprueba y publica, rechaza, elimina el registro y descarga CSV.
- Al actualizar, las sugerencias que ya estaban en wp_posts se importan una sola vez a la tabla nueva. No se borra nada.
- El estado se sincroniza: publicar el lugar lo marca "aprobada"; enviarlo a la papelera lo marca "rechazada".

NOVEDADES 1.9.3
- Segunda vía de búsqueda de fotos: si el artículo de Wikipedia de un lugar no tiene imagen libre, el plugin busca directamente en Wikimedia Commons. Solo acepta archivos cuyo título, categorías o descripción mencionen el nombre del lugar y "Salvador", con licencia libre, de al menos 1000 px de ancho y sin repetir imágenes.

NOVEDADES 1.9.2
- Dos fotografías verificadas manualmente en Commons (autor y licencia revisados): Teatro Nacional de San Salvador y Parque Arqueológico San Andrés.

NOVEDADES 1.9.1
- Fotografías automáticas: para los 44 destinos sin foto, el plugin busca en segundo plano la imagen del artículo de Wikipedia de ese mismo lugar y confirma en Wikimedia Commons que tenga licencia libre (CC BY, CC BY-SA, CC0 o dominio público). Cada tarjeta muestra autor, licencia y enlace a la fuente.
- Salvaguardas: el artículo debe mencionar "Salvador" (evita homónimos), se descartan desambiguaciones, mapas, escudos, SVG e imágenes pequeñas, y una misma imagen nunca se repite en dos destinos.
- Nueva pantalla Lugares turísticos > Fotografías: botón para buscar, reintentar o reiniciar, con la tabla de qué foto, autor y licencia tiene cada destino.
- La foto destacada de WordPress y el catálogo manual siguen teniendo prioridad. Los destinos sin foto libre encontrada conservan el marcador pendiente; asígnales una foto destacada.
- El servidor necesita conexiones salientes a wikipedia.org y commons.wikimedia.org y WP-Cron activo.
- Datos estructurados (SEO): la imagen del destino ahora también se incluye cuando proviene de Commons.

NOVEDADES 1.9.0
- Dos categorías nuevas: "Parques recreativos y acuáticos" y "Plazas, teatros y museos".
- 41 lugares nuevos (parques del ISTU como Amapulapa, Apulo, Atecozol, Sihuatehuacán, Ichanmichen; Teatro Nacional, plazas y museos del Centro Histórico; pueblos, sitios arqueológicos, playas y áreas naturales).
- Al actualizar, el plugin agrega solo los lugares que no existen (por slug); no modifica ni borra los existentes.
- Fotografías: los lugares nuevos muestran marcador pendiente hasta asignar una foto destacada propia o verificar una de Wikimedia Commons con licencia.

INSTALACIÓN
1. Haz una copia de seguridad de la base de datos y de la carpeta del plugin.
2. En WordPress, desactiva únicamente el plugin Ruta Cuscatlán.
3. Sustituye su carpeta por la carpeta ruta-cuscatlan incluida en este ZIP.
4. Activa el plugin y conserva activa la misma base de datos configurada en wp-config.php.
5. Instala/actualiza el tema Ruta Cuscatlán Explorer por separado.

BASE DE DATOS
El plugin usa la conexión MySQL definida por WordPress en wp-config.php. No crea una base paralela. Los lugares se guardan como tipo de contenido 'lugar' en la tabla wp_posts (con el prefijo real de WordPress); departamentos, municipios, actividades, hospedajes e imágenes se guardan en wp_postmeta. Las categorías utilizan las tablas de términos de WordPress. No borra ni combina físicamente registros existentes.

DUPLICADOS
El catálogo público oculta entradas publicadas cuyo título normalizado coincide con el de otra entrada. El registro elegido es el de mayor contenido/completitud. Las entradas originales permanecen intactas en MySQL. En Lugares turísticos > Estado de datos se muestran el número de lugares únicos y el número de registros duplicados ocultos.

FOTOGRAFÍAS
Las fotografías destacadas asignadas en WordPress y las imágenes heredadas tienen prioridad. Si no existen, el plugin usa una fotografía específica del catálogo Commons solo cuando coincide con el slug del destino y hay fuente/licencia registrada. No reutiliza imágenes genéricas de categoría. Cuando no hay foto exacta verificada, muestra un marcador pendiente.

Las imágenes del catálogo Commons se cargan desde Wikimedia Commons y requieren Internet. Cada foto muestra autor, licencia y enlace a la fuente. El catálogo incluye 35 fotografías específicas de los 38 destinos iniciales; Miramundo, Costa del Sol y Maculís quedan con marcador pendiente hasta localizar y verificar una fotografía exacta.

HOSPEDAJES
En la edición de cada lugar, usa el selector de la Biblioteca de Medios para asignar una imagen propia a cada hospedaje. La página de hospedajes y la ficha de cada destino muestran exclusivamente la foto asignada a ese establecimiento. Si no existe foto propia, muestran un marcador y no copian la fotografía del destino.

MIGRACIÓN
Si detecta las tablas antiguas wp_ruta_lugares y wp_ruta_categorias en la base activa, el plugin puede importar los registros al catálogo WordPress sin borrar las tablas originales. Antes de cualquier migración, realiza un respaldo.

CONTENIDO DOCUMENTAL
La página Nosotros incluye la introducción, objetivos, idea de negocio, misión, visión, valores, principios, políticas y anexos A-F basados en el documento entregado.
