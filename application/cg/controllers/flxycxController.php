<?php
/******************************************************************
 ***** 模         块：       采购模块(CG)
 ***** 机         能：     返利协议执行状态查询(flxycx)
 ***** 作  成  者：       handong
 ***** 作  成  日：        2011/06/10
 ***** 更新履历：

 ******************************************************************/

class cg_flxycxController extends cg_controllers_baseController {
     /*
      * 返利协议画面显示
      */
	public function indexAction(){
		$this->_view->assign('title','采购管理-返利协议执行状态查询');
		$this->_view->display('flxycx_01.php');
	}
    /*
     *   返利协议详情画面
     */
	public function detailAction() {
		$model2 = new cg_models_flxycx();
		$model = new cg_models_flxygxx(); //员工  所属部门
		
		//画面项目赋值
		$this->_view->assign ( 'title', '采购管理-采购返利协议(供应商)详情' );
		$xybh = $this->_getParam ( "xybh" );
		$rec = $model->getYgxx();
		$rec2 = $model2->getFlxycxgxx($xybh);
		$this->_view->assign ( "flfsh_ops", array ('1' => '数量累计', '2' => '金额累计' ) );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec);
		$this->_view->assign('rec2',$rec2);
		$this->_view->display ('flxycxg_01.php' );
	}
	/*
	 * 返利协议详情画面
	 */
	public function detail2Action(){
		$model = new cg_models_flxywhs();		
		$xybh = $this->_getParam ( "xybh" );
		$this->_view->assign ( "rec",$model->getXymingxiData ($xybh) );
		$this->_view->assign ( "recs",$model->getMingxiData($xybh) );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign("title","采购管理-采购返利协议(商品)详情");
		$this->_view->display ( "flxycxsh_01.php" );
	}
   /*
	 * 得到供应商信息
	 */
	public function getlistdataAction(){
	//取得分页排序参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
    	$filter ['orderby'] = $this->_getParam ( "orderby","1"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['searchParams'] = $_POST;
				unset($_SESSION['flxycx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['flxycx_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['flxycx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
	
		$model = new cg_models_flxycx();
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData ( $filter );
	}
	
   /*
	 * 得到商品信息
	 */
	public function getlistdata2Action(){
	//取得分页排序参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
    	$filter ['orderby'] = $this->_getParam ( "orderby","1"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['searchParams'] = $_POST;
				unset($_SESSION['flxycx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['flxycx_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['flxycx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
	
		$model = new cg_models_flxycx();
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData2 ( $filter );
	}
    /*
	 * 取得返利协议供应商信息  上一条,下一条
	 */
	public function getflxygxxAction() {
		$xybh = $this->_getParam ( "xybh" );
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter['filterParams'] = $_SESSION['flxywhgxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		$model = new cg_models_flxygxx();
		$model2 = new cg_models_flxycx();
        $rec2 = $model2->getFlxycxgxx($xybh, $filter, $flg);
		$rec = $model->getYgxx();
		//没有找到记录
		if ($rec2 == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "flfsh_ops", array ('1' => '数量累计', '2' => '金额累计' ) );
			$this->_view->assign ( "rec", $rec );
			$this->_view->assign('rec2',$rec2);
			echo $this->_view->fetchPage ( "flxycxg_01.php" );
		}
	}
	  /*
	 * 取得返利协议商品信息  上一条,下一条
	 */
	public function getxyshxxAction(){
		$xybh = $this->_getParam ( "xybh" );
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter['filterParams'] = $_SESSION['flxywhgxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		$model = new cg_models_flxycx();
		$rec = $model->getFlxycxshxx($xybh,$filter,$flg);
		$this->_view->assign ( "rec", $rec );
	//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo  $this->_view->fetchPage ( "flxycxsh_01.php" ) ;
		}
	
	}
	
}

?>