<?php
/*********************************
 * 模块：   基础模块(jc)
 * 机能：   赠品策略(ZPCL)
 * 作成者：姚磊
 * 作成日：2011/7/27
 * 更新履历：
 *********************************/
class jc_zpclController extends jc_controllers_baseController {

	
	
	/*
	 * 赠品维护页面
	 */
	public function indexAction() { 	
		
		$this->_view->assign ( "title", "基础管理 -赠品策略" ); //标题
		$this->_view->display ( "zpcl_01.php" );
	}
	
	/*
     * 赠品设定画面显示
     * 
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );			
		$this->_view->assign ( 'title', '基础管理 -赠品策略设定' );			
		$this->_view->display ( 'zpcl_02.php' );
	}
	
	
	/*
	 * 赠品策略获取信息grid
	 */
 	public function getdataAction(){

		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['zpcl_searchParams'] = $_POST;
				unset($_SESSION['zpcl_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['zpcl_filterParams'] = $_POST;
				unset($_SESSION['zpcl_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['zpcl_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['zpcl_searchParams'];  //固定查询条件		
 		$model = new jc_models_zpcl();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
 	   echo $model->getdataList ($filter);
 	}
 	
 	/*
 	 * 上一条下一条 获取grid 组合策略
 	 */
 	public function getzhgridAction(){
 		$zpclbh = $this->_getParam('flg');                               //获取赠品组合策略编号
 		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['zpcl_searchParams'] = $_POST;
				unset($_SESSION['zpcl_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['zpcl_filterParams'] = $_POST;
				unset($_SESSION['zpcl_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['zpcl_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['zpcl_searchParams'];  //固定查询条件		
 		$model = new jc_models_zpcl();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
 	   echo $model->getdatagrid ($filter,$zpclbh);
 		
 		
 		
 		
 	}
 	
 	
 	/*
	 * 
	 */
 	public  function getshpgridAction(){
 		$dwbh = $this->_getParam ( "dwbh" );
 		$model = new jc_models_zpcl();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getshpinGridData( $dwbh );
 	}
 	
 	/*
 	 * 赠品策略详情
 	 */
 	
	public function detailAction() {
		//赠品信息取得
		$model = new jc_models_zpcl ( );
		
		$zpclbh = $this->_getParam('zpclbh');
		$filter['filterParams'] = $_SESSION['zpcl_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['zpcl_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序	
		
		$retuzl = $model->getzhuangtai($zpclbh);//获取赠品策略状态 判断单品 组合
		if($retuzl == '1'){
		$this->_view->assign ( 'action', 'danpin' ); //设置页面为单品策略		
		$danpin = $model->getDate ( $zpclbh, $filter );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "danpin", $danpin );
		$this->_view->assign ( 'title', '赠品策略单品信息详情' );	
		$this->_view->display ( 'zpcl_03.php' );
			
		}else {
			
		$this->_view->assign ( 'action', 'zhuhe' );
		$danpin = $model->getDate ( $zpclbh, $filter );
		$this->_view->assign ( "danpin", $danpin );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( 'title', '赠品策略组合信息详情' );	
		$this->_view->display ( 'zpcl_03.php' );	

		
		}
	
	}
 	/*
 	 * 获取赠品策略状态
 	 */
	
	public function checkzlAction(){
		$result = array (); //定义返回值
		$model = new jc_models_zpcl ( );
		$zpclbh = $this->_getParam('flg');
		$retuzl = $model->getzhuangtai($zpclbh);//获取赠品策略状态 判断单品 组合
		$result ['status'] = $retuzl; 
		echo Common_Tool::json_encode($result); //返回状态结果集
	}
	
 	/*
 	 * 上一条 下一条
 	 */
 	public function getupdownAction(){
 		$model = new jc_models_zpcl ( );
		$zpclbh = $this->_getParam('zpclbh');
 		$filter['filterParams'] = $_SESSION['zpcl_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['zpcl_filterParams'];  //固定查询条件				
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		$flg = $this->_getParam ( 'flg', 'current' ); //检索方向	
		
		$retuzl = $model->getzhuangtai($zpclbh);//获取赠品策略状态 判断单品 组合
		if($retuzl == '1'){   //如果赠品策略状态为1 即为单品策略
		$danpin = $model->getDate ( $zpclbh, $filter, $flg );
		
		//没有找到记录
		if ($danpin == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "danpin", $danpin );
			if($danpin['CLZHL'] == '1'){
				$this->_view->assign ( 'action', 'danpin' );  //如果点击下一条为单品策略
			echo json_encode ( $this->_view->fetchPage ( "zpcl_03.php" ) );	
			}else{      //如果点击下一条为组合策略
				$this->_view->assign ( 'action', 'zhuhe' );
				echo json_encode ( $this->_view->fetchPage ( "zpcl_03.php" ) );
			}			
		}		
		}else{				
			$danpin = $model->getDate ( $zpclbh, $filter, $flg );
					//没有找到记录
		if ($danpin == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( 'action', 'zhuhe' ); 		
			$this->_view->assign ( "danpin", $danpin );
			echo json_encode ( $this->_view->fetchPage ( "zpcl_03.php" ) );	
			
		}
		}	
		
 	}
 	
 	/*
 	 * 赠品 - 单品策略 保存
 	 */
 	
 	public function saveAction(){
 				
 				$result = array (); //定义返回值
 				
 			try{
 				$Model = new jc_models_zpcl();
 				$zpclbh = Common_Tool::getDanhao('ZPC',date("Y-m-d")); //赠品策略编号
		    	$Model->beginTransaction ();				//开启一个事物
		  
			    //商品与供应商明细保存
			    $Model->saveDanpincl($zpclbh);
			    $Model->saveDanpin($zpclbh);
			    $Model->commit ();
			    $result ['status'] = 0; //保存成功
			    $result ['zpclbh'] = $zpclbh; //赠品策略编号
				Common_Logger::logToDb ("【新增赠品单品策略 赠品编号 ：".$zpclbh."】");
		}catch( Exception $e){
		//回滚
			$Model->rollBack ();
     		throw $e;
		}
	echo Common_Tool::json_encode($result);
 	}
 	
 	/*
 	 * 赠品组合保存 grid
 	 * 
 	 */
 	public function savegridAction(){
 		$result = array (); //定义返回值
			
 			try{
 				$Model = new jc_models_zpcl();
		    	$Model->beginTransaction ();				//开启一个事物
		    	$zpclbh = Common_Tool::getDanhao('ZPC',date("Y-m-d")); //赠品策略编号
		    	$Model->saveForm($zpclbh);
			 	$Model->saveGrid($zpclbh);			  
			    $Model->commit ();
			    $result ['status'] = 1; //登录成功
			    $result ['zpclbh'] = $zpclbh; //赠品策略编号
				Common_Logger::logToDb ("【新增赠品组合策略 赠品编号 ：".$zpclbh."】");
		}catch( Exception $e){
		//回滚
			$Model->rollBack ();
     		throw $e;
		}
 		echo Common_Tool::json_encode($result);
 	}
 	
 	/*
 	 * 赠品组合修改 保存
 	 */
	public function savezuheAction(){
				
		$result = array (); //定义返回值
		try{
		$model = new jc_models_zpcl ( );	
		$model->beginTransaction ();				//开启一个事物			
		$zpclbh = $_POST['ZPCLBH'];;	
		$model->updatezpclxx ($zpclbh); //更新赠品策略单品信息
		$model->del($zpclbh); // 删除赠品策略单品商品信息
		$model->saveGrid($zpclbh); //保存grid数据
		$model->commit ();
		$result ['status'] = 0; //修改成功
		$result ['zpclbh'] = $zpclbh; //赠品编号
		Common_Logger::logToDb ("【修改赠品组合策略 赠品编号 ：".$zpclbh."】");
		}catch( Exception $e){
		//回滚
			$model->rollBack ();
     		throw $e;
		}
 		echo Common_Tool::json_encode($result);
		
		
	}
 	
	/*
	 * 获取商品信息
	 */
	public function getshpbhAction(){
		
		$filter ['shpbh'] = $this->_getParam('shpbh');   //获取商品编号
		$model = new jc_models_zpcl();
	    echo json_encode($model->getShpInfo($filter));
		
	}
	
	/*
	 * 更新赠品策略
	 */
	
	public function updateAction(){
		
		$model = new jc_models_zpcl ( );		
		$zpclbh = $this->_getParam('flg');
		$filter['filterParams'] = $_SESSION['zpcl_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['zpcl_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序	
		
		$retuzl = $model->getzhuangtai($zpclbh);//获取赠品策略状态 判断单品 组合
		if($retuzl == '1'){
		$this->_view->assign ( 'action', 'danpin' ); //设置页面为单品策略		
		$danpin = $model->getDate ( $zpclbh, $filter );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "danpin", $danpin );
		$this->_view->assign ( 'title', '赠品策略单品信息详情' );	
		$this->_view->display ( 'zpcl_04.php' );
			
		}else {
		$danpin = $model->getDate ( $zpclbh, $filter );
		$this->_view->assign ( "danpin", $danpin );
		$this->_view->assign ( 'title', '赠品策略组合信息详情' );	
		$this->_view->display ( 'zpcl_05.php' );		
		}
		}
	
	/*
	 * 保存赠品策略单品
	 */
	public function upzpcldpAction(){
		$result = array (); //定义返回值
		try{
		$model = new jc_models_zpcl ( );	
		$model->beginTransaction ();				//开启一个事物			
		$zpclbh = $_POST['ZPCLBH'];;	
		$model->updatezpclxx ($zpclbh); //更新赠品策略单品信息
		$model->del($zpclbh); // 删除赠品策略单品商品信息
		$model->saveDanpin($zpclbh); //插入赠品策略单品商品数据
		$model->commit ();
		$result ['status'] = 0; //修改成功
		$result ['zpclbh'] = $zpclbh; //赠品编号
		Common_Logger::logToDb ("【修改赠品单品策略 赠品编号 ：".$zpclbh."】");
		}catch( Exception $e){
		//回滚
			$model->rollBack ();
     		throw $e;
		}
 		echo Common_Tool::json_encode($result);
		
	}
	
	/*
	 * 赠品信息指向页面
	 */
	public function  zpxxindexAction(){
		
		$this->_view->assign ( "title", "基础管理 -赠品选择" ); //标题
		$this->_view->display ( "zpcl_06.php" );
		
	}
	
	
	/*
	 * 获取赠品名称
	 */
	public function getzpmchAction(){
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );       //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		     //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	     //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		$filter['searchParams']["ZPXX"] =$this->_getParam ( "ZPXX" ); //获取赠品编号/名称
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['zpxx_searchParams'] = $_POST;
				unset($_SESSION['zpxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['zpxx_filterParams'] = $_POST;
				unset($_SESSION['zpxx_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['zpxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['zpxx_searchParams'];  //固定查询条件
		$model = new jc_models_zpcl ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getzpmch( $filter );
				
	}
	/*
	 * 自动获取赠品信息
	 */
	
	public function autozpmchAction(){
		$filter ['searchkey'] = $this->_getParam('q');   //检索项目值
		$model = new jc_models_zpcl ( );
	    $result = $model->getAutocompleteData($filter);
	    echo Common_Tool::json_encode($result);
		
		
	}
	
}