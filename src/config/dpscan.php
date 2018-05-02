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

	'root' => false,

	/*
	|
    | debug
    | debug for showing trow error set it false to disable
    |
    */

	'debug' => true,

	/*
	|
    | cachepath
    | if we want to use cache we must set this config.
    | we set just path like. '/storage/framework/cache/data'
    | using cache() without enabling this config, will be show
    | error. false by default
    |
    */

	'cache' => false,

	/*
	|
    | cacheautoupdate
    | if we want to use auto update on cache we must set this
    | config to true. this setting will active if cache path
    | not false false by default. look wiki.
    |
    */

	'cacheautoupdate' => false,


	/*
	|
    | protected
    | we can block some string result from this setting
    | and disable .git from here
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
		// // 'vendor',
		// 'webpack.mix.js',
		// 'yarn.lock',
		// 'index.php',
		// '.htaccess'
			'.git'
	],
];