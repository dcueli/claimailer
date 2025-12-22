<?php declare(strict_types=1);

namespace App\Contracts\Interfaces;

interface ISingleton {
  /**
  * Initialize and/or return the singleton instance for this class.
  * Should create the instance on first call and return the existing one on subsequent calls.
  * This keeps construction logic centralized (lazy initialization).
  * @return static
  */
  public function init(): static;
}