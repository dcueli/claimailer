<?php declare(strict_types=1);

namespace App\Contracts\Interfaces;

interface IFacade {
  /**
   * Return the name of the registered facade.
   *
   * @return string
   */
  public static function getFacadeAccessor(): string;
}