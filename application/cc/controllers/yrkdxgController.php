<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    预入库单修改(YRKDXG)
 * 作成者：ZhangZeliang
 * 作成日：2011/06/10
 * 更新履历：
 *********************************/

class cc_yrkdxgController extends cc_controllers_baseController
{
	/*
	 * 初始页面
	 */
	public function indexAction()
	{
		$this->_view->assign('KSRQKEY', date('Y-m-d',(time() - 14*24*60*60)));
		$this->_view->assign('ZZRQKEY', date('Y-m-d'));
		$this->_view->assign('title', '仓储管理-预入库单查询');
        $this->_view->display('yrkdxg_01.php');
	}
	
	/*
	 * 修改页面
	 */
	public function updatepageAction()
	{
		$yrkdbh = $this->_getParam('yrkdbh');
		$model = new cc_models_yrkdxg();
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
        $this->_view->assign('bmbh', $_SESSION['auth']->bmbh);  //部门名称
        $this->_view->assign('rec', $model->getYrkdxx($yrkdbh));
		$this->_view->assign('title', '仓储管理-预入库单修改');
        $this->_view->display('yrkdxg_02.php');
	}
	
    /*
     * 获取采购单信息(01页面)
     */
    public function getlistdataAction()
    {
        //取得分页排序参数
        $filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
        $filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
        $filter ['orderby'] = $this->_getParam ( "orderby","1"); //排序列
        $filter ['direction'] = $this->_getParam ( "direction","DESC"); //排序方式
        //保持排序条件
        $_SESSION["yrkdxg_sortParams"]["orderby"] = $filter ['orderby'];
        $_SESSION["yrkdxg_sortParams"]["direction"] = $filter ['direction'];
        
        //一般查询
        if($this->_getParam ( "isfilter", '0' )=='0'){
           //取得一般查询条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['yrkdxg_searchParams'] = $_POST;
                unset($_SESSION['yrkdxg_filterParams']); //清空精确查询条件
            }
            
        }else{//精确查询
            //取得过滤条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['yrkdxg_filterParams'] = $_POST;
                unset($_SESSION['yrkdxg_searchParams']); //清空一般查询条件
            }
        }

        //取得检索条件
        @$filter['filterParams'] = $_SESSION['yrkdxg_filterParams'];  //精确查询条件
        @$filter['searchParams'] = $_SESSION['yrkdxg_searchParams'];  //固定查询条件
        
        $model = new cc_models_yrkdxg();
        header ( "Content-type:text/xml" ); //返回数据格式xml
        echo $model->getGridData($filter);
    }
    
    /*
     * 获取采购单信息明细信息(01页面)
     */
    public function getmxdataAction()
    {
        $yrkdbh = $this->_getParam('yrkdbh');
        $model = new cc_models_yrkdxg();
        header ( "Content-type:text/xml" ); //返回数据格式xml
        echo $model->getmxdata($yrkdbh);
    }
    
    /*
     * 获取采购单明细信息(cgyrk_01页面使用)
     */
    public function getmingxiAction()
    {
        $cgdbh = $this->_getParam('cgdbh');
        $model = new cc_models_yrkdxg();
        echo Common_Tool::json_encode($model->getmingxi($cgdbh));
    }
    
/*
     * 保存
     */
    public function saveAction()
    {
        $result['status'] = '0';
        try
        {
            $model = new cc_models_yrkdxg();
            $message = $model->turnMessage($_POST['msg']);
            if(!$model->inputCheck())
            {
                $result['status'] = '1';    //必须输入项目验证
            }else 
            {
                //开始一个事物
                $model->beginTransaction();
                
                //保存明细信息
                $model->updateMingxi();
                
                //保存message信息
                if($_POST['rkzt'] == '1')
                {
                    if(count($message) > 0)
                    {
                        $model->saveMessage($message);
                    }else
                    {
                        $result['status'] = '2';    //状态与message不符
                    }
                }
                
               //保存成功
               if($result['status'] == '0')
               {
                   $model->commit();
                   Common_Logger::logToDb("采购预入库 预入库单编号：".$_POST['YRKDBH']);
               }else {
                   $model->rollBack();
               }
           }
           echo json_encode($result);
        }catch (Exception $e)
        {
            $model->rollBack();
            throw $e;
        }
    }
    
/*
     * 获取单位信息
     */
    public function getdanweiinfoAction()
    {
        $dwbh = $this->_getParam('dwbh');   //单位编号
        $model = new cc_models_yrkdxg();
        $result['dwinfo'] =  $model->getDanweiInfo($dwbh); //单位相关信息
        echo Common_Tool::json_encode($result);
    }
}