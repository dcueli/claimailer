<?php declare(strict_types=1);

namespace App\Providers\Mail\Factories;

// Application
// use App\Providers\Mail\Dtos\MailDataDTO;
use App\Providers\Mail\Dtos\MailDataDTO;

class MailDataFactory {
  public static function fromArray(array $data): MailDataDTO {
    $mailDataDto = new MailDataDTO(
      mailTemplateFilePath: $data['mailTemplateFilePath'],
      subject:              $data['subject']
    );

    // Subject
    if(!!!empty($data['subject']))
      $mailDataDto->setSubject($data['subject']);
    // Recipients
    if(!!!empty($data['to']))
      $mailDataDto->setTo($data['to']);
    if(!!!empty($data['cc']))
      $mailDataDto->setCc($data['cc']);
    if(!!!empty($data['bcc']))
      $mailDataDto->setBcc($data['bcc']);
    // Reply
    if(!!!empty($data['reply']))
      $mailDataDto->setReply($data['reply']);
    // From
    if(!!!empty($data['from']))
      $mailDataDto->setFrom($data['from']);
    // Body message
    $mailDataDto->setBody($data['body'] ?? NULL);
    // Attachments
    if(!!!empty($data['attachments']))
      $mailDataDto->setAttachments($data['attachments']);

    return $mailDataDto;
  }
}
