<?php declare(strict_types=1);

namespace App\Services\Config;

// Native
use
  InvalidArgumentException,

// Application
  // Interfaces
  App\Contracts\Interfaces\ISingleton,
  App\Contracts\Interfaces\ISegetter,
  // Helpers
  App\Support\Helpers\Arr,
  App\Support\Helpers\Helpers,
  // Traits
  App\Support\Traits\SingletonTrait;

class ConfigurationService implements ISingleton, ISegetter {
  use SingletonTrait;

  /**
   * @var array<string, mixed>
   */
  private array $configOption = [];

  /**
   * Create a new Service instance.
   *
   * @param array<string, mixed> $configOptions
   * @return void
   */
  public function __construct(?array $configOptions = []){
    if (!!!empty($configOptions))
      $this->setOptions($configOptions);
      // $this->init($configOptions);
  }

  /**
   * Create a new Service instance.
   *
   * @param array<string, mixed>|null $configOptions
   * @return static
   */
  public function init(?array $configOptions = []): static {
    if (!!!empty($configOptions)) {
      self::$instance ??= static::getInstance($configOptions);

      if (empty($this->configOption))
        $this->configOption = self::$instance->configOption;
    }

    return self::$instance;
  }

  /**
   * Retrieve if the Configuration has been set up
   * 
   * @return bool
   */
  public function IsSettedUp(): bool {
    return !!!empty($this->configOption) && 0 < count($this->configOption);
  }

  /**
   * Initializes configuration paths from an array.
   * 
   * @param array<string, string> $configOptions Associative array mapping config keys to its values.
   * @throws InvalidArgumentException
   * @return void
   */
  public function setOptions(array $configOptions = []): void {
    if (empty($configOptions))
      throw new InvalidArgumentException('The parameters $configOptions array cannot be empty.');

    foreach ($configOptions as $key => $values)
      $this->bind($key, $values);
  }

  /**
   * Add new configuration from files
   * 
   * @param string|int $key
   * @param string $values
   * @return void
   */
  public function bind(string|int $key, string $values): void {
    if (!!!file_exists($values)) return;

    // Get returned file value
    // Not process if the $values is NULL
    $ext = pathinfo($values, PATHINFO_EXTENSION);
    if (NULL === ($values = match (strtolower($ext)) {
      'php'  => require $values,
      'json' => Helpers::IsJson($values) 
        ? json_decode(file_get_contents($values), TRUE)
        : NULL,
      default => is_dir($values) ? realpath($values) : NULL,
    })) return;

    // So the $values is NULL, that means it is not an array of files and it's an array
    // of key/value pairs for the application mailing configuration
    // if (is_string($key))
    $this->set(is_string($key)?$key:NULL, $values);
  }

  /** 
   * Get a configuration by key using dot notation.
   * 
   * @param string $key The key to retrieve (e.g., 'config.DEBUG').
   * @param mixed $default The default value to return if the key is not found.
   * @return mixed The configuration value or the default.
   */
  public function get(?string $key = NULL, mixed $default = NULL): mixed {
    if ('*' === $key || NULL === $key)
      return $this->configOption;

    $el = Arr::GetEl($this->configOption, $key, $default);

    return $el;

    // $keys = explode('.', $key);
    // foreach ($keys as $k)
    //   return $this->configOption[$k] ?? $default;
    // // return array_key_exists($k, $this->configOption) ? $this->configOption[$k] : $default;

    // return $default;
  }

  public function g(?string $key = NULL, mixed $default = NULL): mixed {
    return $this->get($key, $default);
  }

  public function set(?string $key = NULL, mixed $value = NULL): self {
    if (empty($value))
      return $this;

    // Reference to the options of the configuration
    $temp = &$this->configOption;

    // If $key is empty (falsy), must add the values into the index key in $keys
    if (!!!empty($key)) {
      // Separate key into parts and prepare the reference to the main array
      $keys = explode('.', $key);

      // Loop on keys to construct the nested structure
      foreach ($keys as $index => $k) {
        $k = strtolower($k);

        // If the key is the last one, append the value
        if ($index === count($keys) - 1) {
          $temp[$k] = $value;
        } else {
          // If there is not the last one, it ensure that the next level is an array
          if (!!!isset($temp[$k]) || !!!is_array($temp[$k]))
            $temp[$k] = [];

          // Put on the reference to a more inside level
          $temp = &$temp[$k];
        }
      }
    // If not, $value will be a pairs key/value array and must add the values into the main array
    } else {
      foreach($value as $k => $v)
        $temp[strtolower($k)] = $v;
    }

    // Unset the reference to the main array at the end
    unset($temp);

    return $this;
  }

  public function s(string $key, mixed $value): mixed {
    return $this->set($key, $value);
  }

  /**
   * Get the Composer configuration key
   * 
   * @return array|mixed|string Return the configuration Array or NULL if not exists
   */
  public function Composer(?string $key = NULL): array {
    $res = $this->configOption['composer'] ?? [];    

    return (null === $key || '*' === $key)
      ? $res 
      : self::g(strtolower(basename(__FUNCTION__)).'.'.$key);
  }

  /**
   * Get the mailing (server) configuration key
   * 
   * @return mixed Return the configuration Array or NULL if not exists
   */
  public function Mailing(?string $key = NULL): mixed {
    $res = $this->configOption['mailing'] ?? [];

    return (null === $key || '*' === $key)
      ? $res 
      : self::g(strtolower(basename(__FUNCTION__)).'.'.$key);
  }

  /**
   * Reset the singleton instance for testing or to free memory between runs.
   *
   * @return void
   */
  public static function Reset(): void {
    // resetInstance comes from SingletonTrait and is protected
    self::resetInstance();
  }
}
