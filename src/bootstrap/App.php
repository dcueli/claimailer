<?php declare(strict_types=1);

use 
  App\Support\Exceptions\Basexception,
  App\Support\Helpers\Helpers;

try {

  /*
  |--------------------------------------------------------------------------
  | Create The Application
  |--------------------------------------------------------------------------
  |
  | The first thing we will do is create a new Laravel application instance
  | which serves as the "glue" for all the components of Laravel, and is
  | the IoC container for the system binding all of the various parts.
  |
  */

  $app = new App\Application(basePath: realpath(__DIR__.'/../../'));

  /*
  |--------------------------------------------------------------------------
  | Configuring the Application
  |--------------------------------------------------------------------------
  |
  | Setting-up the main configuration options for the application like the 
  | base exception fot those that is unhandled. Here stores the composer 
  | configuration as well as the global options of the application.
  | And, to finish, the services configuration.
  |
  */

  $app->Settingup(
    registry: [
      Helpers::Realpath($app->basePath, ['src/config/Services.php']),
    ],
    settings: [
      Helpers::Realpath($app->basePath, ['src/config/Config.php']),
      'composer' => Helpers::Realpath($app->basePath, ['composer.json']),
    ]
  );

  /*
  |--------------------------------------------------------------------------
  | Return The Application
  |--------------------------------------------------------------------------
  |
  | This script returns the application instance. The instance is given to
  | the calling script so we can separate the building of the instances
  | from the actual running of the application and sending responses.
  |
  */

  return $app;

} catch(Basexception $e) {

  /*
  |--------------------------------------------------------------------------
  | Manage the exception
  |--------------------------------------------------------------------------
  |
  | The bootstrap process must be as easy and fail-safe as possible. So, we 
  | cannot use the custom Logger class to register the exception here 
  | because depends on parts of the application that have not yet 
  | been bootstrapped.
  | 
  */  

  $e->ShowableException($e);

}
