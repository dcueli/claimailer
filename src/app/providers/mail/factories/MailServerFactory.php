<?php declare(strict_types=1);

namespace App\Providers\Mail\Factories;

// Application
use App\Providers\Mail\Dtos\MailServerDTO;

class MailServerFactory {
  public static function fromData(
    string $host, 
    string $username, 
    string $pwd, 
    int|string|null $port = NULL
  ): MailServerDTO {
    return new MailServerDTO(
      host:     $host,
      username: $username,
      pwd:      $pwd,
      port:     $port,
    );
  }

  public static function fromArray(array $data): MailServerDTO {
    return new MailServerDTO(dataConn: $data ?? []);
  }
}
