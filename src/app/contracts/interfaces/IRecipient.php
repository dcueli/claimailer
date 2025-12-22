<?php declare(strict_types=1);

namespace App\Contracts\Interfaces;

interface IRecipient {
  public string|null $email{ get; set; }
  public string|null $name{ get; set; }

  public function ValidateEmail(?string $email = NULL): bool;
}