<<<<<<< HEAD

![Claimailer](https://claimailer.dcueli.com/source/assets/logo.png)
> Autosender mail

# Claimailer — Envío automático de correo electrónico

![Test status](https://github.com/PHPMailer/PHPMailer/workflows/Tests/badge.svg)
![Latest Stable Version](https://poser.pugx.org/phpmailer/phpmailer/v/stable.svg)

<!-- 
[![Test status](https://github.com/PHPMailer/PHPMailer/workflows/Tests/badge.svg)](https://github.com/PHPMailer/PHPMailer/actions)
[![Latest Stable Version](https://poser.pugx.org/phpmailer/phpmailer/v/stable.svg)](https://packagist.org/packages/phpmailer/phpmailer)
[![Total Downloads](https://poser.pugx.org/phpmailer/phpmailer/downloads)](https://packagist.org/packages/phpmailer/phpmailer)
[![License](https://poser.pugx.org/phpmailer/phpmailer/license.svg)](https://packagist.org/packages/phpmailer/phpmailer)
[![API Docs](https://github.com/phpmailer/phpmailer/workflows/Docs/badge.svg)](https://phpmailer.github.io/PHPMailer/)
[![OpenSSF Scorecard](https://api.securityscorecards.dev/projects/github.com/PHPMailer/PHPMailer/badge)](https://api.securityscorecards.dev/projects/github.com/PHPMailer/PHPMailer) -->

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
1 - desde que buzón enviar, con sus credeciales,
2 - a qué direcciones de buzones enviar, incluyendo la posibilidad de hacerlo "Con Copia" (CC) o "Con Copia Oculta" (BCC),
3 - y qué enviar, esto es el mensaje, incluido la posibilidad de archivos adjuntos.

**_Utiliza, obligatoriamente, librerías (o componentes) de envío de correo en PHP para poder reemplazar o mejorar el uso directo de la función mail() de PHP y gestionar así, SMTP, adjuntos, HTML, etc._**

Por defecto utiliza **PHPMailer**, aunque cuando se instala la fuente para desarrollo (`> composer install`), a parte de PHPMailer, también instala **Symfony Mail**, aunque se puede instalar otras _al gusto del consumidor_. Para usar estas librerías hay que desarrollar su componente o consumidor (_consumer class_) de Claimailer con su mismo nombre, por ejemplo, si se va a usar la librería PHPMailer, como desarrollador deberemos crear **_PHPMailer.php_** en la ubicación `src/app/providers/mail/clients/` (esto posiblemente se modifique en versiones posteriores).

Si queremos usar Symfony Mail deberíamos crear `src/app/providers/mail/clients/`**_SymfonyMailer.php_**.
> **Nota**: aunque para este consumidor de la librería, su desarrollo está comenzado, para las siguientes versiones habría que terminarlo.

### 🧩 Características principales

- **NO TIENE LICENCIA**, vamos que se puede hacer con esto lo que a uno le venga en gana, lo que le de la gana o lo que se salga de los coj***s. 
- **NI DE COÑA ME HAGO P\*\*O RESPONSABLE DE LO QUE CADA UNO HAGA CON ESTO**, deberíamos ser ya lo bastante responsables como para saber qué hacer o no, aunque la maldad del ser humano es intrínseca e innata a su ser.
- Se puede usar como Job o como Servicio
- No usa base de datos, al menos de momento.
- Es obligatorio tener una cuanta de correo electrónico para poder enviar.
- Patrones de diseño utilizados: Singleton, Factory entre otros; los de siempre vamos.

---

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

---

## 💡 ¿Por qué puedes necesitar esto?

### Casos de uso

1. **Respuestas automáticas y notificaciones de recepción**  
   Administraciones, servicios públicos o sistemas que necesitan confirmar automáticamente la recepción de solicitudes sin depender de servicios de terceros (Mailchimp, SendGrid, AWS SES).

2. **Alertas y recordatorios en sistemas internos**  
   Notificaciones automáticas de eventos en aplicaciones: cambios de estado, vencimientos, errores críticos, generación de reportes o tareas programadas.

3. **Automatización sin infraestructura compleja**  
   Integración directa en scripts, jobs cron o servicios que requieren envío de correo sin agregar dependencias externas o costos recurrentes de plataformas especializadas.

### Ventajas

- **Control total**: Usa tu propio servidor SMTP, sin intermediarios.
- **Sin base de datos**: Almacenamiento de registro de envíos en archivos; arquitectura ligera.
- **Flexible**: Soporta múltiples proveedores de mail (PHPMailer, Symfony Mailer) mediante adaptadores.
- **Escalable**: Funciona como Job único o como Servicio continuo según necesidad.
- **Bajo costo**: Solo requiere una cuenta de correo SMTP válida; sin tarifas por volumen de envío.
- **Independencia**: No depende de APIs de terceros que pueden cambiar, desaparecer o tener costos prohibitivos.

---

## 📄 Licencia

Indica claramente el tipo de licencia y cualquier matiz relevante.
Este proyecto está licenciado bajo la licencia [NOOO TIENE LIENCIA]. Consulta el archivo LICENSE para más detalles.

---

## ⚙️ Instalación

### 🟢 Instalación para su uso

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

### 🛠️ Instalación para modificación

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
=======
# claimailer
Framework para envío automático de correo electrónico
>>>>>>> c019f55f3eaef3303a49e8a20e7ee3f7b9960a09
