<?php
/**********************************************************
 * 模块：    采购模块(CG)
 * 机能：    采购开票单审核(CGKPSH)
 * 作成者：刘枞
 * 作成日：2011/03/04
 * 更新履历：
 **********************************************************/
class cg_cgkpshController extends cg_controllers_baseController {
	/*
	 * 采购开票单审核初始页面
	 */
	public function indexAction(){
		$this->_view->assign ( "title", "采购管理-采购订单审核" ); //标题
		$this->_view->display ( "cgkpsh_01.php" );
	}
	
	/*
	 * 采购开票单列表xml数据取得
	 */
	public function getthdlistdataAction(){
		//取得列表参数
		$filter ['flg'] = $this->_getParam ( "flg", 1 );  //当前页起始
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
				$_SESSION['cg_cgkpsh_searchParams'] = $_POST;
				unset($_SESSION['cg_cgkpsh_filterParams']);                      //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cg_cgkpsh_filterParams'] = $_POST;
				unset($_SESSION['cg_cgkpsh_searchParams']);                      //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['cg_cgkpsh_filterParams'];           //精确查询条件
		$filter['searchParams'] = $_SESSION['cg_cgkpsh_searchParams'];           //固定查询条件

		$model = new cg_models_cgkpsh();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
	
	/*
	 * 采购开票单明细列表xml数据取得
	 */
	public function getthdmxlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh", '' ); 	    	//单据编号
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cg_models_cgkpsh();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getGridMingxiData( $filter );
	}
	
	
	/*
	 * 警示原因列表xml数据取得
	 */
	public function getyuanyindataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh", '' ); 	    	//单据编号
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cg_models_cgkpsh();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getyuanyindata( $filter );
	}
	
	/*
	 * 采购开票单审核通过
	 */
	public function checkyesAction(){
		$result['status'] = '0'; 
		$filter['bh'] = $this->_getParam( "bh", '' );
		$filter['shhyj'] = $this->_getParam( "shhyj", '' );
		
		$model = new cg_models_cgkpsh();
		if($model->checkyes($filter)){
			$result['status'] = '1';       //审核通过
			Common_Logger::logToDb( "采购订单审核通过，单据编号：".$filter['bh']);
		}else{
			$result['status'] = '0';       //审核出现异常
		}
		echo json_encode($result);
	}
	
	/*
	 * 采购开票单审核未通过
	 */
	public function checknoAction(){
		$result['status'] = '0'; 
		$filter['bh'] = $this->_getParam( "bh", '' );
		$filter['shhyj'] = $this->_getParam( "shhyj", '' );
		
		$model = new cg_models_cgkpsh();
		if($model->checkno($filter)){
			$result['status'] = '2';       //审核未通过
			Common_Logger::logToDb( "采购订单审核未通过，单据编号：".$filter['bh']);
		}else{
			$result['status'] = '0';       //审核出现异常
		}
		echo json_encode($result);
	}
	
}