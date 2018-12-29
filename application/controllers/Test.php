<?php
class TestController extends Yaf_Controller_Abstract
{
    function init(){
        header('content-type:text/html;charset=utf-8');
    }

    function addPicAction(){
        if($this->getRequest()->isPost()) {
            $model = new CommonModel();
            if (!empty($_FILES['upload_file']) && $_FILES['upload_file']['error'] == 0) {
                $pic = $model->addPic($upload_file = 'upload_file');
                if (!empty($pic)) {
                   echo CommonModel::IMAGE_URL.$pic;
                } else {
                  echo '上传失败';die;
                }
            }
        }
    }


    function testAction(){
        echo file_get_contents("https://douban.uieee.com/v2/movie/in_theaters");die;

    }

    public function planAction(){

        $contract_type = [
            '1'=>'普通合同',
            '2'=>'五星合同',
        ];


        $once_course = [
            '45'=>'0.75',
            '60'=>'1',
            '90'=>'1.5',
            '120'=>'2',
            '180'=>'3',

        ];

        $level = [
            1 => '周',
            2 => '二周',
            3 => '三周',
            5 => '月',
            6 => '天',
        ];
        $level_info = [
            1=>[
                '1'=>'周一',
                '2'=>'周二',
                '3'=>'周三',
                '4'=>'周四',
                '5'=>'周五',
                '6'=>'周六',
                '7'=>'周日',
            ],
            2=>[
                '1'=>'第一周周一',
                '2'=>'第一周周二',
                '3'=>'第一周周三',
                '4'=>'第一周周四',
                '5'=>'第一周周五',
                '6'=>'第一周周六',
                '7'=>'第一周周日',
                '8'=>'第二周周一',
                '9'=>'第二周周二',
                '10'=>'第二周周三',
                '11'=>'第二周周四',
                '12'=>'第二周周五',
                '13'=>'第二周周六',
                '14'=>'第二周周日',
            ],
            3=>[
                '1'=>'第一周周一',
                '2'=>'第一周周二',
                '3'=>'第一周周三',
                '4'=>'第一周周四',
                '5'=>'第一周周五',
                '6'=>'第一周周六',
                '7'=>'第一周周日',
                '8'=>'第二周周一',
                '9'=>'第二周周二',
                '10'=>'第二周周三',
                '11'=>'第二周周四',
                '12'=>'第二周周五',
                '13'=>'第二周周六',
                '14'=>'第二周周日',
                '15'=>'第三周周一',
                '16'=>'第三周周二',
                '17'=>'第三周周三',
                '18'=>'第三周周四',
                '19'=>'第三周周五',
                '20'=>'第三周周六',
                '21'=>'第三周周日',
            ],
            5=>[
                '1'=>'1号',
                '2'=>'2号',
                '3'=>'3号',
                '4'=>'4号',
                '5'=>'5号',
                '6'=>'6号',
                '7'=>'7号',
                '8'=>'8号',
                '9'=>'9号',
                '10'=>'10号',
                '11'=>'11号',
                '12'=>'12号',
                '13'=>'13号',
                '14'=>'14号',
                '15'=>'15号',
                '16'=>'16号',
                '17'=>'17号',
                '18'=>'18号',
                '19'=>'19号',
                '20'=>'20号',
                '21'=>'21号',
                '22'=>'22号',
                '23'=>'23号',
                '24'=>'24号',
                '25'=>'25号',
                '26'=>'26号',
                '27'=>'27号',
                '28'=>'28号',
                '29'=>'29号',
                '30'=>'30号',
                '31'=>'31号',
            ]
        ];

        if($this->getRequest()->isPost()) {
            $subjectId = 1;
            $courseName = '3';
            $courseVersion = '1';
            $level = 2;// 1-周 2-2周 3-3周  5-月 6-日
            $times = 4;// 频次
            $once = 60;// 45 60 90 120 ....
            $plan_start = '2018-09-01';
            $plan_end = '2018-12-01';

            $hope['1'] = [//第一周的周一
                [
                    'start_time' => '18:00:00',
                    'end_time' => '19:00:00'
                ],
                [
                    'start_time' => '19:00:00',
                    'end_time' => '20:00:00'
                ]
            ];

            $hope['11'] = [//第二周的周四
                [
                    'start_time' => '18:00:00',
                    'end_time' => '19:00:00'
                ],
                [
                    'start_time' => '19:00:00',
                    'end_time' => '20:00:00'
                ]
            ];

            $message = $this->checkTime($hope, $once_course, $once, 1, $times);
            if ($message) {
                echo $message;
                die;
            }


            //根据条件换合理时间(有序)
            $time_list = $this->getTimeList($plan_start,$plan_end,$level,$hope);

            //关联合同算课时
            $contract_type = 'pt';//普通合同
            $course_hour = $this->countCourseHour($contract_type,$time_list);

            $this->_view->show_list = $time_list;
            $this->_view->course_times = count($time_list);//总排课几次
            $this->_view->course_hour = $course_hour;
            $this->_view->params = $_REQUEST;
        }

        $this->_view->hope = $level_info;
        $this->_view->level = $level;
        $this->_view->once = $once_course;
    }


