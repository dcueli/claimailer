<?php declare(strict_types=1);

/**
 * @dcueli/logger
 * EXPLANATION OF BITWISE OPERATIONS FOR LOGGING MODES
 * ==================================================================================================
 * How exactly the PHP flags bitwise work
 * Let's assume we have this mode active:
 * --------------------------------------------------------------------------------------------------
 * SMTP LOGGING MODES
 * 0 = off (for production use)
 * 1 = client messages
 * 2 = client and server messages
 * $this->mode = LOG_PRINT_MODE | LOG_FILE_MODE; // 0010 | 0100 = 0110
 * 
 * |---------------------------------------------------------|
 * | Flag             | Decimal value | Bynary value | State |
 * | ---------------- | ------------- | ------------ | ----- |
 * | LOG_SILENCE_MODE | 0             | 0000         | 0     |
 * |                  | 1             | 0001         | 0     |
 * | LOG_PRINT_MODE   | 2             | 0010         | 1     |
 * | LOG_FILE_MODE    | 3             | 0100         | 0     |
 * |---------------------------------------------------------|
 * 
 * Disable LOG_FILE_MODE:
 * --------------------------------------------------------------------------------------------------
 * $flag  = LOG_FILE_MODE; // 0100
 * ~$flag = 1011
 * 
 * 'AND' bitwise
 * --------------------------------------------------------------------------------------------------
 * $this->mode &= ~$flag;
 * 
 * bit a bit operation
 * --------------------------------------------------------------------------------------------------
 * $this->mode:  0110  (IN_LINE + ON_FILE enabled)
 * ~$flag:       1011  (inversion de ON_FILE)
 * AND (&):      0010  (only IN_LINE remains active)
 * 
 * Result
 * |---------------------------------------------------------|
 * | Flag             | Decimal value | Bynary value | State |
 * | ---------------- | ------------- | ------------ | ----- |
 * | LOG_SILENCE_MODE | 1             | 0001         | 0     |
 * | LOG_PRINT_MODE   | 2             | 0010         | 1     |
 * | LOG_FILE_MODE    | 3             | 0100         | 0     |
 * |---------------------------------------------------------|
 * 
 * LOG_FILE_MODE have been disables
 * The others flags remain the same
 * 
 * 
 * Visual summary
 * --------------------------------------------------------------------------------------------------
 * Before: 0110  (IN_LINE + ON_FILE)
 * Invert: 1011  (~ON_FILE)
 * AND:    0010  (IN_LINE solo)
 */

namespace App\Support\Helpers;

// Native
use 
  Exception,
  Throwable,

// Application
  // Contracts
    // Interfaces
    App\Contracts\Interfaces\ISingleton,
  // Support
    // Facades
    App\Support\Facades\Config,
    // Traits 
    App\Support\Traits\ToStringTrait,
    App\Support\Traits\SingletonTrait,
    App\Support\Traits\LogSaverTrait,
    // Types
    App\Support\Types\LogLevels;

class Logger implements ISingleton{
  use 
    ToStringTrait,
    SingletonTrait,
    LogSaverTrait;
  
  private int|null $mode = NULL;
  private LogLevels|null $level = NULL;
  private string|null $File = NULL;
  public string|null $file {
    get => $this->File;
  }


  // public function __construct(?int $mode = NULL, ?string $file = NULL) {
  public function __construct(?array $opts = []) {
    if (!!!empty($opts))
      $this->set($opts);
  }

  /**
   * Create a new Logger instance.
   *
   * @param array<string, mixed>|null $opts
   * @return static
   */
  public function Init(?array $opts = []): static {
    if (!!!empty($opts)) {
      self::$instance ??= static::getInstance($opts);

      $this->mode ??= self::$instance->mode;
      $this->File ??= self::$instance->file;
      $this->level ??= self::$instance->level ?? LogLevels::INFO;
    }

    return self::$instance;
  }

  /**
   * Retrieve if the Logger has been set up
   * 
   * @return bool
   */
  public function IsSettedUp(): bool {
    return NULL !== $this->mode && file_exists($this->File);
  }

