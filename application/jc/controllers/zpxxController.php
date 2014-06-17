<?php
/**********************************************************
 * 模     块：  基础模块(JC)
 * 机     能：  赠品信息(zpxx)
 * 作成者：    姚磊
 * 作成日：    2011/07/04
 * 更新履历：
 **********************************************************/	
class jc_zpxxController extends cc_controllers_baseController {

	public function indexAction(){
		$this->_view->assign ( "title", "基础管理-赠品信息维护" ); //标题
		$this->_view->display ( "zpxx_01.php" );
	}

	/*
	 * 查询赠品信息 返回xml格式
	 */
	public function getdataAction(){
		//取得列表参数
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
		$model = new jc_models_zpxx();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData( $filter );
	}
	
	/*
     * 赠品新建画面显示
     * 登录修改共用一个画面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );			
		$this->_view->assign ( 'title', '赠品信息' );			
		$this->_view->display ( 'zpxx_02.php' );
	}

	/*
	 * 保存数据
	 */
    function saveAction(){
    	
    	$result = array (); //定义返回值
    	$kprq = date("Y-m-d");//获取当前系统日期
    	$zpbh = Common_Tool::getDanhao('ZPH',$kprq);	
		$model = new jc_models_zpxx ( );
		//库存存储
		if ($_POST ['action'] == 'new') {
			//$model->beginTransaction();//开启事物
			//插入新数据
			if ($model->insertzPxx ($zpbh) == false) {
				$result ['status'] = 2; //
			} else {
				$result ['status'] = 0; //登录成功
				$result ['zpbh']= $zpbh; //赠品编号
				Common_Logger::logToDb ( "保存赠品信息  赠品编号：" . $zpbh );
			}
		
		} else {
			//更新数据
			if ($model->updatezpxx ($zpbh) == false) {
				$result ['status'] = 3; //时间戳已变化
			} else {
				$result ['status'] = 1; //修改成功
				$result ['zpbh']= $_POST ['ZPBHH']; //赠品编号
				Common_Logger::logToDb ( "修改赠品  赠品编号：" . $_POST ['ZPBHH']  );
			}
		}
	
		echo Common_Tool::json_encode($result); 	
    }
    /*
     * 修改页面
     */
    function updateAction(){    	
   		$model = new jc_models_zpxx ( );				
		$zpbh = $this->_getParam('flg');
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
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
		$rec = $model->getDate ( $zpbh,$filter );		
		$this->_view->assign ( 'action', 'update' ); //修改						
		$this->_view->assign ( 'title', '赠品信息修改' );
		$this->_view->assign ( "rec", $rec );	
		$this->_view->display ( 'zpxx_02.php' );
    }
    
	/*
     * 赠品详情画面
     */
	public function detailAction() {
		//赠品信息取得
		$model = new jc_models_zpxx ( );
		$zpbh = $this->_getParam('zpbh');
		$filter['filterParams'] = $_SESSION['zpxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['zpxx_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序	
		$rec = $model->getDate ( $zpbh, $filter );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec );
		$this->_view->assign ( 'title', '赠品信息详情' );
		
		$this->_view->display ( 'zpxx_03.php' );
	}
    
	/*
	 * 上一条下一条
	 */
	function getupdownAction(){
		
		$model = new jc_models_zpxx ( );
		$zpbh = $this->_getParam('zpbh');
		$filter['filterParams'] = $_SESSION['zpxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['zpxx_searchParams'];  //固定查询条件				
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		$flg = $this->_getParam ( 'flg', 'current' ); //检索方向		
		$rec = $model->getDate ( $zpbh, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			echo json_encode ( $this->_view->fetchPage ( "zpxx_03.php" ) );
		}
	}
	/*
	 * 删除
	 */
	function deleteAction(){
		$model = new jc_models_zpxx ( );
		$flg = $this->_getParam('flg');
		$model->del($flg);
		
	}	
}
