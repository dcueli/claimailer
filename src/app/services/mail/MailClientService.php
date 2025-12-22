<?php declare(strict_types=1);

namespace App\Services\Mail;

use
// Native
  // Exceptions
  BadFunctionCallException,
  Exception,
  Throwable,
  UnexpectedValueException,

// Application
  // Providers
  App\Providers\Mail\MailClientProvider,
    // DTOs
    App\Providers\Mail\Dtos\MailDataDTO,
    App\Providers\Mail\Dtos\MailServerDTO,
    // Factories
    App\Providers\Mail\Factories\MailDataFactory,
    App\Providers\Mail\Factories\MailServerFactory,
    // Types/Enums
    App\Providers\Mail\Types\MailerClient,
    App\Providers\Mail\Types\MailServerConfig,
  // Support
    // Exceptions
    App\Support\Exceptions\FileContentNotValidException,
    // Facades
    App\Support\Facades\Config,
    // Helpers
    App\Support\Helpers\Arr,
    App\Support\Helpers\Str,
    // Traits
    App\Support\Traits\LogSaverTrait,
    // Types
    App\Support\Types\LogLevels;

class MailClientService {
  use LogSaverTrait;

	/**
	 * Mail client Provider
	 * @var MailClientProvider|null
	 */
	private ?MailClientProvider $MailerProvider = NULL;

  /**
   * Mail data DTO
   * @var MailDataDTO
   */
  private MailDataDTO|null $MailData = NULL;
	
  /**
   * Mail server DTO
   * @var MailServerDTO
   */
  private MailServerDTO|null $MailServer = NULL;

	/**
	 * Mail sending counter log
	 * @var string|null
	 */
	private string|null $CounterFilePath = NULL;
	
	/**
	 * Sent mail folder path
	 * @var string|null
	 */
	private string|null $SentMailPath = NULL;

	/**
	 * Sent mails counter
	 * @var int
	 */
	private int $SentMailCounter = 0;

  /**
   * Whether or not the application is set up
   * @var bool
   */
  private bool $Init = false;

  /**
   * Set up the mail to be sent
   * 
   * @param ?array $mailServer
   * @param ?array $mailData
   * @param ?string $client
   * @param ?string $counterFilePath
   * @param ?string $sentMailPath
   * @param ?int $counterMailSent
   * @throws BadFunctionCallException
   */
	public function __construct(
    ?array $mailServer = NULL, 
    ?array $mailData = NULL,
    ?string $client = NULL,
    ?string $counterFilePath = NULL,
    ?string $sentMailPath = NULL,
    ?int $counterMailSent = NULL
  ) {
    // Initialize the settings of the mail server and mail data to be sent
    if (empty($mailData) && empty($mailServer))
      throw new BadFunctionCallException("+MAIL CONSTRUCT +".strtolower(LogLevels::ERROR->value)."+ " . date('d/m/Y H:i:s') . " - The mail built-in parameters are undefined", 1);
    
    $this
      // Set Mail Client provider
      ->setMailerClientProvider($client)
      // Mail sending counter log
      ->setCounterFilePath($counterFilePath)
      // Sent mail folder path 
      ->setSentMailPath($sentMailPath)
      // Set the sent mail counter
      ->setSentMailCounter($counterMailSent)
      // Mail server settings
      ->setMailServerSettings($mailServer)
      //  Set mail data
      ->setMailData($mailData)
      // Set mailer client
      ->setMail()
      // Set whether or not the application is set up
      ->setSettedup();
	}

  /**
   * Get TRUE if the mail to sent has been set up or FALSE if not.
   * 
   * @return bool
   */
  public function IsReady(): bool {
    return $this->Init;
  }

  /**
   * Send Mail using PHPMailer
   * 
   * @param mixed $saveMailRegistry
   * @return bool
   */
  public function Send(?bool $saveMailRegistry = FALSE): bool {
    $client = $this->MailerProvider->client;
    $wasSent = FALSE;
    
    try {
      $client->Send();
      $wasSent = TRUE;

    } catch (Exception $e) {
      self::SaveLogOnFile(
        "MailClientService::Send() ".strtolower(LogLevels::WARNING->value)."+ " . date('d/m/Y H:i:s') . " - The email could not be sent. Exception message: {$e->getMessage()}\n",
        $e->getCode(),
        $e
      );

    } finally {
      if ($saveMailRegistry)
        $this->SaveMailOnRegistry($wasSent, client: $client);

      return $wasSent;

    }
	}

