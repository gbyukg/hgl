<?php
/***********************************************************
 ***** 模     块：    采购模块(CG)
 ***** 机     能：    采购退货审核(CGTHSH)
 ***** 作成者：    刘枞
 ***** 作成日：    2011/01/17
 ***** 更新履历：
 ***********************************************************/

class cg_cgthshController extends cg_controllers_baseController {
	/*
	 * 采购退货审核初始页面
	 */
	public function indexAction(){
		$this->_view->display ( "cgthsh_01.php" );
	}
	
	/*
	 * 采购退货单列表xml数据取得
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
				$_SESSION['cg_cgthsh_searchParams'] = $_POST;
				unset($_SESSION['cg_cgthsh_filterParams']);                      //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cg_cgthsh_filterParams'] = $_POST;
				unset($_SESSION['cg_cgthsh_searchParams']);                      //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cg_cgthsh_filterParams'];           //精确查询条件
		$filter['searchParams'] = $_SESSION['cg_cgthsh_searchParams'];           //固定查询条件
		
		$model = new cg_models_cgthsh();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
	
	/*
	 * 采购退货明细列表xml数据取得
	 */
	public function getthdmxlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh", '' ); 	    //开始日期
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cg_models_cgthsh();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getGridMingxiData( $filter );
	}
	
	/*
	 * 采购退货单审核通过
	 */
	public function checkyesAction(){
		$result['status'] = '0'; 
		$filter['bh'] = $this->_getParam( "bh", '' );
		$filter['shhyj'] = $this->_getParam( "shhyj", '' );
		$model = new cg_models_cgthsh();
		if($model->checkyes($filter)){
			$result['status'] = '1';       //审核通过
			Common_Logger::logToDb( "采购退货单审核通过，单据编号：".$filter['bh']);
		}else{
			$result['status'] = '0';       //审核出现异常
		}
		echo json_encode($result);
	}
	
	/*
	 * 采购退货单审核未通过
	 */
	public function checknoAction(){
		$result['status'] = '0'; 
		$filter['bh'] = $this->_getParam( "bh", '' );
		$filter['shhyj'] = $this->_getParam( "shhyj", '' );
		$model = new cg_models_cgthsh();
		if($model->checkno($filter)){
			$result['status'] = '2';       //审核未通过
			Common_Logger::logToDb( "采购退货单审核未通过，单据编号：".$filter['bh']);
		}else{
			$result['status'] = '0';       //审核出现异常
		}
		echo json_encode($result);
	}
	
}