<?php declare(strict_types=1);

namespace App\Support\Traits;
// Native
use Throwable;

trait ToStringTrait {
  protected function toString(): string {
    $ref = new \ReflectionObject($this);
    $data = [];
    foreach ($ref->getProperties() as $prop) {
      $prop->setAccessible(true);
      $data[$prop->getName()] = $prop->getValue($this);
    }

    try {
      $serializable = $this->toSerializable($data);
      $json = json_encode($serializable, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
      if ($json !== false && json_last_error() === JSON_ERROR_NONE) {
        return $json;
      }
    } catch (Throwable $e) {
      // Caer al fallback
    }

    return print_r($data, true);
  }

  protected function toSerializable($var, int $depth = 0) {
    if ($depth > 10) return '...';

    if (is_null($var) || is_scalar($var)) return $var;
    if (is_bool($var)) return $var;

    if (is_array($var)) {
      $out = [];
      foreach ($var as $k => $v)
        $out[$k] = $this->toSerializable($v, $depth + 1);

      return $out;
    }

    if (is_object($var)) {
      if ($var instanceof \JsonSerializable) {
        try {
          return $this->toSerializable($var->jsonSerialize(), $depth + 1);
        } catch (Throwable $e) {
          return 'Object(' . get_class($var) . ')';
        }
      }

      if ($var instanceof \DateTimeInterface) {
        return $var->format(DATE_ATOM);
      }

      if (method_exists($var, 'toArray')) {
        try {
          return $this->toSerializable($var->toArray(), $depth + 1);
        } catch (Throwable $e) {
          // Ignore at the moment
        }
      }

      $arr = @get_object_vars($var);
      if (is_array($arr) && $arr !== [])
        return $this->toSerializable($arr, $depth + 1);

      try {
        $j = json_encode($var, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($j !== false && json_last_error() === JSON_ERROR_NONE) {
          return json_decode($j, true);
        }
      } catch (Throwable $e) {
        // Ignore at the moment
      }

      return 'Object(' . get_class($var) . ')';
    }

    if (is_resource($var)) return 'resource';

    return (string)$var;
  }

  public function __toString(): string {
    try { return $this->toString(); }
    catch (Throwable $e) { return static::class.'::__toString error'; }
  }
}
