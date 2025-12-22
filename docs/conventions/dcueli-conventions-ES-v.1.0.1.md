██████╗░░█████╗░██╗░░░██╗███████╗██╗░░░░░██╗  
██╔══██╗██╔══██╗██║░░░██║██╔════╝██║░░░░░██║  
██║░░██║██║░░╚═╝██║░░░██║█████╗░░██║░░░░░██║  
██║░░██║██║░░██╗██║░░░██║██╔══╝░░██║░░░░░██║    
██████╔╝╚█████╔╝╚██████╔╝███████╗███████╗██║  
╚═════╝░░╚════╝░░╚═════╝░╚══════╝╚══════╝╚═╝ v1.0.1
# 🅲🅾🅽🆅🅴🅽🅲🅸🅾🅽🅴🆂 🅳🅴 🅳🅴🆂🅰🆁🆁🅾🅻🅻🅾

Documento que describe las convenciones y filosofías de desarrollo de software.

## Filosofía Central: Principio Estricto de Responsabilidad Única (SRP)

Mi enfoque de la arquitectura de software se basa en una interpretación estricta del Principio de Responsabilidad Única (SRP), priorizando una separación clara y tangible de responsabilidades sobre los estándares comúnmente aceptados por la comunidad.

### La Analogía del "Almacén" vs. El Patrón del "Contenedor"

Un patrón común en los marcos modernos es el "Contenedor", una única clase responsable tanto del registro (binding) como de la recuperación (resolución) de dependencias. Considero que esta es una abstracción defectuosa.

Mi razonamiento se basa en una analogía del mundo real: un almacén. En un almacén funcional, tienes roles distintos:
1.  **Recepción:** Personal que recibe mercancía entrante.
2.  **Gestión Interna:** Personal que organiza y almacena la mercancía en el almacén.
3.  **Entrega/Envío:** Personal que recupera la mercancía y la prepara para envío.

Estas son responsabilidades separadas. No tiene sentido agruparlas en una única entidad. De manera similar, en código:
-   **Registrar** una dependencia es como **recepción**.
-   **Almacenar** la dependencia es **gestión interna**.
-   **Resolver/obtener** una dependencia es como **entrega**.

Por lo tanto, defiendo la separación de estas responsabilidades en clases o componentes distintos. Una clase que registra una dependencia no debe ser responsable también de recuperarla. Esto mantiene una arquitectura limpia, lógica y altamente especializada donde cada componente hace una cosa y la hace bien.

### Crítica del Sobre-Ingeniería y Estándares de "Comunidad"

Soy crítico con lo que percibo como complejidad innecesaria introducida por "comunidades de desarrolladores" anónimas. Los estándares deben ser cuestionados, no seguidos ciegamente.

-   **Sobre TypeScript:** Veo tecnologías como TypeScript como una capa innecesaria de complejidad sobre JavaScript. A menudo sirve como muleta para desarrolladores acostumbrados a lenguajes fuertemente tipados como Java, impidiéndoles abrazar los paradigmas únicos orientados a funciones de JavaScript. La necesidad de transpilar código añade un paso extra, a menudo superfluo, al proceso de desarrollo.

-   **Sobre Lógica:** La arquitectura del código debe seguir lógica tangible del mundo real en lugar de convenciones abstractas. Si un patrón no tiene sentido en un escenario físico del mundo real (como el almacén), es probable que sea una abstracción defectuosa para código.

Esta filosofía debe aplicarse a todos los proyectos, asegurando un código consistente, lógico y mantenible.

---

## Convenciones de Nombres

Todos los nombres siguen convenciones estrictas para claridad, consistencia y significado funcional:

### Métodos y Funciones
- **Métodos Generales:** `PascalCase` (ej., `CheckClient`, `Reset`, `Terminate`, `SetMapping`)
- **Métodos Getter:** `camelCase` con prefijo `get` (ej., `getError`, `getServerExtList`)
- **Métodos Setter:** `camelCase` con prefijo `set` (ej., `setCredentials`, `setDriver`, `setRecipients`)
- **Excepción:** Métodos que realizan I/O o tienen efectos secundarios usan `PascalCase` de todas formas (ej., `SendWithPHPMailer`)

### Variables
- **Variables Locales e Instancia:** `camelCase` (ej., `$mailService`, `$client`, `$realClient`, `$endtime`)
- **Inicialización de Propiedades:** `camelCase` (ej., `$bEnable`, `$counterFilePath`)

