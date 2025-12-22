<?php declare(strict_types=1);

namespace App\Providers\Mail;

use
// Native
//-> Exceptions
  InvalidArgumentException,

// Application
  // Helpers
  App\Support\Helpers\Arr,
  // Types
  App\Providers\Mail\Types\MailerClientTypes,
  App\Providers\Mail\Types\PotentialRecipientsTypes,
  App\Providers\Mail\Types\PotentialRecipientsSetters,
  // Facades
  App\Support\Facades\Config,
  // Contracts
  App\Contracts\Abstracts\Getters;
use UnexpectedValueException;

class MailClientProvider extends Getters {
	/**
	 * Mailer client type
	 * @private Type
   * @public type
   * @var MailerClientTypes|null
	 */
	private MailerClientTypes|null $Type = NULL;
  public MailerClientTypes|null $type {
    get => $this->Type;
    set => $this->Type = $value;
  }

  /**
   * Mapping of potential recipient types with method setters according 
   * to the mailer client
	 * @private Map
   * @public map
   * @var array<string>
   */
  private array $Map = [];
  public array $map {
    get => $this->Map ?? [];
    set => $this->Map = $value;
  }

  /**
   * Third-party mailer client
	 * @private Client
   * @public client
   * @var object|null
   */
  private object|null $Client = NULL;
  public object|null $client {
    get => $this->Client;
  }
  
  public function __construct(MailerClientTypes|string|null $type = NULL) {
    $this
      ->setClient($type)
      ->setMapping();
  }

  /**
   * Check if the $client property is an instance of the enum App\Providers\Mail\Types\MailerClientTypes
   * where it would return true, otherwise false.
   * 
   * @return bool
   */
  public function CheckClientType(): bool {
    return $this->Type instanceof MailerClientTypes;
  }
  
  /**
   * Check if the $mailer property is a valid Mail Client defined in the cases of the enum
   * App\Providers\Mail\Types\MailerClientTypes
   * 
   * @param object|null $client
   * @return bool
   */
  public function IsValidMailerClient(object|null $client = NULL): bool {
    $client ??= $this->Client;

    foreach (MailerClientTypes::cases() as $case)
      if ($case->IsInstance($client)) 
        return TRUE;

    return FALSE;
  }

  /**
   * Set mailer client
   * 
   * @param MailerClientTypes|string|null $type
   * @return void
   */
  private function setClient(MailerClientTypes|string|null $type = NULL, bool $reMapping = false): self {
    if ($type instanceof MailerClientTypes)
      $this->Type = $type;
    else      
      $this->Type = MailerClientTypes::tryFrom($type ??= Config::Mailing('client.class'));
    
    if (!!!$this->CheckClientType())
      throw new InvalidArgumentException("MailClientProvider::setClient ERROR+ " . date('d/m/Y H:i:s') . " - The mailer Type is invalid, so it could not be defined.\n", 1);

    if (empty($this->Client = $this->Type->Make()))
      throw new InvalidArgumentException("MailClientProvider::setClient ERROR+ " . date('d/m/Y H:i:s') . " - The mailer Client instance has not been defined.\n", 2);

    return $reMapping ? $this->setMapping() : $this;
  }

  /**
   * Set the mapping of potential recipient types with method setters according 
   * to the mailer client.
   * 
   * @throws InvalidArgumentException
   * @return self
   */
  private function setMapping(): self {
    if (!!!$this->IsValidMailerClient())
      throw new InvalidArgumentException("MailClientProvider::setMapping ERROR+ " . date('d/m/Y H:i:s') . " - The mailer client instance is not defined, so the methods mapping could not be set.\n", 1);

    // Reference to the mailer client
    $client = $this->Client->mailer;
    // For each element of the Array of possible methods by recipient type
    // check if the method exists in the mail client instance. If this
    // exists, assign it into the methods array ($map property)
    foreach (PotentialRecipientsTypes::cases() as $type) {
      if (empty($strPotentialMethods = PotentialRecipientsSetters::{$type->name}?->value))
        continue;

      $arrPotentialMethods = Arr::SmartExplode($strPotentialMethods, '|');
      // Explota directamente la cadena de setters (sin crear variables extra)
      foreach ($arrPotentialMethods as $currMethod) {
        // evita cadenas vacías
        if (empty($currMethod)) continue;
        if (
          method_exists($client, $currMethod) && 
          is_callable([$client, $currMethod], true)
        ) {
          $this->Map[strtolower($type->name)] = $currMethod;
          break;
        }
      }
    }

    if (empty($this->Map))
      throw new InvalidArgumentException("MailClientProvider::setMapping ERROR+ " . date('d/m/Y H:i:s') . " - No method setters could be mapped for the mailer client.\n", 2); 

    return $this;
  }

  /**
   * Clear the internal static map cache.
   * Useful to free static memory between job executions.
   *
   * @return void
   */
  public static function ClearCache(): void {
    // TODO: Implement cache clearing logic if applicable
  }
}