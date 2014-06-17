<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售开票(XSKP)
 * 作成者：周义
 * 作成日：2010/07/05
 * 更新履历：
 *********************************/
class xs_xskpController extends xs_controllers_baseController {
	
	/*
	 * 销售开票初始页面
	 */
	public function indexAction() {
    	$xskpModel = new xs_models_xskp ( );
    	$this->_view->assign ( "title", "销售管理-销售开票" ); //标题
		$this->_view->assign ( "fahuoqu", $xskpModel->getFHQInfo() ); //发货区
		$this->_view->assign ( "kprq", date("Y-m-d"));  //开票日期
		$this->_view->assign ( "kpymch", $_SESSION ["auth"]->userName ); //开票员
		$this->_view->assign ( "bmmch", $_SESSION ["auth"]->bmmch ); //部门
		$this->_view->display ( "xskp_01.php" );
		
	}
	
	
	/*
	 * 销售订单数据保存
	 */

	public function saveAction() {
		$result['status'] = '0';  //处理结果
		try {
			$xskpModel = new xs_models_xskp ( );
			
			//必须输入项验证
			if(!$xskpModel->inputCheck($_POST)){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$xskpModel->logicCheck($_POST)){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $xskpModel->beginTransaction ();
			    //销售单编号取得
			    $xshdbh = Common_Tool::getDanhao('XSD');
			    $result['xshdbh'] = $xshdbh;
			    
                //生成销售订单(销售单，销售单明细)
			    $xskpModel->createXshd($xshdbh,$_POST);
			    //生成结算单
			    $xskpModel->createJsd($xshdbh,$_POST);
			    
			    //订单资格审查（证照有效期，信用，出货量等）
                $zige = $xskpModel->checkQualification($xshdbh,$_POST);
                //订单资格审查未通过，需要审批
                if($zige['status']=="1"){
                	$result['status'] = '3';//资格有问题，进入等待审批状态
                	$result['data'] = $zige["data"];//需要审批的内容列表
                }
			    
			    //商品出库处理(出库单，补货单)
			    $chuku =  $xskpModel->doChuku($xshdbh,$_POST);
			    
			    //库存有问题
			    if($chuku['status']!='0'){
			       $result['status'] = '4'; //库存不足	
			       $result['data'] = $chuku['data']; //库存数据
			    }

			    

			    //正常开票完成或者有部分需要审批
			    if($result['status'] == '0' || $result['status'] == '3'){
				    $xskpModel->commit ();
				    Common_Logger::logToDb("新建销售订单：".$xshdbh);
			    }else{
				    $xskpModel->rollBack ();//有错误发生
			    }
			}
		echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$xskpModel->rollBack ();
     		throw $e;
		}
	
	}
	
	
    /**
     * 通过商品编号取得商品相关信息
     *
     */
	public function getshangpininfoAction(){
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$xskpModel = new xs_models_xskp ( );
		
	    echo json_encode($xskpModel->getShangpinInfo($filter));
	}
	
	 /**
     * 取得单位信息,业务员信息
     *
     */
	public function getdanweiinfoAction(){
    	$dwfilter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
    	$dwfilter ['flg'] = "0";
 		$dwModel = new gt_models_danwei ( );
 		$result['dwinfo'] =  $dwModel->getDanweiInfo($dwfilter); //单位相关信息
 		$ywyModel = new gt_models_ywy();
 		$ywyfilter ['dwbh'] = $this->_getParam('dwbh');
 		$ywyfilter ['flg'] = '1'; //销售 
 		$result['ywyinfo'] =  $ywyModel->getData($ywyfilter);
	    echo Common_Tool::json_encode($result);
	}
	
	/*
	 * 检查账期是否超期
	 */
	public function checkxdqAction(){
		
		$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xskpModel = new xs_models_xskp ( );
		
	    echo $xskpModel->checkXdq($filter);
	}
	
	//价格信息
	public function getjiageinfoAction(){
		$filter ['shpbh'] = $this->_getParam('shpbh');   //商品编号
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$xskpModel = new xs_models_xskp ( );
 		
 		//特价一品多价信息
 		$result = $xskpModel->getJiageInfo($filter);
     	echo json_encode($result);
 	}
 	
 	/**
 	 * 销售挂单处理
 	 */
 	public function exporttempAction(){
 	    $result['status'] = '0'; 
		try {
			$xskpModel = new xs_models_xskp ( );
			//开始一个事务
		    $xskpModel->beginTransaction ();
		    //销售挂账单号取得
		    $xshgzhdbh = Common_Tool::getDanhao('XSG');
		    //销售订单挂账保存
		    $xskpModel->saveTempMain($xshgzhdbh,$_POST);
		    //销售订单明细保存
		    $xskpModel->saveTempMingxi($xshgzhdbh,$_POST);
			$xskpModel->commit ();
			Common_Logger::logMessage("新建销售挂账单：".$xshgzhdbh);
			$result['data'] = $xshgzhdbh; 
		    echo json_encode($result);
		} catch ( Exception $e ) {
			$xskpModel->rollBack ();
     		throw $e;
		} 		
 	}
    /**
     * 挂单导入
     *
     */
 	public function importtempAction(){
 		$this->_view->assign("title","销售开票-销售挂单导入");
 		$this->_view->display("xskp_02.php");
 		
 	}
 	
 	/**
 	 * 销售挂单数据取得
 	 *
 	 */
 	public function getxshgdlistdata(){
// 		$searchParam["begindate"] = $this->_getParam("BEGINDATE");//开始日期
// 		$searchParam["enddate"] = $this->_getParam("ENDDATE");//结束日期
// 		$searchParam["dwbh"] = $this->_getParam("DWBH");//单位编号
// 		$model = new xs_models_xskp();
// 		echo $model->getXshgdListData();
 	}
}