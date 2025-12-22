<?php declare(strict_types=1);

namespace App\Providers\Mail\Types;

/**
 * Cada tipo de contenedor de destinatarios de correo electrónico y por cada cliente 
 * de correo electrónico, tiene un método setter correspondiente.
 * Por ejemplo, para el contenedor "to" (destinatario), el método setter en:
 * - PHPMailer es "addAddress"
 * - Symfony\Mailer es "Symfony\Mail->addAddress"
 * - etc.
 * Esta enumeración mapea esos contenedores con sus métodos setter correspondientes.
 */
enum PotentialRecipientsSetters: string {
  case TO    = 'addAddress|setTo';
  case CC    = 'addCC|setCc';
  case BCC   = 'addBCC|setBcc';
  case REPLY = 'addReplyTo|setReplyTo';
  case FROM  = 'setFrom|setFrom';
  case ATTACH = 'addAttachment|attach';
}