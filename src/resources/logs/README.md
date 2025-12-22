Directorio de logs de la aplicación
=================================

Propósito
--------

Este directorio almacena los ficheros de registro (logs) que la aplicación usa para llevar contador de envíos y registrar errores.

Archivos esperados
------------------

- `cont_log`
  - Contiene únicamente el contador del número de correos electrónicos enviados.
  - Valor inicial recomendado: `0` (un solo número en texto plano).
  - Uso típico: incrementarlo cada vez que la aplicación confirma el envío de un correo.

- `error_log`
  - Registro de errores, excepciones y avisos relevantes durante la ejecución.
  - Formato libre (texto plano), preferiblemente con marca temporal por línea: `YYYY-MM-DD HH:MM:SS - Mensaje`.

Buenas prácticas
----------------

- Asegurar permisos: el proceso PHP debe tener permisos de escritura en este directorio y en los ficheros (`cont_log` necesita escritura para incrementar el contador; `error_log` necesita escritura para anexar errores).
- Rotación: implementar rotación o archiving para `error_log` si puede crecer mucho (p.ej. rotar mensualmente o cuando supere cierto tamaño).
- Consistencia: al actualizar `cont_log` usar mecanismos atómicos o bloqueo (flock) para evitar condiciones de carrera en concurrencia.
- Codificación: usar UTF-8 para los mensajes en `error_log`.

Inicializar `cont_log`
---------------------

En Linux/Unix:

  echo 0 > src/resources/logs/cont_log

En PowerShell/Windows:

  Set-Content -Path 'src/resources/logs/cont_log' -Value '0' -Encoding UTF8

Seguridad
---------

- No exponer estos ficheros desde la web pública. Asegurar que el directorio no sea accesible vía HTTP.
- Registrar información útil, pero evitar volcar datos sensibles (credenciales, tokens completos, etc.).