  /**
   * Close SMTP connection and free resources held by this service.
   * Safe to call multiple times (idempotent).
   *
   * @return void
   */
  public function Close(): void {
    try {
      if (!empty($this->MailerProvider)) {
        $client = $this->MailerProvider->client;
        if (isset($client->mailer)) {
          $mailer = $client->mailer;
          if (method_exists($mailer, 'smtpClose'))
            $mailer->smtpClose();
        }
      }
    } catch (Throwable $e) {
      // Suppress errors during cleanup
    } finally {
      $this->MailerProvider = NULL;
      $this->MailData = NULL;
      $this->MailServer = NULL;
      $this->CounterFilePath = NULL;
      $this->SentMailPath = NULL;
      $this->MailerProvider = NULL;
      $this->Init = FALSE;
    }
  }

  /**
   * Save the mail registry on a new log file in src/resources/mail/sent/
   * 
   * @param bool $sent
   * @param string|null $registryPath
   * @param object|null $client
   * @return void
   */
  private function SaveMailOnRegistry(
    bool $sent = FALSE, 
    ?object $client = NULL,
    ?string $registryPath = NULL
  ): void {
    $client ??= $this->MailerProvider->client;

    if (NULL !== $registryPath && is_dir($registryPath))
      $this->setSentMailPath($registryPath);

    $savedMailMessage = (!!!$sent 
        ? "<br>The below mail message was not sent:"
        : "<br>The below mail message was sent successfully:")."<br>\n" .
      "<br><b>Subject:</b>\n" .
      "<br><i>".$client->mailer->Subject."</i>\n" .
      "<br><b>Recipients:</b>\n" .
      var_export($this->MailData->getRecipients(all: TRUE), TRUE)."\n" .
      "<br><b>Body:</b><br>\n" .
      nl2br($client->mailer->AltBody).
      "<br>\n";

    if (Config::g('debug')) {
      echo $savedMailMessage;
    } else {
      $fPath = Str::Sanitize(
        $this->SentMailPath.DS.
        Str::ToLowerSnakeCase('mail_' . date('Ymd_His') . '_').
        Str::ToPascalCase($client->mailer->Subject) . '.txt',
        ':\\/.()'
      );

      if (!!!file_put_contents(
        $fPath, 
        $savedMailMessage, 
        FILE_APPEND | LOCK_EX)
      ) {
        self::SaveLogOnFile(
          "+MailClientService::SaveMailOnRegistry +".strtolower(LogLevels::ERROR->value)."+ Could not write mail registry to file [{$fPath}]",
          errorCode: 500,
          e: new Exception("File write failed")
        );
      }
    }
  }

  /**
   * Set TRUE if the mail to sent has been set up or FALSE if not.
   * 
   * @return self
   */
  private function setSettedup(): self {
    $this->Init = TRUE;

    if (!!!$this->MailServer->IsReady())
      return ($this->Init = FALSE) ?: $this;
    if (!!!$this->MailData->IsReady(chkRecipients: TRUE, chkSubject: TRUE, chkBody: TRUE))
      return ($this->Init = FALSE) ?: $this;

    return $this;
  }

  /**
   * Set Mail client instance
   * 
   * @param MailClientProvider|string|null $client
   * @throws UnexpectedValueException
   * @return self
   */
  private function setMailerClientProvider(MailClientProvider|string|null $client = NULL): self {
    $this->MailerProvider = new MailClientProvider($client);

    if(!!!$this->MailerProvider->IsValidMailerClient())
      throw new UnexpectedValueException("MailClientService::setMailerClientProvider +".strtolower(LogLevels::ERROR->value)."+ " . date('d/m/Y H:i:s') . " - The mailer client is not defined in App\Providers\Mail\MailClientProvider.\n", 1);

    return $this;
  }

  /**
   * Set the server configuration parameters
   * 
   * @param array<string>|null $params
   * @return self
   */
  private function setMailServerSettings(?array $params = NULL): self {
    $this->MailServer = MailServerFactory::fromArray(Arr::Filter(
      $params ?? [], 
      MailServerConfig::cases()
    ));

    return $this;
  }

  /**
   * Set the Subject of the mail with the sent times counter
   * 
   * @param string|null $subject
   * @return self
   */
	private function setAddSentTimesCounterToSubject(?string $subject = NULL): self {
    $subject ??= $this->MailData->subject;

    $this->MailData->setSubject(0 < $this->SentMailCounter
      ? sprintf($subject, '(n. ' . $this->SentMailCounter . ')')
      : trim(sprintf($subject, '') ) );

		return $this;
	}

