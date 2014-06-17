<?php
/*********************************
 * 模块：   基础模块(jc)
 * 机能：    商品与客户关系维护(SPYKHGXWH)
 * 作成者：姚磊
 * 作成日：2011/1/6
 * 更新履历：
 *********************************/
class jc_spykhgxwhController extends jc_controllers_baseController {

	
	
	/*
	 * 商品与客户关系维护初始页面
	 */
	public function indexAction() { 	
		
		$this->_view->assign ( "title", "基础管理-商品与客户关系维护" ); //标题
		$this->_view->display ( "spykhgxwh_01.php" );
	}
	
	/*
	 * 商品与客户关系维护列表
	 */
 	public function getshangpininfoAction(){
 		
 		$shpbh = $this->_getParam ( "shpbh" );
 		$model = new jc_models_spykhgxwh();
 		$result = $model->getShpbhList ( $shpbh );
 		$rec = $result['XDBZH'];
 		$this->_view->assign ( "XDBZH", $rec );
 	   echo Common_Tool::json_encode($result);
 	}
 	
 	/*
	 * 商品与客户关系维护grid
	 */
 	public  function getshpgridAction(){
 		$shpbh = $this->_getParam ( "shpbh" );
 		$model = new jc_models_spykhgxwh();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getshpinGridData( $shpbh );
 	}
 	
 	/*
	 * 客户 与商品关系列表
	 */
 	public function  getdanweiinfoAction(){
 		
 		$dwbh = $this->_getParam ( "dwbh" );
 		$model = new jc_models_spykhgxwh();
 		 echo Common_Tool::json_encode($model->getDanweibhList ( $dwbh ));
 	}
 	/*
	 * 客户 与商品关系grid
	 */
 	public function  getdwbhgridAction(){
 		$dwbh = $this->_getParam ( "dwbh" );
 		$model = new jc_models_spykhgxwh();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getdanweiGridData( $dwbh );
 	}
 	
 	/*
 	 * 商品与客户 保存
 	 */
 	
 	public function saveAction(){
 				
 				$result = array (); //定义返回值
 				
 			try{
 				$spgywhModel = new jc_models_spykhgxwh();
 				$shpbh =$_POST['SHPBH'];
 				$result['shpbh'] = $shpbh;
		    	$spgywhModel->beginTransaction ();//开启一个事物
		    	//商品与客户删除客商信息					
			    $spgywhModel->delectShpbh($shpbh);			    		  
			    //商品与客户明细保存
			    $spgywhModel->saveShpbhMingxi($shpbh);
			    $spgywhModel->commit ();
			    $result ['status'] = 0; //登录成功
				Common_Logger::logToDb ("【商品与客户关系维护  为商品指客户  商品编号 ：".$shpbh."】");
		}catch( Exception $e){
		//回滚
			$spgywhModel->rollBack ();
     		throw $e;
		}
	echo Common_Tool::json_encode($result);
 	}
 	
 	/*
 	 * 客户与商品 保存
 	 * 
 	 */
 	public function savegridAction(){
 		$result = array (); //定义返回值
 			
 			try{
 				$spgywhModel = new jc_models_spykhgxwh();
 				$dwbh =$_POST['DWBH'];
 				$result['dwbh'] = $dwbh;
		    	$spgywhModel->beginTransaction ();				//开启一个事物
			    $spgywhModel->delectDwbh($dwbh);			  
			    //商品与供应商明细保存
			    $spgywhModel->saveDwbhMingxi($dwbh);
			    $spgywhModel->commit ();
			    $result ['status'] = 1; //登录成功
				Common_Logger::logToDb ("【商品与客户关系维护  为客户指定商品  单位编号 ：".$dwbh."】");
		}catch( Exception $e){
		//回滚
			$spgywhModel->rollBack ();
     		throw $e;
		}
 		echo Common_Tool::json_encode($result);
 	}
 	
 	/*
 	 * 获取商品名称
 	 */
 	public function getshpmcmAction(){ 		
 		$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$model = new jc_models_spygysgxwh();
	    echo json_encode($model->getshpmcm($filter));
 	} 
 	
 	/*
 	 * 获取单位名称
 	 */
 	public function getdwmcmAction(){
 			$filter ['dwbh'] = $this->_getParam('dwbh');   //检索项目值
		$model = new jc_models_spygysgxwh();
	    echo json_encode($model->getdwmcm($filter));
 	}
 	
}