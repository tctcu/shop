<?php

/**
 * 多文本内容
 * @author auto create
 */
class Texts
{
	
	/** 
	 * 文本相似度匹配文本内容模板
	 **/
	public $content_dst;
	
	/** 
	 * 文本相似度匹配文本
	 **/
	public $content_src;
	
	/** 
	 * 业务处理ID
	 **/
	public $id;
	
	/** 
	 * 文本相似度匹配类型：1为余弦距离，2为编辑距离，3为simHash距离
	 **/
	public $type;	
}
?>