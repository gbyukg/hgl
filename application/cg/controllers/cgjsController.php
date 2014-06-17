<?php
/*********************************
 * 模块：   采购模块(CG)
 * 机能：    采购结算(CGJS)
 * 作成者：苏迅
 * 作成日：2011/06/23
 * 更新履历：
 *********************************/
class cg_cgjsController extends cg_controllers_baseController {
	
	/*
	 * 采购结算初始页面
	 */
	public function indexAction() {
		$this->_view->assign ( 'title', '采购管理-采购结算' );
		$this->_view->display ( "cgjs_01.php" );
		
	}
	
	/*
	 * 采购订单/入库单信息数据取得xml
	 */
	public function getrkdlistdataAction() {
		//取得列表参数
		$filter ['dwbh'] = $this->_getParam ( "dwbh", '' ); //单位编号
		
		$model = new cg_models_cgjs ();
		echo Common_Tool::json_encode($model->getGridData($filter));
		
	}
	
	/*
	 * 采购订单明细信息数据取得xml
	 */
	public function getmxlistdataAction() {
		//取得列表参数
		//$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		//$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['ckdbh'] = $this->_getParam ( "ckdbh", '' ); 	//参考单编号
		$filter ['fkfsh'] = $this->_getParam ( "fkfsh", '' ); 	//付款方式
		$model = new cg_models_cgjs ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridMingxiData ( $filter );
		
	}
	
	
	/*
	 * 采购结算数据保存
	 */

	public function saveAction() {
		$result['status'] = '0'; 
		try {
			$cgjsModel = new cg_models_cgjs ( );
			
			//必须输入项验证
			if(!$cgjsModel->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$cgjsModel->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $cgjsModel->beginTransaction ();
			    //保存客户付款信息
			    $fkbh = Common_Tool::getDanhao('KFK');
			    $result['fkbh'] = $fkbh;
			    $check = $cgjsModel->saveCgjs($fkbh);	
			    if($check['status'] != "0"){
			    	$result['status'] = $check['status'];
			    }
			    //保存成功
			    if($result['status'] == '0'){
			    	$result['data'] = $fkbh;		    	
				    $cgjsModel->commit ();
				    Common_Logger::logToDb("采购结算  客户编号：".$_POST['DWBH']. " 付款编号：".$result['data']);
			    }else{
				    $cgjsModel->rollBack ();//有错误发生
			    }
			}
		echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$cgjsModel->rollBack ();
     		throw $e;
		}
	
	}
}