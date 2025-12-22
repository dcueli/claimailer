Directorio de plantillas de correo
=================================

Propósito
--------

Aquí se guardan las plantillas de correo en formato HTML. Cada plantilla se organiza en un subdirectorio que representa la fecha de creación/versión de la plantilla.

Formato de carpetas
-------------------

- El usuario indicó la nomenclatura `dd/mm/aaaa`. Atención: el carácter `/` no es válido en nombres de carpetas en la mayoría de sistemas de ficheros. Recomendamos uno de estos formatos válidos:
  - `dd-mm-aaaa` (ej. `09-10-2025`)
  - o preferible por orden cronológico y compatibilidad: `YYYY-MM-DD` (ej. `2025-10-09`).

Estructura recomendada
----------------------

src/resources/templates/
├─ 09-10-2025/
│  └─ mi-plantilla.html
├─ 15-06-2023/
│  └─ oferta.html
└─ README.md

Convenciones
------------

- Cada subdirectorio contiene una o más plantillas HTML relacionadas con esa fecha/versión.
- El nombre del fichero de plantilla puede ser libre (defínelo según su propósito), p.ej. `claim.html`, `newsletter.html`.
- Encodificación: guardar las plantillas en UTF-8.
- Recursos estáticos: las imágenes referenciadas desde una plantilla deberían apuntar a los ficheros en `src/resources/img/<fecha>/...` o incluir rutas absolutas/externas según la política del proyecto.

Buenas prácticas
----------------

- Mantener una convención de nombres clara para facilitar el mantenimiento.
- Versionado: si una plantilla cambia con frecuencia, crear nuevas carpetas por fecha para mantener el historial.
- Validación: comprobar que las plantillas HTML son válidas y que los enlaces a recursos (imágenes, CSS inline) funcionan antes de enviarlas.
