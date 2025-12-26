<p align="center"><img src="docs/logo.png" alt="Claimailer Logo" width="600"/></p>
<h1 align="center">Claimailer</h1>
<p align="center"><strong>Un framework de correo para PHP moderno, ligero y extensible.</strong></p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-%3E%3D8.2-8892BF?style=for-the-badge&logo=php" alt="PHP Version">
  <img src="https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge" alt="License">
  <img src="https://img.shields.io/badge/Version-2.1.0-blue?style=for-the-badge" alt="Version">
</p>

## 📄 Descripción y características
### ℹ️ ¿Qué resuelve? La necesidad de desarrollo

La mera existencia de este servicio surgió de la necesidad del envío de correos electrónicos, no masivamente, pero sí quizás un poco intrusivo, a una administricación no pública pero gestionada con dinero público.

La administración, la cual no voy a revelar su nombre para evitar posibles problemas (todos sabemos cómo funciona esto), tenía o tiene, si no lo han quitado ya, un buzón electrónico a donde enviar asuntos relativos a quejas o solicitudes de reparaciones, de ahí el nombre del servicio. Este buzón debería responder de forma automática al recibir un correo, diciendo que se ha recibido la solicitud y que se está procesando, si no lo hace a estas alturas.

Yo escribí una petición, con toda la educación y el respeto del mundo como hago siempre la Primera y Segunda vez, y envié el correo electrónico a dicho buzón. Al cabo de un tiempo prudencial, no sé cinco días, una semana o diez días, como siempre hago, envío el segundo correo electrónico comentando que éste es el segundo correo electrónico que envío porque no he recibido ninguna respuesta. Vuelvo a esperar unos días, pero esta vez, nunca espero más de tres.

Como tampoco obtuve ninguna respuesta, volví a enviar un tercer correo electrónico, pero esta vez con un tono no tan cordial y comentando que iba a enviar un correo electrónico cada 15 minutos hasta obtener respuesta, ya que mi situación requería de cierta urgencia, que tampoco viene al caso comentar, que el que lea esto piense lo que quiera.

Ya que todas las personas en la faz de La Tierra sabemos cómo funcionan los funcionarios o las funcionarias de la administración pública, mal que les pese, aquí "**_se paga justos por pecadores_**", y el que se sienta **_aludido_** o **_aludida_**, será porque algo mal estará haciendo, y cómo no, son unos "**_ofendiditos_**" u "**_ofendiditas_**". Aprovecho para avisar que ya estoy harto de indicar el género y voy a escribir únicamente en uno, el genérico, que para las "**_ofendiditas_**", como bien explica la RAE es el masculino.
Volviendo a **la necesidad**, no obtuve respuesta hasta el quinto o sexto correo electrónico, pero fíjate por donde, ya repsondieron a mi solicitud diciendo que van a empezar a procesar la solicitud y de paso pidiendo por favor que no recibieran más correos.  
Para la persona que esté pensando que estaba colapsando el buzón de correo, debería pensar un poco más en que ese buzón seguramente ya estaría colapsado por la falta de eficiencia y competencia de esos funcionarios públicos.

Todo esto es para contar la necesidad por la que me surgió y por qué desarrollé este servicio.

Muchos "**_listillos_**" en la "_comunidad de desarrollo_", no sólo de software y no solo desarrolladores, analistas, tech leads, PM's, etc; pensarán que para qué desarrollar un servicio que haga esto cuando ya hay proveedores de correo electrónico, véase Google, Microsoft, Yahoo etc, etc, etc; done puedes programar un envío de correo electrónico e incluso enviadores de listas de correo electrónico como pueden ser Mailchimp u otros. A estas "**_maravillosísimas personas_**" decirles que bueno, desarrolle este servicio únicamente **porque puedo** y **punto** no hay que darle más vueltas, sin depender de servicios de cuartos, quintos y demás, como todos los anteriormente comentados.

### 📚 ¿Qué hace?