  /**
   * Summary of Log
   * 
   * @param string $msg
   * @param mixed $args
   * @param LogLevels|null $lvl Log level (debug, info, warning, error, critical)
   * @return bool
   */
  public function Log(
    string $msg, 
    ?int $errCode = NULL,
    // Exception|ParseError|null $e = NULL,
    ?Throwable $e = NULL,
    ?LogLevels $lvl = NULL
  ): bool {
    if (!!!$msg) return FALSE;

    if (Config::g('debug'))
      return $this->WrFileLog(
        msg: $msg, 
        lvl: $lvl ?? $this->level ?? LogLevels::DEBUG, 
        errCode: $errCode, 
        e: $e
      );

    if (!!!$this->IsLoggable())
      return FALSE;

    if ($this->mode & LOG_PRINT_MODE)
      return $this->PrLog($msg, $errCode, $e, $lvl);

    if ($this->mode & LOG_FILE_MODE)
      return $this->WrFileLog(
        msg: $msg, 
        lvl: $lvl ?? LogLevels::cases()[$this->mode] ?? $this->level ?? LogLevels::INFO, 
        errCode: $errCode, 
        e: $e
      );

    return FALSE;    
  }

  public function setMode(int $mode): void {
    $this->mode = $mode;
  }

  public function EnableFlag(int $flag): void {
    $this->mode |= $flag;
  }

  public function DisableFlag(int $flag): void {
    $this->mode &= ~$flag;
  }

  /**
   * Set up the values of each class attribute if it exists.
   * 
   * @param array<string|mixed> $opts
   * @return void
   */
  private function set(?array $opts = []): void { 
    if (empty($opts)) return;

    $this->mode = $opts['mode'] ?? $this->mode;
    $this->File = $opts['file'] ?? $this->File;
  }

  /**
   * Check if logging should be performed based on mode and level.
   * 
   * @param string|null $mode
   * @return bool
   */
  private function IsLoggable(int $mode = 0): bool {
    $mode ??= $this->mode;

    // If silence mode is enabled, don't log anything
    if ($mode & LOG_SILENCE_MODE)
      return FALSE;
    
    // If no level specified or at least one output mode is enabled, it's loggable
    return ($mode & LOG_PRINT_MODE) || ($mode & LOG_FILE_MODE);
  }

  private function BuildLogEntry(
    string $msg, 
    ?int $errCode = NULL,
    // Exception|ParseError|null $e = NULL,
    ?Throwable $e = NULL,
    ?LogLevels $lvl = NULL
  ): string {
    $msg ??= $e?->getMessage() ?? '';
    $lvl ??= strtoupper($this->level?->value ?? LogLevels::INFO->value);
    $errCode ??= $e?->getCode() ?? NULL;
    $errFile = $e?->getFile() ?? NULL;
    $errLine = $e?->getLine() ?? NULL;
    $errTrace = $e?->getTraceAsString() ?? NULL;
    
    $tmstamp = date('Y-m-d H:i:s');
    $errNumber = $errCode ? "+ERROR NUMBER+: ".$errCode : "";
    $errFile = $errFile ? "\r\n +FILE".($errLine ? "(in line no: $errLine)" : "")."+: " . $errFile : '';
    $lgEntry = "[$tmstamp] ".strtoupper($lvl->value)." $errNumber \r\n $msg \r\n $errFile \r\n\r\n $errTrace".PHP_EOL;

    return $lgEntry;
  }

  /**
   * Print log message to output (stdout/echo).
   * 
   * @param string $msg
   * @param string|null $lvl
   * @return bool
   */
  private function PrLog(
    string $msg, 
    ?int $errCode = NULL,
    // Exception|ParseError|null $e = NULL,
    ?Throwable $e = NULL,
    ?LogLevels $lvl = NULL
  ): bool {
    if(!!!$msg) return FALSE;

    echo "<br><br>" . $this->BuildLogEntry($msg, $errCode, $e, $lvl). "<br><br>";
    
    return TRUE;
  }

  /**
   * Save log message on the log file path in $file
   * 
   * @param string $msg
   * @param int|null $errCode
   * @param Exception|null $e
   * @return bool
   */
  private function WrFileLog(
    string $msg,
    ?LogLevels $lvl = NULL, 
    ?string $fPath = NULL,
    ?int $errCode = NULL,
    // Exception|ParseError|null $e = NULL,
    ?Throwable $e = NULL,
  ): bool {
    if(!!!$msg) return FALSE;
    
    $fPath ??= $this->File ?? '';
    if (!!!file_exists($fPath))
      return FALSE;
    if(!!!($handleF = fopen($fPath, 'a')))
      return FALSE;
    if(!!!fwrite($handleF, $this->BuildLogEntry(Str::RealNl2Br($msg, TRUE), $errCode, $e, $lvl)))
      return FALSE;

    return fclose($handleF);
  }
}