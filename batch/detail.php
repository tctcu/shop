<?php

$detail_api = 'https://h5api.m.taobao.com/h5/mtop.taobao.detail.getdesc/6.0/?data={"id":"'.$val['itemid'].'"}';
$detail = get_curl($detail_api);
print_r($detail);die;
$insert['taobao_detail'] = $detail['data'] ? $detail['data']['pcDescContent'] : '';
print_r($insert);die;