Simplemente es un software de script, desarrollado en PHP, que puede ser ejecutado como Job o como Servicio para el envío de correos electrónicos sencillos al que únicamente hay que indicar:

1. _desde que buzón enviar, con sus credeciales_,
2. _a qué direcciones de buzones enviar, incluyendo la posibilidad de hacerlo "Con Copia" (CC) o "Con Copia Oculta" (BCC)_,
3. _y qué enviar, esto es el mensaje, incluido la posibilidad de archivos adjuntos_.

**_Utiliza, obligatoriamente, librerías (o componentes) de envío de correo en PHP para poder reemplazar o mejorar el uso directo de la función mail() de PHP y gestionar así, SMTP, adjuntos, HTML, etc._**

Por defecto utiliza **PHPMailer**, aunque cuando se instala la fuente para desarrollo (`> composer install`), a parte de PHPMailer, también instala **Symfony Mail**, aunque se puede instalar otras _al gusto del consumidor_. Para usar estas librerías hay que desarrollar su componente o consumidor (_consumer class_) de Claimailer con su mismo nombre, por ejemplo, si se va a usar la librería PHPMailer, como desarrollador deberemos crear **_PHPMailer.php_** en la ubicación `src/app/providers/mail/clients/` (esto posiblemente se modifique en versiones posteriores).

Si queremos usar Symfony Mail deberíamos crear `src/app/providers/mail/clients/`**_SymfonyMailer.php_**.
> **Nota**: aunque para este consumidor de la librería, su desarrollo está comenzado, para las siguientes versiones habría que terminarlo.

### 🧩 Características principales

- La arquitectura del proyecto se aleja de estándares abstractos para seguir una lógica más coherente, más categórica y vehemente a la par que flexible, al igual que su idioma de desarrollo ya que cada desarrollador, como los escritores, deberían tener su estilo propio de escritura.
- **Licencia MIT**: Se puede hacer "casi" lo que a uno le venga en gana, o lo que se salga de los coj***s, excepto apropiarse del código fuente original. La atribución es obligatoria.
- **Cero Responsabilidad**: a ver si lo digo clarito para que se entienda. "*NI DE COÑA ME HAGO P**O RESPONSABLE DE LO QUE CADA UNO HAGA CON ESTO*", deberíamos ser ya lo bastante responsables como para saber qué hacer o no, aunque la maldad del ser humano es intrínseca e innata a su ser.
- **Flexibilidad de Ejecución**: Diseñado para operar tanto como un Job para ejecuciones puntuales o como un **Servicio** (continuo) según las necesidades de tu infraestructura.
- **Configuración Simple y Centralizada**: Toda la lógica de envío, la gestión decredenciales, destinatarios, plantillas, etc; se gestiona desde el archivo de configuración y el `index`, permitiendo un uso de "configurar y olvidar".
- **Sin Dependencias de Base de Datos**: Su funcionamiento es autónomo y no requiere de ninguna conexión o sistema de base de datos, al menos de momento, creo que esto es más un TODO que una característica, pido disculpas por ello, pero así se queda. De momento...
- Es obligatorio tener una cuenta de correo para que el "_algoritmo_", ahora que está de moda la palabreja otra vez gracias a la IA, pueda enviar correos.
- **Patrones de diseño**: El núcleo del sistema está construido siguiendo principios de software SOLID y aplicando un conjunto de patrones de diseño para garantizar un código desacoplado, mantenible y escalable. Singleton, Factory, Facade, Adapter, Strategy, Inyección de Dependencias (DI) y DTOs (Data Transfer Objects), entre otros como son **Strategy** para permitir intercambiar el cliente de envío de correo PHPMailer, Symfony Mailer, o el que te haya dado la gana instalar, sin alterar el servicio que lo consume. También **Adapter/Wrapper** para abstraer y adaptar las librerías externas a una interfaz común.
- **Gestión Eficiente de Recursos**: Incluye un ciclo de vida (`Boot`, `Reset`, `Terminate`) para asegurar un rendimiento óptimo en ejecuciones recurrentes que priorizan el rendimiento, la lógica interna y la claridad sobre esos estándares estúpidos de la "comunidad".
- **Convenciones de Código Propias y Documentadas**: El proyecto sigue un conjunto estricto y pragmático de convenciones (ver `docs/conventions`).

