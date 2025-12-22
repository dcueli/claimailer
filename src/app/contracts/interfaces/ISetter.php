<?php declare(strict_types=1);

namespace App\Contracts\Interfaces;

interface ISetter {
  public function set(string $key, mixed $value): mixed;
  public function s(string $key, mixed $value): mixed;
}