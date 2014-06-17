<?php
/******************************************************************
 ***** 模         块：       采购模块(CG)
 ***** 机         能：       采购返利协议维护(供应商)(flxywhgxx)
 ***** 作  成  者：       handong
 ***** 作  成  日：        2011/06/02
 ***** 更新履历：

 ******************************************************************/

class cg_flxywhgxxController extends cg_controllers_baseController {
     /*
      * 返利协议画面显示
      */
	public function indexAction(){
		$this->_view->assign('title','采购管理-采购返利协议(供应商)维护');
		$this->_view->display('flxywhg_01.php');
	}
    /*
     *   返利协议登录画面显示
     */
	public function newAction(){
		$this->_view->assign("kprq",date("Y-m-d"));//开票日期
		$model = new cg_models_flxygxx();
		$this->_view->assign('action','new');
		$rec = $model->getYgxx();
		$this->_view->assign('title','采购管理-采购返利协议(供应商)新规');
		$this->_view->assign("rec",$rec);
		$this->_view->display('flxyg_01.php');
	}
    /*
     *   返利协议详情画面
     */
	public function detailAction() {
		$model2 = new cg_models_flxywhgxx();
		$model = new cg_models_flxygxx();
		
		//画面项目赋值
		$this->_view->assign ( 'title', '采购管理-采购返利协议(供应商)详情' );
		$xybh = $this->_getParam ( "xybh" );
		$rec = $model->getYgxx();
		$rec2 = $model2->getFlxywhgxx($xybh);
		$this->_view->assign ( "flfsh_ops", array ('1' => '数量累计', '2' => '金额累计' ) );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec);
		$this->_view->assign('rec2',$rec2);
		$this->_view->display ('flxyg_02.php' );
	}
	/*
     * 返利协议修改画面显示
     * 登录修改共用一个画面
     */
	public function updateAction() {
		
		$model2 = new cg_models_flxywhgxx();
		$model = new cg_models_flxygxx();
		//画面项目赋值
		$xybh = $this->_getParam ( "xybh" );//协议编号
		$this->_view->assign ( "flfsh_ops", array ('1' => '数量累计', '2' => '金额累计' ) );
		$rec = $model->getYgxx();
		$rec2 = $model2->getFlxywhgxx( $xybh);			
		$this->_view->assign ( 'action', 'update' ); //修改						
		$this->_view->assign ( 'title', '采购管理-采购返利协议(供应商)修改' );
		$this->_view->assign ( 'rec', $rec );
		$this->_view->assign('rec2',$rec2);
		$this->_view->display ( 'flxyg_01.php' );
	}
	
   /*
	 * 得到返利协议信息
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
				unset($_SESSION['flxywhgxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['flxywhgxx_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['flxywhgxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
	
		$model = new cg_models_flxywhgxx();
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData ( $filter );
	}
	
    /*
	 * 取得返利协议信息  上一条,下一条
	 */
	public function getflxygxxAction() {
		$xybh = $this->_getParam ( "xybh" );
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter['filterParams'] = $_SESSION['flxywhgxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		$model = new cg_models_flxygxx();
		$model2 = new cg_models_flxywhgxx();
        $rec2 = $model2->getFlxywhgxx($xybh, $filter, $flg);
		$rec = $model->getYgxx();
		//没有找到记录
		if ($rec2 == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "flfsh_ops", array ('1' => '数量累计', '2' => '金额累计' ) );
			$this->_view->assign ( "rec", $rec );
			$this->_view->assign('rec2',$rec2);
			echo $this->_view->fetchPage ( "flxyg_02.php" );
		}
	}
    /*
	 * 更改 返利协议使用状态
	 */
	public function changestatusAction() {
		 $model = new cg_models_flxywhgxx();
			$model->updateStatus ( $_POST ['xybh'],$_POST ['flxygzht']);
			//写入日志
			Common_Logger::logToDb (($_POST ['flxygzht'] == 'X' ? "返利协议禁用" : "返利协议启用") .  "协议编号: " .$_POST['xybh'] );
		        
	}
}
?>