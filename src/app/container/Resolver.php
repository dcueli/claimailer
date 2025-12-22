<?php  declare(strict_types=1);

namespace App\Container;

// Application
  // Contracts
  use App\Contracts\Interfaces\IGetter;

use App\Container\Container;

class Resolver implements IGetter{
  /**
   * Create a new Register instance.
   *
   * @param Container $container
   */
  public function __construct(private Container $container){}

  /**
   * Get a service from the container.
   *
   * @param string $key
   * @return mixed
   */
  public function get(string $key, $default = NULL): mixed {
    return $this->container->g($key, $default);
  }

  /**
   * Alias of get() method.
   *
   * @param string $key
   * @return mixed
   */
  public function g(string $key, $default = NULL): mixed {
    return $this->g($key, $default);
  }
}