<?php namespace Heedworks\LaravelQueueAzure\Queue\Connectors;

use WindowsAzure\Common\ServicesBuilder;
use Heedworks\LaravelQueueAzure\Queue\AzureQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;

class AzureConnector implements ConnectorInterface {

	/**
	 * Establish a queue connection.
	 *
	 * @param  array  $config
	 * @return \Illuminate\Queue\QueueInterface
	 */
	public function connect(array $config)
	{
		$connectionString = "DefaultEndpointsProtocol={$config['protocol']};AccountName={$config['account']};AccountKey={$config['key']}";

		$azure = ServicesBuilder::getInstance()->createQueueService($connectionString);

		return new AzureQueue($azure, $config['queue']);
	}

}