<?php declare(strict_types=1);

namespace App\Providers\Mail\Dtos;

use InvalidArgumentException;

class AttachmentDTO {
  public function __construct(
    public string $file,
  ) {
    if(!!!$this->IsValidFile())
      throw new InvalidArgumentException('Attachment file does not exist: {'.$this->file.'}');
  }

  /**
   * Validate template exists 
   *
   * @throws InvalidArgumentException
   * @return bool
   */
  private function IsValidFile(): bool {
    return file_exists($this->file);
  }
}
