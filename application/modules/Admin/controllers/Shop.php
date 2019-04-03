<?php
class ShopController extends AdminController
{

    function init()
    {
        parent::init();
    }

    #商品列表
    function indexAction(){
        $condition = array();
        $condition['itemshorttitle'] = isset($_REQUEST['itemshorttitle']) ? trim($_REQUEST['itemshorttitle']) : '';
        $condition['itemid'] = isset($_REQUEST['itemid']) ? intval($_REQUEST['itemid']) : 0;
        $condition['fqcat'] = isset($_REQUEST['fqcat']) ? intval($_REQUEST['fqcat']) : 0;
        $condition['status'] = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : '';

        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $page_size = 20;
        $tb_model = new TbModel();
        $show_list = $tb_model->getListData($page,$page_size,$condition);

        $this->_view->show_list = $show_list;
        #分页处理
        $total_num = $tb_model->getListCount($condition);
        $pagination = $this->getPagination($total_num, $page, $page_size);
        $this->_view->page = $page;
        $this->_view->pager = new System_Page($this->base_url, $condition, $pagination);
        $this->_view->params = $condition;

        $this->_layout->meta_title = '商品列表';
    }

    #广告列表
    function bannerAction(){
        $condition = array();
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $page_size = 20;
        $banner_model = new BannerModel();
        $show_list = $banner_model->getListData($page,$page_size,$condition);

        $this->_view->show_list = $show_list;
        #分页处理
        $total_num = $banner_model->getListCount($condition);
        $pagination = $this->getPagination($total_num, $page, $page_size);
        $this->_view->page = $page;
        $this->_view->pager = new System_Page($this->base_url, $condition, $pagination);

        $this->_layout->meta_title = '广告列表';
    }

    #新增/编辑广告
    function createBannerAction(){
        $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        $banner_model = new BannerModel();

        $info = [];
        if($id > 0){
            $info = $banner_model->getData($id);
        }

        if($this->getRequest()->isPost()) {
            $data = array(
                'position' => trim($_REQUEST['position']),
                'type' => intval($_REQUEST['type']),
                'goto' => trim($_REQUEST['goto'])
            );

            if (!empty($_FILES['upload_file']) && $_FILES['upload_file']['error'] == 0) {
                $common_model = new CommonModel();
                $pic = $common_model->addPic($upload_file = 'upload_file');
                if (!empty($pic)) {
                    $data['pic'] = $pic;
                } else {
                    echo '上传失败';die;
                }
            }

            if($info['id']) {
                $banner_model->updateData($data, $info['id']);
            } else {
                $banner_model->addData($data);
            }

            $this->set_flush_message("编辑/添加广告成功");
            $this->redirect('/admin/shop/banner/');
            return FALSE;
        }

        $this->_view->info = $info;
        $this->_layout->meta_title = '编辑/添加广告';

    }

    #删除广告
    function delBannerAction(){
        $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        $banner_model = new BannerModel();
        if($id > 0){
            $banner_model->deleteData($id);
        }
        $this->set_flush_message("删除广告成功");
        $this->redirect('/admin/shop/banner/');
        return FALSE;
    }

    #配置
    function commonAction(){
        $type = !empty($_REQUEST['type']) ? trim($_REQUEST['type']) : CommonModel::TYPE[0];
        $common_model = new CommonModel();
        $data = $common_model->getDataByType($type);

        $this->_view->params = ['type' => $type];
        $this->_view->show_list = $data;
        $this->_layout->meta_title = '配置';
    }

    #新增/编辑配置
    function createCommonAction(){
        $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        $common_model = new CommonModel();

        $info = [];
        if($id > 0){
            $info = $common_model->getData($id);
        }

        if($this->getRequest()->isPost()) {
            $data = array(
                'type' => trim($_REQUEST['type']),
                'key' => intval($_REQUEST['key']),
                'value' => trim($_REQUEST['value'])
            );

            if($info['id']) {
                $common_model->updateData($data, $info['id']);
            } else {
                $common_model->addData($data);
            }

            $this->set_flush_message("编辑/添加配置成功");
            $this->redirect('/admin/shop/common/?type='.$data['type']);
            return FALSE;
        }

        $this->_view->info = $info;
        $this->_layout->meta_title = '编辑/添加配置';
    }

    #删除广告
    function delCommonAction(){
        $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        $common_model = new CommonModel();
        if($id > 0){
            $common_model->deleteData($id);
        }
        $this->set_flush_message("删除配置成功");
        $this->redirect('/admin/shop/common/');
        return FALSE;
    }

}