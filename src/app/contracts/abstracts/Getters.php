<?php declare(strict_types=1);

namespace App\Contracts\Abstracts;

// Application
  // Interfaces
  use App\Contracts\Interfaces\IGetter;

class Getters implements IGetter {
  /** 
   * Get a property value by key.
   * 
   * @param string $key The key to retrieve (e.g., 'config.DEBUG').
   * @param mixed $def The default value to return if the property not exists.
   * @return mixed The property value or the default.
   */
  public function get(?string $key = NULL, mixed $def = NULL): mixed {
    if (empty($key))
      return $def;
    if (property_exists($this, $key))
      return $this->{$key};

    return $def;
  }

  /**
   * Alias of get method
   * 
   * @param mixed $key
   * @param mixed $default
   * @return mixed
   */
  public function g(?string $key = NULL, mixed $default = NULL): mixed {
    return $this->get($key, $default);
  }
}