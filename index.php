<?php declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Autosender Mail
|--------------------------------------------------------------------------
|
* @application   Autosender Mail
* @version       2.1.0
* @author        David Cueli <info@dcueli.com>
|
*/

define('AUTOSENDER_MAIL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| DS (DIRECTORY_SEPARATOR) Native superglobal variable
| LS (Level Separator) for URLs
|--------------------------------------------------------------------------
|
| These global variables are defined to difference directory separator 
| between Windows and Linux or MacOS systems
|
*/
define('DS', DIRECTORY_SEPARATOR);
define('LS', '\\');
define('BASE_PATH', realpath(__DIR__));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| the application. Just need to utilize it! It'll simply require it into
| the script here so that no worry about manual loading.
|
*/

require_once __DIR__.'/./'.'vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Bootstrap the application
|--------------------------------------------------------------------------
|
| This bootstraps the application and gets it ready for use, then it will
| load up this application.
|
*/

$app = require_once __DIR__.'/src/bootstrap/App.php';

try {
  
  /*
  |--------------------------------------------------------------------------
  | Send mail to the recipients
  |--------------------------------------------------------------------------
  |
  | Set the recipients, subject and body mail to send.
  |
  */

  $app->Send([
    'mail'        => 'Claim.html',
    'subject'     => 'Vamos a probar el cambio de Asunto %s',
    'recipients'  => [
      'to'        => [
        ['name' => 'Loidors Stranger Fightered ', 'email' => 'sardaucar.stranger@gmail.com'],
      ],
    ],
    'reply'       => [
      'email'     => 'dcueli@hotmail.com',
      'name'      => 'David Cueli Hotmail'
    ],
    // Must be the same account as Creditals in src/config/Config file
    'from'        => [
      'email'     => 'info@dcueli.com',
      'name'      => 'David Cueli'
    ],
    'attachments' => [
      // 'path/to/file-1.ext'
      // 'path/to/file-2.ext'
      // ...
      // 'path/to/file-N.ext'
    ],
  ]);

} catch(Throwable $e) {

  return App\Support\Facades\Logger::Log($e->getMessage(), $e->getCode(), $e);

} finally {

  /*
  |--------------------------------------------------------------------------
  | Terminate The Application
  |--------------------------------------------------------------------------
  |
  | Once the application has finished the send email, we have to free memory.
  |
  */
  $app->Terminate();

}

