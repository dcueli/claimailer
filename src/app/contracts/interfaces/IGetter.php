<?php declare(strict_types=1);

namespace App\Contracts\Interfaces;

interface IGetter {
  public function get(string $key, $default = NULL): mixed;
  public function g(string $key, $default = NULL): mixed;
}