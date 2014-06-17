<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   打包台登陆信息(dbtdl)
 * 作成者：ZhangZeliang
 * 作成日：2011/05/18
 * 更新履历：
 *********************************/
class cc_kqdlController extends cc_controllers_baseController 
{
    /*
     * 首页
     */
    public function indexAction()
    {
        $this->_view->assign('title', '仓储管理-库区登陆');
        $model = new cc_models_kqdl();
        $array_ckxx = $model->get_ckxx(); //获取仓库信息
        //如仓库信息中只有一条数据，则将该条数据默认显示在打开页面中
        if(count($array_ckxx) == 1)
        {
            $this->_view->assign('con', 'one');
            $this->_view->assign('rec',$array_ckxx[0]);
        }else
        {
            $this->_view->assign('con', 'more');
        }
        $this->_view->display('kqdl_01.php');
    }
    
    /*
     * 下一步
     */
    public function nextAction()
    {
        $filter['ckbh'] = $this->_getParam('CKBH');
        $filter['kqbh'] = $this->_getParam('KQBH');
        try
        {
            $model = new cc_models_kqdl();
            $model->beginTransaction();
            $status = $model->get_status($filter);
            if($status == '1')
            {
                //更改库区登陆信息
                $model->updateKqdlxx($filter);
            }
            //保存库区登陆信息
            $model->saveKq($filter);
            //更改库区信息
            $model->updateKqxx($filter);
            $model->commit();
        }catch (Exception $e)
        {
            $model->rollBack();
            throw $e;
        }
    }
}

?>