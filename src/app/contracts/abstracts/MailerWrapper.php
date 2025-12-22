<?php declare(strict_types=1);

namespace App\Contracts\Abstracts;

use
// Native
UnexpectedValueException,

// Application
  // Interfaces
  App\Contracts\Interfaces\IMailer,
  App\Contracts\Interfaces\IGetter,
  // Providers
    // DTOs
    App\Providers\Mail\Dtos\MailDataDTO,
  // Support
    // Facades
    App\Support\Facades\Config;


abstract class MailerWrapper extends Getters implements IMailer, IGetter{
  private object $Mailer;
  public object $mailer {
    get => $this->Mailer;
    set => $this->Mailer = $value;
  }

  protected function CheckClient(?object &$client = NULL): object{
    $client ??= $this ?? NULL;
    if (empty($client) || !!!is_object($client))
      throw new UnexpectedValueException(basename(__CLASS__)."::setCredentials ERROR+ " . date('d/m/Y H:i:s') . " - The mailer client instance is not defined, so the mail server credentials could not be set.\n", 1);

    return $client;
  }

  /**
   * Set the mail credential to be able to send emails
   * 
   * @param array|null $client
   * @return self
   */
  abstract public function setCredentials(?array $props = NULL, ?object &$client = NULL): self;

  abstract public function setRecipients(
    ?MailDataDTO $mailData = NULL,
    ?array $setters = NULL,
    ?object &$client = NULL
  ): self;

  abstract public function Send(?object $email = NULL): void;
}