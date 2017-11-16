<?php namespace Hht\AliPush;

use Hht\AliPush\Config\ConfigAwareTrait;
use Hht\AliPush\Plugin\PluggableTrait;
use Hht\AliPush\Builder\Message;

class Pusher implements PusherInterface
{
    use PluggableTrait;
    use ConfigAwareTrait;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Constructor.
     *
     * @param AdapterInterface $adapter
     * @param Config|array     $config
     */
    public function __construct(AdapterInterface $adapter, $config = null)
    {
        $this->adapter = $adapter;
        $this->setConfig($config);
    }

    /**
     * Get the Adapter.
     *
     * @return AdapterInterface adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
	
	/**
     * Send a message to ios.
     *
     * @return \Hht\AliPush\Client\Result
     */
	public function sendToIos(Message $message)
	{
		return $this->adapter
			->setDeviceType('iOS')
			->send($message);
	}
	
	/**
     * Send a message to android.
     *
     * @return \Hht\AliPush\Client\Result
     */
	public function sendToAndroid(Message $message)
	{
		return $this->adapter
			->setDeviceType('ANDROID')
			->send($message);
	}
	
	/**
     * Send a message.
     *
     * @return \Hht\AliPush\Client\Result
     */
	public function send(Message $message)
	{
		return $this->adapter
			->setDeviceType('ALL')
			->send($message);
	}

	/**
     * Pass dynamic methods call onto PusherAdapter.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, array $parameters)
    {
        $back = call_user_func_array([$this->adapter, $method], $parameters);

		if ($back instanceof AdapterInterface)
			return $this;
		else
			return $back;
    }
}
