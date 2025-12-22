# Instrucciones para Agentes de IA - Claimailer

## Descripción del Proyecto
Claimailer es un framework de servicios de correo PHP (v2.1.0) construido sobre las convenciones de desarrollo fijadas en dcueli.com. Proporciona capas de abstracción para proveedores de correo (PHPMailer, Symfony Mailer, etc.) a través de una arquitectura de inyección de dependencias.

## Arquitectura y Patrones de Diseño

### Principio Estricto de Responsabilidad Única (SRP)
Este proyecto sigue las **convenciones fijadas en dcueli.com.** con una interpretación estricta del SRP. Distinción clave:
- **Container:** Solo almacena/recupera bindings (`get()`, `Has()`)
- **Register:** Gestiona el registro/binding de servicios
- **Resolver:** Maneja la resolución de dependencias
- **Razón:** Como un almacén con roles separados de Recepción, Almacenamiento y Envío—no los mezcles

**Impacto para IA:** Al añadir funcionalidades, aísla las preocupaciones rigurosamente. No hagas que Container sea responsable de almacenamiento Y resolución.

### Arquitectura de Servicios
1. **MailClientService** (`src/app/services/mail/MailClientService.php`)
   - Orquestación de correo de alto nivel (configuración, envío, logging)
   - Gestiona MailDataDTO (destinatario, asunto, cuerpo) y MailServerDTO (config SMTP)
   - Rastrea contador de correos enviados y archivos de log
   
2. **MailClientProvider** (`src/app/providers/mail/MailClientProvider.php`)
   - Envuelve clientes de terceros (PHPMailer, Symfony Mailer)
   - Sistema de tipos de cliente basado en Enum (`MailerClient`)
   - Mapea tipos de destinatarios a setters específicos del proveedor
   
3. **Bootstrap de Aplicación** (`src/bootstrap/App.php`)
   - Punto de entrada: crea singleton `Application`
   - Carga registro de Servicios → Configuración → Metadatos de Composer
   - El manejo de excepciones debe ser personalizado (antes de inicializar Logger)

### Flujo de Datos
```
Bootstrap → Singleton Application
           ↓
           Container + Register + Resolver
           ↓
           MailClientService (orquesta)
           ↓
           MailClientProvider (envuelve) → PHPMailer/Symfony
           ↓
           DTOs: MailDataDTO, MailServerDTO
```

## Convenciones de Nombres (Estricto)

**Métodos:**
- Generales: `PascalCase` (ej., `CheckClient`, `SendWithPHPMailer`)
- Getters: `camelCase` con prefijo `get` (ej., `getError`, `getServerExtList`)
- Setters: `camelCase` con prefijo `set` (ej., `setCredentials`, `setRecipients`)

**Variables:**
- Instancia/locales: `camelCase` (ej., `$mailService`, `$realClient`)
- Booleanos: Notación húngara con prefijo `b` (ej., `$bEnable`, `$bBooted`, `$bReMapping`)

**Archivos:**
- Clases: `PascalCase` (ej., `MailClientService.php`)
- Excepciones: Sufijo `Exception` (ej., `FileContentNotValidException`)

## Archivos Críticos y Patrones

| Archivo | Propósito | Patrón Clave |
|---------|-----------|------------|
| [src/app/Application.php](../src/app/Application.php) | Singleton central, orquesta Container/Register/Resolver | SRP de triple separación |
| [src/app/services/mail/MailClientService.php](../src/app/services/mail/MailClientService.php) | Orquestación de correo, logging de contador, gestión de DTOs | Patrón Service + Facade |
| [src/app/providers/mail/MailClientProvider.php](../src/app/providers/mail/MailClientProvider.php) | Envoltorio de proveedor, selección de cliente basada en enum | Patrón Adapter + Enum |
| [src/config/Services.php](../src/config/Services.php) | Registro de facades/servicios | Binding de DI basado en array |
| [src/config/Config.php](../src/config/Config.php) | Config de app (VERSION, DEBUG, LANGUAGE, HOSTNAME, etc.) | Array de config inmutable |

## Flujos de Trabajo de Desarrollo