    #计算课时
    function countCourseHour($contract_type,$time_list,$once=''){

        $copy_contract = $pt_contract = $this->getContract($contract_type);
        $total_used_course_hour = 0;

        $contract_num = count($pt_contract);
        foreach($time_list as $val){
            if($once){//指定单次时长 这种情况被产品否决了 因为规定好每次时间还是根据具体差值来排
                $this_minute = $once;
            }else{
                $this_minute = (strtotime($val['end_time']) - strtotime($val['start_time'])) / 60;
            }

            foreach($pt_contract as $k=>$v){
                if(($pt_contract[$k]['ks'] * $pt_contract[$k]['type']) - $copy_contract[$k]['used'] >= $this_minute){
                    $copy_contract[$k]['used'] += $this_minute;
                    $used_course_hour = floor(($this_minute/$v['type'])*100)/100;//保留2位舍去
                    $copy_contract[$k]['ks'] = $copy_contract[$k]['ks']-$used_course_hour;
                    $total_used_course_hour += $used_course_hour;//总消耗课时
                    break;
                }elseif($contract_num == $k+1){ //最后一份合同
                    return '合同总课时不够';
                }

            }
        }
        return $total_used_course_hour;
    }

    #获取合同课时相关
    function getContract($contract_type){
        //普通合同
        $pt_ht = [
            [
                'ks'=>'10',//合同剩余课时
                'type'=>'45',//合同类型 单课时分钟数
            ],
            [
                'ks'=>'18',
                'type'=>'60',
            ]
        ];
        //五星合同
        $wx_ht = [
            [
                'ks'=>'10',
                'type'=>'45',
            ],
            [
                'ks'=>'20',
                'type'=>'60',
            ]
        ];
        return $contract_type == 'wx' ? $wx_ht : $pt_ht;
    }


    #获取排课时间
    function getTimeList($plan_start,$plan_end,$level,$hope)
    {
        $plan_first_weekday = date("w", strtotime($plan_start));//排课开始第一天是周几
        $week_first_day = 1; //以周几为周的第一天 1-周一 0-周日

        $week_count = 1;//第几周
        if ($plan_first_weekday == $week_first_day) {
            $week_count = 0;//第几周
        }

        $time_list = [];

        //循环首位时间处理
        $s_data = strtotime(date('Y-m-d', strtotime($plan_start)));
        $e_data = strtotime(date('Y-m-d', strtotime($plan_end)));

        for ($i = $s_data; $i <= $e_data; $i += 86400) {
            $w = date("w", $i);
            if ($level == 5) { //指定具体几号
                $d = intval(date("d", $i));
                if ($hope[$d]) {
                    foreach ($hope[$d] as $v) {
                        $time_list[] = $this->getTime($i, $v['start_time'], $v['end_time']);
                    }
                }
            } elseif ($level == 7) { //指定具体日期  可能用不上
                foreach ($hope as $key => $val) {
                    if (date("Ymd", $i) == date("Ymd", strtotime($key))) {
                        foreach ($val as $v) {
                            $time_list[] = $this->getTime($i, $v['start_time'], $v['end_time']);
                        }
                    }
                }
            } elseif ($level == 6) { //按天 (数组的一维下标为0)
                foreach ($hope[0] as $v) {
                    $time_list[] = $this->getTime($i, $v['start_time'], $v['end_time']);
                }
            } else {

                if ($w == $week_first_day) {
                    $week_count++;
                }

                foreach ($hope as $key => $val) {
                    $this_weekday = $key % 7;//周几
                    $this_week = ceil($key / 7);//第几周
                    if ($this_week == $level) {
                        $this_week = 0;
                    }
                    if (($week_count % $level == $this_week) && $this_weekday == $w) {
                        foreach ($val as $v) {
                            $time_list[] = $this->getTime($i, $v['start_time'], $v['end_time']);
                        }
                    }
                }

            }
        }

        return $time_list;
    }



