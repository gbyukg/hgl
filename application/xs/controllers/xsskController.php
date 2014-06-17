<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售收款(XSSK)
 * 作成者：苏迅
 * 作成日：2011/03/03
 * 更新履历：
 *********************************/
class xs_xsskController extends xs_controllers_baseController {
	
	/*
	 * 销售收款初始页面
	 */
	public function indexAction() {
		$this->_view->assign ( 'title', '销售管理-销售收款' );
		$this->_view->display ( "xssk_01.php" );
		
	}
	
	/*
	 * 销售订单信息数据取得xml
	 */
	public function getxsddlistdataAction() {
		//取得列表参数
		$filter ['dwbh'] = $this->_getParam ( "dwbh", '' ); //单位编号		
		$model = new xs_models_xssk ();
		echo Common_Tool::json_encode($model->getGridData($filter));
		
	}
	
	/*
	 * 销售订单信息数据取得xml
	 */
	public function getmxlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['xshdbh'] = $this->_getParam ( "xshdbh", '' ); 	//开始日期
		//$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		//$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new xs_models_xssk ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridMingxiData ( $filter );
		
	}
	
	/*
	 * 取得客户预付款
	 */
	public function getyfkAction() {
		$filter ['dwbh'] = $this->_getParam ( "dwbh", '' ); //单位编号		
		$model = new xs_models_xssk ();
		echo Common_Tool::json_encode($model->getYfk($filter));
		
	}
	
	
	/*
	 * 销售收款数据保存
	 */

	public function saveAction() {
		$result['status'] = '0'; 
		try {
			$xsskModel = new xs_models_xssk ( );
			
			//必须输入项验证
			if(!$xsskModel->inputCheck()){
				$result['status'] = '1';  //必须输入项验证错误
			}elseif(!$xsskModel->logicCheck()){
				$result['status'] = '2';  //项目合法性验证错误
			}else{
				//开始一个事务
			    $xsskModel->beginTransaction ();
			    //保存客户付款信息
			    $khfkbh = Common_Tool::getDanhao('KFK');
			    $result['khfkbh'] = $khfkbh;
			    $check = $xsskModel->saveFkxx($khfkbh);	
			    if($check['status'] != "0"){
			    	$result['status'] = $check['status'];
			    }
			    //保存成功
			    if($result['status'] == '0'){
			    	$result['data'] = $khfkbh;		    	
				    $xsskModel->commit ();
				    Common_Logger::logToDb("客户付款  客户编号：".$_POST['DWBH']. " 付款编号：".$result['data']);
			    }else{
				    $xsskModel->rollBack ();//有错误发生
			    }
			}
		echo json_encode($result);

		} catch ( Exception $e ) {
			//回滚
			$xsskModel->rollBack ();
     		throw $e;
		}
	
	}
}