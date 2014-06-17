<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库间调拨出库(kjdbck)
 * 作成者：dltt
 * 作成日：2010/11/10
 * 更新履历：
 *********************************/
class cc_kjdbckController extends cc_controllers_baseController {

	/*
     * 库间调拨出库生成画面显示
     */
	public function indexAction() {
		$this->_view->assign('bmmch', $_SESSION['auth']->bmmch);  //部门名称
        $this->_view->assign('bmbh', $_SESSION['auth']->bmbh);  //部门编号
		$this->_view->assign ( 'action', 'new' );  								
		$this->_view->assign ( 'title', '基础管理-库间调拨出库' );
	    $this->_view->assign('kpdate',date('Y-m-d')); 
		$this->_view->display ( 'kjdbck_01.php' );
				
	}
	
	/*
     * 库间调拨出库生成画面显示
     */
	public function saveAction() {
		
		$result['status'] = '0'; 
		try {
			$Model = new cc_models_kjdbck( );

			//必须输入项验证
			$retFlg = $Model->inputCheck();
			
			if(!$retFlg){
				
				$result['status'] = '1';  //必须输入项验证错误
				
				echo json_encode($result);
				
				return true;
				
			}
			
			//2)逻辑验证
			$rest = $Model->logicCheck();
			
			if ($rest !='0'){
				
				$result['status'] = $rest;
				
			}else{
				//开始一个事务
			    $Model->beginTransaction ();
			    //编号取得
			    $AutoBh = Common_Tool::getDanhao('DBC',$_POST['KPRQ']);
			    //保存
			    $Model->saveMain($AutoBh);
			    //订单明细保存
			    $Model->saveMingxi($AutoBh);
			    //库存相关数据更新
			    $returnValue =  $Model->updateKucun($AutoBh);
			    if($returnValue['status']!='0'){
			       $result['status'] = '3'; //库存不足	
			       $result['data'] = $returnValue['data']; //库存数据
			    }
			    //保存成功
			    if($result['status'] == '0'){
			    	$result['data'] = $AutoBh;
				    $Model->commit ();
				    Common_Logger::logToDb("新建调拨出库单：".$AutoBh);
			    }else{
				    $Model->rollBack ();//有错误发生
			    }
			}
			
			echo Common_Tool::json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$Model->rollBack ();
     		throw $e;
		}
	}
	
	/**
     * 通过商品编号取得商品相关信息
     *
     */
	public function getshangpininfoAction()
	{
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$filter ['dwbh'] = $this->_getParam("dwbh",'00000000'); //单位编号
 		$Model = new cc_models_kjdbck( );
		
	    echo Common_Tool::json_encode($Model->getShangpinInfo($filter));
	}
	
	/**
	 * 判断对象库位状态
	 *
	 */
	public function chkKwStatsAction(){
		$filter ['ckbh'] = $this->_getParam('ckbh');   //仓库编号
		$filter ['kqbh'] = $this->_getParam('kqbh'); //库区编号
		$filter ['kwbh'] = $this->_getParam('kwbh'); //库位编号
		
		$Model = new cc_models_kjdbck( );
		
		echo Common_Tool::json_encode($Model->getKwInfo($filter));
		
	}
	/**
	 * 检索调拨在库商品的数量
	 *
	 */
	public function getHaveCountAction(){
		$filter ['ckbh'] = $this->_getParam('ckbh');   //仓库编号
		$filter ['kqbh'] = $this->_getParam('kqbh'); //库区编号
		$filter ['kwbh'] = $this->_getParam('kwbh'); //库位编号
		
		$Model = new cc_models_kjdbck( );
		
		echo Common_Tool::json_encode($Model->getKwInfo($filter));
	}
	
}