<?php

declare(strict_types=1);

namespace App\Support\Helpers;

use ArrayAccess;
use stdClass;
use Closure;
use Exception;
use UnexpectedValueException;

final class Helpers {
  /**
   * Return the real value of the given value or the default one
   *
   * @param  mixed  $value
   * @return bool
   */
  public static function Realvalue(mixed $value, mixed $default = NULL): mixed {
    return $value instanceof Closure ? $value() : ($value ?? $default);
  }

  /**
   * Resize image dimension in base a new size and its side
   * 
   * @param string $img, 
   * @param string $side = 'W' | 'H', 
   * @param ?int $newSize = NULL
   * 
   * @return array<string, int>
   */
  public static function getResizeImage(
    string $img, 
    string $side = 'W' | 'H', 
    ?int $newSize = NULL
  ): array {
    list($oldW, $oldH) = getimagesize($img);
    
    // Set the size of the side to resize
    $sideValue = ('W' === $side) ? $oldW : $oldH;
    // Set resize array
    if ($sideValue > $newSize) {
      $resize['width'] = $oldW;
      $resize['height'] = $oldH;
    } else {
      $side_versus = ($side == 'W') ? $oldH : $oldW;
      // Set percentage to resize
      $pcent = $newSize / $sideValue;
      $value_versus = $side_versus * $pcent;
      // Set new dimensions to return
      $resize['width'] = (int) number_format(($side == 'W') ? $newSize : $value_versus, 0);
      $resize['height'] = (int) number_format(($side == 'H') ? $newSize : $value_versus, 0);
    }

    return $resize;
  }

  /**
   * Returns a random String with a minimum length of $length
   *
   * @param int $length    -> Number of characters
   * @param bool $minus    -> If True, string contains lowercase letters
   * @param bool $mayus    -> If True, string contains uppercase letters
   * @param bool $num      -> If True, string contains numbers
   * @param bool $symbols  -> If True, string contains special characters
   * @return string|false
   */
  public static function Rndpwd(
    int $length = 8, 
    bool $minus = true, 
    bool $mayus = true, 
    bool $num = true, 
    bool $symbols = true
  ) {
    if (empty($length)) {
      return false;
    } else {
      $pw = '';
      $length = abs($length);
      $sarr = array(
        'minus'   => 'abcdefghijklmnopqrstuvwxyz',
        'mayus'   => 'ABCDEFGHiJKLMNOPQRSTUVWXYZ',
        'num'     => '0123456789',
        'symbols' => '~!@#$%^&*()_+-=\\|[]{};:,./<>?'
      );

      $s = ($minus ? $sarr['minus'] : '') . ($mayus ? $sarr['mayus'] : '') . ($num ? $sarr['num'] : '') . ($symbols ? $sarr['symbols'] : '');
      if (empty($s)) return false;
      else {
        // Concatenate $length times a selected character by the random index of the string $s.
        for ($i = 0; $i < abs($length); $i++)
          $pw .= substr($s, mt_rand(0, (strlen($s) - 1) ) );

        // @dcueli -> TODO testPassword();
        // testPassword(pw);
        return $pw;
      }
    }
  }

  /**
   * Return two random letters group or FALSE if an error occurs
   *
   * @param int $totalwords -> Number of character groups, 2 by default
   * @param int $wordlength -> Number of characters contained inside groups, 4 by defaultpor defecto 4
   * @return string|false
   */
  public static function getRndKeywords(
    int $totalwords = 2, 
    int $wordlength = 4,
    string $charType = 'alphabetic'
  ): string|false {
    try {
      if (empty($totalwords)) return false;
      else {
        $words = [];
        for ($i = 0; $i < $totalwords; $i++) {
          $words[$i] = '';
          for ($j = 0; $j < $wordlength; $j++) {
            $words[$i] .= chr(
               'alphanumeric' === $charType 
               ? mt_rand(35, 122) 
               : ($charType == 'numeric' 
                  ? mt_rand(48, 57) 
                  : mt_rand(97, 122)
              )
            );
          }
        }

        return implode(' ', $words);
      }
    } catch (Exception $e) {
      return false;
    }
  }

  /**
   * Quita todos los Caracteres extraños de una cadena y lo sustituye por su equivalente
   * los espacios en blanco son sustituidos por '-'
   *
   * @param string $str
   * @param bool $decode
   * @return string
   */
  public static function ReplaceSpecialChars(
    string $str, 
    bool $decode = false
  ): string {
    if (empty($str)) return $str;

    if ($decode) $str = mb_convert_encoding($str, 'UTF-8');

    $str = trim($str);
    $str = str_replace(
      array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
      array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
      $str
    );
    $str = str_replace(
      array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
      array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
      $str
    );
    $str = str_replace(
      array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
      array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
      $str
    );
    $str = str_replace(
      array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
      array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
      $str
    );
    $str = str_replace(
      array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
      array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
      $str
    );
    $str = str_replace(
      array('ñ', 'Ñ', 'ç', 'Ç'),
      array('n', 'N', 'c', 'C'),
      $str
    );
    //Esta parte se encarga de eliminar cualquier caracter extraño
    $str = str_replace(' ', '-', $str);
    //Esta parte se encarga de eliminar cualquier caracter extraño
    $str = str_replace(
      [
        "\\",
        "¨",
        "º",
        "~",
        "#",
        "@",
        "|",
        "!",
        "\"",
        "·",
        "$",
        "%",
        "&",
        "/",
        "(",
        ")",
        "?",
        "'",
        "¡",
        "¿",
        "[",
        "^",
        "`",
        "]",
        "+",
        "}",
        "{",
        "¨",
        "´",
        ">",
        "< ",
        ";",
        ",",
        ":",
        "."
      ],
      '',
      $str
    );
    return $str;
  }

