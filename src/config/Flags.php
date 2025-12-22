<?php declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Kind of Log mode
|--------------------------------------------------------------------------
|
| Managing the log message is possible through the use of these global 
| definitions.
|
*/
define('LOG_SILENCE_MODE', 1 << 0); // 1
define('LOG_PRINT_MODE', 1 << 1); // 2
define('LOG_FILE_MODE', 1 << 2); // 4
