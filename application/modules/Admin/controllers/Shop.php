<?php
class ShopController extends AdminController
{

    function init()
    {
        parent::init();
    }

    function indexAction(){

    }

    #广告列表
    function bannerAction(){
        $condition = array();
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $page_size = 20;
        $admin_user_model = new BannerModel();
        $show_list = $admin_user_model->getListData($page,$page_size,$condition);

        $this->_view->page = $page;
        $this->_view->show_list = $show_list;
        #分页处理
        $total_num = $admin_user_model->getListCount($condition);
        $pagination = $this->getPagination($total_num, $page, $page_size);
        $this->_view->page = $page;
        $this->_view->pager = new System_Page($this->base_url, $condition, $pagination);

        $this->_layout->meta_title = '广告列表';
    }

    #新增/编辑广告
    function createBannerAction(){
        $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        $book_model = new BannerModel();

        $info = [];
        if($id > 0){
            $info = $book_model->getData($id);
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
                $book_model->updateData($data, $info['id']);
            } else {
                $book_model->addData($data);
            }

            $this->set_flush_message("编辑/添加广告成功");
            $this->redirect('/admin/shop/banner/');
            return FALSE;
        }

        $this->_view->info = $info;
        $this->_layout->meta_title = '编辑/添加广告';

    }
}