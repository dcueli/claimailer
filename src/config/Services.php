<?php declare(strict_types=1);

return [
  // Facades
	'config' => \App\Services\Config\ConfigurationService::class,
	'logger' => \App\Support\Helpers\Logger::class,

  // Classes
  'basexception' => \App\Support\Exceptions\Basexception::class,
];


