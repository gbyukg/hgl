<?php
/***********************************************************
 ***** 模     块：    销售模块(XS)
 ***** 机     能：    销售审核(XSSH)
 ***** 作成者：    刘枞
 ***** 作成日：    2011/01/27
 ***** 更新履历：
 ***********************************************************/

class xs_xsshController extends xs_controllers_baseController {
	/*
	 * 销售审核初始页面
	 */
	public function indexAction(){
		$this->_view->display ( "xssh_01.php" );
	}
	
	
	/*
	 * 销售审核列表xml数据取得
	 */
	public function getthdlistdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['xs_xskpsh_searchParams'] = $_POST;
				unset($_SESSION['xs_xskpsh_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['xs_xskpsh_filterParams'] = $_POST;
				unset($_SESSION['xs_xskpsh_searchParams']); //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['xs_xskpsh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['xs_xskpsh_searchParams'];  //固定查询条件
		
		$model = new xs_models_xssh();
		header ( "Content-type:text/xml" );            //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
	
	
	/*
	 * 销售审核明细列表xml数据取得
	 */
	public function getthdmxlistdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh", '' ); 	        //开始日期
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new xs_models_xssh();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getGridMingxiData( $filter );
	}
	
	
	/*
	 * 审批原因列表xml数据取得
	 */
	public function getshpgriddataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh", '' ); 	        //开始日期
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new xs_models_xssh();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getSHPGridData( $filter );
	}
	
	
	/*
	 * 审核通过
	 */
	public function checkyesAction(){
		$result['status'] = '0'; 
		$filter['bh'] = $this->_getParam( "bh", '' );
		$filter['shyj'] = $this->_getParam( "shyj", '' );
		$model = new xs_models_xssh();
		if($model->checkyes($filter)){
			$result['status'] = '1';       //审核通过
			Common_Logger::logToDb("销售订单审核通过，单据编号：".$filter['bh']);
		}else{
			$result['status'] = '0';       //审核出现异常
		}
		echo json_encode($result);
	}
	
	
	/*
	 * 审核未通过
	 */
	public function checknoAction(){
		$result['status'] = '0'; 
		$filter['bh'] = $this->_getParam( "bh", '' );
		$filter['shyj'] = $this->_getParam( "shyj", '' );
		$model = new xs_models_xssh();
		if($model->checkno($filter)){
			$result['status'] = '2';       //审核未通过
			Common_Logger::logToDb("销售订单审核未通过，单据编号：".$filter['bh']);
		}else{
			$result['status'] = '0';       //审核出现异常
		}
		echo json_encode($result);
	}
	
}