## 💡 ¿Por qué puedes necesitar esto?

### Casos de uso
1. **Envío periódico de notificaciones con configuración fija**  
   Job o Servicio que ejecuta repetidamente el envío de una plantilla HTML predefinida a destinatarios fijos. Ideal para reclamaciones repetitivas, alertas sistemáticas, confirmaciones automáticas o avisos recurrentes donde el contenido y destinatarios no varían.
2. **Control local sin terceros**  
   Cualquier aplicación que requiera envío de correo programado completamente autónomo, sin necesidad de APIs de servicios como SendGrid, Mailchimp o AWS SES—evitando costos y dependencias externas.

### Ventajas clave
- **Configuración centralizada y simple**: Todo se define en archivos de config (plantilla, asunto, destinatarios, remitente, credenciales SMTP). Una vez establecido, ejecuta automáticamente.
- **Bajo costo operacional**: Solo necesita una cuenta SMTP válida (cualquier proveedor). Sin suscripciones, sin pago por volumen, sin intermediarios financieros.
- **Independencia total**: Control sobre el servidor SMTP propio, sin depender de APIs de terceros que cambian, desaparecen o imponen límites de envío.
- **Registro transparente**: Contador automático de envíos y almacenamiento de historial de correos en archivos (sin base de datos).
- **Flexible en infraestructura**: Funciona como Job único (ejecución puntual) o como Servicio continuo según necesidad.
- **Múltiples proveedores**: Soporta PHPMailer, Symfony Mailer y otros mediante adaptadores—intercambiables sin cambiar lógica del núcleo.

## 🏗️ Arquitectura

Como no puede ser de otra manera, Claimailer se encuentra basado en los principios SOLID, pero no aplicados desde su forma más dogmática, sino que se adapta o se filtra a la propia visión del desarrollador. El punto clave es la visión del desarrollador,  modificando los principios para mejorar las carcterísticas, rendimiento, funcionalidad y legibilidad propias de cada colaborador, respetando así el estilo de escritura, tono, enfoque y discurso al proyecto.


### 1. Principio de Responsabilidad Única (SRP)
Este es el principio central con el que se guía el diseño de software. La arquitectura del sistema de Inyección de Dependencias (DIC) evita, lo que actualmente se viene observando en los desarrollos, ese "Contenedor monolítico" que mezcla responsabilidades.

- **`Register`**: Su única función es registrar servicios (la "recepción").
- **`Resolver`**: Su única función es almacenar y recuperar los servicios creados por `Register`.
- **`Container`**: Actúa como un simple almacén de clave-valor. Orquesta a los dos anteriores sin conocer los detalles de su implementación.

### 2. Principio de Abierto/Cerrado(OCP)
El sistema está diseñado para ser extensible sin necesidad de modificar el código existente. El mejor ejemplo es el **_MailClientProvider_** con el que se añadie nuevos clientes de correo (como una implementación para SendGrid o Mailgun) creando simplemente un nuevo wrapper que implemente la interfaz IMailer. El núcleo del servicio de envío permanece inalterado.

### 3. Principio de Sustitución de Liskov (LSP)
Gracias al uso de contratos (interfaces), cualquier implementación concreta puede ser sustituida por otra sin afectar al sistema. Por ejemplo, el MailClientService opera con la interfaz IMailer, por lo que puede usar PHPMailer, SymfonyMailer o cualquier otro cliente futuro de forma intercambiable.

### 4. Principio de Segregación de Interfaces (ISP)
Se favorecen las interfaces pequeñas y específicas sobre las grandes y genéricas. En src/app/contracts/interfaces se pueden encontrar ejemplos como IGetter, ISetter, IRecipient, que permiten a las clases implementar únicamente los comportamientos que necesitan, evitando la carga de métodos innecesarios.

