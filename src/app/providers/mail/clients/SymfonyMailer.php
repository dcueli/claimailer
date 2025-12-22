<?php declare(strict_types=1);

namespace App\Providers\Mail\Clients;

use 
  Symfony\Component\Mailer\Mailer,
  Symfony\Component\Mailer\Transport,
  Symfony\Component\Mime\Email;

// TODO: Implement the Symfony Mailer client wrapper here.
class SymfonyMailer {
  public static Mailer $mailer;
  public static Email $email;

  public function __construct(string $transport, mixed ...$args) {
    self::$mailer = new Mailer(Transport::fromDsn($transport));
    self::$email  = (new Email())
      ->from($args[0]['from'] ?? '')
      ->to($args[0]['to'] ?? '')
      ->cc($args[0]['cc'] ?? '')
      ->bcc($args[0]['bcc'] ?? '')
      ->replyTo($args[0]['replyTo'] ?? '')
      ->priority(Email::PRIORITY_HIGH)
      ->subject($args[0]['subject'] ?? '')
      ->text($args[0]['text'] ?? '')
      ->html($args[0]['html'] ?? '');
  }
}
