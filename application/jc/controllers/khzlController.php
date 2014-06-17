<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   客户资料(khzl)
 * 作成者：苏迅
 * 作成日：2010/11/01
 * 更新履历：
 *********************************/
class jc_khzlController extends jc_controllers_baseController {
	
	/*
     * 客户资料维护画面显示
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '基础管理-客户资料维护' );
		$this->_view->display ( 'khzl_01.php' );
	}
	
	/*
     * 客户资料登录
     */
	public function newAction() {
		
		$model = new jc_models_khzl ( );
		$sheng = $model->getShengList (); //省列表取得
		$fhq = $model->getfhq (); //发货区取得
		$this->_view->assign ( 'action', 'new' ); //登录
		$this->_view->assign ( 'title', '客户资料登录' );
		$this->_view->assign ( "xiaoshou_opts", array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) );
		$this->_view->assign ( "gonghuo_opts", array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) );
		$this->_view->assign ( "khjl_opts", array ('9' => '- - 请 选 择 - -','1' => '远距离', '2' => '中距离', '3' => '近距离' ) );
		$this->_view->assign ( 'sheng', $sheng );
		$this->_view->assign ( 'shi', array ('0' => '- - 请 选 择 - -' ) );
		$this->_view->assign ( 'fhq', $fhq );
		$this->_view->display ( 'khzl_02.php' );
	
	}
	
	/*
     * 客户修改画面显示
     * 登录修改共用一个画面
     */
	public function updateAction() {
		
		$model = new jc_models_khzl ( );
		$rec = $model->getKhzl ( $this->_getParam ( "dwbh", '00000000' ));
		$sheng = $model->getShengList (); //省列表取得
		$shi = $model->getShiList ( ($rec ["SZSH"] != null ? $rec ["SZSH"] : 1) ); //市列表取得
		$fhq = $model->getfhq (); //发货区取得
		$this->_view->assign ( 'action', 'update' ); //修改
		$this->_view->assign ( 'title', '客户资料修改' );
//		$this->_view->assign ( "xiaoshou_opts", array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) );
//		$this->_view->assign ( "gonghuo_opts", array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) );
$this->_view->assign ( "khjl_opts", array ('9' => '- - 请 选 择 - -','1' => ' 远距离 ', '2' => '中距离', '3' => '近距离' ) );
		$this->_view->assign ( "rec", $rec );
		$this->_view->assign ( 'sheng', $sheng );
		$this->_view->assign ( 'shi', $shi );
		$this->_view->assign ( 'fhq', $fhq );
		$this->_view->display ( 'khzl_02.php' );
	}
	
	/*
     * 客户资料详情画面
     */
	public function detailAction() {
		
		$model = new jc_models_khzl ( );
		$rec = $model->getKhzl ( $this->_getParam ( "dwbh", '00000000' ));
		$sheng = $model->getShengList (); //省列表取得
		$shi = $model->getShiList ( ($rec ["SZSH"] != null ? $rec ["SZSH"] : 1) ); //市列表取得
		$fhq = $model->getfhq (); //发货区取得

		//画面项目赋值
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( 'title', '基础管理-客户资料详情' );
//		$this->_view->assign ( "xiaoshou_opts", array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) );
//		$this->_view->assign ( "gonghuo_opts", array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) );
$this->_view->assign ( "khjl_opts", array ('9' => '- - 请 选 择 - -','1' => ' 远距离 ', '2' => '中距离', '3' => '近距离' ) );
		$this->_view->assign ( "rec", $rec );
		$this->_view->assign ( 'sheng', $sheng );
		$this->_view->assign ( 'shi', $shi );
		$this->_view->assign ( 'fhq', $fhq );
		$this->_view->display ( 'khzl_03.php' );
	}
	
	/*
	 * 得到客户列表数据
	 */
	public function getlistdataAction() {
		
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); //默认显示数量
/*		$filter ['dwbh'] = $this->_getParam ( "dwbh", '' ); //单位
		$filter ['shfxsh'] = $this->_getParam ( "shfxsh", '' ); //是否销售
		$filter ['shfjh'] = $this->_getParam ( "shfjh", '' ); //是否供应商*/
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['jc_khzl_searchParams'] = $_POST;
				unset($_SESSION['khzl_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['khzl_filterParams'] = $_POST;
				unset($_SESSION['jc_khzl_searchParams']); //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['khzl_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['jc_khzl_searchParams'];  //固定查询条件
		
		$model = new jc_models_khzl ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/**
	 *  市名取得
	 */
	public function getshiAction() {
		$request = $this->getRequest ();
		if ($request->isXmlHttpRequest ()) {
			$c = intval ( $request->getParam ( 'c' ) );
			$model = new jc_models_khzl ( );
			$shi = $model->getShiList ( $c );
			$this->_helper->getHelper ( 'Json' )->sendJson ( $shi );
		}
	}
	
	/*
	 * 判断单位编号是否存在
	 */
	public function checkAction() {
		$model = new jc_models_khzl ( );
		if ($model->getKhzl ( $this->_getParam ( 'dwbh' )) == FALSE) {
			echo 0; //不存在

		} else {
			echo 1; //存在
		}
	}
	
	/*
	 * 得到单位名称助记码
	 */
	public function getzhjmAction() {
		echo Common_Tool::getPy ( $this->_getParam ( 'dwmch' ) );
	}
	
	/*
	 * 取得客户资料信息
	 */
	public function getkhzlAction() {
		$dwbh = $this->_getParam ( 'dwbh', '00000000' );
		$filter['filterParams'] = $_SESSION['khzl_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['jc_khzl_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		$model = new jc_models_khzl ( );
		$rec = $model->getKhzl ( $dwbh, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$sheng = $model->getShengList (); //省列表取得
			$shi = $model->getShiList ( ($rec ["SZSH"] != null ? $rec ["SZSH"] : 1) ); //市列表取得
			$fhq = $model->getfhq (); //发货区取得
//			$this->_view->assign ( "xiaoshou_opts", array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) );
//			$this->_view->assign ( "gonghuo_opts", array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) );
$this->_view->assign ( "khjl_opts", array ('9' => '- - 请 选 择 - -','1' => ' 远距离 ', '2' => '中距离', '3' => '近距离' ) );
			$this->_view->assign ( "rec", $rec );
			$this->_view->assign ( 'sheng', $sheng );
			$this->_view->assign ( 'shi', $shi );
			$this->_view->assign ( 'fhq', $fhq );
			echo json_encode ( $this->_view->fetchPage ( "khzl_03.php" ) );
		}
	}
	/*
	 * 保存
	 */
	public function saveAction() {
		
		$result = array (); //定义返回值
		$result ['DWBH'] = $_POST ['DWBH'];
		$model = new jc_models_khzl ( );
		
		if ($_POST ['action'] == 'new') {
			
			//插入新数据
			if ($model->insertKhzl () == false) {
				$result ['status'] = 2; //单位编号已存在
			} else {
				$result ['status'] = 0; //登录成功
				Common_Logger::logToDb ( "客户资料登录  单位编号：" . $_POST ['DWBH'] );
			}		
		} else {
			//更新数据
			if ($model->updateKhzl () == false) {
				$result ['status'] = 3; //时间戳已变化
			} else {
				$result ['status'] = 1; //修改成功
				Common_Logger::logToDb ( "客户信息修改  单位编号：" . $_POST ['DWBH'] );
			}
		}
		
		//返回处理结果
		echo json_encode ( $result );
	
	}
	
	/*
	 * 初装应收应付余额
	 */
	public function chuzhuangAction() {
		$model = new jc_models_khzl ( );
		$rec = $model->getKhzl ( ($this->_getParam ( "dwbh" )));
		$this->_view->assign ( "rec", $rec );
		$this->_view->assign ( 'title', '基础管理-客户初装应收应付' );
		$this->_view->display ( 'khzl_04.php' );
	
	}
	
	/*
	 * 更改客户使用状态
	 */
	public function changestatusAction() {
		
		$model = new jc_models_khzl ( );
		$model->updateStatus ( $_POST ['dwbh'], $_POST ['khzht'] );
		//写入日志
		Common_Logger::logToDb ( ($_POST ['khzht'] == '1' ? "客户解锁" : "客户锁定") . " 客户编号：" . $_POST ['dwbh'] );
	
	}

}