### Variables Booleanas/Banderas
- **Notación Húngara para Banderas:** Prefijo `b` en `camelCase` (ej., `$bReMapping`, `$bValid`, `$bEnable`, `$bBooted`)
- **Propósito:** Señala inmediatamente a los desarrolladores que una variable contiene un estado booleano

### Constantes
- **Todas las Constantes:** `UPPER_SNAKE_CASE` (ej., `DEFAULT_TIMEOUT`, `MAX_LINE_LENGTH`, `DEBUG_OFF`)

### Clases y Enums
- **Estándar:** `PascalCase` (ej., `PHPMailer`, `MailClientProvider`, `Configurator`)
- **Interfaces:** `PascalCase` con prefijo `I` (ej., `ISingleton`, `IMailer`)
- **Enums:** `PascalCase` (ej., `MailerClientTypes`, `PotentialRecipientsTypes`)
- **Traits:** `PascalCase` con sufijo `Trait` (ej., `SingletonTrait`, `ToStringTrait`)

---

## Implementación del Patrón Singleton

### Interfaz ISingleton
- Requiere: `public static function init(): static;`
- Propósito: Hook de inicialización para singletons después de la instanciación
- Instancia única almacenada en: `static::$instance` (de `SingletonTrait`)

### Uso de SingletonTrait
- Proporciona: `getInstance()`, `resetInstance()`, prevención de clonación/deserialización
- Método Reset: `protected static function resetInstance(): void` (llamado por envoltorios públicos `Reset()`)
- Método envolorio público: `public static function Reset(): void` (sigue PascalCase para métodos estáticos)

### Ciclo de Vida de la Aplicación
- **Al Inicio:** `getInstance()` crea instancia única, `init()` se llama automáticamente
- **Al Terminar:** `Reset()` limpia la instancia, permitiendo inicialización fresca en el siguiente Job
- **Caso de Uso:** Hosting Jobs que llaman `index.php` repetidamente (ej., tareas cron)

---

## Arquitectura de Cliente SMTP/Mail

### Patrón Envolorio de Cliente
- **Ubicación del Envolorio:** `App\Providers\Mail\Clients\{ClientName}` (ej., `PHPMailer`, `SymfonyMailer`, `SwiftMailer`)
- **Propósito del Envolorio:** Abstrae librerías de mail de terceros, implementa interfaz `IMailer`
- **Referencia del Cliente Real:** Almacenada en propiedad `->mailer` con getter/setter de PHP 8.4

### Configuración de PHPMailer
- **Configuración de Timeout** (ambas requeridas para prevenir timeout de ejecución):
  - `$client->mailer->Timeout = 30;` — Timeout de lectura/escritura de socket
  - `$client->mailer->getSMTPInstance()->Timelimit = 30;` — Timeout de `stream_select()` usado en `get_lines()`
- **Fuente de Configuración:** `Config::g('mailtimeout', DEFAULT_TIMEOUT)` permite fallback a constante
- **Valor Predeterminado:** `DEFAULT_TIMEOUT = 30` (segundos)

### Manejo de Timeout
- **Problema:** PHPMailer puede colgarse indefinidamente si el servidor es lento, excediendo el `max_execution_time` de PHP
- **Solución:** Establecer ambos `Timeout` y `Timelimit` al mismo valor para forzar un tiempo de espera máximo
- **Ubicación:** Configurar en método `setDriver()` del envolorio de cliente durante setup SMTP

---

## Limpieza de Aplicación y Gestión de Recursos

### Método Terminate (Application)
Toda la limpieza de recursos ocurre en `Application::Terminate()`:
1. **Limpiar Cachés de Proveedores:** `MailClientProvider::ClearCache()` — vacía array estático `$map`
2. **Reset Configurador:** `\App\Providers\Configurator::Reset()` — limpia singleton de configuración
3. **Cerrar Conexiones de Mail:** Intenta cerrar conexión SMTP via reflection (mejor esfuerzo)
4. **Reset Aplicación:** `static::Reset()` — limpia instancia singleton de aplicación
5. **Opcional:** Establecer timestamp `$autosender_mail_end` si se necesita timing

### Patrón Reset de Método Estático
- **Envolorio Público:** `public static function Reset(): void` (PascalCase)
- **Llamada Interna:** `self::resetInstance()` (de `SingletonTrait`)
- **Propósito:** Permite reset explícito entre ejecuciones de Job

