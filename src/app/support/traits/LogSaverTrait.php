<?php declare(strict_types=1);

namespace App\Support\Traits;

// Native
use 
  Throwable,
// Application
  App\Support\Facades\Config,
  App\Support\Traits\ToStringTrait;

trait LogSaverTrait {
  use ToStringTrait;

  public static function SaveLogOnFile(
    string $message, 
    ?int $errorCode = NULL,
    ?Throwable $e = NULL,
    ?string $file = NULL
  ): bool {
    if(empty($message))
      return FALSE;
    
    $errorCode ??= $e?->getCode() ?? NULL;
    $file ??= Config::g('path.logs').Config::g('error_log_file');
    if($log = fopen($file, 'a')){
      fputs($log, "Error code: " .$errorCode ?? $e->getCode() . ": \r\n" . $message . "\r\n" . $e->getMessage());
      return fclose($log);
    } 
    
    return FALSE;
  }
}
