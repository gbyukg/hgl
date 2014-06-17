<?php
/*********************************
 * 模块：   配送模块(PS)
 * 机能：  车辆信息(clxx)
 * 作成者：姚磊
 * 作成日：2010/11/25
 * 更新履历：
 *********************************/
class ps_clxxController extends ps_controllers_baseController {
	/*
     * 车辆信息列表画面显示
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '配送管理-车辆管理信息维护' );
		$this->_view->display ( 'clxx_01.php' );
	}
	
	/*
     * 车辆信息登录画面显示
     * 登录修改共用一个画面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );
		$this->_view->assign ( 'title', '配送管理-车辆信息登录' );
		$this->_view->assign ( "chlfl_ops", array ('9' => '请选择', '1' => '本公司车辆', '2' => '挂靠车辆' ) );
		$this->_view->display ( 'clxx_02.php' );
	}
	
	/*
     * 车辆信息详情画面
     */
	public function detailAction() {
		
		$model = new ps_models_clxx ( );
		//画面项目赋值
		$this->_view->assign ( 'title', '配送管理-车辆信息详情' );
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "rec", $model->getClxx ($this->_getParam ( "chphm",'' )));
		$this->_view->assign ( "chlfl_ops", array ('9' => '', '1' => '本公司车辆', '2' => '挂靠车辆' ) );
		$this->_view->display ( 'clxx_03.php' );
	}
	
	/*
	 * 取得车辆信息
	 */
	public function getclxxAction() {
		$chphm = $this->_getParam ( "chphm",'' );//当前员工编号
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$filter['searchParams'] = $_SESSION['searchParams'];//固定查询条件
		$filter['filterParams'] = $_SESSION['clxx_filterParams'];  //精确查询条件
		$filter ['orderby'] =$_SESSION['sortParams']['orderby']; //排序列
		$filter ['direction']=$_SESSION['sortParams']['direction']; //排序方式	
		$model = new ps_models_clxx ( );
		$rec = $model->getClxx ( $chphm, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "chlfl_ops", array ('9' => '', '1' => '本公司车辆', '2' => '挂靠车辆' ) );
			$this->_view->assign ( "rec", $rec );
			
			//$this->_view->assign ( 'title', '车辆信息详情' );
			echo $this->_view->fetchPage ( "clxx_03.php" );
		}
	}
	
	/*
	 *  车辆信息修改
	 */
	public function updateAction() {
		
		$model = new ps_models_clxx ( );
		$this->_view->assign ( 'action', 'update' ); //修改
		$this->_view->assign ( 'title', '配送管理-车辆信息修改' );
		$this->_view->assign ( "chlfl_ops", array ('9' => '请选择', '1' => '本公司车辆', '2' => '挂靠车辆' ) );
		$this->_view->assign ( "rec", $model->getClxx ($this->_getParam ( "chphm",'' ) ) );
		$this->_view->display ( 'clxx_02.php' );
	}
	
	/*
	 * 取得车辆信息列表
	 */
	public function getlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 20 ); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式	
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['searchParams'] = $_POST;
				unset($_SESSION['clxx_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['clxx_filterParams'] = $_POST;
				unset($_SESSION['searchParams']); //清空一般查询条件
			}
		}
		$filter['filterParams'] = $_SESSION['clxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];  //固定查询条件
		$model = new ps_models_clxx ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 改变车辆信息状态
	 */
	public function changestatusAction() {
		
		$model = new ps_models_clxx ( );
		$model->updateStatus ( $_POST ['chphm'], $_POST ['shyzht'] );
		//写入日志
		Common_Logger::logToDb ( ($_POST ['shyzht'] == 'X' ? "车辆禁用" : "车辆启用") . " 车牌号码：" . $_POST ['chphm'] );
	}
	
	/*
	 * 车牌号码存在验证				
	 * 
	 */
	public function checkAction() {
		
		$model = new ps_models_clxx ( );
		$chphm = $this->_getParam ( "chphm" );
		if ($model->getClxx ( $chphm ) == FALSE) {
			echo 0; //不存在
		

		} else {
			echo 1; //存在
		}
	}
	
	/*
	 * 车辆信息保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值
		$result ['chphm'] = $_POST ['CHPHM']; //车牌号码				
	
		try{
			$model = new ps_models_clxx();
			$model->beginTransaction();
		//库存存储
		if ($_POST ['action'] == 'new') {
			//插入新数据
			if ($model->insertClxx () == false) {
				$result ['status'] = 2; //车辆编号已存在
			} else {
				$result ['status'] = 0; //登录成功
				Common_Logger::logToDb ( "车辆信息登录  仓库编号：" . $_POST ['CHPHM'] );
			}
		
		} else {
			//更新数据
			if ($model->updateClxx () == false) {
				$result ['status'] = 3; //时间戳已变化
			} else {
				$result ['status'] = 1; //修改成功
				Common_Logger::logToDb ( "车辆信息修改 仓库编号：" . $_POST ['CHPHM'] );
			}
		}
		     $model->commit();
		//返回处理结果
		     echo json_encode($result);
		     
	    }catch (Exception $ex){
			$model->rollBack();
			throw $ex;
	    }
	}
}

