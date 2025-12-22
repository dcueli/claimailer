<?php declare(strict_types=1);

namespace App\Support\Helpers;

use App\Support\Helpers\Helpers;

final class Str {
  /**
   * Native nl2br function with reverse option
   * 
   * @param $str - string with br's
   * @param $rev - boolean to reverse the operation
   * @return string
   */
  public static function RealNl2Br(string $str, bool $rev = FALSE): array|string|null {
    if($rev)
      return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $str);
      
    return nl2br($str);
  }  

  /**
   * Normalize any separator to a space and split words in an Array
   * 
   * @param string $str
   * @return array|bool
   */
  public static function NormalizeWords(string $str): array {
    return preg_split(
      '/\s+/', 
      preg_replace(
        '/[-_]+/', ' ', 
        strtolower(
          trim($str)
        )
      )
    );
  }

  /**
   * The following methods convert a string to different cases strings conventionsq
   * 
   * @param string $str
   * @return string
   */
  public static function ToPascalCase(string $str): string {
    return str_replace(' ', '', ucwords(
      str_replace(['-', '_'], ' ', strtolower($str)
    ) ) );
  }

  /**
   * @param string $str
   * @return string
   */
  public static function ToCamelCase(string $str): string {
    return lcfirst(self::ToPascalCase($str));
  }

  /**
   * @param string $str
   * @return string
   */
  public static function ToSnakeCase(string $str): string {
      return implode('_', self::NormalizeWords($str));
  }

  /**
   * @param string $str
   * @return string
   */
  public static function ToUpperSnakeCase(string $str): string {
      return strtoupper(self::ToSnakeCase($str));
  }

  /**
   * @param string $str
   * @return string
   */
  public static function ToLowerSnakeCase(string $str): string {
      return strtolower(self::ToSnakeCase($str));
  }

  /**
   * @param string $str
   * @return string
   */  
  public static function ToKebabCase(string $str): string {
      return implode('-', self::NormalizeWords($str));
  }

  /**
   * Transliterate accents, keep letters/numbers/space/dash/underscore and additional 
   * chars in $allowedChars, remove parens
   * 
   * @param string $str
   * @return string
   */
  public static function Sanitize(
    string $str, 
    ?string $allowedChars = NULL, 
    int $maxLength = 160
  ): string {
    // Transliterate accents to ASCII where possible
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str) ?: $str;

    // Allow additional characters if provided in $allowedChars
    // Prepare allowed chars for regex (escape delimiters/specials)
    $allowed = NULL;
    if (!!!empty($allowedChars))
      $allowed = preg_quote($allowedChars, '/');

    // Keep Unicode letters (\p{L}), numbers (\p{N}), whitespace, underscore and dash,
    // plus any additional allowed characters passed in $allowed.
    $pattern = '/[^\p{L}\p{N}\s\-_'. $allowed . ']+/u';
    $s = preg_replace($pattern, '', $s);

    // Replace spaces with underscores
    $s = preg_replace('/\s+/', '_', trim($s));
    // Replace multiple underscores with a single one, I mean, collapse underscores
    $s = preg_replace('/_+/', '_', $s);    

    // Limit length, trim to $maxLength characters using multibyte-safe substring
    if ($maxLength > 0 && mb_strlen($s) > $maxLength)
      $s = mb_substr($s, 0, $maxLength);

    return $s === '' ? 'untitled' : $s;
  }
}
