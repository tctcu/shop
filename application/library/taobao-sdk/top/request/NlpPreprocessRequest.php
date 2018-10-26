<?php
/**
 * TOP API: taobao.nlp.preprocess request
 * 
 * @author auto create
 * @since 1.0, 2016.01.12
 */
class NlpPreprocessRequest
{
	/** 
	 * 1)繁简字转换：func_type=1，对应type =1 繁转简 type=2 简转繁；2)拆分字转换：func_type =2，对应type=1 文字拆分 type=2 拆分字合并；3)文字转拼音：func_type =3，对应type=1 文字转拼音 type=2 拼音+声调；4)谐音同音字替换：func_type =4，对应type=1 谐音字替换 type=2 同音字替换；5)形似字替换：func_type =5，对应type=1 形似字替换;
	 **/
	private $funcType;
	
	/** 
	 * 谐音字转换、形似字转换需提供关键词进行替换，关键词之间以#分隔。keyword示例：兼职#招聘#微信、天猫#日结#微信#招聘#加微
	 **/
	private $keyword;
	
	/** 
	 * 文本内容
	 **/
	private $text;
	
	private $apiParas = array();
	
	public function setFuncType($funcType)
	{
		$this->funcType = $funcType;
		$this->apiParas["func_type"] = $funcType;
	}

	public function getFuncType()
	{
		return $this->funcType;
	}

	public function setKeyword($keyword)
	{
		$this->keyword = $keyword;
		$this->apiParas["keyword"] = $keyword;
	}

	public function getKeyword()
	{
		return $this->keyword;
	}

	public function setText($text)
	{
		$this->text = $text;
		$this->apiParas["text"] = $text;
	}

	public function getText()
	{
		return $this->text;
	}

	public function getApiMethodName()
	{
		return "taobao.nlp.preprocess";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->funcType,"funcType");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
