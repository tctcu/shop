<?php
/**
 * TOP API: taobao.nlp.semantic.texts.analyze request
 * 
 * @author auto create
 * @since 1.0, 2015.06.12
 */
class NlpSemanticTextsAnalyzeRequest
{
	/** 
	 * 文本内容
	 **/
	private $texts;
	
	/** 
	 * 需要获取的语义分析结果类型，以半角逗号(,)分隔,可以指定获取不同类型值的结果,以(=)号分割。如logistics_speed=0,logistics_speed=1;logistics_speed-物流速度分析;logistics_service-物流服务态度分析;logistics_package-物流包裹破损分析;
	 **/
	private $types;
	
	private $apiParas = array();
	
	public function setTexts($texts)
	{
		$this->texts = $texts;
		$this->apiParas["texts"] = $texts;
	}

	public function getTexts()
	{
		return $this->texts;
	}

	public function setTypes($types)
	{
		$this->types = $types;
		$this->apiParas["types"] = $types;
	}

	public function getTypes()
	{
		return $this->types;
	}

	public function getApiMethodName()
	{
		return "taobao.nlp.semantic.texts.analyze";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
