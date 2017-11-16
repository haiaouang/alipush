<?php namespace Hht\AliPush;

use Hht\AliPush\Builder\Message;

interface AdapterInterface
{
	public function setSendTo($field, $value = true);

	public function setDeviceType($deviceType);

	public function send(Message $message);
}
