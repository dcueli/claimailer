<?php declare(strict_types=1);

namespace App\Support\Helpers;

use ArrayAccess;

use App\Support\Helpers\Helpers;

final class Arr {
  /**
   * Determine whether the given value is array accessible.
    *
    * @param  mixed $value
    * @return bool
    */
  public static function Is($value): bool {
    return is_array($value) || ($value instanceof ArrayAccess);
  }
  
  /**
   * Alias of Is method
   *
   * @param  mixed  $value
   * @return bool
   */
  public static function IsArr($value): bool {
    return self::Is($value);
  }

  /**
   * Get an item from an array using "dot" notation.
   *
   * @param  array<mixed|mixed> $arr
   * @param  mixed $key
   * @param  mixed $def
   * @return mixed
   */
  public static function GetEl(array $arr, mixed $key, $def = NULL): mixed {
    if (empty($arr))
      return Helpers::RealValue($def);
    if (empty($key))
      return $arr;
    if (array_key_exists($key, $arr))
      return $arr[$key];

    if (false === strpos($key, '.'))
      return $arr[$key] ?? Helpers::RealValue($def);

    $seg = explode('.', $key);
    $el = array_shift($seg);
    if (array_key_exists($el, $arr)) {
      if (self::IsArr($arr[$el]))
        return self::GetEl($arr[$el], implode('.', $seg), $def);
      else
        return Helpers::RealValue($arr[$el]);
    } else {
      return Helpers::RealValue($def);
    }
  }

  /**
   * Retrieve an Array with unique values from an array of objects or associative 
   * arrays, based on a key.
   * Here, other implementation without function <array_reduce>:
   *
   * $uniqueMap = [];
   * foreach ($array as $item) {
   *     $keyValue = NULL;
   * 
   *     if (is_object($item) && isset($item->{$key}))
   *         $keyValue = $item->{$key};
   *     elseif (is_array($item) && isset($item[$key]))
   *         $keyValue = $item[$key];
   * 
   *     if (NULL !== $keyValue)
   *         $uniqueMap[$keyValue] = $item;
   * }
   *
   * @param string $key The key by which uniqueness will be verified.
   * @param array $array Entry array.
   * @return array Array without duplicates
   */
  public static function UniqueBy(string $key, array &$array): array {
    // The native function <array_reduce> iterate on Array ($array), passing the 
    // previous iteration result ($carry) to the next iteration. We start with 
    // an empty array [] and, at the end use <array_values> the function to 
    // re-indexes the array and remove the keys
    return array_values(array_reduce(
      $array, 
      function ($carry, $item) use ($key): mixed {
        $keyValue = NULL;

        if (is_object($item) && isset($item->{$key}))
          $keyValue = $item->{$key};
        elseif (self::IsArr($item) && isset($item[$key]))
          $keyValue = $item[$key];

        // If it has a valid key(p.ej. el email), used as index in Array ($array).
        // This automatically overwrites duplicates. 
        if (NULL !== $keyValue)
          $carry[$keyValue] = $item;

        // Devolvemos el array $carry para la siguiente iteración.
        return $carry;
      }, [])
    );
  }

  /**
   * Split string in parts using multiple possible separators.
   * Remove espaces and empty entries.
   *
   * @param string $str
   * @param string $delimiters By default (espace, dot, comma, semicolon and pipe)
   * @param bool $rmvDupl If TRUE normalize and remove duplicates
   * @return array
   */
  public static function SmartExplode(
    string $str, 
    string $delimiters = ' .,;|', 
    bool $rmvDupl = FALSE
  ): array {
    // Escape delimiters for regular expressions
    $pattern = '/[' . preg_quote($delimiters, '/') . ']+/';
    // Break by por .,;| and clean espaces around
    $parts = preg_split($pattern, $str, -1, PREG_SPLIT_NO_EMPTY);

    // Normalize and remove duplicates
    if ($rmvDupl)
      return array_values(array_unique(array_map('trim', $parts)));

    // Fast Trim by each element
    foreach ($parts as &$p)
      $p = trim($p);
    // Clean referenced memory
    unset($p);

    return $parts;
  }

  /**
   * Filter an Array by keys
   * 
   * @param array|string $data
   * @param mixed $keys
   * @return array
   */
  public static function Filter(
    array|string $data, 
    mixed $keys = NULL, 
  ): array {
    if (empty($data) || empty($keys))
      return $data;

    if (is_object($keys))
      // Normalize keys to an Array<string>
      $keys = is_iterable($keys) ? iterator_to_array($keys) : [$keys];
    elseif(is_string($keys))
      // String to Array transform
      // Get only the values from $keys to can filter by those keys
      $keys = self::SmartExplode($keys);


    static $cache = [];
    $normalized = [];
    foreach ($keys as $k) {
      if (is_object($k)){
        $prop = isset($k->value) ? 'value' : 'name';
        $normalized[] = $k->{$prop};
        continue;
      }

      $normalized[] = $k;
    }
      
    // Cache micro-optimized for arrays of keys
    $hash = md5(implode(',', $normalized));
    $keys = $cache[$hash] ??= array_flip($normalized);

    // Return only the values from keys 
    return array_intersect_key($data, $keys);
  }

  /**
   * Returns the modified Array ‘$arr’ in another Array with the structure [id => value]
   * depending on the parameters ‘$idxField’ and ‘$valField’ respectively.
   * The indexes indicated in the above parameters must exist in the Array.
   * 
   * @param array $arr
   * @param string $idxField
   * @param string $valField
   * @return array
   */
  public function Pluck(
    array $arr, 
    string $idxField, 
    string $valField
  ): array {
    if (empty($arr) || !!!self::IsArr($arr))
      return $arr;   

    if (
      !!!array_key_exists($idxField, $arr) || 
      !!!array_key_exists($valField, $arr)
    ) return $arr;

    $result = [];
    foreach ($arr as $item)
      $result[$item[$idxField]] = $item[$valField];

    return $result;
  }

  function AreEquals(
    array $a, 
    array $b, 
    bool $ignoreOrder = false, 
    bool $ignoreKeys = false
  ): bool {
    // Fater case, strict equiality (keys, order, types)
    if (!!!$ignoreOrder && !!!$ignoreKeys)
      return $a === $b;

    // Ignore keys (only compare values)
    if (!!!$ignoreOrder && $ignoreKeys) {
      $a = array_values($a);
      $b = array_values($b);

      if ($ignoreOrder) {
        sort($a);
        sort($b);
      }

      return $a === $b;
    }

    // Ignore order but keep keys/pairs key-value
    if ($ignoreOrder && !!!$ignoreKeys) {
      ksort($a);
      ksort($b);
    }

    return $a === $b;
  }

}
