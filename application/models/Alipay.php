<?php
include_once(dirname(dirname(__FILE__)) . '/library/Alipay/AopClient.php'); // 加载支付宝sdk

/**
 * @name 支付宝模型
 */
class AlipayModel
{
    protected $aop = null;
    protected $appId = '';

    public function __construct()
    {
        $alipay_config = Yaf_Registry::get("config")->get('alipay');
        $this->appId = $alipay_config->appId;

        $this->aop = new AopClient ();
        $this->aop->gatewayUrl = $alipay_config->gatewayUrl;//网关地址
        $this->aop->appId = $this->appId;
        #应用私钥
        $this->aop->rsaPrivateKey = $alipay_config->rsaPrivateKey;
        #支付宝公钥
        $this->aop->alipayrsaPublicKey = $alipay_config->alipayrsaPublicKey;
        $this->aop->apiVersion = '1.0';
        $this->aop->signType = 'RSA2';
        $this->aop->postCharset = 'UTF-8';
        $this->aop->format = 'json';
    }

    #自动加载
    private function autoload($class)
    {
        $filename = dirname(dirname(__FILE__)) . "/library/Alipay/request/" . $class . ".php";
        if (is_file($filename)) {
            include_once(dirname(dirname(__FILE__)) . '/library/Alipay/request/' . $class . '.php'); // 自动加载类
        }
        return;
    }