---

## Validación de Arrays y Datos

### Patrones de Verificación Vacía
- **Preferencia:** `empty()` sobre `!count()` o `!(bool)[]` por legibilidad
- **Razón:** `empty()` es más rápido, más idiomático y igualmente claro
- **Coalescencia Nula:** `$var ??= DEFAULT_VALUE` para fallbacks simples
- **Triple Negación:** `!!!condition` evitado a favor de `!condition` o aserciones positivas

### Mapeo de Destinatarios y Mail
- **Mapeo Basado en Enum:** Enum `PotentialRecipientsSetters` almacena nombres de métodos como strings separados por pipe (ej., `'addAddress|setTo'`)
- **Detección de Métodos:** `method_exists($client, $method)` verificado contra **objeto cliente real** (no envolorio)
- **Almacenamiento de Mapeo:** Array estático `$map` cachea nombres de métodos por tipo de destinatario, limpiado en `ClearCache()`

---

## Características de PHP 8.4

### Accesores de Propiedades (Propiedades Tipadas con Getters/Setters)
- **Sintaxis:** `public object $mailer { get; set; }`
- **Propósito:** Encapsulación sin boilerplate de métodos getter/setter
- **Uso:** Implementado en clases envolorio (ej., `PHPMailer`, `SymfonyMailer`)
- **Nota:** Mantener declaraciones de interfaz simples; permitir que implementaciones manejen detalles de accesores

---

## Estilo de Código y Filosofía

### Comentarios y Documentación
- Usar comentarios claros y accionables explicando **por qué**, no solo **qué**
- Comentarios en español aceptables donde el contexto del código es específico del español
- Valores configurables preferidos sobre valores hardcodeados

### Minimizar Variables Temporales
- Evitar variables intermedias innecesarias (ej., `$candidates`, `$tmp`, `$available`)
- Operaciones inline donde la lógica es clara y de un solo paso
- Romper solo cuando múltiples usos o lógica compleja justifiquen la variable

### Llaves para Condicionales y Bucles

- **Ramas de una línea:** Para `if`, `if/else`, `if/elseif`, `if/elseif/else` donde cada rama es una única declaración en una línea, omitir las llaves de apertura `{` y cierre `}`. Ejemplo:

  ```php
  if ($cond) return $value;
  ```

- **Ramas de múltiples líneas:** Si cualquier rama contiene más de una declaración o abarca múltiples líneas, usar llaves para *todas* las ramas en ese condicional. Esto mantiene el condicional inequívoco y consistente. Ejemplo:

  ```php
  if ($cond) {
      doFirstThing();
      doSecondThing();
  } else {
      doOther();
  }
  ```

- **Bucles:** La misma regla aplica a estructuras de bucle (`for`, `foreach`, `while`, `do-while`): omitir llaves solo para cuerpos de una sola línea; incluir llaves cuando el cuerpo del bucle tiene múltiples declaraciones.

- **Excepciones / llaves requeridas:** Usar llaves cuando la construcción del lenguaje lo requiere (ej., bloques `switch`/`match`) o cuando incluir llaves mejora la legibilidad y reduce errores potenciales de ediciones futuras.

- **Razón:** Esta política reduce ruido visual para one-liners simples mientras refuerza límites de bloques explícitos cuando una rama contiene más de una declaración, mejorando mantenibilidad y reduciendo bugs introducidos por indentación ambigua.

### Consideraciones de Rendimiento
- **Cacheo Estático:** Cachear valores computados (listas de métodos, mapeos) en arrays estáticos, limpiados al final del ciclo de vida
- **Referencia Directa vs. Copia:** Objetos pasados por referencia solo cuando modificación intencional es necesaria; usar asignación de valor para iteración
- **Verificaciones de Existencia de Métodos:** Realizar una vez y cachear; no repetir por iteración

---

## Pruebas y Validación

### Hostname y Configuración
- Permitir fallback a `Config::g()` para todas las configuraciones externas
- Usar constantes como defaults (ej., `Config::g('mailtimeout', self::DEFAULT_TIMEOUT)`)
- Validar presencia antes de usar, lanzar excepciones descriptivas con timestamps

*-- Este documento refleja las opiniones y directivas arquitectónicas fijadas en dcueli.com, actualizado durante el desarrollo. --*
