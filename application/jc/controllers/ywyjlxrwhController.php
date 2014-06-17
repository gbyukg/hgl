<?php
/*********************************
 * 模块：   基础模块(jc)
 * 机能：  客户业务员及联系人维护(YWYJLXRWH)
 * 作成者：姚磊
 * 作成日：2011/1/10
 * 更新履历：
 *********************************/
class jc_ywyjlxrwhController extends jc_controllers_baseController {

	
	
	/*
	 * 商品与供应商关系维护初始页面
	 */
	public function indexAction() { 	
		
		$this->_view->assign ( "title", "基础管理-客户业务员及联系人维护" ); //标题
		$this->_view->display ( "ywyjlxrwh_01.php" );
	}
	
	/*
	 * 商品与供应商关系维护列表
	 */
 	public function getshangpininfoAction(){
 		
 		$shpbh = $this->_getParam ( "shpbh" );
 		$model = new jc_models_ywyjlxrwh();
 		$result = $model->getShpbhList ( $shpbh );
 	   echo Common_Tool::json_encode($result);
 	}
 	
 	/*
	 * 商品与供应商关系维护grid
	 */
 	public  function getshpgridAction(){
 		$dwbh = $this->_getParam ( "dwbh" );
 		$model = new jc_models_ywyjlxrwh();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getshpinGridData( $dwbh );
 	}
 	
 	/*
	 * 供应商 与商品关系列表
	 */
 	public function  getdanweiinfoAction(){
 		
 		$dwbh = $this->_getParam ( "dwbh" );
 		$model = new jc_models_ywyjlxrwh();
 		 echo Common_Tool::json_encode($model->getDanweibhList ( $dwbh ));
 	}
 	/*
	 * 供应商 与商品关系grid
	 */
 	public function  getdwbhgridAction(){
 		$dwbh = $this->_getParam ( "dwbh" );
 		$model = new jc_models_ywyjlxrwh();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getdanweiGridData( $dwbh );
 	}
 	
 	/*
 	 * 商品与供应商 保存
 	 */
 	
 	public function saveAction(){
 				
 				$result = array (); //定义返回值
 				
 			try{
 				$spgywhModel = new jc_models_ywyjlxrwh();
 				$dwbh =$_POST['DWBH'];
 				$result['dwbh'] = $dwbh;
		    	$spgywhModel->beginTransaction ();				//开启一个事物
			    $spgywhModel->delectShpbh($dwbh);
			    //商品与供应商删除客商信息			  
			    //商品与供应商明细保存
			    $spgywhModel->saveShpbhMingxi($dwbh);
			    $spgywhModel->commit ();
			    $result ['status'] = 0; //登录成功
				Common_Logger::logToDb ("【 客户业务员及联系人维护  为客户指定联系人  客户编号 ：".$dwbh."】");
		}catch( Exception $e){
		//回滚
			$spgywhModel->rollBack ();
     		throw $e;
		}
	echo Common_Tool::json_encode($result);
 	}
 	
 	/*
 	 * 供应商与商品 保存
 	 * 
 	 */
 	public function savegridAction(){
 		$result = array (); //定义返回值
 			
 			try{
 				$spgywhModel = new jc_models_ywyjlxrwh();
 				$dwbh =$_POST['DWBH_1'];
 				$result['dwbh'] = $dwbh;
		    	$spgywhModel->beginTransaction ();				//开启一个事物
			    $spgywhModel->delectDwbh($dwbh);			  
			    //商品与供应商明细保存
			    $spgywhModel->saveDwbhMingxi($dwbh);
			    $spgywhModel->commit ();
			    $result ['status'] = 1; //登录成功
				Common_Logger::logToDb ("【客户业务员及联系人维护  指定客户方联系人  客户编号：".$dwbh."】");
		}catch( Exception $e){
		//回滚
			$spgywhModel->rollBack ();
     		throw $e;
		}
 		echo Common_Tool::json_encode($result);
 	}
 	
 	/*
 	 * 取得员工信息
 	 */
 	
	public function getyuangonginfoAction()
	{
    	$filter ['ygbh'] = $this->_getParam('ygbh');   //检索项目值
		$model = new jc_models_ywyjlxrwh();
	    echo json_encode($model->getYuangongInfo($filter));
	}
 	
}