<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售开发票(XSKFP)
 * 作成者：苏迅
 * 作成日：2011/06/30
 * 更新履历：
 *********************************/
class xs_xskfpController extends xs_controllers_baseController {
	
	/*
	 * 销售开发票初始页面
	 */
	public function indexAction() {
		$this->_view->assign ( 'title', '销售管理-销售开发票' );
		$this->_view->display ( "xskfp_01.php" );
		
	}
	
	/*
	 * 销售订单信息数据取得xml
	 */
	public function getxsddlistdataAction() {
		//取得列表参数
		$filter ['dwbh'] = $this->_getParam ( "dwbh", '' ); //单位编号		
		$model = new xs_models_xskfp ();
		echo Common_Tool::json_encode($model->getGridData($filter));
		
	}
	
	/*
	 * 销售订单信息数据取得xml
	 */
	public function getmxlistdataAction() {
		//取得列表参数
		$filter ['xshdbh'] = $this->_getParam ( "xshdbh", '' ); 	//开始日期
		$model = new xs_models_xskfp ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridMingxiData ( $filter );
		
	}	
	
	/*
	 * 销售开发票数据保存
	 */

	public function saveAction() {
		$result['status'] = '0'; 
		try {
			$xskfpModel = new xs_models_xskfp ( );
			
			//必须输入项验证
			if(!$xskfpModel->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$xskfpModel->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $xskfpModel->beginTransaction ();
			    //保存客户开发票信息
			    $xskfpModel->saveFpxx();	
			    //保存成功
			    if($result['status'] == '0'){ 	
				    $xskfpModel->commit ();
				    Common_Logger::logToDb("客户销售开发票  客户编号：".$_POST['DWBH']. " 发票编号：".$_POST['FPBH']);
			    }else{
				    $xskfpModel->rollBack ();//有错误发生
			    }
			}
		echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$xskfpModel->rollBack ();
     		throw $e;
		}
	
	}
}