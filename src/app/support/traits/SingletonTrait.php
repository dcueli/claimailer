<?php declare(strict_types=1);

namespace App\Support\Traits;

// Native
use LogicException;
// Application
use App\Support\Traits\ToStringTrait;

trait SingletonTrait {
  use ToStringTrait;

  /**
   * Instance container per class (late static binding).
   * Not typed so that the Trait can be generic.
   * 
   * @var object|null
   */
  public static ?object $instance = NULL;

  /**
   * Protected Constructor to avoid external instantiation.
   * The class using the trait can define its own protected constructor
   * and then call parent::__construct() if desired (not be mandatory).
   */
  protected function __construct() {
    // Empty on purpose. Classes can define their own logic.
  }

  /**
   * @DEPRECATED
   * Alias to fulfill ISingleton interface.
   * 
   * @return static
   */
  // public static function init(): static {
  //   // return static::getInstance();
  //   return static::$instance;
  // }  

  /**
   * Returns the one instance of the Class
   * Create the instance if not exists.
   *
   * @return static
   */
  public static function getInstance(mixed $args = NULL): static {
    if (empty(static::$instance)) {
      // Explicit call to protected Constructor of the proper class
      static::$instance = new static($args);

      // If the proper Class implements init(): call it
      if (method_exists(static::$instance, 'init'))
        static::$instance->init($args);
    }

    return static::$instance;
  }

  /**
   * Clone prevention
   */
  final public function __clone(): void {
    throw new LogicException('Cannot clone a singleton.');
  }

  /**
   * Unserialize prevention
   */
  final public function __wakeup(): never {
    throw new LogicException('Cannot unserialize a singleton.');
  }

  /**
   * For the testing to reset the instance.
   * Useful in unit tests if you need to reset the singleton.
   */
  protected static function resetInstance(): void {
    static::$instance = null;
  }
}
