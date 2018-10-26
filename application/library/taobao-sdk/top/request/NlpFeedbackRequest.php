<?php
/**
 * TOP API: taobao.nlp.feedback request
 * 
 * @author auto create
 * @since 1.0, 2015.06.12
 */
class NlpFeedbackRequest
{
	/** 
	 * api接口名称
	 **/
	private $apiName;
	
	/** 
	 * 文本内容
	 **/
	private $content;
	
	/** 
	 * 反馈的具体原因
	 **/
	private $description;
	
	/** 
	 * 反馈类型 1-物流信息判断错误
	 **/
	private $type;
	
	private $apiParas = array();
	
	public function setApiName($apiName)
	{
		$this->apiName = $apiName;
		$this->apiParas["api_name"] = $apiName;
	}

	public function getApiName()
	{
		return $this->apiName;
	}

	public function setContent($content)
	{
		$this->content = $content;
		$this->apiParas["content"] = $content;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setDescription($description)
	{
		$this->description = $description;
		$this->apiParas["description"] = $description;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setType($type)
	{
		$this->type = $type;
		$this->apiParas["type"] = $type;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getApiMethodName()
	{
		return "taobao.nlp.feedback";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->content,"content");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