  /**
   * Build a text message with the whole parts of $parts array, concatening to $message if not NULL
   * 
   * @param string $message
   * @param mixed $parts
   * @return string
   */
  public static function BuildMessage(
    string $message, 
    ?array $parts = []
  ): string {
    return $message ?? vsprintf($message, $parts);
  }

  /**
   * Convert a string to a byte
   * The format of the $input parameter must be 10MB, 10GB, etc.
   * 
   * @param string $input
   * @return int|null
   */
  public static function ByteConvert($input = NULL): int|null {
    preg_match('/(\d+)(\w+)/', $input, $matches);
    $type = strtoupper(end($matches));
    $output = NULL;

    switch ($type) {
      case "B":
        $output = $matches[1];
        break;
      case "K":
      case "KB":
        $output = $matches[1] * pow(1024, 1);
        break;
      case "M":
      case "MB":
        $output = $matches[1] *  pow(1024, 2);
        break;
      case "G":
      case "GB":
        $output = $matches[1] *  pow(1024, 3);
        break;
      case "T":
      case "TB":
        $output = $matches[1] *  pow(1024, 4);
        break;
      case "P":
      case "PB":
        $output = $matches[1] *  pow(1024, 5);
        break;
      case "Y":
      case "YB":
        $output = $matches[1] *  pow(1024, 6);
        break;
    }
    return $output;
  }

  /**
   * Convert the size indicated in the parameter $size from its scale 
   * indicated in $scale_from to scale indicated in $scale_to
   *
   * @param integer $size
   * @param string $scale_to
   * @param string $scale_from
   * @return string|int|null
   */
  public static function ConvertToSize(
    int $size = 0, 
    string $scale_to = 'MB', 
    string $scale_from = 'B'
  ): string|int|float {
    if (empty($size)) return $size;

    $size = floatval($size);
    $bytes = [
      'YB' => pow(1024, 6),
      'Y'  => pow(1024, 6),
      'PB' => pow(1024, 5),
      'P'  => pow(1024, 5),
      'TB' => pow(1024, 4),
      'T'  => pow(1024, 4),
      'GB' => pow(1024, 3),
      'G'  => pow(1024, 3),
      'MB' => pow(1024, 2),
      'M'  => pow(1024, 2),
      'KB' => 1024,
      'K'  => 1024,
      'B'  => 1,
    ];

    if (
      !!!array_key_exists($scale_to, $bytes) || 
      !!!array_key_exists($scale_from, $bytes)
    ) return $size;

    return str_replace(
      '.', 
      ',', 
      strval(
        round(($size / $bytes[$scale_to]), 
        2
    ) ) ) . ' ' . $scale_to;
  }

  /**
   * Check if the given value is a valid email address
   *
   * @param mixed $email
   * @return bool
   */
  public static function IsEmail(mixed $email = NULL): bool {
    $email ??= self::Realvalue($email);
    if(empty($email) || !!!is_string($email))
      return FALSE;

    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
  } 

  /**
   * Determine whether the given value is array accessible.
   *
   * @param  mixed  $value
   * @return bool
   */
  public static function IsArray($value): bool {
    return is_array($value) || ($value instanceof ArrayAccess);
  }

  /**
   * Check if string has JSON format
   *
   * @param string $str
   * @return bool
   */
  public static function IsJson($str): bool {
    @json_decode($str);
    return (JSON_ERROR_NONE == json_last_error());
  }

  /**
   * Return the file extension or Mime type of the given file
   * 
   * @param string $pathfile
   * @param string $infotype
   * @return bool|string
   */
  public static function getFileType(string $pathfile, string $infotype = 'extension'): bool|string {
    if (empty($pathfile)) 
      return false;

    $realPath = realpath($pathfile);
    if (!!!file_exists($realPath))
      return false;

    return match ($infotype) {
      'mimetype' => (function() use ($realPath): string {
        if (function_exists('finfo_file')) {
          $finfo = finfo_open(FILEINFO_MIME_TYPE);
          $type = finfo_file($finfo, $realPath);
          finfo_close($finfo);
          return $type;
        }

        return pathinfo($realPath)['extension'];
      })(),
      default => (function() use ($realPath): string {
        $finfo = pathinfo($realPath);
        return $finfo['extension'];
      })()
    };
  }

  /**
   * Get a native stdClass instantiated object with the attributes defined
   * in the parameter $attributes
   *
   * @param array $attributes
   * @return stdClass
   */
  public static function getStdClassObject(array $attributes = []): stdClass {
    $object = new stdClass;

    if (empty($attributes)) return $object;

    foreach ($attributes as $attr_name => $attr_value)
      $object->{$attr_name} = !!!empty($attr_value) ?: $attr_value;

    return $object;
  }

  public static function Basepath(string $path = ''): string {
    return realpath(realpath(__DIR__.'/../').($path ? DS.$path : $path));
  }

  public static function Realpath(string $basePath, array $paths = []): string {
    if(empty($basePath))
      return self::Basepath('/');
    
    $realPath = self::Realtrim(realpath($basePath), DS.'..'.LS).LS;
    
    if(empty($paths))
      return $realPath;

    foreach($paths as $currPath)
      $realPath = realpath($realPath.self::Realtrim($currPath, DS.'..'.LS));

    return $realPath;
  }  
  
  public static function Realtrim(
    string $str, 
    ?string $chars = NULL, 
    ?string $encod = NULL
  ): string {
    if(8 > PHP_MAJOR_VERSION)
      return trim($str, $chars);

    return mb_trim($str, $chars, $encod);
  }
}
