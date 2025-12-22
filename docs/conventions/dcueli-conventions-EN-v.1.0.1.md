██████╗░░█████╗░██╗░░░██╗███████╗██╗░░░░░██╗  
██╔══██╗██╔══██╗██║░░░██║██╔════╝██║░░░░░██║  
██║░░██║██║░░╚═╝██║░░░██║█████╗░░██║░░░░░██║  
██║░░██║██║░░██╗██║░░░██║██╔══╝░░██║░░░░░██║    
██████╔╝╚█████╔╝╚██████╔╝███████╗███████╗██║  
╚═════╝░░╚════╝░░╚═════╝░╚══════╝╚══════╝╚═╝ v1.0.1

This document outlines the personal development conventions and philosophies on dcueli.com.

## Core Philosophy: Strict Single Responsibility Principle (SRP)

My approach to software architecture is rooted in a strict interpretation of the Single Responsibility Principle (SRP), prioritizing a clear and tangible separation of concerns over commonly accepted community standards.

### The "Warehouse" Analogy vs. The "Container" Pattern

A common pattern in modern frameworks is the "Container," a single class responsible for both registering (binding) and retrieving (resolving) dependencies. I find this to be a flawed abstraction.

My reasoning is based on a real-world analogy: a warehouse. In a functional warehouse, you have distinct roles:
1.  **Reception:** Staff who receive incoming goods.
2.  **Internal Management:** Staff who organize and store the goods within the warehouse.
3.  **Delivery/Dispatch:** Staff who retrieve the goods and prepare them for shipment.

These are separate responsibilities. It is illogical to bundle them into a single entity. Similarly, in code:
-   **Registering** a dependency is like **reception**.
-   **Storing** the dependency is **internal management**.
-   **Resolving/getting** a dependency is like **delivery**.

Therefore, I advocate for separating these concerns into distinct classes or components. A class that registers a dependency should not also be responsible for retrieving it. This maintains a clean, logical, and highly specialized architecture where each component does one thing and does it well.

### Critique of Over-Engineering and "Community" Standards

I am critical of what I perceive as unnecessary complexity introduced by anonymous "developer communities." Standards should be questioned, not blindly followed.

-   **On TypeScript:** I view technologies like TypeScript as an unnecessary layer of complexity over JavaScript. It often serves as a crutch for developers accustomed to strongly-typed languages like Java, preventing them from embracing the unique, function-oriented paradigms of JavaScript. The need to transpile code adds an extra, often superfluous, step to the development process.

-   **On Logic:** Code architecture should follow tangible, real-world logic rather than abstract conventions. If a pattern doesn't make sense in a physical, real-world scenario (like the warehouse), it's likely a poor abstraction for code.

This philosophy is to be applied to all projects, ensuring a consistent, logical, and maintainable codebase.

---

## Naming Conventions

All naming follows strict conventions for clarity, consistency, and functional meaning:

### Methods & Functions
- **General Methods:** `PascalCase` (e.g., `CheckClient`, `Reset`, `Terminate`, `SetMapping`)
- **Getter Methods:** `camelCase` with `get` prefix (e.g., `getError`, `getServerExtList`)
- **Setter Methods:** `camelCase` with `set` prefix (e.g., `setCredentials`, `setDriver`, `setRecipients`)
- **Exception:** Methods that perform I/O or have side effects use `PascalCase` regardless (e.g., `SendWithPHPMailer`)

### Variables
- **Local & Instance Variables:** `camelCase` (e.g., `$mailService`, `$client`, `$realClient`, `$endtime`)
- **Property Initialization:** `camelCase` (e.g., `$bEnable`, `$counterFilePath`)

### Boolean/Flag Variables
- **Hungarian Notation for Flags:** Prefix with `b` in `camelCase` (e.g., `$bReMapping`, `$bValid`, `$bEnable`, `$bBooted`)
- Purpose: Immediately signals to developers that a variable holds a boolean state

### Constants
- **All Constants:** `UPPER_SNAKE_CASE` (e.g., `DEFAULT_TIMEOUT`, `MAX_LINE_LENGTH`, `DEBUG_OFF`)

### Classes & Enums
- **Standard:** `PascalCase` (e.g., `PHPMailer`, `MailClientProvider`, `Configurator`)
- **Interfaces:** `PascalCase` with `I` prefix (e.g., `ISingleton`, `IMailer`)
- **Enums:** `PascalCase` (e.g., `MailerClientTypes`, `PotentialRecipientsTypes`)
- **Traits:** `PascalCase` with `Trait` suffix (e.g., `SingletonTrait`, `ToStringTrait`)

---

## Singleton Pattern Implementation

### ISingleton Interface
- Requires: `public static function init(): static;`
- Purpose: Initialization hook for singletons after instantiation
- Single instance stored in: `static::$instance` (from `SingletonTrait`)

### SingletonTrait Usage
- Provides: `getInstance()`, `resetInstance()`, clone/unserialize prevention
- Reset method: `protected static function resetInstance(): void` (called by public `Reset()` wrappers)
- Public wrapper method: `public static function Reset(): void` (follows PascalCase for static methods)

### Application Lifecycle
- **On Start:** `getInstance()` creates single instance, `init()` called automatically
- **On Terminate:** `Reset()` clears instance, allowing fresh initialization on next Job run
- **Use Case:** Hosting Jobs that call `index.php` repeatedly (e.g., cron tasks)

---

## SMTP/Mail Client Architecture

