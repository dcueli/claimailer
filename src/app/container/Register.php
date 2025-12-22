<?php  declare(strict_types=1);

namespace App\Container;

use App\Container\Container;

class Register {
  /**
   * Create a new Register instance.
   *
   * @param Container $container
   */
  public function __construct(private Container $container){}

  /**
   * Bind a service into the container.
   *
   * @param string $key
   * @param mixed $value
   * @return void
   */
  public function Bind(string $key, mixed $value): void {
    $this->container->set($key, $value);
  }
}