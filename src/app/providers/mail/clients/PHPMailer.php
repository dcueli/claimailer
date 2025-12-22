<?php declare(strict_types=1);

namespace App\Providers\Mail\Clients;

// RealNl2Br
use
// Native
Throwable,
UnexpectedValueException,

// Third-party
PHPMailer\PHPMailer\PHPMailer as Mailer,
PHPMailer\PHPMailer\Exception as PHPMailerException,

// Application 
  // Contracts
  App\Contracts\Abstracts\MailerWrapper,
  // Providers
    // DTOs
    App\Providers\Mail\Dtos\MailDataDTO,
    App\Providers\Mail\Dtos\RecipientDTO,
  // Support
    // Types
    App\Support\Types\LogLevels,
    // Facades
    App\Support\Facades\Config,
    App\Support\Facades\Logger,
    // Helpers
    App\Support\Helpers\Arr,
    App\Support\Helpers\Helpers,
    App\Support\Helpers\Str;

class PHPMailer extends MailerWrapper {
  // Default timeout in seconds for mail operations in case none is set in configuration
  const DEFAULT_TIMEOUT = 30;

  public function __construct(bool $exceptions = TRUE) {
    $this->mailer = new Mailer($exceptions ?? Config::g('debug'));
  }

  /**
   * Set the mail credential to be able to send emails
   * 
   * @param array|null $client
   * @return self
   */
  public function setCredentials(?array $props = NULL, ?object &$client = NULL): self {
    $client = $this->CheckClient($client);

    if(empty($credentialsProps = Config::Mailing('client.props.server')))
      throw new UnexpectedValueException(basename(__CLASS__)."::setCredentials ERROR+ " . date('d/m/Y H:i:s') . " - The mailer client server properties are not defined in the configuration file, so the mail server credentials could not be set.\n", 2);

    foreach($credentialsProps as $propVal) {
      $propVal = ucfirst((string)$propVal);
      if (!!!property_exists($client->mailer, $propVal))
        continue;

      $client->mailer->{$propVal} = $props[$propVal] ?? Config::Mailing('credentials.'.$propVal);
    }

    return $this;
  }

  /**
   * Set up the Mail driver configuration to be able to send the email
   * 
   * @param string $driver
   * @return self
   */
  public function setDriver(string $driver = 'smtp'|'pop', ?object $client = NULL): self {
    $client = $this->CheckClient($client);

    // Set mailer character set and encoding
    $client->mailer->CharSet = 'UTF-8';
    $client->mailer->Encoding = 'base64';

    switch ($driver) {
      case 'smtp':
        $client->mailer->isSMTP();

        // Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        if (Config::g('debug')) {
          $client->mailer->SMTPDebug = Config::g('debug_mode');
          // $client->mailer->Debugoutput = 'error_log';
          $client->mailer->Debugoutput = function($str, $level): void {
            Logger::Log(msg: $str, lvl: LogLevels::cases()[$level] ?? LogLevels::DEBUG);
          };
        }
        
        // SMTP encryption
        // Enable SMTP authentication
        $client->mailer->SMTPAuth = true;
        $client->mailer->SMTPSecure = true;
        // Enable TLS encryption, `SSL` also accepted
        $client->mailer->SMTPSecure = match (strtoupper(Config::Mailing('encrypt'))) {
          'STARTTLS'    => Mailer::ENCRYPTION_STARTTLS,
          'TLS', 'SSL'  => Mailer::ENCRYPTION_SMTPS,
          default       => [
            'ssl' => [
              'verify_peer' => false,
              'verify_peer_name' => false,
              'allow_self_signed' => true
            ],
          ],
        };

        // Timeout for the stream_set_timeout (seconds)
        $client->mailer->Timeout = Config::g('mailtimeout', self::DEFAULT_TIMEOUT);

        // PHPMailer also creates an internal SMTP object; it also sets the limit used by stream_select
        // we also adjust Timelimit in seconds (default is 300s)
        $client->mailer->getSMTPInstance()->Timelimit = Config::g('mailtimeout', self::DEFAULT_TIMEOUT);

        break;
      case 'pop':
      default:
        $client->mailer->isMail();
    }

