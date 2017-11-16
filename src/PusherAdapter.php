<?php namespace Hht\AliPush;

use Hht\AliPush\Builder\Message;
use Hht\AliPush\Client\Result;

class PusherAdapter implements AdapterInterface
{
	/**
     * @var Send field.
     */
	protected $field;
	
	/**
     * @var Send value.
     */
	protected $value;
	
	/**
     * @var Send device type.
     */
	protected $deviceType;
	
	/**
     * @var PusherClient
     */
    protected $client;

	public function __construct(PusherClient $client, $config = null) {
		$this->client = $client;
	}

	/**
	 * Set message send to.
	 *
	 * @param   String       $field
	 * @param   object       $value
	 */
	public function setSendTo($field, $value = true) {
		$this->field = $field;
		$this->value = $value;

		return $this;
	}

	/**
	 * Set message device type.
	 *
	 * @param   String       $field
	 * @param   object       $value
	 * @return  mixed
	 */
	public function setDeviceType($deviceType) {
		$this->deviceType = strtolower($deviceType);

		return $this;
	}
	
	/**
	 * Send.
	 *
	 * @param   Message    $message
	 * @return  \Hht\AliPush\Client\Result
	 */
    public function send(Message $message) {
		$fields = $message->queryParameters;
		
		return $this->_handSendTo($fields);
	}
	
	/**
	 * Private handler and send.
	 *
	 * @param   Message    $message
	 * @return  \Hht\AliPush\Client\Result
	 */
	private function _handSendTo($fields) {
		switch ($this->field)
		{
			case 'device':
				$fields['Target'] = 'DEVICE';
				$fields['TargetValue'] = is_array($this->value) ? implode(',', $this->value) : $this->value;
				break;
			case 'account':
				$fields['Target'] = 'ACCOUNT';
				$fields['TargetValue'] = is_array($this->value) ? implode(',', $this->value) : $this->value;
				break;
			case 'alias':
				$fields['Target'] = 'ALIAS';
				$fields['TargetValue'] = $this->client->getPrefix() . (is_array($this->value) ? (implode(',' . $this->client->getPrefix(), $this->value)) : $this->value);
				break;
			case 'tag':
				$fields['Target'] = 'TAG';
				$fields['TargetValue'] = is_array($this->value) ? implode(',', $this->value) : $this->value;
				break;
			case 'all':
				$fields['Target'] = 'ALL';
				$fields['TargetValue'] = 'all';
				break;
			default;
				return new Result(json_encode(['code' => '999']));
		}

		switch ($this->deviceType)
		{
			case 'ios':
				$fields['DeviceType'] = 'iOS';
				$fields['AppKey'] = $this->client->getIOSConfig()->app_key;
				break;
			case 'andriod':
				$fields['DeviceType'] = 'ANDROID';
				$fields['AppKey'] = $this->client->getAndroidConfig()->app_key;
				break;
			case 'all':
				$fields['DeviceType'] = 'ALL';
				$fields['AppKey'] = $this->client->getAndroidConfig()->app_key;
				break;
			default;
				return new Result(json_encode(['code' => '999']));
		}
		
		$url = $this->client->getUrl();
		
		if (empty($url))
			return new Result(json_encode(['code' => '999']));
		else
			return $this->client->postResult($url, $fields, 1);
	}
	
	/**
     * Pass dynamic methods call onto PusherAdapter.
     *
     * @param  string  $method
     * @param  object  $param
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
	public function __call($method, array $param)
	{
		$methods = ['setDevice', 'setAccount', 'setAlias', 'setTag', 'setAll'];

		if (in_array($method, $methods))
			$this->setSendTo(lcfirst(substr($method, 3)), $param);

		return $this;
	}
}
