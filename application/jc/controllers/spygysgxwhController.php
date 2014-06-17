<?php
/*********************************
 * 模块：   基础模块(jc)
 * 机能：    商品与供应商关系维护(SPYGYSGXWH)
 * 作成者：姚磊
 * 作成日：2011/1/1
 * 更新履历：
 *********************************/
class jc_spygysgxwhController extends jc_controllers_baseController {

	
	
	/*
	 * 商品与供应商关系维护初始页面
	 */
	public function indexAction() { 	
		
		$this->_view->assign ( "title", "基础管理-商品与供应商关系维护" ); //标题
		$this->_view->display ( "spygysgxwh_01.php" );
	}
	
	/*
	 * 商品与供应商关系维护列表
	 */
 	public function getshangpininfoAction(){
 		
 		$shpbh = $this->_getParam ( "shpbh" );
 		$model = new jc_models_spygysgxwh();
 		$result = $model->getShpbhList ( $shpbh );
 	   echo Common_Tool::json_encode($result);
 	}
 	
 	/*
	 * 商品与供应商关系维护grid
	 */
 	public  function getshpgridAction(){
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式	
 		
 		$shpbh = $this->_getParam ( "shpbh" );
 		$model = new jc_models_spygysgxwh();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getshpinGridData( $filter,$shpbh );
 	}
 	
 	/*
	 * 供应商 与商品关系列表
	 */
 	public function  getdanweiinfoAction(){
 		
 		$dwbh = $this->_getParam ( "dwbh" );
 		$model = new jc_models_spygysgxwh();
 		 echo Common_Tool::json_encode($model->getDanweibhList ( $dwbh ));
 	}
 	/*
	 * 供应商 与商品关系grid
	 */
 	public function  getdwbhgridAction(){
 		$dwbh = $this->_getParam ( "dwbh" );
 		$model = new jc_models_spygysgxwh();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getdanweiGridData( $dwbh );
 	}
 	
 	/*
 	 * 商品与供应商 保存
 	 */
 	
 	public function saveAction(){
 				
 				$result = array (); //定义返回值
 				
 			try{
 				$spgywhModel = new jc_models_spygysgxwh();
 				$shpbh =$_POST['SHPBH'];
 				$result['shpbh'] = $shpbh;
		    	$spgywhModel->beginTransaction ();				//开启一个事物
			    $spgywhModel->delectShpbh($shpbh);
			    //商品与供应商删除客商信息			  
			    //商品与供应商明细保存
			    $spgywhModel->saveShpbhMingxi($shpbh);
			    $spgywhModel->commit ();
			    $result ['status'] = 0; //登录成功
				Common_Logger::logToDb ("【商品与供应商关系维护  为商品指定供商  商品编号 ：".$shpbh."】");
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
 				$spgywhModel = new jc_models_spygysgxwh();
 				$dwbh =$_POST['DWBH'];
 				$result['dwbh'] = $dwbh;
		    	$spgywhModel->beginTransaction ();				//开启一个事物
			    $spgywhModel->delectDwbh($dwbh);			  
			    //商品与供应商明细保存
			    $spgywhModel->saveDwbhMingxi($dwbh);
			    $spgywhModel->commit ();
			    $result ['status'] = 1; //登录成功
				Common_Logger::logToDb ("【商品与供应商关系维护  为供应商指定商品  单位编号 ：".$dwbh."】");
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