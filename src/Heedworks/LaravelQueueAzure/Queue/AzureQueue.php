<?php namespace Heedworks\LaravelQueueAzure\Queue;

use WindowsAzure\Queue\Models\ListMessagesOptions;
use WindowsAzure\Queue\Models\PeekMessagesOptions;
use WindowsAzure\Queue\Models\CreateMessageOptions;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Queue\QueueRestProxy;
use Heedworks\LaravelQueueAzure\Queue\Jobs\AzureJob;
use Illuminate\Queue\Queue;
use Illuminate\Queue\QueueInterface;

class AzureQueue extends Queue implements QueueInterface {

	/**
	 * The Microsoft Azure instance.
	 *
	 * @var WindowsAzure\Queue\QueueRestProxy
	 */
	protected $azure;

	/**
	 * The name of the default tube.
	 *
	 * @var string
	 */
	protected $default;

	/**
	 * Create a new Microsoft Azure queue instance.
	 *
	 * @param  WindowsAzure\Queue\QueueRestProxy  $azure
	 * @param  string  $default
	 * @return void
	 */
	public function __construct(QueueRestProxy $azure, $default)
	{
		$this->azure = $azure;
		$this->default = $default;
	}

	/**
	 * Push a new job onto the queue.
	 *
	 * @param  string  $job
	 * @param  mixed   $data
	 * @param  string  $queue
	 * @return mixed
	 */
	public function push($job, $data = '', $queue = null)
	{
		return $this->pushRaw($this->createPayload($job, $data), $queue);
	}

	/**
	 * Push a raw payload onto the queue.
	 *
	 * @param  string  $payload
	 * @param  string  $queue
	 * @param  array   $options
	 * @return mixed
	 */
	public function pushRaw($payload, $queue = null, array $options = array())
	{
		return $this->azure->createMessage($this->getQueue($queue), $payload);
	}

	/**
	 * Push a new job onto the queue after a delay.
	 *
	 * @param  \DateTime|int  $delay
	 * @param  string  $job
	 * @param  mixed   $data
	 * @param  string  $queue
	 * @return mixed
	 */
	public function later($delay, $job, $data = '', $queue = null)
	{
		$options = new CreateMessageOptions();
		$options->setVisibilityTimeoutInSeconds($this->getSeconds($delay));

		return $this->azure->createMessage($this->getQueue($queue), $this->createPayload($job, $data), $options);
	}

	/**
	 * Pop the next job off of the queue.
	 *
	 * @param  string  $queue
	 * @return \Illuminate\Queue\Jobs\Job|null
	 */
	public function pop($queue = null)
	{
		$options = new ListMessagesOptions();
		$options->setNumberOfMessages(1);

		$queue = $this->getQueue($queue);

		$listMessagesResult = $this->azure->listMessages($queue, $options);
		$messages = $listMessagesResult->getQueueMessages();

		if (count($messages)) {
			return new AzureJob($this->container, $this->azure, $queue, $messages[0]);
		}
	}

	public function peak($queue = null)
	{
		$options = new PeekMessagesOptions();
		$options->setNumberOfMessages(32);

		$queue = $this->getQueue($queue);

		$peekMessagesResult = $this->azure->peekMessages($queue, $options);

		$messages = $peekMessagesResult->getQueueMessages();

		return $messages;
	}

	/**
	 * Get the queue or return the default.
	 *
	 * @param  string|null  $queue
	 * @return string
	 */
	protected function getQueue($queue)
	{
		$_queue = $queue ?: $this->default;

		$this->azure->createQueue($_queue);

		return $_queue;
	}

	/**
	 * Get the underlying Azure instance.
	 *
	 * @return WindowsAzure\Common\ServicesBuilder
	 */
	public function getAzure()
	{
		return $this->azure;
	}

}