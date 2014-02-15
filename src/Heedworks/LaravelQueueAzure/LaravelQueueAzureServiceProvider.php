<?php namespace Heedworks\LaravelQueueAzure;

use Heedworks\LaravelQueueAzure\Queue\Connectors\AzureConnector;
use Illuminate\Support\ServiceProvider;
use Queue;

class LaravelQueueAzureServiceProvider extends ServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('heedworks/laravel-queue-azure');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->booted(function () {

			Queue::extend('azure', function () {
				return new AzureConnector;
			});

		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('azure');
	}
}