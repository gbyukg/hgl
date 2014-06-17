<?php
/*********************************
 * 模块：   门店模块(MD)
 * 机能：   货架管理(HJGL)
 * 作成者：李洪波
 * 作成日：2011/02/16
 * 更新履历：
 *********************************/
class md_hjglController extends md_controllers_baseController {
	/*
	 * 柜组编号选择弹出画面
	 */
	public function listAction(){
		$this->_view->assign("title","门店管理-柜组编号选择");
		$this->_view->display ( "gzgl_04.php" );
	}
	
	/*
	 * 柜组编号列表画面数据取得
	 */
	public function getlistgzdataAction(){
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
      	$filter ['orderby'] = $this->_getParam ( "orderby",1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式

		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];

		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['.'] = $_POST;
				unset($_SESSION['gzxx_filterParams']); //清空精确查询条件
			}
				
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gzxx_filterParams'] = $_POST;
				unset($_SESSION['gt_shangpin_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['gzxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_shangpin_searchParams'];  //固定查询条件
        $gzbh_model = new md_models_hjgl();		
		header("Content-type:text/xml");
		echo $gzbh_model->getListData($filter);
	}

     /*
	 * 自动完成
	 */
	public function autocompleteAction(){
		$gzbh_model = new md_models_hjgl();
	    $result = $gzbh_model->getAutocompleteData();
	    echo json_encode($result);
	}

	/*
     * 货架管理画面显示
     */
	public function indexAction() {
		$this->_view->assign("title","门店管理-货架信息维护");
		$this->_view->assign ( "XTRQ", date("Y-m-d"));  //登记日期
		$this->_view->assign ( "MDBH_H", $_SESSION ['auth']->mdbh);  //门店编号
		$this->_view->assign ( "MDBH", $_SESSION ['auth']->mdmch);  //门店名称
		$this->_view->assign ( "CZYBH", $_SESSION ['auth']->userName);  //操作员
		$this->_view->assign ( "CZYBH_H", $_SESSION ['auth']->userId);  //操作员编号

    	$this->_view->display ( 'hjgl_01.php' );
	}
	/*
     * 货架登录画面显示
     * 登录修改共用一个画面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );  //登录
		$this->_view->assign ( 'title', '门店管理-货架信息登录' );
		$this->_view->assign ( "XTRQ", date("Y-m-d"));  //登记日期
		$this->_view->assign ( "MDBH_H", $_SESSION ['auth']->mdbh);  //门店编号
		$this->_view->assign ( "MDBH", $_SESSION ['auth']->mdmch);  //门店名称
		$this->_view->assign ( "CZYBH", $_SESSION ['auth']->userName);  //操作员
		$this->_view->assign ( "CZYBH_H", $_SESSION ['auth']->userId);  //操作员编号
		$this->_view->assign ( "shyzht_opts", array ('9'=>'请选择','0' => '停用', '1' => '正常' ) );
		$rec["SHYZHT"]=1;
		
		$this->_view->assign ( "rec",$rec);		
		$this->_view->display ( 'hjgl_02.php' );
	}
	
	/*
     * 货架修改画面显示
     * 登录修改共用一个画面
     */
	public function updateAction() {
		
		$model = new md_models_hjgl ( );		
		//画面项目赋值
		$this->_view->assign ( 'action', 'update' );//修改
		$this->_view->assign ( 'title', '门店管理-货架信息修改' );
		$this->_view->assign ( "XTRQ", date("Y-m-d"));  //登记日期
		$this->_view->assign ( "MDBH_H", $_SESSION ['auth']->mdbh);  //门店编号
		$this->_view->assign ( "MDBH", $_SESSION ['auth']->mdmch);  //门店名称
		$this->_view->assign ( "CZYBH", $_SESSION ['auth']->userName);  //操作员
		$this->_view->assign ( "CZYBH_H", $_SESSION ['auth']->userId);  //操作员编号

		$this->_view->assign ( "shyzht_opts", array ('9'=>'请选择','0' => '停用', '1' => '正常' ) );
		$this->_view->assign ( "rec", $model->getHjxx ($this->_getParam ( "gzbh", '' ), $_SESSION ['auth']->mdbh,$this->_getParam ( "hjbh", '' )));
		$this->_view->display ( 'hjgl_02.php' );
	}
	
	/*
     * 货架详情画面
     */
	public function detailAction() {
		//货架信息取得
		$model = new md_models_hjgl ( );
		$this->_view->assign ( 'title', '门店管理-货架信息详情' );
		$this->_view->assign ( "XTRQ", date("Y-m-d"));  //登记日期
		$this->_view->assign ( "MDBH_H", $_SESSION ['auth']->mdbh);  //门店编号
		$this->_view->assign ( "MDBH", $_SESSION ['auth']->mdmch);  //门店名称
		$rec=$model->getHjxx ( $this->_getParam ( "gzbh", '' ), $_SESSION ['auth']->mdbh,$this->_getParam ( "hjbh", '' ));
		//画面项目赋值
		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) );    //列表画面排序
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) );//列表画面排序
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec);
		if($rec["SHYZHT"]==0){
			$this->_view->assign ( "SHYZHT", "停用");  //使用状态

		}else{
			$this->_view->assign ( "SHYZHT", "正常");  //使用状态

		}
		$this->_view->assign ( "CZYBH", $rec["FZRBH"]);  //操作员
		$this->_view->assign ( "CZYBH_H", $rec["FZRBH"]);  //操作员编号

		$this->_view->display ( 'hjgl_03.php' );
	}
	
         /*
	 * 得到货架列表数据
	 */
	public function getlistdataAction() {
		//取得列表参数
		$filter ['gzbh'] = $this->_getParam ( "searchKey",''); //柜组编号
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
	   	$filter ['orderby'] = $this->_getParam ( "orderby",2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_shangpin_searchParams'] = $_POST;
				unset($_SESSION['hjxx_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['hjxx_filterParams'] = $_POST;
				unset($_SESSION['gt_shangpin_searchParams']); //清空一般查询条件
			}
		}
		//取得检索条件
		$filter['filterParams'] = $_SESSION['hjxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_shangpin_searchParams'];  //固定查询条件
		$model = new md_models_hjgl ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	/*
	 * 判断柜组编号是否存在
	 */
	public function getgzxxAction() {

		$model = new md_models_hjgl();		
		$result=$model->getGzxx ( $this->_getParam ( 'gzbh'));
		echo Common_Tool::json_encode($result);
	}
	
	/*
	 * 判断货架编号是否存在
	 */
	public function checkAction() {

		$model = new md_models_hjgl ( );
		
		if ($model->getHjxx ($_POST ['HJBH'], $_POST ['MDBH_H'],$_POST ['SEARCHKEY_H']) == FALSE) {
			echo 0; //不存在

		} else {
			echo 1; //存在
		}
	}

	/*
	 * 取得货架信息
	 */
	public function gethjxxAction() {
		$gzbh =$this->_getParam ( "gzbh",'');; //当前柜组编号
		$hjbh =$this->_getParam ( "hjbh",'');; //当前货架编号
	    $filter['filterParams'] = $_SESSION['gzxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_shangpin_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		$flg = $this->_getParam ( 'flg', "current" );//检索方向

		$model = new md_models_hjgl();
		$rec = $model->getHjxx ( $gzbh,$_SESSION ['auth']->mdbh,$hjbh,$filter, $flg );
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			if($rec["SHYZHT"]==0){
				$this->_view->assign ( "SHYZHT", "停用");  //使用状态

			}else{
				$this->_view->assign ( "SHYZHT", "正常");  //使用状态

			}
			$this->_view->assign ( "rec", $rec );
	    	echo  $this->_view->fetchPage ( "hjgl_03.php" ) ;
		}
	}

	/*
	 * 货架信息保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值

		$result ['hjbh'] = $_POST ['HJBH']; //货架编号
		try{
			$model = new md_models_hjgl ( );
			$model->beginTransaction();
			
			//货架登录
			if ($_POST ['action'] == 'new') {
				//插入新数据

				if ($model->insertGzxx () == false) {
					$result ['status'] = 2; //货架编号已存在

				} else {
					$result ['status'] = 0; //登录成功
					Common_Logger::logToDb( "货架信息登录  货架编号：".$_POST ['HJBH']);
				}
			
			} else {
				//更新数据
				if ($model->updateGzxx () == false) {
					$result ['status'] = 3; //时间戳已变化
				} else {
					$result ['status'] = 1; //修改成功
					Common_Logger::logToDb( "货架信息修改  货架编号：".$_POST ['HJBH']);
				}
			
			}
			
			$model->commit();
			
			//返回处理结果
			echo json_encode ( $result );
		}catch (Exception $ex){
			$model->rollBack();
			throw $ex;
		}
	}
}
