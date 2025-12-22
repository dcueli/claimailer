<?php declare(strict_types=1);

namespace App\Providers\Mail\Dtos;

use
// Native
InvalidArgumentException,
// Application
  // Contracts
    // Interfaces
    App\Contracts\Interfaces\IRecipient,
  // Support
    // Traits
    App\Support\Traits\ToStringTrait;

class RecipientDTO implements IRecipient {
  use ToStringTrait;

  private string|null $Email = NULL;
  public string|null $email {
    get => $this->Email;
    set => $this->Email = $value;
  }

  private string|null $Name = NULL;
  public string|null $name {
    get => $this->Name;
    set => $this->Name = $value;
  }

  public function __construct(?string $email = NULL, ?string $name = NULL) {
    $this->Email = $email;
    $this->Name = $name;

    $this->ValidateEmail();
  }

  /**
   * Set recipient(To)
   * 
   * @param string $email
   * @return RecipientDTO
   */
  public function setEmail(string $email): RecipientDTO {
    $this->Email = $email;

    return $this;
  }

  /**
   * Set recipient(To)
   * 
   * @param string|null $name
   * @return RecipientDTO
   */
  public function setName(string|null $name): RecipientDTO {
    $this->Name = $name;

    return $this;
  }

  /**
   * Validate email
   * 
   * @param string|null $email
   * @throws InvalidArgumentException
   * @return bool
   */
  public function ValidateEmail(?string $email = NULL): bool {
    $email ??= $this->Email ?? '';

    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
  }
}
