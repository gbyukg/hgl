<?php
/*********************************
 * 模块：    销售模块(xs)
 * 机能：    销售一步完成(xsybwc)
 * 作成者：魏峰
 * 作成日：2011/01/21
 * 更新履历：

 *********************************/
class xs_xsybwcController extends xs_controllers_baseController {
	
	/*
	 *  销售一步完成页面
	 */
	public function indexAction() {
		$xsybwcModel = new xs_models_xsybwc( );
		$this->_view->assign ( "kprq", date("Y-m-d"));  			//开票日期
		$this->_view->assign ( "fahuoqu", $xsybwcModel->getFHQInfo() ); //发货区
		$this->_view->assign ( "title", "销售管理-销售一步完成" ); 	//标题
		$this->_view->display ( "xsybwc_01.php" );
	}	
	
	/*
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xsybwcModel = new xs_models_xsybwc( );
		
	    echo Common_Tool::json_encode($xsybwcModel->getDanweiInfo($filter));
	}	
	
	/*
	 * 检查账期是否超期
	 */
	public function checkxdqAction(){
		
		$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xsybwcModel = new xs_models_xsybwc( );
		
	    echo $xsybwcModel->checkXdq($filter);
	}	
	
    /*
     * 通过商品编号取得商品相关信息
     *
     */
	public function getshangpininfoAction()
	{
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$xsybwcModel = new xs_models_xsybwc( );
		
	    echo json_encode($xsybwcModel->getShangpinInfo($filter));
	}
	
	//价格信息
	public function getjiageinfoAction(){
		$filter ['shpbh'] = $this->_getParam('shpbh');   //商品编号
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
  		$xsybwcModel = new xs_models_xsybwc( );
 		
 		//特价一品多价信息
 		$result = $xsybwcModel->getJiageInfo($filter);
     	echo json_encode($result);
 	}	
	
	/*
	 * 销售订单数据保存
	 */
	public function saveAction() {
		$result['status'] = '0'; 
		try {
  			$xsybwcModel = new xs_models_xsybwc( );
			
			//必须输入项验证
			if(!$xsybwcModel->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$xsybwcModel->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $xsybwcModel->beginTransaction ();
			    //销售单编号取得
			    $xshdbh = Common_Tool::getDanhao('XSD',$_POST['KPRQ']);
			    //出库单编号取得
			    $chkdbh = Common_Tool::getDanhao('CKD',$_POST['KPRQ']);			    
			    //销售订单保存
			    $xsybwcModel->saveXshdMain($xshdbh);
			    //销售订单明细保存
			    $xsybwcModel->saveXshdMingxi($xshdbh);
			    //出库单信息保存
			    $xsybwcModel->saveChkdMain($chkdbh,$xshdbh);			    
			    //出库单明细信息保存
			    $xsybwcModel->saveChkdMingxi($chkdbh);				       			    
			    //库存相关数据更新
			    $returnValue =  $xsybwcModel->updateKucun($xshdbh);
			    if($returnValue['status']!='0'){
			       $result['status'] = '3'; //库存不足	
			       $result['data'] = $returnValue['data']; //库存数据
			    }
			    //保存成功
			    if($result['status'] == '0'){
			    	$result['xshdbh'] = $xshdbh;
			    	$result['chkdbh'] = $chkdbh;
				    $xsybwcModel->commit ();
				    Common_Logger::logToDb("新建销售订单：".$result['xshdbh']);
				    Common_Logger::logToDb("新建出库单：".$result['chkdbh']);
			    }else{
				    $xsybwcModel->rollBack ();//有错误发生
			    }
			}
		echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$xsybwcModel->rollBack ();
     		throw $e;
		}
	
	}	
	
 	/**
 	 * 销售挂单处理
 	 */
 	public function exporttempAction(){
 	    $result['status'] = '0'; 
		try {
			$xsybwcModel = new xs_models_xsybwc( );
			//开始一个事务
		    $xsybwcModel->beginTransaction ();
		    //销售挂账单号取得
		    $xshgzhdbh = Common_Tool::getDanhao('XSG',$_POST['KPRQ']);
		    //销售订单挂账保存
		    $xsybwcModel->saveTempMain($xshgzhdbh);
		    //销售订单明细保存
		    $xsybwcModel->saveTempMingxi($xshgzhdbh);
			$xsybwcModel->commit ();
			Common_Logger::logToDb("新建销售挂账单：".$xshgzhdbh);
			$result['data'] = $xshgzhdbh; 
		    echo json_encode($result);
		} catch ( Exception $e ) {
			$xsybwcModel->rollBack ();
     		throw $e;
		} 		
 	} 	
}	