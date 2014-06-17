<?php

/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       仓库信息(ckxx)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2010/11/11
 ***** 更新履历：
 ******************************************************************/

class cc_ckxxController extends cc_controllers_baseController {
	
	/*
     * 仓库列表画面显示
     */
	public function indexAction() {
		$this->_view->display ( 'ckxx_01.php' );
	}
	
	/*
     * 仓库登录画面显示
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );      //登录
		$this->_view->assign ( 'title', '仓库信息登录' );
		$this->_view->display ( 'ckxx_02.php' );
	}
	
	/*
     * 仓库修改画面显示
     */
	public function updateAction() {	
		$model = new cc_models_ckxx ( );
		//画面项目赋值
		$this->_view->assign ( 'action', 'update' );    //修改
		$this->_view->assign ( 'title', '仓库信息修改' );
		//$this->_view->assign ( "zhuangtai_opts", array ('0' => '冻结', '1' => '可用', 'X'=>'删除') );
		$this->_view->assign ( "rec", $model->getCkxx($this->_getParam ( "ckbh", '' )));
		$this->_view->display ( 'ckxx_03.php' );
	}
	
	/*
     * 仓库详情画面
     */
	public function detailAction() {
		//仓库信息取得
		$model = new cc_models_ckxx ( );
		//画面项目赋值
		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) );    //列表画面排序
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) );//列表画面排序
		$this->_view->assign ( "searchkey", $this->_getParam ( "searchkey", '' ) );//列表画面条件
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "zhuangtai_opts", array ('0' => '冻结', '1' => '可用', 'X'=>'删除') );
		$this->_view->assign ( "rec", $model->getCkxx ( $this->_getParam ( "ckbh", '' ) ));
		$this->_view->display ( 'ckxx_04.php' );
	}
	
    /*
	 * 得到仓库列表数据
	 */
	public function getlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$filter ['ckbh'] = $this->_getParam ( "ckbh", '' ); //仓库编号
		$filter ['orderby'] = $this->_getParam ( "orderby",2 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['ckxx_searchParams'] = $_POST;
				unset($_SESSION['ckxx_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['ckxx_filterParams'] = $_POST;
				unset($_SESSION['ckxx_searchParams']); //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['ckxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['ckxx_searchParams'];  //固定查询条件
		$model = new cc_models_ckxx ( );
		header ( "Content-type:text/xml" );       //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 更改仓库使用状态
	 */
	public function changestatusAction() {	
		try {
			$model = new cc_models_ckxx ( );
			$model -> beginTransaction ();				//开始一个事务
			$model->updateStatus ( $_POST ['ckbh'], $_POST ['ckzht'] );	
			Common_Logger::logToDb( "仓库信息维护  仓库启用  仓库编号：".$_POST ['ckbh']);  		//写入日志
			$model -> commit();            //事务提交
		} catch ( Exception $e ) {
			$model -> rollBack();			//事务回滚
     		throw $e;
		}
	}
	
	/*
	 * 判断仓库编号是否存在
	 */
	public function checkAction() {
		$model = new cc_models_ckxx ( );	
		if ($model->getCkxx ( $this->_getParam ( 'ckbh')) == FALSE) {
			echo 0; //不存在
		} else {
			echo 1; //存在
		}
	}
	
	
	/*
	 * 仓库信息保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值
		try {
			$model = new cc_models_ckxx ( );
			$model -> beginTransaction ();				//开始一个事务

			$result ['ckbh'] = $_POST ['CKBH']; //仓库编号

			if ($_POST ['action'] == 'new') {    //插入新数据
				if ($model->insertCkxx () == false) {
					$result ['status'] = 2; //仓库编号已存在					$model -> rollBack();
				} else {
					$result ['status'] = 0; //登录成功
					Common_Logger::logToDb( "仓库信息登录  仓库编号：".$_POST ['CKBH']);
					$model -> commit();            //事务提交
				}	
			} else {    //更新数据	
				if ($model->updateCkxx () == false) {
					$result ['status'] = 3;    //时间戳已变化
					$model -> rollBack();
				} else {
					$result ['status'] = 1;    //修改成功
					Common_Logger::logToDb( "仓库信息修改  仓库编号：".$_POST ['CKBH']);
					$model -> commit();            //事务提交
				}
			}	
			echo Common_Tool::json_encode( $result );     //返回处理结果
			
		} catch ( Exception $e ) {
			//事务回滚
			$model -> rollBack();
     		throw $e;
		}
	}
	
	/*
	 * 取得仓库信息
	 */
	public function getckxxAction() {
		$ckbh = $this->_getParam ( 'ckbh', '' );
		$flg = $this->_getParam ( 'flg', "current" );//检索方向

		$filter['filterParams'] = $_SESSION['ckxx_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['ckxx_searchParams'];  //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];//排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];//排序

		$model = new cc_models_ckxx ();
		$rec = $model->getCkxx ( $ckbh, $filter, $flg );
		if ($rec == false) {    //没有找到记录
			echo 'false';
		} else {		
			$this->_view->assign ( "zhuangtai_opts", array ('0' => '冻结', '1' => '可用', 'X'=>'删除') );
			$this->_view->assign ( "rec", $rec );
			echo  $this->_view->fetchPage ( "ckxx_04.php" ) ;
		}
	}
	
	
	/*
	 * 获取库区状态信息
	 */
	public function getkqstatusAction() {
		$ckbh = $this->_getParam ( 'ckbh', "" );
		$result ['ckbh'] = $ckbh;
		$kqzht = $this->_getParam ( 'ckzht', "" );
		$model = new cc_models_ckxx();
		$rec = $model->getkqstatus( $ckbh, $kqzht );
		if($kqzht == "0"){      // 冻结仓库：$kqzht == "0"   
			if($rec == TRUE){   
				//编号为$ckbh的仓库的下属库区没有正在使用的库区,进行冻结操作
				$model->updateStatus ( $ckbh, $kqzht );
				Common_Logger::logToDb( "仓库信息维护  冻结仓库  仓库编号：".$ckbh);
				$result ['status'] = 0;     
			}else{
				//编号为$ckbh的仓库的下属库区有正在使用的库区
				$result ['status'] = 1;
			}
		} else{                // 删除仓库：$kqzht == "X"
			if($rec == TRUE){
				//编号为$ckbh的仓库的下属库区所以库区都处于删除状态，进行删除操作
				$model->updateStatus ( $ckbh, $kqzht );
				Common_Logger::logToDb( "仓库信息维护  仓库禁用  仓库编号：".$ckbh);
				$result ['status'] = 0;
			}else{
				//编号为$ckbh的仓库的下属库区有库区未处于删除状态
				$result ['status'] = 2;
			}
		}
		echo Common_Tool::json_encode( $result );     //返回处理结果
	}
	
}