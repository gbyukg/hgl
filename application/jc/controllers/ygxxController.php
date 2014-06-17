<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   员工信息(ygxx)
 * 作成者：周义
 * 作成日：2010/10/14
 * 更新履历：
 *********************************/
class jc_ygxxController extends jc_controllers_baseController {

	/*
     * 员工列表画面显示
     */
	public function indexAction() {	
        $this->_view->assign ( 'title', '基础管理-员工信息维护' );
        
        $this->_view->display ( 'ygxx_01.php' ); 
	}
	
	/*
     * 员工登录画面显示
     * 登录修改共用一个画面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );  //登录
		$this->_view->assign ( 'title', '基础管理-员工信息登录' );
		$this->_view->assign ( "xingbie_opts", array ('0' => '男', '1' => '女' ) );
		$this->_view->display ( 'ygxx_02.php' );
	}
	
	/*
     * 员工修改画面显示
     * 登录修改共用一个画面
     */
	public function updateAction() {
		
		$model = new jc_models_ygxx ( );
		
		//画面项目赋值
		$this->_view->assign ( 'action', 'update' );//修改
		$this->_view->assign ( 'title', '基础管理-员工信息修改' );
		$this->_view->assign ( "xingbie_opts", array ('0' => '男', '1' => '女' ) );
		$this->_view->assign ( "rec", $model->getYgxx ($this->_getParam ( "ygbh", '' )));
		$this->_view->display ( 'ygxx_02.php' );
	}
	
	/*
     * 员工详情画面
     */
	public function detailAction() {
		//员工信息取得
		$model = new jc_models_ygxx ( );
		
		//画面项目赋值
		$this->_view->assign ( 'title', '基础管理-员工信息详情' );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "xingbie_opts", array ('0' => '男', '1' => '女' ) );
		$this->_view->assign ( "rec", $model->getYgxx ( $this->_getParam ( "ygbh", '' )));
		$this->_view->display ( 'ygxx_03.php' );
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
				unset($_SESSION['ygxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['ygxx_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['ygxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
	
		$model = new jc_models_ygxx ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData ( $filter );
	
	}
	
	
	public function getlistreportdataAction(){
		//取得检索条件
		$filter['filterParams'] = $_SESSION['ygxx_filterParams']; //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
		$filter["sortParams"]= $_SESSION["sortParams"];//排序条件
	
		$model = new jc_models_ygxx ( );
		//header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getListReportData($filter);
	}
	/*
	 * 更改员工使用状态
	 */
	public function changestatusAction() {
		
		$model = new jc_models_ygxx ( );
		$model->updateStatus ( $_POST ['ygbh'], $_POST ['ygzht'] );
		//写入日志
		Common_Logger::logToDb( ($_POST ['ygzht']=='X'? "员工锁定":"员工解锁")." 员工编号：".$_POST ['ygbh']);
	
	}
	
	/*
	 * 判断员工编号是否存在
	 */
	public function checkAction() {

		$model = new jc_models_ygxx ( );
		
		if ($model->getYgxx ( $this->_getParam ( 'ygbh')) == FALSE) {
			echo "0"; //不存在
		} else {
			echo "1"; //存在
		}
	}
	
	/*
	 * 得到员工姓名助记码
	 */
	public function getzhjmAction() {
		echo Common_Tool::getPy ( $this->_getParam ( 'ygxm') );
	}
	
	/*
	 * 员工信息保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值
		$result ['ygbh'] = $_POST ['YGBH']; //员工编号

		try{
			$model = new jc_models_ygxx ( );
			$model->beginTransaction();
			
			//员工登录
			if ($_POST ['action'] == 'new') {
				//插入新数据
				if ($model->insertYgxx () == false) {
					$result ['status'] = 2; //员工编号已存在
				} else {
					$result ['status'] = 0; //登录成功
					Common_Logger::logToDb( "员工信息登录  员工编号：".$_POST ['YGBH']);
				}
			
			} else {
				//更新数据
				if ($model->updateYgxx () == false) {
					$result ['status'] = 3; //时间戳已变化
				} else {
					$result ['status'] = 1; //修改成功
					Common_Logger::logToDb( "员工信息修改  员工编号：".$_POST ['YGBH']);
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
	
	/*
	 * 取得员工信息
	 */
	public function getygxxAction() {
		//取得检索条件
		$ygbh = $this->_getParam ( "ygbh", '' ); //当前员工编号
		$flg = $this->_getParam ( 'flg', "current" );//检索方向
		$filter['filterParams'] = $_SESSION['ygxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
	
		$model = new jc_models_ygxx ( );
		$rec = $model->getYgxx ( $ygbh,$filter, $flg );
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {

			$this->_view->assign ( "xingbie_opts", array ('0' => '男', '1' => '女' ) );
			$this->_view->assign ( "rec", $rec );
	    	echo  $this->_view->fetchPage ( "ygxx_03.php" ) ;

		}
	}
}