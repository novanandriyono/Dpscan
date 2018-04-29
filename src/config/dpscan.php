<?php
return [

	/*
    |--------------------------------------------------------------------------
    | Dpscan Setting
    |--------------------------------------------------------------------------
    | root
    | we can block out scan from root dir
    |
    */

	'root' => base_path(),

	/*
	|
    | debug
    | debug for showing trow error set it false to disable
    |
    */
	'debug' => true,

	/*
	|
    | protected
    | we can block some string result from this setting
    |
    */

	'protected' => [
		// '.env',
		// '.env.example',
		// '.gitattributes',
		// '.gitignore',
		// 'app',
		// 'artisan',
		// 'bootstrap',
		// 'composer.json',
		// 'composer.lock',
		// 'config',
		// 'database',
		// 'package.json',
		// 'phpunit.xml',
		// //'public',
		// 'resources',
		// 'routes',
		// 'server.php',
		// //'storage',
		// 'tests',
		// 'vendor',
		// 'webpack.mix.js',
		// 'yarn.lock',
		// 'index.php',
		// '.htaccess'
	],
];