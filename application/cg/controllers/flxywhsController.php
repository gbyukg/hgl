<?php
/*********************************
 * 模块：    采购模块(cg)
 * 机能：   返利协议商品订单维护(FLXYWHS)
 * 作成者：侯殊佳
 * 作成日：2011/5/31
 * 更新履历：
 *********************************/
class cg_flxywhsController extends cg_controllers_baseController {
private $idx_ROWNUM = 0; // 行号
	private $idx_SHPBH = 1; // 商品编号
	private $idx_SHPMCH = 2; // 商品名称
	private $idx_GUIGE = 3; // 商品规格
	private $idx_BZHDWM = 4; // 包装单位
	private $idx_JLGG = 5; // 计量规格
	private $idx_CHANDI = 6; // 产地
	private $idx_QSRQ = 7; // 起始日期	
	private $idx_ZZRQ = 8; // 终止日期
	private $idx_ZCLJSL = 9; // 政策累计数量
	private $idx_XYDJ = 10; // 协议单价
	private $idx_ZCLJJE = 11; // 政策累计金额
	private $idx_FLJE = 12; // 返利金额
	private $idx_BEIZHU = 13; // 备注
	
	
	/*
	 * 商品返利协议维护初始页面
	 */
	public function indexAction() { 	
		$this->_view->assign ( "title", "采购管理-返利协议(商品)维护" ); //标题
		$this->_view->display ( "flxywhs_01.php" );
	}	
/*
     * 商品返利协议登录画面显示
     * 登录修改共用一个画面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );  //登录
		$this->_view->assign ( "kprq", date("Y-m-d")); 
		$model = new cg_models_flxywhs (); 
		$ywybm = $model->getywybm ();
		$this->_view->assign ( "ywybm", $ywybm);
		$this->_view->assign ( 'title', '采购管理-返利协议(商品)登录' );
		$this->_view->display ( 'flxys_01.php' );
	}
	
	
	/*
     * 商品返利协议修改画面显示
     * 登录修改共用一个画面
     */
	public function updateAction() {
		
		$model = new cg_models_flxywhs();
		
		//画面项目赋值
		$this->_view->assign ( 'action', 'update' );//修改 
		$this->_view->assign ( 'title', '采购管理-返利协议(商品)修改' );
		$xybh = $this->_getParam ( "xybh" );
		$this->_view->assign('xybh', $xybh);
		$this->_view->assign ( "rec",$model->getXymingxiData ($xybh) );
		$this->_view->display ( 'flxys_01.php' );
	}
	
	
 	/*
 	 *  获取返利协议维护信息列表
 	 */
 	public function getlistdataAction(){
 		//取得列表参数			
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式	
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['flxywhs_searchParams'] = $_POST;
				unset($_SESSION['flxywhs_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['flxywhs_filterParams'] = $_POST;
				unset($_SESSION['flxywhs_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['flxywhs_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['flxywhs_searchParams'];  //固定查询条件
		
			
		$model = new cg_models_flxywhs();
		
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );

 	}
	/*
 	 *  获取返利协议维护商品明细信息列表
 	 */
 	
 	public function getmingxilistdataAction(){
 		
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$xybh = $this->_getParam ( "xybh" );
		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式		
		$model = new cg_models_flxywhs();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getMingxiGridData ($xybh,$filter );;
 	}
 	
 	
	/*
	 * 返利协议维护详情页面初始化
	 */
	public function xiangqingAction(){
		$model = new cg_models_flxywhs();		
		$xybh = $this->_getParam ( "xybh" );
		$this->_view->assign ( "rec",$model->getXymingxiData ($xybh) );
		$filter ['DWBH'] = $this->_getParam ( "DWBH", '' ); 	            //单位编号
		$filter ['SHPBH'] = $this->_getParam ( "SHPBH", '' );            //商品编号
		$filter ['lssj'] = $this->_getParam ( "lssj", '' ); 
		$this->_view->assign ( "recs",$model->getMingxiData($xybh) );
		$this->_view->assign ( "filter",$filter );
		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) ); //排序列
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) ); //排序方式
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign("title","采购管理-返利协议(商品)详情");
		$this->_view->display ( "flxywhs_02.php" );
	}
	
	/**
     * 返利协议详情商品明细信息
     */
	public function getflxyAction()
	{
		$xybh = $this->_getParam('xybh');
		$model = new cg_models_flxywhs();
		header("Content-type:text/xml");
		echo $model->getFlxy($xybh);
	}
	
/*
	 * 上一条 下一条
	 */
	
	public function getxyxxAction(){
		$xybh = $this->_getParam('xybh');
		$model = new cg_models_flxywhs();
		$filter ['DWBH'] = $this->_getParam ( "dwbh", '' ); 	            //单位编号
		$filter ['SHPBH'] = $this->_getParam ( "shpbh", '' );            //商品编号
		$filter ['lssj'] = $this->_getParam ( "lssj", '' );
		$this->_view->assign ( "filter",$filter );
		$flg = $this->_getParam ( 'flg', "current" );//检索方向
		$filter['filterParams'] = $_SESSION['flxywhs_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['flxywhs_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
		$rec = $model->getXyxx($xybh,$filter,$flg);
		$this->_view->assign ( "rec", $rec );
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo  $this->_view->fetchPage ( "flxywhs_02.php" ) ;
		}
	
	}
	
	/*
	 * 更改协议使用状态
	 */
	public function changestatusAction() {
		
		$model = new cg_models_flxywhs ( );
		$model->updateStatus ( $_POST ['xybh'], $_POST ['xyzht'] );
		//写入日志
		Common_Logger::logToDb( ($_POST ['xyzht']=='X'? "协议锁定":"协议解锁")." 协议编号：".$_POST ['ygbh']);
	
	}
	
 
 	
	 /**
     * 取得单位信息
     *
     */
	public function getdanweiinfoAction(){
    	$filter ['dwbh'] = $this->_getParam('dwbh');   //单位编号
 		$xskpModel = new cg_models_cgddwh() ;
		
	    echo Common_Tool::json_encode($xskpModel->getDanweiInfo($filter));
	}
 	
 

}