### Client Wrapper Pattern
- **Wrapper Location:** `App\Providers\Mail\Clients\{ClientName}` (e.g., `PHPMailer`, `SymfonyMailer`, `SwiftMailer`)
- **Wrapper Purpose:** Abstracts third-party mail libraries, implements `IMailer` interface
- **Real Client Reference:** Stored in `->mailer` property with PHP 8.4 getter/setter

### PHPMailer Configuration
- **Timeout Settings** (both required to prevent execution timeout):
  - `$client->mailer->Timeout = 30;` — Socket read/write timeout
  - `$client->mailer->getSMTPInstance()->Timelimit = 30;` — `stream_select()` timeout used in `get_lines()`
- **Configuration Source:** `Config::g('mailtimeout', DEFAULT_TIMEOUT)` allows fallback to constant
- **Default Value:** `DEFAULT_TIMEOUT = 30` (seconds)

### Timeout Handling
- **Problem:** PHPMailer may hang indefinitely if server is slow, exceeding PHP's `max_execution_time`
- **Solution:** Set both `Timeout` and `Timelimit` to same value to enforce maximum wait time
- **Location:** Configure in `setDriver()` method of client wrapper during SMTP setup

---

## Application Cleanup & Resource Management

### Terminate Method (Application)
All resource cleanup happens in `Application::Terminate()`:
1. **Clear Provider Caches:** `MailClientProvider::ClearCache()` — empties static `$map` array
2. **Reset Configurator:** `\App\Providers\Configurator::Reset()` — clears configuration singleton
3. **Close Mail Connections:** Attempts to close SMTP connection via reflection (best-effort)
4. **Reset Application:** `static::Reset()` — clears application singleton instance
5. **Optional:** Set `$autosender_mail_end` timestamp if timing is needed

### Static Method Reset Pattern
- **Public Wrapper:** `public static function Reset(): void` (PascalCase)
- **Internal Call:** `self::resetInstance()` (from `SingletonTrait`)
- **Purpose:** Allows explicit reset between Job executions

---

## Array & Data Validation

### Empty Check Patterns
- **Preference:** `empty()` over `!count()` or `!(bool)[]` for readability
- **Rationale:** `empty()` is faster, more idiomatic, and equally clear
- **Null Coalescing:** `$var ??= DEFAULT_VALUE` for simple fallbacks
- **Triple Negation:** `!!!condition` avoided in favor of `!condition` or positive assertions

### Recipient & Mail Mapping
- **Enum-Based Mapping:** `PotentialRecipientsSetters` enum stores method names as pipe-separated strings (e.g., `'addAddress|setTo'`)
- **Method Detection:** `method_exists($client, $method)` checked against **real client object** (not wrapper)
- **Mapping Storage:** Static array `$map` caches method names per recipient type, cleared on `ClearCache()`

---

## PHP 8.4 Features

### Property Accessors (Typed Properties with Getters/Setters)
- **Syntax:** `public object $mailer { get; set; }`
- **Purpose:** Encapsulation without boilerplate getter/setter methods
- **Usage:** Implemented in wrapper classes (e.g., `PHPMailer`, `SymfonyMailer`)
- **Note:** Keep interface declarations simple; let implementations handle accessor details

---

## Code Style & Philosophy

### Comments & Documentation
- Use clear, actionable comments explaining **why**, not just **what**
- Spanish comments acceptable where code context is Spanish-specific
- Configuration-driven values preferred over hardcoded values

### Minimize Temporary Variables
- Avoid unnecessary intermediate variables (e.g., `$candidates`, `$tmp`, `$available`)
- Inline operations where logic is clear and single-pass
- Break only when multiple uses or complex logic justify the variable

### Braces for Conditionals and Loops

- **Single-line branches:** For `if`, `if/else`, `if/elseif`, `if/elseif/else` where each branch is a single statement on one line, omit the opening `{` and closing `}` braces. Example:

  ```php
  if ($cond) return $value;
  ```

- **Multi-line branches:** If any branch contains more than one statement or spans multiple lines, use braces for *all* branches in that conditional. This keeps the conditional unambiguous and consistent. Example:

  ```php
  if ($cond) {
      doFirstThing();
      doSecondThing();
  } else {
      doOther();
  }
  ```

- **Loops:** The same rule applies to loop structures (`for`, `foreach`, `while`, `do-while`): omit braces only for single-line bodies; include braces when the loop body has multiple statements.

- **Exceptions / required braces:** Use braces when the language construct requires them (e.g., `switch`/`match` blocks) or when including braces improves readability and reduces potential errors from future edits.

- **Rationale:** This policy reduces visual noise for simple one-liners while enforcing explicit block boundaries when a branch contains more than one statement, improving maintainability and reducing bugs introduced by ambiguous indentation.

### Performance Considerations
- **Static Caching:** Cache computed values (method lists, mappings) in static arrays, cleared at lifecycle end
- **Direct Reference vs. Copy:** Objects passed by reference only when intentional modification is needed; use value assignment for iteration
- **Method Existence Checks:** Perform once and cache; don't repeat per iteration

---

## Testing & Validation

### Hostname & Configuration
- Allow fallback to `Config::g()` for all external settings
- Use constants as defaults (e.g., `Config::g('mailtimeout', self::DEFAULT_TIMEOUT)`)
- Validate presence before use, throw descriptive exceptions with timestamps

*-- This document reflects the opinions and architectural directives on dcueli.com, updated throughout development. --*