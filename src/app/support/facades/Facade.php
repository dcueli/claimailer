<?php declare(strict_types=1);

namespace App\Support\Facades;

// Native
use Exception;
use ReflectionClass;
use RuntimeException;

// Application
  // Contracts
  use App\Contracts\Interfaces\IFacade;
  // Traits
  use App\Support\Traits\ToStringTrait;
  // Application
  use App\Application;
  use App\Services\Config\ConfigurationService;

abstract class Facade implements IFacade {
  use ToStringTrait;

  /**
   * The application instance being facaded.
   *
   * @var Application|null
   */
  protected static ?Application $app = null;

  /**
   * The application configuration service instance being facaded.
   *
   * @var ConfigurationService|null
   */
  protected static ?ConfigurationService $cnf = null;

  /**
   * The resolved object instances.
   *
   * @var array
   */
  protected static array $resolvedInstance;

  /**
   * Set the application instance.
   *
   * @param Application $app
   * @return void
   */
  public static function setFacadeApplication(Application $app): void {
    static::$app = $app;
  }

  /**
   * Get the application instance behind the facade.
   *
   * @return Application|null
   */
  public static function getFacadeApplication(): Application|null {
    return static::$app;
  }

  public static function isBound(): bool {
    if (!!!static::$app)
      return false;

    return static::$app->has(static::getFacadeAccessor());
  }
  /**
   * Get the registered name of the component.
   *
   * @return string
   *
   * @throws \RuntimeException
   */
  public static function getFacadeAccessor(): string {
    return strtolower(basename(str_replace(LS, DS, static::class) ) );
  }

  /**
   * Resolve the facade root instance from the container.
   *
   * @param string $name
   * @return mixed
   */
  protected static function resolveFacadeInstance(string $name): mixed {
    if (isset(static::$resolvedInstance[$name]))
      return static::$resolvedInstance[$name];

    if (static::$app)
      return static::$resolvedInstance[$name] = static::$app->get($name);

    return null;
  }

  /**
   * Handle dynamic, static calls to the object.
   *
   * @param string $method
   * @param array $args
   * @return mixed
   *
   * @throws \RuntimeException
   */
  public static function __callStatic(string $method, array $args): mixed {
    $instance = static::resolveFacadeInstance(static::getFacadeAccessor());

    if (!!!$instance)
      throw new RuntimeException('A facade root has not been set.');

    return $instance->$method(...$args);
  }
}