    /**
     * 单笔转账到支付宝账户
     * @param out_biz_no :商户转账唯一订单号
     * @param payee_account : 收款方账户。与payee_type配合使用。付款方和收款方不能是同一个账户。
     * @param amount :转账金额，单位：元。
     * @param payer_show_name :付款方姓名
     * @param payee_real_name :收款方真实姓名
     * @param remark :转账备注
     * @return array order_id:支付宝转账单据号
     * @return array pay_date:支付时间：格式为yyyy-MM-dd HH:mm:ss，仅转账成功返回。
     */
    public function AlipayFundTransToaccountTransferRequest($out_biz_no, $payee_account, $payee_real_name, $amount)
    {
        $ret = array(
            'errcode' => 1,
            'errmsg' => '',
            'errmsg_alipay' => '',
            'order_id' => '',
            'pay_date' => ''
        );
        if (empty($out_biz_no) || empty($payee_account) || empty($payee_real_name) || empty($amount)) {
            $ret['errcode'] = 1;
            $ret['errmsg'] = '参数错误';
            return $ret;
        }
        $this->autoload('AlipayFundTransToaccountTransferRequest');
        $request = new AlipayFundTransToaccountTransferRequest();

        $bizContent = array(
            "out_biz_no" => $out_biz_no,
            "payee_type" => 'ALIPAY_LOGONID',//'ALIPAY_LOGONID',//收款方账户类型 1、ALIPAY_USERID：支付宝账号对应的支付宝唯一用户号。以2088开头的16位纯数字组成。 2、ALIPAY_LOGONID：支付宝登录号，支持邮箱和手机号格式。
            "payee_account" => $payee_account,
            "amount" => $amount,
            "payer_show_name" => '券购',//付款方姓名
            "payee_real_name" => $payee_real_name,
            "remark" => "券购返利",
        );
        $request->setBizContent(json_encode($bizContent));
        $result = $this->aop->execute($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode)) {
            if ($resultCode == 10000) {
                $ret['errcode'] = 2;
                $ret['errmsg'] = '成功';
                $ret['order_id'] = $result->$responseNode->order_id;
                $ret['pay_date'] = $result->$responseNode->pay_date;
            } else {
                $sub_code = $result->$responseNode->sub_code;
                $sub_msg = $result->$responseNode->sub_msg;
echo $sub_msg;die;
                if ($sub_code == 'SYSTEM_ERROR') {
                   // self::TransToaccount($out_biz_no, $payee_account, $payee_real_name, $amount);
                }
                /*switch ($sub_code){
                    case 'INVALID_PARAMETER':
                        $ret['errmsg'] = '参数错误！';
                        break;
                    case 'SYSTEM_ERROR':
                        self::TransToaccount($out_biz_no, $payee_account, $payee_real_name, $amount);
                        $ret['errmsg'] = '支付宝系统繁忙，请稍候再试！';
                        break;
                    case 'PERMIT_CHECK_PERM_LIMITED':
                    case 'PERM_AML_NOT_REALNAME_REV':
                    case 'EXCEED_LIMIT_UNRN_DM_AMOUNT':
                    case 'PERMIT_NON_BANK_LIMIT_PAYEE':
                        $ret['errmsg'] = '收款账号需补充身份信息!';
                        break;
                    case 'PAYEE_NOT_EXIST':
                        $ret['errmsg'] = '收款账号不存在!';
                        break;
                    case 'PAYEE_USER_INFO_ERROR':
                        $ret['errmsg'] = '支付宝账号和姓名不匹配，请确认姓名是否正确!';
                        break;
                    case 'PAYER_BALANCE_NOT_ENOUGH':
                        $ret['errmsg'] = '付款方余额不足!';
                        break;
                    case 'PAYMENT_INFO_INCONSISTENCY':
                        $ret['errmsg'] = '两次请求商户单号一样，但是参数不一致!';
                        break;
                    case 'EXCEED_LIMIT_DM_MAX_AMOUNT':
                        $ret['errmsg'] = '单日最多可转100万元。!';
                        break;
                    default :
                        $ret['errmsg'] = '提现异常,请联系客服人员.错误码：' . $msg;
                        break;
                }*/
            }
            return $ret;
        } else {
            $ret['errcode'] = 1;
            $ret['errmsg'] = '支付宝连接失败';
            return $ret;
        }
    }


    #芝麻认证初始化 获取biz_no
    public function ZhimaCustomerCertificationInitializeRequest($transaction_id, $identity_param = '')
    {
        $this->autoload('ZhimaCustomerCertificationInitializeRequest');
        $request = new ZhimaCustomerCertificationInitializeRequest();
        if (empty($identity_param)) {
            $identity_param = new StdClass();//不加个人信息验证
        }
        $req = [
            "transaction_id" => $transaction_id,
            "product_code" => "w1010100000000002978",
            "biz_code" => "FACE",//FACE:人脸 CERT_PHOTO_FACE:证照和人脸 CERT_PHOTO:证照 SMART_FACE:快捷
            "identity_param" => $identity_param
        ];

        $request->setBizContent(json_encode($req));
        $result = $this->aop->execute($request);print_r($result);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            return $result->$responseNode->biz_no;
        } else {
            return false;
        }
    }


    #芝麻认证查询
    public function ZhimaCustomerCertificationQueryRequest($biz_no)
    {
        $this->autoload('ZhimaCustomerCertificationQueryRequest');
        $request = new ZhimaCustomerCertificationQueryRequest();
        $req = [
            'biz_no' => $biz_no
        ];
        $request->setBizContent(json_encode($req));
        $result = $this->aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            return (array)$result->$responseNode;
        } else {
            return false;
        }
    }

    #芝麻认证开始认证
    public function ZhimaCustomerCertificationCertifyRequest($biz_no, $redirect_uri)
    {
        $this->autoload('ZhimaCustomerCertificationCertifyRequest');
        $request = new ZhimaCustomerCertificationCertifyRequest ();
        $req = [
            'biz_no' => $biz_no
        ];
        $request->setBizContent(json_encode($req));
        $request->setReturnUrl($redirect_uri);
        $request_url = $this->aop->pageExecute($request, "GET");

        return $request_url;
    }


    #授权查询 待完成
    public function ZhimaAuthInfoAuthqueryRequest()
    {
        $this->autoload('ZhimaAuthInfoAuthqueryRequest');
        $request = new ZhimaAuthInfoAuthqueryRequest ();
        $request->setBizContent("{" .
            "\"identity_param\":\"{\\\"certType\\\":\\\"IDENTITY_CARD\\\",\\\"name\\\":\\\"张三\\\",\\\"certNo\\\":\\\"33021199003132432\\\"}\"," .
            "\"identity_type\":\"2\"," .
            "\"auth_category\":\"C2B\"" .
            "  }");
        $result = $this->aop->execute($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            echo "成功";
        } else {
            echo "失败";
        }
    }

    #蚁盾
    public function mobileRisk($mobile)
    {
        $this->autoload('SsdataDataserviceRiskRainscoreQueryRequest');
        $request = new SsdataDataserviceRiskRainscoreQueryRequest ();
        $request->setBizContent("{" .
            "\"account_type\":\"MOBILE_NO\"," .
            "\"account\":\"{$mobile}\"," .
            "\"version\":\"2.0\"" .
            "  }");
        $result = $this->aop->execute ( $request);print_r($result);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }

    #芝麻分 待调试
    public function ZhimaCreditScoreGetRequest($accessToken)
    {
        $this->autoload('ZhimaCreditScoreGetRequest');
        $request = new ZhimaCreditScoreGetRequest ();
        $req = [
            'transaction_id' => '201512100936588040000000465158',
            'product_code' => 'w1010100100000000001',
        ];
        $request->setBizContent(json_encode($req));
        $result = $this->aop->execute($request, $accessToken);
        echo '<pre>';
        var_dump($result);
        die;
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            echo "成功";
        } else {
            echo "失败";
        }
    }

    #授权
    public function oauth2code($redirect_uri)
    {
        $redirect_uri = urlencode($redirect_uri);
        $request_url = "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=" . $this->appId . "&scope=auth_user,auth_base,auth_ecard&redirect_uri=" . $redirect_uri;
        //return $request_url;die;
        //手机端支付宝app外换起支付宝
        $request_url = "alipays://platformapi/startapp?appId=20000067&url=" . urlencode($request_url);
        return $request_url;
        header("Location:$request_url");
        exit;
    }

    #换取token
    public function AlipaySystemOauthTokenRequest($type, $code_or_token)
    {
        $this->autoload('AlipaySystemOauthTokenRequest');
        $request = new AlipaySystemOauthTokenRequest ();
        $request->setGrantType($type);//值为authorization_code时，代表用code换取,值为refresh_token时，代表用refresh_token换取
        if ($type == 'refresh_token') {
            $request->setRefreshToken($code_or_token);
        } else {
            $request->setCode($code_or_token);
        }

        $result = $this->aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        if (isset($result->$responseNode->access_token) && !empty($result->$responseNode->access_token)) {
            return [
                'user_id' => $result->$responseNode->user_id,
                'access_token' => $result->$responseNode->access_token,
                'refresh_token' => $result->$responseNode->refresh_token,
            ];
        } else {
            return "失败";
        }
    }


    #获取资料
    public function AlipayUserInfoShareRequest($accessToken)
    {
        $this->autoload('AlipayUserInfoShareRequest');
        $request = new AlipayUserInfoShareRequest ();
        $result = $this->aop->execute($request, $accessToken);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            return (array)$result->$responseNode;
        } else {
            return "失败";
        }
    }


}