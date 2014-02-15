<?php namespace Heedworks\LaravelQueueAzure\Queue\Jobs;

use WindowsAzure\Queue\Models\WindowsAzureQueueMessage;
use WindowsAzure\Queue\QueueRestProxy;
use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\Job;
use Queue;

class AzureJob extends Job {

	/**
	 * The Microsoft Azure client instance.
	 *
	 * @var WindowsAzure\Queue\QueueRestProxy
	 */
	protected $azure;

	/**
	 * The queue URL that the job belongs to.
	 *
	 * @var string
	 */
	protected $queue;

	/**
	 * The Microsoft Azure job instance.
	 *
	 * @var WindowsAzure\Queue\Models\WindowsAzureQueueMessage
	 */
	protected $job;

	/**
	 * Create a new job instance.
	 *
	 * @param  \Illuminate\Container\Container  $container
	 * @param  \WindowsAzure\Queue\QueueRestProxy  $azure
	 * @param  string  $queue
	 * @param  WindowsAzure\Queue\Models\WindowsAzureQueueMessage   $job
	 * @return void
	 */
	public function __construct(Container $container,
                                QueueRestProxy $azure,
								$queue,
								WindowsAzureQueueMessage $job)
	{
		$this->azure = $azure;
		$this->job = $job;
		$this->queue = $queue;
		$this->container = $container;
	}

	/**
	 * Fire the job.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->resolveAndFire(json_decode($this->getRawBody(), true));
	}

	/**
	 * Get the raw body string for the job.
	 *
	 * @return string
	 */
	public function getRawBody()
	{
		return $this->job->getMessageText();
	}

	/**
	 * Delete the job from the queue.
	 *
	 * @return void
	 */
	public function delete()
	{
		$messageId = $this->job->getMessageId();
		$popReceipt = $this->job->getPopReceipt();

		$this->azure->deleteMessage($this->queue, $messageId, $popReceipt);
	}

	/**
	 * Release the job back into the queue.
	 *
	 * @param  int   $delay
	 * @return void
	 */
	public function release($delay = 0)
	{
		$messageId = $this->job->getMessageId();
		$popReceipt = $this->job->getPopReceipt();
		$messageText = $this->job->getMessageText();

		$this->azure->updateMessage($this->queue, $messageId, $popReceipt, $messageText, $delay);
	}

	/**
	 * Get the number of times the job has been attempted.
	 *
	 * @return int
	 */
	public function attempts()
	{
		return $this->job->getDequeueCount();
	}

	/**
	 * Get the job identifier.
	 *
	 * @return string
	 */
	public function getJobId()
	{
		return $this->job->getMessageId();
	}

	/**
	 * Get the IoC container instance.
	 *
	 * @return \Illuminate\Container
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Get the underlying Azure service instance.
	 *
	 * @return \WindowsAzure\Queue\QueueRestProxy
	 */
	public function getAzure()
	{
		return $this->azure;
	}

	/**
	 * Get the underlying raw Azure job.
	 *
	 * @return array
	 */
	public function getAzureJob()
	{
		return $this->job;
	}

}