  /**
   * Set the mail information data to send
   * 
   * @param array<string>|null $params
   * @return self
   */
  private function setMailData(?array $params = NULL): self {
    if (!!!empty($params))
      $this->MailData = MailDataFactory::fromArray([
        'mailTemplateFilePath' => !!!empty($params['mail']) ? Config::g('path.templates').$params['mail'] : NULL,
        'subject'              => $params['subject'] ?? NULL,
        'body'                 => $params['body'] ?? NULL,
        'to'                   => $params['recipients']['to'] ?? NULL,
        'cc'                   => $params['recipients']['cc'] ?? NULL,
        'bcc'                  => $params['recipients']['bcc'] ?? NULL,
        'reply'                => $params['reply'] ?? NULL,
        'from'                 => $params['from'] ?? NULL,
        'attachments'          => $params['attachments'] ?? NULL,
      ]);

    $this->setAddSentTimesCounterToSubject();

    return $this;
  }

  /**
   * Set file path where save the counter sent mails
   * 
   * @param string|null $completePath
   * @throws UnexpectedValueException
   * @return self
   */
  private function setCounterFilePath(?string $completePath = NULL): self {
    $completePath ??= $this->CounterFilePath;
    $completePath ??= Config::g('path.logs').Config::Mailing('counterFilePath');

		if (!!!file_exists($completePath))
      throw new UnexpectedValueException('The counter sent mails log file path [counterFilePath:{'.$completePath.'}] not exists and this is mandatory.');

		$this->CounterFilePath = $completePath;

    return $this;
  }

  /**
   * Set the sent mails folder path
   * 
   * @param string|null $sentMailPath
   * @throws UnexpectedValueException
   * @return self
   */
  private function setSentMailPath(?string $sentMailPath = NULL): self {
    $sentMailPath ??= $this->SentMailPath ?? Config::g('path.sent');

		if (!!!is_dir($sentMailPath))
      throw new UnexpectedValueException('MailClientService::SaveMailOnRegistry +'.strtolower(LogLevels::ERROR->value).'+ The sent mails folder path [sentMailPath:{'.$sentMailPath.'}] not exists.');
    
		if (!!!is_dir($this->SentMailPath = realpath($sentMailPath) ) )
      throw new UnexpectedValueException('MailClientService::SaveMailOnRegistry +'.strtolower(LogLevels::ERROR->value).'+ The sent mails folder path [$this->SentMailPath :{'.$this->SentMailPath .'}] not exists.');

    return $this;
  }

  /**
   * Read the mail sent counter to monitor the number of sending on the mail subject
   * 
   * @return self
   */
	private function setSentMailCounter(?int $counterMailSent = NULL): self {
    $this->SentMailCounter = FALSE !== ($cont = file_get_contents($this->CounterFilePath))
      ? ((int)$cont)+1
      : ((int)($counterMailSent ?? $this->SentMailCounter ?? 0))+1;

    return $this->UpdateMailCounterFile();
	}

  /**
   * Update the counter file with the current sent mails counter
   *
   * @return self
   */
  private function UpdateMailCounterFile(): self {
    try {
      // Write the content (replaces the previous one) with exclusive lock
      if (FALSE === @file_put_contents($this->CounterFilePath, (string)$this->SentMailCounter, LOCK_EX))
        throw new FileContentNotValidException("MailClientService::UpdateMailCounterFile +".strtolower(LogLevels::ERROR->value)."+ Could not write counter to file [{$this->CounterFilePath}]", 1);
        // throw new Exception("MailClientService::UpdateMailCounterFile +".strtolower(LogLevels::ERROR->value)."+ Could not write counter to file [{$this->CounterFilePath}]", 1);

    } catch (FileContentNotValidException $e) {
      self::SaveLogOnFile(
        $e->getMessage(),
        $e->getCode() ?: 500,
        $e
      );

    } finally {
      return $this;
    }
  }

  /**
   * Set up the third party mail client used to send emails 
   * 
   * @param MailClientProvider|string|null $client
   * @return self
   */
	private function setMail(MailClientProvider|string|null $client = NULL): self {
    if (!!!empty($client))
      $this->setMailerClientProvider($client);

    $client = $this->MailerProvider->client;

    // I will leave a comment for each of the below methods, although it's obvious what they do.
    $client
      // Configuration of the Mail delivery server
      ->setCredentials()
		  // Set mailer to use SMTP
      ->setDriver(Config::Mailing('driver'))
      // Set mail recipients
      ->setRecipients($this->MailData, $this->MailerProvider->g('map'))
      // Set mail content
      ->setContent($this->MailData->subject, $this->MailData->body)
      // Set mail attachments
      ->setAttachments($this->MailData->attachments);

		return $this;
	}
}
