<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   商品资料(shpzl)
 * 作成者：苏迅
 * 作成日：2010/11/15
 * 更新履历：

 *********************************/
class jc_shpzlController extends jc_controllers_baseController {
	
	/*
     * 商品基础资料维护画面显示
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '基础管理-商品资料维护' );
		$this->_view->display ( 'shpzl_01.php' );
	}
	
	/*
	 * 得到商品基础资料列表数据
	 */
	public function getlistdataAction() {
		
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart" ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50 ); //默认显示数量
		//$filter ['shpbh'] = $this->_getParam ( "shpbh", '' ); //
		$filter ['flbm'] = $this->_getParam ( "flbm", '' ); //分類
		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['jc_shpzl_searchParams'] = $_POST;
				unset($_SESSION['shpzl_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['shpzl_filterParams'] = $_POST;
				unset($_SESSION['jc_shpzl_searchParams']); //清空一般查询条件
				unset($filter ['flbm']);
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['shpzl_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['jc_shpzl_searchParams'];  //固定查询条件
				
		$model = new jc_models_shpzl ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
     * 商品基础资料登录
     */
	public function newAction() {
		
		$model = new jc_models_shpzl ( );
		$jixing = $model->getJixingList (); //剂型
		$leibie = $model->getLeibieList (); //类别
		$yyfl = $model->getYyflList (); //用药分类
		$chffl = $model->getChfflList (); //处方分类
		$yfpd = $model->getYfpdList (); //药方判断
		$shplx = $model->getShplxList (); //商品类型
		$chbjs = $model->getChbjsList (); //成本计算
		$bzhdw = $model->getBzhdwList (); //包装单位
		$shfotc = $model->getShfotcList (); //是否OTC
		$zhdkqlx = $model->getZhdkqlxList (); //指定库区类型
		

		//给画面变量赋值
		$this->_view->assign ( 'action', 'new' ); //登录
		$this->_view->assign ( 'title', '基础管理-商品基础资料登录' );
		$this->_view->assign ( 'jixing', $jixing ); //剂型
		$this->_view->assign ( 'leibie', $leibie ); //类别
		$this->_view->assign ( 'yyfl', $yyfl ); //用药分类
		$this->_view->assign ( 'chffl', $chffl ); //处方分类
		$this->_view->assign ( 'yfpd', $yfpd ); //药方判断
		$this->_view->assign ( 'shplx', $shplx ); //商品类型
		$this->_view->assign ( 'chbjs', $chbjs ); //成本计算
		$this->_view->assign ( 'bzhdw', $bzhdw ); //包装单位
		$this->_view->assign ( 'shfotc', $shfotc ); //是否OTC
		$this->_view->assign ( 'zhdkqlx', $zhdkqlx ); //指定库区类型	
		$this->_view->assign ( 'gzhbzh_opt', array ('9' => '- - 请 选 择 - -', '0' => '普通', '1' => '贵重' ) ); //贵重标志	
		$this->_view->assign ( 'jxdx_opt', array ('9' => '- - 请 选 择 - -', '0' => '经销', '1' => '代销' ) ); //经销代销
		//$this->_view->assign ( 'bzhqfsh_opt', array ('9' => '- - 请 选 择 - -', '0' => '没有', '1' => '出厂日期', '2' => '失效日期' ) ); //保质期方式        $this->_view->assign ( 'bzhqfsh_opt', array ('9' => '- - 请 选 择 - -', '0' => '没有', '1' => '有') ); //保质期方式
		$this->_view->assign ( 'shfyp_opt', array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) ); //是否饮片
		$this->_view->assign ( 'shfyaop_opt', array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) ); //是否药品
		$this->_view->display ( 'shpzl_02.php' );
	}
	
	/*
     * 商品修改画面显示
     * 登录修改共用一个画面

     */
	public function updateAction() {
		
		$model = new jc_models_shpzl ( );
		$rec = $model->getshpzl ( $this->_getParam ( "shpbh", '00000000' ));
		$jixing = $model->getJixingList (); //剂型
		$leibie = $model->getLeibieList (); //类别
		$yyfl = $model->getYyflList (); //用药分类
		$chffl = $model->getChfflList (); //处方分类
		$yfpd = $model->getYfpdList (); //药方判断
		$shplx = $model->getShplxList (); //商品类型
		$chbjs = $model->getChbjsList (); //成本计算
		$bzhdw = $model->getBzhdwList (); //包装单位
		$shfotc = $model->getShfotcList (); //是否OTC
		$zhdkqlx = $model->getZhdkqlxList (); //指定库区类型
		$this->_view->assign ( 'action', 'update' ); //修改
		$this->_view->assign ( 'title', '基础管理-商品基础资料修改' );
		$this->_view->assign ( 'rec', $rec );
		$this->_view->assign ( 'jixing', $jixing ); //剂型
		$this->_view->assign ( 'leibie', $leibie ); //类别
		$this->_view->assign ( 'yyfl', $yyfl ); //用药分类
		$this->_view->assign ( 'chffl', $chffl ); //处方分类
		$this->_view->assign ( 'yfpd', $yfpd ); //药方判断
		$this->_view->assign ( 'shplx', $shplx ); //商品类型
		$this->_view->assign ( 'chbjs', $chbjs ); //成本计算
		$this->_view->assign ( 'bzhdw', $bzhdw ); //包装单位
		$this->_view->assign ( 'shfotc', $shfotc ); //是否OTC
		$this->_view->assign ( 'zhdkqlx', $zhdkqlx ); //指定库区类型	
		$this->_view->assign ( 'gzhbzh_opt', array ('9' => '- - 请 选 择 - -', '0' => '普通', '1' => '贵重' ) ); //贵重标志	
		$this->_view->assign ( 'jxdx_opt', array ('9' => '- - 请 选 择 - -', '0' => '经销', '1' => '代销' ) ); //经销代销
		//$this->_view->assign ( 'bzhqfsh_opt', array ('9' => '- - 请 选 择 - -', '0' => '没有', '1' => '出厂日期', '2' => '失效日期' ) ); //保质期方式
		$this->_view->assign ( 'bzhqfsh_opt', array ('9' => '- - 请 选 择 - -', '0' => '没有', '1' => '有') ); //保质期方式
		$this->_view->assign ( 'shfyp_opt', array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) ); //是否饮片
		$this->_view->assign ( 'shfyaop_opt', array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) ); //是否药品
		$this->_view->display ( 'shpzl_02.php' );
	}
	
