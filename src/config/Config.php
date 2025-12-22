<?php declare(strict_types=1);

return [
	/*
	|-------------------------------------------------------------------------------------------------
	| The Application version
	|-------------------------------------------------------------------------------------------------
	|
	| This value is the debug mode is ON or not.
	|
	*/
	'VERSION' => '2.1.0',

	/*
	|-------------------------------------------------------------------------------------------------
	| Application exection type
	|-------------------------------------------------------------------------------------------------
	|
	| This value is the debug mode is ON or not.
	|
	*/
	'DEBUG' => FALSE,

	/*
	|-------------------------------------------------------------------------------------------------
	| Debug Application exection mode
	|-------------------------------------------------------------------------------------------------
	|
	| This value is the debugging mode of the application.
	|
	*/
	'DEBUG_MODE' => 0,

	/*
	|-------------------------------------------------------------------------------------------------
	| Application Language
	|-------------------------------------------------------------------------------------------------
	|
	| This value is the language of application.
	|
	*/
	'LANGUAGE' => 'es',

	/*
	|-------------------------------------------------------------------------------------------------
	| Application Hostname server
	|-------------------------------------------------------------------------------------------------
	|
	| The host name of current server.
	|
	*/
	'HOSTNAME' => $_SERVER['SERVER_NAME'],

  /*
	|-------------------------------------------------------------------------------------------------
	| Log Application exection mode
	|-------------------------------------------------------------------------------------------------
	|
	| This value is the logger mode of the application.
	| 0 -> Silence
	| 1 -> Register in line
	| 2 -> Register on log file
	|
	*/
	'LOG_MODE' => 2,

  /*
	|-------------------------------------------------------------------------------------------------
	| Application log file
	|-------------------------------------------------------------------------------------------------
	|
	| The log file of the application.
	|
	*/
	'ERROR_LOG_FILE' => 'Error_log',

	/*
	|-------------------------------------------------------------------------------------------------
	| Primal application paths global variables
	|-------------------------------------------------------------------------------------------------
	|
	| These global variables are defined to get the base path of the 
	| application
	|
	*/
  'PATH' => [
    'root' => BASE_PATH.DS,
    'src' => BASE_PATH.DS.'src'.DS,
    'app' => BASE_PATH.DS.'src'.DS.'app'.DS,
    'controllers' => BASE_PATH.DS.'src'.DS.'controllers'.DS,
    'providers' => BASE_PATH.DS.'src'.DS.'providers'.DS,
    'services' => BASE_PATH.DS.'src'.DS.'services'.DS,
    'config' => BASE_PATH.DS.'src'.DS.'config'.DS,
    'resources' => BASE_PATH.DS.'src'.DS.'resources'.DS,
      'images' => BASE_PATH.DS.'src'.DS.'resources'.DS.'img'.DS,
      'logs' => BASE_PATH.DS.'src'.DS.'resources'.DS.'logs'.DS,
      'mail' => BASE_PATH.DS.'src'.DS.'resources'.DS.'mail'.DS,
        'templates' => BASE_PATH.DS.'src'.DS.'resources'.DS.'mail'.DS.'templates'.DS,
        'sent' => BASE_PATH.DS.'src'.DS.'resources'.DS.'mail'.DS.'sent'.DS,
    'vendor' => BASE_PATH.DS.'src'.DS.'vendor'.DS,
  ],
  /*
  |-------------------------------------------------------------------------------------------------
  | The mailing configuration
  |-------------------------------------------------------------------------------------------------
  |
  | For the application works properly, the mailing configuration must be 
  | set up as follows
  |
  */
  'MAILING' => [
    'client' => [
      'class' => App\Providers\Mail\Clients\PHPMailer::class,
      'props' => [
        'server' => [
          'Hostname',
          'Host',
          // The greeting
          'Helo',
          // TCP port to connect to
          'Port',
          // Specify credentials for the Mail server
          'Username',
          'Password',
        ],
        'mail' => [
          'Subject',
          'AltBody',
          'ConfirmReadingTo',
        ]
      ],
    ],
    // Log file path of the sending mail counter.
    'counterFilePath' => 'Cont_log',
    // Conexión con el buzón del remitente
    'credentials' => [
      'Hostname'        => 'smtp.hostinger.com',
      'Host'            => 'smtp.hostinger.com',
      'Helo'            => '',
      'Port'            => 465,
      'Username'        => 'info@dcueli.com',
      'Password'        => 'g*|LH8!p',
    ],
    'mailtimeout' => 10,
	  // Driver server mail
	  'driver' => 'smtp',
    // Encrypt server mail
    'encrypt' => 'tls',
    // Save sent mails registry on new file per mail
    'saveSentMailsRegistry' => TRUE,
    // [Optional] 
    // Loop time in seconds to send the email. NULL, 0 or no define the 'time' key
    // 'time'       => <<< EOT
    //   Example:
    //   'time'       => 900, // 15 minutes
    //   'time'       => NULL
    //   'time'       => 0
    // EOT,
  ],
];


