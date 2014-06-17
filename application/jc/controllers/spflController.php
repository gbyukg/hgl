<?php

/******************************************************************
 ***** 模         块：       基础模块(JC)
 ***** 机         能：       商品分类(spfl)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2010/11/19
 ***** 更新履历：
 ******************************************************************/

class jc_spflController extends jc_controllers_baseController {
	
	/*
     * 商品分类维护画面显示
     */
	public function indexAction() {
		$this->_view->display ( 'spfl_01.php' );
	}
	
	
	/*
     * 商品分类增加根节点画面显示
     */
	public function newfnodeAction() {
		$this->_view->display ( 'spfl_02.php' );
	}
	
	
	/*
     * 商品分类增加根节点画面显示
     */
	public function newcnodeAction() {
		$shjfl = $this->_getParam("shpfl");
		$this->_view->assign ( "shjfl", $shjfl );
		$this->_view->display ( 'spfl_03.php' );
	}
	
	
	/*
     * 商品分类修改画面显示
     */
	public function updateAction() {	
		$model = new jc_models_spfl( );
		$shpfl = $this->_getParam("shpfl");
		$rec = $model->getShpfl($shpfl);
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'spfl_04.php' );
	}
	
	
	/**
	 * 商品分类树形列表xml
	 */
	public function getlistdataAction()
	{
		$model = new jc_models_spfl ( );
		header("Content-type:text/xml");
		echo $model->gettreeData();
	}
	
	
	/*
	 * 删除商品分类
	 */
	public function deleteAction() {
		$result = array (); //定义返回值		try {
			$model = new jc_models_spfl ( );
			$model -> beginTransaction ();				//开始一个事务
			
			if($model->getxinxi( $_POST ['shpfl'])){
				$model->delete( $_POST ['shpfl']);	
				Common_Logger::logToDb( "商品分类删除"." 分类编号：".$_POST ['shpfl']);		//写入日志
				$result ['status'] = 0;        //删除成功
				$model -> commit(); 
			}else{
				$result ['status'] = 1;        //有子分类或该分类下存在商品，不能删除
				$model -> rollBack();
			}
			
			echo Common_Tool::json_encode( $result );     //返回处理结果
			
		} catch ( Exception $e ) {
			//事务回滚
			$model -> rollBack();
     		throw $e;
		}
	}
	
	
	/*
	 * 判断编号是否存在
	 */
	public function checkAction() {
		$model = new jc_models_spfl ( );	
		if ($model->getshpfl( $this->_getParam('shpfl') ) == FALSE) {
			echo 0; //不存在
		} else {
			echo 1; //已存在
		}
	}
	

	/*
	 * 信息保存
	 */
	public function saveAction() {
		$result = array (); //定义返回值
		try {
			$model = new jc_models_spfl ();
			$model -> beginTransaction ();				//开始一个事务
			$result ['shpfl'] = $_POST['SHPFL'];        //商品分类编号
			if ($_POST ['action'] == 'new') {           //插入新数据
				if ($model->insert() == false) {
					$result ['status'] = 2;             //编号已存在					$model -> rollBack();
				} else {
					$result ['status'] = 0;             //登录成功
					Common_Logger::logToDb( "商品分类信息登录  分类编号：".$_POST ['SHPFL']);
					$model -> commit(); 
				}	
			} else {    //更新数据	
				if ($model->update() == false) {
					$result ['status'] = 3;            //修改失败
					$model -> rollBack();
				} else {
					$result ['status'] = 1;            //修改成功
					Common_Logger::logToDb( "商品分类信息修改  分类编号：".$_POST ['SHPFL']);
					$model -> commit(); 
				}
			}	
			echo Common_Tool::json_encode( $result );     //返回处理结果
		} catch ( Exception $e ) {
			//事务回滚
			$model -> rollBack();
     		throw $e;
		}
	}

	
}