    #判断时间是否合理 $times_type 0-不检查 1-强排课频次检查 2-弱频次检查
    function checkTime($data,$once_course,$once='',$times_type='',$times =''){
        $count_times = 0;//统计频次
        foreach($data as $val){
            foreach($val as $v){
                $start_time = strtotime($v['start_time']);
                $end_time = strtotime($v['end_time']);
                if($start_time > $end_time){//跨天
                    $end_time += 86400;
                }
                $this_minute = ($end_time-$start_time)/60;
                if($once && $this_minute < $once){
                    return '这节排课时间不够';
                }
                if(!in_array($this_minute,array_keys($once_course))){
                    return '这节排课时间不合理';
                }

                $count_times++;
            }
        }
        if($times_type==1 && $count_times <> $times){//排课频次(总次数)强检测
            return '排课频次(总次数)与可排课时间不一致';
        }
        if($times_type==2 && $count_times < $times){//排课频次(总次数)弱检测
            return '设定了频次(总次数),可选排课时间不够';
        }
        return false;
    }

    #拼接具体排课时间包含跨天处理
    function getTime($i,$start_time,$end_time){
        $start = date('Y-m-d',$i).' '.$start_time;
        $end = date('Y-m-d',$i).' '.$end_time;
        if(strtotime($start) > strtotime($end)){//跨天
            $end = date('Y-m-d',$i+86400).' '.$end;
        }
        return [
            'start_time' => $start,
            'end_time' => $end,
        ];
    }




    function tAction(){
        $plan_start = '2018-07-09 16:00:01';
        $plan_end = '2018-10-09 01:00:01';

        $s_data = date('Y-m-d',strtotime($plan_start));
        //$s_time = date('H:i:s',strtotime($plan_start));

        $e_data = date('Y-m-d',strtotime($plan_end));
        //$e_time = date('H:i:s',strtotime($plan_end));
        $start = '15:00:01';
        $end = '03:00:01';
        $s_time = strtotime($s_data);
        $e_time = strtotime($e_data);
        if(strtotime($plan_start) > strtotime($s_data.' '.$start) ){
            $s_time += 86400;
        }
        if(strtotime($e_data.' '.$end) > strtotime($plan_end)){
            $e_time += 86400;
        }

        echo date("Y-m-d H:i:s",$s_time).'<hr>';
        echo date("Y-m-d H:i:s",$e_time);die;
    }

    function gaoyongAction(){
        $request_url = 'http://v2.api.haodanku.com/ratesurl';
        //$request_url = 'http://v2.api.haodanku.com/super_classify/apikey/allfree';
        $request_data = array();
        $request_data['apikey'] = 'allfree';
        $request_data['itemid'] = '535615570326';
        $request_data['pid'] = 'mm_116356778_18618211_65740777';
        $request_data['activityid'] = '7a08d992d2d545ff9bacc0bb4fc4ed54';

        $res = $this->post_curl($request_url,$request_data);
        echo '<pre>';
        print_r($res);die;
    }


    function post_curl($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result_json = curl_exec($ch);
        curl_close($ch);
        return  json_decode($result_json, true);
    }

