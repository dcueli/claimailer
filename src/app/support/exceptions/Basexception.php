<?php declare(strict_types=1);

namespace App\Support\Exceptions;

// Native
use Error;
use ErrorException;
use Throwable;
// Application
use App\Contracts\Interfaces\ISingleton;
use App\Support\Traits\SingletonTrait;

class Basexception extends Error implements ISingleton {
  use SingletonTrait;

  /**
   * Inheritance attributes
   */ 
  protected string $realMsg = '';
  protected ?string $realCode;
  protected string $file = '';
  protected int $line;
  private array $trace = [];
  private ?Throwable $previous = NULL;
  /**
   * Inheritance methods
   */
  public function __construct(
    string $msg = '', 
    string $code = '', 
    ?Throwable $previous = null
  ) {
    $this->Init($msg, $code, $previous);
  }

  private function set(
    string $msg, 
    mixed $code = NULL, 
    ?string $file = NULL,
    ?int $line = NULL,
    ?array $trace = [],
    ?Throwable $prev = NULL
  ): void {
    $this->realMsg = $msg;
    $this->realCode = (string)$code;
    $this->file = (string)$file;
    $this->line = (int)$line;
    $this->trace = (array)$trace;
    $this->previous = $prev;
  }

  public function ExceptionsHandler(Throwable $e):void {
    $this->ShowableException($e);
  }  

  public function ErrorHandler($severity, $msg, $filename, $lineno): never {
    $this->set(
      $msg, 
      parent::getCode(), 
      $filename, 
      $lineno
    );

    throw new ErrorException(
      $msg, 
      parent::getCode(), 
      $severity, 
      $filename, 
      $lineno
    );
  }  

  public function Init(
    string|null $msg = NULL, 
    string|null $code = NULL, 
    ?Throwable $previous = NULL
  ): static {
    $this->set(
      msg: $msg, 
      code: $code, 
      prev: $previous
    );

    parent::__construct($this->realMsg, (int)$this->realCode, $this->previous);

    set_error_handler([$this, 'ErrorHandler']);
    set_exception_handler([$this, 'ExceptionsHandler']);

    return $this;
  }

  public function ShowableException(Throwable $e): void {
    $errorMessage = sprintf(
      "[%s] +APPPLICATION FAILED+: %s in %s:%d\nStack trace:\n%s\n%s",
      date('Y-m-d H:i:s'),
      $this->getMessage(),
      $this->getFile(),
      $this->getLine(),
      $this->getTraceAsString(),
      $e
    );
    // var_dump($errorMessage);

    // Use the native error_log() function to save the message into a specified file.
    // The '3' means that the message will be appended to the specified file path in the next parameter.
    if(file_exists($log_file = realpath(BASE_PATH.DS.'src'.DS.'resources'.DS.'logs'.DS.'Error_log') ) )
      error_log($errorMessage, 3, $log_file);
    else {
      // Show a generic error message and stop execution.
      var_dump($errorMessage);
      die("<br><br>A Fatal error happened on the Application check above Log");
    }
    
    // [Optional] Return FALSE to caller file to know that the app has not been loaded.
    // return null;  
  }
}