### Configuración y Dependencias
```powershell
composer install          # Instala PHPMailer, Symfony Mailer, paquetes PSR
composer require X        # Añade nueva dependencia (respeta autoload psr-4)
```

### Configuración
- Edita [src/config/Config.php](../src/config/Config.php): VERSION, DEBUG_MODE, LANGUAGE, HOSTNAME
- Edita [src/config/Services.php](../src/config/Services.php): Bindings de servicios (facades, helpers)
- Plantillas de correo: `resources/mail/templates/`
- Logs: `resources/logs/` (Cont_log, Error_log)

### Clases Clave para Referenciar
- **Manejo de excepciones:** `App\Support\Exceptions\Basexception` (excepciones personalizadas con lógica de visualización)
- **Facades:** `Config`, `Logger` (acceso estático a servicios)
- **Helpers:** `Arr`, `Str`, `Helpers` (funciones de utilidad con métodos estáticos)
- **Traits:** `SingletonTrait` (fuerza patrón singleton), `LogSaverTrait` (logging)

## Requisitos de Estilo de Código

1. **Tipos Estrictos:** Todos los archivos deben declarar `<?php declare(strict_types=1);` al inicio
2. **Rendimiento sobre legibilidad:** Usa código conciso, líneas mínimas
3. **DTOs para transferencia de datos:** Usa `MailDataDTO`, `MailServerDTO` en lugar de arrays
4. **Lógica basada en Enum:** Usa enum `MailerClient` para selección de proveedor, no strings
5. **Logging:** Usa `LogSaverTrait` + facade Logger para todas las operaciones
6. **Permisos de archivo:** Evita modificar archivos existentes sin autorización explícita (según AI-Instructions.md)

## Decisiones Específicas del Proyecto

- **Sin Laravel**: Sistema ligero personalizado de DI/Service Provider (no Laravel)
- **Proveedores basados en Enum**: Seguridad de tipos vía enum `MailerClient` en lugar de strings
- **Container personalizado**: Separa Register/Container/Resolver según convenciones fijadas por dcueli.com
- **Autoload de Composer**: PSR-4 para `App\` y `Bootstrap\`; funciones globales en Flags.php

## Referencias
- [Convenciones dcueli.com v1.0.1](../docs/conventions/dcueli-conventions-ES-v.1.0.1.md) - Filosofía central y reglas de nombres
- [Instrucciones de IA](../docs/ai/AI-Instructions.md) - Reglas de modificación de archivos (solicita primero, no modifiques)
- [composer.json](../composer.json) - Dependencias, autoload, metadatos del proyecto

---
# AI Instructions

## Español

### Principios de Codificación
- Código conciso: usar la menor cantidad de líneas posible.
- Rendimiento primero: priorizar rendimiento sobre legibilidad, salvo cuando la complejidad requiere documentación adicional.
- Hecho: el que no sepa leer que aprenda.

### Modificación de Archivos
- NUNCA modificar archivos sin autorización previa.
- Mostrar solo código en el chat; el usuario decide si copia/pega.

### Explicaciones
- Las explicaciones deben ser precisas, claras y concisas; sin rodeos vagos, pero rigurosas. Evitar lenguaje impreciso o ambiguo.

### Interacción y Colaboración
- **Asertividad Crítica**: No dar siempre la razón, ni estar dando alabanzas por algo que digo. Si no se tiene razón, hay que decirlo y expliar el por qué. Hacer una negociación y llegar a un punto intermedio, y en el caso más extremo, si quiero o lo necesito, hacer lo que yo indique.

---

## English

### Coding Principles
- Concise code: use the minimum number of lines possible.
- Performance first: prioritize performance over readability, except where complexity requires thorough documentation.
- Fact: Those who can't read should learn.

### File Modification
- NEVER modify files without prior authorization.
- Only show code in the chat; the user decides whether to copy/paste.

### Explanations
- Explanations must be precise, clear and concise; no vague hedging, while remaining rigorous. Avoid imprecise or ambiguous language.

### Interaction and Collaboration
- **Critical Assertiveness**: Do not always agree with me. If you believe a request is incorrect or can be improved, state your arguments and propose alternatives. The goal is to negotiate the best solution. The final decision, however, is mine.