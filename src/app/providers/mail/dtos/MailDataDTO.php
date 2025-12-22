<?php declare(strict_types=1);

namespace App\Providers\Mail\Dtos;

use
// Native
  InvalidArgumentException,

// Application
  // Providers
    // DTOs
    App\Providers\Mail\Dtos\RecipientDTO,
  // Support
    // Exceptions
    App\Support\Exceptions\FileContentNotValidException,
    // Helpers
    App\Support\Helpers\Arr,
    // Traits
    App\Support\Traits\ToStringTrait;

class MailDataDTO {
  use ToStringTrait;

  /**
   * @param string               $mailTemplateFilePath
   * @param string               $subject
   * @param string               $body
   * @param array<RecipientDTO>  $to
   * @param array<RecipientDTO>  $cc
   * @param array<RecipientDTO>  $bcc
   * @param RecipientDTO         $reply
   * @param RecipientDTO         $from
   * @param array<AttachmentDTO> $attachments
   */
  public function __construct(
    public string         $mailTemplateFilePath,
    public string         $subject,
    public ?string        $body        = NULL,
    public ?array         $to          = NULL,
    public ?array         $cc          = NULL,
    public ?array         $bcc         = NULL,
    public ?RecipientDTO  $reply       = NULL,
    public ?RecipientDTO  $from        = NULL,
    public ?array         $attachments = NULL
  ) {
    // TODO
  }

  /**
   * Set an array of recipients
   * 
   * @param array<'email', 'name'>|array<string></string>|string $data
   * @param mixed $targetRecipient
   * @return array
   */
  public function setRecipients(array|string $data, &$targetRecipient): array {
    $data = is_string($data) ? [$data] : $data;
    $targetRecipient ??= [];

    foreach ($data as $el)
      if(NULL !== ($recipient = $this->setRecipient($el) ) )
        $targetRecipient[] = $recipient;

    return Arr::UniqueBy('email', $targetRecipient);
  }

  /**
   * Check if mandatory fields are defined
   *
   * @throws InvalidArgumentException
   * @return bool
   */
  public function ValidateRequiredFields(
    ?bool $chkSubject = FALSE, 
    ?bool $chkBody = FALSE,
    ?bool $chkRecipients = FALSE,
  ): bool {
    if ($chkSubject && empty($this->subject))
      throw new InvalidArgumentException('Subject is required');
    
    if ($chkBody && empty($this->body) && !!!$this->IsValidMailTemplate())
      throw new InvalidArgumentException('Mail template file does not exist: {'.$this->mailTemplateFilePath.'}');

    if ($chkRecipients && !!!$this->IsValidRecipients())
      throw new InvalidArgumentException(
        'Invalid emails. At least one recipient must be specified as '.
        "<br>[To]: ".var_export($this->to, true).
        "<br>[Cc]: ".var_export($this->cc, true).
        "<br>[Bcc]: ".var_export($this->bcc, true));

    return TRUE;
  }

  /**
   * Validate recipient
   * 
   * @param array<RecipientDTO> $rec
   * @throws InvalidArgumentException
   * @return bool
   */
  public function IsValidRecipient(array|RecipientDTO &$rec): bool {
    $rec ??= [];
    if (!!!$rec) return FALSE;

    // Check if $rec param is a single RecipientDTO instance
    if ($rec instanceof RecipientDTO)
      return $rec->ValidateEmail();

    if (!!!Arr::IsArr($rec))
      return FALSE;

    // Array of RecipientDTO: filter out invalid emails (optimized foreach vs array_reduce)
    foreach ($rec as $i => $item)
      if (!!!$item->ValidateEmail())
        unset($rec[$i]);
      
    return (bool)$rec;
  }

  /**
   * Validate recipients To, CC, BCC
   *
   * @throws InvalidArgumentException
   * @return bool
   */
  public function IsValidRecipients(): bool {
    // If no recipients at all, return false
    if (!!!$this->to && !!!$this->cc && !!!$this->bcc)
      return FALSE;

    // If none of the recipient lists are valid, throw an exception
    if (!!!$this->IsValidRecipient($this->to))
      if (!!!$this->IsValidRecipient($this->cc))
        if (!!!$this->IsValidRecipient($this->bcc))
          return FALSE;

    return TRUE;
  }

  /**
   * Tell if the Mail is ready to send
   * 
   * @param bool $chkSubject
   * @param bool $chkRecipients
   * @param bool $chkBody
   * @param bool $chkReply
   * @param bool $chkFrom
   * @param bool $chkAttachments
   * @return bool
   */
  public function IsReady(
    ?bool $chkSubject = FALSE, 
    ?bool $chkRecipients = FALSE, 
    ?bool $chkBody = FALSE,
    ?bool $chkReply = FALSE,
    ?bool $chkFrom = FALSE,
    ?bool $chkAttachments = FALSE,
  ): bool {
    if (!!!$this->ValidateRequiredFields($chkSubject, $chkRecipients, $chkBody))
      throw new InvalidArgumentException('The mandatory fields are not defined');
    if ($chkReply && !!!$this->IsValidRecipient($this->reply))
      return FALSE;
    if ($chkFrom && !!!$this->IsValidRecipient($this->from))
      return FALSE;
    if ($chkAttachments && empty($this->attachments))
      return FALSE;

    return TRUE;
  }

