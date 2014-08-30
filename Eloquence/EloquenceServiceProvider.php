<?php

namespace Eloquence;

use Illuminate\Support\ServiceProvider;
use Eloquence\Database\Schema\Blueprint;

class EloquenceServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Initialises the service provider, and here we attach our own blueprint
	 * resolver to the schema, so as to provide the enhanced functionality.
	 */
	public function boot()
	{
		$this->app['schema']->blueprintResolver(function($table, $callback) {
			return new Blueprint($table, $callback);
		});
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