    function get_curl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result_json = curl_exec($ch);
        curl_close($ch);
        return  json_decode($result_json, true);
    }

    #淘宝详情页
    function tbAction(){
        $item_id = 571503809371;
        $taobao_model = new TaobaoModel(2);
        $condition = [
            'num_iid' => $item_id
        ];
        $item_info = $taobao_model->TaeItemsListRequest($condition);
        echo '<pre>';
        print_r($item_info);die;
        echo '<pre>';
//        print_r($item_info);die;
        $condition = [
            't_iid' => $item_info['open_iid']
        ];
        $item_info = $taobao_model->TaeItemDetailGetRequest($condition);
        print_r($item_info['mobile_desc_info']['desc_list']['desc_fragment']);
        die;

        die;
    }
//通过md5加密生成签名的函数
    function getSignature($appkey,$appsecret,$date,$tid){
        $string = $appkey.$appsecret.$date.$tid;
        $md5 = md5($string);
        return $md5;
    }
    #详情
    function xqyAction(){
        $val['itemid'] = 569279621940;
       // $val['itemid'] = 535615570326;


        //淘宝详细页
        $detail_info = 'https://acs.m.taobao.com/h5/mtop.taobao.detail.getdetail/6.0/?data=%7B%22itemNumId%22%3A%22'.$val['itemid'].'%22%7D';
        //淘宝图片
        $detail_pic = 'https://h5api.m.taobao.com/h5/mtop.taobao.detail.getdesc/6.0/?data={"id":"'.$val['itemid'].'"}';
        //淘宝图片  加密参数
        $url = 'https://hws.m.taobao.com/d/modulet/v5/WItemMouldDesc.do?id=535615570326&f=TB17qTGyAvoK1RjSZFN8qwxMVXa';

        //瞄有券
        $api = 'http://open.jxb001.cn/tbkopen/detail/tb/api';
        $appkey = '1530477080482482';
        $appsecret = '568176ec67c2d16e69158b2329a5bf29';
        $tbName = '川律网络';
        $api = $api. '?appkey='.$appkey.'&appsecret='.$appsecret.'&tbName='.$tbName.'&itemId='.$val['itemid'];
        //echo $api;die;
        $detail = file_get_contents($api);
        echo '<pre>';
        var_dump($detail);die;

        //api
        $appkey = 'tbweau7kx';
        $appsecret = 'vm9M6IvBizDITNLS';
        $tid = 569279621940;

        $date = date('Y-m-d'); //生成日期
        $signature = $this->getSignature($appkey,$appsecret,$date,$tid); //生成签名
        $data = json_encode(array('tid'=>$tid,'appkey'=>$appkey,'sign'=>$signature)); //输出json字符串到客户端
        $url = 'https://taoapi.ndxiu.com/service/get_detail_full.php?sign=b9d9d8ead9fc10ce03fb9ecc332dfeec&appkey=tbweau7kx&tid=569279621940'.$data;
echo $url;die;
        $detail = $this->get_curl($url);
        echo '<pre>';
        print_r($detail);die;




        $detail = $this->get_curl($detail_api);
        echo '<pre>';
        print_r($detail);die;
        if($detail['data']['item']['images'] && $detail['data']['item']['moduleDescUrl']){

            $taobao_img = 'https:'.implode(',https:',$detail['data']['item']['images']);
            print_r($taobao_img);die;
            $pic_url = 'https:'.$detail['data']['item']['moduleDescUrl'];
            $pic = $this->get_curl($pic_url);
            if($pic['data']['children'] ){
                foreach($pic['data']['children'] as $val){
                    if($val['key'] == 'detail_container_style7'){
                        echo $val['params']['picUrl'].'<br>';
                    }
                }
            }
        }die;
        print_r($detail);die;

        var_dump($aa);die;
    }


    function sendAction(){
        $mail = new SendMail();

       // var_dump($mail);die;
        $res = $mail->send(['xm0563@qq.com'],'测试','内容');
        var_dump($res);die;
    }


    private function log($content='') {
        $fp = fopen('/tmp/test_book.log','a');
        if(!$fp){
            return ;
        }
        fwrite($fp, $content);
        fclose($fp);
    }


}
