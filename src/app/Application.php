<?php declare(strict_types=1);

namespace App;

use 
// Native
  ReflectionObject,
  RuntimeException,
  Throwable,
  UnexpectedValueException,
  ValueError,
  
// Application
  // Containers
  App\Container\Container,
  App\Container\Register,
  App\Container\Resolver,
  // Contracts
    // Interfaces
    App\Contracts\Interfaces\ISingleton,
  // Providers
  App\Providers\Mail\MailClientProvider,
  // Services
    // Mail
    App\Services\Mail\MailClientService,
  // Support
    // Facades
    App\Support\Facades\Config,
    App\Support\Facades\Facade,
    App\Support\Facades\Logger,
    // Helpers
    App\Support\Helpers\Arr,
    // Traits
    App\Support\Traits\SingletonTrait;

class Application implements ISingleton {
  use SingletonTrait;

  /**
   * The Autosender Mail version.
   *
   * @var string|null
   */
  public static string|null $ver = NULL;

  /**
   * The application's service container.
   *
   * @var Container|null
   */
  protected Container|null $container = NULL;

  /**
   * The application's service dependencies register.
   *
   * @var Register|null
   */
  protected Register|null $register = NULL;

  /**
   * The application's service dependencies resolver.
   *
   * @var Resolver|null
   */
  protected Resolver|null $resolver = NULL;

  /**
   * Indicates if the application has "booted". 
   *
   * @var bool
   */
  private bool $Booted = FALSE;
  public bool $booted {
    get => $this->Booted;
  }

  /**
   * The application namespace.
   *
   * @var string|null
   */
  private string|null $Namespace = NULL;
  public string|null $namespace {
    get => $this->Namespace;
  }

  /**
   * When the application finished
   * 
   * @var int
   */
  private int $AutosenderMailEnd = 0;
  public int $autosenderMailEnd {
    get => $this->AutosenderMailEnd;
  }

  /**
   * Whether registry has been initialized.
   *
   * @var bool
   */
  private bool $IsRegistryOn = FALSE;
  public bool $isRegistry {
    get => $this->IsRegistryOn;
  }

  /**
   * Whether application settings has been initialized.
   * 
   * @var bool
   */
  private bool $IsSettingsOn = FALSE;
  public bool $isSettings {
    get => $this->IsSettingsOn;
  }

  /**
   * Mail controller
   * 
   * @var MailClientService|null
   */
  private MailClientService|null $MailService = NULL;
  public MailClientService|null $mailService {
    get => $this->MailService;
  }

  /**
   * Create a new application instance.
   * 
   * @param string|null $basePath
   * @return Application
   */ 
  public function __construct(public string|null $basePath = NULL) {
    if (empty($basePath))
      throw new UnexpectedValueException('Application basepath [$basePath:{'.$basePath.'}] is not defined');

    Facade::setFacadeApplication($this);

    $this->container = new Container();
    $this->register = new Register($this->container);
    $this->resolver = new Resolver($this->container);
  }

  /**
   * Bind a service into the container.
   *
   * @param string $key
   * @param mixed $value
   * @return void
   */
  private function Bind(string $key, mixed $value): void {
    $this->register->Bind($key, $value);
  }

  /**
   * Registry in the Container <$container> attributes the services application
   * 
   * @param array<string> $registry Paths files
   * @return bool
   */
  private function Registry(array $registry = []): bool {
    if (empty($registry))
      return FALSE;

    // Loop the services array and register them from configurations files
    foreach ($registry as $fPath) {
      if (!!!file_exists($fPath)) 
        continue;

      if('php' === strtolower(pathinfo($fPath, PATHINFO_EXTENSION) ) ) {
        // Get returned file value and loop on registry array and 
        // register them from configurations files
        $service = require_once($fPath);
        foreach ($service as $key => $class) {
          // Check if the service has been registered previously
          if ($this->Has($key)) continue;

          // Get the specific paramters for this service, if exists
          $this->bind($key, new $class());
        }
      };
    }
    
    return TRUE;
  }

  /**
   * Configure the application through ConfigurationServiceclass (facade Config) and other items
   * 
   * @param array<string, mixed> $settings
   * @return bool
   */
  private function Configure(array $settings = []): bool {
    if (empty($settings))
      return FALSE;

    // Check if Config exists and it has been initialized
    if (!!!$this->container->Has(Config::getFacadeAccessor()) || !!!Config::isBound())
      return FALSE;
    
    Config::init($settings);
    
    if (!!!Config::IsSettedUp())
      return FALSE;
    
    // Check if Logger exists and it has been initialized
    if (!!!$this->container->Has(Logger::getFacadeAccessor()) || !!!Logger::isBound())
      return FALSE;
    
    Logger::Init([
      'mode' => Config::g('log_mode'),
      'file' => Config::g('path.logs').Config::g('error_log_file')
    ]);
    
    if (!!!Logger::IsSettedUp())
      return FALSE;

    return TRUE;
  }

  /**
   * Check if the Application has the mandatory containers and if the registry and 
   * settings are set up properly. If all the properties are ok, the booted 
   * property of the application will be set to "true" or "false".
   * 
   * @return Application
   */
  private function setBooted(): Application {
    if (!!!$this->container->has(Config::getFacadeAccessor()) || !!!Config::isBound())
      return ($this->Booted = FALSE) ?: $this;
    if (!!!$this->container->has(Logger::getFacadeAccessor()) || !!!Logger::isBound())
      return ($this->Booted = FALSE) ?: $this;
    if(!!!$this->IsRegistryOn || !!!$this->IsSettingsOn)
      return ($this->Booted = FALSE) ?: $this;

    $this->Booted = (bool)(static::$ver = Config::g('version', NULL));

    return $this;
  }

