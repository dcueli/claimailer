<?php declare(strict_types=1);

namespace App\Container;

// Native
use RuntimeException;
// Application
//  Contracts
    use App\Contracts\Interfaces\ISegetter;

class Container implements ISegetter{
  /**
   * The storage for the bindings.
   *
   * @var array<string, mixed>
   */
  private array $storage = [];

  /**
   * Set a value in the storage.
   *
   * @param string $key
   * @param mixed $value
   * @return void
   */
  public function set(string $key, mixed $value): mixed {
    return $this->storage[$key] = $value;
  }
  public function s(string $key, mixed $value): mixed {
    return $this->set($key, $value);
  }

  /**
   * Get a value from the storage.
   *
   * @param string $key
   * @return mixed
   */
  public function get(string $key, $default = NULL): mixed {
    if (!!!$this->Has($key))
      throw new RuntimeException("No item found for key [{$key}] in Warehouse.");

    return $this->storage[$key];
  }

  public function g(string $key, $default = NULL): mixed {
    return $this->get($key);
  }

  /**
   * Check if a key exists in the storage.
   *
   * @param string $key
   * @return boolean
   */
  public function Has(?string $key = NULL): bool|array {
    if (!!!empty($key))
      return array_key_exists($key, $this->storage);

    return $this->storage;
  }
}