  /**
   * Set recipient(To)
   * 
   * @param array|string $recipients
   * @return MailDataDTO
   */
  public function setTo(array|string $recipients): MailDataDTO {
    $this->setRecipients($recipients, $this->to);

    return $this;
  }

  /**
   * Set recipient(Cc)
   * 
   * @param array|string $recipients
   * @return MailDataDTO
   */
  public function setCc(array|string $recipients): MailDataDTO {
    $this->setRecipients($recipients, $this->cc);

    return $this;
  }

  /**
   * Set recipient(Bcc)
   * 
   * @param array|string $recipients
   * @return MailDataDTO
   */
  public function setBcc(array|string $recipients): MailDataDTO {
    $this->setRecipients($recipients, $this->bcc);

    return $this;
  }

  /**
   * Set Reply
   * 
   * @param array $reply
   * @return MailDataDTO
   */
  public function setReply(array $reply): MailDataDTO {
    $this->reply = $this->setRecipient($reply);

    return $this;
  }

  /**
   * Set From
   * 
   * @param array $from
   * @return MailDataDTO
   */
  public function setFrom(array $from): MailDataDTO {
    $this->from = $this->setRecipient($from);

    return $this;
  }

  /**
   * Set the mail subject
   * 
   * @param mixed $subject
   * @return MailDataDTO
   */
  public function setSubject(?string $subject = NULL): MailDataDTO {
    if (!!!empty($subject))
      $this->subject = $subject;

    return $this;
  }

  /**
   * Set the HTML mail template
   * 
   * @param mixed $filePath
   * @return MailDataDTO
   */
  public function setMailTemplateFilePath(?string $filePath = NULL): MailDataDTO {
    $filePath ??= $this->mailTemplateFilePath;

    if ($this->IsValidMailTemplate($filePath))
      $this->mailTemplateFilePath = $filePath;

    return $this;
  }

  /**
   * Set body message
   * 
   * @param string|null $body
   * @return MailDataDTO
   */
  public function setBody(?string $body = NULL): MailDataDTO {
    $body ??= $this->body ?? $this->mailTemplateFilePath ?? NULL;

    if ($this->IsValidMailTemplate($body)) {
      $this->setMailTemplateFilePath($body);
      if (!!!($this->body = file_get_contents($this->mailTemplateFilePath)))
        throw new FileContentNotValidException("+MAILDTO CONSTRUCT BODY ERROR+ " . date('d/m/Y H:i:s') . " - The file content on [file:{$this->mailTemplateFilePath}] is not valid", 1);
    }

    return $this;
  }

  /**
   * The attachments files can be either an address string with the path 
   * to a file or an Array, with each element being an email address, or
   * an Array with each element being a path to a file.
   * 
   * @param array|string $data
   * @return MailDataDTO
   */
  public function setAttachments(array|string $data): MailDataDTO {
    // Convert to array
    $data = is_string($data) ? [$data] : $data;

    // Set attachments if property if NULL
    $this->attachments = $this->attachments ?: [];

    // Add attachments
    foreach ($data as $el)
      $this->attachments[] = new AttachmentDTO($el);

    // Remove duplicates
    $this->attachments = array_values(array_unique(array_map(
      fn(string $p): string => str_replace(LS, DS, strtolower($p)), 
      $this->attachments
    ) ) );

    return $this;
  }

  /**
   * Return recipients just on an Array
   * 
   * @return array
   */
  public function getRecipients(
    bool $cc = FALSE, 
    bool $bcc = FALSE,
    bool $reply = FALSE,
    bool $from = FALSE,
    bool $all = FALSE
  ): array {
    $allRecipients = [];

    if ($all) {
      $cc = TRUE;
      $bcc = TRUE;
      $reply = TRUE;
      $from = TRUE;
    }

    if (!!!empty($this->to))
      $allRecipients = array_merge($allRecipients, $this->to);
    
    if ($cc && !!!empty($this->cc))
      $allRecipients = array_merge($allRecipients, $this->cc);
    
    if ($bcc && !!!empty($this->bcc))
      $allRecipients = array_merge($allRecipients, $this->bcc);

    if ($reply && !!!empty($this->reply))
      $allRecipients[] = $this->reply;

    if ($from && !!!empty($this->from))
      $allRecipients[] = $this->from;

    return $allRecipients;
  }

  /**
   * Validate template exists 
   *
   * @throws InvalidArgumentException
   * @return bool
   */
  private function IsValidMailTemplate(?string $filePath = NULL): bool {
    $filePath ??= $this->mailTemplateFilePath;

    return file_exists($filePath);
  }

  /**
   * Return a RecipientDTO from the initial array<'email', 'name'> or string(email)
   * 
   * @param array<'email', 'name'>|string $data
   * @return RecipientDTO|null
   */
  private function setRecipient(array|string $data): RecipientDTO|NULL {
    $r = new RecipientDTO();

    if (Arr::IsArr($data))
      $r->setEmail($data['email'] ?? reset($data) ?? $data)
        ->setName($data['name'] ?? NULL);
    elseif (is_string($data))
      $r->email = $data;

    return $r->ValidateEmail() ? $r : NULL;
  }
}