### 5. Principio de Inversión de Dependencia (DIP)
Los módulos de alto nivel no dependen de los de bajo nivel; ambos dependen de abstracciones. La clase Application y los servicios no dependen directamente de PHPMailer, sino de la interfaz IMailer. Es el contenedor DIC el que se encarga de "inyectar" la implementación concreta en tiempo de ejecución.

### Otros Patrones de Diseño Relevantes
Singleton Personalizado: Se utiliza un patrón Singleton a través del SingletonTrait y la interfaz ISingleton, con un ciclo de vida init()/Reset() diseñado específicamente para entornos donde el script se ejecuta de forma repetida (como un Job o tarea programada), asegurando un estado limpio en cada ejecución.
Wrapper (Adaptador): Los clientes de correo de terceros son envueltos en clases adaptadoras (src/app/providers/mail/clients) que abstraen su complejidad y unifican su comportamiento bajo la interfaz común IMailer.





Claimailer se basa en una interpretación estricta del **Principio de Responsabilidad Única**. Esto se refleja en su núcleo:

- **`Register`**: Responsable únicamente de registrar (vincular) interfaces o claves a implementaciones concretas.
- **`Container`**: Su única función es almacenar y recuperar las vinculaciones creadas por el `Register`. Actúa como un simple almacén de clave-valor.
- **`Resolver`**: Es el único que sabe cómo construir (resolver) un objeto, manejando sus dependencias a través de la reflexión.

Este enfoque, a diferencia de contenedores DIC más comunes que mezclan estas responsabilidades, garantiza un sistema más predecible y fácil de depurar.

## 🧱 Estructura

