Directorio de imágenes usadas en plantillas de correo
===============================================

Propósito
--------

Este directorio almacena las imágenes que se adjuntan (mediante PHP) a las plantillas de correo ubicadas en `src/resources/templates`.
Cada plantilla de correo puede tener imágenes asociadas que deben buscarse/adjuntarse por código cuando se genera el correo.

Estructura requerida
--------------------

Las imágenes deben organizarse en subdirectorios nombrados por fecha. Esa fecha corresponde a la fecha de creación o versión de la plantilla de correo a la que pertenecen.

Formato recomendado de nombre de carpeta: YYYY-MM-DD

Ejemplo de estructura:

src/resources/img/
├─ 2023-05-01/
│  ├─ logo.png
│  ├─ banner.jpg
│  └─ signature.png
├─ 2023-06-15/
│  ├─ header.png
│  └─ footer.jpg
└─ README.md

Convenciones y buenas prácticas
-------------------------------

- Nombres de carpetas: usar el formato ISO `YYYY-MM-DD` para facilitar búsquedas y orden cronológico.
- Nombres de archivos: usar nombres descriptivos y en minúsculas, separando palabras con guiones (`logo-header.png`), evitando espacios y caracteres especiales.
- Formatos aceptados: PNG, JPG/JPEG, GIF, SVG (según lo que acepte el motor de plantillas). Preferir PNG/JPEG para imágenes rasterizadas y SVG para logotipos vectoriales.
- Permisos: asegurar que el proceso PHP tenga permisos de lectura en estos ficheros. Evitar permisos globales abiertos (p.ej. 0777) en producción.
- Tratamiento de imágenes: si se generan thumbnails o versiones optimizadas, preferir almacenarlas en subdirectorios con sufijos como `2023-05-01/optimized/`.
- Seguridad: validar y sanear cualquier fichero subido antes de moverlo aquí. No confiar en la extensión; validar el mime type y/o procesar la imagen (p.ej. con `getimagesize()` o una librería de manipulación de imágenes).

Acceso desde el código PHP
--------------------------

Cuando se genere un correo, el código debe buscar las imágenes en el subdirectorio correspondiente a la plantilla. Por ejemplo:

1. Determinar la fecha/versión de la plantilla: `2023-05-01`.
2. Construir la ruta: `src/resources/img/2023-05-01/`.
3. Verificar existencia y leer los ficheros a adjuntar.

Ejemplo (pseudocódigo):

// $date = '2023-05-01';
// $dir = __DIR__ . '/../img/' . $date . '/';
// if (is_dir($dir)) { foreach (scandir($dir) as $f) { /* adjuntar $dir.$f si es imagen */ } }

Limpieza y gestión
-------------------

- Para evitar acumulación, definir una política de retención (p.ej. limpiar carpetas con más de N meses si ya no se usan).
- Registrar metadatos (qué plantilla/version usa cada carpeta) en la base de datos o en un manifiesto paralelo si es necesario.

Notas finales
-------------

Este README describe la convención de organización para facilitar el mantenimiento y la carga/adjunto de imágenes cuando se construyen correos desde plantillas en `src/resources/templates`.
