<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：  供货企业调查(ghqy)
 * 作成者：姚磊
 * 作成日：2010/11/19
 * 更新履历：
 *********************************/
class jc_ghqyController extends jc_controllers_baseController {
	
	/*
     * 企业供货企业调查维护
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '供货企业法定资格及质量信誉调查维护表' );
		$this->_view->display ( 'ghqy_01.php' );
	}
	
	/*
     * 企业供货信息登录画面显示
     * 登录修改共用一个画面
     */
	public function newAction() {
		//$model = new jc_models_ghqy();
		$this->_view->assign ( 'txrq', date("Y-m-d"));  //填写日期
		$this->_view->assign ( 'action', 'new' );		
		$this->_view->assign ( 'title', '供货企业法定资格及质量信誉调查表' );
		$this->_view->display ( 'ghqy_02.php' );
	}
	
	/*
     * 供货企业供货信息修改
     */
	public function updateAction() {
		
		$model = new jc_models_ghqy ( );
		$djbh = $this->_getParam ( "djbh" );
		$rec = $model->getGhqy ( $djbh );
		$this->_view->assign ( 'action', 'update' ); //修改
		$this->_view->assign ( 'title', '供货企业法定资格及质量信誉调查表' );
		$txrq = $rec['TXRQ'];  //获取填写日期
		$this->_view->assign ( "rec", $rec );
		$this->_view->assign ( 'txrq', date($txrq));  //填写日期转换格式赋值到页面
		$this->_view->display ( 'ghqy_02.php' );
	}
	
	/*
	 * 获取企业供货信息列表
	 */
	public function getlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
				//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['ghqy_searchParams'] = $_POST;
				unset($_SESSION['ghqy_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['ghqy_filterParams'] = $_POST;
				unset($_SESSION['ghqy_searchParams']); //清空一般查询条件
			}
		}
		
		$filter['filterParams'] = $_SESSION['ghqy_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['ghqy_searchParams'];  //固定查询条件
		$model = new jc_models_ghqy ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 企业供货信息保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值
		try {
			//开始一个事务
			$model = new jc_models_ghqy ( );
			$model->beginTransaction ();			
			$djbh = Common_Tool::getDanhao('001',$_POST['TXRQ']);
			$result['djbh'] = $djbh;
		//供货信息存储&修改
		if ($_POST ['action'] == 'new') {
			
			//插入新数据
			if ($model->insertGhqy ($djbh) == false) {
				$result ['status'] = 2; //有空置选项
			} else {
				$result ['status'] = 0; //登录成功
				$model->commit ();		
				Common_Logger::logToDb ( "供货企业信息登录  单据号：" . $result['djbh'] );
			}
		
		} else {
			//更新数据
			if ($model->updateGhqy () == false) {
				$result ['status'] = 3; //时间戳已变化
			} else {
				$result ['status'] = 1; //修改成功
				$model->commit ();
				Common_Logger::logToDb ( "供货企业信息修改 单据号：" . $result['djbh'] );
			}
		}
		echo json_encode ( $result );
		
		} catch ( Exception $e ) {
			//回滚
			$model->rollBack ();
     		throw $e;
		}
	
	}
	
	/*
	  * 企业供货信息调查详情
	  */
	public function detailAction() {
		
		$model = new jc_models_ghqy ( );		
		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) ); //列表画面排序
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) ); //列表画面排序
		$this->_view->assign ( "searchkeyqymch", $this->_getParam ( "searchkeyqymch", '' ) ); //列表画面条件
		$this->_view->assign ( "searchkeytxrq", $this->_getParam ( "searchkeytxrq", '' ) ); //列表画面条件
		
		$djbh = $this->_getParam ( "djbh" );
		$rec = $model->getGhqy ( $djbh );
		//画面项目赋值
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $rec );
		$this->_view->assign ( 'title', '供货企业法定资格及质量信誉详情表' );
		$this->_view->display ( 'ghqy_03.php' );
	}
	
	/*
	 * 企业供货信息删除
	 */
	public function deleteAction() {
		
		$model = new jc_models_ghqy ( );
		$djbh = $this->_getParam ( "djbh" );
		$model->deleteGhqy ( $djbh );
		$this->_view->display ( 'ghqy_01.php' );
	}
	
	/*
	 * 企业供货单据号存在验证				 
	 */
	public function checkAction() {
		
		$model = new jc_models_ghqy ( );
		$filter['filterParams'] = $_SESSION['ghqy_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['ghqy_searchParams'];  //固定查询条件		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		$djbh = $this->_getParam ( "djbh" );
		if ($model->getGhqy ( $djbh,$filter ) == FALSE) {
			echo 0; //不存在
		

		} else {
			echo 1; //存在
		}
	}
	
	/*
	 * 取得企业供货信息 上一条,下一条
	 */
	public function getghqyAction() {
		$djbh = $this->_getParam ( "djbh" );
		$filter['filterParams'] = $_SESSION['ghqy_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['ghqy_searchParams'];  //固定查询条件		
		
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$model = new jc_models_ghqy ( );
		$rec = $model->getGhqy ( $djbh, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "rec", $rec );
			$this->_view->assign ( 'title', '供货企业法定资格及质量信誉详情表' );
			echo json_encode ( $this->_view->fetchPage ( "ghqy_03.php" ) );
			
		}
	}
}
