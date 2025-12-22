<?php declare(strict_types=1);

namespace App\Providers\Mail\Clients;

use Swiftmailer\Swiftmailer as Mailer;

class SwiftMailer {
  public static Mailer $mailer;

  public function __construct() {
    $this->mailer = new Mailer( 'smtp://localhost' );
  }
}
