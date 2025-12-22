<?php declare(strict_types=1);

namespace App\Providers\Mail\Types;

enum MailServerConfig: string {
  case HOST     = 'Host';
  case USERNAME = 'Username';
  case PWD      = 'Password';
  case PORT     = 'Port';
}