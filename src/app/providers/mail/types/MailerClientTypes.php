<?php declare(strict_types=1);

namespace App\Providers\Mail\Types;

use
  // Facades
  App\Support\Facades\Config,
  // Mailer clients
  App\Providers\Mail\Clients\PHPMailer,
  App\Providers\Mail\Clients\SymfonyMailer,
  App\Providers\Mail\Clients\SwiftMailer;

enum MailerClientTypes: string {
  case PHPMailer     = PHPMailer::class;
  case SymfonyMailer = SymfonyMailer::class;
  case SwiftMailer   = SwiftMailer::class;

  /**
   * Create the client instance.
   * Pass optional parameters in $args (e.g. debug flag, transport...).
   *
   * @return object|null
   */
  public function Make(...$args): object|null {
    return match ($this) {
      self::PHPMailer     => new PHPMailer(Config::g('debug')),
      self::SymfonyMailer => new SymfonyMailer('smtp://'.Config::Mailing('transport', 'localhost'), $args),
      self::SwiftMailer   => new SwiftMailer(),
      default => NULL
    };
  }

  /**
   * Check if the given object is an instance of the enum value class.
   *
   * @param object|null $obj
   * @return bool
   */
  public function IsInstance(object|null $obj = NULL): bool {
    if (empty($obj)) return FALSE;

    return $obj instanceof $this->value;
  }
}