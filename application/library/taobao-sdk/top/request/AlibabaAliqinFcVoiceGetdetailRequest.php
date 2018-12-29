<?php
/**
 * TOP API: alibaba.aliqin.fc.voice.getdetail request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class AlibabaAliqinFcVoiceGetdetailRequest
{
	/** 
	 * 呼叫唯一ID
	 **/
	private $callId;
	
	/** 
	 * 语音通知为:11000000300006, 语音验证码为:11010000138001, IVR为:11000000300005, 点击拨号为:11000000300004, SIP为:11000000300009
	 **/
	private $prodId;
	
	/** 
	 * Unix时间戳，会查询这个时间点对应那一天的记录（单位毫秒）
	 **/
	private $queryDate;
	
	private $apiParas = array();
	
	public function setCallId($callId)
	{
		$this->callId = $callId;
		$this->apiParas["call_id"] = $callId;
	}

	public function getCallId()
	{
		return $this->callId;
	}

	public function setProdId($prodId)
	{
		$this->prodId = $prodId;
		$this->apiParas["prod_id"] = $prodId;
	}

	public function getProdId()
	{
		return $this->prodId;
	}

	public function setQueryDate($queryDate)
	{
		$this->queryDate = $queryDate;
		$this->apiParas["query_date"] = $queryDate;
	}

	public function getQueryDate()
	{
		return $this->queryDate;
	}

	public function getApiMethodName()
	{
		return "alibaba.aliqin.fc.voice.getdetail";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->callId,"callId");
		RequestCheckUtil::checkNotNull($this->prodId,"prodId");
		RequestCheckUtil::checkNotNull($this->queryDate,"queryDate");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