**Claimailer**  
├─ `📁 .github/` → _Configuraciones de GitHub (workflows CI/CD, templates de PR, etc.)_  
├─ `📁 docs/` → _Documentación adicional, diagramas, convenciones de desarrollo_  
├─ `📁 docker/` → _[Opcional] Archivos de configuración de contenedores, `Dockerfile`, `docker-compose.yml`, etc._  
├─ `📁 src/` → _Código fuente principal del Job o Servicio_  
│  ├─ `📁 app/` → _Lógica de aplicación, orquestación y servicios_  
│  │  ├─ `📄 Application.php`  
│  │  │  └─ _Clase singleton central; orquesta Container, Register, Resolver; inicializa servicios, configuración y ciclo de vida (Boot, Reset, Terminate)_  
│  │  │  
│  │  ├─ `📁 container/` → _Inyección de dependencias (DI) con separación SRP_  
│  │  │  ├─ `📄 Container.php`  
│  │  │  │    └─ _Almacena bindings de servicios (get, Has)_  
│  │  │  │  
│  │  │  ├─ `📄 Register.php`  
│  │  │  │    └─ _Registra/vincula servicios al contenedor (Bind)_  
│  │  │  │  
│  │  │  └─ `📄 Resolver.php`  
│  │  │       └─ _Resuelve dependencias a partir de bindings_  
│  │  │  
│  │  ├─ `📁 contracts/` → _Interfaces y clases abstractas_  
│  │  │  ├─ `📁 interfaces/` → _Definiciones de contratos (ISingleton, IMailer, etc.)_  
│  │  │  └─ `📁 abstracts/` → _Clases base con lógica común (Getters, excepciones base)_  
│  │  │  
│  │  ├─ `📁 providers/` → _Proveedores de servicios (adaptadores para librerías externas)_  
│  │  │  └─ `📁 mail/` → _Proveedor de envío de correo_  
│  │  │     ├─ `📄 MailClientProvider.php`  
│  │  │     │  └─ _Selector y envoltor de clientes de mail; mapea tipos de destinatarios a métodos según proveedor_  
│  │  │     │  
│  │  │     ├─ `📁 clients/` → _Adaptadores para librerías de terceros_  
│  │  │     │  ├─ `📄 PHPMailer.php`  
│  │  │     │  │  └─ _Envoltorio/Consumider para PHPMailer (send, config SMTP)_  
│  │  │     │  │  
│  │  │     │  └─ `📄 SymfonyMailer.php`  
│  │  │     │     └─ _Envoltorio/Consumidor para Symfony Mailer (en desarrollo)_  
│  │  │     │  
│  │  │     ├─ `📁 dtos/` → _Objetos de transferencia de datos_  
│  │  │     │  ├─ `📄 MailDataDTO.php`  
│  │  │     │  │  └─ _Datos de correo (destinatarios, asunto, cuerpo)_  
│  │  │     │  │  
│  │  │     │  └─ `📄 MailServerDTO.php`  
│  │  │     │     └─ _Configuración SMTP (host, puerto, credenciales)_  
│  │  │     │  
│  │  │     ├─ `📁 factories/` → _Construcción de objetos DTOs_  
│  │  │     │  ├─ `📄 MailDataFactory.php`  
│  │  │     │  │  └─ _Crea MailDataDTO desde array_  
│  │  │     │  │  
│  │  │     │  └─ `📄 MailServerFactory.php`  
│  │  │     │     └─ _Crea MailServerDTO desde array_  
│  │  │     │  
│  │  │     └─ `📁 types/` → _Enums y tipos de datos_  
│  │  │        ├─ `📄 MailerClient.php`  
│  │  │        │  └─ _Enum de clientes disponibles_  
│  │  │        │  
│  │  │        ├─ `📄 MailServerConfig.php`  
│  │  │        │  └─ _Enum de configuración SMTP_  
│  │  │        │  
│  │  │        ├─ `📄 PotentialRecipientsTypes.php`  
│  │  │        │  └─ _Tipos de destinatarios (to, cc, bcc)_  
│  │  │        │  
│  │  │        └─ `📄 PotentialRecipientsSetters.php`  
│  │  │           └─ _Mapeo métodos/proveedor_  
│  │  │  
│  │  ├─ `📁 services/` → _Servicios de aplicación (orquestación de alto nivel)_  
│  │  │  ├─ `📁 config/`  
│  │  │  │  └─ `📄 ConfigurationService.php`  
│  │  │  │     └─ _Carga y gestiona configuración global_  
│  │  │  │  
│  │  │  └─ `📁 mail/`  
│  │  │     └─ `📄 MailClientService.php`  
│  │  │        └─ _Orquestador principal de envío de correo; gestiona MailClientProvider, DTOs, contador de envíos, logging, limpieza de recursos_  
│  │  │  
│  │  └─ `📁 support/` → _Utilidades, helpers, traits y extensiones_  
│  │     ├─ `📁 exceptions/`  
│  │     │  ├─ `📄 Basexception.php`  
│  │     │  │  └─ _Excepción base personalizada_  
│  │     │  │  
│  │     │  └─ `📄 FileContentNotValidException.php`  
│  │     │     └─ _Excepción para archivos inválidos_  
│  │     │  
│  │     ├─ `📁 facades/`  
│  │     │  ├─ `📄 Facade.php`  
│  │     │  │  └─ _Clase base para patrones Facade_  
│  │     │  │  
│  │     │  ├─ `📄 Config.php`  
│  │     │  │  └─ _Acceso estático a configuración global_  
│  │     │  │  
│  │     │  └─ `📄 Logger.php`  
│  │     │     └─ _Acceso estático a logging_  
│  │     │  
│  │     ├─ `📁 helpers/`  
│  │     │  ├─ `📄 Helpers.php`  
│  │     │  │  └─ _Funciones generales de utilidad_  
│  │     │  │  
│  │     │  ├─ `📄 Arr.php`  
│  │     │  │  └─ _Operaciones sobre arrays_  
│  │     │  │  
│  │     │  ├─ `📄 Str.php`  
│  │     │  │  └─ _Operaciones sobre strings_  
│  │     │  │  
│  │     │  ├─ `📄 Logger.php`  
│  │     │  │  └─ _Gestión de logs (archivo y consola)_  
│  │     │  │  
│  │     │  └─ `📄 Summary.php`  
│  │     │     └─ _Funciones globales (autoload files)_  
│  │     │  
│  │     ├─ `📁 traits/`  
│  │     │  ├─ `📄 SingletonTrait.php`  
│  │     │  │  └─ _Implementa patrón Singleton_  
│  │     │  │  
│  │     │  └─ `📄 LogSaverTrait.php`  
│  │     │     └─ _Logging reutilizable en clases_  
│  │     │  
│  │     └─ `📁 types/`  
│  │        └─ `📄 LogLevels.php`  
│  │           └─ _Enum de niveles de log (INFO, WARNING, ERROR)_  
│  │  
│  ├─ `📁 bootstrap/` → _Inicialización de la aplicación_  
│  │  └─ `📄 App.php` → Entry point: crea Application singleton, carga registro de servicios, configura aplicación, inicia Logger, y maneja excepciones antes de inicializar sistema de logging  
│  │  
│  ├─ `📁 config/` → _Archivos de configuración_  
│  │  ├─ `📄 Config.php`  
│  │  │  └─ _Configuración global: VERSION, DEBUG, LANGUAGE, HOSTNAME, rutas, MAILING (credenciales SMTP, driver, opciones de log)_  
│  │  │  
│  │  ├─ `📄 Flags.php`  
│  │  │  └─ _Definiciones globales: constantes de modo de log (SILENCE, PRINT, FILE)_  
│  │  │  
│  │  └─ `📄 Services.php`  
│  │     └─ _Registro de servicios (bindings): facades y clases de soporte_  
│  │  
│  └─ `📁 resources/` → _Recursos y plantillas_  
│     ├─ `📁 img/` → _Imágenes del proyecto_  
│     ├─ `📁 logs/`  
│     │  ├─ `📄 Cont_log`  
│     │  │  └─ _Contador de correos enviados_  
│     │  │  
│     │  ├─ `📄 Error_log`  
│     │  │  └─ _Log de errores_  
│     │  │  
│     │  └─ `📄 README.md`  
│     │     └─ _Descripción de logs_  
│     │  
│     └─ `📁 mail/`  
│        ├─ `📁 sent/` → _Registro de correos enviados en formato de archivos de texto con la estructura `mail_[Fecha]_[Hora]_[AsuntoEnFormatoPascalCase](n.[Número de envío]).txt`_  
│        └─ `📁 templates/` → _Plantillas HTML de correo_  
│           └─ `📄 README.md`  
│              └─ _Descripción de logs_  
│  
├─ `📁 test/` → _Pruebas y herramientas de desarrollo_  
├─ `📁 vendor/` → _Dependencias instaladas por Composer (PHPMailer, Symfony Mailer, etc.)_  
├─ `📄 codecov.xml`  
│  └─ _Configuración de cobertura de código_  
├─ `📄 composer.json`  
│  └─ _Manifiesto de dependencias y autoload PSR-4_  
├─ `📄 index.php`  
│  └─ _Entry point (define constantes, autocarga, inicia bootstrap)_  
├─ `📄 phpstan.neon`  
│  └─ _Configuración de análisis estático (PHPStan)_  
├─ `📄 phpunit.xml`  
│  └─ _Configuración de pruebas unitarias (PHPUnit)_  
├─ `📄 README.md`  
│  └─ _Documentación principal (Este archivo)_  
├─ `📄 VERSION`  
│  └─ _Archivo con el número de la última versión_  
│  
└─ **_[otros archivos de configuración]_**  

