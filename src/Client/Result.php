<?php namespace Hht\AliPush\Client;

use Hht\Support\Contracts\Result as ResultContract;

class Result implements ResultContract
{
	
	/**
     * @var Error code
     */
	private $errorCode;
	/**
     * @var Data raw
     */
	private $raw;
	
	public function __construct($jsonStr)
	{
		$data = json_decode($jsonStr, true);
		$this->raw = $data;
		$this->errorCode = isset($data['Code']) ? $data['Code'] : 0;
	}
	
	/**
     * HttpBase getErrorCode.
	 * @return  String
     */
	public function getErrorCode()
	{
		return $this->errorCode;
	}
	
	/**
     * HttpBase getRaw.
	 * @return  Array
     */
	public function getRaw()
	{
		return $this->raw;
	}
}

?>
