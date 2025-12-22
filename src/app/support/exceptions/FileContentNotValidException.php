<?php declare(strict_types=1);

namespace App\Support\Exceptions;

use
// Native
  InvalidArgumentException,
  Throwable,
// Application
  App\Support\Facades\Config,
  App\Support\Traits\LogSaverTrait;

class FileContentNotValidException extends InvalidArgumentException {
  use LogSaverTrait;

  public static function fromError(
    mixed $message, 
    int $code = 0, 
    ?Throwable $previous = null
  ): static {
    if(Config::get('LOG_MODE'))
       self::SaveLogOnFile($message, $code, $previous);

    return new static($message, $code, $previous);
   }
}