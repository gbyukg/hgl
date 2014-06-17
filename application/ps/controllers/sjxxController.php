<?php
/*********************************
 * 模块：   配送模块(PS)
 * 机能：   司机信息(sjxx)
 * 作成者：张宇
 * 作成日：2010/11/24
 * 更新履历：
 *********************************/
class ps_sjxxController extends ps_controllers_baseController {
	

	/*
     * 司机列表画面显示
     */
	public function indexAction() {
		$this->_view->assign( 'title',' 配送管理-司机信息维护');
		$this->_view->display ( 'sjxx_01.php' );
	}
	
	/*
     * 司机登录画面显示
     * 登录修改共用一个画面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );  //登录
		$this->_view->assign ( 'title', '配送管理-司机信息登录' );
		$this->_view->display ( 'sjxx_02.php' );
	}
	
     /*
     * 司机信息修改画面显示
     * 登录修改共用一个画面

     */
	public function updateAction() {
		
		$model = new ps_models_sjxx( );
		
		//画面项目赋值

		$this->_view->assign ( 'action', 'update' );//修改
		$this->_view->assign ( 'title', '配送管理-司机信息修改' );
    	$this->_view->assign ( "rec", $model->getSjxx($this->_getParam ( "sjbh", '' )));
		$this->_view->display ( 'sjxx_02.php' );
	}
	
	
	/*
     *司机详情画面
     */
	public function detailAction() {
		//员工信息取得
		$model = new ps_models_sjxx ( );
		//画面项目赋值
		$this->_view->assign ( 'title', '基础管理-司机信息详情' );
		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) );    //列表画面排序
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) );//列表画面排序
		$this->_view->assign ( "sjbh", $this->_getParam ( "sjbh", '' ) );//列表画面条件		
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $model->getSjxx ( $this->_getParam ( "sjbh", '' )));
		$this->_view->display ( 'sjxx_03.php' );
		


		}
	
	
  /*
	 * 得到员工列表数据
	 */
	public function getlistdataAction() {
		//取得分页排序参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
    	$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['searchParams'] = $_POST;
				unset($_SESSION['sjxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['sjxx_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['sjxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
	
		$model = new ps_models_sjxx ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 更改司机使用状态	 */
	public function changestatusAction() {
		
		$model = new ps_models_sjxx ( );
		$model->updateStatus ( $_POST ['sjbh'], $_POST ['zhuangtai'] );
		//写入日志
		Common_Logger::logToDb( $_POST ['zhuangtai']=='1'? "司机解锁":"司机锁定"." 司机编号：".$_POST ['sjbh']);
	
	}
	/*
	 * 判断司机编号是否存在
	 */
	public function checkAction() {

		$model = new ps_models_sjxx ( );
		
		if ($model->getSjxx ( $this->_getParam ( 'sjbh')) == FALSE) {
			echo 0; //不存在
		} else {
			echo 1; //存在
		}
	}
	/*
	 * 司机信息保存
	 */
	
	public function saveAction() {
		
		$result = array (); //定义返回值

		$result ['sjbh'] = $_POST ['SJBH']; //司机编号
		$model = new ps_models_sjxx ( );
		
		//司机登录
		if ($_POST ['action'] == 'new') {
			//插入新数据

			if ($model->insertSjxx () == false) {
				$result ['status'] = 2; //司机编号已存在

			} else {
				$result ['status'] = 0; //登录成功
				Common_Logger::logToDb( "司机信息登录  司机编号：".$_POST ['SJBH']);
			}
		
		} else {
			//更新数据
			if ($model->updateSjxx () == false) {
				$result ['status'] = 3; //时间戳已变化
			} else {
				$result ['status'] = 1; //修改成功
				Common_Logger::logToDb( "司机信息修改  司机编号：".$_POST ['SJBH']);
			}
		
		}
		

		echo Common_Tool::json_encode($result);
	
	}
	
	/*
	 * 取得司机信息
	 */
	public function getsjxxAction() {
		$sjbh = $this->_getParam ( "sjbh", '' ); //当前司机编号
		$flg = $this->_getParam ( 'flg', "current" );//检索方向
		$filter['filterParams'] = $_SESSION['sjxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		
		$model = new ps_models_sjxx ( );
		$rec = $model->getSjxx ( $sjbh,$filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {

			$this->_view->assign ( "rec", $rec );
			echo  $this->_view->fetchPage ( "sjxx_03.php" ) ;
		}
	}

}