	/*
     * 商品资料详情画面
     */
	public function detailAction() {
		
		$model = new jc_models_shpzl ( );
		$rec = $model->getShpzl ( $this->_getParam ( "shpbh", '00000000' ) );
		//画面项目赋值
/*		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) ); //列表画面排序
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) ); //列表画面排序
		$this->_view->assign ( "shpbhkey", $this->_getParam ( "shpbhkey", '' ) ); //列表画面条件-商品*/
		$this->_view->assign ( "flbm", $this->_getParam ( "flbm", '' ) ); //列表画面条件-分類
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( 'gzhbzh_opt', array ('9' => '- - 请 选 择 - -', '0' => '普通', '1' => '贵重' ) ); //贵重标志	
		$this->_view->assign ( 'jxdx_opt', array ('9' => '- - 请 选 择 - -', '0' => '经销', '1' => '代销' ) ); //经销代销
		//$this->_view->assign ( 'bzhqfsh_opt', array ('9' => '- - 请 选 择 - -', '0' => '没有', '1' => '出厂日期', '2' => '失效日期' ) ); //保质期方式
		$this->_view->assign ( 'bzhqfsh_opt', array ('9' => '- - 请 选 择 - -', '0' => '没有', '1' => '有') ); //保质期方式
		$this->_view->assign ( 'shfyp_opt', array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) ); //是否饮片
		$this->_view->assign ( 'shfyaop_opt', array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) ); //是否药品
		$this->_view->assign ( 'title', '商品基础资料详情' );
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'shpzl_03.php' );
	}
	
	/*
	 * 判断商品编号是否存在
	 */
	public function checkAction() {
		$model = new jc_models_shpzl ( );
		if ($model->getShpzl ( $this->_getParam ( 'shpbh' ) ) == FALSE) {
			echo 0; //不存在
		} else {
			echo 1; //存在
		}
	}
	
	/*
	 * 判断商品条码是否存在
	 */
	public function checkshptmAction() {
		$model = new jc_models_shpzl ( );
		if ($model->checkShptm ( $this->_getParam ( 'shptm' ) ) == 0) {
			echo 0; //不存在
		} else {
			echo 1; //存在
		}
	}
	
	/*
	 * 判断商品拆散信息是否存在
	 */
	public function checkbzhdwAction() {
		$model = new jc_models_shpzl ( );
		$shpbh = $this->_getParam ( 'shpbh', '' );
		$bzhdw = $this->_getParam ( 'bzhdw', '' );
		$oldbzhdw = $model->checkBzhdw ( $shpbh );
		if ($oldbzhdw == FALSE) {
			echo 0; //没有该商品拆散信息         
		} else {
			if ($oldbzhdw == $bzhdw) {
				echo 0; //该商品基本包装单位没有改变
			} else {
				echo 1; //该商品基本包装单位发生变化
			}
		
		}
	}
	
	/*
	 * 得到商品名称助记码
	 */
	public function getzhjmAction() {
		echo Common_Tool::getPy ( $this->_getParam ( 'shpmc' ) );
	}
	
	/*
	 * 取得商品资料信息(上下条)
	 */
	public function getshpzlAction() {
		$shpbh = $this->_getParam ( 'shpbh', '00000000' );
/*		$filter ['shpbhkey'] = $this->_getParam ( "shpbhkey", '' ); //检索条件		
		$filter ['orderby'] = $this->_getParam ( "orderby", 2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式*/
		$filter ['flbm'] = $this->_getParam ( "flbm", '' ); //检索条件
		$flg = $this->_getParam ( 'flg', "current" ); //检索方向
		
		$filter['filterParams'] = $_SESSION['shpzl_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['jc_shpzl_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序

		$model = new jc_models_shpzl ( );
		$rec = $model->getShpzl ( $shpbh, $filter, $flg );
		
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( 'gzhbzh_opt', array ('9' => '- - 请 选 择 - -', '0' => '普通', '1' => '贵重' ) ); //贵重标志	
			$this->_view->assign ( 'jxdx_opt', array ('9' => '- - 请 选 择 - -', '0' => '经销', '1' => '代销' ) ); //经销代销
			//$this->_view->assign ( 'bzhqfsh_opt', array ('9' => '- - 请 选 择 - -', '0' => '没有', '1' => '出厂日期', '2' => '失效日期' ) ); //保质期方式			$this->_view->assign ( 'bzhqfsh_opt', array ('9' => '- - 请 选 择 - -', '0' => '没有', '1' => '有') ); //保质期方式
			$this->_view->assign ( 'shfyp_opt', array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) ); //是否饮片
			$this->_view->assign ( 'shfyaop_opt', array ('9' => '- - 请 选 择 - -', '0' => '否', '1' => '是' ) ); //是否药品
			$this->_view->assign ( 'rec', $rec );
			echo json_encode ( $this->_view->fetchPage ( "shpzl_03.php" ) );
		}
	}
	
	/*
	 * 保存
	 */
	public function saveAction() {
		
		$result = array (); //定义返回值
		$result ['SHPBH'] = $_POST ['SHPBH'];
		$model = new jc_models_shpzl ( );
		
		if ($_POST ['FLG_DELCHAISAN'] == 1) {
			$model->delete ( $_POST ['SHPBH'] );
			Common_Logger::logToDb ( "商品拆散信息删除  商品编号：" . $_POST ['SHPBH'] );
		}
		
		if ($_POST ['action'] == 'new') {
			
			//插入新数据
			if ($model->insertShpzl () == false) {
				$result ['status'] = 2; //商品编号已存在
			} else {
				$result ['status'] = 0; //登录成功
				Common_Logger::logToDb ( "商品基础资料登录  单位编号：" . $_POST ['SHPBH'] );
			}
		
		} else {
			//更新数据
			if ($model->updateShpzl () == false) {
				$result ['status'] = 3; //时间戳已变化
			} else {
				$result ['status'] = 1; //修改成功
				Common_Logger::logToDb ( "商品基础资料信息修改  单位编号：" . $_POST ['SHPBH'] );
			}
		}
		
		//返回处理结果
		echo json_encode ( $result );
	
	}
	
	/*
	 * 更改商品使用状态
	 */
	public function changestatusAction() {
		
		$model = new jc_models_shpzl ( );
		$model->updateStatus ( $_POST ['shpbh'], $_POST ['shpzht'] );
		//写入日志
		Common_Logger::logToDb ( ($_POST ['shpzht'] == '1' ? "商品解锁" : "商品锁定") . " 商品编号：" . $_POST ['shpbh'] );
	
	}
}