    return $this;
  }

  /**
   * Set mail recipients from DTO, array<DTO> or string (email)
   * 
   * @param MailDataDTO|null $mailData
   * @param array|null $setters
   * @param object|null $client
   * @return self
   */
  public function setRecipients(
    ?MailDataDTO $mailData = NULL,
    ?array $setters = NULL,
    ?object &$client = NULL
  ): self {
    if (empty($mailData) || !!!is_object($mailData))
      throw new UnexpectedValueException(basename(__CLASS__)."::setRecipients ERROR+ " . date('d/m/Y H:i:s') . " - The recipients mail data are not defined, so the mailer client could not be set.\n", 1);
    if (empty($setters))
      throw new UnexpectedValueException(basename(__CLASS__)."::setRecipients ERROR+ " . date('d/m/Y H:i:s') . " - The methods setters mailer client data are not defined, so the mailer client could not be set.\n", 2);

    $client = $this->CheckClient($client);

    //  Each recipient type with its corresponding method setter
    foreach ($setters as $currRecipient => $currSetter) {
      if (Arr::Is($mailData->{$currRecipient})) {
        foreach($mailData->{$currRecipient} as $r) 
          $client->mailer->{$currSetter}($r->email, $r->name ?? '');
      } elseif ($mailData->{$currRecipient} instanceof RecipientDTO) {
        $client->mailer->{$currSetter}(
          $mailData->{$currRecipient}->email, 
          $mailData->{$currRecipient}->name ?? ''
        );
      } elseif (Helpers::IsEmail($mailData->{$currRecipient})) {
        $client->mailer->{$currSetter}($mailData->{$currRecipient});
      }
    }

    if (Helpers::IsEmail($client->mailer->From))
        $client->mailer->ConfirmReadingTo = $client->mailer->From;

    return $this;
  }

  /**
   * Set the mail content
   * 
   * @param mixed $subject
   * @param mixed $body
   * @param mixed $isHTML
   * @param mixed $client
   * @throws UnexpectedValueException
   * @return self
   */
  public function setContent(
    ?string $subject = NULL,
    ?string $body = NULL,
    ?bool $isHTML = TRUE,
    ?object &$client = NULL
  ): self {
    if (empty($body))
      throw new UnexpectedValueException(basename(__CLASS__)."::setContent ERROR+ " . date('d/m/Y H:i:s') . " - The mail body are not defined, so the mail content could not be set.\n", 1);

    $client = $this->CheckClient($client);

    $client->mailer->isHTML($isHTML ?? TRUE);

    $client->mailer->Subject = $subject ?? '';
    $client->mailer->Body    = $body;

    if ($isHTML) {
		  $client->mailer->msgHTML($body);
      $client->mailer->AltBody = Str::RealNl2Br(strip_tags($body), TRUE);
    }
    
    return $this;
  }

  /**
   * Set attachments to the email
   * 
   * @param mixed $attachments
   * @param mixed $client
   * @return self
   */
  public function setAttachments(
    ?array $attachments = NULL,
    ?object &$client = NULL
  ): self {
    if (empty($attachments))
      return $this;

    $client = $this->CheckClient($client);

    if(!!!empty($attachments))
      foreach($attachments as $f)
        $client->mailer->addAttachment($f->file, basename($f->file));

    return $this;
  } 

  public function Send(?object $email = NULL): void {
    $client = $this->CheckClient($email);

    try {
      $client->mailer->send();
    } catch (Throwable $e) {
      throw new PHPMailerException(basename(__CLASS__)."::Send ERROR+ " . date('d/m/Y H:i:s') . " - The email could not be sent. Mailer Error: {".$client->mailer->ErrorInfo."}\n", 1);
    }
  }
}