<?php namespace Hht\AliPush;

use Illuminate\Support\Arr;
use Hht\AliPush\Client\ErrorCode;
use Hht\AliPush\Client\Result;
use Hht\AliPush\Client\Config;

class PusherClient 
{
	/**
	 * @var Http request address.
	 */
	protected $url;
	
	/**
	 * @var Http request action.
	 */
	protected $action;

	/**
	 * @var Http request format.
	 */
	protected $format;

	/**
	 * @var Api version.
	 */
	protected $version;

	/**
	 * @var region id.
	 */
	protected $region_id;

	/**
	 * @var access key.
	 */
	protected $access_key;

	/**
	 * @var access key secret.
	 */
	protected $access_key_secret;

	/**
	 * @var Http request timeout.
	 */
	protected $timeout;
	
	/**
	 * @var Config ios config.
	 */
	private $ios;
	
	/**
	 * @var Config android config.
	 */
	private $android;
	
	/**
	 * @var Config prefix.
	 */
	private $prefix;
	
	public function __construct($config) {
		$keys = ['url', 'action', 'format', 'version', 'region_id', 'access_key', 'access_key_secret', 'prefix'];

		foreach ($keys as $key)
		{
			$this->$key = Arr::get($config, $key);
		}

		$this->android = new Config(Arr::get($config, 'android'));
		$this->ios = new Config(Arr::get($config, 'ios'));
	}

	/**
	 * Get url.
	 *
	 * @return  String
	 */
	public function getUrl() {
		return $this->url;
	}
	
	/**
	 * Get prefix.
	 *
	 * @return  String
	 */
	public function getPrefix() {
		return $this->prefix;
	}
	
	/**
	 * Get ios config.
	 *
	 * @return  Hht\AliPush\Client\Config
	 */
	public function getIOSConfig() {
		return $this->ios;
	}
	
	/**
	 * Get android config.
	 *
	 * @return  Hht\AliPush\Client\Config
	 */
	public function getAndroidConfig() {
		return $this->android;
	}
		
	/**
	 * Post result.
	 *
	 * @param   Array    $fields
	 * @param   Int      $retries
	 * @param   String   $url
	 * @param   Array    $header
	 * @param   Int      $timeout
	 * @return  \Hht\AliPush\Client\Result
	 */
	public function postResult($url = '', $fields, $retries = 1, $header = [], $timeout = 0) 
	{
		$url = $url ? $url : $this->url;
		$timeout = $timeout ? $timeout : $this->timeout;

		for($i = 0; $i < $retries; $i ++) 
		{
			$result = new Result($this->request($url, $fields, $header, $timeout));
		    if ($result->getErrorCode() == ErrorCode::Success) break;
		}

		return $result;
	}
	
	/**
	 * Request.
	 *
	 * @param   String   $url
	 * @param   Array    $data
	 * @param   Array    $header
	 * @param   Int      $timeout
	 * @return  String
	 */
	public function request($url = '', $data = [], $header = [], $timeout = 30) 
	{
		$ch = curl_init();
		$data = $this->getParam($data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildQuery($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 

		$response = curl_exec($ch);

		if ($error = curl_error($ch)) {
			//die($error);
		}

		curl_close($ch);

		return $response;
	}

	private function getParam($data) {
		$data['Action'] = ucfirst($this->action);
		$data['Format'] = strtoupper($this->format);
		$data['RegionId'] = $this->region_id;
		$data['Version'] = $this->version;
		$data['AccessKeyId'] = $this->access_key;
		$data['SignatureMethod'] = 'HMAC-SHA1';
		$data['Timestamp'] = urlencode(gmdate('Y-m-d\TH:i:s\Z'));
		$data['SignatureVersion'] = '1.0';
		$data['SignatureNonce'] = time() . substr(microtime(), 2, 8) . mt_rand(11111, 99999);
		
		ksort($data);
		$str = 'POST&' . urlencode('/') . '&' . urlencode($this->buildQuery($data));
		
		$data['Signature'] = urlencode(base64_encode(hash_hmac("sha1", $str, $this->access_key_secret, true))); 

		return $data;
	}

	private function buildQuery($data) {
		$str = '';

		foreach ($data as $key => $val)
		{
			$str .= "{$key}={$val}&";
		}

		return substr($str, 0, -1);
	}
	
}
