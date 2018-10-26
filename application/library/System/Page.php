<?php
class System_Page {
    private $params = array();
    private $pager = array();
    private $id = '';
    private $base_url = '';


    function __construct($base_url= '',$params = array(),$pager = array(),$id ='pagination'){
        $this->params = $params;
        $this->pager = $pager;
        $this->id = $id;
        $this->base_url = $base_url;
    }
    
    function url($params){
        
        return $this->base_url.'?'.http_build_query($params);
    }

	function render($return = false){
        $attrs = array(
            'length' => 9,
            'slider' => 2,
            'prev_label' => '&lt;前页',
            'next_label' => '后页&gt;',
        );

        $pager = $this->pager;
        $id = $this->id;

        if(! $pager['record_count']
            || ! ($pager['page_count'] > 1))
        {
            return '';
        }

        $params = array_merge($_GET,$this->params);

        $s = "<ul class=\"{$id}\" style='display: inline'>\n";

        if ($pager['current'] == $pager['first'])
        {
            $s .= "<li class=\"disabled\"><a>{$attrs['prev_label']}</a></li>\n";
        }
        else
        {
            $params['page'] = $pager['prev'];
            $url = $this->url($params);
            $s .= "<li><a href=\"{$url}\">{$attrs['prev_label']}</a></li>\n";
        }

        $current = $pager['current'];

        $mid = intval($attrs['length'] / 2);
        if ($current < $pager['first']) {
            $current = $pager['first'];
        }
        if ($current > $pager['last']) {
            $current = $pager['last'];
        }

        $begin = $current - $mid;
        if ($begin < $pager['first']) { $begin = $pager['first']; }
        $end = $begin + $attrs['length'] - 1;
        if ($end >= $pager['last']) {
            $end = $pager['last'];
            $begin = $end - $attrs['length'] + 1;
            if ($begin < $pager['first']) { $begin = $pager['first']; }
        }

        if ($begin > $pager['first']) {
            for ($i = $pager['first']; $i < $pager['first'] + $attrs['slider'] && $i < $begin; $i++) {
                $params['page'] = $i;
                $url = $this->url($params);
                $s .= "<li><a href=\"{$url}\">{$i}</a></li>\n";
            }

            if ($i < $begin) {
                $s .= "<li><span>...</span></li>";
            }
        }

        for ($i = $begin; $i <= $end; $i++) {
            $params['page'] = $i;
            $url = $this->url($params);
            if ($i == $pager['current']) {
                $s .= "<li class=\"active\" ><a href=\"{$url}\">{$i}</a></li>\n";
            } else {
                $s .= "<li><a href=\"{$url}\">{$i}</a></li>\n";
            }
        }

        if ($pager['last'] - $end > $attrs['slider']) {
            $s .= "<li><span>...</span></li>";
            $end = $pager['last'] - $attrs['slider'];
        }

        for ($i = $end + 1; $i <= $pager['last']; $i++) {
            $params['page'] = $i;
            $url = $this->url($params);
            $s .= "<li><a href=\"{$url}\">{$i}</a></li>\n";
        }

        if ($pager['current'] == $pager['last'])
        {
            $s .= "<li class=\"disabled\" ><a>{$attrs['next_label']}</a>\n";
        }
        else
        {
            $params['page'] = $pager['next'];
            $url = $this->url($params);
            $s .= "<li><a  href=\"{$url}\">{$attrs['next_label']}</a></li>\n";
        }

        $s .= "</ul>\n";

        $params['page'] = '';
        $url = $this->url($params);

        $s .= "<input type='text' id='page_num' style='width: 40px;margin-left: 20px;height: 30px;'/>";
        $s .= "<button type='button' style='margin-left: 20px;height: 35px;' onclick=\"var page_num=document.getElementById('page_num').value;var url='{$url}'+page_num;window.location.href=url;\">跳转</button>";
        if($return)
        {
                return $s;  
        }
        echo $s;
    }
    
    function render_small($return = false){
        $attrs = array(
            'length' => 3,
            'slider' => 1,
            'first_label' => '&lt;首页',
            'last_label' => '末页&gt;',
        );

        $pager = $this->pager;
        $id = $this->id;

        if(! $pager['record_count']
            || ! ($pager['page_count'] > 1))
        {
            return '';
        }

        $params = array_merge($_GET,$this->params);

        $s = "<ul class=\"{$id}\">\n";

        if ($pager['current'] == $pager['first'])
        {
            $s .= "<li class=\"disabled\"><a>{$attrs['first_label']}</a></li>\n";
        }
        else
        {
            $params['page'] = 1;
            $url = $this->url($params);
            $s .= "<li><a href=\"{$url}\">{$attrs['first_label']}</a></li>\n";
        }

        $current = $pager['current'];

        $mid = intval($attrs['length'] / 2);
        if ($current < $pager['first']) {
            $current = $pager['first'];
        }
        if ($current > $pager['last']) {
            $current = $pager['last'];
        }

        $begin = $current - $mid;
        if ($begin < $pager['first']) { $begin = $pager['first']; }
        $end = $begin + $attrs['length'] - 1;
        if ($end >= $pager['last']) {
            $end = $pager['last'];
            $begin = $end - $attrs['length'] + 1;
            if ($begin < $pager['first']) { $begin = $pager['first']; }
        }

        for ($i = $begin; $i <= $end; $i++) {
            $params['page'] = $i;
            $url = $this->url($params);
            if ($i == $pager['current']) {
                $s .= "<li class=\"active\" ><a href=\"{$url}\">{$i}</a></li>\n";
            } else {
                $s .= "<li><a href=\"{$url}\">{$i}</a></li>\n";
            }
        }

        if ($pager['current'] == $pager['last'])
        {
            $s .= "<li class=\"disabled\" ><a>{$attrs['last_label']}</a>\n";
        }
        else
        {
            $params['page'] = $pager['last'];
            $url = $this->url($params);
            $s .= "<li><a  href=\"{$url}\">{$attrs['last_label']}</a></li>\n";
        }

        $s .= "</ul>\n";

        if($return) {
            return $s;
        }
        echo $s;
    }
}