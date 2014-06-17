<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   实盘数据录入(spsjlr)
 * 作成者：李洪波
 * 作成日：2011/01/17
 * 更新履历：
 *********************************/
class cc_spsjlrController extends cc_controllers_baseController {
	/*
	 * 盘点单据号选择弹出画面
	 */
	public function listAction(){
		$this->_view->assign("title","仓储管理-盘点单据号选择");
		$this->_view->display ( "spsjlr_02.php" );
	}
	/*
	 * 盘点单据号列表画面数据取得
	 */
	public function getlistdataAction(){
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
       
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_shangpin_searchParams'] = $_POST;
				unset($_SESSION['cc_spsjlr_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_spsjlr_filterParams'] = $_POST;
				unset($_SESSION['gt_shangpin_searchParams']); //清空一般查询条件
			}
		}
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cc_spsjlr_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_shangpin_searchParams'];  //固定查询条件
		$djbh_model = new cc_models_spsjlr();		
		header("Content-type:text/xml");
		echo $djbh_model->getListData($filter);
	}
     /*
	 * 自动完成
	 */
	public function autocompleteAction(){
		$djbh_model = new cc_models_spsjlr();
	    $result = $djbh_model->getAutocompleteData();
	    echo json_encode($result);
	}
	/*
	 *  实盘数据录入初始页面
	 */
	public function indexAction()
	{
		$this->_view->assign("DJBH",$this->_getParam ( "pddjbh", '' )); //单据编号
		$this->_view->assign ( "title", " 仓储管理-实盘数据录入" );
		$this->_view->assign ( "bmmch", $_SESSION ["auth"]->bmmch ); //部门
		$this->_view->display ( "spsjlr_01.php" );
	}

	/*
     * 获取单据信息
     */
	public function getdanjuxxAction() {
 		$model = new cc_models_spsjlr();
		$result = $model->getDanjuxx($this->_getParam ('djbh'));
		echo Common_Tool::json_encode($result);
	}
	/*
     * 检查特价信息验证
     */
	public function checktjxxAction() {
		$model = new cc_models_spsjlr();
		//取得列表参数
		$filter ['dwbh'] = $this->_getParam ( "dwbh", '' ); //检索条件_单位编号
		$filter ['shpbh'] = $this->_getParam ( "shpbh", '' ); //检索条件_商品编号
		$filter ['zhxrq'] = $this->_getParam ( "zhxrq", '' ); //检索条件_执行日期
		$filter ['zhzhrq'] = $this->_getParam ( "zhzhrq", '' ); //检索条件_终止日期
		if($model->checkTjxx($filter)==FALSE){
			echo 0; //不存在
		} else {
			echo 1; //存在
		};
	}

	/*
     * 获取表格信息
     */
	public function getgridlistdataAction() {
		//取得列表参数
		//$filter ['djbh'] = $this->_getParam ( "djbh", '' ); //检索条件_单据编号
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //检索条件_排序字段
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //检索条件_升序降序
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_shangpin_searchParams'] = $_POST;
				unset($_SESSION['cc_spsjlr_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['cc_spsjlr_filterParams'] = $_POST;
				//unset($_SESSION['gt_shangpin_searchParams']); //清空一般查询条件
			}
		}
		//取得检索条件
		$filter['filterParams'] = $_SESSION['cc_spsjlr_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_shangpin_searchParams'];  //固定查询条件
		$model = new cc_models_spsjlr();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 保存实盘信息
	 */
	public function saveAction() {
		//单据编号取得
		$djbh = $_POST['DJBH'];
		try {
			$model = new cc_models_spsjlr();
			//开始一个事务
			$model->beginTransaction ();
			//实盘信息保存
			$result=$model->saveShpshjlr($djbh);
			//保存成功
			if($result['status'] == '0'){
			    Common_Logger::logToDb("【实盘数据录入 盘点单据号：".$djbh."】");
			    $model->commit ();
			 }else{
			    $model->rollBack ();//有错误发生
			}
			echo json_encode($result);
		}
		 catch ( Exception $e )
		 {
			//回滚
			$model->rollBack ();
     		throw $e;
		}
	}
     /*
     * 盘点详细生成画面显示
     */
	public function detailAction() {
		$djbh = $this->_getParam("djbh"); //盘点单据号
 		$flg = $this->_getParam ( 'flg', 'current' );//检索方向
		$filter['filterParams'] = $_SESSION['pdwh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['pdwh_searchParams'];  //固定查询条件
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //检索条件_排序字段
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //检索条件_升序降序
		$model = new cc_models_spsjlr();
		$rec = $model->getPdwhOne($djbh,$filter,$flg);
		$this->_view->assign ( 'action', 'detail' );
		$this->_view->assign ( 'title', '仓储管理-盘点详情' );
		//画面赋值
		$this->_view->assign ( 'pdkshshj', $filter ['pdkshshj'] );
		$this->_view->assign ( 'pdjshshj', $filter ['pdjshshj'] );
		$this->_view->assign ( 'zhtai', $filter ['pdzht'] );
		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) );    //列表画面排序
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) );//列表画面排序
		$this->_view->assign ( "djbhwh",$filter ['djbhwh'] );
		$this->_view->assign ( "rec", $rec);
		//账面数量条件设定
		switch($rec[ZHMSHLTJ]){
			case 1 :
				$this->_view->assign ( 'check1', 'checked');
				break;
			case 2 :
				$this->_view->assign ( 'check2', 'checked');
				break;
			case 3 :
				$this->_view->assign ( 'check3', 'checked');
				break;
			default:
				$this->_view->assign ( 'check1', 'checked');
				break;
		}
		//冻结标志设定
		if ($rec[DJBZH]==1){
			$this->_view->assign ( 'check4', 'checked');
		}
		//翻页用
		if ($flg !='current'){
			//上一页 下一页
			if ($rec == FALSE) {
				//当返回为空时
				echo 'false';
			}else{
				//上一页 下一页数据存在时
				$this->_view->assign ( "full_page", 0 );
				echo  $this->_view->fetchPage ('spsjlr_03.php' );
			}
		}else{
			//第一次进入详细画面时
			$this->_view->assign ( "full_page", 1 );
			$this->_view->display ( 'spsjlr_03.php' );
		}
	}
}
