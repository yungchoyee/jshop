<?php
// +----------------------------------------------------------------------
// | JSHOP [ 小程序商城 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://jihainet.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mark <jima@jihainet.com>
// +----------------------------------------------------------------------
namespace app\Manage\controller;

use app\common\controller\Manage;
use app\common\model\WeixinMenu;
use app\common\model\WeixinMessage;
use app\common\validate\WeixinMenu as weixinMenuValidate;

use Request;
use app\common\model\Template;
use app\common\model\Setting;




class Wechat extends Manage
{

    private $author = [];//小程序授权信息
    private $authorType = 'b2c';//授权类型


    /**
     * 自助绑定小程序
     */
    public function edit()
    {
        $host = \request()->host();
        $this->assign('weixin_host',$host);
        $settingModel = new Setting();
        $data = $settingModel->getAll();
        $this->assign('data', $data);
        $wechat = config('thirdwx.');
        $this->assign('wechat', $wechat);
        return $this->fetch('edit');
    }

    public function doEdit()
    {
        $result            = [
            'status' => false,
            'data'   => '',
            'msg'    => '保存失败',
        ];
        $settingModel = new Setting();

        if(Request::isAjax()){
            foreach(input('param.') as $k => $v) {
                $result = $settingModel->setValue($k, $v);
                //如果出错，就返回，如果是没有此参数，就默认跳过
                if (!$result['status'] && $result['data'] != 10008) {
                    return $result;
                }
            }
            $result['status'] = true;
            $result['msg']    = '保存成功';
            return $result;
        }
        return $result;
    }


    /**
     *展示授权信息
     */
    public function info()
    {
        $settingModel = new Setting();
        $data = $settingModel->getAll();
        $this->assign('data', $data);
        $wechat = config('thirdwx.');
        $this->assign('wechat', $wechat);

        $host = \request()->host();
        $this->assign('weixin_host',$host);
        return $this->fetch('edit');
    }

    /**
     * 获取模板信息
     */
    public function template()
    {
        $templateModel = new Template();
        $data          = $templateModel->getAllTemplate($templateModel::TYPE_MINI);
        $this->assign('data', $data);
        return $this->fetch('template');
    }

    /**
     * 公众号配置
     * @return mixed
     */
    public function official(){
        $host = \request()->host();
        $this->assign('weixin_host',$host);
        $settingModel = new Setting();
        $data = $settingModel->getAll();
        $this->assign('data', $data);
        return $this->fetch('official');
    }

    public function officialMenu(){

        $weixinMenu  = new WeixinMenu();
        $menu = $weixinMenu->getMenu();
        $this->assign('weixin_menu',$menu);
        return $this->fetch('official_menu');
    }

    /**
     * 编辑菜单项
     */
    public function editMenu(){
        $result            = [
            'status' => false,
            'data'   => '',
            'msg'    => '参数错误',
        ];
        $this->view->engine->layout(false);
        $id = input('id/d');
        $pid = input('pid/d');
        if(!$id)
        {
            return $result;
        }
        $weixinMenu  = new WeixinMenu();
        $menu = $weixinMenu->where(['menu_id'=>$id,'pid'=>$pid])->find();
        if($menu){
            $menu['params'] = json_decode($menu['params'],true);
        }
        $site_url = \request()->domain();//站点地址


        $this->assign('id',$id);
        $this->assign('pid',$pid);//父级菜单ID
        $this->assign('menu',$menu);
        $this->assign('site_url',$site_url);
        $this->assign('wx_appid',getSetting('wx_appid'));
        return $this->fetch('edit_menu');

    }

    /**
     * 保存菜单
     */
    public function doEditMenu()
    {
        $result   = [
            'status' => false,
            'data'   => [],
            'msg'    => '参数错误',
        ];
        $data     = input('param.');
        $validate = new weixinMenuValidate();
        if (!$validate->check($data)) {
            $result['msg'] = $validate->getError();
            return $result;
        }
        $weixinMenu = new WeixinMenu();
        $res        = $weixinMenu->toSave($data);
        return $res;
    }

    /**
     * 删除菜单
     * @return array
     */
    public function deleteMenu()
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '参数错误',
        ];
        $id     = input('id/d', 0);
        $pid    = input('pid/d', 0);
        if (!$id) {
            return $result;
        }
        $weixinMenu = new WeixinMenu();
        $info       = $weixinMenu->where(['pid' => $pid, 'menu_id' => $id])->find();
        //无此菜单时，前台直接删除
        if (!$info) {
            $result['status'] = true;
            $result['msg']    = '删除成功';
            return $result;
        }
        // 当删除父节点菜单时，检查是否有子节点
        if ($pid == 0) {
            $nums = $weixinMenu->where(['pid' => $id])->count();
            if ($nums > 0) {
                $result['msg'] = '请先删除子节点菜单';
                return $result;
            }
        }
        $res = $weixinMenu->where(['pid' => $pid, 'menu_id' => $id])->delete();
        if (!$res) {
            $result['msg'] = '删除失败';
            return $result;
        }
        $result['status'] = true;
        $result['msg']    = '删除成功';
        return $result;
    }

    /**
     * 创建微信菜单
     * @return array
     */
    public function updateMenu()
    {
        $returnData = [
            'status' => false,
            'data'   => [],
            'msg'    => '参数错误',
        ];

        $menu = load_wechat('menu');
        //创建微信菜单
        $weixinMenu = new WeixinMenu();
        $menuData   = $weixinMenu->weixinMenu();
        $result     = $menu->createMenu($menuData);
        try{
            // 处理创建结果
            if ($result === FALSE) {
                // 接口失败的处理
                $returnData['msg'] = $menu->errMsg;
                return $returnData;
            } else {
                // 接口成功的处理
                $returnData['msg']    = '同步成功';
                $returnData['status'] = true;
                return $returnData;
            }
        }catch (Exception $e){
            $returnData['msg'] = $e->getMessage();
            return $returnData;
        }

    }

    /**
     * 微信消息管理
     * @return mixed
     */
    public function message(){
        if (Request::isAjax()) {
            $messageModel        = new WeixinMessage();
            $filter              = input('request.');
            return $messageModel->tableData($filter);
        }
        return $this->fetch();
    }

    /**
     * 添加微信消息
     * @return array|mixed
     */
    public function addMessage(){
        $this->view->engine->layout(false);
        if(Request::isPost())
        {
            $messageModel = new WeixinMessage();
            return $messageModel->addData(input('param.'));
        }
        return $this->fetch('add_message');
    }

    /**
     * 删除消息
     * @return array
     */
    public function delMessage()
    {
        $result       = [
            'status' => false,
            'msg'    => '删除失败',
            'data'   => [],
        ];
        $messageModel = new WeixinMessage();
        $id           = input('param.id/d', 0);
        if (!$id) {
            return $result;
        }
        if ($messageModel->where(['id' => $id])->delete()) {
            $result['status'] = true;
            $result['msg']    = '删除成功';
        }
        return $result;
    }

    /**
     * 编辑消息
     * @return array|mixed
     */
    public function editMessage(){
        $this->view->engine->layout(false);
        $messageModel = new WeixinMessage();
        if(Request::isPost())
        {
            return $messageModel->addData(input('param.'));
        }
        $data = $messageModel->where('id',input('param.id/d'))->find();
        if (!$data) {
            return error_code(10002);
        }
        $data['params'] = json_decode($data['params'],true);
        return $this->fetch('edit_message',['data' => $data]);
    }

    /**
     * 微信消息互动
     */
    public function autoreply(){

    }


}