## 📋 Requisitos

- **PHP >= 8.2**

## ⚙️ Uso e instalación

### 🟢 Instalación para usar como Job o Servicio

Pensada para personas/equipos que solo necesitan **usar** el servicio (no modificar su código).

1. Requisitos previos
   - Lenguaje / runtime (por ejemplo, Node.js, Java, .NET, etc.).
   - Docker / Docker Compose (si aplica).
   - Variables de entorno necesarias.

2. Pasos básicos

3. Verificación
   - Endpoint de health-check: `GET /health` (por ejemplo).
   - Logs esperados al iniciar.
   - Códigos de respuesta esperados.

---

### 🛠️ Instalación para desarrollo y ampliación de funcionalidad

Pensada para **desarrolladores** que van a tocar el código, extenderlo o depurarlo.

1. Requisitos adicionales
   - Dependencias de desarrollo (por ejemplo, Node.js vXX, Java JDK, Docker, make, etc.).
   - Herramientas recomendadas (por ejemplo, VS Code + extensiones X, Y).

2. Entorno de desarrollo
/> git clone cd cp .env.example .env npm install # o el gestor de paquetes de tu stack npm run dev # o script equivalente para modo desarrollo

3. Tests y calidad

4. Guía rápida de contribución
   - Rama principal: `main` / `master`.
   - Flujo de ramas: `feature/*`, `fix/*`, etc.
   - Reglas mínimas de PR: tests pasando, cobertura mínima, revisión al menos de 1 miembro, etc.

