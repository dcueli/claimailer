<?php

declare(strict_types=1);

namespace App\Support\Helpers;

final class Price {
  /**
   * Fomat a given price.
   * If has not decimals, have to control not set X.00 by default.
   * 
   * @param int|float|string $givenPrice
   * @param string $decPoint
   * @return float|int|string
   */
  public static function PriceFormat(
    int|float|string $givenPrice,
    string $decPoint = ','
  ): int|float|string {
    if (empty($givenPrice) || '0' === $givenPrice || '0.00' === $givenPrice)
      return $givenPrice;

    $control = explode('.', $givenPrice);

    foreach ($control as $con) $cheat = $con;

    return ('00' == $cheat || 1 === count($control))
      ? $givenPrice = number_format($givenPrice, 0, $decPoint, '.')
      : $givenPrice = number_format($givenPrice, 2, $decPoint, '.');
  }

  /**
   * Round up the value with the given precision in the parameter $precision
   *
   * @param float $number
   * @param int $precision
   * @return float
   */
  public static function roundUp(
    float $number, 
    int $precision = 2, 
    bool $returnNull = false
  ): float|int|null {
    if (empty($number)) return 0;

    return empty($number)
      ? ($returnNull ? NULL : 0.00)
      : round((float)$number, $precision, PHP_ROUND_HALF_UP);
  }

  /**
   * Round down the value with the given precision in the parameter $precision
   *
   * @param float $number
   * @param int $precision
   * @return float
   */
  public static function roundDown(
    float $number, 
    int $precision = 2, 
    bool $returnNull = false
  ): float|int|null {
    if (empty($number)) return 0;

    return empty((float)$number)
      ? ($returnNull ? NULL : 0.00)
      : round((float)$number, $precision, PHP_ROUND_HALF_DOWN);
  }

  /**
   * Return price with added tax
   * 
   * @param float $price
   * @param int|null $tax
   * @return float
   */
  public function addTax(
    float $price, 
    ?int $tax = NULL
  ): float {
    if (empty($price)) return 0.00;

    // @dcueli :: TODO
    // Set tax value depending on default tax value
    // ----------------------------------------------------
    // if (empty($tax)) 
    // $tax = session()->get('generalconfig.default_tax',
    //   session()->get(
      //     'generalconfig.tax_1',
      //     session()->get(
        //       'generalconfig.tax_2',
        //       session()->get(
          //         'generalconfig.tax_3',
          //         session()->get('generalconfig.tax_4')
      //       )
      //     )
      //   )
      // );
    // ====================================================
    
    return self::roundUp($price * (($tax / 100) + 1));
  }

  /**
   * Return the original amount price without the applied tax
   * 
   * @param float $price
   * @param int|null $tax
   * @return float
   */
  public static function substractTax(
    float $price, 
    ?int $tax = NULL
  ): float {
    if (empty($price)) return 0.00;
    
    // @dcueli :: TODO
    // Set tax value depending on default tax value
    // ----------------------------------------------------
    // if (empty($tax)) 
      // $tax = session()->get(
      //   'generalconfig.default_tax',
      //   session()->get(
      //     'generalconfig.tax_1',
      //     session()->get(
      //       'generalconfig.tax_2',
      //       session()->get(
      //         'generalconfig.tax_3',
      //         session()->get('generalconfig.tax_4')
      //       )
      //     )
      //   )
      // );
    // ====================================================

    return self::roundUp($price / (($tax / 100) + 1));
  }

  /**
   * Return just tax amount substracted the original amount price
   * 
   * @param float $price
   * @param int $tax
   * @return float
   */
  public function getSubstractedTax(
    float $price, 
    int $tax
  ): float {
    if (empty($price)) return 0.00;

    if (!!!empty($tax))
      return $price - self::substractTax($price, $tax);
    // else 
    // @dcueli :: TODO
    // Set tax value depending on default tax value
    // ----------------------------------------------------
    // if (empty($tax)) 
      // $tax = session()->get(
      //   'generalconfig.default_tax',
      //   session()->get(
      //     'generalconfig.tax_1',
      //     session()->get(
      //       'generalconfig.tax_2',
      //       session()->get(
      //         'generalconfig.tax_3',
      //         session()->get('generalconfig.tax_4')
      //       )
      //     )
      //   )
      // );
    // ====================================================

    return $price - (self::roundUp($price / (($tax / 100) + 1)));
  }

  /**
   * Return just the amount of tax if the price has not it applied
   * 
   * @param float $price
   * @param int $tax
   * @return float
   */
  public function importTax(
    float $price, 
    ?int $tax = NULL
  ): float {
    if (empty($price)) return 0.00;

    // @dcueli :: TODO
    // Set tax value depending on default tax value
    // ----------------------------------------------------
    // if (empty($tax)) 
      // $tax = session()->get(
      //   'generalconfig.default_tax',
      //   session()->get(
      //     'generalconfig.tax_1',
      //     session()->get(
      //       'generalconfig.tax_2',
      //       session()->get(
      //         'generalconfig.tax_3',
      //         session()->get('generalconfig.tax_4')
      //       )
      //     )
      //   )
      // );
    // ====================================================

    return self::roundUp($price * ($tax / 100));
  }

  /**
   * Return just the amount of tax if the price already has it applied
   * 
   * @param float $price
   * @param int $tax
   * @return float
   */
  public function importTaxInclude(
    float $price, 
    ?int $tax = NULL
  ): float {
    if (empty($price)) return 0.00;

    // @dcueli :: TODO
    // Set tax value depending on default tax value
    // ----------------------------------------------------
    // if (empty($tax)) 
      // $tax = session()->get(
      //   'generalconfig.default_tax',
      //   session()->get(
      //     'generalconfig.tax_1',
      //     session()->get(
      //       'generalconfig.tax_2',
      //       session()->get(
      //         'generalconfig.tax_3',
      //         session()->get('generalconfig.tax_4')
      //       )
      //     )
      //   )
      // );
    // ====================================================

    return ($price - self::substractTax($price, $tax));
  }

}
