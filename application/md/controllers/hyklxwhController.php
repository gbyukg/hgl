<?php
/**********************************************************
 * 模     块：    门店模块(MD)
 * 机     能：    会员卡类型维护(HYKLXWH)
 * 作成者：    刘    枞
 * 作成日：    2011/02/12
 * 更新履历：
 **********************************************************/

class md_hyklxwhController extends md_controllers_baseController {

	/*
     * 会员卡类型维护画面显示
     */
	public function indexAction() {
    	$this->_view->display ( 'hyklxwh_01.php' );
	}
	
	
	/*
     * 会员卡类型登录画面显示
     * 登录,修改共用一个画面
     */
	public function newAction() {
		$this->_view->assign ( 'action', 'new' );       //登录
		$this->_view->assign ( 'title', '会员卡类型登录' );
		$this->_view->assign ( "zht_opts", array ('9'=>'请选择','X' => '禁止', '1' => '启用' ) );
		$this->_view->display ( 'hyklxwh_02.php' );
	}
	
	
	/*
     * 会员卡类型修改画面显示
     * 登录,修改共用一个画面
     */
	public function updateAction() {
		$model = new md_models_hyklxwh();
		//画面项目赋值
		$this->_view->assign ( 'action', 'update' );     //修改
		$this->_view->assign ( 'title', '员工信息修改' );
		$this->_view->assign ( "zht_opts", array ('9'=>'请选择','X' => '禁止', '1' => '启用' ) );
		$this->_view->assign ( "rec", $model->getxx ($this->_getParam ( "bh", '' )));
		$this->_view->display ( 'hyklxwh_02.php' );
	}
	
	
	/*
     * 会员卡类型详情画面
     */
	public function detailAction() {
		//会员卡类型信息取得
		$model = new md_models_hyklxwh();
		//画面项目赋值
		$this->_view->assign ( "orderby", $this->_getParam ( "orderby", '' ) );    //列表画面排序
		$this->_view->assign ( "direction", $this->_getParam ( "direction", '' ) );//列表画面排序
		$this->_view->assign ( "full_page", 1 );
		$this->_view->assign ( "zht_opts", array ('9'=>'请选择','X' => '禁止', '1' => '启用' ) );
		$this->_view->assign ( "rec", $model->getxx ( $this->_getParam ( "bh", '' )));
		$this->_view->display ( 'hyklxwh_03.php' );
	}
	
	
    /*
	 * 得到会员卡类型列表数据
	 */
	public function getlistdataAction(){
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",10);        //默认显示数量
    	$filter ['orderby'] = $this->_getParam ( "orderby",2 );    //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['hyklxwh_searchParams'] = $_POST;
				unset($_SESSION['hyklxwh_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['hyklxwh_filterParams'] = $_POST;
				unset($_SESSION['hyklxwh_searchParams']); //清空一般查询条件
			}
		}
		
		//取得检索条件
		$filter['filterParams'] = $_SESSION['hyklxwh_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['hyklxwh_searchParams'];  //固定查询条件

		$model = new md_models_hyklxwh();
		header ( "Content-type:text/xml" );      //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	
	/*
	 * 更改会员卡类型状态
	 */
	public function changestatusAction(){
		$bh = $this->_getParam ( 'bh', '' );
		$zht = $this->_getParam ( 'zht', '' );
		$model = new md_models_hyklxwh();
		$model->updateStatus ( $bh, $zht );
		//写入日志
		Common_Logger::logToDb(($zht=='X'? "会员卡类型禁用":"会员卡类型启用")." 会员卡类型编号：".$bh);
	}
	
	
	/*
	 * 判断编号是否存在
	 */
	public function checkAction(){
		$model = new md_models_hyklxwh();
		if ($model->getxx( $this->_getParam('bh') ) == FALSE) {
			echo 0;     // 不存在
		} else {
			echo 1;     // 存在
		}
	}
	

	/*
	 * 会员卡类型信息保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值
		$result ['bh'] = $_POST ['KPLXBH'];     // 会员卡类型编号
		try{
			$model = new md_models_hyklxwh();
			$model->beginTransaction();

			// 会员卡类型信息登录
			if ($_POST ['action'] == 'new') {
				//插入新数据
				if ($model->insertxx () == false) {
					$result ['status'] = 2;      // 编号已存在
				} else {
					$result ['status'] = 0;      // 登录成功
					Common_Logger::logToDb( "会员卡类型信息登录  会员卡类型编号：".$result ['bh']);
				}
			} else {
				// 会员卡类型信息更新
				if ($model->updatexx () == false) {
					$result ['status'] = 3;      // 时间戳已变化
				} else {
					$result ['status'] = 1;      // 修改成功
					Common_Logger::logToDb( "会员卡类型信息修改  会员卡类型编号：".$result ['bh']);
				}
			}

			$model->commit();

			echo json_encode ( $result );		 // 返回处理结果
		}catch (Exception $ex){
			$model->rollBack();
			throw $ex;
		}
	}


	/*
	 * 取得会员卡类型信息
	 */
	public function getxxAction() {
		$bh = $this->_getParam ( "bh", '' );                             //编号
		$flg = $this->_getParam ( 'flg', "current" );                    //检索方向
		
		$filter['filterParams'] = $_SESSION['hyklxwh_filterParams'];     //精确查询条件
		$filter['searchParams'] = $_SESSION['searchParams'];             //固定查询条件
		$filter['orderby'] = $_SESSION["sortParams"]["orderby"];         //排序
		$filter['direction'] = $_SESSION["sortParams"]["direction"];     //排序
		
		$model = new md_models_hyklxwh();
		$rec = $model->getxx( $bh,$filter,$flg );
		//没有找到记录
		if ($rec == FALSE) {
			echo 'false';
		} else {
			$this->_view->assign ( "zht_opts", array ('9'=>'请选择','X' => '禁止', '1' => '启用' ) );
			$this->_view->assign ( "rec", $rec );
	    	echo  $this->_view->fetchPage ( "hyklxwh_03.php" );
		}
	}
}