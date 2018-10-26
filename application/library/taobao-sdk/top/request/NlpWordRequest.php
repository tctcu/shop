<?php
/**
 * TOP API: taobao.nlp.word request
 * 
 * @author auto create
 * @since 1.0, 2016.01.12
 */
class NlpWordRequest
{
	/** 
	 * 文本内容
	 **/
	private $text;
	
	/** 
	 * 功能类型选择：1)wType=1时提供分词功能，type=0时为基本粒度，type=1时为混合粒度，type=3时为基本粒度和混合粒度共同输出；
	 **/
	private $wType;
	
	private $apiParas = array();
	
	public function setText($text)
	{
		$this->text = $text;
		$this->apiParas["text"] = $text;
	}

	public function getText()
	{
		return $this->text;
	}

	public function setwType($wType)
	{
		$this->wType = $wType;
		$this->apiParas["w_type"] = $wType;
	}

	public function getwType()
	{
		return $this->wType;
	}

	public function getApiMethodName()
	{
		return "taobao.nlp.word";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->wType,"wType");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
