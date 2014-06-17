<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购预入库审核(CGYRKSHH)
 * 作成者：ZhangZeliang
 * 作成日：2011/06/03
 * 更新履历：
 *********************************/
class cc_yrkcgshhController extends cc_controllers_baseController
{
/*
     * 采购预入库审核页面
     */
    public function indexAction()
    {
        $this->_view->assign ("title", "仓储管理-预采购入库采购审核");    //标题
        $this->_view->display ("yrkcgshh_01.php");
    }
    
    /*
     * 采购预入库审核02页面
     */
    public function yrgshhAction()
    {
    	$yrkdbh = $this->_getParam('yrkdbh');
    	$model = new cc_models_yrkcgshh();
    	$this->_view->assign('title', '仓储管理-预入库采购审核');
    	$this->_view->assign ('yrkdbh', $this->_getParam('yrkdbh'));    //标题
    	$this->_view->assign('rec', $model->getYrkdInfo($yrkdbh));
        $this->_view->display ("yrkcgshh_02.php");
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
        $_SESSION["yrkcgshh_sortParams"]["orderby"] = $filter ['orderby'];
        $_SESSION["yrkcgshh_sortParams"]["direction"] = $filter ['direction'];
        
        //一般查询
        if($this->_getParam ( "isfilter", '0' )=='0'){
           //取得一般查询条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['yrkcgshh_searchParams'] = $_POST;
                unset($_SESSION['yrkcgshh_filterParams']); //清空精确查询条件
            }
            
        }else{//精确查询
            //取得过滤条件参数并保存至session
            if($this->_request->isPost()){
                $_SESSION['yrkcgshh_filterParams'] = $_POST;
                unset($_SESSION['yrkcgshh_searchParams']); //清空一般查询条件
            }
        }

        //取得检索条件
        @$filter['filterParams'] = $_SESSION['yrkcgshh_filterParams'];  //精确查询条件
        @$filter['searchParams'] = $_SESSION['yrkcgshh_searchParams'];  //固定查询条件
        
        $model = new cc_models_yrkcgshh();
        header ( "Content-type:text/xml" ); //返回数据格式xml
        echo $model->getGridData($filter);
    }
    
    /*
     * 获取采购单信息明细信息(01页面)
     */
    public function getmxdataAction()
    {
    	$yrkdbh = $this->_getParam('yrkdbh');
    	$model = new cc_models_yrkcgshh();
    	header ( "Content-type:text/xml" ); //返回数据格式xml
    	echo $model->getmxdata($yrkdbh);
    }
    
    /*
     * 获取单位信息
     */
    public function getdanweiinfoAction()
    {
    	$dwbh = $this->_getParam('dwbh');   //单位编号
        $model = new cc_models_yrkcgshh();
        $result['dwinfo'] =  $model->getDanweiInfo($dwbh); //单位相关信息
        echo Common_Tool::json_encode($result);
    }
    
    /*
     * 获取比较数据grid_match
     */
    public function getmatchAction()
    {
    	$model = new cc_models_yrkcgshh();
        header ( "Content-type:text/xml" ); //返回数据格式xml
        echo $model->getMatch($this->_getParam('yrkdbh'));
    }
    
    /*
     * 获取商品报警信息grid_alarm
     */
    public function getalarmAction()
    {
    	$filter['shpbh'] = $this->_getParam('shpbh');
    	$filter['yrkdbh'] = $this->_getParam('yrkdbh');
    	$model = new cc_models_yrkcgshh();
        header ( "Content-type:text/xml" ); //返回数据格式xml
        echo $model->getAlarm($filter);
    }
    
    /*
     * 仓储管理-预入库采购审核保存
     */
    public function saveAction()
    {
    	
    	//获取预入库单编号
        $filter['cgdbh'] = $_POST['CGDBH'];
        $filter['yrkdbh'] = $_POST['YRKDBH'];
        $filter['clff'] = $_POST['namez'];
        $result['status'] = 0;
        $result['new_cgdbh'] = $filter['new_cgdbh'];
        try {
        	$model = new cc_models_yrkcgshh();
        	$model->beginTransaction();
        	
	    	if($filter['clff'] == 0)
	    	{
	    		$filter['new_cgdbh'] = Common_Tool::getDanhao('CGD', $_POST['KPRQ']);
	    		//重做采购订单
	            $model->newCgdd($filter);
	            $model->save($filter, '0');
	    	}else if($filter['clff'] == 1)
	    	{
	    		//重发清单
	    		$model->save($filter, '1');
	    	}else if($filter['clff'] == 2)
	    	{
	    		//整单退回
	    		$model->save($filter, '2');
	    	}
	    	$model->commit();
	    	$result['status'] = 1;
	    	echo json_encode($result);
        }catch(Exception $e)
        {
        	$model->rollBack();
        	throw $e;
        }
    }
    
    /*
     * 预入库单采购审核新订单预览
     */
    public function xddylAction()
    {
    	$flg = $this->_getParam('flg');
    	$cgdbh = $this->_getParam('cgdbh');
        $model = new cc_models_yrkcgshh();
        $this->_view->assign('title', '仓储管理-新采购订单单据信息');
        $this->_view->assign('flg', $flg);
        if($flg == 'new')
        {
        	$yrkdbh = $this->_getParam('yrkdbh');
            //$this->_view->assign ('djbh', Common_Tool::getDanhao('CGD', $_POST['KPRQ']));
            $this->_view->assign ('yrkdbh', $yrkdbh);
        }else {
            $this->_view->assign ('djbh', $cgdbh);
        }
            $this->_view->assign('rec', $model->getCgdxx($cgdbh));
        $this->_view->display ("yrkcgshh_04.php");
    }
    /*
     * 新订单预览明细信息
     */
    public function getnewmxdataAction()
    {
    	$yrkdbh = $this->_getParam('yrkdbh');
    	$model = new cc_models_yrkcgshh();
    	header ( "Content-type:text/xml" ); //返回数据格式xml
    	echo $model->getNewMxdata($yrkdbh);
    }
    
    /*
     * 原订单预览明细信息
     */
    public function getoldmxdataAction()
    {
    	$cgdbh = $this->_getParam('cgdbh');
    	$model = new cc_models_yrkcgshh();
    	header ( "Content-type:text/xml" ); //返回数据格式xml
    	echo $model->getOldMxdata($cgdbh);
    }
}