## 🚀 Uso Básico

Aquí tienes un ejemplo completo de cómo enviar un correo. Claimailer se encarga de la complejidad interna.

```php
<?php
declare(strict_types=1);

```

## 📄 Licencia

Este proyecto se distribuye bajo los términos de la **Licencia MIT**.

Esta licencia te concede una amplia libertad para hacer casi cualquier cosa que quieras con el software, incluyendo:
- **Usar** el software para cualquier propósito, incluso comercial.
- **Modificarlo** para adaptarlo a tus necesidades.
- **Distribuirlo** libremente.
- **Sublicenciarlo** e incluso **venderlo** como parte de un producto tuyo.

La única condición fundamental es que **el aviso de copyright original y el texto de esta licencia deben incluirse** en todas las copias o partes sustanciales del software.

Además, el software se proporciona "tal cual", **sin ninguna garantía**, y los autores no son responsables de ningún daño derivado de su uso.

Para consultar el texto completo y legal, revisa el archivo `LICENSE` que acompaña al proyecto.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor, abre un *issue* para discutir los cambios propuestos o envía directamente una *pull request* adhiriéndote a las convenciones de código del proyecto.









Resumen (ES):
- Código PHP orientado a servicios de mailing y proveedores (PHPMailer, wrappers, configurador).
- Sigue las convenciones de desarrollo de Dcuéli: ver [dcueli-conventions-ES-v.1.0.1.md](dcueli-conventions-ES-v.1.0.1.md).
- Instrucciones para la IA y reglas de modificación: ver [AI-Instructions.md](AI-Instructions.md).

Quick summary (EN):
- PHP project for mail services and providers (wrappers for third-party mailers).
- Follow Dcuéli development conventions: see [dcueli-conventions-ES-v.1.0.1.md](dcueli-conventions-ES-v.1.0.1.md).
- AI usage and file-modification rules: see [AI-Instructions.md](AI-Instructions.md).

Estructura relevante:
- `src/` — código fuente principal (app, providers, services, support...)
- `resources/mail/templates` — plantillas de correo
- `vendor/` — dependencias (composer)

Cómo empezar (local):
1. Instalar dependencias: `composer install`
2. Ajustar configuración en `src/config/📄 Config.php` o variables de entorno según sea necesario
3. Ejecutar scripts o probar en un entorno PHP (CLI o servidor web)

Notas:
- No modificar archivos del proyecto sin autorización previa (consúltalo primero). El README y los archivos de convenciones documentan cómo trabajar.
- Si quieres, puedo añadir ejemplos de uso, comandos para pruebas unitarias o instrucciones de despliegue.

---

Contacto: repositorio local en este workspace.
