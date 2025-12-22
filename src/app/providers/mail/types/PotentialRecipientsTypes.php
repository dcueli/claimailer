<?php declare(strict_types=1);

namespace App\Providers\Mail\Types;

enum PotentialRecipientsTypes: string {
  case TO    = 'to';
  case CC    = 'cc';
  case BCC   = 'bcc';
  case REPLY = 'reply';
  case FROM  = 'from';
}