  /**
   * Set a property $key with a specified $value
   * 
   * @param string $key
   * @param mixed $value
   * @return mixed
   */
  public function set(string $key, mixed $value): mixed {
    // Most optimized case
    return @$this->{$key} = $value;

    // Even though this is a native function, property_exists is slower than isset or above instruction
    // return property_exists($this, $key) ? ($this->{$key} = $value) : NULL;
  }

  /**
   * Alias of set() method.
   * 
   * @param string $key
   * @param mixed $value
   * @return mixed
   */
  public function s(string $key, mixed $value): mixed {
    return $this->set($key, $value);
  }

  /**
   * Get a service from the container.
   *
   * @param string $key
   * @return mixed
   */
  public function get(string $key): mixed {
    return $this->resolver->get($key);
  }

  /**
   * Alias of get() method.
   *
   * @param string $key
   * @return mixed
   */
  public function g(string $key): mixed {
    return $this->get($key);
  }

  /**
   * Initializes configuration paths from an array.
   * 
   * @param array<string, string> $paths Associative array mapping config keys to file paths.
   * @return static 
   */
  public function Init(): static {
    return $this->instance ??= static::getInstance();
  }

  public function Settingup(array $registry = [], array $settings = []): void {
    // Create services dependencies
    if (!!!empty($registry))
      $this->IsRegistryOn = $this->Registry($registry);

    //  Initializing application classes
    if (!!!empty($settings))
      $this->IsSettingsOn = $this->Configure($settings);

    if (!!!$this->setBooted()->booted)
      throw new RuntimeException('The application dependencie service '.var_export($this, true).' has not initialized or not exists');

    static::$instance = $this;
  }

  /**
   * Check if a binding exists in the container.
   *
   * @param string $key
   * @return bool
   */
  public function Has(string $key): bool {
    return $this->container->Has($key);
  }

  /**
   * @param array $mailData - The same structure of $mail param in Send() method:
   * @param ?string $client - Mailer client class name
   * @return MailClientService
   */
  public function BindMailings(array $mailData = [], ?string $client = NULL): MailClientService {
    return new MailClientService(Config::Mailing('credentials'), $mailData, $client);
  }

  /**
   * @param array $mail - Proper structure of $mail:
   * [
   *   'mail' => 'filename.html',
   *   'subject' => 'Testing mailing'
   *   'body' => 'The message body of email'
   *   'recipients' => [
   *     'to' => ['mail address'],
   *     'cc'  => ['destinatarios en copia del correo'],
   *     'bcc' => ['destinatarios en copia oculta del correo'],
   *   ],
   *   'reply' => [
   *     'email' => 'mail address', 
   *     'name' => 'name'
   *   ],
   *   'from' => [
   *     'email' => 'mail address', 
   *     'name' => 'name'
   *   ],
   *   'attachments' => [
   *     'path/to/the/file-1.ext',
   *     'path/to/the/file-2.ext',
   *     '...',
   *     'path/to/the/file-N.ext',
   *   ],
   * ];
   * @param ?string $client
   * @throws ValueError
   * @throws RuntimeException
   * @return bool
   */
  public function Send(array $mail = [], mixed $client = NULL): bool {
    if(empty($mail))
      throw new ValueError("The settings mail is not defined [\$mail:\n".var_export($mail, true)."]");

    $this->MailService = $this->BindMailings($mail, $client);

    if (empty($this->MailService instanceof MailClientService) || !!!$this->MailService->IsReady())
      throw new RuntimeException('Some happend with the mail data configuration, so check it out. $mail['.var_export($mail, true).']');

    return $this->MailService->Send(Config::Mailing('saveSentMailsRegistry'));

    // return false;
  }

  /**
   * Get the application namespace.
   *
   * @return string
   * @throws RuntimeException
   */
  public function Namespace(): string {
    if (!!!empty($this->Namespace))
      return $this->Namespace;

    if(empty($composer = Config::Composer() ) );
      $composer = json_decode(file_get_contents(Config::g('basepath')), true);

    foreach ((array)Arr::GetEl($composer, 'autoload.psr-4') as $namespace => $path)
      foreach ((array)$path as $pathChoice)
        if (realpath($this->basePath) == realpath(Config::g('basepath').'/'.$pathChoice))
          return $this->Namespace = $namespace;

    throw new RuntimeException('Unable to detect application namespace.');
  }  

  /**
   * Get the version number of the application.
   *
   * @return string|null
   */
  public static function Ver(): string|null {
    return static::$ver;
  }

  /**
   * Reset the application singleton instance.
   * Useful for freeing singleton state between job runs.
   *
   * @return void
   */
  public static function Reset(): void {
    self::resetInstance();
  }

  public function Terminate(): void {
    $cleanups = [
      fn() => $this->MailService?->Close(),
      fn() => MailClientProvider::ClearCache(),
      fn() => Config::Reset(),
    ];

    foreach ($cleanups as $fn) {
      try {
        $fn();
      } catch (Throwable $e) {
        // Registrar con SaveLogOnFile o Logger si quieres traza
        // self::SaveLogOnFile("Terminate cleanup failed: ".$e->getMessage(), $e->getCode(), $e);
      }
    }

    // 4. Nullify instance references to aid garbage collection
    $this->MailService = NULL;
    $this->container = NULL;
    $this->register = NULL;
    $this->resolver = NULL;
    
    // 5. Reset the application singleton so subsequent job runs start fresh
    static::Reset();

    // Finally. Record end time if timing is enabled
    if (defined('AUTOSENDER_MAIL_START'))
      $this->AutosenderMailEnd = (int) microtime(true);
  }
}
